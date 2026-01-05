<?php

namespace App\Services;

use App\Models\Grantee;
use App\Models\ScholarshipApplication;
use App\Models\ArchivedStudent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SimpleReportService
{
    /**
     * Generate Executive Summary (Simple PDF)
     */
    public function generateExecutiveSummary()
    {
        try {
            $data = $this->getExecutiveSummaryData();

            $pdf = Pdf::loadView('admin.reports.simple-executive', compact('data'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'defaultFont' => 'Arial'
                ]);

            return $pdf->download('executive_summary_' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            // Return a simple error response
            return response()->json([
                'error' => 'PDF Generation Failed',
                'message' => $e->getMessage(),
                'suggestion' => 'Please try the Excel report instead or contact administrator.'
            ], 500);
        }
    }

    /**
     * Generate Department Performance Report
     */
    public function generateDepartmentReport()
    {
        try {
            $data = $this->getDepartmentPerformanceData();

            $pdf = Pdf::loadView('admin.reports.department-performance', compact('data'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'defaultFont' => 'Arial'
                ]);

            return $pdf->download('department_performance_' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            // Return a simple error response
            return response()->json([
                'error' => 'Department PDF Generation Failed',
                'message' => $e->getMessage(),
                'suggestion' => 'Please try the Excel report instead or contact administrator.'
            ], 500);
        }
    }

    /**
     * Generate Scholarship Effectiveness Report
     */
    public function generateEffectivenessReport()
    {
        $data = $this->getScholarshipEffectivenessData();
        
        $pdf = Pdf::loadView('admin.reports.scholarship-effectiveness', compact('data'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('scholarship_effectiveness_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Generate Enhanced Excel Report with Multiple Sheets
     */
    public function generateEnhancedExcelReport()
    {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Summary
        $this->createSummarySheet($spreadsheet);
        
        // Sheet 2: All Students
        $this->createStudentsSheet($spreadsheet);
        
        // Sheet 3: Department Analysis
        $this->createDepartmentSheet($spreadsheet);
        
        // Sheet 4: Applications Data
        $this->createApplicationsSheet($spreadsheet);

        // Sheet 5: Trends
        $this->createTrendsSheet($spreadsheet);

        $filename = "comprehensive_report_" . date('Y-m-d') . '.xlsx';
        
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Get Executive Summary Data (Connected to Real Database)
     */
    private function getExecutiveSummaryData()
    {
        $currentYear = date('Y');
        $lastYear = $currentYear - 1;

        // Basic counts from actual database
        $totalApplications = ScholarshipApplication::count();
        $totalGrantees = Grantee::count();
        $activeGrantees = Grantee::where('status', 'Active')->count();
        $graduatedStudents = ArchivedStudent::where('remarks', 'like', '%graduated%')->count();

        // Additional status counts
        $pendingApplications = ScholarshipApplication::where('status', 'Pending Review')->count();
        $rejectedApplications = ScholarshipApplication::where('status', 'Rejected')->count();
        $underReviewApplications = ScholarshipApplication::where('status', 'Under Review')->count();

        // Performance metrics
        $approvalRate = $totalApplications > 0 ? round(($totalGrantees / $totalApplications) * 100, 1) : 0;
        $retentionRate = ($totalGrantees + $graduatedStudents) > 0 ?
            round(($graduatedStudents / ($totalGrantees + $graduatedStudents)) * 100, 1) : 0;

        // Year-over-year comparison
        $thisYearApplications = ScholarshipApplication::whereYear('created_at', $currentYear)->count();
        $lastYearApplications = ScholarshipApplication::whereYear('created_at', $lastYear)->count();
        $yearOverYearGrowth = $lastYearApplications > 0 ?
            round((($thisYearApplications - $lastYearApplications) / $lastYearApplications) * 100, 1) : 0;

        // Scholarship type distribution from grantees (actual recipients)
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

        $mappedScholarshipTypes = [];
        foreach ($scholarshipTypes as $type => $count) {
            $displayName = $typeMapping[$type] ?? ucfirst($type);
            $mappedScholarshipTypes[$displayName] = $count;
        }

        // Department distribution from grantees
        $departments = Grantee::select('department', DB::raw('count(*) as count'))
            ->whereNotNull('department')
            ->groupBy('department')
            ->pluck('count', 'department')
            ->toArray();

        // Application status distribution
        $applicationsByStatus = [
            'Pending Review' => $pendingApplications,
            'Under Review' => $underReviewApplications,
            'Approved' => $totalGrantees,
            'Rejected' => $rejectedApplications
        ];

        return [
            'summary' => [
                'total_applications' => $totalApplications,
                'total_grantees' => $totalGrantees,
                'active_grantees' => $activeGrantees,
                'graduated_students' => $graduatedStudents,
                'pending_applications' => $pendingApplications,
                'approval_rate' => $approvalRate,
                'retention_rate' => $retentionRate,
                'year_over_year_growth' => $yearOverYearGrowth
            ],
            'scholarship_types' => $mappedScholarshipTypes,
            'departments' => $departments,
            'applications_by_status' => $applicationsByStatus,
            'generated_date' => date('F d, Y'),
            'academic_year' => $currentYear,
            'data_source' => 'Live Database - ' . now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get Department Performance Data (Connected to Real Database)
     */
    private function getDepartmentPerformanceData()
    {
        $departments = ['SITE', 'SBAHM', 'SNASH', 'SASTE'];
        $performanceData = [];

        foreach ($departments as $dept) {
            // Get actual data from database
            $totalStudents = Grantee::where('department', $dept)->count();
            $activeStudents = Grantee::where('department', $dept)->where('status', 'Active')->count();
            $inactiveStudents = Grantee::where('department', $dept)->where('status', 'Inactive')->count();
            $graduatedStudents = ArchivedStudent::where('department', $dept)
                ->where('remarks', 'like', '%graduated%')->count();

            // Get applications for this department
            $totalApplications = ScholarshipApplication::where('department', $dept)->count();
            $pendingApplications = ScholarshipApplication::where('department', $dept)
                ->where('status', 'Pending Review')->count();

            // Calculate metrics
            $retentionRate = ($totalStudents + $graduatedStudents) > 0 ?
                round(($graduatedStudents / ($totalStudents + $graduatedStudents)) * 100, 1) : 0;

            $approvalRate = $totalApplications > 0 ?
                round(($totalStudents / $totalApplications) * 100, 1) : 0;

            $activeRate = $totalStudents > 0 ?
                round(($activeStudents / $totalStudents) * 100, 1) : 0;

            $performanceData[$dept] = [
                'total_students' => $totalStudents,
                'active_students' => $activeStudents,
                'inactive_students' => $inactiveStudents,
                'graduated_students' => $graduatedStudents,
                'total_applications' => $totalApplications,
                'pending_applications' => $pendingApplications,
                'retention_rate' => $retentionRate,
                'approval_rate' => $approvalRate,
                'active_rate' => $activeRate
            ];
        }

        // Calculate overall statistics
        $overallStats = [
            'total_students' => array_sum(array_column($performanceData, 'total_students')),
            'total_active' => array_sum(array_column($performanceData, 'active_students')),
            'total_graduated' => array_sum(array_column($performanceData, 'graduated_students')),
            'total_applications' => array_sum(array_column($performanceData, 'total_applications')),
            'avg_retention_rate' => count($performanceData) > 0 ?
                round(array_sum(array_column($performanceData, 'retention_rate')) / count($performanceData), 1) : 0
        ];

        return [
            'departments' => $performanceData,
            'overall_stats' => $overallStats,
            'generated_date' => date('F d, Y'),
            'data_source' => 'Live Database - ' . now()->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Get Scholarship Effectiveness Data
     */
    private function getScholarshipEffectivenessData()
    {
        $scholarshipTypes = ['government', 'academic', 'employees', 'alumni'];
        $effectivenessData = [];

        foreach ($scholarshipTypes as $type) {
            $totalStudents = Grantee::where('scholarship_type', $type)->count();
            $graduatedStudents = ArchivedStudent::where('scholarship_type', $type)
                ->where('remarks', 'like', '%graduated%')->count();

            $successRate = ($totalStudents + $graduatedStudents) > 0 ? 
                round(($graduatedStudents / ($totalStudents + $graduatedStudents)) * 100, 1) : 0;

            $effectivenessData[$type] = [
                'total_students' => $totalStudents,
                'graduated_students' => $graduatedStudents,
                'success_rate' => $successRate
            ];
        }

        return [
            'scholarship_types' => $effectivenessData,
            'generated_date' => date('F d, Y')
        ];
    }

    /**
     * Create Summary Sheet for Excel
     */
    private function createSummarySheet($spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Executive Summary');
        
        $data = $this->getExecutiveSummaryData();
        
        // Headers
        $sheet->setCellValue('A1', 'SCHOLARSHIP MANAGEMENT SYSTEM - EXECUTIVE SUMMARY');
        $sheet->setCellValue('A2', 'Generated: ' . $data['generated_date']);
        
        // Summary data
        $row = 4;
        $sheet->setCellValue('A' . $row, 'OVERVIEW');
        $row += 2;
        
        foreach ($data['summary'] as $key => $value) {
            $label = ucwords(str_replace('_', ' ', $key));
            $sheet->setCellValue('A' . $row, $label);
            $sheet->setCellValue('B' . $row, $value . (strpos($key, 'rate') !== false || strpos($key, 'growth') !== false ? '%' : ''));
            $row++;
        }
        
        // Scholarship types
        $row += 2;
        $sheet->setCellValue('A' . $row, 'SCHOLARSHIP TYPES');
        $row += 2;
        
        foreach ($data['scholarship_types'] as $type => $count) {
            $sheet->setCellValue('A' . $row, ucfirst($type));
            $sheet->setCellValue('B' . $row, $count);
            $row++;
        }
        
        // Styling
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
    }

    /**
     * Create Students Sheet for Excel (Connected to Real Database)
     */
    private function createStudentsSheet($spreadsheet)
    {
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->setActiveSheetIndex(1);
        $sheet->setTitle('All Grantees');

        // Headers
        $headers = [
            'Student ID', 'Last Name', 'First Name', 'Middle Name', 'Scholarship Type',
            'Department', 'Course', 'Year Level', 'Status', 'GWA', 'Email',
            'Contact Number', 'Approved Date', 'Academic Year', 'Semester'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Get actual data from database
        $grantees = Grantee::select([
            'student_id', 'last_name', 'first_name', 'middle_name', 'scholarship_type',
            'department', 'course', 'year_level', 'status', 'gwa', 'email',
            'contact_number', 'approved_date', 'academic_year', 'semester'
        ])->orderBy('approved_date', 'desc')->get();

        $row = 2;
        foreach ($grantees as $grantee) {
            $sheet->setCellValue('A' . $row, $grantee->student_id);
            $sheet->setCellValue('B' . $row, $grantee->last_name);
            $sheet->setCellValue('C' . $row, $grantee->first_name);
            $sheet->setCellValue('D' . $row, $grantee->middle_name);
            $sheet->setCellValue('E' . $row, ucfirst($grantee->scholarship_type));
            $sheet->setCellValue('F' . $row, $grantee->department);
            $sheet->setCellValue('G' . $row, $grantee->course);
            $sheet->setCellValue('H' . $row, $grantee->year_level);
            $sheet->setCellValue('I' . $row, $grantee->status);
            $sheet->setCellValue('J' . $row, $grantee->gwa);
            $sheet->setCellValue('K' . $row, $grantee->email);
            $sheet->setCellValue('L' . $row, $grantee->contact_number);
            $sheet->setCellValue('M' . $row, $grantee->approved_date ? $grantee->approved_date->format('Y-m-d') : '');
            $sheet->setCellValue('N' . $row, $grantee->academic_year);
            $sheet->setCellValue('O' . $row, $grantee->semester);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add summary at the bottom
        $row += 2;
        $sheet->setCellValue('A' . $row, 'SUMMARY');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Grantees: ' . $grantees->count());
        $row++;
        $sheet->setCellValue('A' . $row, 'Active: ' . $grantees->where('status', 'Active')->count());
        $row++;
        $sheet->setCellValue('A' . $row, 'Inactive: ' . $grantees->where('status', 'Inactive')->count());
    }

    /**
     * Create Department Sheet for Excel
     */
    private function createDepartmentSheet($spreadsheet)
    {
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->setActiveSheetIndex(2);
        $sheet->setTitle('Department Analysis');
        
        $data = $this->getDepartmentPerformanceData();
        
        // Headers
        $sheet->setCellValue('A1', 'DEPARTMENT PERFORMANCE ANALYSIS');
        $sheet->setCellValue('A3', 'Department');
        $sheet->setCellValue('B3', 'Total Students');
        $sheet->setCellValue('C3', 'Active Students');
        $sheet->setCellValue('D3', 'Graduated');
        $sheet->setCellValue('E3', 'Retention Rate');
        
        $row = 4;
        foreach ($data['departments'] as $dept => $stats) {
            $sheet->setCellValue('A' . $row, $dept);
            $sheet->setCellValue('B' . $row, $stats['total_students']);
            $sheet->setCellValue('C' . $row, $stats['active_students']);
            $sheet->setCellValue('D' . $row, $stats['graduated_students']);
            $sheet->setCellValue('E' . $row, $stats['retention_rate'] . '%');
            $row++;
        }
        
        // Styling
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3:E3')->getFont()->setBold(true);
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Create Applications Sheet for Excel (All Applications Data)
     */
    private function createApplicationsSheet($spreadsheet)
    {
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->setActiveSheetIndex(3);
        $sheet->setTitle('All Applications');

        // Headers
        $headers = [
            'Application ID', 'Student ID', 'Last Name', 'First Name', 'Scholarship Type',
            'Department', 'Course', 'Year Level', 'Status', 'GWA', 'Email',
            'Application Date', 'Academic Year', 'Semester'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Get actual applications data from database
        $applications = ScholarshipApplication::select([
            'application_id', 'student_id', 'last_name', 'first_name', 'scholarship_type',
            'department', 'course', 'year_level', 'status', 'gwa', 'email',
            'created_at', 'academic_year', 'semester'
        ])->orderBy('created_at', 'desc')->get();

        $row = 2;
        foreach ($applications as $application) {
            $sheet->setCellValue('A' . $row, $application->application_id);
            $sheet->setCellValue('B' . $row, $application->student_id);
            $sheet->setCellValue('C' . $row, $application->last_name);
            $sheet->setCellValue('D' . $row, $application->first_name);
            $sheet->setCellValue('E' . $row, ucfirst($application->scholarship_type));
            $sheet->setCellValue('F' . $row, $application->department);
            $sheet->setCellValue('G' . $row, $application->course);
            $sheet->setCellValue('H' . $row, $application->year_level);
            $sheet->setCellValue('I' . $row, $application->status);
            $sheet->setCellValue('J' . $row, $application->gwa);
            $sheet->setCellValue('K' . $row, $application->email);
            $sheet->setCellValue('L' . $row, $application->created_at ? $application->created_at->format('Y-m-d') : '');
            $sheet->setCellValue('M' . $row, $application->academic_year);
            $sheet->setCellValue('N' . $row, $application->semester);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add summary at the bottom
        $row += 2;
        $sheet->setCellValue('A' . $row, 'SUMMARY');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Applications: ' . $applications->count());
        $row++;
        $sheet->setCellValue('A' . $row, 'Pending: ' . $applications->where('status', 'Pending Review')->count());
        $row++;
        $sheet->setCellValue('A' . $row, 'Under Review: ' . $applications->where('status', 'Under Review')->count());
        $row++;
        $sheet->setCellValue('A' . $row, 'Approved: ' . $applications->where('status', 'Approved')->count());
        $row++;
        $sheet->setCellValue('A' . $row, 'Rejected: ' . $applications->where('status', 'Rejected')->count());
    }

    /**
     * Create Trends Sheet for Excel (Real Database Trends)
     */
    private function createTrendsSheet($spreadsheet)
    {
        $spreadsheet->createSheet();
        $sheet = $spreadsheet->setActiveSheetIndex(4);
        $sheet->setTitle('Trends Analysis');

        // Monthly trends for the last 12 months using real data
        $sheet->setCellValue('A1', 'MONTHLY TRENDS - LAST 12 MONTHS (LIVE DATA)');
        $sheet->setCellValue('A3', 'Month');
        $sheet->setCellValue('B3', 'Applications Received');
        $sheet->setCellValue('C3', 'Scholarships Approved');
        $sheet->setCellValue('D3', 'Approval Rate (%)');

        $row = 4;
        $totalApplications = 0;
        $totalApprovals = 0;

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M Y');

            // Get actual data from database
            $applications = ScholarshipApplication::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $approvals = Grantee::whereYear('approved_date', $date->year)
                ->whereMonth('approved_date', $date->month)
                ->count();

            $approvalRate = $applications > 0 ? round(($approvals / $applications) * 100, 1) : 0;

            $sheet->setCellValue('A' . $row, $month);
            $sheet->setCellValue('B' . $row, $applications);
            $sheet->setCellValue('C' . $row, $approvals);
            $sheet->setCellValue('D' . $row, $approvalRate . '%');

            $totalApplications += $applications;
            $totalApprovals += $approvals;
            $row++;
        }

        // Add totals and summary
        $row += 2;
        $sheet->setCellValue('A' . $row, 'SUMMARY (12 MONTHS)');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Applications:');
        $sheet->setCellValue('B' . $row, $totalApplications);
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Approvals:');
        $sheet->setCellValue('B' . $row, $totalApprovals);
        $row++;
        $sheet->setCellValue('A' . $row, 'Overall Approval Rate:');
        $overallRate = $totalApplications > 0 ? round(($totalApprovals / $totalApplications) * 100, 1) : 0;
        $sheet->setCellValue('B' . $row, $overallRate . '%');

        // Styling
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3:D3')->getFont()->setBold(true);
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add data source note
        $row += 3;
        $sheet->setCellValue('A' . $row, 'Data Source: Live Database - ' . now()->format('Y-m-d H:i:s'));
        $sheet->getStyle('A' . $row)->getFont()->setItalic(true)->setSize(10);
    }
}
