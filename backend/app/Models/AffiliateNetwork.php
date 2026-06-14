<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateNetwork extends Model
{
    protected $fillable = ['name', 'slug', 'postback_secret', 'is_active'];

    protected $hidden = ['postback_secret'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'postback_secret' => 'encrypted',
        ];
    }

    public function providers(): HasMany
    {
        return $this->hasMany(Provider::class);
    }
}
