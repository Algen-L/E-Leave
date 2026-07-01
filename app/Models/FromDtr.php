<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FromDtr extends Model
{
    protected $table = 'fromdtr';

    protected $fillable = [
        'employee_number',
        'total_minutes',
        'date',
        'is_processed',
        'processed_at',
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'processed_at' => 'datetime',
        'date' => 'date',
    ];

    /**
     * Get the user that owns this DTR record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_number', 'employee_number');
    }
}
