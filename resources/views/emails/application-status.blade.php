<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application Status Update</title>
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

        .status-approved {
            color: #27ae60;
            background-color: #d5f4e6;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border-left: 4px solid #27ae60;
        }

        .status-rejected {
            color: #e74c3c;
            background-color: #fdf2f2;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border-left: 4px solid #e74c3c;
        }

        .status-pending {
            color: #f39c12;
            background-color: #fef9e7;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border-left: 4px solid #f39c12;
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

        .remarks {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .next-steps {
            background-color: #e8f4fd;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
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
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">Scholarship Management System</div>
            <p>Application Status Update</p>
        </div>

        <h2>Dear {{ $studentName }},</h2>

        <p>We hope this email finds you well. We are writing to inform you about an update regarding your scholarship
            application.</p>

        <!-- Status Alert -->
        @if ($status === 'Approved')
            <div class="status-approved">
                <h3>🎉 Congratulations! Your Application Has Been Approved!</h3>
                <p>We are pleased to inform you that your scholarship application has been approved.</p>
            </div>
        @elseif($status === 'Rejected')
            <div class="status-rejected">
                <h3>Application Status Update</h3>
                <p>After careful review, we regret to inform you that your scholarship application was not approved at
                    this time.</p>
            </div>
        @else
            <div class="status-pending">
                <h3>Application Under Review</h3>
                <p>Your scholarship application is currently being reviewed by our committee.</p>
            </div>
        @endif

        <!-- Application Details -->
        <div class="application-details">
            <h4>Application Details:</h4>
            <div class="detail-row">
                <span class="detail-label">Application ID:</span>
                <span class="detail-value">{{ $application->application_id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Student ID:</span>
                <span class="detail-value">{{ $application->student_id }}</span>
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
                <span class="detail-label">Status:</span>
                <span class="detail-value">{{ $status }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date Updated:</span>
                <span class="detail-value">{{ now()->format('F d, Y') }}</span>
            </div>
        </div>

        <!-- Remarks (if any) -->
        @if ($remarks)
            <div class="remarks">
                <h4>Additional Information:</h4>
                <p>{{ $remarks }}</p>
            </div>
        @endif

        <!-- Next Steps -->
        <div class="next-steps">
            <h4>Next Steps:</h4>
            @if ($status === 'Approved')
                <p>• You will be contacted soon with further instructions regarding your scholarship.</p>
                <p>• Please keep your contact information updated in our system.</p>
                <p>• Continue to maintain your academic performance as required by the scholarship terms.</p>
                <p>• Watch for additional communications regarding scholarship disbursement and requirements.</p>
            @elseif($status === 'Rejected')
                <p>• You may reapply for scholarships in future application periods.</p>
                <p>• Consider reviewing the scholarship requirements and improving your application for next time.</p>
                <p>• Contact our office if you have questions about the decision or future opportunities.</p>
            @else
                <p>• Please wait for further updates regarding your application status.</p>
                <p>• Ensure your contact information is up to date.</p>
                <p>• You will be notified once a decision has been made.</p>
            @endif
        </div>

        @if ($status === 'Approved')
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('scholarship.tracker') }}" class="button">View Application Status</a>
            </div>
        @endif

        <p>If you have any questions or concerns, please don't hesitate to contact our scholarship office.</p>

        <p>Best regards,<br>
            <strong>Scholarship Management Team</strong><br>
            Scholarship Office
        </p>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Scholarship Management System. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
