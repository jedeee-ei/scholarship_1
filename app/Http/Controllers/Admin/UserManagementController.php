<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    /**
     * Show student registration page
     */
    public function studentRegister()
    {
        // Get scholarship students data
        $scholarshipStudents = $this->getScholarshipStudents();

        return view('admin.student-register', [
            'scholarshipStudents' => $scholarshipStudents
        ]);
    }

    /**
     * Store student registration
     */
    public function storeStudentRegister(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|unique:users,student_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        try {
            $user = User::create([
                'student_id' => $request->student_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make('student123'), // Default password
                'role' => 'student',
                'is_active' => true
            ]);

            Log::info('Student registered by admin', [
                'student_id' => $user->student_id,
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name
            ]);

            return redirect()->back()->with([
                'success' => 'Student registered successfully!',
                'student_data' => [
                    'student_id' => $user->student_id,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'password' => 'student123'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Student registration error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Check student ID availability
     */
    public function checkStudentIdAvailability(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string'
        ]);

        $exists = User::where('student_id', $request->student_id)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Student ID already exists' : 'Student ID is available'
        ]);
    }

    /**
     * List all students
     */
    public function listStudents(Request $request)
    {
        $query = User::where('role', 'student');

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('course') && $request->course) {
            $query->where('course', $request->course);
        }

        if ($request->has('year_level') && $request->year_level) {
            $query->where('year_level', $request->year_level);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'students' => $students->items(),
            'pagination' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total()
            ]
        ]);
    }

    /**
     * Update student information
     */
    public function updateStudent(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'contact_number' => 'required|string|max:20',
            'course' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'year_level' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        try {
            $user = User::findOrFail($id);

            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_name' => $request->middle_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'course' => $request->course,
                'department' => $request->department,
                'year_level' => $request->year_level,
                'is_active' => $request->has('is_active') ? $request->is_active : $user->is_active
            ]);

            Log::info('Student updated by admin', [
                'student_id' => $user->student_id,
                'updated_by' => auth()->user() ? auth()->user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully',
                'student' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Student update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete student
     */
    public function deleteStudent($id)
    {
        try {
            $user = User::findOrFail($id);
            $studentName = $user->first_name . ' ' . $user->last_name;
            $studentId = $user->student_id;

            $user->delete();

            Log::info('Student deleted by admin', [
                'student_id' => $studentId,
                'student_name' => $studentName,
                'deleted_by' => auth()->user() ? auth()->user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Student delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset student password
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        try {
            $user = User::findOrFail($id);

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            Log::info('Student password reset by admin', [
                'student_id' => $user->student_id,
                'reset_by' => auth()->user() ? auth()->user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Password reset error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle student active status
     */
    public function toggleActiveStatus($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'is_active' => !$user->is_active
            ]);

            $status = $user->is_active ? 'activated' : 'deactivated';

            Log::info("Student {$status} by admin", [
                'student_id' => $user->student_id,
                'new_status' => $user->is_active,
                'updated_by' => auth()->user() ? auth()->user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Student {$status} successfully",
                'is_active' => $user->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle active status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Status update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student statistics
     */
    public function getStudentStatistics()
    {
        $stats = [
            'total' => User::where('role', 'student')->count(),
            'active' => User::where('role', 'student')->where('is_active', true)->count(),
            'inactive' => User::where('role', 'student')->where('is_active', false)->count(),
            'by_year_level' => User::where('role', 'student')
                ->selectRaw('year_level, COUNT(*) as count')
                ->groupBy('year_level')
                ->pluck('count', 'year_level')
                ->toArray(),
            'by_course' => User::where('role', 'student')
                ->selectRaw('course, COUNT(*) as count')
                ->groupBy('course')
                ->orderBy('count', 'desc')
                ->take(10)
                ->pluck('count', 'course')
                ->toArray(),
            'recent_registrations' => User::where('role', 'student')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['student_id', 'first_name', 'last_name', 'email', 'created_at'])
        ];

        return response()->json($stats);
    }

    /**
     * Get students registered for scholarships
     */
    private function getScholarshipStudents()
    {
        // Get students from scholarship applications
        $applicationStudents = ScholarshipApplication::select(
            'student_id',
            'first_name',
            'last_name',
            'email',
            'contact_number',
            'course',
            'department',
            'year_level',
            'scholarship_type',
            'status',
            'created_at',
            'application_id'
        )
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($student) {
            return [
                'student_id' => $student->student_id,
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'email' => $student->email,
                'contact_number' => $student->contact_number,
                'course' => $student->course ?? 'Not specified',
                'department' => $student->department ?? 'Not specified',
                'year_level' => $student->year_level ?? 'Not specified',
                'scholarship_type' => ucfirst($student->scholarship_type),
                'status' => $student->status,
                'application_date' => $student->created_at->format('M d, Y'),
                'application_id' => $student->application_id,
                'source' => 'application'
            ];
        });

        // Get students from grantees (approved scholarships)
        $granteeStudents = Grantee::select(
            'student_id',
            'first_name',
            'last_name',
            'email',
            'contact_number',
            'course',
            'department',
            'year_level',
            'scholarship_type',
            'status',
            'created_at',
            'grantee_id',
            'application_id'
        )
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($student) {
            return [
                'student_id' => $student->student_id,
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'email' => $student->email,
                'contact_number' => $student->contact_number,
                'course' => $student->course ?? 'Not specified',
                'department' => $student->department ?? 'Not specified',
                'year_level' => $student->year_level ?? 'Not specified',
                'scholarship_type' => ucfirst($student->scholarship_type),
                'status' => 'Active Scholar',
                'application_date' => $student->created_at->format('M d, Y'),
                'application_id' => $student->application_id ?? $student->grantee_id,
                'source' => 'grantee'
            ];
        });

        // Combine and remove duplicates (prefer grantee data over application data)
        $allStudents = collect();
        $processedStudentIds = [];

        // Add grantees first (they have priority)
        foreach ($granteeStudents as $student) {
            if (!in_array($student['student_id'], $processedStudentIds)) {
                $allStudents->push($student);
                $processedStudentIds[] = $student['student_id'];
            }
        }

        // Add application students if they're not already in grantees
        foreach ($applicationStudents as $student) {
            if (!in_array($student['student_id'], $processedStudentIds)) {
                $allStudents->push($student);
                $processedStudentIds[] = $student['student_id'];
            }
        }

        return $allStudents->sortByDesc('application_date')->values();
    }
}
