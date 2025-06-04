<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scholarship Application Form - St. Paul University Philippines</title>
    <link rel="stylesheet" href="{{ asset('css/scholarship.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- University Header -->
    <header class="university-header">
        <div class="header-content">
            <div class="university-logo-title">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo" class="university-logo">
                <div class="university-title">
                    <h1>St. Paul University Philippines</h1>
                    <h2>Office of the Registrar</h2>
                </div>
            </div>
            <div class="user-actions">
                <a href="{{ route('logout') }}" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </div>
    </header>

    <!-- Dashboard Banner -->
    <div class="dashboard-banner">
        <div class="banner-container">
            <h2>STUDENT DASHBOARD</h2>
        </div>
    </div>

    <div class="application-container">
        <div class="form-header">
            <h1>Application Forms</h1>
        </div>

        @if(!$canApply)
            <div class="application-notice">
                @if($hasActiveScholarship)
                    <div class="notice warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <div class="notice-content">
                            <h3>You already have an active scholarship</h3>
                            <p>You currently have an approved scholarship. New applications are only allowed during the renewal period.</p>
                        </div>
                    </div>
                @elseif($hasPendingApplication)
                    <div class="notice info">
                        <i class="fas fa-info-circle"></i>
                        <div class="notice-content">
                            <h3>Application in progress</h3>
                            <p>You already have a pending scholarship application. You can track its status on your dashboard.</p>
                            <a href="{{ route('student.dashboard') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
                        </div>
                    </div>
                @endif

                @if(!$isRenewalPeriod)
                    <div class="notice info mt-4">
                        <i class="fas fa-calendar-alt"></i>
                        <div class="notice-content">
                            <h3>Renewal Period</h3>
                            <p>The scholarship renewal period is not currently open. Please check back later or contact the scholarship office for more information.</p>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="scholarship-tabs">
                <button class="tab-btn active" data-form="ched-form">CHED</button>
                <button class="tab-btn" data-form="presidents-form">Institutional</button>
                <button class="tab-btn" data-form="employees-form">Employees Scholar</button>
                <button class="tab-btn" data-form="private-form">Private</button>
                <button class="tab-btn add-more">+ Add more</button>
            </div>

            <!-- Form content remains the same -->
        @endif
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            // Set up CSRF token for AJAX requests
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // For any fetch or AJAX requests
            document.querySelectorAll('form').forEach(form => {
                if (!form.querySelector('input[name="_token"]')) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = token;
                    form.appendChild(csrfInput);
                }
            });

            // Tab switching functionality
            const tabButtons = document.querySelectorAll('.tab-btn');
            const forms = document.querySelectorAll('.scholarship-form');

            // Remove the :not(.add-more) selector to include all buttons
            tabButtons.forEach(button => {
                if (!button.classList.contains('add-more')) {
                    button.addEventListener('click', function() {
                        // Remove active class from all buttons and forms
                        tabButtons.forEach(btn => btn.classList.remove('active'));
                        forms.forEach(form => form.classList.remove('active'));

                        // Add active class to clicked button
                        this.classList.add('active');

                        // Show corresponding form
                        const formId = this.getAttribute('data-form');
                        const targetForm = document.getElementById(formId);
                        if (targetForm) {
                            targetForm.classList.add('active');
                        }

                        // Debug
                        console.log('Switching to form:', formId);
                    });
                }
            });

            // Special handling for add-more button
            const addMoreBtn = document.querySelector('.tab-btn.add-more');
            if (addMoreBtn) {
                addMoreBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    alert('This feature will allow administrators to add custom scholarship forms in the future.');
                });
            }

            // Handle education stage selection for CHED form
            const educationRadios = document.querySelectorAll('input[name="education_stage"]');
            const bsuFields = document.querySelectorAll('.bsu-field');
            const collegeFields = document.querySelectorAll('.college-field');
            const strandField = document.querySelector('.strand-field');

            educationRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'BSU') {
                        bsuFields.forEach(field => field.style.display = 'block');
                        collegeFields.forEach(field => field.style.display = 'none');
                        // Reset strand field visibility (will be controlled by grade level)
                        strandField.style.display = 'none';
                    } else if (this.value === 'College') {
                        bsuFields.forEach(field => field.style.display = 'none');
                        collegeFields.forEach(field => field.style.display = 'block');
                        strandField.style.display = 'none';
                    }
                });
            });

            // Handle grade level selection to show strand for grades 11 and 12
            const gradeSelect = document.getElementById('grade_level');

            gradeSelect.addEventListener('change', function() {
                const selectedGrade = this.value;

                if (selectedGrade === 'Grade 11' || selectedGrade === 'Grade 12') {
                    strandField.style.display = 'block';
                } else {
                    strandField.style.display = 'none';
                }
            });

            // Handle department and course selection for all forms
            const departmentSelects = [
                document.getElementById('department'),
                document.getElementById('pd_department'),
                document.getElementById('emp_department'),
                document.getElementById('prv_department')
            ];

            const courseSelects = [
                document.getElementById('course'),
                document.getElementById('pd_course'),
                document.getElementById('emp_course'),
                document.getElementById('prv_course')
            ];

            const coursesByDepartment = {
                'SITE': ['BS Information Technology', 'Bachelor of Library and Information Science', 'BS Civil Engineering','BS Environmental and Sanitary Engineering', 'BS Computer Engineering'],
                'SASTE': ['Bachelor of Arts in English Language Studies', 'Bachelor of Secondary Education','BS Psychology','BS Biology','BS Public Administration', 'Bachelor of Science in Biology Major in MicroBiology','BS Social Work', 'Bachelor of Elementary Education', 'Bachelor of Physical Education'],
                'SBAHM': ['BS Accountancy', 'BS Entrepreneurship', 'BS Business Administration', 'BS Management Accounting','BS Hospitality Management', 'BS Tourism Management', 'BS Product Design and Marketing Innovation'],
                'SNAHS': ['BS Nursing', 'BS Pharmacy', 'BS Medical Technology', 'BS Radiologic Technology', 'BS Physical Therapy', 'BS Midwifery']
            };

            departmentSelects.forEach((departmentSelect, index) => {
                if (departmentSelect) {
                    departmentSelect.addEventListener('change', function() {
                        const department = this.value;
                        const courseSelect = courseSelects[index];

                        if (courseSelect) {
                            courseSelect.innerHTML = '<option value="" disabled selected>Select Course</option>';

                            if (department && coursesByDepartment[department]) {
                                coursesByDepartment[department].forEach(course => {
                                    const option = document.createElement('option');
                                    option.value = course;
                                    option.textContent = course;
                                    courseSelect.appendChild(option);
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
    <script>
        // Immediate execution to fix tab switching
        (function() {
            const tabButtons = document.querySelectorAll('.tab-btn:not(.add-more)');
            const forms = document.querySelectorAll('.scholarship-form');

            console.log('Found tab buttons:', tabButtons.length);
            console.log('Found forms:', forms.length);

            tabButtons.forEach(button => {
                button.onclick = function(e) {
                    e.preventDefault();

                    // Remove active class from all buttons and forms
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    forms.forEach(form => form.classList.remove('active'));

                    // Add active class to clicked button
                    this.classList.add('active');

                    // Show corresponding form
                    const formId = this.getAttribute('data-form');
                    console.log('Activating form:', formId);
                    document.getElementById(formId).classList.add('active');
                };
            });

            // Handle add-more button separately
            const addMoreBtn = document.querySelector('.tab-btn.add-more');
            if (addMoreBtn) {
                addMoreBtn.onclick = function(e) {
                    e.preventDefault();
                    alert('This feature will allow administrators to add custom scholarship forms in the future.');
                };
            }
        })();
    </script>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scholarshipForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // If using fetch or AJAX to submit
        fetch('{{ route('scholarship.submit') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route('scholarship.success') }}';
            } else {
                // Handle errors
                console.error(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize radio buttons
        const radioInputs = document.querySelectorAll('.radio-label input[type="radio"]');

        // Set initial state for any pre-selected radio buttons
        radioInputs.forEach(input => {
            if (input.checked) {
                const customRadio = input.nextElementSibling;
                customRadio.style.backgroundColor = '#1e5631';
                customRadio.style.borderColor = '#fff';
                customRadio.style.boxShadow = '0 0 0 2px #1e5631';
            }
        });

        // Add change event listeners
        radioInputs.forEach(input => {
            input.addEventListener('change', function() {
                // Reset all radio buttons in the same group
                const name = this.getAttribute('name');
                const groupInputs = document.querySelectorAll(`input[name="${name}"]`);

                groupInputs.forEach(groupInput => {
                    const customRadio = groupInput.nextElementSibling;
                    if (groupInput.checked) {
                        customRadio.style.backgroundColor = '#1e5631';
                        customRadio.style.borderColor = '#fff';
                        customRadio.style.boxShadow = '0 0 0 2px #1e5631';
                    } else {
                        customRadio.style.backgroundColor = '#fff';
                        customRadio.style.borderColor = '#ccc';
                        customRadio.style.boxShadow = 'none';
                    }
                });
            });
        });
    });
</script>

<style>
    /* Add ripple animation */
    @keyframes rippleEffect {
        0% { transform: scale(0); opacity: 1; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    /* Add subtle bounce to form sections */
    @keyframes sectionBounce {
        0% { transform: translateY(20px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }

    .form-section {
        animation: sectionBounce 0.5s ease-out;
    }
</style>
</body>





