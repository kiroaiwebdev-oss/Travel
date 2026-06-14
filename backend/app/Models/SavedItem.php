<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedItem extends Model
{
    public const SAVED_HOTEL = 'saved_hotel';
    public const SAVED_FLIGHT = 'saved_flight';
    public const SAVED_SEARCH = 'saved_search';
    public const WATCHLIST = 'watchlist';

    protected $fillable = [
        'user_id', 'kind', 'category', 'reference', 'payload', 'target_price',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'target_price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
