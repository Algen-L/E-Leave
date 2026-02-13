<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveCreditAuditLog extends Model
{
    protected $fillable = [
        'actor_id',
        'target_user_id',
        'action',
        'leave_type_name',
        'previous_value',
        'new_value',
        'reason'
    ];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
