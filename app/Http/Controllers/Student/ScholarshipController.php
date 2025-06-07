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
        Log::info('=== SCHOLARSHIP APPLICATION SUBMISSION STARTED ===', [
            'scholarship_type' => $request->scholarship_type,
            'student_id' => $request->student_id,
            'method' => $request->method(),
            'url' => $request->url()
        ]);

        // Basic validation
        $request->validate([
            'scholarship_type' => 'required|string',
            'student_id' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'contact_number' => 'required|string',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120' // 5MB max per file
        ]);

        // Enhanced duplicate checking with specific scholarship type validation
        $existingApplication = ScholarshipApplication::where('student_id', $request->student_id)
            ->where('scholarship_type', $request->scholarship_type)
            ->first();

        if ($existingApplication) {
            Log::warning('Duplicate student ID for same scholarship type detected', [
                'student_id' => $request->student_id,
                'scholarship_type' => $request->scholarship_type,
                'existing_application_id' => $existingApplication->application_id,
                'existing_created_at' => $existingApplication->created_at,
                'existing_status' => $existingApplication->status
            ]);

            $scholarshipTypeNames = [
                'ched' => 'CHED Scholarship',
                'academic' => 'Academic Scholarship',
                'employees' => 'Employee\'s Scholarship'
            ];

            $scholarshipName = $scholarshipTypeNames[$request->scholarship_type] ?? ucfirst($request->scholarship_type);

            return back()->withErrors([
                'student_id' => "This Student ID has already been used for a {$scholarshipName} application on " .
                    $existingApplication->created_at->format('M d, Y') . ". " .
                    "Status: {$existingApplication->status}. " .
                    "Each student can only apply once per scholarship type."
            ])->withInput();
        }

        // Generate a unique application ID
        $applicationId = 'SCH-' . strtoupper(Str::random(2)) . rand(10000, 99999);

        // Create a new scholarship application
        $application = new ScholarshipApplication();
        $application->application_id = $applicationId;
        $application->scholarship_type = $request->scholarship_type;

        // Determine scholarship subtype for Academic scholarships based on GWA
        if ($request->scholarship_type == 'academic' && $request->gwa) {
            $gwa = floatval($request->gwa);
            if ($gwa >= 1.0 && $gwa <= 1.25) {
                $application->scholarship_subtype = "PL";
            } elseif ($gwa == 1.50) {
                $application->scholarship_subtype = "DL";
            }
        }

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

        // Log all form data for debugging
        Log::info('Form data received:', $request->all());

        // Fill in all the fields from the request
        foreach ($fieldMap as $formField => $dbField) {
            if ($request->has($formField)) {
                $application->$dbField = $request->$formField;
                Log::info("Setting {$dbField} = " . $request->$formField);
            }
        }

        // Handle document uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                if ($file->isValid()) {
                    // Generate unique filename
                    $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();

                    // Store file in private storage
                    $path = $file->storeAs('scholarship_documents/' . $applicationId, $filename, 'local');

                    // Add document info to array
                    $documents[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'filename' => $filename,
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString()
                    ];

                    Log::info("Document uploaded: {$filename}", [
                        'path' => $path,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType()
                    ]);
                }
            }
        }

        // Store documents array in the application
        $application->documents = $documents;

        try {
            // Save the application
            $application->save();

            // Store application ID and scholarship type in session for the success page
            session(['application_id' => $applicationId]);

            // Format scholarship type for display
            $scholarshipTypes = [
                'ched' => 'CHED Scholarship',
                'academic' => 'Academic Scholarship',
                'employees' => 'Employees Scholar'
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
