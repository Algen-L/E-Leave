<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'category',
        'name',
    ];

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get offices grouped by category
     */
    public static function getGroupedByCategory(): array
    {
        return static::all()->groupBy('category')->toArray();
    }

    /**
     * Get all OSDS offices
     */
    public static function getOSDS()
    {
        return static::byCategory('OSDS')->get();
    }

    /**
     * Get all SGOD offices
     */
    public static function getSGOD()
    {
        return static::byCategory('SGOD')->get();
    }

    /**
     * Get all CID offices
     */
    public static function getCID()
    {
        return static::byCategory('CID')->get();
    }
}
