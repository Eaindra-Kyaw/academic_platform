@extends('layouts.app')

@section('title', $course->course_code . ' - ' . ucfirst($period) . ' Attendance')
@section('role', 'Lecturer')
@section('page-title', '📊 ' . $course->course_code . ' - ' . ucfirst($period) . ' Attendance')
@section('welcome-text', $start->format('M d, Y') . ' – ' . $end->format('M d, Y'))

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        .period-tabs .btn {
            border-radius: 20px;
            padding: 5px 18px;
            font-weight: 600;
            font-size: 13px;
        }

        .period-tabs .btn-primary {
            background: #0A2463;
            border-color: #0A2463;
        }

        .period-tabs .btn-outline-secondary {
            color: #6b7280;
            border-color: #d1d5db;
        }

        .stats-mini {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .stats-mini .box {
            background: white;
            border-radius: 10px;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .stats-mini .box .num {
            font-size: 24px;
            font-weight: 700;
            color: #0A2463;
        }

        .stats-mini .box .label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .elig-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .elig-eligible {
            background: #d1fae5;
            color: #065f46;
        }

        .elig-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .elig-not_eligible {
            background: #fee2e2;
            color: #991b1b;
        }

        .table-wrap {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .table-wrap th {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .progress-bar-sm {
            height: 6px;
            border-radius: 10px;
            background: #e5e7eb;
            overflow: hidden;
            width: 100px;
            display: inline-block;
            vertical-align: middle;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="period-tabs">
            <a href="?period=weekly" class="btn {{ $period == 'weekly' ? 'btn-primary' : 'btn-outline-secondary' }}">📅 This
                Week</a>
            <a href="?period=monthly" class="btn {{ $period == 'monthly' ? 'btn-primary' : 'btn-outline-secondary' }}">📅 This
                Month</a>
        </div>
        <a href="{{ route('lecturer.students') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Students
        </a>
    </div>

    <div class="stats-mini">
        <div class="box">
            <div class="num">{{ $courseStats['total_students'] }}</div>
            <div class="label">Total Students</div>
        </div>
        <div class="box">
            <div class="num">{{ $courseStats['avg_attendance'] }}%</div>
            <div class="label">Average Attendance</div>
        </div>
        <div class="box">
            <div class="num">{{ $courseStats['total_periods'] }}</div>
            <div class="label">Total Periods Conducted</div>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Attended</th>
                    <th>Total Periods</th>
                    <th>Attendance</th>
                    <th>Eligibility</th>
                </tr>
            </thead>
            <tbody>
                @forelse($studentData as $index => $data)
                    @php
                        $att = $data['attendance'];
                        $color = $att >= 75 ? '#10b981' : ($att >= 60 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $data['student']->name }}</strong>
                            <br><small class="text-muted">{{ $data['student']->student_id ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $data['attended'] }}</td>
                        <td>{{ $data['total'] }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-weight:700; min-width:45px;">{{ $att }}%</span>
                                <div class="progress-bar-sm">
                                    <div class="fill"
                                        style="width:{{ $att }}%; background:{{ $color }};"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="elig-badge elig-{{ $data['eligibility'] }}">
                                {{ ucfirst(str_replace('_', ' ', $data['eligibility'])) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No students enrolled or no sessions conducted in this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
