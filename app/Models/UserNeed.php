<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNeed extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'need_text',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns this need
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a need for a user
     */
    public static function createForUser(int $userId, string $needText, ?string $description = null): static
    {
        return static::create([
            'user_id' => $userId,
            'need_text' => $needText,
            'description' => $description,
        ]);
    }

    /**
     * Scope for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
