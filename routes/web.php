<?php

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
use App\Http\Controllers\Admin\AttendanceEvaluationController;
use App\Http\Controllers\Admin\RiskAnalysisController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\CourseAssessmentController;
use App\Http\Controllers\Lecturer\MessageController as LecturerMessageController;
use App\Http\Controllers\Student\MessageController as StudentMessageController;
use App\Http\Controllers\Auth\RoleLoginController;
use App\Http\Controllers\Student\EnrollmentController as StudentEnrollmentController;
use App\Http\Controllers\Lecturer\AttendanceController;
use App\Http\Controllers\Student\QRScanController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// ============================================================
// ✅ EVALUATION CONTROLLER (Legacy - Keep for reference)
// ============================================================
use App\Http\Controllers\Admin\EvaluationController;

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
// MAIN LOGIN SELECTION PAGE (The unified entry point for all users)
// ============================================================
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return view('auth.login');
})->name('login');

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
// REGISTER ROUTE
// ============================================================
Route::get('/register', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return app(RegisteredUserController::class)->create();
})->name('register');

// ============================================================
// ROLE-SPECIFIC LOGIN HANDLERS (POST Routes)
// ============================================================
Route::post('/admin/login', [RoleLoginController::class, 'adminLogin'])->name('admin.login.submit');
Route::post('/lecturer/login', [RoleLoginController::class, 'lecturerLogin'])->name('lecturer.login.submit');
Route::post('/student/login', [RoleLoginController::class, 'studentLogin'])->name('student.login.submit');

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
// PROFILE ROUTES
// ============================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', function () {
        return view('profile.edit');
    })->name('profile.edit');

    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])
        ->name('profile.update');
});

