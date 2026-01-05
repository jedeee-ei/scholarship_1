<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchivedStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArchiveController extends Controller
{
    /**
     * Show archived students page
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $archiveType = $request->get('archive_type', 'all');
        $scholarshipType = $request->get('scholarship_type', 'all');

        // Base query
        $query = ArchivedStudent::query();

        // Apply archive type filter
        if ($archiveType !== 'all') {
            $query->where('archive_type', $archiveType);
        }

        // Apply scholarship type filter
        if ($scholarshipType !== 'all') {
            $query->where('scholarship_type', $scholarshipType);
        }

        // Get archived students
        $archivedStudents = $query->orderBy('archived_at', 'desc')->get();

        // Format data for display
        $students = $archivedStudents->map(function ($student) {
            return [
                'id' => $student->id,
                'grantee_id' => $student->original_application_id,
                'student_id' => $student->student_id,
                'name' => trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name),
                'scholarship_type' => ucfirst($student->scholarship_type),
                'course' => $student->course,
                'department' => $student->department,
                'year_level' => $student->year_level,
                'gwa' => $student->gwa,
                'archived_semester' => $student->archived_semester,
                'archived_academic_year' => $student->archived_academic_year,
                'archive_type' => $student->archive_type,
                'remarks' => $student->remarks,
                'archived_at' => $student->archived_at,
                'archived_by' => $student->archived_by
            ];
        });

        return view('admin.archived-students', [
            'archivedStudents' => $archivedStudents,
            'students' => $students,
            'archiveTypeFilter' => $archiveType,
            'scholarshipTypeFilter' => $scholarshipType
        ]);
    }

    /**
     * Export archived students
     */
    public function exportArchivedStudents(Request $request)
    {
        $query = ArchivedStudent::query();

        // Apply filters if provided
        if ($request->has('archive_type') && $request->archive_type !== 'all') {
            $query->where('archive_type', $request->archive_type);
        }

        if ($request->has('scholarship_type') && $request->scholarship_type !== 'all') {
            $query->where('scholarship_type', $request->scholarship_type);
        }

        $archivedStudents = $query->orderBy('archived_at', 'desc')->get();

        $filename = 'archived_students_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($archivedStudents) {
            $handle = fopen('php://output', 'w');

            // Headers
            fputcsv($handle, [
                'Grantee ID',
                'Student ID',
                'Full Name',
                'Email',
                'Scholarship Type',
                'Course',
                'Department',
                'Year Level',
                'GWA',
                'Archived Semester',
                'Archived Academic Year',
                'Archive Type',
                'Remarks',
                'Archived Date',
                'Archived By'
            ]);

            // Data
            foreach ($archivedStudents as $student) {
                fputcsv($handle, [
                    $student->original_application_id,
                    $student->student_id,
                    trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name),
                    $student->email,
                    ucfirst($student->scholarship_type),
                    $student->course,
                    $student->department,
                    $student->year_level,
                    $student->gwa,
                    $student->archived_semester,
                    $student->archived_academic_year,
                    ucfirst($student->archive_type),
                    $student->remarks ?: 'N/A',
                    $student->archived_at ? $student->archived_at->format('Y-m-d H:i:s') : 'N/A',
                    $student->archived_by
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Search archived students
     */
    public function searchArchive(Request $request)
    {
        $query = ArchivedStudent::query();

        // Search by name, student ID, or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by archive type
        if ($request->has('archive_type') && $request->archive_type !== 'all') {
            $query->where('archive_type', $request->archive_type);
        }

        // Filter by scholarship type
        if ($request->has('scholarship_type') && $request->scholarship_type !== 'all') {
            $query->where('scholarship_type', $request->scholarship_type);
        }

        $results = $query->orderBy('archived_at', 'desc')->get();

        return response()->json([
            'data' => $results->map(function ($student) {
                return [
                    'id' => $student->id,
                    'grantee_id' => $student->original_application_id,
                    'student_id' => $student->student_id,
                    'name' => trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name),
                    'email' => $student->email,
                    'scholarship_type' => ucfirst($student->scholarship_type),
                    'course' => $student->course,
                    'department' => $student->department,
                    'year_level' => $student->year_level,
                    'gwa' => $student->gwa,
                    'archived_semester' => $student->archived_semester,
                    'archived_academic_year' => $student->archived_academic_year,
                    'archive_type' => $student->archive_type,
                    'remarks' => $student->remarks,
                    'archived_at' => $student->archived_at->format('Y-m-d H:i:s'),
                    'archived_by' => $student->archived_by
                ];
            })
        ]);
    }

    /**
     * Download archive data
     */
    public function downloadArchive(Request $request)
    {
        $format = $request->get('format', 'csv');
        $archiveType = $request->get('archive_type', 'all');

        $query = ArchivedStudent::query();

        if ($archiveType !== 'all') {
            $query->where('archive_type', $archiveType);
        }

        $data = $query->orderBy('archived_at', 'desc')->get();

        if ($format === 'csv') {
            return $this->downloadCSV($data, $archiveType);
        } elseif ($format === 'excel') {
            return $this->downloadExcel($data, $archiveType);
        }

        return response()->json(['error' => 'Invalid format'], 400);
    }

    /**
     * Delete archived student record
     */
    public function deleteArchive($id)
    {
        try {
            $archivedStudent = ArchivedStudent::findOrFail($id);
            $studentName = $archivedStudent->first_name . ' ' . $archivedStudent->last_name;

            $archivedStudent->delete();

            Log::info('Archived student record deleted', [
                'archived_student_id' => $id,
                'student_name' => $studentName,
                'student_id' => $archivedStudent->student_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Archived student record deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting archived student: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting archived student: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get archive statistics
     */
    public function getStatistics()
    {
        $stats = [
            'total' => ArchivedStudent::count(),
            'masterlist' => ArchivedStudent::where('archive_type', 'masterlist')->count(),
            'inactive' => ArchivedStudent::where('archive_type', 'inactive')->count(),
            'by_scholarship_type' => [
                'government' => ArchivedStudent::where('scholarship_type', 'government')->count(),
                'academic' => ArchivedStudent::where('scholarship_type', 'academic')->count(),
                'employees' => ArchivedStudent::where('scholarship_type', 'employees')->count(),
                'private' => ArchivedStudent::where('scholarship_type', 'private')->count(),
            ],
            'recent_archives' => ArchivedStudent::orderBy('archived_at', 'desc')->take(5)->get()
        ];

        return response()->json($stats);
    }

    /**
     * Download CSV format
     */
    private function downloadCSV($data, $archiveType)
    {
        $filename = "archived_students_{$archiveType}_" . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Headers
            fputcsv($handle, [
                'Grantee ID',
                'Student ID',
                'Full Name',
                'Email',
                'Scholarship Type',
                'Course',
                'Department',
                'Year Level',
                'GWA',
                'Archived Semester',
                'Archived Academic Year',
                'Archive Type',
                'Remarks',
                'Archived Date',
                'Archived By'
            ]);

            // Data
            foreach ($data as $student) {
                fputcsv($handle, [
                    $student->original_application_id,
                    $student->student_id,
                    trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name),
                    $student->email,
                    ucfirst($student->scholarship_type),
                    $student->course,
                    $student->department,
                    $student->year_level,
                    $student->gwa,
                    $student->archived_semester,
                    $student->archived_academic_year,
                    ucfirst($student->archive_type),
                    $student->remarks ?: 'N/A',
                    $student->archived_at ? $student->archived_at->format('Y-m-d H:i:s') : 'N/A',
                    $student->archived_by
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Download Excel format
     */
    private function downloadExcel($data, $archiveType)
    {
        // For now, return CSV format as Excel functionality requires additional setup
        return $this->downloadCSV($data, $archiveType);
    }

    /**
     * Get archived students data for API
     */
    public function getArchivedStudentsData(Request $request)
    {
        $query = ArchivedStudent::query();

        // Apply filters
        if ($request->has('archive_type') && $request->archive_type !== 'all') {
            $query->where('archive_type', $request->archive_type);
        }

        if ($request->has('scholarship_type') && $request->scholarship_type !== 'all') {
            $query->where('scholarship_type', $request->scholarship_type);
        }

        $students = $query->orderBy('archived_at', 'desc')->get();

        return response()->json([
            'data' => $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'grantee_id' => $student->original_application_id,
                    'student_id' => $student->student_id,
                    'name' => trim($student->first_name . ' ' . ($student->middle_name ? $student->middle_name . ' ' : '') . $student->last_name),
                    'email' => $student->email,
                    'scholarship_type' => ucfirst($student->scholarship_type),
                    'course' => $student->course,
                    'department' => $student->department,
                    'year_level' => $student->year_level,
                    'gwa' => $student->gwa,
                    'archived_semester' => $student->archived_semester,
                    'archived_academic_year' => $student->archived_academic_year,
                    'archive_type' => $student->archive_type,
                    'remarks' => $student->remarks,
                    'archived_at' => $student->archived_at->format('Y-m-d H:i:s'),
                    'archived_by' => $student->archived_by
                ];
            })
        ]);
    }

    /**
     * Get archived student details for modal view
     */
    public function getArchivedStudentDetails($id)
    {
        try {
            $student = ArchivedStudent::findOrFail($id);

            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'original_application_id' => $student->original_application_id,
                    'student_id' => $student->student_id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'email' => $student->email,
                    'contact_number' => $student->contact_number,
                    'course' => $student->course,
                    'department' => $student->department,
                    'year_level' => $student->year_level,
                    'gwa' => $student->gwa,
                    'scholarship_type' => ucfirst($student->scholarship_type),
                    'archived_semester' => $student->archived_semester,
                    'archived_academic_year' => $student->archived_academic_year,
                    'archive_type' => $student->archive_type,
                    'remarks' => $student->remarks,
                    'archived_at' => $student->archived_at->format('Y-m-d H:i:s'),
                    'archived_by' => $student->archived_by
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found or error occurred: ' . $e->getMessage()
            ], 404);
        }
    }
}
