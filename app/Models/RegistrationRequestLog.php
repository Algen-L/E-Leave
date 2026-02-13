<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationRequestLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'email',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];

    /**
     * Log a registration request
     */
    public static function log(string $email): static
    {
        return static::create(['email' => $email]);
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
