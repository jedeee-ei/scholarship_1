<?php

namespace App\Http\Controllers\Student;


use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ScholarshipController extends Controller
{


    public function submitApplication(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('Scholarship application submission started', [
            'scholarship_type' => $request->scholarship_type,
            'student_id' => $request->student_id,
            'all_data' => $request->all()
        ]);

        // Base validation rules
        $baseRules = [
            'scholarship_type' => 'required|string',
            'student_id' => 'required|string',
            'last_name' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'email' => 'required|email',
            'sex' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'contact_number' => 'nullable|string',
        ];

        // Scholarship-specific validation rules
        $scholarshipSpecificRules = [];

        switch ($request->scholarship_type) {
            case 'presidents':
                $scholarshipSpecificRules = [
                    'sex' => 'required|string',
                    'birthdate' => 'required|date',
                    'education_stage' => 'required|string',
                    'gwa' => 'required|numeric|min:1.0|max:4.0',
                    'semester' => 'required|string',
                    'academic_year' => 'required|string',
                ];

                // Add conditional validation based on education stage
                if ($request->education_stage === 'BSU') {
                    $scholarshipSpecificRules['strand'] = 'required|string';
                    $scholarshipSpecificRules['grade_level'] = 'required|string';
                } elseif ($request->education_stage === 'College') {
                    $scholarshipSpecificRules['department'] = 'required|string';
                    $scholarshipSpecificRules['course'] = 'required|string';
                    $scholarshipSpecificRules['year_level'] = 'required|string';
                }
                break;

            case 'institutional':
                $scholarshipSpecificRules = [
                    'department' => 'required|string',
                    'course' => 'required|string',
                    'year_level' => 'required|string',
                    'semester' => 'required|string',
                    'academic_year' => 'required|string',
                    'gwa' => 'required|numeric|min:1.0|max:5.0',
                    'contact_number' => 'required|string',
                    'address' => 'required|string',
                ];
                break;

            case 'ched':
                $scholarshipSpecificRules = [
                    'education_stage' => 'required|string',
                    'father_last_name' => 'nullable|string',
                    'father_first_name' => 'nullable|string',
                    'mother_last_name' => 'nullable|string',
                    'mother_first_name' => 'nullable|string',
                    'street' => 'nullable|string',
                    'barangay' => 'nullable|string',
                    'city' => 'nullable|string',
                    'province' => 'nullable|string',
                    'zipcode' => 'nullable|string',
                ];
                break;

            case 'employees':
                $scholarshipSpecificRules = [
                    'employee_name' => 'required|string',
                    'employee_relationship' => 'required|string',
                    'employee_department' => 'required|string',
                    'employee_position' => 'required|string',
                ];
                break;

            case 'private':
                $scholarshipSpecificRules = [
                    'scholarship_name' => 'required|string',
                    'other_scholarship' => 'nullable|string',
                ];
                break;
        }

        // Merge base rules with scholarship-specific rules
        $validationRules = array_merge($baseRules, $scholarshipSpecificRules);

        // Validate the request
        try {
            $request->validate($validationRules);
            Log::info('Validation passed for scholarship application');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for scholarship application', [
                'errors' => $e->errors(),
                'scholarship_type' => $request->scholarship_type
            ]);

            // Return back with validation errors
            return back()->withErrors($e->errors())->withInput();
        }

        // Check for duplicate student ID submissions
        $existingApplication = ScholarshipApplication::where('student_id', $request->student_id)->first();
        if ($existingApplication) {
            Log::warning('Duplicate student ID submission attempt', [
                'student_id' => $request->student_id,
                'existing_application_id' => $existingApplication->application_id,
                'existing_scholarship_type' => $existingApplication->scholarship_type,
                'new_scholarship_type' => $request->scholarship_type
            ]);

            return back()->withErrors([
                'student_id' => 'This Student ID has already been used for a scholarship application (' .
                               ucfirst($existingApplication->scholarship_type) . ' on ' .
                               $existingApplication->created_at->format('M d, Y') . '). ' .
                               'Each student can only apply once per scholarship type.'
            ])->withInput();
        }

        // Generate a unique application ID
        $applicationId = 'SCH-' . strtoupper(Str::random(2)) . rand(10000, 99999);

        // Create a new scholarship application
        $application = new ScholarshipApplication();
        $application->application_id = $applicationId;
        $application->scholarship_type = $request->scholarship_type;
        $application->status = 'Pending Review';

        // Map form fields to database fields - UPDATED to include all fields
        $fieldMap = [
            'student_id' => 'student_id',
            'last_name' => 'last_name',
            'first_name' => 'first_name',
            'middle_name' => 'middle_name',
            'sex' => 'sex',
            'birthdate' => 'birthdate',
            'education_stage' => 'education_stage',
            'department' => 'department',
            'course' => 'course',
            'year_level' => 'year_level',
            'grade_level' => 'grade_level',
            'strand' => 'strand',
            'gwa' => 'gwa',
            'semester' => 'semester',
            'academic_year' => 'academic_year',
            'father_last_name' => 'father_last_name',
            'father_first_name' => 'father_first_name',
            'father_middle_name' => 'father_middle_name',
            'mother_last_name' => 'mother_last_name',
            'mother_first_name' => 'mother_first_name',
            'mother_middle_name' => 'mother_middle_name',
            'street' => 'street',
            'barangay' => 'barangay',
            'city' => 'city',
            'province' => 'province',
            'zipcode' => 'zipcode',
            'disability' => 'disability',
            'indigenous' => 'indigenous',
            'contact_number' => 'contact_number',
            'email' => 'email',
            'employee_name' => 'employee_name',
            'employee_relationship' => 'employee_relationship',
            'employee_department' => 'employee_department',
            'employee_position' => 'employee_position',
            'scholarship_name' => 'scholarship_name',
            'other_scholarship' => 'other_scholarship',
            'address' => 'address',
        ];

        // Fill in all the fields from the request
        foreach ($fieldMap as $formField => $dbField) {
            if ($request->has($formField)) {
                $application->$dbField = $request->$formField;
            }
        }

        try {
            // Save the application
            $application->save();

            // Store application ID and scholarship type in session for the success page
            session(['application_id' => $applicationId]);

            // Format scholarship type for display
            $scholarshipTypes = [
                'ched' => 'CHED Scholarship',
                'presidents' => 'President\'s and Dean\'s Lister Scholarship',
                'institutional' => 'Institutional Scholarship',
                'employees' => 'Employees Scholar',
                'private' => 'Private Scholarship'
            ];

            session(['scholarship_type' => $scholarshipTypes[$request->scholarship_type] ?? $request->scholarship_type]);

            // Log successful submission for debugging
            Log::info('Scholarship application submitted successfully', [
                'application_id' => $applicationId,
                'scholarship_type' => $request->scholarship_type,
                'student_id' => $request->student_id
            ]);

            // Redirect to success page
            return redirect()->route('scholarship.success');

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error saving scholarship application', [
                'error' => $e->getMessage(),
                'scholarship_type' => $request->scholarship_type,
                'student_id' => $request->student_id
            ]);

            // Return back with error message
            return back()->withErrors(['error' => 'There was an error submitting your application. Please try again.'])->withInput();
        }
    }

    public function showSuccess()
    {
        // If there's no application ID in the session, redirect to the student dashboard
        if (!session('application_id')) {
            return redirect()->route('student.dashboard')
                ->with('error', 'No application found. Please submit an application first.');
        }

        return view('scholarship.success');
    }

    public function checkStudentId(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string'
        ]);

        $studentId = $request->input('student_id');

        // Check if this student ID already exists in scholarship applications
        $existingApplication = ScholarshipApplication::where('student_id', $studentId)->first();

        if ($existingApplication) {
            return response()->json([
                'exists' => true,
                'scholarship_type' => ucfirst($existingApplication->scholarship_type),
                'application_date' => $existingApplication->created_at->format('M d, Y'),
                'application_id' => $existingApplication->application_id,
                'status' => $existingApplication->status
            ]);
        }

        return response()->json([
            'exists' => false
        ]);
    }
}










