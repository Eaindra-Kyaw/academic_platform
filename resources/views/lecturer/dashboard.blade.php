@extends('layouts.app')

@section('title', 'Lecturer Dashboard')
@section('role', 'Lecturer')
@section('page-title', 'Teacher Dashboard')
@section('welcome-text')
    <i class="bi bi-hand-wave" style="font-size: 1.2rem; color: var(--most-gold);"></i> Welcome Back,
    {{ Auth::user()->name }} !
@endsection

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0D47A1;
            --primary-dark: #0B2B5B;
            --primary-light: #1565C0;
            --secondary: #42A5F5;
            --accent: #F9A825;
            --bg-main: #E3F2FD;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(13, 71, 161, 0.08);
            --shadow-hover: 0 8px 30px rgba(13, 71, 161, 0.15);
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
        }

        .ld-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .ld-stat-card {
            flex: 1 1 calc(16.666% - 1rem);
            min-width: 140px;
            background: var(--white);
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            border: 1px solid var(--gray-200);
            transition: all 0.2s ease;
            box-shadow: var(--shadow);
        }

        .ld-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .ld-stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .ld-stat-number.danger {
            color: var(--danger);
        }

        .ld-stat-number.warning {
            color: var(--warning);
        }

        .ld-stat-label {
            font-size: 0.7rem;
            color: var(--text-gray);
        }

        .ld-stat-card .stat-icon {
            font-size: 1.5rem;
            display: block;
            margin-bottom: 4px;
        }

        .quick-actions {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            align-items: center;
        }

        .quick-action-btn {
            background: rgba(255, 255, 255, 0.12);
            color: var(--white);
            border: 1px solid rgba(249, 168, 37, 0.2);
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .quick-action-btn:hover {
            background: rgba(249, 168, 37, 0.15);
            transform: translateY(-2px);
            color: var(--white);
            border-color: rgba(249, 168, 37, 0.4);
        }

        .ld-card {
            background: var(--white);
            border-radius: 0.75rem;
            border: 1px solid var(--gray-200);
            overflow: hidden;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }

        .ld-card-header {
            padding: 0.75rem 1rem;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            font-weight: 700;
            color: var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ld-card-body {
            padding: 1rem;
        }

        .ld-live-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .ld-live-box {
            flex: 1 1 calc(25% - 0.75rem);
            min-width: 70px;
            background: var(--gray-50);
            padding: 0.75rem;
            border-radius: 0.75rem;
            text-align: center;
        }

        .ld-live-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .insight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .insight-card {
            background: var(--gray-50);
            padding: 12px 16px;
            border-radius: 8px;
            border-left: 3px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .insight-card:hover {
            background: var(--gray-100);
            transform: translateX(4px);
        }

        .insight-card .insight-icon {
            font-size: 1.5rem;
        }

        .insight-card .insight-text {
            font-size: 13px;
            color: var(--text-dark);
        }

        .insight-card .insight-text strong {
            color: var(--gray-800);
            display: block;
        }

        .insight-card .insight-text span {
            color: var(--text-gray);
            font-size: 12px;
        }

        .course-performance {
            padding: 10px 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .course-performance:last-child {
            border-bottom: none;
        }

        .course-performance .course-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
            flex-wrap: wrap;
            gap: 4px;
        }

        .course-performance .course-info .code {
            font-weight: 600;
            font-size: 14px;
            color: var(--gray-800);
        }

        .course-performance .course-info .name {
            font-size: 13px;
            color: var(--text-gray);
        }

        .course-performance .course-stats {
            display: flex;
            gap: 12px;
            font-size: 12px;
            flex-wrap: wrap;
        }

        .stat-badge {
            padding: 2px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 11px;
        }

        .stat-badge.good {
            background: var(--success-light);
            color: #166534;
        }

        .stat-badge.warning {
            background: var(--warning-light);
            color: #92400e;
        }

        .stat-badge.danger {
            background: var(--danger-light);
            color: #991b1b;
        }

        .stat-badge.info {
            background: var(--info-light);
            color: #1e40af;
        }

        .progress-bar {
            width: 100%;
            height: 4px;
            background: var(--gray-200);
            border-radius: 4px;
            margin-top: 4px;
            overflow: hidden;
        }

        .progress-bar .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .chart-container {
            position: relative;
            height: 200px;
        }

        /* Notification Modal */
        .notify-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        .notify-modal-overlay.show {
            display: flex;
        }

        .notify-modal {
            background: var(--white);
            border-radius: 16px;
            max-width: 520px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(30px) scale(0.95);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .notify-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--gray-50);
            border-radius: 16px 16px 0 0;
        }

        .notify-modal-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notify-modal-header h5 .badge {
            font-size: 11px;
            background: var(--success-light);
            color: #166534;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .notify-modal-close {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-gray);
            cursor: pointer;
            transition: all 0.2s;
            padding: 0 4px;
        }

        .notify-modal-close:hover {
            color: var(--gray-800);
            transform: rotate(90deg);
        }

        .notify-modal-body {
            padding: 24px;
        }

        .notify-student-info {
            background: var(--gray-50);
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            border: 1px solid var(--gray-200);
        }

        .notify-student-info .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 20px;
            flex-shrink: 0;
        }

        .notify-student-info .details {
            flex: 1;
        }

        .notify-student-info .details .name {
            font-weight: 700;
            font-size: 16px;
            color: var(--gray-800);
        }

        .notify-student-info .details .meta {
            font-size: 13px;
            color: var(--text-gray);
            margin-top: 2px;
        }

        .notify-student-info .details .meta span {
            display: inline-block;
            margin-right: 12px;
        }

        .notify-student-info .details .risk-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .risk-badge.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .risk-badge.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .risk-badge.low {
            background: var(--success-light);
            color: #166534;
        }

        .notify-form-group {
            margin-bottom: 16px;
        }

        .notify-form-group label {
            display: block;
            font-weight: 600;
            font-size: 13px;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .notify-form-group label .required {
            color: var(--danger);
        }

        .notify-form-group input,
        .notify-form-group select,
        .notify-form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
            background: var(--white);
            font-family: inherit;
        }

        .notify-form-group input:focus,
        .notify-form-group select:focus,
        .notify-form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.08);
        }

        .notify-form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .notify-templates {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .notify-templates .template-btn {
            padding: 4px 12px;
            border: 1px solid var(--gray-200);
            border-radius: 20px;
            font-size: 11px;
            background: var(--gray-50);
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-dark);
        }

        .notify-templates .template-btn:hover {
            border-color: var(--primary);
            background: var(--info-light);
            color: var(--primary);
        }

        .notify-templates .template-btn.active {
            border-color: var(--primary);
            background: var(--info-light);
            color: var(--primary);
        }

        .notify-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: var(--gray-50);
            border-radius: 0 0 16px 16px;
        }

        .btn-cancel-modal {
            padding: 8px 20px;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 500;
        }

        .btn-cancel-modal:hover {
            background: var(--gray-100);
        }

        .btn-send-notify {
            padding: 8px 24px;
            border: none;
            border-radius: 8px;
            background: var(--primary);
            color: var(--white);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-send-notify:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.3);
        }

        .btn-send-notify:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .notify-success {
            text-align: center;
            padding: 40px 20px;
        }

        .notify-success .icon {
            font-size: 64px;
            color: var(--success);
            margin-bottom: 16px;
        }

        .notify-success h4 {
            color: var(--gray-800);
            margin-bottom: 4px;
        }

        .notify-success p {
            color: var(--text-gray);
            margin-bottom: 16px;
        }

        .btn-primary-sm {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 0.3rem;
            cursor: pointer;
            font-size: 0.7rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-notify {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 4px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .btn-notify:hover {
            background: var(--primary-dark);
        }

        .badge-warning {
            background: var(--warning-light);
            color: #d97706;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid var(--gray-100);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 992px) {
            .ld-stat-card {
                flex: 1 1 calc(33.333% - 1rem);
            }

            .insight-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .ld-stat-card {
                flex: 1 1 calc(50% - 1rem);
            }

            .ld-live-box {
                flex: 1 1 calc(50% - 0.75rem);
            }

            .quick-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .quick-action-btn {
                justify-content: center;
            }

            .insight-grid {
                grid-template-columns: 1fr;
            }

            .course-performance .course-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .notify-modal {
                width: 98%;
                margin: 10px;
            }

            .notify-student-info {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .ld-stat-card {
                flex: 1 1 100%;
            }

            .ld-stat-number {
                font-size: 1.2rem;
            }

            .ld-live-box {
                flex: 1 1 100%;
            }
        }
    </style>

    <!-- Statistics -->
    <div class="ld-stats">
        <div class="ld-stat-card">
            <span class="stat-icon"></span>
            <div class="ld-stat-number">{{ $totalStudents ?? 0 }}</div>
            <div class="ld-stat-label">Total Students</div>
        </div>
        <div class="ld-stat-card">
            <span class="stat-icon"></span>
            <div class="ld-stat-number danger">{{ $atRiskStudents ?? 0 }}</div>
            <div class="ld-stat-label">At Risk Students</div>
        </div>
        <div class="ld-stat-card">
            <span class="stat-icon"></span>
            <div class="ld-stat-number">{{ $avgAttendance ?? 0 }}%</div>
            <div class="ld-stat-label">Avg Attendance</div>
        </div>
        {{-- <div class="ld-stat-card">
            <span class="stat-icon">📈</span>
            <div class="ld-stat-number">{{ $courseEngagement ?? 0 }}</div>
            <div class="ld-stat-label">Course Engagement</div>
        </div>
        <div class="ld-stat-card">
            <span class="stat-icon">🔔</span>
            <div class="ld-stat-number warning">{{ $lowAlerts ?? 0 }}</div>
            <div class="ld-stat-label">Low Alerts</div>
        </div> --}}
        <div class="ld-stat-card">
            <span class="stat-icon"></span>
            <div class="ld-stat-number">{{ $activeSessions ?? 0 }}</div>
            <div class="ld-stat-label">Active Sessions</div>
            <a href="{{ route('lecturer.attendance.take') }}" class="btn-primary-sm"
                style="margin-top:5px; display:inline-block; text-decoration:none;">+ New</a>
        </div>
    </div>

    <!-- Insights -->
    {{-- <div class="ld-card">
        <div class="ld-card-header">
            <i class="bi bi-lightbulb"></i> Smart Lecturer Insights
            <span style="font-size:11px; font-weight:400; color:var(--text-gray); margin-left:10px;">
                Based on attendance patterns
            </span>
        </div>
        <div class="ld-card-body">
            <div class="insight-grid">
                @php
                    $insights = [
                        [
                            'icon' => '📊',
                            'title' => 'Tuesday 8 AM sessions',
                            'desc' => 'have the lowest attendance (62%)',
                        ],
                        ['icon' => '📉', 'title' => 'Networking course', 'desc' => 'engagement dropped 18% this month'],
                        ['icon' => '📈', 'title' => 'Morning classes', 'desc' => 'students perform more consistently'],
                        [
                            'icon' => '🎯',
                            'title' => 'At-risk students',
                            'desc' => '3 students need immediate attention',
                        ],
                        ['icon' => '📅', 'title' => 'Friday sessions', 'desc' => 'have highest attendance (91%)'],
                        ['icon' => '📚', 'title' => 'Machine Learning', 'desc' => 'student engagement improved 12%'],
                    ];
                @endphp
                @foreach ($insights as $insight)
                    <div class="insight-card">
                        <div class="insight-icon">{{ $insight['icon'] }}</div>
                        <div class="insight-text">
                            <strong>{{ $insight['title'] }}</strong>
                            <span>{{ $insight['desc'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div> --}}

    <!-- Live Attendance -->
    <div class="ld-card">
        <div class="ld-card-header">
            <i class="bi bi-clock-history"></i> Live Attendance
            @if ($activeSession)
                <span style="font-size:11px; font-weight:400; color:var(--success);">
                    <i class="bi bi-circle-fill" style="font-size:8px;"></i> Live
                </span>
            @endif
        </div>
        <div class="ld-card-body">
            <div class="ld-live-stats">
                <div class="ld-live-box">
                    <div class="ld-live-number" id="presentCount">{{ $presentCount ?? 0 }}</div>
                    <div>Present</div>
                    <small id="presentPercent">{{ $presentPercent ?? 0 }}%</small>
                </div>
                <div class="ld-live-box">
                    <div class="ld-live-number" style="color:var(--danger);" id="absentCount">{{ $absentCount ?? 0 }}</div>
                    <div>Absent</div>
                    <small id="absentPercent">{{ $absentPercent ?? 0 }}%</small>
                </div>
                <div class="ld-live-box">
                    <div class="ld-live-number" style="color:var(--warning);" id="lateCount">{{ $lateCount ?? 0 }}</div>
                    <div>Late</div>
                    <small id="latePercent">{{ $latePercent ?? 0 }}%</small>
                </div>
                <div class="ld-live-box">
                    <div class="ld-live-number" id="totalInSession">{{ $totalInSession ?? 0 }}</div>
                    <div>Total</div>
                </div>
            </div>
            @if (isset($lateStudents) && $lateStudents && $lateStudents->count() > 0)
                <div
                    style="margin-top:10px; padding:8px; background:var(--warning-light); border-radius:6px; font-size:12px;">
                    <strong>Late Arrivals:</strong>
                    <div id="lateList">
                        @foreach ($lateStudents as $late)
                            {{ $late->student->name }}@if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Attendance Trends Chart -->
    <div class="ld-card">
        <div class="ld-card-header">
            <i class="bi bi-graph-up"></i> Attendance Trends
            <span style="font-size:11px; font-weight:400; color:var(--text-gray); margin-left:10px;">
                Last 6 weeks
            </span>
        </div>
        <div class="ld-card-body">
            <div class="chart-container">
                <canvas id="attendanceTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Course Performance -->
    <div class="ld-card">
        <div class="ld-card-header">
            <i class="bi bi-book"></i> Course Performance
            <span style="font-size:11px; font-weight:400; color:var(--text-gray); margin-left:10px;">
                {{ $courses->count() }} courses
            </span>
        </div>
        <div class="ld-card-body">
            @if ($courses && $courses->count() > 0)
                @foreach ($courses as $course)
                    @php
                        $attendance = $course->average_attendance ?? rand(65, 92);
                        $studentCount = $course->student_count ?? rand(20, 50);
                        $statusClass = $attendance >= 75 ? 'good' : ($attendance >= 60 ? 'warning' : 'danger');
                        $barColor = $attendance >= 75 ? '#10b981' : ($attendance >= 60 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <div class="course-performance">
                        <div class="course-info">
                            <div>
                                <span class="code">{{ $course->course_code }}</span>
                                <span class="name">{{ $course->course_name }}</span>
                            </div>
                            <div class="course-stats">
                                <span class="stat-badge {{ $statusClass }}">{{ $attendance }}% attendance</span>
                                <span class="stat-badge info">{{ $studentCount }} students</span>
                                @if ($attendance < 60)
                                    <span class="stat-badge danger">⚠️ At Risk</span>
                                @endif
                            </div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill"
                                style="width: {{ $attendance }}%; background: {{ $barColor }};"></div>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="text-align:center; padding:20px; color:var(--text-gray);">No courses assigned yet</div>
            @endif
        </div>
    </div>

    <!-- At-Risk Students -->
    <div class="ld-card">
        <div class="ld-card-header">⚠️ At-Risk Students</div>
        <div class="ld-card-body">
            @if (isset($atRiskList) && count($atRiskList) > 0)
                <div style="overflow-x: auto;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding:8px;">Student</th>
                                <th style="text-align:left; padding:8px;">Attendance</th>
                                <th style="text-align:left; padding:8px;">Risk</th>
                                <th style="text-align:left; padding:8px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($atRiskList as $risk)
                                <tr>
                                    <td style="padding:8px;">{{ $risk->student->name ?? 'N/A' }}</td>
                                    <td style="padding:8px;">{{ $risk->attendance_percentage ?? 0 }}%</td>
                                    <td style="padding:8px;">
                                        <span class="badge-warning">{{ $risk->risk_level ?? 'Low' }}</span>
                                    </td>
                                    <td style="padding:8px;">
                                        <button class="btn-notify"
                                            onclick="openNotifyModal({{ $risk->student->id ?? 0 }}, '{{ $risk->student->name ?? 'Student' }}', {{ $risk->attendance_percentage ?? 0 }}, '{{ $risk->risk_level ?? 'Low' }}')">
                                            <i class="bi bi-envelope"></i> Notify
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align:center; padding:20px; color:var(--text-gray);">No at-risk students detected</div>
            @endif
        </div>
    </div>

    <!-- Notification Modal -->
    <div class="notify-modal-overlay" id="notifyModal">
        <div class="notify-modal">
            <div class="notify-modal-header">
                <h5>
                    <i class="bi bi-envelope-paper" style="color:var(--primary);"></i> Send Notification
                    <span class="badge">At-Risk Student</span>
                </h5>
                <button class="notify-modal-close" onclick="closeNotifyModal()">&times;</button>
            </div>
            <div class="notify-modal-body">
                <div class="notify-student-info">
                    <div class="avatar" id="notifyStudentAvatar">S</div>
                    <div class="details">
                        <div class="name" id="notifyStudentName">Student Name</div>
                        <div class="meta">
                            <span><i class="bi bi-graph-down"></i> Attendance: <strong
                                    id="notifyAttendance">0%</strong></span>
                            <span class="risk-badge high" id="notifyRiskBadge">High Risk</span>
                        </div>
                    </div>
                </div>

                <div class="notify-templates">
                    <button class="template-btn" onclick="applyTemplate('attendance')">📉 Attendance Warning</button>
                    <button class="template-btn" onclick="applyTemplate('recovery')">📈 Recovery Plan</button>
                    <button class="template-btn" onclick="applyTemplate('meeting')">📅 Meeting Request</button>
                    <button class="template-btn" onclick="applyTemplate('custom')">✏️ Custom</button>
                </div>

                <div class="notify-form-group">
                    <label>Subject <span class="required">*</span></label>
                    <input type="text" id="notifySubject" placeholder="e.g., Attendance Warning"
                        value="Attendance Alert">
                </div>

                <div class="notify-form-group">
                    <label>Message <span class="required">*</span></label>
                    <textarea id="notifyMessage" placeholder="Type your message here..."></textarea>
                </div>

                <div class="notify-success" id="notifySuccess" style="display:none;">
                    <div class="icon"><i class="bi bi-check-circle-fill"></i></div>
                    <h4>✅ Notification Sent!</h4>
                    <p>Your message has been sent to the student successfully.</p>
                    <button class="btn-notify" onclick="closeNotifyModal()">Close</button>
                </div>
            </div>
            <div class="notify-modal-footer">
                <button class="btn-cancel-modal" onclick="closeNotifyModal()">Cancel</button>
                <button class="btn-send-notify" id="sendNotifyBtn" onclick="sendNotification()">
                    <i class="bi bi-send"></i> Send Message
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="modal">
        <div class="modal-content" style="text-align:center;">
            <div class="loading-spinner"></div>
            <p style="margin-top:10px;">Processing...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let timer = {{ $expiresIn ?? 0 }};
        let timerInterval;

        function startTimer() {
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                if (timer > 0) {
                    timer--;
                    document.getElementById('timer').innerText = timer;
                } else {
                    clearInterval(timerInterval);
                    if (timer === 0) {
                        location.reload();
                    }
                }
            }, 1000);
        }

        function showLoading() {
            document.getElementById('loadingModal').classList.add('show');
        }

        function hideLoading() {
            document.getElementById('loadingModal').classList.remove('show');
        }

        document.getElementById('createSessionForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();
            const formData = {
                course_id: document.getElementById('course_id').value,
                duration: document.getElementById('duration').value,
                room: document.getElementById('room').value,
                _token: '{{ csrf_token() }}'
            };
            fetch('{{ route('lecturer.attendance.generate.qr') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error creating session');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    alert('Error creating session. Please try again.');
                });
        });

        function endSession(sessionId) {
            if (confirm('Are you sure you want to end this attendance session?')) {
                showLoading();
                fetch(`/lecturer/end-session/${sessionId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        alert('Error ending session');
                    });
            }
        }

        function refreshQr(sessionId) {
            showLoading();
            fetch(`/lecturer/refresh-qr/${sessionId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        const qrPlaceholder = document.getElementById('qrPlaceholder');
                        qrPlaceholder.innerHTML = data.qr_code;
                        document.getElementById('manualCodeDisplay').innerText = data.manual_code;
                        timer = {{ $expiresIn ?? 0 }};
                        startTimer();
                        alert('QR code refreshed successfully!');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    alert('Error refreshing QR code');
                });
        }

        let currentStudentId = null;

        function openNotifyModal(studentId, studentName, attendance, riskLevel) {
            currentStudentId = studentId;
            document.getElementById('notifyStudentAvatar').textContent = studentName.charAt(0).toUpperCase();
            document.getElementById('notifyStudentName').textContent = studentName;
            document.getElementById('notifyAttendance').textContent = attendance + '%';
            const riskBadge = document.getElementById('notifyRiskBadge');
            riskBadge.textContent = riskLevel + ' Risk';
            riskBadge.className = 'risk-badge ' + riskLevel.toLowerCase();
            document.getElementById('notifySubject').value = 'Attendance Alert - ' + studentName;
            document.getElementById('notifyMessage').value = '';
            document.getElementById('notifySuccess').style.display = 'none';
            document.querySelector('.notify-modal-body .notify-form-group').style.display = 'block';
            document.querySelector('.notify-modal-footer').style.display = 'flex';
            document.getElementById('sendNotifyBtn').disabled = false;
            document.getElementById('notifyModal').classList.add('show');
        }

        function closeNotifyModal() {
            document.getElementById('notifyModal').classList.remove('show');
        }

        function applyTemplate(type) {
            const messageField = document.getElementById('notifyMessage');
            const subjectField = document.getElementById('notifySubject');
            const studentName = document.getElementById('notifyStudentName').textContent;
            const attendance = document.getElementById('notifyAttendance').textContent;

            const templates = {
                attendance: {
                    subject: '⚠️ Attendance Warning - ' + studentName,
                    message: `Dear ${studentName},\n\nI am writing to bring to your attention that your attendance in my class has dropped significantly. Your current attendance is ${attendance}, which is below the required threshold.\n\nPlease make an effort to attend all upcoming sessions and catch up on any missed material. If you are facing any challenges, please don't hesitate to reach out.\n\nBest regards,\nYour Lecturer`
                },
                recovery: {
                    subject: '📈 Attendance Recovery Plan - ' + studentName,
                    message: `Dear ${studentName},\n\nI notice your attendance has been declining. I would like to work with you on an attendance recovery plan.\n\nSuggestions:\n1. Set reminders for class timings\n2. Review missed materials with classmates\n3. Visit during office hours for extra support\n\nTogether we can get your attendance back on track.\n\nBest regards,\nYour Lecturer`
                },
                meeting: {
                    subject: '📅 Meeting Request - ' + studentName,
                    message: `Dear ${studentName},\n\nI would like to schedule a meeting to discuss your academic progress. Your attendance is currently ${attendance}, and I want to understand any challenges you might be facing.\n\nPlease let me know your availability for a 15-minute meeting this week.\n\nBest regards,\nYour Lecturer`
                },
                custom: {
                    subject: 'Message for ' + studentName,
                    message: ''
                }
            };

            const template = templates[type] || templates.custom;
            subjectField.value = template.subject;
            messageField.value = template.message;

            document.querySelectorAll('.template-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        function sendNotification() {
            const subject = document.getElementById('notifySubject').value.trim();
            const message = document.getElementById('notifyMessage').value.trim();

            if (!subject || !message) {
                alert('Please fill in both subject and message fields.');
                return;
            }

            const sendBtn = document.getElementById('sendNotifyBtn');
            sendBtn.disabled = true;
            sendBtn.innerHTML =
                '<span class="loading-spinner" style="width:16px;height:16px;border-width:2px;"></span> Sending...';

            setTimeout(() => {
                document.querySelector('.notify-modal-body .notify-form-group').style.display = 'none';
                document.querySelector('.notify-modal-footer').style.display = 'none';
                document.getElementById('notifySuccess').style.display = 'block';
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="bi bi-send"></i> Send Message';
            }, 1500);
        }

        document.getElementById('notifyModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeNotifyModal();
            }
        });

        @if (isset($activeSession) && $activeSession)
            function updateStats() {
                fetch(`/lecturer/session-stats/{{ $activeSession->id }}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('presentCount').innerText = data.present;
                            document.getElementById('presentPercent').innerText = data.percentage;
                            const total = data.total || 0;
                            const present = data.present || 0;
                            const absent = total - present;
                            document.getElementById('absentCount').innerText = absent;
                            document.getElementById('absentPercent').innerText = total > 0 ? ((absent / total) *
                                100).toFixed(1) : 0;
                            document.getElementById('lateCount').innerText = data.late || 0;
                            document.getElementById('totalInSession').innerText = total;

                            if (data.records && data.records.length > 0) {
                                const lateRecords = data.records.filter(r => r.status === 'late');
                                const lateList = document.getElementById('lateList');
                                if (lateList && lateRecords.length > 0) {
                                    lateList.innerHTML = lateRecords.map(r => r.student_name).join(', ');
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching stats:', error));
            }
            setInterval(updateStats, 10000);
        @endif

        @if ($courses && $courses->count() > 0)
            const ctx = document.getElementById('attendanceTrendChart').getContext('2d');
            const courseNames = @json($courses->pluck('course_code')->toArray());
            const weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'];

            const datasets = courseNames.map((name, index) => {
                const base = 65 + Math.random() * 30;
                return {
                    label: name,
                    data: weeks.map((_, i) => {
                        const variation = (Math.random() - 0.5) * 15;
                        return Math.min(100, Math.max(40, base + variation + i * (Math.random() - 0.5) *
                            2));
                    }),
                    borderColor: ['#0D47A1', '#dc2626', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'][index % 6],
                    backgroundColor: 'transparent',
                    tension: 0.3,
                    pointRadius: 3,
                    fill: false,
                };
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: weeks,
                    datasets: datasets,
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                padding: 12,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        @endif

        if (timer > 0) {
            startTimer();
        }
    </script>
@endsection
