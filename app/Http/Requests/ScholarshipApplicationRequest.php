<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScholarshipApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow authenticated users
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $scholarshipType = $this->input('scholarship_type');

        $rules = [
            // Basic required fields for all scholarship types
            'scholarship_type' => 'required|in:government,academic,employees,alumni',
            'student_id' => [
                'required',
                'string',
                'max:20',
                // Enhanced rule to check for duplicates across ALL scholarship types
                Rule::unique('scholarship_applications', 'student_id')->ignore($this->route('id')),
                // Also check grantees table for duplicates
                Rule::unique('grantees', 'student_id')
            ],
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|regex:/^[0-9]{11}$/|size:11',
        ];

        // Add scholarship-specific validation rules
        switch ($scholarshipType) {
            case 'government':
                $rules = array_merge($rules, $this->getGovernmentRules());
                break;
            case 'academic':
                $rules = array_merge($rules, $this->getAcademicRules());
                break;
            case 'employees':
                $rules = array_merge($rules, $this->getEmployeesRules());
                break;
            case 'alumni':
                $rules = array_merge($rules, $this->getAlumniRules());
                break;
        }

        return $rules;
    }

    /**
     * Get validation rules for Government scholarship
     */
    private function getGovernmentRules(): array
    {
        return [
            'government_benefactor_type' => 'required|in:CHED,DOST,DSWD,DOLE',
            'sex' => 'required|in:Male,Female',
            'birthdate' => 'required|date|before:today',
            'education_stage' => 'required|in:BEU,BSU,College',
            'grade_level' => 'required_if:education_stage,BEU,BSU|string',
            'strand' => 'required_if:grade_level,Grade 11,Grade 12|string',
            'department' => 'required_if:education_stage,College|string',
            'course' => 'required_if:education_stage,College|string',
            'year_level' => 'required_if:education_stage,College|string',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'zipcode' => 'required|string|max:10',
            'disability' => 'nullable|string|max:255',
            'indigenous' => 'nullable|string|max:255',
            'father_last_name' => 'nullable|string|max:100',
            'father_first_name' => 'nullable|string|max:100',
            'father_middle_name' => 'nullable|string|max:100',
            'mother_last_name' => 'nullable|string|max:100',
            'mother_first_name' => 'nullable|string|max:100',
            'mother_middle_name' => 'nullable|string|max:100',
        ];
    }

    /**
     * Get validation rules for Academic scholarship
     */
    private function getAcademicRules(): array
    {
        return [
            'department' => 'required|string|max:100',
            'course' => 'required|string|max:255',
            'year_level' => 'required|string|max:50',
            'semester' => 'required|string|max:50',
            'academic_year' => 'required|string|max:20',
            'gwa' => 'required|numeric|min:1.0|max:1.75',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'zipcode' => 'required|string|max:10',
            'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
        ];
    }



    /**
     * Get validation rules for Employee's scholarship
     */
    private function getEmployeesRules(): array
    {
        return [
            'employee_name' => 'required|string|max:255',
            'employee_relationship' => 'required|in:Son,Daughter,Spouse',
            'employee_department' => 'required|string|max:100',
            'employee_position' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'zipcode' => 'required|string|max:10',
        ];
    }

    /**
     * Get validation rules for Alumni scholarship
     */
    private function getAlumniRules(): array
    {
        return [
            'scholarship_name' => 'required|string|max:255',
            'other_scholarship' => 'nullable|string|max:1000',
            'street' => 'required|string|max:255',
            'barangay' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'zipcode' => 'required|string|max:10',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'student_id.unique' => 'This Student ID has already been used for a scholarship application or is already an approved grantee. Each student can only submit one scholarship application.',
            'student_id.required' => 'Student ID is required.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'middle_name.required' => 'Middle name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'contact_number.required' => 'Contact number is required.',
            'contact_number.regex' => 'Contact number must be exactly 11 digits and contain only numbers.',
            'contact_number.size' => 'Contact number must be exactly 11 digits.',
            'sex.required' => 'Please select your sex.',
            'birthdate.required' => 'Birthdate is required.',
            'birthdate.before' => 'Birthdate must be before today.',
            'government_benefactor_type.required' => 'Please select a benefactor type.',
            'government_benefactor_type.in' => 'Please select a valid benefactor type.',
            'education_stage.required' => 'Please select your education stage.',
            'department.required' => 'Department is required.',
            'course.required' => 'Course is required.',
            'year_level.required' => 'Year level is required.',
            'semester.required' => 'Semester is required.',
            'academic_year.required' => 'Academic year is required.',

            'gwa.required' => 'GWA is required for academic scholarships.',
            'gwa.numeric' => 'GWA must be a number.',
            'gwa.min' => 'GWA must be at least 1.0.',
            'gwa.max' => 'GWA cannot exceed 5.0.',
            'address.required' => 'Address is required.',
            'street.required' => 'Street is required.',
            'barangay.required' => 'Barangay is required.',
            'city.required' => 'City is required.',
            'province.required' => 'Province is required.',
            'zipcode.required' => 'Zipcode is required.',
            'father_last_name.required' => 'Father\'s last name is required.',
            'father_first_name.required' => 'Father\'s first name is required.',
            'father_middle_name.required' => 'Father\'s middle name is required.',
            'mother_last_name.required' => 'Mother\'s last name is required.',
            'mother_first_name.required' => 'Mother\'s first name is required.',
            'mother_middle_name.required' => 'Mother\'s middle name is required.',
            'employee_name.required' => 'Employee name is required.',
            'employee_relationship.required' => 'Relationship to employee is required.',
            'employee_department.required' => 'Employee department is required.',
            'employee_position.required' => 'Employee position is required.',
            'scholarship_name.required' => 'Scholarship name is required.',
            'other_scholarship.required' => 'Other scholarship details are required.',
            'documents.*.required' => 'Documents are required.',
            'documents.*.mimes' => 'Documents must be PDF, DOC, DOCX, JPG, JPEG, or PNG files.',
            'documents.*.max' => 'Each document must not exceed 5MB.',
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'student_id' => 'Student ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'middle_name' => 'Middle Name',
            'email' => 'Email Address',
            'contact_number' => 'Contact Number',
            'gwa' => 'General Weighted Average',
            'employee_name' => 'Employee Name',
            'employee_relationship' => 'Relationship to Employee',
            'employee_department' => 'Employee Department',
            'employee_position' => 'Employee Position',
            'scholarship_name' => 'Scholarship Name',
        ];
    }
}
