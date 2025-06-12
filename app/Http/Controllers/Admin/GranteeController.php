<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grantee;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GranteeController extends Controller
{
    /**
     * Show students/grantees page
     */
    public function index(Request $request)
    {
        // Get current semester and academic year from system settings
        $currentSemester = SystemSetting::where('key', 'current_semester')->value('value') ?? '1st Semester';
        $currentAcademicYear = SystemSetting::where('key', 'current_academic_year')->value('value') ?? '2024-2025';

        // Get scholarship type filter
        $scholarshipTypeFilter = $request->get('scholarship_type');

        // Base query for grantees
        $query = Grantee::query();

        // Apply scholarship type filter
        if ($scholarshipTypeFilter && $scholarshipTypeFilter !== 'all') {
            $query->where('scholarship_type', $scholarshipTypeFilter);
        }

        // Get all grantees
        $allGrantees = $query->orderBy('created_at', 'desc')->get();

        // Format grantees data for display
        $students = $allGrantees->map(function ($grantee) use ($currentSemester, $currentAcademicYear) {
            return [
                'id' => $grantee->grantee_id,
                'application_id' => $grantee->application_id,
                'student_id' => $grantee->student_id,
                'name' => trim($grantee->first_name . ' ' . ($grantee->middle_name ? $grantee->middle_name . ' ' : '') . $grantee->last_name),
                'course' => $grantee->course ?: $grantee->strand,
                'strand' => $grantee->strand,
                'department' => $grantee->department,
                'year_level' => $grantee->year_level,
                'gwa' => $grantee->gwa ?: $grantee->current_gwa,
                'scholarship_type' => $grantee->scholarship_type,
                'government_benefactor_type' => $grantee->government_benefactor_type,
                'employee_name' => $grantee->employee_name,
                'employee_relationship' => $grantee->employee_relationship,
                'scholarship_name' => $grantee->scholarship_name,
                'current_semester' => $grantee->semester ?: $grantee->current_semester ?: $currentSemester,
                'current_academic_year' => $grantee->academic_year ?: $grantee->current_academic_year ?: $currentAcademicYear,
                'status' => $grantee->status ?: 'Active',
                'approved_date' => $grantee->approved_date,
                'created_at' => $grantee->created_at
            ];
        });

        return view('admin.students', [
            'students' => $students,
            'scholarshipTypeFilter' => $scholarshipTypeFilter,
            'currentSemester' => $currentSemester,
            'currentAcademicYear' => $currentAcademicYear
        ]);
    }

    /**
     * Update student information
     */
    public function updateStudent(Request $request, $id)
    {
        try {
            $grantee = Grantee::where('grantee_id', $id)->firstOrFail();

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'contact_number' => 'required|string|max:20',
                'course' => 'required|string|max:255',
                'department' => 'nullable|string|max:255',
                'year_level' => 'nullable|string|max:50',
                'gwa' => 'nullable|numeric|min:1|max:5',
                'current_semester' => 'required|string',
                'current_academic_year' => 'required|string',
                'status' => 'required|string|in:Active,Inactive'
            ]);

            $grantee->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'course' => $request->course,
                'department' => $request->department,
                'year_level' => $request->year_level,
                'gwa' => $request->gwa,
                'current_semester' => $request->current_semester,
                'current_academic_year' => $request->current_academic_year,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student information updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update grantee information
     */
    public function updateGrantee(Request $request, $granteeId)
    {
        try {
            $grantee = Grantee::where('grantee_id', $granteeId)->firstOrFail();

            $request->validate([
                'status' => 'required|string|in:Active,Inactive',
                'notes' => 'nullable|string|max:500'
            ]);

            $newStatus = $request->status;
            $remarks = $request->notes;

            // Note: Manual status changes to Inactive do not create archive records
            // Only semester/year updates create archive records

            // Update grantee status
            $grantee->status = $newStatus;
            if ($remarks) {
                $grantee->notes = $remarks; // Use 'notes' field instead of 'remarks'
            }
            $grantee->save();

            return response()->json([
                'success' => true,
                'message' => 'Grantee status updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating grantee: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating grantee: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get students data for API
     */
    public function getStudentsData(Request $request)
    {
        $query = Grantee::query();

        // Apply filters
        if ($request->has('scholarship_type') && $request->scholarship_type !== 'all') {
            $query->where('scholarship_type', $request->scholarship_type);
        }

        $students = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $students->map(function ($grantee) {
                return [
                    'id' => $grantee->grantee_id,
                    'student_id' => $grantee->student_id,
                    'name' => trim($grantee->first_name . ' ' . ($grantee->middle_name ? $grantee->middle_name . ' ' : '') . $grantee->last_name),
                    'email' => $grantee->email,
                    'scholarship_type' => ucfirst($grantee->scholarship_type),
                    'course' => $grantee->course,
                    'department' => $grantee->department,
                    'year_level' => $grantee->year_level,
                    'gwa' => $grantee->gwa ?: $grantee->current_gwa,
                    'status' => $grantee->status ?: 'Active',
                    'created_at' => $grantee->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }

    /**
     * Get students by category
     */
    public function getStudentsByCategory($category)
    {
        $query = Grantee::query();

        if ($category !== 'all') {
            $query->where('scholarship_type', $category);
        }

        $students = $query->orderBy('created_at', 'desc')->get();

        return response()->json($students);
    }

    /**
     * Add new student
     */
    public function addStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|unique:grantees,student_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:grantees,email',
            'scholarship_type' => 'required|string',
            'course' => 'required|string',
            'department' => 'required|string',
            'year_level' => 'required|string',
            'gwa' => 'nullable|numeric|min:1|max:5'
        ]);

        $grantee = Grantee::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Student added successfully',
            'data' => $grantee
        ]);
    }

    /**
     * Export students data
     */
    public function exportStudents(Request $request)
    {
        $students = Grantee::all();
        $filename = 'students_export_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($students) {
            $handle = fopen('php://output', 'w');

            // Headers
            fputcsv($handle, [
                'Student ID',
                'Name',
                'Email',
                'Scholarship Type',
                'Course',
                'Department',
                'Year Level',
                'GWA',
                'Academic Year',
                'Semester'
            ]);

            // Data
            foreach ($students as $student) {
                fputcsv($handle, [
                    $student->student_id,
                    $student->first_name . ' ' . $student->last_name,
                    $student->email,
                    ucfirst($student->scholarship_type),
                    $student->course,
                    $student->department,
                    $student->year_level,
                    $student->gwa,
                    $student->academic_year,
                    $student->semester
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Get course or strand for display
     */
    private function getCourseOrStrand($grantee)
    {
        // For BEU students, show strand if available, otherwise course
        if ($grantee->education_stage === 'BEU' && $grantee->strand) {
            return $grantee->strand;
        }
        return $grantee->course;
    }
}
