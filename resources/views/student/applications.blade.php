<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - Scholarship Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e5631;
            --primary-light: #e8f5e8;
            --primary-dark: #164023;
            --text-color: #333;
            --border-color: #ddd;
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Header */
        .university-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .university-logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 20px;
        }

        .user-actions {
            display: flex;
            gap: 15px;
        }

        .action-link {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            text-decoration: none;
            color: white;
        }

        .action-link.active {
            background-color: rgba(255, 255, 255, 0.3);
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-subtitle {
            color: #666;
            font-size: 16px;
        }

        /* Applications Table */
        .applications-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .applications-table {
            width: 100%;
            border-collapse: collapse;
        }

        .applications-table th,
        .applications-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .applications-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--text-color);
        }

        .applications-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-badge.approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .view-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .view-btn:hover {
            background-color: var(--primary-dark);
            text-decoration: none;
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 30px;
        }

        .empty-icon {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .empty-description {
            color: #666;
            margin-bottom: 30px;
        }

        .apply-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .apply-btn:hover {
            background-color: var(--primary-dark);
            text-decoration: none;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }

            .user-actions {
                flex-direction: column;
                width: 100%;
            }

            .applications-table {
                font-size: 14px;
            }

            .applications-table th,
            .applications-table td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="university-header">
        <div class="header-container">
            <div class="university-logo">
                <div class="logo-img">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                St. Paul University Philippines - Scholarship Management
            </div>
            <div class="user-actions">
                <a href="/student/dashboard" class="action-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="/student/applications" class="action-link active">
                    <i class="fas fa-clipboard-list"></i> My Applications
                </a>
                <a href="/student/profile" class="action-link">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-clipboard-list"></i>
                My Scholarship Applications
            </h1>
            <p class="page-subtitle">Track and manage your scholarship applications</p>
        </div>

        <div class="applications-container">
            @if(isset($applications) && $applications->count() > 0)
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>Application ID</th>
                            <th>Scholarship Type</th>
                            <th>Date Applied</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                        <tr>
                            <td><strong>{{ $application->application_id }}</strong></td>
                            <td>{{ ucfirst($application->scholarship_type) }} Scholarship</td>
                            <td>{{ $application->created_at->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge {{ strtolower(str_replace(' ', '-', $application->status)) }}">
                                    {{ $application->status }}
                                </span>
                            </td>
                            <td>
                                <a href="/scholarship/tracker/{{ $application->application_id }}" class="view-btn">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3 class="empty-title">No Applications Yet</h3>
                    <p class="empty-description">You haven't submitted any scholarship applications yet. Start your journey by applying for a scholarship!</p>
                    <a href="/student/dashboard" class="apply-btn">
                        <i class="fas fa-plus"></i> Apply for Scholarship
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
