<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\GranteeController;
use App\Http\Controllers\Admin\ScholarshipManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ArchiveController;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Student\ScholarshipController;
use App\Http\Controllers\Student\ScholarshipTrackerController;
use App\Http\Controllers\Api\ScholarshipDataController;
use Illuminate\Support\Facades\Auth;

Route::get('/welcome', function () {
    return view('layouts.welcome');
})->name('welcome');
Route::get('/splashscreen', function () {
    return view('layouts.splashscreen');
});
Route::get('/login', function () {
    return view('layouts.login');
})->name('login');

Route::get('/logout', function () {
    // If using Laravel's built-in auth
    if (Auth::check()) {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    // For custom session handling
    session()->forget('user_id');
    session()->forget('user_name');
    session()->forget('user_role');

    return redirect()->route('login');
})->name('logout');

// Authentication routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login/{type}', [App\Http\Controllers\Auth\LoginController::class, 'showLoginFormByType'])->name('login.form');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Student routes with authentication
Route::middleware(['auth', 'student'])->group(function () {
    // Student dashboard route
    Route::get('/student/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('student.dashboard');

    // Student password change
    Route::post('/student/change-password', [\App\Http\Controllers\Student\DashboardController::class, 'changePassword'])->name('student.change-password');

    // Scholarship routes
    Route::post('/scholarship/submit', [ScholarshipController::class, 'submitApplication'])->name('scholarship.submit');

    Route::get('/scholarship/success', [ScholarshipController::class, 'showSuccess'])->name('scholarship.success');
    Route::post('/student/check-duplicate', [ScholarshipController::class, 'checkDuplicate'])->name('student.check-duplicate');

    // Scholarship Application Tracker - remove ownership middleware for now to test
    Route::get('/scholarship/tracker', [ScholarshipTrackerController::class, 'showTracker'])->name('scholarship.tracker');
    Route::post('/scholarship/track', [ScholarshipTrackerController::class, 'trackApplication'])->name('scholarship.track');

    // Test routes
    Route::get('/test-applications', function() {
        $student = Auth::user();
        if (!$student) {
            return response()->json(['error' => 'Not logged in']);
        }

        $allApplications = \App\Models\ScholarshipApplication::where('student_id', $student->student_id)->get();
        $permanentStatus = \App\Models\ScholarshipApplication::where('student_id', $student->student_id)
            ->whereIn('status', ['Approved', 'Rejected'])
            ->orderBy('updated_at', 'desc')
            ->first();

        return response()->json([
            'student_id' => $student->student_id,
            'student_name' => $student->name,
            'all_applications' => $allApplications->map(function($app) {
                return [
                    'id' => $app->application_id,
                    'status' => $app->status,
                    'type' => $app->scholarship_type,
                    'created' => $app->created_at,
                    'updated' => $app->updated_at
                ];
            }),
            'permanent_status' => $permanentStatus ? [
                'id' => $permanentStatus->application_id,
                'status' => $permanentStatus->status,
                'type' => $permanentStatus->scholarship_type,
                'subtype' => $permanentStatus->scholarship_subtype,
                'updated_at' => $permanentStatus->updated_at
            ] : null
        ]);
    });

    // Route to create test data for current user
    Route::get('/create-test-data', function() {
        $student = Auth::user();
        if (!$student) {
            return response()->json(['error' => 'Not logged in']);
        }

        // Check if user already has applications
        $existing = \App\Models\ScholarshipApplication::where('student_id', $student->student_id)->first();
        if ($existing) {
            return response()->json(['message' => 'Test data already exists', 'application_id' => $existing->application_id]);
        }

        // Create test application
        $application = \App\Models\ScholarshipApplication::create([
            'application_id' => 'SCH-' . strtoupper(substr($student->student_id, -6)),
            'student_id' => $student->student_id,
            'first_name' => $student->first_name ?? 'Test',
            'last_name' => $student->last_name ?? 'Student',
            'email' => $student->email,
            'contact_number' => '09123456789',
            'scholarship_type' => 'academic',
            'scholarship_subtype' => 'PL',
            'department' => 'SITE',
            'course' => 'BSIT',
            'year_level' => '3rd Year',
            'gwa' => '1.25',
            'status' => 'Approved',
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subDays(5)
        ]);

        return response()->json(['message' => 'Test data created', 'application_id' => $application->application_id]);
    });
});

// API route for loading subjects (used by student dashboard) - accessible to authenticated users
Route::get('/api/subjects', [ScholarshipDataController::class, 'getSubjectsForDashboard'])->middleware('auth');

// Admin routes with authentication and authorization
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/applications', [ApplicationController::class, 'index'])->name('admin.applications');
    Route::get('/applications/{id}', [ApplicationController::class, 'show'])->name('admin.application.view');
    Route::post('/applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('admin.application.status');

    // New admin page routes
    Route::get('/admin/students', [GranteeController::class, 'index'])->name('admin.students');
    Route::get('/admin/student-register', [UserManagementController::class, 'studentRegister'])->name('admin.student-register');
    Route::post('/admin/student-register', [UserManagementController::class, 'storeStudentRegister'])->name('admin.student-register.store');
    Route::post('/admin/check-student-id', [UserManagementController::class, 'checkStudentIdAvailability'])->name('admin.check-student-id');
    Route::post('/admin/students/{id}/edit', [UserManagementController::class, 'editStudent'])->name('admin.student.edit');
    Route::post('/admin/students/{id}/delete', [UserManagementController::class, 'deleteStudent'])->name('admin.student.delete');
    Route::post('/students/{id}/update', [GranteeController::class, 'updateStudent'])->name('admin.student.update');
    Route::post('/admin/grantees/{id}/update', [GranteeController::class, 'updateGrantee'])->name('admin.grantee.update');
    Route::get('/admin/scholarships', [ScholarshipManagementController::class, 'index'])->name('admin.scholarships');
    Route::post('/admin/scholarships/add', [ScholarshipManagementController::class, 'addScholarship'])->name('admin.scholarship.add');

    Route::get('/admin/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements');
    Route::post('/admin/announcements/store', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
    Route::put('/admin/announcements/{id}', [AnnouncementController::class, 'update'])->name('admin.announcements.update');
    Route::delete('/admin/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.delete');
    Route::get('/api/announcements', [AnnouncementController::class, 'getPublishedAnnouncements'])->name('api.announcements');

    Route::get('/admin/archived-students', [ArchiveController::class, 'index'])->name('admin.archived-students');
    Route::get('/admin/archived-students/export', [ArchiveController::class, 'exportArchivedStudents'])->name('admin.archived-students.export');
    Route::get('/admin/archived-students/{id}/details', [ArchiveController::class, 'getArchivedStudentDetails'])->name('admin.archived-students.details');
    Route::get('/admin/archived-scholarships', [ArchiveController::class, 'index'])->name('admin.archived-scholarships');
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');
    // New admin functionality routes
    Route::post('/admin/students/import', [ImportExportController::class, 'bulkImportStudents'])->name('admin.students.import');
    Route::get('/admin/applications/export', [ImportExportController::class, 'exportApplicationsData'])->name('admin.applications.export');

    // Reports and Archive routes
    Route::post('/admin/reports/generate', [ReportController::class, 'generateReport'])->name('admin.reports.generate');
    Route::post('/admin/reports/preview', [ReportController::class, 'previewReport'])->name('admin.reports.preview');
    Route::get('/admin/archive/search', [ArchiveController::class, 'searchArchive'])->name('admin.archive.search');
    Route::get('/admin/archive/download/{fileId}', [ArchiveController::class, 'downloadArchive'])->name('admin.archive.download');
    Route::delete('/admin/archive/delete/{fileId}', [ArchiveController::class, 'deleteArchive'])->name('admin.archive.delete');

    // Students management routes
    Route::get('/admin/students/data', [GranteeController::class, 'getStudentsData'])->name('admin.students.data');
    Route::get('/admin/students/category/{category}', [GranteeController::class, 'getStudentsByCategory'])->name('admin.students.category');
    Route::post('/admin/students/add', [GranteeController::class, 'addStudent'])->name('admin.students.add');
    Route::get('/admin/students/export', [GranteeController::class, 'exportStudents'])->name('admin.students.export');

    // Applications management routes
    Route::get('/admin/applications/data', [ApplicationController::class, 'getApplicationsData'])->name('admin.applications.data');
    Route::get('/admin/applications/{id}/detail', [ApplicationController::class, 'getApplicationDetail'])->name('admin.application.detail');
    Route::post('/admin/applications/{id}/status', [ApplicationController::class, 'updateApplicationStatus'])->name('admin.application.update-status');

    // Document management routes
    Route::get('/admin/applications/{application}/documents/{document}/download', [DocumentController::class, 'downloadDocument'])->name('admin.application.document.download');
    Route::get('/admin/applications/{application}/documents/{document}/view', [DocumentController::class, 'viewDocument'])->name('admin.application.document.view');

    // API routes for scholarship data
    Route::prefix('api/scholarship')->group(function () {
        Route::get('/departments', [ScholarshipDataController::class, 'getDepartments']);
        Route::get('/departments/{departmentCode}/courses', [ScholarshipDataController::class, 'getCoursesByDepartment']);
        Route::get('/course-durations', [ScholarshipDataController::class, 'getAllCourseDurations']);
        Route::get('/department-course-mapping', [ScholarshipDataController::class, 'getDepartmentCourseMapping']);
        Route::get('/subjects/{courseName}/{yearLevel}/{semester}', [ScholarshipDataController::class, 'getSubjects']);
    });

    // API routes for dashboard analytics
    Route::prefix('api/admin')->group(function () {
        Route::get('/chart-data', [DashboardController::class, 'getChartDataApi']);
        Route::get('/analytics-summary', [DashboardController::class, 'getAnalyticsSummary']);
        Route::get('/dashboard-stats', [DashboardController::class, 'getDashboardStats']);
    });

    // API route for checking duplicate student IDs
    Route::post('/api/check-student-id', [ScholarshipController::class, 'checkStudentId'])->middleware('auth');

    // CSRF token refresh route
    Route::get('/csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });

    // Session keep-alive route
    Route::post('/keep-alive', function () {
        return response()->json(['status' => 'alive', 'csrf_token' => csrf_token()]);
    });



    // Toggle application status route
    Route::post('/admin/toggle-application-status', [DashboardController::class, 'toggleApplicationStatus'])->name('admin.toggle-application-status');

    // Dashboard action routes
    Route::post('/admin/bulk-import', [ImportExportController::class, 'bulkImport'])->name('admin.bulk-import');
    Route::get('/admin/download-template', [ImportExportController::class, 'downloadTemplate'])->name('admin.download-template');
    Route::get('/admin/export/{type}', [ImportExportController::class, 'exportData'])->name('admin.export');


    // Student import routes
    Route::post('/admin/import-students', [ImportExportController::class, 'importStudents'])->name('admin.import-students');
    Route::post('/admin/import-grantees', [ImportExportController::class, 'importGrantees'])->name('admin.import-grantees');
    Route::post('/admin/import-grantees-dynamic', [ImportExportController::class, 'importGranteesDynamic'])->name('admin.import-grantees-dynamic');
    Route::post('/admin/add-grantee', [GranteeController::class, 'addGrantee'])->name('admin.add-grantee');

    // Test route to debug
    Route::post('/admin/test-route', function() {
        return response()->json(['message' => 'Test route works']);
    })->name('admin.test-route');
    Route::get('/admin/download-student-template', [ImportExportController::class, 'downloadStudentTemplate'])->name('admin.download-student-template');
    Route::get('/admin/download-grantee-template', [ImportExportController::class, 'downloadGranteeTemplate'])->name('admin.download-grantee-template');

    // Settings routes
    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::get('/admin/current-semester-year', [SettingsController::class, 'getCurrentSemesterYear'])->name('admin.current-semester-year');
    Route::post('/admin/settings', [SettingsController::class, 'saveSettings'])->name('admin.settings.save');
    Route::post('/admin/settings/update-semester', [SettingsController::class, 'updateSemester'])->name('admin.settings.update-semester');
    Route::post('/admin/settings/update-year', [SettingsController::class, 'updateAcademicYear'])->name('admin.settings.update-year');
});
