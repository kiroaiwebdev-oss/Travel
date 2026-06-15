<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Offer extends Model
{
    protected $fillable = [
        'provider_id', 'title', 'slug', 'category', 'cashback_label', 'cashback_type',
        'cashback_value', 'description', 'terms', 'image_url', 'deep_link',
        'is_featured', 'is_active', 'sort_order', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'cashback_value' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Offer $offer) {
            $offer->slug = $offer->slug ?: Str::slug($offer->title).'-'.Str::random(5);
        });
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true)
            ->where(fn ($w) => $w->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }
}
