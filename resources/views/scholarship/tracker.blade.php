@extends('layouts.student')

@section('title', 'Application Tracker')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/scholarship.css') }}">
@endpush

@section('content')
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
            margin-bottom: 30px;
        }

        .tracker-title {
            font-size: 24px;
            color: #1e5631;
            margin-bottom: 10px;
            text-align: center;
        }

        .back-to-dashboard-btn {
            background: linear-gradient(135deg, #052F11 0%, #0a5a1f 100%);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 200px;
            justify-content: center;
        }

        .back-to-dashboard-btn:hover {
            background: linear-gradient(135deg, #0a5a1f 0%, #052F11 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 47, 17, 0.3);
            color: white;
            text-decoration: none;
        }

        .bottom-navigation {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }

        .tracker-description {
            color: #666;
            font-size: 16px;
            text-align: center;
            margin: 0;
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
            padding: 25px;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .my-applications-title {
            font-size: 20px;
            color: #1e5631;
            margin-bottom: 20px;
            font-weight: 600;
        }

        /* Minimalistic Application Cards */
        .application-card-minimal {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .application-card-minimal:hover {
            border-color: #1e5631;
            box-shadow: 0 4px 12px rgba(30, 86, 49, 0.1);
            transform: translateY(-1px);
        }

        .application-card-minimal.approved {
            border-color: #28a745;
            background: linear-gradient(135deg, #ffffff 0%, #f8fff8 100%);
        }

        .application-card-minimal.rejected {
            border-color: #dc3545;
            background: linear-gradient(135deg, #ffffff 0%, #fff8f8 100%);
        }

        .application-card-minimal.pending {
            border-color: #ffc107;
            background: linear-gradient(135deg, #ffffff 0%, #fffef8 100%);
        }

        .card-header-minimal {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .app-id-minimal {
            font-size: 16px;
            font-weight: 700;
            color: #1e5631;
        }

        .status-badge-minimal {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge-minimal.approved {
            background: #28a745;
            color: white;
        }

        .status-badge-minimal.rejected {
            background: #dc3545;
            color: white;
        }

        .status-badge-minimal.pending {
            background: #ffc107;
            color: #212529;
        }

        .card-content-minimal {
            color: #6c757d;
        }

        .scholarship-type-minimal {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .application-date-minimal {
            font-size: 12px;
            color: #adb5bd;
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

        /* Minimalistic Status Notifications for Tracker */
        .tracker-status-notification {
            background: #ffffff;
            border: 1px solid #e8f5e8;
            border-left: 4px solid #28a745;
            border-radius: 8px;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }

        .tracker-status-notification.rejected {
            border-left-color: #dc3545;
            border-color: #fdf2f2;
        }

        .tracker-status-notification .notification-content {
            display: flex;
            align-items: center;
            padding: 20px 24px;
            gap: 16px;
        }

        .tracker-status-notification .notification-icon {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #28a745;
        }

        .tracker-status-notification.rejected .notification-icon {
            border-color: #dc3545;
        }

        .tracker-status-notification .notification-icon i {
            color: #28a745;
            font-size: 1.2rem;
        }

        .tracker-status-notification.rejected .notification-icon i {
            color: #dc3545;
        }

        .tracker-status-notification .notification-text {
            flex: 1;
            color: #2c3e50;
        }

        .tracker-status-notification .notification-text .status-title {
            color: #28a745;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 4px;
            display: block;
        }

        .tracker-status-notification.rejected .notification-text .status-title {
            color: #dc3545;
        }

        .tracker-status-notification .notification-text .status-details {
            font-size: 0.9rem;
            margin-bottom: 8px;
            line-height: 1.4;
            color: #6c757d;
        }

        .tracker-status-notification .notification-text .status-info {
            font-size: 0.8rem;
            color: #adb5bd;
        }

        .tracker-status-notification .notification-badge {
            flex-shrink: 0;
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tracker-status-notification.rejected .notification-badge {
            background: #dc3545;
        }

        /* Enhanced Application Details for Final Status */
        .final-status-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #28a745;
        }

        .final-status-details.rejected {
            border-left-color: #dc3545;
        }

        .final-status-details .status-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .final-status-details .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #28a745;
            color: white;
        }

        .final-status-details.rejected .status-icon {
            background: #dc3545;
        }

        .final-status-details .status-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #28a745;
            margin: 0;
        }

        .final-status-details.rejected .status-title {
            color: #dc3545;
        }

        .final-status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .final-status-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .final-status-item .label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .final-status-item .value {
            font-size: 0.95rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .final-status-item.highlight .value {
            color: #28a745;
            font-weight: 700;
        }

        .final-status-item.highlight.rejected .value {
            color: #dc3545;
        }

        /* Scholarship Status Card Styles */
        .scholarship-status-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 20px;
        }

        .status-card-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .status-icon-large {
            width: 64px;
            height: 64px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .status-icon-large.rejected {
            background: #dc3545;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .status-info {
            flex: 1;
        }

        .status-title-large {
            font-size: 1.5rem;
            font-weight: 700;
            color: #28a745;
            margin: 0 0 8px 0;
        }

        .status-title-large.rejected {
            color: #dc3545;
        }

        .status-subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin: 0;
        }

        .scholarship-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            padding: 24px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #e9ecef;
        }

        .detail-item.highlight {
            background: #e8f5e8;
            border-left-color: #28a745;
        }

        .detail-item.highlight.rejected {
            background: #fdf2f2;
            border-left-color: #dc3545;
        }

        .detail-item .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-item .detail-value {
            font-size: 1rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .detail-item.highlight .detail-value {
            color: #28a745;
            font-weight: 700;
        }

        .detail-item.highlight.rejected .detail-value {
            color: #dc3545;
        }

        .scholar-benefits {
            background: linear-gradient(135deg, #e8f5e8, #d4edda);
            padding: 24px;
            margin-top: 20px;
        }

        .scholar-benefits h5 {
            color: #155724;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .scholar-benefits ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .scholar-benefits li {
            padding: 8px 0;
            color: #155724;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        /* Responsive Design for Tracker Notifications */
        @media (max-width: 768px) {
            .tracker-status-notification {
                margin: 15px 10px;
                max-width: calc(100% - 20px);
            }

            .tracker-status-notification .notification-content {
                padding: 16px 20px;
                gap: 12px;
            }

            .final-status-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .scholarship-details-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                padding: 20px;
            }

            .status-card-header {
                padding: 20px;
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }

            .status-icon-large {
                width: 56px;
                height: 56px;
                font-size: 1.6rem;
            }
        }

        @media (max-width: 480px) {
            .tracker-status-notification {
                margin: 10px 5px;
            }

            .tracker-status-notification .notification-content {
                padding: 12px 16px;
                gap: 10px;
                flex-direction: column;
                text-align: center;
            }

            .final-status-details {
                padding: 16px;
            }

            .scholarship-details-grid {
                padding: 16px;
                gap: 12px;
            }

            .detail-item {
                padding: 12px;
            }

            .scholar-benefits {
                padding: 20px;
            }
        }
    </style>

    <div class="page-container">
        <div class="application-container">
            <div class="tracker-container">
                <div class="tracker-header">
                    <h1 class="tracker-title">Application Status Tracker</h1>
                    <p class="tracker-description">Track your scholarship applications by clicking on them below.</p>
                </div>



                <!-- Minimalistic Status Notifications for Tracker -->
                @if ($permanentStatus)
                    <div class="tracker-status-notification {{ $permanentStatus->status === 'Rejected' ? 'rejected' : '' }}">
                        <div class="notification-content">
                            <div class="notification-icon">
                                @if ($permanentStatus->status === 'Approved')
                                    <i class="fas fa-check"></i>
                                @else
                                    <i class="fas fa-times"></i>
                                @endif
                            </div>
                            <div class="notification-text">
                                <span class="status-title">
                                    @if ($permanentStatus->status === 'Approved')
                                        Scholarship Approved
                                    @else
                                        Application Rejected
                                    @endif
                                </span>
                                <div class="status-details">
                                    @if ($permanentStatus->status === 'Approved')
                                        Your {{ ucfirst($permanentStatus->scholarship_type) }} scholarship application has been approved.
                                        @if ($permanentStatus->scholarship_subtype)
                                            Awarded: {{ $permanentStatus->scholarship_subtype }} Scholarship
                                        @endif
                                    @else
                                        Your {{ ucfirst($permanentStatus->scholarship_type) }} scholarship application was not approved.
                                    @endif
                                </div>
                                <div class="status-info">
                                    {{ $permanentStatus->application_id }} • {{ $permanentStatus->updated_at->format('M d, Y') }}
                                    @if ($permanentStatus->status === 'Approved')
                                        • Active Scholar
                                    @endif
                                </div>
                            </div>
                            <div class="notification-badge">
                                @if ($permanentStatus->status === 'Approved')
                                    APPROVED
                                @else
                                    REJECTED
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if ($userApplications && $userApplications->count() > 0)
                    <div class="my-applications">
                        <h3 class="my-applications-title">My Applications</h3>
                        <div class="applications-list">
                            @foreach ($userApplications as $app)
                                <div class="application-item" onclick="trackApplication('{{ $app->application_id }}')">
                                    <div class="application-info">
                                        <div class="application-id-text">{{ $app->application_id }}</div>
                                        <div class="application-meta">
                                            @if ($app->scholarship_type == 'government')
                                                Government Scholarship
                                            @elseif($app->scholarship_type == 'academic')
                                                Academic Scholarship
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
                                            @if ($app->status === 'Approved')
                                                ✓ APPROVED
                                            @elseif ($app->status === 'Rejected')
                                                ✗ REJECTED
                                            @else
                                                {{ $app->status }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="my-applications">
                        <h3 class="my-applications-title">My Applications</h3>
                        @if ($permanentStatus)
                            <!-- Show approved/rejected scholarship details -->
                            <div class="scholarship-status-card">
                                <div class="status-card-header">
                                    <div class="status-icon-large {{ $permanentStatus->status === 'Rejected' ? 'rejected' : '' }}">
                                        @if ($permanentStatus->status === 'Approved')
                                            <i class="fas fa-trophy"></i>
                                        @else
                                            <i class="fas fa-info-circle"></i>
                                        @endif
                                    </div>
                                    <div class="status-info">
                                        <h4 class="status-title-large {{ $permanentStatus->status === 'Rejected' ? 'rejected' : '' }}">
                                            @if ($permanentStatus->status === 'Approved')
                                                🎓 Active Scholarship
                                            @else
                                                📋 Application Record
                                            @endif
                                        </h4>
                                        <p class="status-subtitle">
                                            @if ($permanentStatus->status === 'Approved')
                                                You are currently an active scholar
                                            @else
                                                Your application record
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="scholarship-details-grid">
                                    <div class="detail-item">
                                        <div class="detail-label">Application ID</div>
                                        <div class="detail-value">{{ $permanentStatus->application_id }}</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Student ID</div>
                                        <div class="detail-value">{{ $permanentStatus->student_id }}</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Scholarship Type</div>
                                        <div class="detail-value">
                                            @if ($permanentStatus->scholarship_type == 'government')
                                                Government Scholarship
                                            @elseif($permanentStatus->scholarship_type == 'academic')
                                                Academic Scholarship
                                            @elseif($permanentStatus->scholarship_type == 'employees')
                                                Employees Scholar
                                            @elseif($permanentStatus->scholarship_type == 'private')
                                                Private Scholarship
                                            @else
                                                {{ ucfirst($permanentStatus->scholarship_type) }} Scholarship
                                            @endif
                                        </div>
                                    </div>
                                    @if ($permanentStatus->scholarship_subtype)
                                        <div class="detail-item highlight {{ $permanentStatus->status === 'Rejected' ? 'rejected' : '' }}">
                                            <div class="detail-label">Award Type</div>
                                            <div class="detail-value">{{ $permanentStatus->scholarship_subtype }} Scholarship</div>
                                        </div>
                                    @endif
                                    @if ($permanentStatus->department)
                                        <div class="detail-item">
                                            <div class="detail-label">Department</div>
                                            <div class="detail-value">{{ $permanentStatus->department }}</div>
                                        </div>
                                    @endif
                                    @if ($permanentStatus->course)
                                        <div class="detail-item">
                                            <div class="detail-label">Course</div>
                                            <div class="detail-value">{{ $permanentStatus->course }}</div>
                                        </div>
                                    @endif
                                    @if ($permanentStatus->year_level)
                                        <div class="detail-item">
                                            <div class="detail-label">Year Level</div>
                                            <div class="detail-value">{{ $permanentStatus->year_level }}</div>
                                        </div>
                                    @endif
                                    @if ($permanentStatus->gwa)
                                        <div class="detail-item highlight {{ $permanentStatus->status === 'Rejected' ? 'rejected' : '' }}">
                                            <div class="detail-label">GWA</div>
                                            <div class="detail-value">{{ $permanentStatus->gwa }}</div>
                                        </div>
                                    @endif
                                    <div class="detail-item">
                                        <div class="detail-label">Application Date</div>
                                        <div class="detail-value">{{ $permanentStatus->created_at->format('F d, Y') }}</div>
                                    </div>
                                    <div class="detail-item highlight {{ $permanentStatus->status === 'Rejected' ? 'rejected' : '' }}">
                                        <div class="detail-label">
                                            @if ($permanentStatus->status === 'Approved')
                                                Approval Date
                                            @else
                                                Decision Date
                                            @endif
                                        </div>
                                        <div class="detail-value">{{ $permanentStatus->updated_at->format('F d, Y') }}</div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Current Status</div>
                                        <div class="detail-value">
                                            <span class="status-badge {{ $permanentStatus->status === 'Approved' ? 'approved' : 'rejected' }}">
                                                @if ($permanentStatus->status === 'Approved')
                                                    Active Scholar
                                                @else
                                                    {{ $permanentStatus->status }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                @if ($permanentStatus->status === 'Approved')
                                    <div class="scholar-benefits">
                                        <h5>📚 Scholar Benefits & Responsibilities</h5>
                                        <ul>
                                            <li>✅ Tuition fee assistance or full scholarship coverage</li>
                                            <li>✅ Priority enrollment and academic support</li>
                                            <li>✅ Access to scholar-exclusive programs and activities</li>
                                            <li>📋 Maintain required GWA for scholarship renewal</li>
                                            <li>📋 Participate in community service activities</li>
                                            <li>📋 Submit periodic academic progress reports</li>
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="no-applications">
                                You haven't submitted any scholarship applications yet.
                            </div>
                        @endif
                    </div>
                @endif



                @php
                    // Use the application passed from the controller
                    // $application is already set by the controller
                @endphp

                @if (request('id') && $application)
                    <div class="result-container">

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
                                    @if ($application->scholarship_type == 'government')
                                        Government Scholarship
                                    @elseif($application->scholarship_type == 'academic')
                                        Academic Scholarship
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

                            @if ($application->status === 'Approved')
                                <div class="detail-row">
                                    <div class="detail-label">Decision Date</div>
                                    <div class="detail-value">{{ $application->updated_at->format('F d, Y') }}</div>
                                </div>
                                @if ($application->scholarship_subtype)
                                    <div class="detail-row">
                                        <div class="detail-label">Scholarship Award</div>
                                        <div class="detail-value">{{ $application->scholarship_subtype }} Scholarship</div>
                                    </div>
                                @endif
                                @if ($application->gwa)
                                    <div class="detail-row">
                                        <div class="detail-label">GWA</div>
                                        <div class="detail-value">{{ $application->gwa }}</div>
                                    </div>
                                @endif
                            @elseif ($application->status === 'Rejected')
                                <div class="detail-row">
                                    <div class="detail-label">Decision Date</div>
                                    <div class="detail-value">{{ $application->updated_at->format('F d, Y') }}</div>
                                </div>
                            @endif
                        </div>

                        @if (in_array($application->status, ['Approved', 'Rejected']))
                            <div class="final-status-details {{ $application->status === 'Rejected' ? 'rejected' : '' }}">
                                <div class="status-header">
                                    <div class="status-icon">
                                        @if ($application->status === 'Approved')
                                            <i class="fas fa-trophy"></i>
                                        @else
                                            <i class="fas fa-info-circle"></i>
                                        @endif
                                    </div>
                                    <h4 class="status-title">
                                        @if ($application->status === 'Approved')
                                            Scholarship Details
                                        @else
                                            Application Information
                                        @endif
                                    </h4>
                                </div>

                                <div class="final-status-grid">
                                    <div class="final-status-item">
                                        <div class="label">Application ID</div>
                                        <div class="value">{{ $application->application_id }}</div>
                                    </div>
                                    <div class="final-status-item">
                                        <div class="label">Student ID</div>
                                        <div class="value">{{ $application->student_id }}</div>
                                    </div>
                                    <div class="final-status-item">
                                        <div class="label">Scholarship Type</div>
                                        <div class="value">{{ ucfirst($application->scholarship_type) }}</div>
                                    </div>
                                    @if ($application->scholarship_subtype)
                                        <div class="final-status-item highlight {{ $application->status === 'Rejected' ? 'rejected' : '' }}">
                                            <div class="label">Award Type</div>
                                            <div class="value">{{ $application->scholarship_subtype }} Scholarship</div>
                                        </div>
                                    @endif
                                    @if ($application->department)
                                        <div class="final-status-item">
                                            <div class="label">Department</div>
                                            <div class="value">{{ $application->department }}</div>
                                        </div>
                                    @endif
                                    @if ($application->course)
                                        <div class="final-status-item">
                                            <div class="label">Course</div>
                                            <div class="value">{{ $application->course }}</div>
                                        </div>
                                    @endif
                                    @if ($application->year_level)
                                        <div class="final-status-item">
                                            <div class="label">Year Level</div>
                                            <div class="value">{{ $application->year_level }}</div>
                                        </div>
                                    @endif
                                    @if ($application->gwa)
                                        <div class="final-status-item highlight {{ $application->status === 'Rejected' ? 'rejected' : '' }}">
                                            <div class="label">GWA</div>
                                            <div class="value">{{ $application->gwa }}</div>
                                        </div>
                                    @endif
                                    <div class="final-status-item">
                                        <div class="label">Application Date</div>
                                        <div class="value">{{ $application->created_at->format('F d, Y') }}</div>
                                    </div>
                                    <div class="final-status-item highlight {{ $application->status === 'Rejected' ? 'rejected' : '' }}">
                                        <div class="label">Decision Date</div>
                                        <div class="value">{{ $application->updated_at->format('F d, Y') }}</div>
                                    </div>
                                    @if ($application->status === 'Approved')
                                        <div class="final-status-item highlight">
                                            <div class="label">Scholar Status</div>
                                            <div class="value">Active Scholar</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="status-timeline">
                            <h3 class="timeline-title">Application Timeline</h3>

                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-dot active"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-status">Application Submitted</div>
                                        <div class="timeline-date">{{ $application->created_at->format('F d, Y') }}
                                        </div>
                                        <div class="timeline-description">Your application has been successfully
                                            submitted
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
                                                We regret to inform you that your scholarship application has been
                                                rejected.
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
                            <p>We couldn't find an application with the ID "{{ request('id') }}". Please check the ID
                                and
                                try again.</p>

                            <a href="{{ route('student.dashboard') }}" class="back-button">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Bottom Navigation -->
                <div class="bottom-navigation" style="display: flex; justify-content: center; margin-top: 30px;">
                    <a href="{{ route('student.dashboard') }}" class="back-to-dashboard-btn">
                        <i class="fas fa-arrow-left"></i> Back to Dashboards
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function trackApplication(applicationId) {
            // Redirect directly to the tracker with the application ID
            window.location.href = "{{ route('scholarship.tracker') }}?id=" + applicationId;
        }
    </script>
@endpush
