<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\Grantee;
use App\Models\ArchivedStudent;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SemesterUpdateNotification;

class ScholarshipManagementController extends Controller
{
    /**
     * Show scholarships page
     */
    public function index()
    {
        // Get current semester and academic year from system settings
        $currentSemester = SystemSetting::where('key', 'current_semester')->value('value') ?? '1st Semester';
        $currentAcademicYear = SystemSetting::where('key', 'current_academic_year')->value('value') ?? '2024-2025';

        // Get scholarship statistics in the format expected by the view
        $scholarshipStats = [
            'government' => [
                'name' => 'Government Scholarship',
                'type' => 'Government',
                'active_grantees' => Grantee::where('scholarship_type', 'government')->count(),
                'semester' => $currentSemester,
                'academic_year' => $currentAcademicYear
            ],
            'academic' => [
                'name' => 'Academic Scholarship',
                'type' => 'Academic',
                'active_grantees' => Grantee::where('scholarship_type', 'academic')->count(),
                'semester' => $currentSemester,
                'academic_year' => $currentAcademicYear
            ],
            'employees' => [
                'name' => 'Employee Scholarship',
                'type' => 'Employee',
                'active_grantees' => Grantee::where('scholarship_type', 'employees')->count(),
                'semester' => $currentSemester,
                'academic_year' => $currentAcademicYear
            ],
            'private' => [
                'name' => 'Private Scholarship',
                'type' => 'Private',
                'active_grantees' => Grantee::where('scholarship_type', 'private')->count(),
                'semester' => $currentSemester,
                'academic_year' => $currentAcademicYear
            ]
        ];

        // Get benefactor statistics for government scholarships
        $benefactorStats = [
            'CHED' => Grantee::where('scholarship_type', 'government')
                ->where('government_benefactor_type', 'CHED')->count(),
            'DOST' => Grantee::where('scholarship_type', 'government')
                ->where('government_benefactor_type', 'DOST')->count(),
            'DSWD' => Grantee::where('scholarship_type', 'government')
                ->where('government_benefactor_type', 'DSWD')->count(),
            'DOLE' => Grantee::where('scholarship_type', 'government')
                ->where('government_benefactor_type', 'DOLE')->count(),
        ];

        return view('admin.scholarships', [
            'currentSemester' => $currentSemester,
            'currentAcademicYear' => $currentAcademicYear,
            'scholarshipStats' => $scholarshipStats,
            'benefactorStats' => $benefactorStats
        ]);
    }

