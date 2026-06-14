<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SearchLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'category', 'origin', 'destination', 'depart_date',
        'return_date', 'travellers', 'filters', 'result_count', 'response_ms',
        'cache_hit', 'session_id', 'ip_address', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'depart_date' => 'date',
            'return_date' => 'date',
            'cache_hit' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
