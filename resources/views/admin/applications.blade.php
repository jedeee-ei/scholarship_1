@extends('layouts.admin')

@section('title', 'Applications')

@section('breadcrumbs')
    <x-breadcrumb :items="[['title' => 'Applications', 'icon' => 'fas fa-graduation-cap']]" />
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>Scholarship Applications</h1>
        <div class="filter-controls">
            <form action="{{ route('admin.applications') }}" method="GET">
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Pending Review" {{ $currentStatus == 'Pending Review' ? 'selected' : '' }}>
                        Pending Review</option>
                    <option value="Under Committee Review"
                        {{ $currentStatus == 'Under Committee Review' ? 'selected' : '' }}>Committee Review
                    </option>
                </select>
                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="government" {{ $currentType == 'government' ? 'selected' : '' }}>Government Scholarship
                    </option>
                    <option value="academic" {{ $currentType == 'academic' ? 'selected' : '' }}>Academic
                        Scholarship</option>
                    <option value="employees" {{ $currentType == 'employees' ? 'selected' : '' }}>Employees
                        Scholar</option>
                    <option value="private" {{ $currentType == 'private' ? 'selected' : '' }}>Private
                        Scholarship</option>
                </select>
            </form>
        </div>
    </div>



    <div class="table-container">
        <table class="applications-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Scholarship Type</th>
                    <th>Date Applied</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td>{{ $application->application_id }}</td>
                        <td>{{ $application->first_name }} {{ $application->last_name }}</td>
                        <td>{{ $application->student_id }}</td>
                        <td>
                            @if ($application->scholarship_type == 'government')
                                Government Scholarship
                                @if ($application->government_benefactor_type)
                                    <br><small class="text-muted">({{ $application->government_benefactor_type }})</small>
                                @endif
                            @elseif($application->scholarship_type == 'academic')
                                President's Scholarship
                            @elseif($application->scholarship_type == 'employees')
                                Employees Scholar
                            @elseif($application->scholarship_type == 'private')
                                Private Scholarship
                            @else
                                {{ ucfirst($application->scholarship_type) }}
                            @endif
                        </td>
                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                        <td>
                            @if ($application->status == 'Pending Review')
                                <span class="status pending">Pending Review</span>
                            @elseif($application->status == 'Under Committee Review')
                                <span class="status review">Committee Review</span>
                            @elseif($application->status == 'Approved')
                                <span class="status approved">Approved</span>
                            @elseif($application->status == 'Rejected')
                                <span class="status rejected">Rejected</span>
                            @else
                                <span class="status">{{ $application->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.application.view', $application->application_id) }}"
                                class="action-btn">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


@endsection

@push('scripts')
    <script>
        // Application management functions can be added here
        console.log('Applications page loaded');
    </script>
@endpush
