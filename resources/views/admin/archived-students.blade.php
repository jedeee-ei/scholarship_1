@extends('layouts.admin')

@section('title', 'Archives')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/students.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/archived-students.css') }}">
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[['title' => 'Archives', 'active' => true]]" />
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>Archives</h1>
        <div class="date">{{ date('F d, Y') }}</div>
    </div>


    <!-- Archive Categories -->
    <div class="student-categories">
        <div class="category-tabs">
            <div class="tab-group">
                <button class="tab-btn active" onclick="showArchiveCategory('masterlist', this)">Masterlist</button>
                <button class="tab-btn" onclick="showArchiveCategory('inactive', this)">Inactive</button>
            </div>
            <div class="archive-actions">
                <button class="export-btn" onclick="exportArchivedStudents()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- Archived Grantees Table -->
    <div class="student-table-container">
        <div class="table-header hidden">
            <h3 id="archiveTableTitle"></h3>
        </div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>Grantee ID</th>
                    <th>Name</th>
                    <th>Benefactor Type</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="archivedStudentsTableBody">
                @forelse($archivedStudents as $student)
                    <tr data-year="{{ $student->archived_academic_year }}" data-semester="{{ $student->archived_semester }}"
                        data-type="{{ $student->scholarship_type }}"
                        data-archive-type="{{ $student->archive_type ?? 'masterlist' }}"
                        data-search="{{ strtolower($student->first_name . ' ' . $student->last_name . ' ' . $student->student_id) }}">
                        <td>{{ $student->student_id }}</td>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td>
                            <span class="benefactor-badge">
                                {{ ucfirst($student->scholarship_type) }}
                            </span>
                        </td>
                        <td>
                            <span class="remarks-badge {{ $student->archive_type === 'inactive' ? 'inactive-remarks' : 'masterlist-remarks' }}"
                                  title="Archive Type: {{ $student->archive_type }} | Remarks: {{ $student->remarks ?? 'NULL' }}">
                                @if($student->archive_type === 'inactive')
                                    @if($student->remarks)
                                        {{ $student->remarks }}
                                    @else
                                        No specific reason provided
                                    @endif
                                @else
                                    Open for Renewal
                                @endif
                            </span>
                        </td>
                        <td>
                            <button class="action-btn view" onclick="viewArchivedStudent({{ $student->id }})"
                                title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            No archived grantees found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Archived Student Details Modal -->
    <div id="archivedStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Archived Student Details</h2>
                <span class="close" onclick="closeArchivedStudentModal()">&times;</span>
            </div>
            <div class="modal-body" id="archivedStudentDetails">
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading student details...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize navigation to ensure proper link behavior
        document.addEventListener('DOMContentLoaded', function() {
            initializeNavigation();
            // Initialize masterlist tab as active on page load
            showArchiveCategory('masterlist', document.querySelector('.tab-btn.active'));
        });

        function initializeNavigation() {
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Allow default link behavior
                    return true;
                });
            });
        }

        function filterArchives() {
            const yearFilter = document.getElementById('yearFilter').value;
            const semesterFilter = document.getElementById('semesterFilter').value;
            const typeFilter = document.getElementById('typeFilter').value;
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();

            const rows = document.querySelectorAll('#archivedStudentsTableBody tr[data-year]');

            rows.forEach(row => {
                const year = row.dataset.year;
                const semester = row.dataset.semester;
                const type = row.dataset.type;
                const searchText = row.dataset.search;

                let show = true;

                if (yearFilter && year !== yearFilter) show = false;
                if (semesterFilter && semester !== semesterFilter) show = false;
                if (typeFilter && type !== typeFilter) show = false;
                if (searchFilter && !searchText.includes(searchFilter)) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        function viewArchivedStudent(studentId) {
            console.log('Viewing archived grantee:', studentId);

            // Show modal
            document.getElementById('archivedStudentModal').style.display = 'block';

            // Show loading state
            document.getElementById('archivedStudentDetails').innerHTML = `
                <div class="loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading student details...</p>
                </div>
            `;

            // Fetch student details
            fetch(`/admin/archived-students/${studentId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayArchivedStudentDetails(data.student);
                    } else {
                        document.getElementById('archivedStudentDetails').innerHTML = `
                            <div class="error">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p>Error loading student details: ${data.message}</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('archivedStudentDetails').innerHTML = `
                        <div class="error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>Unable to load student details. Please try again.</p>
                        </div>
                    `;
                });
        }

        function displayArchivedStudentDetails(student) {
            const detailsHtml = `
                <div class="student-details-grid">
                    <div class="detail-section">
                        <h3>Personal Information</h3>
                        <div class="detail-row">
                            <span class="label">Student ID:</span>
                            <span class="value">${student.student_id}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Full Name:</span>
                            <span class="value">${student.first_name} ${student.last_name}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Email:</span>
                            <span class="value">${student.email || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Contact Number:</span>
                            <span class="value">${student.contact_number || 'N/A'}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3>Academic Information</h3>
                        <div class="detail-row">
                            <span class="label">Course:</span>
                            <span class="value">${student.course || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Department:</span>
                            <span class="value">${student.department || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Year Level:</span>
                            <span class="value">${student.year_level || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">GWA:</span>
                            <span class="value">${student.gwa || 'N/A'}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3>Scholarship Information</h3>
                        <div class="detail-row">
                            <span class="label">Application ID:</span>
                            <span class="value">${student.original_application_id}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Scholarship Type:</span>
                            <span class="value">${student.scholarship_type}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Archive Type:</span>
                            <span class="value badge ${student.archive_type}">${student.archive_type.charAt(0).toUpperCase() + student.archive_type.slice(1)}</span>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3>Archive Information</h3>
                        <div class="detail-row">
                            <span class="label">Archived Semester:</span>
                            <span class="value">${student.archived_semester}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Archived Academic Year:</span>
                            <span class="value">${student.archived_academic_year}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Archived Date:</span>
                            <span class="value">${new Date(student.archived_at).toLocaleDateString()}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Archived By:</span>
                            <span class="value">${student.archived_by || 'System'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Remarks:</span>
                            <span class="value remarks ${student.archive_type}">
                                ${student.archive_type === 'inactive'
                                    ? (student.remarks ? student.remarks : 'No specific reason provided')
                                    : 'Open for Renewal'
                                }
                            </span>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('archivedStudentDetails').innerHTML = detailsHtml;
        }

        function closeArchivedStudentModal() {
            document.getElementById('archivedStudentModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('archivedStudentModal');
            if (event.target === modal) {
                closeArchivedStudentModal();
            }
        }

        function exportArchivedStudents() {
            // Get the currently active tab to determine export type
            const activeTab = document.querySelector('.tab-btn.active');
            const archiveType = activeTab.textContent.toLowerCase().trim();

            // Build export URL with type parameter
            let exportUrl = '{{ route('admin.archived-students.export') }}';
            if (archiveType !== 'all') {
                exportUrl += '?type=' + archiveType;
            }

            // Trigger download
            window.location.href = exportUrl;
        }

        function showArchiveCategory(category, button) {
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Add active class to clicked button
            button.classList.add('active');

            // Filter table rows based on category (no title update for cleaner look)
            const rows = document.querySelectorAll('#archivedStudentsTableBody tr');
            rows.forEach(row => {
                const archiveType = row.dataset.archiveType;
                if (category === 'all' || archiveType === category) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
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
