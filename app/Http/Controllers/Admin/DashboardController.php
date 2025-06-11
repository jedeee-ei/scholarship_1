<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use Illuminate\Http\Request;
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
     * Get chart data for dashboard using real grantee data
     */
    private function getChartData()
    {
        // Grantee approvals over time (last 6 months) - using approved_date
        $months = [];
        $applicationCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            // Count grantees approved in this month + any pending applications created
            $granteeCount = Grantee::whereYear('approved_date', $date->year)
                ->whereMonth('approved_date', $date->month)
                ->count();
            $pendingCount = ScholarshipApplication::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $applicationCounts[] = $granteeCount + $pendingCount;
        }

        // GWA distribution using grantee data (use current_gwa or gwa field)
        $gwaRanges = [
            '1.00-1.25' => Grantee::where(function ($query) {
                $query->whereNotNull('gwa')->where('gwa', '>=', 1.00)->where('gwa', '<=', 1.25)
                    ->orWhere(function ($q) {
                        $q->whereNotNull('current_gwa')->where('current_gwa', '>=', 1.00)->where('current_gwa', '<=', 1.25);
                    });
            })->count(),
            '1.26-1.50' => Grantee::where(function ($query) {
                $query->whereNotNull('gwa')->where('gwa', '>=', 1.26)->where('gwa', '<=', 1.50)
                    ->orWhere(function ($q) {
                        $q->whereNotNull('current_gwa')->where('current_gwa', '>=', 1.26)->where('current_gwa', '<=', 1.50);
                    });
            })->count(),
            '1.51-1.75' => Grantee::where(function ($query) {
                $query->whereNotNull('gwa')->where('gwa', '>=', 1.51)->where('gwa', '<=', 1.75)
                    ->orWhere(function ($q) {
                        $q->whereNotNull('current_gwa')->where('current_gwa', '>=', 1.51)->where('current_gwa', '<=', 1.75);
                    });
            })->count(),
            '1.76-2.00' => Grantee::where(function ($query) {
                $query->whereNotNull('gwa')->where('gwa', '>=', 1.76)->where('gwa', '<=', 2.00)
                    ->orWhere(function ($q) {
                        $q->whereNotNull('current_gwa')->where('current_gwa', '>=', 1.76)->where('current_gwa', '<=', 2.00);
                    });
            })->count(),
            '2.01+' => Grantee::where(function ($query) {
                $query->whereNotNull('gwa')->where('gwa', '>', 2.00)
                    ->orWhere(function ($q) {
                        $q->whereNotNull('current_gwa')->where('current_gwa', '>', 2.00);
                    });
            })->count(),
        ];

        // Status distribution using real data
        $statusDistribution = [
            'pending' => ScholarshipApplication::where('status', 'Pending Review')->count(),
            'under_review' => ScholarshipApplication::where('status', 'Under Committee Review')->count(),
            'approved' => Grantee::where('status', 'Active')->count(), // Use grantees for approved
            'rejected' => ScholarshipApplication::where('status', 'Rejected')->count(),
        ];

        // Department distribution using grantee data
        $departmentDistribution = Grantee::select('department', DB::raw('count(*) as count'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->pluck('count', 'department')
            ->toArray();

        // Scholarship type distribution using grantee data
        $scholarshipTypeDistribution = Grantee::select('scholarship_type', DB::raw('count(*) as count'))
            ->groupBy('scholarship_type')
            ->pluck('count', 'scholarship_type')
            ->toArray();

        return [
            'months' => $months,
            'applicationCounts' => $applicationCounts,
            'gwaRanges' => $gwaRanges,
            'statusDistribution' => $statusDistribution,
            'departmentDistribution' => $departmentDistribution,
            'scholarshipTypeDistribution' => $scholarshipTypeDistribution,
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
}
