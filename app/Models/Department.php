<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the courses for the department.
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get active courses for the department.
     */
    public function activeCourses()
    {
        return $this->hasMany(Course::class)->where('is_active', true);
    }

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
