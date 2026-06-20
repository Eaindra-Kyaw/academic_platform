<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Department;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display student dashboard
     */
    public function dashboard()
    {
        $student = Auth::user();

        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        $stats = [
            'total_courses' => $enrollments->count(),
            'avg_attendance' => round($enrollments->avg('attendance_percentage') ?? 0, 1),
            'eligible_courses' => $enrollments->where('eligibility_status', 'eligible')->count(),
            'at_risk_courses' => $enrollments->where('eligibility_status', '!=', 'eligible')->count(),
        ];

        $announcements = Announcement::forRole('student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($student->id)) {
                $announcement->markAsRead($student->id);
            }
        }

        return view('student.dashboard', compact('student', 'enrollments', 'stats', 'announcements'));
    }

    public function attendance()
    {
        $student = Auth::user();
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        return view('student.attendance', compact('student', 'enrollments'));
    }

    public function timetable()
    {
        $student = Auth::user();
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        return view('student.timetable', compact('student', 'enrollments'));
    }

    public function progress()
    {
        $student = Auth::user();
        $enrollments = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get();

        return view('student.progress', compact('student', 'enrollments'));
    }

    public function show(User $student)
    {
        if ($student->role_id != 3) {
            abort(404, 'User is not a student');
        }

        $student->load(['department', 'enrollments' => function($query) {
            $query->where('status', 'approved')->with('course');
        }]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Display student announcements list
     */
    public function announcements()
    {
        $student = Auth::user();

        $announcements = Announcement::forRole('student')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        foreach ($announcements as $announcement) {
            if (!$announcement->isReadBy($student->id)) {
                $announcement->markAsRead($student->id);
            }
        }

        return view('student.announcements.index', compact('announcements', 'student'));
    }

    /**
     * Display a single announcement detail
     */
    public function showAnnouncement($id)
    {
        try {
            $student = Auth::user();

            // Find the announcement or fail
            $announcement = Announcement::with('creator')->findOrFail($id);

            // Mark as read
            if (!$announcement->isReadBy($student->id)) {
                $announcement->markAsRead($student->id);
            }

            // Return the show view
            return view('student.announcements.show', compact('announcement', 'student'));

        } catch (\Exception $e) {
            \Log::error('Error showing announcement: ' . $e->getMessage());
            return redirect()->route('student.announcements.index')
                ->with('error', 'Announcement not found.');
        }
    }
}
