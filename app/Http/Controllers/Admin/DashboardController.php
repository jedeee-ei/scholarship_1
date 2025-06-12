<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use App\Models\ArchivedStudent;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard
     */
    public function index()
    {
        // Get recent applications (pending ones)
        $recentApplications = ScholarshipApplication::orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get all applications for the applications section (pending ones)
        $allApplications = ScholarshipApplication::orderBy('created_at', 'desc')
            ->paginate(10);

        // Get chart data using real grantee data
        $chartData = $this->getChartData();

        // Get application status setting
        $applicationStatus = SystemSetting::get('application_status', 'closed');

        // Get current semester and academic year
        $currentSemester = SystemSetting::get('current_semester', '1st Semester');
        $currentAcademicYear = SystemSetting::get('current_academic_year', '2024-2025');

        // Get pending applications count for current semester
        $pendingApplicationsCount = ScholarshipApplication::where('semester', $currentSemester)
            ->where('academic_year', $currentAcademicYear)
            ->whereIn('status', ['Pending Review', 'Under Review', 'Pending'])
            ->count();

        // Get approved applications count (active grantees)
        $approvedApplicationsCount = Grantee::count();

        return view('admin.dashboard', [
            'recentApplications' => $recentApplications,
            'allApplications' => $allApplications,
            'chartData' => $chartData,
            'currentStatus' => '',
            'currentType' => '',
            'applicationStatus' => $applicationStatus,
            'currentSemester' => $currentSemester,
            'currentAcademicYear' => $currentAcademicYear,
            'pendingApplicationsCount' => $pendingApplicationsCount,
            'approvedApplicationsCount' => $approvedApplicationsCount
        ]);
    }

    /**
     * Get chart data for dashboard (simplified to 2 charts)
     */
    private function getChartData()
    {
        // 1. Pie Chart: Grantees by Scholarship Type
        $scholarshipDistribution = [];

        // Get grantees by scholarship type
        $scholarshipTypes = Grantee::select('scholarship_type', DB::raw('count(*) as count'))
            ->whereNotNull('scholarship_type')
            ->groupBy('scholarship_type')
            ->pluck('count', 'scholarship_type')
            ->toArray();

        // Map scholarship types to display names
        $typeMapping = [
            'government' => 'Government',
            'academic' => 'Academic',
            'employees' => 'Employee',
            'alumni' => 'Alumni'
        ];

        foreach ($scholarshipTypes as $type => $count) {
            $displayName = $typeMapping[$type] ?? ucfirst($type);
            $scholarshipDistribution[$displayName] = $count;
        }

        // 2. Line Chart: Scholarships through the years (last 5 years)
        $years = [];
        $scholarshipCounts = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $years[] = $year;

            // Count grantees created in this year
            $count = Grantee::whereYear('created_at', $year)->count();

            // Also count archived students from this year
            $archivedCount = ArchivedStudent::whereYear('archived_at', $year)->count();

            $scholarshipCounts[] = $count + $archivedCount;
        }

        return [
            'scholarshipDistribution' => $scholarshipDistribution,
            'years' => $years,
            'scholarshipCounts' => $scholarshipCounts,
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
     * Get analytics summary for dashboard widgets using real data
     */
    public function getAnalyticsSummary()
    {
        $totalGrantees = Grantee::count();
        $totalApplications = ScholarshipApplication::count();
        $totalAll = $totalGrantees + $totalApplications;

        $thisMonth = Grantee::whereMonth('approved_date', now()->month)->count() +
            ScholarshipApplication::whereMonth('created_at', now()->month)->count();
        $lastMonth = Grantee::whereMonth('approved_date', now()->subMonth()->month)->count() +
            ScholarshipApplication::whereMonth('created_at', now()->subMonth()->month)->count();

        $monthlyGrowth = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        // Get top course from grantees (real data)
        $topCourse = Grantee::select('course', DB::raw('count(*) as count'))
            ->whereNotNull('course')
            ->groupBy('course')
            ->orderBy('count', 'desc')
            ->first();

        // Get average GWA from grantees
        $averageGwa = Grantee::where(function ($query) {
            $query->whereNotNull('gwa')->where('gwa', '>', 0)
                ->orWhere(function ($q) {
                    $q->whereNotNull('current_gwa')->where('current_gwa', '>', 0);
                });
        })
            ->selectRaw('AVG(COALESCE(NULLIF(current_gwa, 0), NULLIF(gwa, 0))) as avg_gwa')
            ->value('avg_gwa');

        // Calculate approval rate (grantees vs total)
        $approvalRate = $totalAll > 0 ? round(($totalGrantees / $totalAll) * 100, 1) : 0;

        return response()->json([
            'total_applications' => $totalAll,
            'monthly_growth' => round($monthlyGrowth, 1),
            'top_course' => $topCourse ? $topCourse->course : 'N/A',
            'top_course_count' => $topCourse ? $topCourse->count : 0,
            'average_gwa' => $averageGwa ? round($averageGwa, 2) : 0,
            'approval_rate' => $approvalRate
        ]);
    }

    /**
     * Get real-time dashboard stats for AJAX updates
     */
    public function getDashboardStats()
    {
        // Get current semester and academic year
        $currentSemester = SystemSetting::get('current_semester', '1st Semester');
        $currentAcademicYear = SystemSetting::get('current_academic_year', '2024-2025');

        // Get pending applications count for current semester
        $pendingApplicationsCount = ScholarshipApplication::where('semester', $currentSemester)
            ->where('academic_year', $currentAcademicYear)
            ->whereIn('status', ['Pending Review', 'Under Review', 'Pending'])
            ->count();

        // Get approved applications count (active grantees)
        $approvedApplicationsCount = Grantee::count();

        return response()->json([
            'pending_applications' => $pendingApplicationsCount,
            'approved_applications' => $approvedApplicationsCount,
            'current_semester' => $currentSemester,
            'current_academic_year' => $currentAcademicYear
        ]);
    }

    /**
     * Toggle application status
     */
    public function toggleApplicationStatus()
    {
        $currentStatus = SystemSetting::get('application_status', 'closed');
        $newStatus = $currentStatus === 'open' ? 'closed' : 'open';

        SystemSetting::set('application_status', $newStatus);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Application status updated to ' . $newStatus
        ]);
    }
}
