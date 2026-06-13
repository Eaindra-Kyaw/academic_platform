@extends('layouts.app')

@section('title', 'Available Courses')
@section('role', 'Student')
@section('page-title', 'Course Enrollment')
@section('welcome-text', 'Browse and enroll in courses for your academic year')

@section('sidebar')
    <div class="nav-label">Navigation</div>
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('student.courses.available') }}" class="nav-item active">
        <i class="bi bi-book"></i><span>Available Courses</span>
    </a>
    <a href="{{ route('student.my.enrollments') }}" class="nav-item">
        <i class="bi bi-list-check"></i><span>My Enrollments</span>
    </a>
    <a href="{{ route('student.scan') }}" class="nav-item">
        <i class="bi bi-qr-code-scan"></i><span>QR Attendance</span>
    </a>
    <a href="{{ route('student.timetable') }}" class="nav-item">
        <i class="bi bi-calendar"></i><span>Timetable</span>
    </a>
    <a href="{{ route('student.progress') }}" class="nav-item">
        <i class="bi bi-graph-up"></i><span>My Progress</span>
    </a>
    <div class="nav-label">Support</div>
    <a href="#" class="nav-item" onclick="openUniBot()">
        <i class="bi bi-robot"></i><span>Uni Bot</span>
    </a>
    <a href="#" class="nav-item">
        <i class="bi bi-bell"></i><span>Notifications</span>
    </a>
@endsection

