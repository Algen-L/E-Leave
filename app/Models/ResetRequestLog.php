<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetRequestLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'email',
        'type',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];

    /**
     * Log a reset request
     */
    public static function log(string $email, string $type = 'request'): static
    {
        return static::create([
            'email' => $email,
            'type' => $type,
        ]);
    }

    /**
     * Log a resend request
     */
    public static function logResend(string $email): static
    {
        return static::log($email, 'resend');
    }

    /**
     * Get hourly request count for an email
     */
    public static function getHourlyCount(string $email): int
    {
        return static::where('email', $email)
            ->where('requested_at', '>', now()->subHour())
            ->count();
    }

    /**
     * Check if rate limit exceeded (3 per hour)
     */
    public static function isRateLimitExceeded(string $email, int $limit = 3): bool
    {
        return static::getHourlyCount($email) >= $limit;
    }
}
