<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Scholarship Management</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }

        .stat-icon.total {
            background-color: #1e5631;
        }

        .stat-icon.pending {
            background-color: #f57f17;
        }

        .stat-icon.approved {
            background-color: #2e7d32;
        }

        .stat-icon.rejected {
            background-color: #c62828;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 0 0 5px;
        }

        .stat-change {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-change.positive {
            color: #2e7d32;
        }

        .stat-change.negative {
            color: #c62828;
        }

        .stat-change.neutral {
            color: #666;
        }

        .recent-applications {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 30px;
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .recent-title {
            font-size: 18px;
            color: #333;
            margin: 0;
        }

        .view-all {
            color: #1e5631;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        .applications-table {
            width: 100%;
            border-collapse: collapse;
        }

        .applications-table th {
            background-color: #f5f5f5;
            padding: 12px 15px;
            text-align: left;
            font-size: 14px;
            color: #555;
            font-weight: 600;
            border-bottom: 1px solid #ddd;
        }

        .applications-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #333;
        }

        .applications-table tr:last-child td {
            border-bottom: none;
        }

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status.pending {
            background-color: #fff8e1;
            color: #f57f17;
        }

        .status.review {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .status.approved {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status.rejected {
            background-color: #ffebee;
            color: #c62828;
        }

        .action-btn {
            display: inline-block;
            padding: 5px 10px;
            background-color: #1e5631;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
        }

        .action-btn:hover {
            background-color: #164023;
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        .chart-canvas {
            position: relative;
            height: 300px;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .action-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover {
            transform: translateY(-5px);
            text-decoration: none;
            color: inherit;
        }

        .action-icon {
            font-size: 32px;
            color: #1e5631;
            margin-bottom: 10px;
        }

        .action-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .action-description {
            font-size: 12px;
            color: #666;
        }

        /* Reports Section Styles */
        .report-categories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .category-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .category-icon {
            font-size: 48px;
            color: #1e5631;
            margin-bottom: 15px;
        }

        .category-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .category-description {
            font-size: 14px;
            color: #666;
        }

        /* Report Panel Styles */
        .report-panel,
        .archive-panel {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .panel-header {
            background-color: #1e5631;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .panel-header h3 {
            margin: 0;
            font-size: 20px;
        }

        .close-panel-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .close-panel-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .panel-body {
            padding: 30px;
        }

        .custom-date-range {
            margin-top: 15px;
        }

        /* Archive Table Styles */
        .archive-table,
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .archive-table th,
        .archive-table td,
        .students-table th,
        .students-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .archive-table th,
        .students-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .action-btn.delete {
            background-color: #dc3545;
            margin-left: 5px;
        }

        .action-btn.delete:hover {
            background-color: #c82333;
        }

        /* Students Section Styles */
        .student-categories {
            margin-bottom: 30px;
        }

        .category-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 12px 20px;
            border: 2px solid #1e5631;
            background-color: white;
            color: #1e5631;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .tab-btn.active,
        .tab-btn:hover {
            background-color: #1e5631;
            color: white;
        }

        .student-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .student-stats .stat-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 25px;
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: #1e5631;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .student-table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background-color: #f8f9fa;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .table-header h3 {
            margin: 0;
            color: #333;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .category-tabs {
                flex-wrap: wrap;
            }

            .tab-btn {
                padding: 10px 15px;
                font-size: 14px;
            }

            .table-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .table-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>
<body>
    <!-- University Header -->
    <header class="university-header">
        <div class="header-content">
            <div class="university-logo-title">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo" class="university-logo">
                <div class="university-title">
                    <h1>St. Paul University Philippines</h1>
                    <h2>ADMINISTRATOR DASHBOARD</h2>
                </div>
            </div>
            <div class="user-actions">
                <a href="{{ route('welcome') }}" class="logout-btn">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </header>

    <!-- Dashboard Banner -->
    <div class="dashboard-banner">
        <div class="banner-container">
            <h2>SCHOLARSHIP MANAGEMENT SYSTEM</h2>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-profile">
                <div class="profile-image">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-info">
                    <h3>Admin User</h3>
                    <p>Administrator</p>
                </div>
            </div>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}" class="nav-item active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.applications') }}" class="nav-item">
                    <i class="fas fa-graduation-cap"></i> Applications
                </a>
                <a href="#" class="nav-item" onclick="showStudentsSection()">
                    <i class="fas fa-users"></i> Students
                </a>
                <a href="#" class="nav-item" onclick="showScholarshipsSection()">
                    <i class="fas fa-award"></i> Scholarships
                </a>
                <a href="#" class="nav-item" onclick="showReportsSection()">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="#" class="nav-item" onclick="showSettingsSection()">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>
        </div>

        <!-- Main Dashboard Content -->
        <div class="admin-content">
            <div class="dashboard-header">
                <h1>Dashboard</h1>
                <div class="date">{{ date('F d, Y') }}</div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="#" class="action-card" onclick="showAddScholarshipForm()">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-title">Add Scholarship</div>
                    <div class="action-description">Create new scholarship program</div>
                </a>
                <a href="#" class="action-card" onclick="showBulkImportForm()">
                    <div class="action-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <div class="action-title">Import Students</div>
                    <div class="action-description">Bulk import student data</div>
                </a>
                <a href="#" class="action-card" onclick="exportApplications()">
                    <div class="action-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="action-title">Export Data</div>
                    <div class="action-description">Download application reports</div>
                </a>
                <a href="#" class="action-card" onclick="showSystemSettings()">
                    <div class="action-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="action-title">System Settings</div>
                    <div class="action-description">Configure system parameters</div>
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Total Applications</h3>
                        <div class="stat-icon total">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> {{ $changes['total'] }}% from last month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Pending Applications</h3>
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
                    <div class="stat-change {{ $changes['pending'] > 0 ? 'negative' : 'positive' }}">
                        <i class="fas fa-arrow-{{ $changes['pending'] > 0 ? 'up' : 'down' }}"></i> {{ abs($changes['pending']) }}% from last month
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Approved Applications</h3>
                        <div class="stat-icon approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['approved'] }}</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> {{ $changes['approved'] }}% from last month
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Rejected Applications</h3>
                        <div class="stat-icon rejected">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value">{{ $stats['rejected'] }}</div>
                    <div class="stat-change neutral">
                        <i class="fas fa-minus"></i> {{ $changes['rejected'] }}% from last month
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-container">
                    <h3 class="chart-title">Applications Over Time</h3>
                    <div class="chart-canvas">
                        <canvas id="applicationsChart"></canvas>
                    </div>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">Scholarship Types</h3>
                    <div class="chart-canvas">
                        <canvas id="scholarshipTypesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="recent-applications">
                <div class="recent-header">
                    <h3 class="recent-title">Recent Applications</h3>
                    <a href="{{ route('admin.applications') }}" class="view-all">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApplications as $application)
                            <tr>
                                <td>{{ $application->application_id }}</td>
                                <td>{{ $application->first_name }} {{ $application->last_name }}</td>
                                <td>
                                    @if($application->scholarship_type == 'ched')
                                        CHED
                                    @elseif($application->scholarship_type == 'presidents')
                                        Institutional
                                    @elseif($application->scholarship_type == 'employees')
                                        Employees
                                    @elseif($application->scholarship_type == 'private')
                                        Private
                                    @else
                                        {{ ucfirst($application->scholarship_type) }}
                                    @endif
                                </td>
                                <td>{{ $application->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="status
                                        @if($application->status == 'Pending Review') pending
                                        @elseif($application->status == 'Under Committee Review' || $application->status == 'Decision Made') review
                                        @elseif($application->status == 'Approved') approved
                                        @elseif($application->status == 'Rejected') rejected
                                        @endif">
                                        {{ $application->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.application.view', $application->application_id) }}" class="action-btn">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">No applications found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reports Section (Hidden by default) -->
        <div class="admin-content" id="reports-section" style="display: none;">
            <div class="dashboard-header">
                <h1>Reports & Archive</h1>
                <div class="date">{{ date('F d, Y') }}</div>
            </div>

            <!-- Report Categories -->
            <div class="report-categories">
                <div class="category-card" onclick="showApplicationReports()">
                    <div class="category-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="category-title">Application Reports</div>
                    <div class="category-description">Generate reports for scholarship applications</div>
                </div>
                <div class="category-card" onclick="showStudentReports()">
                    <div class="category-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="category-title">Student Reports</div>
                    <div class="category-description">Generate reports for student data</div>
                </div>
                <div class="category-card" onclick="showScholarshipReports()">
                    <div class="category-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="category-title">Scholarship Reports</div>
                    <div class="category-description">Generate reports for scholarship programs</div>
                </div>
                <div class="category-card" onclick="showArchive()">
                    <div class="category-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="category-title">Archive</div>
                    <div class="category-description">Access archived reports and data</div>
                </div>
            </div>

            <!-- Report Generation Panel -->
            <div class="report-panel" id="report-panel" style="display: none;">
                <div class="panel-header">
                    <h3 id="panel-title">Generate Report</h3>
                    <button class="close-panel-btn" onclick="closeReportPanel()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="panel-body">
                    <form id="reportForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="reportType">Report Type</label>
                                <select id="reportType" name="report_type" required>
                                    <option value="">Select Report Type</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="dateRange">Date Range</label>
                                <select id="dateRange" name="date_range" required>
                                    <option value="">Select Date Range</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="quarter">This Quarter</option>
                                    <option value="year">This Year</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row custom-date-range" style="display: none;">
                            <div class="form-group">
                                <label for="startDate">Start Date</label>
                                <input type="date" id="startDate" name="start_date">
                            </div>
                            <div class="form-group">
                                <label for="endDate">End Date</label>
                                <input type="date" id="endDate" name="end_date">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="format">Export Format</label>
                                <select id="format" name="format" required>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="includeCharts">Include Charts</label>
                                <select id="includeCharts" name="include_charts">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" onclick="previewReport()" class="btn-secondary">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-download"></i> Generate & Download
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Archive Panel -->
            <div class="archive-panel" id="archive-panel" style="display: none;">
                <div class="panel-header">
                    <h3>Archive</h3>
                    <button class="close-panel-btn" onclick="closeArchivePanel()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="panel-body">
                    <div class="archive-filters">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="archiveType">Archive Type</label>
                                <select id="archiveType" name="archive_type">
                                    <option value="">All Types</option>
                                    <option value="applications">Applications</option>
                                    <option value="students">Students</option>
                                    <option value="reports">Reports</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="archiveYear">Year</label>
                                <select id="archiveYear" name="archive_year">
                                    <option value="">All Years</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                    <option value="2021">2021</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="button" onclick="searchArchive()" class="btn-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="archive-results">
                        <table class="archive-table">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Type</th>
                                    <th>Date Created</th>
                                    <th>Size</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="archiveTableBody">
                                <tr>
                                    <td>Applications_Report_2023_Q4.pdf</td>
                                    <td>Applications</td>
                                    <td>Dec 31, 2023</td>
                                    <td>2.5 MB</td>
                                    <td>
                                        <button class="action-btn" onclick="downloadArchive('app_2023_q4')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteArchive('app_2023_q4')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Student_Data_2023.xlsx</td>
                                    <td>Students</td>
                                    <td>Dec 15, 2023</td>
                                    <td>1.8 MB</td>
                                    <td>
                                        <button class="action-btn" onclick="downloadArchive('student_2023')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteArchive('student_2023')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Scholarship_Summary_2023.pdf</td>
                                    <td>Reports</td>
                                    <td>Nov 30, 2023</td>
                                    <td>3.2 MB</td>
                                    <td>
                                        <button class="action-btn" onclick="downloadArchive('scholarship_2023')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteArchive('scholarship_2023')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Section (Hidden by default) -->
        <div class="admin-content" id="students-section" style="display: none;">
            <div class="dashboard-header">
                <h1>Student Management</h1>
                <div class="date">{{ date('F d, Y') }}</div>
            </div>

            <!-- Student Categories -->
            <div class="student-categories">
                <div class="category-tabs">
                    <button class="tab-btn active" onclick="showStudentCategory('all')">All Students</button>
                    <button class="tab-btn" onclick="showStudentCategory('ched')">CHED Scholars</button>
                    <button class="tab-btn" onclick="showStudentCategory('presidents')">Institutional Scholars</button>
                    <button class="tab-btn" onclick="showStudentCategory('employees')">Employee Scholars</button>
                    <button class="tab-btn" onclick="showStudentCategory('private')">Private Scholars</button>
                </div>
            </div>

            <!-- Student Statistics -->
            <div class="student-stats">
                <div class="stat-card">
                    <div class="stat-number" id="totalStudents">0</div>
                    <div class="stat-label">Total Students</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="activeScholars">0</div>
                    <div class="stat-label">Active Scholars</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="pendingApplications">0</div>
                    <div class="stat-label">Pending Applications</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="graduatedScholars">0</div>
                    <div class="stat-label">Graduated Scholars</div>
                </div>
            </div>

            <!-- Student Table -->
            <div class="student-table-container">
                <div class="table-header">
                    <h3 id="categoryTitle">All Students</h3>
                    <div class="table-actions">
                        <button class="btn-secondary" onclick="exportStudentData()">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn-primary" onclick="showAddStudentForm()">
                            <i class="fas fa-plus"></i> Add Student
                        </button>
                    </div>
                </div>
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Scholarship Type</th>
                            <th>Status</th>
                            <th>GWA</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <!-- Student data will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Forms -->
    <!-- Add Scholarship Form Modal -->
    <div id="addScholarshipModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Scholarship Program</h3>
                <span class="close" onclick="closeModal('addScholarshipModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="addScholarshipForm">
                    <div class="form-group">
                        <label for="scholarshipName">Scholarship Name *</label>
                        <input type="text" id="scholarshipName" name="scholarship_name" required>
                    </div>
                    <div class="form-group">
                        <label for="scholarshipType">Scholarship Type *</label>
                        <select id="scholarshipType" name="scholarship_type" required>
                            <option value="">Select Type</option>
                            <option value="ched">CHED Scholarship</option>
                            <option value="institutional">Institutional Scholarship</option>
                            <option value="private">Private Scholarship</option>
                            <option value="employees">Employees Scholar</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scholarshipDescription">Description</label>
                        <textarea id="scholarshipDescription" name="description" rows="4"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="applicationDeadline">Application Deadline</label>
                            <input type="date" id="applicationDeadline" name="deadline">
                        </div>
                        <div class="form-group">
                            <label for="maxSlots">Maximum Slots</label>
                            <input type="number" id="maxSlots" name="max_slots" min="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="eligibilityCriteria">Eligibility Criteria</label>
                        <textarea id="eligibilityCriteria" name="eligibility" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeModal('addScholarshipModal')" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Add Scholarship</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Import Students Modal -->
    <div id="bulkImportModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Bulk Import Students</h3>
                <span class="close" onclick="closeModal('bulkImportModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="bulkImportForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="importFile">Select CSV File *</label>
                        <input type="file" id="importFile" name="import_file" accept=".csv" required>
                        <small>Upload a CSV file with student information. <a href="#" onclick="downloadTemplate()">Download template</a></small>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="updateExisting" name="update_existing">
                            Update existing students if found
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeModal('bulkImportModal')" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Import Students</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- System Settings Modal -->
    <div id="systemSettingsModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>System Settings</h3>
                <span class="close" onclick="closeModal('systemSettingsModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="systemSettingsForm">
                    <div class="form-group">
                        <label for="applicationPeriod">Application Period Status</label>
                        <select id="applicationPeriod" name="application_period">
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                            <option value="renewal">Renewal Only</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="academicYear">Current Academic Year</label>
                            <input type="text" id="academicYear" name="academic_year" placeholder="2023-2024">
                        </div>
                        <div class="form-group">
                            <label for="currentSemester">Current Semester</label>
                            <select id="currentSemester" name="current_semester">
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                                <option value="summer">Summer</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="notificationEmail">Notification Email</label>
                        <input type="email" id="notificationEmail" name="notification_email" placeholder="admin@spup.edu.ph">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="emailNotifications" name="email_notifications">
                            Enable email notifications for new applications
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="closeModal('systemSettingsModal')" class="btn-secondary">Cancel</button>
                        <button type="submit" class="btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Modal Styles */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: #1e5631;
        }

        .close {
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .close:hover {
            color: #333;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1e5631;
            box-shadow: 0 0 0 3px rgba(30, 86, 49, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .btn-primary,
        .btn-secondary {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #1e5631;
            color: white;
        }

        .btn-primary:hover {
            background-color: #164023;
        }

        .btn-secondary {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
        }
    </style>

    <script>
        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
        });

        // Chart initialization
        function initializeCharts() {
            // Applications Over Time Chart
            const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
            new Chart(applicationsCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['months']) !!},
                    datasets: [{
                        label: 'Applications',
                        data: {!! json_encode($chartData['applicationCounts']) !!},
                        borderColor: '#1e5631',
                        backgroundColor: 'rgba(30, 86, 49, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Scholarship Types Chart
            const typesCtx = document.getElementById('scholarshipTypesChart').getContext('2d');
            const scholarshipData = {!! json_encode(array_values($chartData['scholarshipTypes'])) !!};
            new Chart(typesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['CHED', 'Institutional', 'Private', 'Employees'],
                    datasets: [{
                        data: scholarshipData,
                        backgroundColor: [
                            '#1e5631',
                            '#2e7d32',
                            '#388e3c',
                            '#4caf50'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Modal functions
        function showAddScholarshipForm() {
            document.getElementById('addScholarshipModal').style.display = 'flex';
        }

        function showBulkImportForm() {
            document.getElementById('bulkImportModal').style.display = 'flex';
        }

        function showSystemSettings() {
            document.getElementById('systemSettingsModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Navigation functions
        function showStudentsSection() {
            // Hide all sections
            document.querySelectorAll('.admin-content').forEach(section => {
                section.style.display = 'none';
            });

            // Show students section
            document.getElementById('students-section').style.display = 'block';

            // Update navigation
            updateNavigation('students');

            // Load student data
            loadStudentData();
        }

        function showScholarshipsSection() {
            alert('Scholarship management section will be implemented here.');
        }

        function showReportsSection() {
            // Hide all sections
            document.querySelectorAll('.admin-content').forEach(section => {
                section.style.display = 'none';
            });

            // Show reports section
            document.getElementById('reports-section').style.display = 'block';

            // Update navigation
            updateNavigation('reports');
        }

        function showSettingsSection() {
            showSystemSettings();
        }

        function updateNavigation(activeSection) {
            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Add active class to current section
            const navItems = document.querySelectorAll('.nav-item');
            switch(activeSection) {
                case 'dashboard':
                    navItems[0].classList.add('active');
                    break;
                case 'applications':
                    navItems[1].classList.add('active');
                    break;
                case 'students':
                    navItems[2].classList.add('active');
                    break;
                case 'scholarships':
                    navItems[3].classList.add('active');
                    break;
                case 'reports':
                    navItems[4].classList.add('active');
                    break;
                case 'settings':
                    navItems[5].classList.add('active');
                    break;
            }
        }

        // Export function
        function exportApplications() {
            window.location.href = '{{ route("admin.applications.export") }}';
        }

        // Download template function
        function downloadTemplate() {
            alert('CSV template download will be implemented here.');
        }

        // Form submissions
        document.getElementById('addScholarshipForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route("admin.scholarships.add") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('addScholarshipModal');
                    this.reset();
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the scholarship');
            });
        });

        document.getElementById('bulkImportForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route("admin.students.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('bulkImportModal');
                    this.reset();
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while importing students');
            });
        });

        document.getElementById('systemSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('{{ route("admin.settings.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeModal('systemSettingsModal');
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating settings');
            });
        });

        // Reports Section Functions
        function showApplicationReports() {
            document.getElementById('report-panel').style.display = 'block';
            document.getElementById('panel-title').textContent = 'Application Reports';

            const reportTypeSelect = document.getElementById('reportType');
            reportTypeSelect.innerHTML = `
                <option value="">Select Report Type</option>
                <option value="all_applications">All Applications</option>
                <option value="pending_applications">Pending Applications</option>
                <option value="approved_applications">Approved Applications</option>
                <option value="rejected_applications">Rejected Applications</option>
                <option value="application_summary">Application Summary</option>
            `;
        }

        function showStudentReports() {
            document.getElementById('report-panel').style.display = 'block';
            document.getElementById('panel-title').textContent = 'Student Reports';

            const reportTypeSelect = document.getElementById('reportType');
            reportTypeSelect.innerHTML = `
                <option value="">Select Report Type</option>
                <option value="all_students">All Students</option>
                <option value="active_scholars">Active Scholars</option>
                <option value="graduated_scholars">Graduated Scholars</option>
                <option value="student_performance">Student Performance</option>
                <option value="scholarship_recipients">Scholarship Recipients</option>
            `;
        }

        function showScholarshipReports() {
            document.getElementById('report-panel').style.display = 'block';
            document.getElementById('panel-title').textContent = 'Scholarship Reports';

            const reportTypeSelect = document.getElementById('reportType');
            reportTypeSelect.innerHTML = `
                <option value="">Select Report Type</option>
                <option value="scholarship_distribution">Scholarship Distribution</option>
                <option value="budget_utilization">Budget Utilization</option>
                <option value="program_effectiveness">Program Effectiveness</option>
                <option value="renewal_rates">Renewal Rates</option>
            `;
        }

        function showArchive() {
            document.getElementById('archive-panel').style.display = 'block';
            document.getElementById('report-panel').style.display = 'none';
        }

        function closeReportPanel() {
            document.getElementById('report-panel').style.display = 'none';
        }

        function closeArchivePanel() {
            document.getElementById('archive-panel').style.display = 'none';
        }

        function previewReport() {
            alert('Report preview will be implemented here.');
        }

        function searchArchive() {
            const archiveType = document.getElementById('archiveType').value;
            const archiveYear = document.getElementById('archiveYear').value;

            // Here you would filter the archive table based on the selected criteria
            alert(`Searching archive for Type: ${archiveType || 'All'}, Year: ${archiveYear || 'All'}`);
        }

        function downloadArchive(fileId) {
            alert(`Downloading archive file: ${fileId}`);
        }

        function deleteArchive(fileId) {
            if (confirm('Are you sure you want to delete this archive file?')) {
                alert(`Deleting archive file: ${fileId}`);
            }
        }

        // Students Section Functions
        function loadStudentData() {
            // Sample student data - in real implementation, this would come from the backend
            const sampleStudents = [
                {
                    id: 'STU-001',
                    name: 'John Doe',
                    course: 'BS Computer Science',
                    scholarshipType: 'CHED',
                    status: 'Active',
                    gwa: '1.25'
                },
                {
                    id: 'STU-002',
                    name: 'Jane Smith',
                    course: 'BS Nursing',
                    scholarshipType: 'Institutional',
                    status: 'Active',
                    gwa: '1.50'
                },
                {
                    id: 'STU-003',
                    name: 'Mike Johnson',
                    course: 'BS Engineering',
                    scholarshipType: 'Private',
                    status: 'Graduated',
                    gwa: '1.75'
                }
            ];

            updateStudentStats(sampleStudents);
            displayStudents(sampleStudents, 'all');
        }

        function updateStudentStats(students) {
            document.getElementById('totalStudents').textContent = students.length;
            document.getElementById('activeScholars').textContent = students.filter(s => s.status === 'Active').length;
            document.getElementById('pendingApplications').textContent = '5'; // Sample data
            document.getElementById('graduatedScholars').textContent = students.filter(s => s.status === 'Graduated').length;
        }

        function showStudentCategory(category) {
            // Update tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Update category title
            const titles = {
                'all': 'All Students',
                'ched': 'CHED Scholars',
                'presidents': 'Institutional Scholars',
                'employees': 'Employee Scholars',
                'private': 'Private Scholars'
            };
            document.getElementById('categoryTitle').textContent = titles[category];

            // Filter and display students
            // In real implementation, this would filter the actual data
            loadStudentData(); // For now, just reload sample data
        }

        function displayStudents(students, category) {
            const tbody = document.getElementById('studentsTableBody');
            tbody.innerHTML = '';

            students.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.id}</td>
                    <td>${student.name}</td>
                    <td>${student.course}</td>
                    <td>${student.scholarshipType}</td>
                    <td><span class="status-badge ${student.status.toLowerCase()}">${student.status}</span></td>
                    <td>${student.gwa}</td>
                    <td>
                        <button class="action-btn" onclick="viewStudent('${student.id}')">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn" onclick="editStudent('${student.id}')">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function viewStudent(studentId) {
            alert(`Viewing student details for: ${studentId}`);
        }

        function editStudent(studentId) {
            alert(`Editing student: ${studentId}`);
        }

        function exportStudentData() {
            alert('Exporting student data...');
        }

        function showAddStudentForm() {
            alert('Add student form will be implemented here.');
        }

        // Date range change handler
        document.addEventListener('DOMContentLoaded', function() {
            const dateRangeSelect = document.getElementById('dateRange');
            if (dateRangeSelect) {
                dateRangeSelect.addEventListener('change', function() {
                    const customDateRange = document.querySelector('.custom-date-range');
                    if (this.value === 'custom') {
                        customDateRange.style.display = 'flex';
                    } else {
                        customDateRange.style.display = 'none';
                    }
                });
            }

            // Report form submission
            const reportForm = document.getElementById('reportForm');
            if (reportForm) {
                reportForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    alert('Generating report... This will be connected to backend.');
                });
            }
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });
    </script>

    <style>
        /* Additional styles for status badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.graduated {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            background-color: #1e5631;
            color: white;
            border: none;
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            font-size: 12px;
        }

        .action-btn:hover {
            background-color: #164023;
        }
    </style>
</body>
</html>


