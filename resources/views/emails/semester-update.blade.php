<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $updateType === 'academic_year' ? 'New Academic Year' : 'New Semester' }} - Scholarship Application
        Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .announcement {
            background-color: #e8f4fd;
            color: #2c3e50;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }

        .period-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: bold;
            color: #555;
        }

        .detail-value {
            color: #333;
        }

        .action-required {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .steps {
            background-color: #d5f4e6;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #27ae60;
        }

        .important-note {
            background-color: #fdf2f2;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            color: #721c24;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #666;
            font-size: 14px;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }

        .button:hover {
            background-color: #219a52;
        }

        .deadline-info {
            background-color: #ffebee;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #e74c3c;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">Scholarship Management System</div>
            <p>{{ $updateType === 'academic_year' ? 'New Academic Year' : 'New Semester' }} Notification</p>
        </div>

        <h2>Dear {{ $studentName }},</h2>

        <div class="announcement">
            <h3>🎓 {{ $updateType === 'academic_year' ? 'New Academic Year Has Begun!' : 'New Semester Has Started!' }}
            </h3>
            <p>We hope you're ready for another exciting period of learning and growth!</p>
        </div>

        @if ($updateType === 'academic_year')
            <p>We are pleased to inform you that a new academic year has officially begun. As we start this fresh
                academic period, we want to ensure that all eligible students have the opportunity to apply for
                scholarship assistance.</p>
        @else
            <p>We are pleased to inform you that a new semester has officially begun. As we start this new semester, we
                want to ensure that all eligible students have the opportunity to apply for scholarship assistance.</p>
        @endif

        <!-- Period Information -->
        <div class="period-info">
            <h4>{{ $updateType === 'academic_year' ? 'New Academic Period:' : 'Current Academic Period:' }}</h4>
            @if ($newAcademicYear)
                <div class="detail-row">
                    <span class="detail-label">Academic Year:</span>
                    <span class="detail-value">{{ $newAcademicYear }}</span>
                </div>
            @endif
            @if ($newSemester)
                <div class="detail-row">
                    <span class="detail-label">Semester:</span>
                    <span class="detail-value">{{ $newSemester }}</span>
                </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Notification Date:</span>
                <span class="detail-value">{{ now()->format('F d, Y') }}</span>
            </div>
        </div>

        <!-- Action Required -->
        <div class="action-required">
            <h4>⚠️ Action Required: New Scholarship Application</h4>
            @if ($updateType === 'academic_year')
                <p>With the start of the new academic year, all previous scholarship applications have been archived. If
                    you wish to continue receiving scholarship assistance, you must submit a new application for this
                    academic year.</p>
            @else
                <p>With the start of the new semester, all previous scholarship applications have been archived. If you
                    wish to continue receiving scholarship assistance, you must submit a new application for this
                    semester.</p>
            @endif
        </div>

        <!-- Application Steps -->
        <div class="steps">
            <h4>📝 How to Apply:</h4>
            <p><strong>Step 1:</strong> Visit the scholarship application portal using the button below</p>
            <p><strong>Step 2:</strong> Log in with your student credentials</p>
            <p><strong>Step 3:</strong> Select the appropriate scholarship type for your situation</p>
            <p><strong>Step 4:</strong> Complete all required fields and upload necessary documents</p>
            <p><strong>Step 5:</strong> Review your application carefully before submitting</p>
            <p><strong>Step 6:</strong> Submit your application and save your Application ID for tracking</p>
        </div>

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ route('student.dashboard') }}" class="button">Apply for Scholarship Now</a>
        </div>

        <!-- Available Scholarships -->
        <div class="period-info">
            <h4>Available Scholarship Types:</h4>
            <p>• <strong>Government Scholarships:</strong> CHED, DOST, DSWD, DOLE funded programs</p>
            <p>• <strong>Academic Scholarships:</strong> Merit-based scholarships for high achievers</p>
            <p>• <strong>Employee Scholarships:</strong> For children of university employees</p>
            <p>• <strong>Alumni Scholarships:</strong> Sponsored by alumni organizations</p>
        </div>

        <!-- Important Deadlines -->
        <div class="deadline-info">
            <h4>⏰ Important Deadlines:</h4>
            <p>• <strong>Early Application:</strong> Submit within the first 2 weeks for priority consideration</p>
            <p>• <strong>Regular Deadline:</strong> Check with the scholarship office for specific deadlines</p>
            <p>• <strong>Late Applications:</strong> May be considered on a case-by-case basis</p>
        </div>

        <!-- Important Notes -->
        <div class="important-note">
            <h4>📌 Important Reminders:</h4>
            <p>• Previous scholarship awards do not automatically renew - you must reapply</p>
            <p>• Ensure all your academic records are up to date</p>
            <p>• Prepare all required documents before starting your application</p>
            <p>• Only one application per scholarship type is allowed</p>
            <p>• Incomplete applications will not be processed</p>
        </div>

        <p>We encourage you to apply as soon as possible to ensure your application receives full consideration. Our
            scholarship programs are designed to support deserving students in achieving their academic goals.</p>

        <p>If you have any questions about the application process, eligibility requirements, or available scholarships,
            please don't hesitate to contact our scholarship office.</p>

        <p>We wish you the best of luck in your studies and look forward to supporting your educational journey!</p>

        <p>Best regards,<br>
            <strong>Scholarship Management Team</strong><br>
            Scholarship Office
        </p>

        <div class="footer">
            <p>This is an automated notification. Please do not reply to this email.</p>
            <p>For questions or assistance, please contact our scholarship office directly.</p>
            <p>© {{ date('Y') }} Scholarship Management System. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
