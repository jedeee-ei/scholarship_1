@extends('layouts.student')

@section('title', 'Student Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/student/student-dashboard.css') }}">
@endpush

@section('content')
    <div class="page-container">

        <div class="container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-text">
                    <div class="welcome-greeting">Welcome, {{ $student->full_name ?? ($student->name ?? 'Student') }}!</div>
                    @if ($student->student_id)
                        <div class="student-id-display">Student ID: {{ $student->student_id }}</div>
                    @endif
                </div>
                <div class="user-actions">
                    @if ($applications && $applications->count() > 0)
                        <a href="{{ route('scholarship.tracker', ['id' => $applications->first()->application_id]) }}"
                            class="action-link">
                            <i class="fas fa-search"></i> Track Application
                        </a>
                    @else
                        <a href="{{ route('scholarship.tracker') }}" class="action-link">
                            <i class="fas fa-search"></i> Track Application
                        </a>
                    @endif
                    <a href="#" class="action-link notification-link" onclick="showAnnouncementsModal(); return false;">
                        <i class="fas fa-bell"></i> Notifications
                        @if (isset($announcements) && $announcements->count() > 0)
                            <span class="notification-badge" id="notificationBadge">{{ $announcements->count() }}</span>
                        @endif
                    </a>
                    <a href="#" class="action-link" onclick="showSettingsModal(); return false;">
                        <i class="fas fa-cog"></i> Settings
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

            <!-- Backend Grade Error Notifications -->
            @if ($errors->has('grades') || $errors->has('gwa'))
                <div class="main-screen-grade-disqualification-notification">
                    <div class="notification-content">
                        <div class="notification-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="notification-text">
                            <strong>Academic Scholarship Application Blocked!</strong><br>
                            @if ($errors->has('grades'))
                                {{ $errors->first('grades') }}<br>
                            @endif
                            @if ($errors->has('gwa'))
                                {{ $errors->first('gwa') }}<br>
                            @endif
                            Please review your grades and ensure they meet the requirements before attempting to apply.
                        </div>
                        <button class="notification-close" onclick="removeMainScreenGradeDisqualificationNotification()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Grade Disqualification Notification Placeholder -->
            <div id="grade-disqualification-notification-placeholder"></div>

            <!-- Permanent Status Notification -->
            @if ($permanentStatus)
                <div class="permanent-status-notification {{ $permanentStatus->status === 'Rejected' ? 'rejected' : '' }}">
                    <div class="notification-content">
                        <div class="notification-icon">
                            @if ($permanentStatus->status === 'Approved')
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas fa-times"></i>
                            @endif
                        </div>
                        <div class="notification-text">
                            <span class="status-title">
                                @if ($permanentStatus->status === 'Approved')
                                    Scholarship Approved
                                @else
                                    Application Rejected
                                @endif
                            </span>
                            <div class="status-details">
                                @if ($permanentStatus->status === 'Approved')
                                    Your {{ ucfirst($permanentStatus->scholarship_type) }} scholarship application has been approved.
                                    @if ($permanentStatus->scholarship_subtype)
                                        Awarded: {{ $permanentStatus->scholarship_subtype }} Scholarship
                                    @endif
                                @else
                                    Your {{ ucfirst($permanentStatus->scholarship_type) }} scholarship application was not approved.
                                @endif
                            </div>
                            <div class="status-info">
                                <div class="info-item">
                                    <i class="fas fa-hashtag"></i>
                                    <span>{{ $permanentStatus->application_id }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ \Carbon\Carbon::parse($permanentStatus->updated_at)->format('M d, Y') }}</span>
                                </div>
                                @if ($permanentStatus->status === 'Approved')
                                    <div class="info-item">
                                        <i class="fas fa-user-graduate"></i>
                                        <span>Active Scholar</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="notification-badge">
                            @if ($permanentStatus->status === 'Approved')
                                APPROVED
                            @else
                                REJECTED
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Applications Closed Notification -->
            @if(!$applicationsOpen)
                <div class="applications-closed-notification">
                    <div class="notification-content">
                        <div class="notification-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="notification-text">
                            <strong>Applications for this semester are closed.</strong><br>
                            Please check back later or contact the Registrars' Office for more information.
                        </div>
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
                                    <!-- Government Scholarship -->
                                    <div class="scholarship-card" data-scholarship="government">
                                        <h3 class="scholarship-title">Government Scholarship</h3>
                                        <p class="scholarship-description">Government scholarship for qualified students.
                                        </p>
                                        @if($applicationsOpen && $canApplyForScholarship)
                                            <button class="apply-btn" data-form="government-form">
                                                <i class="fas fa-paper-plane"></i> Apply Now
                                            </button>
                                        @else
                                            <button class="apply-btn disabled" disabled>
                                                <i class="fas fa-lock"></i>
                                                @if(!$applicationsOpen)
                                                    Applications Closed
                                                @else
                                                    Cannot Apply
                                                @endif
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Academic Scholarship -->
                                    <div class="scholarship-card" data-scholarship="academic">
                                        <h3 class="scholarship-title">Academic Scholarship</h3>
                                        <p class="scholarship-description">For students with exceptional academic
                                            performance.
                                        </p>
                                        @if($applicationsOpen && $canApplyForScholarship)
                                            <button class="apply-btn" data-form="academic-form">
                                                <i class="fas fa-paper-plane"></i> Apply Now
                                            </button>
                                        @else
                                            <button class="apply-btn disabled" disabled>
                                                <i class="fas fa-lock"></i>
                                                @if(!$applicationsOpen)
                                                    Applications Closed
                                                @else
                                                    Cannot Apply
                                                @endif
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Employee's Scholarship -->
                                    <div class="scholarship-card" data-scholarship="employees">
                                        <h3 class="scholarship-title">Employee's Scholarship</h3>
                                        <p class="scholarship-description">For children of university employees.</p>
                                        @if($applicationsOpen && $canApplyForScholarship)
                                            <button class="apply-btn" data-form="employees-form">
                                                <i class="fas fa-paper-plane"></i> Apply Now
                                            </button>
                                        @else
                                            <button class="apply-btn disabled" disabled>
                                                <i class="fas fa-lock"></i>
                                                @if(!$applicationsOpen)
                                                    Applications Closed
                                                @else
                                                    Cannot Apply
                                                @endif
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Alumni Scholarship -->
                                    <div class="scholarship-card" data-scholarship="alumni">
                                        <h3 class="scholarship-title">Alumni Scholarship</h3>
                                        <p class="scholarship-description">For students with alumni scholarship
                                            opportunities.</p>
                                        @if($applicationsOpen && $canApplyForScholarship)
                                            <button class="apply-btn" data-form="alumni-form">
                                                <i class="fas fa-paper-plane"></i> Apply Now
                                            </button>
                                        @else
                                            <button class="apply-btn disabled" disabled>
                                                <i class="fas fa-lock"></i>
                                                @if(!$applicationsOpen)
                                                    Applications Closed
                                                @else
                                                    Cannot Apply
                                                @endif
                                            </button>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Application Forms -->
                        <!-- Government Application Form -->
                        <div class="application-form-container" id="government-form">
                            <div class="form-header">
                                <div class="application-forms-header">
                                    <h3>Government Scholarship Application</h3>
                                </div>
                                <button class="close-form-btn" onclick="closeForm()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="form-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
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
                                    <input type="hidden" name="scholarship_type" value="government">

                                    <!-- Form Title -->
                                    <div class="form-title">
                                        <h4>Application Instructions</h4>
                                    </div>

                                    <!-- Form Description -->
                                    <div class="form-description">
                                        <p>Please fill out all required fields marked with an asterisk (*). Ensure all
                                            information is accurate and complete before submitting your application.</p>
                                    </div>

                                    <!-- Government Benefactor Type Selection -->
                                    <div class="form-section-title">Government Benefactor Type</div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="government_benefactor_type">Benefactor Type *</label>
                                            <select id="government_benefactor_type" name="government_benefactor_type"
                                                required>
                                                <option value="">Select Benefactor Type</option>
                                                <option value="CHED">CHED (Commission on Higher Education)</option>
                                                <option value="DOST">DOST (Department of Science and Technology)</option>
                                                <option value="DSWD">DSWD (Department of Social Welfare and Development)
                                                </option>
                                                <option value="DOLE">DOLE (Department of Labor and Employment)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-section-title">Personal Information</div>
                                    <div class="form-row">
                                        <div class="form-group student-id-group">
                                            <label for="student_id">Student ID *</label>
                                            <input type="text" id="student_id" name="student_id"
                                                value="{{ $student->student_id }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="last_name">Last Name *</label>
                                            <input type="text" id="last_name" name="last_name"
                                                value="{{ $student->last_name }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="first_name">First Name *</label>
                                            <input type="text" id="first_name" name="first_name"
                                                value="{{ $student->first_name }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
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
                                                    <input type="radio" id="government_bsu" name="education_stage"
                                                        value="BEU" required>
                                                    <label for="government_bsu">BEU</label>
                                                </div>
                                                <div class="radio-option">
                                                    <input type="radio" id="government_college" name="education_stage"
                                                        value="College" required>
                                                    <label for="government_college">College</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- BEU Fields (Hidden by default) -->
                                    <div class="government-bsu-fields">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="government_grade_level">Grade Level *</label>
                                                <select id="government_grade_level" name="grade_level">
                                                    <option value="">Select Grade Level</option>
                                                    <option value="Grade 7">Grade 7</option>
                                                    <option value="Grade 8">Grade 8</option>
                                                    <option value="Grade 9">Grade 9</option>
                                                    <option value="Grade 10">Grade 10</option>
                                                    <option value="Grade 11">Grade 11</option>
                                                    <option value="Grade 12">Grade 12</option>
                                                </select>
                                            </div>
                                            <div class="form-group government-strand-field">
                                                <label for="government_strand">Strand *</label>
                                                <select id="government_strand" name="strand">
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
                                    <div class="government-college-fields">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="government_department">Department *</label>
                                                <select id="government_department" name="department">
                                                    <option value="">Select Department</option>
                                                    <option value="SITE">SITE</option>
                                                    <option value="SASTE">SASTE</option>
                                                    <option value="SBAHM">SBAHM</option>
                                                    <option value="SNAHS">SNAHS</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="government_course">Course *</label>
                                                <select id="government_course" name="course">
                                                    <option value="">Select Course</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="government_year_level">Year Level *</label>
                                                <select id="government_year_level" name="year_level">
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
                                            <input type="tel" id="contact_number" name="contact_number" required
                                                maxlength="11" placeholder="09123456789" pattern="[0-9]{11}"
                                                oninput="validateContactNumber(this)"
                                                onkeypress="return isNumberKey(event)"
                                                title="Please enter exactly 11 digits">
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="email">Email Address *</label>
                                            <input type="email" id="email" name="email"
                                                value="{{ $student->email }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <label for="indigenous_people">Indigenous People</label>
                                            <input type="text" id="indigenous_people" name="indigenous"
                                                placeholder="Indigenous People">
                                        </div>
                                    </div>

                                    <!-- Document Upload Section -->
                                    <div class="form-section-title">Document Upload</div>
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label for="government_document_upload">Upload Required Documents</label>
                                            <div class="file-upload-container">
                                                <input type="file" id="government_document_upload" name="documents[]"
                                                    multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                <div class="file-upload-area">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <p>Click to upload or drag and drop files here</p>
                                                    <small>Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB per file)</small>
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

                        <!-- Academic Scholarship Application Form -->
                        <div class="application-form-container" id="academic-form">
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
                                        <ul>
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
                                            <label for="academic_student_id">Student ID *</label>
                                            <input type="text" id="academic_student_id" name="student_id"
                                                value="{{ $student->student_id }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="academic_last_name">Last Name *</label>
                                            <input type="text" id="academic_last_name" name="last_name"
                                                value="{{ $student->last_name }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="academic_first_name">First Name *</label>
                                            <input type="text" id="academic_first_name" name="first_name"
                                                value="{{ $student->first_name }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <label for="academic_middle_name">Middle Name</label>
                                            <input type="text" id="academic_middle_name" name="middle_name">
                                        </div>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="academic_department">College/Department *</label>
                                            <select id="academic_department" name="department" required>
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
                                            <label for="academic_course">Course *</label>
                                            <select id="academic_course" name="course" required>
                                                <option value="">Select Course</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="academic_year_level">Year Level *</label>
                                            <select id="academic_year_level" name="year_level" required>
                                                <option value="">Select Year Level</option>
                                                <option value="1st Year">1st Year</option>
                                                <option value="2nd Year">2nd Year</option>
                                                <option value="3rd Year">3rd Year</option>
                                                <option value="4th Year">4th Year</option>
                                                <option value="5th Year">5th Year</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="academic_semester">Semester</label>
                                            <input type="text" id="academic_semester" name="semester"
                                                value="{{ $currentSemester }}" readonly
                                                class="readonly-field">
                                        </div>
                                    </div>

                                    <!-- Subjects and Grades Section -->
                                    <div class="subjects-section" id="academic-subjects-section"
                                        style="display: none;">
                                        <div class="form-section-title">Academic Performance - Subjects and Grades</div>
                                        <div class="subjects-container">
                                            <div class="subjects-header">
                                                <div class="subject-code-header">Subject Code & Course Title</div>
                                                <div class="grades-header">Grades</div>
                                                <div class="units-header">Units</div>
                                            </div>
                                            <div class="subjects-list" id="academic-subjects-list">
                                                <!-- Subjects will be dynamically populated here -->
                                            </div>
                                            <div class="gwa-calculation">
                                                <div class="gwa-row">
                                                    <div class="gwa-label">Total Units:</div>
                                                    <div class="gwa-value" id="academic-total-units">0</div>
                                                </div>
                                                <div class="gwa-row">
                                                    <div class="gwa-label">Total Grade Points:</div>
                                                    <div class="gwa-value" id="academic-total-grade-points">0.00</div>
                                                </div>
                                                <div class="gwa-row gwa-final">
                                                    <div class="gwa-label"><strong>GWA (General Weighted Average):</strong>
                                                    </div>
                                                    <div class="gwa-value" id="academic-calculated-gwa">
                                                        <strong>0.00</strong>
                                                    </div>
                                                </div>
                                                <div class="gwa-requirements">
                                                    <small class="form-help-text">
                                                        <strong>Academic Scholarship Requirements:</strong><br>
                                                        • All grades must be between 1.0 - 1.75 (passing grades)<br>
                                                        • Grades of 2.0 and below are not eligible<br>
                                                        • President's Lister (PL): GWA 1.0 - 1.25<br>
                                                        • Dean's Lister (DL): GWA 1.30 - 1.60
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Academic Year -->
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="inst_academic_year">Academic Year</label>
                                            <input type="text" id="inst_academic_year" name="academic_year"
                                                value="{{ $currentAcademicYear }}" readonly
                                                class="readonly-field">
                                        </div>
                                    </div>

                                    <!-- Hidden GWA field for form submission -->
                                    <input type="hidden" id="inst_calculated_gwa" name="gwa" value="0.00">

                                    <!-- Contact Information -->
                                    <div class="form-section-title">Contact Information</div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="inst_contact_number">Contact Number *</label>
                                            <input type="tel" id="inst_contact_number" name="contact_number" required
                                                maxlength="11" placeholder="09123456789" pattern="[0-9]{11}"
                                                oninput="validateContactNumber(this)"
                                                onkeypress="return isNumberKey(event)"
                                                title="Please enter exactly 11 digits">
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="inst_email">Email Address *</label>
                                            <input type="email" id="inst_email" name="email"
                                                value="{{ $student->email }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
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
                                            <input type="text" id="employees_student_id" name="student_id"
                                                value="{{ $student->student_id }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="employees_last_name">Last Name *</label>
                                            <input type="text" id="employees_last_name" name="last_name"
                                                value="{{ $student->last_name }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="employees_first_name">First Name *</label>
                                            <input type="text" id="employees_first_name" name="first_name"
                                                value="{{ $student->first_name }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
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
                                            <input type="tel" id="employees_contact_number" name="contact_number"
                                                required maxlength="11" placeholder="09123456789" pattern="[0-9]{11}"
                                                oninput="validateContactNumber(this)"
                                                onkeypress="return isNumberKey(event)"
                                                title="Please enter exactly 11 digits">
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="employees_email">Email Address *</label>
                                            <input type="email" id="employees_email" name="email"
                                                value="{{ $student->email }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
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

                                    <!-- Document Upload Section -->
                                    <div class="form-section-title">Document Upload</div>
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label for="employees_document_upload">Upload Required Documents</label>
                                            <div class="file-upload-container">
                                                <input type="file" id="employees_document_upload" name="documents[]"
                                                    multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                <div class="file-upload-area">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <p>Click to upload or drag and drop files here</p>
                                                    <small>Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB per file)</small>
                                                </div>
                                                <div class="uploaded-files-list"></div>
                                            </div>
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

                        <!-- Alumni Scholarship Application Form -->
                        <div class="application-form-container" id="alumni-form">
                            <div class="form-header">
                                <div class="application-forms-header">
                                    <h3>Alumni Scholarship Application</h3>
                                </div>
                                <button class="close-form-btn" onclick="closeForm()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="form-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
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
                                    <input type="hidden" name="scholarship_type" value="alumni">

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
                                            <input type="text" id="private_student_id" name="student_id"
                                                value="{{ $student->student_id }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="private_last_name">Last Name *</label>
                                            <input type="text" id="private_last_name" name="last_name"
                                                value="{{ $student->last_name }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="private_first_name">First Name *</label>
                                            <input type="text" id="private_first_name" name="first_name"
                                                value="{{ $student->first_name }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
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
                                            <input type="tel" id="private_contact_number" name="contact_number"
                                                required maxlength="11" placeholder="09123456789" pattern="[0-9]{11}"
                                                oninput="validateContactNumber(this)"
                                                onkeypress="return isNumberKey(event)"
                                                title="Please enter exactly 11 digits">
                                        </div>
                                        <div class="form-group auto-filled-group">
                                            <label for="private_email">Email Address *</label>
                                            <input type="email" id="private_email" name="email"
                                                value="{{ $student->email }}" readonly required>
                                            <small class="form-help-text">
                                                <i class="fas fa-info-circle"></i> Automatically filled based on your login
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Alumni Scholarship Information -->
                                    <div class="form-section-title">Alumni Scholarship Information</div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="alumni_scholarship_name">Scholarship Name *</label>
                                            <input type="text" id="alumni_scholarship_name" name="scholarship_name"
                                                required placeholder="Name of the alumni scholarship program">
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

                                    <!-- Document Upload Section -->
                                    <div class="form-section-title">Document Upload</div>
                                    <div class="form-row">
                                        <div class="form-group full-width">
                                            <label for="alumni_document_upload">Upload Required Documents</label>
                                            <div class="file-upload-container">
                                                <input type="file" id="alumni_document_upload" name="documents[]"
                                                    multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                <div class="file-upload-area">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <p>Click to upload or drag and drop files here</p>
                                                    <small>Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB per file)</small>
                                                </div>
                                                <div class="uploaded-files-list"></div>
                                            </div>
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

                        // Check if button is disabled
                        if (this.disabled || this.classList.contains('disabled')) {
                            return false;
                        }

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

                            // Initialize academic scholarship validation if it's the academic form
                            if (formId === 'academic-form') {
                                // Reset submit button to default state
                                updateAcademicSubmitButton(false);
                                // Remove any existing alerts
                                removeAllGradeAlerts();
                                removeGWADisqualificationAlert();
                                removeMainScreenGradeDisqualificationNotification();
                            }

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

                // Initialize Government form functionality
                initializeGovernmentForm();

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



                // Check for backend grade disqualification notifications and scroll to them
                const backendGradeNotification = document.querySelector(
                    '.main-screen-grade-disqualification-notification');
                if (backendGradeNotification) {
                    setTimeout(() => {
                        backendGradeNotification.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
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

            // Government Form Dynamic Functionality
            function initializeGovernmentForm() {
                // Academic Information card-style radio button handling
                const academicEducationRadios = document.querySelectorAll('#government-form input[name="education_stage"]');
                const governmentBsuFields = document.querySelectorAll('.government-bsu-fields');
                const governmentCollegeFields = document.querySelectorAll('.government-college-fields');

                academicEducationRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        // Show/hide appropriate fields based on selection
                        if (this.value === 'BEU') {
                            // Show BEU fields, hide college fields
                            governmentBsuFields.forEach(field => {
                                field.style.display = 'block';
                                // Make BEU fields required
                                const selects = field.querySelectorAll('select');
                                selects.forEach(select => select.required = true);
                            });
                            governmentCollegeFields.forEach(field => {
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
                            governmentCollegeFields.forEach(field => {
                                field.style.display = 'block';
                                // Make college fields required
                                const selects = field.querySelectorAll('select');
                                selects.forEach(select => select.required = true);
                            });
                            governmentBsuFields.forEach(field => {
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

                // Grade level change handler for Government form
                const governmentGradeLevelSelect = document.getElementById('government_grade_level');
                const governmentStrandField = document.querySelector('.government-strand-field');
                const governmentStrandSelect = document.getElementById('government_strand');

                if (governmentGradeLevelSelect && governmentStrandField && governmentStrandSelect) {
                    governmentGradeLevelSelect.addEventListener('change', function() {
                        const selectedGrade = this.value;

                        if (selectedGrade === 'Grade 11' || selectedGrade === 'Grade 12') {
                            // Show strand field for Grade 11 and 12
                            governmentStrandField.style.display = 'block';
                            governmentStrandSelect.required = true;
                        } else {
                            // Hide strand field for other grades
                            governmentStrandField.style.display = 'none';
                            governmentStrandSelect.required = false;
                            governmentStrandSelect.value = ''; // Clear selection
                        }
                    });
                }

                // Department change handler for Government form
                const governmentDepartmentSelect = document.getElementById('government_department');
                const governmentCourseSelect = document.getElementById('government_course');

                if (governmentDepartmentSelect && governmentCourseSelect) {
                    governmentDepartmentSelect.addEventListener('change', function() {
                        const selectedDepartment = this.value;
                        const courses = departmentCourses[selectedDepartment] || [];

                        // Clear existing options
                        governmentCourseSelect.innerHTML = '<option value="">Select Course</option>';

                        // Add new options
                        courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course;
                            option.textContent = course;
                            governmentCourseSelect.appendChild(option);
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
                const academicDepartmentSelect = document.getElementById('academic_department');
                const academicCourseSelect = document.getElementById('academic_course');

                if (academicDepartmentSelect && academicCourseSelect) {
                    academicDepartmentSelect.addEventListener('change', function() {
                        const selectedDepartment = this.value;
                        const courses = departmentCourses[selectedDepartment] || [];

                        // Clear existing options
                        academicCourseSelect.innerHTML = '<option value="">Select Course</option>';

                        // Add new options
                        courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course;
                            option.textContent = course;
                            academicCourseSelect.appendChild(option);
                        });
                    });
                }

                // Show subjects section when all required fields are filled
                const academicYearLevelSelect = document.getElementById('academic_year_level');
                const academicSemesterSelect = document.getElementById('academic_semester');

                function checkPresidentsFormCompletion() {
                    const selectedCourse = academicCourseSelect.value;
                    const selectedYearLevel = academicYearLevelSelect.value;
                    const selectedSemester = academicSemesterSelect.value;

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

                if (academicCourseSelect) {
                    academicCourseSelect.addEventListener('change', checkPresidentsFormCompletion);
                }
                if (academicYearLevelSelect) {
                    academicYearLevelSelect.addEventListener('change', checkPresidentsFormCompletion);
                }
                if (academicSemesterSelect) {
                    academicSemesterSelect.addEventListener('change', checkPresidentsFormCompletion);
                }
            }

            // Load subjects from API
            function loadSubjectsFromAPI(course, yearLevel, semester) {
                // Show loading state
                const subjectsSection = document.getElementById('academic-subjects-section');
                const subjectsList = document.getElementById('academic-subjects-list');

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
                const subjectsList = document.getElementById('academic-subjects-list');
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
                               onchange="calculateGWA(); validateAcademicGrades();"
                               oninput="validateIndividualGrade(this)">
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
                const subjectsSection = document.getElementById('academic-subjects-section');
                if (subjectsSection) {
                    subjectsSection.style.display = 'none';
                }
            }

            // Calculate GWA
            function calculateGWA() {
                const gradeInputs = document.querySelectorAll('#academic-subjects-list input[type="number"]');
                let totalUnits = 0;
                let totalGradePoints = 0;
                let hasDisqualifyingGrade = false;

                gradeInputs.forEach(input => {
                    const grade = parseFloat(input.value) || 0;
                    const units = parseFloat(input.getAttribute('data-units')) || 0;

                    if (grade > 0 && units > 0) {
                        totalUnits += units;
                        totalGradePoints += (grade * units);

                        // Check for disqualifying grade (2.0 and above)
                        if (grade >= 2.0) {
                            hasDisqualifyingGrade = true;
                        }
                    }
                });

                const gwa = totalUnits > 0 ? (totalGradePoints / totalUnits) : 0;

                // Update display
                document.getElementById('academic-total-units').textContent = totalUnits;
                document.getElementById('academic-total-grade-points').textContent = totalGradePoints.toFixed(2);
                document.getElementById('academic-calculated-gwa').innerHTML = `<strong>${gwa.toFixed(2)}</strong>`;

                // Update hidden field for form submission
                const hiddenGwaField = document.getElementById('inst_calculated_gwa');
                if (hiddenGwaField) {
                    hiddenGwaField.value = gwa.toFixed(2);
                }

                // Validate all grades individually to show/hide alerts
                validateAcademicGrades();
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
                        const contactNumber = field.value.toString().replace(/\D/g, '');
                        if (!/^\d+$/.test(contactNumber)) {
                            showFieldError(field, 'Contact number must contain only numbers');
                            isValid = false;
                        } else if (contactNumber.length !== 11) {
                            showFieldError(field, 'Contact number must be exactly 11 digits');
                            isValid = false;
                        }
                    } else {
                        showFieldError(field, 'Contact number is required');
                        isValid = false;
                    }
                });

                // Validate GWA if present
                const gwaField = form.querySelector('input[name="gwa"]');
                if (gwaField && gwaField.value) {
                    const gwa = parseFloat(gwaField.value);
                    if (gwa < 1.0 || gwa > 1.75) {
                        showFieldError(gwaField, 'GWA must be between 1.0 and 1.75 for Academic Scholarship eligibility');
                        isValid = false;
                    }
                }

                // Check for duplicate Student ID (only for non-readonly fields)
                const studentIdField = form.querySelector('input[name="student_id"]:not([readonly])');
                if (studentIdField && studentIdField.getAttribute('data-duplicate') === 'true') {
                    showFieldError(studentIdField, 'This Student ID has already been used.');
                    isValid = false;
                }

                // Check for disqualifying grades/GWA in academic scholarship
                const scholarshipTypeInput = form.querySelector('input[name="scholarship_type"]');
                if (scholarshipTypeInput && scholarshipTypeInput.value === 'academic') {
                    if (form.getAttribute('data-grade-disqualified') === 'true') {
                        // Show main screen grade disqualification notification
                        showMainScreenGradeDisqualificationNotification();

                        // Scroll to the main screen notification
                        const notification = document.querySelector('.main-screen-grade-disqualification-notification');
                        if (notification) {
                            notification.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }

                        // Show field error on submit button
                        const submitButton = form.querySelector('.submit-btn');
                        if (submitButton) {
                            showFieldError(submitButton, 'APPLICATION BLOCKED: Check the notification above for details.');
                        }

                        isValid = false;
                    }
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
                const studentIdInputs = document.querySelectorAll('input[name="student_id"]:not([readonly])');

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

            // Show duplicate warning
            function showDuplicateWarning(inputElement, data) {
                removeDuplicateWarning(inputElement);

                // Show simple warning
                const warning = document.createElement('div');
                warning.className = 'duplicate-warning';
                warning.innerHTML = `This Student ID has already been used for a ${data.scholarship_type} application.`;
                inputElement.parentNode.appendChild(warning);

                inputElement.classList.add('duplicate-error');
                inputElement.setAttribute('data-duplicate', 'true');
            }

            // Remove duplicate warning
            function removeDuplicateWarning(inputElement) {
                const existingWarning = inputElement.parentNode.querySelector('.duplicate-warning');
                if (existingWarning) {
                    existingWarning.remove();
                }
                inputElement.classList.remove('duplicate-error');
                inputElement.removeAttribute('data-duplicate');
            }

            // Show individual grade alert below specific input
            function showIndividualGradeAlert(input) {
                // Remove any existing alert for this input
                removeIndividualGradeAlert(input);

                // Create minimalistic grade alert
                const alert = document.createElement('div');
                alert.className = 'grade-alert';
                alert.innerHTML = `
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-text">
                    NOT QUALIFIED
                </div>
            `;

                // Insert alert directly below the input
                if (input.parentNode) {
                    input.parentNode.appendChild(alert);
                }
            }

            // Remove individual grade alert for specific input
            function removeIndividualGradeAlert(input) {
                const existingAlert = input.parentNode.querySelector('.grade-alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
            }

            // Remove all grade alerts
            function removeAllGradeAlerts() {
                const allAlerts = document.querySelectorAll('.grade-alert');
                allAlerts.forEach(alert => alert.remove());

                // Remove disqualification flag from form
                const academicForm = document.querySelector('form[action*="scholarship.submit"] input[value="academic"]');
                if (academicForm) {
                    academicForm.closest('form').removeAttribute('data-grade-disqualified');
                }
            }

            // Show GWA disqualification alert
            function showGWADisqualificationAlert(gwa) {
                // Remove any existing GWA alert
                removeGWADisqualificationAlert();

                // Create GWA disqualification alert
                const alert = document.createElement('div');
                alert.className = 'gwa-disqualification-alert';
                alert.innerHTML = `
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-text">
                    <strong>Academic Scholarship Disqualification!</strong><br>
                    Your GWA of <strong>${gwa}</strong> does not meet the requirement. Academic Scholarship requires a GWA between 1.0-1.60.
                </div>
            `;

                // Insert alert after the GWA calculation section
                const gwaCalculation = document.querySelector('.gwa-calculation');
                if (gwaCalculation && gwaCalculation.parentNode) {
                    gwaCalculation.parentNode.insertBefore(alert, gwaCalculation.nextSibling);
                }
            }

            // Remove GWA disqualification alert
            function removeGWADisqualificationAlert() {
                const existingAlert = document.querySelector('.gwa-disqualification-alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
            }

            // Show main screen grade disqualification notification
            function showMainScreenGradeDisqualificationNotification() {
                // Remove any existing grade disqualification notifications
                removeMainScreenGradeDisqualificationNotification();

                // Create main screen notification
                const notification = document.createElement('div');
                notification.className = 'main-screen-grade-disqualification-notification';
                notification.innerHTML = `
                <div class="notification-content">
                    <div class="notification-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="notification-text">
                        <strong>Academic Scholarship Application Blocked!</strong><br>
                        You cannot apply for Academic Scholarship due to disqualifying grades.<br><br>
                        <strong>Requirements:</strong> All grades must be between 1.0-1.75 (grades of 2.0 and above are not eligible)<br>
                        <strong>GWA Requirement:</strong> Overall GWA must be between 1.0-1.75<br><br>
                        Please review your grades and ensure they meet the requirements before attempting to apply.
                    </div>
                    <button class="notification-close" onclick="removeMainScreenGradeDisqualificationNotification()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

                // Insert notification at the placeholder
                const placeholder = document.getElementById('grade-disqualification-notification-placeholder');
                if (placeholder) {
                    placeholder.appendChild(notification);
                }

                // Scroll to notification
                setTimeout(() => {
                    notification.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 100);
            }

            // Remove main screen grade disqualification notification
            function removeMainScreenGradeDisqualificationNotification() {
                const existingNotification = document.querySelector('.main-screen-grade-disqualification-notification');
                if (existingNotification) {
                    existingNotification.remove();
                }
            }

            // Disable/Enable submit button for academic scholarship
            function updateAcademicSubmitButton(isDisqualified) {
                const academicForm = document.querySelector('form[action*="scholarship.submit"] input[value="academic"]');
                if (academicForm) {
                    const submitButton = academicForm.closest('form').querySelector('.submit-btn');
                    if (submitButton) {
                        if (isDisqualified) {
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<i class="fas fa-ban"></i> APPLICATION BLOCKED - Ineligible';
                            submitButton.style.cursor = 'not-allowed';
                            submitButton.title =
                                'You cannot apply due to disqualifying grades. All grades must be below 2.0 and GWA must be 1.0-1.75.';
                        } else {
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Application';
                            submitButton.style.cursor = 'pointer';
                            submitButton.title = '';
                        }
                    }
                }
            }

            // Validate individual grade input
            function validateIndividualGrade(input) {
                const grade = parseFloat(input.value);

                // Remove any existing grade error styling and alert
                input.classList.remove('grade-disqualified');
                removeIndividualGradeAlert(input);

                // If grade is entered and is 2.0 or above, mark as disqualified and show alert
                if (!isNaN(grade) && grade > 0 && grade >= 2.0) {
                    input.classList.add('grade-disqualified');
                    showIndividualGradeAlert(input);
                }
            }

            // Validate all academic grades and GWA
            function validateAcademicGrades() {
                const gradeInputs = document.querySelectorAll('#academic-subjects-list input[type="number"]');
                let hasDisqualifyingGrade = false;
                let hasValidGrades = false;

                // Check individual grades
                gradeInputs.forEach(input => {
                    validateIndividualGrade(input);
                    const grade = parseFloat(input.value);
                    if (!isNaN(grade) && grade > 0) {
                        hasValidGrades = true;
                        if (grade >= 2.0) {
                            hasDisqualifyingGrade = true;
                        }
                    }
                });

                // Check overall GWA if there are valid grades
                let gwaDisqualified = false;
                if (hasValidGrades) {
                    const gwaElement = document.getElementById('academic-calculated-gwa');
                    if (gwaElement) {
                        const gwaText = gwaElement.textContent.trim();
                        const gwa = parseFloat(gwaText);

                        if (!isNaN(gwa) && gwa > 1.74) {
                            gwaDisqualified = true;
                            showGWADisqualificationAlert(gwa.toFixed(2));
                        } else {
                            removeGWADisqualificationAlert();
                        }
                    }
                } else {
                    removeGWADisqualificationAlert();
                }

                // Overall disqualification check
                const isDisqualified = hasDisqualifyingGrade || gwaDisqualified;

                // Show/hide main screen grade disqualification notification
                if (isDisqualified && hasValidGrades) {
                    showMainScreenGradeDisqualificationNotification();
                } else {
                    removeMainScreenGradeDisqualificationNotification();
                }

                // Mark form as having disqualifying grades/GWA
                const academicForm = document.querySelector('form[action*="scholarship.submit"] input[value="academic"]');
                if (academicForm) {
                    if (isDisqualified) {
                        academicForm.closest('form').setAttribute('data-grade-disqualified', 'true');
                    } else {
                        academicForm.closest('form').removeAttribute('data-grade-disqualified');
                    }
                }

                // Update submit button state
                updateAcademicSubmitButton(isDisqualified);
            }

            // Validate contact number input
            function validateContactNumber(input) {
                // Remove any non-digit characters
                let value = input.value.replace(/\D/g, '');

                // Limit to 11 digits
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }

                // Update the input value
                input.value = value;

                // Remove any existing error styling
                input.classList.remove('contact-error');
                const existingError = input.parentNode.querySelector('.contact-error-message');
                if (existingError) {
                    existingError.remove();
                }

                // Validate length and show error if needed
                if (value.length > 0 && value.length < 11) {
                    input.classList.add('contact-error');

                    // Add error message
                    const errorMessage = document.createElement('small');
                    errorMessage.className = 'contact-error-message';
                    errorMessage.style.color = '#dc3545';
                    errorMessage.style.fontSize = '0.875rem';
                    errorMessage.style.marginTop = '5px';
                    errorMessage.style.display = 'block';
                    errorMessage.innerHTML =
                        `<i class="fas fa-exclamation-triangle"></i> Contact number must be exactly 11 digits (${value.length}/11)`;

                    input.parentNode.appendChild(errorMessage);
                }
            }

            // Allow only numeric input for contact numbers
            function isNumberKey(evt) {
                var charCode = (evt.which) ? evt.which : evt.keyCode;

                // Allow backspace, delete, tab, escape, enter
                if (charCode == 8 || charCode == 9 || charCode == 27 || charCode == 13 ||
                    charCode == 46 || charCode == 37 || charCode == 39) {
                    return true;
                }

                // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                if ((charCode == 65 || charCode == 67 || charCode == 86 || charCode == 88) &&
                    (evt.ctrlKey === true || evt.metaKey === true)) {
                    return true;
                }

                // Ensure that it is a number and stop the keypress
                if (charCode < 48 || charCode > 57) {
                    return false;
                }

                // Check if adding this digit would exceed 11 characters
                const currentValue = evt.target.value || '';
                if (currentValue.length >= 11) {
                    return false;
                }

                return true;
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

            // Settings Modal Functions
            function showSettingsModal() {
                document.getElementById('settingsModal').style.display = 'block';
                document.body.style.overflow = 'hidden';
                console.log('Settings modal opened');
            }

            function closeSettingsModal() {
                document.getElementById('settingsModal').style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            function switchSettingsTab(tabName) {
                // Hide all tab contents
                document.querySelectorAll('.settings-tab-content').forEach(content => {
                    content.style.display = 'none';
                });

                // Remove active class from all tabs
                document.querySelectorAll('.settings-tab').forEach(tab => {
                    tab.classList.remove('active');
                });

                // Show selected tab content
                document.getElementById(tabName + '-tab-content').style.display = 'block';

                // Add active class to selected tab
                document.getElementById(tabName + '-tab').classList.add('active');
            }

            function togglePasswordVisibility(inputId) {
                const input = document.getElementById(inputId);
                const icon = input.nextElementSibling.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            // Password form validation and submission
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('changePasswordForm');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        console.log('Form submission intercepted - AJAX will be used');

                        const currentPassword = document.getElementById('current_password').value;
                        const newPassword = document.getElementById('new_password').value;
                        const confirmPassword = document.getElementById('new_password_confirmation').value;

                        // Clear previous errors
                        clearPasswordErrors();

                        // Validate passwords
                        let hasErrors = false;

                        if (newPassword.length < 8) {
                            showPasswordError('new_password', 'Password must be at least 8 characters long');
                            hasErrors = true;
                        }

                        if (newPassword !== confirmPassword) {
                            showPasswordError('new_password_confirmation', 'Passwords do not match');
                            hasErrors = true;
                        }

                        if (currentPassword === newPassword) {
                            showPasswordError('new_password',
                                'New password must be different from current password');
                            hasErrors = true;
                        }

                        if (hasErrors) {
                            return;
                        }

                        // Submit form via AJAX
                        const formData = new FormData(this);
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;

                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing Password...';
                        submitBtn.disabled = true;

                        console.log('Making AJAX request to:', this.action);
                        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'));

                        fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(data => {
                                        throw new Error(JSON.stringify(data));
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {

                                if (data.success) {
                                    showPasswordSuccess(data.message);
                                    this.reset();
                                    // Update password status in profile tab
                                    updatePasswordStatus(true);
                                } else {
                                    if (data.errors) {
                                        Object.keys(data.errors).forEach(field => {
                                            showPasswordError(field, data.errors[field][0]);
                                        });
                                    } else {
                                        showPasswordError('current_password', data.message ||
                                            'An error occurred');
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);

                                try {
                                    const errorData = JSON.parse(error.message);
                                    if (errorData.errors) {
                                        Object.keys(errorData.errors).forEach(field => {
                                            showPasswordError(field, errorData.errors[field][0]);
                                        });
                                    } else {
                                        showPasswordError('current_password', errorData.message ||
                                            'An error occurred');
                                    }
                                } catch (parseError) {
                                    showPasswordError('current_password',
                                        'An error occurred. Please try again.');
                                }
                            })
                            .finally(() => {
                                submitBtn.innerHTML = originalText;
                                submitBtn.disabled = false;
                            });
                    });
                }
            });

            function clearPasswordErrors() {
                document.querySelectorAll('.password-error').forEach(error => error.remove());
                document.querySelectorAll('.password-success').forEach(success => success.remove());
                document.querySelectorAll('.password-input-container input').forEach(input => {
                    input.classList.remove('error', 'success');
                });
            }

            function showPasswordError(fieldName, message) {
                const field = document.getElementById(fieldName);
                const container = field.closest('.form-group');

                field.classList.add('error');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'password-error';
                errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
                container.appendChild(errorDiv);
            }

            function showPasswordSuccess(message) {
                // Remove any existing success messages
                document.querySelectorAll('.password-success').forEach(success => success.remove());

                const form = document.getElementById('changePasswordForm');
                const successDiv = document.createElement('div');
                successDiv.className = 'password-success';
                successDiv.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
                form.insertBefore(successDiv, form.firstChild);

                // Auto-dismiss after 3 seconds
                setTimeout(() => {
                    if (successDiv.parentNode) {
                        successDiv.remove();
                    }
                }, 3000);
            }

            function updatePasswordStatus(changed) {
                const statusElement = document.querySelector('.password-status');
                const warningElement = document.querySelector('.password-warning');

                if (statusElement) {
                    statusElement.textContent = changed ? 'Custom Password Set' : 'Using Default Password';
                    statusElement.className = `password-status ${changed ? 'changed' : 'default'}`;
                }

                if (warningElement && changed) {
                    warningElement.style.display = 'none';
                }
            }







            // Announcements Modal Functions
            function showAnnouncementsModal() {
                document.getElementById('announcementsModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
                // Mark announcements as viewed
                markAnnouncementsAsViewed();
            }

            function markAnnouncementsAsViewed() {
                // Get current announcements count
                const currentCount = {{ isset($announcements) ? $announcements->count() : 0 }};

                // Store the viewed count in localStorage
                localStorage.setItem('viewedAnnouncementsCount', currentCount);

                // Hide notification badge
                const badge = document.querySelector('.notification-badge');
                if (badge) {
                    badge.style.display = 'none';
                }
            }

            function closeAnnouncementsModal() {
                document.getElementById('announcementsModal').style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            // Check for new announcements periodically
            function checkForNewAnnouncements() {
                fetch('/api/announcements')
                    .then(response => response.json())
                    .then(data => {
                        const newCount = data.data.length;
                        const viewedCount = parseInt(localStorage.getItem('viewedAnnouncementsCount') || '0');
                        const badge = document.getElementById('notificationBadge');

                        if (newCount > viewedCount) {
                            // Show badge with unviewed announcements count
                            const unviewedCount = newCount - viewedCount;
                            if (badge) {
                                badge.textContent = unviewedCount;
                                badge.style.display = 'flex';
                            } else {
                                // Create new badge if it doesn't exist
                                const notificationLink = document.querySelector('.notification-link');
                                if (notificationLink && unviewedCount > 0) {
                                    const newBadge = document.createElement('span');
                                    newBadge.className = 'notification-badge';
                                    newBadge.id = 'notificationBadge';
                                    newBadge.textContent = unviewedCount;
                                    notificationLink.appendChild(newBadge);
                                }
                            }

                            // Update modal content
                            updateAnnouncementsModal(data.data);
                        } else if (badge) {
                            // Hide badge if no new announcements
                            badge.style.display = 'none';
                        }
                    })
                    .catch(error => console.log('Error checking announcements:', error));
            }

            function updateAnnouncementsModal(announcements) {
                const modalBody = document.querySelector('.announcements-modal-body');
                if (!modalBody) return;

                if (announcements.length > 0) {
                    const announcementsList = announcements.map(announcement => `
                        <div class="announcement-modal-item">
                            <div class="announcement-modal-header">
                                <h4 class="announcement-modal-title">${announcement.title}</h4>
                                <span class="announcement-modal-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    ${new Date(announcement.publish_date).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric'
                                    })}
                                </span>
                            </div>
                            <div class="announcement-modal-content">
                                <p>${announcement.content}</p>
                            </div>
                        </div>
                    `).join('');

                    modalBody.innerHTML = `
                        <div class="announcements-list">
                            ${announcementsList}
                        </div>
                    `;
                } else {
                    modalBody.innerHTML = `
                        <div class="no-announcements-modal">
                            <div class="no-announcements-modal-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h3>No Announcements</h3>
                            <p>There are no announcements at this time.</p>
                            <small>Check back later for updates from the administration.</small>
                        </div>
                    `;
                }
            }

            // Check for new announcements on page load
            document.addEventListener('DOMContentLoaded', function() {
                checkNotificationBadgeOnLoad();
            });

            // Check for new announcements every 30 seconds
            setInterval(checkForNewAnnouncements, 30000);

            function checkNotificationBadgeOnLoad() {
                const currentCount = {{ isset($announcements) ? $announcements->count() : 0 }};
                const viewedCount = parseInt(localStorage.getItem('viewedAnnouncementsCount') || '0');
                const badge = document.getElementById('notificationBadge');

                if (badge) {
                    if (currentCount > viewedCount) {
                        // Show badge with new announcements count
                        const newCount = currentCount - viewedCount;
                        badge.textContent = newCount;
                        badge.style.display = 'flex';
                    } else {
                        // Hide badge if no new announcements
                        badge.style.display = 'none';
                    }
                }
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                const settingsModal = document.getElementById('settingsModal');
                const announcementsModal = document.getElementById('announcementsModal');

                if (event.target === settingsModal) {
                    closeSettingsModal();
                }

                if (event.target === announcementsModal) {
                    closeAnnouncementsModal();
                }
            }
        </script>

        <!-- Announcements Modal -->
        <div id="announcementsModal" class="announcements-modal">
            <div class="announcements-modal-content">
                <div class="announcements-modal-header">
                    <h2><i class="fas fa-bell"></i> Announcements</h2>
                    <button class="close-announcements-btn" onclick="closeAnnouncementsModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="announcements-modal-body">
                    @if (isset($announcements) && $announcements->count() > 0)
                        <div class="announcements-list">
                            @foreach ($announcements as $announcement)
                                <div class="announcement-modal-item">
                                    <div class="announcement-modal-header">
                                        <h4 class="announcement-modal-title">{{ $announcement->title }}</h4>
                                        <span class="announcement-modal-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ $announcement->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="announcement-modal-content">
                                        <p>{{ $announcement->content }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-announcements-modal">
                            <div class="no-announcements-modal-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h3>No Announcements</h3>
                            <p>There are no announcements at this time.</p>
                            <small>Check back later for updates from the administration.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Settings Modal -->
        <div id="settingsModal" class="settings-modal">
            <div class="settings-modal-content">
                <div class="settings-modal-header">
                    <h2><i class="fas fa-cog"></i> Account Settings</h2>
                    <button class="close-settings-btn" onclick="closeSettingsModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="settings-tabs">
                    <button id="profile-tab" class="settings-tab active" onclick="switchSettingsTab('profile')">
                        <i class="fas fa-user"></i> Profile Details
                    </button>
                    <button id="password-tab" class="settings-tab" onclick="switchSettingsTab('password')">
                        <i class="fas fa-lock"></i> Change Password
                    </button>
                </div>

                <div class="settings-modal-body">
                    <!-- Profile Details Tab -->
                    <div id="profile-tab-content" class="settings-tab-content">
                        <div class="settings-section">
                            <h3>Personal Information</h3>
                            <div class="profile-details">
                                <div class="detail-row">
                                    <label>Full Name:</label>
                                    <span>{{ $student->full_name ?? ($student->name ?? 'Not Set') }}</span>
                                </div>
                                <div class="detail-row">
                                    <label>Student ID:</label>
                                    <span class="student-id-badge">{{ $student->student_id ?? 'Not Set' }}</span>
                                </div>
                                <div class="detail-row">
                                    <label>Email Address:</label>
                                    <span>{{ $student->email ?? 'Not Set' }}</span>
                                </div>
                                <div class="detail-row">
                                    <label>Account Status:</label>
                                    <span
                                        class="status-badge {{ $student->status === 'active' ? 'active' : 'inactive' }}">
                                        {{ ucfirst($student->status ?? 'Unknown') }}
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <label>Password Status:</label>
                                    <span
                                        class="password-status {{ $student->password_changed ? 'changed' : 'default' }}">
                                        {{ $student->password_changed ? 'Custom Password Set' : 'Using Default Password' }}
                                    </span>
                                </div>
                            </div>

                            @if (!$student->password_changed)
                                <div class="password-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Security Notice:</strong> You are still using the default password. Please
                                    change it for better security.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Change Password Tab -->
                    <div id="password-tab-content" class="settings-tab-content" style="display: none;">
                        <div class="settings-section">
                            <h3>Change Password</h3>
                            <form id="changePasswordForm" action="{{ route('student.change-password') }}"
                                method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <div class="password-input-container">
                                        <input type="password" id="current_password" name="current_password"
                                            placeholder="Enter your current password" required>
                                        <button type="button" class="password-toggle"
                                            onclick="togglePasswordVisibility('current_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <div class="password-input-container">
                                        <input type="password" id="new_password" name="new_password"
                                            placeholder="Enter your new password" required minlength="8">
                                        <button type="button" class="password-toggle"
                                            onclick="togglePasswordVisibility('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <small class="password-hint">Password must be at least 8 characters long</small>
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation">Confirm New Password</label>
                                    <div class="password-input-container">
                                        <input type="password" id="new_password_confirmation"
                                            name="new_password_confirmation" placeholder="Confirm your new password"
                                            required>
                                        <button type="button" class="password-toggle"
                                            onclick="togglePasswordVisibility('new_password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn-primary">
                                        <i class="fas fa-save"></i> Change Password
                                    </button>
                                    <button type="button" class="btn-secondary" onclick="closeSettingsModal()">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
