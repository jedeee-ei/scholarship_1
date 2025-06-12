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
            <div class="date">{{ date('F d, Y') }}</div>
        </div>
    </div>
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Pending Applications</h3>
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value" id="pendingApplicationsCount">{{ $pendingApplicationsCount }}</div>
            <div class="stat-change neutral">
                <i class="fas fa-calendar"></i> Current Semester
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Approved Applications</h3>
                <div class="stat-icon approved">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value" id="approvedApplicationsCount">{{ $approvedApplicationsCount }}</div>
            <div class="stat-change positive">
                <i class="fas fa-users"></i> Active Grantees
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">System Settings</h3>
                <div class="stat-icon total">
                    <i class="fas fa-cogs"></i>
                </div>
            </div>
            <div class="stat-value" style="font-size: 18px; line-height: 1.2;">
                {{ $currentSemester }}<br>
                <small style="font-size: 14px; color: #666;">{{ $currentAcademicYear }}</small>
            </div>
            <div class="stat-change neutral">
                <a href="#" onclick="showSettingsModal(); return false;" style="color: #1e5631; text-decoration: none;">
                    <i class="fas fa-edit"></i> Configure
                </a>
            </div>
        </div>
    </div>



    <!-- Pie Charts Section -->
    <div class="charts-section">
        <div class="chart-container">
            <h3 class="chart-title">Grantees per Scholarship Type</h3>
            <div class="chart-canvas">
                <canvas id="studentsChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <h3 class="chart-title">Scholarship Types</h3>
            <div class="chart-canvas">
                <canvas id="scholarshipTypesChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <h3 class="chart-title">Graduates per Academic Year</h3>
            <div class="chart-canvas">
                <canvas id="graduatesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Line Chart Section -->
    <div class="charts-section line-chart-section">
        <div class="chart-container">
            <h3 class="chart-title">Total Scholarship Recipients Growth</h3>
            <div class="chart-canvas">
                <canvas id="yearlyChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Action button functions - Define immediately for onclick handlers
        window.showBulkImportForm = function() {
            console.log('showBulkImportForm called');
            try {
                // Show modal for bulk import
                window.showBulkImportModal();
            } catch (error) {
                console.error('Error in showBulkImportForm:', error);
            }
        };

        window.exportApplications = function() {
            console.log('exportApplications called');
            try {
                // Show export options modal
                window.showExportModal();
            } catch (error) {
                console.error('Error in exportApplications:', error);
            }
        };

        window.showSystemSettings = function() {
            console.log('showSystemSettings called');
            try {
                // Show system settings modal
                window.showSettingsModal();
            } catch (error) {
                console.error('Error in showSystemSettings:', error);
                alert('Error opening system settings. Please try again.');
            }
        };

        // Modal utility functions
        window.closeModal = function() {
            const modal = document.querySelector('.modal-overlay');
            if (modal) {
                modal.remove();
            }
        };



        // System Settings Modal
        window.showSettingsModal = async function() {
            try {
                // Fetch current settings
                const response = await fetch('/admin/current-semester-year');

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

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
                                <!-- Application Control Section -->
                                <div class="settings-section">
                                    <h4>Application Control</h4>
                                    <div class="application-control-section">
                                        <div class="application-control-info">
                                            <h5>Allow New Applications</h5>
                                            <p>Enable or disable student scholarship applications</p>
                                        </div>
                                        <div class="toggle-control">
                                            <div class="toggle-container">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" id="modalApplicationToggle" name="application_status" ${data.application_status === 'open' ? 'checked' : ''}>
                                                    <span class="toggle-slider"></span>
                                                </label>
                                                <span id="modalApplicationStatusText" class="status-text" style="color: ${data.application_status === 'open' ? '#28a745' : '#dc3545'};">
                                                    ${data.application_status === 'open' ? 'Open' : 'Closed'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Year Settings Section -->
                                <div class="settings-section">
                                    <h4>Academic Year Settings</h4>
                                    <div class="form-group">
                                        <label for="currentAY">Current Academic Year:</label>
                                        <input type="text" id="currentAY" name="current_academic_year" value="${data.current_academic_year}">
                                    </div>
                                    <div class="form-group">
                                        <label for="currentSem">Current Semester:</label>
                                        <select id="currentSem" name="current_semester">
                                            <option value="1st Semester" ${data.current_semester === '1st Semester' ? 'selected' : ''}>1st Semester</option>
                                            <option value="2nd Semester" ${data.current_semester === '2nd Semester' ? 'selected' : ''}>2nd Semester</option>
                                            <option value="Summer" ${data.current_semester === 'Summer' ? 'selected' : ''}>Summer</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Advanced Operations Section -->
                                <div class="settings-section">
                                    <h4>Advanced Operations</h4>
                                    <div class="advanced-operations">
                                        <button type="button" onclick="updateSemester()" class="btn-warning">
                                            <i class="fas fa-calendar-alt"></i> Update Semester
                                        </button>
                                        <button type="button" onclick="updateAcademicYear()" class="btn-danger">
                                            <i class="fas fa-calendar-check"></i> Update Academic Year
                                        </button>
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

                // Add toggle functionality
                const toggle = document.getElementById('modalApplicationToggle');
                const statusText = document.getElementById('modalApplicationStatusText');

                if (toggle && statusText) {
                    toggle.addEventListener('change', function() {
                        const isOpen = this.checked;
                        statusText.textContent = isOpen ? 'Open' : 'Closed';
                        statusText.style.color = isOpen ? '#28a745' : '#dc3545';
                    });
                }

            } catch (error) {
                console.error('Error loading settings:', error);
                alert('Error loading current settings. Please try again.');
            }
        };

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard DOM loaded, initializing...');
            try {
                initializeCharts();
                initializeNavigation();
                console.log('Dashboard initialization complete');
            } catch (error) {
                console.error('Dashboard initialization error:', error);
            }
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

        // Chart initialization with 4 charts
        function initializeCharts() {
            try {
                const chartData = {!! json_encode($chartData) !!};
                console.log('Dashboard Chart Data:', chartData); // Debug log to see what data we have

                // Check if Chart.js is loaded
                if (typeof Chart === 'undefined') {
                    console.error('Chart.js is not loaded');
                    return;
                }

                // 1. Students per Scholarship Type Chart (Pie Chart)
                const studentsCtx = document.getElementById('studentsChart').getContext('2d');
                const studentsData = chartData.studentsPerScholarshipType || {};

                // Check if we have data
                if (Object.keys(studentsData).length === 0) {
                    // Show "No data" message
                    studentsCtx.font = "16px Arial";
                    studentsCtx.fillStyle = "#666";
                    studentsCtx.textAlign = "center";
                    studentsCtx.fillText("No student data available", studentsCtx.canvas.width / 2, studentsCtx.canvas.height / 2);
                } else {
                    new Chart(studentsCtx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(studentsData),
                            datasets: [{
                                data: Object.values(studentsData),
                                backgroundColor: [
                                    '#3498db', // Government - Blue
                                    '#e74c3c', // Academic - Red
                                    '#f39c12', // Employee - Orange
                                    '#9b59b6', // Alumni - Purple
                                    '#1abc9c', // Others - Teal
                                    '#34495e', // Additional - Dark Gray
                                    '#e67e22', // Extra - Dark Orange
                                    '#2ecc71'  // Extra - Green
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
                                            return `${label}: ${value} student${value !== 1 ? 's' : ''} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // 2. Scholarship Types Count Chart (Pie Chart)
                const scholarshipTypesCtx = document.getElementById('scholarshipTypesChart').getContext('2d');
                const scholarshipTypesData = chartData.scholarshipTypesCount || {};

                // Check if we have scholarship types data
                if (Object.keys(scholarshipTypesData).length === 0) {
                    // Show "No data" message
                    scholarshipTypesCtx.font = "16px Arial";
                    scholarshipTypesCtx.fillStyle = "#666";
                    scholarshipTypesCtx.textAlign = "center";
                    scholarshipTypesCtx.fillText("No scholarship types data available", scholarshipTypesCtx.canvas.width / 2, scholarshipTypesCtx.canvas.height / 2);
                } else {
                    new Chart(scholarshipTypesCtx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(scholarshipTypesData),
                            datasets: [{
                                data: Object.values(scholarshipTypesData),
                                backgroundColor: [
                                    '#27ae60', // Government - Green
                                    '#8e44ad', // Academic - Purple
                                    '#e67e22', // Employee - Orange
                                    '#2980b9', // Alumni - Blue
                                    '#f39c12', // Others - Yellow
                                    '#95a5a6', // Additional - Gray
                                    '#c0392b', // Extra - Red
                                    '#16a085'  // Extra - Teal
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
                                            return `${label}: ${value} type${value !== 1 ? 's' : ''} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // 3. Graduates Chart (Pie Chart)
                const graduatesCtx = document.getElementById('graduatesChart').getContext('2d');
                const graduatesData = chartData.graduatesData || {};

                // Check if we have graduates data
                if (Object.keys(graduatesData).length === 0) {
                    // Show "No data" message
                    graduatesCtx.font = "16px Arial";
                    graduatesCtx.fillStyle = "#666";
                    graduatesCtx.textAlign = "center";
                    graduatesCtx.fillText("No graduates data available", graduatesCtx.canvas.width / 2, graduatesCtx.canvas.height / 2);
                } else {
                    new Chart(graduatesCtx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(graduatesData),
                            datasets: [{
                                data: Object.values(graduatesData),
                                backgroundColor: [
                                    '#2ecc71', // Green
                                    '#3498db', // Blue
                                    '#e74c3c', // Red
                                    '#f39c12', // Orange
                                    '#9b59b6', // Purple
                                    '#1abc9c', // Teal
                                    '#34495e', // Dark Gray
                                    '#e67e22'  // Dark Orange
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
                                            return `${label}: ${value} graduate${value !== 1 ? 's' : ''} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // 4. Yearly Scholarships Chart (Line Chart)
                const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
                new Chart(yearlyCtx, {
                    type: 'line',
                    data: {
                        labels: chartData.years,
                        datasets: [{
                            label: 'Total Recipients',
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
                                        return 'Total: ' + count + ' scholarship recipient' + (count !== 1 ? 's' : '');
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
                                    text: 'Total Recipients'
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

            } catch (error) {
                console.error('Error initializing charts:', error);
            }
        }

        // Refresh charts and data
        function refreshDashboard() {
            // Refresh charts by reinitializing them
            initializeCharts();
            // Refresh dashboard stats
            refreshDashboardStats();
        }

        // Refresh dashboard stats
        async function refreshDashboardStats() {
            try {
                const response = await fetch('/api/admin/dashboard-stats');
                if (response.ok) {
                    const data = await response.json();

                    // Update pending applications count
                    const pendingElement = document.getElementById('pendingApplicationsCount');
                    if (pendingElement) {
                        pendingElement.textContent = data.pending_applications;
                    }

                    // Update approved applications count
                    const approvedElement = document.getElementById('approvedApplicationsCount');
                    if (approvedElement) {
                        approvedElement.textContent = data.approved_applications;
                    }

                    console.log('Dashboard stats refreshed:', data);
                } else {
                    console.error('Failed to refresh dashboard stats');
                }
            } catch (error) {
                console.error('Error refreshing dashboard stats:', error);
            }
        }









        window.saveSettings = async function() {
            const form = document.getElementById('settingsForm');
            const formData = new FormData(form);

            // Handle checkbox for application status
            const toggle = document.getElementById('modalApplicationToggle');
            if (toggle) {
                formData.set('application_status', toggle.checked ? 'open' : 'closed');
            }

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
                    showNotification('Settings saved successfully!', 'success');
                    closeModal();
                    // Refresh the page to update any displayed settings
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Failed to save settings: ' + result.message, 'error');
                }
            } catch (error) {
                showNotification('Settings save failed. Please try again.', 'error');
            }
        };

        // Semester update function
        window.updateSemester = async function() {
            try {
                // Fetch current semester data
                const response = await fetch('/admin/current-semester-year');
                if (!response.ok) {
                    throw new Error('Failed to fetch current semester/year');
                }

                const data = await response.json();
                const currentSemester = data.current_semester;
                const nextSemester = currentSemester === '1st Semester' ? '2nd Semester' : '1st Semester';

                const confirmed = await customConfirm(
                    `Are you sure you want to update from "${currentSemester}" to "${nextSemester}"?\n\nThis will archive current students and reset applications.`,
                    'Update Semester',
                    'warning'
                );

                if (confirmed) {
                    const updateResponse = await fetch('/admin/settings/update-semester', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            current_semester: currentSemester,
                            new_semester: nextSemester
                        })
                    });

                    const result = await updateResponse.json();

                    if (result.success) {
                        showNotification(result.message, 'success');
                        closeModal();
                        // Refresh dashboard stats immediately
                        refreshDashboardStats();
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showNotification('Failed to update semester: ' + result.message, 'error');
                    }
                }
            } catch (error) {
                console.error('Error updating semester:', error);
                showNotification('Error updating semester. Please try again.', 'error');
            }
        };

        // Academic year update function
        window.updateAcademicYear = async function() {
            try {
                // Fetch current academic year data
                const response = await fetch('/admin/current-semester-year');
                if (!response.ok) {
                    throw new Error('Failed to fetch current semester/year');
                }

                const data = await response.json();
                const currentYear = data.current_academic_year;
                const yearParts = currentYear.split('-');
                const nextYear = (parseInt(yearParts[0]) + 1) + '-' + (parseInt(yearParts[1]) + 1);

                const confirmed = await customConfirm(
                    `Are you sure you want to update from "${currentYear}" to "${nextYear}"?\n\nThis will reset to 1st Semester, archive current students, and reset applications.`,
                    'Update Academic Year',
                    'warning'
                );

                if (confirmed) {
                    const updateResponse = await fetch('/admin/settings/update-year', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            current_year: currentYear,
                            new_year: nextYear
                        })
                    });

                    const result = await updateResponse.json();

                    if (result.success) {
                        showNotification(result.message, 'success');
                        closeModal();
                        // Refresh dashboard stats immediately
                        refreshDashboardStats();
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showNotification('Failed to update academic year: ' + result.message, 'error');
                    }
                }
            } catch (error) {
                console.error('Error updating academic year:', error);
                showNotification('Error updating academic year. Please try again.', 'error');
            }
        };

        // Custom confirm dialog with modal
        function customConfirm(message, title = 'Confirm', type = 'warning') {
            return new Promise((resolve) => {
                const modal = document.createElement('div');
                modal.className = 'modal-overlay';
                modal.innerHTML = `
                    <div class="modal-content confirm-dialog">
                        <div class="modal-header">
                            <h3>${title}</h3>
                            <button onclick="closeConfirmModal(false)" class="close-btn">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="confirm-icon">⚠️</div>
                            <div class="confirm-message">${message.replace(/\n/g, '<br>')}</div>
                            <div class="confirm-buttons">
                                <button onclick="closeConfirmModal(true)" class="btn-primary">Yes</button>
                                <button onclick="closeConfirmModal(false)" class="btn-secondary">No</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);

                // Store the resolve function globally so the buttons can access it
                window.currentConfirmResolve = resolve;

                // Function to close modal and resolve promise
                window.closeConfirmModal = function(result) {
                    if (window.currentConfirmResolve) {
                        window.currentConfirmResolve(result);
                        window.currentConfirmResolve = null;
                    }
                    if (modal.parentElement) {
                        modal.remove();
                    }
                };
            });
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
