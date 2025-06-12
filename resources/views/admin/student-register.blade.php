@extends('layouts.admin')

@section('title', 'Users Management')

@push('styles')
    <style>
        .register-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .register-form {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-header h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #7f8c8d;
            margin: 0;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
        }

        .form-group.full-width {
            flex: 100%;
        }

        .required {
            color: #e74c3c;
        }

        .submit-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #2980b9, #1f5f8b);
            transform: translateY(-2px);
        }

        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-left: 5px solid;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-color: #28a745;
            color: #155724;
            position: relative;
            overflow: hidden;
        }

        .alert-success::before {
            content: '✅';
            font-size: 18px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .alert-success .student-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .alert-success .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .alert-success .detail-label {
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0d4f1c;
            opacity: 0.8;
        }

        .alert-success .detail-value {
            font-weight: 500;
            font-size: 14px;
            color: #155724;
            background: rgba(255, 255, 255, 0.8);
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .alert-success .notice {
            margin-top: 15px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            border-left: 3px solid #ffc107;
            font-style: italic;
            color: #856404;
            font-size: 13px;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-color: #dc3545;
            color: #721c24;
        }

        .alert-error::before {
            content: '❌';
            font-size: 18px;
            margin-right: 10px;
            vertical-align: middle;
        }

        .password-info {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            font-size: 14px;
        }

        .password-info h4 {
            margin: 0 0 15px 0;
            color: #0c5460;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .password-info p {
            margin: 0 0 10px 0;
            color: #0c5460;
        }

        .password-info ul {
            margin: 0;
            padding-left: 20px;
        }

        .password-info li {
            color: #0c5460;
            margin-bottom: 8px;
        }

        .password-info code {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 4px;
            padding: 2px 6px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #0c5460;
        }

        .feedback-message {
            margin-top: 8px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            display: none;
        }

        .feedback-message.success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            display: block;
        }

        .feedback-message.error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            display: block;
        }

        .feedback-message.checking {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            display: block;
        }

        /* Tab Styles */
        .tab-container {
            margin-bottom: 30px;
        }

        .tab-buttons {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 20px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 15px 25px;
            font-size: 16px;
            font-weight: 600;
            color: #7f8c8d;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn:hover {
            color: #3498db;
            background-color: #f8f9fa;
        }

        .tab-btn.active {
            color: #3498db;
            border-bottom-color: #3498db;
            background-color: #f8f9fa;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Scholarship Students Table Styles */
        .students-table-container {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .table-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .students-table th,
        .students-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .students-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .students-table td {
            font-size: 14px;
            color: #333;
        }

        .students-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-badge.approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-badge.active {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .scholarship-type-badge {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            background-color: #e9ecef;
            color: #495057;
        }

        .no-students {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }

        .search-filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .filter-select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background-color: white;
        }

        .count-badge {
            background-color: #3498db;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .register-container {
                padding: 10px;
            }

            .register-form {
                padding: 20px;
            }

            .tab-buttons {
                flex-direction: column;
            }

            .tab-btn {
                border-bottom: none;
                border-right: 3px solid transparent;
            }

            .tab-btn.active {
                border-right-color: #3498db;
                border-bottom-color: transparent;
            }

            .search-filter-container {
                flex-direction: column;
            }

            .search-input {
                min-width: auto;
            }

            .students-table {
                font-size: 12px;
            }

            .students-table th,
            .students-table td {
                padding: 8px 4px;
            }
        }
    </style>
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[['title' => 'Users Management', 'icon' => 'fas fa-users-cog']]" />
@endsection

@section('content')
    <div class="register-container">
        <!-- Tab Navigation -->
        <div class="tab-container">
            <div class="tab-buttons">
                <button class="tab-btn active" onclick="switchTab('register')">
                    <i class="fas fa-user-plus"></i> Register Student
                </button>
                <button class="tab-btn" onclick="switchTab('scholarship-students')">
                    <i class="fas fa-graduation-cap"></i> Scholarship Students
                    @if(isset($scholarshipStudents))
                        <span class="count-badge" style="margin-left: 8px; font-size: 12px;">{{ $scholarshipStudents->count() }}</span>
                    @endif
                </button>
            </div>
        </div>

        <!-- Register Student Tab -->
        <div id="register-tab" class="tab-content active">
            <div class="register-form">
                <div class="form-header">
                    <h2><i class="fas fa-user-plus"></i> Register New Student</h2>
                    <p>Create student user accounts. Students will complete their academic details during scholarship
                        application.</p>
                </div>

            @if (session('success'))
                <div class="alert alert-success">
                    <strong>{{ session('success') }}</strong>

                    @if (session('student_data'))
                        @php
                            $studentData = session('student_data');
                        @endphp

                        <div class="student-details">
                            <div class="detail-item">
                                <span class="detail-label">Student ID:</span>
                                <span class="detail-value">{{ $studentData['student_id'] }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">{{ $studentData['name'] }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">{{ $studentData['email'] }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Default Password:</span>
                                <span class="detail-value">{{ $studentData['password'] }}</span>
                            </div>
                        </div>
                        <div class="notice">
                            📝 <strong>Important:</strong> Student should change password upon first login.
                        </div>
                    @endif
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.student-register.store') }}" method="POST">
                @csrf

                <!-- Student ID (Required) -->
                <div class="form-group">
                    <label for="student_id">Student ID <span class="required">*</span></label>
                    <input type="text" id="student_id" name="student_id" value="{{ old('student_id') }}"
                        placeholder="Enter student ID (e.g., 2024-001234)" required>
                    <div id="student-id-feedback" class="feedback-message"></div>
                </div>

                <!-- Name Fields -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}"
                            placeholder="Enter first name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}"
                            placeholder="Enter last name" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="Enter email address" required>
                </div>

                <!-- Default Password Information -->
                <div class="password-info">
                    <h4><i class="fas fa-info-circle"></i> Default Password Information</h4>
                    <p>A default password will be automatically assigned to this student account:</p>
                    <ul>
                        <li><strong>Default Password:</strong> <code>student123</code></li>
                        <li>The student will be required to change this password upon first login</li>
                        <li>Students can update their password anytime from their profile settings</li>
                    </ul>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-user-plus"></i> Create User Account
                </button>
            </form>
            </div>
        </div>

        <!-- Scholarship Students Tab -->
        <div id="scholarship-students-tab" class="tab-content">
            <div class="students-table-container">
                <div class="table-header">
                    <h2 class="table-title"><i class="fas fa-graduation-cap"></i> Scholarship Students</h2>
                    @if(isset($scholarshipStudents))
                        <div class="student-count">
                            <span class="count-badge">{{ $scholarshipStudents->count() }} Students</span>
                        </div>
                    @endif
                </div>

                <!-- Search and Filter -->
                <div class="search-filter-container">
                    <input type="text" id="studentSearch" class="search-input" placeholder="Search by name, student ID, or email...">
                    <select id="statusFilter" class="filter-select">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="active">Active Scholar</option>
                    </select>
                    <select id="scholarshipFilter" class="filter-select">
                        <option value="">All Scholarships</option>
                        <option value="academic">Academic</option>
                        <option value="government">Government</option>
                        <option value="institutional">Institutional</option>
                        <option value="employee">Employee</option>
                        <option value="private">Private</option>
                    </select>
                </div>

                <!-- Students Table -->
                @if(isset($scholarshipStudents) && $scholarshipStudents->count() > 0)
                    <table class="students-table" id="studentsTable">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Course</th>
                                <th>Department</th>
                                <th>Year Level</th>
                                <th>Scholarship Type</th>
                                <th>Status</th>
                                <th>Application Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scholarshipStudents as $student)
                                <tr>
                                    <td><strong>{{ $student['student_id'] }}</strong></td>
                                    <td>{{ $student['name'] }}</td>
                                    <td>{{ $student['email'] ?? 'Not provided' }}</td>
                                    <td>{{ $student['contact_number'] ?? 'Not provided' }}</td>
                                    <td>{{ $student['course'] }}</td>
                                    <td>{{ $student['department'] }}</td>
                                    <td>{{ $student['year_level'] }}</td>
                                    <td>
                                        <span class="scholarship-type-badge">{{ $student['scholarship_type'] }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge {{ strtolower(str_replace(' ', '-', $student['status'])) }}">
                                            {{ $student['status'] }}
                                        </span>
                                    </td>
                                    <td>{{ $student['application_date'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="no-students">
                        <i class="fas fa-graduation-cap" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                        <p>No scholarship students found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const studentIdInput = document.getElementById('student_id');
            const feedback = document.getElementById('student-id-feedback');
            const submitBtn = document.querySelector('.submit-btn');
            let checkTimeout;

            // Auto-format student ID input and check availability
            studentIdInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9-]/g, '');
                e.target.value = value;

                // Clear previous timeout
                clearTimeout(checkTimeout);

                if (value.length >= 3) {
                    // Show checking message
                    feedback.className = 'feedback-message checking';
                    feedback.textContent = 'Checking availability...';

                    // Check availability after 500ms delay
                    checkTimeout = setTimeout(() => {
                        checkStudentIdAvailability(value);
                    }, 500);
                } else {
                    feedback.style.display = 'none';
                }
            });

            // Function to check student ID availability
            function checkStudentIdAvailability(studentId) {
                fetch('{{ route('admin.check-student-id') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            student_id: studentId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            feedback.className = 'feedback-message success';
                            feedback.textContent = '✓ Student ID is available';
                            submitBtn.disabled = false;
                        } else {
                            feedback.className = 'feedback-message error';
                            feedback.textContent = '✗ Student ID already exists';
                            submitBtn.disabled = true;
                        }
                    })
                    .catch(error => {
                        feedback.className = 'feedback-message error';
                        feedback.textContent = 'Error checking availability';
                        console.error('Error:', error);
                    });
            }

            // Form submission validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const studentId = studentIdInput.value.trim();
                if (studentId.length < 3) {
                    e.preventDefault();
                    alert('Please enter a valid Student ID');
                    studentIdInput.focus();
                    return false;
                }

                // Check if student ID is available before submitting
                if (feedback.classList.contains('error')) {
                    e.preventDefault();
                    alert('Student ID already exists. Please use a different Student ID.');
                    studentIdInput.focus();
                    return false;
                }
            });

            // Clear form if there's a success message (after successful registration)
            @if (session('success'))
                // Clear form fields after successful registration
                setTimeout(function() {
                    form.reset();
                    feedback.style.display = 'none';
                    submitBtn.disabled = false;
                }, 100);
            @endif
        });

        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.add('active');

            // Add active class to clicked tab button
            event.target.classList.add('active');
        }

        // Search and filter functionality for scholarship students
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('studentSearch');
            const statusFilter = document.getElementById('statusFilter');
            const scholarshipFilter = document.getElementById('scholarshipFilter');
            const table = document.getElementById('studentsTable');

            if (searchInput && statusFilter && scholarshipFilter && table) {
                function filterTable() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const statusValue = statusFilter.value.toLowerCase();
                    const scholarshipValue = scholarshipFilter.value.toLowerCase();
                    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                    for (let i = 0; i < rows.length; i++) {
                        const row = rows[i];
                        const cells = row.getElementsByTagName('td');

                        if (cells.length > 0) {
                            const studentId = cells[0].textContent.toLowerCase();
                            const name = cells[1].textContent.toLowerCase();
                            const email = cells[2].textContent.toLowerCase();
                            const scholarshipType = cells[7].textContent.toLowerCase();
                            const status = cells[8].textContent.toLowerCase();

                            const matchesSearch = searchTerm === '' ||
                                studentId.includes(searchTerm) ||
                                name.includes(searchTerm) ||
                                email.includes(searchTerm);

                            const matchesStatus = statusValue === '' ||
                                status.includes(statusValue);

                            const matchesScholarship = scholarshipValue === '' ||
                                scholarshipType.includes(scholarshipValue);

                            if (matchesSearch && matchesStatus && matchesScholarship) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    }
                }

                searchInput.addEventListener('input', filterTable);
                statusFilter.addEventListener('change', filterTable);
                scholarshipFilter.addEventListener('change', filterTable);
            }
        });
    </script>
@endpush
