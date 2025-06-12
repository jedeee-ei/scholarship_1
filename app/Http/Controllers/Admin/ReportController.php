<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    /**
     * Show reports page
     */
    public function index()
    {
        try {
            // Get report statistics from grantees table
            $totalGrantees = Grantee::count();
            $totalApplications = ScholarshipApplication::count();

            $reportStats = [
                'total_applications' => $totalGrantees, // Show grantees as "applications" since they represent processed applications
                'applications_this_month' => Grantee::whereMonth('approved_date', now()->month)
                    ->whereYear('approved_date', now()->year)->count(),
                'applications_this_year' => Grantee::whereYear('approved_date', now()->year)->count(),
                'by_status' => [
                    'pending' => ScholarshipApplication::where('status', 'Pending Review')->count(),
                    'approved' => Grantee::where('status', 'Active')->count(), // Active grantees are "approved"
                    'rejected' => ScholarshipApplication::where('status', 'Rejected')->count(),
                ],
                'by_type' => [
                    'government' => Grantee::where('scholarship_type', 'government')->count(),
                    'academic' => Grantee::where('scholarship_type', 'academic')->count(),
                    'employees' => Grantee::where('scholarship_type', 'employees')->count(),
                    'private' => Grantee::where('scholarship_type', 'private')->count(),
                ]
            ];

            // Debug: Log the statistics
            Log::debug('Report statistics (using real grantee data):', $reportStats);
            Log::debug('Total grantees in database: ' . $totalGrantees);
            Log::debug('Total applications in database: ' . $totalApplications);

            return view('admin.reports', compact('reportStats'));
        } catch (\Exception $e) {
            Log::error('Error in reports method: ' . $e->getMessage());

            // Return default values if there's an error
            $reportStats = [
                'total_applications' => 0,
                'applications_this_month' => 0,
                'applications_this_year' => 0,
                'by_status' => [
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0,
                ],
                'by_type' => [
                    'government' => 0,
                    'academic' => 0,
                    'employees' => 0,
                    'private' => 0,
                ]
            ];

            return view('admin.reports', compact('reportStats'));
        }
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

        try {
            // Get report data
            $reportData = $this->getReportData($request);

            // Debug logging
            Log::info('Report generation request:', [
                'report_type' => $request->report_type,
                'date_range' => $request->date_range,
                'format' => $request->format,
                'data_count' => $reportData->count()
            ]);

            // Check if we have data
            if ($reportData->isEmpty()) {
                // If no data for specific filters, try getting all data
                $allData = ScholarshipApplication::all();
                Log::info('No filtered data found. Total applications in database: ' . $allData->count());

                return response()->json([
                    'success' => false,
                    'message' => 'No data found for the selected criteria. Total applications in database: ' . $allData->count() . '. Please try different filters or select "This Year" for date range.'
                ], 404);
            }

            // Generate report based on format
            switch ($request->format) {
                case 'csv':
                    return $this->generateCSVReport($reportData, $request->report_type);
                case 'excel':
                    return $this->generateExcelReport($reportData, $request->report_type);
                case 'pdf':
                    $includeCharts = $request->get('include_charts', 'yes') === 'yes';
                    return $this->generatePDFReport($reportData, $request->report_type, $includeCharts);
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unsupported format'
                    ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Report generation error: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview report
     */
    public function previewReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'date_range' => 'required|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        try {
            $reportData = $this->getReportPreviewData($request->report_type, $request->date_range);

            return response()->json([
                'success' => true,
                'data' => $reportData,
                'total_records' => count($reportData)
            ]);
        } catch (\Exception $e) {
            Log::error('Report preview error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating report preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get report data based on request parameters
     */
    private function getReportData($request)
    {
        $reportType = $request->report_type;
        $dateRange = $request->date_range;

        // Start with base query
        if ($reportType === 'applications') {
            $query = ScholarshipApplication::query();
        } else {
            // For other report types, use grantees data
            $query = Grantee::query();
        }

        // Apply date filters
        switch ($dateRange) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                break;
            case 'this_year':
                $query->whereYear('created_at', now()->year);
                break;
            case 'custom':
                if ($request->start_date && $request->end_date) {
                    $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                }
                break;
        }

        // Apply report type specific filters
        switch ($reportType) {
            case 'scholarship_summary':
                // Group by scholarship type
                break;
            case 'department_analysis':
                $query->whereNotNull('department');
                break;
            case 'gwa_distribution':
                $query->whereNotNull('gwa');
                break;
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Generate CSV report
     */
    private function generateCSVReport($data, $reportType)
    {
        $filename = strtolower(str_replace(' ', '_', $reportType)) . '_report_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, [
                'Application ID',
                'Student ID',
                'Full Name',
                'Scholarship Type',
                'Status',
                'Department',
                'Course',
                'Year Level',
                'GWA',
                'Email',
                'Contact Number',
                'Application Date'
            ]);

            // Add data rows
            foreach ($data as $record) {
                // Handle both grantee and application data structures
                if (isset($record->grantee_id)) {
                    // This is grantee data
                    fputcsv($handle, [
                        $record->grantee_id,
                        $record->student_id,
                        trim($record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name),
                        ucfirst($record->scholarship_type),
                        $record->status,
                        $record->department ?: 'N/A',
                        $record->course ?: 'N/A',
                        $record->year_level ?: 'N/A',
                        $record->gwa ?: $record->current_gwa ?: 'N/A',
                        $record->email ?: 'N/A',
                        $record->contact_number ?: 'N/A',
                        $record->approved_date ? \Carbon\Carbon::parse($record->approved_date)->format('Y-m-d H:i:s') : $record->created_at->format('Y-m-d H:i:s')
                    ]);
                } else {
                    // This is application data
                    fputcsv($handle, [
                        $record->application_id,
                        $record->student_id,
                        trim($record->first_name . ' ' . ($record->middle_name ?: '') . ' ' . $record->last_name),
                        ucfirst($record->scholarship_type),
                        $record->status,
                        $record->department ?: 'N/A',
                        $record->course ?: 'N/A',
                        $record->year_level ?: 'N/A',
                        $record->gwa ?: 'N/A',
                        $record->email ?: 'N/A',
                        $record->contact_number ?: 'N/A',
                        $record->created_at->format('Y-m-d H:i:s')
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate Excel report
     */
    private function generateExcelReport($data, $reportType)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'Application ID',
            'Student ID',
            'Full Name',
            'Scholarship Type',
            'Status',
            'Department',
            'Course',
            'Year Level',
            'GWA',
            'Email',
            'Contact Number',
            'Application Date'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Add data
        $row = 2;
        foreach ($data as $record) {
            $rowData = [];

            // Handle both grantee and application data structures
            if (isset($record->grantee_id)) {
                // This is grantee data
                $rowData = [
                    $record->grantee_id,
                    $record->student_id,
                    trim($record->first_name . ' ' . $record->middle_name . ' ' . $record->last_name),
                    ucfirst($record->scholarship_type),
                    $record->status,
                    $record->department ?: 'N/A',
                    $record->course ?: 'N/A',
                    $record->year_level ?: 'N/A',
                    $record->gwa ?: $record->current_gwa ?: 'N/A',
                    $record->email ?: 'N/A',
                    $record->contact_number ?: 'N/A',
                    $record->approved_date ? \Carbon\Carbon::parse($record->approved_date)->format('Y-m-d H:i:s') : $record->created_at->format('Y-m-d H:i:s')
                ];
            } else {
                // This is application data
                $rowData = [
                    $record->application_id,
                    $record->student_id,
                    trim($record->first_name . ' ' . ($record->middle_name ?: '') . ' ' . $record->last_name),
                    ucfirst($record->scholarship_type),
                    $record->status,
                    $record->department ?: 'N/A',
                    $record->course ?: 'N/A',
                    $record->year_level ?: 'N/A',
                    $record->gwa ?: 'N/A',
                    $record->email ?: 'N/A',
                    $record->contact_number ?: 'N/A',
                    $record->created_at->format('Y-m-d H:i:s')
                ];
            }

            $sheet->fromArray($rowData, null, 'A' . $row);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = strtolower(str_replace(' ', '_', $reportType)) . '_report_' . date('Y-m-d_H-i-s') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate PDF report
     */
    private function generatePDFReport($data, $reportType, $includeCharts = true)
    {
        // For now, return CSV format as PDF generation requires additional setup
        return $this->generateCSVReport($data, $reportType);
    }

    /**
     * Get report preview data
     */
    private function getReportPreviewData($reportType, $dateRange)
    {
        // This is a simplified version for preview
        $query = Grantee::query();

        // Apply date filters
        switch ($dateRange) {
            case 'this_month':
                $query->whereMonth('created_at', now()->month);
                break;
            case 'this_year':
                $query->whereYear('created_at', now()->year);
                break;
        }

        return $query->take(10)->get()->map(function ($grantee) {
            return [
                'id' => $grantee->student_id,
                'student_id' => $grantee->student_id,
                'name' => $grantee->first_name . ' ' . $grantee->last_name,
                'scholarship_type' => ucfirst($grantee->scholarship_type),
                'status' => $grantee->status ?: 'Active',
                'course' => $grantee->course,
                'gwa' => $grantee->gwa ?: $grantee->current_gwa,
                'created_at' => $grantee->created_at->format('Y-m-d')
            ];
        })->toArray();
    }
}