    /**
     * Add new scholarship
     */
    public function addScholarship(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        try {
            $scholarship = Scholarship::create([
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'benefits' => $request->benefits,
                'application_deadline' => $request->application_deadline,
                'is_active' => $request->has('is_active'),
                'created_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scholarship added successfully',
                'data' => $scholarship
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding scholarship: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding scholarship: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update semester
     */
    public function updateSemester(Request $request)
    {
        $request->validate([
            'current_semester' => 'required|string',
            'new_semester' => 'required|string|different:current_semester'
        ]);

        try {
            DB::beginTransaction();

            // Get all current grantees
            $allGrantees = Grantee::all();
            $archivedCount = 0;

            // Archive all current grantees to masterlist
            foreach ($allGrantees as $grantee) {
                try {
                    ArchivedStudent::create([
                        'original_application_id' => $grantee->original_application_id,
                        'student_id' => $grantee->student_id,
                        'first_name' => $grantee->first_name,
                        'last_name' => $grantee->last_name,
                        'email' => $grantee->email,
                        'contact_number' => $grantee->contact_number,
                        'course' => $grantee->course ?: 'N/A',
                        'department' => $grantee->department ?: 'N/A',
                        'year_level' => $grantee->year_level ?: 'N/A',
                        'gwa' => $grantee->gwa ?: $grantee->current_gwa ?: 0,
                        'scholarship_type' => $grantee->scholarship_type,
                        'archived_semester' => $request->current_semester,
                        'archived_academic_year' => $grantee->current_academic_year ?: '2024-2025',
                        'archive_type' => $grantee->status === 'Inactive' ? 'inactive' : 'masterlist',
                        'remarks' => $grantee->status === 'Inactive' ? $grantee->remarks : null,
                        'archived_at' => now(),
                        'archived_by' => Auth::user() ? Auth::user()->name : 'Admin'
                    ]);

                    $archivedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to archive grantee during semester update', [
                        'grantee_id' => $grantee->grantee_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Delete all grantees from the current table
            Grantee::truncate();

            // Update system settings
            SystemSetting::updateOrCreate(
                ['key' => 'current_semester'],
                ['value' => $request->new_semester]
            );

            // Send email notifications to all archived grantees about semester update
            if ($archivedCount > 0) {
                foreach ($allGrantees as $grantee) {
                    try {
                        $studentName = $grantee->first_name . ' ' . $grantee->last_name;
                        $studentEmail = $grantee->email;

                        if ($studentEmail) {
                            Mail::to($studentEmail)->send(new SemesterUpdateNotification(
                                $studentName,
                                $studentEmail,
                                $request->new_semester,
                                null, // No academic year change
                                'semester'
                            ));

                            Log::info('Semester update email sent successfully', [
                                'student_id' => $grantee->student_id,
                                'email' => $studentEmail,
                                'new_semester' => $request->new_semester
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send semester update email', [
                            'student_id' => $grantee->student_id,
                            'email' => $grantee->email ?? 'N/A',
                            'error' => $e->getMessage()
                        ]);
                        // Continue with other emails even if one fails
                    }
                }
            }

            DB::commit();

            Log::info('Semester updated successfully', [
                'old_semester' => $request->current_semester,
                'new_semester' => $request->new_semester,
                'archived_count' => $archivedCount,
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Semester updated successfully! {$archivedCount} grantees have been archived and email notifications sent.",
                'archived_count' => $archivedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Semester update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update semester: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update academic year
     */
    public function updateAcademicYear(Request $request)
    {
        $request->validate([
            'current_year' => 'required|string',
            'new_year' => 'required|string|different:current_year'
        ]);

        try {
            DB::beginTransaction();

            // Get all current grantees
            $allGrantees = Grantee::all();
            $archivedCount = 0;

            // Archive all current grantees to masterlist
            foreach ($allGrantees as $grantee) {
                try {
                    ArchivedStudent::create([
                        'original_application_id' => $grantee->original_application_id,
                        'student_id' => $grantee->student_id,
                        'first_name' => $grantee->first_name,
                        'last_name' => $grantee->last_name,
                        'email' => $grantee->email,
                        'contact_number' => $grantee->contact_number,
                        'course' => $grantee->course ?: 'N/A',
                        'department' => $grantee->department ?: 'N/A',
                        'year_level' => $grantee->year_level ?: 'N/A',
                        'gwa' => $grantee->gwa ?: $grantee->current_gwa ?: 0,
                        'scholarship_type' => $grantee->scholarship_type,
                        'archived_semester' => $grantee->current_semester ?: '2nd Semester',
                        'archived_academic_year' => $request->current_year,
                        'archive_type' => $grantee->status === 'Inactive' ? 'inactive' : 'masterlist',
                        'remarks' => $grantee->status === 'Inactive' ? $grantee->remarks : null,
                        'archived_at' => now(),
                        'archived_by' => Auth::user() ? Auth::user()->name : 'Admin'
                    ]);

                    $archivedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to archive grantee during academic year update', [
                        'grantee_id' => $grantee->grantee_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Delete all grantees from the current table
            Grantee::truncate();

            // Update system settings
            SystemSetting::updateOrCreate(
                ['key' => 'current_academic_year'],
                ['value' => $request->new_year]
            );

            // Reset semester to 1st Semester for new academic year
            SystemSetting::updateOrCreate(
                ['key' => 'current_semester'],
                ['value' => '1st Semester']
            );

            // Send email notifications to all archived grantees about academic year update
            if ($archivedCount > 0) {
                foreach ($allGrantees as $grantee) {
                    try {
                        $studentName = $grantee->first_name . ' ' . $grantee->last_name;
                        $studentEmail = $grantee->email;

                        if ($studentEmail) {
                            Mail::to($studentEmail)->send(new SemesterUpdateNotification(
                                $studentName,
                                $studentEmail,
                                '1st Semester', // Reset to 1st semester
                                $request->new_year,
                                'academic_year'
                            ));

                            Log::info('Academic year update email sent successfully', [
                                'student_id' => $grantee->student_id,
                                'email' => $studentEmail,
                                'new_academic_year' => $request->new_year
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send academic year update email', [
                            'student_id' => $grantee->student_id,
                            'email' => $grantee->email ?? 'N/A',
                            'error' => $e->getMessage()
                        ]);
                        // Continue with other emails even if one fails
                    }
                }
            }

            DB::commit();

            Log::info('Academic year updated successfully', [
                'old_year' => $request->current_year,
                'new_year' => $request->new_year,
                'archived_count' => $archivedCount,
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Academic year updated successfully! {$archivedCount} grantees have been archived and email notifications sent. Semester reset to 1st Semester.",
                'archived_count' => $archivedCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Academic year update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update academic year: ' . $e->getMessage()
            ], 500);
        }
    }
}
