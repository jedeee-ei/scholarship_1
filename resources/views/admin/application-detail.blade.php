@extends('layouts.admin')

@section('title', 'Application Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/application-detail.css') }}">
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[
        [
            'title' => 'Applications',
            'url' => route('admin.applications'),
            'icon' => 'fas fa-graduation-cap',
        ],
        ['title' => 'Application Details', 'icon' => 'fas fa-file-alt'],
    ]" />
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>Application Details</h1>
        <div class="date">{{ date('F d, Y') }}</div>
    </div>

    @if (session('success'))
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="application-detail-container">
        <div class="detail-header">
            <div class="application-info">
                <div class="application-id">{{ $application->application_id }}</div>
                <div class="application-date">Submitted on {{ $application->created_at->format('F d, Y') }}
                </div>
            </div>

            <div class="status-section">


                <form action="{{ route('admin.application.status', $application->application_id) }}" method="POST"
                    class="status-form">
                    @csrf
                    <select name="status" id="status" class="status-select">
                        <option value="Pending Review" {{ $application->status == 'Pending Review' ? 'selected' : '' }}>
                            Pending Review
                        </option>
                        <option value="Under Committee Review"
                            {{ $application->status == 'Under Committee Review' ? 'selected' : '' }}>Under
                            Committee Review</option>
                        <option value="Approved" {{ $application->status == 'Approved' ? 'selected' : '' }}>
                            Approved</option>
                        <option value="Rejected" {{ $application->status == 'Rejected' ? 'selected' : '' }}>
                            Rejected</option>
                    </select>
                    <button type="submit" class="update-btn">Update Status</button>
                </form>
            </div>
        </div>

        <div class="detail-content">
            <div class="left-column">
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>

                    <div class="detail-group">
                        <div class="detail-label">Student ID</div>
                        <div class="detail-value">{{ $application->student_id }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">{{ $application->first_name }} {{ $application->last_name }}
                        </div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">{{ $application->email }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Phone</div>
                        <div class="detail-value">{{ $application->contact_number }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Address</div>
                        <div class="detail-value">{{ $application->address }}</div>
                    </div>
                </div>

                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-graduation-cap"></i> Scholarship Information</h3>

                    <div class="detail-group">
                        <div class="detail-label">Scholarship Type</div>
                        <div class="detail-value">
                            @if ($application->scholarship_type == 'ched')
                                CHED Scholarship
                            @elseif($application->scholarship_type == 'presidents')
                                President's Scholarship
                            @elseif($application->scholarship_type == 'employees')
                                Employees Scholar
                            @elseif($application->scholarship_type == 'private')
                                Private Scholarship
                            @else
                                {{ ucfirst($application->scholarship_type) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-column">
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-school"></i> Educational Information</h3>

                    <div class="detail-group">
                        <div class="detail-label">Education Stage</div>
                        <div class="detail-value">{{ $application->education_stage }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Program</div>
                        <div class="detail-value">{{ $application->course }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Current Year</div>
                        <div class="detail-value">{{ $application->year_level }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Current Semester</div>
                        <div class="detail-value">{{ $application->semester }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">GPA</div>
                        <div class="detail-value">{{ $application->gwa }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <form action="{{ route('admin.application.status', $application->application_id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Approved">
                <button type="submit" class="action-btn approve">
                    <i class="fas fa-check"></i> Approve Application
                </button>
            </form>

            <form action="{{ route('admin.application.status', $application->application_id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Rejected">
                <button type="submit" class="action-btn reject">
                    <i class="fas fa-times"></i> Reject Application
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Status change confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const statusForm = document.querySelector('.status-form');
            const approveForm = document.querySelectorAll('.action-buttons form')[0];
            const rejectForm = document.querySelectorAll('.action-buttons form')[1];

            statusForm.addEventListener('submit', function(e) {
                const status = document.getElementById('status').value;
                const currentStatus = '{{ $application->status }}';

                if (status !== currentStatus) {
                    if (!confirm(`Are you sure you want to change the status to "${status}"?`)) {
                        e.preventDefault();
                    }
                }
            });

            approveForm.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to approve this application?')) {
                    e.preventDefault();
                }
            });

            rejectForm.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to reject this application?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
