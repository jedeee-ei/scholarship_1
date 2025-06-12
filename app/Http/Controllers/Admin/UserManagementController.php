<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ScholarshipApplication;
use App\Models\Grantee;
use App\Models\ArchivedStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Show student registration page
     */
    public function studentRegister(Request $request)
    {
        // Handle success messages from URL parameters
        if ($request->has('success')) {
            $successType = $request->get('success');
            switch ($successType) {
                case 'student_registered':
                    session()->flash('success', 'Student registered successfully!');
                    break;
                case 'student_updated':
                    session()->flash('success', 'Student updated successfully!');
                    break;
                case 'student_deleted':
                    session()->flash('success', 'Student deleted successfully!');
                    break;
            }
        }

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
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'
            ],
        ], [
            'email.regex' => 'Email address must be a Gmail account (@gmail.com)',
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

            // Check if this is an AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student registered successfully!',
                    'student_data' => [
                        'id' => $user->id,
                        'student_id' => $user->student_id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'password' => 'student123'
                    ]
                ]);
            }

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

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Check student ID availability across all sources
     */
    public function checkStudentIdAvailability(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string'
        ]);

        $studentId = $request->student_id;

        // Check in users table
        $existsInUsers = User::where('student_id', $studentId)->exists();

        // Check in scholarship applications
        $existsInApplications = ScholarshipApplication::where('student_id', $studentId)->exists();

        // Check in grantees
        $existsInGrantees = Grantee::where('student_id', $studentId)->exists();

        // Check in archived students
        $existsInArchived = ArchivedStudent::where('student_id', $studentId)->exists();

        $exists = $existsInUsers || $existsInApplications || $existsInGrantees || $existsInArchived;

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
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
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
                'reset_by' => Auth::user() ? Auth::user()->name : 'Admin'
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
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
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
        // Get students from users table (registered students)
        $userStudents = User::where('role', 'student')
            ->select('id', 'student_id', 'first_name', 'last_name', 'email', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'name' => trim($student->first_name . ' ' . $student->last_name),
                    'email' => $student->email,
                    'contact_number' => null, // Users table doesn't have contact_number
                    'registration_date' => $student->created_at->format('M d, Y'),
                    'source' => 'user'
                ];
            });

        // Get students from scholarship applications
        $applicationStudents = ScholarshipApplication::select(
            'student_id',
            'first_name',
            'last_name',
            'email',
            'contact_number',
            'created_at',
            'application_id'
        )
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($student) {
            return [
                'id' => $student->application_id,
                'student_id' => $student->student_id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'email' => $student->email,
                'contact_number' => $student->contact_number,
                'registration_date' => $student->created_at->format('M d, Y'),
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
            'created_at',
            'application_id'
        )
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($student) {
            return [
                'id' => $student->student_id,
                'student_id' => $student->student_id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'email' => $student->email,
                'contact_number' => $student->contact_number,
                'registration_date' => $student->created_at->format('M d, Y'),
                'source' => 'grantee'
            ];
        });

        // Get archived students
        $archivedStudents = ArchivedStudent::select(
            'id',
            'student_id',
            'first_name',
            'last_name',
            'email',
            'contact_number',
            'created_at'
        )
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($student) {
            return [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'email' => $student->email,
                'contact_number' => $student->contact_number,
                'registration_date' => $student->created_at->format('M d, Y'),
                'source' => 'archived'
            ];
        });

        // Combine all students (including archived) and remove duplicates
        // Priority: grantees > applications > users > archived
        $allStudents = collect();
        $processedStudentIds = [];

        // Add grantees first (they have highest priority)
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

        // Add user students if they're not already in grantees or applications
        foreach ($userStudents as $student) {
            if (!in_array($student['student_id'], $processedStudentIds)) {
                $allStudents->push($student);
                $processedStudentIds[] = $student['student_id'];
            }
        }

        // Add archived students if they're not already in the list
        foreach ($archivedStudents as $student) {
            if (!in_array($student['student_id'], $processedStudentIds)) {
                $allStudents->push($student);
                $processedStudentIds[] = $student['student_id'];
            }
        }

        return $allStudents->sortByDesc('registration_date')->values();
    }

    /**
     * Edit student information
     */
    public function editStudent(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'nullable|string|max:20',
            'source' => 'required|string|in:application,grantee,archived,user'
        ]);

        try {
            $source = $request->source;

            Log::info('Edit student request', [
                'id' => $id,
                'source' => $source,
                'request_data' => $request->only(['first_name', 'last_name', 'email', 'contact_number'])
            ]);

            if ($source === 'application') {
                $student = ScholarshipApplication::findOrFail($id);
            } elseif ($source === 'grantee') {
                $student = Grantee::findOrFail($id);
            } elseif ($source === 'user') {
                $student = User::findOrFail($id);
            } else {
                $student = ArchivedStudent::findOrFail($id);
            }

            // Prepare update data based on source
            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
            ];

            // Only add contact_number if the model supports it (not for User model)
            if ($source !== 'user' && $request->has('contact_number')) {
                $updateData['contact_number'] = $request->contact_number;
            }

            $student->update($updateData);

            Log::info('Student updated by admin', [
                'student_id' => $student->student_id,
                'source' => $source,
                'updated_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully'
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
    public function deleteStudent(Request $request, $id)
    {
        $request->validate([
            'source' => 'required|string|in:application,grantee,archived,user'
        ]);

        try {
            $source = $request->source;

            Log::info('Delete student request', [
                'id' => $id,
                'source' => $source
            ]);

            if ($source === 'application') {
                $student = ScholarshipApplication::findOrFail($id);
            } elseif ($source === 'grantee') {
                $student = Grantee::findOrFail($id);
            } elseif ($source === 'user') {
                $student = User::findOrFail($id);
            } else {
                $student = ArchivedStudent::findOrFail($id);
            }

            $studentId = $student->student_id;
            $studentName = $student->first_name . ' ' . $student->last_name;

            $student->delete();

            Log::info('Student deleted by admin', [
                'student_id' => $studentId,
                'student_name' => $studentName,
                'source' => $source,
                'deleted_by' => Auth::user() ? Auth::user()->name : 'Admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Student deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
