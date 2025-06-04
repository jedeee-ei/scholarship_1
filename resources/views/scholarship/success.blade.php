<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted - St. Paul University Philippines</title>
    <link rel="stylesheet" href="{{ asset('css/scholarship.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background-color: #1e5631;
            color: white;
        }

        .track-btn:hover {
            background-color: #164023;
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
</head>
<body>
    <!-- University Header -->
    <header class="university-header">
        <div class="header-content">
            <div class="university-logo-title">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo" class="university-logo">
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
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="success-title">Application Submitted Successfully!</h1>
            <p class="success-message">
                Your scholarship application has been received and is now being processed.
                You will receive updates on your application status via email and SMS.
            </p>

            <div class="application-details">
                <div class="detail-row">
                    <div class="detail-label">Application ID:</div>
                    <div class="detail-value">{{ session('application_id', 'SCH-'.rand(10000, 99999)) }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Scholarship Type:</div>
                    <div class="detail-value">
                        @if(session('scholarship_type') == 'ched')
                            CHED Scholarship
                        @elseif(session('scholarship_type') == 'presidents')
                            Institutional Scholarship
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
                                Your application will be reviewed by the Scholarship Committee to ensure all requirements are met.
                                This typically takes 3-5 business days.
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <div class="step-title">Committee Evaluation</div>
                            <div class="step-description">
                                If your application passes the initial review, it will be forwarded to the Scholarship Committee for evaluation.
                                This process may take 1-2 weeks.
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <div class="step-title">Decision Notification</div>
                            <div class="step-description">
                                You will be notified of the committee's decision via email and SMS. If approved, you will receive further instructions
                                on the next steps to complete your scholarship process.
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="{{ route('scholarship.application') }}" class="action-btn back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="{{ route('scholarship.tracker', ['id' => session('application_id', 'SCH-'.rand(10000, 99999))]) }}" class="action-btn track-btn">
                    <i class="fas fa-search"></i> Track Application
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Success page loaded');

            // Store application ID in local storage for easy tracking
            localStorage.setItem('lastApplicationId', '{{ session('application_id', 'SCH-'.rand(10000, 99999)) }}');
        });
    </script>
</body>
</html>

