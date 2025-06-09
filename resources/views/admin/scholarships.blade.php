@extends('layouts.admin')

@section('title', 'Benefactor')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/scholarships.css') }}">
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[['title' => 'Benefactor', 'icon' => 'fas fa-award']]" />
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>Benefactor Management</h1>
    </div>

    <!-- Quick Actions for Benefactor -->
    <div class="quick-actions">
        <a href="#" class="action-card" onclick="showAddScholarshipForm(); return false;">
            <div class="action-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-title">Add Benefactor</div>
            <div class="action-description">Create new benefactor program</div>
        </a>
    </div>

    <!-- Benefactor Programs Table -->
    <div class="scholarship-programs">
        <div class="table-header">
            <h3>Scholarship Programs</h3>
            <div class="table-actions">
                <button class="update-semester-year-btn" onclick="showUpdateSemesterYearModal()">
                    <i class="fas fa-calendar-alt"></i> Update Semester/Year
                </button>
            </div>
        </div>
        <table class="scholarships-table">
            <thead>
                <tr>
                    <th>Benefactor Name</th>
                    <th>Type</th>
                    <th>Active Grantees</th>
                    <th>Semester</th>
                    <th>Academic Year</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="scholarshipTableBody">
                @if ($scholarshipStats && count($scholarshipStats) > 0)
                    @foreach ($scholarshipStats as $key => $scholarship)
                        <tr>
                            <td>
                                <div class="scholarship-name-cell">
                                    @php
                                        $typeClass = isset($scholarship['is_custom'])
                                            ? strtolower($scholarship['type'])
                                            : $key;
                                    @endphp
                                    <span class="scholarship-type-indicator {{ $typeClass }}"></span>
                                    {{ $scholarship['name'] }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $typeClass = strtolower($scholarship['type']);
                                @endphp
                                <span class="scholarship-type {{ $typeClass }}">{{ $scholarship['type'] }}</span>
                            </td>
                            <td>
                                <div class="grantees-count">
                                    <span class="count-number">{{ $scholarship['active_grantees'] ?? 0 }}</span>
                                    <span
                                        class="count-label">{{ $scholarship['active_grantees'] == 1 ? 'Grantee' : 'Grantees' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="semester-badge">{{ $scholarship['semester'] ?? '1st Semester' }}</span>
                            </td>
                            <td>
                                <span class="academic-year">{{ $scholarship['academic_year'] ?? '2024-2025' }}</span>
                            </td>
                            <td>
                                @php
                                    $scholarshipKey = strtolower($scholarship['type']);
                                @endphp
                                <a href="{{ route('admin.students', ['type' => $scholarshipKey]) }}" class="action-btn view"
                                    title="View Grantees">
                                    <i class="fas fa-users"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="fas fa-award"></i>
                            <h3>No Scholarship Programs</h3>
                            <p>No scholarship programs have been configured yet.</p>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection

<!-- Add Benefactor Modal -->
<div id="addScholarshipModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Benefactor Program</h2>
            <span class="close" onclick="closeAddScholarshipModal()">&times;</span>
        </div>
        <form id="addScholarshipForm" onsubmit="saveNewScholarship(event)">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="scholarshipName">Program Name</label>
                        <input type="text" id="scholarshipName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="scholarshipType">Type</label>
                        <select id="scholarshipType" name="type" required>
                            <option value="">Select Type</option>
                            <option value="government">Government</option>
                            <option value="academic">Academic</option>
                            <option value="employees">Employee</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="scholarshipSemester">Semester</label>
                        <select id="scholarshipSemester" name="semester" required>
                            <option value="">Select Semester</option>
                            <option value="1st Semester">1st Semester</option>
                            <option value="2nd Semester">2nd Semester</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scholarshipAcademicYear">Academic Year</label>
                        <input type="text" id="scholarshipAcademicYear" name="academic_year"
                            placeholder="e.g., 2024-2025" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="scholarshipDescription">Description</label>
                        <textarea id="scholarshipDescription" name="description" rows="3"
                            placeholder="Brief description of the scholarship program"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddScholarshipModal()">Cancel</button>
                <button type="submit" class="btn-primary">Add Scholarship</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Semester/Year Modal -->
<div id="updateSemesterYearModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h2>Monitor Benefactor's Scholars</h2>
            <span class="close" onclick="closeUpdateSemesterYearModal()">&times;</span>
        </div>
        <div class="modal-body" style="padding: 20px;">
            <div class="update-selection">
                <div class="radio-group" style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="updateType" value="semester" id="semesterRadio"
                            style="margin-right: 8px;">
                        <span>Per Semester</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="updateType" value="year" id="yearRadio"
                            style="margin-right: 8px;">
                        <span>Per Academic Year</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding: 15px 20px; display: flex; gap: 10px; justify-content: flex-end;">
            <button type="button" class="btn btn-secondary" onclick="closeUpdateSemesterYearModal()"
                style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Close</button>
            <button type="button" class="btn btn-primary" id="updateBtn" onclick="executeUpdate()"
                style="background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Update</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Scholarship management functions
        async function showAddScholarshipForm() {
            console.log('showAddScholarshipForm called'); // Debug log

            try {
                // Fetch current semester and year from API
                const response = await fetch('/admin/current-semester-year');
                if (response.ok) {
                    const data = await response.json();

                    // Set current values as defaults
                    const academicYearInput = document.getElementById('scholarshipAcademicYear');
                    const semesterSelect = document.getElementById('scholarshipSemester');

                    if (academicYearInput) {
                        academicYearInput.value = data.current_academic_year;
                    }
                    if (semesterSelect) {
                        semesterSelect.value = data.current_semester;
                    }
                } else {
                    // Fallback to calculated values
                    const currentYear = new Date().getFullYear();
                    const currentMonth = new Date().getMonth() + 1;
                    let academicYear;

                    if (currentMonth >= 7) {
                        academicYear = currentYear + '-' + (currentYear + 1);
                    } else {
                        academicYear = (currentYear - 1) + '-' + currentYear;
                    }

                    const academicYearInput = document.getElementById('scholarshipAcademicYear');
                    if (academicYearInput) {
                        academicYearInput.value = academicYear;
                    }
                }
            } catch (error) {
                console.error('Error fetching current semester/year:', error);
                // Use fallback values
                const currentYear = new Date().getFullYear();
                const currentMonth = new Date().getMonth() + 1;
                let academicYear;

                if (currentMonth >= 7) {
                    academicYear = currentYear + '-' + (currentYear + 1);
                } else {
                    academicYear = (currentYear - 1) + '-' + currentYear;
                }

                const academicYearInput = document.getElementById('scholarshipAcademicYear');
                if (academicYearInput) {
                    academicYearInput.value = academicYear;
                }
            }

            const modal = document.getElementById('addScholarshipModal');
            if (modal) {
                modal.style.display = 'block';
                modal.classList.add('modal-show');
                console.log('Modal opened successfully'); // Debug log
            } else {
                console.error('Modal not found!');
            }
        }

        function closeAddScholarshipModal() {
            const modal = document.getElementById('addScholarshipModal');
            modal.style.display = 'none';
            modal.classList.remove('modal-show');
            document.getElementById('addScholarshipForm').reset();
        }

        function saveNewScholarship(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            // Show loading state
            submitBtn.textContent = 'Adding...';
            submitBtn.disabled = true;

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('Security token not found. Please refresh the page.');
                return;
            }

            // Send request to add scholarship
            fetch('/admin/scholarships/add', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => {
                    if (response.status === 419) {
                        throw new Error('Session expired. Please refresh the page and try again.');
                    }
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        closeAddScholarshipModal();
                        window.location.reload();
                    } else {
                        console.error(data.message || 'Failed to add scholarship program.');
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

        function viewScholarshipStudents(scholarshipType) {
            // Redirect to students page with filter for this scholarship type
            window.location.href = "{{ route('admin.students') }}?scholarship_type=" + scholarshipType;
        }

        async function showUpdateSemesterYearModal() {
            // Show modal
            const modal = document.getElementById('updateSemesterYearModal');
            modal.style.display = 'block';
            modal.classList.add('modal-show');
        }

        function closeUpdateSemesterYearModal() {
            const modal = document.getElementById('updateSemesterYearModal');
            modal.style.display = 'none';
            modal.classList.remove('modal-show');
            // Reset selections
            document.querySelectorAll('input[name="updateType"]').forEach(radio => radio.checked = false);
        }

        // Add event listeners for radio buttons
        document.addEventListener('DOMContentLoaded', function() {
            const radioButtons = document.querySelectorAll('input[name="updateType"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Radio button selection automatically enables the update button
                    // No additional logic needed since radio buttons handle selection
                });
            });
        });

        function executeUpdate() {
            const selectedType = document.querySelector('input[name="updateType"]:checked');

            if (!selectedType) {
                alert('Please select an update type first.');
                return;
            }

            if (selectedType.value === 'semester') {
                updateSemester();
            } else if (selectedType.value === 'year') {
                updateAcademicYear();
            }
        }

        async function updateSemester() {
            try {
                // Fetch current semester data
                const response = await fetch('/admin/current-semester-year');
                if (!response.ok) {
                    throw new Error('Failed to fetch current semester/year');
                }

                const data = await response.json();
                const currentSemester = data.current_semester;
                const nextSemester = currentSemester === '1st Semester' ? '2nd Semester' : '1st Semester';

                if (confirm(
                        `Are you sure you want to update from "${currentSemester}" to "${nextSemester}"?\n\nThis will archive current students and reset applications.`
                    )) {
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        alert('Security token not found. Please refresh the page.');
                        return;
                    }

                    // Send request to update semester
                    fetch('/admin/scholarships/update-semester', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                current_semester: currentSemester,
                                new_semester: nextSemester
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert('Semester updated successfully!');
                                closeUpdateSemesterYearModal();
                                window.location.reload();
                            } else {
                                alert('Failed to update semester: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            alert('Error updating semester: ' + error.message);
                        });
                }
            } catch (error) {
                alert('Error fetching current data: ' + error.message);
            }
        }

        async function updateAcademicYear() {
            try {
                // Fetch current academic year data
                const response = await fetch('/admin/current-semester-year');
                if (!response.ok) {
                    throw new Error('Failed to fetch current semester/year');
                }

                const data = await response.json();
                const currentYear = data.current_academic_year;
                const yearParts = currentYear.split('-');
                const nextYear = (parseInt(yearParts[0]) + 1) + '-' + (parseInt(yearParts[1]) + 1);

                if (confirm(
                        `Are you sure you want to update from "${currentYear}" to "${nextYear}"?\n\nThis will reset to 1st Semester, archive current students, and reset applications.`
                    )) {
                    // Get CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) {
                        alert('Security token not found. Please refresh the page.');
                        return;
                    }

                    // Send request to update academic year
                    fetch('/admin/scholarships/update-year', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                current_year: currentYear,
                                new_year: nextYear
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert('Academic year updated successfully!');
                                closeUpdateSemesterYearModal();
                                window.location.reload();
                            } else {
                                alert('Failed to update academic year: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            alert('Error updating academic year: ' + error.message);
                        });
                }
            } catch (error) {
                alert('Error fetching current data: ' + error.message);
            }
        }



        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('addScholarshipModal');
            if (event.target === modal) {
                closeAddScholarshipModal();
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

        // Function to refresh CSRF token
        async function refreshCSRFToken() {
            try {
                const response = await fetch('/csrf-token', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    if (csrfMeta && data.csrf_token) {
                        csrfMeta.setAttribute('content', data.csrf_token);
                        return data.csrf_token;
                    }
                }
            } catch (error) {
                console.error('Failed to refresh CSRF token:', error);
            }
            return null;
        }

        // Auto-refresh CSRF token every 60 minutes
        setInterval(refreshCSRFToken, 60 * 60 * 1000);

        // Keep session alive every 30 minutes
        setInterval(async () => {
            try {
                const response = await fetch('/keep-alive', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            ?.getAttribute(
                                'content') || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    // Update CSRF token if provided
                    if (data.csrf_token) {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (csrfMeta) {
                            csrfMeta.setAttribute('content', data.csrf_token);
                        }
                    }
                }
            } catch (error) {
                console.warn('Session keep-alive failed:', error);
            }
        }, 30 * 60 * 1000); // 30 minutes

        // Helper function to make authenticated requests with automatic CSRF token refresh
        async function makeAuthenticatedRequest(url, options = {}) {
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!csrfToken) {
                csrfToken = await refreshCSRFToken();
                if (!csrfToken) {
                    throw new Error('Unable to obtain CSRF token. Please refresh the page.');
                }
            }

            const defaultOptions = {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...options.headers
                }
            };

            const mergedOptions = {
                ...defaultOptions,
                ...options
            };

            try {
                const response = await fetch(url, mergedOptions);

                // If we get a 419 error, try to refresh the token and retry once
                if (response.status === 419) {
                    const newToken = await refreshCSRFToken();
                    if (newToken) {
                        mergedOptions.headers['X-CSRF-TOKEN'] = newToken;
                        return await fetch(url, mergedOptions);
                    }
                }

                return response;
            } catch (error) {
                throw error;
            }
        }

        // Confirmation Modal Functions
        function showConfirmModal(title, message, buttonText, onConfirm) {
            const modal = document.getElementById('confirmModal');
            const titleElement = document.getElementById('confirmTitle');
            const messageElement = document.getElementById('confirmMessage');
            const confirmButton = document.getElementById('confirmButton');

            titleElement.textContent = title;
            messageElement.innerHTML = message.replace(/\n/g, '<br>');
            confirmButton.textContent = buttonText;

            // Remove any existing event listeners
            const newConfirmButton = confirmButton.cloneNode(true);
            confirmButton.parentNode.replaceChild(newConfirmButton, confirmButton);

            // Add new event listener
            newConfirmButton.addEventListener('click', () => {
                closeConfirmModal();
                onConfirm();
            });

            modal.style.display = 'block';
        }

        function closeConfirmModal() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const confirmModal = document.getElementById('confirmModal');
            if (event.target === confirmModal) {
                closeConfirmModal();
            }
        });
    </script>
@endpush

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="modal" style="display: none;">
    <div class="modal-content confirmation-modal">
        <div class="modal-header">
            <h2 id="confirmTitle">Confirm Action</h2>
        </div>
        <div class="modal-body">
            <div class="confirmation-content">
                <i class="fas fa-exclamation-triangle warning-icon"></i>
                <div class="confirmation-text">
                    <p id="confirmMessage">Are you sure you want to perform this action?</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmButton">Confirm</button>
        </div>
    </div>
</div>
