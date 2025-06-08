<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Student\ScholarshipController;
use App\Http\Controllers\Student\ScholarshipTrackerController;
use App\Http\Controllers\Api\ScholarshipDataController;
use Illuminate\Support\Facades\Auth;

// Route::get('/', function () {
//     return view('welcome');
// });


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





// Student routes
Route::middleware(['auth', 'student'])->group(function () {
    // Student dashboard route
    Route::get('/student/dashboard', [\App\Http\Controllers\Student\DashboardController::class, 'index'])->name('student.dashboard');

    Route::get('/student/profile', function () {
        return view('student.profile');
    })->name('student.profile');



    // Scholarship routes
    Route::post('/scholarship/submit', [ScholarshipController::class, 'submitApplication'])->name('scholarship.submit');

    Route::get('/scholarship/success', [ScholarshipController::class, 'showSuccess'])->name('scholarship.success');
    Route::post('/student/check-duplicate', [ScholarshipController::class, 'checkDuplicate'])->name('student.check-duplicate');

    // Scholarship Application Tracker - remove ownership middleware for now to test
    Route::get('/scholarship/tracker', [ScholarshipTrackerController::class, 'showTracker'])->name('scholarship.tracker');
    Route::post('/scholarship/track', [ScholarshipTrackerController::class, 'trackApplication'])->name('scholarship.track');
});


// Admin routes without authentication (for testing)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/applications', [DashboardController::class, 'applications'])->name('admin.applications');
Route::get('/applications/{id}', [DashboardController::class, 'viewApplication'])->name('admin.application.view');
Route::post('/applications/{id}/status', [DashboardController::class, 'updateStatus'])->name('admin.application.status');

// New admin page routes
Route::get('/admin/students', [DashboardController::class, 'students'])->name('admin.students');
Route::post('/students/{id}/update', [DashboardController::class, 'updateStudent'])->name('admin.student.update');
Route::get('/admin/scholarships', [DashboardController::class, 'scholarships'])->name('admin.scholarships');
Route::post('/admin/scholarships/add', [DashboardController::class, 'addScholarship'])->name('admin.scholarship.add');
Route::post('/admin/scholarships/update-semester', [DashboardController::class, 'updateSemester'])->name('admin.scholarship.update-semester');
Route::post('/admin/scholarships/update-year', [DashboardController::class, 'updateAcademicYear'])->name('admin.scholarship.update-year');
Route::get('/admin/announcements', [DashboardController::class, 'announcements'])->name('admin.announcements');
Route::post('/admin/announcements/store', [DashboardController::class, 'storeAnnouncement'])->name('admin.announcements.store');
Route::put('/admin/announcements/{id}', [DashboardController::class, 'updateAnnouncement'])->name('admin.announcements.update');
Route::delete('/admin/announcements/{id}', [DashboardController::class, 'deleteAnnouncement'])->name('admin.announcements.delete');

Route::get('/admin/archived-students', [DashboardController::class, 'archivedStudents'])->name('admin.archived-students');
Route::get('/admin/archived-scholarships', [DashboardController::class, 'archivedScholarships'])->name('admin.archived-scholarships');
Route::get('/admin/reports', [DashboardController::class, 'reports'])->name('admin.reports');
Route::get('/admin/settings', [DashboardController::class, 'settings'])->name('admin.settings');

// New admin functionality routes
Route::post('/admin/students/import', [DashboardController::class, 'bulkImportStudents'])->name('admin.students.import');
Route::post('/admin/settings/update', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
Route::get('/admin/applications/export', [DashboardController::class, 'exportApplications'])->name('admin.applications.export');

// Reports and Archive routes
Route::post('/admin/reports/generate', [DashboardController::class, 'generateReport'])->name('admin.reports.generate');
Route::post('/admin/reports/preview', [DashboardController::class, 'previewReport'])->name('admin.reports.preview');
Route::get('/admin/archive/search', [DashboardController::class, 'searchArchive'])->name('admin.archive.search');
Route::get('/admin/archive/download/{fileId}', [DashboardController::class, 'downloadArchive'])->name('admin.archive.download');
Route::delete('/admin/archive/delete/{fileId}', [DashboardController::class, 'deleteArchive'])->name('admin.archive.delete');

// Debug route for testing data
Route::get('/admin/reports/test-data', function() {
    $totalApps = \App\Models\ScholarshipApplication::count();
    $allApps = \App\Models\ScholarshipApplication::all();

    return response()->json([
        'total_applications' => $totalApps,
        'sample_data' => $allApps->take(3),
        'scholarship_types' => $allApps->pluck('scholarship_type')->unique(),
        'statuses' => $allApps->pluck('status')->unique(),
        'created_dates' => $allApps->pluck('created_at')->map(function($date) {
            return $date->format('Y-m-d H:i:s');
        })
    ]);
})->name('admin.reports.test-data');

// Students management routes
Route::get('/admin/students/data', [DashboardController::class, 'getStudentsData'])->name('admin.students.data');
Route::get('/admin/students/category/{category}', [DashboardController::class, 'getStudentsByCategory'])->name('admin.students.category');
Route::post('/admin/students/add', [DashboardController::class, 'addStudent'])->name('admin.students.add');
Route::get('/admin/students/export', [DashboardController::class, 'exportStudents'])->name('admin.students.export');

// Applications management routes
Route::get('/admin/applications/data', [DashboardController::class, 'getApplicationsData'])->name('admin.applications.data');
Route::get('/admin/applications/{id}/detail', [DashboardController::class, 'getApplicationDetail'])->name('admin.application.detail');
Route::post('/admin/applications/{id}/status', [DashboardController::class, 'updateApplicationStatus'])->name('admin.application.update-status');

// Document management routes
Route::get('/admin/applications/{application}/documents/{document}/download', [DashboardController::class, 'downloadDocument'])->name('admin.application.document.download');
Route::get('/admin/applications/{application}/documents/{document}/view', [DashboardController::class, 'viewDocument'])->name('admin.application.document.view');

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
});

// API route for checking duplicate student IDs
Route::post('/api/check-student-id', [ScholarshipController::class, 'checkStudentId'])->middleware('auth');

// API route for loading subjects (used by student dashboard)
Route::get('/api/subjects', [ScholarshipDataController::class, 'getSubjectsForDashboard']);

// CSRF token refresh route
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Session keep-alive route
Route::post('/keep-alive', function () {
    return response()->json(['status' => 'alive', 'csrf_token' => csrf_token()]);
});

// Get current semester/year route
Route::get('/admin/current-semester-year', [DashboardController::class, 'getCurrentSemesterYear']);

// Dashboard action routes
Route::post('/admin/bulk-import', [DashboardController::class, 'bulkImport'])->name('admin.bulk-import');
Route::get('/admin/download-template', [DashboardController::class, 'downloadTemplate'])->name('admin.download-template');
Route::get('/admin/export/{type}', [DashboardController::class, 'exportData'])->name('admin.export');
Route::post('/admin/settings', [DashboardController::class, 'saveSettings'])->name('admin.settings');

// Student import routes
Route::post('/admin/import-students', [DashboardController::class, 'importStudents'])->name('admin.import-students');
Route::get('/admin/download-student-template', [DashboardController::class, 'downloadStudentTemplate'])->name('admin.download-student-template');
