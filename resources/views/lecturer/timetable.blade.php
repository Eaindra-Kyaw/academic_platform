@extends('layouts.app')

@section('title', 'Timetable')
@section('role', 'Lecturer')
@section('page-title', 'Weekly Timetable')
@section('welcome-text', 'Your class schedule and teaching hours')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        /* ============================================
                       STATS CARDS
                       ============================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 14px 18px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.12);
            transform: translateY(-2px);
            border-color: #0A2463;
        }

        .stat-card .label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-card .value {
            font-size: 22px;
            font-weight: 700;
            color: #0A2463;
            margin: 2px 0;
        }

        .stat-card .sub {
            font-size: 12px;
            color: #9ca3af;
        }

        /* ============================================
                       NEXT CLASS WIDGET
                       ============================================ */
        .next-class-widget {
            background: linear-gradient(135deg, #0A2463, #061840);
            color: white;
            border-radius: 12px;
            padding: 18px 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.3);
        }

        .next-class-widget .info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .next-class-widget .info .icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .next-class-widget .info .label {
            font-size: 11px;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .next-class-widget .info .name {
            font-size: 20px;
            font-weight: 700;
        }

        .next-class-widget .info .meta {
            font-size: 14px;
            opacity: 0.9;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .next-class-widget .info .meta i {
            margin-right: 3px;
        }

        .next-class-widget .countdown {
            background: rgba(255, 255, 255, 0.12);
            padding: 10px 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .next-class-widget .countdown .label {
            font-size: 11px;
            opacity: 0.7;
            text-transform: uppercase;
        }

        .next-class-widget .countdown .time {
            font-size: 28px;
            font-weight: 700;
        }

        /* ============================================
                       TIMETABLE CONTAINER
                       ============================================ */
        .timetable-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .timetable-header {
            padding: 16px 20px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .timetable-header .left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .timetable-header .left h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        .timetable-header .left h5 i {
            color: #0A2463;
            margin-right: 6px;
        }

        .timetable-header .left .week-range {
            color: #6b7280;
            font-size: 14px;
        }

        .timetable-header .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-manage {
            background: #0A2463;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-manage:hover {
            background: #061840;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.25);
        }

        .btn-today {
            background: #C5A020;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-today:hover {
            background: #b08a1a;
        }

        .filter-form {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-form select {
            padding: 5px 10px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 12px;
            background: white;
            color: #1f2937;
            outline: none;
            transition: all 0.2s;
        }

        .filter-form select:focus {
            border-color: #0A2463;
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.1);
        }

        .filter-form .btn-apply {
            background: #0A2463;
            color: white;
            border: none;
            padding: 5px 14px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-form .btn-apply:hover {
            background: #061840;
        }

        .btn-export {
            background: #059669;
            color: white;
            border: none;
            padding: 5px 14px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .btn-export:hover {
            background: #047857;
            color: white;
        }

        /* ============================================
                       CALENDAR GRID
                       ============================================ */
        .timetable-grid {
            overflow-x: auto;
            padding: 0;
        }

        .timetable-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
            font-size: 13px;
            table-layout: fixed;
        }

        .timetable-table th {
            padding: 12px 8px;
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            width: 14%;
        }

        .timetable-table th .day-label {
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
            display: block;
        }

        .timetable-table th .day-date {
            font-size: 12px;
            color: #9ca3af;
            font-weight: 400;
        }

        .timetable-table th.today {
            background: #e3f2fd;
        }

        .timetable-table th.today .day-label {
            color: #0A2463;
        }

        .timetable-table th.weekend {
            background: #f3f4f6;
            opacity: 0.6;
        }

        .timetable-table td {
            padding: 4px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
            height: 70px;
            min-width: 100px;
            background: white;
            transition: all 0.2s;
        }

        .timetable-table td .time-slot {
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
            padding: 4px 0;
        }

        .timetable-table td.today {
            background: #f5f9ff;
        }

        .timetable-table td.weekend {
            background: #f9fafb;
        }

        .timetable-table td .empty-slot {
            color: #d1d5db;
            font-size: 12px;
            text-align: center;
            padding: 20px 0;
        }

        /* ============================================
                       CLASS BLOCK
                       ============================================ */
        .class-block {
            background: #e3f2fd;
            border-left: 4px solid #0A2463;
            padding: 6px 8px;
            border-radius: 6px;
            margin: 2px 0;
            transition: all 0.2s;
            cursor: default;
            height: 100%;
            min-height: 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .class-block:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.18);
            z-index: 10;
            position: relative;
        }

        .class-block .course-name {
            font-weight: 700;
            font-size: 13px;
            color: #1f2937;
            line-height: 1.2;
        }

        .class-block .course-code {
            font-size: 10px;
            color: #6b7280;
            font-weight: 500;
            margin-top: 1px;
        }

        .class-block .class-meta {
            display: flex;
            gap: 6px;
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
            flex-wrap: wrap;
        }

        .class-block .class-meta span {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .class-block .class-meta i {
            font-size: 10px;
        }

        .class-block .class-time {
            font-size: 10px;
            color: #0A2463;
            font-weight: 600;
        }

        .class-block .session-badge {
            font-size: 8px;
            padding: 1px 6px;
            border-radius: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .session-badge.lecture {
            background: #e3f2fd;
            color: #0A2463;
        }

        .session-badge.lab {
            background: #fef3c7;
            color: #92400e;
        }

        .session-badge.tutorial {
            background: #dcfce7;
            color: #166534;
        }

        .session-badge.seminar {
            background: #dbeafe;
            color: #1e40af;
        }

        .session-badge.workshop {
            background: #f3e8ff;
            color: #6b21a8;
        }

        /* ============================================
                       EMPTY STATE
                       ============================================ */
        .no-schedule {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .no-schedule .icon {
            font-size: 48px;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .no-schedule h5 {
            color: #1f2937;
            margin-bottom: 8px;
        }

        .no-schedule .btn-primary {
            background: #0A2463;
            color: white;
            padding: 8px 24px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-top: 12px;
            transition: all 0.2s;
        }

        .no-schedule .btn-primary:hover {
            background: #061840;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.25);
        }

        /* ============================================
                       ALERT
                       ============================================ */
        .alert {
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* ============================================
                       RESPONSIVE
                       ============================================ */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .timetable-header {
                flex-direction: column;
                align-items: stretch;
            }

            .timetable-header .left {
                flex-wrap: wrap;
            }

            .timetable-header .actions {
                flex-wrap: wrap;
            }

            .filter-form {
                flex-wrap: wrap;
            }

            .timetable-table td {
                height: 55px;
                min-width: 70px;
                padding: 2px;
            }

            .class-block {
                padding: 3px 5px;
                min-height: 40px;
            }

            .class-block .course-name {
                font-size: 10px;
            }

            .class-block .course-code {
                font-size: 8px;
            }

            .class-block .class-meta {
                font-size: 8px;
                gap: 3px;
            }

            .class-block .class-meta span:not(.class-time) {
                display: none;
            }

            .next-class-widget {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .next-class-widget .info {
                justify-content: center;
            }

            .timetable-table th .day-label {
                font-size: 12px;
            }

            .timetable-table th .day-date {
                font-size: 10px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .stat-card {
                padding: 10px 14px;
            }

            .stat-card .value {
                font-size: 18px;
            }

            .timetable-table td {
                height: 45px;
                min-width: 55px;
            }

            .next-class-widget .countdown .time {
                font-size: 22px;
            }
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- ==========================================
                STATS CARDS
                ========================================== -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Courses</div>
            <div class="value">{{ $stats['total_courses'] ?? 0 }}</div>
            <div class="sub">{{ $stats['departments'] ?? 0 }} departments</div>
        </div>

        <div class="stat-card">
            <div class="label">Weekly Hours</div>
            <div class="value">{{ $stats['total_weekly_hours'] ?? 0 }}h</div>
            <div class="sub">{{ $stats['total_classes'] ?? 0 }} classes</div>
        </div>

        <div class="stat-card">
            <div class="label">Year Levels</div>
            <div class="value">{{ $stats['year_levels'] ?? 0 }}</div>
            <div class="sub">Different levels</div>
        </div>

        <div class="stat-card">
            <div class="label">Busiest Day</div>
            <div class="value">{{ $stats['busiest_day'] ?? 'N/A' }}</div>
            <div class="sub">Most classes</div>
        </div>

        <div class="stat-card">
            <div class="label">Free Periods</div>
            <div class="value">{{ $stats['free_periods'] ?? 0 }}</div>
            <div class="sub">This week</div>
        </div>
    </div>

    <!-- ==========================================
                NEXT CLASS WIDGET
                ========================================== -->
    @if ($nextClass)
        <div class="next-class-widget">
            <div class="info">
                <div class="icon"><i class="bi bi-clock"></i></div>
                <div>
                    <div class="label">
                        @if ($nextClass['is_tomorrow'] ?? false)
                            Next Class (Tomorrow)
                        @else
                            Next Class
                        @endif
                    </div>
                    <div class="name">{{ $nextClass['course_name'] ?? 'N/A' }}</div>
                    <div class="meta">
                        <span><i class="bi bi-code-square"></i> {{ $nextClass['course_code'] ?? 'N/A' }}</span>
                        <span><i class="bi bi-clock"></i> {{ $nextClass['time'] ?? 'N/A' }}</span>
                        <span><i class="bi bi-door-open"></i> Room {{ $nextClass['room'] ?? 'N/A' }}</span>
                        <span><i class="bi bi-calendar"></i> {{ $nextClass['day'] ?? '' }}</span>
                    </div>
                </div>
            </div>
            <div class="countdown">
                <div class="label">Starts in</div>
                <div class="time" id="countdownTimer">--:--:--</div>
            </div>
        </div>
    @else
        <div class="next-class-widget" style="background: #6b7280;">
            <div class="info">
                <div class="icon"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <div class="label">No More Classes</div>
                    <div class="name">Enjoy your free time! 🎉</div>
                    <div class="meta">
                        <span>No upcoming classes scheduled</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('lecturer.timetable.manage') }}" class="btn-manage"
                style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); text-decoration: none;">
                <i class="bi bi-plus-circle"></i> Add Schedule
            </a>
        </div>
    @endif

    <!-- ==========================================
                TIMETABLE CALENDAR VIEW
                ========================================== -->
    <div class="timetable-container">
        <!-- Header -->
        <div class="timetable-header">
            <div class="left">
                <h5><i class="bi bi-calendar-week"></i> Timetable</h5>
                <span class="week-range">
                    {{ $weekStart->format('M d') }} – {{ $weekEnd->format('M d, Y') }}
                </span>
            </div>
            <div class="actions">
                <!-- Filters -->
                <form method="GET" action="{{ route('lecturer.timetable.index') }}" class="filter-form">
                    @if ($academicYears->isNotEmpty())
                        <select name="academic_year">
                            <option value="">All Years</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    @if ($semesters->isNotEmpty())
                        <select name="semester">
                            <option value="">All Semesters</option>
                            @foreach ($semesters as $sem)
                                <option value="{{ $sem }}" {{ $semester == $sem ? 'selected' : '' }}>
                                    {{ $sem }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <select name="session_type">
                        <option value="">All Types</option>
                        @foreach ($sessionTypes as $type)
                            <option value="{{ $type }}" {{ $sessionType == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-apply">Apply</button>
                </form>

                <!-- Export -->
                <div class="btn-group">
                    <button class="btn-export dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('lecturer.timetable.export', request()->query()) }}">
                                <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('lecturer.timetable.export.pdf', request()->query()) }}">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('lecturer.timetable.manage') }}" class="btn-manage">
                    <i class="bi bi-gear"></i> Manage
                </a>
                <button class="btn-today" onclick="goToday()">Today</button>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="timetable-grid">
            @php
                $hasClasses = false;
                foreach ($timetable as $day) {
                    foreach ($day as $slot) {
                        if ($slot !== null) {
                            $hasClasses = true;
                            break 2;
                        }
                    }
                }
            @endphp

            @if ($hasClasses)
                <table class="timetable-table">
                    <thead>
                        <tr>
                            <th style="min-width: 80px; width: 80px;">Time</th>
                            @foreach ($days as $day)
                                <th
                                    class="{{ $day['is_today'] ? 'today' : '' }} {{ $day['is_weekend'] ? 'weekend' : '' }}">
                                    <span class="day-label">{{ $day['short'] }}</span>
                                    <span class="day-date">{{ $day['date'] }} {{ $day['month'] }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($timeSlots as $slot)
                            <tr>
                                <td>
                                    <div class="time-slot">{{ $slot['time'] }}</div>
                                </td>
                                @foreach ($days as $dayIndex => $day)
                                    <td
                                        class="{{ $day['is_today'] ? 'today' : '' }} {{ $day['is_weekend'] ? 'weekend' : '' }}">
                                        @php
                                            $class = $timetable[$dayIndex][$slot['period']] ?? null;
                                        @endphp
                                        @if ($class)
                                            <div class="class-block">
                                                <div class="course-name">{{ $class['course_name'] }}</div>
                                                <div class="course-code">{{ $class['course_code'] }}</div>
                                                <div class="class-meta">
                                                    <span class="class-time"><i class="bi bi-clock"></i>
                                                        {{ $class['time'] }}</span>
                                                    <span><i class="bi bi-door-open"></i> {{ $class['room'] }}</span>
                                                    @if ($class['session_type'] ?? null)
                                                        <span class="session-badge {{ $class['session_type'] }}">
                                                            {{ $class['session_type'] }}
                                                        </span>
                                                    @endif
                                                    @if ($class['year'] ?? null)
                                                        <span><i class="bi bi-mortarboard"></i>
                                                            {{ $class['year'] }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="empty-slot">—</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-schedule">
                    <div class="icon"><i class="bi bi-calendar-plus"></i></div>
                    <h5>No Schedule Added Yet</h5>
                    <p>Click "Manage Timetable" to add your courses.</p>
                    <a href="{{ route('lecturer.timetable.manage') }}" class="btn-primary">
                        <i class="bi bi-plus-circle"></i> Manage Timetable
                    </a>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function goToday() {
                const today = document.querySelector('td.today, th.today');
                if (today) {
                    today.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            @if ($nextClass && isset($nextClass['start_time']))
                let countdownDate = new Date('{{ $nextClass['start_time'] }}').getTime();
                setInterval(function() {
                    let now = new Date().getTime();
                    let distance = countdownDate - now;

                    if (distance < 0) {
                        document.getElementById('countdownTimer').innerHTML = 'Now';
                        return;
                    }

                    let hours = Math.floor(distance / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById('countdownTimer').innerHTML =
                        (hours > 0 ? hours + 'h ' : '') +
                        String(minutes).padStart(2, '0') + 'm ' +
                        String(seconds).padStart(2, '0') + 's';
                }, 1000);
            @endif
        </script>
    @endpush
@endsection
