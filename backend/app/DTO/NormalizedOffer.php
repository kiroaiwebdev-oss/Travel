<?php

namespace App\DTO;

/**
 * The single canonical offer shape the whole platform understands. Adapters
 * translate provider-specific JSON into this DTO. The UI, cashback engine and
 * search index only ever deal with NormalizedOffer — never raw provider data.
 */
final class NormalizedOffer
{
    public function __construct(
        public readonly int $providerId,
        public readonly string $providerSlug,
        public readonly string $providerName,
        public readonly string $category,
        public readonly string $title,
        public readonly float $price,
        public readonly string $currency = 'INR',
        public readonly ?string $logoUrl = null,
        public readonly ?string $origin = null,
        public readonly ?string $destination = null,
        public readonly ?string $city = null,
        public readonly ?float $rating = null,
        public readonly int $reviewCount = 0,
        public readonly ?int $stops = null,
        public readonly ?int $durationMinutes = null,
        public readonly array $images = [],
        public readonly array $amenities = [],
        public readonly array $attributes = [], // category-specific extras (airline, vehicle_type, languages...)
        public readonly ?string $offerRef = null,
        public readonly ?string $bookUrl = null,
        // Filled in by the cashback engine after normalization.
        public float $cashback = 0.0,
    ) {}

    /** Stable de-dupe hash. */
    public function hash(): string
    {
        return hash('sha256', implode('|', [
            $this->providerSlug, $this->category, $this->title,
            $this->origin, $this->destination, $this->offerRef, (string) $this->price,
        ]));
    }

    public function toArray(): array
    {
        return [
            'provider_id' => $this->providerId,
            'provider_slug' => $this->providerSlug,
            'provider_name' => $this->providerName,
            'logo_url' => $this->logoUrl,
            'category' => $this->category,
            'title' => $this->title,
            'price' => round($this->price, 2),
            'cashback' => round($this->cashback, 2),
            'currency' => $this->currency,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'city' => $this->city,
            'rating' => $this->rating,
            'review_count' => $this->reviewCount,
            'stops' => $this->stops,
            'duration_minutes' => $this->durationMinutes,
            'images' => $this->images,
            'amenities' => $this->amenities,
            'attributes' => $this->attributes,
            'offer_ref' => $this->offerRef,
            'book_url' => $this->bookUrl,
            'hash' => $this->hash(),
        ];
    }

    public function withCashback(float $amount): self
    {
        $this->cashback = round($amount, 2);

        return $this;
    }
}
