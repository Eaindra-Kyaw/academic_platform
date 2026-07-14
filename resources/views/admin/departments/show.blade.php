@extends('layouts.app')

@section('title', $department->name)
@section('page-title', $department->name)
@section('welcome-text', $department->code . ' • ' . ($department->head_of_department ?? 'No HOD assigned'))

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #3B82F6;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --purple: #8b5cf6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.1);
            transition: var(--transition);
            margin-bottom: 1.25rem;
        }

        .back-link:hover {
            color: var(--primary);
            border-color: var(--primary);
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
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-action-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .btn-action-edit {
            background: var(--warning-light);
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
            background: var(--white);
            border-radius: var(--radius);
            padding: 0.9rem 1.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary);
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .stat-icon.blue {
            background: var(--info-light);
            color: var(--info);
        }

        .stat-icon.green {
            background: var(--success-light);
            color: var(--success);
        }

        .stat-icon.yellow {
            background: var(--warning-light);
            color: var(--warning);
        }

        .stat-icon.red {
            background: var(--danger-light);
            color: var(--danger);
        }

        .stat-content .number {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .stat-content .label {
            font-size: 0.6rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tabs-modern {
            display: flex;
            gap: 0.25rem;
            background: var(--white);
            border-radius: var(--radius);
            padding: 0.3rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            flex-wrap: wrap;
        }

        .tab-btn-modern {
            padding: 0.4rem 1.2rem;
            border: none;
            background: transparent;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-gray);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .tab-btn-modern:hover {
            background: #f3f4f6;
            color: var(--text-dark);
        }

        .tab-btn-modern.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(10, 36, 99, 0.25);
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
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .section-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .section-card .section-header {
            padding: 0.7rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .section-card .section-header .title {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .section-card .section-header .title i {
            color: var(--primary);
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
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.06);
            border-radius: 8px;
            padding: 0.9rem;
            text-align: center;
            transition: var(--transition);
        }

        .student-card-modern:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .student-card-modern.current {
            border-color: var(--warning);
            background: var(--warning-light);
        }

        .student-card-modern .year-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--text-dark);
        }

        .student-card-modern .count {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0.15rem 0;
        }

        .student-card-modern .sub {
            font-size: 0.6rem;
            color: var(--text-gray);
        }

        .student-card-modern .attendance {
            font-size: 0.7rem;
            color: var(--success);
            margin: 0.15rem 0;
        }

        .student-card-modern .btn-view-sm {
            display: inline-block;
            margin-top: 0.3rem;
            background: var(--primary);
            color: var(--white);
            padding: 0.1rem 0.8rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.65rem;
            transition: var(--transition);
        }

        .student-card-modern .btn-view-sm:hover {
            background: var(--primary-light);
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
            color: var(--text-gray);
            border-bottom: 2px solid rgba(10, 36, 99, 0.06);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
            white-space: nowrap;
        }

        .course-table tbody td {
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .course-table tbody tr {
            transition: var(--transition);
        }

        .course-table tbody tr:hover {
            background: #fafbfc;
        }

        .course-table .course-code-cell {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.65rem;
            background: rgba(212, 160, 23, 0.12);
            padding: 0.05rem 0.6rem;
            border-radius: 6px;
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
            background: var(--success-light);
            color: #166534;
        }

        .course-table .attendance-pill.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .course-table .attendance-pill.low {
            background: var(--danger-light);
            color: #991b1b;
        }

        .course-table .btn-action-sm {
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            font-size: 0.65rem;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            border: none;
            cursor: pointer;
            min-width: 30px;
            justify-content: center;
        }

        .btn-view-course {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-view-course:hover {
            background: #bfdbfe;
        }

        .btn-edit-course {
            background: var(--warning-light);
            color: #92400e;
        }

        .btn-edit-course:hover {
            background: #fde68a;
        }

        .btn-delete-course {
            background: var(--danger-light);
            color: var(--danger);
        }

        .btn-delete-course:hover {
            background: #fca5a5;
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
            border: 1px solid rgba(10, 36, 99, 0.06);
            border-radius: 8px;
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
            color: var(--text-dark);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .year-group .year-header .badge-count {
            background: var(--primary);
            color: var(--white);
            font-size: 0.55rem;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
        }

        .empty-modern {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--text-gray);
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
            background: var(--white);
            border-radius: var(--radius);
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
            color: var(--danger);
            margin-bottom: 0.5rem;
        }

        .confirm-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.3rem 0;
        }

        .confirm-box p {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-gray);
            margin: 0 0 1.5rem 0;
        }

        .confirm-box .buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .confirm-box .btn-confirm-cancel {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(10, 36, 99, 0.1);
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .confirm-box .btn-confirm-cancel:hover {
            background: #f3f4f6;
        }

        .confirm-box .btn-confirm-delete {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: var(--danger);
            color: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }

        .confirm-box .btn-confirm-delete:hover {
            background: #b91c1c;
        }

        .faculty-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1.25rem;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .faculty-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
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

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-item {
                padding: 0.75rem;
            }

            .stat-item .stat-icon {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
            }

            .stat-content .number {
                font-size: 1.1rem;
            }
        }
    </style>

    <!-- Back Link -->
    <a href="{{ route('admin.departments.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Departments
    </a>

    <!-- Action Buttons -->
    <div class="action-bar">
        <a href="{{ route('admin.departments.courses.create', $department) }}" class="btn-action btn-action-primary">
            <i class="bi bi-plus-circle"></i> Add Course
        </a>
        <a href="{{ route('admin.departments.edit', $department) }}" class="btn-action btn-action-edit">
            <i class="bi bi-pencil"></i> Edit
        </a>
    </div>

    <!-- Stats -->
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

    <!-- Tabs -->
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
                            <div class="year-group {{ $isCurrent ? 'current' : '' }}"
                                style="border-color: {{ $isCurrent ? 'var(--warning)' : 'rgba(10, 36, 99, 0.06)' }};">
                                <div class="year-header"
                                    style="background: {{ $isCurrent ? 'var(--warning-light)' : '#f8f9fc' }};">
                                    <span>
                                        {{ $year }}{{ $year <= 3 ? ['th', 'st', 'nd', 'rd'][$year] : 'th' }} Year
                                        @if ($isCurrent)
                                            <span
                                                style="background:var(--warning); color:var(--white); font-size:0.5rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:0.3rem;">Current</span>
                                        @endif
                                    </span>
                                    <span style="display:flex; gap:0.75rem; align-items:center; font-size:0.7rem;">
                                        <span>{{ $data->total }} students</span>
                                        <span
                                            style="color:var(--success);">{{ number_format($data->avg_attendance ?? 0, 1) }}%</span>
                                        <a href="{{ route('admin.departments.year.students', [$department, $year]) }}"
                                            style="color:var(--primary); text-decoration:none; font-weight:500; font-size:0.7rem;">View
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
                            <div class="year-group"
                                style="border-color: {{ $isCurrent ? 'var(--warning)' : 'rgba(10, 36, 99, 0.06)' }};">
                                <div class="year-header"
                                    style="background: {{ $isCurrent ? 'var(--warning-light)' : '#f8f9fc' }};">
                                    <span>
                                        {{ $year }}
                                        @if ($isCurrent)
                                            <span
                                                style="background:var(--warning); color:var(--white); font-size:0.5rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:0.3rem;">Current</span>
                                        @endif
                                    </span>
                                    <span style="display:flex; gap:0.75rem; align-items:center; font-size:0.7rem;">
                                        <span>{{ $courses->count() }} courses</span>
                                        <a href="{{ route('admin.departments.courses.index', $department) }}"
                                            style="color:var(--primary); text-decoration:none; font-weight:500; font-size:0.7rem;">View
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
                                        style="background:var(--warning); color:var(--white); font-size:0.45rem; padding:0.05rem 0.4rem; border-radius:1rem; display:inline-block;">Current</span>
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
    <!-- TAB 3: COURSES -->
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
                <div class="year-group"
                    style="border-color: {{ $isCurrent ? 'var(--warning)' : 'rgba(10, 36, 99, 0.06)' }}; margin-bottom:1.5rem; border-radius:8px; overflow:hidden; background:var(--white);">
                    <div class="year-header"
                        style="padding:0.4rem 0.75rem; background:{{ $isCurrent ? 'var(--warning-light)' : '#f8f9fc' }}; font-weight:600; font-size:0.8rem; color:var(--text-dark); border-bottom:1px solid rgba(10, 36, 99, 0.06); display:flex; justify-content:space-between; align-items:center;">
                        <span>
                            {{ $year }}
                            @if ($isCurrent)
                                <span
                                    style="background:var(--warning); color:var(--white); font-size:0.5rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:0.3rem;">Current</span>
                            @endif
                            <span class="badge-count"
                                style="background:var(--primary); color:var(--white); font-size:0.55rem; padding:0.05rem 0.5rem; border-radius:1rem; margin-left:0.3rem;">{{ $courses->count() }}
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
                                        <td style="font-weight:500; color:var(--text-dark);">{{ $course->course_name }}
                                        </td>
                                        <td style="color:var(--text-gray); font-size:0.75rem;">
                                            <i class="bi bi-person" style="color:var(--primary); font-size:0.6rem;"></i>
                                            {{ $lecturerName }}
                                        </td>
                                        <td style="color:var(--text-gray); font-size:0.75rem; text-align:center;">
                                            <i class="bi bi-door-open" style="font-size:0.6rem;"></i>
                                            {{ $course->room ?? 'N/A' }}
                                        </td>
                                        <td style="text-align:center; font-weight:600; color:var(--text-dark);">
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
                            style="color:var(--primary); text-decoration:none; font-weight:500; font-size:0.85rem;">
                            Add your first course →
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- ============================================================ -->
    <!-- TAB 4: LECTURERS -->
    <!-- ============================================================ -->
    <div class="tab-panel-modern" id="panel-lecturers">
        @if ($lecturers->count() > 0)
            <div
                style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem;">
                <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                    <h5 style="font-size:0.95rem; font-weight:700; color:var(--text-dark); margin:0;">
                        <i class="bi bi-person-badge" style="color:var(--primary);"></i>
                        Lecturers
                    </h5>
                    <span
                        style="background:#f1f5f9; color:var(--text-gray); padding:0.05rem 0.6rem; border-radius:1rem; font-size:0.7rem; font-weight:600;">
                        {{ $lecturers->count() }}
                    </span>
                </div>
                <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                    <div
                        style="display:flex; align-items:center; background:var(--white); border:1px solid rgba(10, 36, 99, 0.1); border-radius:8px; padding:0.15rem 0.5rem; transition:var(--transition);">
                        <i class="bi bi-search" style="color:#9ca3af; font-size:0.7rem;"></i>
                        <input type="text" id="facultySearch" placeholder="Search lecturers..."
                            style="border:none; outline:none; padding:0.25rem 0.4rem; font-size:0.75rem; color:var(--text-dark); background:transparent; width:150px;">
                        <i class="bi bi-x-circle" id="clearFacultySearch"
                            style="color:#9ca3af; font-size:0.7rem; cursor:pointer; display:none;"
                            onclick="document.getElementById('facultySearch').value=''; filterFaculty();"></i>
                    </div>
                    <a href="{{ route('admin.lecturers.create') }}?department={{ $department->id }}"
                        style="background:var(--primary); color:var(--white); border:none; padding:0.25rem 0.8rem; border-radius:6px; font-size:0.7rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.3rem;">
                        <i class="bi bi-plus-circle"></i> Assign Lecturers
                    </a>
                </div>
            </div>

            <div id="facultyGrid"
                style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:1rem;">
                @foreach ($lecturers as $lecturer)
                    @php
                        $coursesCount = $lecturer->courses_count ?? 0;
                        $studentCount = $lecturer->students_count ?? 0;
                        $attendance = $lecturer->avg_attendance ?? 0;

                        if ($attendance >= 75) {
                            $color = '#10b981';
                            $bgColor = 'var(--success-light)';
                            $label = 'Excellent';
                        } elseif ($attendance >= 60) {
                            $color = '#f59e0b';
                            $bgColor = 'var(--warning-light)';
                            $label = 'Good';
                        } else {
                            $color = '#ef4444';
                            $bgColor = 'var(--danger-light)';
                            $label = 'Needs Attention';
                        }

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

                        $searchData = strtolower(
                            $lecturer->name . ' ' . $lecturer->email . ' ' . ($lecturer->department->name ?? ''),
                        );
                    @endphp

                    <div class="faculty-card" data-search="{{ $searchData }}">
                        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem;">
                            <div
                                style="width:48px; height:48px; border-radius:50%; background:linear-gradient(135deg, var(--primary), var(--primary-light)); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.9rem; flex-shrink:0;">
                                {{ $initials }}
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-weight:600; font-size:0.9rem; color:var(--text-dark);">
                                    {{ $lecturer->name }}</div>
                                <div
                                    style="font-size:0.65rem; color:var(--text-gray); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $lecturer->email }}
                                </div>
                            </div>
                            <div
                                style="font-size:0.55rem; font-weight:600; padding:0.1rem 0.5rem; border-radius:1rem; background:{{ $bgColor }}; color:{{ $color }}; white-space:nowrap;">
                                {{ $label }}
                            </div>
                        </div>

                        <div
                            style="display:grid; grid-template-columns:repeat(3, 1fr); gap:0.5rem; padding:0.5rem 0; border-top:1px solid rgba(10, 36, 99, 0.06); border-bottom:1px solid rgba(10, 36, 99, 0.06); margin-bottom:0.75rem;">
                            <div style="text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:var(--text-dark);">
                                    {{ $coursesCount }}</div>
                                <div
                                    style="font-size:0.55rem; color:var(--text-gray); text-transform:uppercase; letter-spacing:0.3px;">
                                    Courses</div>
                            </div>
                            <div style="text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:var(--text-dark);">
                                    {{ $studentCount }}</div>
                                <div
                                    style="font-size:0.55rem; color:var(--text-gray); text-transform:uppercase; letter-spacing:0.3px;">
                                    Students</div>
                            </div>
                            <div style="text-align:center;">
                                <div style="font-size:1.1rem; font-weight:700; color:{{ $color }};">
                                    {{ number_format($attendance, 1) }}%
                                </div>
                                <div
                                    style="font-size:0.55rem; color:var(--text-gray); text-transform:uppercase; letter-spacing:0.3px;">
                                    Attendance</div>
                            </div>
                        </div>

                        <div
                            style="height:4px; background:#f1f5f9; border-radius:4px; overflow:hidden; margin-bottom:0.75rem;">
                            <div
                                style="height:100%; width:{{ $attendance }}%; background:{{ $color }}; border-radius:4px;">
                            </div>
                        </div>

                        <div style="display:flex; gap:0.5rem;">
                            <a href="{{ route('admin.lecturers.show', $lecturer) }}"
                                style="flex:1; text-align:center; background:var(--primary); color:var(--white); border:none; padding:0.3rem 0.75rem; border-radius:6px; font-size:0.75rem; text-decoration:none; transition:var(--transition);">
                                <i class="bi bi-eye"></i> View Profile
                            </a>
                            <a href="{{ route('admin.lecturers.edit', $lecturer) }}"
                                style="flex:1; text-align:center; background:var(--warning-light); color:#92400e; border:none; padding:0.3rem 0.75rem; border-radius:6px; font-size:0.75rem; text-decoration:none; transition:var(--transition);">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="noFacultyResults" style="display:none; text-align:center; padding:2rem; color:var(--text-gray);">
                <i class="bi bi-search" style="font-size:1.5rem; display:block; margin-bottom:0.5rem;"></i>
                <p style="font-size:0.9rem; margin:0;">No faculty members found matching your search</p>
            </div>

            <div style="text-align:center; margin-top:1rem;">
                <a href="{{ route('admin.lecturers.index') }}"
                    style="color:var(--primary); text-decoration:none; font-size:0.75rem; font-weight:500; display:inline-flex; align-items:center; gap:0.3rem; transition:var(--transition);">
                    View All Lecturers <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @else
            <div
                style="text-align:center; padding:2.5rem 1rem; background:var(--white); border-radius:var(--radius); border:1px solid rgba(10, 36, 99, 0.06);">
                <i class="bi bi-person-badge"
                    style="font-size:2rem; color:#d1d5db; display:block; margin-bottom:0.5rem;"></i>
                <h6 style="color:var(--text-dark); margin:0; font-size:0.9rem;">No Lecturers Assigned</h6>
                <p style="color:var(--text-gray); font-size:0.8rem; margin:0.2rem 0 0.8rem;">
                    This department doesn't have any lecturers assigned yet.
                </p>
                <a href="{{ route('admin.lecturers.create') }}?department={{ $department->id }}"
                    style="background:var(--primary); color:var(--white); border:none; padding:0.3rem 1rem; border-radius:6px; font-size:0.75rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem;">
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

            const noResults = document.getElementById('noFacultyResults');
            if (visibleCount === 0 && searchTerm !== '') {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('facultySearch');
            if (searchInput) {
                searchInput.addEventListener('keyup', filterFaculty);
                searchInput.addEventListener('search', filterFaculty);
            }
        });
    </script>

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
