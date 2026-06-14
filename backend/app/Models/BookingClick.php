<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingClick extends Model
{
    protected $fillable = [
        'click_id', 'user_id', 'provider_id', 'category', 'offer_ref',
        'expected_amount', 'currency', 'session_id', 'ip_address',
        'user_agent', 'landing_url', 'status', 'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'expected_amount' => 'decimal:2',
            'converted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }
}
