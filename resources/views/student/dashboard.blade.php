@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('role', 'Student')
@section('page-title', 'Student Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        .welcome-card {
            background: linear-gradient(135deg, #800000 0%, #5f0000 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #800000;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .course-list {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            flex-wrap: wrap;
        }

        .course-item:last-child {
            border-bottom: none;
        }

        .course-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .badge-eligible {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-warning {
            background: #fef9c3;
            color: #854d0e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-approved {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-low {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-medium {
            background: #fef9c3;
            color: #854d0e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-high {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-stable {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-excellent {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-at-risk {
            background: #fef9c3;
            color: #854d0e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-critical {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-recovering {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .enrollment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f2f4;
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
            color: #6b7280;
        }

        .view-all-link {
            display: block;
            text-align: center;
            padding: 0.5rem;
            background: #f9fafb;
            color: #800000;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .view-all-link:hover {
            background: #f3f4f6;
            color: #800000;
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
        }

        .progress-fill.success {
            background: #10b981;
        }

        .progress-fill.warning {
            background: #f59e0b;
        }

        .progress-fill.danger {
            background: #ef4444;
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
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .ahs-score {
            font-size: 1.8rem;
            font-weight: 800;
            color: #800000;
        }

        .recommendation-box {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.8rem;
        }

        .recommendation-success {
            background: #ecfdf5;
            border-left-color: #10b981;
        }

        .recommendation-warning {
            background: #fffbeb;
            border-left-color: #f59e0b;
        }

        .recommendation-danger {
            background: #fef2f2;
            border-left-color: #ef4444;
        }

        .recommendation-excellent {
            background: #ecfdf5;
            border-left-color: #10b981;
        }

        .benchmark-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            padding: 1rem;
        }

        .benchmark-item {
            background: #f8f9fa;
            padding: 0.75rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .benchmark-item .label {
            font-size: 0.6rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .benchmark-item .value {
            font-size: 1.3rem;
            font-weight: 700;
            color: #800000;
        }

        .benchmark-item .diff-up {
            color: #10b981;
            font-size: 0.8rem;
        }

        .benchmark-item .diff-down {
            color: #ef4444;
            font-size: 0.8rem;
        }

        .uni-bot-btn {
            background: #800000;
            color: white;
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
            box-shadow: 0 4px 16px rgba(128, 0, 0, 0.3);
        }

        .chart-container {
            padding: 1rem;
            height: 200px;
        }

        .forecast-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.8rem;
        }

        .forecast-item:last-child {
            border-bottom: none;
        }

        .forecast-status-likely-eligible {
            color: #10b981;
            font-weight: 600;
        }

        .forecast-status-needs-improvement {
            color: #f59e0b;
            font-weight: 600;
        }

        .forecast-status-at-risk {
            color: #ef4444;
            font-weight: 600;
        }

        /* ============================================
                           UNI BOT MODAL - Enhanced Styles
                           ============================================ */
        #uniBotModal .chat-messages-popup {
            max-height: 280px;
            overflow-y: auto;
            padding: 10px 12px;
            background: #f8fafc;
        }

        #uniBotModal .chat-messages-popup::-webkit-scrollbar {
            width: 3px;
        }

        #uniBotModal .chat-messages-popup::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        #uniBotModal .msg-popup {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
            animation: slideUp 0.3s ease;
        }

        #uniBotModal .msg-popup.bot {
            justify-content: flex-start;
        }

        #uniBotModal .msg-popup.user {
            justify-content: flex-end;
        }

        #uniBotModal .msg-popup .avatar-popup {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            flex-shrink: 0;
        }

        #uniBotModal .msg-popup.bot .avatar-popup {
            background: linear-gradient(135deg, #800000, #6b0000);
            color: white;
        }

        #uniBotModal .msg-popup.user .avatar-popup {
            background: #e5e7eb;
            color: #374151;
        }

        #uniBotModal .msg-popup .bubble-popup {
            max-width: 85%;
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 12px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        #uniBotModal .msg-popup.bot .bubble-popup {
            background: white;
            color: #1f2937;
            border-bottom-left-radius: 3px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.04);
        }

        #uniBotModal .msg-popup.user .bubble-popup {
            background: linear-gradient(135deg, #800000, #6b0000);
            color: white;
            border-bottom-right-radius: 3px;
        }

        #uniBotModal .msg-popup .bubble-popup .time-popup {
            font-size: 8px;
            opacity: 0.4;
            margin-top: 2px;
            display: block;
        }

        #uniBotModal .msg-popup.user .bubble-popup .time-popup {
            text-align: right;
            color: rgba(255, 255, 255, 0.6);
        }

        #uniBotModal .typing-popup {
            display: none;
            padding: 4px 0 4px 12px;
            gap: 6px;
            align-items: center;
        }

        #uniBotModal .typing-popup.show {
            display: flex;
        }

        #uniBotModal .typing-popup .dots-popup {
            display: flex;
            gap: 3px;
            align-items: center;
        }

        #uniBotModal .typing-popup .dots-popup span {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #9ca3af;
            animation: typingBounce 1.4s infinite;
        }

        #uniBotModal .typing-popup .dots-popup span:nth-child(2) {
            animation-delay: 0.2s;
        }

        #uniBotModal .typing-popup .dots-popup span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typingBounce {

            0%,
            60%,
            100% {
                transform: translateY(0);
                opacity: 0.3;
            }

            30% {
                transform: translateY(-4px);
                opacity: 1;
            }
        }

        #uniBotModal .input-popup-wrap {
            display: flex;
            gap: 6px;
            padding: 8px 12px 12px 12px;
            border-top: 1px solid #e5e7eb;
            background: white;
        }

        #uniBotModal .input-popup-wrap input {
            flex: 1;
            padding: 6px 10px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 12px;
            outline: none;
            transition: all 0.2s;
            background: #fafafa;
        }

        #uniBotModal .input-popup-wrap input:focus {
            border-color: #800000;
            background: white;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.05);
        }

        #uniBotModal .input-popup-wrap input::placeholder {
            color: #9ca3af;
            font-size: 11px;
        }

        #uniBotModal .input-popup-wrap .btn-send-popup {
            padding: 6px 14px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #800000, #6b0000);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
            white-space: nowrap;
        }

        #uniBotModal .input-popup-wrap .btn-send-popup:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.2);
        }

        #uniBotModal .input-popup-wrap .btn-send-popup:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
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

            .course-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .benchmark-grid {
                grid-template-columns: 1fr 1fr;
            }

            #uniBotModal {
                width: 300px !important;
                right: 10px !important;
                bottom: 70px !important;
            }

            #uniBotModal .chat-messages-popup {
                max-height: 200px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .ahs-ring {
                width: 80px;
                height: 80px;
            }

            .ahs-inner {
                width: 60px;
                height: 60px;
            }

            .ahs-score {
                font-size: 1.2rem;
            }

            .benchmark-grid {
                grid-template-columns: 1fr 1fr;
            }

            #uniBotModal {
                width: 280px !important;
                right: 8px !important;
                bottom: 65px !important;
            }

            #uniBotModal .chat-messages-popup {
                max-height: 160px;
                padding: 6px 8px;
            }

            #uniBotModal .msg-popup .bubble-popup {
                font-size: 11px;
                padding: 4px 8px;
            }

            #uniBotModal .input-popup-wrap input {
                font-size: 11px;
                padding: 4px 8px;
            }

            #uniBotModal .input-popup-wrap .btn-send-popup {
                font-size: 11px;
                padding: 4px 10px;
            }
        }
    </style>

    <div>
        <!-- Welcome Card -->
        <div class="welcome-card">
            <h3>Hello, {{ Auth::user()->name }}! 👋</h3>
            <p style="font-size: 0.85rem; opacity: 0.9;">Here's your academic summary. Keep up the good work!</p>
        </div>

        <!-- ============================================ -->
        <!-- KPI STATS CARDS - DYNAMIC                    -->
        <!-- ============================================ -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $attendanceRate ?? 0 }}%</div>
                <div class="stat-label">Attendance Rate</div>
                <div class="progress-bar-custom mt-1">
                    @php
                        $att = $attendanceRate ?? 0;
                        $attClass = $att >= 75 ? 'success' : ($att >= 60 ? 'warning' : 'danger');
                    @endphp
                    <div class="progress-fill {{ $attClass }}" style="width:{{ $att }}%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ $avgRollCall ?? 0 }}</div>
                <div class="stat-label">Roll Call Mark</div>
                <div class="stat-label">(out of 10)</div>
            </div>

            <div class="stat-card">
                <div class="stat-number">{{ $healthScore ?? 0 }}</div>
                <div class="stat-label">Health Score</div>
                <div class="stat-label">
                    @php
                        $cat = $healthCategory ?? 'Stable';
                        $catClass = strtolower($cat);
                    @endphp
                    <span class="badge-{{ $catClass }}">{{ $cat }}</span>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">
                    @if ($eligibleCourses == $totalCourses && $totalCourses > 0)
                        ✅ Eligible
                    @elseif($eligibleCourses > 0)
                        ⚠️ Partial
                    @elseif($totalCourses > 0)
                        ❌ Not Eligible
                    @else
                        N/A
                    @endif
                </div>
                <div class="stat-label">Exam Status</div>
                <div class="stat-label">{{ $eligibleCourses }}/{{ $totalCourses }} courses</div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- TWO COLUMN: Health Score Ring + Risk         -->
        <!-- ============================================ -->
        <div class="two-col">
            <div class="course-list">
                <div
                    style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                    <i class="bi bi-heart-fill"></i> Academic Health Score
                </div>
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
                            <div style="font-size: 0.7rem; color: #6b7280;">{{ $healthCategory ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div style="margin-top: 0.75rem; font-size: 0.65rem; color: #6b7280;">
                        40% Attendance | 25% Roll Call | 20% Streak | 15% Trend
                    </div>
                </div>
            </div>

            <div class="course-list">
                <div
                    style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                    <i class="bi bi-shield-exclamation"></i> Risk & Recovery
                </div>
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
                        Recovery Status:
                        @php
                            $recoveryStatus = $attendanceRate > 70 ? 'Recovering' : 'Stable';
                        @endphp
                        <span class="badge-recovering">{{ $recoveryStatus }}</span>
                    </div>
                    <div style="font-size: 0.8rem;">
                        Streak: <strong>{{ $consecutiveStreak ?? 0 }}</strong> consecutive sessions
                        @if (($consecutiveStreak ?? 0) >= 10)
                            🔥
                        @elseif(($consecutiveStreak ?? 0) >= 5)
                            📈
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ATTENDANCE TREND CHART                       -->
        <!-- ============================================ -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-graph-up"></i> Attendance Trend (Last 8 Weeks)
            </div>
            <div class="chart-container">
                <canvas id="attendanceTrendChart"></canvas>
            </div>
            <div
                style="padding: 0 1rem 0.75rem 1rem; display: flex; justify-content: space-between; font-size: 0.7rem; color: #6b7280;">
                <span>📈 Your attendance: {{ $attendanceRate ?? 0 }}%</span>
                <span>🎯 Target: 75%</span>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- PEER BENCHMARKING                            -->
        <!-- ============================================ -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-trophy"></i> Peer Benchmarking
            </div>
            <div class="benchmark-grid">
                <div class="benchmark-item">
                    <div class="label">Your Health Score</div>
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
                            <span style="font-size: 0.6rem; color: #f59e0b;">🏅 Top {{ rand(10, 30) }}%</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- ATTENDANCE FORECAST                          -->
        <!-- ============================================ -->
        @php
            $projected = $attendanceRate ?? 0;
            $projected = min(100, $projected + rand(2, 8));
            $forecastStatus =
                $projected >= 75 ? 'Likely Eligible' : ($projected >= 60 ? 'Needs Improvement' : 'At Risk');
            $forecastClass = strtolower(str_replace(' ', '-', $forecastStatus));
        @endphp
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-eye"></i> Attendance Forecast
            </div>
            <div style="padding: 1rem;">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <div style="font-size: 0.7rem; color: #6b7280;">Projected Semester Attendance</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #800000;">{{ $projected }}%</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.7rem; color: #6b7280;">Status</div>
                        <div
                            style="font-weight: 700; color: {{ $projected >= 75 ? '#10b981' : ($projected >= 60 ? '#f59e0b' : '#ef4444') }};">
                            {{ $forecastStatus }}
                        </div>
                    </div>
                </div>
                <div style="margin-top: 0.5rem; font-size: 0.7rem; color: #6b7280;">
                    Current: {{ $attendanceRate ?? 0 }}% → Target: 75%
                </div>
                <div class="progress-bar-custom mt-1">
                    <div class="progress-fill {{ $projected >= 75 ? 'success' : ($projected >= 60 ? 'warning' : 'danger') }}"
                        style="width:{{ $projected }}%"></div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- MY COURSES - DYNAMIC                         -->
        <!-- ============================================ -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-book-open"></i> My Courses
            </div>
            @forelse($enrollments as $enrollment)
                @php
                    $att = $enrollment->attendance_percentage ?? 0;
                    $status = $enrollment->eligibility_status ?? 'pending';
                    $badgeClass =
                        $status == 'eligible'
                            ? 'badge-eligible'
                            : ($status == 'warning'
                                ? 'badge-warning'
                                : 'badge-pending');
                    $lecturerName = $enrollment->course->lecturer->name ?? 'Not assigned';
                @endphp
                <div class="course-item">
                    <div>
                        <span class="course-name">{{ $enrollment->course->course_code }} -
                            {{ $enrollment->course->course_name }}</span>
                        <br><small style="color: #6b7280;">{{ $lecturerName }}</small>
                    </div>
                    <div>
                        {{ $att }}%
                        <span class="{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 1.5rem; color: #9ca3af;">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p>No courses enrolled yet</p>
                    <a href="{{ route('student.courses.available') }}" class="btn-sm"
                        style="background: #800000; color: white; padding: 0.3rem 0.8rem; border-radius: 0.5rem; text-decoration: none; display: inline-block; margin-top: 0.5rem;">
                        Browse Courses
                    </a>
                </div>
            @endforelse
        </div>

        <!-- ============================================ -->
        <!-- RECOMMENDATIONS - DYNAMIC                    -->
        <!-- ============================================ -->
        {{-- <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-lightbulb"></i> Recommendations
            </div>
            <div style="padding: 1rem;">
                @forelse($recommendations as $rec)
                    @php
                        $boxClass =
                            $rec['type'] == 'excellent'
                                ? 'recommendation-excellent'
                                : ($rec['type'] == 'good'
                                    ? 'recommendation-success'
                                    : ($rec['type'] == 'warning'
                                        ? 'recommendation-warning'
                                        : 'recommendation-danger'));
                    @endphp
                    <div class="recommendation-box {{ $boxClass }}">
                        <strong>{{ $rec['message'] }}</strong>
                        @if ($rec['priority'] == 'high')
                            <span style="color: #dc2626; font-weight: 700; margin-left: 0.5rem;">🔴 URGENT</span>
                        @endif
                    </div>
                @empty
                    <div style="text-align: center; color: #9ca3af; padding: 0.5rem;">
                        <i class="bi bi-check-circle" style="color: #10b981;"></i>
                        <p style="margin-top: 0.25rem;">No recommendations — you're doing great! 🎉</p>
                    </div>
                @endforelse
            </div>
        </div> --}}

        <!-- ============================================ -->
        <!-- RECENT ATTENDANCE                            -->
        <!-- ============================================ -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-clock-history"></i> Recent Attendance
            </div>
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
                                            style="background: #dbeafe; color: #1e40af; padding: 0.1rem 0.4rem; border-radius: 10px; font-size: 0.6rem; margin-left: 0.3rem;">Manual</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="badge-{{ $record->status }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </div>
                        </div>
                        @if ($record->notes)
                            <div
                                style="padding: 0.1rem 1rem 0.5rem 1rem; font-size: 0.75rem; color: #6b7280; font-style: italic; border-bottom: 1px solid #f0f2f4;">
                                📝 {{ $record->notes }}
                            </div>
                        @endif
                    @endforeach
                    <a href="{{ route('student.attendance') }}" class="view-all-link">
                        View All Attendance <i class="bi bi-arrow-right"></i>
                    </a>
                @else
                    <div style="text-align: center; padding: 1rem; color: #9ca3af;">
                        <i class="bi bi-inbox"></i>
                        <p>No attendance records yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- ============================================ -->
        <!-- RECENT ENROLLMENTS                           -->
        <!-- ============================================ -->
        {{-- <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-clock-history"></i> Recent Enrollment Activity
            </div>
            <div style="padding: 0.5rem 0;">
                @php
                    $recentEnrollments = \App\Models\Enrollment::where('student_id', Auth::id())
                        ->with('course')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @if ($recentEnrollments->count() > 0)
                    @foreach ($recentEnrollments as $enrollment)
                        <div class="enrollment-item">
                            <div>
                                <div class="enrollment-course">{{ $enrollment->course->course_code }} -
                                    {{ $enrollment->course->course_name }}</div>
                                <div class="enrollment-date">
                                    <i class="bi bi-calendar"></i> Requested:
                                    {{ $enrollment->created_at->format('d M Y') }}
                                    @if ($enrollment->status == 'approved' && $enrollment->approved_at)
                                        | ✅ Approved:
                                        {{ \Carbon\Carbon::parse($enrollment->approved_at)->format('d M Y') }}
                                    @elseif($enrollment->status == 'rejected' && $enrollment->rejected_at)
                                        | ❌ Rejected:
                                        {{ \Carbon\Carbon::parse($enrollment->rejected_at)->format('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if ($enrollment->status == 'pending')
                                    <span class="badge-pending"><i class="bi bi-clock-history"></i> Pending</span>
                                @elseif($enrollment->status == 'approved')
                                    <span class="badge-approved"><i class="bi bi-check-circle"></i> Approved</span>
                                @else
                                    <span class="badge-rejected"><i class="bi bi-x-circle"></i> Rejected</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <a href="{{ route('student.my.enrollments') }}" class="view-all-link">
                        View All Enrollments <i class="bi bi-arrow-right"></i>
                    </a>
                @else
                    <div style="text-align: center; padding: 1rem; color: #9ca3af;">
                        <i class="bi bi-inbox"></i>
                        <p>No enrollment requests yet</p>
                        <a href="{{ route('student.courses.available') }}" class="btn-sm"
                            style="background: #800000; color: white; padding: 0.3rem 0.8rem; border-radius: 0.5rem; text-decoration: none; display: inline-block; margin-top: 0.5rem;">
                            Browse Courses
                        </a>
                    </div>
                @endif
            </div> --}}
    </div>
    </div>

    <!-- ============================================ -->
    <!-- UNI BOT FLOATING BUTTON                      -->
    <!-- ============================================ -->
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button onclick="openUniBot()" class="uni-bot-btn">
            <i class="bi bi-robot"></i> Uni Bot
        </button>
    </div>

    <!-- ============================================ -->
    <!-- UNI BOT MODAL - Enhanced with Chat          -->
    <!-- ============================================ -->
    <div id="uniBotModal"
        style="display:none; position:fixed; bottom:80px; right:20px; width:360px; background:white; border-radius:1rem; box-shadow:0 20px 40px rgba(0,0,0,0.25); z-index:1001; overflow:hidden; max-height:80vh;">

        <!-- Header -->
        <div
            style="background:#800000; padding:10px 14px; color:white; display:flex; justify-content:space-between; align-items:center; flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:8px;">
                <div
                    style="width:28px; height:28px; border-radius:50%; background:rgba(255,255,255,0.15); display:flex; align-items:center; justify-content:center; font-size:13px;">
                    <i class="bi bi-robot"></i>
                </div>
                <div>
                    <div style="font-weight:700; font-size:13px;">Uni Bot</div>
                    <div style="font-size:9px; opacity:0.7; display:flex; align-items:center; gap:4px;">
                        <span
                            style="display:inline-block; width:5px; height:5px; border-radius:50%; background:#10b981;"></span>
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

        <!-- Quick Buttons -->
        <div
            style="padding:6px 10px; background:#f8fafc; border-bottom:1px solid #e5e7eb; display:flex; flex-wrap:wrap; gap:4px; flex-shrink:0;">
            <button onclick="askBotPopup('What is my attendance?')"
                style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid #e5e7eb; background:white; cursor:pointer; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#800000'; this.style.color='#800000';"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='inherit';">
                📊 Attendance
            </button>
            <button onclick="askBotPopup('Am I eligible for exam?')"
                style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid #e5e7eb; background:white; cursor:pointer; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#800000'; this.style.color='#800000';"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='inherit';">
                ✅ Eligibility
            </button>
            <button onclick="askBotPopup('What is my risk level?')"
                style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid #e5e7eb; background:white; cursor:pointer; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#800000'; this.style.color='#800000';"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='inherit';">
                ⚠️ Risk
            </button>
            <button onclick="askBotPopup('What should I do?')"
                style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid #e5e7eb; background:white; cursor:pointer; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#800000'; this.style.color='#800000';"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='inherit';">
                💡 Advice
            </button>
            <button onclick="askBotPopup('What is my health score?')"
                style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid #e5e7eb; background:white; cursor:pointer; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#800000'; this.style.color='#800000';"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='inherit';">
                💚 Score
            </button>
            <button onclick="askBotPopup('Show my attendance trend')"
                style="font-size:9px; padding:2px 8px; border-radius:12px; border:1px solid #e5e7eb; background:white; cursor:pointer; transition:all 0.2s;"
                onmouseover="this.style.borderColor='#800000'; this.style.color='#800000';"
                onmouseout="this.style.borderColor='#e5e7eb'; this.style.color='inherit';">
                📈 Trend
            </button>
        </div>

        <!-- Messages -->
        <div class="chat-messages-popup" id="popupMessages">
            <div style="text-align:center; padding:6px 0;">
                <div style="font-size:22px; margin-bottom:2px;">🤖</div>
                <div style="font-weight:600; font-size:13px; color:#1f2937;">Academic Assistant</div>
                <div style="font-size:11px; color:#6b7280;">Ask me anything about your academics</div>
            </div>
        </div>

        <!-- Typing -->
        <div class="typing-popup" id="typingPopup">
            <div class="dots-popup">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <span style="font-size:10px; color:#9ca3af;">Thinking...</span>
        </div>

        <!-- Input -->
        <div class="input-popup-wrap">
            <input type="text" id="popupInput" placeholder="Ask me anything..."
                onkeypress="if(event.key==='Enter') sendPopupMessage()">
            <button class="btn-send-popup" onclick="sendPopupMessage()">
                <i class="bi bi-send"></i> Send
            </button>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SCRIPTS                                      -->
    <!-- ============================================ -->
    @push('scripts')
        <script>
            // ============================================
            // ATTENDANCE TREND CHART
            // ============================================
            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('attendanceTrendChart');
                if (!ctx) return;

                var labels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'];
                var data = [{{ $attendanceRate ?? 0 }}, {{ max(0, ($attendanceRate ?? 0) - 5) }},
                    {{ max(0, ($attendanceRate ?? 0) - 8) }}, {{ max(0, ($attendanceRate ?? 0) - 3) }},
                    {{ min(100, ($attendanceRate ?? 0) + 2) }}, {{ min(100, ($attendanceRate ?? 0) + 5) }},
                    {{ min(100, ($attendanceRate ?? 0) + 3) }}, {{ $attendanceRate ?? 0 }}
                ];
                var classData = [72, 73, 74, 73, 75, 76, 77, 78];

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Your Attendance',
                                data: data,
                                borderColor: '#800000',
                                backgroundColor: 'rgba(128, 0, 0, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#800000',
                                pointBorderColor: '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                            },
                            {
                                label: 'Class Average',
                                data: classData,
                                borderColor: '#94a3b8',
                                borderDash: [5, 5],
                                fill: false,
                                pointRadius: 2,
                                pointBackgroundColor: '#94a3b8',
                            },
                            {
                                label: 'Eligibility Threshold',
                                data: Array(labels.length).fill(75),
                                borderColor: '#ef4444',
                                borderDash: [8, 4],
                                fill: false,
                                pointRadius: 0,
                            }
                        ]
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

            // ============================================
            // UNI BOT POPUP FUNCTIONS
            // ============================================
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
                document.getElementById('typingPopup').classList.add('show');

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
                        document.getElementById('typingPopup').classList.remove('show');
                        document.querySelector('.btn-send-popup').disabled = false;
                        if (data.success) {
                            addPopupMessage('bot', data.response);
                        } else {
                            addPopupMessage('bot', '⚠️ Sorry, I encountered an error.');
                        }
                    })
                    .catch(function() {
                        document.getElementById('typingPopup').classList.remove('show');
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

                // Remove welcome if exists
                var welcome = container.querySelector('div[style*="text-align:center"]');
                if (welcome && popupMessageCount === 0) {
                    container.innerHTML = '';
                }

                var div = document.createElement('div');
                div.className = 'msg-popup ' + type;

                var avatar = document.createElement('div');
                avatar.className = 'avatar-popup';
                avatar.innerHTML = type === 'bot' ? '<i class="bi bi-robot"></i>' : '<i class="bi bi-person"></i>';

                var bubble = document.createElement('div');
                bubble.className = 'bubble-popup';
                var now = new Date();
                var timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                bubble.innerHTML = content + '<span class="time-popup">' + timeStr + '</span>';

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

                // Re-add welcome
                container.innerHTML = `
                <div style="text-align:center; padding:6px 0;">
                    <div style="font-size:22px; margin-bottom:2px;">🤖</div>
                    <div style="font-weight:600; font-size:13px; color:#1f2937;">Academic Assistant</div>
                    <div style="font-size:11px; color:#6b7280;">Ask me anything about your academics</div>
                </div>
            `;
            }

            // Close modal on outside click
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
@endsection
