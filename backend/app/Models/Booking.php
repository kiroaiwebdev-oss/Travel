<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    public const REFUNDED = 'refunded';

    protected $fillable = [
        'user_id', 'provider_id', 'booking_click_id', 'category', 'external_ref',
        'title', 'details', 'amount', 'commission_amount', 'currency',
        'status', 'booked_at', 'travel_date',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'booked_at' => 'datetime',
            'travel_date' => 'datetime',
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

    public function click(): BelongsTo
    {
        return $this->belongsTo(BookingClick::class, 'booking_click_id');
    }

    public function cashback(): HasOne
    {
        return $this->hasOne(Cashback::class);
    }
}
