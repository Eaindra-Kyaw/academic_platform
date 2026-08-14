<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseAssessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentSubmission;
use App\Models\Semester;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseAssessmentController extends Controller
{
    /**
     * Assessment Dashboard (Admin)
     */
    public function dashboard()
    {
        $statusCounts = [
            'draft' => CourseAssessment::where('status', 'draft')->count(),
            'active' => CourseAssessment::where('status', 'active')->count(),
            'closed' => CourseAssessment::where('status', 'closed')->count(),
            'archived' => CourseAssessment::where('status', 'archived')->count(),
        ];

        $assessments = CourseAssessment::with(['semester', 'course', 'lecturer', 'questions', 'submissions'])
    ->when(request('year'), function($query) {
        return $query->where('year', request('year'));
    })
    ->when(request('semester'), function($query) {
        return $query->where('semester', request('semester'));
    })
    ->when(request('status'), function($query) {
        return $query->where('status', request('status'));
    })
    ->orderBy('created_at', 'desc')
    ->get()

            ->map(function($assessment) {
                // Calculate submission count
                $submitted = $assessment->submissions->count();
                $uniqueStudents = $assessment->submissions->groupBy('student_id')->count();
                $totalStudents = $this->getTotalEnrolledStudentsForAssessment($assessment);

                // Only count 'rating' type questions, ignore 'text' (Comments)
                $ratingQuestionsCount = $assessment->questions->where('type', 'rating')->count();

                return [
                    'id' => $assessment->id,
                    'name' => $assessment->name,
                    'year' => $assessment->year,
                    'semester' => $assessment->semester,
                    'course' => $assessment->course ? $assessment->course->toArray() : null,
                    'lecturer' => $assessment->lecturer ? $assessment->lecturer->toArray() : null,
                    'status' => $assessment->status,
                    'questions_count' => $ratingQuestionsCount,
                    'submitted' => $submitted,
                    'unique_students' => $uniqueStudents,
                    'total_students' => $totalStudents,
                    'response_rate' => $totalStudents > 0 ? round(($uniqueStudents / $totalStudents) * 100, 1) : 0,
                    'is_open' => $assessment->isOpen(),
                    'opens_at' => $assessment->opens_at,
                    'closes_at' => $assessment->closes_at,
                    'has_submissions' => $submitted > 0,
                ];
            });

        $recentSubmissions = AssessmentSubmission::with(['student', 'course', 'assessment'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($submission) {
                return [
                    'student_name' => $submission->student->name ?? 'Unknown',
                    'student_id' => $submission->student->student_id ?? 'N/A',
                    'course_code' => $submission->course->course_code ?? 'N/A',
                    'assessment_name' => $submission->assessment->name ?? 'N/A',
                    'submitted_at' => $submission->submitted_at,
                    'rating' => $submission->average_rating,
                ];
            });

        $courseCompletion = [];
        $years = ['First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'];

        foreach ($years as $year) {
            $courses = Course::where('year', $year)->where('is_active', true)->get();
            $totalSubmissions = 0;
            $totalEnrolled = 0;

            foreach ($courses as $course) {
                $submissions = AssessmentSubmission::where('course_id', $course->id)->count();
                $enrolled = Enrollment::where('course_id', $course->id)
                    ->where('status', 'approved')
                    ->count();

                $totalSubmissions += $submissions;
                $totalEnrolled += $enrolled;
            }

            if ($totalEnrolled > 0) {
                $courseCompletion[] = [
                    'year' => $year,
                    'submitted' => $totalSubmissions,
                    'total' => $totalEnrolled,
                    'rate' => round(($totalSubmissions / $totalEnrolled) * 100, 1),
                ];
            }
        }

        $totalAssessments = CourseAssessment::count();
        $totalSubmissions = AssessmentSubmission::count();
        $uniqueParticipants = AssessmentSubmission::distinct('student_id')->count('student_id');

        return view('admin.assessments.dashboard', compact(
            'assessments',
            'statusCounts',
            'recentSubmissions',
            'courseCompletion',
            'totalAssessments',
            'totalSubmissions',
            'uniqueParticipants'
        ));
    }

    /**
     * Display all assessment forms
     */
    public function index()
    {
        $assessments = CourseAssessment::with(['semester', 'course', 'lecturer', 'questions'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.assessments.index', compact('assessments'));
    }

    /**
     * Show form creation page
     */
    public function create()
    {
        $semesters = Semester::orderBy('academic_year', 'desc')
            ->orderBy('semester_number', 'asc')
            ->get();

        $years = [
            'First Year' => 'First Year',
            'Second Year' => 'Second Year',
            'Third Year' => 'Third Year',
            'Fourth Year' => 'Fourth Year',
            'Fifth Year' => 'Fifth Year',
            'Sixth Year' => 'Sixth Year',
        ];

        $semesterOptions = ['First Semester', 'Second Semester'];

        return view('admin.assessments.create', compact('semesters', 'years', 'semesterOptions'));
    }

    /**
     * AJAX: FETCH COURSES
     */
    public function fetchCourses(Request $request)
    {
        $year = $request->query('year');
        $semester = $request->query('semester');

        if (!$year || !$semester) {
            return response()->json([]);
        }

        $courses = Course::where('year', $year)
            ->where('semester', $semester)
            ->where('is_active', true)
            ->orderBy('course_code')
            ->get(['id', 'course_code', 'course_name', 'lecturer_id']);

        return response()->json($courses);
    }

    /**
     * AJAX: FETCH LECTURERS
     */
    public function fetchLecturers(Request $request)
    {
        $courseId = $request->query('course_id');

        if (!$courseId) {
            return response()->json([]);
        }

        $course = Course::with('lecturer')->find($courseId);

        $lecturers = [];
        if ($course && $course->lecturer) {
            $lecturers[] = [
                'id' => $course->lecturer->id,
                'name' => $course->lecturer->name,
            ];
        }

        // Also check timetable entries
        $timetableLecturers = \App\Models\TimetableEntry::where('course_id', $courseId)
            ->where('is_active', true)
            ->with('lecturer')
            ->get()
            ->pluck('lecturer')
            ->filter()
            ->unique('id')
            ->map(function($lecturer) {
                return [
                    'id' => $lecturer->id,
                    'name' => $lecturer->name,
                ];
            })
            ->values()
            ->toArray();

        foreach ($timetableLecturers as $tl) {
            $exists = false;
            foreach ($lecturers as $l) {
                if ($l['id'] == $tl['id']) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $lecturers[] = $tl;
            }
        }

        return response()->json($lecturers);
    }

    /**
     * AJAX: FETCH ALL COURSES IN A YEAR/SEMESTER (For "Create All" feature)
     */
    public function fetchCoursesByYearAndSemester(Request $request)
    {
        $year = $request->query('year');
        $semester = $request->query('semester');

        if (!$year || !$semester) {
            return response()->json([]);
        }

        $courses = Course::where('year', $year)
            ->where('semester', $semester)
            ->where('is_active', true)
            ->orderBy('course_code')
            ->get(['id', 'course_code', 'course_name', 'lecturer_id']);

        return response()->json($courses);
    }

    /**
     * STORE: Create assessments (Supports Single OR Batch creation)
     */
    public function store(Request $request)
    {
        $rules = [
            'year' => 'required|string',
            'semester' => 'required|string',
            'questions' => 'required|array|min:5',
            'questions.*' => 'required|string|min:5',
            'status' => 'required|in:draft,active,closed,archived',
            'opens_at' => 'required|date',
            'closes_at' => 'required|date|after:opens_at',
        ];

        if ($request->input('scope') === 'all') {
            $rules['year'] = 'required|string';
            $rules['semester'] = 'required|string';
        } else {
            $rules['course_id'] = 'required|exists:courses,id';
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $coursesToProcess = [];

            if ($request->input('scope') === 'all') {
                $coursesToProcess = Course::where('year', $request->year)
                    ->where('semester', $request->semester)
                    ->where('is_active', true)
                    ->get();
            } else {
                $coursesToProcess = Course::where('id', $request->course_id)->get();
            }

            foreach ($coursesToProcess as $course) {

                // UPDATE TITLE: Code + Name + Lecturer
                $lecturerName = $course->lecturer ? $course->lecturer->name : 'No Lecturer';
                $name = $course->course_code . ' - ' . $course->course_name . ' (' . $lecturerName . ')';

                $assessment = CourseAssessment::create([
                    'name' => $name,
                    'description' => null,
                    'semester_id' => null,
                    'year' => $request->year,
                    'semester' => $request->semester,
                    'course_id' => $course->id,
                    'lecturer_id' => $course->lecturer_id,
                    'status' => $request->status,
                    'opens_at' => $request->opens_at,
                    'closes_at' => $request->closes_at,
                ]);

                foreach ($request->questions as $index => $questionText) {
                    AssessmentQuestion::create([
                        'assessment_id' => $assessment->id,
                        'order' => $index + 1,
                        'question_text' => $questionText,
                        'type' => 'rating',
                        'min_rating' => 1,
                        'max_rating' => 5,
                    ]);
                }

                AssessmentQuestion::create([
                    'assessment_id' => $assessment->id,
                    'order' => count($request->questions) + 1,
                    'question_text' => 'အခြားမှတ်ချက်များ (Other Comments)',
                    'type' => 'text',
                    'min_rating' => null,
                    'max_rating' => null,
                ]);
            }

            DB::commit();

            $count = count($coursesToProcess);
            return redirect()->route('admin.assessments.dashboard')
                ->with('success', "Successfully created {$count} course assessment(s)!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create: ' . $e->getMessage());
        }
    }

    /**
     * Show assessment results (Admin only)
     */
    public function results($id)
    {
        $assessment = CourseAssessment::with([
            'questions',
            'submissions.student',
            'submissions.course',
            'course',
            'lecturer'
        ])->findOrFail($id);

        $stats = [
            'total_submissions' => $assessment->submissions->count(),
            'unique_students' => $assessment->submissions->unique('student_id')->count(),
            'overall_average' => $assessment->average_rating,
            'courses_count' => $assessment->submissions->unique('course_id')->count(),
            'response_rate' => $assessment->response_rate,
        ];

        $questionResults = [];
        foreach ($assessment->questions as $question) {
            $ratings = [];
            $textResponses = [];

            foreach ($assessment->submissions as $submission) {
                $answer = $submission->answers[$question->id] ?? null;
                if ($question->type === 'text') {
                    if ($answer) {
                        $textResponses[] = $answer;
                    }
                } else {
                    if (is_numeric($answer)) {
                        $ratings[] = $answer;
                    }
                }
            }

            $questionResults[] = [
                'question' => $question,
                'ratings' => $ratings,
                'average' => count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 2) : 0,
                'count' => count($ratings),
                'distribution' => $this->getDistribution($ratings, 1, 5),
                'text_responses' => $textResponses,
            ];
        }

        $courseBreakdown = [];
        foreach ($assessment->submissions->groupBy('course_id') as $courseId => $submissions) {
            $course = $submissions->first()->course;
            $courseBreakdown[] = [
                'course' => $course,
                'total' => $submissions->count(),
                'average_rating' => $submissions->avg(function($s) {
                    return $s->average_rating;
                }),
            ];
        }

        $lecturerBreakdown = [];
        foreach ($assessment->submissions->groupBy('lecturer_id') as $lecturerId => $submissions) {
            if ($lecturerId) {
                $lecturer = $submissions->first()->lecturer;
                $lecturerBreakdown[] = [
                    'lecturer' => $lecturer,
                    'total' => $submissions->count(),
                    'average_rating' => $submissions->avg(function($s) {
                        return $s->average_rating;
                    }),
                ];
            }
        }

        return view('admin.assessments.results', compact(
            'assessment',
            'questionResults',
            'courseBreakdown',
            'lecturerBreakdown',
            'stats'
        ));
    }

    /**
     * Export results to CSV
     */
    public function export($id)
    {
        $assessment = CourseAssessment::with(['questions', 'submissions.student', 'submissions.course'])
            ->findOrFail($id);

        $filename = 'assessment_results_' . str_replace(' ', '_', $assessment->name) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($assessment) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            $header = ['Student Name', 'Student ID', 'Course', 'Average Rating'];
            foreach ($assessment->questions as $q) {
                $header[] = 'Q' . $q->order;
            }
            $header[] = 'Comments';
            fputcsv($file, $header);

            foreach ($assessment->submissions as $submission) {
                $row = [
                    $submission->student->name ?? 'N/A',
                    $submission->student->student_id ?? 'N/A',
                    $submission->course->course_code ?? 'N/A',
                    $submission->average_rating,
                ];

                foreach ($assessment->questions as $q) {
                    $row[] = $submission->answers[$q->id] ?? '';
                }

                $row[] = $submission->comments ?? '';
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * TOGGLE: Open or Close an assessment
     */
    public function toggleStatus($id)
    {
        $assessment = CourseAssessment::findOrFail($id);

        if ($assessment->status === 'active') {
            $assessment->status = 'closed';
            $message = 'Assessment closed successfully.';
        } else {
            $assessment->status = 'active';
            $message = 'Assessment opened successfully.';
        }

        $assessment->save();

        return redirect()->route('admin.assessments.dashboard')
            ->with('success', $message);
    }

    /**
     * DESTROY: Delete assessment
     */
    public function destroy($id)
    {
        $assessment = CourseAssessment::findOrFail($id);

        DB::beginTransaction();
        try {
            AssessmentQuestion::where('assessment_id', $assessment->id)->delete();
            AssessmentSubmission::where('assessment_id', $assessment->id)->delete();
            $assessment->delete();
            DB::commit();

            return redirect()->route('admin.assessments.dashboard')
                ->with('success', 'Assessment deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete assessment: ' . $e->getMessage());
        }
    }

    // ============================================================
    // STUDENT METHODS
    // ============================================================

    public function studentIndex()
    {
        $student = Auth::user();

        $yearMap = [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];
        $yearString = $yearMap[$student->current_year] ?? '';

        $currentSemester = Semester::where('is_current', true)->first();
        $semesterString = $currentSemester ? $currentSemester->semester_name : 'Second Semester';

        $activeAssessments = CourseAssessment::active()
            ->where('year', $yearString)
            ->where('semester', $semesterString)
            ->get();

        $submittedIds = AssessmentSubmission::where('student_id', $student->id)
            ->pluck('assessment_id')
            ->toArray();

        $pendingAssessments = $activeAssessments->filter(function($assessment) use ($submittedIds) {
            return !in_array($assessment->id, $submittedIds);
        });

        $submittedCount = AssessmentSubmission::where('student_id', $student->id)->count();

        return view('student.assessments.index', compact('pendingAssessments', 'submittedCount'));
    }

    public function studentShow($id)
    {
        $assessment = CourseAssessment::with(['questions', 'course', 'lecturer'])
            ->findOrFail($id);

        if (!$assessment->isOpen()) {
            return redirect()->route('student.assessments.index')
                ->with('error', 'This assessment is not currently open.');
        }

        $hasSubmitted = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->where('student_id', Auth::id())
            ->exists();

        if ($hasSubmitted) {
            return redirect()->route('student.assessments.index')
                ->with('error', 'You have already submitted this assessment.');
        }

        $student = Auth::user();

        $courses = Enrollment::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('course')
            ->get()
            ->pluck('course')
            ->filter(function($course) use ($assessment) {
                return $course->id == $assessment->course_id;
            });

        return view('student.assessments.show', compact('assessment', 'courses'));
    }

    public function studentSubmit(Request $request)
    {
        $request->validate([
            'assessment_id' => 'required|exists:course_assessments,id',
            'course_id' => 'required|exists:courses,id',
            'lecturer_id' => 'required|exists:users,id',
            'answers' => 'required|array|min:1',
        ]);

        $student = Auth::user();
        $assessmentId = $request->assessment_id;
        $courseId = $request->course_id;

        $exists = AssessmentSubmission::where('assessment_id', $assessmentId)
            ->where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already submitted this assessment.');
        }

        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('course_id', $courseId)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'You are not enrolled in this course.');
        }

        $assessment = CourseAssessment::with('questions')->findOrFail($assessmentId);
        $validQuestionIds = $assessment->questions->pluck('id')->toArray();

        $cleanedAnswers = [];
        foreach ($request->answers as $questionId => $value) {
            if (in_array($questionId, $validQuestionIds)) {
                $cleanedAnswers[$questionId] = $value;
            }
        }

        AssessmentSubmission::create([
            'assessment_id' => $assessmentId,
            'student_id' => $student->id,
            'course_id' => $courseId,
            'lecturer_id' => $request->lecturer_id,
            'answers' => $cleanedAnswers,
            'comments' => $request->comments,
            'submitted_at' => now(),
        ]);

        return redirect()->route('student.assessments.index')
            ->with('success', '✅ Thank you! Your course assessment has been submitted successfully.');
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    private function getTotalEnrolledStudentsForAssessment($assessment)
    {
        try {
            $query = Enrollment::where('status', 'approved');

            if ($assessment->course_id) {
                $query->where('course_id', $assessment->course_id);
            } else if ($assessment->year && $assessment->semester) {
                $query->whereHas('course', function($q) use ($assessment) {
                    $q->where('year', $assessment->year)
                      ->where('semester', $assessment->semester);
                });
            }

            $count = $query->distinct('student_id')->count('student_id');
            return $count > 0 ? $count : 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    private function getDistribution($ratings, $min, $max)
    {
        $dist = [];
        for ($i = $min; $i <= $max; $i++) {
            $dist[$i] = count(array_filter($ratings, function($r) use ($i) {
                return $r == $i;
            }));
        }
        return $dist;
    }
}
