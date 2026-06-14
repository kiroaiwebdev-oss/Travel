<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class CachedOffer extends Model
{
    use Searchable;

    protected $fillable = [
        'offer_hash', 'provider_id', 'provider_slug', 'category', 'title',
        'origin', 'destination', 'city', 'price', 'cashback', 'rating',
        'review_count', 'stops', 'duration_minutes', 'currency', 'raw',
        'deep_link', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cashback' => 'decimal:2',
            'rating' => 'decimal:1',
            'raw' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /** The index name for Meilisearch (matches config/scout.php). */
    public function searchableAs(): string
    {
        return 'offers';
    }

    /** Flattened document pushed to Meilisearch. */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'provider_slug' => $this->provider_slug,
            'title' => $this->title,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'city' => $this->city,
            'price' => (float) $this->price,
            'cashback' => (float) $this->cashback,
            'rating' => (float) $this->rating,
            'stops' => $this->stops,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}
