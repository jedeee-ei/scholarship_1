<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ScholarshipApplication;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        // Get student's applications
        $applications = ScholarshipApplication::where('student_id', $student->student_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Check if student can apply for a new scholarship
        $hasActiveScholarship = ScholarshipApplication::where('student_id', $student->student_id)
            ->where('status', 'Approved')
            ->where('is_active', true)
            ->exists();

        $hasPendingApplication = ScholarshipApplication::where('student_id', $student->student_id)
            ->whereIn('status', ['Pending Review', 'Under Committee Review', 'Decision Made'])
            ->exists();

        $isRenewalPeriod = config('scholarship.renewal_period', false);

        $canApplyForScholarship = (!$hasActiveScholarship && !$hasPendingApplication) || $isRenewalPeriod;

        // Check for recent status updates (within last 7 days) for notifications
        $recentStatusUpdate = ScholarshipApplication::where('student_id', $student->student_id)
            ->whereIn('status', ['Approved', 'Rejected'])
            ->where('updated_at', '>=', now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->first();

        // Check for permanent scholarship status (current active or most recent final status)
        $permanentStatus = ScholarshipApplication::where('student_id', $student->student_id)
            ->whereIn('status', ['Approved', 'Rejected'])
            ->orderBy('updated_at', 'desc')
            ->first();

        // Get published announcements
        $announcements = Announcement::published()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Check if applications are open
        $applicationStatus = SystemSetting::get('application_status', 'closed');
        $applicationsOpen = $applicationStatus === 'open';

        return view('student.dashboard', [
            'student' => $student,
            'applications' => $applications,
            'canApplyForScholarship' => $canApplyForScholarship && $applicationsOpen,
            'announcements' => $announcements,
            'recentStatusUpdate' => $recentStatusUpdate,
            'permanentStatus' => $permanentStatus,
            'applicationsOpen' => $applicationsOpen
        ]);
    }

    /**
     * Change student password
     */
    public function changePassword(Request $request)
    {
        $student = Auth::user();

        // Validate the request
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'different:current_password'
                ],
            ], [
                'new_password.different' => 'The new password must be different from your current password.',
                'new_password.min' => 'The new password must be at least 8 characters long.',
                'new_password.confirmed' => 'The password confirmation does not match.',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // Check if current password is correct
        if (!Hash::check($request->current_password, $student->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['current_password' => ['The current password is incorrect.']]
                ], 422);
            }
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.'
            ])->withInput();
        }

        // Update password
        $student->update([
            'password' => Hash::make($request->new_password),
            'password_changed' => true
        ]);

        $message = 'Password changed successfully! You are now using a custom password.';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }
}
