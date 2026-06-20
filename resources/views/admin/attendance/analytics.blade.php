{{-- resources/views/admin/attendance/analytics.blade.php --}}
@extends('layouts.app')

@section('title', 'Attendance Analytics')
@section('role', 'Admin')
@section('page-title', '📊 Attendance Analytics')
@section('welcome-text', 'Monitor university-wide attendance performance')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            text-align: center;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-number.green {
            color: #10b981;
        }

        .stat-number.yellow {
            color: #f59e0b;
        }

        .stat-number.red {
            color: #ef4444;
        }

        .stat-number.blue {
            color: #3b82f6;
        }

        .stat-number.purple {
            color: #8b5cf6;
        }

        .stat-label {
            font-size: 0.65rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .filter-bar {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
            align-items: center;
        }

        .filter-bar .filter-group {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .filter-bar .filter-group label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #4b5563;
        }

        .filter-bar .filter-group select,
        .filter-bar .filter-group input {
            padding: 0.3rem 0.6rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            background: #f9fafb;
        }

        .filter-bar .filter-group select:focus,
        .filter-bar .filter-group input:focus {
            outline: none;
            border-color: #800000;
        }

        .filter-bar .btn-filter {
            background: #800000;
            color: white;
            border: none;
            padding: 0.3rem 1rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-bar .btn-filter:hover {
            background: #5f0000;
        }

        .filter-bar .btn-reset {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            padding: 0.3rem 1rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .filter-bar .btn-reset:hover {
            background: #e5e7eb;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .chart-card .card-header {
            padding: 0.75rem 1rem;
            background: #fafafa;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            font-size: 0.85rem;
            color: #1f2937;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-card .card-body {
            padding: 1rem;
        }

        .chart-container {
            position: relative;
            height: 250px;
        }

        .ranking-table {
            width: 100%;
            font-size: 0.8rem;
            border-collapse: collapse;
        }

        .ranking-table th {
            text-align: left;
            padding: 0.4rem 0.5rem;
            font-size: 0.6rem;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }

        .ranking-table td {
            padding: 0.3rem 0.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .ranking-table tr:last-child td {
            border-bottom: none;
        }

        .ranking-table .rank-num {
            font-weight: 700;
            color: #9ca3af;
            font-size: 0.7rem;
            width: 24px;
            text-align: center;
        }

        .ranking-table .rank-1 {
            color: #f59e0b;
        }

        .ranking-table .rank-2 {
            color: #9ca3af;
        }

        .ranking-table .rank-3 {
            color: #d97706;
        }

        .attendance-bar {
            height: 6px;
            background: #f3f4f6;
            border-radius: 3px;
            overflow: hidden;
            width: 80px;
            display: inline-block;
        }

        .attendance-bar .fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.5s ease;
        }

        .attendance-bar .fill.high {
            background: #10b981;
        }

        .attendance-bar .fill.medium {
            background: #f59e0b;
        }

        .attendance-bar .fill.low {
            background: #ef4444;
        }

        .dept-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .dept-card {
            background: #fafafa;
            border-radius: 0.5rem;
            padding: 0.6rem 0.8rem;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .dept-card .dept-code {
            font-weight: 600;
            font-size: 0.75rem;
            color: #1f2937;
        }

        .dept-card .dept-attendance {
            font-size: 1.1rem;
            font-weight: 700;
            margin: 0.1rem 0;
        }

        .risk-badge {
            display: inline-block;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .risk-high {
            background: #fee2e2;
            color: #991b1b;
        }

        .risk-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .risk-low {
            background: #dcfce7;
            color: #166534;
        }

        .risk-table {
            width: 100%;
            font-size: 0.8rem;
            border-collapse: collapse;
        }

        .risk-table th {
            text-align: left;
            padding: 0.3rem 0.5rem;
            font-size: 0.6rem;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }

        .risk-table td {
            padding: 0.3rem 0.5rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .risk-table tr:last-child td {
            border-bottom: none;
        }

        @media (max-width: 1024px) {
            .chart-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar .filter-group {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar .filter-group select,
            .filter-bar .filter-group input {
                width: 100%;
            }

            .dept-card-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-card {
                padding: 0.75rem;
            }

            .stat-number {
                font-size: 1.2rem;
            }

            .dept-card-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number blue">{{ $stats['total_sessions'] ?? 0 }}</div>
            <div class="stat-label">📋 Total Sessions</div>
        </div>
        <div class="stat-card">
            <div class="stat-number purple">{{ $stats['total_records'] ?? 0 }}</div>
            <div class="stat-label">📊 Total Records</div>
        </div>
        <div class="stat-card">
            <div class="stat-number green">{{ $stats['avg_attendance'] ?? 0 }}%</div>
            <div class="stat-label">📈 Avg Attendance</div>
        </div>
        <div class="stat-card">
            <div class="stat-number yellow">{{ $stats['present_count'] ?? 0 }}</div>
            <div class="stat-label">✅ Present</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form class="filter-bar" method="GET" action="{{ route('admin.attendance.analytics') }}">
        <div class="filter-group">
            <label>Department</label>
            <select name="department_id">
                <option value="">All Departments</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Course</label>
            <select name="course_id">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Year</label>
            <select name="year">
                <option value="">All Years</option>
                @for ($i = 1; $i <= 6; $i++)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                        {{ $yearLabels[$i] ?? $i . 'th Year' }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="filter-group">
            <label>Date Range</label>
            <select name="date_range">
                <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ $dateRange == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_semester" {{ $dateRange == 'this_semester' ? 'selected' : '' }}>This Semester</option>
            </select>
        </div>

        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn-filter">
                <i class="bi bi-funnel"></i> Apply
            </button>
            <a href="{{ route('admin.attendance.analytics') }}" class="btn-reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>
    </form>

    {{-- Charts --}}
    <div class="chart-grid">
        {{-- Weekly Trend Chart --}}
        <div class="chart-card">
            <div class="card-header">
                <span>📈 Weekly Attendance Trend</span>
                <span style="font-size:0.65rem; color:#6b7280;">Last 12 weeks</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Department Ranking --}}
        <div class="chart-card">
            <div class="card-header">
                <span>🏛️ Department Attendance</span>
                <span style="font-size:0.65rem; color:#6b7280;">Ranking</span>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                @if (count($departmentAttendance) > 0)
                    <div class="dept-card-grid">
                        @foreach ($departmentAttendance as $dept)
                            @php
                                $color =
                                    $dept['attendance'] >= 75
                                        ? '#10b981'
                                        : ($dept['attendance'] >= 60
                                            ? '#f59e0b'
                                            : '#ef4444');
                            @endphp
                            <div class="dept-card">
                                <div class="dept-code">{{ $dept['name'] }}</div>
                                <div class="dept-attendance" style="color:{{ $color }};">
                                    {{ $dept['attendance'] }}%
                                </div>
                                <div style="font-size:0.55rem; color:#9ca3af;">
                                    {{ $dept['total_students'] }} students
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:1rem; color:#9ca3af;">
                        No department data available
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Course Ranking Table --}}
    <div class="chart-card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span>📚 Course Attendance Ranking</span>
            <span style="font-size:0.65rem; color:#6b7280;">Top 20 courses</span>
        </div>
        <div class="card-body" style="padding:0;">
            @if (count($courseRanking) > 0)
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th style="width:30px;">#</th>
                            <th>Course</th>
                            <th>Department</th>
                            <th style="text-align:center;">Students</th>
                            <th style="text-align:center;">Sessions</th>
                            <th style="text-align:center;">Attendance</th>
                            <th style="text-align:center;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courseRanking as $index => $course)
                            @php
                                $rankClass = '';
                                if ($index == 0) {
                                    $rankClass = 'rank-1';
                                } elseif ($index == 1) {
                                    $rankClass = 'rank-2';
                                } elseif ($index == 2) {
                                    $rankClass = 'rank-3';
                                }

                                $barClass =
                                    $course['attendance'] >= 75
                                        ? 'high'
                                        : ($course['attendance'] >= 60
                                            ? 'medium'
                                            : 'low');
                            @endphp
                            <tr>
                                <td class="rank-num {{ $rankClass }}">{{ $index + 1 }}</td>
                                <td>
                                    <div style="font-weight:600; font-size:0.8rem; color:#1f2937;">
                                        {{ $course['course_code'] }}
                                    </div>
                                    <div style="font-size:0.65rem; color:#6b7280;">
                                        {{ Str::limit($course['course_name'], 25) }}
                                    </div>
                                </td>
                                <td style="font-size:0.7rem; color:#6b7280;">{{ $course['department'] }}</td>
                                <td style="text-align:center; font-size:0.75rem;">{{ $course['students'] }}</td>
                                <td style="text-align:center; font-size:0.75rem;">{{ $course['sessions'] }}</td>
                                <td style="text-align:center; font-weight:600; font-size:0.85rem;">
                                    {{ $course['attendance'] }}%
                                </td>
                                <td>
                                    <div class="attendance-bar">
                                        <div class="fill {{ $barClass }}"
                                            style="width: {{ $course['attendance'] }}%;"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align:center; padding:2rem; color:#9ca3af;">
                    No course data available
                </div>
            @endif
        </div>
    </div>

    {{-- At-Risk Students --}}
    <div class="chart-card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span>⚠️ Students At Risk</span>
            <span style="font-size:0.65rem; color:#6b7280;">
                {{ count($atRiskStudents) }} students at risk
            </span>
        </div>
        <div class="card-body" style="padding:0;">
            @if (count($atRiskStudents) > 0)
                <table class="risk-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Department</th>
                            <th>Year</th>
                            <th style="text-align:center;">Attendance</th>
                            <th style="text-align:center;">Risk Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($atRiskStudents as $student)
                            <tr>
                                <td>
                                    <div style="font-weight:600; font-size:0.8rem; color:#1f2937;">
                                        {{ $student['student']->name }}
                                    </div>
                                    <div style="font-size:0.65rem; color:#6b7280;">
                                        {{ $student['student']->email }}
                                    </div>
                                </td>
                                <td style="font-size:0.75rem; color:#6b7280;">{{ $student['department'] }}</td>
                                <td style="font-size:0.75rem; color:#6b7280;">{{ $student['year'] }}</td>
                                <td style="text-align:center; font-weight:600; font-size:0.85rem;">
                                    {{ $student['attendance'] }}%
                                </td>
                                <td style="text-align:center;">
                                    <span class="risk-badge risk-{{ strtolower($student['risk_level']) }}">
                                        {{ $student['risk_level'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div style="text-align:center; padding:2rem; color:#9ca3af;">
                    <i class="bi bi-check-circle"
                        style="font-size:2rem; color:#10b981; display:block; margin-bottom:0.5rem;"></i>
                    No students at risk! All students have good attendance.
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Weekly Chart
            const weeklyData = @json($weeklyTrend);
            const ctx = document.getElementById('weeklyChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: weeklyData.map(item => item.label),
                    datasets: [{
                        label: 'Attendance %',
                        data: weeklyData.map(item => item.attendance),
                        borderColor: '#800000',
                        backgroundColor: 'rgba(128, 0, 0, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#800000',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 4,
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
                                color: 'rgba(0,0,0,0.05)'
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
        });
    </script>

@endsection
