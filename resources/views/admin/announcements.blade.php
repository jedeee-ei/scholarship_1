@extends('layouts.admin')

@section('title', 'Announcements Management')

@section('breadcrumbs')
    <x-breadcrumb :items="[['title' => 'Announcements', 'icon' => 'fas fa-bullhorn']]" />
@endsection

@section('content')



    <!-- Announcements Table -->
    <div class="announcements-table-container">
        <div class="table-header">
            <h2>All Announcements</h2>
            <div class="table-actions">
                <button class="btn btn-primary" onclick="openAddAnnouncementModal()">
                    <i class="fas fa-plus"></i> Add Announcement
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="announcements-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content Preview</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $announcement)
                        <tr data-announcement-id="{{ $announcement->id }}">
                            <td>
                                <div class="announcement-title">
                                    {{ $announcement->title }}
                                </div>
                            </td>
                            <td>
                                <div class="content-preview">
                                    {{ Str::limit($announcement->content, 100) }}
                                </div>
                            </td>
                            <td>
                                <div class="date-info">
                                    <div class="date">{{ $announcement->created_at->format('M d, Y') }}</div>
                                    <div class="time">{{ $announcement->created_at->format('h:i A') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-edit"
                                        onclick="editAnnouncement({{ $announcement->id }}, {{ json_encode($announcement->title) }}, {{ json_encode($announcement->content) }})"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action btn-delete"
                                        onclick="deleteAnnouncement({{ $announcement->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="no-data">
                                <i class="fas fa-bullhorn"></i>
                                <p>No announcements found</p>
                                <small>Click "Add Announcement" to create your first announcement.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Announcement Modal -->
    <div id="announcementModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Announcement</h2>
                <span class="close" onclick="closeAnnouncementModal()">&times;</span>
            </div>
            <form id="announcementForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" id="title" name="title" required maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="content">Content *</label>
                        <textarea id="content" name="content" required rows="6" placeholder="Enter announcement content..."></textarea>
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeAnnouncementModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <span id="submitText">Create Announcement</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content confirmation-modal">
            <div class="modal-header">
                <h2 id="confirmTitle">Confirm Action</h2>
            </div>
            <div class="modal-body">
                <div class="confirmation-content">
                    <i class="fas fa-exclamation-triangle warning-icon"></i>
                    <p id="confirmMessage">Are you sure you want to perform this action?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeConfirmModal()">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmButton">Delete</button>
            </div>
        </div>
    </div>


@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/announcements.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/admin/announcements.js') }}"></script>
@endpush
