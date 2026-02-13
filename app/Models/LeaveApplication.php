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
    ];

    protected $casts = [
        'date_filing' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'dates' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function details(): HasOne
    {
        return $this->hasOne(LeaveDetailsForm6::class);
    }
}
