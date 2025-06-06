@extends('layouts.admin')

@section('title', 'Reports & Archive')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/reports.css') }}">
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[['title' => 'Reports & Archive', 'icon' => 'fas fa-chart-bar']]" />
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>Reports & Archive</h1>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Total Applications</h3>
                <div class="stat-icon total">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="stat-value">{{ $reportStats['total_applications'] }}</div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> All time
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">This Month</h3>
                <div class="stat-icon pending">
                    <i class="fas fa-calendar"></i>
                </div>
            </div>
            <div class="stat-value">{{ $reportStats['applications_this_month'] }}</div>
            <div class="stat-change positive">
                <i class="fas fa-calendar-alt"></i> {{ date('F Y') }}
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Approved</h3>
                <div class="stat-icon approved">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ $reportStats['by_status']['approved'] }}</div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> Active scholars
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Pending</h3>
                <div class="stat-icon rejected">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value">{{ $reportStats['by_status']['pending'] }}</div>
            <div class="stat-change neutral">
                <i class="fas fa-minus"></i> Awaiting review
            </div>
        </div>
    </div>

    <!-- Report Categories -->
    <div class="report-categories">
        <div class="category-card" onclick="showApplicationReports()">
            <div class="category-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="category-title">Application Reports</div>
            <div class="category-description">Generate reports for scholarship applications
                ({{ $reportStats['total_applications'] }} total)</div>
        </div>
        <div class="category-card" onclick="showStudentReports()">
            <div class="category-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="category-title">Grantee Reports</div>
            <div class="category-description">Generate reports for grantee data
                ({{ $reportStats['by_status']['approved'] }} active)</div>
        </div>
        <div class="category-card" onclick="showScholarshipReports()">
            <div class="category-icon">
                <i class="fas fa-award"></i>
            </div>
            <div class="category-title">Benefactor Reports</div>
            <div class="category-description">Generate reports for benefactor programs (4 types)</div>
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
    <div class="report-panel" id="report-panel">
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
                <div class="form-row custom-date-range">
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
    <div class="archive-panel" id="archive-panel">
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
                            <option value="students">Grantees</option>
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
                            <td>Grantee_Data_2023.xlsx</td>
                            <td>Grantees</td>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Report management functions
        function showApplicationReports() {
            showReportPanel('Application Reports');
            populateReportTypes(['Application Summary', 'Pending Applications', 'Approved Applications',
                'Rejected Applications'
            ]);
        }

        function showStudentReports() {
            showReportPanel('Grantee Reports');
            populateReportTypes(['Grantee List', 'Grantee Performance', 'GWA Summary', 'Grantee Demographics']);
        }

        function showScholarshipReports() {
            showReportPanel('Benefactor Reports');
            populateReportTypes(['Benefactor Summary', 'Program Statistics', 'Budget Analysis', 'Utilization Report']);
        }

        function showArchive() {
            document.getElementById('archive-panel').style.display = 'block';
            document.getElementById('report-panel').style.display = 'none';
        }

        function showReportPanel(title) {
            document.getElementById('panel-title').textContent = title;
            document.getElementById('report-panel').style.display = 'block';
            document.getElementById('archive-panel').style.display = 'none';
        }

        function populateReportTypes(types) {
            const select = document.getElementById('reportType');
            select.innerHTML = '<option value="">Select Report Type</option>';
            types.forEach(type => {
                const option = document.createElement('option');
                option.value = type.toLowerCase().replace(/\s+/g, '_');
                option.textContent = type;
                select.appendChild(option);
            });
        }

        function closeReportPanel() {
            document.getElementById('report-panel').style.display = 'none';
        }

        function closeArchivePanel() {
            document.getElementById('archive-panel').style.display = 'none';
        }

        function previewReport() {
            console.log('Previewing report...');
        }

        function searchArchive() {
            console.log('Searching archive...');
        }

        function downloadArchive(fileId) {
            console.log('Downloading archive file:', fileId);
        }

        function deleteArchive(fileId) {
            console.log('Deleting archive file:', fileId);
        }

        // Show custom date range when selected
        document.getElementById('dateRange').addEventListener('change', function() {
            const customRange = document.querySelector('.custom-date-range');
            if (this.value === 'custom') {
                customRange.style.display = 'block';
            } else {
                customRange.style.display = 'none';
            }
        });
    </script>
@endpush
