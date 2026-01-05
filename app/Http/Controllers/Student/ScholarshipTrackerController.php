<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScholarshipTrackerController extends Controller
{
    public function showTracker(Request $request)
    {
        $applicationId = $request->query('id');
        $application = null;
        $student = Auth::user();

        // Get all applications for the logged-in user from both tables
        $userApplications = collect();
        $granteeRecords = collect();

        if ($student && $student->student_id) {
            // Get from scholarship_applications table
            $userApplications = ScholarshipApplication::where('student_id', $student->student_id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Also get from grantees table (approved scholarships)
            $granteeRecords = Grantee::where('student_id', $student->student_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($applicationId) {
            // Find application by ID - check both tables
            $application = ScholarshipApplication::where('application_id', $applicationId)->first();
            if (!$application) {
                $application = Grantee::where('application_id', $applicationId)->first();
            }
        }

        // Check for permanent scholarship status for notifications
        $permanentStatus = null;
        if ($student && $student->student_id) {
            // First check applications table
            $permanentStatus = ScholarshipApplication::where('student_id', $student->student_id)
                ->whereIn('status', ['Approved', 'Rejected'])
                ->orderBy('updated_at', 'desc')
                ->first();

            // If no status found in applications, check grantees table
            if (!$permanentStatus && $granteeRecords->isNotEmpty()) {
                $latestGrantee = $granteeRecords->first();
                // Convert grantee to application-like object for consistency
                $permanentStatus = (object) [
                    'application_id' => $latestGrantee->application_id ?? $latestGrantee->grantee_id,
                    'student_id' => $latestGrantee->student_id,
                    'scholarship_type' => $latestGrantee->scholarship_type,
                    'scholarship_subtype' => $latestGrantee->scholarship_subtype ?? null,
                    'status' => 'Approved', // Grantees are approved by definition
                    'first_name' => $latestGrantee->first_name,
                    'last_name' => $latestGrantee->last_name,
                    'created_at' => $latestGrantee->created_at,
                    'updated_at' => $latestGrantee->updated_at,
                ];
            }
        }

        // Combine applications and grantee records for display
        $allRecords = collect();

        // Add scholarship applications
        foreach ($userApplications as $app) {
            $allRecords->push($app);
        }

        // Add grantee records as application-like objects
        foreach ($granteeRecords as $grantee) {
            $allRecords->push((object) [
                'application_id' => $grantee->application_id ?? $grantee->grantee_id,
                'student_id' => $grantee->student_id,
                'scholarship_type' => $grantee->scholarship_type,
                'scholarship_subtype' => $grantee->scholarship_subtype ?? null,
                'status' => 'Approved', // Grantees are approved
                'first_name' => $grantee->first_name,
                'last_name' => $grantee->last_name,
                'created_at' => $grantee->created_at,
                'updated_at' => $grantee->updated_at,
            ]);
        }

        // Sort by most recent
        $userApplications = $allRecords->sortByDesc('updated_at');

        return view('scholarship.tracker', [
            'application' => $application,
            'applicationId' => $applicationId,
            'student' => $student,
            'userApplications' => $userApplications,
            'permanentStatus' => $permanentStatus
        ]);
    }



    public function trackApplication(Request $request)
    {
        $applicationId = $request->input('application_id');
        $student = Auth::user();

        if (!$student) {
            return response()->json(['found' => false, 'error' => 'Not authenticated']);
        }

        // Find application by ID - allow tracking any application for now
        $application = ScholarshipApplication::where('application_id', $applicationId)->first();

        if (!$application) {
            return response()->json(['found' => false]);
        }

        // Determine current step based on status
        $currentStep = 0;
        $progress = 0;

        switch ($application->status) {
            case 'Pending Review':
                $currentStep = 1; // Initial Review
                $progress = 20;
                $statusTitle = 'Initial Review';
                $statusMessage = 'Your application is currently undergoing initial review by our staff.';
                $statusNote = 'This process typically takes 1-2 weeks.';
                break;

            case 'Under Committee Review':
                $currentStep = 2; // Committee Review
                $progress = 60;
                $statusTitle = 'Under Committee Review';
                $statusMessage = 'Your application is currently being reviewed by the Scholarship Committee.';
                $statusNote = 'This process typically takes 2-3 weeks. You may be contacted for additional information if needed.';
                break;

            case 'Approved':
                $currentStep = 3; // Notification
                $progress = 100;
                $statusTitle = 'Application Approved';
                $statusMessage = 'Congratulations! Your scholarship application has been approved.';
                $statusNote = 'Please check your email for further instructions.';
                break;

            case 'Rejected':
                $currentStep = 3; // Notification
                $progress = 100;
                $statusTitle = 'Application Not Approved';
                $statusMessage = 'We regret to inform you that your scholarship application was not approved at this time.';
                $statusNote = 'Please check your email for more details and future opportunities.';
                break;

            default:
                $currentStep = 0; // Submitted
                $progress = 0;
                $statusTitle = 'Application Submitted';
                $statusMessage = 'Your application has been received and is awaiting review.';
                $statusNote = 'The review process will begin shortly.';
        }

        // Format dates
        $submissionDate = Carbon::parse($application->created_at)->format('F d, Y');
        $updatedDate = Carbon::parse($application->updated_at)->format('F d, Y');
        $expectedCompletion = Carbon::parse($application->created_at)->addDays(30)->format('F d, Y');

        // Get scholarship type display name
        $scholarshipTypes = [
            'government' => 'Government Scholarship',
            'academic' => 'Academic Scholarship',
            'employees' => 'Employees Scholar',
            'alumni' => 'Alumni Scholarship'
        ];

        $scholarshipType = $scholarshipTypes[$application->scholarship_type] ?? ucfirst($application->scholarship_type);

        // Prepare response data
        $data = [
            'found' => true,
            'application' => [
                'id' => $application->application_id,
                'type' => $scholarshipType,
                'name' => $application->first_name . ' ' . $application->last_name,
                'studentId' => $application->student_id,
                'submissionDate' => $submissionDate,
                'expectedCompletion' => $expectedCompletion,
                'progress' => $progress,
                'currentStep' => $currentStep,
                'currentStatus' => [
                    'title' => $statusTitle,
                    'date' => $updatedDate,
                    'message' => $statusMessage,
                    'note' => $statusNote
                ]
            ]
        ];

        return response()->json($data);
    }
}
