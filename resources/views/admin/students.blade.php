@extends('layouts.admin')

@section('title', 'Grantee Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/students.css') }}">
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[['title' => 'Grantee', 'icon' => 'fas fa-users']]" />
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>
            @if (isset($scholarshipName))
                {{ $scholarshipName }} Grantees
            @else
                Grantee Management
            @endif
        </h1>
        <div class="date">{{ date('F d, Y') }}</div>
    </div>



    <!-- Grantee Categories -->
    <div class="student-categories">
        <div class="category-tabs">
            <button class="tab-btn {{ !isset($scholarshipTypeFilter) ? 'active' : '' }}"
                onclick="showStudentCategory('all', this)">All Grantees</button>
            <button class="tab-btn {{ isset($scholarshipTypeFilter) && $scholarshipTypeFilter === 'ched' ? 'active' : '' }}"
                onclick="showStudentCategory('ched', this)">CHED Grantees</button>
            <button
                class="tab-btn {{ isset($scholarshipTypeFilter) && $scholarshipTypeFilter === 'academic' ? 'active' : '' }}"
                onclick="showStudentCategory('academic', this)">Academic Grantees</button>
            <button
                class="tab-btn {{ isset($scholarshipTypeFilter) && $scholarshipTypeFilter === 'employees' ? 'active' : '' }}"
                onclick="showStudentCategory('employees', this)">Employee Grantees</button>
            <button
                class="tab-btn {{ isset($scholarshipTypeFilter) && $scholarshipTypeFilter === 'private' ? 'active' : '' }}"
                onclick="showStudentCategory('private', this)">Private Grantees</button>
        </div>
    </div>

    <!-- Grantee Table -->
    <div class="student-table-container">
        <div class="table-header">
            <h3 id="categoryTitle">
                @if (isset($scholarshipTypeFilter))
                    @switch($scholarshipTypeFilter)
                        @case('ched')
                            CHED Grantees
                        @break

                        @case('academic')
                            Academic Grantees
                        @break

                        @case('employees')
                            Employee Grantees
                        @break

                        @case('private')
                            Private Grantees
                        @break

                        @default
                            All Grantees
                    @endswitch
                @else
                    All Grantees
                @endif
            </h3>
            <div class="table-actions" id="tableActions">
                <button class="btn-secondary" id="importBtn" onclick="triggerFileImport()"
                    style="display: {{ isset($scholarshipTypeFilter) ? 'none' : 'block' }};">
                    <i class="fas fa-upload"></i> Import
                </button>
                <button class="btn-primary" onclick="showAddStudentForm()">
                    <i class="fas fa-plus"></i> Add Grantee
                </button>
            </div>
        </div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>Grantee ID</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Benefactor Type</th>
                    <th>Semester</th>
                    <th>Academic Year</th>
                    <th>Status</th>
                    <th>GWA</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="studentsTableBody">
                @forelse($students as $student)
                    <tr data-type="{{ strtolower(str_replace(' ', '', $student['scholarship_type'])) }}">
                        <td>{{ $student['id'] }}</td>
                        <td>{{ $student['name'] }}</td>
                        <td>{{ $student['course'] }}</td>
                        <td>{{ $student['scholarship_type'] }}</td>
                        <td>
                            <span class="semester-badge">{{ $student['current_semester'] ?? $currentSemester }}</span>
                        </td>
                        <td>
                            <span
                                class="academic-year-badge">{{ $student['current_academic_year'] ?? $currentAcademicYear }}</span>
                        </td>
                        <td><span class="status-badge active">{{ $student['status'] }}</span></td>
                        <td>{{ $student['gwa'] }}</td>
                        <td>
                            <button class="action-btn edit"
                                onclick="editStudent('{{ $student['application_id'] }}', '{{ $student['id'] }}')"
                                title="Edit Grantee" data-student-id="{{ $student['id'] }}"
                                data-student-name="{{ $student['name'] }}" data-student-course="{{ $student['course'] }}"
                                data-student-gwa="{{ $student['gwa'] }}"
                                data-student-email="{{ $student['email'] ?? '' }}"
                                data-student-contact="{{ $student['contact_number'] ?? '' }}"
                                data-student-department="{{ $student['department'] ?? '' }}"
                                data-student-year="{{ $student['year_level'] ?? '' }}"
                                data-application-id="{{ $student['application_id'] }}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state">
                            No grantees found. Grantees will appear here once benefactor applications are
                            approved.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

<!-- Edit Grantee Modal -->
<div id="editStudentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Grantee Information</h2>
            <span class="close" onclick="closeEditModal()">&times;</span>
        </div>
        <form id="editStudentForm" onsubmit="saveStudentChanges(event)">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="editStudentId">Grantee ID</label>
                        <input type="text" id="editStudentId" name="student_id" readonly>
                    </div>
                    <div class="form-group">
                        <label for="editStudentName">Full Name</label>
                        <input type="text" id="editStudentName" name="name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="editStudentEmail">Email</label>
                        <input type="email" id="editStudentEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editStudentContact">Contact Number</label>
                        <input type="text" id="editStudentContact" name="contact_number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="editStudentCourse">Course</label>
                        <input type="text" id="editStudentCourse" name="course" required>
                    </div>
                    <div class="form-group">
                        <label for="editStudentDepartment">Department</label>
                        <input type="text" id="editStudentDepartment" name="department">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="editStudentYear">Year Level</label>
                        <select id="editStudentYear" name="year_level">
                            <option value="">Select Year</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                            <option value="5th Year">5th Year</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editStudentGwa">GWA</label>
                        <input type="number" id="editStudentGwa" name="gwa" step="0.01" min="1"
                            max="4">
                    </div>
                </div>
                <input type="hidden" id="editApplicationId" name="application_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Grantee Modal -->
