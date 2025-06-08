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
                            <option value="pdf">PDF (Text Format)</option>
                            <option value="excel">Excel (CSV Format)</option>
                            <option value="csv">CSV</option>
                        </select>
                        <small style="color: #666; font-size: 0.85rem; margin-top: 4px; display: block;">
                            Note: PDF exports as formatted text file, Excel exports as CSV format
                        </small>
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
                    <button type="button" onclick="generateAndDownloadReport()" class="btn-primary">
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
    <!-- Preload PDF generator for faster PDF generation -->
    <script src="{{ asset('js/pdf-generator.js') }}"></script>
    <script>
        // Report management functions
        function showApplicationReports() {
            showReportPanel('Application Reports');
            populateReportTypes([
                'Application Summary',
                'Approved Applications',
                'CHED Applications',
                'Academic Applications'
            ]);
        }

        function showStudentReports() {
            showReportPanel('Grantee Reports');
            populateReportTypes(['Grantee List']);
        }

        function showScholarshipReports() {
            showReportPanel('Benefactor Reports');
            populateReportTypes(['Benefactor Summary']);
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
            const reportType = document.getElementById('reportType').value;
            const dateRange = document.getElementById('dateRange').value;
            const includeCharts = document.getElementById('includeCharts').value;

            if (!reportType) {
                showNotification('Please select a report type', 'error');
                return;
            }

            // Show loading state
            const previewBtn = document.querySelector('.btn-secondary');
            const originalText = previewBtn.innerHTML;
            previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating Preview...';
            previewBtn.disabled = true;

            // Make AJAX request to preview report
            fetch('/admin/reports/preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    report_type: reportType,
                    date_range: dateRange,
                    include_charts: includeCharts
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showReportPreviewModal(data);
                } else {
                    showNotification(data.message || 'Failed to generate preview', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while generating preview', 'error');
            })
            .finally(() => {
                // Restore button state
                previewBtn.innerHTML = originalText;
                previewBtn.disabled = false;
            });
        }

        function generateAndDownloadReport() {
            const reportType = document.getElementById('reportType').value;
            const dateRange = document.getElementById('dateRange').value;
            const exportFormat = document.getElementById('format').value;
            const includeCharts = document.getElementById('includeCharts').value;

            if (!reportType) {
                showNotification('Please select a report type', 'error');
                return;
            }

            // Show loading state with format-specific message
            const generateBtn = document.querySelector('.btn-primary');
            const originalText = generateBtn.innerHTML;
            const loadingMessage = exportFormat === 'pdf' ?
                '<i class="fas fa-file-pdf"></i> Generating PDF...' :
                '<i class="fas fa-spinner fa-spin"></i> Generating...';
            generateBtn.innerHTML = loadingMessage;
            generateBtn.disabled = true;

            // Prepare form data
            const formData = {
                report_type: reportType,
                date_range: dateRange,
                format: exportFormat,
                include_charts: includeCharts
            };

            // Add custom date range if selected
            if (dateRange === 'custom') {
                const startDate = document.getElementById('startDate')?.value;
                const endDate = document.getElementById('endDate')?.value;
                if (startDate && endDate) {
                    formData.start_date = startDate;
                    formData.end_date = endDate;
                } else {
                    showNotification('Please select start and end dates for custom range', 'error');
                    generateBtn.innerHTML = originalText;
                    generateBtn.disabled = false;
                    return;
                }
            }

            // Handle PDF generation differently
            if (exportFormat === 'pdf') {
                generatePDFReport(reportType, dateRange, includeCharts, generateBtn, originalText);
                return;
            }

            // Make AJAX request to generate report
            fetch('/admin/reports/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                const contentType = response.headers.get('content-type');

                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // Handle file download
                    const contentDisposition = response.headers.get('content-disposition');
                    let filename = `${reportType}_report_${Date.now()}.${exportFormat}`;

                    // Extract filename from content-disposition header if available
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    }

                    return response.blob().then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);

                        showNotification('Report downloaded successfully!', 'success');
                        setTimeout(() => closeReportPanel(), 1000);
                        return { success: true };
                    });
                }
            })
            .then(data => {
                if (data.success && data.download_url) {
                    // Handle JSON response with download URL
                    const downloadLink = document.createElement('a');
                    downloadLink.href = data.download_url;
                    downloadLink.download = `report_${reportType}_${Date.now()}.${exportFormat}`;
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);

                    showNotification('Report generated successfully!', 'success');
                    setTimeout(() => closeReportPanel(), 1000);
                } else if (!data.success && data.message) {
                    showNotification(data.message || 'Failed to generate report', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while generating report', 'error');
            })
            .finally(() => {
                // Restore button state
                generateBtn.innerHTML = originalText;
                generateBtn.disabled = false;
            });
        }

        // PDF Generation Function (optimized for speed)
        function generatePDFReport(reportType, dateRange, includeCharts, generateBtn, originalText) {
            // Update loading message for PDF
            generateBtn.innerHTML = '<i class="fas fa-file-pdf"></i> Preparing PDF data...';

            // First get the report data (optimized request)
            const formData = {
                report_type: reportType,
                date_range: dateRange,
                include_charts: includeCharts,
                limit: 50 // Limit data for faster PDF generation
            };

            // Add custom date range if selected
            if (dateRange === 'custom') {
                const startDate = document.getElementById('startDate')?.value;
                const endDate = document.getElementById('endDate')?.value;

                if (!startDate || !endDate) {
                    showNotification('Please select both start and end dates for custom range', 'error');
                    generateBtn.innerHTML = originalText;
                    generateBtn.disabled = false;
                    return;
                }

                formData.start_date = startDate;
                formData.end_date = endDate;
            }

            // Get report preview data for PDF generation
            fetch('/admin/reports/preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Prepare data for PDF generation
                    const reportData = {
                        title: data.data.report_type + ' Report',
                        summary: data.data.summary,
                        chartData: {
                            by_scholarship_type: data.data.summary.by_scholarship_type || {},
                            by_status: data.data.summary.by_status || {}
                        },
                        data: data.data.preview_records || []
                    };

                    // Initialize PDF generator and create PDF (optimized)
                    const generatePDFNow = () => {
                        // Update progress
                        generateBtn.innerHTML = '<i class="fas fa-file-pdf"></i> Creating PDF...';

                        const pdfGenerator = new window.ReportPDFGenerator();
                        pdfGenerator.generatePDF(reportData, reportType, includeCharts === 'yes')
                            .then(() => {
                                showNotification('PDF report generated and downloaded successfully!', 'success');
                            })
                            .catch(error => {
                                console.error('PDF generation error:', error);
                                showNotification('Error generating PDF. Please try again.', 'error');
                            })
                            .finally(() => {
                                generateBtn.innerHTML = originalText;
                                generateBtn.disabled = false;
                            });
                    };

                    if (typeof window.ReportPDFGenerator === 'undefined') {
                        // Load PDF generator script (faster loading)
                        const script = document.createElement('script');
                        script.src = '/js/pdf-generator.js';
                        script.onload = generatePDFNow;
                        script.onerror = () => {
                            showNotification('Error loading PDF generator. Please try again.', 'error');
                            generateBtn.innerHTML = originalText;
                            generateBtn.disabled = false;
                        };
                        document.head.appendChild(script);
                    } else {
                        // PDF generator already loaded, generate immediately
                        generatePDFNow();
                    }
                } else {
                    throw new Error(data.message || 'Failed to get report data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error generating PDF report. Please try again.', 'error');
                generateBtn.innerHTML = originalText;
                generateBtn.disabled = false;
            });
        }

        function searchArchive() {
            const searchTerm = document.getElementById('archiveSearch')?.value || '';
            const fileType = document.getElementById('archiveType').value;
            const year = document.getElementById('archiveYear').value;

            // Show loading state
            const searchBtn = document.querySelector('#archive-panel .btn-primary');
            const originalText = searchBtn.innerHTML;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
            searchBtn.disabled = true;

            // Make AJAX request to search archive
            fetch('/admin/archive/search', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateArchiveResults(data.files);
                    showNotification(`Found ${data.files.length} files`, 'success');
                } else {
                    showNotification(data.message || 'Search failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while searching', 'error');
            })
            .finally(() => {
                // Restore button state
                searchBtn.innerHTML = originalText;
                searchBtn.disabled = false;
            });
        }

        function downloadArchive(fileId) {
            showNotification('Downloading file...', 'info');

            // Create download URL using the base URL
            const downloadUrl = `/admin/archive/download/${fileId}`;

            // Create temporary download link
            const downloadLink = document.createElement('a');
            downloadLink.href = downloadUrl;
            downloadLink.download = '';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);

            showNotification('Download started', 'success');
        }

        function deleteArchive(fileId) {
            if (confirm('Are you sure you want to delete this archive file?')) {
                showNotification('Deleting file...', 'info');

                // Make AJAX request to delete file
                fetch(`/admin/archive/delete/${fileId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('File deleted successfully', 'success');
                        searchArchive(); // Refresh the archive list
                    } else {
                        showNotification(data.message || 'Failed to delete file', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred while deleting file', 'error');
                });
            }
        }

        function populateArchiveResults(files) {
            const tbody = document.getElementById('archiveTableBody');
            tbody.innerHTML = '';

            if (files.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">No files found</td>
                    </tr>
                `;
                return;
            }

            files.forEach(file => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${file.name}</td>
                    <td>${file.type}</td>
                    <td>${file.date}</td>
                    <td>${file.size}</td>
                    <td>
                        <button class="action-btn" onclick="downloadArchive('${file.id}')" title="Download">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteArchive('${file.id}')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function showReportPreviewModal(data) {
            const reportType = document.getElementById('reportType').selectedOptions[0].text;
            const dateRange = document.getElementById('dateRange').selectedOptions[0].text;
            const includeCharts = document.getElementById('includeCharts').value === 'yes' ? 'Yes' : 'No';

            // Generate detailed preview content based on data
            let detailsHTML = '';
            if (data.data) {
                const reportData = data.data;

                // Summary section
                detailsHTML = `
                    <div class="preview-summary">
                        <h4>Report Summary</h4>
                        <div class="summary-stats">
                            <div class="stat-item">
                                <span class="stat-label">Total Records:</span>
                                <span class="stat-value">${reportData.total_records || 0}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Showing Preview:</span>
                                <span class="stat-value">${reportData.showing_records || 0} of ${reportData.total_records || 0}</span>
                            </div>
                        </div>
                    </div>
                `;

                // Data table section
                if (reportData.preview_records && reportData.preview_records.length > 0) {
                    detailsHTML += `
                        <div class="preview-data">
                            <h4>Data Preview</h4>
                            <div class="table-container">
                                <table class="preview-table">
                                    <thead>
                                        <tr>
                                            <th>Application ID</th>
                                            <th>Student ID</th>
                                            <th>Full Name</th>
                                            <th>Scholarship Type</th>
                                            <th>Status</th>
                                            <th>Department</th>
                                            <th>Course</th>
                                            <th>GWA</th>
                                            <th>Application Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;

                    reportData.preview_records.forEach(record => {
                        detailsHTML += `
                            <tr>
                                <td>${record.application_id}</td>
                                <td>${record.student_id}</td>
                                <td>${record.full_name}</td>
                                <td>${record.scholarship_type}</td>
                                <td><span class="status-badge status-${record.status.toLowerCase().replace(' ', '-')}">${record.status}</span></td>
                                <td>${record.department}</td>
                                <td>${record.course}</td>
                                <td>${record.gwa}</td>
                                <td>${record.application_date}</td>
                            </tr>
                        `;
                    });

                    detailsHTML += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                } else {
                    detailsHTML += `
                        <div class="preview-data">
                            <p class="no-data">No data available for preview.</p>
                        </div>
                    `;
                }

                // Legacy support for old summary format
                if (reportData.total_applications !== undefined) {
                    detailsHTML += `
                        <div class="legacy-summary">
                            <div class="stat-item">
                                <span class="stat-label">Total Applications:</span>
                                <span class="stat-value">${reportData.total_applications}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Pending:</span>
                                <span class="stat-value">${reportData.pending_applications || 0}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Approved:</span>
                                <span class="stat-value">${reportData.approved_applications || 0}</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-label">Rejected:</span>
                                <span class="stat-value">${reportData.rejected_applications || 0}</span>
                            </div>
                        </div>
                    `;

                    if (reportData.by_scholarship_type) {
                        detailsHTML += '<div class="stat-item full-width"><span class="stat-label">By Scholarship Type:</span><br>';
                        for (const [type, count] of Object.entries(reportData.by_scholarship_type)) {
                            detailsHTML += `<span class="type-stat">${type.toUpperCase()}: ${count}</span><br>`;
                        }
                        detailsHTML += '</div>';
                    }
                } else if (reportData.total_pending !== undefined) {
                    detailsHTML = `
                        <div class="stat-item">
                            <span class="stat-label">Total Pending Applications:</span>
                            <span class="stat-value">${reportData.total_pending}</span>
                        </div>
                    `;
                } else if (reportData.total_approved !== undefined) {
                    detailsHTML = `
                        <div class="stat-item">
                            <span class="stat-label">Total Approved Applications:</span>
                            <span class="stat-value">${reportData.total_approved}</span>
                        </div>
                    `;
                } else if (reportData.total_rejected !== undefined) {
                    detailsHTML = `
                        <div class="stat-item">
                            <span class="stat-label">Total Rejected Applications:</span>
                            <span class="stat-value">${reportData.total_rejected}</span>
                        </div>
                    `;
                } else {
                    // Handle specific scholarship type reports
                    const keys = Object.keys(reportData);
                    for (const key of keys) {
                        if (key.includes('total_') && key.includes('_applications')) {
                            const scholarshipType = key.replace('total_', '').replace('_applications', '');
                            detailsHTML += `
                                <div class="stat-item">
                                    <span class="stat-label">Total ${scholarshipType.toUpperCase()} Applications:</span>
                                    <span class="stat-value">${reportData[key]}</span>
                                </div>
                            `;
                        }
                    }

                    if (reportData.by_status) {
                        detailsHTML += '<div class="stat-item full-width"><span class="stat-label">By Status:</span><br>';
                        for (const [status, count] of Object.entries(reportData.by_status)) {
                            detailsHTML += `<span class="type-stat">${status}: ${count}</span><br>`;
                        }
                        detailsHTML += '</div>';
                    }
                }
            }

            // Create preview modal
            const modal = document.createElement('div');
            modal.className = 'report-preview-modal';
            modal.innerHTML = `
                <div class="modal-overlay">
                    <div class="modal-content large-modal">
                        <div class="modal-header">
                            <h3><i class="fas fa-eye"></i> Report Preview</h3>
                            <button class="close-btn" onclick="closeReportPreviewModal()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="preview-content">
                                <div class="preview-info">
                                    <div class="info-item">
                                        <span class="info-label">Report Type:</span>
                                        <span class="info-value">${reportType}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Date Range:</span>
                                        <span class="info-value">${dateRange}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Include Charts:</span>
                                        <span class="info-value">${includeCharts}</span>
                                    </div>
                                </div>
                                ${detailsHTML}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn-secondary" onclick="closeReportPreviewModal()">
                                <i class="fas fa-times"></i> Close
                            </button>
                            <button class="btn-primary" onclick="generateAndDownloadReport(); closeReportPreviewModal();">
                                <i class="fas fa-download"></i> Generate & Download
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Close on overlay click
            modal.querySelector('.modal-overlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeReportPreviewModal();
                }
            });
        }

        function closeReportPreviewModal() {
            const modal = document.querySelector('.report-preview-modal');
            if (modal) {
                modal.remove();
            }
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    <span>${message}</span>
                </div>
                <button class="notification-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;

            // Add to page
            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }

        // Show custom date range when selected
        document.addEventListener('DOMContentLoaded', function() {
            const dateRangeSelect = document.getElementById('dateRange');
            if (dateRangeSelect) {
                dateRangeSelect.addEventListener('change', function() {
                    const customRange = document.querySelector('.custom-date-range');
                    if (this.value === 'custom') {
                        customRange.style.display = 'block';
                    } else {
                        customRange.style.display = 'none';
                    }
                });
            }
        });
    </script>

    <!-- Additional CSS for PDF generation feedback -->
    <style>
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-primary .fas.fa-file-pdf {
            color: #dc3545;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }

        /* Speed indicator */
        .speed-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1e5631;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 12px;
            z-index: 1000;
            display: none;
        }
    </style>
@endpush
