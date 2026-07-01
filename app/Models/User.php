<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
                if (!empty($user->first_name))
                    $parts[] = $user->first_name;
                if (!empty($user->middle_name))
                    $parts[] = $user->middle_name;
                if (!empty($user->last_name))
                    $parts[] = $user->last_name;

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
        'esignature', // E-Signature path
        'employee_number',
        'rating_period',
        'area_of_specialization',
        'age',
        'sex',
        'role',
        'profile_picture',
        'is_active',
        'dtr_minute_balance',
        'created_by',
        'passkey',
        'passkey_expires_at',
        'secretary_id',
        'department_head_id',
        'is_dept_head',
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
            'is_dept_head' => 'boolean',
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
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($roles): bool
    {
        // Eager load if not loaded to prevent N+1
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }
        $userRoles = $this->roles->pluck('name')->toArray();
        if (is_array($roles)) {
            return !empty(array_intersect($roles, $userRoles));
        }
        return in_array($roles, $userRoles);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isHR(): bool
    {
        return $this->hasRole(['hr', 'head_hr', 'hr_review_officer']);
    }

    public function isHeadHR(): bool
    {
        return $this->hasRole('head_hr');
    }

    public function isRecordPersonnel(): bool
    {
        return $this->hasRole('record_personnel');
    }

    public function isImmediateHead(): bool
    {
        return $this->hasRole('immediate_head');
    }

    public function isASDS(): bool
    {
        return $this->hasRole('asds');
    }

    public function isSDS(): bool
    {
        return $this->hasRole('sds');
    }

    public function isHigherRole(): bool
    {
        return $this->hasRole(['sgod_chief', 'cid_chief', 'ao', 'sds', 'asds']);
    }

    /**
     * Get the display name for the user's role.
     */
    public function getRoleDisplayNameAttribute(): string
    {
        $roleMap = [
            'user' => 'USER',
            'super_admin' => 'SUPER ADMIN',
            'admin' => 'ADMIN',
            'head_hr' => 'HR PERSONNEL',
            'hr' => 'HR STAFF',
            'hr_review_officer' => 'HR REVIEW OFFICER',
            'record_personnel' => 'RECORD PERSONNEL',
            'immediate_head' => 'IMMEDIATE HEAD',
            'asds' => 'ASDS',
            'sds' => 'SDS',
            'sgod_chief' => 'SGOD CHIEF',
            'cid_chief' => 'CID CHIEF',
            'ao' => 'AO',
        ];

        $roleNames = $this->roles->pluck('name')->toArray();
        $displayNames = [];
        foreach ($roleNames as $name) {
            $displayNames[] = $roleMap[$name] ?? strtoupper(str_replace('_', ' ', $name));
        }

        return implode(' + ', $displayNames) ?: 'USER';
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

    public function departmentHead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'department_head_id');
    }

    public function departmentSubordinates(): HasMany
    {
        return $this->hasMany(User::class, 'department_head_id');
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

    /**
     * Get the office station (if any)
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'office_station', 'name');
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    public function isSecretary(): bool
    {
        // A user is a secretary if they are assigned as a secretary to ANY user
        return User::where('secretary_id', $this->id)->exists();
    }

    public function bosses()
    {
        // Users who have this user as their secretary
        return User::where('secretary_id', $this->id)->get();
    }
}
