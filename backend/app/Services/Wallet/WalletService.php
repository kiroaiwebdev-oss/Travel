<?php

namespace App\Services\Wallet;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The only place wallet balances ever change. Every mutation writes an immutable
 * ledger row and updates the cached projections atomically (row lock + idempotency
 * key prevent double posting from retried jobs/postbacks).
 */
class WalletService
{
    public function walletFor(User $user): Wallet
    {
        return $user->wallet()->firstOrCreate([], ['currency' => $user->currency]);
    }

    /**
     * Post a CREDIT. When $pending is true the amount lands in pending_balance
     * (not yet withdrawable); otherwise it hits the withdrawable balance.
     */
    public function credit(
        User $user,
        float $amount,
        string $type,
        ?Model $source = null,
        ?string $idempotencyKey = null,
        ?string $description = null,
        bool $pending = false,
        array $meta = [],
    ): ?WalletTransaction {
        return $this->post($user, $amount, WalletTransaction::CREDIT, $type, $source, $idempotencyKey, $description, $pending, $meta);
    }

    public function debit(
        User $user,
        float $amount,
        string $type,
        ?Model $source = null,
        ?string $idempotencyKey = null,
        ?string $description = null,
        array $meta = [],
    ): ?WalletTransaction {
        return $this->post($user, $amount, WalletTransaction::DEBIT, $type, $source, $idempotencyKey, $description, false, $meta);
    }

    /** Move an amount from pending_balance to withdrawable balance (cashback maturing). */
    public function release(User $user, float $amount, ?Model $source = null, ?string $idempotencyKey = null): ?WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $source, $idempotencyKey) {
            if ($idempotencyKey && WalletTransaction::where('idempotency_key', $idempotencyKey)->exists()) {
                return null;
            }
            $wallet = $this->lock($user);
            $wallet->pending_balance = max(0, (float) $wallet->pending_balance - $amount);
            $wallet->balance = (float) $wallet->balance + $amount;
            $wallet->save();

            return $this->writeRow($wallet, $user, $amount, WalletTransaction::CREDIT, 'cashback_release', $source, $idempotencyKey, 'Cashback matured', []);
        });
    }

    /**
     * Reverse a still-pending credit (e.g. a cancelled/declined booking's cashback).
     * Row-locked + idempotent + ledgered so duplicate cancellation postbacks can never
     * double-reverse a balance.
     */
    public function reversePending(User $user, float $amount, ?Model $source = null, ?string $idempotencyKey = null, ?string $description = null): ?WalletTransaction
    {
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($user, $amount, $source, $idempotencyKey, $description) {
            if ($idempotencyKey && WalletTransaction::where('idempotency_key', $idempotencyKey)->exists()) {
                return null;
            }
            $wallet = $this->lock($user);
            $wallet->pending_balance = max(0, (float) $wallet->pending_balance - $amount);
            $wallet->lifetime_earned = max(0, (float) $wallet->lifetime_earned - $amount);
            $wallet->save();

            return $this->writeRow($wallet, $user, $amount, WalletTransaction::DEBIT, 'cashback_reversal', $source, $idempotencyKey, $description ?? 'Cashback reversed', []);
        });
    }

    private function post(
        User $user,
        float $amount,
        string $direction,
        string $type,
        ?Model $source,
        ?string $idempotencyKey,
        ?string $description,
        bool $pending,
        array $meta,
    ): ?WalletTransaction {
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($user, $amount, $direction, $type, $source, $idempotencyKey, $description, $pending, $meta) {
            // Idempotency: a key that already exists means this was already posted.
            if ($idempotencyKey && WalletTransaction::where('idempotency_key', $idempotencyKey)->exists()) {
                return null;
            }

            $wallet = $this->lock($user);

            if ($direction === WalletTransaction::CREDIT) {
                if ($pending) {
                    $wallet->pending_balance = (float) $wallet->pending_balance + $amount;
                } else {
                    $wallet->balance = (float) $wallet->balance + $amount;
                }
                $wallet->lifetime_earned = (float) $wallet->lifetime_earned + $amount;
            } else {
                if ((float) $wallet->balance < $amount) {
                    throw new \RuntimeException('Insufficient withdrawable balance.');
                }
                $wallet->balance = (float) $wallet->balance - $amount;
                $wallet->lifetime_withdrawn = (float) $wallet->lifetime_withdrawn + $amount;
            }

            $wallet->save();

            return $this->writeRow($wallet, $user, $amount, $direction, $type, $source, $idempotencyKey, $description, $meta);
        });
    }

    private function lock(User $user): Wallet
    {
        $this->walletFor($user); // ensure exists

        return Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
    }

    private function writeRow(Wallet $wallet, User $user, float $amount, string $direction, string $type, ?Model $source, ?string $idempotencyKey, ?string $description, array $meta): WalletTransaction
    {
        return $wallet->transactions()->create([
            'user_id' => $user->id,
            'type' => $type,
            'direction' => $direction,
            'amount' => $amount,
            'currency' => $wallet->currency,
            'balance_after' => $wallet->balance,
            'source_type' => $source ? $source::class : null,
            'source_id' => $source?->getKey(),
            'idempotency_key' => $idempotencyKey,
            'description' => $description,
            'meta' => $meta ?: null,
        ]);
    }

    /** Reconcile cached balances against the ledger (scheduled safety net). */
    public function reconcile(User $user): Wallet
    {
        return DB::transaction(function () use ($user) {
            $wallet = $this->lock($user);
            $credits = $wallet->transactions()->where('direction', WalletTransaction::CREDIT)->sum('amount');
            $debits = $wallet->transactions()->where('direction', WalletTransaction::DEBIT)->sum('amount');
            $wallet->lifetime_earned = $credits;
            $wallet->lifetime_withdrawn = $debits;
            $wallet->save();

            return $wallet;
        });
    }
}
