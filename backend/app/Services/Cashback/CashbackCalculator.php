<?php

namespace App\Services\Cashback;

use App\Models\CashbackRule;
use App\Models\Provider;

/**
 * Computes the cashback amount a user earns. Cashback is a share of the
 * commission WE earn from the provider:
 *
 *   commission = bookingAmount * provider.commission_percent / 100
 *   percentage rule -> cashback = commission * rule.value / 100   (share of commission)
 *   fixed rule      -> cashback = rule.value
 *
 * `max_cap` caps the payout. Falls back to the global default share % when no
 * rule matches.
 */
class CashbackCalculator
{
    public function __construct(private readonly CashbackRuleResolver $resolver) {}

    public function commissionFor(Provider $provider, float $bookingAmount): float
    {
        return round($bookingAmount * ((float) $provider->commission_percent) / 100, 2);
    }

    /**
     * @return array{amount: float, commission: float, rule_id: ?int}
     */
    public function compute(Provider $provider, ?string $category, float $bookingAmount): array
    {
        $commission = $this->commissionFor($provider, $bookingAmount);
        $rule = $this->resolver->resolve($provider->id, $category, $bookingAmount);

        if ($rule === null) {
            $share = (float) config('travelcash.cashback.default_share_percent', 40);
            $amount = $commission * $share / 100;

            return ['amount' => round($amount, 2), 'commission' => $commission, 'rule_id' => null];
        }

        $amount = match ($rule->type) {
            CashbackRule::FIXED => (float) $rule->value,
            default => $commission * ((float) $rule->value) / 100,
        };

        if ($rule->max_cap !== null) {
            $amount = min($amount, (float) $rule->max_cap);
        }

        return ['amount' => round($amount, 2), 'commission' => $commission, 'rule_id' => $rule->id];
    }

    /** Lightweight estimate for search-result display (no rule_id needed). */
    public function estimate(Provider $provider, ?string $category, float $price): float
    {
        return $this->compute($provider, $category, $price)['amount'];
    }
}
