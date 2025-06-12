<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\Grantee;
use App\Models\ScholarshipApplication;
use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        $currentSemester = SystemSetting::get('current_semester', '1st Semester');
        $currentAcademicYear = SystemSetting::get('current_academic_year', '2024-2025');
        $applicationStatus = SystemSetting::get('application_status', 'closed');

        return view('admin.settings', compact('currentSemester', 'currentAcademicYear', 'applicationStatus'));
    }

    /**
     * Get current semester and academic year
     */
    public function getCurrentSemesterYear()
    {
        $currentSemester = SystemSetting::get('current_semester', '1st Semester');
        $currentAcademicYear = SystemSetting::get('current_academic_year', '2024-2025');
        $applicationStatus = SystemSetting::get('application_status', 'closed');

        return response()->json([
            'current_semester' => $currentSemester,
            'current_academic_year' => $currentAcademicYear,
            'application_status' => $applicationStatus
        ]);
    }

    /**
     * Save settings
     */
    public function saveSettings(Request $request)
    {
        try {
            $request->validate([
                'current_semester' => 'required|string',
                'current_academic_year' => 'required|string',
                'application_status' => 'required|string|in:open,closed'
            ]);

            // Update system settings
            SystemSetting::set('current_semester', $request->current_semester);
            SystemSetting::set('current_academic_year', $request->current_academic_year);
            SystemSetting::set('application_status', $request->application_status);

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update semester
     */
    public function updateSemester(Request $request)
    {
        try {
            $currentSemester = SystemSetting::get('current_semester', '1st Semester');
            $nextSemester = $currentSemester === '1st Semester' ? '2nd Semester' : '1st Semester';

            // Archive current grantees
            $this->archiveCurrentGrantees($currentSemester, SystemSetting::get('current_academic_year', '2024-2025'));

            // Update semester
            SystemSetting::set('current_semester', $nextSemester);

            return response()->json([
                'success' => true,
                'message' => "Semester updated to {$nextSemester}. Current grantees have been archived."
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating semester: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating semester: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update academic year
     */
    public function updateAcademicYear(Request $request)
    {
        try {
            $currentYear = SystemSetting::get('current_academic_year', '2024-2025');
            $yearParts = explode('-', $currentYear);
            $nextYear = (intval($yearParts[0]) + 1) . '-' . (intval($yearParts[1]) + 1);

            // Archive current grantees
            $this->archiveCurrentGrantees(SystemSetting::get('current_semester', '1st Semester'), $currentYear);

            // Update academic year and reset to 1st semester
            SystemSetting::set('current_academic_year', $nextYear);
            SystemSetting::set('current_semester', '1st Semester');

            return response()->json([
                'success' => true,
                'message' => "Academic year updated to {$nextYear}. Semester reset to 1st Semester. Current grantees have been archived."
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating academic year: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating academic year: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Archive current grantees
     */
    private function archiveCurrentGrantees($semester, $academicYear)
    {
        $grantees = Grantee::where('status', 'Active')->get();

        foreach ($grantees as $grantee) {
            // Create archive record
            Archive::create([
                'original_application_id' => $grantee->id ?? 'N/A',
                'student_id' => $grantee->student_id,
                'first_name' => $grantee->first_name,
                'last_name' => $grantee->last_name,
                'email' => $grantee->email,
                'contact_number' => $grantee->contact_number,
                'course' => $grantee->course ?? 'N/A',
                'department' => $grantee->department,
                'year_level' => $grantee->year_level,
                'gwa' => $grantee->gwa,
                'scholarship_type' => $grantee->scholarship_type,
                'archived_semester' => $semester,
                'archived_academic_year' => $academicYear,
                'archive_type' => 'masterlist',
                'remarks' => 'Archived due to semester/year update',
                'archived_at' => now(),
                'archived_by' => 'System - Semester/Year Update'
            ]);

            // Send notification email if email exists
            if ($grantee->email) {
                try {
                    // You can implement email notification here
                    // Mail::to($grantee->email)->send(new ArchiveNotification($grantee));
                } catch (\Exception $e) {
                    Log::warning('Failed to send archive notification to ' . $grantee->email . ': ' . $e->getMessage());
                }
            }
        }

        // Remove grantees from active list
        Grantee::where('status', 'Active')->delete();

        // Clear pending applications for the current semester/year
        ScholarshipApplication::where('semester', $semester)
            ->where('academic_year', $academicYear)
            ->whereIn('status', ['Pending Review', 'Under Review', 'Pending'])
            ->delete();
    }
}
