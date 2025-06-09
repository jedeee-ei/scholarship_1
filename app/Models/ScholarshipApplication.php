<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        //Common Fields
        'application_id',
        'scholarship_type',
        'scholarship_subtype',
        'government_benefactor_type',
        'student_id',
        'last_name',
        'first_name',
        'middle_name',
        'sex',
        'birthdate',
        'education_stage',
        'department',
        'course',
        'year_level',
        'grade_level',
        'strand',

        //PL and DL
        'gwa',
        'subject_grades',
        'semester',
        'academic_year',

        //Government
        'father_last_name',
        'father_first_name',
        'father_middle_name',
        'mother_last_name',
        'mother_first_name',
        'mother_middle_name',
        'street',
        'barangay',
        'city',
        'province',
        'zipcode',
        'address',
        'disability',
        'indigenous',
        'contact_number',
        'email',

        //Employees
        'employee_name',
        'employee_relationship',
        'employee_department',
        'employee_position',
        'scholarship_name',
        'other_scholarship',
        'documents',
        'status',
    ];

    protected $casts = [
        'documents' => 'array',
        'subject_grades' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($application) {
            // Generate a unique application ID if not provided
            if (!$application->application_id) {
                $application->application_id = 'SCH-' . strtoupper(substr(uniqid(), -6));
            }

            // Set default status if not provided
            if (!$application->status) {
                $application->status = 'Pending Review';
            }
        });
    }
}
