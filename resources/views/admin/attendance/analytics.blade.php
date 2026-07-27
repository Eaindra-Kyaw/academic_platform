@extends('layouts.app')

@section('title', 'Attendance Analytics')
@section('role', 'Admin')
@section('page-title', 'Attendance Analytics')
@section('welcome-text', 'Monitor university-wide attendance performance')

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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-number.green {
            color: var(--success);
        }

        .stat-number.yellow {
            color: var(--warning);
        }

        .stat-number.red {
            color: var(--danger);
        }

        .stat-number.blue {
            color: var(--info);
        }

        .stat-number.purple {
            color: var(--purple);
        }

        .stat-label {
            font-size: 0.65rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .filter-bar {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            background: var(--white);
            padding: 0.75rem 1rem;
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 1.5rem;
            align-items: center;
            box-shadow: var(--shadow);
        }

        .filter-bar .filter-group {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .filter-bar .filter-group label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-dark);
            white-space: nowrap;
        }

        .filter-bar .filter-group select,
        .filter-bar .filter-group input {
            padding: 0.3rem 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 6px;
            font-size: 0.8rem;
            background: #fafbfc;
            transition: var(--transition);
        }

        .filter-bar .filter-group select:focus,
        .filter-bar .filter-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .filter-bar .btn-filter {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            padding: 0.3rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-bar .btn-filter:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.2);
        }

        .filter-bar .btn-reset {
            background: #f3f4f6;
            color: var(--text-dark);
            border: 1px solid rgba(10, 36, 99, 0.1);
            padding: 0.3rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }

        .filter-bar .btn-reset:hover {
            background: #e5e7eb;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: visible !important;
            /* FIX: allow dropdown to overflow */
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .chart-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .chart-card .card-header {
            padding: 0.75rem 1rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-card .card-body {
            padding: 1rem;
            overflow: visible !important;
            /* FIX */
        }

        .chart-container {
            position: relative;
            height: 250px;
        }

        .ranking-table {
            width: 100%;
            font-size: 0.8rem;
            border-collapse: collapse;
        }

        .ranking-table th {
            text-align: left;
            padding: 0.4rem 0.5rem;
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--text-gray);
            font-weight: 600;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .ranking-table td {
            padding: 0.3rem 0.5rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
        }

        .ranking-table tr:last-child td {
            border-bottom: none;
        }

        .ranking-table .rank-num {
            font-weight: 700;
            color: var(--text-gray);
            font-size: 0.7rem;
            width: 24px;
            text-align: center;
        }

        .ranking-table .rank-1 {
            color: var(--accent);
        }

        .ranking-table .rank-2 {
            color: #9ca3af;
        }

        .ranking-table .rank-3 {
            color: #d97706;
        }

        .attendance-bar {
            height: 6px;
            background: #f3f4f6;
            border-radius: 3px;
            overflow: hidden;
            width: 80px;
            display: inline-block;
        }

        .attendance-bar .fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .attendance-bar .fill.high {
            background: var(--success);
        }

        .attendance-bar .fill.medium {
            background: var(--warning);
        }

        .attendance-bar .fill.low {
            background: var(--danger);
        }

        .dept-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .dept-card {
            background: #fafbfc;
            border-radius: 8px;
            padding: 0.6rem 0.8rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
        }

        .dept-card .dept-code {
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--text-dark);
        }

        .dept-card .dept-attendance {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0.1rem 0;
        }

        .risk-badge {
            display: inline-block;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .risk-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .risk-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .risk-low {
            background: #d1fae5;
            color: #166534;
        }

        .risk-table {
            width: 100%;
            font-size: 0.8rem;
            border-collapse: collapse;
        }

        .risk-table th {
            text-align: left;
            padding: 0.3rem 0.5rem;
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--text-gray);
            font-weight: 600;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .risk-table td {
            padding: 0.3rem 0.5rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
        }

        .risk-table tr:last-child td {
            border-bottom: none;
        }

        /* ===== SEARCHABLE DROPDOWN ===== */
        .searchable-dropdown {
            position: relative;
            flex: 2;
            min-width: 200px;
            z-index: 9999;
        }

        .searchable-dropdown input {
            width: 100%;
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.12);
            background: #fff;
            font-size: 0.9rem;
            box-sizing: border-box;
        }

        .searchable-dropdown .dropdown-list {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid rgba(10, 36, 99, 0.15);
            border-radius: 8px;
            max-height: 240px;
            overflow-y: auto;
            z-index: 10000;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .searchable-dropdown .dropdown-list.show {
            display: block;
        }

        .searchable-dropdown .dropdown-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-size: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .searchable-dropdown .dropdown-item:hover {
            background: #f1f5f9;
        }

        .searchable-dropdown .dropdown-item.selected {
            background: #e2e8f0;
        }

        .searchable-dropdown .no-results {
            padding: 0.5rem 1rem;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .quick-action-wrapper {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .quick-action-wrapper .searchable-dropdown {
            flex: 2;
            min-width: 220px;
        }

        .btn-view-attendance {
            background: #dbeafe;
            color: #1d4ed8;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
        }

        .btn-view-attendance:hover {
            background: #bfdbfe;
        }

        /* ===== MODAL STYLES ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            border-radius: var(--radius);
            max-width: 800px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafbfc;
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .modal-header h4 {
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-header h4 .student-name {
            color: var(--primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-gray);
            cursor: pointer;
            transition: var(--transition);
            padding: 0 4px;
            line-height: 1;
        }

        .modal-close:hover {
            color: var(--text-dark);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            background: #fafbfc;
            border-radius: 0 0 var(--radius) var(--radius);
        }

        .btn-close-modal {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-close-modal:hover {
            background: #e5e7eb;
        }

        .modal-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .modal-stat {
            background: #fafbfc;
            padding: 0.75rem;
            border-radius: 8px;
            text-align: center;
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .modal-stat .number {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .modal-stat .number.high {
            color: var(--danger);
        }

        .modal-stat .number.medium {
            color: var(--warning);
        }

        .modal-stat .number.low {
            color: var(--success);
        }

        .modal-stat .label {
            font-size: 0.6rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .modal-chart-container {
            position: relative;
            height: 200px;
            margin: 1rem 0;
        }

        .modal-monthly-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .modal-monthly-stat {
            background: #fafbfc;
            border-radius: 6px;
            padding: 0.3rem 0.4rem;
            text-align: center;
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .modal-monthly-stat .m-label {
            font-size: 0.55rem;
            color: var(--text-gray);
        }

        .modal-monthly-stat .m-value {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--primary);
        }

        .loading-spinner {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid #f3f4f6;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spinner 0.8s linear infinite;
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 1024px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar .filter-group {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar .filter-group select,
            .filter-bar .filter-group input {
                width: 100%;
            }

            .dept-card-grid {
                grid-template-columns: 1fr 1fr;
            }

            .modal-stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .modal-monthly-stats {
                grid-template-columns: 1fr 1fr;
            }

            .modal-content {
                margin: 10px;
                max-height: 95vh;
            }

            .quick-action-wrapper .searchable-dropdown {
                flex: 1 1 100%;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-card {
                padding: 0.75rem;
            }

            .stat-number {
                font-size: 1.2rem;
            }

            .dept-card-grid {
                grid-template-columns: 1fr;
            }

            .modal-stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number blue">{{ $stats['total_sessions'] ?? 0 }}</div>
            <div class="stat-label"> Total Sessions</div>
        </div>
        <div class="stat-card">
            <div class="stat-number purple">{{ $stats['total_records'] ?? 0 }}</div>
            <div class="stat-label"> Total Records</div>
        </div>
        <div class="stat-card">
            <div class="stat-number green">{{ $stats['avg_attendance'] ?? 0 }}%</div>
            <div class="stat-label"> Avg Attendance</div>
        </div>
        <div class="stat-card">
            <div class="stat-number yellow">{{ $stats['present_count'] ?? 0 }}</div>
            <div class="stat-label"> Present</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form class="filter-bar" method="GET" action="{{ route('admin.attendance.analytics') }}">
        <div class="filter-group">
            <label>Department</label>
            <select name="department_id">
                <option value="">All</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Course</label>
            <select name="course_id">
                <option value="">All</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Year</label>
            <select name="year">
                <option value="">All</option>
                @for ($i = 1; $i <= 6; $i++)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                        {{ $yearLabels[$i] ?? $i . 'th' }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="filter-group">
            <label>Date</label>
            <select name="date_range">
                <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ $dateRange == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_semester" {{ $dateRange == 'this_semester' ? 'selected' : '' }}>This Semester</option>
            </select>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn-filter"><i class="bi bi-funnel"></i> Apply</button>
            <a href="{{ route('admin.attendance.analytics') }}" class="btn-reset"><i
                    class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </div>
    </form>

    {{-- ===== STUDENT QUICK ACTIONS (FIXED DROPDOWN) ===== --}}
    <div class="chart-card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span><i class="bi bi-person"></i> View each student's attendance</span>
            {{-- <span style="font-size:0.65rem; color:var(--text-gray);">Start typing to search, or click to select</span> --}}
        </div>
        <div class="card-body">
            <div class="quick-action-wrapper">
                <div class="searchable-dropdown" id="attendanceStudentDropdown">
                    <input type="text" id="attendanceSearchInput" placeholder="Search by name or ID..."
                        autocomplete="off">
                    <div class="dropdown-list" id="attendanceDropdownList">
                        @foreach ($students as $s)
                            <div class="dropdown-item" data-id="{{ $s->id }}"
                                data-label="{{ $s->name }} ({{ $s->student_id ?? 'N/A' }})">
                                {{ $s->name }} ({{ $s->student_id ?? 'N/A' }})
                            </div>
                        @endforeach
                        <div class="no-results" style="display:none;">No students found</div>
                    </div>
                </div>
                <button class="btn-view-attendance" onclick="viewQuickAttendance()">
                    <i class="bi bi-calendar-check"></i> View Attendance
                </button>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="chart-grid">
        <div class="chart-card">
            <div class="card-header">
                <span>📈 Weekly Attendance Trend</span>
                <span style="font-size:0.65rem; color:var(--text-gray);">Last 12 weeks</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-card">
            <div class="card-header">
                <span>🏛️ Department Attendance</span>
                <span style="font-size:0.65rem; color:var(--text-gray);">Ranking</span>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                @if (count($departmentAttendance) > 0)
                    <div class="dept-card-grid">
                        @foreach ($departmentAttendance as $dept)
                            @php
                                $color =
                                    $dept['attendance'] >= 75
                                        ? '#10b981'
                                        : ($dept['attendance'] >= 60
                                            ? '#f59e0b'
                                            : '#ef4444');
                            @endphp
                            <div class="dept-card">
                                <div class="dept-code">{{ $dept['name'] }}</div>
                                <div class="dept-attendance" style="color:{{ $color }};">{{ $dept['attendance'] }}%
                                </div>
                                <div style="font-size:0.55rem; color:var(--text-gray);">{{ $dept['total_students'] }}
                                    students</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:1rem; color:var(--text-gray);">No department data available</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Course Ranking Table --}}
    <div class="chart-card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span>📚 Course Attendance Ranking</span>
            <span style="font-size:0.65rem; color:var(--text-gray);">Top 20 courses</span>
        </div>
        <div class="card-body" style="padding:0;">
            @if (count($courseRanking) > 0)
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Course</th>
                            <th>Dept</th>
                            <th style="text-align:center;">Students</th>
                            <th style="text-align:center;">Sessions</th>
                            <th style="text-align:center;">Attendance</th>
                            <th style="text-align:center;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courseRanking as $index => $course)
                            @php
                                $rankClass =
                                    $index == 0 ? 'rank-1' : ($index == 1 ? 'rank-2' : ($index == 2 ? 'rank-3' : ''));
                                $barClass =
                                    $course['attendance'] >= 75
                                        ? 'high'
                                        : ($course['attendance'] >= 60
                                            ? 'medium'
                                            : 'low');
                            @endphp
                            <tr>
                                <td class="rank-num {{ $rankClass }}">{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight:600; font-size:0.8rem; color:var(--text-dark);">
                                        {{ $course['course_code'] }}</div>
                                    <div style="font-size:0.65rem; color:var(--text-gray);">
                                        {{ Str::limit($course['course_name'], 25) }}</div>
                                </td>
                                <td style="font-size:0.7rem; color:var(--text-gray);">{{ $course['department'] }}</td>
                                <td style="text-align:center; font-size:0.75rem;">{{ $course['students'] }}</td>
                                <td style="text-align:center; font-size:0.75rem;">{{ $course['sessions'] }}</td>
                                <td style="text-align:center; font-weight:600; font-size:0.85rem;">
                                    {{ $course['attendance'] }}%</td>
                                <td>
                                    <div class="attendance-bar">
                                        <div class="fill {{ $barClass }}"
                                            style="width: {{ $course['attendance'] }}%;"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align:center; padding:2rem; color:var(--text-gray);">No course data available</div>
            @endif
        </div>
    </div>

    {{-- At-Risk Students --}}
    <div class="chart-card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span>⚠️ Students At Risk</span>
            <span style="font-size:0.65rem; color:var(--text-gray);">{{ count($atRiskStudents) }} students at risk</span>
        </div>
        <div class="card-body" style="padding:0;">
            @if (count($atRiskStudents) > 0)
                <table class="risk-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th style="text-align:center;">Attendance</th>
                            <th style="text-align:center;">Risk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($atRiskStudents as $student)
                            <tr>
                                <td>
                                    <div style="font-weight:600; font-size:0.8rem; color:var(--text-dark);">
                                        {{ $student['student']->name }}</div>
                                    <div style="font-size:0.65rem; color:var(--text-gray);">
                                        {{ $student['student']->email }}</div>
                                </td>
                                <td style="font-size:0.75rem; color:var(--text-gray);">{{ $student['department'] }}</td>
                                <td style="font-size:0.75rem; color:var(--text-gray);">{{ $student['year'] }}</td>
                                <td style="text-align:center; font-weight:600; font-size:0.85rem;">
                                    {{ $student['attendance'] }}%</td>
                                <td style="text-align:center;">
                                    <span
                                        class="risk-badge risk-{{ strtolower($student['risk_level']) }}">{{ $student['risk_level'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align:center; padding:2rem; color:var(--text-gray);">
                    <i class="bi bi-check-circle"
                        style="font-size:2rem; color:var(--success); display:block; margin-bottom:0.5rem;"></i>
                    No students at risk! All students have good attendance.
                </div>
            @endif
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ATTENDANCE MODAL -->
    <!-- ============================================================ -->
    <div class="modal-overlay" id="attendanceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i class="bi bi-calendar-check" style="color:var(--info);"></i>
                    Attendance: <span class="student-name" id="attendanceStudentName">Student</span>
                    <span style="font-size:0.7rem; font-weight:400; color:var(--text-gray);"
                        id="attendanceStudentId"></span>
                </h4>
                <button class="modal-close" onclick="closeModal('attendanceModal')">&times;</button>
            </div>
            <div class="modal-body" id="attendanceModalBody">
                <div class="text-center" style="padding:2rem;">
                    <div class="loading-spinner"></div>
                    <p style="margin-top:0.5rem; color:var(--text-gray);">Loading attendance data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-close-modal" onclick="closeModal('attendanceModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- RISK MODAL (not used but kept) -->
    <!-- ============================================================ -->
    <div class="modal-overlay" id="riskModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i class="bi bi-shield-exclamation" style="color:var(--warning);"></i>
                    Risk Analysis: <span class="student-name" id="riskStudentName">Student</span>
                    <span style="font-size:0.7rem; font-weight:400; color:var(--text-gray);" id="riskStudentId"></span>
                </h4>
                <button class="modal-close" onclick="closeModal('riskModal')">&times;</button>
            </div>
            <div class="modal-body" id="riskModalBody">
                <div class="text-center" style="padding:2rem;">
                    <div class="loading-spinner"></div>
                    <p style="margin-top:0.5rem; color:var(--text-gray);">Loading risk data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-close-modal" onclick="closeModal('riskModal')">Close</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ====== SEARCHABLE DROPDOWN ======
                const input = document.getElementById('attendanceSearchInput');
                const list = document.getElementById('attendanceDropdownList');
                const items = list.querySelectorAll('.dropdown-item');
                const noResults = list.querySelector('.no-results');
                let selectedId = null;

                function filterItems(query) {
                    const q = query.toLowerCase().trim();
                    let visible = 0;
                    items.forEach(item => {
                        const label = item.dataset.label.toLowerCase();
                        const match = label.indexOf(q) > -1;
                        item.style.display = match ? '' : 'none';
                        if (match) visible++;
                    });
                    noResults.style.display = visible === 0 ? '' : 'none';
                    list.classList.add('show');
                }

                input.addEventListener('input', function() {
                    const val = this.value;
                    if (val.length === 0) {
                        items.forEach(item => item.style.display = '');
                        noResults.style.display = 'none';
                        list.classList.remove('show');
                        selectedId = null;
                        return;
                    }
                    filterItems(val);
                });

                items.forEach(item => {
                    item.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const label = this.dataset.label;
                        input.value = label;
                        selectedId = id;
                        list.classList.remove('show');
                        items.forEach(i => i.classList.remove('selected'));
                        this.classList.add('selected');
                    });
                });

                input.addEventListener('blur', function() {
                    setTimeout(() => list.classList.remove('show'), 200);
                });

                input.addEventListener('focus', function() {
                    if (this.value.length > 0) filterItems(this.value);
                });

                window.getSelectedStudentId = function() {
                    if (selectedId) return selectedId;
                    const val = input.value.trim();
                    if (val) {
                        for (let item of items) {
                            if (item.dataset.label === val) return item.dataset.id;
                        }
                    }
                    return null;
                };

                // ====== Quick Action ======
                window.viewQuickAttendance = function() {
                    const id = window.getSelectedStudentId();
                    if (!id) {
                        alert('Please select a student first.');
                        return;
                    }
                    const label = input.value.trim();
                    const match = label.match(/\(([^)]+)\)/);
                    const studentIdNumber = match ? match[1] : 'N/A';
                    const studentName = label.replace(/\([^)]*\)/, '').trim() || 'Student';
                    openAttendanceModal(id, studentName, studentIdNumber);
                };

                // ====== Modal functions ======
                function closeModal(id) {
                    document.getElementById(id).classList.remove('show');
                }
                window.closeModal = closeModal;

                document.querySelectorAll('.modal-overlay').forEach(overlay => {
                    overlay.addEventListener('click', function(e) {
                        if (e.target === this) this.classList.remove('show');
                    });
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove(
                            'show'));
                    }
                });

                let attendanceChart = null;

                function openAttendanceModal(studentId, studentName, studentIdNumber) {
                    const modal = document.getElementById('attendanceModal');
                    const body = document.getElementById('attendanceModalBody');

                    document.getElementById('attendanceStudentName').textContent = studentName;
                    document.getElementById('attendanceStudentId').textContent = '(' + studentIdNumber + ')';

                    modal.classList.add('show');

                    body.innerHTML = `
                        <div class="text-center" style="padding:2rem;">
                            <div class="loading-spinner"></div>
                            <p style="margin-top:0.5rem; color:var(--text-gray);">Loading attendance data...</p>
                        </div>
                    `;

                    fetch(`/admin/attendance/student-data/${studentId}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            if (!data.success) throw new Error(data.message || 'Failed to load');

                            let html = '';
                            const avgAttendance = data.weekly.reduce((sum, w) => sum + w.attendance, 0) / (data
                                .weekly.length || 1);
                            const attClass = avgAttendance >= 75 ? 'low' : (avgAttendance >= 60 ? 'medium' :
                                'high');

                            html += `
                            <div class="modal-stats-grid">
                                <div class="modal-stat">
                                    <div class="number ${attClass}">${Math.round(avgAttendance)}%</div>
                                    <div class="label">Avg Attendance</div>
                                </div>
                                <div class="modal-stat">
                                    <div class="number">${data.weekly.length}</div>
                                    <div class="label">Weeks Tracked</div>
                                </div>
                                <div class="modal-stat">
                                    <div class="number">${data.monthly.length}</div>
                                    <div class="label">Months Tracked</div>
                                </div>
                                <div class="modal-stat">
                                    <div class="number ${attClass}">${avgAttendance >= 75 ? '✅ Good' : (avgAttendance >= 60 ? '⚠️ Moderate' : '🚨 Low')}</div>
                                    <div class="label">Status</div>
                                </div>
                            </div>
                            <div style="margin-bottom:0.5rem;"><strong style="font-size:0.8rem; color:var(--text-dark);">📊 Monthly Summary</strong></div>
                            <div class="modal-monthly-stats">`;
                            if (data.monthly && data.monthly.length > 0) {
                                data.monthly.forEach(m => {
                                    html += `
                                    <div class="modal-monthly-stat">
                                        <div class="m-label">${m.month}</div>
                                        <div class="m-value">${m.avg_attendance}%</div>
                                    </div>
                                `;
                                });
                            } else {
                                html +=
                                    `<div class="modal-monthly-stat" style="grid-column:1/-1; text-align:center; color:var(--text-gray);">No monthly data</div>`;
                            }
                            html += `</div>
                            <div class="modal-chart-container"><canvas id="attendanceModalChart"></canvas></div>`;

                            body.innerHTML = html;

                            if (attendanceChart) {
                                attendanceChart.destroy();
                                attendanceChart = null;
                            }
                            const ctx = document.getElementById('attendanceModalChart');
                            if (ctx && data.weekly && data.weekly.length > 0) {
                                attendanceChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: data.weekly.map(w => w.label),
                                        datasets: [{
                                            label: 'Attendance %',
                                            data: data.weekly.map(w => w.attendance),
                                            borderColor: '#0A2463',
                                            backgroundColor: 'rgba(10,36,99,0.08)',
                                            borderWidth: 2.5,
                                            fill: true,
                                            tension: 0.3,
                                            pointBackgroundColor: '#0A2463',
                                            pointBorderColor: 'white',
                                            pointBorderWidth: 2,
                                            pointRadius: 4,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    callback: function(value) {
                                                        return value + '%';
                                                    },
                                                    font: {
                                                        size: 9
                                                    }
                                                },
                                                grid: {
                                                    color: 'rgba(0,0,0,0.05)'
                                                }
                                            },
                                            x: {
                                                grid: {
                                                    display: false
                                                },
                                                ticks: {
                                                    font: {
                                                        size: 9
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            body.innerHTML = `
                            <div style="text-align:center; padding:2rem; color:var(--danger);">
                                <i class="bi bi-exclamation-triangle" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                <p>${error.message || 'Failed to load attendance data'}</p>
                                <button class="btn-close-modal" onclick="closeModal('attendanceModal')">Close</button>
                            </div>
                        `;
                        });
                }

                // ====== Weekly Chart ======
                const weeklyData = @json($weeklyTrend);
                const ctx = document.getElementById('weeklyChart');
                if (ctx && weeklyData && weeklyData.length > 0) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: weeklyData.map(item => item.label),
                            datasets: [{
                                label: 'Attendance %',
                                data: weeklyData.map(item => item.attendance),
                                borderColor: '#0A2463',
                                backgroundColor: 'rgba(10,36,99,0.08)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#0A2463',
                                pointBorderColor: 'white',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: {
                                        callback: function(value) {
                                            return value + '%';
                                        },
                                        font: {
                                            size: 10
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0,0,0,0.05)'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 10
                                        }
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                } else if (ctx) {
                    ctx.parentElement.innerHTML =
                        '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--text-gray);font-size:0.9rem;">No attendance data available</div>';
                }
            });
        </script>
    @endpush
@endsection
