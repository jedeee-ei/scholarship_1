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
        // Get statistics for dashboard using real data
        $totalGrantees = Grantee::count();
        $totalApplications = ScholarshipApplication::count();

        $stats = [
            'total' => $totalGrantees + $totalApplications, // Total includes both grantees and pending applications
            'pending' => ScholarshipApplication::where('status', 'Pending Review')->count(),
            'approved' => Grantee::where('status', 'Active')->count(), // Use grantees for approved
            'rejected' => ScholarshipApplication::where('status', 'Rejected')->count(),
        ];

        // Calculate percentage changes (comparing with previous month)
        $currentMonth = now()->month;
        $previousMonth = now()->subMonth()->month;

        $currentMonthStats = [
            'total' => Grantee::whereMonth('approved_date', $currentMonth)->count() +
                ScholarshipApplication::whereMonth('created_at', $currentMonth)->count(),
            'pending' => ScholarshipApplication::where('status', 'Pending Review')->whereMonth('created_at', $currentMonth)->count(),
            'approved' => Grantee::where('status', 'Active')->whereMonth('approved_date', $currentMonth)->count(),
            'rejected' => ScholarshipApplication::where('status', 'Rejected')->whereMonth('created_at', $currentMonth)->count(),
        ];

        $previousMonthStats = [
            'total' => Grantee::whereMonth('approved_date', $previousMonth)->count() +
                ScholarshipApplication::whereMonth('created_at', $previousMonth)->count(),
            'pending' => ScholarshipApplication::where('status', 'Pending Review')->whereMonth('created_at', $previousMonth)->count(),
            'approved' => Grantee::where('status', 'Active')->whereMonth('approved_date', $previousMonth)->count(),
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

        return view('admin.dashboard', [
            'stats' => $stats,
            'changes' => $changes,
            'recentApplications' => $recentApplications,
            'allApplications' => $allApplications,
            'chartData' => $chartData,
            'currentStatus' => '',
            'currentType' => '',
            'applicationStatus' => $applicationStatus
        ]);
    }

    /**
     * Get chart data for dashboard (simplified to 2 charts)
     */
    private function getChartData()
    {
        // 1. Pie Chart: Grantees by Benefactor Type
        $benefactorDistribution = [];

        // Get government grantees by benefactor type
        $governmentGrantees = Grantee::where('scholarship_type', 'government')
            ->select('government_benefactor_type', DB::raw('count(*) as count'))
            ->whereNotNull('government_benefactor_type')
            ->groupBy('government_benefactor_type')
            ->pluck('count', 'government_benefactor_type')
            ->toArray();

        // Get employee grantees
        $employeeCount = Grantee::where('scholarship_type', 'employee')->count();
        if ($employeeCount > 0) {
            $benefactorDistribution['Employee'] = $employeeCount;
        }

        // Get private grantees
        $privateCount = Grantee::where('scholarship_type', 'private')->count();
        if ($privateCount > 0) {
            $benefactorDistribution['Private'] = $privateCount;
        }

        // Add government benefactor types
        foreach ($governmentGrantees as $type => $count) {
            $benefactorDistribution[$type] = $count;
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
            'benefactorDistribution' => $benefactorDistribution,
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
