<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Scholarship Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1e5631;
            --primary-light: #e8f5e8;
            --primary-dark: #164023;
            --text-color: #333;
            --border-color: #ddd;
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Header */
        .university-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .university-logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: 700;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 20px;
        }

        .user-actions {
            display: flex;
            gap: 15px;
        }

        .action-link {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            text-decoration: none;
            color: white;
        }

        .action-link.active {
            background-color: rgba(255, 255, 255, 0.3);
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-header {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-subtitle {
            color: #666;
            font-size: 16px;
        }

        /* Profile Content */
        .profile-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .profile-tabs {
            display: flex;
            border-bottom: 1px solid #eee;
        }

        .profile-tabs a {
            padding: 20px 30px;
            text-decoration: none;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .profile-tabs a.active,
        .profile-tabs a:hover {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .tab-content {
            padding: 30px;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 86, 49, 0.1);
        }

        .row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .col-md-6 {
            flex: 1;
        }

        /* Student ID field styling */
        .student-id-field {
            flex: 0 0 300px;
            max-width: 300px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .d-grid {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }

            .user-actions {
                flex-direction: column;
                width: 100%;
            }

            .profile-tabs {
                flex-direction: column;
            }

            .row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="university-header">
        <div class="header-container">
            <div class="university-logo">
                <div class="logo-img">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                St. Paul University Philippines - Scholarship Management
            </div>
            <div class="user-actions">
                <a href="/student/dashboard" class="action-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="/student/applications" class="action-link">
                    <i class="fas fa-clipboard-list"></i> My Applications
                </a>
                <a href="/student/profile" class="action-link active">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-user-circle"></i>
                My Profile
            </h1>
            <p class="page-subtitle">Manage your personal information and account settings</p>
        </div>

        <div class="profile-container">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="profile-tabs">
                <a href="#personal" class="active" id="personal-tab" onclick="switchTab('personal'); return false;">Personal Information</a>
                <a href="#security" id="security-tab" onclick="switchTab('security'); return false;">Security Settings</a>
            </div>

            <div class="tab-content">
                <div id="personal-content">
                    <form action="/student/profile/update" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-row">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="Student User" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-row">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" name="email" value="student@spup.edu.ph" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="student-id-field">
                                <div class="form-row">
                                    <label class="form-label">Student ID</label>
                                    <input type="text" class="form-control" name="student_id" value="2024-001" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>

                <div id="security-content" style="display: none;">
                    <form action="/student/profile/password" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-row">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>

                        <div class="form-row">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>

                        <div class="form-row">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all content
            document.getElementById('personal-content').style.display = 'none';
            document.getElementById('security-content').style.display = 'none';

            // Remove active class from all tabs
            document.getElementById('personal-tab').classList.remove('active');
            document.getElementById('security-tab').classList.remove('active');

            // Show selected content and activate tab
            document.getElementById(tab + '-content').style.display = 'block';
            document.getElementById(tab + '-tab').classList.add('active');
        }
    </script>
</body>
</html>

