<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    public const REQUESTED = 'requested';
    public const APPROVED = 'approved';
    public const PROCESSING = 'processing';
    public const PAID = 'paid';
    public const REJECTED = 'rejected';

    protected $fillable = [
        'user_id', 'amount', 'currency', 'method', 'payout_details',
        'status', 'reference', 'admin_note', 'processed_by', 'processed_at',
        'gateway', 'gateway_payout_id', 'gateway_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payout_details' => 'encrypted:array',
            'gateway_response' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
