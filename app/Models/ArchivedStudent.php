<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivedStudent extends Model
{
    protected $fillable = [
        'original_application_id',
        'student_id',
        'first_name',
        'last_name',
        'email',
        'contact_number',
        'course',
        'department',
        'year_level',
        'gwa',
        'scholarship_type',
        'archived_semester',
        'archived_academic_year',
        'archived_at',
        'archived_by'
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'gwa' => 'decimal:2'
    ];
}
