<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use Illuminate\Support\Facades\Auth;

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

        return view('student.dashboard', [
            'student' => $student,
            'applications' => $applications,
            'canApplyForScholarship' => $canApplyForScholarship
        ]);
    }
}
