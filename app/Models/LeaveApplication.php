<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LeaveApplication extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type_id',
        'date_filing',
        'start_date',
        'end_date',
        'dates',
        'days_applied',
        'commutation',
        'status',
        'recommending_officer_id',
        'approving_officer_id',
        'hr_verifier_id',
        'hr_verified_at',
        'recommended_at',
        'approved_at',
        'rejected_at',
        'rejection_remarks',
        'days_with_pay',
        'days_without_pay',
        'others_remarks',
    ];

    protected $casts = [
        'date_filing' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'dates' => 'array',
        'hr_verified_at' => 'datetime',
        'recommended_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function recommendingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recommending_officer_id');
    }

    public function approvingOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approving_officer_id');
    }

    public function hrVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_verifier_id');
    }

    public function details(): HasOne
    {
        return $this->hasOne(LeaveDetailsForm6::class);
    }
}
