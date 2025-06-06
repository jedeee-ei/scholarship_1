<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Tracker - St. Paul University Philippines</title>
    <link rel="stylesheet" href="{{ asset('css/scholarship.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tracker-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .tracker-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .tracker-title {
            font-size: 24px;
            color: #1e5631;
            margin-bottom: 10px;
        }

        .tracker-description {
            color: #666;
            font-size: 16px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }

        .search-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .search-btn {
            background-color: #1e5631;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-btn:hover {
            background-color: #164023;
        }

        .result-container {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }

        .application-details {
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 25px;
        }

        .application-id {
            font-size: 20px;
            color: #1e5631;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }

        .detail-label {
            width: 150px;
            font-size: 14px;
            color: #666;
            flex-shrink: 0;
        }

        .detail-value {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-badge.pending {
            background-color: #fff8e1;
            color: #f57f17;
        }

        .status-badge.review {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .status-badge.approved {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .status-badge.rejected {
            background-color: #ffebee;
            color: #c62828;
        }

        .status-timeline {
            margin-top: 30px;
        }

        .timeline-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            height: 100%;
            width: 2px;
            background-color: #ddd;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 25px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -30px;
            top: 0;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background-color: #ddd;
        }

        .timeline-dot.active {
            background-color: #1e5631;
        }

        .timeline-content {
            padding-left: 10px;
        }

        .timeline-status {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            margin-bottom: 5px;
        }

        .timeline-date {
            font-size: 14px;
            color: #666;
        }

        .timeline-description {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .no-result {
            text-align: center;
            padding: 30px;
            color: #666;
        }

        .no-result i {
            font-size: 48px;
            color: #ddd;
            margin-bottom: 15px;
        }

        .no-result-title {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 14px;
            text-decoration: none;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #e5e5e5;
        }

        .my-applications {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
        }

        .my-applications-title {
            font-size: 18px;
            color: #1e5631;
            margin-bottom: 15px;
        }

        .applications-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .application-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 15px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .application-item:hover {
            border-color: #1e5631;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .application-info {
            flex: 1;
        }

        .application-id-text {
            font-weight: 600;
            color: #1e5631;
            margin-bottom: 5px;
        }

        .application-meta {
            font-size: 14px;
            color: #666;
        }

        .application-status {
            margin-left: 15px;
        }

        .no-applications {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body>
    <!-- University Header -->
    <header class="university-header">
        <div class="header-content">
            <div class="university-logo-title">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo"
                    class="university-logo">
                <div class="university-title">
                    <h1>St. Paul University Philippines</h1>
                    <h2>OFFICE OF THE REGISTRAR</h2>
                </div>
            </div>
            <div class="user-actions">
                <a href="{{ route('logout') }}" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </div>
    </header>

    <!-- Dashboard Banner -->
    <div class="dashboard-banner">
        <div class="banner-container">
            <h2>STUDENT DASHBOARD</h2>
        </div>
    </div>

    <div class="application-container">
        <div class="tracker-container">
            <div class="tracker-header">
                <h1 class="tracker-title">Application Status Tracker</h1>
                <p class="tracker-description">Track your scholarship applications by clicking on them below or enter an
                    application ID manually.</p>
            </div>

            @if ($userApplications && $userApplications->count() > 0)
                <div class="my-applications">
                    <h3 class="my-applications-title">My Applications</h3>
                    <div class="applications-list">
                        @foreach ($userApplications as $app)
                            <div class="application-item" onclick="trackApplication('{{ $app->application_id }}')">
                                <div class="application-info">
                                    <div class="application-id-text">{{ $app->application_id }}</div>
                                    <div class="application-meta">
                                        @if ($app->scholarship_type == 'ched')
                                            CHED Scholarship
                                        @elseif($app->scholarship_type == 'presidents')
                                            President's Scholarship
                                        @elseif($app->scholarship_type == 'employees')
                                            Employees Scholar
                                        @elseif($app->scholarship_type == 'private')
                                            Private Scholarship
                                        @else
                                            {{ ucfirst($app->scholarship_type) }} Scholarship
                                        @endif
                                        • Applied {{ $app->created_at->format('M d, Y') }}
                                    </div>
                                </div>
                                <div class="application-status">
                                    <span
                                        class="status-badge
                                        @if ($app->status == 'Pending Review') pending
                                        @elseif($app->status == 'Under Committee Review') review
                                        @elseif($app->status == 'Approved') approved
                                        @elseif($app->status == 'Rejected') rejected @endif">
                                        {{ $app->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="my-applications">
                    <h3 class="my-applications-title">My Applications</h3>
                    <div class="no-applications">
                        You haven't submitted any scholarship applications yet.
                    </div>
                </div>
            @endif

            <form action="{{ route('scholarship.tracker') }}" method="GET" class="search-form" id="trackForm">
                <input type="text" name="id" class="search-input" id="applicationIdInput"
                    placeholder="Or enter Application ID manually (e.g., SCH-12345678)" value="{{ request('id') }}">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Track
                </button>
            </form>

            @php
                // Use the application passed from the controller
                // $application is already set by the controller
            @endphp

            @if (request('id') && $application)
                <div class="result-container">
                    @if (isset($application->is_debug_view) && $application->is_debug_view)
                        <div
                            style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 10px; margin-bottom: 20px; color: #856404;">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Debug Mode:</strong> Showing application
                            from different student for testing purposes.
                        </div>
                    @endif

                    <div class="application-details">
                        <div class="application-id">Application ID: {{ $application->application_id }}</div>

                        <div class="detail-row">
                            <div class="detail-label">Student Name</div>
                            <div class="detail-value">{{ $application->first_name }} {{ $application->last_name }}
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Student ID</div>
                            <div class="detail-value">{{ $application->student_id }}</div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Scholarship Type</div>
                            <div class="detail-value">
                                @if ($application->scholarship_type == 'ched')
                                    CHED Scholarship
                                @elseif($application->scholarship_type == 'presidents')
                                    President's Scholarship
                                @elseif($application->scholarship_type == 'employees')
                                    Employees Scholar
                                @elseif($application->scholarship_type == 'private')
                                    Private Scholarship
                                @else
                                    {{ ucfirst($application->scholarship_type) }} Scholarship
                                @endif
                            </div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Date Applied</div>
                            <div class="detail-value">{{ $application->created_at->format('F d, Y') }}</div>
                        </div>

                        <div class="detail-row">
                            <div class="detail-label">Current Status</div>
                            <div class="detail-value">
                                <span
                                    class="status-badge
                                    @if ($application->status == 'Pending Review') pending
                                    @elseif($application->status == 'Under Committee Review') review
                                    @elseif($application->status == 'Approved') approved
                                    @elseif($application->status == 'Rejected') rejected @endif">
                                    {{ $application->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="status-timeline">
                        <h3 class="timeline-title">Application Timeline</h3>

                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot active"></div>
                                <div class="timeline-content">
                                    <div class="timeline-status">Application Submitted</div>
                                    <div class="timeline-date">{{ $application->created_at->format('F d, Y') }}</div>
                                    <div class="timeline-description">Your application has been successfully submitted
                                        and is awaiting review.</div>
                                </div>
                            </div>

                            <div class="timeline-item">
                                <div
                                    class="timeline-dot {{ in_array($application->status, ['Under Committee Review', 'Approved', 'Rejected']) ? 'active' : '' }}">
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-status">Under Committee Review</div>
                                    <div class="timeline-date">
                                        {{ $application->status == 'Pending Review' ? 'Pending' : $application->updated_at->format('F d, Y') }}
                                    </div>
                                    <div class="timeline-description">Your application is being reviewed by the
                                        scholarship committee.</div>
                                </div>
                            </div>



                            <div class="timeline-item">
                                <div
                                    class="timeline-dot {{ in_array($application->status, ['Approved', 'Rejected']) ? 'active' : '' }}">
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-status">Final Status: {{ $application->status }}</div>
                                    <div class="timeline-date">
                                        {{ in_array($application->status, ['Pending Review', 'Under Committee Review']) ? 'Pending' : $application->updated_at->format('F d, Y') }}
                                    </div>
                                    <div class="timeline-description">
                                        @if ($application->status == 'Approved')
                                            Congratulations! Your scholarship application has been approved. Please
                                            check your email for further instructions.
                                        @elseif($application->status == 'Rejected')
                                            We regret to inform you that your scholarship application has been rejected.
                                            Please contact the Office of the Registrar for more information.
                                        @else
                                            The final decision on your application is pending.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <a href="{{ route('student.dashboard') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            @elseif(request('id'))
                <div class="result-container">
                    <div class="no-result">
                        <i class="fas fa-search"></i>
                        <h3 class="no-result-title">No Application Found</h3>
                        <p>We couldn't find an application with the ID "{{ request('id') }}". Please check the ID and
                            try again.</p>

                        <a href="{{ route('student.dashboard') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function trackApplication(applicationId) {
            // Set the application ID in the input field
            document.getElementById('applicationIdInput').value = applicationId;

            // Submit the form
            document.getElementById('trackForm').submit();
        }
    </script>
</body>

</html>
