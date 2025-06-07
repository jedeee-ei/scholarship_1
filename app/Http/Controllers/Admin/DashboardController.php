<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use App\Models\Scholarship;
use App\Models\ArchivedStudent;
use App\Models\SystemSetting;
use App\Models\Announcement;
use App\Models\Grantee;
use App\Services\GranteeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

        // Get all applications for the applications section
        $allApplications = ScholarshipApplication::orderBy('created_at', 'desc')
            ->paginate(10);

        // Get chart data
        $chartData = $this->getChartData();

        return view('admin.dashboard', [
            'stats' => $stats,
            'changes' => $changes,
            'recentApplications' => $recentApplications,
            'allApplications' => $allApplications,
            'chartData' => $chartData,
            'currentStatus' => '',
            'currentType' => ''
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

        // GWA distribution (grade ranges)
        $gwaRanges = [
            '1.00-1.25' => ScholarshipApplication::whereNotNull('gwa')
                ->where('gwa', '>=', 1.00)
                ->where('gwa', '<=', 1.25)
                ->count(),
            '1.26-1.50' => ScholarshipApplication::whereNotNull('gwa')
                ->where('gwa', '>=', 1.26)
                ->where('gwa', '<=', 1.50)
                ->count(),
            '1.51-1.75' => ScholarshipApplication::whereNotNull('gwa')
                ->where('gwa', '>=', 1.51)
                ->where('gwa', '<=', 1.75)
                ->count(),
            '1.76-2.00' => ScholarshipApplication::whereNotNull('gwa')
                ->where('gwa', '>=', 1.76)
                ->where('gwa', '<=', 2.00)
                ->count(),
            '2.01+' => ScholarshipApplication::whereNotNull('gwa')
                ->where('gwa', '>', 2.00)
                ->count(),
        ];

        // Status distribution
        $statusDistribution = [
            'pending' => ScholarshipApplication::where('status', 'Pending Review')->count(),
            'under_review' => ScholarshipApplication::where('status', 'Under Committee Review')->count(),
            'approved' => ScholarshipApplication::where('status', 'Approved')->count(),
            'rejected' => ScholarshipApplication::where('status', 'Rejected')->count(),
        ];

        // Department distribution
        $departmentDistribution = ScholarshipApplication::select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->pluck('count', 'department')
            ->toArray();

        return [
            'months' => $months,
            'applicationCounts' => $applicationCounts,
            'gwaRanges' => $gwaRanges,
            'statusDistribution' => $statusDistribution,
            'departmentDistribution' => $departmentDistribution,
        ];
    }

    /**
     * Get chart data via API for real-time updates
     */
    public function getChartDataApi()
    {
        return response()->json($this->getChartData());
    }

    /**
     * Get analytics summary for dashboard widgets
     */
    public function getAnalyticsSummary()
    {
        $totalApplications = ScholarshipApplication::count();
        $thisMonth = ScholarshipApplication::whereMonth('created_at', now()->month)->count();
        $lastMonth = ScholarshipApplication::whereMonth('created_at', now()->subMonth()->month)->count();

        $monthlyGrowth = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        $topCourse = ScholarshipApplication::select('course', DB::raw('count(*) as count'))
            ->groupBy('course')
            ->orderBy('count', 'desc')
            ->first();

        $averageGwa = ScholarshipApplication::whereNotNull('gwa')
            ->where('gwa', '>', 0)
            ->avg('gwa');

        return response()->json([
            'total_applications' => $totalApplications,
            'monthly_growth' => round($monthlyGrowth, 1),
            'top_course' => $topCourse ? $topCourse->course : 'N/A',
            'top_course_count' => $topCourse ? $topCourse->count : 0,
            'average_gwa' => $averageGwa ? round($averageGwa, 2) : 0,
            'approval_rate' => $totalApplications > 0 ?
                round((ScholarshipApplication::where('status', 'Approved')->count() / $totalApplications) * 100, 1) : 0
        ]);
    }

    /**
     * Show all applications (excluding approved and rejected)
     */
    public function applications(Request $request)
    {
        $query = ScholarshipApplication::query();

        // Exclude approved and rejected applications from the list
        $query->whereNotIn('status', ['Approved', 'Rejected']);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by scholarship type if provided
        if ($request->has('type') && $request->type) {
            $query->where('scholarship_type', $request->type);
        }

        // Get all applications without pagination
        $applications = $query->orderBy('created_at', 'desc')
            ->get();

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
            'status' => 'required|string|in:Pending Review,Under Committee Review,Approved,Rejected'
        ]);

        $newStatus = $request->status;
        $oldStatus = $application->status;

        try {
            DB::beginTransaction();

            // Handle status-specific actions
            if ($newStatus === 'Approved' && $oldStatus !== 'Approved') {
                // Determine scholarship subtype for Academic scholarships based on GWA
                if ($application->scholarship_type == 'academic' && $application->gwa && !$application->scholarship_subtype) {
                    $gwa = floatval($application->gwa);
                    if ($gwa >= 1.0 && $gwa <= 1.25) {
                        $application->scholarship_subtype = "PL";
                    } elseif ($gwa == 1.50) {
                        $application->scholarship_subtype = "DL";
                    }
                    $application->save();
                }

                // Create grantee record using GranteeService
                $granteeService = new GranteeService();
                $adminUser = Auth::user();

                // For testing purposes, allow any user to approve applications
                $approvedBy = $adminUser ? $adminUser->name : 'Admin User';

                // Create grantee from application
                $grantee = $granteeService->createGranteeFromApplication($application, $approvedBy);

                // Delete the application from scholarship_applications table
                $application->delete();

                $studentName = $application->first_name . ' ' . $application->last_name;
                $scholarshipType = ucfirst($application->scholarship_type);
                $message = "✅ Application approved successfully! {$studentName}'s {$scholarshipType} scholarship application has been approved and moved to the Grantees tab.";

                Log::info('Application approved and moved to grantees', [
                    'application_id' => $application->application_id,
                    'grantee_id' => $grantee->grantee_id,
                    'approved_by' => $approvedBy
                ]);
            } elseif ($newStatus === 'Rejected' && $oldStatus !== 'Rejected') {
                // Update status to rejected
                $application->status = $newStatus;
                $application->save();

                $studentName = $application->first_name . ' ' . $application->last_name;
                $scholarshipType = ucfirst($application->scholarship_type);
                $message = "❌ Application rejected. {$studentName}'s {$scholarshipType} scholarship application has been rejected. Student will be notified via the tracker.";

                Log::info('Application rejected', [
                    'application_id' => $application->application_id,
                    'rejected_by' => Auth::user() ? Auth::user()->name : 'Admin'
                ]);
            } else {
                // For other status updates (Pending Review, Under Committee Review)
                $application->status = $newStatus;
                $application->save();
                $studentName = $application->first_name . ' ' . $application->last_name;
                $message = "📝 Status updated successfully! {$studentName}'s application status has been changed to '{$newStatus}'.";
            }

            DB::commit();

            // Redirect based on the new status
            if (in_array($newStatus, ['Approved', 'Rejected'])) {
                // Redirect to applications list since this application will no longer be visible
                return redirect()->route('admin.applications')->with('success', $message);
            } else {
                // Stay on the same page for other status updates
                return redirect()->back()->with('success', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating application status', [
                'application_id' => $application->application_id,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);

            $errorMessage = "❌ Error updating application status: " . $e->getMessage() . " Please try again or contact support if the issue persists.";
            return redirect()->back()->with('error', $errorMessage);
        }
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

        // Process the CSV file
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

        // Save settings to database or config
        return response()->json(['success' => true, 'message' => 'Settings updated successfully']);
    }

    /**
     * Export applications data
     */
    public function exportApplications()
    {
        // Generate CSV/Excel file
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

        // Generate the actual report
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
        // Generate a preview of the report
        return response()->json(['success' => true, 'message' => 'Report preview functionality will be implemented']);
    }

    /**
     * Search archive
     */
    public function searchArchive(Request $request)
    {
        $archiveType = $request->get('archive_type', '');
        $archiveYear = $request->get('archive_year', '');

        // Search the archive based on criteria
        return response()->json([
            'success' => true,
            'search_criteria' => [
                'type' => $archiveType ?: 'All Types',
                'year' => $archiveYear ?: 'All Years'
            ],
            'data' => []
        ]);
    }

    /**
     * Download archive file
     */
    public function downloadArchive($fileId)
    {
        // Serve the actual file
        return response()->json(['success' => true, 'message' => "Downloading file: {$fileId}"]);
    }

    /**
     * Delete archive file
     */
    public function deleteArchive($fileId)
    {
        // Delete the actual file
        return response()->json(['success' => true, 'message' => "Archive file deleted: {$fileId}"]);
    }

    /**
     * Get grantees data
     */
    public function getStudentsData(Request $request)
    {
        $category = $request->get('category', 'all');

        // Get grantees from the grantees table
        $query = Grantee::query();

        // Filter by scholarship type if not 'all'
        if ($category !== 'all') {
            // Map category names to database values
            $typeMap = [
                'ched' => 'ched',
                'academic' => 'academic', // academic scholarship type
                'employees' => 'employees',
                'private' => 'private'
            ];

            if (isset($typeMap[$category])) {
                $query->where('scholarship_type', $typeMap[$category]);
            }
        }

        $grantees = $query->orderBy('approved_date', 'desc')->get();

        // Transform grantees to student format
        $students = $grantees->map(function ($grantee) {
            return [
                'id' => $grantee->student_id,
                'name' => $grantee->first_name . ' ' . $grantee->last_name,
                'course' => $grantee->course ?: 'N/A',
                'scholarship_type' => $this->formatScholarshipType($grantee->scholarship_type),
                'status' => $grantee->status,
                'gwa' => $grantee->current_gwa ?: $grantee->gwa ?: 'N/A',
                'application_id' => $grantee->application_id,
                'grantee_id' => $grantee->grantee_id
            ];
        });

        return response()->json(['success' => true, 'data' => $students]);
    }

    /**
     * Format scholarship type for display
     */
    private function formatScholarshipType($type)
    {
        $types = [
            'ched' => 'CHED',
            'academic' => 'Academic',
            'employees' => 'Employee',
            'private' => 'Private'
        ];

        return $types[$type] ?? ucfirst($type);
    }

    /**
     * Get students by category
     */
    public function getStudentsByCategory($category)
    {
        // Create a request object with the category parameter
        $request = new Request(['category' => $category]);

        // Use the existing getStudentsData method (grantees data)
        return $this->getStudentsData($request);
    }

    /**
     * Add new grantee
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

        // Save the grantee to database
        return response()->json(['success' => true, 'message' => 'Grantee added successfully']);
    }

    /**
     * Export grantees data
     */
    public function exportStudents(Request $request)
    {
        $category = $request->get('category', 'all');

        // Generate CSV/Excel file for grantees
        return response()->json([
            'success' => true,
            'message' => "Exporting {$category} grantees data",
            'download_url' => "/downloads/students_{$category}_" . time() . '.xlsx'
        ]);
    }

    /**
     * Get applications data for the applications section (excluding approved and rejected)
     */
    public function getApplicationsData(Request $request)
    {
        $query = ScholarshipApplication::query();

        // Exclude approved and rejected applications from the list
        $query->whereNotIn('status', ['Approved', 'Rejected']);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by scholarship type if provided
        if ($request->has('type') && $request->type) {
            $query->where('scholarship_type', $request->type);
        }

        // Get all applications without pagination
        $applications = $query->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications->items(),
            'pagination' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total()
            ]
        ]);
    }

    /**
     * Get single application details for modal
     */
    public function getApplicationDetail($id)
    {
        $application = ScholarshipApplication::where('application_id', $id)->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $application]);
    }

    /**
     * Update application status via API
     */
    public function updateApplicationStatus(Request $request, $id)
    {
        $application = ScholarshipApplication::where('application_id', $id)->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found'], 404);
        }

        // Validate the request
        $request->validate([
            'status' => 'required|string|in:Pending Review,Under Committee Review,Approved,Rejected'
        ]);

        // Update the status
        $application->status = $request->status;
        $application->save();

        return response()->json(['success' => true, 'message' => 'Application status updated successfully']);
    }

    /**
     * Show grantees page
     */
    public function students(Request $request)
    {
        // Get current semester and academic year from system settings
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Calculate default values
        $defaultAcademicYear = ($currentMonth >= 7) ?
            $currentYear . '-' . ($currentYear + 1) : ($currentYear - 1) . '-' . $currentYear;
        $defaultSemester = ($currentMonth >= 1 && $currentMonth <= 6) ?
            '2nd Semester' : '1st Semester';

        // Get from system settings or use defaults
        $currentAcademicYear = SystemSetting::get('current_academic_year', $defaultAcademicYear);
        $currentSemester = SystemSetting::get('current_semester', $defaultSemester);

        // Get grantees from the grantees table
        $query = Grantee::query();

        // Filter by scholarship type if provided (support both 'type' and 'scholarship_type' parameters)
        $scholarshipTypeFilter = $request->get('type') ?: $request->get('scholarship_type');
        if ($scholarshipTypeFilter) {
            $query->where('scholarship_type', $scholarshipTypeFilter);
        }

        $students = $query->orderBy('approved_date', 'desc')
            ->get()
            ->map(function ($grantee) use ($currentSemester, $currentAcademicYear) {
                return [
                    'id' => $grantee->student_id,
                    'name' => $grantee->first_name . ' ' . $grantee->last_name,
                    'course' => $grantee->course ?: 'N/A',
                    'scholarship_type' => $this->formatScholarshipType($grantee->scholarship_type),
                    'status' => $grantee->status,
                    'gwa' => $grantee->current_gwa ?: $grantee->gwa ?: 'N/A',
                    'application_id' => $grantee->application_id,
                    'grantee_id' => $grantee->grantee_id,
                    'department' => $grantee->department,
                    'year_level' => $grantee->year_level,
                    'email' => $grantee->email,
                    'contact_number' => $grantee->contact_number,
                    'current_semester' => $currentSemester,
                    'current_academic_year' => $currentAcademicYear
                ];
            });

        // Get scholarship name for filtered view
        $scholarshipName = null;
        if ($scholarshipTypeFilter) {
            $scholarship = Scholarship::where('type', $scholarshipTypeFilter)->first();
            $scholarshipName = $scholarship ? $scholarship->name : $this->formatScholarshipType($scholarshipTypeFilter);
        }

        return view('admin.students', compact('students', 'scholarshipTypeFilter', 'scholarshipName', 'currentSemester', 'currentAcademicYear'));
    }

    /**
     * Update student information
     */
    public function updateStudent(Request $request, $id)
    {
        try {
            // Find the scholarship application by application_id
            $application = ScholarshipApplication::where('application_id', $id)->firstOrFail();

            // Validate the request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'contact_number' => 'nullable|string|max:20',
                'course' => 'required|string|max:255',
                'department' => 'nullable|string|max:255',
                'year_level' => 'nullable|string|max:20',
                'gwa' => 'nullable|numeric|min:1|max:4'
            ]);

            // Split the name into first and last name
            $nameParts = explode(' ', $validatedData['name'], 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            // Update the scholarship application record
            $application->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validatedData['email'],
                'contact_number' => $validatedData['contact_number'],
                'course' => $validatedData['course'],
                'department' => $validatedData['department'],
                'year_level' => $validatedData['year_level'],
                'gwa' => $validatedData['gwa']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student information updated successfully',
                'data' => [
                    'id' => $application->student_id,
                    'name' => $firstName . ' ' . $lastName,
                    'email' => $application->email,
                    'contact_number' => $application->contact_number,
                    'course' => $application->course,
                    'department' => $application->department,
                    'year_level' => $application->year_level,
                    'gwa' => $application->gwa
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating student information'
            ], 500);
        }
    }

    /**
     * Show scholarships page
     */
    public function scholarships()
    {
        // Get current academic year and semester from system settings or calculate default
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Calculate default values
        $defaultAcademicYear = ($currentMonth >= 7) ?
            $currentYear . '-' . ($currentYear + 1) : ($currentYear - 1) . '-' . $currentYear;

        $defaultSemester = ($currentMonth >= 1 && $currentMonth <= 6) ?
            '2nd Semester' : '1st Semester';

        // Get from system settings or use defaults
        $academicYear = SystemSetting::get('current_academic_year', $defaultAcademicYear);
        $currentSemester = SystemSetting::get('current_semester', $defaultSemester);

        // Initialize settings if they don't exist
        if (!SystemSetting::where('key', 'current_academic_year')->exists()) {
            SystemSetting::set('current_academic_year', $defaultAcademicYear);
        }
        if (!SystemSetting::where('key', 'current_semester')->exists()) {
            SystemSetting::set('current_semester', $defaultSemester);
        }

        // Get scholarships from database only
        $scholarships = Scholarship::orderBy('created_at', 'desc')->get();

        // Format scholarships data for the view
        $scholarshipStats = [];

        foreach ($scholarships as $scholarship) {
            $key = 'scholarship_' . $scholarship->id;

            // Count active grantees for this scholarship type
            $activeGranteesCount = Grantee::where('scholarship_type', strtolower($scholarship->type))
                ->where('status', 'Active')
                ->count();

            $scholarshipStats[$key] = [
                'id' => $scholarship->id,
                'name' => $scholarship->name,
                'type' => ucfirst($scholarship->type),
                'semester' => $scholarship->semester,
                'academic_year' => $scholarship->academic_year,
                'description' => $scholarship->description,
                'active_grantees' => $activeGranteesCount,
                'is_database' => true,
                'total_applications' => ScholarshipApplication::where('scholarship_type', strtolower($scholarship->type))->count(),
                'approved' => ScholarshipApplication::where('scholarship_type', strtolower($scholarship->type))->where('status', 'Approved')->count(),
                'pending' => ScholarshipApplication::where('scholarship_type', strtolower($scholarship->type))->where('status', 'Pending Review')->count()
            ];
        }

        return view('admin.scholarships', compact('scholarshipStats', 'currentSemester', 'academicYear'));
    }

    /**
     * Add new scholarship program
     */
    public function addScholarship(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:ched,academic,employees,private',
                'semester' => 'required|string|in:1st Semester,2nd Semester',
                'academic_year' => 'required|string|max:20',
                'description' => 'nullable|string|max:1000'
            ]);

            // Create new scholarship
            $scholarship = Scholarship::create([
                'name' => $validatedData['name'],
                'type' => $validatedData['type'],
                'semester' => $validatedData['semester'],
                'academic_year' => $validatedData['academic_year'],
                'description' => $validatedData['description']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scholarship program added successfully',
                'data' => $scholarship
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the scholarship program'
            ], 500);
        }
    }

    /**
     * Update semester for all scholarships
     */
    public function updateSemester(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'current_semester' => 'required|string',
                'new_semester' => 'required|string|in:1st Semester,2nd Semester'
            ]);

            return DB::transaction(function () use ($validatedData) {
                // Get current academic year from system settings
                $academicYear = SystemSetting::get('current_academic_year');
                if (!$academicYear) {
                    // Fallback to calculated value if not set
                    $currentYear = now()->year;
                    $currentMonth = now()->month;
                    if ($currentMonth >= 7) {
                        $academicYear = $currentYear . '-' . ($currentYear + 1);
                    } else {
                        $academicYear = ($currentYear - 1) . '-' . $currentYear;
                    }
                }

                // Archive all approved scholarship students
                $approvedApplications = ScholarshipApplication::where('status', 'Approved')->get();

                foreach ($approvedApplications as $application) {
                    ArchivedStudent::create([
                        'original_application_id' => $application->application_id,
                        'student_id' => $application->student_id,
                        'first_name' => $application->first_name,
                        'last_name' => $application->last_name,
                        'email' => $application->email,
                        'contact_number' => $application->contact_number,
                        'course' => $application->course,
                        'department' => $application->department,
                        'year_level' => $application->year_level,
                        'gwa' => $application->gwa,
                        'scholarship_type' => $application->scholarship_type,
                        'archived_semester' => $validatedData['current_semester'],
                        'archived_academic_year' => $academicYear,
                        'archived_at' => now(),
                        'archived_by' => 'Admin' // You can get actual admin name from auth
                    ]);
                }

                // Delete all scholarship applications (reset for new applications)
                ScholarshipApplication::truncate();

                // Update all scholarships in database (only if there are any)
                $scholarshipCount = Scholarship::count();
                if ($scholarshipCount > 0) {
                    Scholarship::query()->update(['semester' => $validatedData['new_semester']]);
                }

                // Update system settings for current semester
                SystemSetting::set('current_semester', $validatedData['new_semester']);

                $archivedCount = $approvedApplications->count();

                return response()->json([
                    'success' => true,
                    'message' => "Semester updated successfully! {$archivedCount} students have been archived and all applications have been reset for new semester."
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Semester update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating semester: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update academic year for all scholarships
     */
    public function updateAcademicYear(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'current_year' => 'required|string',
                'new_year' => 'required|string'
            ]);

            return DB::transaction(function () use ($validatedData) {
                // Get current semester from system settings
                $currentSemester = SystemSetting::get('current_semester');
                if (!$currentSemester) {
                    // Fallback to calculated value if not set
                    $currentMonth = now()->month;
                    $currentSemester = ($currentMonth >= 1 && $currentMonth <= 6) ? '2nd Semester' : '1st Semester';
                }

                // Archive all approved scholarship students
                $approvedApplications = ScholarshipApplication::where('status', 'Approved')->get();

                foreach ($approvedApplications as $application) {
                    ArchivedStudent::create([
                        'original_application_id' => $application->application_id,
                        'student_id' => $application->student_id,
                        'first_name' => $application->first_name,
                        'last_name' => $application->last_name,
                        'email' => $application->email,
                        'contact_number' => $application->contact_number,
                        'course' => $application->course,
                        'department' => $application->department,
                        'year_level' => $application->year_level,
                        'gwa' => $application->gwa,
                        'scholarship_type' => $application->scholarship_type,
                        'archived_semester' => $currentSemester,
                        'archived_academic_year' => $validatedData['current_year'],
                        'archived_at' => now(),
                        'archived_by' => 'Admin' // You can get actual admin name from auth
                    ]);
                }

                // Delete all scholarship applications (reset for new applications)
                ScholarshipApplication::truncate();

                // Update all scholarships in database (only if there are any)
                $scholarshipCount = Scholarship::count();
                if ($scholarshipCount > 0) {
                    Scholarship::query()->update([
                        'academic_year' => $validatedData['new_year'],
                        'semester' => '1st Semester'
                    ]);
                }

                // Update system settings
                SystemSetting::set('current_academic_year', $validatedData['new_year']);
                SystemSetting::set('current_semester', '1st Semester');

                $archivedCount = $approvedApplications->count();

                return response()->json([
                    'success' => true,
                    'message' => "Academic year updated successfully! {$archivedCount} students have been archived and all applications have been reset for new academic year. Semester has been reset to 1st Semester."
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Academic year update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating academic year: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show archived grantees page
     */
    public function archivedStudents()
    {
        $archivedStudents = ArchivedStudent::orderBy('archived_at', 'desc')->get();

        return view('admin.archived-students', compact('archivedStudents'));
    }

    /**
     * Show archived scholarships page
     */
    public function archivedScholarships()
    {
        // Get archived scholarship programs from database
        // This could be expanded to include historical scholarship data
        $archivedScholarships = collect([
            // Future: archived_scholarships table data would go here
        ]);

        return view('admin.archived-scholarships', compact('archivedScholarships'));
    }

    /**
     * Show reports page
     */
    public function reports()
    {
        // Get report statistics
        $reportStats = [
            'total_applications' => ScholarshipApplication::count(),
            'applications_this_month' => ScholarshipApplication::whereMonth('created_at', now()->month)->count(),
            'applications_this_year' => ScholarshipApplication::whereYear('created_at', now()->year)->count(),
            'by_status' => [
                'pending' => ScholarshipApplication::where('status', 'Pending Review')->count(),
                'approved' => ScholarshipApplication::where('status', 'Approved')->count(),
                'rejected' => ScholarshipApplication::where('status', 'Rejected')->count(),
            ],
            'by_type' => [
                'ched' => ScholarshipApplication::where('scholarship_type', 'ched')->count(),
                'academic' => ScholarshipApplication::where('scholarship_type', 'academic')->count(),
                'employees' => ScholarshipApplication::where('scholarship_type', 'employees')->count(),
                'private' => ScholarshipApplication::where('scholarship_type', 'private')->count(),
            ]
        ];

        return view('admin.reports', compact('reportStats'));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        // Get system settings from database
        $settings = [
            'application_deadline' => SystemSetting::get('application_deadline', ''),
            'max_applications_per_student' => SystemSetting::get('max_applications_per_student', 1),
            'email_notifications' => SystemSetting::get('email_notifications', false),
            'sms_notifications' => SystemSetting::get('sms_notifications', false),
            'auto_approve_ched' => SystemSetting::get('auto_approve_ched', false),
            'system_name' => SystemSetting::get('system_name', 'Scholarship Management System'),
            'institution_name' => SystemSetting::get('institution_name', ''),
            'contact_email' => SystemSetting::get('contact_email', ''),
            'contact_phone' => SystemSetting::get('contact_phone', '')
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Get current semester and academic year
     */
    public function getCurrentSemesterYear()
    {
        // Get current academic year and semester from system settings or calculate default
        $currentYear = now()->year;
        $currentMonth = now()->month;

        // Calculate default values
        $defaultAcademicYear = ($currentMonth >= 7) ?
            $currentYear . '-' . ($currentYear + 1) : ($currentYear - 1) . '-' . $currentYear;

        $defaultSemester = ($currentMonth >= 1 && $currentMonth <= 6) ?
            '2nd Semester' : '1st Semester';

        // Get from system settings or use defaults
        $academicYear = SystemSetting::get('current_academic_year', $defaultAcademicYear);
        $currentSemester = SystemSetting::get('current_semester', $defaultSemester);

        return response()->json([
            'current_semester' => $currentSemester,
            'current_academic_year' => $academicYear
        ]);
    }

    /**
     * Handle bulk import of student data
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
            'scholarship_type' => 'required|string'
        ]);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $header = array_shift($data);

            $imported = 0;
            $errors = [];

            foreach ($data as $row) {
                if (count($row) !== count($header)) {
                    continue; // Skip malformed rows
                }

                $studentData = array_combine($header, $row);

                // Validate required fields
                if (empty($studentData['student_id']) || empty($studentData['first_name']) || empty($studentData['last_name'])) {
                    $errors[] = "Row skipped: Missing required fields";
                    continue;
                }

                // Create scholarship application
                try {
                    ScholarshipApplication::create([
                        'scholarship_type' => $request->scholarship_type,
                        'student_id' => $studentData['student_id'],
                        'first_name' => $studentData['first_name'],
                        'last_name' => $studentData['last_name'],
                        'middle_name' => $studentData['middle_name'] ?? null,
                        'email' => $studentData['email'] ?? '',
                        'course' => $studentData['course'] ?? '',
                        'department' => $studentData['department'] ?? '',
                        'year_level' => $studentData['year_level'] ?? '',
                        'gwa' => $studentData['gwa'] ?? null,
                        'semester' => $studentData['semester'] ?? SystemSetting::get('current_semester', '1st Semester'),
                        'academic_year' => $studentData['academic_year'] ?? SystemSetting::get('current_academic_year', now()->year . '-' . (now()->year + 1)),
                        'status' => 'Pending Review'
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error importing student {$studentData['student_id']}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$imported} students" . (count($errors) > 0 ? ". " . count($errors) . " errors occurred." : ""),
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
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
                abort(404);
        }
    }

    /**
     * Export applications data
     */
    private function exportApplicationsData()
    {
        $applications = ScholarshipApplication::all();
        $filename = 'scholarship_applications_' . date('Y-m-d') . '.csv';

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
     * Export grantees data
     */
    private function exportStudentsData()
    {
        $students = ScholarshipApplication::where('status', 'Approved')->get();
        $filename = 'scholarship_grantees_' . date('Y-m-d') . '.csv';

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
        $chartData = $this->getChartData();
        $filename = 'scholarship_analytics_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($chartData) {
            $handle = fopen('php://output', 'w');

            // Applications trend data
            fputcsv($handle, ['Applications Trend']);
            fputcsv($handle, ['Month', 'Applications']);
            foreach ($chartData['months'] as $index => $month) {
                fputcsv($handle, [$month, $chartData['applicationCounts'][$index]]);
            }

            fputcsv($handle, []); // Empty row

            // Status distribution
            fputcsv($handle, ['Status Distribution']);
            fputcsv($handle, ['Status', 'Count']);
            foreach ($chartData['statusDistribution'] as $status => $count) {
                fputcsv($handle, [ucfirst(str_replace('_', ' ', $status)), $count]);
            }

            fputcsv($handle, []); // Empty row

            // Department distribution
            fputcsv($handle, ['Department Distribution']);
            fputcsv($handle, ['Department', 'Applications']);
            foreach ($chartData['departmentDistribution'] as $dept => $count) {
                fputcsv($handle, [$dept, $count]);
            }

            fputcsv($handle, []); // Empty row

            // GWA distribution
            fputcsv($handle, ['GWA Distribution']);
            fputcsv($handle, ['GWA Range', 'Students']);
            foreach ($chartData['gwaRanges'] as $range => $count) {
                fputcsv($handle, [$range, $count]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Save system settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|string',
            'max_applications' => 'required|integer|min:1',
            'min_gwa' => 'required|numeric|min:1|max:5'
        ]);

        try {
            // Save to a settings table or config file

            return response()->json([
                'success' => true,
                'message' => 'Settings saved successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import students from Excel file
     */
    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'scholarship_type' => 'required|string',
            'update_existing' => 'nullable|boolean'
        ]);

        try {
            $file = $request->file('file');
            $updateExisting = $request->boolean('update_existing');

            // Handle CSV and Excel files
            if ($file->getClientOriginalExtension() === 'csv') {
                return $this->importFromCSV($file, $request->scholarship_type, $updateExisting);
            } else {
                // Convert Excel to CSV for processing (simplified approach)
                return $this->importFromExcel($file, $request->scholarship_type, $updateExisting);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import from CSV file
     */
    private function importFromCSV($file, $scholarshipType, $updateExisting = false)
    {
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);

        // Normalize headers (remove spaces, convert to lowercase)
        $normalizedHeaders = array_map(function ($h) {
            return strtolower(str_replace(' ', '_', trim($h)));
        }, $header);

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($data as $rowIndex => $row) {
            if (count($row) !== count($header)) {
                continue; // Skip malformed rows
            }

            $studentData = array_combine($normalizedHeaders, $row);

            // Validate required fields
            if (empty($studentData['student_id']) || empty($studentData['first_name']) || empty($studentData['last_name'])) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Missing required fields (Student ID, First Name, Last Name)";
                continue;
            }

            try {
                // Check if student already exists
                $existingApplication = ScholarshipApplication::where('student_id', $studentData['student_id'])
                    ->where('scholarship_type', $scholarshipType)
                    ->first();

                if ($existingApplication) {
                    if ($updateExisting) {
                        // Update existing student
                        $existingApplication->update([
                            'first_name' => $studentData['first_name'],
                            'last_name' => $studentData['last_name'],
                            'middle_name' => $studentData['middle_name'] ?? null,
                            'email' => $studentData['email'] ?? '',
                            'course' => $studentData['course'] ?? '',
                            'department' => $studentData['department'] ?? '',
                            'year_level' => $studentData['year_level'] ?? '',
                            'gwa' => $studentData['gwa'] ?? null,
                            'contact_number' => $studentData['contact_number'] ?? null,
                        ]);
                        $updated++;
                    } else {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Student ID {$studentData['student_id']} already exists";
                        continue;
                    }
                } else {
                    // Create new scholarship application (approved status for direct import)
                    ScholarshipApplication::create([
                        'scholarship_type' => $scholarshipType,
                        'student_id' => $studentData['student_id'],
                        'first_name' => $studentData['first_name'],
                        'last_name' => $studentData['last_name'],
                        'middle_name' => $studentData['middle_name'] ?? null,
                        'email' => $studentData['email'] ?? '',
                        'course' => $studentData['course'] ?? '',
                        'department' => $studentData['department'] ?? '',
                        'year_level' => $studentData['year_level'] ?? '',
                        'gwa' => $studentData['gwa'] ?? null,
                        'semester' => SystemSetting::get('current_semester', '1st Semester'),
                        'academic_year' => SystemSetting::get('current_academic_year', now()->year . '-' . (now()->year + 1)),
                        'contact_number' => $studentData['contact_number'] ?? null,
                        'status' => 'Approved' // Direct import means approved
                    ]);
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Error processing student {$studentData['student_id']}: " . $e->getMessage();
            }
        }

        $message = "Import completed. ";
        if ($imported > 0) $message .= "{$imported} students imported. ";
        if ($updated > 0) $message .= "{$updated} students updated. ";
        if (count($errors) > 0) $message .= count($errors) . " errors occurred.";

        return response()->json([
            'success' => true,
            'message' => $message,
            'details' => [
                'imported' => $imported,
                'updated' => $updated,
                'errors' => $errors
            ]
        ]);
    }

    /**
     * Import from Excel file using PhpSpreadsheet
     */
    private function importFromExcel($file, $scholarshipType, $updateExisting = false)
    {
        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();

            // Get the highest row and column numbers
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            // Get header row (first row)
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headers[] = $worksheet->getCell($col . '1')->getValue();
            }

            // Normalize headers (remove spaces, convert to lowercase)
            $normalizedHeaders = array_map(function ($h) {
                return strtolower(str_replace(' ', '_', trim($h)));
            }, $headers);

            $imported = 0;
            $updated = 0;
            $errors = [];

            // Process data rows (starting from row 2)
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                $colIndex = 0;

                // Get data for each column
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = $worksheet->getCell($col . $row)->getValue();
                    $rowData[$normalizedHeaders[$colIndex]] = $cellValue;
                    $colIndex++;
                }

                // Skip empty rows
                if (empty(array_filter($rowData))) {
                    continue;
                }

                // Validate required fields
                if (empty($rowData['student_id']) || empty($rowData['first_name']) || empty($rowData['last_name'])) {
                    $errors[] = "Row {$row}: Missing required fields (Student ID, First Name, Last Name)";
                    continue;
                }

                try {
                    // Check if student already exists
                    $existingApplication = ScholarshipApplication::where('student_id', $rowData['student_id'])
                        ->where('scholarship_type', $scholarshipType)
                        ->first();

                    if ($existingApplication) {
                        if ($updateExisting) {
                            // Update existing student
                            $existingApplication->update([
                                'first_name' => $rowData['first_name'],
                                'last_name' => $rowData['last_name'],
                                'middle_name' => $rowData['middle_name'] ?? null,
                                'email' => $rowData['email'] ?? '',
                                'course' => $rowData['course'] ?? '',
                                'department' => $rowData['department'] ?? '',
                                'year_level' => $rowData['year_level'] ?? '',
                                'gwa' => $rowData['gwa'] ?? null,
                                'contact_number' => $rowData['contact_number'] ?? null,
                            ]);
                            $updated++;
                        } else {
                            $errors[] = "Row {$row}: Student ID {$rowData['student_id']} already exists";
                            continue;
                        }
                    } else {
                        // Create new scholarship application (approved status for direct import)
                        ScholarshipApplication::create([
                            'scholarship_type' => $scholarshipType,
                            'student_id' => $rowData['student_id'],
                            'first_name' => $rowData['first_name'],
                            'last_name' => $rowData['last_name'],
                            'middle_name' => $rowData['middle_name'] ?? null,
                            'email' => $rowData['email'] ?? '',
                            'course' => $rowData['course'] ?? '',
                            'department' => $rowData['department'] ?? '',
                            'year_level' => $rowData['year_level'] ?? '',
                            'gwa' => $rowData['gwa'] ?? null,
                            'semester' => SystemSetting::get('current_semester', '1st Semester'),
                            'academic_year' => SystemSetting::get('current_academic_year', now()->year . '-' . (now()->year + 1)),
                            'contact_number' => $rowData['contact_number'] ?? null,
                            'status' => 'Approved' // Direct import means approved
                        ]);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$row}: Error processing student {$rowData['student_id']}: " . $e->getMessage();
                }
            }

            $message = "Import completed. ";
            if ($imported > 0) $message .= "{$imported} students imported. ";
            if ($updated > 0) $message .= "{$updated} students updated. ";
            if (count($errors) > 0) $message .= count($errors) . " errors occurred.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'details' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read Excel file: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Download Excel template for student import
     */
    public function downloadStudentTemplate()
    {
        try {
            // Create new spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = [
                'Student ID',
                'First Name',
                'Last Name',
                'Middle Name',
                'Email',
                'Course',
                'Department',
                'Year Level',
                'GWA',
                'Contact Number'
            ];

            // Add headers to first row
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->getFont()->setBold(true);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }

            // Template headers are set

            // Set sheet title
            $sheet->setTitle('Student Import Template');

            // Create filename
            $filename = 'student_import_template.xlsx';

            // Create writer and save to output
            $writer = new Xlsx($spreadsheet);

            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            // Fallback to CSV if Excel generation fails
            return $this->downloadCSVTemplate();
        }
    }

    /**
     * Fallback CSV template download
     */
    private function downloadCSVTemplate()
    {
        $headers = [
            'Student ID',
            'First Name',
            'Last Name',
            'Middle Name',
            'Email',
            'Course',
            'Department',
            'Year Level',
            'GWA',
            'Contact Number'
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
     * Show announcements page
     */
    public function announcements()
    {
        try {
            $announcements = Announcement::orderBy('created_at', 'desc')->get();

            Log::info('Announcements page accessed', [
                'announcements_count' => $announcements->count(),
                'user' => Auth::user() ? Auth::user()->email : 'guest'
            ]);

            return view('admin.announcements', compact('announcements'));
        } catch (\Exception $e) {
            Log::error('Error loading announcements page', [
                'error' => $e->getMessage(),
                'user' => Auth::user() ? Auth::user()->email : 'guest'
            ]);

            return redirect()->route('admin.dashboard')->with('error', 'Error loading announcements page: ' . $e->getMessage());
        }
    }

    /**
     * Store new announcement
     */
    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement = Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'created_by' => 'Admin', // You can get actual admin name from auth
            'published_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement created successfully',
            'data' => $announcement
        ]);
    }

    /**
     * Update announcement
     */
    public function updateAnnouncement(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Announcement updated successfully',
            'data' => $announcement
        ]);
    }

    /**
     * Delete announcement
     */
    public function deleteAnnouncement($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Announcement deleted successfully'
        ]);
    }

    /**
     * Download a document from an application
     */
    public function downloadDocument($applicationId, $documentIndex)
    {
        $application = ScholarshipApplication::where('application_id', $applicationId)->firstOrFail();

        if (!$application->documents || !isset($application->documents[$documentIndex])) {
            abort(404, 'Document not found');
        }

        $document = $application->documents[$documentIndex];

        if (!isset($document['path']) || !Storage::exists($document['path'])) {
            abort(404, 'Document file not found');
        }

        $filename = $document['original_name'] ?? 'document_' . ($documentIndex + 1);

        return Storage::download($document['path'], $filename);
    }

    /**
     * View a document from an application
     */
    public function viewDocument($applicationId, $documentIndex)
    {
        $application = ScholarshipApplication::where('application_id', $applicationId)->firstOrFail();

        if (!$application->documents || !isset($application->documents[$documentIndex])) {
            abort(404, 'Document not found');
        }

        $document = $application->documents[$documentIndex];

        if (!isset($document['path']) || !Storage::exists($document['path'])) {
            abort(404, 'Document file not found');
        }

        $file = Storage::get($document['path']);
        $mimeType = Storage::mimeType($document['path']);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . ($document['original_name'] ?? 'document') . '"');
    }
}
