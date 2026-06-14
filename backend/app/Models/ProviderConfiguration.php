<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderConfiguration extends Model
{
    protected $fillable = ['provider_id', 'environment', 'config', 'is_active'];

    protected $hidden = ['config'];

    protected function casts(): array
    {
        return [
            // Secrets (api_key, secret_key, base_url, params) encrypted at rest.
            'config' => 'encrypted:array',
            'is_active' => 'boolean',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }
}
