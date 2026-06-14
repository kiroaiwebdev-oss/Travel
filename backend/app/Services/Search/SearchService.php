<?php

namespace App\Services\Search;

use App\DTO\NormalizedOffer;
use App\DTO\SearchQuery;
use App\Models\SearchLog;
use App\Services\Cashback\CashbackCalculator;
use App\Services\Providers\ProviderManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

/**
 * Orchestrates a search across all active providers for a category, enriches each
 * offer with an estimated cashback, then sorts/filters. Results are cached on a
 * short TTL so repeat searches and high traffic stay well under the latency budget.
 */
class SearchService
{
    public function __construct(
        private readonly ProviderManager $providers,
        private readonly CashbackCalculator $cashback,
    ) {}

    /**
     * @return array{offers: array<int,array>, meta: array}
     */
    public function search(SearchQuery $query, ?int $userId = null, ?string $sessionId = null): array
    {
        $start = microtime(true);
        $cacheKey = $query->cacheKey();
        $cacheHit = Cache::has($cacheKey);

        $offers = Cache::remember(
            $cacheKey,
            (int) config('travelcash.search.cache_ttl', 300),
            fn () => $this->fanOut($query)
        );

        $offers = $this->applyFilters($offers, $query->filters);
        $offers = $this->sort($offers, $query->sort);
        $offers = array_slice($offers, 0, $query->limit);
        $offers = array_map([$this, 'attachGoUrl'], $offers);

        $responseMs = (int) round((microtime(true) - $start) * 1000);

        $this->log($query, count($offers), $responseMs, $cacheHit, $userId, $sessionId);

        return [
            'offers' => $offers,
            'meta' => [
                'category' => $query->category,
                'count' => count($offers),
                'response_ms' => $responseMs,
                'cache_hit' => $cacheHit,
                'sort' => $query->sort,
                'cheapest' => $offers[0]['price'] ?? null,
                'best_cashback' => collect($offers)->max('cashback'),
            ],
        ];
    }

    /** Fan out to every active provider, normalize + attach cashback. */
    private function fanOut(SearchQuery $query): array
    {
        $providers = $this->providers->activeFor($query->category);
        $perProvider = (int) config('travelcash.search.per_provider_limit', 50);
        $all = [];

        foreach ($providers as $provider) {
            $adapter = $this->providers->adapterFor($provider);
            $offers = array_slice($adapter->search($query), 0, $perProvider);

            foreach ($offers as $offer) {
                /** @var NormalizedOffer $offer */
                $cb = $this->cashback->estimate($provider, $query->category, $offer->price);
                $all[] = $offer->withCashback($cb)->toArray();
            }
        }

        return $all;
    }

    private function applyFilters(array $offers, array $filters): array
    {
        return array_values(array_filter($offers, function (array $o) use ($filters) {
            if (isset($filters['price_min']) && $o['price'] < (float) $filters['price_min']) {
                return false;
            }
            if (isset($filters['price_max']) && $o['price'] > (float) $filters['price_max']) {
                return false;
            }
            if (isset($filters['rating']) && (float) ($o['rating'] ?? 0) < (float) $filters['rating']) {
                return false;
            }
            if (! empty($filters['providers']) && ! in_array($o['provider_slug'], (array) $filters['providers'], true)) {
                return false;
            }
            if (isset($filters['max_stops']) && $o['stops'] !== null && $o['stops'] > (int) $filters['max_stops']) {
                return false;
            }
            if (! empty($filters['amenities'])) {
                $need = (array) $filters['amenities'];
                $have = (array) ($o['amenities'] ?? []);
                if (array_diff($need, $have)) {
                    return false;
                }
            }

            return true;
        }));
    }

    private function sort(array $offers, string $sort): array
    {
        usort($offers, function (array $a, array $b) use ($sort) {
            return match ($sort) {
                'lowest_price' => $a['price'] <=> $b['price'],
                'highest_cashback' => $b['cashback'] <=> $a['cashback'],
                'highest_rating' => ($b['rating'] ?? 0) <=> ($a['rating'] ?? 0),
                // best_value: maximise cashback-adjusted price, then rating.
                default => $this->valueScore($b) <=> $this->valueScore($a),
            };
        });

        return $offers;
    }

    private function valueScore(array $o): float
    {
        $net = max(1, $o['price'] - $o['cashback']);
        $ratingBoost = 1 + (($o['rating'] ?? 0) / 10);

        return ($o['cashback'] / $net) * 100 * $ratingBoost;
    }

    /**
     * Attach a signed click-out URL. Signing the price/category prevents a user
     * from tampering with the amount that drives their cashback.
     */
    private function attachGoUrl(array $o): array
    {
        $o['go_url'] = URL::signedRoute('go', [
            'provider' => $o['provider_slug'],
            'offer_ref' => $o['offer_ref'],
            'amount' => $o['price'],
            'category' => $o['category'],
            'url' => $o['book_url'],
        ]);

        return $o;
    }

    private function log(SearchQuery $q, int $count, int $ms, bool $cacheHit, ?int $userId, ?string $sessionId): void
    {
        try {
            SearchLog::create([
                'user_id' => $userId,
                'category' => $q->category,
                'origin' => $q->origin,
                'destination' => $q->destination,
                'depart_date' => $q->departDate,
                'return_date' => $q->returnDate,
                'travellers' => $q->travellers,
                'filters' => $q->filters ?: null,
                'result_count' => $count,
                'response_ms' => $ms,
                'cache_hit' => $cacheHit,
                'session_id' => $sessionId,
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // Logging must never break a search response.
        }
    }
}
