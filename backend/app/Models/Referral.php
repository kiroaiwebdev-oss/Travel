<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    public const PENDING = 'pending';
    public const QUALIFIED = 'qualified';
    public const REWARDED = 'rewarded';
    public const REJECTED = 'rejected';

    protected $fillable = [
        'referrer_id', 'referee_id', 'code', 'status', 'reward_amount',
        'ip_address', 'signup_fingerprint', 'qualified_at', 'rewarded_at',
    ];

    protected function casts(): array
    {
        return [
            'reward_amount' => 'decimal:2',
            'qualified_at' => 'datetime',
            'rewarded_at' => 'datetime',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_id');
    }
}
