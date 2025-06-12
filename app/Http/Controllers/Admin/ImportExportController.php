<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
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
            'scholarship_type',
            'middle_name',
            'email',
            'course',
            'department',
            'year_level',
            'gwa',
            'government_benefactor_type',
            'employee_name',
            'employee_relationship',
            'scholarship_name'
        ];

        $filename = 'student_import_template.csv';

        return response()->streamDownload(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            // Add sample data rows for different scholarship types
            fputcsv($handle, [
                '2024-001',
                'John',
                'Doe',
                'government',
                'Smith',
                'john.doe@example.com',
                'Bachelor of Science in Information Technology',
                'SITE',
                '1st Year',
                '1.25',
                'CHED',
                '',
                '',
                ''
            ]);

            fputcsv($handle, [
                '2024-002',
                'Jane',
                'Smith',
                'academic',
                'Marie',
                'jane.smith@example.com',
                'Bachelor of Science in Computer Science',
                'SITE',
                '2nd Year',
                '1.50',
                '',
                '',
                '',
                ''
            ]);

            fputcsv($handle, [
                '2024-003',
                'Mike',
                'Johnson',
                'employees',
                'Robert',
                'mike.johnson@example.com',
                'Bachelor of Science in Business Administration',
                'SBAHM',
                '3rd Year',
                '1.75',
                '',
                'Robert Johnson',
                'Son',
                ''
            ]);

            fputcsv($handle, [
                '2024-004',
                'Sarah',
                'Williams',
                'alumni',
                'Anne',
                'sarah.williams@example.com',
                'Bachelor of Science in Nursing',
                'SNAHS',
                '4th Year',
                '1.30',
                '',
                '',
                '',
                'Williams Family Scholarship'
            ]);

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
                    'status' => 'Active',
                    'approved_date' => now(),
                    'approved_by' => 'Admin Import',
                    'scholarship_start_date' => now()
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

    /**
     * Import grantees with simplified format (Student ID, Name, Scholarship Type)
     */
    public function importGrantees(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        try {
            $file = $request->file('file');

            if ($file->getClientOriginalExtension() === 'csv') {
                return $this->importGranteesFromCSV($file);
            } else {
                return $this->importGranteesFromExcel($file);
            }
        } catch (\Exception $e) {
            Log::error('Grantee import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import grantees dynamically based on scholarship type in the file
     */
    public function importGranteesDynamic(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
            'update_existing' => 'boolean'
        ]);

        try {
            $file = $request->file('file');
            $updateExisting = $request->boolean('update_existing', false);

            if ($file->getClientOriginalExtension() === 'csv') {
                return $this->importGranteesDynamicFromCSV($file, $updateExisting);
            } else {
                return $this->importGranteesDynamicFromExcel($file, $updateExisting);
            }
        } catch (\Exception $e) {
            Log::error('Dynamic grantee import error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import grantees from Excel with simplified format
     */
    private function importGranteesFromExcel($file)
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $header = array_shift($rows); // Remove header row
        $imported = 0;
        $errors = [];
        $usersCreated = 0;
        $granteesCreated = 0;

        foreach ($rows as $index => $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = array_combine($header, $row);

                // Extract data - handle different possible column names
                $studentId = $this->extractValue($data, ['student_id', 'Student ID', 'ID', 'student id']);
                $fullName = $this->extractValue($data, ['name', 'Name', 'full_name', 'Full Name']);
                $scholarshipType = $this->extractValue($data, ['scholarship_type', 'Scholarship Type', 'Type', 'scholarship type']);

                // Validate required fields
                if (empty($studentId) || empty($fullName) || empty($scholarshipType)) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (Student ID, Name, or Scholarship Type)";
                    continue;
                }

                // Parse name (assume "First Last" or "First Middle Last" format)
                $nameParts = explode(' ', trim($fullName));
                $firstName = $nameParts[0];
                $lastName = end($nameParts);
                $middleName = count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : null;

                // Normalize scholarship type
                $scholarshipType = $this->normalizeScholarshipType($scholarshipType);

                // Create user record (without email initially)
                $userData = [
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $studentId . '@temp.placeholder', // Temporary email
                    'password' => Hash::make('student123'), // Default password
                    'role' => 'student',
                    'is_active' => true
                ];

                // Create or update user
                $user = User::updateOrCreate(
                    ['student_id' => $studentId],
                    $userData
                );

                if ($user->wasRecentlyCreated) {
                    $usersCreated++;
                }

                // Determine which table to add to based on scholarship type
                $targetTable = $this->determineTargetTable($scholarshipType);

                if ($targetTable === 'grantees') {
                    // Create grantee record
                    $granteeData = [
                        'student_id' => $studentId,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'middle_name' => $middleName,
                        'scholarship_type' => $scholarshipType,
                        'status' => 'Active',
                        'approved_date' => now(),
                        'approved_by' => 'Admin Import'
                    ];

                    Grantee::updateOrCreate(
                        ['student_id' => $studentId],
                        $granteeData
                    );

                    $granteesCreated++;
                }

                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully imported {$imported} records",
            'details' => [
                'total_imported' => $imported,
                'users_created' => $usersCreated,
                'grantees_created' => $granteesCreated
            ],
            'errors' => $errors
        ]);
    }

    /**
     * Extract value from data array with multiple possible keys
     */
    private function extractValue($data, $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($data[$key]) && !empty($data[$key])) {
                return trim($data[$key]);
            }
        }
        return null;
    }

    /**
     * Normalize scholarship type to match system values
     */
    private function normalizeScholarshipType($type)
    {
        $type = strtolower(trim($type));

        $mappings = [
            'academic' => 'academic',
            'government' => 'government',
            'employee' => 'employee',
            'alumni' => 'alumni',
            'private' => 'alumni', // Map private to alumni as per user preference
            'institutional' => 'academic' // Map institutional to academic
        ];

        foreach ($mappings as $key => $value) {
            if (strpos($type, $key) !== false) {
                return $value;
            }
        }

        return 'academic'; // Default fallback
    }

    /**
     * Import grantees from CSV with simplified format
     */
    private function importGranteesFromCSV($file)
    {
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle); // Get header row

        $imported = 0;
        $errors = [];
        $usersCreated = 0;
        $granteesCreated = 0;

        while (($row = fgetcsv($handle)) !== false) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $data = array_combine($header, $row);

                // Extract data - handle different possible column names
                $studentId = $this->extractValue($data, ['student_id', 'Student ID', 'ID', 'student id']);
                $fullName = $this->extractValue($data, ['name', 'Name', 'full_name', 'Full Name']);
                $scholarshipType = $this->extractValue($data, ['scholarship_type', 'Scholarship Type', 'Type', 'scholarship type']);

                // Validate required fields
                if (empty($studentId) || empty($fullName) || empty($scholarshipType)) {
                    $errors[] = "Row {$imported}: Missing required fields (Student ID, Name, or Scholarship Type)";
                    continue;
                }

                // Parse name (assume "First Last" or "First Middle Last" format)
                $nameParts = explode(' ', trim($fullName));
                $firstName = $nameParts[0];
                $lastName = end($nameParts);
                $middleName = count($nameParts) > 2 ? implode(' ', array_slice($nameParts, 1, -1)) : null;

                // Normalize scholarship type
                $scholarshipType = $this->normalizeScholarshipType($scholarshipType);

                // Create user record (without email initially)
                $userData = [
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $studentId . '@temp.placeholder', // Temporary email
                    'password' => Hash::make('student123'), // Default password
                    'role' => 'student',
                    'is_active' => true
                ];

                // Create or update user
                $user = User::updateOrCreate(
                    ['student_id' => $studentId],
                    $userData
                );

                if ($user->wasRecentlyCreated) {
                    $usersCreated++;
                }

                // Create grantee record
                $granteeData = [
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $middleName,
                    'scholarship_type' => $scholarshipType,
                    'status' => 'Active',
                    'approved_date' => now(),
                    'approved_by' => 'Admin Import'
                ];

                Grantee::updateOrCreate(
                    ['student_id' => $studentId],
                    $granteeData
                );

                $granteesCreated++;
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$imported}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "Successfully imported {$imported} records",
            'details' => [
                'total_imported' => $imported,
                'users_created' => $usersCreated,
                'grantees_created' => $granteesCreated
            ],
            'errors' => $errors
        ]);
    }

    /**
     * Import grantees dynamically from CSV
     */
    private function importGranteesDynamicFromCSV($file, $updateExisting = false)
    {
        $handle = fopen($file->getPathname(), 'r');
        $header = fgetcsv($handle); // Get header row

        // Normalize header names
        $header = array_map(function($col) {
            return strtolower(trim(str_replace(' ', '_', $col)));
        }, $header);

        $imported = 0;
        $usersCreated = 0;
        $granteesCreated = 0;
        $errors = [];
        $scholarshipTypes = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($header, $row);

                // Extract required fields
                $studentId = $data['student_id'] ?? $data['grantee_id'] ?? null;
                $firstName = $data['first_name'] ?? null;
                $lastName = $data['last_name'] ?? null;
                $scholarshipType = strtolower(trim($data['scholarship_type'] ?? ''));

                if (!$studentId || !$firstName || !$lastName || !$scholarshipType) {
                    $errors[] = "Row {$imported}: Missing required fields (Student ID, First Name, Last Name, Scholarship Type)";
                    continue;
                }

                // Check for duplicate student ID in users table (unless updating existing)
                if (!$updateExisting && User::where('student_id', $studentId)->exists()) {
                    $errors[] = "Row {$imported}: Student ID '{$studentId}' already exists in the system";
                    continue;
                }

                // Check for duplicate student ID in grantees table (unless updating existing)
                if (!$updateExisting && Grantee::where('student_id', $studentId)->exists()) {
                    $errors[] = "Row {$imported}: Student ID '{$studentId}' already exists as a grantee";
                    continue;
                }

                // Validate scholarship type
                $validTypes = ['government', 'academic', 'employees', 'alumni'];
                if (!in_array($scholarshipType, $validTypes)) {
                    $errors[] = "Row {$imported}: Invalid scholarship type '{$scholarshipType}'. Valid types: " . implode(', ', $validTypes);
                    continue;
                }

                if (!in_array($scholarshipType, $scholarshipTypes)) {
                    $scholarshipTypes[] = $scholarshipType;
                }

                // Create or update user
                $userData = [
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $data['middle_name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'password' => Hash::make('password123'),
                    'role' => 'student'
                ];

                $user = User::updateOrCreate(
                    ['student_id' => $studentId],
                    $userData
                );

                if ($user->wasRecentlyCreated) {
                    $usersCreated++;
                }

                // Create grantee record
                $granteeData = [
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $data['middle_name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'course' => $data['course'] ?? null,
                    'department' => $data['department'] ?? null,
                    'year_level' => $data['year_level'] ?? null,
                    'gwa' => $data['gwa'] ?? null,
                    'scholarship_type' => $scholarshipType,
                    'government_benefactor_type' => $data['government_benefactor_type'] ?? null,
                    'employee_name' => $data['employee_name'] ?? null,
                    'employee_relationship' => $data['employee_relationship'] ?? null,
                    'scholarship_name' => $data['scholarship_name'] ?? null,
                    'status' => 'Active',
                    'approved_date' => now(),
                    'approved_by' => 'Admin Import'
                ];

                if ($updateExisting) {
                    Grantee::updateOrCreate(
                        ['student_id' => $studentId],
                        $granteeData
                    );
                } else {
                    Grantee::create($granteeData);
                }

                $granteesCreated++;
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row {$imported}: " . $e->getMessage();
            }
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "Successfully imported {$imported} grantees",
            'details' => [
                'total_imported' => $imported,
                'users_created' => $usersCreated,
                'grantees_created' => $granteesCreated,
                'scholarship_types' => $scholarshipTypes,
                'errors' => $errors
            ]
        ]);
    }

    /**
     * Import grantees dynamically from Excel
     */
    private function importGranteesDynamicFromExcel($file, $updateExisting = false)
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $header = array_shift($rows); // Remove header row

        // Normalize header names
        $header = array_map(function($col) {
            return strtolower(trim(str_replace(' ', '_', $col)));
        }, $header);

        $imported = 0;
        $usersCreated = 0;
        $granteesCreated = 0;
        $errors = [];
        $scholarshipTypes = [];

        foreach ($rows as $index => $row) {
            try {
                $data = array_combine($header, $row);

                // Extract required fields
                $studentId = $data['student_id'] ?? $data['grantee_id'] ?? null;
                $firstName = $data['first_name'] ?? null;
                $lastName = $data['last_name'] ?? null;
                $scholarshipType = strtolower(trim($data['scholarship_type'] ?? ''));

                if (!$studentId || !$firstName || !$lastName || !$scholarshipType) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (Student ID, First Name, Last Name, Scholarship Type)";
                    continue;
                }

                // Check for duplicate student ID in users table (unless updating existing)
                if (!$updateExisting && User::where('student_id', $studentId)->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Student ID '{$studentId}' already exists in the system";
                    continue;
                }

                // Check for duplicate student ID in grantees table (unless updating existing)
                if (!$updateExisting && Grantee::where('student_id', $studentId)->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Student ID '{$studentId}' already exists as a grantee";
                    continue;
                }

                // Validate scholarship type
                $validTypes = ['government', 'academic', 'employees', 'alumni'];
                if (!in_array($scholarshipType, $validTypes)) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid scholarship type '{$scholarshipType}'. Valid types: " . implode(', ', $validTypes);
                    continue;
                }

                if (!in_array($scholarshipType, $scholarshipTypes)) {
                    $scholarshipTypes[] = $scholarshipType;
                }

                // Create or update user
                $userData = [
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $data['middle_name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'password' => Hash::make('password123'),
                    'role' => 'student'
                ];

                $user = User::updateOrCreate(
                    ['student_id' => $studentId],
                    $userData
                );

                if ($user->wasRecentlyCreated) {
                    $usersCreated++;
                }

                // Create grantee record
                $granteeData = [
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'middle_name' => $data['middle_name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'course' => $data['course'] ?? null,
                    'department' => $data['department'] ?? null,
                    'year_level' => $data['year_level'] ?? null,
                    'gwa' => $data['gwa'] ?? null,
                    'scholarship_type' => $scholarshipType,
                    'government_benefactor_type' => $data['government_benefactor_type'] ?? null,
                    'employee_name' => $data['employee_name'] ?? null,
                    'employee_relationship' => $data['employee_relationship'] ?? null,
                    'scholarship_name' => $data['scholarship_name'] ?? null,
                    'status' => 'Active',
                    'approved_date' => now(),
                    'approved_by' => 'Admin Import'
                ];

                if ($updateExisting) {
                    Grantee::updateOrCreate(
                        ['student_id' => $studentId],
                        $granteeData
                    );
                } else {
                    Grantee::create($granteeData);
                }

                $granteesCreated++;
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully imported {$imported} grantees",
            'details' => [
                'total_imported' => $imported,
                'users_created' => $usersCreated,
                'grantees_created' => $granteesCreated,
                'scholarship_types' => $scholarshipTypes,
                'errors' => $errors
            ]
        ]);
    }

    /**
     * Download grantee import template
     */
    public function downloadGranteeTemplate()
    {
        $filename = 'grantee_import_template.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            // Headers for simplified import
            fputcsv($handle, [
                'Student ID',
                'Name',
                'Scholarship Type'
            ]);

            // Sample data
            fputcsv($handle, [
                '2024-001234',
                'Juan Dela Cruz',
                'Academic'
            ]);

            fputcsv($handle, [
                '2024-001235',
                'Maria Santos',
                'Government'
            ]);

            fputcsv($handle, [
                '2024-001236',
                'Jose Rizal',
                'Alumni'
            ]);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Determine which table to add the record to based on scholarship type
     */
    private function determineTargetTable($scholarshipType)
    {
        // For now, all imported records go to grantees table
        // This can be expanded later if needed
        return 'grantees';
    }
}