@section('content')
    <style>
        .year-badge {
            background: linear-gradient(135deg, #800000 0%, #6b0000 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        /* Search Filter Section */
        .filter-section {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
        }

        .filter-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #800000;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-input {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            background: #f9fafb;
            transition: all 0.2s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #800000;
            background: white;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
        }

        .filter-select {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.85rem;
            background: #f9fafb;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            border-color: #800000;
        }

        .btn-filter {
            background: #800000;
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-filter:hover {
            background: #9a0000;
            transform: translateY(-1px);
        }

        .btn-reset {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            padding: 0.7rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-reset:hover {
            background: #e5e7eb;
        }

        .result-count {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .course-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }

        .course-header {
            background: linear-gradient(135deg, #800000 0%, #6b0000 100%);
            padding: 1rem;
            color: white;
        }

        .course-code {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .course-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }

        .course-body {
            padding: 1rem;
        }

        .course-info {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.75rem;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .course-info i {
            width: 20px;
            color: #800000;
        }

        .btn-enroll {
            background: #800000;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-enroll:hover {
            background: #9a0000;
        }

        .btn-pending {
            background: #fef3c7;
            color: #92400e;
            width: 100%;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-align: center;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-enrolled {
            background: #dcfce7;
            color: #166534;
            width: 100%;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-align: center;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 1rem;
        }

        /* Modern Pagination Styles */
        .pagination-wrapper {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 1rem;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .pagination li {
            display: inline-block;
        }

        .pagination a,
        .pagination span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 0.9rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .pagination a {
            background: white;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .pagination a:hover {
            background: #fef3c7;
            color: #800000;
            border-color: #800000;
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(128, 0, 0, 0.15);
        }

        .pagination .active span {
            background: #800000;
            color: white;
            border: 1px solid #800000;
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.25);
        }

        .pagination .disabled span {
            background: #f9fafb;
            color: #d1d5db;
            border: 1px solid #e5e7eb;
            cursor: not-allowed;
            transform: none;
        }

        .pagination .disabled span:hover {
            transform: none;
            box-shadow: none;
        }

        .pagination a i,
        .pagination span i {
            font-size: 1rem;
            font-weight: bold;
        }

        .pagination-info {
            text-align: center;
            font-size: 0.75rem;
            color: #6b7280;
            padding: 0.5rem 1rem;
            background: #f9fafb;
            border-radius: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-info i {
            color: #800000;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .courses-grid {
                grid-template-columns: 1fr;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .btn-filter,
            .btn-reset {
                width: 100%;
                justify-content: center;
            }

            .pagination a,
            .pagination span {
                min-width: 36px;
                height: 36px;
                padding: 0 0.75rem;
                font-size: 0.75rem;
            }

            .pagination-info {
                font-size: 0.65rem;
            }
        }
    </style>

    <div>
        <div class="year-badge">
            <i class="bi bi-building"></i>
            {{ Auth::user()->department ? Auth::user()->department->name : 'Your Department' }} |
            <i class="bi bi-calendar-check"></i> {{ $studentYearString }}
        </div>

        @if (session('success'))
            <div
                style="background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Search Filter Section -->
        <div class="filter-section">

            <form method="GET" action="{{ route('student.courses.available') }}">
                <div class="filter-grid">
                    <input type="text" name="search" class="filter-input"
                        placeholder="🔍 Search by course code, name, or lecturer..." value="{{ request('search') }}">
                    <select name="department" class="filter-select">
                        <option value="">All Departments (within your year)</option>
                        @foreach (\App\Models\Department::orderBy('name')->get() as $dept)
                            <option value="{{ $dept->id }}"
                                {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->code }} - {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="semester" class="filter-select">
                        <option value="">All Semesters</option>
                        <option value="First Semester" {{ request('semester') == 'First Semester' ? 'selected' : '' }}>
                            First Semester</option>
                        <option value="Second Semester" {{ request('semester') == 'Second Semester' ? 'selected' : '' }}>
                            Second Semester</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <button type="submit" class="btn-filter">
                        <i class="bi bi-search"></i> Apply
                    </button>
                    <a href="{{ route('student.courses.available') }}" class="btn-reset">
                        <i class="bi bi-arrow-repeat"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Result Count -->
        <div class="result-count">
            <i class="bi bi-info-circle"></i> Found <strong>{{ $availableCourses->total() }}</strong> courses
            @if (request('search') || request('department') || request('semester'))
                <span style="color: #800000;">(filtered)</span>
                <a href="{{ route('student.courses.available') }}" style="color: #800000; margin-left: 0.5rem;">Clear
                    filters</a>
            @endif
        </div>

        <h3 style="color: #800000; margin-bottom: 1rem;">📚 Available Courses</h3>

        @if ($availableCourses->count() > 0)
            <div class="courses-grid">
                @foreach ($availableCourses as $course)
                    @php
                        // Check enrollment status for this specific course
                        $enrollment = App\Models\Enrollment::where('student_id', auth()->id())
                            ->where('course_id', $course->id)
                            ->first();
                        $enrollmentStatus = $enrollment ? $enrollment->status : null;
                    @endphp
                    <div class="course-card">
                        <div class="course-header">
                            <div class="course-code">{{ $course->course_code }}</div>
                            <div class="course-title">{{ $course->course_name }}</div>
                        </div>
                        <div class="course-body">
                            <div class="course-info">
                                <i class="bi bi-building"></i>
                                <span>{{ $course->department->code ?? 'N/A' }}</span>
                            </div>
                            <div class="course-info">
                                <i class="bi bi-person-badge"></i>
                                <span>{{ $course->lecturer_name ?? 'Not Assigned' }}</span>
                            </div>
                            <div class="course-info">
                                <i class="bi bi-clock"></i>
                                <span>{{ $course->credits }} Credits</span>
                            </div>
                            @if ($course->schedule_day && $course->schedule_time)
                                <div class="course-info">
                                    <i class="bi bi-calendar"></i>
                                    <span>{{ $course->schedule_day }} at
                                        @if ($course->schedule_time)
                                            {{ \Carbon\Carbon::parse($course->schedule_time)->format('g:i A') }}
                                        @else
                                            TBA
                                        @endif
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div style="padding: 0 1rem 1rem 1rem;">
                            @if ($enrollmentStatus == 'approved')
                                <div class="btn-enrolled">
                                    <i class="bi bi-check-circle-fill"></i> ✅ Already Enrolled
                                </div>
                            @elseif($enrollmentStatus == 'pending')
                                <div class="btn-pending">
                                    <i class="bi bi-clock-history"></i> ⏳ Pending Approval
                                </div>
                            @else
                                <form method="POST" action="{{ route('student.courses.enroll', $course->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-enroll">
                                        <i class="bi bi-plus-circle"></i> Request Enrollment
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Modern Pagination -->
            @if ($availableCourses->hasPages())
                <div class="pagination-wrapper">
                    <ul class="pagination">
                        @if ($availableCourses->onFirstPage())
                            <li class="disabled">
                                <span><i class="bi bi-chevron-left"></i> Prev</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $availableCourses->previousPageUrl() }}" rel="prev">
                                    <i class="bi bi-chevron-left"></i> Prev
                                </a>
                            </li>
                        @endif

                        @foreach ($availableCourses->getUrlRange(1, $availableCourses->lastPage()) as $page => $url)
                            @if ($page == $availableCourses->currentPage())
                                <li class="active">
                                    <span>{{ $page }}</span>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        @if ($availableCourses->hasMorePages())
                            <li>
                                <a href="{{ $availableCourses->nextPageUrl() }}" rel="next">
                                    Next <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="disabled">
                                <span>Next <i class="bi bi-chevron-right"></i></span>
                            </li>
                        @endif
                    </ul>

                    <div class="pagination-info">
                        <i class="bi bi-info-circle"></i>
                        Showing <strong>{{ $availableCourses->firstItem() }}</strong> to
                        <strong>{{ $availableCourses->lastItem() }}</strong>
                        of <strong>{{ $availableCourses->total() }}</strong> courses
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="bi bi-book" style="font-size: 3rem; color: #9ca3af;"></i>
                <p>No courses available for your department and year.</p>
                @if (request('search') || request('department') || request('semester'))
                    <a href="{{ route('student.courses.available') }}" class="btn-reset" style="margin-top: 1rem;">Clear
                        Filters</a>
                @endif
            </div>
        @endif
    </div>

    <script>
        function openUniBot() {
            alert(
                '🤖 Uni Bot: How can I help you?\n\n- What courses are available?\n- How to enroll?\n- What is my enrollment status?'
            );
        }
    </script>
@endsection
