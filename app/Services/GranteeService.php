<?php

namespace App\Services;

use App\Models\Grantee;
use App\Models\ScholarshipApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GranteeService
{
    /**
     * Create a grantee from an approved application.
     *
     * @param ScholarshipApplication $application
     * @param string $approvedBy
     * @param array $additionalData
     * @return Grantee
     */
    public function createGranteeFromApplication(
        ScholarshipApplication $application,
        string $approvedBy,
        array $additionalData = []
    ): Grantee {
        return DB::transaction(function () use ($application, $approvedBy, $additionalData) {
            // Create grantee record
            $grantee = Grantee::create([
                // Copy all application data
                'application_id' => $application->application_id,
                'scholarship_type' => $application->scholarship_type,
                'government_benefactor_type' => $application->government_benefactor_type,
                'student_id' => $application->student_id,
                'last_name' => $application->last_name,
                'first_name' => $application->first_name,
                'middle_name' => $application->middle_name,
                'sex' => $application->sex,
                'birthdate' => $application->birthdate,
                'education_stage' => $application->education_stage,
                'department' => $application->department,
                'course' => $application->course,
                'year_level' => $application->year_level,
                'grade_level' => $application->grade_level,
                'strand' => $application->strand,
                'gwa' => $application->gwa,
                'semester' => $application->semester,
                'academic_year' => $application->academic_year,

                // CHED specific fields
                'father_last_name' => $application->father_last_name,
                'father_first_name' => $application->father_first_name,
                'father_middle_name' => $application->father_middle_name,
                'mother_last_name' => $application->mother_last_name,
                'mother_first_name' => $application->mother_first_name,
                'mother_middle_name' => $application->mother_middle_name,
                'street' => $application->street,
                'barangay' => $application->barangay,
                'city' => $application->city,
                'province' => $application->province,
                'zipcode' => $application->zipcode,
                'disability' => $application->disability,
                'indigenous' => $application->indigenous,

                // Contact information
                'contact_number' => $application->contact_number,
                'email' => $application->email,
                'address' => $application->address,

                // Employee scholarship fields
                'employee_name' => $application->employee_name,
                'employee_relationship' => $application->employee_relationship,
                'employee_department' => $application->employee_department,
                'employee_position' => $application->employee_position,

                // Private scholarship fields
                'scholarship_name' => $application->scholarship_name,
                'other_scholarship' => $application->other_scholarship,

                // Documents
                'documents' => $application->documents,

                // Grantee specific fields
                'approved_date' => now(),
                'approved_by' => $approvedBy,
                'status' => 'Active',
                'scholarship_start_date' => $additionalData['scholarship_start_date'] ?? now(),
                'scholarship_end_date' => $additionalData['scholarship_end_date'] ?? null,
                'scholarship_amount' => $additionalData['scholarship_amount'] ?? null,
                'special_conditions' => $additionalData['special_conditions'] ?? null,
                'notes' => $additionalData['notes'] ?? null,

                // Renewal tracking
                'is_renewable' => $additionalData['is_renewable'] ?? $this->isRenewableScholarship($application->scholarship_type),
                'renewal_count' => 0,
                'next_renewal_date' => $additionalData['next_renewal_date'] ?? $this->calculateNextRenewalDate($application->scholarship_type),

                // Performance tracking
                'current_gwa' => $application->gwa,
                'performance_notes' => null,
            ]);

            // Update application status to indicate it's been processed
            $application->update(['status' => 'Approved']);

            Log::info('Grantee created from application', [
                'grantee_id' => $grantee->grantee_id,
                'application_id' => $application->application_id,
                'approved_by' => $approvedBy
            ]);

            return $grantee;
        });
    }

    /**
     * Determine if a scholarship type is renewable.
     *
     * @param string $scholarshipType
     * @return bool
     */
    private function isRenewableScholarship(string $scholarshipType): bool
    {
        return in_array($scholarshipType, ['ched', 'academic']);
    }

    /**
     * Calculate the next renewal date based on scholarship type.
     *
     * @param string $scholarshipType
     * @return \Carbon\Carbon|null
     */
    private function calculateNextRenewalDate(string $scholarshipType)
    {
        if (!$this->isRenewableScholarship($scholarshipType)) {
            return null;
        }

        // Most scholarships renew annually
        return now()->addYear();
    }

    /**
     * Update grantee status.
     *
     * @param Grantee $grantee
     * @param string $status
     * @param string|null $notes
     * @return bool
     */
    public function updateGranteeStatus(Grantee $grantee, string $status, ?string $notes = null): bool
    {
        $validStatuses = ['Active', 'Inactive', 'Graduated', 'Terminated'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }

        return $grantee->update([
            'status' => $status,
            'notes' => $notes ? ($grantee->notes ? $grantee->notes . "\n" . $notes : $notes) : $grantee->notes
        ]);
    }

    /**
     * Process scholarship renewal.
     *
     * @param Grantee $grantee
     * @param array $renewalData
     * @return bool
     */
    public function renewScholarship(Grantee $grantee, array $renewalData = []): bool
    {
        if (!$grantee->is_renewable) {
            throw new \InvalidArgumentException("This scholarship is not renewable");
        }

        return $grantee->update([
            'renewal_count' => $grantee->renewal_count + 1,
            'next_renewal_date' => $this->calculateNextRenewalDate($grantee->scholarship_type),
            'current_gwa' => $renewalData['current_gwa'] ?? $grantee->current_gwa,
            'performance_notes' => $renewalData['performance_notes'] ?? null,
            'scholarship_amount' => $renewalData['scholarship_amount'] ?? $grantee->scholarship_amount,
        ]);
    }

    /**
     * Get grantees by scholarship type.
     *
     * @param string $scholarshipType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGranteesByType(string $scholarshipType)
    {
        return Grantee::byScholarshipType($scholarshipType)
            ->orderBy('approved_date', 'desc')
            ->get();
    }

    /**
     * Get active grantees.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveGrantees()
    {
        return Grantee::active()
            ->orderBy('approved_date', 'desc')
            ->get();
    }

    /**
     * Get grantees due for renewal.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGranteesDueForRenewal()
    {
        return Grantee::renewable()
            ->where('next_renewal_date', '<=', now())
            ->where('status', 'Active')
            ->orderBy('next_renewal_date', 'asc')
            ->get();
    }
}
