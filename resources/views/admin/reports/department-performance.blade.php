<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Department Performance Report</title>
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
        
        .performance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .performance-table th,
        .performance-table td {
            border: 1px solid #ddd;
            padding: 10px 12px;
            text-align: left;
        }
        
        .performance-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        .performance-table td.number {
            text-align: right;
        }
        
        .performance-table td.center {
            text-align: center;
        }
        
        .performance-indicator {
            padding: 4px 8px;
            border-radius: 3px;
            display: inline-block;
            font-size: 10px;
            font-weight: bold;
        }
        
        .excellent { background-color: #d4edda; color: #155724; }
        .good { background-color: #d1ecf1; color: #0c5460; }
        .average { background-color: #fff3cd; color: #856404; }
        .needs-improvement { background-color: #f8d7da; color: #721c24; }
        
        .department-row {
            background-color: #f8f9fa;
        }
        
        .department-row.highlight {
            background-color: #e8f5e8;
        }
        
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        
        .summary-box h4 {
            margin: 0 0 10px 0;
            color: #052f11;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SCHOLARSHIP MANAGEMENT SYSTEM</h1>
        <h2>Department Performance Analysis</h2>
        <p>Generated: {{ $data['generated_date'] }}</p>
    </div>

    <!-- Performance Overview -->
    <div class="section">
        <h3 class="section-title">DEPARTMENT PERFORMANCE OVERVIEW</h3>
        <table class="performance-table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Total Students</th>
                    <th>Active Students</th>
                    <th>Graduated Students</th>
                    <th>Retention Rate</th>
                    <th>Performance Rating</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalStudents = 0;
                    $totalActive = 0;
                    $totalGraduated = 0;
                    $bestDepartment = '';
                    $bestRate = 0;
                @endphp
                
                @foreach($data['departments'] as $dept => $stats)
                    @php
                        $totalStudents += $stats['total_students'];
                        $totalActive += $stats['active_students'];
                        $totalGraduated += $stats['graduated_students'];
                        
                        if ($stats['retention_rate'] > $bestRate) {
                            $bestRate = $stats['retention_rate'];
                            $bestDepartment = $dept;
                        }
                        
                        $performanceClass = 'needs-improvement';
                        $performanceText = 'Needs Improvement';
                        
                        if ($stats['retention_rate'] >= 90) {
                            $performanceClass = 'excellent';
                            $performanceText = 'Excellent';
                        } elseif ($stats['retention_rate'] >= 80) {
                            $performanceClass = 'good';
                            $performanceText = 'Good';
                        } elseif ($stats['retention_rate'] >= 70) {
                            $performanceClass = 'average';
                            $performanceText = 'Average';
                        }
                    @endphp
                    
                    <tr class="{{ $dept === $bestDepartment ? 'department-row highlight' : 'department-row' }}">
                        <td><strong>{{ $dept }}</strong></td>
                        <td class="number">{{ number_format($stats['total_students']) }}</td>
                        <td class="number">{{ number_format($stats['active_students']) }}</td>
                        <td class="number">{{ number_format($stats['graduated_students']) }}</td>
                        <td class="center">
                            <strong>{{ $stats['retention_rate'] }}%</strong>
                        </td>
                        <td class="center">
                            <span class="performance-indicator {{ $performanceClass }}">
                                {{ $performanceText }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                
                <tr style="font-weight: bold; background-color: #052f11; color: white;">
                    <td>TOTAL</td>
                    <td class="number">{{ number_format($totalStudents) }}</td>
                    <td class="number">{{ number_format($totalActive) }}</td>
                    <td class="number">{{ number_format($totalGraduated) }}</td>
                    <td class="center">
                        @php
                            $overallRetention = ($totalStudents + $totalGraduated) > 0 ? 
                                round(($totalGraduated / ($totalStudents + $totalGraduated)) * 100, 1) : 0;
                        @endphp
                        {{ $overallRetention }}%
                    </td>
                    <td class="center">Overall</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Department Analysis -->
    <div class="section">
        <h3 class="section-title">DETAILED DEPARTMENT ANALYSIS</h3>
        
        @foreach($data['departments'] as $dept => $stats)
            <div class="summary-box">
                <h4>{{ $dept }} - 
                    @switch($dept)
                        @case('SITE')
                            School of Information Technology and Engineering
                            @break
                        @case('SBAHM')
                            School of Business, Accountancy, and Hospitality Management
                            @break
                        @case('SNASH')
                            School of Nursing and Allied Health Sciences
                            @break
                        @case('SASTE')
                            School of Arts, Sciences and Teacher Education
                            @break
                        @default
                            {{ $dept }}
                    @endswitch
                </h4>
                
                <div style="display: table; width: 100%;">
                    <div style="display: table-row;">
                        <div style="display: table-cell; width: 50%; padding-right: 20px;">
                            <p><strong>Student Statistics:</strong></p>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                <li>Total Scholarship Recipients: {{ number_format($stats['total_students']) }}</li>
                                <li>Currently Active: {{ number_format($stats['active_students']) }}</li>
                                <li>Successfully Graduated: {{ number_format($stats['graduated_students']) }}</li>
                            </ul>
                        </div>
                        <div style="display: table-cell; width: 50%;">
                            <p><strong>Performance Metrics:</strong></p>
                            <ul style="margin: 5px 0; padding-left: 20px;">
                                <li>Retention Rate: <strong>{{ $stats['retention_rate'] }}%</strong></li>
                                <li>Active vs Total: {{ $stats['total_students'] > 0 ? round(($stats['active_students'] / $stats['total_students']) * 100, 1) : 0 }}%</li>
                                <li>Graduation Success: {{ ($stats['total_students'] + $stats['graduated_students']) > 0 ? round(($stats['graduated_students'] / ($stats['total_students'] + $stats['graduated_students'])) * 100, 1) : 0 }}%</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                @php
                    $recommendations = [];
                    if ($stats['retention_rate'] < 70) {
                        $recommendations[] = "Consider implementing additional student support programs";
                        $recommendations[] = "Review scholarship requirements and student guidance processes";
                    } elseif ($stats['retention_rate'] < 85) {
                        $recommendations[] = "Good performance - consider sharing best practices with other departments";
                    } else {
                        $recommendations[] = "Excellent performance - this department can serve as a model for others";
                    }
                    
                    if ($stats['active_students'] > $stats['graduated_students'] * 2) {
                        $recommendations[] = "High number of active students - monitor graduation progress";
                    }
                @endphp
                
                @if(!empty($recommendations))
                    <p><strong>Recommendations:</strong></p>
                    <ul style="margin: 5px 0; padding-left: 20px; color: #052f11;">
                        @foreach($recommendations as $recommendation)
                            <li>{{ $recommendation }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Key Insights -->
    <div class="section">
        <h3 class="section-title">KEY INSIGHTS & RECOMMENDATIONS</h3>
        <div class="summary-box">
            <h4>Overall Performance Summary</h4>
            <ul style="margin: 10px 0; padding-left: 20px;">
                @if($bestDepartment)
                    <li><strong>Top Performing Department:</strong> {{ $bestDepartment }} with {{ $bestRate }}% retention rate</li>
                @endif
                
                @php
                    $avgRetention = count($data['departments']) > 0 ? 
                        round(array_sum(array_column($data['departments'], 'retention_rate')) / count($data['departments']), 1) : 0;
                @endphp
                <li><strong>Average Retention Rate:</strong> {{ $avgRetention }}% across all departments</li>
                
                <li><strong>Total Program Impact:</strong> {{ number_format($totalStudents + $totalGraduated) }} students have benefited from scholarship programs</li>
                
                @if($avgRetention >= 80)
                    <li><strong>Program Health:</strong> Excellent - retention rates indicate strong student support</li>
                @elseif($avgRetention >= 70)
                    <li><strong>Program Health:</strong> Good - some departments may benefit from additional support</li>
                @else
                    <li><strong>Program Health:</strong> Needs attention - consider reviewing support mechanisms</li>
                @endif
            </ul>
            
            <h4>Strategic Recommendations</h4>
            <ul style="margin: 10px 0; padding-left: 20px; color: #052f11;">
                <li>Share best practices from high-performing departments with others</li>
                <li>Implement regular monitoring of student progress and early intervention programs</li>
                <li>Consider department-specific support programs based on individual needs</li>
                <li>Establish mentorship programs connecting successful graduates with current students</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>This report was automatically generated by the Scholarship Management System on {{ $data['generated_date'] }}</p>
        <p>For detailed student data and additional analysis, please refer to the comprehensive Excel reports.</p>
    </div>
</body>
</html>
