@extends('layouts.app')

@section('title', 'Attendance Monitoring')
@section('role', 'Lecturer')
@section('page-title', 'Attendance Monitoring')
@section('welcome-text', 'Track student attendance across your courses')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        /* ============================================================
                               DESIGN SYSTEM
                               ============================================================ */
        :root {
            --primary: #0A2463;
            --primary-light: #1E3A8A;
            --primary-dark: #061840;
            --success: #10b981;
            --success-bg: #d1fae5;
            --warning: #f59e0b;
            --warning-bg: #fef3c7;
            --danger: #ef4444;
            --danger-bg: #fee2e2;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 16px rgba(10, 36, 99, 0.08);
            --shadow-lg: 0 8px 32px rgba(10, 36, 99, 0.12);
            --radius: 10px;
            --radius-sm: 6px;
            --transition: all 0.2s ease;
        }

        /* ============================================================
                               PERIOD CONTROLS
                               ============================================================ */
        .period-controls {
            background: white;
            border-radius: var(--radius);
            padding: 14px 20px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px 16px;
        }

        .period-tabs {
            display: flex;
            gap: 4px;
            background: var(--gray-100);
            padding: 4px;
            border-radius: var(--radius-sm);
            flex-wrap: wrap;
        }

        .period-tabs .btn-tab {
            padding: 6px 16px;
            border-radius: var(--radius-sm);
            border: none;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            background: transparent;
            color: var(--gray-600);
            text-decoration: none;
            white-space: nowrap;
        }

        .period-tabs .btn-tab:hover {
            background: rgba(255, 255, 255, 0.6);
            color: var(--gray-800);
        }

        .period-tabs .btn-tab.active {
            background: white;
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .period-tabs .btn-tab i {
            margin-right: 4px;
        }

        .period-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
            flex-wrap: wrap;
        }

        .period-nav .btn-nav {
            padding: 4px 12px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
            background: white;
            color: var(--gray-600);
            text-decoration: none;
            font-size: 13px;
            transition: var(--transition);
        }

        .period-nav .btn-nav:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--gray-50);
        }

        .period-nav .period-label {
            font-weight: 600;
            font-size: 14px;
            color: var(--gray-800);
            min-width: 180px;
            text-align: center;
        }

        .course-filter {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .course-filter select {
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
            font-size: 13px;
            background: white;
            color: var(--gray-700);
            outline: none;
            cursor: pointer;
            transition: var(--transition);
            min-width: 140px;
        }

        .course-filter select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .course-filter .clear-btn {
            padding: 4px 8px;
            border-radius: 50%;
            background: var(--gray-200);
            color: var(--gray-600);
            text-decoration: none;
            font-size: 14px;
            line-height: 1;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        .course-filter .clear-btn:hover {
            background: var(--danger);
            color: white;
        }

        .custom-range-toggle {
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
            background: white;
            color: var(--gray-600);
            font-size: 13px;
            cursor: pointer;
            transition: var(--transition);
        }

        .custom-range-toggle:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .custom-range {
            display: none;
            width: 100%;
            padding: 12px 16px;
            background: var(--gray-50);
            border-radius: var(--radius-sm);
            margin-top: 8px;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .custom-range.show {
            display: flex;
        }

        .custom-range input[type="date"] {
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
            font-size: 13px;
            background: white;
        }

        .custom-range .btn-apply {
            padding: 6px 18px;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--primary);
            color: white;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: var(--transition);
        }

        .custom-range .btn-apply:hover {
            background: var(--primary-dark);
        }

        .custom-range .btn-close-range {
            background: none;
            border: none;
            color: var(--gray-400);
            font-size: 18px;
            cursor: pointer;
            padding: 0 4px;
        }

        /* ============================================================
                               STATS ROW
                               ============================================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 14px 18px;
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .stat-card .number {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-card .number.primary {
            color: var(--primary);
        }

        .stat-card .number.green {
            color: var(--success);
        }

        .stat-card .number.yellow {
            color: var(--warning);
        }

        .stat-card .number.red {
            color: var(--danger);
        }

        .stat-card .label {
            font-size: 11px;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 600;
            margin-top: 2px;
        }

        .stat-card .sub {
            font-size: 11px;
            color: var(--gray-400);
            margin-top: 2px;
        }

        /* ============================================================
                               COURSE CARDS
                               ============================================================ */
        .course-card {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            overflow: hidden;
            transition: var(--transition);
        }

        .course-card:hover {
            box-shadow: var(--shadow-md);
        }

        .course-header {
            padding: 12px 20px;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px 12px;
        }

        .course-header .title {
            font-weight: 600;
            font-size: 15px;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .course-header .title .code {
            color: var(--gray-500);
            font-weight: 400;
            font-size: 13px;
        }

        .course-header .title .count {
            font-weight: 400;
            color: var(--gray-500);
            font-size: 12px;
            background: var(--gray-200);
            padding: 0 12px;
            border-radius: 20px;
        }

        .course-header .title .avg-badge {
            font-size: 13px;
            font-weight: 600;
            padding: 2px 12px;
            border-radius: 20px;
        }

        .avg-badge.high {
            background: var(--success-bg);
            color: #065f46;
        }

        .avg-badge.medium {
            background: var(--warning-bg);
            color: #92400e;
        }

        .avg-badge.low {
            background: var(--danger-bg);
            color: #991b1b;
        }

        .course-header .actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center;
        }

        .course-header .actions .badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-eligible {
            background: var(--success-bg);
            color: #065f46;
        }

        .badge-warning {
            background: var(--warning-bg);
            color: #92400e;
        }

        .badge-atrisk {
            background: var(--danger-bg);
            color: #991b1b;
        }

        .badge-total {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        /* ============================================================
                               STUDENT TABLE - FULLY RESPONSIVE
                               ============================================================ */
        .table-wrap {
            overflow-x: auto;
            padding: 0;
            -webkit-overflow-scrolling: touch;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: fixed;
            min-width: 0;
        }

        /* Column widths – fluid (roll call column removed) */
        .students-table .col-index {
            width: 4%;
        }

        .students-table .col-student {
            width: 22%;
        }

        .students-table .col-id {
            width: 12%;
        }

        .students-table .col-attendance {
            width: 30%;
        }

        .students-table .col-eligibility {
            width: 16%;
        }

        .students-table .col-risk {
            width: 14%;
        }

        .students-table thead {
            background: var(--gray-50);
        }

        .students-table th {
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--gray-500);
            letter-spacing: 0.4px;
            border-bottom: 2px solid var(--gray-200);
            white-space: nowrap;
        }

        .students-table th.sortable {
            cursor: pointer;
            user-select: none;
            position: relative;
            padding-right: 28px;
        }

        .students-table th .sort-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: var(--gray-400);
        }

        .students-table td {
            padding: 8px 12px;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .students-table td .student-name {
            font-weight: 500;
            color: var(--gray-800);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .students-table td .student-email {
            font-size: 11px;
            color: var(--gray-400);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .students-table td .student-id {
            font-size: 12px;
            color: var(--gray-500);
            font-family: monospace;
            background: var(--gray-100);
            padding: 1px 8px;
            border-radius: 4px;
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .students-table tbody tr {
            transition: var(--transition);
        }

        .students-table tbody tr:hover {
            background: var(--gray-50);
        }

        .students-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ============================================================
                               ATTENDANCE CELL
                               ============================================================ */
        .attendance-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
        }

        .attendance-cell .value {
            font-weight: 700;
            font-size: 14px;
            min-width: 48px;
            flex-shrink: 0;
        }

        .attendance-cell .value.high {
            color: var(--success);
        }

        .attendance-cell .value.medium {
            color: var(--warning);
        }

        .attendance-cell .value.low {
            color: var(--danger);
        }

        .progress-track {
            flex: 1;
            min-width: 40px;
            height: 7px;
            background: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-track .fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .progress-track .fill.high {
            background: var(--success);
        }

        .progress-track .fill.medium {
            background: var(--warning);
        }

        .progress-track .fill.low {
            background: var(--danger);
        }

        .elig-badge {
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .elig-eligible {
            background: var(--success-bg);
            color: #065f46;
        }

        .elig-warning {
            background: var(--warning-bg);
            color: #92400e;
        }

        .elig-not_eligible {
            background: var(--danger-bg);
            color: #991b1b;
        }

        .elig-not-evaluated {
            background: var(--gray-100);
            color: var(--gray-500);
        }

        .risk-badge {
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .risk-low {
            background: var(--success-bg);
            color: #065f46;
        }

        .risk-medium {
            background: var(--warning-bg);
            color: #92400e;
        }

        .risk-high {
            background: var(--danger-bg);
            color: #991b1b;
        }

        /* ============================================================
                               EMPTY STATE
                               ============================================================ */
        .empty-state {
            text-align: center;
            padding: 32px 20px;
            color: var(--gray-500);
        }

        .empty-state .icon {
            font-size: 32px;
            color: var(--gray-300);
            display: block;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 13px;
            margin: 0;
        }

        /* ============================================================
                               RESPONSIVE
                               ============================================================ */
        @media (max-width: 1200px) {
            .students-table .col-student {
                width: 20%;
            }

            .students-table .col-attendance {
                width: 28%;
            }
        }

        @media (max-width: 992px) {
            .period-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .period-nav {
                margin-left: 0;
                justify-content: center;
            }

            .course-filter {
                width: 100%;
            }

            .course-filter select {
                width: 100%;
            }

            .students-table .col-student {
                width: 18%;
            }

            .students-table .col-id {
                width: 10%;
            }

            .students-table .col-attendance {
                width: 26%;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .course-header {
                flex-direction: column;
                align-items: stretch;
            }

            .course-header .actions {
                justify-content: flex-start;
            }

            /* Table becomes scrollable horizontally on small screens */
            .table-wrap {
                overflow-x: auto;
            }

            .students-table {
                min-width: 680px;
                font-size: 12px;
            }

            .students-table th,
            .students-table td {
                padding: 6px 10px;
            }

            .students-table .col-student {
                width: 20%;
            }

            .students-table .col-attendance {
                width: 28%;
            }

            .progress-track {
                min-width: 30px;
            }

            .period-nav .period-label {
                min-width: auto;
                font-size: 13px;
            }

            .period-tabs .btn-tab {
                font-size: 12px;
                padding: 4px 12px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }

            .stat-card {
                padding: 10px 12px;
            }

            .stat-card .number {
                font-size: 20px;
            }

            .period-tabs .btn-tab {
                flex: 1;
                text-align: center;
                font-size: 11px;
                padding: 4px 8px;
            }

            .period-nav {
                flex-wrap: wrap;
            }

            .students-table {
                min-width: 580px;
                font-size: 11px;
            }

            .students-table th,
            .students-table td {
                padding: 4px 8px;
            }

            .attendance-cell .value {
                font-size: 12px;
                min-width: 38px;
            }

            .progress-track {
                min-width: 20px;
                height: 5px;
            }

            .students-table .col-student {
                width: 22%;
            }

            .students-table .col-id {
                width: 8%;
            }

            .students-table .col-attendance {
                width: 26%;
            }

            .students-table .col-eligibility {
                width: 12%;
            }

            .students-table .col-risk {
                width: 10%;
            }
        }

        @media (max-width: 380px) {
            .students-table {
                min-width: 480px;
                font-size: 10px;
            }

            .students-table th,
            .students-table td {
                padding: 3px 6px;
            }

            .attendance-cell .value {
                font-size: 11px;
                min-width: 32px;
            }

            .elig-badge,
            .risk-badge {
                font-size: 9px;
                padding: 1px 6px;
            }
        }

        @media print {
            .period-controls {
                display: none;
            }

            .course-card {
                border: 1px solid #ddd;
                box-shadow: none;
                break-inside: avoid;
            }

            .students-table {
                font-size: 10px;
            }

            .students-table th {
                background: #f5f5f5 !important;
            }

            .progress-track .fill {
                background: #666 !important;
            }
        }
    </style>

    {{-- ============================================================
    PERIOD CONTROLS (NEW ORDER)
    ============================================================ --}}
    <div class="period-controls">
        <div class="period-tabs">
            {{-- 1. This Week (DEFAULT) --}}
            <a href="{{ route('lecturer.students', ['period' => 'weekly', 'offset' => 0]) }}"
                class="btn-tab {{ $period == 'weekly' ? 'active' : '' }}">
                <i class="bi bi-calendar-week"></i> This Week
            </a>

            {{-- 2. This Month --}}
            <a href="{{ route('lecturer.students', ['period' => 'monthly', 'offset' => 0]) }}"
                class="btn-tab {{ $period == 'monthly' ? 'active' : '' }}">
                <i class="bi bi-calendar-month"></i> This Month
            </a>

            {{-- 3. Custom Range --}}
            <button class="btn-tab" onclick="toggleCustomRange()">
                <i class="bi bi-calendar-range"></i> Custom
            </button>

            {{-- 4. Semester (formerly Overall) --}}
            <a href="{{ route('lecturer.students', ['period' => 'overall']) }}"
                class="btn-tab {{ $period == 'overall' ? 'active' : '' }}">
                <i class="bi bi-calendar-range"></i> Semester
            </a>
        </div>

        <div class="period-nav">
            @if ($period != 'overall' && $period != 'custom')
                <a href="{{ route('lecturer.students', ['period' => $period, 'offset' => $offset - 1]) }}" class="btn-nav">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            <span class="period-label">
                @if ($period == 'overall')
                    📊 Semester
                @elseif($period == 'custom')
                    📅 Custom Range
                @else
                    {{ $periodLabel ?? $startDate->format('M d, Y') . ' – ' . $endDate->format('M d, Y') }}
                @endif
            </span>

            @if ($period != 'overall' && $period != 'custom')
                <a href="{{ route('lecturer.students', ['period' => $period, 'offset' => $offset + 1]) }}" class="btn-nav">
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif
        </div>

        {{-- ============================================================
        COURSE FILTER – USING allCourses (UNFILTERED) FOR DROPDOWN
        ============================================================ --}}
        <div class="course-filter">
            <form method="GET" id="courseFilterForm" style="display:flex; align-items:center; gap:4px;">
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="offset" value="{{ $offset }}">
                <select name="course_id" onchange="this.form.submit()">
                    <option value=""> All Courses</option>
                    @foreach ($allCourses as $course)
                        <option value="{{ $course->id }}" {{ ($courseId ?? '') == $course->id ? 'selected' : '' }}>
                            {{ $course->course_code }}
                        </option>
                    @endforeach
                </select>
                @if (!empty($courseId))
                    <a href="{{ route('lecturer.students', ['period' => $period, 'offset' => $offset]) }}"
                        class="clear-btn" title="Clear filter">
                        ✕
                    </a>
                @endif
            </form>
        </div>

        {{-- Custom Range --}}
        <div class="custom-range" id="customRange">
            <form method="GET" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <input type="hidden" name="period" value="custom">
                <input type="hidden" name="course_id" value="{{ $courseId ?? '' }}">
                <input type="date" name="start_date" value="{{ request('start_date') }}">
                <span style="color:var(--gray-400);">→</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}">
                <button type="submit" class="btn-apply">Apply</button>
                <button type="button" class="btn-close-range" onclick="toggleCustomRange()">✕</button>
            </form>
        </div>
    </div>

    {{-- ============================================================
    STATS
    ============================================================ --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="number primary">{{ $totalStudents }}</div>
            <div class="label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="number green">{{ $eligibleCount }}</div>
            <div class="label">Eligible</div>
            <div class="sub">{{ $totalStudents > 0 ? round(($eligibleCount / $totalStudents) * 100) : 0 }}% of total
            </div>
        </div>
        <div class="stat-card">
            <div class="number yellow">{{ $warningCount }}</div>
            <div class="label"> Warning</div>
            <div class="sub">{{ $totalStudents > 0 ? round(($warningCount / $totalStudents) * 100) : 0 }}% of total
            </div>
        </div>
        <div class="stat-card">
            <div class="number red">{{ $atRiskCount }}</div>
            <div class="label"> At Risk</div>
            <div class="sub">{{ $totalStudents > 0 ? round(($atRiskCount / $totalStudents) * 100) : 0 }}% of total</div>
        </div>
        <div class="stat-card">
            <div class="number primary">{{ $avgAttendance }}%</div>
            <div class="label">Avg Attendance</div>
            <div class="sub">{{ $avgAttendance >= 75 ? '✅ Good' : ($avgAttendance >= 60 ? '⚠️ Moderate' : '🚨 Low') }}
            </div>
        </div>
    </div>

    {{-- ============================================================
    COURSE CARDS
    ============================================================ --}}
    @forelse ($courses as $course)
        @php
            $courseStudents = $students->filter(function ($student) use ($course) {
                return $student->enrollments->contains('course_id', $course->id);
            });
            $total = $courseStudents->count();
            $eligible = $courseStudents->where('status', 'Eligible')->count();
            $atRisk = $courseStudents->where('status', 'At Risk')->count();
            $avgAtt = $total > 0 ? round($courseStudents->avg('attendance_percentage'), 1) : 0;

            $avgClass = $avgAtt >= 75 ? 'high' : ($avgAtt >= 60 ? 'medium' : 'low');
        @endphp
        <div class="course-card">
            <div class="course-header">
                <div class="title">
                    {{ $course->course_code }}
                    <span class="code">{{ $course->course_name }}</span>
                    <span class="count">{{ $total }} students</span>
                    @if ($total > 0)
                        <span class="avg-badge {{ $avgClass }}">{{ $avgAtt }}% avg</span>
                    @endif
                </div>
                <div class="actions">
                    @if ($total > 0)
                        @if ($eligible > 0)
                            <span class="badge badge-eligible">✅ {{ $eligible }}</span>
                        @endif
                        @if ($atRisk > 0)
                            <span class="badge badge-atrisk">🚨 {{ $atRisk }}</span>
                        @endif
                    @endif
                </div>
            </div>

            @if ($courseStudents->isNotEmpty())
                <div class="table-wrap">
                    <table class="students-table" id="table-{{ $course->id }}">
                        <thead>
                            <tr>
                                <th class="col-index">#</th>
                                <th class="col-student sortable" data-sort="string">Student</th>
                                <th class="col-id sortable" data-sort="string">ID</th>
                                <th class="col-attendance sortable" data-sort="number">Attendance</th>
                                {{-- Roll Call column removed --}}
                                <th class="col-eligibility sortable" data-sort="string">Eligibility</th>
                                <th class="col-risk sortable" data-sort="string">Risk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courseStudents as $index => $student)
                                @php
                                    $att = $student->attendance_percentage ?? 0;
                                    $status = $student->status ?? 'Not Evaluated';
                                    $risk = $student->risk_level ?? 'Low';
                                    $hasEval = ($student->total_courses ?? 0) > 0;

                                    $attClass = $att >= 75 ? 'high' : ($att >= 60 ? 'medium' : 'low');
                                    $eligClass = $hasEval
                                        ? ($status == 'Eligible'
                                            ? 'eligible'
                                            : ($status == 'Warning'
                                                ? 'warning'
                                                : 'not_eligible'))
                                        : 'not-evaluated';
                                    $riskClass = strtolower($risk);
                                @endphp
                                <tr>
                                    <td class="col-index">{{ $index + 1 }}</td>
                                    <td class="col-student">
                                        <div class="student-name" title="{{ $student->name }}">{{ $student->name }}
                                        </div>
                                        <div class="student-email" title="{{ $student->email }}">{{ $student->email }}
                                        </div>
                                    </td>
                                    <td class="col-id">
                                        @if ($student->student_id)
                                            <span class="student-id"
                                                title="{{ $student->student_id }}">{{ $student->student_id }}</span>
                                        @else
                                            <span style="color:var(--gray-400);font-size:12px;">—</span>
                                        @endif
                                    </td>
                                    <td class="col-attendance">
                                        <div class="attendance-cell">
                                            <span class="value {{ $attClass }}">{{ number_format($att, 1) }}%</span>
                                            <div class="progress-track">
                                                <div class="fill {{ $attClass }}"
                                                    style="width:{{ $att }}%;"></div>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Roll Call data cell removed --}}
                                    <td class="col-eligibility">
                                        @if ($hasEval)
                                            <span class="elig-badge elig-{{ $eligClass }}">
                                                {{ $status }}
                                            </span>
                                        @else
                                            <span class="elig-badge elig-not-evaluated">—</span>
                                        @endif
                                    </td>
                                    <td class="col-risk">
                                        @if ($hasEval)
                                            <span class="risk-badge risk-{{ $riskClass }}">
                                                {{ $risk }}
                                            </span>
                                        @else
                                            <span style="color:var(--gray-400);font-size:12px;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <span class="icon">👤</span>
                    <p>No students enrolled in this course yet</p>
                </div>
            @endif
        </div>
    @empty
        <div class="empty-state" style="padding:48px;">
            <span class="icon" style="font-size:48px;"><i class="bi bi-inbox"></i></span>
            <h5 style="color:var(--gray-700);margin-bottom:4px;">No Courses Assigned</h5>
            <p>You are not assigned to any courses at the moment.</p>
        </div>
    @endforelse

    {{-- ============================================================
    SCRIPTS
    ============================================================ --}}
    @push('scripts')
        <script>
            // Toggle custom range visibility
            function toggleCustomRange() {
                const el = document.getElementById('customRange');
                el.classList.toggle('show');
            }

            // Auto-show custom range if date parameters are present
            @if (request()->has('start_date') || request()->has('end_date'))
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('customRange').classList.add('show');
                });
            @endif

            // Table sorting
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.students-table').forEach(function(table) {
                    const headers = table.querySelectorAll('th.sortable');
                    let sortOrder = {};

                    headers.forEach(function(header, index) {
                        header.style.cursor = 'pointer';
                        header.style.userSelect = 'none';
                        header.style.position = 'relative';
                        header.style.paddingRight = '28px';

                        const sortIcon = document.createElement('span');
                        sortIcon.className = 'sort-icon';
                        sortIcon.textContent = '⇅';
                        header.appendChild(sortIcon);

                        header.addEventListener('click', function() {
                            const tbody = table.querySelector('tbody');
                            const rows = Array.from(tbody.querySelectorAll('tr'));
                            const dataType = header.dataset.sort || 'string';

                            // Toggle sort order
                            if (!sortOrder[index]) {
                                sortOrder[index] = 'asc';
                            } else if (sortOrder[index] === 'asc') {
                                sortOrder[index] = 'desc';
                            } else {
                                sortOrder[index] = 'asc';
                            }
                            const order = sortOrder[index];

                            // Update sort icon
                            const icon = header.querySelector('.sort-icon');
                            if (icon) {
                                if (order === 'asc') {
                                    icon.textContent = '▲';
                                    icon.style.color = '#0A2463';
                                } else {
                                    icon.textContent = '▼';
                                    icon.style.color = '#0A2463';
                                }
                            }

                            // Reset other column icons
                            headers.forEach(function(otherHeader, otherIndex) {
                                if (otherIndex !== index) {
                                    const otherIcon = otherHeader.querySelector(
                                        '.sort-icon');
                                    if (otherIcon) {
                                        otherIcon.textContent = '⇅';
                                        otherIcon.style.color = '#94a3b8';
                                    }
                                    sortOrder[otherIndex] = null;
                                }
                            });

                            // Sort rows
                            rows.sort(function(a, b) {
                                const aCells = a.querySelectorAll('td');
                                const bCells = b.querySelectorAll('td');
                                let aVal = aCells[index] ? aCells[index].textContent
                                    .trim() : '';
                                let bVal = bCells[index] ? bCells[index].textContent
                                    .trim() : '';

                                if (dataType === 'number') {
                                    aVal = parseFloat(aVal.replace(/%/g, '').replace(
                                        /\s/g, '')) || 0;
                                    bVal = parseFloat(bVal.replace(/%/g, '').replace(
                                        /\s/g, '')) || 0;
                                    return order === 'asc' ? aVal - bVal : bVal - aVal;
                                } else {
                                    aVal = aVal.toLowerCase();
                                    bVal = bVal.toLowerCase();
                                    if (order === 'asc') {
                                        return aVal.localeCompare(bVal);
                                    } else {
                                        return bVal.localeCompare(aVal);
                                    }
                                }
                            });

                            rows.forEach(function(row) {
                                tbody.appendChild(row);
                            });
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
