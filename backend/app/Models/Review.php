<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    protected $fillable = ['user_id', 'name', 'location', 'rating', 'type', 'message', 'status', 'is_featured'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved(Builder $q): Builder
    {
        return $q->where('status', self::APPROVED);
    }

    public function scopeReviews(Builder $q): Builder
    {
        return $q->where('type', 'review');
    }
}
