<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FromAlpas extends Model
{
    protected $table = 'fromalpas';

    protected $fillable = [
        'employee_no',
        'full_name',
        'leave_credits',
        'source_system',
        'source_reference',
        'processed_credits',
    ];
}
