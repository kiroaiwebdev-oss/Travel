<?php

namespace App\Services\Providers;

use App\DTO\NormalizedOffer;
use App\DTO\SearchQuery;
use App\Models\Provider;

/**
 * Generates realistic, varied sample offers per category. Used while a provider
 * has no live API credentials, so the entire search → click → cashback funnel is
 * demonstrable from the first install. Swapped out automatically once real keys
 * are configured (see AbstractProviderAdapter::isDemoMode()).
 */
class DemoOfferFactory
{
    private const HOTELS = ['Grand Plaza', 'Seaside Resort', 'Urban Boutique', 'Heritage Palace', 'Skyline Suites', 'Palm Retreat'];
    private const AIRLINES = ['IndiGo', 'Air India', 'Vistara', 'Emirates', 'Qatar Airways', 'Singapore Airlines'];
    private const VEHICLES = ['Mini', 'Sedan', 'SUV', 'Premier', 'Auto'];
    private const LANGS = ['English', 'Hindi', 'Spanish', 'French', 'Mandarin'];

    public function __construct(private readonly Provider $provider) {}

    /** @return array<int, NormalizedOffer> */
    public function make(SearchQuery $q): array
    {
        $count = min(8, max(4, $q->limit));
        $offers = [];
        // Seed variety per provider so prices differ between providers.
        $seed = crc32($this->provider->slug.$q->destination.$q->origin);
        mt_srand($seed);

        for ($i = 0; $i < $count; $i++) {
            $offers[] = match ($q->category) {
                'flights' => $this->flight($q, $i),
                'cabs' => $this->cab($q, $i),
                'trains' => $this->train($q, $i),
                'packages' => $this->package($q, $i),
                'guides' => $this->guide($q, $i),
                'activities', 'transfers' => $this->activity($q, $i),
                default => $this->hotel($q, $i),
            };
        }
        mt_srand();

        return $offers;
    }

    private function base(string $title, float $price, array $extra = []): array
    {
        return array_merge([
            'providerId' => $this->provider->id,
            'providerSlug' => $this->provider->slug,
            'providerName' => $this->provider->name,
            'logoUrl' => $this->provider->logo_url,
            'title' => $title,
            'price' => $price,
            'currency' => 'INR',
            'offerRef' => $this->provider->slug.'-'.substr(md5($title.$price), 0, 8),
        ], $extra);
    }

    private function hotel(SearchQuery $q, int $i): NormalizedOffer
    {
        $name = self::HOTELS[$i % count(self::HOTELS)];
        $city = $q->destination ?: 'Goa';
        $a = $this->base("$name $city", mt_rand(2200, 12000), [
            'category' => 'hotels',
            'city' => $city,
            'destination' => $city,
            'rating' => round(mt_rand(35, 49) / 10, 1),
            'reviewCount' => mt_rand(120, 4200),
            'images' => ["https://picsum.photos/seed/{$this->provider->slug}{$i}/640/420"],
            'amenities' => array_slice(['Free WiFi', 'Pool', 'Breakfast', 'Spa', 'Gym', 'Parking'], 0, mt_rand(2, 5)),
        ]);

        return new NormalizedOffer(...$a);
    }

    private function flight(SearchQuery $q, int $i): NormalizedOffer
    {
        $airline = self::AIRLINES[$i % count(self::AIRLINES)];
        $stops = mt_rand(0, 2);
        $duration = mt_rand(90, 720);
        $a = $this->base("$airline {$q->origin}→{$q->destination}", mt_rand(3500, 42000), [
            'category' => 'flights',
            'origin' => $q->origin ?: 'DEL',
            'destination' => $q->destination ?: 'DXB',
            'stops' => $stops,
            'durationMinutes' => $duration,
            'attributes' => [
                'airline' => $airline,
                'depart_time' => sprintf('%02d:%02d', mt_rand(0, 23), [0, 15, 30, 45][mt_rand(0, 3)]),
                'arrive_time' => sprintf('%02d:%02d', mt_rand(0, 23), [0, 15, 30, 45][mt_rand(0, 3)]),
                'cabin' => 'Economy',
            ],
        ]);

        return new NormalizedOffer(...$a);
    }

    private function cab(SearchQuery $q, int $i): NormalizedOffer
    {
        $v = self::VEHICLES[$i % count(self::VEHICLES)];
        $a = $this->base("$v · {$this->provider->name}", mt_rand(120, 1800), [
            'category' => 'cabs',
            'city' => $q->destination ?: $q->origin,
            'attributes' => ['vehicle_type' => $v, 'eta_minutes' => mt_rand(2, 18), 'seats' => mt_rand(3, 6)],
        ]);

        return new NormalizedOffer(...$a);
    }

    private function train(SearchQuery $q, int $i): NormalizedOffer
    {
        $classes = ['SL', '3A', '2A', '1A', 'CC'];
        $cls = $classes[$i % count($classes)];
        $a = $this->base("Train {$q->origin}→{$q->destination} ($cls)", mt_rand(250, 3500), [
            'category' => 'trains',
            'origin' => $q->origin ?: 'NDLS',
            'destination' => $q->destination ?: 'BCT',
            'durationMinutes' => mt_rand(240, 1800),
            'attributes' => ['class' => $cls, 'available' => mt_rand(0, 80)],
        ]);

        return new NormalizedOffer(...$a);
    }

    private function package(SearchQuery $q, int $i): NormalizedOffer
    {
        $nights = [3, 4, 5, 6, 7][$i % 5];
        $dest = $q->destination ?: 'Thailand';
        $a = $this->base("$dest $nights Nights Getaway", mt_rand(18000, 95000), [
            'category' => 'packages',
            'destination' => $dest,
            'durationMinutes' => $nights * 24 * 60,
            'images' => ["https://picsum.photos/seed/pkg{$this->provider->slug}{$i}/640/420"],
            'attributes' => ['nights' => $nights, 'inclusions' => ['Flights', 'Hotel', 'Transfers', 'Sightseeing']],
        ]);

        return new NormalizedOffer(...$a);
    }

    private function guide(SearchQuery $q, int $i): NormalizedOffer
    {
        $a = $this->base('Certified Local Guide', mt_rand(800, 4500), [
            'category' => 'guides',
            'city' => $q->destination ?: 'Jaipur',
            'rating' => round(mt_rand(40, 50) / 10, 1),
            'reviewCount' => mt_rand(20, 600),
            'attributes' => [
                'languages' => array_slice(self::LANGS, 0, mt_rand(2, 4)),
                'experience_years' => mt_rand(2, 18),
            ],
        ]);

        return new NormalizedOffer(...$a);
    }

    private function activity(SearchQuery $q, int $i): NormalizedOffer
    {
        $a = $this->base('Top Experience #'.($i + 1), mt_rand(500, 6000), [
            'category' => $q->category,
            'city' => $q->destination ?: 'Bali',
            'rating' => round(mt_rand(38, 50) / 10, 1),
            'reviewCount' => mt_rand(40, 1500),
        ]);

        return new NormalizedOffer(...$a);
    }
}