// ============================================================
// TEST ROUTES
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
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ============================================================
    // ADMIN DASHBOARD
    // ============================================================
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // ============================================================
    // USER MANAGEMENT ROUTES
    // ============================================================
    Route::get('/users', [AdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{id}/setup-link', [AdminController::class, 'getSetupLink'])->name('users.setup-link');

    // ============================================================
    // ✅ PENDING USERS APPROVAL ROUTES
    // ============================================================
    Route::get('/users/pending', [AdminController::class, 'pendingUsers'])->name('users.pending');
    Route::get('/users/{id}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::get('/users/{id}/reject', [AdminController::class, 'rejectUser'])->name('users.reject');
    Route::post('/users/{id}/reject', [AdminController::class, 'processRejectUser'])->name('users.process-reject');

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
    Route::get('/records', [AttendanceAnalyticsController::class, 'records'])->name('attendance.records');

    // ============================================================
    // MESSAGE ROUTES
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

    Route::prefix('departments/{department}')->group(function () {
        Route::get('/year/{year}/students', [DepartmentController::class, 'studentsByYear'])->name('departments.year.students');
        Route::get('/year/{year}/students/export', [DepartmentController::class, 'exportStudents'])->name('departments.year.students.export');
        Route::get('/year/{year}/courses', [DepartmentController::class, 'coursesByYear'])->name('departments.year.courses');
        Route::get('/semester/{semester}/courses', [DepartmentController::class, 'semesterCourses'])->name('departments.semester.courses');
        Route::get('/courses/export', [CourseController::class, 'export'])->name('departments.courses.export');

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
    // ENROLLMENT MANAGEMENT ROUTES
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
    // ANNOUNCEMENT ROUTES
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
    // SEMESTER ROUTES
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
    // ATTENDANCE ROUTES
    // ============================================================
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', function() {
            return view('admin.attendance.coming-soon');
        })->name('index');
        Route::get('/analytics', [AttendanceAnalyticsController::class, 'index'])->name('analytics');
        Route::get('/chart-data', [AttendanceAnalyticsController::class, 'chartData'])->name('chart-data');
        Route::post('/evaluate/date-range', [AttendanceEvaluationController::class, 'evaluateByDateRange'])
            ->name('attendance.evaluate.date-range');
        Route::get('/student-data/{student}', [AttendanceAnalyticsController::class, 'studentAttendanceData'])
            ->name('attendance.student-data');
        Route::get('/course-students/{courseId}', [AttendanceAnalyticsController::class, 'courseStudents'])
            ->name('attendance.course-students');
    });

    // ============================================================
    // RISK ANALYSIS ROUTES
    // ============================================================
    Route::prefix('risk')->name('risk.')->group(function () {
        Route::get('/', [RiskAnalysisController::class, 'index'])->name('index');
        Route::get('/export', [RiskAnalysisController::class, 'export'])->name('export');
        Route::get('/student-risk/{student}', [RiskAnalysisController::class, 'studentRiskHistory'])
            ->name('risk.student-risk');
    });

    // ============================================================
    // ATTENDANCE EVALUATION ROUTES (Legacy KG+12)
    // ============================================================
    Route::post('/attendance/evaluate/student-course', [AttendanceEvaluationController::class, 'evaluateStudentCourse'])
        ->name('attendance.evaluate.student-course');
    Route::post('/attendance/evaluate/course/{courseId}', [AttendanceEvaluationController::class, 'evaluateCourse'])
        ->name('attendance.evaluate.course');
    Route::get('/attendance/student/{studentId}/summary', [AttendanceEvaluationController::class, 'studentSummary'])
        ->name('attendance.student.summary');
    Route::get('/attendance/course/{courseId}/summary', [AttendanceEvaluationController::class, 'courseSummary'])
        ->name('attendance.course.summary');
    Route::get('/attendance/risk-distribution/{departmentId?}/{year?}', [AttendanceEvaluationController::class, 'riskDistribution'])
        ->name('attendance.risk.distribution');
    Route::post('/attendance/evaluate/batch', [AttendanceEvaluationController::class, 'batchEvaluate'])
        ->name('attendance.evaluate.batch');

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

    // ============================================================
    // COURSE ASSESSMENT ROUTES (Admin)
    // ============================================================
    Route::prefix('assessments')->name('assessments.')->group(function () {
        // Dashboard & Index
        Route::get('/dashboard', [CourseAssessmentController::class, 'dashboard'])->name('dashboard');
        Route::get('/', [CourseAssessmentController::class, 'index'])->name('index');

        // Create & Store
        Route::get('/create', [CourseAssessmentController::class, 'create'])->name('create');
        Route::post('/', [CourseAssessmentController::class, 'store'])->name('store');

        // Results & Export
        Route::get('/{id}/results', [CourseAssessmentController::class, 'results'])->name('results');
        Route::get('/{id}/export', [CourseAssessmentController::class, 'export'])->name('export');

        // AJAX ROUTES
        Route::get('/courses', [CourseAssessmentController::class, 'fetchCourses'])->name('fetchCourses');
        Route::get('/lecturers', [CourseAssessmentController::class, 'fetchLecturers'])->name('fetchLecturers');
        Route::get('/courses-by-year', [CourseAssessmentController::class, 'fetchCoursesByYearAndSemester'])->name('fetchCoursesByYear');

        // Actions
        Route::put('/{id}/toggle', [CourseAssessmentController::class, 'toggleStatus'])->name('toggle');
        Route::delete('/{id}', [CourseAssessmentController::class, 'destroy'])->name('destroy');
    });

    // ============================================================
    // ✅ LEGACY EVALUATION ROUTES (Keep for backward compatibility)
    // ============================================================
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/', [EvaluationController::class, 'index'])->name('index');
        Route::get('/create', [EvaluationController::class, 'create'])->name('create');
        Route::post('/', [EvaluationController::class, 'store'])->name('store');
        Route::get('/{id}', [EvaluationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EvaluationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EvaluationController::class, 'update'])->name('update');
        Route::delete('/{id}', [EvaluationController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle', [EvaluationController::class, 'toggleStatus'])->name('toggle');
        Route::post('/{id}/generate-results', [EvaluationController::class, 'generateResults'])->name('generate-results');
        Route::post('/{id}/send-results', [EvaluationController::class, 'sendResults'])->name('send-results');
        Route::post('/{id}/send-to-students', [EvaluationController::class, 'sendToStudents'])->name('send-to-students');
        Route::get('/{id}/student-count', [EvaluationController::class, 'getStudentCount'])->name('student-count');
        Route::get('/{id}/status', [EvaluationController::class, 'status'])->name('status');
    });

}); // 🔴 CLOSING BRACE FOR ADMIN ROUTES


