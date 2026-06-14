<?php

namespace App\Services\Cashback;

use App\Models\Booking;
use App\Models\Cashback;
use App\Models\Provider;
use App\Services\Wallet\WalletService;
use Illuminate\Support\Facades\DB;

/**
 * Owns the cashback lifecycle and its money movements:
 *   create(pending) -> confirm -> mature(withdrawable) ; or -> reject(reverse)
 *
 * Cashback is a liability, so it is created PENDING on a booking and only
 * released to the withdrawable balance after the provider confirms the
 * commission and the hold period elapses.
 */
class CashbackService
{
    public function __construct(
        private readonly CashbackCalculator $calculator,
        private readonly WalletService $wallet,
    ) {}

    /** Create a pending cashback for a booking and park the amount in pending_balance. */
    public function createForBooking(Booking $booking): ?Cashback
    {
        if (! $booking->user_id) {
            return null; // guest booking; nothing to credit
        }

        /** @var Provider $provider */
        $provider = $booking->provider;
        $result = $this->calculator->compute($provider, $booking->category, (float) $booking->amount);

        if ($result['amount'] <= 0) {
            return null;
        }

        return DB::transaction(function () use ($booking, $provider, $result) {
            $cashback = Cashback::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'user_id' => $booking->user_id,
                    'provider_id' => $provider->id,
                    'cashback_rule_id' => $result['rule_id'],
                    'category' => $booking->category,
                    'booking_amount' => $booking->amount,
                    'commission_amount' => $result['commission'],
                    'amount' => $result['amount'],
                    'currency' => $booking->currency,
                    'status' => Cashback::PENDING,
                ]
            );

            $this->wallet->credit(
                user: $booking->user,
                amount: (float) $cashback->amount,
                type: 'cashback_credit',
                source: $cashback,
                idempotencyKey: 'cb_pending_'.$cashback->id,
                description: "Cashback (pending) — {$provider->name}",
                pending: true,
            );

            return $cashback;
        });
    }

    /** Provider confirmed the commission: start the hold clock. */
    public function confirm(Cashback $cashback): Cashback
    {
        if ($cashback->status !== Cashback::PENDING) {
            return $cashback;
        }
        $holdDays = (int) config('travelcash.cashback.hold_days', 30);
        $cashback->update([
            'status' => Cashback::CONFIRMED,
            'confirmed_at' => now(),
            'matures_at' => now()->addDays($holdDays),
        ]);

        return $cashback;
    }

    /** Hold elapsed: move pending -> withdrawable. */
    public function mature(Cashback $cashback): Cashback
    {
        if ($cashback->status !== Cashback::CONFIRMED) {
            return $cashback;
        }

        return DB::transaction(function () use ($cashback) {
            $this->wallet->release(
                user: $cashback->user,
                amount: (float) $cashback->amount,
                source: $cashback,
                idempotencyKey: 'cb_release_'.$cashback->id,
            );
            $cashback->update(['status' => Cashback::WITHDRAWABLE]);

            return $cashback;
        });
    }

    /** Booking cancelled/declined: reverse the pending amount. */
    public function reject(Cashback $cashback, string $reason = 'Booking not confirmed by provider'): Cashback
    {
        if (in_array($cashback->status, [Cashback::REJECTED, Cashback::PAID], true)) {
            return $cashback;
        }

        return DB::transaction(function () use ($cashback, $reason) {
            // Reverse the pending credit (only if it had not yet matured/withdrawn).
            if (in_array($cashback->status, [Cashback::PENDING, Cashback::CONFIRMED], true)) {
                $wallet = $this->wallet->walletFor($cashback->user);
                $wallet->pending_balance = max(0, (float) $wallet->pending_balance - (float) $cashback->amount);
                $wallet->lifetime_earned = max(0, (float) $wallet->lifetime_earned - (float) $cashback->amount);
                $wallet->save();
            }
            $cashback->update([
                'status' => Cashback::REJECTED,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            return $cashback;
        });
    }

    /** Batch maturation for the scheduler. */
    public function matureDue(): int
    {
        $count = 0;
        Cashback::matured()->chunkById(200, function ($chunk) use (&$count) {
            foreach ($chunk as $cashback) {
                $this->mature($cashback);
                $count++;
            }
        });

        return $count;
    }
}
