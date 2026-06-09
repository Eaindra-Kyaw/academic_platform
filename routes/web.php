<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Route;

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

// Dashboard Routes (will be implemented in Day 5)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role_id == 1) {
            return view('admin.dashboard');
        } elseif ($user->role_id == 2) {
            return view('lecturer.dashboard');
        } else {
            return view('student.dashboard');
        }
    })->name('dashboard');
});

// Admin Routes (will be implemented in Day 5)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');

    Route::get('/courses', function () {
        return view('admin.courses');
    })->name('courses');

    Route::get('/departments', function () {
        return view('admin.departments');
    })->name('departments');

    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('reports');
});

// Lecturer Routes (will be implemented in Day 5)
Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {
    Route::get('/dashboard', function () {
        return view('lecturer.dashboard');
    })->name('dashboard');

    Route::get('/attendance', function () {
        return view('lecturer.attendance');
    })->name('attendance');

    Route::get('/students', function () {
        return view('lecturer.students');
    })->name('students');

    Route::get('/schedule', function () {
        return view('lecturer.schedule');
    })->name('schedule');

    Route::get('/reports', function () {
        return view('lecturer.reports');
    })->name('reports');
});

// Student Routes (will be implemented in Day 5)
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', function () {
        return view('student.dashboard');
    })->name('dashboard');

    Route::get('/attendance', function () {
        return view('student.attendance');
    })->name('attendance');

    Route::get('/scan', function () {
        return view('student.scan');
    })->name('scan');

    Route::get('/timetable', function () {
        return view('student.timetable');
    })->name('timetable');

    Route::get('/progress', function () {
        return view('student.progress');
    })->name('progress');
});

// Fallback route for 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

require __DIR__.'/auth.php';
