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
            width: 100%;
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
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .timetable-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            table-layout: fixed;
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
            word-break: break-word;
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

        /* ============================================
               RESPONSIVE - FIXED
               ============================================ */

        /* Laptop / Desktop */
        @media (max-width: 1200px) {
            .timetable-table {
                font-size: 12px;
            }

            .timetable-table th {
                padding: 10px 10px;
            }

            .timetable-table th .day-label {
                font-size: 13px;
            }

            .timetable-table td {
                height: 65px;
                padding: 4px;
            }

            .class-block {
                padding: 4px 8px;
            }

            .class-block .course-name {
                font-size: 12px;
            }

            .class-block .class-meta {
                font-size: 10px;
            }
        }

        /* Tablet */
        @media (max-width: 992px) {
            .timetable-table {
                font-size: 11px;
                min-width: 700px;
            }

            .timetable-table th {
                padding: 8px 8px;
            }

            .timetable-table th .day-label {
                font-size: 12px;
            }

            .timetable-table th .day-date {
                font-size: 10px;
            }

            .timetable-table td {
                height: 60px;
                padding: 4px;
            }

            .class-block {
                padding: 3px 6px;
            }

            .class-block .course-name {
                font-size: 11px;
            }

            .class-block .course-code {
                font-size: 10px;
            }

            .class-block .class-meta {
                font-size: 10px;
                gap: 4px;
            }

            .class-block .class-time {
                font-size: 10px;
            }

            .timetable-header {
                padding: 12px 16px;
            }

            .timetable-header h5 {
                font-size: 15px;
            }
        }

        /* Mobile Landscape */
        @media (max-width: 768px) {
            .timetable-table {
                font-size: 10px;
                min-width: 600px;
            }

            .timetable-table th {
                padding: 6px 6px;
            }

            .timetable-table th .day-label {
                font-size: 11px;
            }

            .timetable-table th .day-date {
                font-size: 9px;
            }

            .timetable-table td {
                height: 55px;
                padding: 3px;
            }

            .timetable-table td .time-slot {
                font-size: 9px;
                padding: 2px 0;
            }

            .class-block {
                padding: 3px 5px;
                border-left-width: 2px;
            }

            .class-block .course-name {
                font-size: 10px;
            }

            .class-block .course-code {
                font-size: 9px;
            }

            .class-block .class-meta {
                font-size: 9px;
                gap: 3px;
                margin-top: 2px;
            }

            .class-block .class-meta span {
                gap: 2px;
            }

            .class-block .class-meta i {
                font-size: 8px;
            }

            .class-block .class-time {
                font-size: 9px;
            }

            .timetable-table td .empty-slot {
                font-size: 10px;
                padding: 10px 0;
            }

            .timetable-header {
                padding: 10px 14px;
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .timetable-header .week-range {
                font-size: 12px;
            }

            .timetable-header .btn-today {
                align-self: flex-start;
                font-size: 12px;
                padding: 4px 14px;
            }
        }

        /* Mobile Portrait */
        @media (max-width: 480px) {
            .timetable-table {
                font-size: 9px;
                min-width: 480px;
            }

            .timetable-table th {
                padding: 4px 4px;
            }

            .timetable-table th .day-label {
                font-size: 10px;
            }

            .timetable-table th .day-date {
                font-size: 8px;
            }

            .timetable-table td {
                height: 50px;
                padding: 2px;
            }

            .timetable-table td .time-slot {
                font-size: 8px;
                padding: 1px 0;
            }

            .class-block {
                padding: 2px 4px;
                border-left-width: 2px;
                border-radius: 4px;
                margin: 1px 0;
            }

            .class-block .course-name {
                font-size: 9px;
            }

            .class-block .course-code {
                font-size: 8px;
            }

            .class-block .class-meta {
                font-size: 8px;
                gap: 2px;
                margin-top: 1px;
            }

            .class-block .class-meta span {
                gap: 1px;
            }

            .class-block .class-meta i {
                font-size: 7px;
            }

            .class-block .class-time {
                font-size: 8px;
            }

            .timetable-table td .empty-slot {
                font-size: 8px;
                padding: 6px 0;
            }

            .timetable-header {
                padding: 8px 12px;
            }

            .timetable-header h5 {
                font-size: 13px;
            }

            .timetable-header .week-range {
                font-size: 11px;
            }

            .timetable-header .btn-today {
                font-size: 11px;
                padding: 3px 12px;
            }

            .timetable-container {
                border-radius: 8px;
            }

            .no-schedule {
                padding: 40px 16px;
            }

            .no-schedule .icon {
                font-size: 36px;
            }

            .no-schedule h5 {
                font-size: 16px;
            }

            .no-schedule p {
                font-size: 13px;
            }
        }

        /* Very Small Phones */
        @media (max-width: 380px) {
            .timetable-table {
                font-size: 8px;
                min-width: 380px;
            }

            .timetable-table th {
                padding: 3px 2px;
            }

            .timetable-table th .day-label {
                font-size: 9px;
            }

            .timetable-table th .day-date {
                font-size: 7px;
            }

            .timetable-table td {
                height: 40px;
                padding: 1px;
            }

            .class-block .course-name {
                font-size: 8px;
            }

            .class-block .course-code {
                font-size: 7px;
            }

            .class-block .class-meta {
                font-size: 7px;
            }

            .class-block .class-time {
                font-size: 7px;
            }

            .timetable-table td .empty-slot {
                font-size: 7px;
                padding: 4px 0;
            }

            .timetable-header h5 {
                font-size: 12px;
            }

            .timetable-header .week-range {
                font-size: 10px;
            }

            .timetable-header .btn-today {
                font-size: 10px;
                padding: 2px 10px;
            }
        }

        /* Print Styles */
        @media print {
            .btn-today {
                display: none !important;
            }

            .timetable-container {
                border: 1px solid #ddd;
                border-radius: 0;
            }

            .timetable-header {
                background: white !important;
                border-bottom: 2px solid #ddd;
            }

            .class-block {
                background: #f5f5f5 !important;
                border-left-color: #333 !important;
            }

            .timetable-grid {
                overflow: visible !important;
            }

            .timetable-table {
                font-size: 10px !important;
                min-width: 100% !important;
            }

            .timetable-table th {
                background: #f5f5f5 !important;
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
            <button class="btn-today" onclick="goToday()">📅 Today</button>
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
                            <th style="width: 70px; min-width: 60px;">Time</th>
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
                    block: 'center',
                    inline: 'center'
                });
            }
        }
    </script>
@endsection
