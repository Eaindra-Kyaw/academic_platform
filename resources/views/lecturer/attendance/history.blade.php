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
        /* ============================================
               COMPACT PROFESSIONAL DESIGN
               ============================================ */

        /* Stats Row - Compact */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-box {
            background: white;
            border-radius: 10px;
            padding: 14px 18px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .stat-box:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .stat-box .label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stat-box .value {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
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
            background: #dcfce7;
            color: #166534;
        }

        .stat-box .trend.down {
            background: #fee2e2;
            color: #991b1b;
        }

        .stat-box .trend.stable {
            background: #fef3c7;
            color: #92400e;
        }

        /* ============================================
               SESSION TABLE - Compact
               ============================================ */
        .session-table-wrap {
            background: white;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .session-table-wrap .table-header {
            padding: 12px 18px;
            background: #fafafa;
            border-bottom: 1px solid #e5e7eb;
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
            color: #1f2937;
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
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 12px;
            background: white;
            color: #1f2937;
            outline: none;
            transition: all 0.2s;
        }

        .session-table-wrap .table-header .filters select:focus,
        .session-table-wrap .table-header .filters input:focus {
            border-color: #800000;
        }

        /* Table */
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
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        .session-table tbody td {
            padding: 9px 14px;
            border-bottom: 1px solid #f3f4f6;
            color: #1f2937;
            vertical-align: middle;
        }

        .session-table tbody tr:hover {
            background: #fafafa;
        }

        .session-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Course Cell */
        .course-cell .name {
            font-weight: 500;
            font-size: 13px;
            color: #1f2937;
        }

        .course-cell .code {
            font-size: 11px;
            color: #6b7280;
        }

        /* Code Badge */
        .code-badge {
            background: #f3f4f6;
            padding: 1px 10px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: 600;
            font-size: 12px;
            color: #374151;
            letter-spacing: 0.5px;
        }

        /* Status Badge */
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
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.active::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 1.5s infinite;
        }

        .status-badge.ended {
            background: #f3f4f6;
            color: #6b7280;
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

        /* Attendance Progress - Compact */
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
            color: #1f2937;
            min-width: 36px;
            text-align: right;
        }

        .attendance-cell .stats-mini {
            display: flex;
            gap: 8px;
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        .attendance-cell .stats-mini .p {
            color: #10b981;
        }

        .attendance-cell .stats-mini .l {
            color: #f59e0b;
        }

        .attendance-cell .stats-mini .a {
            color: #ef4444;
        }

        /* Action Buttons - Only End Session */
        .action-group {
            display: flex;
            gap: 4px;
            justify-content: center;
        }

        .btn-icon-sm {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            cursor: pointer;
            color: #6b7280;
            font-size: 13px;
        }

        .btn-icon-sm:hover {
            background: #f3f4f6;
            border-color: #800000;
            color: #800000;
        }

        .btn-icon-sm.danger {
            border-color: #fee2e2;
            color: #ef4444;
        }

        .btn-icon-sm.danger:hover {
            background: #fee2e2;
            border-color: #ef4444;
        }

        /* ============================================
               PAGINATION - Compact
               ============================================ */
        .pagination-wrap {
            padding: 12px 18px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            background: #fafafa;
        }

        .pagination-wrap .info {
            color: #6b7280;
            font-size: 12px;
        }

        .pagination-wrap .pagination {
            display: flex;
            gap: 3px;
            margin: 0;
        }

        .pagination-wrap .pagination .page-link {
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.2s;
            background: white;
        }

        .pagination-wrap .pagination .page-link:hover {
            border-color: #800000;
            color: #800000;
        }

        .pagination-wrap .pagination .page-link.active {
            background: #800000;
            border-color: #800000;
            color: white;
        }

        /* ============================================
               EMPTY STATE
               ============================================ */
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
            color: #1f2937;
            margin-bottom: 4px;
            font-size: 16px;
        }

        .empty-state p {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .empty-state .btn-primary {
            background: #800000;
            color: white;
            padding: 8px 24px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            display: inline-block;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 13px;
        }

        .empty-state .btn-primary:hover {
            background: #6b0000;
        }

        /* ============================================
               RESPONSIVE
               ============================================ */
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
                border-bottom: 1px solid #f3f4f6;
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
                color: #6b7280;
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

    <!-- ==========================================
        STATISTICS - COMPACT
        ========================================== -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Total Sessions</div>
            <div class="value">{{ $totalSessions ?? 0 }}</div>
            <span class="trend stable">All Time</span>
        </div>

        <div class="stat-box">
            <div class="label">Active</div>
            <div class="value">{{ $activeSessions ?? 0 }}</div>
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

    <!-- ==========================================
        SESSION TABLE
        ========================================== -->
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
                            <th>Status</th>
                            <th>Attendance</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sessions as $session)
                            @php
                                $present = $session->records->where('status', 'present')->count();
                                $late = $session->records->where('status', 'late')->count();
                                $absent = $session->records->where('status', 'absent')->count();
                                $total = $present + $late + $absent;
                                $percentage = $total > 0 ? round(($present / $total) * 100) : 0;

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
                                    <small style="color: #6b7280; font-size: 11px;">
                                        {{ $session->started_at ? \Carbon\Carbon::parse($session->started_at)->format('h:i A') : '' }}
                                        · {{ $session->duration }}m
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
                                <td data-label="Action" style="text-align: center;">
                                    {{-- End Session Button (only for active sessions) --}}
                                    @if ($session->status == 'active')
                                        <form
                                            action="{{ route('lecturer.attendance.session.end', ['id' => $session->id]) }}"
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

            <!-- Pagination -->
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
