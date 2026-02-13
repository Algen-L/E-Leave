<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            // Only update full_name if parts are present.
            if (!empty($user->first_name) || !empty($user->last_name)) {
                $parts = [];
                if (!empty($user->first_name)) $parts[] = $user->first_name;
                if (!empty($user->middle_name)) $parts[] = $user->middle_name;
                if (!empty($user->last_name)) $parts[] = $user->last_name;
                
                $user->full_name = implode(' ', $parts);
                
                // Also update the 'name' field if it is not set or generic
                if (empty($user->name) || $user->name == $user->getOriginal('full_name')) {
                    $user->name = $user->full_name;
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'gmail',
        'password',
        'first_name',
        'middle_name',
        'last_name',
        'full_name',
        'office_station',
        'position',
        'salary',
        'recommending_approver', // Legacy?
        'final_approver', // Legacy?
        'recommending_officer_id', // New relational
        'approving_officer_id', // New relational
        'employee_number',
        'rating_period',
        'area_of_specialization',
        'age',
        'sex',
        'role',
        'profile_picture',
        'is_active',
        'created_by',
        'passkey',
        'passkey_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'passkey',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'passkey_expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user's email attribute (alias for gmail for Laravel compatibility)
     */
    public function getEmailAttribute(): ?string
    {
        return $this->gmail;
    }

    /**
     * Role check helpers
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isHR(): bool
    {
        return in_array($this->role, ['hr', 'head_hr']);
    }

    public function isHeadHR(): bool
    {
        return $this->role === 'head_hr';
    }

    public function isImmediateHead(): bool
    {
        return $this->role === 'immediate_head';
    }

    /**
     * Relationships
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    public function receivedNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_id');
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'recipient_id')->where('is_read', false);
    }

    public function userNeeds(): HasMany
    {
        return $this->hasMany(UserNeed::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for excluding roles
     */
    public function scopeExcludeRoles($query, array $roles)
    {
        return $query->whereNotIn('role', $roles);
    }

    public function recommendingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommending_officer_id');
    }

    public function approvingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approving_officer_id');
    }
}
