@extends('layouts.app')

@section('title', 'Admin Dashboard | MTU Academic Intelligence')
@section('role', 'Admin')
@section('page-title', '🎯 University Intelligence Dashboard')
@section('welcome-text', 'Real-time academic insights & predictive analytics')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        /* ============================================================
                               CSS VARIABLES
                               ============================================================ */
        :root {
            --primary: #800000;
            --primary-light: #a00000;
            --primary-dark: #4a0000;
            --primary-gradient: linear-gradient(135deg, #800000 0%, #a00000 50%, #c00000 100%);
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
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 8px 30px rgba(0, 0, 0, 0.08);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ============================================================
                               STATS GRID - COMPACT (8 Cards)
                               ============================================================ */
        .stats-grid-premium {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card-premium {
            background: white;
            border-radius: var(--radius-md);
            padding: 1rem 1.25rem;
            border: 1px solid rgba(128, 0, 0, 0.06);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .stat-card-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: var(--transition);
        }

        .stat-card-premium:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: rgba(128, 0, 0, 0.12);
        }

        .stat-card-premium:hover::before {
            opacity: 1;
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
            transition: var(--transition);
        }

        .stat-card-premium:hover .stat-icon-wrap {
            transform: scale(1.05);
        }

        .stat-icon-wrap.primary {
            background: rgba(128, 0, 0, 0.08);
            color: var(--primary);
        }

        .stat-icon-wrap.success {
            background: rgba(16, 185, 129, 0.08);
            color: var(--success);
        }

        .stat-icon-wrap.warning {
            background: rgba(245, 158, 11, 0.08);
            color: var(--warning);
        }

        .stat-icon-wrap.danger {
            background: rgba(239, 68, 68, 0.08);
            color: var(--danger);
        }

        .stat-icon-wrap.info {
            background: rgba(59, 130, 246, 0.08);
            color: var(--info);
        }

        .stat-icon-wrap.purple {
            background: rgba(139, 92, 246, 0.08);
            color: var(--purple);
        }

        .stat-card-premium .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -0.02em;
            line-height: 1.2;
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
            background: var(--primary-gradient);
        }

        .stat-card-premium .mini-progress .bar.success {
            background: var(--success);
        }

        .stat-card-premium .mini-progress .bar.warning {
            background: var(--warning);
        }

        .stat-card-premium .mini-progress .bar.danger {
            background: var(--danger);
        }

        /* ============================================================
                               CHART GRID
                               ============================================================ */
        .chart-grid-premium {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .chart-card-premium {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(128, 0, 0, 0.06);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: var(--transition);
        }

        .chart-card-premium:hover {
            box-shadow: var(--shadow-md);
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
            background: rgba(128, 0, 0, 0.08);
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

        /* ============================================================
                               DEPARTMENT RANKING
                               ============================================================ */
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
            cursor: pointer;
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
            color: #f59e0b;
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

        /* ============================================================
                               INSIGHTS CARDS (Compact 3)
                               ============================================================ */
        .insights-grid-premium {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .insight-card-premium {
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid rgba(128, 0, 0, 0.06);
            padding: 0.75rem 1rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .insight-card-premium:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .insight-card-premium .insight-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .insight-card-premium .insight-icon.purple {
            background: var(--purple-light);
            color: var(--purple);
        }

        .insight-card-premium .insight-icon.green {
            background: var(--success-light);
            color: var(--success);
        }

        .insight-card-premium .insight-icon.red {
            background: var(--danger-light);
            color: var(--danger);
        }

        .insight-card-premium .insight-content {
            flex: 1;
            min-width: 0;
        }

        .insight-card-premium .insight-content .insight-title {
            font-size: 0.6rem;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .insight-card-premium .insight-content .insight-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-top: 0.05rem;
        }

        .insight-card-premium .insight-content .insight-text .highlight {
            color: var(--primary);
        }

        .insight-card-premium .insight-content .insight-sub {
            font-size: 0.6rem;
            color: var(--gray-400);
            margin-top: 0.05rem;
        }

        /* ============================================================
                               SESSION & CLASSROOM CARDS
                               ============================================================ */
        .session-grid-premium {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
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
        }

        .session-item-premium:hover {
            border-color: rgba(128, 0, 0, 0.1);
            background: white;
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

        .session-item-premium .session-status.improving {
            background: var(--success-light);
            color: var(--success);
        }

        .session-item-premium .session-status.declining {
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

        /* ============================================================
                               QUICK ACTIONS
                               ============================================================ */
        .quick-actions-premium {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .quick-action-premium {
            background: white;
            border-radius: var(--radius-md);
            border: 1px solid rgba(128, 0, 0, 0.06);
            padding: 0.75rem;
            text-align: center;
            transition: var(--transition);
            text-decoration: none;
            color: var(--gray-700);
            box-shadow: var(--shadow-sm);
            cursor: pointer;
        }

        .quick-action-premium:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .quick-action-premium .qa-icon {
            font-size: 1.5rem;
            margin-bottom: 0.15rem;
            display: block;
        }

        .quick-action-premium .qa-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--gray-600);
        }

        .quick-action-premium .qa-count {
            font-size: 0.55rem;
            color: var(--gray-400);
        }

        /* ============================================================
                               RESPONSIVE
                               ============================================================ */
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

            .insights-grid-premium {
                grid-template-columns: 1fr 1fr;
            }

            .session-grid-premium {
                grid-template-columns: 1fr;
            }

            .quick-actions-premium {
                grid-template-columns: repeat(2, 1fr);
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

            .insights-grid-premium {
                grid-template-columns: 1fr;
            }

            .quick-actions-premium {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
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

            .quick-actions-premium {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* ============================================================
                               ANIMATIONS
                               ============================================================ */
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

        .animate-in:nth-child(5) {
            animation-delay: 0.15s;
        }

        .animate-in:nth-child(6) {
            animation-delay: 0.18s;
        }

        .animate-in:nth-child(7) {
            animation-delay: 0.21s;
        }

        .animate-in:nth-child(8) {
            animation-delay: 0.24s;
        }
    </style>

    <!-- ============================================================
                STATS CARDS - COMPACT (8 Cards)
                ============================================================ -->
    <div class="stats-grid-premium">
        <!-- Row 1: 4 Cards -->
        <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap primary"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ number_format($totalStudents ?? 0) }}</div>
            <div class="stat-label">Total Students</div>
            <div class="stat-sub">👨‍🎓 Active enrollments</div>
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
            <div class="stat-value" style="color: var(--danger);">{{ $atRiskStudents ?? 0 }}</div>
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
            <div class="stat-sub">📱 Live QR sessions</div>
        </div>

        <!-- Row 2: 4 Cards -->
        {{-- <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap info"><i class="bi bi-book"></i></div>
            <div class="stat-value">{{ $totalCourses ?? 0 }}</div>
            <div class="stat-label">Active Courses</div>
            <div class="stat-sub">📚 This semester</div>
        </div>

        <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap success"><i class="bi bi-trophy"></i></div>
            <div class="stat-value">{{ $eligibilityRate ?? 0 }}%</div>
            <div class="stat-label">Eligibility Rate</div>
            <div class="stat-sub">✅ Eligible for exams</div>
            <div class="mini-progress">
                <div class="bar success" style="width: {{ $eligibilityRate ?? 0 }}%;"></div>
            </div>
        </div>

        <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap warning"><i class="bi bi-clock-history"></i></div>
            <div class="stat-value">{{ $totalDepartments ?? 0 }}</div>
            <div class="stat-label">Departments</div>
            <div class="stat-sub">🏛️ All faculties</div>
        </div>

        <div class="stat-card-premium animate-in">
            <div class="stat-icon-wrap primary"><i class="bi bi-people"></i></div>
            <div class="stat-value">{{ $totalLecturers ?? 0 }}</div>
            <div class="stat-label">Total Lecturers</div>
            <div class="stat-sub">👨‍🏫 Faculty members</div>
        </div> --}}
    </div>

    <!-- ============================================================
                CHARTS ROW
                ============================================================ -->
    <div class="chart-grid-premium">
        <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-bar-chart-fill" style="color: var(--primary);"></i>
                    Attendance Trend
                    <span class="badge">Last 6 Months</span>
                </span>
                <span class="subtitle">📈 Weekly performance</span>
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
                    <span class="badge">Real-time</span>
                </span>
                <span class="subtitle">🎯 Student risk levels</span>
            </div>
            <div class="card-body-premium">
                <div class="chart-container-premium">
                    <canvas id="riskChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
                INSIGHTS ROW (3 Cards - Compact)
                ============================================================ -->
    <div class="insights-grid-premium">
        <div class="insight-card-premium">
            <div class="insight-icon purple"><i class="bi bi-trophy"></i></div>
            <div class="insight-content">
                <div class="insight-title">🏆 Top Performing Department</div>
                <div class="insight-text">
                    @if (isset($departmentAttendance) && count($departmentAttendance) > 0)
                        <span class="highlight">{{ $departmentAttendance[0]['name'] ?? 'N/A' }}</span>
                        with {{ $departmentAttendance[0]['attendance'] ?? 0 }}% attendance
                    @else
                        No data available
                    @endif
                </div>
                <div class="insight-sub">Leading in academic engagement</div>
            </div>
        </div>

        <div class="insight-card-premium">
            <div class="insight-icon green"><i class="bi bi-arrow-up-circle"></i></div>
            <div class="insight-content">
                <div class="insight-title">📈 Most Improved</div>
                <div class="insight-text">
                    @php
                        $best = isset($departmentAttendance)
                            ? collect($departmentAttendance)->sortByDesc('change')->first()
                            : null;
                    @endphp
                    @if ($best)
                        <span class="highlight">{{ $best['name'] ?? 'N/A' }}</span>
                        ↑ +{{ $best['change'] ?? 0 }}% this month
                    @else
                        No data available
                    @endif
                </div>
                <div class="insight-sub">Significant improvement trend</div>
            </div>
        </div>

        <div class="insight-card-premium">
            <div class="insight-icon red"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="insight-content">
                <div class="insight-title">⚠️ Needs Attention</div>
                <div class="insight-text">
                    @php
                        $worst = isset($departmentAttendance)
                            ? collect($departmentAttendance)->sortBy('attendance')->first()
                            : null;
                    @endphp
                    @if ($worst)
                        <span class="highlight">{{ $worst['name'] ?? 'N/A' }}</span>
                        at {{ $worst['attendance'] ?? 0 }}% attendance
                    @else
                        No data available
                    @endif
                </div>
                <div class="insight-sub">Requires intervention strategy</div>
            </div>
        </div>
    </div>

    <!-- ============================================================
                DEPARTMENT RANKING
                ============================================================ -->
    <div class="chart-grid-premium">
        <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-trophy" style="color: var(--primary);"></i>
                    Department Rankings
                    <span class="badge">{{ isset($departmentAttendance) ? count($departmentAttendance) : 0 }}
                        depts</span>
                </span>
                <span class="subtitle">🏅 Attendance leaderboard</span>
            </div>
            <div class="card-body-premium">
                <div class="dept-ranking-premium">
                    @if (isset($departmentAttendance) && count($departmentAttendance) > 0)
                        @foreach ($departmentAttendance as $index => $dept)
                            @php
                                $rankClass = '';
                                if ($index == 0) {
                                    $rankClass = 'gold';
                                } elseif ($index == 1) {
                                    $rankClass = 'silver';
                                } elseif ($index == 2) {
                                    $rankClass = 'bronze';
                                }

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
                        <p class="text-muted text-center" style="padding: 1rem;">No department data available</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="chart-card-premium">
            <div class="card-header-premium">
                <span class="title">
                    <i class="bi bi-graph-up" style="color: var(--primary);"></i>
                    Performance Insights
                    <span class="badge">AI Analytics</span>
                </span>
                <span class="subtitle">📊 Key metrics summary</span>
            </div>
            <div class="card-body-premium">
                @if (isset($departmentAttendance) && count($departmentAttendance) > 0)
                    @php
                        $highest = collect($departmentAttendance)->sortByDesc('attendance')->first();
                        $lowest = collect($departmentAttendance)->sortBy('attendance')->first();
                        $avg = collect($departmentAttendance)->avg('attendance');
                    @endphp
                    <div style="padding: 0.25rem 0;">
                        <div
                            style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid var(--gray-100);">
                            <span style="font-size: 0.7rem; color: var(--gray-500);">Average Attendance</span>
                            <span
                                style="font-size: 0.8rem; font-weight: 700; color: var(--gray-800);">{{ round($avg) }}%</span>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid var(--gray-100);">
                            <span style="font-size: 0.7rem; color: var(--gray-500);">Highest</span>
                            <span
                                style="font-size: 0.8rem; font-weight: 700; color: var(--success);">{{ $highest['name'] ?? 'N/A' }}
                                ({{ $highest['attendance'] ?? 0 }}%)</span>
                        </div>
                        <div
                            style="display: flex; justify-content: space-between; padding: 0.4rem 0; border-bottom: 1px solid var(--gray-100);">
                            <span style="font-size: 0.7rem; color: var(--gray-500);">Lowest</span>
                            <span
                                style="font-size: 0.8rem; font-weight: 700; color: var(--danger);">{{ $lowest['name'] ?? 'N/A' }}
                                ({{ $lowest['attendance'] ?? 0 }}%)</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.4rem 0;">
                            <span style="font-size: 0.7rem; color: var(--gray-500);">Total Departments</span>
                            <span
                                style="font-size: 0.8rem; font-weight: 700; color: var(--gray-800);">{{ count($departmentAttendance) }}</span>
                        </div>
                    </div>
                @else
                    <p class="text-muted text-center" style="padding: 1rem;">No data available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- ============================================================
                SESSION SUMMARIES & BUSIEST CLASSROOMS
                ============================================================ -->
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
                    <p class="text-muted text-center" style="padding: 0.5rem;">No recent sessions</p>
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
                    <p class="text-muted text-center" style="padding: 0.5rem;">No classroom data available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- ============================================================
                QUICK ACTIONS
                ============================================================ -->
    {{-- <div class="quick-actions-premium">
        <a href="{{ route('admin.users.index') }}" class="quick-action-premium">
            <span class="qa-icon">👨‍🎓</span>
            <span class="qa-label">User Management</span>
            <span class="qa-count">{{ ($totalStudents ?? 0) + ($totalLecturers ?? 0) }} users</span>
        </a>
        <a href="{{ route('admin.enrollments.index') }}" class="quick-action-premium">
            <span class="qa-icon">📋</span>
            <span class="qa-label">Enrollments</span>
            <span class="qa-count">{{ $pendingEnrollments ?? 0 }} pending</span>
        </a>
        <a href="{{ route('admin.departments.index') }}" class="quick-action-premium">
            <span class="qa-icon">🏛️</span>
            <span class="qa-label">Departments</span>
            <span class="qa-count">{{ $totalDepartments ?? 0 }} departments</span>
        </a>
        <a href="{{ route('admin.reports') }}" class="quick-action-premium">
            <span class="qa-icon">📊</span>
            <span class="qa-label">Export Reports</span>
            <span class="qa-count">Download data</span>
        </a>
    </div> --}}

    <!-- ============================================================
                SCRIPTS
                ============================================================ -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ============================================================
                // TREND CHART
                // ============================================================
                var trendLabels =
                    {{ json_encode(isset($trendData) ? array_column($trendData, 'month') : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) }};
                var trendData =
                    {{ json_encode(isset($trendData) ? array_column($trendData, 'attendance') : [65, 68, 70, 72, 75, 78]) }};

                var trendCtx = document.getElementById('trendChart');
                if (trendCtx) {
                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{
                                label: 'Attendance %',
                                data: trendData,
                                borderColor: '#800000',
                                backgroundColor: 'rgba(128, 0, 0, 0.08)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: '#800000',
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
                }

                // ============================================================
                // RISK CHART
                // ============================================================
                var riskData =
                    {{ json_encode(isset($riskDistribution) ? $riskDistribution : ['Low' => 0, 'Medium' => 0, 'High' => 0]) }};

                var riskCtx = document.getElementById('riskChart');
                if (riskCtx) {
                    new Chart(riskCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Low Risk', 'Medium Risk', 'High Risk'],
                            datasets: [{
                                data: [riskData.Low || 0, riskData.Medium || 0, riskData.High || 0],
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
                                            return context.label + ': ' + context.parsed + ' students (' +
                                                percentage + '%)';
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
                }
            });
        </script>
    @endpush
@endsection
