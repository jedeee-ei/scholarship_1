<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student Portal') - St. Paul University Philippines</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional CSS files -->
    @stack('styles')

    <style>
        /* Shared Header Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }

        .header {
            background: linear-gradient(135deg, #052F11 0%, #052F11 100%);
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
            gap: 15px;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .university-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .office-name {
            font-size: 0.9rem;
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 400;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
        }

        /* Main content area */
        .main-content {
            min-height: calc(100vh - 80px);
        }

        /* Container for page content */
        .page-container {
            padding: 10px 20px;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header-container {
                padding: 0 15px;
            }

            .university-logo {
                gap: 10px;
            }

            .logo-img {
                width: 40px;
                height: 40px;
            }

            .university-name {
                font-size: 1.2rem;
            }

            .office-name {
                font-size: 0.8rem;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }

            .page-container {
                padding: 10px 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="university-logo">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo"
                    class="logo-img">
                <div>
                    <h1 class="university-name">St. Paul University Philippines</h1>
                    <p class="office-name">OFFICE OF THE REGISTRAR</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>

    <!-- Notification Component -->
    @include('components.notification')

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Confirm Dialog -->
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

    <!-- Additional scripts -->
    @stack('scripts')
</body>

</html>
