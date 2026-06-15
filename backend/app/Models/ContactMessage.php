<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    public const NEW = 'new';
    public const REPLIED = 'replied';
    public const CLOSED = 'closed';

    protected $fillable = [
        'user_id', 'name', 'email', 'subject', 'message',
        'status', 'admin_reply', 'replied_by', 'replied_at', 'ip_address',
    ];

    protected function casts(): array
    {
        return ['replied_at' => 'datetime'];
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
