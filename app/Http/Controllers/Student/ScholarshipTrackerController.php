<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Models\ScholarshipApplication;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
class ScholarshipTrackerController extends Controller
{
    public function showTracker(Request $request)
    {
        $applicationId = $request->query('id');
        $application = null;
        $student = auth()->user();

        if ($applicationId && $student) {
            // First try to find application that belongs to the authenticated student
            $application = ScholarshipApplication::where('application_id', $applicationId)
                ->where('student_id', $student->student_id)
                ->first();

            // If not found and we're in development/testing mode, show any application
            // This helps with testing when student IDs don't match
            if (!$application && config('app.debug', false)) {
                $application = ScholarshipApplication::where('application_id', $applicationId)->first();

                // Add a flag to indicate this is a test/debug view
                if ($application) {
                    $application->is_debug_view = true;
                }
            }
        }

        return view('scholarship.tracker', [
            'application' => $application,
            'applicationId' => $applicationId,
            'student' => $student
        ]);
    }

    public function trackApplication(Request $request)
    {
        $applicationId = $request->input('application_id');
        $student = auth()->user();

        if (!$student) {
            return response()->json(['found' => false, 'error' => 'Not authenticated']);
        }

        // Only allow tracking applications that belong to the authenticated student
        $application = ScholarshipApplication::where('application_id', $applicationId)
            ->where('student_id', $student->student_id)
            ->first();

        if (!$application) {
            return response()->json(['found' => false]);
        }

        // Define the steps in the application process
        $steps = ['Submitted', 'Initial Review', 'Committee Review', 'Decision', 'Notification'];

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
                $progress = 40;
                $statusTitle = 'Under Committee Review';
                $statusMessage = 'Your application is currently being reviewed by the Scholarship Committee.';
                $statusNote = 'This process typically takes 2-3 weeks. You may be contacted for additional information if needed.';
                break;

            case 'Decision Made':
                $currentStep = 3; // Decision
                $progress = 60;
                $statusTitle = 'Decision Made';
                $statusMessage = 'The Scholarship Committee has made a decision on your application.';
                $statusNote = 'You will be notified of the decision soon.';
                break;

            case 'Approved':
                $currentStep = 4; // Notification
                $progress = 100;
                $statusTitle = 'Application Approved';
                $statusMessage = 'Congratulations! Your scholarship application has been approved.';
                $statusNote = 'Please check your email for further instructions.';
                break;

            case 'Rejected':
                $currentStep = 4; // Notification
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
            'ched' => 'CHED Scholarship',
            'presidents' => 'President\'s and Dean\'s Lister Scholarship',
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





