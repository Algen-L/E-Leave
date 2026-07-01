<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message',
        'is_read',
        'link_url',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the sender of the notification
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of the notification
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Send a notification
     */
    public static function send(int $senderId, int $recipientId, string $message, ?string $linkUrl = null): static
    {
        return static::create([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'message' => $message,
            'link_url' => $linkUrl,
        ]);
    }

    /**
     * Send a broadcast notification to all users
     */
    public static function broadcast(int $senderId, string $message): void
    {
        $userIds = User::where('id', '!=', $senderId)->pluck('id');
        
        foreach ($userIds as $recipientId) {
            static::send($senderId, $recipientId, $message);
        }
    }

    /**
     * Mark as read
     */
    public function markAsRead(): bool
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsReadForUser(int $userId): int
    {
        return static::where('recipient_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for a specific recipient
     */
    public function scopeForRecipient($query, int $recipientId)
    {
        return $query->where('recipient_id', $recipientId);
    }

    /**
     * Get unread count for a user
     */
    public static function getUnreadCount(int $userId): int
    {
        return static::forRecipient($userId)->unread()->count();
    }

    /**
     * Get unread notifications for a user with sender details
     */
    public static function getUnreadForUser(int $userId)
    {
        return static::forRecipient($userId)
            ->unread()
            ->with('sender:id,full_name,profile_picture')
            ->latest('created_at')
            ->get();
    }
}
