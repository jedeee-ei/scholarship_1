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
        <h1>Application Detail for
            @if ($application->scholarship_type == 'government')
                Government Scholarship
            @elseif($application->scholarship_type == 'academic')
                Academic Scholarship
            @elseif($application->scholarship_type == 'employees')
                Employee's Scholarship
            @elseif($application->scholarship_type == 'private')
                Private Scholarship
            @else
                {{ ucfirst($application->scholarship_type) }}
            @endif
        </h1>
    </div>



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

                    @if ($application->sex)
                        <div class="detail-group">
                            <div class="detail-label">Sex</div>
                            <div class="detail-value">{{ $application->sex }}</div>
                        </div>
                    @endif

                    @if ($application->birthdate)
                        <div class="detail-group">
                            <div class="detail-label">Birthdate</div>
                            <div class="detail-value">
                                {{ \Carbon\Carbon::parse($application->birthdate)->format('F d, Y') }}</div>
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

                    <!-- Address Information -->
                    @php
                        $addressParts = array_filter([
                            $application->street,
                            $application->barangay,
                            $application->city,
                            $application->province,
                            $application->zipcode,
                        ]);
                    @endphp
                    @if (!empty($addressParts))
                        <div class="detail-group">
                            <div class="detail-label">Address</div>
                            <div class="detail-value">{{ implode(', ', $addressParts) }}</div>
                        </div>
                    @endif

                    @if ($application->disability)
                        <div class="detail-group">
                            <div class="detail-label">Disability</div>
                            <div class="detail-value">{{ $application->disability }}</div>
                        </div>
                    @endif

                    @if ($application->indigenous)
                        <div class="detail-group">
                            <div class="detail-label">Indigenous People</div>
                            <div class="detail-value">{{ $application->indigenous }}</div>
                        </div>
                    @endif
                </div>





                <!-- Parents Information (Government Scholarship) -->
                @if (
                    $application->scholarship_type == 'government' &&
                        ($application->father_first_name || $application->mother_first_name))
                    <div class="detail-section">
                        <h3 class="section-title"><i class="fas fa-users"></i> Parents Information</h3>

                        @if ($application->father_first_name)
                            <div class="detail-group">
                                <div class="detail-label">Father's Name</div>
                                <div class="detail-value">
                                    {{ $application->father_first_name }}
                                    @if ($application->father_middle_name)
                                        {{ $application->father_middle_name }}
                                    @endif
                                    {{ $application->father_last_name }}
                                </div>
                            </div>
                        @endif

                        @if ($application->mother_first_name)
                            <div class="detail-group">
                                <div class="detail-label">Mother's Maiden Name</div>
                                <div class="detail-value">
                                    {{ $application->mother_first_name }}
                                    @if ($application->mother_middle_name)
                                        {{ $application->mother_middle_name }}
                                    @endif
                                    {{ $application->mother_last_name }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Employee Information (Employee Scholarship) -->
                @if (
                    $application->scholarship_type == 'employees' &&
                        ($application->employee_name ||
                            $application->employee_relationship ||
                            $application->employee_department ||
                            $application->employee_position))
                    <div class="detail-section">
                        <h3 class="section-title"><i class="fas fa-briefcase"></i> Employee Information</h3>

                        @if ($application->employee_name)
                            <div class="detail-group">
                                <div class="detail-label">Employee Name</div>
                                <div class="detail-value">{{ $application->employee_name }}</div>
                            </div>
                        @endif

                        @if ($application->employee_relationship)
                            <div class="detail-group">
                                <div class="detail-label">Relationship to Employee</div>
                                <div class="detail-value">{{ $application->employee_relationship }}</div>
                            </div>
                        @endif

                        @if ($application->employee_department)
                            <div class="detail-group">
                                <div class="detail-label">Employee Department</div>
                                <div class="detail-value">{{ $application->employee_department }}</div>
                            </div>
                        @endif

                        @if ($application->employee_position)
                            <div class="detail-group">
                                <div class="detail-label">Employee Position</div>
                                <div class="detail-value">{{ $application->employee_position }}</div>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($application->scholarship_type == 'academic')
                    <!-- Academic Performance Section -->
                    <div class="detail-section">
                        <h3 class="section-title"><i class="fas fa-chart-line"></i> Academic Performance - Subjects and
                            Grades</h3>

                        <div class="subjects-grades-container">
                            <div class="subjects-header">
                                <div class="subject-code-header">Subject Code & Course Title</div>
                                <div class="grades-header">Grades</div>
                                <div class="units-header">Units</div>
                            </div>

                            <div class="subjects-list" id="admin-subjects-list">
                                <!-- Subjects will be loaded here -->
                                <div class="loading-subjects">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <p>Loading subjects...</p>
                                </div>
                            </div>

                            <div class="gwa-summary">
                                <div class="gwa-row">
                                    <div class="gwa-label">Total Units:</div>
                                    <div class="gwa-value" id="admin-total-units">-</div>
                                </div>
                                <div class="gwa-row">
                                    <div class="gwa-label">Total Grade Points:</div>
                                    <div class="gwa-value" id="admin-total-grade-points">-</div>
                                </div>
                                @if ($application->gwa)
                                    <div class="gwa-row gwa-final">
                                        <div class="gwa-label"><strong>GWA (General Weighted Average):</strong></div>
                                        <div class="gwa-value"><strong>{{ $application->gwa }}</strong></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="right-column">
                <!-- Educational Information -->
                <div class="detail-section">
                    <h3 class="section-title"><i class="fas fa-school"></i> Educational Information</h3>

                    @if ($application->scholarship_type == 'government')
                        <!-- Government Scholarship Fields -->
                        @if ($application->government_benefactor_type)
                            <div class="detail-group">
                                <div class="detail-label">Benefactor Type</div>
                                <div class="detail-value">
                                    <span class="benefactor-badge">{{ $application->government_benefactor_type }}</span>
                                </div>
                            </div>
                        @endif

                        @if ($application->education_stage)
                            <div class="detail-group">
                                <div class="detail-label">Education Stage</div>
                                <div class="detail-value">
                                    @if ($application->education_stage == 'BEU' || $application->education_stage == 'BSU')
                                        BEU (Basic Education Unit)
                                    @else
                                        {{ $application->education_stage }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if (
                            $application->education_stage == 'BEU' ||
                                $application->education_stage == 'BSU' ||
                                $application->education_stage == 'Basic Education')
                            <!-- Basic Education Fields -->
                            @if ($application->grade_level)
                                <div class="detail-group">
                                    <div class="detail-label">Grade Level</div>
                                    <div class="detail-value">{{ $application->grade_level }}</div>
                                </div>
                            @endif

                            @if ($application->strand && in_array($application->grade_level, ['Grade 11', 'Grade 12']))
                                <div class="detail-group">
                                    <div class="detail-label">Strand</div>
                                    <div class="detail-value">{{ $application->strand }}</div>
                                </div>
                            @endif
                        @else
                            <!-- College Fields -->
                            @if ($application->department)
                                <div class="detail-group">
                                    <div class="detail-label">Department</div>
                                    <div class="detail-value">{{ $application->department }}</div>
                                </div>
                            @endif

                            @if ($application->course)
                                <div class="detail-group">
                                    <div class="detail-label">Course/Program</div>
                                    <div class="detail-value">{{ $application->course }}</div>
                                </div>
                            @endif

                            @if ($application->year_level)
                                <div class="detail-group">
                                    <div class="detail-label">Year Level</div>
                                    <div class="detail-value">{{ $application->year_level }}</div>
                                </div>
                            @endif
                        @endif

                        @if ($application->semester)
                            <div class="detail-group">
                                <div class="detail-label">Semester</div>
                                <div class="detail-value">{{ $application->semester }}</div>
                            </div>
                        @endif

                        @if ($application->academic_year)
                            <div class="detail-group">
                                <div class="detail-label">Academic Year</div>
                                <div class="detail-value">{{ $application->academic_year }}</div>
                            </div>
                        @endif

                        @if ($application->gwa)
                            <div class="detail-group">
                                <div class="detail-label">GWA</div>
                                <div class="detail-value">{{ $application->gwa }}</div>
                            </div>
                        @endif
                    @elseif($application->scholarship_type == 'employees')
                        <!-- Employee Scholarship Fields -->
                        @if ($application->education_stage)
                            <div class="detail-group">
                                <div class="detail-label">Education Stage</div>
                                <div class="detail-value">
                                    @if ($application->education_stage == 'BEU' || $application->education_stage == 'BSU')
                                        BEU (Basic Education Unit)
                                    @else
                                        {{ $application->education_stage }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if (
                            $application->education_stage == 'BEU' ||
                                $application->education_stage == 'BSU' ||
                                $application->education_stage == 'Basic Education')
                            <!-- Basic Education Fields -->
                            @if ($application->grade_level)
                                <div class="detail-group">
                                    <div class="detail-label">Grade Level</div>
                                    <div class="detail-value">{{ $application->grade_level }}</div>
                                </div>
                            @endif

                            @if ($application->strand && in_array($application->grade_level, ['Grade 11', 'Grade 12']))
                                <div class="detail-group">
                                    <div class="detail-label">Strand</div>
                                    <div class="detail-value">{{ $application->strand }}</div>
                                </div>
                            @endif
                        @else
                            <!-- College Fields -->
                            @if ($application->department)
                                <div class="detail-group">
                                    <div class="detail-label">Department</div>
                                    <div class="detail-value">{{ $application->department }}</div>
                                </div>
                            @endif

                            @if ($application->course)
                                <div class="detail-group">
                                    <div class="detail-label">Course/Program</div>
                                    <div class="detail-value">{{ $application->course }}</div>
                                </div>
                            @endif

                            @if ($application->year_level)
                                <div class="detail-group">
                                    <div class="detail-label">Year Level</div>
                                    <div class="detail-value">{{ $application->year_level }}</div>
                                </div>
                            @endif
                        @endif

                        @if ($application->semester)
                            <div class="detail-group">
                                <div class="detail-label">Semester</div>
                                <div class="detail-value">{{ $application->semester }}</div>
                            </div>
                        @endif

                        @if ($application->academic_year)
                            <div class="detail-group">
                                <div class="detail-label">Academic Year</div>
                                <div class="detail-value">{{ $application->academic_year }}</div>
                            </div>
                        @endif

                        @if ($application->gwa)
                            <div class="detail-group">
                                <div class="detail-label">GWA</div>
                                <div class="detail-value">{{ $application->gwa }}</div>
                            </div>
                        @endif
                    @elseif($application->scholarship_type == 'private')
                        <!-- Private Scholarship Fields -->
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

                        @if ($application->education_stage)
                            <div class="detail-group">
                                <div class="detail-label">Education Stage</div>
                                <div class="detail-value">
                                    @if ($application->education_stage == 'BEU' || $application->education_stage == 'BSU')
                                        BEU (Basic Education Unit)
                                    @else
                                        {{ $application->education_stage }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if (
                            $application->education_stage == 'BEU' ||
                                $application->education_stage == 'BSU' ||
                                $application->education_stage == 'Basic Education')
                            <!-- Basic Education Fields -->
                            @if ($application->grade_level)
                                <div class="detail-group">
                                    <div class="detail-label">Grade Level</div>
                                    <div class="detail-value">{{ $application->grade_level }}</div>
                                </div>
                            @endif

                            @if ($application->strand && in_array($application->grade_level, ['Grade 11', 'Grade 12']))
                                <div class="detail-group">
                                    <div class="detail-label">Strand</div>
                                    <div class="detail-value">{{ $application->strand }}</div>
                                </div>
                            @endif
                        @else
                            <!-- College Fields -->
                            @if ($application->course)
                                <div class="detail-group">
                                    <div class="detail-label">Course/Program</div>
                                    <div class="detail-value">{{ $application->course }}</div>
                                </div>
                            @endif

                            @if ($application->year_level)
                                <div class="detail-group">
                                    <div class="detail-label">Year Level</div>
                                    <div class="detail-value">{{ $application->year_level }}</div>
                                </div>
                            @endif
                        @endif

                        @if ($application->gwa)
                            <div class="detail-group">
                                <div class="detail-label">GWA</div>
                                <div class="detail-value">{{ $application->gwa }}</div>
                            </div>
                        @endif
                    @else
                        <!-- Institutional/Presidents/Academic Scholarship Fields -->
                        @if ($application->education_stage)
                            <div class="detail-group">
                                <div class="detail-label">Education Stage</div>
                                <div class="detail-value">
                                    @if ($application->education_stage == 'BEU' || $application->education_stage == 'BSU')
                                        BEU (Basic Education Unit)
                                    @elseif ($application->scholarship_type == 'academic')
                                        College
                                    @else
                                        {{ $application->education_stage }}
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if (
                            $application->education_stage == 'BEU' ||
                                $application->education_stage == 'BSU' ||
                                $application->education_stage == 'Basic Education')
                            <!-- Basic Education Fields -->
                            @if ($application->grade_level)
                                <div class="detail-group">
                                    <div class="detail-label">Grade Level</div>
                                    <div class="detail-value">{{ $application->grade_level }}</div>
                                </div>
                            @endif

                            @if ($application->strand && in_array($application->grade_level, ['Grade 11', 'Grade 12']))
                                <div class="detail-group">
                                    <div class="detail-label">Strand</div>
                                    <div class="detail-value">{{ $application->strand }}</div>
                                </div>
                            @endif
                        @else
                            <!-- College Fields -->
                            @if ($application->department)
                                <div class="detail-group">
                                    <div class="detail-label">Department</div>
                                    <div class="detail-value">{{ $application->department }}</div>
                                </div>
                            @endif

                            @if ($application->course)
                                <div class="detail-group">
                                    <div class="detail-label">Course/Program</div>
                                    <div class="detail-value">{{ $application->course }}</div>
                                </div>
                            @endif

                            @if ($application->year_level)
                                <div class="detail-group">
                                    <div class="detail-label">Year Level</div>
                                    <div class="detail-value">{{ $application->year_level }}</div>
                                </div>
                            @endif
                        @endif

                        @if ($application->semester)
                            <div class="detail-group">
                                <div class="detail-label">Semester</div>
                                <div class="detail-value">{{ $application->semester }}</div>
                            </div>
                        @endif

                        @if ($application->academic_year)
                            <div class="detail-group">
                                <div class="detail-label">Academic Year</div>
                                <div class="detail-value">{{ $application->academic_year }}</div>
                            </div>
                        @endif

                        @if ($application->gwa)
                            <div class="detail-group">
                                <div class="detail-label">GWA</div>
                                <div class="detail-value">{{ $application->gwa }}</div>
                            </div>
                        @endif

                        @if ($application->scholarship_type == 'academic' && ($application->scholarship_subtype || $application->gwa))
                            <div class="detail-group">
                                <div class="detail-label">Academic Classification</div>
                                <div class="detail-value">
                                    @if ($application->scholarship_subtype)
                                        @if ($application->scholarship_subtype == 'PL')
                                            <span class="classification-badge">President's Lister (PL)</span>
                                        @elseif ($application->scholarship_subtype == 'DL')
                                            <span class="classification-badge">Dean's Lister (DL)</span>
                                        @else
                                            <span
                                                class="classification-badge">{{ $application->scholarship_subtype }}</span>
                                        @endif
                                    @elseif ($application->gwa)
                                        @if ($application->gwa >= 1.0 && $application->gwa <= 1.25)
                                            <span class="classification-badge">President's Lister (PL) - Eligible</span>
                                        @elseif ($application->gwa >= 1.26 && $application->gwa <= 1.74)
                                            <span class="classification-badge">Dean's Lister (DL) - Eligible</span>
                                        @else
                                            <span class="status-badge rejected">Not Qualified (GWA:
                                                {{ $application->gwa }})</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif
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
                                        @php
                                            $documentPath = is_string($document) ? $document : ($document['path'] ?? '');
                                            $fileExists = Storage::disk('local')->exists($documentPath);
                                            $documentName = is_array($document) ? ($document['original_name'] ?? 'Document ' . ($index + 1)) : 'Document ' . ($index + 1);
                                            $mimeType = is_array($document) ? ($document['mime_type'] ?? 'application/octet-stream') : 'application/octet-stream';
                                        @endphp
                                        @if ($fileExists)
                                            <a href="{{ route('admin.application.document.download', ['application' => $application->application_id, 'document' => $index]) }}"
                                                class="btn-download" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" class="btn-view" title="View"
                                                onclick="openDocumentModal('{{ route('admin.application.document.view', ['application' => $application->application_id, 'document' => $index]) }}', '{{ addslashes($documentName) }}', '{{ $mimeType }}')">
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

            statusForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const status = document.getElementById('status').value;
                const currentStatus = '{{ $application->status }}';

                if (status !== currentStatus) {
                    const confirmed = await customConfirm(
                        `Are you sure you want to change the status to "${status}"?`,
                        'Confirm Status Change',
                        'warning'
                    );
                    if (confirmed) {
                        this.submit();
                    }
                } else {
                    this.submit();
                }
            });

            approveForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const confirmed = await customConfirm(
                    'Are you sure you want to approve this application?',
                    'Approve Application',
                    'warning'
                );
                if (confirmed) {
                    this.submit();
                }
            });

            rejectForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const confirmed = await customConfirm(
                    'Are you sure you want to reject this application?',
                    'Reject Application',
                    'danger'
                );
                if (confirmed) {
                    this.submit();
                }
            });

            // Load subjects for academic scholarship
            @if ($application->scholarship_type == 'academic')
                loadAcademicSubjects();
            @endif
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

        // Load subjects for academic scholarship
        async function loadAcademicSubjects() {
            const course = '{{ $application->course }}';
            const yearLevel = '{{ $application->year_level }}';
            const semester = '{{ $application->semester }}';
            const subjectsList = document.getElementById('admin-subjects-list');

            if (!course || !yearLevel || !semester) {
                subjectsList.innerHTML = `
                    <div class="no-subjects-message">
                        <p>Incomplete academic information. Cannot load subjects.</p>
                        <small>Course: ${course || 'Not specified'}, Year: ${yearLevel || 'Not specified'}, Semester: ${semester || 'Not specified'}</small>
                    </div>
                `;
                return;
            }

            try {
                // Convert year level to number for API
                const yearLevelNumber = parseInt(yearLevel.replace(/\D/g, ''));

                const response = await fetch(
                    `/api/scholarship/subjects/${encodeURIComponent(course)}/${yearLevelNumber}/${encodeURIComponent(semester)}`
                );
                const data = await response.json();

                if (response.ok && data.subjects && data.subjects.length > 0) {
                    displayAcademicSubjects(data.subjects);
                } else {
                    subjectsList.innerHTML = `
                        <div class="no-subjects-message">
                            <p>No subjects found for ${course} - ${yearLevel} - ${semester}</p>
                            <small>This may indicate that subjects haven't been configured for this course yet.</small>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading subjects:', error);
                subjectsList.innerHTML = `
                    <div class="no-subjects-message">
                        <p>Error loading subjects for this course.</p>
                        <small>Please check the console for more details.</small>
                    </div>
                `;
            }
        }

        function displayAcademicSubjects(subjects) {
            const subjectsList = document.getElementById('admin-subjects-list');
            const totalUnitsElement = document.getElementById('admin-total-units');
            const totalGradePointsElement = document.getElementById('admin-total-grade-points');

            // Get submitted grades from the application
            const submittedGrades = @json($application->subject_grades ?? []);

            let subjectsHTML = '';
            let totalUnits = 0;
            let totalGradePoints = 0;
            let hasGrades = Object.keys(submittedGrades).length > 0;

            subjects.forEach((subject, index) => {
                totalUnits += subject.units;

                // Get the grade for this subject if it exists
                const grade = submittedGrades[subject.code] || 0;
                const gradeValue = grade > 0 ? grade.toFixed(2) : '0.00';
                const gradeClass = grade > 0 ? 'grade-display has-grade' : 'grade-display no-grade';

                // Calculate grade points for this subject
                if (grade > 0) {
                    totalGradePoints += grade * subject.units;
                }

                subjectsHTML += `
                    <div class="subject-row">
                        <div class="subject-info">
                            <div class="subject-code">${subject.code}</div>
                            <div class="subject-title">${subject.title}</div>
                        </div>
                        <div class="subject-grade">
                            <input type="number"
                                   value="${gradeValue}"
                                   min="1.00"
                                   max="5.00"
                                   step="0.01"
                                   readonly
                                   class="${gradeClass}"
                                   title="${grade > 0 ? 'Grade: ' + gradeValue : 'No grade submitted'}">
                        </div>
                        <div class="subject-units">${subject.units}</div>
                    </div>
                `;
            });

            subjectsList.innerHTML = subjectsHTML;
            totalUnitsElement.textContent = totalUnits;

            // Display actual calculated grade points or estimated based on GWA
            if (hasGrades && totalGradePoints > 0) {
                totalGradePointsElement.textContent = totalGradePoints.toFixed(2);
            } else {
                // Calculate estimated grade points based on GWA if no individual grades
                const gwa = parseFloat('{{ $application->gwa }}') || 0;
                const estimatedGradePoints = (gwa * totalUnits).toFixed(2);
                totalGradePointsElement.textContent = estimatedGradePoints + ' (estimated)';
            }
        }
    </script>
@endpush
