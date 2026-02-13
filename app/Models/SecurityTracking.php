<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityTracking extends Model
{
    public $timestamps = false;

    protected $table = 'security_tracking';

    protected $fillable = [
        'email',
        'page_visits',
        'otp_requests',
        'otp_inputs',
        'resends',
        'status',
        'is_blocked',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'last_activity' => 'datetime',
    ];

    /**
     * Get or create tracking record for an email
     */
    public static function getOrCreate(string $email): static
    {
        return static::firstOrCreate(['email' => $email]);
    }

    /**
     * Increment page visits
     */
    public function incrementPageVisits(): bool
    {
        return $this->increment('page_visits');
    }

    /**
     * Increment OTP requests
     */
    public function incrementOtpRequests(): bool
    {
        return $this->increment('otp_requests');
    }

    /**
     * Increment OTP inputs
     */
    public function incrementOtpInputs(): bool
    {
        return $this->increment('otp_inputs');
    }

    /**
     * Increment resends
     */
    public function incrementResends(): bool
    {
        return $this->increment('resends');
    }

    /**
     * Block the email
     */
    public function block(): bool
    {
        return $this->update([
            'is_blocked' => true,
            'status' => 'Blocked',
        ]);
    }

    /**
     * Unblock the email
     */
    public function unblock(): bool
    {
        return $this->update([
            'is_blocked' => false,
            'status' => 'Active',
        ]);
    }

    /**
     * Check if blocked
     */
    public function isBlocked(): bool
    {
        return (bool) $this->is_blocked;
    }
}
