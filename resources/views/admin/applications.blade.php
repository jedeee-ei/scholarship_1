<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications - Scholarship Management</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        <!-- Main Applications Content -->
        <div class="admin-content">
            <div class="dashboard-header">
                <h1>Scholarship Applications</h1>
                <div class="filter-controls">
                    <form action="{{ route('admin.applications') }}" method="GET">
                        <select name="status" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Pending Review" {{ $currentStatus == 'Pending Review' ? 'selected' : '' }}>Pending Review</option>
                            <option value="Under Committee Review" {{ $currentStatus == 'Under Committee Review' ? 'selected' : '' }}>Committee Review</option>
                            <option value="Decision Made" {{ $currentStatus == 'Decision Made' ? 'selected' : '' }}>Decision Made</option>
                            <option value="Approved" {{ $currentStatus == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ $currentStatus == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <select name="type" class="filter-select" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="ched" {{ $currentType == 'ched' ? 'selected' : '' }}>CHED Scholarship</option>
                            <option value="presidents" {{ $currentType == 'presidents' ? 'selected' : '' }}>President's Scholarship</option>
                            <option value="employees" {{ $currentType == 'employees' ? 'selected' : '' }}>Employees Scholar</option>
                            <option value="private" {{ $currentType == 'private' ? 'selected' : '' }}>Private Scholarship</option>
                        </select>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-container">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Scholarship Type</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                        <tr>
                            <td>{{ $application->application_id }}</td>
                            <td>{{ $application->first_name }} {{ $application->last_name }}</td>
                            <td>{{ $application->student_id }}</td>
                            <td>
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
                            </td>
                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($application->status == 'Pending Review')
                                    <span class="status pending">Pending Review</span>
                                @elseif($application->status == 'Under Committee Review')
                                    <span class="status review">Committee Review</span>
                                @elseif($application->status == 'Decision Made')
                                    <span class="status review">Decision Made</span>
                                @elseif($application->status == 'Approved')
                                    <span class="status approved">Approved</span>
                                @elseif($application->status == 'Rejected')
                                    <span class="status rejected">Rejected</span>
                                @else
                                    <span class="status">{{ $application->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.application.view', $application->application_id) }}" class="action-btn">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                {{ $applications->appends(['status' => $currentStatus, 'type' => $currentType])->links() }}
            </div>
        </div>
    </div>
</body>
</html>

