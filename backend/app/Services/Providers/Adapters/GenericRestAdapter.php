<?php

namespace App\Services\Providers\Adapters;

use App\DTO\NormalizedOffer;
use App\DTO\SearchQuery;
use App\Services\Providers\AbstractProviderAdapter;

/**
 * Works with any REST API that returns a list of offers. Field mapping is fully
 * config-driven (provider_configurations.config["map"]), so MOST new providers
 * need no new code — just a DB row pointing at this adapter.
 *
 * Expected config:
 *   base_url, search_path, api_key,
 *   results_key  (dot path to the array of offers, default "results"),
 *   map          (NormalizedOffer field => dot path in provider item)
 */
class GenericRestAdapter extends AbstractProviderAdapter
{
    protected function normalize(array $raw, SearchQuery $query): array
    {
        $resultsKey = (string) ($this->config['results_key'] ?? 'results');
        $items = data_get($raw, $resultsKey, []);
        $map = (array) ($this->config['map'] ?? []);

        $offers = [];
        foreach ($items as $item) {
            $offers[] = new NormalizedOffer(
                providerId: $this->provider->id,
                providerSlug: $this->provider->slug,
                providerName: $this->provider->name,
                category: $query->category,
                title: (string) $this->pick($item, $map, 'title', 'name'),
                price: (float) $this->pick($item, $map, 'price', 'price'),
                currency: (string) ($this->pick($item, $map, 'currency', 'currency') ?: $query->currency),
                logoUrl: $this->provider->logo_url,
                origin: $this->pick($item, $map, 'origin', 'origin'),
                destination: $this->pick($item, $map, 'destination', 'destination'),
                city: $this->pick($item, $map, 'city', 'city'),
                rating: ($r = $this->pick($item, $map, 'rating', 'rating')) !== null ? (float) $r : null,
                reviewCount: (int) ($this->pick($item, $map, 'review_count', 'reviews') ?? 0),
                stops: ($s = $this->pick($item, $map, 'stops', 'stops')) !== null ? (int) $s : null,
                durationMinutes: ($d = $this->pick($item, $map, 'duration_minutes', 'duration')) !== null ? (int) $d : null,
                images: (array) ($this->pick($item, $map, 'images', 'images') ?? []),
                amenities: (array) ($this->pick($item, $map, 'amenities', 'amenities') ?? []),
                attributes: (array) ($this->pick($item, $map, 'attributes', 'attributes') ?? []),
                offerRef: $this->pick($item, $map, 'offer_ref', 'id') !== null ? (string) $this->pick($item, $map, 'offer_ref', 'id') : null,
                bookUrl: $this->pick($item, $map, 'book_url', 'url'),
            );
        }

        return $offers;
    }

    /** Resolve a value using the configured map, falling back to a default key. */
    private function pick(array $item, array $map, string $field, string $default): mixed
    {
        return data_get($item, $map[$field] ?? $default);
    }
}
