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
use App\Http\Controllers\DataController;

Route::get('/welcome', function () {
    return view('layouts.welcome');
})->name('welcome');

Route::get('/splashscreen', function () {
    return view('layouts.splashscreen');
});

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
});

// API routes for data (accessible to authenticated users)
Route::middleware('auth')->group(function () {
    Route::prefix('api/scholarship')->group(function () {
        Route::get('/department-course-mapping', [DataController::class, 'getDepartmentCourseMapping']);
    });
});

// Admin routes with authentication and authorization
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/applications', [ApplicationController::class, 'index'])->name('admin.applications');
    Route::get('/applications/{id}', [ApplicationController::class, 'show'])->name('admin.application.view');


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

    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports');
    // New admin functionality routes

    Route::get('/admin/applications/export', [ImportExportController::class, 'exportApplicationsData'])->name('admin.applications.export');

    // Reports and Archive routes
    Route::post('/admin/reports/generate', [ReportController::class, 'generateReport'])->name('admin.reports.generate');
    Route::post('/admin/reports/preview', [ReportController::class, 'previewReport'])->name('admin.reports.preview');

    // Advanced Reports routes
    Route::get('/admin/reports/executive-summary', [ReportController::class, 'generateExecutiveSummary'])->name('admin.reports.executive-summary');
    Route::get('/admin/reports/department-performance', [ReportController::class, 'generateDepartmentReport'])->name('admin.reports.department-performance');
    Route::get('/admin/reports/enhanced-excel', [ReportController::class, 'generateEnhancedExcel'])->name('admin.reports.enhanced-excel');
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
    Route::post('/admin/applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('admin.application.update-status');
    Route::post('/applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('admin.application.status');

    // Document management routes
    Route::get('/admin/applications/{application}/documents/{document}/download', [DocumentController::class, 'downloadDocument'])->name('admin.application.document.download');
    Route::get('/admin/applications/{application}/documents/{document}/view', [DocumentController::class, 'viewDocument'])->name('admin.application.document.view');



    // API routes for dashboard analytics
    Route::prefix('api/admin')->group(function () {
        Route::get('/chart-data', [DashboardController::class, 'getChartDataApi']);
        Route::get('/analytics-summary', [DashboardController::class, 'getAnalyticsSummary']);
        Route::get('/dashboard-stats', [DashboardController::class, 'getDashboardStats']);
    });



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


    Route::get('/admin/download-template', [ImportExportController::class, 'downloadTemplate'])->name('admin.download-template');
    Route::get('/admin/export/{type}', [ImportExportController::class, 'exportData'])->name('admin.export');


    // Student import routes
    Route::post('/admin/students/import', [ImportExportController::class, 'bulkImportStudents'])->name('admin.students.import');
    Route::post('/admin/import-grantees', [ImportExportController::class, 'importGrantees'])->name('admin.import-grantees');
    Route::post('/admin/import-grantees-dynamic', [ImportExportController::class, 'importGranteesDynamic'])->name('admin.import-grantees-dynamic');
    Route::post('/admin/add-grantee', [GranteeController::class, 'addGrantee'])->name('admin.add-grantee');


    Route::get('/admin/download-student-template', [ImportExportController::class, 'downloadTemplate'])->name('admin.download-student-template');
    Route::get('/admin/download-grantee-template', [ImportExportController::class, 'downloadGranteeTemplate'])->name('admin.download-grantee-template');

    // Settings API routes (used by dashboard)
    Route::get('/admin/current-semester-year', [SettingsController::class, 'getCurrentSemesterYear'])->name('admin.current-semester-year');
    Route::post('/admin/settings', [SettingsController::class, 'saveSettings'])->name('admin.settings.save');
    Route::post('/admin/settings/update-semester', [SettingsController::class, 'updateSemester'])->name('admin.settings.update-semester');
    Route::post('/admin/settings/update-year', [SettingsController::class, 'updateAcademicYear'])->name('admin.settings.update-year');
});
