<?php

namespace App\DTO;

/**
 * Immutable, provider-agnostic search request. Every adapter receives this exact
 * shape, so the rest of the application never needs to know provider specifics.
 */
final class SearchQuery
{
    public function __construct(
        public readonly string $category,          // hotels, flights, ...
        public readonly ?string $origin = null,
        public readonly ?string $destination = null,
        public readonly ?string $departDate = null, // Y-m-d
        public readonly ?string $returnDate = null,
        public readonly int $travellers = 1,
        public readonly int $rooms = 1,
        public readonly string $currency = 'INR',
        public readonly array $filters = [],        // price_min, price_max, rating, providers, amenities...
        public readonly string $sort = 'best_value',// lowest_price|highest_cashback|best_value|highest_rating
        public readonly int $limit = 50,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            category: (string) ($data['category'] ?? 'hotels'),
            origin: $data['origin'] ?? null,
            destination: $data['destination'] ?? null,
            departDate: $data['depart_date'] ?? null,
            returnDate: $data['return_date'] ?? null,
            travellers: (int) ($data['travellers'] ?? 1),
            rooms: (int) ($data['rooms'] ?? 1),
            currency: (string) ($data['currency'] ?? config('tripcash.currency', 'INR')),
            filters: (array) ($data['filters'] ?? []),
            sort: (string) ($data['sort'] ?? 'best_value'),
            limit: (int) ($data['limit'] ?? 50),
        );
    }

    /** Stable hash used as the cache key for this query. */
    public function cacheKey(): string
    {
        return 'search:'.md5(json_encode([
            $this->category, $this->origin, $this->destination,
            $this->departDate, $this->returnDate, $this->travellers,
            $this->rooms, $this->currency, $this->filters, $this->sort,
        ]));
    }

    public function toArray(): array
    {
        return [
            'category' => $this->category,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'depart_date' => $this->departDate,
            'return_date' => $this->returnDate,
            'travellers' => $this->travellers,
            'rooms' => $this->rooms,
            'currency' => $this->currency,
            'filters' => $this->filters,
            'sort' => $this->sort,
            'limit' => $this->limit,
        ];
    }
}
