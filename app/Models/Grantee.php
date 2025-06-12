<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grantee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Basic information
        'application_id',
        'scholarship_type',
        'student_id',
        'government_benefactor_type',
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
        'gwa',
        'semester',
        'academic_year',

        // Government specific fields
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
        'disability',
        'indigenous',

        // Contact information
        'contact_number',
        'email',
        'address',

        // Employee scholarship fields
        'employee_name',
        'employee_relationship',
        'employee_department',
        'employee_position',

        // Alumni scholarship fields
        'scholarship_name',
        'other_scholarship',

        // Documents
        'documents',

        // Grantee specific fields
        'approved_date',
        'approved_by',
        'status',
        'scholarship_start_date',
        'scholarship_end_date',
        'scholarship_amount',
        'special_conditions',
        'notes',

        // Renewal tracking
        'is_renewable',
        'renewal_count',
        'next_renewal_date',

        // Performance tracking
        'current_gwa',
        'performance_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birthdate' => 'date',
        'approved_date' => 'date',
        'scholarship_start_date' => 'date',
        'scholarship_end_date' => 'date',
        'next_renewal_date' => 'date',
        'scholarship_amount' => 'decimal:2',
        'is_renewable' => 'boolean',
        'renewal_count' => 'integer',
        'documents' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];



    /**
     * Get the full name of the grantee.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    /**
     * Get the formatted scholarship type.
     */
    public function getFormattedScholarshipTypeAttribute(): string
    {
        return match ($this->scholarship_type) {
            'government' => 'Government Scholarship',
            'academic' => 'Academic Scholarship',
            'employees' => 'Employee\'s Scholarship',
            'alumni' => 'Alumni Scholarship',
            default => ucfirst($this->scholarship_type) . ' Scholarship'
        };
    }

    /**
     * Get the complete address.
     */
    public function getCompleteAddressAttribute(): string
    {
        if ($this->address) {
            return $this->address;
        }

        $addressParts = array_filter([
            $this->street,
            $this->barangay,
            $this->city,
            $this->province,
            $this->zipcode
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Scope for active grantees.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for specific scholarship type.
     */
    public function scopeByScholarshipType($query, $type)
    {
        return $query->where('scholarship_type', $type);
    }

    /**
     * Scope for renewable scholarships.
     */
    public function scopeRenewable($query)
    {
        return $query->where('is_renewable', true);
    }

    /**
     * Check if the scholarship is due for renewal.
     */
    public function isDueForRenewal(): bool
    {
        return $this->is_renewable &&
            $this->next_renewal_date &&
            $this->next_renewal_date <= now();
    }

    /**
     * Get the original application.
     */
    public function application()
    {
        return $this->belongsTo(ScholarshipApplication::class, 'application_id', 'application_id');
    }
}
