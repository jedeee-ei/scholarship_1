<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Scholarship Management</title>

    <!-- Base CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/modals.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Page-specific CSS -->
    @stack('styles')

    <!-- Additional CSS from sections -->
    @yield('additional-css')
</head>

<body>
    <!-- University Header -->
    <header class="university-header">
        <div class="header-content">
            <div class="university-logo-title">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo"
                    class="university-logo">
                <div class="university-title">
                    <h1>St. Paul University Philippines</h1>
                    <h2>ADMINISTRATOR DASHBOARD</h2>
                </div>
            </div>
            <div class="user-actions">
                <a href="{{ route('welcome') }}" class="logout-btn">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </header>

    <!-- Dashboard Banner -->
    <div class="dashboard-banner">
        <div class="banner-container">
            <div class="banner-content">
                <h2>SCHOLARSHIP MANAGEMENT SYSTEM</h2>
                @yield('breadcrumbs')
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-profile">
                <div class="profile-image">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-info">
                    <h3>Admin User</h3>
                    <p>Administrator</p>
                </div>
            </div>
            <nav class="admin-nav">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('admin.applications') }}"
                    class="nav-item {{ request()->routeIs('admin.applications*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i> Applications
                </a>
                <a href="{{ route('admin.students') }}"
                    class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Grantee
                </a>
                <a href="{{ route('admin.scholarships') }}"
                    class="nav-item {{ request()->routeIs('admin.scholarships*') ? 'active' : '' }}">
                    <i class="fas fa-award"></i> Benefactor
                </a>
                <a href="{{ route('admin.announcements') }}"
                    class="nav-item {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i> Announcements
                </a>
                <a href="{{ route('admin.archived-students') }}"
                    class="nav-item {{ request()->routeIs('admin.archived-students*') ? 'active' : '' }}">
                    <i class="fas fa-archive"></i> Archives
                </a>
                <a href="{{ route('admin.reports') }}"
                    class="nav-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="{{ route('admin.settings') }}"
                    class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="admin-content">
            @yield('content')
        </div>
    </div>

    <!-- Notification Component -->
    @include('components.notification')

    <!-- Preload jsPDF for faster PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- Base JavaScript -->
    <script>
        // Initialize navigation to ensure proper link behavior
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Allow default link behavior
                    return true;
                });
            });
        });
    </script>

    <!-- Page-specific JavaScript -->
    @stack('scripts')

    <!-- Additional JavaScript from sections -->
    @yield('additional-js')
</body>

</html>
