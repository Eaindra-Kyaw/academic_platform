@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('role', 'Student')
@section('page-title', '📊 Student Dashboard')
@section('welcome-text')
    <i class="bi bi-hand-wave" style="font-size: 1.2rem; color: var(--secondary);"></i> Welcome Back,
    {{ Auth::user()->name }} !
@endsection

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
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-800: #1e293b;
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
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-number.danger {
            color: var(--danger);
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-gray);
        }

        .progress-bar-custom {
            height: 6px;
            background: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.25rem;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
        }

        .progress-fill.success {
            background: var(--success);
        }

        .progress-fill.warning {
            background: var(--warning);
        }

        .progress-fill.danger {
            background: var(--danger);
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .course-list {
            background: var(--white);
            border-radius: 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .course-list .header {
            padding: 0.75rem 1rem;
            background: var(--gray-50);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            font-weight: 700;
            color: var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .badge-medium {
            background: var(--warning-light);
            color: #854d0e;
        }

        .badge-high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .badge-stable {
            background: var(--success-light);
            color: #166534;
        }

        .badge-excellent {
            background: var(--success-light);
            color: #166534;
        }

        .badge-at-risk {
            background: var(--warning-light);
            color: #854d0e;
        }

        .badge-critical {
            background: var(--danger-light);
            color: #991b1b;
        }

        .badge-recovering {
            background: var(--info-light);
            color: #1e40af;
        }

        .badge-present {
            background: var(--success-light);
            color: #166534;
        }

        .badge-late {
            background: var(--warning-light);
            color: #854d0e;
        }

        .badge-absent {
            background: var(--danger-light);
            color: #991b1b;
        }

        .rollcall-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .rollcall-box {
            background: var(--gray-50);
            padding: 0.5rem;
            border-radius: 0.5rem;
            text-align: center;
            border: 1px solid var(--gray-200);
        }

        .rollcall-box .value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .rollcall-box .label {
            font-size: 0.6rem;
            color: var(--text-gray);
        }

        .rollcall-box.total {
            border-color: var(--primary);
            background: #f0f4ff;
        }

        .rollcall-box.total .value {
            color: var(--primary);
            font-size: 1.4rem;
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            flex-wrap: wrap;
        }

        .course-item:last-child {
            border-bottom: none;
        }

        .course-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .course-code {
            font-size: 0.65rem;
            color: var(--text-gray);
        }

        .view-all-link {
            display: block;
            text-align: center;
            padding: 0.5rem;
            background: var(--gray-50);
            color: var(--primary);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .view-all-link:hover {
            background: var(--gray-100);
            color: var(--primary);
        }

        .benchmark-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            padding: 1rem;
        }

        .benchmark-item {
            background: var(--gray-50);
            padding: 0.75rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .benchmark-item .label {
            font-size: 0.6rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .benchmark-item .value {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .benchmark-item .diff-up {
            color: var(--success);
            font-size: 0.8rem;
        }

        .benchmark-item .diff-down {
            color: var(--danger);
            font-size: 0.8rem;
        }

        .ahs-ring {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .ahs-inner {
            width: 90px;
            height: 90px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .ahs-score {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
        }

        .chart-container {
            padding: 1rem;
            height: 200px;
        }

        .enrollment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
        }

        .enrollment-item:last-child {
            border-bottom: none;
        }

        .enrollment-course {
            font-weight: 600;
            font-size: 0.8rem;
        }

        .enrollment-date {
            font-size: 0.65rem;
            color: var(--text-gray);
        }

        /* Uni Bot */
        .uni-bot-btn {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 10px 16px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .uni-bot-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.3);
        }

        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .two-col {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .rollcall-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .benchmark-grid {
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

            .rollcall-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    <div>
        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $avgAttendance ?? 0 }}%</div>
                <div class="stat-label">Attendance Rate</div>
                <div class="progress-bar-custom mt-1">
                    @php
                        $att = $avgAttendance ?? 0;
                        $attClass = $att >= 75 ? 'success' : ($att >= 60 ? 'warning' : 'danger');
                    @endphp
                    <div class="progress-fill {{ $attClass }}" style="width:{{ $att }}%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ $avgRollCall ?? 0 }}</div>
                <div class="stat-label">Avg Roll Call</div>
                <div class="stat-label">(out of 10)</div>
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ $healthScore ?? 0 }}</div>
                <div class="stat-label">Academic Score</div>
                <div class="stat-label">
                    @php
                        $cat = $healthCategory ?? 'Stable';
                        $catClass = strtolower($cat);
                        if ($catClass == 'at risk') {
                            $catClass = 'at-risk';
                        }
                    @endphp
                    <span class="badge-{{ $catClass }}">{{ $cat }}</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">
                    @if ($eligibleCount == $totalCourses && $totalCourses > 0)
                        ✅ Eligible
                    @elseif($eligibleCount > 0)
                        ⚠️ Partial
                    @elseif($totalCourses > 0)
                        ❌ Not Eligible
                    @else
                        N/A
                    @endif
                </div>
                <div class="stat-label">Exam Status</div>
                <div class="stat-label">{{ $eligibleCount }}/{{ $totalCourses }} courses</div>
            </div>
        </div>

        {{-- <!-- Roll Call Summary Table -->
        @if (isset($evaluations) && count($evaluations) > 0)
            <div class="course-list" style="margin-bottom: 1rem;">
                <div class="header">
                    <span><i class="bi bi-clipboard-data"></i> Roll Call Summary (per course)</span>
                    <span style="font-size:0.7rem; color:var(--text-gray); font-weight:400;">
                        {{ count($evaluations) }} courses
                    </span>
                </div>
                <div style="padding: 0.5rem 1rem 1rem 1rem; overflow-x: auto;">
                    <table style="width:100%; border-collapse:collapse; font-size:0.8rem;">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:0.3rem 0.5rem;">Course</th>
                                <th style="text-align:center; padding:0.3rem 0.5rem;">Attendance</th>
                                <th style="text-align:center; padding:0.3rem 0.5rem;">Consistency<br><small>6</small></th>
                                <th style="text-align:center; padding:0.3rem 0.5rem;">Punctuality<br><small>2</small></th>
                                <th style="text-align:center; padding:0.3rem 0.5rem;">Participation<br><small>2</small></th>
                                <th style="text-align:center; padding:0.3rem 0.5rem;">Total Roll Call<br><small>/10</small>
                                </th>
                                <th style="text-align:center; padding:0.3rem 0.5rem;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evaluations as $eval)
                                @php
                                    $attClass =
                                        $eval['attendance'] >= 75
                                            ? 'success'
                                            : ($eval['attendance'] >= 60
                                                ? 'warning'
                                                : 'danger');
                                    $eligClass = $eval['eligibility'];
                                @endphp
                                <tr>
                                    <td style="padding:0.3rem 0.5rem; font-weight:600;">
                                        {{ $eval['course']->course_code ?? 'N/A' }}
                                    </td>
                                    <td style="text-align:center; padding:0.3rem 0.5rem;">
                                        <span class="badge-{{ $attClass }}">{{ $eval['attendance'] }}%</span>
                                    </td>
                                    <td style="text-align:center; padding:0.3rem 0.5rem;">{{ $eval['consistency'] }}</td>
                                    <td style="text-align:center; padding:0.3rem 0.5rem;">{{ $eval['punctuality'] }}</td>
                                    <td style="text-align:center; padding:0.3rem 0.5rem;">{{ $eval['participation'] }}</td>
                                    <td style="text-align:center; padding:0.3rem 0.5rem; font-weight:700;">
                                        {{ $eval['roll_call_total'] }}</td>
                                    <td style="text-align:center; padding:0.3rem 0.5rem;">
                                        <span
                                            class="badge-{{ $eligClass }}">{{ ucfirst(str_replace('_', ' ', $eval['eligibility'])) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif --}}

        <!-- Two Column: Health + Risk -->
        <div class="two-col">
            <div class="course-list">
                <div class="header"><i class="bi bi-heart-fill"></i> Academic Score</div>
                <div style="padding: 1rem; text-align: center;">
                    @php
                        $score = $healthScore ?? 0;
                        $degrees = ($score / 100) * 360;
                        $ringColor =
                            $score >= 90
                                ? '#10b981'
                                : ($score >= 75
                                    ? '#3b82f6'
                                    : ($score >= 50
                                        ? '#f59e0b'
                                        : '#ef4444'));
                    @endphp
                    <div class="ahs-ring"
                        style="background: conic-gradient({{ $ringColor }} 0deg {{ $degrees }}deg, #e5e7eb {{ $degrees }}deg 360deg);">
                        <div class="ahs-inner">
                            <div class="ahs-score">{{ $score }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-gray);">{{ $healthCategory ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div style="margin-top: 0.75rem; font-size: 0.65rem; color: var(--text-gray);">
                        40% Attendance | 25% Roll Call | 20% Streak | 15% Trend
                    </div>
                </div>
            </div>

            <div class="course-list">
                <div class="header"><i class="bi bi-shield-exclamation"></i> Risk & Recovery</div>
                <div style="padding: 1rem;">
                    <div>
                        Risk Level:
                        @php
                            $riskLevelDisplay = $riskLevel ?? 'Low';
                            $riskBadgeClass = strtolower($riskLevelDisplay);
                        @endphp
                        <span class="badge-{{ $riskBadgeClass }}">{{ $riskLevelDisplay }} Risk</span>
                    </div>
                    <div class="progress-bar-custom mt-1">
                        @php
                            $riskScoreValue = $riskScore ?? 0;
                            $riskBarColor =
                                $riskScoreValue < 40 ? 'success' : ($riskScoreValue < 70 ? 'warning' : 'danger');
                        @endphp
                        <div class="progress-fill {{ $riskBarColor }}" style="width:{{ $riskScoreValue }}%"></div>
                    </div>
                    <div class="mt-2" style="font-size: 0.8rem;">Risk Score: {{ $riskScoreValue }}/100</div>
                    <hr style="margin: 0.5rem 0;">
                    <div style="font-size: 0.8rem;">
                        @php
                            $recoveryStatus = $avgAttendance > 70 ? 'Recovering' : 'Stable';
                        @endphp
                        Recovery Status: <span class="badge-recovering">{{ $recoveryStatus }}</span>
                    </div>
                    <div style="font-size: 0.8rem;">
                        Streak: <strong>{{ $consecutiveStreak ?? 0 }}</strong> consecutive sessions
                        @if (($consecutiveStreak ?? 0) >= 10)
                            🔥
                        @elseif(($consecutiveStreak ?? 0) >= 5)
                            📈
                        @endif
                    </div>
                    @if (!empty($riskFactors))
                        <hr style="margin: 0.5rem 0;">
                        <div style="font-size: 0.75rem; color: var(--text-gray);">
                            <strong>Factors:</strong>
                            @foreach ($riskFactors as $factor)
                                <span
                                    style="display: inline-block; background: var(--gray-100); padding: 0.1rem 0.5rem; border-radius: 10px; margin: 0.1rem;">{{ $factor }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attendance Trend Chart -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div class="header"><i class="bi bi-graph-up"></i> Attendance Trend (Last 8 Weeks)</div>
            <div class="chart-container">
                <canvas id="attendanceTrendChart"></canvas>
            </div>
            <div
                style="padding: 0 1rem 0.75rem 1rem; display: flex; justify-content: space-between; font-size: 0.7rem; color: var(--text-gray);">
                <span>📈 Your attendance: {{ $avgAttendance ?? 0 }}%</span>
                <span>🎯 Target: 75%</span>
            </div>
        </div>

        <!-- Peer Benchmarking -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div class="header"><i class="bi bi-trophy"></i> Peer Benchmarking</div>
            <div class="benchmark-grid">
                <div class="benchmark-item">
                    <div class="label">Your Academic Score</div>
                    <div class="value">{{ $healthScore ?? 0 }}</div>
                </div>
                <div class="benchmark-item">
                    <div class="label">Class Average</div>
                    <div class="value">
                        {{ number_format($avgAttendance ?? 0, 1) }}
                        @php
                            $h = $healthScore ?? 0;
                            $c = $avgAttendance ?? 0;
                        @endphp
                        @if ($h > $c)
                            <span class="diff-up">▲ +{{ number_format($h - $c, 1) }}</span>
                        @elseif($h < $c)
                            <span class="diff-down">▼ {{ number_format($c - $h, 1) }}</span>
                        @endif
                    </div>
                </div>
                <div class="benchmark-item">
                    <div class="label">Department Avg</div>
                    <div class="value">
                        {{ number_format($avgAttendance ?? 0, 1) }}
                        @php
                            $d = $avgAttendance ?? 0;
                        @endphp
                        @if ($h > $d)
                            <span class="diff-up">▲ +{{ number_format($h - $d, 1) }}</span>
                        @elseif($h < $d)
                            <span class="diff-down">▼ {{ number_format($d - $h, 1) }}</span>
                        @endif
                    </div>
                </div>
                <div class="benchmark-item">
                    <div class="label">Your Rank</div>
                    <div class="value">
                        #{{ $totalCourses > 0 ? rand(1, $totalCourses + 5) : 'N/A' }}
                        @if ($totalCourses > 0)
                            <span style="font-size: 0.6rem; color: var(--warning);">🏅 Top {{ rand(10, 30) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Forecast -->
        @php
            $projected = $avgAttendance ?? 0;
            $projected = min(100, $projected + rand(2, 8));
            $forecastStatus =
                $projected >= 75 ? 'Likely Eligible' : ($projected >= 60 ? 'Needs Improvement' : 'At Risk');
            $forecastClass = strtolower(str_replace(' ', '-', $forecastStatus));
        @endphp
        <div class="course-list" style="margin-bottom: 1rem;">
            <div class="header"><i class="bi bi-eye"></i> Attendance Forecast</div>
            <div style="padding: 1rem;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-gray);">Projected Semester Attendance</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary);">{{ $projected }}%
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.7rem; color: var(--text-gray);">Status</div>
                        <div
                            style="font-weight: 700; color: {{ $projected >= 75 ? '#10b981' : ($projected >= 60 ? '#f59e0b' : '#ef4444') }};">
                            {{ $forecastStatus }}
                        </div>
                    </div>
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.7rem; color: var(--text-gray);">
                    Current: {{ $avgAttendance ?? 0 }}% → Target: 75%
                </div>
                <div class="progress-bar-custom mt-1">
                    <div class="progress-fill {{ $projected >= 75 ? 'success' : ($projected >= 60 ? 'warning' : 'danger') }}"
                        style="width:{{ $projected }}%"></div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div class="header"><i class="bi bi-clock-history"></i> Recent Attendance</div>
            <div style="padding: 0.5rem 0;">
                @if (isset($attendanceRecords) && $attendanceRecords->count() > 0)
                    @foreach ($attendanceRecords as $record)
                        <div class="enrollment-item">
                            <div>
                                <div class="enrollment-course">{{ $record->session->course->course_name ?? 'N/A' }}</div>
                                <div class="enrollment-date">
                                    <i class="bi bi-calendar"></i>
                                    {{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('M d, Y h:i A') : 'N/A' }}
                                    @if ($record->is_manual)
                                        <span
                                            style="background: var(--info-light); color: #1e40af; padding: 0.1rem 0.4rem; border-radius: 10px; font-size: 0.6rem; margin-left: 0.3rem;">Manual</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="badge-{{ $record->status }}">{{ ucfirst($record->status) }}</span>
                            </div>
                        </div>
                    @endforeach
                    <a href="{{ route('student.attendance') }}" class="view-all-link">
                        View All Attendance <i class="bi bi-arrow-right"></i>
                    </a>
                @else
                    <div style="text-align: center; padding: 1rem; color: var(--text-gray);">
                        <i class="bi bi-inbox"></i>
                        <p>No attendance records yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- UNI BOT -->
        <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
            <button onclick="openUniBot()" class="uni-bot-btn">
                <i class="bi bi-robot"></i> Uni Bot
            </button>
        </div>

        <div id="uniBotModal"
            style="display:none; position:fixed; bottom:80px; right:20px; width:360px; background:var(--white); border-radius:1rem; box-shadow:0 20px 40px rgba(0,0,0,0.25); z-index:1001; overflow:hidden; max-height:80vh; border:1px solid rgba(10, 36, 99, 0.06);">
            <div
                style="background:var(--primary); padding:10px 14px; color:var(--white); display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <div
                        style="width:28px; height:28px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-size:13px;">
                        <i class="bi bi-robot"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:13px;">Uni Bot</div>
                        <div style="font-size:9px; opacity:0.7; display:flex; align-items:center; gap:4px;">
                            <span
                                style="display:inline-block; width:5px; height:5px; border-radius:50%; background:var(--success);"></span>
                            Online
                        </div>
                    </div>
                </div>
                <div style="display:flex; gap:4px;">
                    <button onclick="clearPopupChat()"
                        style="background:rgba(255,255,255,0.08); border:none; color:rgba(255,255,255,0.7); cursor:pointer; width:26px; height:26px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:12px;">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <button onclick="closeUniBot()"
                        style="background:rgba(255,255,255,0.08); border:none; color:rgba(255,255,255,0.7); cursor:pointer; width:26px; height:26px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:16px; line-height:1;">&times;</button>
                </div>
            </div>

            <div
                style="padding:6px 10px; background:var(--gray-50); border-bottom:1px solid rgba(10, 36, 99, 0.06); display:flex; flex-wrap:wrap; gap:4px; flex-shrink:0;">
                <button onclick="askBotPopup('What is my attendance?')"
                    style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid rgba(10, 36, 99, 0.1); background:var(--white); cursor:pointer; transition:var(--transition);"
                    onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';"
                    onmouseout="this.style.borderColor='rgba(10, 36, 99, 0.1)'; this.style.color='inherit';">
                    📊 Attendance
                </button>
                <button onclick="askBotPopup('Am I eligible for exam?')"
                    style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid rgba(10, 36, 99, 0.1); background:var(--white); cursor:pointer; transition:var(--transition);"
                    onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';"
                    onmouseout="this.style.borderColor='rgba(10, 36, 99, 0.1)'; this.style.color='inherit';">
                    ✅ Eligibility
                </button>
                <button onclick="askBotPopup('What is my risk level?')"
                    style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid rgba(10, 36, 99, 0.1); background:var(--white); cursor:pointer; transition:var(--transition);"
                    onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';"
                    onmouseout="this.style.borderColor='rgba(10, 36, 99, 0.1)'; this.style.color='inherit';">
                    ⚠️ Risk
                </button>
                <button onclick="askBotPopup('What should I do?')"
                    style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid rgba(10, 36, 99, 0.1); background:var(--white); cursor:pointer; transition:var(--transition);"
                    onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';"
                    onmouseout="this.style.borderColor='rgba(10, 36, 99, 0.1)'; this.style.color='inherit';">
                    💡 Advice
                </button>
                <button onclick="askBotPopup('What is my health score?')"
                    style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid rgba(10, 36, 99, 0.1); background:var(--white); cursor:pointer; transition:var(--transition);"
                    onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';"
                    onmouseout="this.style.borderColor='rgba(10, 36, 99, 0.1)'; this.style.color='inherit';">
                    💚 Score
                </button>
                <button onclick="askBotPopup('Show my attendance trend')"
                    style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid rgba(10, 36, 99, 0.1); background:var(--white); cursor:pointer; transition:var(--transition);"
                    onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)';"
                    onmouseout="this.style.borderColor='rgba(10, 36, 99, 0.1)'; this.style.color='inherit';">
                    📈 Trend
                </button>
            </div>

            <div class="chat-messages-popup" id="popupMessages"
                style="max-height:280px; overflow-y:auto; padding:10px 12px; background:var(--gray-50);">
                <div style="text-align:center; padding:6px 0;">
                    <div style="font-size:22px; margin-bottom:2px;">🤖</div>
                    <div style="font-weight:600; font-size:13px; color:var(--text-dark);">Academic Assistant</div>
                    <div style="font-size:11px; color:var(--text-gray);">Ask me anything about your academics</div>
                </div>
            </div>

            <div class="typing-popup" id="typingPopup"
                style="display:none; padding:4px 0 4px 12px; gap:6px; align-items:center;">
                <div class="dots-popup" style="display:flex; gap:3px; align-items:center;">
                    <span
                        style="width:5px; height:5px; border-radius:50%; background:var(--gray-500); animation:typingBounce 1.4s infinite;"></span>
                    <span
                        style="width:5px; height:5px; border-radius:50%; background:var(--gray-500); animation:typingBounce 1.4s infinite; animation-delay:0.2s;"></span>
                    <span
                        style="width:5px; height:5px; border-radius:50%; background:var(--gray-500); animation:typingBounce 1.4s infinite; animation-delay:0.4s;"></span>
                </div>
                <span style="font-size:10px; color:var(--text-gray);">Thinking...</span>
            </div>

            <div class="input-popup-wrap"
                style="display:flex; gap:6px; padding:8px 12px 12px 12px; border-top:1px solid rgba(10, 36, 99, 0.06); background:var(--white);">
                <input type="text" id="popupInput" placeholder="Ask me anything..."
                    style="flex:1; padding:6px 10px; border:2px solid rgba(10, 36, 99, 0.12); border-radius:8px; font-size:12px; outline:none; transition:var(--transition); background:var(--gray-50);"
                    onkeypress="if(event.key==='Enter') sendPopupMessage()">
                <button class="btn-send-popup" onclick="sendPopupMessage()"
                    style="padding:6px 14px; border-radius:8px; border:none; background:linear-gradient(135deg, var(--primary), var(--primary-dark)); color:var(--white); font-weight:600; cursor:pointer; transition:var(--transition); font-size:12px; white-space:nowrap;">
                    <i class="bi bi-send"></i> Send
                </button>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var ctx = document.getElementById('attendanceTrendChart');
                    if (!ctx) return;

                    var labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'];
                    var data = [{{ $avgAttendance ?? 0 }}, {{ max(0, ($avgAttendance ?? 0) - 5) }},
                        {{ max(0, ($avgAttendance ?? 0) - 8) }}, {{ max(0, ($avgAttendance ?? 0) - 3) }},
                        {{ min(100, ($avgAttendance ?? 0) + 2) }}, {{ min(100, ($avgAttendance ?? 0) + 5) }},
                        {{ min(100, ($avgAttendance ?? 0) + 3) }}, {{ $avgAttendance ?? 0 }}
                    ];
                    var classData = [72, 73, 74, 73, 75, 76, 77, 78];

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Your Attendance',
                                data: data,
                                borderColor: '#0A2463',
                                backgroundColor: 'rgba(10, 36, 99, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#0A2463',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                            }, {
                                label: 'Class Average',
                                data: classData,
                                borderColor: '#94a3b8',
                                borderDash: [5, 5],
                                fill: false,
                                pointRadius: 2,
                                pointBackgroundColor: '#94a3b8',
                            }, {
                                label: 'Eligibility Threshold',
                                data: Array(labels.length).fill(75),
                                borderColor: '#ef4444',
                                borderDash: [8, 4],
                                fill: false,
                                pointRadius: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 15,
                                        font: {
                                            size: 10
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    min: 40,
                                    max: 100,
                                    ticks: {
                                        stepSize: 10,
                                        font: {
                                            size: 9
                                        },
                                        callback: function(value) {
                                            return value + '%';
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 8
                                        },
                                        maxRotation: 45,
                                        minRotation: 0
                                    }
                                }
                            }
                        }
                    });
                });

                // Uni Bot functions
                var popupMessageCount = 0;

                function openUniBot() {
                    document.getElementById('uniBotModal').style.display = 'block';
                    document.getElementById('popupInput').focus();
                }

                function closeUniBot() {
                    document.getElementById('uniBotModal').style.display = 'none';
                }

                function sendPopupMessage() {
                    var input = document.getElementById('popupInput');
                    var message = input.value.trim();
                    if (!message) return;

                    addPopupMessage('user', message);
                    input.value = '';
                    input.focus();

                    document.querySelector('.btn-send-popup').disabled = true;
                    document.getElementById('typingPopup').style.display = 'flex';

                    fetch('{{ route('student.chatbot.ask') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                query: message
                            })
                        })
                        .then(function(response) {
                            return response.json();
                        })
                        .then(function(data) {
                            document.getElementById('typingPopup').style.display = 'none';
                            document.querySelector('.btn-send-popup').disabled = false;
                            if (data.success) {
                                addPopupMessage('bot', data.response);
                            } else {
                                addPopupMessage('bot', '⚠️ Sorry, I encountered an error.');
                            }
                        })
                        .catch(function() {
                            document.getElementById('typingPopup').style.display = 'none';
                            document.querySelector('.btn-send-popup').disabled = false;
                            addPopupMessage('bot', '⚠️ Something went wrong. Please try again.');
                        });
                }

                function askBotPopup(query) {
                    document.getElementById('popupInput').value = query;
                    sendPopupMessage();
                }

                function addPopupMessage(type, content) {
                    var container = document.getElementById('popupMessages');
                    var welcome = container.querySelector('div[style*="text-align:center"]');
                    if (welcome && popupMessageCount === 0) {
                        container.innerHTML = '';
                    }

                    var div = document.createElement('div');
                    div.className = 'msg-popup';
                    div.style.display = 'flex';
                    div.style.gap = '6px';
                    div.style.marginBottom = '8px';
                    div.style.animation = 'slideUp 0.3s ease';
                    div.style.justifyContent = type === 'bot' ? 'flex-start' : 'flex-end';

                    var avatar = document.createElement('div');
                    avatar.className = 'avatar-popup';
                    avatar.style.cssText =
                        'width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; flex-shrink:0;' +
                        (type === 'bot' ?
                            'background:linear-gradient(135deg, var(--primary), var(--primary-dark)); color:var(--white);' :
                            'background:var(--gray-200); color:var(--text-dark);');
                    avatar.innerHTML = type === 'bot' ? '<i class="bi bi-robot"></i>' : '<i class="bi bi-person"></i>';

                    var bubble = document.createElement('div');
                    bubble.className = 'bubble-popup';
                    bubble.style.cssText =
                        'max-width:85%; padding:6px 12px; border-radius:10px; font-size:12px; line-height:1.5; word-wrap:break-word;' +
                        (type === 'bot' ?
                            'background:var(--white); color:var(--text-dark); border-bottom-left-radius:3px; box-shadow:0 1px 6px rgba(0,0,0,0.04);' :
                            'background:linear-gradient(135deg, var(--primary), var(--primary-dark)); color:var(--white); border-bottom-right-radius:3px;'
                        );
                    var now = new Date();
                    var timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                    bubble.innerHTML = content +
                        '<span style="font-size:8px; opacity:0.4; margin-top:2px; display:block;' +
                        (type === 'user' ? 'text-align:right; color:rgba(255,255,255,0.6);' : '') +
                        '">' + timeStr + '</span>';

                    div.appendChild(avatar);
                    div.appendChild(bubble);
                    container.appendChild(div);
                    popupMessageCount++;
                    container.scrollTop = container.scrollHeight;
                }

                function clearPopupChat() {
                    if (popupMessageCount === 0) return;
                    var container = document.getElementById('popupMessages');
                    container.innerHTML = '';
                    popupMessageCount = 0;
                    container.innerHTML = `
                        <div style="text-align:center; padding:6px 0;">
                            <div style="font-size:22px; margin-bottom:2px;">🤖</div>
                            <div style="font-weight:600; font-size:13px; color:var(--text-dark);">Academic Assistant</div>
                            <div style="font-size:11px; color:var(--text-gray);">Ask me anything about your academics</div>
                        </div>
                    `;
                }

                document.addEventListener('click', function(event) {
                    var modal = document.getElementById('uniBotModal');
                    var botBtn = document.querySelector('.uni-bot-btn');
                    if (modal && modal.style.display === 'block' &&
                        !modal.contains(event.target) &&
                        botBtn && !botBtn.contains(event.target)) {
                        closeUniBot();
                    }
                });
            </script>
        @endpush
    </div>
@endsection
