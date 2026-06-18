@extends('layouts.app')

@section('title', $department->name)
@section('page-title', $department->name)
@section('welcome-text', $department->code . ' • ' . ($department->head_of_department ?? 'No HOD assigned'))

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7a8f;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            border-radius: 0.5rem;
            background: white;
            border: 1px solid #e9edf4;
            transition: all 0.2s;
            margin-bottom: 1.25rem;
        }

        .back-link:hover {
            color: #800000;
            border-color: #800000;
            transform: translateX(-3px);
        }

        .action-bar {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.4rem 1.2rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-action-primary {
            background: #800000;
            color: white;
        }

        .btn-action-primary:hover {
            background: #a00000;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        .btn-action-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-action-edit:hover {
            background: #fde68a;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            background: white;
            border-radius: 0.75rem;
            padding: 0.9rem 1.25rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.08);
            border-color: #800000;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stat-icon.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .stat-icon.green {
            background: #ecfdf5;
            color: #10b981;
        }

        .stat-icon.yellow {
            background: #fffbeb;
            color: #f59e0b;
        }

        .stat-icon.red {
            background: #fef2f2;
            color: #ef4444;
        }

        .stat-content .number {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .stat-content .label {
            font-size: 0.6rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tabs-modern {
            display: flex;
            gap: 0.25rem;
            background: white;
            border-radius: 0.75rem;
            padding: 0.3rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            flex-wrap: wrap;
        }

        .tab-btn-modern {
            padding: 0.4rem 1.2rem;
            border: none;
            background: transparent;
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7280;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .tab-btn-modern:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .tab-btn-modern.active {
            background: #800000;
            color: white;
            box-shadow: 0 2px 8px rgba(128, 0, 0, 0.25);
        }

        .tab-btn-modern .badge-tab {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .tab-btn-modern.active .badge-tab {
            background: rgba(255, 255, 255, 0.25);
        }

        .tab-panel-modern {
            display: none;
            animation: fadeSlideIn 0.25s ease;
        }

        .tab-panel-modern.active {
            display: block;
        }

        @keyframes fadeSlideIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .section-card .section-header {
            padding: 0.7rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .section-card .section-header .title {
            font-size: 0.8rem;
            font-weight: 600;
            color: #1f2937;
        }

        .section-card .section-header .title i {
            color: #800000;
            margin-right: 0.4rem;
        }

        .section-card .section-body {
            padding: 1rem 1.25rem;
        }

        .student-grid-modern {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            gap: 1rem;
        }

        .student-card-modern {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.9rem;
            text-align: center;
            transition: all 0.2s;
        }

        .student-card-modern:hover {
            border-color: #800000;
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .student-card-modern.current {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .student-card-modern .year-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #1f2937;
        }

        .student-card-modern .count {
            font-size: 1.6rem;
            font-weight: 700;
            color: #800000;
            margin: 0.15rem 0;
        }

        .student-card-modern .sub {
            font-size: 0.6rem;
            color: #6b7280;
        }

        .student-card-modern .attendance {
            font-size: 0.7rem;
            color: #10b981;
            margin: 0.15rem 0;
        }

        .student-card-modern .btn-view-sm {
            display: inline-block;
            margin-top: 0.3rem;
            background: #800000;
            color: white;
            padding: 0.1rem 0.8rem;
            border-radius: 0.3rem;
            text-decoration: none;
            font-size: 0.65rem;
            transition: all 0.2s;
        }

        .student-card-modern .btn-view-sm:hover {
            background: #a00000;
        }

        .course-table-wrap {
            overflow-x: auto;
        }

        .course-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            min-width: 850px;
        }

        .course-table thead th {
            padding: 0.5rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
            white-space: nowrap;
        }

        .course-table tbody td {
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .course-table tbody tr {
            transition: all 0.2s;
        }

        .course-table tbody tr:hover {
            background: #fafbfc;
        }

        .course-table .course-code-cell {
            font-weight: 700;
            color: #800000;
            font-size: 0.65rem;
            background: #fef3c7;
            padding: 0.05rem 0.6rem;
            border-radius: 0.3rem;
            display: inline-block;
            white-space: nowrap;
        }

        .course-table .attendance-pill {
            font-size: 0.6rem;
            font-weight: 600;
            padding: 0.05rem 0.6rem;
            border-radius: 1rem;
            display: inline-block;
            white-space: nowrap;
        }

        .course-table .attendance-pill.high {
            background: #ecfdf5;
            color: #10b981;
        }

        .course-table .attendance-pill.medium {
            background: #fffbeb;
            color: #f59e0b;
        }

        .course-table .attendance-pill.low {
            background: #fef2f2;
            color: #ef4444;
        }

        .course-table .btn-action-sm {
            padding: 0.15rem 0.5rem;
            border-radius: 0.3rem;
            font-size: 0.65rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            border: none;
            cursor: pointer;
            min-width: 30px;
            justify-content: center;
        }

        .btn-view-course {
            background: #eff6ff;
            color: #3b82f6;
        }

        .btn-view-course:hover {
            background: #dbeafe;
            color: #2563eb;
        }

        .btn-edit-course {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-edit-course:hover {
            background: #fde68a;
            color: #78350f;
        }

        .btn-delete-course {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete-course:hover {
            background: #fca5a5;
            color: #7f1d1d;
        }

        .action-cell {
            display: flex;
            gap: 0.3rem;
            justify-content: center;
            align-items: center;
            flex-wrap: nowrap;
            min-width: 110px;
        }

        .year-group {
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .year-group:last-child {
            margin-bottom: 0;
        }

        .year-group .year-header {
            padding: 0.4rem 0.75rem;
            background: #f8f9fc;
            font-weight: 600;
            font-size: 0.8rem;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .year-group .year-header .badge-count {
            background: #800000;
            color: white;
            font-size: 0.55rem;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
        }

        .empty-modern {
            text-align: center;
            padding: 2rem 1rem;
            color: #9ca3af;
        }

        .empty-modern i {
            font-size: 2rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 0.5rem;
        }

        .empty-modern p {
            font-size: 0.85rem;
            margin: 0;
        }

        .confirm-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .confirm-overlay.show {
            display: flex;
        }

        .confirm-box {
            background: white;
            border-radius: 0.75rem;
            padding: 2rem;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .confirm-box .icon {
            text-align: center;
            font-size: 2.5rem;
            color: #ef4444;
            margin-bottom: 0.5rem;
        }

        .confirm-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 0.3rem 0;
        }

        .confirm-box p {
            text-align: center;
            font-size: 0.85rem;
            color: #6b7280;
            margin: 0 0 1.5rem 0;
        }

        .confirm-box .buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .confirm-box .btn-confirm-cancel {
            padding: 0.4rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid #e5e7eb;
            background: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .confirm-box .btn-confirm-cancel:hover {
            background: #f3f4f6;
        }

        .confirm-box .btn-confirm-delete {
            padding: 0.4rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: #dc2626;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .confirm-box .btn-confirm-delete:hover {
            background: #b91c1c;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .student-grid-modern {
                grid-template-columns: repeat(2, 1fr);
            }

            .tabs-modern {
                gap: 0.15rem;
                padding: 0.25rem;
            }

            .tab-btn-modern {
                padding: 0.3rem 0.8rem;
                font-size: 0.7rem;
            }

            .course-table {
                font-size: 0.7rem;
                min-width: 700px;
            }

            .action-bar {
                justify-content: flex-start;
            }
        }
    </style>

    <!-- ===== BACK LINK ===== -->
    <a href="{{ route('admin.departments.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Departments
    </a>

    <!-- ===== ACTION BUTTONS ===== -->
    <div class="action-bar">
        <a href="{{ route('admin.departments.courses.create', $department) }}" class="btn-action btn-action-primary">
            <i class="bi bi-plus-circle"></i> Add Course
        </a>
        <a href="{{ route('admin.departments.edit', $department) }}" class="btn-action btn-action-edit">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>

    <!-- ===== STATS ===== -->
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-icon blue"><i class="bi bi-people"></i></div>
            <div class="stat-content">
                <div class="number">{{ $stats['total_students'] }}</div>
                <div class="label">Total Students</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon green"><i class="bi bi-book"></i></div>
            <div class="stat-content">
                <div class="number">{{ $stats['total_courses'] }}</div>
                <div class="label">Total Courses</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon yellow"><i class="bi bi-person-badge"></i></div>
            <div class="stat-content">
                <div class="number">{{ $stats['total_lecturers'] }}</div>
                <div class="label">Total Lecturers</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon red"><i class="bi bi-graph-up"></i></div>
            <div class="stat-content">
                <div class="number">{{ number_format($stats['overall_attendance'], 1) }}%</div>
                <div class="label">Avg Attendance</div>
            </div>
        </div>
    </div>

    <!-- ===== TABS ===== -->
    <div class="tabs-modern">
        <button class="tab-btn-modern active" data-tab="overview">
            📊 Overview
        </button>
        <button class="tab-btn-modern" data-tab="students">
            👨‍🎓 Students
            <span class="badge-tab">{{ $stats['total_students'] }}</span>
        </button>
        <button class="tab-btn-modern" data-tab="courses">
            📚 Courses
            <span class="badge-tab">{{ $stats['total_courses'] }}</span>
        </button>
        <button class="tab-btn-modern" data-tab="lecturers">
            👨‍🏫 Lecturers
            <span class="badge-tab">{{ $stats['total_lecturers'] }}</span>
        </button>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 1: OVERVIEW -->
    <!-- ============================================================ -->
    <div class="tab-panel-modern active" id="panel-overview">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
            <div class="section-card">
                <div class="section-header">
                    <span class="title"><i class="bi bi-people"></i> Students by Year</span>
                </div>
                <div class="section-body">
                    @if ($studentsByYear->count() > 0)
                        @foreach ($studentsByYear as $year => $data)
                            @php $isCurrent = ($year == 5); @endphp
                            <div class="year-group {{ $isCurrent ? 'current' : '' }}">
                                <div class="year-header">
                                    <span>
                                        {{ $year }}{{ $year <= 3 ? ['th', 'st', 'nd', 'rd'][$year] : 'th' }} Year
                                        @if ($isCurrent)
                                            <span
                                                style="background:#f59e0b; color:white; font-size:0.5rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:0.3rem;">Current</span>
                                        @endif
                                    </span>
                                    <span style="display:flex; gap:0.75rem; align-items:center; font-size:0.7rem;">
                                        <span>{{ $data->total }} students</span>
                                        <span
                                            style="color:#10b981;">{{ number_format($data->avg_attendance ?? 0, 1) }}%</span>
                                        <a href="{{ route('admin.departments.year.students', [$department, $year]) }}"
                                            style="color:#800000; text-decoration:none; font-weight:500; font-size:0.7rem;">View
                                            →</a>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-modern">
                            <i class="bi bi-people"></i>
                            <p>No students enrolled yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <span class="title"><i class="bi bi-book"></i> Courses by Year</span>
                </div>
                <div class="section-body">
                    @if ($coursesByYear->count() > 0)
                        @foreach ($coursesByYear as $year => $courses)
                            @php
                                $yearMap = [
                                    'First Year' => 1,
                                    'Second Year' => 2,
                                    'Third Year' => 3,
                                    'Fourth Year' => 4,
                                    'Fifth Year' => 5,
                                    'Sixth Year' => 6,
                                ];
                                $yearNum = $yearMap[$year] ?? 1;
                                $isCurrent = $yearNum == 5;
                            @endphp
                            <div class="year-group {{ $isCurrent ? 'current' : '' }}">
                                <div class="year-header">
                                    <span>
                                        {{ $year }}
                                        @if ($isCurrent)
                                            <span
                                                style="background:#f59e0b; color:white; font-size:0.5rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:0.3rem;">Current</span>
                                        @endif
                                    </span>
                                    <span style="display:flex; gap:0.75rem; align-items:center; font-size:0.7rem;">
                                        <span>{{ $courses->count() }} courses</span>
                                        <a href="{{ route('admin.departments.courses.index', $department) }}"
                                            style="color:#800000; text-decoration:none; font-weight:500; font-size:0.7rem;">View
                                            All →</a>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-modern">
                            <i class="bi bi-book"></i>
                            <p>No courses created yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 2: STUDENTS -->
    <!-- ============================================================ -->
    <div class="tab-panel-modern" id="panel-students">
        <div class="section-card">
            <div class="section-header">
                <span class="title"><i class="bi bi-people"></i> Students by Year</span>
            </div>
            <div class="section-body">
                <div class="student-grid-modern">
                    @foreach ($stats['years'] as $year)
                        @php
                            $yearData = $studentsByYear->get($year);
                            $hasStudents = $yearData && $yearData->total > 0;
                            $isCurrent = $year == 5;
                            $suffix = $year <= 3 ? ['th', 'st', 'nd', 'rd'][$year] : 'th';
                        @endphp
                        <div class="student-card-modern {{ $isCurrent ? 'current' : '' }}">
                            <div class="year-label">
                                {{ $year }}{{ $suffix }} Year
                                @if ($isCurrent)
                                    <span
                                        style="background:#f59e0b; color:white; font-size:0.45rem; padding:0.05rem 0.4rem; border-radius:1rem; display:inline-block;">Current</span>
                                @endif
                            </div>
                            @if ($hasStudents)
                                <div class="count">{{ $yearData->total }}</div>
                                <div class="sub">Students</div>
                                <div class="attendance">📊 {{ number_format($yearData->avg_attendance ?? 0, 1) }}%</div>
                                <a href="{{ route('admin.departments.year.students', [$department, $year]) }}"
                                    class="btn-view-sm">View →</a>
                            @else
                                <div style="color:#d1d5db; font-size:0.75rem; padding:0.3rem 0;">No students</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 3: COURSES (With Delete Button) -->
    <!-- ============================================================ -->
    <div class="tab-panel-modern" id="panel-courses">
        @if ($coursesByYear->count() > 0)
            @foreach ($coursesByYear as $year => $courses)
                @php
                    $yearMap = [
                        'First Year' => 1,
                        'Second Year' => 2,
                        'Third Year' => 3,
                        'Fourth Year' => 4,
                        'Fifth Year' => 5,
                        'Sixth Year' => 6,
                    ];
                    $yearNum = $yearMap[$year] ?? 1;
                    $isCurrent = $yearNum == 5;
                @endphp
                <div class="year-group {{ $isCurrent ? 'current' : '' }}"
                    style="margin-bottom:1.5rem; border:1px solid #e5e7eb; border-radius:0.5rem; overflow:hidden; background:white;">
                    <div class="year-header"
                        style="padding:0.4rem 0.75rem; background:#f8f9fc; font-weight:600; font-size:0.8rem; color:#1f2937; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
                        <span>
                            {{ $year }}
                            @if ($isCurrent)
                                <span
                                    style="background:#f59e0b; color:white; font-size:0.5rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:0.3rem;">Current</span>
                            @endif
                            <span class="badge-count"
                                style="background:#800000; color:white; font-size:0.55rem; padding:0.05rem 0.5rem; border-radius:1rem; margin-left:0.3rem;">{{ $courses->count() }}
                                courses</span>
                        </span>
                    </div>
                    <div class="course-table-wrap">
                        <table class="course-table">
                            <thead>
                                <tr>
                                    <th style="width:12%;">Code</th>
                                    <th style="width:22%;">Course Name</th>
                                    <th style="width:18%;">Lecturer</th>
                                    <th style="width:8%;text-align:center;">Room</th>
                                    <th style="width:8%;text-align:center;">Students</th>
                                    <th style="width:12%;text-align:center;">Attendance</th>
                                    <th style="width:20%;text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courses as $course)
                                    @php
                                        $attendance = $course->avg_attendance ?? 0;
                                        $attendanceClass =
                                            $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                                        $lecturerName =
                                            $course->lecturer->name ?? ($course->lecturer_name ?? 'Unassigned');
                                    @endphp
                                    <tr>
                                        <td><span class="course-code-cell">{{ $course->course_code }}</span></td>
                                        <td style="font-weight:500; color:#1f2937;">{{ $course->course_name }}</td>
                                        <td style="color:#6b7280; font-size:0.75rem;">
                                            <i class="bi bi-person" style="color:#800000; font-size:0.6rem;"></i>
                                            {{ $lecturerName }}
                                        </td>
                                        <td style="color:#6b7280; font-size:0.75rem; text-align:center;">
                                            <i class="bi bi-door-open" style="font-size:0.6rem;"></i>
                                            {{ $course->room ?? 'N/A' }}
                                        </td>
                                        <td style="text-align:center; font-weight:600; color:#1f2937;">
                                            {{ $course->student_count ?? 0 }}
                                        </td>
                                        <td style="text-align:center;">
                                            <span class="attendance-pill {{ $attendanceClass }}">
                                                {{ number_format($attendance, 1) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-cell">
                                                <a href="{{ route('admin.departments.courses.show', [$department, $course]) }}"
                                                    class="btn-action-sm btn-view-course" title="View Course">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.departments.courses.edit', [$department, $course]) }}"
                                                    class="btn-action-sm btn-edit-course" title="Edit Course">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn-action-sm btn-delete-course"
                                                    title="Delete Course"
                                                    onclick="showDeleteConfirm('{{ addslashes($course->course_name) }}', '{{ route('admin.departments.courses.destroy', [$department, $course]) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <div class="section-card">
                <div class="section-body">
                    <div class="empty-modern">
                        <i class="bi bi-book"></i>
                        <p>No courses created in this department yet.</p>
                        <a href="{{ route('admin.departments.courses.create', $department) }}"
                            style="color:#800000; text-decoration:none; font-weight:500; font-size:0.85rem;">
                            Add your first course →
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================================ -->
    <!-- TAB 4: LECTURERS (WITH SEARCH) -->
    <!-- ============================================================ -->
    <div class="tab-panel-modern" id="panel-lecturers">
        @if ($lecturers->count() > 0)
            <!-- Header with Search -->
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem;">
                <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                    <h5 style="font-size:0.95rem; font-weight:700; color:#1f2937; margin:0;">
                        <i class="bi bi-person-badge" style="color:#800000;"></i>
                        Lecturers
                    </h5>
                    <span
                        style="background:#f1f5f9; color:#6b7280; padding:0.05rem 0.6rem; border-radius:1rem; font-size:0.7rem; font-weight:600;">
                        {{ $lecturers->count() }}
                    </span>
                </div>
                <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                    <!-- Search Box for Faculty -->
                    <div
                        style="display:flex; align-items:center; background:white; border:1px solid #e9edf4; border-radius:0.5rem; padding:0.15rem 0.5rem; transition:all 0.2s;">
                        <i class="bi bi-search" style="color:#9ca3af; font-size:0.7rem;"></i>
                        <input type="text" id="facultySearch" placeholder="Search lecturers..."
                            style="border:none; outline:none; padding:0.25rem 0.4rem; font-size:0.75rem; color:#1a2332; background:transparent; width:150px;">
                        <i class="bi bi-x-circle" id="clearFacultySearch"
                            style="color:#9ca3af; font-size:0.7rem; cursor:pointer; display:none;"
                            onclick="document.getElementById('facultySearch').value=''; filterFaculty();"></i>
                    </div>
                    <a href="{{ route('admin.lecturers.create') }}?department={{ $department->id }}"
                        style="background:#800000; color:white; border:none; padding:0.25rem 0.8rem; border-radius:0.3rem; font-size:0.7rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.3rem;">
                        <i class="bi bi-plus-circle"></i> Assign Lecturers
                    </a>
                </div>
            </div>

            <!-- Lecturer Cards Grid -->
            <div id="facultyGrid"
                style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:1rem;">
                @foreach ($lecturers as $lecturer)
                    @php
                        $coursesCount = $lecturer->courses_count ?? 0;
                        $studentCount = $lecturer->students_count ?? 0;
                        $attendance = $lecturer->avg_attendance ?? 0;

                        if ($attendance >= 75) {
                            $color = '#10b981';
                            $bgColor = '#ecfdf5';
                            $label = 'Excellent';
                        } elseif ($attendance >= 60) {
                            $color = '#f59e0b';
                            $bgColor = '#fffbeb';
                            $label = 'Good';
                        } else {
                            $color = '#ef4444';
                            $bgColor = '#fef2f2';
                            $label = 'Needs Attention';
                        }

                        // Get initials
                        $nameParts = explode(' ', $lecturer->name);
                        $initials = '';
                        foreach ($nameParts as $part) {
                            $part = trim($part);
                            if (!empty($part) && !in_array($part, ['Dr.', 'Daw', 'Mg', 'U'])) {
                                $initials .= substr($part, 0, 1);
                            }
                        }
                        if (strlen($initials) < 2) {
                            $initials = substr(preg_replace('/[^a-zA-Z]/', '', $lecturer->name), 0, 2);
                        }
                        $initials = strtoupper(substr($initials, 0, 2));

                        // Search data attributes
                        $searchData = strtolower(
                            $lecturer->name . ' ' . $lecturer->email . ' ' . ($lecturer->department->name ?? ''),
                        );
                    @endphp

                    <div class="faculty-card" data-search="{{ $searchData }}"
                        style="background:white; border-radius:0.75rem; border:1px solid #e5e7eb; padding:1.25rem; transition:all 0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                        <!-- Card Top: Avatar + Name -->
                        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem;">
                            <div
                                style="width:48px; height:48px; border-radius:50%; background:linear-gradient(135deg, #800000, #a00000); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.9rem; flex-shrink:0;">
                                {{ $initials }}
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:0.9rem; color:#1f2937;">{{ $lecturer->name }}</div>
                                <div
                                    style="font-size:0.65rem; color:#6b7280; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $lecturer->email }}
                                </div>
                            </div>
                            <!-- Status Badge -->
                            <div
                                style="font-size:0.55rem; font-weight:600; padding:0.1rem 0.5rem; border-radius:1rem; background:{{ $bgColor }}; color:{{ $color }}; white-space:nowrap;">
                                {{ $label }}
                            </div>
                        </div>

                        <!-- Stats -->
                        <div
                            style="display:grid; grid-template-columns:repeat(3, 1fr); gap:0.5rem; padding:0.5rem 0; border-top:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; margin-bottom:0.75rem;">
                            <div style="text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:#1f2937;">{{ $coursesCount }}</div>
                                <div
                                    style="font-size:0.55rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.3px;">
                                    Courses</div>
                            </div>
                            <div style="text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:#1f2937;">{{ $studentCount }}</div>
                                <div
                                    style="font-size:0.55rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.3px;">
                                    Students</div>
                            </div>
                            <div style="text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:{{ $color }};">
                                    {{ number_format($attendance, 1) }}%</div>
                                <div
                                    style="font-size:0.55rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.3px;">
                                    Attendance</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div
                            style="height:4px; background:#f1f5f9; border-radius:4px; overflow:hidden; margin-bottom:0.75rem;">
                            <div
                                style="height:100%; width:{{ $attendance }}%; background:{{ $color }}; border-radius:4px;">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display:flex; gap:0.5rem;">
                            <a href="{{ route('admin.lecturers.show', $lecturer) }}"
                                style="flex:1; text-align:center; background:#800000; color:white; border:none; padding:0.3rem 0.75rem; border-radius:0.4rem; font-size:0.75rem; text-decoration:none; transition:all 0.2s;">
                                <i class="bi bi-eye"></i> View Profile
                            </a>
                            <a href="{{ route('admin.lecturers.edit', $lecturer) }}"
                                style="flex:1; text-align:center; background:#fef3c7; color:#92400e; border:none; padding:0.3rem 0.75rem; border-radius:0.4rem; font-size:0.75rem; text-decoration:none; transition:all 0.2s;">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- No Results Message -->
            <div id="noFacultyResults" style="display:none; text-align:center; padding:2rem; color:#9ca3af;">
                <i class="bi bi-search" style="font-size:1.5rem; display:block; margin-bottom:0.5rem;"></i>
                <p style="font-size:0.9rem; margin:0;">No faculty members found matching your search</p>
            </div>

            <!-- View All Link -->
            <div style="text-align:center; margin-top:1rem;">
                <a href="{{ route('admin.lecturers.index') }}"
                    style="color:#800000; text-decoration:none; font-size:0.75rem; font-weight:500; display:inline-flex; align-items:center; gap:0.3rem; transition:all 0.2s;">
                    View All Lecturers <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @else
            <!-- Empty State -->
            <div
                style="text-align:center; padding:2.5rem 1rem; background:white; border-radius:0.75rem; border:1px solid #e5e7eb;">
                <i class="bi bi-person-badge"
                    style="font-size:2rem; color:#d1d5db; display:block; margin-bottom:0.5rem;"></i>
                <h6 style="color:#374151; margin:0; font-size:0.9rem;">No Lecturers Assigned</h6>
                <p style="color:#9ca3af; font-size:0.8rem; margin:0.2rem 0 0.8rem;">
                    This department doesn't have any lecturers assigned yet.
                </p>
                <a href="{{ route('admin.lecturers.create') }}?department={{ $department->id }}"
                    style="background:#800000; color:white; border:none; padding:0.3rem 1rem; border-radius:0.4rem; font-size:0.75rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem;">
                    <i class="bi bi-plus-circle"></i> Assign Lecturer
                </a>
            </div>
        @endif
    </div>

    <script>
        function filterFaculty() {
            const searchTerm = document.getElementById('facultySearch').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.faculty-card');
            const clearBtn = document.getElementById('clearFacultySearch');
            let visibleCount = 0;

            // Show/hide clear button
            if (searchTerm.length > 0) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
            }

            cards.forEach(card => {
                const searchData = card.getAttribute('data-search') || '';
                if (searchTerm === '' || searchData.includes(searchTerm)) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide no results message
            const noResults = document.getElementById('noFacultyResults');
            if (visibleCount === 0 && searchTerm !== '') {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }

        // Add event listener to search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('facultySearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', filterFaculty);
                searchInput.addEventListener('search', filterFaculty);
            }
        });
    </script>

    <!-- ===== CONFIRM DIALOG ===== -->
    <div class="confirm-overlay" id="deleteConfirm">
        <div class="confirm-box">
            <div class="icon">🗑️</div>
            <h4>Delete Course</h4>
            <p>Are you sure you want to delete "<span id="confirmCourseName"></span>"?<br>This action cannot be undone.</p>
            <div class="buttons">
                <button class="btn-confirm-cancel" onclick="closeConfirm()">Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-confirm-delete">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteConfirm(courseName, deleteUrl) {
            document.getElementById('confirmCourseName').textContent = courseName;
            document.getElementById('deleteForm').action = deleteUrl;
            document.getElementById('deleteConfirm').classList.add('show');
        }

        function closeConfirm() {
            document.getElementById('deleteConfirm').classList.remove('show');
        }

        document.getElementById('deleteConfirm').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirm();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirm();
            }
        });

        document.querySelectorAll('.tab-btn-modern').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn-modern').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const tabId = this.dataset.tab;
                document.querySelectorAll('.tab-panel-modern').forEach(p => p.classList.remove('active'));
                document.getElementById('panel-' + tabId).classList.add('active');
            });
        });
    </script>

@endsection
