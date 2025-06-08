<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Dashboard - St. Paul University Philippines</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/student/student-dashboard.css') }}">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="university-logo">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo"
                    class="logo-img">
                <div>
                    <h1 class="university-name">St. Paul University Philippines</h1>
                    <p class="office-name">OFFICE OF THE REGISTRAR</p>
                </div>
            </div>
            <a href="{{ route('logout') }}" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>

    <!-- Notification Component -->
    @include('components.notification')

    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                Welcome, Student User!
            </div>
            <div class="user-actions">
                <a href="{{ route('scholarship.tracker') }}" class="action-link">
                    <i class="fas fa-search"></i> Track Application
                </a>
            </div>
        </div>

        <!-- Backend Error Notifications -->
        @if ($errors->has('student_id'))
            <div class="main-screen-duplicate-notification">
                <div class="notification-content">
                    <div class="notification-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="notification-text">
                        <strong>Duplicate Student ID Detected!</strong><br>
                        {{ $errors->first('student_id') }}
                    </div>
                    <button class="notification-close" onclick="removeMainScreenDuplicateNotification()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <!-- Scholarship Opportunities -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <i class="fas fa-graduation-cap"></i> Scholarship Opportunities
                        </div>
                        <div class="card-body">
                            <div class="scholarship-grid">
                                <!-- CHED Scholarship -->
                                <div class="scholarship-card" data-scholarship="ched">
                                    <h3 class="scholarship-title">CHED Scholarship</h3>
                                    <p class="scholarship-description">Government scholarship for qualified students.
                                    </p>
                                    <button class="apply-btn" data-form="ched-form">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </button>
                                </div>

                                <!-- Academic Scholarship -->
                                <div class="scholarship-card" data-scholarship="presidents">
                                    <h3 class="scholarship-title">Academic Scholarship</h3>
                                    <p class="scholarship-description">For students with exceptional academic
                                        performance.
                                    </p>
                                    <button class="apply-btn" data-form="presidents-form">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </button>
                                </div>

                                <!-- Employee's Scholarship -->
                                <div class="scholarship-card" data-scholarship="employees">
                                    <h3 class="scholarship-title">Employee's Scholarship</h3>
                                    <p class="scholarship-description">For children of university employees.</p>
                                    <button class="apply-btn" data-form="employees-form">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </button>
                                </div>

                                <!-- Private Scholarship -->
                                <div class="scholarship-card" data-scholarship="private">
                                    <h3 class="scholarship-title">Private Scholarship</h3>
                                    <p class="scholarship-description">For students with private scholarship
                                        opportunities.</p>
                                    <button class="apply-btn" data-form="private-form">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Application Forms -->
                    <!-- CHED Application Form -->
                    <div class="application-form-container" id="ched-form">
                        <div class="form-header">
                            <div class="application-forms-header">
                                <h3>CHED Scholarship Application</h3>
                            </div>
                            <button class="close-form-btn" onclick="closeForm()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="form-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul style="margin: 0; padding-left: 20px;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('scholarship.submit') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="scholarship_type" value="ched">

                                <!-- Form Title -->
                                <div class="form-title">
                                    <h4>Application Instructions</h4>
                                </div>

                                <!-- Form Description -->
                                <div class="form-description">
                                    <p>Please fill out all required fields marked with an asterisk (*). Ensure all
                                        information is accurate and complete before submitting your application.</p>
                                </div>

                                <div class="form-section-title">Personal Information</div>
                                <div class="form-row">
                                    <div class="form-group student-id-group">
                                        <label for="student_id">Student ID *</label>
                                        <input type="text" id="student_id" name="student_id" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Last Name *</label>
                                        <input type="text" id="last_name" name="last_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="first_name">First Name *</label>
                                        <input type="text" id="first_name" name="first_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="middle_name">Middle Name *</label>
                                        <input type="text" id="middle_name" name="middle_name" required>
                                    </div>
                                </div>

                                <!-- Education Stage, Sex, Birthdate -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Sex</label>
                                        <div class="radio-group-inline">
                                            <label class="radio-option-inline">
                                                <input type="radio" name="sex" value="Male" id="sex_male"
                                                    required>
                                                <span class="radio-label">Male</span>
                                            </label>
                                            <label class="radio-option-inline">
                                                <input type="radio" name="sex" value="Female" id="sex_female"
                                                    required>
                                                <span class="radio-label">Female</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="birthdate">Birthdate *</label>
                                        <input type="date" id="birthdate" name="birthdate" required>
                                    </div>
                                </div>

                                <!-- Academic Information Section -->
                                <div class="form-section-title">Academic Information</div>
                                <div class="form-row">
                                    <div class="form-group full-width">
                                        <label>Education Stage *</label>
                                        <div class="radio-group">
                                            <div class="radio-option">
                                                <input type="radio" id="ched_bsu" name="education_stage"
                                                    value="BEU" required>
                                                <label for="ched_bsu">BEU</label>
                                            </div>
                                            <div class="radio-option">
                                                <input type="radio" id="ched_college" name="education_stage"
                                                    value="College" required>
                                                <label for="ched_college">College</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- BEU Fields (Hidden by default) -->
                                <div class="ched-bsu-fields" style="display: none;">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ched_grade_level">Grade Level *</label>
                                            <select id="ched_grade_level" name="grade_level">
                                                <option value="">Select Grade Level</option>
                                                <option value="Grade 7">Grade 7</option>
                                                <option value="Grade 8">Grade 8</option>
                                                <option value="Grade 9">Grade 9</option>
                                                <option value="Grade 10">Grade 10</option>
                                                <option value="Grade 11">Grade 11</option>
                                                <option value="Grade 12">Grade 12</option>
                                            </select>
                                        </div>
                                        <div class="form-group ched-strand-field" style="display: none;">
                                            <label for="ched_strand">Strand *</label>
                                            <select id="ched_strand" name="strand">
                                                <option value="">Select Strand</option>
                                                <option value="STEM">STEM (Science, Technology, Engineering,
                                                    Mathematics)
                                                </option>
                                                <option value="ABM">ABM (Accountancy, Business, Management)</option>
                                                <option value="HUMSS">HUMSS (Humanities and Social Sciences)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- College Fields (Hidden by default) -->
                                <div class="ched-college-fields" style="display: none;">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ched_department">Department *</label>
                                            <select id="ched_department" name="department">
                                                <option value="">Select Department</option>
                                                <option value="SITE">SITE</option>
                                                <option value="SASTE">SASTE</option>
                                                <option value="SBAHM">SBAHM</option>
                                                <option value="SNAHS">SNAHS</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="ched_course">Course *</label>
                                            <select id="ched_course" name="course">
                                                <option value="">Select Course</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="ched_year_level">Year Level *</label>
                                            <select id="ched_year_level" name="year_level">
                                                <option value="">Select Year Level</option>
                                                <option value="1st Year">1st Year</option>
                                                <option value="2nd Year">2nd Year</option>
                                                <option value="3rd Year">3rd Year</option>
                                                <option value="4th Year">4th Year</option>
                                                <option value="5th Year">5th Year</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Father's Name -->
                                <div class="form-section-title">Father's Name</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="father_last_name">Last name *</label>
                                        <input type="text" id="father_last_name" name="father_last_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="father_first_name">First name *</label>
                                        <input type="text" id="father_first_name" name="father_first_name"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="father_middle_name">Middle name *</label>
                                        <input type="text" id="father_middle_name" name="father_middle_name"
                                            required>
                                    </div>
                                </div>

                                <!-- Mother's Maiden Name -->
                                <div class="form-section-title">Mother's Maiden Name</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="mother_last_name">Last name *</label>
                                        <input type="text" id="mother_last_name" name="mother_last_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="mother_first_name">First name *</label>
                                        <input type="text" id="mother_first_name" name="mother_first_name"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="mother_middle_name">Middle name *</label>
                                        <input type="text" id="mother_middle_name" name="mother_middle_name"
                                            required>
                                    </div>
                                </div>

                                <!-- Permanent Address -->
                                <div class="form-section-title">Permanent Address</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="street">Street *</label>
                                        <input type="text" id="street" name="street" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="barangay">Barangay *</label>
                                        <input type="text" id="barangay" name="barangay" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="city">City *</label>
                                        <input type="text" id="city" name="city" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="province">Province *</label>
                                        <input type="text" id="province" name="province" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="zipcode">Zipcode *</label>
                                        <input type="text" id="zipcode" name="zipcode" required>
                                    </div>
                                </div>

                                <!-- Disability -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="disability">Disability</label>
                                        <div class="disability-field-container">
                                            <select id="disability" name="disability" class="disability-select">
                                                <option value="">Disability</option>
                                                <option value="None">None</option>
                                                <option value="Communication Disability">Communication Disability
                                                </option>
                                                <option value="Disability due to Chronic Illness">Disability due to
                                                    Chronic
                                                    Illness</option>
                                                <option value="Learning Disability">Learning Disability</option>
                                                <option value="Intellectual Disability">Intellectual Disability
                                                </option>
                                                <option value="Orthopedic Disability">Orthopedic Disability</option>
                                                <option value="Mental/Psychological Disability">Mental/Psychological
                                                    Disability</option>
                                                <option value="Visual Disability">Visual Disability</option>
                                            </select>
                                            <div class="disability-info">
                                                <i class="fas fa-info-circle"></i>
                                                <span>Spell out. Possible values <strong>(Communication Disability,
                                                        Disability due to Chronic Illness, Learning Disability,
                                                        Intellectual
                                                        Disability, Orthopedic Disability, Mental/Psychological
                                                        Disability,
                                                        Visual Disability)</strong> [Leave blank if not
                                                    applicable]</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="contact_number">Contact Number *</label>
                                        <input type="number" id="contact_number" name="contact_number" required
                                            maxlength="11" placeholder="09123456789">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email Address *</label>
                                        <input type="email" id="email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="indigenous_people">Indigenous People</label>
                                        <input type="text" id="indigenous_people" name="indigenous"
                                            placeholder="Indigenous People">
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <button type="submit" class="submit-btn">
                                            <i class="fas fa-paper-plane"></i> Submit Application
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Academic Scholarship Application Form -->
                    <div class="application-form-container" id="presidents-form">
                        <div class="form-header">
                            <div class="application-forms-header">
                                <h3>Academic Scholarship Application</h3>
                            </div>
                            <button class="close-form-btn" onclick="closeForm()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="form-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul style="margin: 0; padding-left: 20px;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('scholarship.submit') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="scholarship_type" value="academic">

                                <!-- Form Title -->
                                <div class="form-title">
                                    <h4>Application Instructions</h4>
                                </div>

                                <!-- Form Description -->
                                <div class="form-description">
                                    <p>Please fill out all required fields marked with an asterisk (*). Ensure all
                                        information is accurate and complete before submitting your application.</p>
                                </div>

                                <!-- Academic Scholarship Type Selection -->


                                <div class="form-section-title">Personal Information</div>
                                <div class="form-row">
                                    <div class="form-group student-id-group">
                                        <label for="presidents_student_id">Student ID *</label>
                                        <input type="text" id="presidents_student_id" name="student_id" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="presidents_last_name">Last Name *</label>
                                        <input type="text" id="presidents_last_name" name="last_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="presidents_first_name">First Name *</label>
                                        <input type="text" id="presidents_first_name" name="first_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="presidents_middle_name">Middle Name</label>
                                        <input type="text" id="presidents_middle_name" name="middle_name">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="presidents_department">College/Department *</label>
                                        <select id="presidents_department" name="department" required>
                                            <option value="">Select College/Department</option>
                                            <option value="SITE">School of Information Technology and Engineering
                                                (SITE)
                                            </option>
                                            <option value="SASTE">School of Arts, Sciences and Teacher Education
                                                (SASTE)
                                            </option>
                                            <option value="SBAHM">School of Business Administration and Hospitality
                                                Management (SBAHM)</option>
                                            <option value="SNAHS">School of Nursing and Allied Health Sciences (SNAHS)
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="presidents_course">Course *</label>
                                        <select id="presidents_course" name="course" required>
                                            <option value="">Select Course</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="presidents_year_level">Year Level *</label>
                                        <select id="presidents_year_level" name="year_level" required>
                                            <option value="">Select Year Level</option>
                                            <option value="1st Year">1st Year</option>
                                            <option value="2nd Year">2nd Year</option>
                                            <option value="3rd Year">3rd Year</option>
                                            <option value="4th Year">4th Year</option>
                                            <option value="5th Year">5th Year</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="presidents_semester">Semester *</label>
                                        <select id="presidents_semester" name="semester" required>
                                            <option value="">Select Semester</option>
                                            <option value="1st Semester">1st Semester</option>
                                            <option value="2nd Semester">2nd Semester</option>
                                            <option value="Summer">Summer</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Subjects and Grades Section -->
                                <div class="subjects-section" id="presidents-subjects-section"
                                    style="display: none;">
                                    <div class="form-section-title">Academic Performance - Subjects and Grades</div>
                                    <div class="subjects-container">
                                        <div class="subjects-header">
                                            <div class="subject-code-header">Subject Code & Course Title</div>
                                            <div class="grades-header">Grades</div>
                                            <div class="units-header">Units</div>
                                        </div>
                                        <div class="subjects-list" id="presidents-subjects-list">
                                            <!-- Subjects will be dynamically populated here -->
                                        </div>
                                        <div class="gwa-calculation">
                                            <div class="gwa-row">
                                                <div class="gwa-label">Total Units:</div>
                                                <div class="gwa-value" id="presidents-total-units">0</div>
                                            </div>
                                            <div class="gwa-row">
                                                <div class="gwa-label">Total Grade Points:</div>
                                                <div class="gwa-value" id="presidents-total-grade-points">0.00</div>
                                            </div>
                                            <div class="gwa-row gwa-final">
                                                <div class="gwa-label"><strong>GWA (General Weighted Average):</strong>
                                                </div>
                                                <div class="gwa-value" id="presidents-calculated-gwa">
                                                    <strong>0.00</strong>
                                                </div>
                                            </div>
                                            <div class="gwa-requirements">
                                                <small class="form-help-text">
                                                    <strong>Academic Scholarship Requirements:</strong><br>
                                                    • President's Lister (PL): GWA 1.0 - 1.25<br>
                                                    • Dean's Lister (DL): GWA 1.50
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Year -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="inst_academic_year">Academic Year *</label>
                                        <input type="text" id="inst_academic_year" name="academic_year" required
                                            placeholder="e.g., 2023-2024">
                                    </div>
                                </div>

                                <!-- Hidden GWA field for form submission -->
                                <input type="hidden" id="inst_calculated_gwa" name="gwa" value="0.00">

                                <!-- Contact Information -->
                                <div class="form-section-title">Contact Information</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="inst_contact_number">Contact Number *</label>
                                        <input type="number" id="inst_contact_number" name="contact_number" required
                                            maxlength="11" placeholder="09123456789">
                                    </div>
                                    <div class="form-group">
                                        <label for="inst_email">Email Address *</label>
                                        <input type="email" id="inst_email" name="email" required>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="form-section-title">Address Information</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="inst_street">Street *</label>
                                        <input type="text" id="inst_street" name="street" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="inst_barangay">Barangay *</label>
                                        <input type="text" id="inst_barangay" name="barangay" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="inst_city">City *</label>
                                        <input type="text" id="inst_city" name="city" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="inst_province">Province *</label>
                                        <input type="text" id="inst_province" name="province" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="inst_zipcode">Zipcode *</label>
                                        <input type="text" id="inst_zipcode" name="zipcode" required>
                                    </div>
                                </div>

                                <!-- Document Publication -->
                                <div class="form-section-title">Document Publication</div>
                                <div class="form-row">
                                    <div class="form-group full-width">
                                        <label for="inst_document_upload">Upload Required Documents *</label>
                                        <div class="file-upload-container">
                                            <input type="file" id="inst_document_upload" name="documents[]"
                                                multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                            <div class="file-upload-area">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p>Click to upload or drag and drop files here</p>
                                                <small>Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB per
                                                    file)</small>
                                            </div>
                                            <div class="uploaded-files-list"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <button type="submit" class="submit-btn">
                                            <i class="fas fa-paper-plane"></i> Submit Application
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Employee's Scholarship Application Form -->
                    <div class="application-form-container" id="employees-form">
                        <div class="form-header">
                            <div class="application-forms-header">
                                <h3>Employee's Scholar Application</h3>
                            </div>
                            <button class="close-form-btn" onclick="closeForm()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="form-body">
                            <form action="{{ route('scholarship.submit') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="scholarship_type" value="employees">

                                <!-- Form Title -->
                                <div class="form-title">
                                    <h4>Application Instructions</h4>
                                </div>

                                <!-- Form Description -->
                                <div class="form-description">
                                    <p>Please fill out all required fields marked with an asterisk (*). Ensure all
                                        information is accurate and complete before submitting your application.</p>
                                </div>

                                <div class="form-section-title">Personal Information</div>
                                <div class="form-row">
                                    <div class="form-group student-id-group">
                                        <label for="employees_student_id">Student ID *</label>
                                        <input type="text" id="employees_student_id" name="student_id" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="employees_last_name">Last Name *</label>
                                        <input type="text" id="employees_last_name" name="last_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="employees_first_name">First Name *</label>
                                        <input type="text" id="employees_first_name" name="first_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="employees_middle_name">Middle Name</label>
                                        <input type="text" id="employees_middle_name" name="middle_name">
                                    </div>
                                </div>

                                <div class="form-section-title">Employee Information</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="employee_name">Employee Name *</label>
                                        <input type="text" id="employee_name" name="employee_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="employee_relationship">Relationship to Employee *</label>
                                        <select id="employee_relationship" name="employee_relationship" required>
                                            <option value="">Select Relationship</option>
                                            <option value="Son">Son</option>
                                            <option value="Daughter">Daughter</option>
                                            <option value="Spouse">Spouse</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="employee_department">Employee Department *</label>
                                        <input type="text" id="employee_department" name="employee_department"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label for="employee_position">Employee Position *</label>
                                        <input type="text" id="employee_position" name="employee_position"
                                            required>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="form-section-title">Contact Information</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="employees_contact_number">Contact Number *</label>
                                        <input type="number" id="employees_contact_number" name="contact_number"
                                            required maxlength="11" placeholder="09123456789">
                                    </div>
                                    <div class="form-group">
                                        <label for="employees_email">Email Address *</label>
                                        <input type="email" id="employees_email" name="email" required>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="form-section-title">Address Information</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="employees_street">Street *</label>
                                        <input type="text" id="employees_street" name="street" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="employees_barangay">Barangay *</label>
                                        <input type="text" id="employees_barangay" name="barangay" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="employees_city">City *</label>
                                        <input type="text" id="employees_city" name="city" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="employees_province">Province *</label>
                                        <input type="text" id="employees_province" name="province" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="employees_zipcode">Zipcode *</label>
                                        <input type="text" id="employees_zipcode" name="zipcode" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <button type="submit" class="submit-btn">
                                            <i class="fas fa-paper-plane"></i> Submit Application
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Private Scholarship Application Form -->
                    <div class="application-form-container" id="private-form">
                        <div class="form-header">
                            <div class="application-forms-header">
                                <h3>Private Scholarship Application</h3>
                            </div>
                            <button class="close-form-btn" onclick="closeForm()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="form-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul style="margin: 0; padding-left: 20px;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('scholarship.submit') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="scholarship_type" value="private">

                                <!-- Form Title -->
                                <div class="form-title">
                                    <h4>Application Instructions</h4>
                                </div>

                                <!-- Form Description -->
                                <div class="form-description">
                                    <p>Please fill out all required fields marked with an asterisk (*). Ensure all
                                        information is accurate and complete before submitting your application.</p>
                                </div>

                                <div class="form-section-title">Personal Information</div>
                                <div class="form-row">
                                    <div class="form-group student-id-group">
                                        <label for="private_student_id">Student ID *</label>
                                        <input type="text" id="private_student_id" name="student_id" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="private_last_name">Last Name *</label>
                                        <input type="text" id="private_last_name" name="last_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="private_first_name">First Name *</label>
                                        <input type="text" id="private_first_name" name="first_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="private_middle_name">Middle Name</label>
                                        <input type="text" id="private_middle_name" name="middle_name">
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="private_contact_number">Contact Number *</label>
                                        <input type="number" id="private_contact_number" name="contact_number"
                                            required maxlength="11" placeholder="09123456789">
                                    </div>
                                    <div class="form-group">
                                        <label for="private_email">Email Address *</label>
                                        <input type="email" id="private_email" name="email" required>
                                    </div>
                                </div>

                                <!-- Private Scholarship Information -->
                                <div class="form-section-title">Private Scholarship Information</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="private_scholarship_name">Scholarship Name *</label>
                                        <input type="text" id="private_scholarship_name" name="scholarship_name"
                                            required placeholder="Name of the private scholarship program">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="private_other_scholarship">Other Scholarship Details</label>
                                        <textarea id="private_other_scholarship" name="other_scholarship" rows="3"
                                            placeholder="Additional details about the scholarship program, requirements, or conditions"></textarea>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="form-section-title">Address Information</div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="private_street">Street *</label>
                                        <input type="text" id="private_street" name="street" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="private_barangay">Barangay *</label>
                                        <input type="text" id="private_barangay" name="barangay" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="private_city">City *</label>
                                        <input type="text" id="private_city" name="city" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="private_province">Province *</label>
                                        <input type="text" id="private_province" name="province" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="private_zipcode">Zipcode *</label>
                                        <input type="text" id="private_zipcode" name="zipcode" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <button type="submit" class="submit-btn">
                                            <i class="fas fa-paper-plane"></i> Submit Application
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <div class="col-lg-4">


                    <!-- Announcements -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <i class="fas fa-bullhorn"></i> Announcements
                        </div>
                        <div class="card-body">
                            <div class="announcements-container">
                                @if (isset($announcements) && $announcements->count() > 0)
                                    @foreach ($announcements as $announcement)
                                        <div class="announcement-item">
                                            <div class="announcement-header">
                                                <h5 class="announcement-title">{{ $announcement->title }}</h5>
                                                <span class="announcement-date">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    {{ $announcement->created_at->format('M d, Y') }}
                                                </span>
                                            </div>
                                            <div class="announcement-content">
                                                <p>{{ $announcement->content }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="no-announcements">
                                        <div class="no-announcements-icon">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <p class="no-announcements-text">No announcements at this time.</p>
                                        <small class="no-announcements-subtitle">Check back later for updates from the
                                            administration.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <i class="fas fa-question-circle"></i> FAQ's
                        </div>
                        <div class="card-body">
                            <div class="faq-container">
                                <div class="faq-item">
                                    <div class="faq-question" data-toggle="collapse" data-target="#faq1">
                                        <i class="fas fa-chevron-right faq-icon"></i>
                                        <span>How do I apply for a scholarship?</span>
                                    </div>
                                    <div class="faq-answer collapse" id="faq1">
                                        <p>To apply for a scholarship, click on the "Apply Now" button for your desired
                                            scholarship type, fill out the required information, and submit your
                                            application.</p>
                                    </div>
                                </div>

                                <div class="faq-item">
                                    <div class="faq-question" data-toggle="collapse" data-target="#faq2">
                                        <i class="fas fa-chevron-right faq-icon"></i>
                                        <span>What documents do I need to submit?</span>
                                    </div>
                                    <div class="faq-answer collapse" id="faq2">
                                        <p>Required documents vary by scholarship type but typically include academic
                                            transcripts, proof of enrollment, and supporting documents as specified in
                                            each application form.</p>
                                    </div>
                                </div>

                                <div class="faq-item">
                                    <div class="faq-question" data-toggle="collapse" data-target="#faq3">
                                        <i class="fas fa-chevron-right faq-icon"></i>
                                        <span>How can I track my application status?</span>
                                    </div>
                                    <div class="faq-answer collapse" id="faq3">
                                        <p>You can track your application status by clicking the "Track Application"
                                            button at the top of this page or visiting the application tracker.</p>
                                    </div>
                                </div>

                                <div class="faq-item">
                                    <div class="faq-question" data-toggle="collapse" data-target="#faq4">
                                        <i class="fas fa-chevron-right faq-icon"></i>
                                        <span>When will I know if my application is approved?</span>
                                    </div>
                                    <div class="faq-answer collapse" id="faq4">
                                        <p>Application processing times vary by scholarship type. You will be notified
                                            via email and can check your application status through the tracker.</p>
                                    </div>
                                </div>

                                <div class="faq-item">
                                    <div class="faq-question" data-toggle="collapse" data-target="#faq5">
                                        <i class="fas fa-chevron-right faq-icon"></i>
                                        <span>Can I apply for multiple scholarships?</span>
                                    </div>
                                    <div class="faq-answer collapse" id="faq5">
                                        <p>Yes, you may apply for multiple scholarships as long as you meet the
                                            eligibility requirements for each program.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to cards
            const cards = document.querySelectorAll('.dashboard-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });

            // Add click event to apply buttons
            const applyButtons = document.querySelectorAll('.apply-btn');
            applyButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const formId = this.getAttribute('data-form');
                    const scholarshipCard = this.closest('.scholarship-card');

                    // Remove active class from all cards
                    document.querySelectorAll('.scholarship-card').forEach(card => {
                        card.classList.remove('active');
                    });

                    // Hide all forms
                    document.querySelectorAll('.application-form-container').forEach(form => {
                        form.classList.remove('active');
                    });

                    // Show selected form
                    const targetForm = document.getElementById(formId);
                    if (targetForm) {
                        scholarshipCard.classList.add('active');
                        targetForm.classList.add('active');

                        // Smooth scroll to form
                        setTimeout(() => {
                            targetForm.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 100);
                    }
                });
            });

            // Initialize CHED form functionality
            initializeCHEDForm();

            // Initialize President's form functionality
            initializePresidentsForm();

            // Initialize Institutional form functionality
            initializeInstitutionalForm();

            // Initialize file upload functionality
            initializeFileUploads();

            // Initialize form validation
            initializeFormValidation();

            // Initialize duplicate ID prevention
            initializeDuplicateIDPrevention();

            // Initialize tab functionality
            initializeTabFunctionality();

            // Initialize FAQ functionality
            initializeFAQ();

            // Check for backend duplicate notifications and scroll to them
            const backendNotification = document.querySelector('.main-screen-duplicate-notification');
            if (backendNotification) {
                setTimeout(() => {
                    backendNotification.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 500);
            }

        });

        // Tab Functionality
        function initializeTabFunctionality() {
            const tabButtons = document.querySelectorAll('.tab-btn:not(.add-more)');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all tabs
                    tabButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to clicked tab
                    this.classList.add('active');

                    // Hide all forms
                    document.querySelectorAll('.application-form-container').forEach(form => {
                        form.classList.remove('active');
                    });

                    // Show target form
                    const targetForm = document.getElementById(targetTab + '-form');
                    if (targetForm) {
                        targetForm.classList.add('active');
                    }
                });
            });
        }

        // CHED Form Dynamic Functionality
        function initializeCHEDForm() {
            // Academic Information card-style radio button handling
            const academicEducationRadios = document.querySelectorAll('#ched-form input[name="education_stage"]');
            const chedBsuFields = document.querySelectorAll('.ched-bsu-fields');
            const chedCollegeFields = document.querySelectorAll('.ched-college-fields');

            academicEducationRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Show/hide appropriate fields based on selection
                    if (this.value === 'BEU') {
                        // Show BEU fields, hide college fields
                        chedBsuFields.forEach(field => {
                            field.style.display = 'block';
                            // Make BEU fields required
                            const selects = field.querySelectorAll('select');
                            selects.forEach(select => select.required = true);
                        });
                        chedCollegeFields.forEach(field => {
                            field.style.display = 'none';
                            // Remove required from college fields
                            const selects = field.querySelectorAll('select');
                            selects.forEach(select => {
                                select.required = false;
                                select.value = '';
                            });
                        });
                    } else if (this.value === 'College') {
                        // Show college fields, hide BEU fields
                        chedCollegeFields.forEach(field => {
                            field.style.display = 'block';
                            // Make college fields required
                            const selects = field.querySelectorAll('select');
                            selects.forEach(select => select.required = true);
                        });
                        chedBsuFields.forEach(field => {
                            field.style.display = 'none';
                            // Remove required from BEU fields
                            const selects = field.querySelectorAll('select');
                            selects.forEach(select => {
                                select.required = false;
                                select.value = '';
                            });
                        });
                    }
                });
            });

            // Department to Course mapping
            const departmentCourses = {
                'SITE': [
                    'Bachelor of Science in Information Technology',
                    'Bachelor of Library and Information Science',
                    'Bachelor of Science in Civil Engineering',
                    'Bachelor of Science in Environmental and Sanitary Engineering',
                    'Bachelor of Science in Computer Engineering'
                ],
                'SASTE': [
                    'Bachelor of Arts in English Language Studies',
                    'Bachelor of Science in Psychology',
                    'Bachelor of Science in Biology',
                    'Bachelor of Science in Social Work',
                    'Bachelor of Science in Public Administration',
                    'Bachelor of Science in Biology Major in Microbiology',
                    'Bachelor of Secondary Education',
                    'Bachelor of Elementary Education',
                    'Bachelor of Physical Education'
                ],
                'SBAHM': [
                    'Bachelor of Science in Accountancy',
                    'Bachelor of Science in Entrepreneurship',
                    'Bachelor of Science in Business Administration major in: Marketing Management, Financial Management and Operations Management',
                    'Bachelor of Science in Management Accounting',
                    'Bachelor of Science in Hospitality Management',
                    'Bachelor of Science in Tourism Management',
                    'Bachelor of Science in Product Design and Marketing Innovation'
                ],
                'SNAHS': [
                    'Bachelor of Science in Nursing',
                    'Bachelor of Science in Pharmacy',
                    'Bachelor of Science in Medical Technology',
                    'Bachelor of Science in Physical Therapy',
                    'Bachelor of Science in Radiologic Technology',
                    'Bachelor of Science in Midwifery'
                ]
            };

            // Grade level change handler for CHED form
            const chedGradeLevelSelect = document.getElementById('ched_grade_level');
            const chedStrandField = document.querySelector('.ched-strand-field');
            const chedStrandSelect = document.getElementById('ched_strand');

            if (chedGradeLevelSelect && chedStrandField && chedStrandSelect) {
                chedGradeLevelSelect.addEventListener('change', function() {
                    const selectedGrade = this.value;

                    if (selectedGrade === 'Grade 11' || selectedGrade === 'Grade 12') {
                        // Show strand field for Grade 11 and 12
                        chedStrandField.style.display = 'block';
                        chedStrandSelect.required = true;
                    } else {
                        // Hide strand field for other grades
                        chedStrandField.style.display = 'none';
                        chedStrandSelect.required = false;
                        chedStrandSelect.value = ''; // Clear selection
                    }
                });
            }

            // Department change handler for CHED form
            const chedDepartmentSelect = document.getElementById('ched_department');
            const chedCourseSelect = document.getElementById('ched_course');

            if (chedDepartmentSelect && chedCourseSelect) {
                chedDepartmentSelect.addEventListener('change', function() {
                    const selectedDepartment = this.value;
                    const courses = departmentCourses[selectedDepartment] || [];

                    // Clear existing options
                    chedCourseSelect.innerHTML = '<option value="">Select Course</option>';

                    // Add new options
                    courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course;
                        option.textContent = course;
                        chedCourseSelect.appendChild(option);
                    });
                });
            }
        }

        // Presidents Form Dynamic Functionality
        function initializePresidentsForm() {
            const departmentCourses = {
                'SITE': [
                    'Bachelor of Science in Information Technology',
                    'Bachelor of Library and Information Science',
                    'Bachelor of Science in Civil Engineering',
                    'Bachelor of Science in Environmental and Sanitary Engineering',
                    'Bachelor of Science in Computer Engineering'
                ],
                'SASTE': [
                    'Bachelor of Arts in English Language Studies',
                    'Bachelor of Science in Psychology',
                    'Bachelor of Science in Biology',
                    'Bachelor of Science in Social Work',
                    'Bachelor of Science in Public Administration',
                    'Bachelor of Science in Biology Major in Microbiology',
                    'Bachelor of Secondary Education',
                    'Bachelor of Elementary Education',
                    'Bachelor of Physical Education'
                ],
                'SBAHM': [
                    'Bachelor of Science in Accountancy',
                    'Bachelor of Science in Entrepreneurship',
                    'Bachelor of Science in Business Administration major in: Marketing Management, Financial Management and Operations Management',
                    'Bachelor of Science in Management Accounting',
                    'Bachelor of Science in Hospitality Management',
                    'Bachelor of Science in Tourism Management',
                    'Bachelor of Science in Product Design and Marketing Innovation'
                ],
                'SNAHS': [
                    'Bachelor of Science in Nursing',
                    'Bachelor of Science in Pharmacy',
                    'Bachelor of Science in Medical Technology',
                    'Bachelor of Science in Physical Therapy',
                    'Bachelor of Science in Radiologic Technology',
                    'Bachelor of Science in Midwifery'
                ]
            };

            // Department change handler for Presidents form
            const presidentsDepartmentSelect = document.getElementById('presidents_department');
            const presidentsCourseSelect = document.getElementById('presidents_course');

            if (presidentsDepartmentSelect && presidentsCourseSelect) {
                presidentsDepartmentSelect.addEventListener('change', function() {
                    const selectedDepartment = this.value;
                    const courses = departmentCourses[selectedDepartment] || [];

                    // Clear existing options
                    presidentsCourseSelect.innerHTML = '<option value="">Select Course</option>';

                    // Add new options
                    courses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course;
                        option.textContent = course;
                        presidentsCourseSelect.appendChild(option);
                    });
                });
            }

            // Show subjects section when all required fields are filled
            const presidentsYearLevelSelect = document.getElementById('presidents_year_level');
            const presidentsSemesterSelect = document.getElementById('presidents_semester');

            function checkPresidentsFormCompletion() {
                const selectedCourse = presidentsCourseSelect.value;
                const selectedYearLevel = presidentsYearLevelSelect.value;
                const selectedSemester = presidentsSemesterSelect.value;

                if (selectedCourse && selectedSemester && selectedYearLevel) {
                    // Create semester key based on year level and semester
                    let semesterKey = selectedSemester;

                    if (selectedYearLevel === '2nd Year') {
                        semesterKey = selectedSemester + ' (2nd Year)';
                    } else if (selectedYearLevel === '3rd Year') {
                        semesterKey = selectedSemester + ' (3rd Year)';
                    } else if (selectedYearLevel === '4th Year') {
                        semesterKey = selectedSemester + ' (4th Year)';
                    } else if (selectedYearLevel === '5th Year') {
                        semesterKey = selectedSemester + ' (5th Year)';
                    }
                    // For 1st Year, keep the original semester name

                    // Load subjects from API
                    loadSubjectsFromAPI(selectedCourse, selectedYearLevel, selectedSemester);
                } else {
                    hideSubjectsSection();
                }
            }

            if (presidentsCourseSelect) {
                presidentsCourseSelect.addEventListener('change', checkPresidentsFormCompletion);
            }
            if (presidentsYearLevelSelect) {
                presidentsYearLevelSelect.addEventListener('change', checkPresidentsFormCompletion);
            }
            if (presidentsSemesterSelect) {
                presidentsSemesterSelect.addEventListener('change', checkPresidentsFormCompletion);
            }
        }

        // Load subjects from API
        function loadSubjectsFromAPI(course, yearLevel, semester) {
            // Show loading state
            const subjectsSection = document.getElementById('presidents-subjects-section');
            const subjectsList = document.getElementById('presidents-subjects-list');

            if (!subjectsSection || !subjectsList) return;

            subjectsSection.style.display = 'block';
            subjectsList.innerHTML = '<div class="loading">Loading subjects...</div>';

            // Make API call to get subjects
            fetch(
                    `/api/subjects?course=${encodeURIComponent(course)}&year_level=${encodeURIComponent(yearLevel)}&semester=${encodeURIComponent(semester)}`
                )
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.subjects) {
                        displaySubjects(data.subjects);
                    } else {
                        subjectsList.innerHTML =
                            '<div class="error">No subjects found for the selected criteria.</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                    subjectsList.innerHTML = '<div class="error">Error loading subjects. Please try again.</div>';
                });
        }

        // Display subjects in the form
        function displaySubjects(subjects) {
            const subjectsList = document.getElementById('presidents-subjects-list');
            if (!subjectsList) return;

            // Clear existing content
            subjectsList.innerHTML = '';

            // Add subjects
            subjects.forEach((subject, index) => {
                const subjectRow = document.createElement('div');
                subjectRow.className = 'subject-row';
                subjectRow.innerHTML = `
                    <div class="subject-info">
                        <div class="subject-code">${subject.code}</div>
                        <div class="subject-title">${subject.title}</div>
                    </div>
                    <div class="subject-grade">
                        <input type="number"
                               id="grade_${index}"
                               name="grades[${subject.code}]"
                               min="1.00"
                               max="5.00"
                               step="0.01"
                               placeholder="0.00"
                               data-units="${subject.units}"
                               onchange="calculateGWA()">
                    </div>
                    <div class="subject-units">${subject.units}</div>
                `;
                subjectsList.appendChild(subjectRow);
            });

            // Initialize GWA calculation
            calculateGWA();
        }

        // Hide subjects section
        function hideSubjectsSection() {
            const subjectsSection = document.getElementById('presidents-subjects-section');
            if (subjectsSection) {
                subjectsSection.style.display = 'none';
            }
        }

        // Calculate GWA
        function calculateGWA() {
            const gradeInputs = document.querySelectorAll('#presidents-subjects-list input[type="number"]');
            let totalUnits = 0;
            let totalGradePoints = 0;

            gradeInputs.forEach(input => {
                const grade = parseFloat(input.value) || 0;
                const units = parseFloat(input.getAttribute('data-units')) || 0;

                if (grade > 0 && units > 0) {
                    totalUnits += units;
                    totalGradePoints += (grade * units);
                }
            });

            const gwa = totalUnits > 0 ? (totalGradePoints / totalUnits) : 0;

            // Update display
            document.getElementById('presidents-total-units').textContent = totalUnits;
            document.getElementById('presidents-total-grade-points').textContent = totalGradePoints.toFixed(2);
            document.getElementById('presidents-calculated-gwa').innerHTML = `<strong>${gwa.toFixed(2)}</strong>`;

            // Update hidden field for form submission
            const hiddenGwaField = document.getElementById('inst_calculated_gwa');
            if (hiddenGwaField) {
                hiddenGwaField.value = gwa.toFixed(2);
            }
        }

        // Institutional Form (same as Presidents)
        function initializeInstitutionalForm() {
            // Same functionality as Presidents form
            initializePresidentsForm();
        }

        // File Upload Functionality
        function initializeFileUploads() {
            const fileInputs = document.querySelectorAll('input[type="file"]');

            fileInputs.forEach(input => {
                const container = input.closest('.file-upload-container');
                const uploadArea = container.querySelector('.file-upload-area');
                const filesList = container.querySelector('.uploaded-files-list');

                // Click to upload
                uploadArea.addEventListener('click', () => {
                    input.click();
                });

                // Drag and drop
                uploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    uploadArea.classList.add('dragover');
                });

                uploadArea.addEventListener('dragleave', () => {
                    uploadArea.classList.remove('dragover');
                });

                uploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    uploadArea.classList.remove('dragover');
                    input.files = e.dataTransfer.files;
                    displayUploadedFiles(input, filesList);
                });

                // File selection
                input.addEventListener('change', () => {
                    displayUploadedFiles(input, filesList);
                });
            });
        }

        // Display uploaded files
        function displayUploadedFiles(input, filesList) {
            filesList.innerHTML = '';

            Array.from(input.files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'uploaded-file-item';
                fileItem.innerHTML = `
                    <i class="fas fa-file"></i>
                    <span class="file-name">${file.name}</span>
                    <span class="file-size">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                    <button type="button" class="remove-file-btn" onclick="removeFile(${index}, this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                filesList.appendChild(fileItem);
            });
        }

        // Remove uploaded file
        function removeFile(index, button) {
            const container = button.closest('.file-upload-container');
            const input = container.querySelector('input[type="file"]');
            const filesList = container.querySelector('.uploaded-files-list');

            // Create new FileList without the removed file
            const dt = new DataTransfer();
            Array.from(input.files).forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            input.files = dt.files;

            // Update display
            displayUploadedFiles(input, filesList);
        }

        // Form Validation
        function initializeFormValidation() {
            const forms = document.querySelectorAll('form[action*="scholarship.submit"]');

            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    console.log('Form submission attempted');

                    // Log which scholarship type is being submitted
                    const scholarshipType = this.querySelector('input[name="scholarship_type"]');
                    console.log('Scholarship type:', scholarshipType ? scholarshipType.value : 'NOT FOUND');

                    // Log all form data
                    const formData = new FormData(this);
                    console.log('Form data:');
                    for (let [key, value] of formData.entries()) {
                        console.log(`  ${key}: ${value}`);
                    }

                    const isValid = validateForm(this);
                    console.log('Form validation result:', isValid);

                    if (!isValid) {
                        console.log('Form validation failed, preventing submission');
                        e.preventDefault();
                        return false;
                    }

                    console.log('Form validation passed, allowing submission');
                });

                // Real-time validation for required fields
                const requiredInputs = form.querySelectorAll('input[required], select[required]');
                requiredInputs.forEach(input => {
                    input.addEventListener('blur', function() {
                        validateField(this);
                    });

                    input.addEventListener('input', function() {
                        clearFieldError(this);
                    });
                });
            });
        }

        // Validate entire form
        function validateForm(form) {
            console.log('Validating form...');
            let isValid = true;
            const requiredFields = form.querySelectorAll('input[required], select[required]');
            console.log('Found required fields:', requiredFields.length);

            requiredFields.forEach(field => {
                const fieldValid = validateField(field);
                console.log(
                    `Field ${field.name || field.id}: ${fieldValid ? 'valid' : 'invalid'} (value: "${field.value}")`
                );
                if (!fieldValid) {
                    isValid = false;
                }
            });

            // Validate email format
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    showFieldError(field, 'Please enter a valid email address');
                    isValid = false;
                }
            });

            // Validate contact number
            const contactFields = form.querySelectorAll('input[name="contact_number"]');
            contactFields.forEach(field => {
                if (field.value) {
                    const contactNumber = field.value.toString();
                    if (!/^\d+$/.test(contactNumber)) {
                        showFieldError(field, 'Contact number must contain only numbers');
                        isValid = false;
                    } else if (contactNumber.length > 11) {
                        showFieldError(field, 'Contact number must not exceed 11 digits');
                        isValid = false;
                    } else if (contactNumber.length < 1) {
                        showFieldError(field, 'Contact number is required');
                        isValid = false;
                    }
                }
            });

            // Validate GWA if present
            const gwaField = form.querySelector('input[name="gwa"]');
            if (gwaField && gwaField.value) {
                const gwa = parseFloat(gwaField.value);
                if (gwa < 1.0 || gwa > 5.0) {
                    showFieldError(gwaField, 'GWA must be between 1.0 and 5.0');
                    isValid = false;
                }
            }

            // Check for duplicate Student ID
            const studentIdField = form.querySelector('input[name="student_id"]');
            if (studentIdField && studentIdField.getAttribute('data-duplicate') === 'true') {
                showFieldError(studentIdField, 'This Student ID has already been used. Please check the notification above.');

                // Scroll to the main screen notification
                const notification = document.querySelector('.main-screen-duplicate-notification');
                if (notification) {
                    notification.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                isValid = false;
            }

            console.log('Final validation result:', isValid);
            return isValid;
        }

        // Validate individual field
        function validateField(field) {
            if (field.hasAttribute('required') && !field.value.trim()) {
                showFieldError(field, 'This field is required');
                return false;
            }

            clearFieldError(field);
            return true;
        }

        // Show field error
        function showFieldError(field, message) {
            clearFieldError(field);

            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

            field.parentNode.appendChild(errorDiv);
            field.classList.add('error');
        }

        // Clear field error
        function clearFieldError(field) {
            const existingError = field.parentNode.querySelector('.field-error');
            if (existingError) {
                existingError.remove();
            }
            field.classList.remove('error');
        }

        // Email validation
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Duplicate ID Prevention (Laravel-based)
        function initializeDuplicateIDPrevention() {
            const studentIdInputs = document.querySelectorAll('input[name="student_id"]');

            studentIdInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    const studentId = this.value.trim();
                    if (studentId) {
                        checkDuplicateStudentId(studentId, this);
                    }
                });
            });
        }

        // Check for duplicate student ID using Laravel route
        function checkDuplicateStudentId(studentId, inputElement) {
            // Create a temporary form to submit via Laravel
            const tempForm = document.createElement('form');
            tempForm.style.display = 'none';
            tempForm.method = 'POST';
            tempForm.action = '{{ route('student.check-duplicate') }}';

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            tempForm.appendChild(csrfInput);

            // Add student ID
            const studentIdInput = document.createElement('input');
            studentIdInput.type = 'hidden';
            studentIdInput.name = 'student_id';
            studentIdInput.value = studentId;
            tempForm.appendChild(studentIdInput);

            document.body.appendChild(tempForm);

            // Use fetch to submit form data
            const formData = new FormData(tempForm);

            fetch(tempForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        showDuplicateWarning(inputElement, data);
                    } else {
                        removeDuplicateWarning(inputElement);
                    }
                })
                .catch(error => {
                    console.error('Error checking duplicate student ID:', error);
                })
                .finally(() => {
                    document.body.removeChild(tempForm);
                });
        }

        // Show duplicate warning on main screen (enhanced for all scholarship types)
        function showDuplicateWarning(inputElement, data) {
            removeDuplicateWarning(inputElement);

            // Show main screen notification
            showMainScreenDuplicateNotification(data);

            // Still mark the input field as having duplicate
            inputElement.classList.add('duplicate-error');

            // Prevent form submission
            inputElement.setAttribute('data-duplicate', 'true');
        }

        // Show main screen duplicate notification
        function showMainScreenDuplicateNotification(data) {
            // Remove any existing main screen notifications
            removeMainScreenDuplicateNotification();

            // Create main screen notification
            const notification = document.createElement('div');
            notification.className = 'main-screen-duplicate-notification';
            notification.innerHTML = `
                <div class="notification-content">
                    <div class="notification-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="notification-text">
                        <strong>Duplicate Student ID Detected!</strong><br>
                        This Student ID has already been used for a <strong>${data.scholarship_type}</strong> ${data.record_type || 'application'} on ${data.application_date}.<br>
                        Status: <span class="status-badge">${data.status}</span><br>
                        ${data.found_in === 'grantees' ? '<span class="grantee-notice">This student is already an approved scholarship grantee.</span><br>' : ''}
                        <span class="warning-note">Each student can only submit ONE scholarship application. Multiple applications with the same Student ID are not allowed.</span>
                    </div>
                    <button class="notification-close" onclick="removeMainScreenDuplicateNotification()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            // Insert notification at the top of the main content area
            const mainContent = document.querySelector('.main-content') || document.querySelector('.container') || document.body;
            mainContent.insertBefore(notification, mainContent.firstChild);

            // Auto-scroll to show the notification
            notification.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Remove main screen duplicate notification
        function removeMainScreenDuplicateNotification() {
            const existingNotification = document.querySelector('.main-screen-duplicate-notification');
            if (existingNotification) {
                existingNotification.remove();
            }
        }

        // Remove duplicate warning
        function removeDuplicateWarning(inputElement) {
            const existingWarning = inputElement.parentNode.querySelector('.duplicate-warning');
            if (existingWarning) {
                existingWarning.remove();
            }

            // Also remove main screen notification
            removeMainScreenDuplicateNotification();

            inputElement.classList.remove('duplicate-error');
            inputElement.removeAttribute('data-duplicate');
        }



        // Close form function
        function closeForm() {
            // Remove active class from all forms
            document.querySelectorAll('.application-form-container').forEach(form => {
                form.classList.remove('active');
            });

            // Remove active class from all scholarship cards
            document.querySelectorAll('.scholarship-card').forEach(card => {
                card.classList.remove('active');
            });

            // Scroll back to top of scholarship opportunities
            document.querySelector('.scholarship-grid').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // FAQ Functionality
        function initializeFAQ() {
            const faqQuestions = document.querySelectorAll('.faq-question');

            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetAnswer = document.querySelector(targetId);
                    const icon = this.querySelector('.faq-icon');

                    if (targetAnswer) {
                        // Toggle the answer
                        if (targetAnswer.classList.contains('show')) {
                            targetAnswer.classList.remove('show');
                            this.setAttribute('aria-expanded', 'false');
                        } else {
                            // Close all other answers
                            document.querySelectorAll('.faq-answer').forEach(answer => {
                                answer.classList.remove('show');
                            });
                            document.querySelectorAll('.faq-question').forEach(q => {
                                q.setAttribute('aria-expanded', 'false');
                            });

                            // Open this answer
                            targetAnswer.classList.add('show');
                            this.setAttribute('aria-expanded', 'true');
                        }
                    }
                });
            });
        }
    </script>
</body>

</html>
