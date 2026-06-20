<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Lecturer\AttendanceController;
use App\Http\Controllers\Lecturer\EnrollmentController;
use App\Http\Controllers\Student\ScanController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================
// PUBLIC ROUTES
// ============================================

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ============================================
// ADMIN ROUTES
// ============================================

Route::prefix('admin')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Course Management
        Route::resource('courses', AdminController::class)->except(['show']);
        Route::post('/courses/{id}/toggle', [AdminController::class, 'toggleCourse'])->name('courses.toggle');

        // Department Management
        Route::resource('departments', AdminController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

        // Enrollment Management
        Route::get('/enrollments', [AdminController::class, 'enrollments'])->name('enrollments');
        Route::post('/enrollments/{id}/approve', [AdminController::class, 'approveEnrollment'])->name('enrollments.approve');
        Route::post('/enrollments/{id}/reject', [AdminController::class, 'rejectEnrollment'])->name('enrollments.reject');

        // Semester Management
        Route::get('/semesters', [AdminController::class, 'semesters'])->name('semesters');
        Route::post('/semesters', [AdminController::class, 'storeSemester'])->name('semesters.store');
        Route::put('/semesters/{id}', [AdminController::class, 'updateSemester'])->name('semesters.update');
        Route::post('/semesters/{id}/activate', [AdminController::class, 'activateSemester'])->name('semesters.activate');

        // Reports
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [AdminController::class, 'exportReport'])->name('reports.export');

        // Announcements
        Route::resource('announcements', AnnouncementController::class);

        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    });

// ============================================
// LECTURER ROUTES
// ============================================

Route::prefix('lecturer')
    ->middleware(['auth', 'verified', 'lecturer'])
    ->name('lecturer.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');

        // ============================================
        // ATTENDANCE ROUTES
        // ============================================
        Route::prefix('attendance')->name('attendance.')->group(function () {

            // Main pages
            Route::get('/take', [AttendanceController::class, 'takeAttendance'])->name('take');
            Route::get('/sessions', [AttendanceController::class, 'sessions'])->name('sessions');
            Route::get('/history', [AttendanceController::class, 'history'])->name('history');

            // Create & manage sessions
            Route::post('/sessions', [AttendanceController::class, 'createSession'])->name('sessions.create');
            Route::post('/{id}/end', [AttendanceController::class, 'endSession'])->name('session.end');
            Route::post('/{id}/refresh', [AttendanceController::class, 'refreshSession'])->name('session.refresh');
            Route::post('/{courseId}/regenerate-semester', [AttendanceController::class, 'regenerateSemesterQr'])->name('regenerate.semester');

            // Manual attendance
            Route::post('/manual', [AttendanceController::class, 'manualAttendance'])->name('manual');

            // AJAX endpoints
            Route::post('/generate-qr', [AttendanceController::class, 'generateQr'])->name('generate.qr');
            Route::post('/{id}/end-ajax', [AttendanceController::class, 'endSessionAjax'])->name('session.end.ajax');
            Route::post('/{id}/refresh-ajax', [AttendanceController::class, 'refreshQrAjax'])->name('session.refresh.ajax');
            Route::get('/{id}/stats', [AttendanceController::class, 'getSessionStats'])->name('session.stats');
            Route::get('/active-session', [AttendanceController::class, 'getActiveSession'])->name('active.session');
        });

        // ============================================
        // ENROLLMENT ROUTES
        // ============================================
        Route::prefix('enrollments')->name('enrollments.')->group(function () {
            Route::get('/', [EnrollmentController::class, 'index'])->name('index');
            Route::get('/{id}', [EnrollmentController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [EnrollmentController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [EnrollmentController::class, 'reject'])->name('reject');
        });

        // ============================================
        // STUDENT MANAGEMENT
        // ============================================
        Route::get('/students', [LecturerController::class, 'students'])->name('students');
        Route::get('/students/{id}', [LecturerController::class, 'studentDetail'])->name('student.detail');
        Route::get('/students/export', [LecturerController::class, 'exportStudents'])->name('students.export');

        // ============================================
        // TIMETABLE (Updated from Schedule)
        // ============================================
        Route::get('/timetable', [LecturerController::class, 'timetable'])->name('timetable');

        // Keep old route for backward compatibility (optional)
        Route::get('/schedule', [LecturerController::class, 'timetable'])->name('schedule');

        // ============================================
        // ANNOUNCEMENTS
        // ============================================
        Route::get('/announcements', [LecturerController::class, 'announcements'])->name('announcements');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{id}', [LecturerController::class, 'showAnnouncement'])->name('announcements.show');

        // ============================================
        // MESSAGES
        // ============================================
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/inbox', [MessageController::class, 'inbox'])->name('inbox');
            Route::get('/sent', [MessageController::class, 'sent'])->name('sent');
            Route::get('/create', [MessageController::class, 'create'])->name('create');
            Route::post('/send', [MessageController::class, 'send'])->name('send');
            Route::get('/{id}', [MessageController::class, 'show'])->name('show');
            Route::post('/{id}/mark-read', [MessageController::class, 'markRead'])->name('mark.read');
            Route::delete('/{id}', [MessageController::class, 'destroy'])->name('delete');
            Route::get('/unread-count', [MessageController::class, 'unreadCount'])->name('unread.count');
        });

        // ============================================
        // REPORTS
        // ============================================
        Route::get('/reports', [LecturerController::class, 'reports'])->name('reports');
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/reports/attendance', [ReportController::class, 'attendanceReport'])->name('reports.attendance');
        Route::get('/reports/risk', [ReportController::class, 'riskReport'])->name('reports.risk');

        // ============================================
        // INTELLIGENCE (Lecturer Insights)
        // ============================================
        Route::get('/insights', [LecturerController::class, 'insights'])->name('insights');
        Route::get('/insights/refresh', [LecturerController::class, 'refreshInsights'])->name('insights.refresh');

        // ============================================
        // QR GENERATION (Standalone)
        // ============================================
        Route::post('/generate-qr', [LecturerController::class, 'generateQr'])->name('generate.qr');
        Route::post('/session/{id}/end', [LecturerController::class, 'endSession'])->name('session.end.standalone');
        Route::post('/session/{id}/refresh', [LecturerController::class, 'refreshQr'])->name('session.refresh.standalone');
        Route::get('/session/{id}/stats', [LecturerController::class, 'sessionStats'])->name('session.stats.standalone');
    });

