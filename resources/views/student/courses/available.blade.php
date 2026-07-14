@extends('layouts.app')

@section('title', 'Available Courses')
@section('role', 'Student')
@section('page-title', 'Course Enrollment')
@section('welcome-text', 'Browse and enroll in courses for your academic year')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .year-badge {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .filter-section {
            background: var(--white);
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
        }

        .filter-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
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
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.75rem;
            font-size: 0.85rem;
            background: var(--bg-main);
            transition: var(--transition);
        }

        .filter-input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .filter-select {
            width: 100%;
            padding: 0.7rem 1rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.75rem;
            font-size: 0.85rem;
            background: var(--bg-main);
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .btn-filter {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .btn-filter:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.25);
        }

        .btn-reset {
            background: #f3f4f6;
            color: var(--text-dark);
            border: 1px solid rgba(10, 36, 99, 0.1);
            padding: 0.7rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-reset:hover {
            background: #e5e7eb;
        }

        .result-count {
            font-size: 0.8rem;
            color: var(--text-gray);
            margin-bottom: 1rem;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .course-card {
            background: var(--white);
            border-radius: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .course-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 1rem;
            color: var(--white);
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
            color: var(--text-gray);
        }

        .course-info i {
            width: 20px;
            color: var(--primary);
        }

        .btn-enroll {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-enroll:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.2);
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
            background: #d1fae5;
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
            background: var(--white);
            border-radius: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
        }

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
            transition: var(--transition);
            cursor: pointer;
        }

        .pagination a {
            background: var(--white);
            color: var(--text-dark);
            border: 1px solid rgba(10, 36, 99, 0.1);
        }

        .pagination a:hover {
            background: rgba(10, 36, 99, 0.05);
            color: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(10, 36, 99, 0.15);
        }

        .pagination .active span {
            background: var(--primary);
            color: var(--white);
            border: 1px solid var(--primary);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.25);
        }

        .pagination .disabled span {
            background: var(--bg-main);
            color: #d1d5db;
            border: 1px solid rgba(10, 36, 99, 0.06);
            cursor: not-allowed;
            transform: none;
        }

        .pagination-info {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-gray);
            padding: 0.5rem 1rem;
            background: var(--bg-main);
            border-radius: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-info i {
            color: var(--primary);
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
                style="background: #d1fae5; color: #166534; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="filter-section">
            <div class="filter-title"><i class="bi bi-funnel"></i> Filter Courses</div>
            <form method="GET" action="{{ route('student.courses.available') }}">
                <div class="filter-grid">
                    <input type="text" name="search" class="filter-input"
                        placeholder="🔍 Search by course code, name, or lecturer..." value="{{ request('search') }}">
                    <select name="department" class="filter-select">
                        <option value="">All Departments</option>
                        @foreach (\App\Models\Department::orderBy('name')->get() as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
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

        <div class="result-count">
            <i class="bi bi-info-circle"></i> Found <strong>{{ $availableCourses->total() }}</strong> courses
            @if (request('search') || request('department') || request('semester'))
                <span style="color: var(--primary);">(filtered)</span>
                <a href="{{ route('student.courses.available') }}"
                    style="color: var(--primary); margin-left: 0.5rem;">Clear
                    filters</a>
            @endif
        </div>

        <h3 style="color: var(--primary); margin-bottom: 1rem;">📚 Available Courses</h3>

        @if ($availableCourses->count() > 0)
            <div class="courses-grid">
                @foreach ($availableCourses as $course)
                    @php
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
                <i class="bi bi-book"></i>
                <p>No courses available for your department and year.</p>
                @if (request('search') || request('department') || request('semester'))
                    <a href="{{ route('student.courses.available') }}" class="btn-reset" style="margin-top: 1rem;">Clear
                        Filters</a>
                @endif
            </div>
        @endif
    </div>
@endsection
