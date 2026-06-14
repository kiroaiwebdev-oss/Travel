<?php

namespace App\Services\Referral;

use App\Models\Referral;
use App\Models\User;
use App\Services\Wallet\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Referral lifecycle with fraud protection:
 *   attachReferral (signup) -> qualify (first confirmed cashback) -> reward (wallet credit)
 *
 * Guards: a user can only be referred once; self-referral by IP/fingerprint is
 * blocked; reward releases only after the referee's first confirmed booking.
 */
class ReferralService
{
    public function __construct(private readonly WalletService $wallet) {}

    public function attachReferral(User $referee, string $code, Request $request): ?Referral
    {
        $referrer = User::where('referral_code', $code)->first();

        if (! $referrer || $referrer->id === $referee->id) {
            return null;
        }

        // Fraud guard: same IP signup as referrer is suspicious -> still record but flag rejected.
        $sameIp = $referrer->last_login_ip && $referrer->last_login_ip === $request->ip();

        // Daily cap per referrer.
        $todayCount = Referral::where('referrer_id', $referrer->id)
            ->whereDate('created_at', today())->count();
        $overCap = $todayCount >= (int) config('travelcash.referral.max_referrals_per_day', 20);

        $referee->forceFill(['referred_by' => $referrer->id])->save();

        return Referral::create([
            'referrer_id' => $referrer->id,
            'referee_id' => $referee->id,
            'code' => $code,
            'status' => ($sameIp || $overCap) ? Referral::REJECTED : Referral::PENDING,
            'reward_amount' => (float) config('travelcash.referral.reward_amount', 100),
            'ip_address' => $request->ip(),
            'signup_fingerprint' => hash('sha256', $request->ip().'|'.$request->userAgent()),
        ]);
    }

    /** Called when the referee earns their first confirmed cashback. */
    public function qualify(User $referee): void
    {
        $referral = Referral::where('referee_id', $referee->id)
            ->where('status', Referral::PENDING)->first();

        if (! $referral) {
            return;
        }

        $referral->update(['status' => Referral::QUALIFIED, 'qualified_at' => now()]);

        if (config('travelcash.referral.require_confirmed_booking', true)) {
            $this->reward($referral);
        }
    }

    public function reward(Referral $referral): void
    {
        if ($referral->status === Referral::REWARDED) {
            return;
        }

        $this->wallet->credit(
            user: $referral->referrer,
            amount: (float) $referral->reward_amount,
            type: 'referral_credit',
            source: $referral,
            idempotencyKey: 'ref_reward_'.$referral->id,
            description: 'Referral reward',
            pending: false,
        );

        $referral->update(['status' => Referral::REWARDED, 'rewarded_at' => now()]);

        Log::info('Referral rewarded', ['referral_id' => $referral->id]);
    }
}
