@extends('layouts.app')

@section('title', 'My Attendance')
@section('role', 'Student')
@section('page-title', '📊 My Attendance')
@section('welcome-text', 'Track your attendance across all courses with roll call breakdown')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --info: #3b82f6;
            --info-light: #dbeafe;
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
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-hover);
            border-color: var(--primary);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
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

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .course-list {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .course-list .header {
            padding: 0.75rem 1rem;
            background: var(--bg-main);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            font-weight: 700;
            color: var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            transition: var(--transition);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .course-item:last-child {
            border-bottom: none;
        }

        .course-item:hover {
            background: var(--bg-main);
        }

        .course-item .course-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .course-item .course-code {
            font-size: 0.65rem;
            color: var(--text-gray);
        }

        .attendance-pill {
            padding: 0.2rem 0.7rem;
            border-radius: 1rem;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }

        .attendance-pill.high {
            background: var(--success-light);
            color: #166534;
        }

        .attendance-pill.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .attendance-pill.low {
            background: var(--danger-light);
            color: #991b1b;
        }

        .progress-bar-custom {
            height: 6px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.25rem;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.8s ease;
        }

        .badge-eligible {
            background: var(--success-light);
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-warning {
            background: var(--warning-light);
            color: #854d0e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-not_eligible {
            background: var(--danger-light);
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-low {
            background: var(--success-light);
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-medium {
            background: var(--warning-light);
            color: #854d0e;
        }

        .badge-high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .rollcall-mini {
            display: flex;
            gap: 0.3rem;
            justify-content: center;
            font-size: 0.65rem;
            color: var(--text-gray);
        }

        .rollcall-mini span {
            background: var(--gray-100);
            padding: 0.05rem 0.4rem;
            border-radius: 10px;
        }

        .rollcall-mini .total {
            background: rgba(10, 36, 99, 0.08);
            font-weight: 700;
            color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 2rem;
            color: #d1d5db;
        }

        .empty-state p {
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .course-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $overallAttendance ?? 0 }}%</div>
            <div class="stat-label">Overall Attendance</div>
            <div class="progress-bar-custom mt-1">
                @php
                    $att = $overallAttendance ?? 0;
                    $attClass = $att >= 75 ? 'success' : ($att >= 60 ? 'warning' : 'danger');
                @endphp
                <div class="progress-fill {{ $attClass }}" style="width:{{ $att }}%"></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-number green">{{ $presentCount ?? 0 }}</div>
            <div class="stat-label">✅ Present</div>
        </div>

        <div class="stat-card">
            <div class="stat-number yellow">{{ $lateCount ?? 0 }}</div>
            <div class="stat-label">⏰ Late</div>
        </div>

        <div class="stat-card">
            <div class="stat-number red">{{ $absentCount ?? 0 }}</div>
            <div class="stat-label">❌ Absent</div>
        </div>
    </div>

    <!-- Course Attendance with Roll Call Breakdown -->
    <div class="course-list">
        <div class="header">
            <span><i class="bi bi-book"></i> Course Attendance & Roll Call</span>
            <span style="font-size:0.7rem; color:var(--text-gray); font-weight:400;">
                {{ isset($courseData) ? count($courseData) : 0 }} courses
            </span>
        </div>

        @if (isset($courseData) && count($courseData) > 0)
            @foreach ($courseData as $data)
                @php
                    $attendance = $data['attendance'] ?? 0;
                    $attClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                    $color =
                        $attendance >= 75 ? 'var(--success)' : ($attendance >= 60 ? 'var(--warning)' : 'var(--danger)');
                    $eligibility = $data['eligibility'] ?? 'not_eligible';
                    $eligLabels = [
                        'eligible' => ['label' => 'Eligible', 'class' => 'eligible'],
                        'warning' => ['label' => 'Warning', 'class' => 'warning'],
                        'not_eligible' => ['label' => 'Not Eligible', 'class' => 'not_eligible'],
                    ];
                    $elig = $eligLabels[$eligibility] ?? $eligLabels['not_eligible'];
                    $riskLevel = $data['risk_level'] ?? 'Low';
                    $riskClass = strtolower($riskLevel);
                    $course = $data['course'] ?? null;
                @endphp
                <div class="course-item">
                    <div>
                        <div class="course-name">{{ $course->course_name ?? 'Unknown' }}</div>
                        <div class="course-code">{{ $course->course_code ?? 'N/A' }}</div>
                    </div>

                    <div style="text-align:center; min-width:100px;">
                        <span class="attendance-pill {{ $attClass }}">
                            {{ number_format($attendance, 1) }}%
                        </span>
                        <div class="progress-bar-custom mt-1">
                            <div class="progress-fill {{ $attClass }}"
                                style="width:{{ $attendance }}%; background:{{ $color }};"></div>
                        </div>
                    </div>

                    <div style="text-align:center; min-width:120px;">
                        <span style="font-weight:700; font-size:0.9rem;">{{ $data['roll_call_total'] ?? 0 }}/10</span>
                        <div class="rollcall-mini">
                            <span>C:{{ $data['consistency'] ?? 0 }}</span>
                            <span>P:{{ $data['punctuality'] ?? 0 }}</span>
                            <span>R:{{ $data['participation'] ?? 0 }}</span>
                            <span class="total">{{ $data['roll_call_total'] ?? 0 }}</span>
                        </div>
                    </div>

                    <div style="text-align:center;">
                        <span class="badge-{{ $elig['class'] }}">{{ $elig['label'] }}</span>
                        <br>
                        <span class="badge-{{ $riskClass }}" style="font-size:0.6rem;">{{ $riskLevel }}</span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="bi bi-book"></i>
                <p>No courses enrolled yet</p>
            </div>
        @endif
    </div>
@endsection