// ============================================================
// LECTURER ROUTES
// ============================================================
Route::middleware(['auth', 'lecturer'])->prefix('lecturer')->name('lecturer.')->group(function () {

    Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');

    // ============================================================
    // TIMETABLE ROUTES
    // ============================================================
    Route::prefix('timetable')->name('timetable.')->group(function () {
        Route::get('/', [LecturerController::class, 'timetable'])->name('index');
        Route::get('/manage', [LecturerController::class, 'manageTimetable'])->name('manage');
        Route::post('/add', [LecturerController::class, 'addToTimetable'])->name('add');
        Route::match(['post', 'delete'], '/{id}/remove', [LecturerController::class, 'removeFromTimetable'])->name('remove');
        Route::post('/add-multiple', [LecturerController::class, 'addMultipleSessions'])->name('add.multiple');
        Route::get('/export', [LecturerController::class, 'exportTimetable'])->name('export');
        Route::get('/export-pdf', [LecturerController::class, 'exportTimetablePdf'])->name('export.pdf');
    });

    // ============================================================
    // ANNOUNCEMENT ROUTES
    // ============================================================
    Route::get('/announcements', [LecturerController::class, 'announcements'])->name('announcements');
    Route::get('/announcements/{id}', [LecturerController::class, 'showAnnouncement'])->name('announcements.show');

    // ============================================================
    // SEMESTER QR MANAGEMENT ROUTES
    // ============================================================
    Route::prefix('semester-qr')->name('semester-qr.')->group(function () {
        Route::get('/management', [AttendanceController::class, 'semesterQrManagement'])->name('management');
        Route::post('/{id}/end', [AttendanceController::class, 'endSemesterQr'])->name('end');
    });

    // ============================================================
    // STUDENT MANAGEMENT
    // ============================================================
    Route::get('/students', [LecturerController::class, 'monitoring'])->name('students');
    Route::get('/schedule', [LecturerController::class, 'schedule'])->name('schedule');
    Route::get('/reports', [LecturerController::class, 'reports'])->name('reports');

    // ============================================================
    // ENROLLMENT ROUTES
    // ============================================================
    Route::get('/enrollments', [App\Http\Controllers\Lecturer\EnrollmentController::class, 'index'])->name('enrollments.index');

    // ============================================================
    // ATTENDANCE SESSION ROUTES
    // ============================================================
    Route::get('/attendance/take', [AttendanceController::class, 'takeAttendance'])->name('attendance.take');
    Route::get('/attendance/sessions', [AttendanceController::class, 'sessions'])->name('attendance.sessions');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    Route::get('/attendance/records', [AttendanceController::class, 'allRecords'])->name('attendance.records');

    Route::post('/attendance/sessions', [AttendanceController::class, 'createSession'])->name('attendance.sessions.create');
    Route::post('/attendance/sessions/{id}/end', [AttendanceController::class, 'endSession'])->name('attendance.sessions.end');
    Route::get('/attendance/sessions/{id}/refresh', [AttendanceController::class, 'refreshSession'])->name('attendance.sessions.refresh');
    Route::post('/attendance/manual', [AttendanceController::class, 'manualAttendance'])->name('attendance.manual');

    // ============================================================
    // AJAX ROUTES
    // ============================================================
    Route::post('/generate-qr', [AttendanceController::class, 'generateQr'])->name('generateQr');
    Route::post('/end-session/{id}', [AttendanceController::class, 'endSessionAjax'])->name('endSession');
    Route::post('/refresh-qr/{id}', [AttendanceController::class, 'refreshQrAjax'])->name('refreshQr');
    Route::get('/session-stats/{id}', [AttendanceController::class, 'getSessionStats'])->name('sessionStats');
    Route::post('/attendance/generate-qr', [AttendanceController::class, 'generateQr'])->name('attendance.generate.qr');

    // ============================================================
    // SEMESTER QR ROUTES
    // ============================================================
    Route::post('/course/{course}/regenerate-semester-qr', [AttendanceController::class, 'regenerateSemesterQr'])->name('course.regenerate-semester-qr');
    Route::post('/generate-semester-qr-direct', [AttendanceController::class, 'generateSemesterQrDirect'])->name('lecturer.generate.semester.qr.direct');

    // ============================================================
    // MESSAGE ROUTES
    // ============================================================
    Route::get('/messages', [LecturerMessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/sent', [LecturerMessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/compose', [LecturerMessageController::class, 'compose'])->name('messages.compose');
    Route::post('/messages/send', [LecturerMessageController::class, 'send'])->name('messages.send');
    Route::get('/messages/{message}', [LecturerMessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/unread/count', [LecturerMessageController::class, 'unreadCount'])->name('messages.unread');

    // ============================================================
    // REPORTS
    // ============================================================
    Route::get('/reports', [LecturerController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [LecturerController::class, 'exportReport'])->name('reports.export');
    Route::get('/reports/at-risk', [LecturerController::class, 'exportAtRiskReport'])->name('reports.at-risk');

    // ============================================================
    // ANNOUNCEMENT UNREAD COUNT ROUTE
    // ============================================================
    Route::get('/announcements/unread-count', [AnnouncementController::class, 'unreadCount'])->name('announcements.unread');

    // ============================================================
    // PERIOD-BASED COURSE ATTENDANCE
    // ============================================================
    Route::get('/course/{courseId}/attendance-period', [LecturerController::class, 'courseAttendancePeriod'])
        ->name('course.attendance.period');

}); // 🔴 CLOSING BRACE FOR LECTURER ROUTES


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
    Route::get('/attendance/history', [StudentController::class, 'attendanceHistory'])->name('attendance.history');
    Route::get('/attendance/period', [StudentController::class, 'attendancePeriod'])->name('attendance.period');

    // ============================================================
    // ENROLLMENT ROUTES
    // ============================================================
    Route::get('/courses/available', [StudentEnrollmentController::class, 'availableCourses'])->name('courses.available');
    Route::post('/courses/{course}/enroll', [StudentEnrollmentController::class, 'requestEnrollment'])->name('courses.enroll');
    Route::get('/my-enrollments', [StudentEnrollmentController::class, 'myEnrollments'])->name('my.enrollments');

    // ============================================================
    // QR ATTENDANCE ROUTES
    // ============================================================
    Route::get('/scan', [QRScanController::class, 'index'])->name('scan');
    Route::get('/scan/check-session', [QRScanController::class, 'checkSession'])->name('scan.check-session');
    Route::get('/scan/process', [QRScanController::class, 'processScan'])->name('scan.process');
    Route::post('/scan/manual', [QRScanController::class, 'manualAttendance'])->name('scan.manual');
    Route::get('/scan/static', [QRScanController::class, 'staticScan'])->name('scan.static');
    Route::get('/scan/semester', [QRScanController::class, 'semesterScan'])->name('scan.semester');

    // ============================================================
    // ANNOUNCEMENT ROUTES
    // ============================================================
    Route::prefix('announcements')->name('announcements.')->group(function () {
        Route::get('/', [StudentController::class, 'announcements'])->name('index');
        Route::get('/{id}', [StudentController::class, 'showAnnouncement'])->name('show');
    });

    Route::get('/announcements/unread-count', [AnnouncementController::class, 'unreadCount'])->name('announcements.unread');

    // ============================================================
    // NOTIFICATIONS ROUTE
    // ============================================================
    Route::get('/notifications', function() {
        return view('student.notifications');
    })->name('notifications');

    // ============================================================
    // MESSAGE ROUTES
    // ============================================================
    Route::get('/messages', [StudentMessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/{message}', [StudentMessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/unread/count', [StudentMessageController::class, 'unreadCount'])->name('messages.unread');

    // ============================================================
    // CHATBOT ROUTES
    // ============================================================
    Route::get('/chatbot', [StudentController::class, 'chatbot'])->name('chatbot');
    Route::post('/chatbot/ask', [StudentController::class, 'askChatbot'])->name('chatbot.ask');

    // ============================================================
    // COURSE ASSESSMENT ROUTES (Student)
    // ============================================================
    Route::prefix('assessments')->name('assessments.')->group(function () {
        Route::get('/', [CourseAssessmentController::class, 'studentIndex'])->name('index');
        Route::get('/{id}', [CourseAssessmentController::class, 'studentShow'])->name('show');
        Route::post('/submit', [CourseAssessmentController::class, 'studentSubmit'])->name('submit');
        Route::get('/get-lecturers', [CourseAssessmentController::class, 'getLecturersByCourse'])->name('get-lecturers');
    });

    // ============================================================
    // ✅ LEGACY EVALUATION ROUTES (Student - Keep for compatibility)
    // ============================================================
    Route::get('/evaluations', [EvaluationController::class, 'studentIndex'])->name('evaluations.index');
    Route::get('/evaluations/{id}', [EvaluationController::class, 'studentShow'])->name('evaluations.show');
    Route::post('/evaluations/submit', [EvaluationController::class, 'submit'])->name('evaluations.submit');

}); // 🔴 CLOSING BRACE FOR STUDENT ROUTES


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
