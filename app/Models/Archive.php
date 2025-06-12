<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $table = 'archived_students';
    
    protected $fillable = [
        'original_application_id',
        'student_id',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'contact_number',
        'course',
        'department',
        'year_level',
        'gwa',
        'scholarship_type',
        'government_benefactor_type',
        'employee_name',
        'employee_relationship',
        'scholarship_name',
        'archived_semester',
        'archived_academic_year',
        'archive_type',
        'remarks',
        'archived_at',
        'archived_by'
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'gwa' => 'decimal:2'
    ];
}
