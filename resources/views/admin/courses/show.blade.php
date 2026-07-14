@extends('layouts.app')

@section('title', $course->course_code . ' - ' . $course->course_name)
@section('page-title', $course->course_code . ' - ' . $course->course_name)
@section('welcome-text', $department->name . ' • ' . $course->semester ?? 'N/A')

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
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link-course {
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

        .back-link-course:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
        }

        .action-bar-course {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-action-course {
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

        .btn-action-course-edit {
            background: var(--primary-light);
            color: whitesmoke;
        }

        .btn-action-course-edit:hover {
            background: #fde68a;
            transform: translateY(-2px);
        }

        .course-detail-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .course-detail-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .course-detail-card .header {
            padding: 1.25rem 1.5rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .course-detail-card .header .title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .course-detail-card .header .title .code {
            background: rgba(212, 160, 23, 0.12);
            color: var(--primary);
            padding: 0.1rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            margin-right: 0.5rem;
        }

        .course-detail-card .header .badge {
            padding: 0.2rem 0.8rem;
            border-radius: 1rem;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .course-detail-card .header .badge.active {
            background: var(--success-light);
            color: #166534;
        }

        .course-detail-card .header .badge.inactive {
            background: var(--danger-light);
            color: var(--danger);
        }

        .course-detail-card .body {
            padding: 1.25rem 1.5rem;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .course-detail-card .body .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .course-detail-card .body .info-item .label {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--text-gray);
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .course-detail-card .body .info-item .value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .stats-grid-course {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-course {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
        }

        .stat-course:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .stat-course .number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1.2;
        }

        .stat-course .number.danger {
            color: var(--danger);
        }

        .stat-course .number.success {
            color: var(--success);
        }

        .stat-course .label {
            font-size: 0.6rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .students-table-wrap {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .students-table-wrap:hover {
            box-shadow: var(--shadow-hover);
        }

        .students-table-wrap .header {
            padding: 0.75rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .students-table-wrap .header i {
            color: var(--primary);
            margin-right: 0.4rem;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .students-table thead th {
            padding: 0.5rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-gray);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
        }

        .students-table tbody td {
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .students-table tbody tr:hover {
            background: #fafbfc;
        }

        .badge-risk {
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .badge-risk.low {
            background: var(--success-light);
            color: #166534;
        }

        .badge-risk.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .badge-risk.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .attendance-pill {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            display: inline-block;
        }

        .attendance-pill.high {
            background: var(--success-light);
            color: #166534;
        }

        .attendance-pill.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .attendance-pill.low {
            background: var(--danger-light);
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .course-detail-card .body {
                grid-template-columns: 1fr 1fr;
            }

            .stats-grid-course {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-bar-course {
                justify-content: flex-start;
            }

            .students-table {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .course-detail-card .body {
                grid-template-columns: 1fr;
            }

            .stats-grid-course {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-course {
                padding: 0.75rem;
            }

            .stat-course .number {
                font-size: 1.2rem;
            }
        }
    </style>

    <a href="{{ route('admin.departments.courses.index', $department) }}" class="back-link-course">
        <i class="bi bi-arrow-left"></i> Back to Courses
    </a>

    <div class="action-bar-course">
        <a href="{{ route('admin.departments.courses.edit', [$department, $course]) }}"
            class="btn-action-course btn-action-course-edit">
            <i class="bi bi-pencil"></i> Edit Course
        </a>
    </div>

    <div class="course-detail-card">
        <div class="header">
            <div class="title">
                <span class="code">{{ $course->course_code }}</span>
                {{ $course->course_name }}
            </div>
            <span class="badge {{ $course->is_active ? 'active' : 'inactive' }}">
                {{ $course->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="body">
            <div class="info-item">
                <span class="label">Department</span>
                <span class="value">{{ $department->name }} ({{ $department->code }})</span>
            </div>
            <div class="info-item">
                <span class="label">Lecturer</span>
                <span class="value">{{ $course->lecturer->name ?? ($course->lecturer_name ?? 'Not Assigned') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Credits</span>
                <span class="value">{{ $course->credits }}</span>
            </div>
            <div class="info-item">
                <span class="label">Year</span>
                <span class="value">{{ $course->year ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Semester</span>
                <span class="value">{{ $course->semester ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Room</span>
                <span class="value">{{ $course->room ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="label">Academic Year</span>
                <span class="value">{{ $course->academic_year ?? 'N/A' }}</span>
            </div>
            <div class="info-item" style="grid-column: span 2;">
                <span class="label">Schedule</span>
                <span class="value">
                    @if ($course->schedule_day)
                        {{ $course->schedule_day }} at {{ $course->schedule_time ?? 'TBD' }}
                    @else
                        Not scheduled
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="stats-grid-course">
        <div class="stat-course">
            <div class="number">{{ $students->total() }}</div>
            <div class="label">Total Students</div>
        </div>
        <div class="stat-course">
            <div class="number success">
                {{ $students->filter(function ($s) {return ($s->pivot->attendance_percentage ?? 0) >= 75;})->count() }}
            </div>
            <div class="label">Eligible</div>
        </div>
        <div class="stat-course">
            <div class="number danger">
                {{ $students->filter(function ($s) {return ($s->pivot->attendance_percentage ?? 0) < 60;})->count() }}
            </div>
            <div class="label">At Risk</div>
        </div>
        <div class="stat-course">
            <div class="number">
                {{ number_format($students->avg('pivot.attendance_percentage') ?? 0, 1) }}%
            </div>
            <div class="label">Avg Attendance</div>
        </div>
    </div>

    <div class="students-table-wrap">
        <div class="header">
            <span><i class="bi bi-people"></i> Enrolled Students</span>
            <span style="font-size:0.7rem; color:var(--text-gray); font-weight:400;">
                {{ $students->total() }} students enrolled
            </span>
        </div>
        <div style="overflow-x:auto;">
            <table class="students-table">
                <thead>
                    <tr>
                        <th style="min-width:80px;">Student ID</th>
                        <th style="min-width:130px;">Name</th>
                        <th style="min-width:150px;">Email</th>
                        <th style="text-align:center; min-width:80px;">Attendance</th>
                        <th style="text-align:center; min-width:70px;">Risk</th>
                        <th style="text-align:center; min-width:80px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $attendance = $student->pivot->attendance_percentage ?? 0;
                            $risk = $attendance >= 75 ? 'Low' : ($attendance >= 60 ? 'Medium' : 'High');
                            $riskClass = strtolower($risk);
                            $attendanceClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                            $eligibility = $student->pivot->eligibility_status ?? 'unknown';
                            $statusColors = [
                                'eligible' => ['bg' => 'var(--success-light)', 'color' => '#166534'],
                                'warning' => ['bg' => 'var(--warning-light)', 'color' => '#92400e'],
                                'not_eligible' => ['bg' => 'var(--danger-light)', 'color' => '#991b1b'],
                            ];
                            $statusColor = $statusColors[$eligibility] ?? [
                                'bg' => '#f3f4f6',
                                'color' => 'var(--text-gray)',
                            ];
                        @endphp
                        <tr>
                            <td>
                                <span
                                    style="background:#f1f5f9; color:var(--text-gray); padding:0.1rem 0.5rem; border-radius:6px; font-size:0.65rem; font-weight:600; font-family:monospace;">
                                    {{ $student->student_id ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="font-weight:500; color:var(--text-dark);">{{ $student->name }}</td>
                            <td style="color:var(--text-gray); font-size:0.75rem;">{{ $student->email }}</td>
                            <td style="text-align:center;">
                                <span class="attendance-pill {{ $attendanceClass }}">
                                    {{ number_format($attendance, 1) }}%
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span class="badge-risk {{ $riskClass }}">{{ $risk }}</span>
                            </td>
                            <td style="text-align:center;">
                                <span
                                    style="padding:0.1rem 0.6rem; border-radius:1rem; font-size:0.6rem; font-weight:600; background:{{ $statusColor['bg'] }}; color:{{ $statusColor['color'] }};">
                                    {{ ucfirst($eligibility) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:2rem; color:var(--text-gray);">
                                <i class="bi bi-people" style="font-size:1.5rem; display:block; margin-bottom:0.5rem;"></i>
                                No students enrolled in this course yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->hasPages())
            <div
                style="padding:0.75rem 1.25rem; border-top:1px solid rgba(10, 36, 99, 0.06); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem; background:#fafbfc;">
                <div style="font-size:0.75rem; color:var(--text-gray);">
                    Showing <strong>{{ $students->firstItem() ?? 0 }}</strong> to
                    <strong>{{ $students->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $students->total() }}</strong> students
                </div>
                <div>
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
@endsection
