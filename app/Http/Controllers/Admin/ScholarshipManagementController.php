<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\Grantee;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
// Mail functionality temporarily disabled
// use Illuminate\Support\Facades\Mail;
// use App\Mail\SemesterUpdateNotification;

class ScholarshipManagementController extends Controller
{
    /**
     * Show scholarships page
     */
    public function index(Request $request)
    {
        // Handle success message from URL parameter
        if ($request->has('success') && $request->get('success') === 'benefactor_added') {
            session()->flash('success', 'Benefactor added successfully!');
        }

        // Get current semester and academic year from system settings
        $currentSemester = SystemSetting::where('key', 'current_semester')->value('value') ?? '1st Semester';
        $currentAcademicYear = SystemSetting::where('key', 'current_academic_year')->value('value') ?? '2024-2025';

        // Get scholarships from database instead of hardcoded data
        $scholarships = Scholarship::all();
        $scholarshipStats = [];

        // If no scholarships exist in database, return empty array
        if ($scholarships->isEmpty()) {
            $scholarshipStats = [];
        } else {
            // Build scholarship stats from database
            foreach ($scholarships as $scholarship) {
                $key = strtolower(str_replace(' ', '', $scholarship->type));
                $scholarshipStats[$key] = [
                    'name' => $scholarship->name,
                    'type' => $scholarship->type,
                    'active_grantees' => Grantee::where('scholarship_type', strtolower($scholarship->type))->count(),
                    'semester' => $currentSemester,
                    'academic_year' => $currentAcademicYear,
                    'is_custom' => true
                ];
            }
        }

        // Get benefactor statistics for government scholarships
        $benefactorStats = [
            'CHED' => Grantee::where('scholarship_type', 'government')
                ->where('government_benefactor_type', 'CHED')->count(),
            'DOST' => Grantee::where('scholarship_type', 'government')
                ->where('government_benefactor_type', 'DOST')->count(),
            'DSWD' => Grantee::where('scholarship_type', 'government')
                ->where('government_benefactor_type', 'DSWD')->count(),
            'DOLE' => Grantee::where('scholarship_type', 'government')
                ->where('government_benefactor_type', 'DOLE')->count(),
        ];

        return view('admin.scholarships', [
            'currentSemester' => $currentSemester,
            'currentAcademicYear' => $currentAcademicYear,
            'scholarshipStats' => $scholarshipStats,
            'benefactorStats' => $benefactorStats
        ]);
    }

    /**
     * Add new scholarship
     */
    public function addScholarship(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'nullable|date',
            'is_active' => 'boolean'
        ]);

        try {
            $scholarship = Scholarship::create([
                'name' => $request->name,
                'type' => $request->type,
                'semester' => $request->semester,
                'academic_year' => $request->academic_year,
                'description' => $request->description,
                'requirements' => $request->requirements,
                'benefits' => $request->benefits,
                'application_deadline' => $request->application_deadline,
                'is_active' => $request->has('is_active'),
                'created_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scholarship added successfully',
                'data' => $scholarship
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding scholarship: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding scholarship: ' . $e->getMessage()
            ], 500);
        }
    }




}
