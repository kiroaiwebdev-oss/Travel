<?php

namespace App\Services\Cashback;

use App\Models\CashbackRule;
use Illuminate\Support\Facades\Cache;

/**
 * Picks the single best cashback rule for a (provider, category, amount).
 * Specificity order (most specific wins):
 *   1. provider + category
 *   2. provider (any category)
 *   3. category (any provider)
 *   4. global default
 * Ties break on lowest `priority`.
 */
class CashbackRuleResolver
{
    public function resolve(?int $providerId, ?string $category, float $bookingAmount = 0): ?CashbackRule
    {
        /** @var \Illuminate\Support\Collection<int, CashbackRule> $rules */
        $rules = Cache::remember('cashback:rules:active', 120, function () {
            return CashbackRule::query()->activeNow()->orderBy('priority')->get();
        });

        $candidates = $rules->filter(function (CashbackRule $r) use ($providerId, $category, $bookingAmount) {
            if ($r->min_booking_amount > 0 && $bookingAmount < (float) $r->min_booking_amount) {
                return false;
            }
            if ($r->provider_id !== null && $r->provider_id !== $providerId) {
                return false;
            }
            if ($r->category !== null && $r->category !== $category) {
                return false;
            }

            return true;
        });

        if ($candidates->isEmpty()) {
            return null;
        }

        // Rank by specificity, then priority.
        return $candidates->sortBy([
            fn (CashbackRule $r) => $this->specificity($r) * -1, // higher specificity first
            fn (CashbackRule $r) => $r->priority,
        ])->first();
    }

    private function specificity(CashbackRule $r): int
    {
        return ($r->provider_id !== null ? 2 : 0) + ($r->category !== null ? 1 : 0);
    }

    public static function flushCache(): void
    {
        Cache::forget('cashback:rules:active');
    }
}
