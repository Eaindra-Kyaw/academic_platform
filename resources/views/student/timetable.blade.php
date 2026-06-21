@extends('layouts.app')

@section('title', 'My Timetable')
@section('role', 'Student')
@section('page-title', 'My Timetable')
@section('welcome-text', 'Your weekly class schedule')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        .timetable-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
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

        .timetable-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        .timetable-header .week-range {
            color: #6b7280;
            font-size: 14px;
        }

        .timetable-grid {
            overflow-x: auto;
            padding: 0;
        }

        .timetable-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
            font-size: 13px;
        }

        .timetable-table th {
            padding: 12px 14px;
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
            background: #fef2f2;
        }

        .timetable-table th.today .day-label {
            color: #800000;
        }

        .timetable-table td {
            padding: 6px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
            height: 70px;
            min-width: 110px;
        }

        .timetable-table td .time-slot {
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
            padding: 4px 0;
        }

        .timetable-table td.today {
            background: #fafafa;
        }

        .timetable-table td .empty-slot {
            color: #d1d5db;
            font-size: 12px;
            text-align: center;
            padding: 16px 0;
        }

        .class-block {
            background: #fef2f2;
            border-left: 3px solid #800000;
            padding: 6px 10px;
            border-radius: 6px;
            margin: 2px 0;
            transition: all 0.2s;
            cursor: default;
        }

        .class-block .course-name {
            font-weight: 600;
            font-size: 13px;
            color: #1f2937;
        }

        .class-block .course-code {
            font-size: 11px;
            color: #6b7280;
        }

        .class-block .class-meta {
            display: flex;
            gap: 8px;
            font-size: 11px;
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
            font-size: 11px;
            color: #800000;
            font-weight: 500;
        }

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

        .no-schedule p {
            color: #6b7280;
        }

        .btn-today {
            background: #800000;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-today:hover {
            background: #5f0000;
        }

        @media (max-width: 768px) {
            .timetable-table td {
                height: 60px;
                min-width: 80px;
                padding: 4px;
            }

            .class-block {
                padding: 4px 6px;
            }

            .class-block .course-name {
                font-size: 11px;
            }

            .class-block .class-meta {
                font-size: 10px;
                gap: 4px;
            }

            .timetable-table th .day-label {
                font-size: 12px;
            }

            .timetable-table th .day-date {
                font-size: 10px;
            }

            .timetable-header {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <div class="timetable-container">
        <div class="timetable-header">
            <div>
                <h5><i class="bi bi-calendar-week"></i> My Timetable</h5>
                <span class="week-range">
                    {{ $weekStart->format('M d') }} – {{ $weekEnd->format('M d, Y') }}
                </span>
            </div>
            <button class="btn-today" onclick="goToday()">Today</button>
        </div>

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
                                <th class="{{ $day['is_today'] ? 'today' : '' }}">
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
                                    <td class="{{ $day['is_today'] ? 'today' : '' }}">
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
                    <h5>No Classes Scheduled</h5>
                    <p>You don't have any classes in your timetable yet.</p>
                </div>
            @endif
        </div>
    </div>

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
    </script>
@endsection
