<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\Grantee;
use App\Models\ArchivedStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\SemesterUpdateNotification;

class SettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        // Get current settings
        $currentSemester = SystemSetting::where('key', 'current_semester')->value('value') ?? '1st Semester';
        $currentAcademicYear = SystemSetting::where('key', 'current_academic_year')->value('value') ?? '2024-2025';

        return view('admin.settings', [
            'currentSemester' => $currentSemester,
            'currentAcademicYear' => $currentAcademicYear
        ]);
    }

    /**
     * Save settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'current_semester' => 'required|string',
            'current_academic_year' => 'required|string'
        ]);

        try {
            // Update or create settings
            if ($request->has('current_semester')) {
                SystemSetting::updateOrCreate(
                    ['key' => 'current_semester'],
                    ['value' => $request->current_semester]
                );
            }

            if ($request->has('current_academic_year')) {
                SystemSetting::updateOrCreate(
                    ['key' => 'current_academic_year'],
                    ['value' => $request->current_academic_year]
                );
            }

            if ($request->has('application_status')) {
                SystemSetting::updateOrCreate(
                    ['key' => 'application_status'],
                    ['value' => $request->application_status]
                );
            }

            Log::info('System settings updated', [
                'semester' => $request->current_semester,
                'academic_year' => $request->current_academic_year,
                'application_status' => $request->application_status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully'
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
     * Get current semester and year
     */
    public function getCurrentSemesterYear()
    {
        $currentSemester = SystemSetting::where('key', 'current_semester')->value('value') ?? '1st Semester';
        $currentAcademicYear = SystemSetting::where('key', 'current_academic_year')->value('value') ?? '2024-2025';
        $applicationStatus = SystemSetting::where('key', 'application_status')->value('value') ?? 'closed';

        return response()->json([
            'current_semester' => $currentSemester,
            'current_academic_year' => $currentAcademicYear,
            'application_status' => $applicationStatus
        ]);
    }

    /**
     * Update settings (alternative method)
     */
    public function updateSettings(Request $request)
    {
        return $this->saveSettings($request);
    }

    /**
     * Update semester
     */
    public function updateSemester(Request $request)
    {
        Log::info('Semester update request received', [
            'request_data' => $request->all(),
            'user' => Auth::user() ? Auth::user()->name : 'Unknown'
        ]);

        $request->validate([
            'current_semester' => 'required|string',
            'new_semester' => 'required|string|different:current_semester'
        ]);

        try {
            DB::beginTransaction();

            // Get all current grantees
            $allGrantees = Grantee::all();
            $archivedCount = 0;

            // Check if there are any grantees to process
            if ($allGrantees->isEmpty()) {
                Log::info('No grantees found to archive during semester update');
            } else {
                // Archive all current grantees to masterlist
                foreach ($allGrantees as $grantee) {
                    try {
                        ArchivedStudent::create([
                            'original_application_id' => $grantee->application_id,
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
                            'archived_academic_year' => $grantee->academic_year ?: $grantee->current_academic_year ?: '2024-2025',
                            'archive_type' => $grantee->status === 'Inactive' ? 'inactive' : 'masterlist',
                            'remarks' => $grantee->status === 'Inactive' ? ($grantee->notes ?: 'No specific reason provided') : null,
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
            }

            // Send email notifications to archived students
            $emailsSent = 0;
            if (!$allGrantees->isEmpty()) {
                foreach ($allGrantees as $grantee) {
                    try {
                        $studentName = trim($grantee->first_name . ' ' . ($grantee->middle_name ? $grantee->middle_name . ' ' : '') . $grantee->last_name);

                        Mail::to($grantee->email)->send(new SemesterUpdateNotification(
                            $studentName,
                            $grantee->email,
                            $request->new_semester,
                            $grantee->academic_year ?: $grantee->current_academic_year ?: '2024-2025',
                            'semester'
                        ));

                        $emailsSent++;
                        Log::info('Semester update email sent', [
                            'student_id' => $grantee->student_id,
                            'email' => $grantee->email,
                            'student_name' => $studentName
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send semester update email', [
                            'student_id' => $grantee->student_id,
                            'email' => $grantee->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Delete all grantees from the current table (only if there were grantees)
            if (!$allGrantees->isEmpty()) {
                Grantee::query()->delete();
            }

            // Update system settings
            SystemSetting::updateOrCreate(
                ['key' => 'current_semester'],
                ['value' => $request->new_semester]
            );

            DB::commit();

            Log::info('Semester updated successfully', [
                'old_semester' => $request->current_semester,
                'new_semester' => $request->new_semester,
                'archived_count' => $archivedCount,
                'emails_sent' => $emailsSent,
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Semester updated successfully! {$archivedCount} grantees have been archived and {$emailsSent} email notifications sent.",
                'archived_count' => $archivedCount,
                'emails_sent' => $emailsSent
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Semester update failed: ' . $e->getMessage());

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

            // Check if there are any grantees to process
            if ($allGrantees->isEmpty()) {
                Log::info('No grantees found to archive during academic year update');
            } else {
                // Archive all current grantees to masterlist
                foreach ($allGrantees as $grantee) {
                    try {
                        ArchivedStudent::create([
                            'original_application_id' => $grantee->application_id,
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
                            'archived_semester' => $grantee->semester ?: $grantee->current_semester ?: '2nd Semester',
                            'archived_academic_year' => $request->current_year,
                            'archive_type' => $grantee->status === 'Inactive' ? 'inactive' : 'masterlist',
                            'remarks' => $grantee->status === 'Inactive' ? ($grantee->notes ?: 'No specific reason provided') : null,
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
            }

            // Send email notifications to archived students
            $emailsSent = 0;
            if (!$allGrantees->isEmpty()) {
                foreach ($allGrantees as $grantee) {
                    try {
                        $studentName = trim($grantee->first_name . ' ' . ($grantee->middle_name ? $grantee->middle_name . ' ' : '') . $grantee->last_name);

                        Mail::to($grantee->email)->send(new SemesterUpdateNotification(
                            $studentName,
                            $grantee->email,
                            '1st Semester', // Reset to 1st semester for new academic year
                            $request->new_year,
                            'academic_year'
                        ));

                        $emailsSent++;
                        Log::info('Academic year update email sent', [
                            'student_id' => $grantee->student_id,
                            'email' => $grantee->email,
                            'student_name' => $studentName
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send academic year update email', [
                            'student_id' => $grantee->student_id,
                            'email' => $grantee->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // Delete all grantees from the current table (only if there were grantees)
            if (!$allGrantees->isEmpty()) {
                Grantee::query()->delete();
            }

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

            DB::commit();

            Log::info('Academic year updated successfully', [
                'old_year' => $request->current_year,
                'new_year' => $request->new_year,
                'archived_count' => $archivedCount,
                'emails_sent' => $emailsSent,
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Academic year updated successfully! {$archivedCount} grantees have been archived and {$emailsSent} email notifications sent. Semester reset to 1st Semester.",
                'archived_count' => $archivedCount,
                'emails_sent' => $emailsSent
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Academic year update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update academic year: ' . $e->getMessage()
            ], 500);
        }
    }
}
