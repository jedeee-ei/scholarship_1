<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #2c3e50;
            line-height: 1.6;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
        }

        /* Header Section - Invoice Style */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            position: relative;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .report-title {
            font-size: 32px;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .report-number {
            text-align: right;
            font-size: 12px;
            opacity: 0.9;
            background: rgba(255,255,255,0.15);
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .header-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .detail-group h3 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            opacity: 0.8;
            font-weight: 500;
        }

        .detail-group p {
            font-size: 16px;
            font-weight: 400;
            background: rgba(255,255,255,0.15);
            padding: 10px 15px;
            border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* Summary Section - Clean Cards */
        .summary-section {
            padding: 30px;
            background: #f8f9fa;
        }

        .section-title {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
        }

        .summary-card {
            background: white;
            padding: 25px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .summary-card .number {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
            display: block;
            margin-bottom: 10px;
        }

        .summary-card .label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        /* Charts Section - Professional Layout */
        .charts-section {
            padding: 30px;
            background: white;
        }

        .chart-container {
            margin-bottom: 30px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .chart-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 25px;
            border-bottom: none;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-data {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            background: white;
        }

        .chart-item {
            padding: 25px 20px;
            text-align: center;
            border-right: 1px solid #ecf0f1;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transition: background 0.2s ease;
        }

        .chart-item:last-child {
            border-right: none;
        }

        .chart-item .value {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            display: block;
            margin-bottom: 10px;
        }

        .chart-item .label {
            font-size: 11px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        /* Data Table Section - Modern Design */
        .data-section {
            padding: 30px;
            background: white;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .data-table th {
            background: #34495e;
            color: white;
            padding: 15px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #2c3e50;
        }

        .data-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #ecf0f1;
            color: #2c3e50;
            vertical-align: middle;
        }

        .data-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .data-table tr:hover {
            background: #e3f2fd;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .status-pending-review {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-active, .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            text-align: center;
            padding: 25px;
            font-size: 11px;
        }

        .footer p {
            margin: 5px 0;
            opacity: 0.9;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                max-width: none;
            }

            .summary-card, .chart-container, .table-container {
                box-shadow: 0 1px 3px rgba(0,0,0,0.2);
                break-inside: avoid;
            }

            .data-table {
                font-size: 10px;
            }

            .data-table th, .data-table td {
                padding: 8px 6px;
            }
        }

        .print-instructions {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin: 20px;
            color: #1976d2;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-top">
                <div class="report-title">{{ $title ?? 'Report' }}</div>
                <div class="report-number">
                    <div><strong>REPORT #</strong></div>
                    <div>{{ date('Y') }}-{{ str_pad(date('m'), 2, '0', STR_PAD_LEFT) }}-{{ str_pad(date('d'), 2, '0', STR_PAD_LEFT) }}</div>
                    <div style="margin-top: 8px; font-size: 10px;">{{ date('H:i:s') }}</div>
                </div>
            </div>

            <div class="header-details">
                <div class="detail-group">
                    <h3>Generated By</h3>
                    <p>{{ $subtitle ?? 'Scholarship Management System' }}</p>
                </div>
                <div class="detail-group">
                    <h3>Date Generated</h3>
                    <p>{{ $date ?? now()->format('F j, Y \a\t g:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Print Instructions (only show for HTML fallback) -->
        @if(request()->header('Content-Type') === 'text/html')
        <div class="print-instructions">
            <strong>📄 How to Save as PDF:</strong><br>
            Press <strong>Ctrl+P</strong> (Windows) or <strong>Cmd+P</strong> (Mac), then choose <strong>"Save as PDF"</strong> as the destination.
        </div>
        @endif

        <!-- Summary Section -->
        @if(isset($summary) && !empty($summary))
        <div class="summary-section">
            <h2 class="section-title">Report Summary</h2>
            <div class="summary-grid">
                @foreach($summary as $key => $value)
                <div class="summary-card">
                    <span class="number">{{ $value }}</span>
                    <span class="label">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Charts Section -->
        @if($includeCharts && isset($chartData) && !empty($chartData))
        <div class="charts-section">
            <h2 class="section-title">Data Visualization</h2>

            @if(isset($chartData['by_scholarship_type']) && !empty($chartData['by_scholarship_type']))
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Scholarship Types Distribution</h3>
                </div>
                <div class="chart-data">
                    @foreach($chartData['by_scholarship_type'] as $type => $count)
                    <div class="chart-item">
                        <div class="value">{{ $count }}</div>
                        <div class="label">{{ ucfirst($type) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($chartData['by_status']) && !empty($chartData['by_status']))
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">Status Distribution</h3>
                </div>
                <div class="chart-data">
                    @foreach($chartData['by_status'] as $status => $count)
                    <div class="chart-item">
                        <div class="value">{{ $count }}</div>
                        <div class="label">{{ ucfirst(str_replace('_', ' ', $status)) }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Data Section -->
        @if(isset($data) && !empty($data))
        <div class="data-section">
            <div class="table-container">
                <div class="table-header">
                    Detailed Data ({{ count($data) }} records)
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Scholarship Type</th>
                            <th>Status</th>
                            <th>Department</th>
                            <th>Course</th>
                            <th>GWA</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $record)
                        <tr>
                            <td>{{ $record['id'] ?? 'N/A' }}</td>
                            <td>{{ $record['student_id'] ?? 'N/A' }}</td>
                            <td>{{ $record['full_name'] ?? 'N/A' }}</td>
                            <td>{{ ucfirst($record['scholarship_type'] ?? 'N/A') }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $record['status'] ?? 'unknown')) }}">
                                    {{ $record['status'] ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $record['department'] ?? 'N/A' }}</td>
                            <td>{{ $record['course'] ?? 'N/A' }}</td>
                            <td>{{ $record['gwa'] ?? 'N/A' }}</td>
                            <td>{{ $record['date'] ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p><strong>This report was generated automatically by the Scholarship Management System</strong></p>
            <p>© {{ date('Y') }} St. Paul University Philippines - Scholarship Office</p>
            <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>
</html>
