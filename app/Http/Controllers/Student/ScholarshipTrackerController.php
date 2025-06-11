<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Models\ScholarshipApplication;
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

        // Get all applications for the logged-in user
        $userApplications = collect();
        if ($student && $student->student_id) {
            $userApplications = ScholarshipApplication::where('student_id', $student->student_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($applicationId) {
            // Find application by ID - allow any student to track any application for now
            $application = ScholarshipApplication::where('application_id', $applicationId)->first();
        }

        return view('scholarship.tracker', [
            'application' => $application,
            'applicationId' => $applicationId,
            'student' => $student,
            'userApplications' => $userApplications
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
            'private' => 'Private Scholarship'
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
