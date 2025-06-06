@extends('layouts.admin')

@section('title', $scholarshipName . ' Grantees')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/scholarship-students.css') }}">
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[
        [
            'title' => 'Benefactor',
            'url' => route('admin.scholarships'),
            'icon' => 'fas fa-award',
        ],
        ['title' => $scholarshipName . ' Grantees', 'icon' => 'fas fa-users'],
    ]" />
@endsection

@section('content')
    <!-- Scholarship Info -->
    <div class="scholarship-info">
        <div class="info-card">
            <div class="info-icon {{ $scholarshipType }}">
                <i class="fas fa-award"></i>
            </div>
            <div class="info-content">
                <h3>{{ $scholarshipName }}</h3>
                <p>{{ ucfirst($scholarshipType) }} Benefactor Program</p>
            </div>
            <div class="info-stats">
                <div class="stat">
                    <span class="stat-number">{{ $students->count() }}</span>
                    <span class="stat-label">Active Students</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="table-container">
        <div class="table-header">
            <h3>Active {{ $scholarshipName }} Grantees</h3>
            <div class="table-actions">
                <a href="{{ route('admin.scholarships') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Benefactor
                </a>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>Grantee ID</th>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Year Level</th>
                        <th>GWA</th>
                        <th>Department</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>
                                <span class="student-id">{{ $student['id'] }}</span>
                            </td>
                            <td>
                                <div class="student-info">
                                    <span class="student-name">{{ $student['name'] }}</span>
                                    @if ($student['email'])
                                        <small class="student-email">{{ $student['email'] }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="course-name">{{ $student['course'] }}</span>
                            </td>
                            <td>
                                @if ($student['year_level'])
                                    <span class="year-level">{{ $student['year_level'] }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if ($student['gwa'] && $student['gwa'] !== 'N/A')
                                    <span class="gwa-badge">{{ $student['gwa'] }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if ($student['department'])
                                    <span class="department">{{ $student['department'] }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="applied-date">{{ $student['applied_at'] }}</span>
                            </td>
                            <td>
                                <span class="status-badge active">{{ $student['status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-users"></i>
                                <h3>No Students Found</h3>
                                <p>No approved students found for {{ $scholarshipName }}.</p>
                                <a href="{{ route('admin.applications') }}" class="btn-primary">
                                    <i class="fas fa-file-alt"></i> View Applications
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
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
