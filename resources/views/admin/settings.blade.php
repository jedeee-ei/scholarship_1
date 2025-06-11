@extends('layouts.admin')

@section('title', 'System Settings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/settings.css') }}">
@endpush

@section('breadcrumbs')
    <x-breadcrumb :items="[['title' => 'System Settings', 'icon' => 'fas fa-cog']]" />
@endsection

@section('content')
    <div class="dashboard-header">
        <h1>System Settings</h1>
    </div>

    <div class="settings-container">
        <!-- Application Settings -->
        <div class="settings-section">
            <div class="section-header">
                <i class="fas fa-graduation-cap"></i>
                <h3>Application Settings</h3>
            </div>
            <div class="section-body">
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Allow New Applications</h4>
                        <p>Enable or disable new scholarship applications</p>
                    </div>
                    <div class="setting-control">
                        <div class="toggle-switch active" onclick="toggleSetting(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Auto-approve Government Applications</h4>
                        <p>Automatically approve applications that meet Government criteria</p>
                    </div>
                    <div class="setting-control">
                        <div class="toggle-switch {{ $settings['auto_approve_government'] ? 'active' : '' }}"
                            onclick="toggleSetting(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="applicationDeadline">Application Deadline</label>
                    <input type="date" id="applicationDeadline" value="{{ $settings['application_deadline'] }}">
                </div>
                <div class="form-group">
                    <label for="maxApplications">Maximum Applications per Student</label>
                    <select id="maxApplications">
                        <option value="1" {{ $settings['max_applications_per_student'] == 1 ? 'selected' : '' }}>1
                            Application</option>
                        <option value="2" {{ $settings['max_applications_per_student'] == 2 ? 'selected' : '' }}>2
                            Applications</option>
                        <option value="3" {{ $settings['max_applications_per_student'] == 3 ? 'selected' : '' }}>3
                            Applications</option>
                        <option value="unlimited"
                            {{ $settings['max_applications_per_student'] == 'unlimited' ? 'selected' : '' }}>
                            Unlimited</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="settings-section">
            <div class="section-header">
                <i class="fas fa-bell"></i>
                <h3>Notification Settings</h3>
            </div>
            <div class="section-body">
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Email Notifications</h4>
                        <p>Send email notifications for application updates</p>
                    </div>
                    <div class="setting-control">
                        <div class="toggle-switch {{ $settings['email_notifications'] ? 'active' : '' }}"
                            onclick="toggleSetting(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                </div>
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>SMS Notifications</h4>
                        <p>Send SMS notifications for urgent updates</p>
                    </div>
                    <div class="setting-control">
                        <div class="toggle-switch {{ $settings['sms_notifications'] ? 'active' : '' }}"
                            onclick="toggleSetting(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="adminEmail">Admin Email Address</label>
                    <input type="email" id="adminEmail" value="{{ $settings['contact_email'] }}">
                </div>
            </div>
        </div>

        <!-- System Configuration -->
        <div class="settings-section">
            <div class="section-header">
                <i class="fas fa-cogs"></i>
                <h3>System Configuration</h3>
            </div>
            <div class="section-body">
                <div class="form-group">
                    <label for="systemName">System Name</label>
                    <input type="text" id="systemName" value="{{ $settings['system_name'] }}">
                </div>
                <div class="form-group">
                    <label for="institutionName">Institution Name</label>
                    <input type="text" id="institutionName" value="{{ $settings['institution_name'] }}">
                </div>
                <div class="form-group">
                    <label for="contactEmail">Contact Email</label>
                    <input type="email" id="contactEmail" value="{{ $settings['contact_email'] }}">
                </div>
                <div class="form-group">
                    <label for="contactPhone">Contact Phone</label>
                    <input type="tel" id="contactPhone" value="{{ $settings['contact_phone'] }}">
                </div>
                <div class="form-group">
                    <label for="systemMessage">System Message</label>
                    <textarea id="systemMessage" placeholder="Enter a message to display to users...">Welcome to the Scholarship Management System. Please ensure all information is accurate before submitting your application.</textarea>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="settings-section">
            <div class="section-header">
                <i class="fas fa-shield-alt"></i>
                <h3>Security Settings</h3>
            </div>
            <div class="section-body">
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Two-Factor Authentication</h4>
                        <p>Require 2FA for admin accounts</p>
                    </div>
                    <div class="setting-control">
                        <div class="toggle-switch" onclick="toggleSetting(this)">
                            <div class="toggle-slider"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="sessionTimeout">Session Timeout (minutes)</label>
                    <select id="sessionTimeout">
                        <option value="30" selected>30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="120">2 hours</option>
                        <option value="480">8 hours</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="passwordPolicy">Password Policy</label>
                    <select id="passwordPolicy">
                        <option value="basic">Basic (8 characters)</option>
                        <option value="medium" selected>Medium (8 chars, mixed case, numbers)</option>
                        <option value="strong">Strong (12 chars, mixed case, numbers, symbols)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Save Actions -->
        <div class="settings-section">
            <div class="section-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Changes will take effect immediately. Please review all settings before saving.
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="resetSettings()">
                        <i class="fas fa-undo"></i> Reset to Defaults
                    </button>
                    <button type="button" class="btn-primary" onclick="saveSettings()">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            // Settings management functions
            function toggleSetting(element) {
                element.classList.toggle('active');
            }

            function saveSettings() {
                // Show success message
                const alertDiv = document.querySelector('.alert-warning');
                alertDiv.className = 'alert alert-success';
                alertDiv.innerHTML = '<i class="fas fa-check-circle"></i> Settings saved successfully!';

                console.log('Saving settings...');

                // Reset message after 3 seconds
                setTimeout(() => {
                    alertDiv.className = 'alert alert-warning';
                    alertDiv.innerHTML =
                        '<i class="fas fa-exclamation-triangle"></i> Changes will take effect immediately. Please review all settings before saving.';
                }, 3000);
            }

            function resetSettings() {
                if (confirm('Are you sure you want to reset all settings to their default values?')) {
                    console.log('Resetting settings to defaults...');
                    location.reload();
                }
            }
        </script>
    @endpush
