<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cashback extends Model
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const WITHDRAWABLE = 'withdrawable';
    public const PAID = 'paid';
    public const REJECTED = 'rejected';

    protected $fillable = [
        'user_id', 'booking_id', 'provider_id', 'cashback_rule_id', 'category',
        'booking_amount', 'commission_amount', 'amount', 'currency', 'status',
        'confirmed_at', 'matures_at', 'rejected_at', 'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'booking_amount' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'matures_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function rule(): BelongsTo
    {
        return $this->belongsTo(CashbackRule::class, 'cashback_rule_id');
    }

    public function scopePending($q)
    {
        return $q->where('status', self::PENDING);
    }

    public function scopeMatured($q)
    {
        return $q->where('status', self::CONFIRMED)
            ->whereNotNull('matures_at')
            ->where('matures_at', '<=', now());
    }
}
