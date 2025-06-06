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
                <!-- Personal Information -->
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-user"></i> Personal Information</h3>

                    <div class="detail-group">
                        <div class="detail-label">Student ID</div>
                        <div class="detail-value">{{ $application->student_id }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value">
                            {{ $application->first_name }}
                            @if ($application->middle_name)
                                {{ $application->middle_name }}
                            @endif
                            {{ $application->last_name }}
                        </div>
                    </div>

                    @if ($application->scholarship_type == 'ched')
                        <div class="detail-group">
                            <div class="detail-label">Sex</div>
                            <div class="detail-value">{{ $application->sex ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Birthdate</div>
                            <div class="detail-value">
                                @if ($application->birthdate)
                                    {{ \Carbon\Carbon::parse($application->birthdate)->format('F d, Y') }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="detail-group">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">{{ $application->email }}</div>
                    </div>

                    <div class="detail-group">
                        <div class="detail-label">Contact Number</div>
                        <div class="detail-value">{{ $application->contact_number }}</div>
                    </div>

                    @if ($application->scholarship_type == 'ched')
                        <div class="detail-group">
                            <div class="detail-label">Disability</div>
                            <div class="detail-value">{{ $application->disability ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Indigenous People</div>
                            <div class="detail-value">{{ $application->indigenous ?? 'N/A' }}</div>
                        </div>
                    @endif
                </div>

                <!-- Address Information -->
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-map-marker-alt"></i> Address Information</h3>

                    @if ($application->scholarship_type == 'ched')
                        <!-- CHED: Show detailed address fields -->
                        <div class="detail-group">
                            <div class="detail-label">Street</div>
                            <div class="detail-value">{{ $application->street ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Barangay</div>
                            <div class="detail-value">{{ $application->barangay ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">City</div>
                            <div class="detail-value">{{ $application->city ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Province</div>
                            <div class="detail-value">{{ $application->province ?? 'N/A' }}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Zipcode</div>
                            <div class="detail-value">{{ $application->zipcode ?? 'N/A' }}</div>
                        </div>
                    @else
                        <!-- Other scholarships: Show simple address -->
                        <div class="detail-group">
                            <div class="detail-label">Address</div>
                            <div class="detail-value">{{ $application->address ?? 'N/A' }}</div>
                        </div>
                    @endif
                </div>

                <!-- Scholarship Information -->
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-graduation-cap"></i> Scholarship Information</h3>

                    <div class="detail-group">
                        <div class="detail-label">Scholarship Type</div>
                        <div class="detail-value">
                            @if ($application->scholarship_type == 'ched')
                                CHED Scholarship
                            @elseif($application->scholarship_type == 'presidents')
                                President's Scholarship
                            @elseif($application->scholarship_type == 'institutional')
                                Institutional Scholarship
                            @elseif($application->scholarship_type == 'employees')
                                Employee's Scholarship
                            @elseif($application->scholarship_type == 'private')
                                Private Scholarship
                            @else
                                {{ ucfirst($application->scholarship_type) }}
                            @endif
                        </div>
                    </div>

                    @if ($application->scholarship_type == 'private')
                        @if ($application->scholarship_name)
                            <div class="detail-group">
                                <div class="detail-label">Scholarship Name</div>
                                <div class="detail-value">{{ $application->scholarship_name }}</div>
                            </div>
                        @endif

                        @if ($application->other_scholarship)
                            <div class="detail-group">
                                <div class="detail-label">Other Scholarship Details</div>
                                <div class="detail-value">{{ $application->other_scholarship }}</div>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Parent Information (CHED Scholarship) -->
                @if ($application->scholarship_type == 'ched')
                    <div class="detail-section">
                        <h3 class="section-title"><i class="fas fa-users"></i> Parent Information</h3>

                        <div class="detail-group">
                            <div class="detail-label">Father's Name</div>
                            <div class="detail-value">
                                @if ($application->father_first_name)
                                    {{ $application->father_first_name }}
                                    @if ($application->father_middle_name)
                                        {{ $application->father_middle_name }}
                                    @endif
                                    {{ $application->father_last_name }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Mother's Maiden Name</div>
                            <div class="detail-value">
                                @if ($application->mother_first_name)
                                    {{ $application->mother_first_name }}
                                    @if ($application->mother_middle_name)
                                        {{ $application->mother_middle_name }}
                                    @endif
                                    {{ $application->mother_last_name }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Employee Information (Employee Scholarship) -->
                @if ($application->scholarship_type == 'employees')
                    <div class="detail-section">
                        <h3 class="section-title"><i class="fas fa-briefcase"></i> Employee Information</h3>

                        <div class="detail-group">
                            <div class="detail-label">Employee Name</div>
                            <div class="detail-value">{{ $application->employee_name ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Relationship to Employee</div>
                            <div class="detail-value">{{ $application->employee_relationship ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Employee Department</div>
                            <div class="detail-value">{{ $application->employee_department ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Employee Position</div>
                            <div class="detail-value">{{ $application->employee_position ?? 'N/A' }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="right-column">
                <!-- Educational Information -->
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-school"></i> Educational Information</h3>

                    @if ($application->scholarship_type == 'ched')
                        <!-- CHED Scholarship Fields -->
                        <div class="detail-group">
                            <div class="detail-label">Education Stage</div>
                            <div class="detail-value">{{ $application->education_stage ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Department</div>
                            <div class="detail-value">{{ $application->department ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Course/Program</div>
                            <div class="detail-value">{{ $application->course ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Year Level</div>
                            <div class="detail-value">{{ $application->year_level ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Semester</div>
                            <div class="detail-value">{{ $application->semester ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Academic Year</div>
                            <div class="detail-value">{{ $application->academic_year ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">GWA</div>
                            <div class="detail-value">{{ $application->gwa ?? 'N/A' }}</div>
                        </div>
                    @elseif($application->scholarship_type == 'employees')
                        <!-- Employee Scholarship Fields -->
                        <div class="detail-group">
                            <div class="detail-label">Education Stage</div>
                            <div class="detail-value">{{ $application->education_stage ?? 'N/A' }}</div>
                        </div>

                        @if ($application->education_stage == 'Basic Education')
                            <div class="detail-group">
                                <div class="detail-label">Grade Level</div>
                                <div class="detail-value">{{ $application->grade_level ?? 'N/A' }}</div>
                            </div>

                            @if ($application->grade_level && in_array($application->grade_level, ['Grade 11', 'Grade 12']))
                                <div class="detail-group">
                                    <div class="detail-label">Strand</div>
                                    <div class="detail-value">{{ $application->strand ?? 'N/A' }}</div>
                                </div>
                            @endif
                        @else
                            <div class="detail-group">
                                <div class="detail-label">Department</div>
                                <div class="detail-value">{{ $application->department ?? 'N/A' }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Course/Program</div>
                                <div class="detail-value">{{ $application->course ?? 'N/A' }}</div>
                            </div>

                            <div class="detail-group">
                                <div class="detail-label">Year Level</div>
                                <div class="detail-value">{{ $application->year_level ?? 'N/A' }}</div>
                            </div>
                        @endif

                        <div class="detail-group">
                            <div class="detail-label">Semester</div>
                            <div class="detail-value">{{ $application->semester ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Academic Year</div>
                            <div class="detail-value">{{ $application->academic_year ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">GWA</div>
                            <div class="detail-value">{{ $application->gwa ?? 'N/A' }}</div>
                        </div>
                    @elseif($application->scholarship_type == 'private')
                        <!-- Private Scholarship Fields -->
                        <div class="detail-group">
                            <div class="detail-label">Education Stage</div>
                            <div class="detail-value">{{ $application->education_stage ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Course/Program</div>
                            <div class="detail-value">{{ $application->course ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Year Level</div>
                            <div class="detail-value">{{ $application->year_level ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">GWA</div>
                            <div class="detail-value">{{ $application->gwa ?? 'N/A' }}</div>
                        </div>
                    @else
                        <!-- Institutional/Presidents Scholarship Fields -->
                        <div class="detail-group">
                            <div class="detail-label">Education Stage</div>
                            <div class="detail-value">{{ $application->education_stage ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Department</div>
                            <div class="detail-value">{{ $application->department ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Course/Program</div>
                            <div class="detail-value">{{ $application->course ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Year Level</div>
                            <div class="detail-value">{{ $application->year_level ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Semester</div>
                            <div class="detail-value">{{ $application->semester ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">Academic Year</div>
                            <div class="detail-value">{{ $application->academic_year ?? 'N/A' }}</div>
                        </div>

                        <div class="detail-group">
                            <div class="detail-label">GWA</div>
                            <div class="detail-value">{{ $application->gwa ?? 'N/A' }}</div>
                        </div>
                    @endif
                </div>

                <!-- Documents Section -->
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-file-alt"></i> Uploaded Documents</h3>

                    @if ($application->documents && count($application->documents) > 0)
                        <div class="documents-list">
                            @foreach ($application->documents as $index => $document)
                                <div class="document-item">
                                    <div class="document-info">
                                        <i class="fas fa-file-pdf document-icon"></i>
                                        <div class="document-details">
                                            <div class="document-name">
                                                {{ $document['original_name'] ?? 'Document ' . ($index + 1) }}</div>
                                            <div class="document-size">
                                                {{ isset($document['size']) ? number_format($document['size'] / 1024, 2) . ' KB' : 'Unknown size' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="document-actions">
                                        @if (isset($document['path']) && Storage::exists($document['path']))
                                            <a href="{{ route('admin.application.document.download', ['application' => $application->application_id, 'document' => $index]) }}"
                                                class="btn-download" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn-view" title="View"
                                                onclick="openDocumentModal('{{ route('admin.application.document.view', ['application' => $application->application_id, 'document' => $index]) }}', '{{ $document['original_name'] ?? 'Document ' . ($index + 1) }}', '{{ $document['mime_type'] ?? 'application/octet-stream' }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            <span class="document-unavailable">File not found</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-documents">
                            <div class="no-documents-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <p class="no-documents-text">No documents uploaded</p>
                            <small class="no-documents-subtitle">This application does not have any uploaded
                                documents.</small>
                        </div>
                    @endif
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

    <!-- Document Viewer Modal -->
    <div id="documentModal" class="document-modal">
        <div class="document-modal-content">
            <div class="document-modal-header">
                <h3 id="documentTitle">Document Viewer</h3>
                <button type="button" class="document-modal-close" onclick="closeDocumentModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="document-modal-body">
                <div id="documentViewer">
                    <div class="document-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading document...</p>
                    </div>
                </div>
            </div>
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

        // Document Modal Functions
        function openDocumentModal(documentUrl, documentName, mimeType) {
            const modal = document.getElementById('documentModal');
            const title = document.getElementById('documentTitle');
            const viewer = document.getElementById('documentViewer');

            // Set title
            title.textContent = documentName;

            // Show loading
            viewer.innerHTML = `
                <div class="document-loading">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Loading document...</p>
                </div>
            `;

            // Show modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Load document based on type
            if (mimeType.startsWith('image/')) {
                // For images
                viewer.innerHTML = `<img src="${documentUrl}" alt="${documentName}" class="document-image">`;
            } else if (mimeType === 'application/pdf') {
                // For PDFs
                viewer.innerHTML = `<iframe src="${documentUrl}" class="document-iframe" frameborder="0"></iframe>`;
            } else {
                // For other document types
                viewer.innerHTML = `
                    <div class="document-preview-unavailable">
                        <i class="fas fa-file-alt"></i>
                        <p>Preview not available for this file type.</p>
                        <a href="${documentUrl}" download="${documentName}" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Document
                        </a>
                    </div>
                `;
            }
        }

        function closeDocumentModal() {
            const modal = document.getElementById('documentModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';

            // Clear viewer content
            document.getElementById('documentViewer').innerHTML = '';
        }

        // Close modal when clicking outside
        document.getElementById('documentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDocumentModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDocumentModal();
            }
        });
    </script>
@endpush
