@extends('layouts.app')

@section('title', 'My Attendance')
@section('role', 'Student')
@section('page-title', 'My Attendance')
@section('welcome-text', 'Track your attendance across all courses')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --bg-main: #f4f7fc;
            --card-bg: #ffffff;
            --shadow: 0 2px 12px rgba(10, 36, 99, 0.07);
            --radius: 12px;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border: #e9edf4;
            --green: #10b981;
            --green-bg: #d1fae5;
            --amber: #f59e0b;
            --amber-bg: #fef3c7;
            --red: #ef4444;
            --red-bg: #fee2e2;
        }

        /* ── Stats Row ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 0.9rem 1.2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-card .left .number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .stat-card .left .label {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stat-card .icon {
            font-size: 1.6rem;
            opacity: 0.6;
        }

        .stat-card.present .icon {
            color: var(--green);
        }

        .stat-card.late .icon {
            color: var(--amber);
        }

        .stat-card.absent .icon {
            color: var(--red);
        }

        /* ── Course Table ── */
        .course-table-wrap {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .course-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .course-table thead {
            background: #f8fafc;
        }

        .course-table th {
            padding: 0.6rem 1rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            border-bottom: 2px solid var(--border);
        }

        .course-table th:first-child {
            width: 32%;
        }

        .course-table th:nth-child(2) {
            width: 30%;
        }

        .course-table th:nth-child(3) {
            width: 14%;
            text-align: center;
        }

        .course-table th:nth-child(4) {
            width: 12%;
            text-align: center;
        }

        .course-table th:last-child {
            width: 12%;
            text-align: center;
        }

        .course-table td {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid var(--border);
            font-size: 0.85rem;
            color: var(--text-dark);
            vertical-align: middle;
        }

        .course-table tbody tr {
            transition: background 0.15s;
        }

        .course-table tbody tr:hover {
            background: #f8fafc;
        }

        .course-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Course column */
        .course-table .course-name {
            font-weight: 600;
            font-size: 0.85rem;
            display: block;
        }

        .course-table .course-code {
            font-size: 0.7rem;
            color: var(--text-muted);
            display: block;
            margin-top: 0.05rem;
        }

        /* Attendance column */
        .course-table .attendance-cell {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .course-table .attendance-cell .percent {
            font-weight: 700;
            font-size: 0.95rem;
            min-width: 50px;
        }

        .course-table .attendance-cell .bar-track {
            flex: 1;
            height: 6px;
            background: #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
            min-width: 50px;
        }

        .course-table .attendance-cell .bar-fill {
            height: 100%;
            border-radius: 6px;
            transition: width 0.6s;
        }

        /* Roll Call column */
        .course-table .rollcall {
            text-align: center;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .course-table .rollcall span {
            font-weight: 400;
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        /* Eligibility & Risk columns */
        .course-table td:not(:first-child):not(:nth-child(2)) {
            text-align: center;
        }

        .badge {
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            display: inline-block;
        }

        .badge-eligible {
            background: var(--green-bg);
            color: #065f46;
        }

        .badge-warning {
            background: var(--amber-bg);
            color: #92400e;
        }

        .badge-not_eligible {
            background: var(--red-bg);
            color: #991b1b;
        }

        .badge-low {
            background: var(--green-bg);
            color: #065f46;
        }

        .badge-medium {
            background: var(--amber-bg);
            color: #92400e;
        }

        .badge-high {
            background: var(--red-bg);
            color: #991b1b;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 2rem;
            display: block;
            margin-bottom: 0.5rem;
            color: #d1d5db;
        }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .course-table-wrap {
                overflow-x: auto;
            }

            .course-table {
                min-width: 600px;
            }

            .course-table th,
            .course-table td {
                padding: 0.5rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Stats --}}
    @php
        $overall = $overallAttendance ?? 0;
        $present = $presentCount ?? 0;
        $late = $lateCount ?? 0;
        $absent = $absentCount ?? 0;
    @endphp

    <div class="stats-grid">
        <div class="stat-card">
            <div class="left">
                <div class="number">{{ $overall }}%</div>
                <div class="label">Overall Attendance</div>
            </div>
            <div class="icon"><i class="bi bi-graph-up-arrow"></i></div>
        </div>
        <div class="stat-card present">
            <div class="left">
                <div class="number">{{ $present }}</div>
                <div class="label">✅ Present</div>
            </div>
            <div class="icon"><i class="bi bi-check-circle"></i></div>
        </div>
        <div class="stat-card late">
            <div class="left">
                <div class="number">{{ $late }}</div>
                <div class="label">⏰ Late</div>
            </div>
            <div class="icon"><i class="bi bi-clock"></i></div>
        </div>
        <div class="stat-card absent">
            <div class="left">
                <div class="number">{{ $absent }}</div>
                <div class="label">❌ Absent</div>
            </div>
            <div class="icon"><i class="bi bi-x-circle"></i></div>
        </div>
    </div>

    {{-- Course Table --}}
    <div class="course-table-wrap">
        <table class="course-table">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Attendance</th>
                    <th style="text-align:center;">Roll Call</th>
                    <th style="text-align:center;">Eligibility</th>
                    <th style="text-align:center;">Risk</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($courseData) && count($courseData) > 0)
                    @foreach ($courseData as $data)
                        @php
                            $att = $data['attendance'] ?? 0;
                            $color = $att >= 75 ? 'var(--green)' : ($att >= 60 ? 'var(--amber)' : 'var(--red)');
                            $elig = $data['eligibility'] ?? 'not_eligible';
                            $risk = $data['risk_level'] ?? 'Low';
                            $course = $data['course'] ?? null;
                            $rollcall = $data['roll_call_total'] ?? 0;
                        @endphp
                        <tr>
                            <td>
                                <span class="course-name">{{ $course->course_name ?? 'Unknown' }}</span>
                                <span class="course-code">{{ $course->course_code ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="attendance-cell">
                                    <span class="percent">{{ number_format($att, 1) }}%</span>
                                    <div class="bar-track">
                                        <div class="bar-fill"
                                            style="width:{{ $att }}%; background:{{ $color }};"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="rollcall">{{ $rollcall }} <span>/ 10</span></td>
                            <td>
                                <span class="badge badge-{{ $elig }}">
                                    {{ strtoupper(str_replace('_', ' ', $elig)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ strtolower($risk) }}">
                                    {{ strtoupper($risk) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-book"></i>
                                <p>You are not enrolled in any courses yet.</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection
