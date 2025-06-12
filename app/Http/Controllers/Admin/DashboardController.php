<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use App\Models\ArchivedStudent;
use App\Models\SystemSetting;
use App\Models\Scholarship;
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
     * Get chart data for dashboard (3 pie charts + 1 line chart)
     */
    private function getChartData()
    {
        // 1. Pie Chart: Number of Students per Scholarship Type
        $studentsPerScholarshipType = [];

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
            $studentsPerScholarshipType[$displayName] = $count;
        }

        // 2. Pie Chart: Number of Scholarship Types in System
        $scholarshipTypesCount = [];

        // Count all scholarship programs from scholarships table (regardless of grantees)
        $scholarshipPrograms = Scholarship::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();

        foreach ($scholarshipPrograms as $program) {
            $displayName = $typeMapping[$program->type] ?? ucfirst($program->type);
            $scholarshipTypesCount[$displayName] = $program->count;
        }

        // If no scholarship programs exist in the scholarships table,
        // fall back to showing available scholarship types from the system
        if (empty($scholarshipTypesCount)) {
            // Check what types exist in grantees table as a fallback
            $granteeTypes = Grantee::select('scholarship_type')
                ->whereNotNull('scholarship_type')
                ->distinct()
                ->get();

            foreach ($granteeTypes as $typeRecord) {
                $displayName = $typeMapping[$typeRecord->scholarship_type] ?? ucfirst($typeRecord->scholarship_type);
                $scholarshipTypesCount[$displayName] = 1; // Show as 1 type available
            }

            // If still no data, show the predefined types
            if (empty($scholarshipTypesCount)) {
                $predefinedTypes = ['government', 'academic', 'employees', 'alumni'];
                foreach ($predefinedTypes as $type) {
                    $displayName = $typeMapping[$type] ?? ucfirst($type);
                    $scholarshipTypesCount[$displayName] = 1; // Show as 1 type available
                }
            }
        }

        // 3. Graduates Pie Chart: Graduates per academic year
        $graduatesData = [];

        // Get graduates from archived_students where remarks contains "graduated"
        $archivedGraduates = ArchivedStudent::where('remarks', 'like', '%graduated%')
            ->selectRaw('archived_academic_year, COUNT(*) as count')
            ->groupBy('archived_academic_year')
            ->orderBy('archived_academic_year', 'desc')
            ->limit(5)
            ->get();

        foreach ($archivedGraduates as $graduate) {
            $graduatesData[$graduate->archived_academic_year] = $graduate->count;
        }

        // Also get graduates from grantees table where status = 'Graduated'
        $granteeGraduates = Grantee::where('status', 'Graduated')
            ->selectRaw('academic_year, COUNT(*) as count')
            ->groupBy('academic_year')
            ->orderBy('academic_year', 'desc')
            ->limit(5)
            ->get();

        foreach ($granteeGraduates as $graduate) {
            $year = $graduate->academic_year;
            if (isset($graduatesData[$year])) {
                $graduatesData[$year] += $graduate->count;
            } else {
                $graduatesData[$year] = $graduate->count;
            }
        }

        // Sort by academic year descending and limit to 5
        arsort($graduatesData);
        $graduatesData = array_slice($graduatesData, 0, 5, true);

        // 4. Line Chart: Cumulative scholarship growth through the years (last 7 years)
        $years = [];
        $scholarshipCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $year = now()->subYears($i)->year;
            $years[] = $year;

            // Count total active grantees up to this year (cumulative)
            $granteesUpToYear = Grantee::whereYear('created_at', '<=', $year)->count();

            // Count total archived students up to this year (cumulative)
            $archivedUpToYear = ArchivedStudent::whereYear('archived_at', '<=', $year)->count();

            // Total cumulative scholarships (active + graduated)
            $scholarshipCounts[] = $granteesUpToYear + $archivedUpToYear;
        }

        return [
            'studentsPerScholarshipType' => $studentsPerScholarshipType,
            'scholarshipTypesCount' => $scholarshipTypesCount,
            'graduatesData' => $graduatesData,
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
