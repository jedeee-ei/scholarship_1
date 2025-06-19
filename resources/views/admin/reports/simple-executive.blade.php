<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Summary Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #052f11;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            color: #052f11;
            margin: 0;
            font-size: 18px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            background-color: #052f11;
            color: white;
            padding: 8px 12px;
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-cell {
            display: table-cell;
            padding: 8px 12px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        
        .summary-cell.label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 60%;
        }
        
        .summary-cell.value {
            text-align: right;
            width: 40%;
        }
        
        .highlight {
            background-color: #e8f5e8;
            font-weight: bold;
        }
        
        .distribution-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .distribution-table th,
        .distribution-table td {
            border: 1px solid #ddd;
            padding: 8px 12px;
            text-align: left;
        }
        
        .distribution-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .distribution-table td.number {
            text-align: right;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .two-column {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            padding-right: 2%;
        }
        
        .performance-indicator {
            padding: 5px;
            border-radius: 3px;
            display: inline-block;
            margin-left: 10px;
            font-size: 10px;
        }
        
        .good { background-color: #d4edda; color: #155724; }
        .average { background-color: #fff3cd; color: #856404; }
        .needs-improvement { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SCHOLARSHIP MANAGEMENT SYSTEM</h1>
        <h2>Executive Summary Report</h2>
        <p>Academic Year: {{ $data['academic_year'] }} | Generated: {{ $data['generated_date'] }}</p>
        <p style="font-size: 10px; color: #666;">{{ $data['data_source'] ?? 'Live Database Connection' }}</p>
    </div>

    <!-- Key Performance Summary -->
    <div class="section">
        <h3 class="section-title">KEY PERFORMANCE INDICATORS</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell label">Total Applications Received</div>
                <div class="summary-cell value">{{ number_format($data['summary']['total_applications']) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label">Total Scholarship Recipients</div>
                <div class="summary-cell value">{{ number_format($data['summary']['total_grantees']) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label">Currently Active Scholars</div>
                <div class="summary-cell value">{{ number_format($data['summary']['active_grantees']) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label">Graduated Scholars</div>
                <div class="summary-cell value">{{ number_format($data['summary']['graduated_students']) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label">Pending Applications</div>
                <div class="summary-cell value">{{ number_format($data['summary']['pending_applications']) }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label highlight">Application Approval Rate</div>
                <div class="summary-cell value highlight">
                    {{ $data['summary']['approval_rate'] }}%
                    @if($data['summary']['approval_rate'] >= 70)
                        <span class="performance-indicator good">GOOD</span>
                    @elseif($data['summary']['approval_rate'] >= 50)
                        <span class="performance-indicator average">AVERAGE</span>
                    @else
                        <span class="performance-indicator needs-improvement">NEEDS IMPROVEMENT</span>
                    @endif
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label highlight">Student Retention Rate</div>
                <div class="summary-cell value highlight">
                    {{ $data['summary']['retention_rate'] }}%
                    @if($data['summary']['retention_rate'] >= 80)
                        <span class="performance-indicator good">EXCELLENT</span>
                    @elseif($data['summary']['retention_rate'] >= 60)
                        <span class="performance-indicator average">GOOD</span>
                    @else
                        <span class="performance-indicator needs-improvement">NEEDS ATTENTION</span>
                    @endif
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-cell label">Year-over-Year Growth</div>
                <div class="summary-cell value">
                    @if($data['summary']['year_over_year_growth'] > 0)
                        +{{ $data['summary']['year_over_year_growth'] }}%
                        <span class="performance-indicator good">GROWING</span>
                    @elseif($data['summary']['year_over_year_growth'] < 0)
                        {{ $data['summary']['year_over_year_growth'] }}%
                        <span class="performance-indicator needs-improvement">DECLINING</span>
                    @else
                        {{ $data['summary']['year_over_year_growth'] }}%
                        <span class="performance-indicator average">STABLE</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Analysis -->
    <div class="two-column">
        <div class="column">
            <div class="section">
                <h3 class="section-title">SCHOLARSHIP TYPE DISTRIBUTION</h3>
                <table class="distribution-table">
                    <thead>
                        <tr>
                            <th>Scholarship Type</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalScholarships = array_sum($data['scholarship_types']);
                        @endphp
                        @foreach($data['scholarship_types'] as $type => $count)
                        <tr>
                            <td>{{ ucfirst($type) }}</td>
                            <td class="number">{{ number_format($count) }}</td>
                            <td class="number">
                                @if($totalScholarships > 0)
                                    {{ round(($count / $totalScholarships) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr style="font-weight: bold; background-color: #f8f9fa;">
                            <td>TOTAL</td>
                            <td class="number">{{ number_format($totalScholarships) }}</td>
                            <td class="number">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="column">
            <div class="section">
                <h3 class="section-title">DEPARTMENT DISTRIBUTION</h3>
                <table class="distribution-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Count</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDepartments = array_sum($data['departments']);
                        @endphp
                        @foreach($data['departments'] as $dept => $count)
                        <tr>
                            <td>{{ $dept }}</td>
                            <td class="number">{{ number_format($count) }}</td>
                            <td class="number">
                                @if($totalDepartments > 0)
                                    {{ round(($count / $totalDepartments) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                        @endforeach
                        <tr style="font-weight: bold; background-color: #f8f9fa;">
                            <td>TOTAL</td>
                            <td class="number">{{ number_format($totalDepartments) }}</td>
                            <td class="number">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Key Insights -->
    <div class="section">
        <h3 class="section-title">KEY INSIGHTS & RECOMMENDATIONS</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            @if($data['summary']['approval_rate'] >= 70)
                <li><strong>High Approval Rate:</strong> {{ $data['summary']['approval_rate'] }}% approval rate indicates effective screening process.</li>
            @else
                <li><strong>Low Approval Rate:</strong> {{ $data['summary']['approval_rate'] }}% approval rate may indicate need for better application guidance.</li>
            @endif

            @if($data['summary']['retention_rate'] >= 80)
                <li><strong>Excellent Retention:</strong> {{ $data['summary']['retention_rate'] }}% retention rate shows strong student support.</li>
            @else
                <li><strong>Retention Concern:</strong> {{ $data['summary']['retention_rate'] }}% retention rate suggests need for enhanced student support.</li>
            @endif

            @if($data['summary']['year_over_year_growth'] > 0)
                <li><strong>Program Growth:</strong> {{ $data['summary']['year_over_year_growth'] }}% increase in applications shows growing program awareness.</li>
            @elseif($data['summary']['year_over_year_growth'] < 0)
                <li><strong>Application Decline:</strong> {{ abs($data['summary']['year_over_year_growth']) }}% decrease requires investigation and outreach improvement.</li>
            @endif

            @php
                $mostPopularType = array_keys($data['scholarship_types'], max($data['scholarship_types']))[0] ?? 'N/A';
                $mostPopularDept = array_keys($data['departments'], max($data['departments']))[0] ?? 'N/A';
            @endphp
            <li><strong>Most Popular Scholarship:</strong> {{ ucfirst($mostPopularType) }} scholarships are most in demand.</li>
            <li><strong>Leading Department:</strong> {{ $mostPopularDept }} has the highest scholarship participation.</li>
        </ul>
    </div>

    <div class="footer">
        <p>This report was automatically generated by the Scholarship Management System on {{ $data['generated_date'] }}</p>
        <p>For detailed analysis and raw data, please refer to the comprehensive Excel reports.</p>
    </div>
</body>
</html>
