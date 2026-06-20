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
        .timetable-container {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-top: 20px;
        }

        .timetable-header {
            padding: 16px 20px;
            background: #fafafa;
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

        .timetable-header .btn-today {
            background: #800000;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .timetable-header .btn-today:hover {
            background: #6b0000;
        }

        /* Timetable Grid */
        .timetable-grid {
            overflow-x: auto;
            padding: 0;
        }

        .timetable-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .timetable-table th {
            padding: 12px 16px;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }

        .timetable-table th .day-label {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
            display: block;
        }

        .timetable-table th .day-date {
            font-size: 11px;
            color: #6b7280;
            font-weight: 400;
        }

        .timetable-table th.today {
            background: #fef2f2;
        }

        .timetable-table th.today .day-label {
            color: #800000;
        }

        .timetable-table td {
            padding: 8px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
            height: 80px;
            min-width: 120px;
        }

        .timetable-table td .time-slot {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
            padding: 4px 0;
        }

        .timetable-table td.today {
            background: #fafafa;
        }

        /* Class Block */
        .class-block {
            background: #fef2f2;
            border-left: 3px solid #800000;
            padding: 8px 10px;
            border-radius: 6px;
            margin: 2px 0;
            transition: all 0.2s;
            cursor: pointer;
        }

        .class-block:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(128, 0, 0, 0.1);
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

        .empty-slot {
            color: #d1d5db;
            font-size: 12px;
            text-align: center;
            padding: 16px 0;
        }

        /* Next Class Widget */
        .next-class-widget {
            background: #800000;
            color: white;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .next-class-widget .info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .next-class-widget .info .label {
            font-size: 12px;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .next-class-widget .info .class-details .name {
            font-size: 18px;
            font-weight: 600;
        }

        .next-class-widget .info .class-details .meta {
            font-size: 14px;
            opacity: 0.9;
        }

        .next-class-widget .countdown {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .next-class-widget .countdown .time {
            font-size: 20px;
            font-weight: 700;
        }

        /* Empty State */
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
        }

        @media (max-width: 768px) {
            .timetable-header {
                flex-direction: column;
                align-items: stretch;
            }

            .timetable-table td {
                height: 60px;
                min-width: 80px;
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

            .next-class-widget {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .next-class-widget .info {
                justify-content: center;
            }
        }
    </style>

    <!-- Next Class Widget -->
    @if ($nextClass ?? null)
        <div class="next-class-widget">
            <div class="info">
                <div>
                    <div class="label">Next Class</div>
                    <div class="class-details">
                        <div class="name">{{ $nextClass['course_name'] ?? 'N/A' }}</div>
                        <div class="meta">
                            {{ $nextClass['course_code'] ?? '' }}
                            @if ($nextClass['room'] ?? null)
                                · Room {{ $nextClass['room'] }}
                            @endif
                            @if ($nextClass['time'] ?? null)
                                · {{ $nextClass['time'] }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="countdown">
                Starts in <span class="time" id="countdownTimer">--:--</span>
            </div>
        </div>
    @endif

    <!-- Timetable -->
    <div class="timetable-container">
        <div class="timetable-header">
            <div>
                <h5><i class="bi bi-calendar-week"></i> Weekly Timetable</h5>
                <span class="week-range" id="weekRange">
                    {{ $weekStart ?? now()->startOfWeek()->format('M d') }} –
                    {{ $weekEnd ?? now()->endOfWeek()->format('M d, Y') }}
                </span>
            </div>
            <button class="btn-today" onclick="goToday()">Today</button>
        </div>

        <div class="timetable-grid">
            <table class="timetable-table">
                <thead>
                    <tr>
                        <th style="min-width: 60px; width: 60px;">Time</th>
                        @foreach ($days as $day)
                            <th class="{{ $day['is_today'] ? 'today' : '' }}">
                                <span class="day-label">{{ $day['label'] }}</span>
                                <span class="day-date">{{ $day['date'] }}</span>
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
                                            <div class="course-name">{{ $class['course_name'] ?? 'N/A' }}</div>
                                            <div class="course-code">{{ $class['course_code'] ?? '' }}</div>
                                            <div class="class-meta">
                                                <span class="class-time"><i class="bi bi-clock"></i>
                                                    {{ $class['time'] ?? '' }}</span>
                                                @if ($class['room'] ?? null)
                                                    <span><i class="bi bi-door-open"></i> {{ $class['room'] }}</span>
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
        </div>
    </div>

    @push('scripts')
        <script>
            function goToday() {
                const today = document.querySelector('td.today');
                if (today) {
                    today.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            @if ($nextClass ?? null)
                let countdownDate = new Date('{{ $nextClass['start_time'] ?? now()->addHours(2) }}').getTime();
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
                        minutes + 'm ' + seconds + 's';
                }, 1000);
            @endif
        </script>
    @endpush
@endsection