<div id="addStudentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Grantee</h2>
            <span class="close" onclick="closeAddStudentModal()">&times;</span>
        </div>
        <form id="addStudentForm" onsubmit="saveNewStudent(event)">
            <div class="modal-body">
                <!-- Scholarship Type Selection -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="scholarshipType">Benefactor Type *</label>
                        <select id="scholarshipType" name="scholarship_type" required onchange="updateFormFields()">
                            <option value="">Select Benefactor Type</option>
                            <option value="ched">CHED Benefactor</option>
                            <option value="academic">Academic Benefactor</option>
                            <option value="employees">Employee Benefactor</option>
                            <option value="private">Private Benefactor</option>
                        </select>
                    </div>
                </div>

                <!-- Basic Information (Common for all types) -->
                <div class="form-section">
                    <h3>Basic Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="studentId">Grantee ID *</label>
                            <input type="text" id="studentId" name="student_id" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="middleName">Middle Name</label>
                            <input type="text" id="middleName" name="middle_name">
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sex">Sex *</label>
                            <select id="sex" name="sex" required>
                                <option value="">Select Sex</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Birthdate *</label>
                            <input type="date" id="birthdate" name="birthdate" required>
                        </div>
                        <div class="form-group">
                            <label for="contactNumber">Contact Number</label>
                            <input type="text" id="contactNumber" name="contact_number">
                        </div>
                    </div>
                </div>

                <!-- Dynamic Fields Container -->
                <div id="dynamicFields"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddStudentModal()">Cancel</button>
                <button type="submit" class="btn-primary">Add Student</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden file input for import -->
<input type="file" id="hiddenFileInput" accept=".xlsx,.xls,.csv" style="display: none;"
    onchange="handleFileSelection(event)">

