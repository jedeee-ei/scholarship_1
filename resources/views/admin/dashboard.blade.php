@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[]" />
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <div class="dashboard-actions">
            <div class="application-toggle-container">
                <label class="toggle-label">
                    <span class="toggle-text">Student Applications</span>
                    <div class="toggle-switch-wrapper">
                        <input type="checkbox" id="applicationToggle" class="toggle-input"
                               {{ $applicationStatus === 'open' ? 'checked' : '' }}>
                        <div class="toggle-switch">
                            <div class="toggle-slider"></div>
                        </div>
                        <span class="toggle-status">{{ $applicationStatus === 'open' ? 'Open' : 'Closed' }}</span>
                    </div>
                </label>
            </div>
            <div class="date">{{ date('F d, Y') }}</div>
        </div>
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
            <div class="action-title">Import Grantees</div>
            <div class="action-description">Bulk import grantee data</div>
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
                <i class="fas fa-arrow-{{ $changes['pending'] > 0 ? 'up' : 'down' }}"></i>
                {{ abs($changes['pending']) }}% from last month
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
            <h3 class="chart-title">Grantees by Benefactor Type</h3>
            <div class="chart-canvas">
                <canvas id="benefactorChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <h3 class="chart-title">Scholarships Through the Years</h3>
            <div class="chart-canvas">
                <canvas id="yearlyChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            initializeNavigation();
            initializeApplicationToggle();
        });

        // Initialize navigation to ensure proper link behavior
        function initializeNavigation() {
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Allow default link behavior
                    return true;
                });
            });
        }

        // Initialize application toggle functionality
        function initializeApplicationToggle() {
            const toggle = document.getElementById('applicationToggle');
            const statusText = document.querySelector('.toggle-status');

            if (toggle && statusText) {
                toggle.addEventListener('change', async function() {
                    const isChecked = this.checked;

                    try {
                        const response = await fetch('/admin/toggle-application-status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Update status text
                            statusText.textContent = result.status === 'open' ? 'Open' : 'Closed';

                            // Show success message
                            showNotification(`Applications are now ${result.status}`, 'success');
                        } else {
                            // Revert toggle if failed
                            this.checked = !isChecked;
                            showNotification('Failed to update application status', 'error');
                        }
                    } catch (error) {
                        console.error('Error toggling application status:', error);
                        // Revert toggle if failed
                        this.checked = !isChecked;
                        showNotification('Failed to update application status', 'error');
                    }
                });
            }
        }

        // Simple notification function
        function showNotification(message, type = 'success') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : '#dc3545'};
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                font-weight: 500;
                max-width: 300px;
            `;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 3000);
        }

        // Chart initialization with simplified 2 charts
        function initializeCharts() {
            const chartData = {!! json_encode($chartData) !!};
            console.log('Dashboard Chart Data:', chartData); // Debug log to see what data we have

            // 1. Benefactor Distribution Chart (Pie Chart)
            const benefactorCtx = document.getElementById('benefactorChart').getContext('2d');
            const benefactorData = chartData.benefactorDistribution || {};
            new Chart(benefactorCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(benefactorData),
                    datasets: [{
                        data: Object.values(benefactorData),
                        backgroundColor: [
                            '#1e5631', // CHED
                            '#2d7a3d', // DOST
                            '#3e8e4a', // DSWD
                            '#4fa256', // DOLE
                            '#60b662', // Employee
                            '#71c66e', // Private
                            '#82d67a' // Others
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} grantees (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // 2. Yearly Scholarships Chart (Line Chart)
            const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
            new Chart(yearlyCtx, {
                type: 'line',
                data: {
                    labels: chartData.years,
                    datasets: [{
                        label: 'Scholarships',
                        data: chartData.scholarshipCounts,
                        borderColor: '#1e5631',
                        backgroundColor: 'rgba(30, 86, 49, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#1e5631',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    return 'Year: ' + context[0].label;
                                },
                                label: function(context) {
                                    const count = context.parsed.y;
                                    return count + ' scholarship' + (count !== 1 ? 's' : '') + ' awarded';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Number of Scholarships'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Year'
                            }
                        }
                    }
                }
            });

        }

        // Refresh charts and data
        function refreshDashboard() {
            // Refresh charts by reinitializing them
            initializeCharts();
        }

        // Action button functions
        function showAddScholarshipForm() {
            // Redirect to benefactor programs page where they can add new benefactors
            window.location.href = '/admin/scholarship-programs';
        }

        function showBulkImportForm() {
            // Show modal for bulk import
            showBulkImportModal();
        }

        function exportApplications() {
            // Show export options modal
            showExportModal();
        }

        function showSystemSettings() {
            // Show system settings modal
            showSettingsModal();
        }

        // Bulk Import Modal
        function showBulkImportModal() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Bulk Import Grantees</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="bulkImportForm" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="importFile">Select CSV File:</label>
                                <input type="file" id="importFile" name="file" accept=".csv" required>
                                <small>Upload a CSV file with student data. <a href="/admin/download-template" target="_blank">Download Template</a></small>
                            </div>
                            <div class="form-group">
                                <label for="scholarshipType">Scholarship Type:</label>
                                <select id="scholarshipType" name="scholarship_type" required>
                                    <option value="">Select Type</option>
                                    <option value="government">Government Scholarship</option>
                                    <option value="academic">Academic Scholarship</option>
                                    <option value="employees">Employee Scholarship</option>
                                    <option value="private">Private Scholarship</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button onclick="closeModal()" class="btn-secondary">Cancel</button>
                        <button onclick="submitBulkImport()" class="btn-primary">Import</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Export Modal
        function showExportModal() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Export Data</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="export-options">
                            <div class="export-option" onclick="exportData('applications')">
                                <i class="fas fa-file-alt"></i>
                                <h4>Applications Report</h4>
                                <p>Export all scholarship applications</p>
                            </div>
                            <div class="export-option" onclick="exportData('students')">
                                <i class="fas fa-users"></i>
                                <h4>Grantees Report</h4>
                                <p>Export active benefactor grantees</p>
                            </div>
                            <div class="export-option" onclick="exportData('analytics')">
                                <i class="fas fa-chart-bar"></i>
                                <h4>Analytics Report</h4>
                                <p>Export dashboard analytics data</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button onclick="closeModal()" class="btn-secondary">Close</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // System Settings Modal
        function showSettingsModal() {
            const modal = document.createElement('div');
            modal.className = 'modal-overlay';
            modal.innerHTML = `
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>System Settings</h3>
                        <button onclick="closeModal()" class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="settingsForm">
                            <div class="settings-section">
                                <h4>Academic Year Settings</h4>
                                <div class="form-group">
                                    <label for="currentAY">Current Academic Year:</label>
                                    <input type="text" id="currentAY" name="academic_year" value="2024-2025">
                                </div>
                                <div class="form-group">
                                    <label for="currentSem">Current Semester:</label>
                                    <select id="currentSem" name="semester">
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="settings-section">
                                <h4>Application Settings</h4>
                                <div class="form-group">
                                    <label for="maxApplications">Max Applications per Student:</label>
                                    <input type="number" id="maxApplications" name="max_applications" value="3" min="1">
                                </div>
                                <div class="form-group">
                                    <label for="minGWA">Minimum GWA Requirement:</label>
                                    <input type="number" id="minGWA" name="min_gwa" value="1.75" step="0.01" min="1.00" max="5.00">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button onclick="closeModal()" class="btn-secondary">Cancel</button>
                        <button onclick="saveSettings()" class="btn-primary">Save Settings</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Modal utility functions
        function closeModal() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
        }

        // Submit functions
        async function submitBulkImport() {
            const form = document.getElementById('bulkImportForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('/admin/bulk-import', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Import successful! ' + result.message);
                    closeModal();
                    location.reload();
                } else {
                    alert('Import failed: ' + result.message);
                }
            } catch (error) {
                alert('Import failed. Please try again.');
            }
        }

        function exportData(type) {
            const url = `/admin/export/${type}`;
            window.open(url, '_blank');
            closeModal();
        }

        async function saveSettings() {
            const form = document.getElementById('settingsForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('/admin/settings', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Settings saved successfully!');
                    closeModal();
                } else {
                    alert('Failed to save settings: ' + result.message);
                }
            } catch (error) {
                alert('Settings save failed. Please try again.');
            }
        }

        // Dropdown toggle function
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentElement;
            const menu = dropdown.querySelector('.dropdown-menu');
            const arrow = dropdown.querySelector('.dropdown-arrow');

            dropdown.classList.toggle('open');

            if (dropdown.classList.contains('open')) {
                menu.style.maxHeight = menu.scrollHeight + 'px';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                menu.style.maxHeight = '0';
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    </script>
@endpush
