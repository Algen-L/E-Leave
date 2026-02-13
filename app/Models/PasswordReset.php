<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    public $timestamps = false;
    
    protected $table = 'password_resets';

    protected $fillable = [
        'username',
        'token',
        'expires_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Generate a new token for a user
     */
    public static function generateToken(string $username, int $expiresInMinutes = 5): static
    {
        // Delete existing tokens for this user
        static::where('username', $username)->delete();

        return static::create([
            'username' => $username,
            'token' => sprintf("%06d", mt_rand(100000, 999999)),
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);
    }

    /**
     * Verify token
     */
    public static function verifyToken(string $username, string $token): ?static
    {
        return static::where('username', $username)
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Get active token for a user
     */
    public static function getActiveToken(string $username): ?static
    {
        return static::where('username', $username)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();
    }

    /**
     * Check if token is valid
     */
    public function isValid(): bool
    {
        return $this->expires_at > now();
    }

    /**
     * Increment attempts
     */
    public function incrementAttempts(): bool
    {
        return $this->increment('attempts');
    }

    /**
     * Delete token
     */
    public static function deleteForUser(string $username): int
    {
        return static::where('username', $username)->delete();
    }
}
