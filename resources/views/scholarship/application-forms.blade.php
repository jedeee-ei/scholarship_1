@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body p-4">
            <h2 class="text-success mb-4" style="color: #1e6641 !important; font-weight: 600;">Application Forms</h2>
            
            <div class="scholarship-tabs mb-4">
                <div class="btn-group" role="group" aria-label="Scholarship types">
                    <button type="button" class="btn btn-outline-success active" id="ched-tab" 
                            style="background-color: #1e6641; color: white; border-color: #1e6641;">CHED</button>
                    <button type="button" class="btn btn-outline-success" id="institutional-tab"
                            style="color: #333; background-color: #f8f9fa; border-color: #dee2e6;">Institutional</button>
                    <button type="button" class="btn btn-outline-success" id="employees-tab"
                            style="color: #333; background-color: #f8f9fa; border-color: #dee2e6;">Employees Scholar</button>
                    <button type="button" class="btn btn-outline-success" id="private-tab"
                            style="color: #333; background-color: #f8f9fa; border-color: #dee2e6;">Private</button>
                    <button type="button" class="btn btn-outline-success" id="add-more-tab"
                            style="color: #333; background-color: #f8f9fa; border-color: #dee2e6; border-style: dashed;">+ Add more</button>
                </div>
            </div>
            
            <!-- CHED Form -->
            <div class="scholarship-form active" id="ched-form">
                <form action="{{ route('scholarship.submit') }}" method="POST" enctype="multipart/form-data" id="chedScholarshipForm">
                    @csrf
                    <input type="hidden" name="scholarship_type" value="ched">
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="form-section-title">Personal Information</h4>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="student_id" class="form-label">Student ID</label>
                                <input type="text" class="form-control" id="student_id" name="student_id" value="{{ Auth::user()->student_id }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sex" class="form-label">Sex</label>
                                <select class="form-select" id="sex" name="sex" required>
                                    <option value="">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="birthdate" class="form-label">Birthdate</label>
                                <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="form-section-title">Educational Information</h4>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="education_stage" class="form-label">Education Stage</label>
                                <select class="form-select" id="education_stage" name="education_stage" required>
                                    <option value="">Select</option>
                                    <option value="College">College</option>
                                    <option value="BSU">Basic Education</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="department" class="form-label">Department</label>
                                <select class="form-select" id="department" name="department" required>
                                    <option value="">Select</option>
                                    <option value="SITE">SITE</option>
                                    <option value="SASTE">SASTE</option>
                                    <option value="SBAHM">SBAHM</option>
                                    <option value="SNAMS">SNAMS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="course" class="form-label">Course</label>
                                <select class="form-select" id="course" name="course" required>
                                    <option value="">Select Department First</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4 college-field">
                            <div class="form-group">
                                <label for="year_level" class="form-label">Year Level</label>
                                <select class="form-select" id="year_level" name="year_level">
                                    <option value="">Select</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                    <option value="5">5th Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 bsu-field" style="display: none;">
                            <div class="form-group">
                                <label for="grade_level" class="form-label">Grade Level</label>
                                <select class="form-select" id="grade_level" name="grade_level">
                                    <option value="">Select</option>
                                    <option value="11">Grade 11</option>
                                    <option value="12">Grade 12</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 strand-field" style="display: none;">
                            <div class="form-group">
                                <label for="strand" class="form-label">Strand</label>
                                <select class="form-select" id="strand" name="strand">
                                    <option value="">Select</option>
                                    <option value="STEM">STEM</option>
                                    <option value="ABM">ABM</option>
                                    <option value="HUMSS">HUMSS</option>
                                    <option value="GAS">GAS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="form-section-title">Family Information</h4>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="father_last_name" class="form-label">Father's Last Name</label>
                                <input type="text" class="form-control" id="father_last_name" name="father_last_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="father_first_name" class="form-label">Father's First Name</label>
                                <input type="text" class="form-control" id="father_first_name" name="father_first_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="father_middle_name" class="form-label">Father's Middle Name</label>
                                <input type="text" class="form-control" id="father_middle_name" name="father_middle_name">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mother_last_name" class="form-label">Mother's Last Name</label>
                                <input type="text" class="form-control" id="mother_last_name" name="mother_last_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mother_first_name" class="form-label">Mother's First Name</label>
                                <input type="text" class="form-control" id="mother_first_name" name="mother_first_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="mother_middle_name" class="form-label">Mother's Middle Name</label>
                                <input type="text" class="form-control" id="mother_middle_name" name="mother_middle_name">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="form-section-title">Contact Information</h4>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">Submit Application</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Other forms would go here -->
            <div class="scholarship-form" id="presidents-form" style="display: none;">
                <!-- Institutional Scholarship Form -->
            </div>
            
            <div class="scholarship-form" id="employees-form" style="display: none;">
                <!-- Employees Scholar Form -->
            </div>
            
            <div class="scholarship-form" id="private-form" style="display: none;">
                <!-- Private Scholarship Form -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabs = ['ched', 'institutional', 'employees', 'private', 'add-more'];
    const forms = ['ched-form', 'presidents-form', 'employees-form', 'private-form'];
    
    tabs.forEach(tab => {
        const tabElement = document.getElementById(tab + '-tab');
        if (tabElement) {
            tabElement.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => {
                    const el = document.getElementById(t + '-tab');
                    if (el) el.classList.remove('active');
                    if (el) el.style.backgroundColor = '#f8f9fa';
                    if (el) el.style.color = '#333';
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                this.style.backgroundColor = '#1e6641';
                this.style.color = 'white';
                
                // Hide all forms
                forms.forEach(form => {
                    const formElement = document.getElementById(form);
                    if (formElement) formElement.style.display = 'none';
                });
                
                // Show corresponding form
                if (tab === 'ched') {
                    document.getElementById('ched-form').style.display = 'block';
                } else if (tab === 'institutional') {
                    document.getElementById('presidents-form').style.display = 'block';
                } else if (tab === 'employees') {
                    document.getElementById('employees-form').style.display = 'block';
                } else if (tab === 'private') {
                    document.getElementById('private-form').style.display = 'block';
                } else if (tab === 'add-more') {
                    alert('This feature will allow administrators to add custom scholarship forms in the future.');
                }
            });
        }
    });
    
    // Course selection based on department
    const departmentSelect = document.getElementById('department');
    const courseSelect = document.getElementById('course');
    
    const coursesByDepartment = {
        'SITE': ['BS Information Technology', 'Bachelor of Library and Information Science', 'BS Civil Engineering', 'BS Environmental and Sanitary Engineering', 'BS Computer Engineering'],
        'SASTE': ['Bachelor of Arts in English Language Studies', 'Bachelor of Secondary Education', 'BS Psychology', 'BS Biology', 'BS Public Administration', 'Bachelor of Science in Biology Major in MicroBiology', 'BS Social Work', 'Bachelor of Elementary Education', 'Bachelor of Physical Education'],
        'SBAHM': ['BS Accountancy', 'BS Entrepreneurship', 'BS Business Administration', 'BS Management Accounting', 'BS Hospitality Management', 'BS Tourism Management', 'BS Product Design and Marketing Innovation'],
        'SNAMS': ['BS Nursing', 'BS Pharmacy', 'BS Medical Technology', 'BS Radiologic Technology', 'BS Physical Therapy']
    };
    
    if (departmentSelect && courseSelect) {
        departmentSelect.addEventListener('change', function() {
            const department = this.value;
            
            // Clear current options
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            
            // Add new options based on selected department
            if (department && coursesByDepartment[department]) {
                coursesByDepartment[department].forEach(course => {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
            }
        });
    }
    
    // Education stage selection
    const educationStageSelect = document.getElementById('education_stage');
    const yearLevelField = document.querySelector('.college-field');
    const gradeLevelField = document.querySelector('.bsu-field');
    const strandField = document.querySelector('.strand-field');
    
    if (educationStageSelect) {
        educationStageSelect.addEventListener('change', function() {
            if (this.value === 'College') {
                yearLevelField.style.display = 'block';
                gradeLevelField.style.display = 'none';
                strandField.style.display = 'none';
            } else if (this.value === 'BSU') {
                yearLevelField.style.display = 'none';
                gradeLevelField.style.display = 'block';
                
                // Show strand field only for senior high
                const gradeLevelSelect = document.getElementById('grade_level');
                if (gradeLevelSelect) {
                    gradeLevelSelect.addEventListener('change', function() {
                        if (this.value === '11' || this.value === '12') {
                            strandField.style.display = 'block';
                        } else {
                            strandField.style.display = 'none';
                        }
                    });
                }
            }
        });
    }
});
</script>
@endsection