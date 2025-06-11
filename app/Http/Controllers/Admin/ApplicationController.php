<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipApplication;
use App\Services\GranteeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationStatusNotification;

class ApplicationController extends Controller
{
    /**
     * Show all applications (excluding approved and rejected)
     */
    public function index(Request $request)
    {
        $query = ScholarshipApplication::query();

        // Exclude approved and rejected applications from the list
        $query->whereNotIn('status', ['Approved', 'Rejected']);

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by scholarship type if provided
        if ($request->has('type') && $request->type) {
            $query->where('scholarship_type', $request->type);
        }

        // Get all applications without pagination
        $applications = $query->orderBy('created_at', 'desc')
            ->get();

        return view('admin.applications', [
            'applications' => $applications,
            'currentStatus' => $request->status,
            'currentType' => $request->type
        ]);
    }

    /**
     * View a specific application
     */
    public function show($id)
    {
        $application = ScholarshipApplication::where('application_id', $id)->firstOrFail();

        return view('admin.application-detail', [
            'application' => $application
        ]);
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, $id)
    {
        $application = ScholarshipApplication::where('application_id', $id)->firstOrFail();

        // Validate the request
        $request->validate([
            'status' => 'required|string|in:Pending Review,Under Committee Review,Approved,Rejected'
        ]);

        $newStatus = $request->status;
        $oldStatus = $application->status;

        try {
            DB::beginTransaction();

            // Handle status-specific actions
            if ($newStatus === 'Approved' && $oldStatus !== 'Approved') {
                // Determine scholarship subtype for Academic scholarships based on GWA
                if ($application->scholarship_type == 'academic' && $application->gwa && !$application->scholarship_subtype) {
                    $gwa = floatval($application->gwa);
                    if ($gwa >= 1.0 && $gwa <= 1.25) {
                        $application->scholarship_subtype = "PL";
                    } elseif ($gwa >= 1.26 && $gwa <= 1.74) {
                        $application->scholarship_subtype = "DL";
                    }
                    $application->save();
                }

                // Create grantee record using GranteeService
                $granteeService = new GranteeService();
                $adminUser = Auth::user();

                // For testing purposes, allow any user to approve applications
                $approvedBy = $adminUser ? $adminUser->name : 'Admin User';

                // Send approval email notification before deleting application
                try {
                    Mail::to($application->email)->send(new ApplicationStatusNotification($application, 'Approved'));
                    Log::info('Application approval email sent successfully', [
                        'application_id' => $application->application_id,
                        'email' => $application->email
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send application approval email', [
                        'application_id' => $application->application_id,
                        'email' => $application->email,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the approval process if email fails
                }

                // Create grantee from application
                $grantee = $granteeService->createGranteeFromApplication($application, $approvedBy);

                // Delete the application from scholarship_applications table
                $application->delete();

                $studentName = $application->first_name . ' ' . $application->last_name;
                $scholarshipType = ucfirst($application->scholarship_type);
                $message = "✅ Application approved successfully! {$studentName}'s {$scholarshipType} scholarship application has been approved and moved to the Grantees tab.";

                Log::info('Application approved and moved to grantees', [
                    'application_id' => $application->application_id,
                    'grantee_id' => $grantee->grantee_id,
                    'approved_by' => $approvedBy
                ]);
            } elseif ($newStatus === 'Rejected' && $oldStatus !== 'Rejected') {
                // Update status to rejected
                $application->status = $newStatus;
                $application->save();

                // Send rejection email notification
                try {
                    Mail::to($application->email)->send(new ApplicationStatusNotification($application, 'Rejected'));
                    Log::info('Application rejection email sent successfully', [
                        'application_id' => $application->application_id,
                        'email' => $application->email
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send application rejection email', [
                        'application_id' => $application->application_id,
                        'email' => $application->email,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the rejection process if email fails
                }

                $studentName = $application->first_name . ' ' . $application->last_name;
                $scholarshipType = ucfirst($application->scholarship_type);
                $message = "❌ Application rejected. {$studentName}'s {$scholarshipType} scholarship application has been rejected. Student will be notified via email.";

                Log::info('Application rejected', [
                    'application_id' => $application->application_id,
                    'rejected_by' => Auth::user() ? Auth::user()->name : 'Admin'
                ]);
            } else {
                // For other status updates (Pending Review, Under Committee Review)
                $application->status = $newStatus;
                $application->save();
                $studentName = $application->first_name . ' ' . $application->last_name;
                $message = "📝 Status updated successfully! {$studentName}'s application status has been changed to '{$newStatus}'.";
            }

            DB::commit();

            // Redirect based on the new status
            if (in_array($newStatus, ['Approved', 'Rejected'])) {
                // Redirect to applications list since this application will no longer be visible
                return redirect()->route('admin.applications')->with('success', $message);
            } else {
                // Stay on the same page for other status updates
                return redirect()->back()->with('success', $message);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating application status', [
                'application_id' => $application->application_id,
                'new_status' => $newStatus,
                'error' => $e->getMessage()
            ]);

            $errorMessage = "❌ Error updating application status: " . $e->getMessage() . " Please try again or contact support if the issue persists.";
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Get applications data for API
     */
    public function getApplicationsData(Request $request)
    {
        $query = ScholarshipApplication::query();

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type) {
            $query->where('scholarship_type', $request->type);
        }

        $applications = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'data' => $applications->map(function ($app) {
                return [
                    'id' => $app->application_id,
                    'student_id' => $app->student_id,
                    'name' => $app->first_name . ' ' . $app->last_name,
                    'email' => $app->email,
                    'scholarship_type' => ucfirst($app->scholarship_type),
                    'course' => $app->course,
                    'department' => $app->department,
                    'year_level' => $app->year_level,
                    'gwa' => $app->gwa,
                    'status' => $app->status,
                    'created_at' => $app->created_at->format('Y-m-d H:i:s')
                ];
            })
        ]);
    }

    /**
     * Get application detail for API
     */
    public function getApplicationDetail($id)
    {
        $application = ScholarshipApplication::where('application_id', $id)->firstOrFail();

        return response()->json([
            'data' => $application
        ]);
    }

    /**
     * Update application status via API
     */
    public function updateApplicationStatus(Request $request, $id)
    {
        $application = ScholarshipApplication::where('application_id', $id)->firstOrFail();

        $request->validate([
            'status' => 'required|string|in:Pending Review,Under Committee Review,Approved,Rejected'
        ]);

        $application->status = $request->status;
        $application->save();

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully',
            'data' => $application
        ]);
    }
}
