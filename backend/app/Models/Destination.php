<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Destination extends Model
{
    protected $fillable = ['name', 'slug', 'tag', 'image_url', 'category', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        static::saving(function (Destination $d) {
            $d->slug = $d->slug ?: Str::slug($d->name);
        });
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }
}
