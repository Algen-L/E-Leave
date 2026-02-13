<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveDetailsForm6 extends Model
{
    protected $table = 'leave_details_form6';

    protected $fillable = [
        'leave_application_id',
        'leave_type_name',
        'vacation_loc_type',
        'vacation_loc_details',
        'sick_loc_type',
        'sick_illness',
        'women_illness',
        'study_type',
        'study_details',
        'other_purpose',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LeaveApplication::class, 'leave_application_id');
    }
}
