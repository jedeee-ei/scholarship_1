<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = [
        'name',
        'type',
        'semester',
        'academic_year',
        'description',
        'requirements',
        'benefits',
        'application_deadline',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get active scholarships
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
