@extends('layouts.admin')

@section('title', 'Archives')

@push('styles')
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



    <!-- Archived Grantees Table -->
    <div class="table-container">
        <div class="table-header">
            <h3>Archived Grantees History</h3>
            <div class="table-actions">
                <button class="export-btn" onclick="exportArchivedStudents()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="archived-students-table" id="archivedStudentsTable">
                <thead>
                    <tr>
                        <th>Grantee ID</th>
                        <th>Name</th>
                        <th>Benefactor Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="archivedStudentsTableBody">
                    @forelse($archivedStudents as $student)
                        <tr data-year="{{ $student->archived_academic_year }}"
                            data-semester="{{ $student->archived_semester }}" data-type="{{ $student->scholarship_type }}"
                            data-search="{{ strtolower($student->first_name . ' ' . $student->last_name . ' ' . $student->student_id) }}">
                            <td>{{ $student->student_id }}</td>
                            <td>
                                <div class="student-info">
                                    <span class="student-name">{{ $student->first_name }}
                                        {{ $student->last_name }}</span>
                                    <small class="student-email">{{ $student->email }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="scholarship-type {{ $student->scholarship_type }}">
                                    {{ ucfirst($student->scholarship_type) }}
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
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize navigation to ensure proper link behavior
        document.addEventListener('DOMContentLoaded', function() {
            initializeNavigation();
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
            console.log('Exporting archived grantees...');
            // TODO: Implement export functionality
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
