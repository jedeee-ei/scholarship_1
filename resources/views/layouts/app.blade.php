<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'St. Paul University Philippines') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        .university-header {
            background-color: #800000;
            color: white;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .university-logo-title {
            display: flex;
            align-items: center;
            padding-left: 20px;
        }

        .university-logo {
            height: 40px;
            margin-right: 15px;
        }

        .university-title h1 {
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }

        .university-title h2 {
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }

        .logout-btn {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-right: 20px;
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
        }

        /* Dashboard Banner */
        .dashboard-banner {
            background-color: #f0f0f0;
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .dashboard-banner h2 {
            margin: 0;
            font-size: 18px;
            color: #333;
            font-weight: 600;
            text-transform: uppercase;
        }

        /* Main Content */
        .main-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .welcome-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .welcome-text {
            font-size: 20px;
            color: #333;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }

        .action-button:hover {
            background-color: #e9ecef;
            text-decoration: none;
            color: #333;
        }

        /* Profile Card */
        .profile-card {
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .profile-header {
            background-color: #800000;
            color: white;
            padding: 12px 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .profile-header i {
            margin-right: 8px;
        }

        .profile-content {
            padding: 20px;
        }

        .profile-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-role {
            color: #666;
            margin-bottom: 20px;
        }

        /* Tabs */
        .profile-tabs {
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .profile-tabs a {
            display: inline-block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            border-bottom: 2px solid transparent;
        }

        .profile-tabs a.active {
            border-bottom: 2px solid #800000;
            font-weight: bold;
        }

        /* Form Styles */
        .form-row {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #800000;
            outline: none;
        }

        .btn-primary {
            background-color: #800000;
            border-color: #800000;
        }

        .btn-primary:hover {
            background-color: #600000;
            border-color: #600000;
        }
    </style>
</head>
<body>
    <!-- University Header -->
    <header class="university-header">
        <div class="university-logo-title">
            <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo" class="university-logo">
            <div class="university-title">
                <h1>St. Paul University Philippines</h1>
                <h2>OFFICE OF THE REGISTRAR</h2>
            </div>
        </div>
        @auth
        <a href="{{ route('logout') }}" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Log Out
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
        @else
        <a href="{{ route('login') }}" class="logout-btn">
            <i class="fas fa-sign-in-alt"></i> Log In
        </a>
        @endauth
    </header>

    <!-- Dashboard Banner -->
    <div class="dashboard-banner">
        <h2>STUDENT DASHBOARD</h2>
    </div>

    <div class="main-container">
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/custom-confirm.js') }}"></script>
    <script src="{{ asset('js/error-handler.js') }}"></script>

    <!-- Override default browser dialogs to prevent "127.0.0.1:8000 says" -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Override alert() to use custom dialog
            window.originalAlert = window.alert;
            window.alert = function(message) {
                if (window.customConfirm) {
                    return window.customConfirm(message, 'Notice', 'info');
                } else {
                    // Fallback to original alert if custom confirm not available
                    return window.originalAlert(message);
                }
            };

            // Override confirm() to use custom dialog
            window.originalConfirm = window.confirm;
            window.confirm = function(message) {
                if (window.customConfirm) {
                    return window.customConfirm(message, 'Confirm', 'warning');
                } else {
                    // Fallback to original confirm if custom confirm not available
                    return window.originalConfirm(message);
                }
            };

            // Replace any existing confirm dialogs
            if (window.replaceConfirmDialogs) {
                window.replaceConfirmDialogs();
            }
        });
    </script>
</body>
</html>

