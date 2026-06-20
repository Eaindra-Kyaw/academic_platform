<?php
// routes/web.php

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\LecturerController as AdminLecturerController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\AttendanceAnalyticsController;
use App\Http\Controllers\Lecturer\MessageController as LecturerMessageController;
use App\Http\Controllers\Student\MessageController as StudentMessageController;
use App\Http\Controllers\Auth\RoleLoginController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Student\EnrollmentController as StudentEnrollmentController;
use App\Http\Controllers\Lecturer\AttendanceController;
use App\Http\Controllers\Student\QRScanController;

// ============================================================
// LANDING PAGE (Home)
// ============================================================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return view('index');
})->name('home');

// ============================================================
// SEPARATE LOGIN PAGES FOR EACH ROLE (GET Routes)
// ============================================================
Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');

Route::get('/lecturer/login', function () {
    return view('auth.lecturer-login');
})->name('lecturer.login');

Route::get('/student/login', function () {
    return view('auth.student-login');
})->name('student.login');

// ============================================================
// ROLE-SPECIFIC LOGIN HANDLERS (POST Routes)
// ============================================================
Route::post('/admin/login', [RoleLoginController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/lecturer/login', [RoleLoginController::class, 'lecturerLogin'])->name('lecturer.login.submit');
Route::post('/student/login', [RoleLoginController::class, 'studentLogin'])->name('student.login.submit');

// ============================================================
// TEST ROUTES (remove after testing)
// ============================================================
Route::get('/test-relations', function () {
    $results = [];

    $role = Role::first();
    $results['role_users'] = $role ? $role->users()->count() : 0;

    $dept = Department::first();
    $results['department_users'] = $dept ? $dept->users()->count() : 0;

    $user = User::first();
    $results['user_role'] = $user ? ($user->role->name ?? 'null') : 'no user';

    $course = Course::first();
    $results['course_department'] = $course ? ($course->department->name ?? 'null') : 'no course';

    $enrollment = Enrollment::first();
    if ($enrollment) {
        $results['enrollment_student'] = $enrollment->student->name ?? 'null';
        $results['enrollment_course'] = $enrollment->course->name ?? 'null';
    } else {
        $results['enrollment'] = 'no enrollment data';
    }

    return $results;
});

// ============================================================
// DASHBOARD ROUTES
// ============================================================
Route::middleware(['auth', 'must.change.password'])->group(function () {
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

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ============================================================
    // ADMIN DASHBOARD
    // ============================================================
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // ============================================================
    // USER MANAGEMENT ROUTES (Admin)
    // ============================================================
    Route::get('/users', [AdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');

    // ============================================================
    // REPORTS
    // ============================================================
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/detail/{type}', [AdminController::class, 'reportDetail'])->name('reports.detail');
    Route::get('/reports/export/{type}', [AdminController::class, 'exportReport'])->name('reports.export');

    // ============================================================
    // STUDENT MANAGEMENT
    // ============================================================
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

    // ============================================================
    // MESSAGE ROUTES (Admin)
    // ============================================================
    Route::get('/messages', [AdminMessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/sent', [AdminMessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/compose', [AdminMessageController::class, 'compose'])->name('messages.compose');
    Route::post('/messages/send', [AdminMessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/{message}', [AdminMessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/unread/count', [AdminMessageController::class, 'unreadCount'])->name('messages.unread');
    Route::put('/messages/{message}/read', [AdminMessageController::class, 'markAsRead'])->name('messages.read');

    // ============================================================
    // DEPARTMENT ROUTES
    // ============================================================
    Route::resource('departments', DepartmentController::class);

    // Nested routes under departments
    Route::prefix('departments/{department}')->group(function () {
        Route::get('/year/{year}/students', [DepartmentController::class, 'studentsByYear'])->name('departments.year.students');
        Route::get('/year/{year}/students/export', [DepartmentController::class, 'exportStudents'])->name('departments.year.students.export');
        Route::get('/year/{year}/courses', [DepartmentController::class, 'coursesByYear'])->name('departments.year.courses');

        // Semester courses route
        Route::get('/semester/{semester}/courses', [DepartmentController::class, 'semesterCourses'])->name('departments.semester.courses');

        Route::resource('/courses', CourseController::class)->names([
            'index' => 'departments.courses.index',
            'create' => 'departments.courses.create',
            'store' => 'departments.courses.store',
            'show' => 'departments.courses.show',
            'edit' => 'departments.courses.edit',
            'update' => 'departments.courses.update',
            'destroy' => 'departments.courses.destroy',
        ]);
    });

    // ============================================================
    // ENROLLMENT MANAGEMENT ROUTES (Admin)
    // ============================================================
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::get('/enrollments/department/{departmentId}', [EnrollmentController::class, 'showDepartment'])->name('enrollments.department');
    Route::get('/enrollments/department/{departmentId}/year/{year}', [EnrollmentController::class, 'showDepartmentYear'])->name('enrollments.department.year');
    Route::get('/enrollments/course/{courseId}', [EnrollmentController::class, 'showCourse'])->name('enrollments.course');
    Route::get('/enrollments/{id}/approve', [EnrollmentController::class, 'approve'])->name('enrollments.approve');
    Route::post('/enrollments/{id}/reject', [EnrollmentController::class, 'reject'])->name('enrollments.reject');
    Route::get('/enrollments/student/{id}', [EnrollmentController::class, 'showStudent'])->name('enrollments.student');
    Route::post('/enrollments/bulk/approve', [EnrollmentController::class, 'bulkApprove'])->name('enrollments.bulk.approve');
    Route::post('/enrollments/bulk/reject', [EnrollmentController::class, 'bulkReject'])->name('enrollments.bulk.reject');

    // ============================================================
    // ANNOUNCEMENT ROUTES (Admin)
    // ============================================================
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index'])->name('index');
        Route::get('/create', [AnnouncementController::class, 'create'])->name('create');
        Route::post('/', [AnnouncementController::class, 'store'])->name('store');
        Route::get('/{id}', [AnnouncementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AnnouncementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AnnouncementController::class, 'update'])->name('update');
        Route::delete('/{id}', [AnnouncementController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/toggle', [AnnouncementController::class, 'toggleStatus'])->name('toggle');
        Route::get('/unread-count', [AnnouncementController::class, 'unreadCount'])->name('unread');
        Route::post('/{id}/mark-read', [AnnouncementController::class, 'markAsRead'])->name('mark-read');
        Route::get('/reset-unread', [AnnouncementController::class, 'resetUnread'])->name('reset-unread');
        Route::get('/force-reset-all', [AnnouncementController::class, 'forceResetAll'])->name('force-reset-all');
        Route::get('/check-unread', [AnnouncementController::class, 'checkUnread'])->name('check-unread');
    });

    // ============================================================
    // SEMESTER ROUTES (Admin)
    // ============================================================
    Route::prefix('semesters')->name('semesters.')->group(function () {
        Route::get('/', [SemesterController::class, 'index'])->name('index');
        Route::get('/create', [SemesterController::class, 'create'])->name('create');
        Route::post('/', [SemesterController::class, 'store'])->name('store');
        Route::get('/{id}', [SemesterController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SemesterController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SemesterController::class, 'update'])->name('update');
        Route::delete('/{id}', [SemesterController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/toggle', [SemesterController::class, 'toggleStatus'])->name('toggle');
        Route::get('/{id}/set-current', [SemesterController::class, 'setCurrent'])->name('set-current');
        Route::get('/generate', [SemesterController::class, 'generate'])->name('generate');
    });

    // ============================================================
    // ATTENDANCE ROUTES (Admin) - ADD THIS SECTION
    // ============================================================
    Route::prefix('attendance')->name('attendance.')->group(function () {
        // Temporary coming soon page
        Route::get('/', function() {
            return view('admin.attendance.coming-soon');
        })->name('index');

        // Full analytics (for later)
        Route::get('/analytics', [AttendanceAnalyticsController::class, 'index'])->name('analytics');
        Route::get('/chart-data', [AttendanceAnalyticsController::class, 'chartData'])->name('chart-data');
    });

    // ============================================================
    // LECTURER MANAGEMENT ROUTES
    // ============================================================
    Route::get('/lecturers', [AdminLecturerController::class, 'index'])->name('lecturers.index');
    Route::get('/lecturers/create', [AdminLecturerController::class, 'create'])->name('lecturers.create');
    Route::post('/lecturers', [AdminLecturerController::class, 'store'])->name('lecturers.store');
    Route::get('/lecturers/{lecturer}', [AdminLecturerController::class, 'show'])->name('lecturers.show');
    Route::get('/lecturers/{lecturer}/edit', [AdminLecturerController::class, 'edit'])->name('lecturers.edit');
    Route::put('/lecturers/{lecturer}', [AdminLecturerController::class, 'update'])->name('lecturers.update');
    Route::delete('/lecturers/{lecturer}', [AdminLecturerController::class, 'destroy'])->name('lecturers.destroy');
});

// ============================================================
// LECTURER ROUTES
// ============================================================
Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {

    // ============================================================
    // LECTURER DASHBOARD
    // ============================================================
    Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');

    // ============================================================
    // STUDENT MANAGEMENT
    // ============================================================
    Route::get('/students', [LecturerController::class, 'students'])->name('students');
    Route::get('/schedule', [LecturerController::class, 'schedule'])->name('schedule');
    Route::get('/reports', [LecturerController::class, 'reports'])->name('reports');

    // ============================================================
    // ANNOUNCEMENT ROUTES (Lecturer)
    // ============================================================
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [LecturerController::class, 'announcements'])->name('index');
        Route::get('/{id}', [LecturerController::class, 'showAnnouncement'])->name('show');
    });

    // ============================================================
    // ENROLLMENT ROUTES (Lecturer)
    // ============================================================
    Route::get('/enrollments', [App\Http\Controllers\Lecturer\EnrollmentController::class, 'index'])->name('enrollments.index');

    // ============================================================
    // ATTENDANCE SESSION ROUTES
    // ============================================================
    Route::get('/attendance/take', [AttendanceController::class, 'takeAttendance'])->name('attendance.take');
    Route::get('/attendance/sessions', [AttendanceController::class, 'sessions'])->name('attendance.sessions');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');

    Route::post('/attendance/sessions', [AttendanceController::class, 'createSession'])->name('attendance.sessions.create');
    Route::post('/attendance/sessions/{id}/end', [AttendanceController::class, 'endSession'])->name('attendance.sessions.end');
    Route::get('/attendance/sessions/{id}/refresh', [AttendanceController::class, 'refreshSession'])->name('attendance.sessions.refresh');

    Route::post('/attendance/manual', [AttendanceController::class, 'manualAttendance'])->name('attendance.manual');

    // ============================================================
    // AJAX ROUTES (Lecturer)
    // ============================================================
    Route::post('/generate-qr', [AttendanceController::class, 'generateQr'])->name('generateQr');
    Route::post('/end-session/{id}', [AttendanceController::class, 'endSessionAjax'])->name('endSession');
    Route::post('/refresh-qr/{id}', [AttendanceController::class, 'refreshQrAjax'])->name('refreshQr');
    Route::get('/session-stats/{id}', [AttendanceController::class, 'getSessionStats'])->name('sessionStats');

    // ============================================================
    // SEMESTER QR ROUTES
    // ============================================================
    Route::post('/course/{course}/regenerate-semester-qr', [AttendanceController::class, 'regenerateSemesterQr'])->name('course.regenerate-semester-qr');
    Route::post('/generate-semester-qr-direct', [AttendanceController::class, 'generateSemesterQrDirect'])->name('lecturer.generate.semester.qr.direct');

    // ============================================================
    // MESSAGE ROUTES (Lecturer)
    // ============================================================
    Route::get('/messages', [LecturerMessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/sent', [LecturerMessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/compose', [LecturerMessageController::class, 'compose'])->name('messages.compose');
    Route::post('/messages/send', [LecturerMessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/{message}', [LecturerMessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/unread/count', [LecturerMessageController::class, 'unreadCount'])->name('messages.unread');

    // ============================================================
    // ANNOUNCEMENT UNREAD COUNT ROUTE (Lecturer)
    // ============================================================
    Route::get('/announcements/unread-count', [AnnouncementController::class, 'unreadCount'])->name('announcements.unread');
});

// ============================================================
// STUDENT ROUTES
// ============================================================
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {

    // ============================================================
    // STUDENT DASHBOARD
    // ============================================================
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/attendance', [StudentController::class, 'attendance'])->name('attendance');
    Route::get('/timetable', [StudentController::class, 'timetable'])->name('timetable');
    Route::get('/progress', [StudentController::class, 'progress'])->name('progress');

    // ============================================================
    // ENROLLMENT ROUTES (Student)
    // ============================================================
    Route::get('/courses/available', [StudentEnrollmentController::class, 'availableCourses'])->name('courses.available');
    Route::post('/courses/{course}/enroll', [StudentEnrollmentController::class, 'requestEnrollment'])->name('courses.enroll');
    Route::get('/my-enrollments', [StudentEnrollmentController::class, 'myEnrollments'])->name('my.enrollments');

    // ============================================================
    // QR ATTENDANCE ROUTES (Student)
    // ============================================================
    Route::get('/scan', [QRScanController::class, 'index'])->name('scan');
    Route::get('/scan/check-session', [QRScanController::class, 'checkSession'])->name('scan.check-session');
    Route::get('/scan/process', [QRScanController::class, 'processScan'])->name('scan.process');
    Route::post('/scan/manual', [QRScanController::class, 'manualAttendance'])->name('scan.manual');

    // ============================================================
    // STATIC QR SCAN ROUTE (Student scans Static QR from PowerPoint)
    // ============================================================
    Route::get('/scan/static', [QRScanController::class, 'staticScan'])->name('scan.static');
    Route::get('/scan/semester', [QRScanController::class, 'semesterScan'])->name('scan.semester');

    // ============================================================
    // ANNOUNCEMENT ROUTES (Student)
    // ============================================================
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [StudentController::class, 'announcements'])->name('index');
        Route::get('/{id}', [StudentController::class, 'showAnnouncement'])->name('show');
    });

    // ============================================================
    // ANNOUNCEMENT UNREAD COUNT ROUTE (Student)
    // ============================================================
    Route::get('/announcements/unread-count', [AnnouncementController::class, 'unreadCount'])->name('announcements.unread');

    // ============================================================
    // NOTIFICATIONS ROUTE (Student) - Temporary placeholder
    // ============================================================
    Route::get('/notifications', function() {
        return view('student.notifications');
    })->name('notifications');

    // ============================================================
    // MESSAGE ROUTES (Student)
    // ============================================================
    Route::get('/messages', [StudentMessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/{message}', [StudentMessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/unread/count', [StudentMessageController::class, 'unreadCount'])->name('messages.unread');
});

// ============================================================
// DEFAULT LOGIN REDIRECT
// ============================================================
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect()->route('home');
})->name('login');

Route::post('/login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

// ============================================================
// LOGOUT ROUTE
// ============================================================
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('home');
})->name('logout');

// ============================================================
// FALLBACK ROUTE for 404
// ============================================================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});

// ============================================================
// PASSWORD SETUP ROUTES
// ============================================================
Route::get('/password/setup/{token}', [App\Http\Controllers\Auth\PasswordSetupController::class, 'showSetupForm'])->name('password.setup.form');
Route::post('/password/setup', [App\Http\Controllers\Auth\PasswordSetupController::class, 'setupPassword'])->name('password.setup');

// ============================================================
// AUTH ROUTES (Laravel Breeze)
// ============================================================
require __DIR__.'/auth.php';
