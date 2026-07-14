@extends('layouts.app')

@section('title', 'My Progress')
@section('role', 'Student')
@section('page-title', '📈 Academic Progress')
@section('welcome-text', 'Track your academic performance with roll call breakdown')

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
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 18px 20px;
            border: 1px solid rgba(10, 36, 99, 0.06);
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        .stat-card .label {
            font-size: 12px;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 4px 0;
        }

        .stat-card .sub {
            font-size: 12px;
            color: #9ca3af;
        }

        .stat-card .icon {
            float: right;
            font-size: 28px;
        }

        .stat-card .value.green {
            color: var(--success);
        }

        .stat-card .value.orange {
            color: var(--warning);
        }

        .stat-card .value.red {
            color: var(--danger);
        }

        .stat-card .value.blue {
            color: var(--info);
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 18px;
        }

        .course-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .course-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-3px);
            border-color: var(--primary);
        }

        .course-card-header {
            padding: 14px 18px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .course-card-header .course-title .name {
            font-weight: 600;
            font-size: 15px;
            color: var(--text-dark);
        }

        .course-card-header .course-title .code {
            font-size: 12px;
            color: var(--text-gray);
        }

        .course-card-header .status-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge.eligible {
            background: var(--success-light);
            color: #166534;
        }

        .status-badge.warning {
            background: var(--warning-light);
            color: #92400e;
        }

        .status-badge.not_eligible {
            background: var(--danger-light);
            color: #991b1b;
        }

        .status-badge.pending {
            background: #e5e7eb;
            color: var(--text-gray);
        }

        .course-card-body {
            padding: 16px 18px;
        }

        .progress-section {
            margin-bottom: 12px;
        }

        .progress-section .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .progress-section .progress-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-section .progress-bar .fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.8s ease;
        }

        .rollcall-progress {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            font-size: 0.7rem;
            margin-top: 0.2rem;
            flex-wrap: wrap;
        }

        .rollcall-progress span {
            background: var(--gray-100);
            padding: 0.05rem 0.5rem;
            border-radius: 10px;
        }

        .rollcall-progress .total {
            background: rgba(10, 36, 99, 0.08);
            font-weight: 700;
            color: var(--primary);
        }

        .course-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }

        .course-stats .stat-item {
            text-align: center;
        }

        .course-stats .stat-item .number {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .course-stats .stat-item .number.green {
            color: var(--success);
        }

        .course-stats .stat-item .number.orange {
            color: var(--warning);
        }

        .course-stats .stat-item .number.red {
            color: var(--danger);
        }

        .course-stats .stat-item .number.blue {
            color: var(--info);
        }

        .course-stats .stat-item .label {
            font-size: 11px;
            color: var(--text-gray);
        }

        .badge-low {
            background: var(--success-light);
            color: #166534;
            padding: 0.1rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
        }

        .badge-medium {
            background: var(--warning-light);
            color: #854d0e;
            padding: 0.1rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
        }

        .badge-high {
            background: var(--danger-light);
            color: #991b1b;
            padding: 0.1rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-gray);
        }

        .empty-state .icon {
            font-size: 56px;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .empty-state h5 {
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .empty-state a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .empty-state a:hover {
            color: var(--primary-dark);
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .course-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .stat-card .value {
                font-size: 22px;
            }

            .course-grid {
                grid-template-columns: 1fr;
            }

            .course-stats {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .course-card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .course-stats {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    <div class="stats-grid">
        <div class="stat-card">
            <span class="icon"><i class="bi bi-book" style="color:var(--info);"></i></span>
            <div class="label">Total Courses</div>
            <div class="value">{{ count($courseProgress ?? []) }}</div>
            <div class="sub">Active enrollments</div>
        </div>

        <div class="stat-card">
            <span class="icon"><i class="bi bi-check-circle" style="color:var(--success);"></i></span>
            <div class="label">Attendance Rate</div>
            @php
                $total = 0;
                $attended = 0;
                foreach ($courseProgress ?? [] as $p) {
                    $total += $p['total'] ?? 0;
                    $attended += $p['attended'] ?? 0;
                }
                $rate = $total > 0 ? round(($attended / $total) * 100) : 0;
            @endphp
            <div class="value green">{{ $rate }}%</div>
            <div class="sub">{{ $rate >= 75 ? '✅ Good standing' : '⚠️ Needs improvement' }}</div>
        </div>

        <div class="stat-card">
            <span class="icon"><i class="bi bi-star" style="color:var(--warning);"></i></span>
            <div class="label">Avg Roll Call</div>
            @php
                $rollCallTotal = 0;
                $count = 0;
                foreach ($courseProgress ?? [] as $p) {
                    if (isset($p['roll_call_total'])) {
                        $rollCallTotal += $p['roll_call_total'];
                        $count++;
                    }
                }
                $avgRollCall = $count > 0 ? round($rollCallTotal / $count, 1) : 0;
            @endphp
            <div class="value orange">{{ $avgRollCall }}/10</div>
            <div class="sub">Academic performance</div>
        </div>

        <div class="stat-card">
            <span class="icon"><i class="bi bi-people" style="color:var(--primary);"></i></span>
            <div class="label">Eligible</div>
            @php
                $eligible = 0;
                foreach ($courseProgress ?? [] as $p) {
                    if (($p['eligibility_status'] ?? '') === 'eligible') {
                        $eligible++;
                    }
                }
            @endphp
            <div class="value">{{ $eligible }}</div>
            <div class="sub">of {{ count($courseProgress ?? []) }} courses</div>
        </div>
    </div>

    <h5 style="font-size: 16px; font-weight: 600; color: var(--text-dark); margin-bottom: 16px;">
        <i class="bi bi-grid-3x3-gap-fill" style="color:var(--primary);"></i> Course Progress
    </h5>

    @if (isset($courseProgress) && count($courseProgress) > 0)
        <div class="course-grid">
            @foreach ($courseProgress as $progress)
                @php
                    $course = $progress['course'] ?? null;
                    $attendance = $progress['attendance'] ?? 0;
                    $rollCall = $progress['roll_call_total'] ?? 0;
                    $consistency = $progress['consistency'] ?? 0;
                    $punctuality = $progress['punctuality'] ?? 0;
                    $participation = $progress['participation'] ?? 0;
                    $status = $progress['eligibility_status'] ?? 'pending';
                    $riskLevel = $progress['risk_level'] ?? 'Low';
                    $riskScore = $progress['risk_score'] ?? 0;

                    $color = $attendance >= 75 ? '#10b981' : ($attendance >= 50 ? '#f59e0b' : '#ef4444');
                    $statusBadge = $status;
                @endphp
                <div class="course-card">
                    <div class="course-card-header">
                        <div class="course-title">
                            <span class="name">{{ $course->course_name ?? 'Unknown' }}</span>
                            <span class="code">{{ $course->course_code ?? 'N/A' }}</span>
                        </div>
                        <span class="status-badge {{ $statusBadge }}">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                    </div>

                    <div class="course-card-body">
                        <div class="progress-section">
                            <div class="progress-header">
                                <span>Attendance Progress</span>
                                <span><strong>{{ $attendance }}%</strong></span>
                            </div>
                            <div class="progress-bar">
                                <div class="fill" style="width: {{ $attendance }}%; background: {{ $color }};">
                                </div>
                            </div>
                        </div>

                        <div class="rollcall-progress">
                            <span>Consistency: {{ $consistency }}/6</span>
                            <span>Punctuality: {{ $punctuality }}/2</span>
                            <span>Participation: {{ $participation }}/2</span>
                            <span class="total">Total: {{ $rollCall }}/10</span>
                        </div>

                        <div class="course-stats">
                            <div class="stat-item">
                                <div class="number green">{{ $progress['attended'] ?? 0 }}</div>
                                <div class="label">Attended</div>
                            </div>
                            <div class="stat-item">
                                <div class="number blue">{{ $progress['total'] ?? 0 }}</div>
                                <div class="label">Total</div>
                            </div>
                            <div class="stat-item">
                                <div class="number orange">
                                    <span class="badge-{{ strtolower($riskLevel) }}">{{ $riskLevel }}</span>
                                    <div style="font-size:0.6rem;">Score: {{ $riskScore }}</div>
                                </div>
                                <div class="label">Risk</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="icon"><i class="bi bi-inbox"></i></div>
            <h5>No Course Progress</h5>
            <p>You haven't enrolled in any courses yet.</p>
            <a href="{{ route('student.courses.available') }}">
                <i class="bi bi-plus-circle"></i> Browse Courses
            </a>
        </div>
    @endif
@endsection
