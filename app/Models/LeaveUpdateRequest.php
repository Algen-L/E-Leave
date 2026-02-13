<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveUpdateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_user_id',
        'requester_id',
        'leave_type_id', // Added this as per migration
        'reason',
        'status', 
        'approver_id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
