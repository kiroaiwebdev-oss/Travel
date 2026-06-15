<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'affiliate_network_id', 'name', 'slug', 'logo_url', 'adapter',
        'categories', 'is_active', 'priority', 'commission_percent',
        'tracking_template',
    ];

    protected function casts(): array
    {
        return [
            'categories' => 'array',
            'is_active' => 'boolean',
            'commission_percent' => 'decimal:2',
        ];
    }

    public function network(): BelongsTo
    {
        return $this->belongsTo(AffiliateNetwork::class, 'affiliate_network_id');
    }

    public function configurations(): HasMany
    {
        return $this->hasMany(ProviderConfiguration::class);
    }

    public function activeConfiguration(): HasOne
    {
        return $this->hasOne(ProviderConfiguration::class)
            ->where('environment', app()->environment('production') ? 'production' : 'sandbox')
            ->where('is_active', true);
    }

    public function cashbackRules(): HasMany
    {
        return $this->hasMany(CashbackRule::class);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeForCategory(Builder $q, string $category): Builder
    {
        return $q->whereJsonContains('categories', $category);
    }

    public function supports(string $category): bool
    {
        return in_array($category, $this->categories ?? [], true);
    }

    /**
     * Demo mode flag from the active configuration, resilient to decryption
     * failures (e.g. APP_KEY changed after the config was encrypted) so the
     * admin providers list never 500s — it just falls back to "demo".
     */
    public function isDemoMode(): bool
    {
        try {
            return (bool) ($this->activeConfiguration?->config['demo_mode'] ?? true);
        } catch (\Throwable $e) {
            return true;
        }
    }
}
