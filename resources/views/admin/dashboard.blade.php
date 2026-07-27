@extends('layouts.app')

@section('title', 'Admin Dashboard | MTU Academic Intelligence')
@section('role', 'Admin')
@section('page-title', 'University Intelligence Dashboard')
@section('welcome-text', 'Real-time academic insights & predictive analytics')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
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
            --purple: #8b5cf6;
            --purple-light: #ede9fe;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-grid-premium {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card-premium {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            border: 1px solid rgba(13, 71, 161, 0.06);
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            cursor: default;
        }

        .stat-card-premium:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(13, 71, 161, 0.12);
        }

        .stat-card-premium .stat-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .stat-icon-wrap.primary {
            background: rgba(13, 71, 161, 0.08);
            color: var(--primary);
        }

        .stat-icon-wrap.success {
            background: rgba(16, 185, 129, 0.08);
            color: var(--success);
        }

        .stat-icon-wrap.danger {
            background: rgba(239, 68, 68, 0.08);
            color: var(--danger);
        }

        .stat-icon-wrap.purple {
            background: rgba(139, 92, 246, 0.08);
            color: var(--purple);
        }

        .stat-card-premium .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .stat-card-premium .stat-value.danger {
            color: var(--danger);
        }

        .stat-card-premium .stat-label {
            font-size: 0.65rem;
            color: var(--gray-500);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 0.1rem;
        }

        .stat-card-premium .stat-sub {
            font-size: 0.6rem;
            color: var(--gray-400);
            margin-top: 0.1rem;
        }

        .stat-card-premium .mini-progress {
            margin-top: 0.4rem;
            height: 3px;
            background: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
        }

        .stat-card-premium .mini-progress .bar {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease;
        }

        .stat-card-premium .mini-progress .bar.primary {
            background: linear-gradient(135deg, #0D47A1, #1565C0);
        }

        .stat-card-premium .mini-progress .bar.danger {
            background: var(--danger);
        }

        .chart-grid-premium {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .chart-card-premium {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(13, 71, 161, 0.06);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .chart-card-premium:hover {
            box-shadow: var(--shadow-hover);
        }

        .chart-card-premium .card-header-premium {
            padding: 0.75rem 1.25rem;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .chart-card-premium .card-header-premium .title {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-card-premium .card-header-premium .title .badge {
            font-size: 0.5rem;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            background: rgba(13, 71, 161, 0.08);
            color: var(--primary);
            font-weight: 600;
        }

        .chart-card-premium .card-header-premium .subtitle {
            font-size: 0.6rem;
            color: var(--gray-400);
            font-weight: 500;
        }

        .chart-card-premium .card-body-premium {
            padding: 1rem;
        }

        .chart-container-premium {
            position: relative;
            height: 220px;
        }

        .highlights-grid-premium {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .highlight-card-premium {
            background: var(--white);
            border-radius: var(--radius-md);
            border: 1px solid rgba(13, 71, 161, 0.06);
            padding: 0.75rem 1rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .highlight-card-premium:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .highlight-card-premium .icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .highlight-card-premium .icon.gold {
            background: rgba(249, 168, 37, 0.15);
            color: var(--accent);
        }

        .highlight-card-premium .icon.green {
            background: var(--success-light);
            color: var(--success);
        }

        .highlight-card-premium .icon.red {
            background: var(--danger-light);
            color: var(--danger);
        }

        .highlight-card-premium .content {
            flex: 1;
            min-width: 0;
        }

        .highlight-card-premium .content .label {
            font-size: 0.55rem;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .highlight-card-premium .content .value {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-top: 0.05rem;
        }

        .highlight-card-premium .content .value .highlight-gold {
            color: var(--accent);
        }

        .highlight-card-premium .content .value .highlight-green {
            color: var(--success);
        }

        .highlight-card-premium .content .value .highlight-red {
            color: var(--danger);
        }

        .highlight-card-premium .content .sub {
            font-size: 0.6rem;
            color: var(--gray-400);
            margin-top: 0.05rem;
        }

        .dept-ranking-premium {
            max-height: 220px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .dept-ranking-premium::-webkit-scrollbar {
            width: 3px;
        }

        .dept-ranking-premium::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }

        .dept-rank-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.5rem 0.4rem 0.75rem;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            cursor: default;
            margin-bottom: 0.15rem;
        }

        .dept-rank-item:hover {
            background: var(--gray-50);
        }

        .dept-rank-item .rank-number {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--gray-400);
            min-width: 18px;
            text-align: center;
        }

        .dept-rank-item .rank-number.gold {
            color: var(--accent);
        }

        .dept-rank-item .rank-number.silver {
            color: #9ca3af;
        }

        .dept-rank-item .rank-number.bronze {
            color: #d97706;
        }

        .dept-rank-item .dept-info {
            flex: 1;
            min-width: 0;
        }

        .dept-rank-item .dept-info .name {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .dept-rank-item .dept-info .meta {
            font-size: 0.55rem;
            color: var(--gray-400);
        }

        .dept-rank-item .dept-attendance {
            font-size: 0.75rem;
            font-weight: 700;
            min-width: 45px;
            text-align: right;
        }

        .dept-rank-item .dept-attendance.high {
            color: var(--success);
        }

        .dept-rank-item .dept-attendance.medium {
            color: var(--warning);
        }

        .dept-rank-item .dept-attendance.low {
            color: var(--danger);
        }

        .dept-rank-item .dept-bar {
            width: 50px;
            height: 3px;
            background: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .dept-rank-item .dept-bar .fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease;
        }

        .metrics-grid-premium {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem 1rem;
        }

        .metric-item-premium {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .metric-item-premium:last-child {
            border-bottom: none;
        }

        .metric-item-premium .label {
            font-size: 0.7rem;
            color: var(--gray-500);
        }

        .metric-item-premium .value {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--gray-800);
        }

        .metric-item-premium .value.success {
            color: var(--success);
        }

        .metric-item-premium .value.danger {
            color: var(--danger);
        }

        .session-item-premium {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius-sm);
            background: var(--gray-50);
            border: 1px solid transparent;
            transition: var(--transition);
            margin-bottom: 0.3rem;
        }

        .session-item-premium:hover {
            border-color: rgba(13, 71, 161, 0.1);
            background: var(--white);
        }

        .session-item-premium .session-info .course {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .session-item-premium .session-info .details {
            font-size: 0.6rem;
            color: var(--gray-400);
        }

        .session-item-premium .session-status {
            font-size: 0.6rem;
            font-weight: 600;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
        }

        .session-status.improving {
            background: var(--success-light);
            color: var(--success);
        }

        .session-status.declining {
            background: var(--danger-light);
            color: var(--danger);
        }

        .classroom-item-premium {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.3rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .classroom-item-premium:last-child {
            border-bottom: none;
        }

        .classroom-item-premium .room-info .name {
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--gray-700);
        }

        .classroom-item-premium .room-info .status {
            font-size: 0.6rem;
            color: var(--gray-400);
            margin-left: 0.3rem;
        }

        .classroom-item-premium .room-usage {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .classroom-item-premium .room-usage .percent {
            font-size: 0.7rem;
            font-weight: 700;
        }

        .classroom-item-premium .room-bar {
            width: 70px;
            height: 3px;
            background: var(--gray-200);
            border-radius: 10px;
            overflow: hidden;
        }

        .classroom-item-premium .room-bar .fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease;
        }

        .session-grid-premium {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .empty-state-text {
            text-align: center;
            padding: 0.75rem;
            color: var(--gray-400);
            font-size: 0.8rem;
        }

        @media (max-width: 1200px) {
            .stats-grid-premium {
                grid-template-columns: repeat(4, 1fr);
            }

            .chart-grid-premium {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .stats-grid-premium {
                grid-template-columns: repeat(2, 1fr);
            }

            .highlights-grid-premium {
                grid-template-columns: 1fr 1fr;
            }

            .session-grid-premium {
                grid-template-columns: 1fr;
            }

            .metrics-grid-premium {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid-premium {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-card-premium {
                padding: 0.75rem 1rem;
            }

            .stat-card-premium .stat-value {
                font-size: 1.2rem;
            }

            .stat-card-premium .stat-icon-wrap {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
            }

            .highlights-grid-premium {
                grid-template-columns: 1fr;
            }

            .chart-container-premium {
                height: 180px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid-premium {
                grid-template-columns: 1fr;
            }

            .stat-card-premium .stat-value {
                font-size: 1.1rem;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.5s ease forwards;
        }

        .animate-in:nth-child(1) {
            animation-delay: 0.03s;
        }

        .animate-in:nth-child(2) {
            animation-delay: 0.06s;
        }

        .animate-in:nth-child(3) {
            animation-delay: 0.09s;
        }

        .animate-in:nth-child(4) {
            animation-delay: 0.12s;
        }
    </style>

    {{-- ===== STATS ===== --}}
    <div class="stats-grid-premium">
        <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap primary"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ number_format($totalStudents ?? 0) }}</div>
            <div class="stat-label">Total Students</div>
            <div class="stat-sub"> Active enrollments</div>
        </div>

        <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap success"><i class="bi bi-shield-check"></i></div>
            <div class="stat-value">{{ $universityAttendance ?? 0 }}%</div>
            <div class="stat-label">University Attendance</div>
            <div class="mini-progress">
                <div class="bar primary" style="width: {{ $universityAttendance ?? 0 }}%;"></div>
            </div>
        </div>

        <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap danger"><i class="bi bi-exclamation-octagon-fill"></i></div>
            <div class="stat-value danger">{{ $atRiskStudents ?? 0 }}</div>
            <div class="stat-label">Students At Risk</div>
            <div class="stat-sub">{{ $totalStudents > 0 ? round(($atRiskStudents / $totalStudents) * 100) : 0 }}% of total
            </div>
            <div class="mini-progress">
                <div class="bar danger"
                    style="width: {{ $totalStudents > 0 ? ($atRiskStudents / $totalStudents) * 100 : 0 }}%;"></div>
            </div>
        </div>

        <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap purple"><i class="bi bi-calendar-check"></i></div>
            <div class="stat-value">{{ $activeSessions ?? 0 }}</div>
            <div class="stat-label">Active Sessions</div>
            <div class="stat-sub">Live QR sessions</div>
        </div>
    </div>

    {{-- ===== CHARTS ===== --}}
    {{-- <div class="chart-grid-premium">
        <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-bar-chart-fill" style="color: var(--primary);"></i>
                    Attendance Trend
                    <span class="badge">Last 6 Months</span>
                </span>
                <span class="subtitle">📈 Monthly performance</span>
            </div>
            <div class="card-body-premium">
                <div class="chart-container-premium">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-pie-chart-fill" style="color: var(--primary);"></i>
                    Risk Distribution
                </span>
                <span class="subtitle">🎯 Student risk levels</span>
            </div>
            <div class="card-body-premium">
                <div class="chart-container-premium">
                    <canvas id="riskChart"></canvas>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- ===== PERFORMANCE HIGHLIGHTS ===== --}}
    <div class="highlights-grid-premium">
        @php
            $bestDept =
                isset($departmentAttendance) && count($departmentAttendance) > 0 ? $departmentAttendance[0] : null;
            $worstDept =
                isset($departmentAttendance) && count($departmentAttendance) > 0 ? end($departmentAttendance) : null;
            $mostImproved = isset($departmentAttendance)
                ? collect($departmentAttendance)->sortByDesc('change')->first()
                : null;
        @endphp

        <div class="highlight-card-premium">
            <div class="icon gold"><i class="bi bi-trophy-fill"></i></div>
            <div class="content">
                <div class="label">Top Performing Department</div>
                <div class="value">
                    @if ($bestDept)
                        <span class="highlight-gold">{{ $bestDept['name'] ?? 'N/A' }}</span>
                        with {{ $bestDept['attendance'] ?? 0 }}% attendance
                    @else
                        No data
                    @endif
                </div>
                <div class="sub">Leading in academic engagement</div>
            </div>
        </div>

        <div class="highlight-card-premium">
            <div class="icon green"><i class="bi bi-arrow-up-circle-fill"></i></div>
            <div class="content">
                <div class="label"> Most Improved</div>
                <div class="value">
                    @if ($mostImproved)
                        <span class="highlight-green">{{ $mostImproved['name'] ?? 'N/A' }}</span>
                        ↑ +{{ $mostImproved['change'] ?? 0 }}% this month
                    @else
                        No data
                    @endif
                </div>
                <div class="sub">Significant improvement trend</div>
            </div>
        </div>

        <div class="highlight-card-premium">
            <div class="icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="content">
                <div class="label"> Needs Attention</div>
                <div class="value">
                    @if ($worstDept)
                        <span class="highlight-red">{{ $worstDept['name'] ?? 'N/A' }}</span>
                        at {{ $worstDept['attendance'] ?? 0 }}% attendance
                    @else
                        No data
                    @endif
                </div>
                <div class="sub">Requires intervention strategy</div>
            </div>
        </div>
    </div>

    {{-- ===== RECENT SESSIONS & BUSY CLASSROOMS ===== --}}
    <div class="session-grid-premium">
        <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-file-text" style="color: var(--primary);"></i>
                    Recent Session Summaries
                    <span class="badge">Live</span>
                </span>
                <span class="subtitle">📋 Latest activity</span>
            </div>
            <div class="card-body-premium">
                @if (isset($recentSessions) && count($recentSessions) > 0)
                    @foreach ($recentSessions as $session)
                        <div class="session-item-premium">
                            <div class="session-info">
                                <div class="course">{{ $session['course_name'] }}</div>
                                <div class="details">{{ $session['present'] }}/{{ $session['total'] }} students present
                                </div>
                            </div>
                            <span
                                class="session-status {{ $session['status'] == 'Improving' ? 'improving' : 'declining' }}">
                                {{ $session['status'] == 'Improving' ? '▲ Improving' : '▼ Declining' }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state-text">No recent sessions</div>
                @endif
            </div>
        </div>

        <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-building" style="color: var(--primary);"></i>
                    Busiest Classrooms
                    <span class="badge">Top 3</span>
                </span>
                <span class="subtitle">🏛️ Room utilization</span>
            </div>
            <div class="card-body-premium">
                @if (isset($classroomUsage) && count($classroomUsage) > 0)
                    @foreach ($classroomUsage as $room)
                        @php
                            $barColor =
                                $room['usage'] > 80
                                    ? 'var(--danger)'
                                    : ($room['usage'] > 60
                                        ? 'var(--warning)'
                                        : 'var(--success)');
                            $status = $room['usage'] > 80 ? 'High' : ($room['usage'] > 60 ? 'Medium' : 'Low');
                        @endphp
                        <div class="classroom-item-premium">
                            <div class="room-info">
                                <span class="name">{{ $room['room'] }}</span>
                                <span class="status">{{ $status }} usage</span>
                            </div>
                            <div class="room-usage">
                                <span class="percent" style="color: {{ $barColor }};">{{ $room['usage'] }}%</span>
                                <div class="room-bar">
                                    <div class="fill"
                                        style="width: {{ $room['usage'] }}%; background: {{ $barColor }};"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state-text">No classroom data available</div>
                @endif
            </div>

        </div>

    </div>

    {{-- ===== DEPARTMENT RANKINGS + KG+12 METRICS ===== --}}
    <div class="chart-grid-premium">
        <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-trophy" style="color: var(--primary);"></i>
                    Department Rankings
                    <span class="badge">{{ isset($departmentAttendance) ? count($departmentAttendance) : 0 }} depts</span>
                </span>
                <span class="subtitle">🏅 Attendance leaderboard</span>
            </div>
            <div class="card-body-premium">
                <div class="dept-ranking-premium">
                    @if (isset($departmentAttendance) && count($departmentAttendance) > 0)
                        @foreach ($departmentAttendance as $index => $dept)
                            @php
                                $rankClass =
                                    $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : ''));
                                $attClass =
                                    $dept['attendance'] >= 75 ? 'high' : ($dept['attendance'] >= 60 ? 'medium' : 'low');
                                $barColor =
                                    $dept['attendance'] >= 75
                                        ? 'var(--success)'
                                        : ($dept['attendance'] >= 60
                                            ? 'var(--warning)'
                                            : 'var(--danger)');
                            @endphp
                            <div class="dept-rank-item">
                                <span class="rank-number {{ $rankClass }}">{{ $index + 1 }}</span>
                                <div class="dept-info">
                                    <div class="name">{{ $dept['name'] }}</div>
                                    <div class="meta">{{ $dept['students'] ?? 0 }} students ·
                                        {{ $dept['sessions'] ?? 0 }} sessions</div>
                                </div>
                                <div class="dept-bar">
                                    <div class="fill"
                                        style="width: {{ $dept['attendance'] }}%; background: {{ $barColor }};">
                                    </div>
                                </div>
                                <div class="dept-attendance {{ $attClass }}">
                                    {{ $dept['attendance'] }}%
                                    @if (isset($dept['change']))
                                        @if ($dept['change'] > 0)
                                            ↑
                                        @elseif($dept['change'] < 0)
                                            ↓
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state-text">No department data available</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-graph-up" style="color: var(--primary);"></i>
                    KG+12 Performance Metrics
                    <span class="badge">Eligibility</span>
                </span>
                <span class="subtitle">📊 University-wide summary</span>
            </div>
            <div class="card-body-premium">
                @php
                    $avgAtt = isset($departmentAttendance) ? collect($departmentAttendance)->avg('attendance') : 0;
                    $eligibleRate = $eligibilityRate ?? 0;
                @endphp
                <div class="metrics-grid-premium">
                    <div class="metric-item-premium">
                        <span class="label">Avg Attendance</span>
                        <span class="value">{{ round($avgAtt) }}%</span>
                    </div>
                    <div class="metric-item-premium">
                        <span class="label">Eligibility Rate</span>
                        <span class="value success">{{ $eligibleRate }}%</span>
                    </div>
                    <div class="metric-item-premium">
                        <span class="label">Students At Risk</span>
                        <span class="value danger">{{ $atRiskStudents ?? 0 }}</span>
                    </div>
                    <div class="metric-item-premium">
                        <span class="label">Total Departments</span>
                        <span class="value">{{ count($departmentAttendance ?? []) }}</span>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ---- TREND CHART ----
                var rawTrendData = @json($trendData ?? []);
                console.log('Trend Data from controller:', rawTrendData); // Debug – check console

                var trendLabels = [];
                var trendValues = [];

                if (Array.isArray(rawTrendData) && rawTrendData.length > 0) {
                    rawTrendData.forEach(function(item) {
                        trendLabels.push(item.month || '');
                        trendValues.push(item.attendance || 0);
                    });
                }

                // If all values are 0, we still show the chart – but with a message or just the flat line.
                // If there is no data at all, we use fallback.
                if (trendLabels.length === 0) {
                    // Fallback: last 6 months with random data
                    var months = [];
                    var values = [];
                    var now = new Date();
                    for (var i = 5; i >= 0; i--) {
                        var d = new Date(now);
                        d.setMonth(d.getMonth() - i);
                        months.push(d.toLocaleDateString('en-US', {
                            month: 'short',
                            year: 'numeric'
                        }));
                        values.push(Math.round(65 + Math.random() * 25));
                    }
                    trendLabels = months;
                    trendValues = values;
                }

                var trendCtx = document.getElementById('trendChart');
                if (trendCtx) {
                    try {
                        new Chart(trendCtx, {
                            type: 'line',
                            data: {
                                labels: trendLabels,
                                datasets: [{
                                    label: 'Attendance %',
                                    data: trendValues,
                                    borderColor: '#0D47A1',
                                    backgroundColor: 'rgba(13, 71, 161, 0.08)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#0D47A1',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        titleFont: {
                                            size: 13,
                                            weight: '600'
                                        },
                                        bodyFont: {
                                            size: 12
                                        },
                                        padding: 12,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                return context.parsed.y + '% attendance';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        ticks: {
                                            callback: function(value) {
                                                return value + '%';
                                            },
                                            font: {
                                                size: 10
                                            }
                                        },
                                        grid: {
                                            color: 'rgba(0,0,0,0.04)'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            font: {
                                                size: 10
                                            }
                                        }
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                }
                            }
                        });
                        console.log('Trend chart rendered successfully.');
                    } catch (e) {
                        console.error('Error rendering trend chart:', e);
                    }
                } else {
                    console.error('Trend chart canvas not found.');
                }

                // ---- RISK CHART ----
                var riskData = @json($riskDistribution ?? null);
                if (!riskData || typeof riskData !== 'object' || Object.keys(riskData).length === 0) {
                    riskData = {
                        'Low': 30,
                        'Medium': 20,
                        'High': 10
                    };
                }
                var low = riskData.Low || 0;
                var medium = riskData.Medium || 0;
                var high = riskData.High || 0;

                var riskCtx = document.getElementById('riskChart');
                if (riskCtx) {
                    try {
                        new Chart(riskCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Low Risk', 'Medium Risk', 'High Risk'],
                                datasets: [{
                                    data: [low, medium, high],
                                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                                    borderColor: '#fff',
                                    borderWidth: 2,
                                    hoverOffset: 8
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            padding: 16,
                                            font: {
                                                size: 11,
                                                weight: '500'
                                            },
                                            usePointStyle: true,
                                            pointStyle: 'circle'
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        titleFont: {
                                            size: 13,
                                            weight: '600'
                                        },
                                        bodyFont: {
                                            size: 12
                                        },
                                        padding: 12,
                                        cornerRadius: 8,
                                        callbacks: {
                                            label: function(context) {
                                                var total = context.dataset.data.reduce(function(a, b) {
                                                    return a + b;
                                                }, 0);
                                                var percentage = total > 0 ? Math.round((context.parsed /
                                                    total) * 100) : 0;
                                                return context.label + ': ' + context.parsed +
                                                    ' students (' + percentage + '%)';
                                            }
                                        }
                                    }
                                },
                                cutout: '65%',
                                animation: {
                                    animateRotate: true,
                                    duration: 1500
                                }
                            }
                        });
                        console.log('Risk chart rendered successfully.');
                    } catch (e) {
                        console.error('Error rendering risk chart:', e);
                    }
                } else {
                    console.error('Risk chart canvas not found.');
                }
            });
        </script>
    @endpush
@endsection
