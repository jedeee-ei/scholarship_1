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


Route::get('/welcome', function () { return view('layouts.welcome');})->name('welcome');
Route::get('/splashscreen', function () { return view('layouts.splashscreen');});
Route::get('/login', function () {return view('layouts.login'); })->name('login');

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
    Route::get('/student/dashboard', function () {
        return view('student.dashboard');
    })->name('student.dashboard');

    Route::get('/student/profile', function () {
        return view('student.profile');
    })->name('student.profile');

    // Add applications route
    Route::get('/student/applications', function () {
        $applications = \App\Models\ScholarshipApplication::where('student_id', Auth::user()->student_id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.applications', compact('applications'));
    })->name('student.applications');

    // Scholarship routes
    Route::post('/scholarship/submit', [ScholarshipController::class, 'submitApplication'])->name('scholarship.submit');
    Route::get('/scholarship/success', [ScholarshipController::class, 'showSuccess'])->name('scholarship.success');

    // Scholarship Application Tracker - remove ownership middleware for now to test
    Route::get('/scholarship/tracker', [ScholarshipTrackerController::class, 'showTracker'])->name('scholarship.tracker');
    Route::post('/scholarship/track', [ScholarshipTrackerController::class, 'trackApplication'])->name('scholarship.track');
});

// Admin routes without authentication
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/applications', [DashboardController::class, 'applications'])->name('admin.applications');
Route::get('/applications/{id}', [DashboardController::class, 'viewApplication'])->name('admin.application.view');
Route::post('/applications/{id}/status', [DashboardController::class, 'updateStatus'])->name('admin.application.status');

// New admin functionality routes
Route::post('/admin/scholarships/add', [DashboardController::class, 'addScholarship'])->name('admin.scholarships.add');
Route::post('/admin/students/import', [DashboardController::class, 'bulkImportStudents'])->name('admin.students.import');
Route::post('/admin/settings/update', [DashboardController::class, 'updateSettings'])->name('admin.settings.update');
Route::get('/admin/applications/export', [DashboardController::class, 'exportApplications'])->name('admin.applications.export');

// Reports and Archive routes
Route::post('/admin/reports/generate', [DashboardController::class, 'generateReport'])->name('admin.reports.generate');
Route::get('/admin/reports/preview', [DashboardController::class, 'previewReport'])->name('admin.reports.preview');
Route::get('/admin/archive/search', [DashboardController::class, 'searchArchive'])->name('admin.archive.search');
Route::get('/admin/archive/download/{fileId}', [DashboardController::class, 'downloadArchive'])->name('admin.archive.download');
Route::delete('/admin/archive/delete/{fileId}', [DashboardController::class, 'deleteArchive'])->name('admin.archive.delete');

// Students management routes
Route::get('/admin/students/data', [DashboardController::class, 'getStudentsData'])->name('admin.students.data');
Route::get('/admin/students/category/{category}', [DashboardController::class, 'getStudentsByCategory'])->name('admin.students.category');
Route::post('/admin/students/add', [DashboardController::class, 'addStudent'])->name('admin.students.add');
Route::get('/admin/students/export', [DashboardController::class, 'exportStudents'])->name('admin.students.export');

// API routes for scholarship data
Route::prefix('api/scholarship')->group(function () {
    Route::get('/departments', [ScholarshipDataController::class, 'getDepartments']);
    Route::get('/departments/{departmentCode}/courses', [ScholarshipDataController::class, 'getCoursesByDepartment']);
    Route::get('/course-durations', [ScholarshipDataController::class, 'getAllCourseDurations']);
    Route::get('/department-course-mapping', [ScholarshipDataController::class, 'getDepartmentCourseMapping']);
    Route::get('/subjects/{courseName}/{yearLevel}/{semester}', [ScholarshipDataController::class, 'getSubjects']);
});

// API route for checking duplicate student IDs
Route::post('/api/check-student-id', [ScholarshipController::class, 'checkStudentId'])->middleware('auth');






















