<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #1e5631;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #1e5631;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }
        
        .header .date {
            color: #999;
            font-size: 12px;
        }
        
        .summary-section {
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #1e5631;
        }
        
        .summary-section h2 {
            color: #1e5631;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        
        .summary-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .stat-item {
            flex: 1;
            min-width: 150px;
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 3px;
            border: 1px solid #e9ecef;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #1e5631;
            display: block;
        }
        
        .stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        
        .data-section {
            margin-bottom: 30px;
        }
        
        .data-section h2 {
            color: #1e5631;
            margin: 0 0 15px 0;
            font-size: 18px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .data-table th {
            background-color: #1e5631;
            color: white;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #164023;
        }
        
        .data-table td {
            padding: 6px;
            border: 1px solid #e9ecef;
            vertical-align: top;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }
        
        .status-active {
            background-color: #28a745;
        }
        
        .status-pending {
            background-color: #ffc107;
            color: #333;
        }
        
        .status-rejected {
            background-color: #dc3545;
        }
        
        .charts-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .charts-section h2 {
            color: #1e5631;
            margin: 0 0 15px 0;
            font-size: 18px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 5px;
        }
        
        .chart-placeholder {
            width: 100%;
            height: 200px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-style: italic;
            margin-bottom: 15px;
        }
        
        .chart-data {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }
        
        .chart-item {
            flex: 1;
            min-width: 120px;
            text-align: center;
            padding: 10px;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 3px;
        }
        
        .chart-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #1e5631;
        }
        
        .chart-item .label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 11px;
            color: #999;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .header {
                margin-bottom: 20px;
            }
            
            .summary-stats {
                display: block;
            }
            
            .stat-item {
                display: inline-block;
                width: 23%;
                margin: 1%;
            }
            
            .chart-data {
                display: block;
            }
            
            .chart-item {
                display: inline-block;
                width: 18%;
                margin: 1%;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="subtitle">{{ $subtitle }}</div>
        <div class="date">Generated on {{ $date }}</div>
    </div>

    <!-- Summary Section -->
    @if(isset($summary) && !empty($summary))
    <div class="summary-section">
        <h2>Report Summary</h2>
        <div class="summary-stats">
            @foreach($summary as $key => $value)
            <div class="stat-item">
                <span class="stat-value">{{ $value }}</span>
                <span class="stat-label">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Charts Section -->
    @if($includeCharts && isset($chartData) && !empty($chartData))
    <div class="charts-section">
        <h2>Data Visualization</h2>
        
        @if(isset($chartData['by_scholarship_type']) && !empty($chartData['by_scholarship_type']))
        <div class="chart-placeholder">
            Scholarship Types Distribution Chart
        </div>
        <div class="chart-data">
            @foreach($chartData['by_scholarship_type'] as $type => $count)
            <div class="chart-item">
                <div class="value">{{ $count }}</div>
                <div class="label">{{ ucfirst($type) }}</div>
            </div>
            @endforeach
        </div>
        @endif

        @if(isset($chartData['by_status']) && !empty($chartData['by_status']))
        <div class="chart-placeholder">
            Status Distribution Chart
        </div>
        <div class="chart-data">
            @foreach($chartData['by_status'] as $status => $count)
            <div class="chart-item">
                <div class="value">{{ $count }}</div>
                <div class="label">{{ ucfirst($status) }}</div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    <!-- Data Section -->
    @if(isset($data) && !empty($data))
    <div class="data-section">
        <h2>Detailed Data (Showing {{ count($data) }} records)</h2>
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
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by the Scholarship Management System</p>
        <p>© {{ date('Y') }} St. Paul University Philippines - Scholarship Office</p>
    </div>
</body>
</html>
