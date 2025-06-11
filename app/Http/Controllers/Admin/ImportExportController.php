<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportExportController extends Controller
{
    /**
     * Bulk import students
     */
    public function bulkImportStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'scholarship_type' => 'required|string'
        ]);

        try {
            $file = $request->file('file');
            $scholarshipType = $request->scholarship_type;
            $updateExisting = $request->has('update_existing');

            if ($file->getClientOriginalExtension() === 'csv') {
                return $this->importFromCSV($file, $scholarshipType, $updateExisting);
            } else {
                return $this->importFromExcel($file, $scholarshipType, $updateExisting);
            }
        } catch (\Exception $e) {
            Log::error('Bulk import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download CSV template for bulk import
     */
    public function downloadTemplate()
    {
        $headers = [
            'student_id',
            'first_name',
            'last_name',
            'middle_name',
            'email',
            'course',
            'department',
            'year_level',
            'gwa',
            'semester',
            'academic_year'
        ];

        $filename = 'student_import_template.csv';

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export data based on type
     */
    public function exportData($type)
    {
        switch ($type) {
            case 'applications':
                return $this->exportApplicationsData();
            case 'students':
                return $this->exportStudentsData();
            case 'analytics':
                return $this->exportAnalyticsData();
            default:
                return response()->json(['error' => 'Invalid export type'], 400);
        }
    }

    /**
     * Export applications data
     */
    private function exportApplicationsData()
    {
        $applications = ScholarshipApplication::all();
        $filename = 'applications_export_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($applications) {
            $handle = fopen('php://output', 'w');

            // Headers
            fputcsv($handle, [
                'Application ID',
                'Student ID',
                'Name',
                'Email',
                'Scholarship Type',
                'Course',
                'Department',
                'Year Level',
                'GWA',
                'Status',
                'Date Applied'
            ]);

            // Data
            foreach ($applications as $app) {
                fputcsv($handle, [
                    $app->application_id,
                    $app->student_id,
                    $app->first_name . ' ' . $app->last_name,
                    $app->email,
                    ucfirst($app->scholarship_type),
                    $app->course,
                    $app->department,
                    $app->year_level,
                    $app->gwa,
                    $app->status,
                    $app->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Export students data
     */
    private function exportStudentsData()
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
     * Export analytics data
     */
    private function exportAnalyticsData()
    {
        // Get analytics data
        $totalGrantees = Grantee::count();
        $totalApplications = ScholarshipApplication::count();

        $data = [
            ['Metric', 'Value'],
            ['Total Grantees', $totalGrantees],
            ['Total Applications', $totalApplications],
            ['Government Scholarships', Grantee::where('scholarship_type', 'government')->count()],
            ['Academic Scholarships', Grantee::where('scholarship_type', 'academic')->count()],
            ['Employee Scholarships', Grantee::where('scholarship_type', 'employees')->count()],
            ['Private Scholarships', Grantee::where('scholarship_type', 'private')->count()],
        ];

        $filename = 'analytics_export_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            foreach ($data as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Import students from CSV
     */
    private function importFromCSV($file, $scholarshipType, $updateExisting = false)
    {
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle);
        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);

                $studentData = [
                    'student_id' => $data['student_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'email' => $data['email'],
                    'course' => $data['course'],
                    'department' => $data['department'] ?? null,
                    'year_level' => $data['year_level'] ?? null,
                    'gwa' => $data['gwa'] ?? null,
                    'scholarship_type' => $scholarshipType,
                    'current_semester' => $data['semester'] ?? '1st Semester',
                    'current_academic_year' => $data['academic_year'] ?? '2024-2025',
                    'status' => 'Active'
                ];

                if ($updateExisting) {
                    Grantee::updateOrCreate(
                        ['student_id' => $data['student_id']],
                        $studentData
                    );
                } else {
                    Grantee::create($studentData);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$imported}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "Successfully imported {$imported} students",
            'imported' => $imported,
            'errors' => $errors
        ]);
    }

    /**
     * Import students from Excel
     */
    private function importFromExcel($file, $scholarshipType, $updateExisting = false)
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $header = array_shift($rows); // Remove header row
        $imported = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            try {
                $data = array_combine($header, $row);

                $studentData = [
                    'student_id' => $data['student_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'middle_name' => $data['middle_name'] ?? null,
                    'email' => $data['email'],
                    'course' => $data['course'],
                    'department' => $data['department'] ?? null,
                    'year_level' => $data['year_level'] ?? null,
                    'gwa' => $data['gwa'] ?? null,
                    'scholarship_type' => $scholarshipType,
                    'current_semester' => $data['semester'] ?? '1st Semester',
                    'current_academic_year' => $data['academic_year'] ?? '2024-2025',
                    'status' => 'Active'
                ];

                if ($updateExisting) {
                    Grantee::updateOrCreate(
                        ['student_id' => $data['student_id']],
                        $studentData
                    );
                } else {
                    Grantee::create($studentData);
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully imported {$imported} students",
            'imported' => $imported,
            'errors' => $errors
        ]);
    }

    /**
     * Download student template
     */
    public function downloadStudentTemplate()
    {
        return $this->downloadTemplate();
    }

    /**
     * Import students (alternative method)
     */
    public function importStudents(Request $request)
    {
        return $this->bulkImportStudents($request);
    }

    /**
     * Bulk import (alternative method)
     */
    public function bulkImport(Request $request)
    {
        return $this->bulkImportStudents($request);
    }
}
