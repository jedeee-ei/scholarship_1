<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'code',
        'title',
        'units',
        'year_level',
        'semester',
        'description',
        'prerequisites',
        'is_active'
    ];

    protected $casts = [
        'units' => 'integer',
        'year_level' => 'integer',
        'prerequisites' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the course that owns the subject.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope a query to only include active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query for a specific semester.
     */
    public function scopeForSemester($query, $yearLevel, $semester)
    {
        return $query->where('year_level', $yearLevel)
                    ->where('semester', $semester);
    }

    /**
     * Get the full subject name (code + title).
     */
    public function getFullNameAttribute()
    {
        return $this->code . ' - ' . $this->title;
    }
}
