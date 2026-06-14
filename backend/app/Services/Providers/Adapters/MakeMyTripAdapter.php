<?php

namespace App\Services\Providers\Adapters;

use App\DTO\NormalizedOffer;
use App\DTO\SearchQuery;
use App\Services\Providers\AbstractProviderAdapter;

/**
 * Reference multi-category adapter (flights + hotels + trains + packages) showing
 * how one adapter can dispatch normalization per category.
 */
class MakeMyTripAdapter extends AbstractProviderAdapter
{
    protected function fetch(SearchQuery $query): array
    {
        return $this->client()->get(rtrim((string) $this->config['base_url'], '/')."/api/{$query->category}", [
            'from' => $query->origin,
            'to' => $query->destination,
            'date' => $query->departDate,
            'pax' => $query->travellers,
        ])->throw()->json() ?? [];
    }

    protected function normalize(array $raw, SearchQuery $query): array
    {
        return match ($query->category) {
            'flights' => $this->normalizeFlights($raw, $query),
            default => $this->normalizeGeneric($raw, $query),
        };
    }

    private function normalizeFlights(array $raw, SearchQuery $query): array
    {
        $offers = [];
        foreach (data_get($raw, 'flights', []) as $f) {
            $offers[] = new NormalizedOffer(
                providerId: $this->provider->id,
                providerSlug: $this->provider->slug,
                providerName: $this->provider->name,
                category: 'flights',
                title: (string) (data_get($f, 'airline').' '.$query->origin.'→'.$query->destination),
                price: (float) data_get($f, 'fare', 0),
                currency: $query->currency,
                logoUrl: $this->provider->logo_url,
                origin: $query->origin,
                destination: $query->destination,
                stops: (int) data_get($f, 'stops', 0),
                durationMinutes: (int) data_get($f, 'duration_min', 0),
                attributes: [
                    'airline' => data_get($f, 'airline'),
                    'depart_time' => data_get($f, 'dep'),
                    'arrive_time' => data_get($f, 'arr'),
                ],
                offerRef: (string) data_get($f, 'id'),
                bookUrl: data_get($f, 'deeplink'),
            );
        }

        return $offers;
    }

    private function normalizeGeneric(array $raw, SearchQuery $query): array
    {
        $offers = [];
        foreach (data_get($raw, 'items', []) as $item) {
            $offers[] = new NormalizedOffer(
                providerId: $this->provider->id,
                providerSlug: $this->provider->slug,
                providerName: $this->provider->name,
                category: $query->category,
                title: (string) data_get($item, 'title'),
                price: (float) data_get($item, 'price', 0),
                currency: $query->currency,
                logoUrl: $this->provider->logo_url,
                origin: $query->origin,
                destination: $query->destination,
                offerRef: (string) data_get($item, 'id'),
                bookUrl: data_get($item, 'url'),
            );
        }

        return $offers;
    }
}
