<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function index()
    {
        // Get statistics for dashboard
        $stats = [
            'total' => ScholarshipApplication::count(),
            'pending' => ScholarshipApplication::where('status', 'Pending Review')->count(),
            'approved' => ScholarshipApplication::where('status', 'Approved')->count(),
            'rejected' => ScholarshipApplication::where('status', 'Rejected')->count(),
        ];

        // Calculate percentage changes (comparing with previous month)
        $currentMonth = now()->month;
        $previousMonth = now()->subMonth()->month;

        $currentMonthStats = [
            'total' => ScholarshipApplication::whereMonth('created_at', $currentMonth)->count(),
            'pending' => ScholarshipApplication::where('status', 'Pending Review')->whereMonth('created_at', $currentMonth)->count(),
            'approved' => ScholarshipApplication::where('status', 'Approved')->whereMonth('created_at', $currentMonth)->count(),
            'rejected' => ScholarshipApplication::where('status', 'Rejected')->whereMonth('created_at', $currentMonth)->count(),
        ];

        $previousMonthStats = [
            'total' => ScholarshipApplication::whereMonth('created_at', $previousMonth)->count(),
            'pending' => ScholarshipApplication::where('status', 'Pending Review')->whereMonth('created_at', $previousMonth)->count(),
            'approved' => ScholarshipApplication::where('status', 'Approved')->whereMonth('created_at', $previousMonth)->count(),
            'rejected' => ScholarshipApplication::where('status', 'Rejected')->whereMonth('created_at', $previousMonth)->count(),
        ];

        $changes = [];
        foreach ($currentMonthStats as $key => $current) {
            $previous = $previousMonthStats[$key];
            if ($previous > 0) {
                $changes[$key] = round((($current - $previous) / $previous) * 100, 1);
            } else {
                $changes[$key] = $current > 0 ? 100 : 0;
            }
        }

        // Get recent applications
        $recentApplications = ScholarshipApplication::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get chart data
        $chartData = $this->getChartData();

        return view('admin.dashboard', [
            'stats' => $stats,
            'changes' => $changes,
            'recentApplications' => $recentApplications,
            'chartData' => $chartData
        ]);
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData()
    {
        // Applications over time (last 6 months)
        $months = [];
        $applicationCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            $applicationCounts[] = ScholarshipApplication::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Scholarship types distribution
        $scholarshipTypes = [
            'ched' => ScholarshipApplication::where('scholarship_type', 'ched')->count(),
            'presidents' => ScholarshipApplication::where('scholarship_type', 'presidents')->count(),
            'employees' => ScholarshipApplication::where('scholarship_type', 'employees')->count(),
            'private' => ScholarshipApplication::where('scholarship_type', 'private')->count(),
        ];

        return [
            'months' => $months,
            'applicationCounts' => $applicationCounts,
            'scholarshipTypes' => $scholarshipTypes
        ];
    }

    /**
     * Show all applications
     */
    public function applications(Request $request)
    {
        $query = ScholarshipApplication::query();

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by scholarship type if provided
        if ($request->has('type') && $request->type) {
            $query->where('scholarship_type', $request->type);
        }

        // Get applications with pagination
        $applications = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.applications', [
            'applications' => $applications,
            'currentStatus' => $request->status,
            'currentType' => $request->type
        ]);
    }

    /**
     * View a specific application
     */
    public function viewApplication($id)
    {
        $application = ScholarshipApplication::where('application_id', $id)->firstOrFail();

        return view('admin.application-detail', [
            'application' => $application
        ]);
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, $id)
    {
        $application = ScholarshipApplication::where('application_id', $id)->firstOrFail();

        // Validate the request
        $request->validate([
            'status' => 'required|string|in:Pending Review,Under Committee Review,Decision Made,Approved,Rejected'
        ]);

        // Update the status
        $application->status = $request->status;
        $application->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'Application status updated successfully.');
    }

    /**
     * Add new scholarship program
     */
    public function addScholarship(Request $request)
    {
        $request->validate([
            'scholarship_name' => 'required|string|max:255',
            'scholarship_type' => 'required|string',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'max_slots' => 'nullable|integer|min:1',
            'eligibility' => 'nullable|string'
        ]);

        // Here you would save to a scholarships table
        // For now, we'll just return success
        return response()->json(['success' => true, 'message' => 'Scholarship program added successfully']);
    }

    /**
     * Bulk import students
     */
    public function bulkImportStudents(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt',
            'update_existing' => 'boolean'
        ]);

        // Here you would process the CSV file
        // For now, we'll just return success
        return response()->json(['success' => true, 'message' => 'Students imported successfully']);
    }

    /**
     * Update system settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'application_period' => 'required|string|in:open,closed,renewal',
            'academic_year' => 'nullable|string',
            'current_semester' => 'nullable|string|in:1st,2nd,summer',
            'notification_email' => 'nullable|email',
            'email_notifications' => 'boolean'
        ]);

        // Here you would save settings to database or config
        // For now, we'll just return success
        return response()->json(['success' => true, 'message' => 'Settings updated successfully']);
    }

    /**
     * Export applications data
     */
    public function exportApplications()
    {
        // Here you would generate CSV/Excel file
        // For now, we'll just return the data
        return response()->json(['success' => true, 'message' => 'Export functionality will be implemented']);
    }

    /**
     * Generate report
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'date_range' => 'required|string',
            'format' => 'required|string|in:pdf,excel,csv',
            'include_charts' => 'nullable|string|in:yes,no',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        // Here you would generate the actual report
        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully',
            'download_url' => '/downloads/report_' . time() . '.' . $request->format
        ]);
    }

    /**
     * Preview report
     */
    public function previewReport()
    {
        // Here you would generate a preview of the report
        return response()->json(['success' => true, 'message' => 'Report preview functionality will be implemented']);
    }

    /**
     * Search archive
     */
    public function searchArchive(Request $request)
    {
        $archiveType = $request->get('archive_type', '');
        $archiveYear = $request->get('archive_year', '');

        // Here you would search the archive based on criteria
        // For now, return sample data with search info
        return response()->json([
            'success' => true,
            'search_criteria' => [
                'type' => $archiveType ?: 'All Types',
                'year' => $archiveYear ?: 'All Years'
            ],
            'data' => [
                // Sample archive data
                [
                    'id' => 'arch_001',
                    'filename' => 'Applications_Report_2023_Q4.pdf',
                    'type' => 'Applications',
                    'date_created' => '2023-12-31',
                    'size' => '2.5 MB'
                ]
            ]
        ]);
    }

    /**
     * Download archive file
     */
    public function downloadArchive($fileId)
    {
        // Here you would serve the actual file
        return response()->json(['success' => true, 'message' => "Downloading file: {$fileId}"]);
    }

    /**
     * Delete archive file
     */
    public function deleteArchive($fileId)
    {
        // Here you would delete the actual file
        return response()->json(['success' => true, 'message' => "Archive file deleted: {$fileId}"]);
    }

    /**
     * Get students data
     */
    public function getStudentsData()
    {
        // Here you would fetch actual student data from database
        $students = [
            [
                'id' => 'STU-001',
                'name' => 'John Doe',
                'course' => 'BS Computer Science',
                'scholarship_type' => 'CHED',
                'status' => 'Active',
                'gwa' => '1.25'
            ],
            [
                'id' => 'STU-002',
                'name' => 'Jane Smith',
                'course' => 'BS Nursing',
                'scholarship_type' => 'Institutional',
                'status' => 'Active',
                'gwa' => '1.50'
            ]
        ];

        return response()->json(['success' => true, 'data' => $students]);
    }

    /**
     * Get students by category
     */
    public function getStudentsByCategory($category)
    {
        // Here you would filter students by scholarship category
        $students = $this->getStudentsData()->getData()->data;

        if ($category !== 'all') {
            $students = array_filter($students, function($student) use ($category) {
                return strtolower($student->scholarship_type) === $category;
            });
        }

        return response()->json(['success' => true, 'data' => array_values($students)]);
    }

    /**
     * Add new student
     */
    public function addStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|unique:students,student_id',
            'name' => 'required|string',
            'course' => 'required|string',
            'scholarship_type' => 'required|string',
            'gwa' => 'nullable|numeric|min:1|max:4'
        ]);

        // Here you would save the student to database
        return response()->json(['success' => true, 'message' => 'Student added successfully']);
    }

    /**
     * Export students data
     */
    public function exportStudents(Request $request)
    {
        $category = $request->get('category', 'all');

        // Here you would generate CSV/Excel file for students
        return response()->json([
            'success' => true,
            'message' => "Exporting {$category} students data",
            'download_url' => "/downloads/students_{$category}_" . time() . '.xlsx'
        ]);
    }
}