<!-- Scholarship Type Selection Modal (shown after file selection) -->
<div id="scholarshipTypeModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Select Scholarship Type</h2>
            <span class="close" onclick="closeScholarshipTypeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-section">
                <p>Selected file: <strong id="selectedFileName"></strong></p>
                <div class="form-row">
                    <div class="form-group">
                        <label for="scholarshipTypeSelect">Benefactor Type *</label>
                        <select id="scholarshipTypeSelect" required>
                            <option value="">Select Benefactor Type</option>
                            <option value="ched">CHED Benefactor</option>
                            <option value="academic">Academic Benefactor</option>
                            <option value="employees">Employee Benefactor</option>
                            <option value="private">Private Benefactor</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="updateExistingCheckbox">
                            Update existing grantees if Grantee ID already exists
                        </label>
                    </div>
                </div>
                <div class="import-info">
                    <p><strong>Import Instructions:</strong></p>
                    <ul>
                        <li>Excel file should contain columns: Grantee ID, First Name, Last Name, Middle Name,
                            Email, Course, Department, Year Level, GWA, Contact Number</li>
                        <li>Grantee ID, First Name, and Last Name are required</li>
                        <li>The first row should contain column headers</li>
                        <li><a href="/admin/download-student-template" target="_blank">Download Excel Template</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeScholarshipTypeModal()">Cancel</button>
            <button type="button" class="btn-primary" onclick="proceedWithImport()">Import Students</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Add event listener when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Remove the duplicate event listener - button already has onclick attribute

            // Test function to manually populate courses
            window.testCoursePopulation = function() {
                console.log('Testing course population...');
                const courseSelect = document.getElementById('course');
                if (courseSelect) {
                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    const testCourses = [
                        'Bachelor of Science in Information Technology',
                        'Bachelor of Science in Computer Science',
                        'Bachelor of Science in Computer Engineering'
                    ];
                    testCourses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course;
                        option.textContent = course;
                        courseSelect.appendChild(option);
                    });
                    courseSelect.disabled = false;
                    console.log('Test courses added successfully');
                } else {
                    console.error('Course select not found');
                }
            };

            // Force populate courses for current department selection
            window.forcePopulateCourses = function() {
                console.log('Force populating courses...');

                // Check for Institutional form
                const deptSelect = document.getElementById('department');
                const courseSelect = document.getElementById('course');

                if (deptSelect && courseSelect && deptSelect.value) {
                    console.log('Found Institutional form, department:', deptSelect.value);
                    const siteCourses = [
                        'Bachelor of Science in Information Technology',
                        'Bachelor of Science in Computer Science',
                        'Bachelor of Science in Computer Engineering',
                        'Bachelor of Library and Information Science',
                        'Bachelor of Science in Civil Engineering'
                    ];

                    courseSelect.innerHTML = '<option value="">Select Course</option>';
                    siteCourses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course;
                        option.textContent = course;
                        courseSelect.appendChild(option);
                    });
                    courseSelect.disabled = false;
                    console.log('Courses populated for Institutional form');
                    return;
                }

                // Check for CHED form
                const chedDeptSelect = document.getElementById('chedDepartment');
                const chedCourseSelect = document.getElementById('chedCourse');

                if (chedDeptSelect && chedCourseSelect && chedDeptSelect.value) {
                    console.log('Found CHED form, department:', chedDeptSelect.value);
                    const siteCourses = [
                        'Bachelor of Science in Information Technology',
                        'Bachelor of Science in Computer Science',
                        'Bachelor of Science in Computer Engineering',
                        'Bachelor of Library and Information Science',
                        'Bachelor of Science in Civil Engineering'
                    ];

                    chedCourseSelect.innerHTML = '<option value="">Select Course</option>';
                    siteCourses.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course;
                        option.textContent = course;
                        chedCourseSelect.appendChild(option);
                    });
                    chedCourseSelect.disabled = false;
                    console.log('Courses populated for CHED form');
                    return;
                }

                console.error('No department selected or form not found');
            };

            // Add direct event listeners for department dropdowns
            setTimeout(() => {
                // For Institutional scholarship
                const deptSelect = document.getElementById('department');
                if (deptSelect) {
                    deptSelect.addEventListener('change', function() {
                        console.log('Department changed via event listener:', this.value);
                        loadCoursesByDepartment();
                    });
                }

                // For CHED scholarship
                const chedDeptSelect = document.getElementById('chedDepartment');
                if (chedDeptSelect) {
                    chedDeptSelect.addEventListener('change', function() {
                        console.log('CHED Department changed via event listener:', this.value);
                        loadChedCoursesByDepartment();
                    });
                }
            }, 1000);

            // Initialize page with correct active tab based on filter
            @if (isset($scholarshipTypeFilter))
                // If there's a scholarship type filter, activate the corresponding tab
                const filterType = '{{ $scholarshipTypeFilter }}';
                console.log('Initializing with filter type:', filterType);
                activateTabByType(filterType);

                // Also ensure the correct tab is visually highlighted
                setTimeout(() => {
                    const activeTab = document.querySelector('.tab-btn.active');
                    if (activeTab) {
                        console.log('Active tab found:', activeTab.textContent);
                        // Add a subtle visual indicator that this tab is filtered
                        activeTab.style.boxShadow = '0 0 10px rgba(30, 86, 49, 0.3)';
                    }
                }, 100);
            @endif
        });

        // Function to activate tab based on scholarship type
        function activateTabByType(scholarshipType) {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

            // Find and activate the correct tab
            const tabButtons = document.querySelectorAll('.tab-btn');
            tabButtons.forEach(btn => {
                const btnText = btn.textContent.toLowerCase();
                if (
                    (scholarshipType === 'ched' && btnText.includes('ched')) ||
                    (scholarshipType === 'academic' && btnText.includes('academic')) ||
                    (scholarshipType === 'employees' && btnText.includes('employee')) ||
                    (scholarshipType === 'private' && btnText.includes('private'))
                ) {
                    btn.classList.add('active');

                    // Update table title
                    const titles = {
                        'ched': 'CHED Grantees',
                        'academic': 'Academic Grantees',
                        'employees': 'Employee Grantees',
                        'private': 'Private Grantees'
                    };
                    document.getElementById('categoryTitle').textContent = titles[scholarshipType] ||
                        'All Grantees';

                    // Show/hide import button - only show for "All Grantees"
                    const importBtn = document.getElementById('importBtn');
                    if (importBtn) {
                        importBtn.style.display = 'none';
                    }
                }
            });
        }

        // Student management functions
        let isTabSwitching = false;

        function showStudentCategory(category, clickedButton) {
            // Prevent rapid clicking
            if (isTabSwitching) {
                return;
            }

            isTabSwitching = true;

            // Build the URL with the appropriate scholarship type parameter
            let url = "{{ route('admin.students') }}";

            if (category !== 'all') {
                // Map category to scholarship type parameter
                const categoryMap = {
                    'ched': 'ched',
                    'academic': 'academic',
                    'employees': 'employees',
                    'private': 'private'
                };

                if (categoryMap[category]) {
                    url += '?scholarship_type=' + categoryMap[category];
                }
            }

            // Redirect to the URL with the scholarship type filter
            window.location.href = url;
        }

        // Import functions
        let selectedFile = null;

        function triggerFileImport() {
            document.getElementById('hiddenFileInput').click();
        }

        function handleFileSelection(event) {
            const file = event.target.files[0];
            if (file) {
                selectedFile = file;
                document.getElementById('selectedFileName').textContent = file.name;
                document.getElementById('scholarshipTypeModal').style.display = 'block';
            }
        }

        function closeScholarshipTypeModal() {
            document.getElementById('scholarshipTypeModal').style.display = 'none';
            document.getElementById('scholarshipTypeSelect').value = '';
            document.getElementById('updateExistingCheckbox').checked = false;
            selectedFile = null;
            // Reset the file input
            document.getElementById('hiddenFileInput').value = '';
        }

        async function proceedWithImport() {
            const scholarshipType = document.getElementById('scholarshipTypeSelect').value;
            const updateExisting = document.getElementById('updateExistingCheckbox').checked;

            if (!scholarshipType) {
                alert('Please select a scholarship type.');
                return;
            }

            if (!selectedFile) {
                alert('No file selected.');
                return;
            }

            const submitBtn = event.target;
            const originalText = submitBtn.textContent;

            // Show loading state
            submitBtn.textContent = 'Importing...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('file', selectedFile);
                formData.append('scholarship_type', scholarshipType);
                formData.append('update_existing', updateExisting ? '1' : '0');

                const response = await fetch('/admin/import-students', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    alert(`Import successful! ${result.message}`);
                    closeScholarshipTypeModal();
                    // Reload the page to show new grantees
                    window.location.reload();
                } else {
                    alert(`Import failed: ${result.message}`);
                }
            } catch (error) {
                console.error('Import error:', error);
                alert('An error occurred during import. Please try again.');
            } finally {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }

        // Simple, direct course population functions
        function populateCoursesDirectly(department) {
            console.log('=== INSTITUTIONAL COURSE POPULATION ===');
            console.log('Department selected:', department);
            console.log('Function called at:', new Date().toLocaleTimeString());

            const courseSelect = document.getElementById('course');
            console.log('Course select element:', courseSelect);

            if (!courseSelect) {
                console.error('Course select not found - this is the problem!');
                // Try to find it with a different approach
                const allSelects = document.querySelectorAll('select');
                console.log('All select elements found:', allSelects);
                allSelects.forEach((select, index) => {
                    console.log(`Select ${index}:`, select.id, select.name);
                });
                return;
            }

            console.log('Course select found, proceeding...');

            // Clear existing options
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            courseSelect.disabled = true;

            if (!department) {
                console.log('No department provided, stopping');
                return;
            }

            const courses = {
                'SITE': [
                    'Bachelor of Science in Information Technology',
                    'Bachelor of Science in Computer Science',
                    'Bachelor of Science in Computer Engineering',
                    'Bachelor of Library and Information Science',
                    'Bachelor of Science in Civil Engineering',
                    'Bachelor of Science in Environmental and Sanitary Engineering'
                ],
                'SASTE': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education',
                    'Bachelor of Science in Psychology',
                    'Bachelor of Arts in English Language Studies',
                    'Bachelor of Science in Biology',
                    'Bachelor of Science in Social Work',
                    'Bachelor of Science in Public Administration',
                    'Bachelor of Physical Education'
                ],
                'SBAHM': [
                    'Bachelor of Science in Business Administration',
                    'Bachelor of Science in Hospitality Management',
                    'Bachelor of Science in Tourism Management',
                    'Bachelor of Science in Entrepreneurship'
                ],
                'SNAHS': [
                    'Bachelor of Science in Nursing',
                    'Bachelor of Science in Medical Technology',
                    'Bachelor of Science in Pharmacy',
                    'Bachelor of Science in Physical Therapy'
                ]
            };

            if (courses[department]) {
                console.log('Found courses for department:', department);
                console.log('Courses to add:', courses[department]);

                courses[department].forEach((course, index) => {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                    console.log(`Added course ${index + 1}:`, course);
                });

                courseSelect.disabled = false;
                console.log('Course select enabled');
                console.log('Final course select HTML:', courseSelect.innerHTML);
                console.log('Course select disabled status:', courseSelect.disabled);
                console.log('=== COURSE POPULATION COMPLETE ===');
            } else {
                console.error('No courses found for department:', department);
                console.log('Available departments:', Object.keys(courses));
            }
        }

        function populateChedCoursesDirectly(department) {
            console.log('Direct course population for CHED:', department);

            const courseSelect = document.getElementById('chedCourse');
            if (!courseSelect) {
                console.error('CHED Course select not found');
                return;
            }

            // Clear existing options
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            courseSelect.disabled = true;

            if (!department) return;

            const courses = {
                'SITE': [
                    'Bachelor of Science in Information Technology',
                    'Bachelor of Science in Computer Science',
                    'Bachelor of Science in Computer Engineering',
                    'Bachelor of Library and Information Science',
                    'Bachelor of Science in Civil Engineering',
                    'Bachelor of Science in Environmental and Sanitary Engineering'
                ],
                'SASTE': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education',
                    'Bachelor of Science in Psychology',
                    'Bachelor of Arts in English Language Studies',
                    'Bachelor of Science in Biology',
                    'Bachelor of Science in Social Work',
                    'Bachelor of Science in Public Administration',
                    'Bachelor of Physical Education'
                ],
                'SBAHM': [
                    'Bachelor of Science in Business Administration',
                    'Bachelor of Science in Hospitality Management',
                    'Bachelor of Science in Tourism Management',
                    'Bachelor of Science in Entrepreneurship'
                ],
                'SNAHS': [
                    'Bachelor of Science in Nursing',
                    'Bachelor of Science in Medical Technology',
                    'Bachelor of Science in Pharmacy',
                    'Bachelor of Science in Physical Therapy'
                ]
            };

            if (courses[department]) {
                courses[department].forEach(course => {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
                courseSelect.disabled = false;
                console.log('CHED Courses populated successfully for', department);
            }
        }

        function showAddStudentForm() {
            console.log('Add Student button clicked');
            const modal = document.getElementById('addStudentModal');

            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('modal-show');
                console.log('Modal opened successfully');

                // Test course population after modal opens
                setTimeout(() => {
                    console.log('Testing course population after modal open...');
                    const deptSelect = document.getElementById('department');
                    const courseSelect = document.getElementById('course');
                    console.log('Department select found:', !!deptSelect);
                    console.log('Course select found:', !!courseSelect);

                    if (deptSelect && courseSelect) {
                        console.log('Both selects found, testing population...');
                        populateCoursesDirectly('SITE');
                    }
                }, 100);
            } else {
                console.error('Modal not found!');
            }
        }

        function closeAddStudentModal() {
            const modal = document.getElementById('addStudentModal');
            modal.style.display = 'none';
            modal.classList.remove('modal-show');
            document.getElementById('addStudentForm').reset();
            document.getElementById('dynamicFields').innerHTML = '';
        }

        function updateFormFields() {
            const scholarshipType = document.getElementById('scholarshipType').value;
            const dynamicFields = document.getElementById('dynamicFields');

            // Clear previous dynamic fields
            dynamicFields.innerHTML = '';

            if (!scholarshipType) return;

            let fieldsHTML = '';

            switch (scholarshipType) {
                case 'ched':
                    fieldsHTML = `
                        <div class="form-section">
                            <h3>CHED Scholarship Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="educationStage">Education Stage *</label>
                                    <select id="educationStage" name="education_stage" required onchange="updateChedEducationFields()">
                                        <option value="">Select Education Stage</option>
                                        <option value="College">College</option>
                                        <option value="BSU">Basic Education</option>
                                    </select>
                                </div>
                            </div>
                            <div id="chedEducationSpecificFields"></div>

                            <!-- Parents Information -->
                            <div class="form-section">
                                <h3>Parents Information</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="fatherFirstName">Father's First Name</label>
                                        <input type="text" id="fatherFirstName" name="father_first_name">
                                    </div>
                                    <div class="form-group">
                                        <label for="fatherLastName">Father's Last Name</label>
                                        <input type="text" id="fatherLastName" name="father_last_name">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="motherFirstName">Mother's First Name</label>
                                        <input type="text" id="motherFirstName" name="mother_first_name">
                                    </div>
                                    <div class="form-group">
                                        <label for="motherLastName">Mother's Last Name</label>
                                        <input type="text" id="motherLastName" name="mother_last_name">
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="form-section">
                                <h3>Address Information</h3>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="street">Street</label>
                                        <input type="text" id="street" name="street">
                                    </div>
                                    <div class="form-group">
                                        <label for="barangay">Barangay</label>
                                        <input type="text" id="barangay" name="barangay">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" id="city" name="city">
                                    </div>
                                    <div class="form-group">
                                        <label for="province">Province</label>
                                        <input type="text" id="province" name="province">
                                    </div>
                                    <div class="form-group">
                                        <label for="zipcode">Zip Code</label>
                                        <input type="text" id="zipcode" name="zipcode">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    break;

                case 'academic':
                    fieldsHTML = `
                        <div class="form-section">
                            <h3>Academic Scholarship Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="educationStage">Education Stage *</label>
                                    <select id="educationStage" name="education_stage" required onchange="updateEducationFields()">
                                        <option value="">Select Education Stage</option>
                                        <option value="College">College</option>
                                        <option value="BSU">Basic Education</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="semester">Semester *</label>
                                    <select id="semester" name="semester" required onchange="checkAndShowSubjects()">
                                        <option value="">Select Semester</option>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="academicYear">Academic Year *</label>
                                    <input type="text" id="academicYear" name="academic_year" placeholder="e.g., 2024-2025" required>
                                </div>
                            </div>
                            <div id="educationSpecificFields"></div>

                            <!-- Subjects Section -->
                            <div id="subjectsSection" style="display: none;">
                                <div class="form-section">
                                    <h3>Subjects and Grades</h3>
                                    <div id="subjectsContainer"></div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="calculatedGwa">Calculated GWA</label>
                                            <input type="number" id="calculatedGwa" name="gwa" step="0.01" min="1.0" max="4.0" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    break;

                case 'employees':
                    fieldsHTML = `
                        <div class="form-section">
                            <h3>Employee Scholarship Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="employeeName">Employee Name *</label>
                                    <input type="text" id="employeeName" name="employee_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="employeeRelationship">Relationship *</label>
                                    <select id="employeeRelationship" name="employee_relationship" required>
                                        <option value="">Select Relationship</option>
                                        <option value="Son">Son</option>
                                        <option value="Daughter">Daughter</option>
                                        <option value="Spouse">Spouse</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="employeeDepartment">Employee Department *</label>
                                    <input type="text" id="employeeDepartment" name="employee_department" required>
                                </div>
                                <div class="form-group">
                                    <label for="employeePosition">Employee Position *</label>
                                    <input type="text" id="employeePosition" name="employee_position" required>
                                </div>
                            </div>
                        </div>
                    `;
                    break;

                case 'private':
                    fieldsHTML = `
                        <div class="form-section">
                            <h3>Private Scholarship Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="scholarshipName">Scholarship Name *</label>
                                    <input type="text" id="scholarshipName" name="scholarship_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="otherScholarship">Other Scholarship Details</label>
                                    <textarea id="otherScholarship" name="other_scholarship" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    `;
                    break;
            }

            dynamicFields.innerHTML = fieldsHTML;
        }

        function updateEducationFields() {
            const educationStage = document.getElementById('educationStage').value;
            const specificFields = document.getElementById('educationSpecificFields');

            if (!specificFields) return;

            let fieldsHTML = '';

            if (educationStage === 'College') {
                fieldsHTML = `
                    <div class="form-row">
                        <div class="form-group">
                            <label for="department">Department *</label>
                            <select id="department" name="department" required onchange="populateCoursesDirectly(this.value); console.log('Department changed to:', this.value);">
                                <option value="">Select Department</option>
                                <option value="SITE">School of Information Technology and Engineering (SITE)</option>
                                <option value="SASTE">School of Arts, Sciences and Teacher Education (SASTE)</option>
                                <option value="SBAHM">School of Business Administration and Hospitality Management (SBAHM)</option>
                                <option value="SNAHS">School of Nursing and Allied Health Sciences (SNAHS)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="course">Course *</label>
                            <select id="course" name="course" required onchange="loadYearLevelsAndCheckSubjects()">
                                <option value="">Select Course</option>
                                <option value="Bachelor of Science in Information Technology">Bachelor of Science in Information Technology</option>
                                <option value="Bachelor of Science in Computer Science">Bachelor of Science in Computer Science</option>
                                <option value="Bachelor of Science in Computer Engineering">Bachelor of Science in Computer Engineering</option>
                                <option value="Bachelor of Library and Information Science">Bachelor of Library and Information Science</option>
                                <option value="Bachelor of Science in Civil Engineering">Bachelor of Science in Civil Engineering</option>
                                <option value="Bachelor of Science in Environmental and Sanitary Engineering">Bachelor of Science in Environmental and Sanitary Engineering</option>
                            </select>
                            <small style="color: #666; font-size: 12px;">
                                <button type="button" onclick="populateCoursesDirectly('SITE')" style="background: #1e5631; color: white; border: none; padding: 4px 8px; border-radius: 4px; font-size: 11px; margin-top: 5px;">
                                    🔄 Refresh Courses
                                </button>
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="yearLevel">Year Level *</label>
                            <select id="yearLevel" name="year_level" required disabled onchange="checkAndShowSubjects()">
                                <option value="">Select Year Level</option>
                            </select>
                        </div>
                    </div>
                `;
            } else if (educationStage === 'BSU') {
                fieldsHTML = `
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gradeLevel">Grade Level *</label>
                            <select id="gradeLevel" name="grade_level" required>
                                <option value="">Select Grade Level</option>
                                <option value="Grade 11">Grade 11</option>
                                <option value="Grade 12">Grade 12</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="strand">Strand *</label>
                            <input type="text" id="strand" name="strand" required>
                        </div>
                    </div>
                `;
            }

            specificFields.innerHTML = fieldsHTML;

            // Hide subjects section when education stage changes
            hideSubjectsSection();
        }

        // CHED Education Fields Handler
        function updateChedEducationFields() {
            const educationStage = document.getElementById('educationStage').value;
            const specificFields = document.getElementById('chedEducationSpecificFields');

            if (!specificFields) return;

            let fieldsHTML = '';

            if (educationStage === 'College') {
                fieldsHTML = `
                    <div class="form-section">
                        <h3>Academic Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="chedDepartment">Department *</label>
                                <select id="chedDepartment" name="department" required onchange="populateChedCoursesDirectly(this.value)">
                                    <option value="">Select Department</option>
                                    <option value="SITE">School of Information Technology and Engineering (SITE)</option>
                                    <option value="SASTE">School of Arts, Sciences and Teacher Education (SASTE)</option>
                                    <option value="SBAHM">School of Business Administration and Hospitality Management (SBAHM)</option>
                                    <option value="SNAHS">School of Nursing and Allied Health Sciences (SNAHS)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="chedCourse">Course *</label>
                                <select id="chedCourse" name="course" required disabled>
                                    <option value="">Select Course</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="chedYearLevel">Year Level *</label>
                                <select id="chedYearLevel" name="year_level" required>
                                    <option value="">Select Year Level</option>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;
            } else if (educationStage === 'BSU') {
                fieldsHTML = `
                    <div class="form-section">
                        <h3>Basic Education Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="chedGradeLevel">Grade Level *</label>
                                <select id="chedGradeLevel" name="grade_level" required onchange="updateChedStrandField()">
                                    <option value="">Select Grade Level</option>
                                    <option value="Grade 7">Grade 7</option>
                                    <option value="Grade 8">Grade 8</option>
                                    <option value="Grade 9">Grade 9</option>
                                    <option value="Grade 10">Grade 10</option>
                                    <option value="Grade 11">Grade 11</option>
                                    <option value="Grade 12">Grade 12</option>
                                </select>
                            </div>
                            <div id="chedStrandField"></div>
                        </div>
                    </div>
                `;
            }

            specificFields.innerHTML = fieldsHTML;
        }

        function updateChedStrandField() {
            const gradeLevel = document.getElementById('chedGradeLevel').value;
            const strandField = document.getElementById('chedStrandField');

            if (!strandField) return;

            let strandHTML = '';

            if (gradeLevel === 'Grade 11' || gradeLevel === 'Grade 12') {
                strandHTML = `
                    <div class="form-group">
                        <label for="chedStrand">Strand *</label>
                        <select id="chedStrand" name="strand" required>
                            <option value="">Select Strand</option>
                            <option value="STEM">Science, Technology, Engineering and Mathematics (STEM)</option>
                            <option value="ABM">Accountancy, Business and Management (ABM)</option>
                            <option value="HUMSS">Humanities and Social Sciences (HUMSS)</option>
                            <option value="GAS">General Academic Strand (GAS)</option>
                            <option value="TVL-ICT">Technical-Vocational-Livelihood - Information and Communications Technology (TVL-ICT)</option>
                            <option value="TVL-HE">Technical-Vocational-Livelihood - Home Economics (TVL-HE)</option>
                            <option value="TVL-IA">Technical-Vocational-Livelihood - Industrial Arts (TVL-IA)</option>
                            <option value="TVL-Agri">Technical-Vocational-Livelihood - Agri-Fishery Arts (TVL-Agri)</option>
                            <option value="Arts and Design">Arts and Design Track</option>
                            <option value="Sports">Sports Track</option>
                        </select>
                    </div>
                `;
            }

            strandField.innerHTML = strandHTML;
        }

        async function loadChedCoursesByDepartment() {
            const departmentSelect = document.getElementById('chedDepartment');
            const courseSelect = document.getElementById('chedCourse');

            console.log('Loading CHED courses for department...');

            if (!departmentSelect || !courseSelect) {
                console.error('Department or course select not found');
                return;
            }

            const selectedDepartment = departmentSelect.value;
            console.log('Selected department:', selectedDepartment);

            // Clear existing options
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            courseSelect.disabled = true;

            if (!selectedDepartment) return;

            // Use fallback courses immediately for reliability
            const fallbackCourses = {
                'SITE': [
                    'Bachelor of Science in Information Technology',
                    'Bachelor of Science in Computer Science',
                    'Bachelor of Science in Computer Engineering',
                    'Bachelor of Library and Information Science',
                    'Bachelor of Science in Civil Engineering',
                    'Bachelor of Science in Environmental and Sanitary Engineering'
                ],
                'SASTE': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education',
                    'Bachelor of Science in Psychology',
                    'Bachelor of Arts in English Language Studies',
                    'Bachelor of Science in Biology',
                    'Bachelor of Science in Social Work',
                    'Bachelor of Science in Public Administration',
                    'Bachelor of Physical Education'
                ],
                'SBAHM': [
                    'Bachelor of Science in Business Administration',
                    'Bachelor of Science in Hospitality Management',
                    'Bachelor of Science in Tourism Management',
                    'Bachelor of Science in Entrepreneurship'
                ],
                'SNAHS': [
                    'Bachelor of Science in Nursing',
                    'Bachelor of Science in Medical Technology',
                    'Bachelor of Science in Pharmacy',
                    'Bachelor of Science in Physical Therapy'
                ]
            };

            try {
                // Try to load from API first
                console.log('Attempting to fetch from API...');
                const response = await fetch('/api/scholarship/department-course-mapping');
                console.log('API response status:', response.status);

                if (response.ok) {
                    const apiData = await response.json();
                    console.log('API data received:', apiData);

                    if (apiData[selectedDepartment] && apiData[selectedDepartment].length > 0) {
                        console.log('Using API data for courses');
                        apiData[selectedDepartment].forEach(course => {
                            const option = document.createElement('option');
                            option.value = course;
                            option.textContent = course;
                            courseSelect.appendChild(option);
                        });
                        courseSelect.disabled = false;
                        return;
                    }
                }
            } catch (error) {
                console.error('API error:', error);
            }

            // Use fallback data
            console.log('Using fallback courses for department:', selectedDepartment);
            if (fallbackCourses[selectedDepartment]) {
                fallbackCourses[selectedDepartment].forEach(course => {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
                courseSelect.disabled = false;
                console.log('Courses loaded successfully from fallback');
            } else {
                console.error('No courses found for department:', selectedDepartment);
            }
        }

        // Department-Course mapping and subjects functionality
        let departmentCourses = {};

        async function loadCoursesByDepartment() {
            const departmentSelect = document.getElementById('department');
            const courseSelect = document.getElementById('course');
            const yearLevelSelect = document.getElementById('yearLevel');

            console.log('Loading Institutional courses for department...');

            if (!departmentSelect || !courseSelect) {
                console.error('Department or course select not found for Institutional');
                return;
            }

            const selectedDepartment = departmentSelect.value;
            console.log('Selected department for Institutional:', selectedDepartment);

            // Clear existing options
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            if (yearLevelSelect) {
                yearLevelSelect.innerHTML = '<option value="">Select Year Level</option>';
                yearLevelSelect.disabled = true;
            }
            courseSelect.disabled = true;
            hideSubjectsSection();

            if (!selectedDepartment) return;

            // Use fallback courses immediately for reliability
            const fallbackCourses = {
                'SITE': [
                    'Bachelor of Science in Information Technology',
                    'Bachelor of Science in Computer Science',
                    'Bachelor of Science in Computer Engineering',
                    'Bachelor of Library and Information Science',
                    'Bachelor of Science in Civil Engineering',
                    'Bachelor of Science in Environmental and Sanitary Engineering'
                ],
                'SASTE': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education',
                    'Bachelor of Science in Psychology',
                    'Bachelor of Arts in English Language Studies',
                    'Bachelor of Science in Biology',
                    'Bachelor of Science in Social Work',
                    'Bachelor of Science in Public Administration',
                    'Bachelor of Physical Education'
                ],
                'SBAHM': [
                    'Bachelor of Science in Business Administration',
                    'Bachelor of Science in Hospitality Management',
                    'Bachelor of Science in Tourism Management',
                    'Bachelor of Science in Entrepreneurship'
                ],
                'SNAHS': [
                    'Bachelor of Science in Nursing',
                    'Bachelor of Science in Medical Technology',
                    'Bachelor of Science in Pharmacy',
                    'Bachelor of Science in Physical Therapy'
                ]
            };

            try {
                // Try to load from API first
                console.log('Attempting to fetch from API for Institutional...');
                const response = await fetch('/api/scholarship/department-course-mapping');
                console.log('API response status for Institutional:', response.status);

                if (response.ok) {
                    const apiData = await response.json();
                    console.log('API data received for Institutional:', apiData);

                    if (apiData[selectedDepartment] && apiData[selectedDepartment].length > 0) {
                        console.log('Using API data for Institutional courses');
                        apiData[selectedDepartment].forEach(course => {
                            const option = document.createElement('option');
                            option.value = course;
                            option.textContent = course;
                            courseSelect.appendChild(option);
                        });
                        courseSelect.disabled = false;
                        return;
                    }
                }
            } catch (error) {
                console.error('API error for Institutional:', error);
            }

            // Use fallback data
            console.log('Using fallback courses for Institutional department:', selectedDepartment);
            if (fallbackCourses[selectedDepartment]) {
                fallbackCourses[selectedDepartment].forEach(course => {
                    const option = document.createElement('option');
                    option.value = course;
                    option.textContent = course;
                    courseSelect.appendChild(option);
                });
                courseSelect.disabled = false;
                console.log('Institutional courses loaded successfully from fallback');
            } else {
                console.error('No courses found for Institutional department:', selectedDepartment);
            }
        }

        async function loadYearLevelsAndCheckSubjects() {
            const courseSelect = document.getElementById('course');
            const yearLevelSelect = document.getElementById('yearLevel');

            if (!courseSelect || !yearLevelSelect) return;

            const selectedCourse = courseSelect.value;
            yearLevelSelect.innerHTML = '<option value="">Select Year Level</option>';
            yearLevelSelect.disabled = true;
            hideSubjectsSection();

            if (!selectedCourse) return;

            try {
                // Get course duration to determine available year levels
                const response = await fetch(`/api/scholarship/course-durations`);
                const courseDurations = await response.json();

                // Find the course duration
                let duration = 4; // Default to 4 years
                for (const [course, years] of Object.entries(courseDurations)) {
                    if (course === selectedCourse) {
                        duration = years;
                        break;
                    }
                }

                // Add year level options based on course duration
                for (let i = 1; i <= duration; i++) {
                    const option = document.createElement('option');
                    const yearText = i === 1 ? '1st Year' : i === 2 ? '2nd Year' : i === 3 ? '3rd Year' : i === 4 ?
                        '4th Year' : `${i}th Year`;
                    option.value = yearText;
                    option.textContent = yearText;
                    yearLevelSelect.appendChild(option);
                }

                // Enable year level select
                yearLevelSelect.disabled = false;
            } catch (error) {
                console.error('Error loading year levels:', error);
                // Fallback to default year levels
                ['1st Year', '2nd Year', '3rd Year', '4th Year'].forEach(year => {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    yearLevelSelect.appendChild(option);
                });
                yearLevelSelect.disabled = false;
            }
        }

        function checkAndShowSubjects() {
            const courseSelect = document.getElementById('course');
            const semesterSelect = document.getElementById('semester');
            const yearLevelSelect = document.getElementById('yearLevel');

            if (!courseSelect || !semesterSelect || !yearLevelSelect) return;

            const selectedCourse = courseSelect.value;
            const selectedSemester = semesterSelect.value;
            const selectedYearLevel = yearLevelSelect.value;

            if (selectedCourse && selectedSemester && selectedYearLevel) {
                loadSubjectsFromAPI(selectedCourse, selectedYearLevel, selectedSemester);
            } else {
                hideSubjectsSection();
            }
        }

        async function loadSubjectsFromAPI(courseName, yearLevel, semester) {
            try {
                // Convert year level to number for API
                const yearLevelNumber = parseInt(yearLevel.replace(/\D/g, ''));

                const response = await fetch(
                    `/api/scholarship/subjects/${encodeURIComponent(courseName)}/${yearLevelNumber}/${encodeURIComponent(semester)}`
                );
                const data = await response.json();

                if (response.ok && data.subjects && data.subjects.length > 0) {
                    showSubjectsFromAPI(data.subjects);
                } else {
                    showNoSubjectsMessage(courseName, yearLevel, semester);
                }
            } catch (error) {
                console.error('Error loading subjects:', error);
                showNoSubjectsMessage(courseName, yearLevel, semester);
            }
        }

        function showSubjectsFromAPI(subjects) {
            const subjectsSection = document.getElementById('subjectsSection');
            const subjectsContainer = document.getElementById('subjectsContainer');

            if (!subjectsSection || !subjectsContainer) return;

            let subjectsHTML = '';

            subjects.forEach((subject, index) => {
                subjectsHTML += `
                    <div class="subject-row">
                        <div class="subject-info">
                            <span class="subject-code">${subject.code}</span>
                            <span class="subject-title">${subject.title}</span>
                            <span class="subject-units">${subject.units} units</span>
                        </div>
                        <div class="grade-input">
                            <input type="number"
                                   name="subject_grades[${subject.code}]"
                                   placeholder="Grade"
                                   min="1.0"
                                   max="4.0"
                                   step="0.01"
                                   onchange="calculateGWA()"
                                   data-units="${subject.units}">
                        </div>
                    </div>
                `;
            });

            subjectsContainer.innerHTML = subjectsHTML;
            subjectsSection.style.display = 'block';
        }

        function showNoSubjectsMessage(courseName, yearLevel, semester) {
            const subjectsSection = document.getElementById('subjectsSection');
            const subjectsContainer = document.getElementById('subjectsContainer');

            if (!subjectsSection || !subjectsContainer) return;

            subjectsContainer.innerHTML = `
                <div class="no-subjects-message">
                    <p>No subjects found for ${courseName} - ${yearLevel} - ${semester}</p>
                    <p>Please contact the administrator to add subjects for this course.</p>
                </div>
            `;
            subjectsSection.style.display = 'block';
        }

        function hideSubjectsSection() {
            const subjectsSection = document.getElementById('subjectsSection');
            if (subjectsSection) {
                subjectsSection.style.display = 'none';
            }
        }

        function calculateGWA() {
            const gradeInputs = document.querySelectorAll('input[name^="subject_grades"]');
            const gwaInput = document.getElementById('calculatedGwa');

            if (!gwaInput) return;

            let totalGradePoints = 0;
            let totalUnits = 0;
            let hasAllGrades = true;

            gradeInputs.forEach(input => {
                const grade = parseFloat(input.value);
                const units = parseFloat(input.dataset.units);

                if (!isNaN(grade) && grade > 0) {
                    totalGradePoints += grade * units;
                    totalUnits += units;
                } else {
                    hasAllGrades = false;
                }
            });

            if (hasAllGrades && totalUnits > 0) {
                const gwa = totalGradePoints / totalUnits;
                gwaInput.value = gwa.toFixed(2);
            } else {
                gwaInput.value = '';
            }
        }

        function saveNewStudent(event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('addStudentForm'));
            const studentData = Object.fromEntries(formData);

            // Here you would typically send the data to your backend
            console.log('Saving new student:', studentData);

            // Close the modal
            closeAddStudentModal();

            // TODO: Implement actual save functionality with backend API
            // TODO: Refresh the table with new student data
        }

        function editStudent(applicationId, studentId) {
            // Get the button that was clicked to access data attributes
            const button = event.target.closest('button');

            // Populate modal with student data
            document.getElementById('editStudentId').value = button.dataset.studentId;
            document.getElementById('editStudentName').value = button.dataset.studentName;
            document.getElementById('editStudentEmail').value = button.dataset.studentEmail;
            document.getElementById('editStudentContact').value = button.dataset.studentContact;
            document.getElementById('editStudentCourse').value = button.dataset.studentCourse;
            document.getElementById('editStudentDepartment').value = button.dataset.studentDepartment;
            document.getElementById('editStudentYear').value = button.dataset.studentYear;
            document.getElementById('editStudentGwa').value = button.dataset.studentGwa;
            document.getElementById('editApplicationId').value = button.dataset.applicationId;

            // Show modal
            document.getElementById('editStudentModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editStudentModal').style.display = 'none';
        }

        function saveStudentChanges(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const applicationId = formData.get('application_id');

            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;

            // Send update request
            fetch(`/admin/students/${applicationId}/update`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the table row with new data
                        updateTableRow(formData);
                        closeEditModal();
                    } else {
                        console.error(data.message || 'Failed to update student information.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    // Reset button state
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                });
        }

        function updateTableRow(formData) {
            const studentId = formData.get('student_id');
            const rows = document.querySelectorAll('#studentsTableBody tr');

            rows.forEach(row => {
                const firstCell = row.querySelector('td');
                if (firstCell && firstCell.textContent.trim() === studentId) {
                    // Update the row data
                    const cells = row.querySelectorAll('td');
                    cells[1].textContent = formData.get('name'); // Name
                    cells[2].textContent = formData.get('course'); // Course
                    cells[5].textContent = formData.get('gwa') || 'N/A'; // GWA

                    // Update data attributes on the edit button
                    const editBtn = row.querySelector('.action-btn.edit');
                    editBtn.dataset.studentName = formData.get('name');
                    editBtn.dataset.studentEmail = formData.get('email');
                    editBtn.dataset.studentContact = formData.get('contact_number');
                    editBtn.dataset.studentCourse = formData.get('course');
                    editBtn.dataset.studentDepartment = formData.get('department');
                    editBtn.dataset.studentYear = formData.get('year_level');
                    editBtn.dataset.studentGwa = formData.get('gwa');
                }
            });
        }



        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const editModal = document.getElementById('editStudentModal');
            const addModal = document.getElementById('addStudentModal');

            if (event.target === editModal) {
                closeEditModal();
            } else if (event.target === addModal) {
                closeAddStudentModal();
            }
        }

        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentElement;
            const menu = dropdown.querySelector('.dropdown-menu');
            const arrow = dropdown.querySelector('.dropdown-arrow');

            dropdown.classList.toggle('open');

            if (dropdown.classList.contains('open')) {
                menu.style.maxHeight = menu.scrollHeight + 'px';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                menu.style.maxHeight = '0';
                arrow.style.transform = 'rotate(0deg)';
            }
        }
    </script>
@endpush
