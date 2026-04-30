<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'type',
        'from_name',
        'from_email',
        'to_name',
        'to_email',
        'cc',
        'bcc',
        'subject',
        'body',
        'body_html',
        'is_read',
        'is_starred',
        'is_important',
        'sent_at',
        'attachments',
        'message_id',
        'in_reply_to',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'is_important' => 'boolean',
        'sent_at' => 'datetime',
        'attachments' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    public function scopeInbox($query, int $userId)
    {
        return $query->where('type', 'inbox')
            ->where('created_by', $userId);
    }

    public function scopeSent($query, int $userId)
    {
        return $query->where('type', 'sent')
            ->where('created_by', $userId);
    }

    public function scopeDrafts($query, int $userId)
    {
        return $query->where('type', 'draft')
            ->where('created_by', $userId);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    public function scopeImportant($query)
    {
        return $query->where('is_important', true);
    }

    public function getExcerpt(int $length = 100): string
    {
        return substr(strip_tags($this->body), 0, $length) . '...';
    }
}
