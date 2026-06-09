<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;

// Test routes (remove after testing)
Route::get('/test-relations', function () {
    $results = [];

    // Test Role-User
    $role = Role::first();
    $results['role_users'] = $role ? $role->users()->count() : 0;

    // Test Department-User
    $dept = Department::first();
    $results['department_users'] = $dept ? $dept->users()->count() : 0;

    // Test User-Role
    $user = User::first();
    $results['user_role'] = $user ? ($user->role->name ?? 'null') : 'no user';

    // Test Course-Department
    $course = Course::first();
    $results['course_department'] = $course ? ($course->department->name ?? 'null') : 'no course';

    // Test Enrollment
    $enrollment = Enrollment::first();
    if ($enrollment) {
        $results['enrollment_student'] = $enrollment->student->name ?? 'null';
        $results['enrollment_course'] = $enrollment->course->name ?? 'null';
    } else {
        $results['enrollment'] = 'no enrollment data';
    }

    return $results;
});

// Dashboard Routes - Role-based redirect
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role_id == 1) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role_id == 2) {
            return redirect()->route('lecturer.dashboard');
        } else {
            return redirect()->route('student.dashboard');
        }
    })->name('dashboard');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
});

// Lecturer Routes
Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [LecturerController::class, 'attendance'])->name('attendance');
    Route::get('/students', [LecturerController::class, 'students'])->name('students');
    Route::get('/schedule', [LecturerController::class, 'schedule'])->name('schedule');
    Route::get('/reports', [LecturerController::class, 'reports'])->name('reports');
});

// Student Routes
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [StudentController::class, 'attendance'])->name('attendance');
    Route::get('/scan', [StudentController::class, 'scan'])->name('scan');
    Route::get('/timetable', [StudentController::class, 'timetable'])->name('timetable');
    Route::get('/progress', [StudentController::class, 'progress'])->name('progress');
});

// Fallback route for 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

require __DIR__.'/auth.php';
