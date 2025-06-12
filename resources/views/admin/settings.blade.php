@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="container">
    <div class="header">
        <h1>System Settings</h1>
    </div>

    <div class="settings-container">
        <div class="settings-card">
            <h3>Current Settings</h3>
            <div class="settings-info">
                <div class="setting-item">
                    <label>Current Semester:</label>
                    <span>{{ $currentSemester }}</span>
                </div>
                <div class="setting-item">
                    <label>Current Academic Year:</label>
                    <span>{{ $currentAcademicYear }}</span>
                </div>
                <div class="setting-item">
                    <label>Application Status:</label>
                    <span class="status-badge {{ $applicationStatus === 'open' ? 'open' : 'closed' }}">
                        {{ ucfirst($applicationStatus) }}
                    </span>
                </div>
            </div>
            
            <div class="settings-actions">
                <button onclick="showSettingsModal()" class="btn-primary">
                    <i class="fas fa-edit"></i> Edit Settings
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.settings-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.settings-card {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.settings-info {
    margin: 20px 0;
}

.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.setting-item:last-child {
    border-bottom: none;
}

.setting-item label {
    font-weight: 600;
    color: #333;
}

.status-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-badge.open {
    background: #d4edda;
    color: #155724;
}

.status-badge.closed {
    background: #f8d7da;
    color: #721c24;
}

.settings-actions {
    margin-top: 30px;
    text-align: center;
}

.btn-primary {
    background: #1e5631;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #0f2818;
}
</style>

<script>
// Include the settings modal functionality from dashboard
window.showSettingsModal = async function() {
    try {
        // Fetch current settings
        const response = await fetch('/admin/current-semester-year');

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        // Create modal HTML
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2>System Settings</h2>
                    <button onclick="closeModal()" class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="settingsForm">
                        <!-- Application Status Section -->
                        <div class="settings-section">
                            <h4>Application Settings</h4>
                            <div class="form-group">
                                <label for="modalApplicationToggle">Application Status:</label>
                                <div class="toggle-container">
                                    <input type="checkbox" id="modalApplicationToggle" ${data.application_status === 'open' ? 'checked' : ''}>
                                    <label for="modalApplicationToggle" class="toggle-label">
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <span id="modalApplicationStatusText" style="margin-left: 10px; font-weight: bold; color: ${data.application_status === 'open' ? '#28a745' : '#dc3545'};">
                                        ${data.application_status === 'open' ? 'Open' : 'Closed'}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Year Settings Section -->
                        <div class="settings-section">
                            <h4>Academic Year Settings</h4>
                            <div class="form-group">
                                <label for="currentAY">Current Academic Year:</label>
                                <input type="text" id="currentAY" name="current_academic_year" value="${data.current_academic_year}">
                            </div>
                            <div class="form-group">
                                <label for="currentSem">Current Semester:</label>
                                <select id="currentSem" name="current_semester">
                                    <option value="1st Semester" ${data.current_semester === '1st Semester' ? 'selected' : ''}>1st Semester</option>
                                    <option value="2nd Semester" ${data.current_semester === '2nd Semester' ? 'selected' : ''}>2nd Semester</option>
                                    <option value="Summer" ${data.current_semester === 'Summer' ? 'selected' : ''}>Summer</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button onclick="closeModal()" class="btn-secondary">Cancel</button>
                    <button onclick="saveSettings()" class="btn-primary">Save Settings</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        // Add toggle functionality
        const toggle = document.getElementById('modalApplicationToggle');
        const statusText = document.getElementById('modalApplicationStatusText');

        if (toggle && statusText) {
            toggle.addEventListener('change', function() {
                const isOpen = this.checked;
                statusText.textContent = isOpen ? 'Open' : 'Closed';
                statusText.style.color = isOpen ? '#28a745' : '#dc3545';
            });
        }

    } catch (error) {
        console.error('Error loading settings:', error);
        alert('Error loading current settings. Please try again.');
    }
};

window.closeModal = function() {
    const modal = document.querySelector('.modal-overlay');
    if (modal) {
        modal.remove();
    }
};

window.saveSettings = async function() {
    const form = document.getElementById('settingsForm');
    const formData = new FormData(form);

    // Handle checkbox for application status
    const toggle = document.getElementById('modalApplicationToggle');
    if (toggle) {
        formData.set('application_status', toggle.checked ? 'open' : 'closed');
    }

    try {
        const response = await fetch('/admin/settings', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (response.ok) {
            alert('Settings saved successfully!');
            closeModal();
            // Refresh the page to update any displayed settings
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('Failed to save settings: ' + result.message);
        }
    } catch (error) {
        alert('Settings save failed. Please try again.');
    }
};
</script>
@endsection
