<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveCreditPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_type_id',
        'accrual_rate',
        'accrual_period',
        'expiration_rule',
        'expiration_date',
        'max_credits'
    ];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
