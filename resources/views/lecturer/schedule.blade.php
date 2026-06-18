@extends('layouts.app')

@section('title', 'My Schedule')
@section('role', 'Lecturer')
@section('page-title', 'Weekly Schedule')
@section('welcome-text', 'Your teaching schedule')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .schedule-table th {
            padding: 0.75rem;
            text-align: center;
            background: #800000;
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid #6b0000;
        }

        .schedule-table td {
            padding: 0.75rem;
            text-align: center;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .course-cell {
            background: #fef3c7;
            border-radius: 0.5rem;
            padding: 0.5rem;
        }

        .course-code {
            font-weight: 700;
            color: #800000;
            font-size: 0.75rem;
        }

        .course-name {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .room {
            font-size: 0.65rem;
            color: #10b981;
            margin-top: 0.25rem;
        }

        .empty-cell {
            color: #9ca3af;
            font-size: 0.7rem;
        }

        @media (max-width: 768px) {
            .schedule-table {
                min-width: 700px;
            }

            .schedule-table th,
            .schedule-table td {
                padding: 0.5rem;
                font-size: 0.7rem;
            }
        }
    </style>

    <div>
        <h3 style="color: #800000; margin-bottom: 1rem;">📅 Weekly Teaching Schedule</h3>

        <div style="overflow-x: auto;">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        @foreach ($days as $day)
                            <th>{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($timeSlots as $time)
                        <tr>
                            <td style="background: #f9fafb; font-weight: 600;">
                                {{ \Carbon\Carbon::parse($time)->format('g:i A') }}</td>
                            @foreach ($days as $day)
                                <td>
                                    @php
                                        $course = $schedule[$day]->first(function ($c) use ($time) {
                                            return $c->schedule_time &&
                                                \Carbon\Carbon::parse($c->schedule_time)->format('H:i') == $time;
                                        });
                                    @endphp
                                    @if ($course)
                                        <div class="course-cell">
                                            <div class="course-code">{{ $course->course_code }}</div>
                                            <div class="course-name">{{ $course->course_name }}</div>
                                            <div class="room"><i class="bi bi-door-open"></i>
                                                {{ $course->room ?? 'TBA' }}</div>
                                        </div>
                                    @else
                                        <div class="empty-cell">—</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
