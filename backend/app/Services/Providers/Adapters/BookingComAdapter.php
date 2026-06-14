<?php

namespace App\Services\Providers\Adapters;

use App\DTO\NormalizedOffer;
use App\DTO\SearchQuery;
use App\Services\Providers\AbstractProviderAdapter;

/**
 * Reference adapter for a Booking.com-style hotels API. Demonstrates a fully
 * bespoke normalization when the response shape doesn't fit the generic mapper.
 */
class BookingComAdapter extends AbstractProviderAdapter
{
    protected function fetch(SearchQuery $query): array
    {
        return $this->client()->get(rtrim((string) $this->config['base_url'], '/').'/v1/hotels/search', [
            'city' => $query->destination,
            'checkin' => $query->departDate,
            'checkout' => $query->returnDate,
            'adults' => $query->travellers,
            'rooms' => $query->rooms,
            'currency' => $query->currency,
        ])->throw()->json() ?? [];
    }

    protected function normalize(array $raw, SearchQuery $query): array
    {
        $offers = [];
        foreach (data_get($raw, 'result', []) as $hotel) {
            $offers[] = new NormalizedOffer(
                providerId: $this->provider->id,
                providerSlug: $this->provider->slug,
                providerName: $this->provider->name,
                category: 'hotels',
                title: (string) data_get($hotel, 'hotel_name'),
                price: (float) data_get($hotel, 'min_total_price', 0),
                currency: (string) (data_get($hotel, 'currency') ?: $query->currency),
                logoUrl: $this->provider->logo_url,
                destination: $query->destination,
                city: (string) data_get($hotel, 'city'),
                rating: ($r = data_get($hotel, 'review_score')) !== null ? round(((float) $r) / 2, 1) : null,
                reviewCount: (int) data_get($hotel, 'review_nr', 0),
                images: array_filter([data_get($hotel, 'main_photo_url')]),
                amenities: (array) data_get($hotel, 'facilities', []),
                offerRef: (string) data_get($hotel, 'hotel_id'),
                bookUrl: data_get($hotel, 'url'),
            );
        }

        return $offers;
    }
}
