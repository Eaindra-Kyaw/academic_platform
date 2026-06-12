<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LecturerController extends Controller
{
    public function dashboard()
    {
        $lecturer = Auth::user();

        // Get lecturer's courses
        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->get();

        $courseIds = $courses->pluck('id');

        // Get all enrolled students in lecturer's courses
        $students = User::whereHas('enrollments', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds)->where('status', 'approved');
        })->get();

        // Calculate statistics
        $totalStudents = $students->count();

        // At-risk students (attendance below 60%)
        $atRiskStudents = 0;
        $atRiskList = [];
        foreach ($students as $student) {
            $attendance = $this->calculateStudentAttendance($student->id, $courseIds);
            if ($attendance < 60) {
                $atRiskStudents++;
                $atRiskList[] = (object)[
                    'student' => $student,
                    'course' => $courses->first(),
                    'attendance_percentage' => $attendance,
                    'risk_level' => $attendance < 50 ? 'High' : 'Medium'
                ];
            }
        }

        // Average attendance
        $avgAttendance = 0;
        $totalPresent = 0;
        $totalEnrolled = 0;
        foreach ($courses as $course) {
            $enrolled = Enrollment::where('course_id', $course->id)->where('status', 'approved')->count();
            $present = AttendanceRecord::whereHas('session', function($q) use ($course) {
                $q->where('course_id', $course->id);
            })->count();
            $totalEnrolled += $enrolled;
            $totalPresent += $present;
        }
        $avgAttendance = $totalEnrolled > 0 ? round(($totalPresent / $totalEnrolled) * 100) : 0;

        // Active session
        $activeSession = AttendanceSession::where('lecturer_id', $lecturer->id)
            ->where('is_active', true)
            ->where('status', 'active')
            ->with('course')
            ->first();

        $expiresIn = $activeSession ? now()->diffInSeconds($activeSession->qr_expiry) : 0;

        // Live attendance stats
        $presentCount = $activeSession ? $activeSession->present_count : 0;
        $absentCount = $activeSession ? $activeSession->absent_count : 0;
        $lateCount = $activeSession ? $activeSession->late_count : 0;
        $totalInSession = $activeSession ? $activeSession->total_students : 0;

        $presentPercent = $totalInSession > 0 ? round(($presentCount / $totalInSession) * 100) : 0;
        $absentPercent = $totalInSession > 0 ? round(($absentCount / $totalInSession) * 100) : 0;
        $latePercent = $totalInSession > 0 ? round(($lateCount / $totalInSession) * 100) : 0;

        // Late students
        $lateStudents = $activeSession ? AttendanceRecord::where('attendance_session_id', $activeSession->id)
            ->where('status', 'late')
            ->with('student')
            ->get() : collect();

        return view('lecturer.dashboard', compact(
            'totalStudents', 'atRiskStudents', 'avgAttendance', 'atRiskList', 'atRiskStudents',
            'courses', 'students', 'activeSession', 'expiresIn',
            'presentCount', 'absentCount', 'lateCount', 'totalInSession',
            'presentPercent', 'absentPercent', 'latePercent', 'lateStudents'
        ));
    }

    private function calculateStudentAttendance($studentId, $courseIds)
    {
        $totalSessions = AttendanceSession::whereIn('course_id', $courseIds)->count();
        $attended = AttendanceRecord::where('student_id', $studentId)
            ->whereHas('session', function($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })
            ->count();

        return $totalSessions > 0 ? round(($attended / $totalSessions) * 100) : 0;
    }

    // All Students - List all students in lecturer's courses
    public function students()
    {
        $lecturer = Auth::user();

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->get();

        $courseIds = $courses->pluck('id');

        $students = User::whereHas('enrollments', function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds)->where('status', 'approved');
        })->with(['enrollments' => function($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        }])->get();

        // Calculate attendance for each student
        foreach ($students as $student) {
            $student->attendance_percentage = $this->calculateStudentAttendance($student->id, $courseIds);
            if ($student->attendance_percentage >= 75) {
                $student->status = 'Eligible';
            } elseif ($student->attendance_percentage >= 60) {
                $student->status = 'Warning';
            } else {
                $student->status = 'At Risk';
            }
        }

        return view('lecturer.students', compact('students', 'courses'));
    }

    // Schedule - Weekly timetable for lecturer
    public function schedule()
    {
        $lecturer = Auth::user();

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->get();

        $schedule = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $timeSlots = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];

        foreach ($days as $day) {
            $schedule[$day] = $courses->filter(function($course) use ($day) {
                return $course->schedule_day === $day;
            });
        }

        return view('lecturer.schedule', compact('schedule', 'days', 'timeSlots'));
    }

    // Reports - Export attendance reports
    public function reports()
    {
        $lecturer = Auth::user();
        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->get();

        return view('lecturer.reports', compact('courses'));
    }

    // Announcements - Send announcements to students
    public function announcements()
    {
        $lecturer = Auth::user();
        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->get();

        return view('lecturer.announcements', compact('courses'));
    }
}
