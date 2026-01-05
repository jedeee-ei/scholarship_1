<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application Submitted</title>
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

        .success-message {
            background-color: #d5f4e6;
            color: #27ae60;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border-left: 4px solid #27ae60;
        }

        .application-details {
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

        .next-steps {
            background-color: #e8f4fd;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
        }

        .important-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
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
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }

        .button:hover {
            background-color: #2980b9;
        }

        .tracking-info {
            background-color: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #b3d9ff;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">Scholarship Management System</div>
            <p>Application Confirmation</p>
        </div>

        <h2>Dear {{ $studentName }},</h2>

        <div class="success-message">
            <h3>✅ Application Successfully Submitted!</h3>
            <p>Thank you for submitting your scholarship application. We have received your application and it is now
                being processed.</p>
        </div>

        <p>This email serves as confirmation that your scholarship application has been successfully submitted to our
            system.</p>

        <!-- Application Details -->
        <div class="application-details">
            <h4>Application Summary:</h4>
            <div class="detail-row">
                <span class="detail-label">Application ID:</span>
                <span class="detail-value">{{ $applicationId }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Student ID:</span>
                <span class="detail-value">{{ $application->student_id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Full Name:</span>
                <span class="detail-value">{{ $studentName }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $application->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Scholarship Type:</span>
                <span class="detail-value">{{ $scholarshipType }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Course:</span>
                <span class="detail-value">{{ $application->course }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Year Level:</span>
                <span class="detail-value">{{ $application->year_level }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date Submitted:</span>
                <span class="detail-value">{{ $application->created_at->format('F d, Y \a\t g:i A') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">Pending Review</span>
            </div>
        </div>

        <!-- Tracking Information -->
        <div class="tracking-info">
            <h4>📋 Track Your Application:</h4>
            <p>You can track the status of your application using your Application ID:
                <strong>{{ $applicationId }}</strong></p>
            <div style="text-align: center; margin: 15px 0;">
                <a href="{{ route('scholarship.tracker') }}" class="button">Track Application Status</a>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="next-steps">
            <h4>What Happens Next:</h4>
            <p><strong>1. Review Process:</strong> Our scholarship committee will review your application and supporting
                documents.</p>
            <p><strong>2. Evaluation:</strong> Applications are evaluated based on academic merit, financial need, and
                scholarship criteria.</p>
            <p><strong>3. Decision:</strong> You will be notified via email once a decision has been made regarding your
                application.</p>
            <p><strong>4. Timeline:</strong> The review process typically takes 2-4 weeks from the submission date.</p>
        </div>

        <!-- Important Notes -->
        <div class="important-note">
            <h4>⚠️ Important Reminders:</h4>
            <p>• Keep your Application ID (<strong>{{ $applicationId }}</strong>) for your records</p>
            <p>• Ensure all your contact information is accurate and up-to-date</p>
            <p>• Check your email regularly for updates on your application status</p>
            <p>• Do not submit duplicate applications for the same scholarship type</p>
            <p>• Contact our office if you need to update any information in your application</p>
        </div>

        <p>We appreciate your interest in our scholarship programs and thank you for taking the time to apply. Our team
            will carefully review your application, and we will notify you of our decision as soon as possible.</p>

        <p>If you have any questions about your application or the review process, please don't hesitate to contact our
            scholarship office.</p>

        <p>Best regards,<br>
            <strong>Scholarship Management Team</strong><br>
            Scholarship Office
        </p>

        <div class="footer">
            <p>This is an automated confirmation message. Please do not reply to this email.</p>
            <p>For questions or concerns, please contact our scholarship office directly.</p>
            <p>© {{ date('Y') }} Scholarship Management System. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
