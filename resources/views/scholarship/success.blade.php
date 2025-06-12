@extends('layouts.student')

@section('title', 'Application Submitted')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/scholarship.css') }}">
@endpush

@section('content')
    <style>
        .success-container {
            text-align: center;
            padding: 3rem;
        }

        .success-icon {
            font-size: 5rem;
            color: #1b5e20;
            margin-bottom: 1.5rem;
        }

        .success-title {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #1b5e20;
        }

        .success-message {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            color: #333;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .application-details {
            background-color: #f5f5f5;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            text-align: left;
        }

        .detail-row {
            display: flex;
            margin-bottom: 15px;
        }

        .detail-label {
            width: 40%;
            font-weight: 600;
            color: #555;
        }

        .detail-value {
            width: 60%;
            color: #333;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .action-buttons {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
        }

        .action-btn {
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .back-btn {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
        }

        .back-btn:hover {
            background-color: #e5e5e5;
        }

        .track-btn {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: white !important;
            font-size: 16px;
            font-weight: 700;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
            transform: translateY(0);
            position: relative;
            overflow: hidden;
            text-decoration: none !important;
        }

        .track-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .track-btn:hover {
            background: linear-gradient(135deg, #388e3c, #2e7d32);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
            text-decoration: none !important;
        }

        .track-btn:hover:before {
            left: 100%;
        }

        .track-btn i {
            font-size: 18px;
            margin-right: 8px;
            color: white !important;
        }

        .track-btn * {
            color: white !important;
        }

        .track-btn:visited {
            color: white !important;
        }

        /* Pulsing animation for track button */
        @keyframes pulse {
            0% {
                box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
            }

            50% {
                box-shadow: 0 4px 25px rgba(46, 125, 50, 0.5);
            }

            100% {
                box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
            }
        }

        .track-btn {
            animation: pulse 2s infinite;
        }

        .track-btn:hover {
            animation: none;
        }

        /* Call to action section */
        .cta-section {
            background: linear-gradient(135deg, #e8f5e8, #f1f8e9);
            border: 2px solid #4caf50;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section:before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(76, 175, 80, 0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .cta-content {
            position: relative;
            z-index: 1;
        }

        .cta-title {
            font-size: 18px;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .cta-description {
            font-size: 14px;
            color: #1b5e20;
            margin-bottom: 0;
        }

        .cta-icon {
            font-size: 20px;
            color: #4caf50;
        }

        .next-steps {
            margin-top: 40px;
            text-align: left;
        }

        .next-steps h3 {
            font-size: 20px;
            color: #1e5631;
            margin-bottom: 15px;
        }

        .steps-list {
            list-style-type: none;
            padding: 0;
        }

        .steps-list li {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .steps-list li:last-child {
            border-bottom: none;
        }

        .step-number {
            width: 30px;
            height: 30px;
            background-color: #1e5631;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
        }

        .step-content {
            flex-grow: 1;
        }

        .step-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .step-description {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }
    </style>


    <div class="page-container">
        <div class="application-container">
            <div class="success-container">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1 class="success-title">Application Submitted Successfully!</h1>
                <p class="success-message">
                    Your scholarship application has been received and is now being processed.
                    You will receive updates on your application status via email
                </p>

                <div class="application-details">
                    <div class="detail-row">
                        <div class="detail-label">Application ID:</div>
                        <div class="detail-value">{{ session('application_id', 'Not Available') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Scholarship Type:</div>
                        <div class="detail-value">
                            @if (session('scholarship_type') == 'government')
                                Government Scholarship
                            @elseif(session('scholarship_type') == 'academic')
                                Academic Scholarship
                            @elseif(session('scholarship_type') == 'employees')
                                Employees Scholar
                            @elseif(session('scholarship_type') == 'private')
                                Private Scholarship
                            @else
                                {{ ucfirst(session('scholarship_type', 'Scholarship')) }}
                            @endif
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Submission Date:</div>
                        <div class="detail-value">{{ date('F d, Y') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Current Status:</div>
                        <div class="detail-value">Pending Review</div>
                    </div>
                </div>

                <div class="next-steps">
                    <h3>What Happens Next?</h3>
                    <ul class="steps-list">
                        <li>
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <div class="step-title">Initial Review</div>
                                <div class="step-description">
                                    Your application will be reviewed by the Scholarship Committee to ensure all
                                    requirements are met.
                                    This typically takes 3-5 business days.
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <div class="step-title">Committee Evaluation</div>
                                <div class="step-description">
                                    If your application passes the initial review, it will be forwarded to the Scholarship
                                    Committee for evaluation.
                                    This process may take 1-2 weeks.
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <div class="step-title">Decision Notification</div>
                                <div class="step-description">
                                    You will be notified of the committee's decision via email and SMS. If approved, you
                                    will receive further instructions
                                    on the next steps to complete your scholarship process.
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Call to Action Section -->
                <div class="cta-section">
                    <div class="cta-content">
                        <div class="cta-title">
                            <i class="fas fa-bell cta-icon"></i>
                            Stay Updated on Your Application!
                        </div>
                        <div class="cta-description">
                            Click the "Track Your Application Status" button below to monitor your application progress in
                            real-time.
                        </div>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('student.dashboard') }}" class="action-btn back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    @if (session('application_id'))
                        <a href="{{ route('scholarship.tracker', ['id' => session('application_id')]) }}"
                            class="action-btn track-btn">
                            <i class="fas fa-search"></i> Track Your Application Status
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Success page loaded');

            // Store application ID in local storage for easy tracking
            @if (session('application_id'))
                localStorage.setItem('lastApplicationId', '{{ session('application_id') }}');
            @endif
        });
    </script>
@endpush
