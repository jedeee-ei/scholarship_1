<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'code',
        'name',
        'description',
        'duration_years',
        'is_active'
    ];

    protected $casts = [
        'duration_years' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the department that owns the course.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the subjects for the course.
     */
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    /**
     * Get active subjects for the course.
     */
    public function activeSubjects()
    {
        return $this->hasMany(Subject::class)->where('is_active', true);
    }

    /**
     * Scope a query to only include active courses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
