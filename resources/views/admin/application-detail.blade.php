<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Details - Scholarship Management</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .application-detail-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .application-id {
            font-size: 24px;
            font-weight: 700;
            color: #1e5631;
            margin-bottom: 5px;
        }

        .application-date {
            font-size: 14px;
            color: #666;
        }

        .status-section {
            text-align: right;
        }

        .current-status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .current-status.pending {
            background-color: #fff8e1;
            color: #f57f17;
        }

        .current-status.review {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .current-status.approved {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .current-status.rejected {
            background-color: #ffebee;
            color: #c62828;
        }

        .status-form {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .status-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 10px;
            width: 200px;
        }

        .update-btn {
            background-color: #1e5631;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
        }

        .update-btn:hover {
            background-color: #164023;
        }

        .detail-content {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .detail-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: #1e5631;
        }

        .detail-group {
            margin-bottom: 15px;
            display: flex;
        }

        .detail-label {
            font-size: 14px;
            color: #666;
            width: 150px;
            flex-shrink: 0;
        }

        .detail-value {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .action-btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn.approve {
            background-color: #2e7d32;
            color: white;
            border: none;
        }

        .action-btn.approve:hover {
            background-color: #1b5e20;
        }

        .action-btn.reject {
            background-color: #c62828;
            color: white;
            border: none;
        }

        .action-btn.reject:hover {
            background-color: #b71c1c;
        }

        .action-btn.back {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }

        .action-btn.back:hover {
            background-color: #e5e5e5;
        }

        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .success-message i {
            margin-right: 10px;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .detail-content {
                grid-template-columns: 1fr;
            }

            .detail-header {
                flex-direction: column;
            }

            .status-section {
                text-align: left;
                margin-top: 20px;
            }

            .status-form {
                align-items: flex-start;
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
                <a href="{{ route('admin.dashboard') }}" class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.applications') }}" class="nav-item active">
                    <i class="fas fa-graduation-cap"></i> Applications
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-users"></i> Students
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>
        </div>

        <!-- Main Application Detail Content -->
        <div class="admin-content">
            <div class="dashboard-header">
                <h1>Application Details</h1>
                <div class="date">{{ date('F d, Y') }}</div>
            </div>

            @if(session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="application-detail-container">
                <div class="detail-header">
                    <div class="application-info">
                        <div class="application-id">{{ $application->application_id }}</div>
                        <div class="application-date">Submitted on {{ $application->created_at->format('F d, Y') }}</div>
                    </div>

                    <div class="status-section">
                        <div class="current-status
                            @if($application->status == 'Pending Review') pending
                            @elseif($application->status == 'Under Committee Review' || $application->status == 'Decision Made') review
                            @elseif($application->status == 'Approved') approved
                            @elseif($application->status == 'Rejected') rejected
                            @endif">
                            {{ $application->status }}
                        </div>

                        <form action="{{ route('admin.application.status', $application->application_id) }}" method="POST" class="status-form">
                            @csrf
                            <select name="status" id="status" class="status-select">
                                <option value="Pending Review" {{ $application->status == 'Pending Review' ? 'selected' : '' }}>Pending Review</option>
                                <option value="Under Committee Review" {{ $application->status == 'Under Committee Review' ? 'selected' : '' }}>Under Committee Review</option>
                                <option value="Decision Made" {{ $application->status == 'Decision Made' ? 'selected' : '' }}>Decision Made</option>
                                <option value="Approved" {{ $application->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Rejected" {{ $application->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            <button type="submit" class="update-btn">Update Status</button>
                        </form>
                    </div>
                </div>

                <div class="detail-content">
                    <div class="left-column">
                        <div class="detail-section">
                            <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>

                            <div class="detail-group">
                                <div class="detail-label">Student ID</div>
                                <div class="detail-value">{{ $application->student_id }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Full Name</div>
                                <div class="detail-value">{{ $application->first_name }} {{ $application->last_name }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Email</div>
                                <div class="detail-value">{{ $application->email }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value">{{ $application->phone }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Address</div>
                                <div class="detail-value">{{ $application->address }}</div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h3 class="section-title"><i class="fas fa-graduation-cap"></i> Scholarship Information</h3>

                            <div class="detail-group">
                                <div class="detail-label">Scholarship Type</div>
                                <div class="detail-value">
                                    @if($application->scholarship_type == 'ched')
                                        CHED Scholarship
                                    @elseif($application->scholarship_type == 'presidents')
                                        President's Scholarship
                                    @elseif($application->scholarship_type == 'employees')
                                        Employees Scholar
                                    @elseif($application->scholarship_type == 'private')
                                        Private Scholarship
                                    @else
                                        {{ ucfirst($application->scholarship_type) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="right-column">
                        <div class="detail-section">
                            <h3 class="section-title"><i class="fas fa-school"></i> Educational Information</h3>

                            <div class="detail-group">
                                <div class="detail-label">Education Stage</div>
                                <div class="detail-value">{{ $application->education_stage }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Program</div>
                                <div class="detail-value">{{ $application->program }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Current Year</div>
                                <div class="detail-value">{{ $application->current_year }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Current Semester</div>
                                <div class="detail-value">{{ $application->current_semester }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">GPA</div>
                                <div class="detail-value">{{ $application->gpa }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <form action="{{ route('admin.application.status', $application->application_id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Approved">
                        <button type="submit" class="action-btn approve">
                            <i class="fas fa-check"></i> Approve Application
                        </button>
                    </form>

                    <form action="{{ route('admin.application.status', $application->application_id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Rejected">
                        <button type="submit" class="action-btn reject">
                            <i class="fas fa-times"></i> Reject Application
                        </button>
                    </form>

                    <a href="{{ route('admin.applications') }}" class="action-btn back">
                        <i class="fas fa-arrow-left"></i> Back to Applications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Status change confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const statusForm = document.querySelector('.status-form');
            const approveForm = document.querySelectorAll('.action-buttons form')[0];
            const rejectForm = document.querySelectorAll('.action-buttons form')[1];

            statusForm.addEventListener('submit', function(e) {
                const status = document.getElementById('status').value;
                const currentStatus = '{{ $application->status }}';

                if (status !== currentStatus) {
                    if (!confirm(`Are you sure you want to change the status to "${status}"?`)) {
                        e.preventDefault();
                    }
                }
            });

            approveForm.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to approve this application?')) {
                    e.preventDefault();
                }
            });

            rejectForm.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to reject this application?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

