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
        <div class="table-header" style="display: none;">
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
                            <span class="remarks-badge">
                                {{ $student->remarks ?? 'N/A' }}
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
            // TODO: Implement view archived grantee details modal
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
