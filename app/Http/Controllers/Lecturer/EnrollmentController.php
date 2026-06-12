<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    // Show students enrolled in lecturer's courses
    public function index()
    {
        $lecturer = Auth::user();

        // Get courses taught by this lecturer
        $courses = Course::where('lecturer_id', $lecturer->id)
            ->orWhere('lecturer_name', 'like', '%' . $lecturer->name . '%')
            ->with(['enrollments' => function($q) {
                $q->with('student')->where('status', 'approved');
            }])
            ->get();

        return view('lecturer.enrollments', compact('courses'));
    }

    // Show specific course students
    public function courseStudents($courseId)
    {
        $course = Course::with(['enrollments' => function($q) {
                $q->with('student')->where('status', 'approved');
            }])
            ->findOrFail($courseId);

        return view('lecturer.course-students', compact('course'));
    }
}
