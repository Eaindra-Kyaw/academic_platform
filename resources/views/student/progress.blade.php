@extends('layouts.app')

@section('title', 'My Progress')
@section('role', 'Student')
@section('page-title', 'Academic Progress')
@section('welcome-text', 'Track your academic performance')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        /* ============================================
                   STATS CARDS
                   ============================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 18px 20px;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .stat-card .label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
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
            color: #10b981;
        }

        .stat-card .value.orange {
            color: #f59e0b;
        }

        .stat-card .value.red {
            color: #ef4444;
        }

        .stat-card .value.blue {
            color: #3b82f6;
        }

        /* ============================================
                   COURSE CARDS
                   ============================================ */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 18px;
        }

        .course-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            transition: all 0.2s;
        }

        .course-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transform: translateY(-3px);
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

        .course-card-header .course-title {
            display: flex;
            flex-direction: column;
        }

        .course-card-header .course-title .name {
            font-weight: 600;
            font-size: 15px;
            color: #1f2937;
        }

        .course-card-header .course-title .code {
            font-size: 12px;
            color: #6b7280;
        }

        .course-card-header .status-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge.eligible {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.not_eligible {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.pending {
            background: #e5e7eb;
            color: #6b7280;
        }

        .course-card-body {
            padding: 16px 18px;
        }

        /* Progress Bar */
        .progress-section {
            margin-bottom: 12px;
        }

        .progress-section .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #374151;
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

        /* Stats Grid in Card */
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
            color: #1f2937;
        }

        .course-stats .stat-item .number.green {
            color: #10b981;
        }

        .course-stats .stat-item .number.orange {
            color: #f59e0b;
        }

        .course-stats .stat-item .number.red {
            color: #ef4444;
        }

        .course-stats .stat-item .number.blue {
            color: #3b82f6;
        }

        .course-stats .stat-item .label {
            font-size: 11px;
            color: #6b7280;
        }

        /* ============================================
                   EMPTY STATE
                   ============================================ */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state .icon {
            font-size: 56px;
            color: #d1d5db;
            margin-bottom: 16px;
        }

        .empty-state h5 {
            color: #1f2937;
            margin-bottom: 8px;
        }

        /* ============================================
                   RESPONSIVE
                   ============================================ */
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

    <!-- ==========================================
            STATS CARDS
            ========================================== -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="icon"><i class="bi bi-book" style="color:#3b82f6;"></i></span>
            <div class="label">Total Courses</div>
            <div class="value">{{ $enrollments->count() ?? 0 }}</div>
            <div class="sub">Active enrollments</div>
        </div>

        <div class="stat-card">
            <span class="icon"><i class="bi bi-check-circle" style="color:#10b981;"></i></span>
            <div class="label">Attendance Rate</div>
            <div class="value green">
                @php
                    $total = 0;
                    $present = 0;
                    foreach ($courseProgress ?? [] as $p) {
                        $total += $p['total'] ?? 0;
                        $present += $p['attended'] ?? 0;
                    }
                    $rate = $total > 0 ? round(($present / $total) * 100) : 0;
                @endphp
                {{ $rate }}%
            </div>
            <div class="sub">{{ $rate >= 75 ? '✅ Good standing' : '⚠️ Needs improvement' }}</div>
        </div>

        <div class="stat-card">
            <span class="icon"><i class="bi bi-star" style="color:#f59e0b;"></i></span>
            <div class="label">Avg Roll Call</div>
            <div class="value orange">
                @php
                    $rollCallTotal = 0;
                    $rollCallCount = 0;
                    foreach ($courseProgress ?? [] as $p) {
                        if (isset($p['roll_call_mark'])) {
                            $rollCallTotal += $p['roll_call_mark'];
                            $rollCallCount++;
                        }
                    }
                    $avgRollCall = $rollCallCount > 0 ? round($rollCallTotal / $rollCallCount, 1) : 0;
                @endphp
                {{ $avgRollCall }}/10
            </div>
            <div class="sub">Academic performance</div>
        </div>

        <div class="stat-card">
            <span class="icon"><i class="bi bi-people" style="color:#800000;"></i></span>
            <div class="label">Eligible</div>
            <div class="value">
                @php
                    $eligible = 0;
                    foreach ($courseProgress ?? [] as $p) {
                        if (($p['eligibility_status'] ?? '') === 'eligible') {
                            $eligible++;
                        }
                    }
                @endphp
                {{ $eligible }}
            </div>
            <div class="sub">of {{ count($courseProgress ?? []) }} courses</div>
        </div>
    </div>

    <!-- ==========================================
            COURSE PROGRESS CARDS
            ========================================== -->
    <h5 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">
        <i class="bi bi-grid-3x3-gap-fill" style="color:#800000;"></i> Course Progress
    </h5>

    @if (isset($courseProgress) && count($courseProgress) > 0)
        <div class="course-grid">
            @foreach ($courseProgress as $progress)
                @php
                    $percentage = $progress['percentage'] ?? 0;
                    $attendancePercent = $progress['attendance_percentage'] ?? 0;
                    $rollCall = $progress['roll_call_mark'] ?? 0;
                    $status = $progress['eligibility_status'] ?? 'pending';
                    $course = $progress['course'];

                    // Determine color based on percentage
                    if ($percentage >= 75) {
                        $color = '#10b981';
                        $statusBadge = 'eligible';
                    } elseif ($percentage >= 50) {
                        $color = '#f59e0b';
                        $statusBadge = 'warning';
                    } else {
                        $color = '#ef4444';
                        $statusBadge = 'not_eligible';
                    }

                    // Text color for stats
                    $textColor = $percentage >= 75 ? 'green' : ($percentage >= 50 ? 'orange' : 'red');
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
                        <!-- Progress Bar -->
                        <div class="progress-section">
                            <div class="progress-header">
                                <span>Attendance Progress</span>
                                <span><strong>{{ $percentage }}%</strong></span>
                            </div>
                            <div class="progress-bar">
                                <div class="fill" style="width: {{ $percentage }}%; background: {{ $color }};">
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
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
                                <div class="number orange">{{ $rollCall }}</div>
                                <div class="label">Roll Call</div>
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
            <a href="{{ route('student.courses.available') }}"
                style="color:#800000; text-decoration:none; font-weight:500;">
                <i class="bi bi-plus-circle"></i> Browse Courses
            </a>
        </div>
    @endif
@endsection