// ============================================
// STUDENT ROUTES
// ============================================

Route::prefix('student')
    ->middleware(['auth', 'verified', 'student'])
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

        // QR SCANNING
        Route::get('/scan', [ScanController::class, 'index'])->name('scan');
        Route::post('/scan/process', [ScanController::class, 'process'])->name('scan.process');
        Route::post('/scan/manual', [ScanController::class, 'manual'])->name('scan.manual');

        // ATTENDANCE
        Route::get('/attendance', [StudentController::class, 'attendance'])->name('attendance');
        Route::get('/attendance/course/{id}', [StudentController::class, 'courseAttendance'])->name('attendance.course');
        Route::get('/attendance/export', [StudentController::class, 'exportAttendance'])->name('attendance.export');

        // COURSES
        Route::get('/courses', [StudentController::class, 'courses'])->name('courses');
        Route::get('/courses/{id}', [StudentController::class, 'courseDetail'])->name('courses.detail');

        // ENROLLMENTS
        Route::get('/enrollments', [StudentController::class, 'enrollments'])->name('enrollments');
        Route::post('/enrollments', [StudentController::class, 'enroll'])->name('enrollments.enroll');
        Route::delete('/enrollments/{id}', [StudentController::class, 'dropCourse'])->name('enrollments.drop');

        // ACADEMIC HEALTH
        Route::get('/academic-health', [StudentController::class, 'academicHealth'])->name('academic.health');
        Route::get('/academic-health/history', [StudentController::class, 'healthHistory'])->name('academic.health.history');

        // RISK & RECOMMENDATIONS
        Route::get('/risk', [StudentController::class, 'risk'])->name('risk');
        Route::get('/recommendations', [StudentController::class, 'recommendations'])->name('recommendations');
        Route::post('/recommendations/{id}/dismiss', [StudentController::class, 'dismissRecommendation'])->name('recommendations.dismiss');

        // PEER BENCHMARKING
        Route::get('/benchmarks', [StudentController::class, 'benchmarks'])->name('benchmarks');

        // TIMETABLE (Student)
        Route::get('/timetable', [StudentController::class, 'timetable'])->name('timetable');

        // ANNOUNCEMENTS
        Route::get('/announcements', [StudentController::class, 'announcements'])->name('announcements');
        Route::post('/announcements/{id}/read', [StudentController::class, 'markAnnouncementRead'])->name('announcements.read');

        // MESSAGES
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/inbox', [MessageController::class, 'studentInbox'])->name('inbox');
            Route::get('/{id}', [MessageController::class, 'show'])->name('show');
            Route::post('/{id}/mark-read', [MessageController::class, 'markRead'])->name('mark.read');
        });

        // CHATBOT
        Route::get('/chatbot', [StudentController::class, 'chatbot'])->name('chatbot');
        Route::post('/chatbot/ask', [StudentController::class, 'askChatbot'])->name('chatbot.ask');

        // SETTINGS
        Route::get('/settings', [StudentController::class, 'settings'])->name('settings');
        Route::post('/settings', [StudentController::class, 'updateSettings'])->name('settings.update');
    });

// ============================================
// API ROUTES (Web-based AJAX)
// ============================================

Route::prefix('api')->middleware(['auth'])->group(function () {

    // Attendance
    Route::get('/attendance/session/{id}/stats', [AttendanceController::class, 'getSessionStats']);
    Route::get('/attendance/active', [AttendanceController::class, 'getActiveSession']);

    // Notifications
    Route::get('/notifications', function () {
        return auth()->user()->unreadNotifications;
    });
    Route::post('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    });
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    });

    // Messages
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount']);

    // Announcements
    Route::get('/announcements/unread-count', [AnnouncementController::class, 'unreadCount']);
});

// ============================================
// FALLBACK ROUTE
// ============================================

Route::fallback(function () {
    return redirect()->route('dashboard')->with('error', 'Page not found.');
});
