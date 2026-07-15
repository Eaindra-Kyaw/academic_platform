@extends('layouts.app')

@section('title', 'Session History')
@section('role', 'Lecturer')
@section('page-title', 'Session History')
@section('welcome-text', 'View all attendance sessions and records')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
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
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 14px 18px;
            border: 1px solid rgba(10, 36, 99, 0.06);
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .stat-box:hover {
            box-shadow: var(--shadow-hover);
        }

        .stat-box .label {
            font-size: 12px;
            color: var(--text-gray);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stat-box .value {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .stat-box .trend {
            font-size: 11px;
            font-weight: 500;
            display: inline-block;
            padding: 1px 10px;
            border-radius: 10px;
            margin-top: 2px;
        }

        .stat-box .trend.up {
            background: var(--success-light);
            color: #166534;
        }

        .stat-box .trend.down {
            background: var(--danger-light);
            color: #991b1b;
        }

        .stat-box .trend.stable {
            background: var(--warning-light);
            color: #92400e;
        }

        .session-table-wrap {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .session-table-wrap .table-header {
            padding: 12px 18px;
            background: #fafafa;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .session-table-wrap .table-header h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .session-table-wrap .table-header .filters {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .session-table-wrap .table-header .filters select,
        .session-table-wrap .table-header .filters input {
            padding: 5px 10px;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 12px;
            background: var(--white);
            color: var(--text-dark);
            outline: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .session-table-wrap .table-header .filters select:focus,
        .session-table-wrap .table-header .filters input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .table-scroll {
            overflow-x: auto;
        }

        .session-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .session-table thead {
            background: #f9fafb;
        }

        .session-table thead th {
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            white-space: nowrap;
        }

        .session-table tbody td {
            padding: 9px 14px;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            color: var(--text-dark);
            vertical-align: middle;
        }

        .session-table tbody tr:hover {
            background: #fafafa;
        }

        .session-table tbody tr:last-child td {
            border-bottom: none;
        }

        .course-cell .name {
            font-weight: 500;
            font-size: 13px;
            color: var(--text-dark);
        }

        .course-cell .code {
            font-size: 11px;
            color: var(--text-gray);
        }

        .code-badge {
            background: #f3f4f6;
            padding: 1px 10px;
            border-radius: 6px;
            font-family: monospace;
            font-weight: 600;
            font-size: 12px;
            color: var(--text-dark);
            letter-spacing: 0.5px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge.active {
            background: var(--success-light);
            color: #166534;
        }

        .status-badge.active::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse 1.5s infinite;
        }

        .status-badge.ended {
            background: #f3f4f6;
            color: var(--text-gray);
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .attendance-cell {
            min-width: 120px;
        }

        .attendance-cell .bar-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .attendance-cell .bar-wrap .bar {
            flex: 1;
            height: 4px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            min-width: 50px;
        }

        .attendance-cell .bar-wrap .bar .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.4s ease;
        }

        .attendance-cell .bar-wrap .percent {
            font-weight: 600;
            font-size: 13px;
            color: var(--text-dark);
            min-width: 36px;
            text-align: right;
        }

        .attendance-cell .stats-mini {
            display: flex;
            gap: 8px;
            font-size: 11px;
            color: var(--text-gray);
            margin-top: 2px;
        }

        .attendance-cell .stats-mini .p {
            color: var(--success);
        }

        .attendance-cell .stats-mini .l {
            color: var(--warning);
        }

        .attendance-cell .stats-mini .a {
            color: var(--danger);
        }

        .action-group {
            display: flex;
            gap: 4px;
            justify-content: center;
        }

        .btn-icon-sm {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.12);
            background: var(--white);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            cursor: pointer;
            color: var(--text-gray);
            font-size: 13px;
        }

        .btn-icon-sm:hover {
            background: #f3f4f6;
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-icon-sm.danger {
            border-color: var(--danger-light);
            color: var(--danger);
        }

        .btn-icon-sm.danger:hover {
            background: var(--danger-light);
            border-color: var(--danger);
        }

        .pagination-wrap {
            padding: 12px 18px;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: #fafafa;
        }

        .pagination-wrap .info {
            color: var(--text-gray);
            font-size: 12px;
        }

        .pagination-wrap .pagination {
            display: flex;
            gap: 3px;
            margin: 0;
        }

        .pagination-wrap .pagination .page-link {
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.12);
            color: var(--text-gray);
            font-size: 12px;
            text-decoration: none;
            transition: var(--transition);
            background: var(--white);
        }

        .pagination-wrap .pagination .page-link:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .pagination-wrap .pagination .page-link.active {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--white);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state .icon {
            font-size: 40px;
            color: #d1d5db;
            margin-bottom: 12px;
        }

        .empty-state h5 {
            color: var(--text-dark);
            margin-bottom: 4px;
            font-size: 16px;
        }

        .empty-state p {
            color: var(--text-gray);
            font-size: 13px;
            margin-bottom: 16px;
        }

        .empty-state .btn-primary {
            background: var(--primary);
            color: var(--white);
            padding: 8px 24px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            display: inline-block;
            transition: var(--transition);
            font-weight: 500;
            font-size: 13px;
        }

        .empty-state .btn-primary:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .session-table thead {
                display: none;
            }

            .session-table tbody tr {
                display: block;
                padding: 12px 0;
                border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            }

            .session-table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 4px 14px;
                border: none;
                font-size: 12px;
            }

            .session-table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--text-gray);
                font-size: 11px;
            }

            .session-table tbody td:last-child {
                border-bottom: none;
            }

            .attendance-cell .bar-wrap {
                flex: 1;
            }

            .action-group {
                justify-content: flex-end;
            }
        }

        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .stat-box {
                padding: 10px 14px;
            }

            .stat-box .value {
                font-size: 18px;
            }

            .session-table-wrap .table-header {
                flex-direction: column;
                align-items: stretch;
            }

            .session-table-wrap .table-header .filters {
                flex-wrap: wrap;
            }

            .pagination-wrap {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
    </style>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Total Sessions</div>
            <div class="value">{{ $totalSessions ?? 0 }}</div>
            <span class="trend stable">All Time</span>
        </div>

        <div class="stat-box">
            <div class="label">Active</div>
            <div class="value">{{ $activeSessions->count() }}</div>
            <span class="trend up">● Live</span>
        </div>

        <div class="stat-box">
            <div class="label">Avg. Attendance</div>
            <div class="value">{{ $averageAttendance ?? 0 }}%</div>
            <span class="trend {{ ($averageAttendance ?? 0) >= 75 ? 'up' : 'down' }}">
                {{ ($averageAttendance ?? 0) >= 75 ? 'Good' : 'Needs Work' }}
            </span>
        </div>

        <div class="stat-box">
            <div class="label">Total Students</div>
            <div class="value">{{ $totalStudents ?? 0 }}</div>
            <span class="trend stable">Enrolled</span>
        </div>
    </div>

    <div class="session-table-wrap">
        <div class="table-header">
            <h5><i class="bi bi-clock-history"></i> Sessions</h5>
            <div class="filters">
                <select id="statusFilter" onchange="filterTable()">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="ended">Ended</option>
                </select>
                <input type="text" id="searchFilter" placeholder="Search..." onkeyup="filterTable()">
            </div>
        </div>

        @if ($sessions && $sessions->count() > 0)
            <div class="table-scroll">
                <table class="session-table" id="sessionTable">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Code</th>
                            <th>Room</th>
                            <th>Date & Time</th>
                            <th>Periods</th>
                            <th>Status</th>
                            <th>Attendance</th>
                            <th>Roll Call</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sessions as $session)
                            @php
                                $present = $session->records->where('status', 'present')->count();
                                $late = $session->records->where('status', 'late')->count();
                                $absent = $session->records->where('status', 'absent')->count();

                                // ✅ FIX: Get total enrolled students from database if $total_students is empty
                                $totalEnrolled = $session->total_students ?? 0;
                                if ($totalEnrolled == 0) {
                                    $totalEnrolled = \App\Models\Enrollment::where('course_id', $session->course_id)
                                        ->where('status', 'approved')
                                        ->count();
                                }

                                $periods = $session->conducted_periods ?? 1;
                                $attendedPeriods = ($present + $late) * $periods;
                                $totalPeriods = $totalEnrolled * $periods;
                                $percentage = $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100) : 0;

                                // Roll call based on percentage (KG+12 consistency)
                                $rollCall =
                                    $percentage >= 95
                                        ? 10
                                        : ($percentage >= 90
                                            ? 9
                                            : ($percentage >= 85
                                                ? 8
                                                : ($percentage >= 80
                                                    ? 7
                                                    : ($percentage >= 75
                                                        ? 6
                                                        : ($percentage >= 70
                                                            ? 5
                                                            : ($percentage >= 65
                                                                ? 4
                                                                : ($percentage >= 60
                                                                    ? 3
                                                                    : ($percentage >= 55
                                                                        ? 2
                                                                        : 1))))))));

                                $barColor = $percentage >= 75 ? '#10b981' : ($percentage >= 50 ? '#f59e0b' : '#ef4444');
                            @endphp
                            <tr data-status="{{ $session->status }}"
                                data-course="{{ strtolower($session->course->course_name ?? '') }}">
                                <td data-label="Course">
                                    <div class="course-cell">
                                        <div class="name">{{ $session->course->course_name ?? 'Unknown' }}</div>
                                        <div class="code">{{ $session->course->course_code ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td data-label="Code">
                                    <span class="code-badge">{{ $session->manual_code ?? 'N/A' }}</span>
                                </td>
                                <td data-label="Room">{{ $session->room ?? '—' }}</td>
                                <td data-label="Date & Time">
                                    {{ $session->started_at ? \Carbon\Carbon::parse($session->started_at)->format('M d, Y') : 'N/A' }}
                                    <br>
                                    <small style="color: var(--text-gray); font-size: 11px;">
                                        {{ $session->started_at ? \Carbon\Carbon::parse($session->started_at)->format('h:i A') : '' }}
                                        · {{ $session->duration }}m
                                    </small>
                                </td>
                                <td data-label="Periods" style="text-align:center;">
                                    {{ $periods }}
                                    <br>
                                    <small style="color: var(--text-gray); font-size: 10px;">
                                        conducted
                                    </small>
                                </td>
                                <td data-label="Status">
                                    <span class="status-badge {{ $session->status == 'active' ? 'active' : 'ended' }}">
                                        {{ ucfirst($session->status) }}
                                    </span>
                                </td>
                                <td data-label="Attendance" class="attendance-cell">
                                    <div class="bar-wrap">
                                        <div class="bar">
                                            <div class="fill"
                                                style="width: {{ $percentage }}%; background: {{ $barColor }};">
                                            </div>
                                        </div>
                                        <span class="percent">{{ $percentage }}%</span>
                                    </div>
                                    <div class="stats-mini">
                                        <span class="p">P: {{ $present }}</span>
                                        <span class="l">L: {{ $late }}</span>
                                        <span class="a">A: {{ $absent }}</span>
                                    </div>
                                </td>
                                <td data-label="Roll Call" style="text-align:center; font-weight:700;">
                                    {{ $rollCall }}/10
                                </td>
                                <td data-label="Action" style="text-align: center;">
                                    @if ($session->status == 'active')
                                        <form
                                            action="{{ route('lecturer.attendance.sessions.end', ['id' => $session->id]) }}"
                                            method="POST" style="display: inline-block;"
                                            onsubmit="return confirm('End this session?');">
                                            @csrf
                                            <button type="submit" class="btn-icon-sm danger" title="End Session">
                                                <i class="bi bi-stop-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span style="color: #d1d5db; font-size: 11px;">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                <span class="info">
                    {{ $sessions->firstItem() ?? 0 }}–{{ $sessions->lastItem() ?? 0 }} of {{ $sessions->total() }}
                </span>
                {{ $sessions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="icon"><i class="bi bi-inbox"></i></div>
                <h5>No Sessions</h5>
                <p>Start your first attendance session.</p>
                <a href="{{ route('lecturer.attendance.take') }}" class="btn-primary">
                    <i class="bi bi-plus-circle"></i> Create Session
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function filterTable() {
                const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
                const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
                const rows = document.querySelectorAll('#sessionTable tbody tr');

                rows.forEach(row => {
                    const status = row.dataset.status.toLowerCase();
                    const course = row.dataset.course;

                    const matchStatus = statusFilter === 'all' || status === statusFilter;
                    const matchSearch = course.includes(searchFilter);

                    row.style.display = (matchStatus && matchSearch) ? '' : 'none';
                });
            }
        </script>
    @endpush
@endsection
