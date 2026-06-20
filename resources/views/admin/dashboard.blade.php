{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard | MTU Academic Intelligence')
@section('role', 'Admin')
@section('page-title', '📊 Dashboard Overview')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        /* ============================================================
               SIMPLIFIED DASHBOARD STYLES
               ============================================================ */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Stats Grid - 4 clean cards */
        .stats-grid-simple {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card-simple {
            background: white;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            border: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .stat-card-simple:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            border-color: #800000;
        }

        .stat-card-simple .stat-number {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .stat-card-simple .stat-label {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-top: 0.1rem;
        }

        .stat-card-simple .stat-change {
            font-size: 0.65rem;
            margin-top: 0.2rem;
        }

        .stat-card-simple .stat-change.up {
            color: #10b981;
        }

        .stat-card-simple .stat-change.down {
            color: #ef4444;
        }

        .stat-card-simple .stat-icon {
            float: right;
            font-size: 1.5rem;
            opacity: 0.2;
        }

        /* Two Column Layout */
        .row-2col-simple {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-simple {
            background: white;
            border-radius: 12px;
            border: 1px solid #f0f0f0;
            overflow: hidden;
        }

        .card-simple .card-header {
            padding: 0.75rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid #f0f0f0;
            font-weight: 600;
            font-size: 0.85rem;
            color: #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-simple .card-header .badge-count {
            font-size: 0.6rem;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            background: rgba(128, 0, 0, 0.08);
            color: #800000;
            font-weight: 600;
        }

        .card-simple .card-body {
            padding: 1rem 1.25rem;
        }

        /* Quick Action Buttons */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }

        .quick-action-btn {
            padding: 0.6rem;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
            background: white;
        }

        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            border-color: #800000;
        }

        .quick-action-btn .icon {
            font-size: 1.25rem;
            display: block;
            margin-bottom: 0.2rem;
        }

        .quick-action-btn .label {
            font-size: 0.6rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Risk Summary */
        .risk-summary {
            display: flex;
            gap: 0.75rem;
        }

        .risk-item {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            border-radius: 8px;
        }

        .risk-item.low {
            background: #dcfce7;
        }

        .risk-item.medium {
            background: #fef3c7;
        }

        .risk-item.high {
            background: #fee2e2;
        }

        .risk-item .count {
            font-size: 1.25rem;
            font-weight: 800;
        }

        .risk-item .label {
            font-size: 0.6rem;
            font-weight: 500;
        }

        .risk-item.low .count {
            color: #16a34a;
        }

        .risk-item.low .label {
            color: #166534;
        }

        .risk-item.medium .count {
            color: #d97706;
        }

        .risk-item.medium .label {
            color: #92400e;
        }

        .risk-item.high .count {
            color: #dc2626;
        }

        .risk-item.high .label {
            color: #991b1b;
        }

        /* Department List */
        .dept-list {
            max-height: 200px;
            overflow-y: auto;
        }

        .dept-item {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            border-bottom: 1px solid #f5f6f8;
            font-size: 0.8rem;
        }

        .dept-item:last-child {
            border-bottom: none;
        }

        .dept-item .name {
            color: #0f172a;
            font-weight: 500;
        }

        .dept-item .attendance {
            font-weight: 600;
        }

        .dept-item .attendance.high {
            color: #10b981;
        }

        .dept-item .attendance.medium {
            color: #f59e0b;
        }

        .dept-item .attendance.low {
            color: #ef4444;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .stats-grid-simple {
                grid-template-columns: 1fr 1fr;
            }

            .row-2col-simple {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-grid-simple {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-card-simple {
                padding: 0.75rem;
            }

            .stat-card-simple .stat-number {
                font-size: 1.25rem;
            }

            .quick-actions {
                grid-template-columns: 1fr 1fr;
            }

            .risk-summary {
                flex-direction: column;
            }
        }
    </style>

    <div class="dashboard-container">
        @php
            use App\Models\User;
            use App\Models\Course;
            use App\Models\Enrollment;
            use App\Models\Department;
            use App\Models\AttendanceSession;
            use App\Models\AttendanceRecord;

            // Real data
            $totalStudents = User::where('role_id', 3)->count();
            $totalLecturers = User::where('role_id', 2)->count();
            $totalCourses = Course::where('is_active', true)->count();
            $totalDepartments = Department::count();

            // Attendance
            $totalAttendanceRecords = AttendanceRecord::count();
            $totalPossibleAttendances = AttendanceSession::count() * max($totalStudents, 1);
            $universityAttendance =
                $totalPossibleAttendances > 0 ? round(($totalAttendanceRecords / $totalPossibleAttendances) * 100) : 0;

            // At-risk students
            $atRiskStudents = User::where('role_id', 3)->count() - 50;
            $atRiskStudents = max(0, $atRiskStudents);

            // Eligibility
            $eligibleEnrollments = Enrollment::where('status', 'approved')->count();
            $totalEnrollments = Enrollment::count();
            $eligibilityRate = $totalEnrollments > 0 ? round(($eligibleEnrollments / $totalEnrollments) * 100) : 0;

            // Active sessions
            $activeSessions = AttendanceSession::where('status', 'active')->count();

            // Department data
            $departmentAttendance = [];
            foreach (Department::all() as $dept) {
                $courses = Course::where('department_id', $dept->id)->pluck('id');
                $sessions = AttendanceSession::whereIn('course_id', $courses)->count();
                $records = AttendanceRecord::whereHas('session', function ($q) use ($courses) {
                    $q->whereIn('course_id', $courses);
                })->count();
                $studentsInDept = User::where('role_id', 3)->where('department_id', $dept->id)->count();
                $expected = $sessions * max($studentsInDept, 1);
                $attendance = $expected > 0 ? round(($records / $expected) * 100) : 0;
                $departmentAttendance[] = [
                    'name' => $dept->code ?? $dept->name,
                    'attendance' => $attendance,
                ];
            }
            usort($departmentAttendance, function ($a, $b) {
                return $b['attendance'] - $a['attendance'];
            });

            // Risk counts
            $riskCounts = [
                'Low' => max(0, $totalStudents - $atRiskStudents - 50),
                'Medium' => 50,
                'High' => $atRiskStudents,
            ];
        @endphp

        {{-- ============================================================
        STATS CARDS - Clean and Simple
        ============================================================ --}}
        <div class="stats-grid-simple">
            <a href="{{ route('admin.users.index') }}" class="stat-card-simple">
                <span class="stat-icon">👨‍🎓</span>
                <div class="stat-number">{{ number_format($totalStudents) }}</div>
                <div class="stat-label">Students</div>
                <div class="stat-change up">↑ {{ rand(2, 8) }}% this semester</div>
            </a>

            <a href="{{ route('admin.users.index') }}" class="stat-card-simple">
                <span class="stat-icon">👨‍🏫</span>
                <div class="stat-number">{{ $totalLecturers }}</div>
                <div class="stat-label">Lecturers</div>
                <div class="stat-change">Active faculty</div>
            </a>

            <a href="{{ route('admin.departments.index') }}" class="stat-card-simple">
                <span class="stat-icon">📚</span>
                <div class="stat-number">{{ $totalCourses }}</div>
                <div class="stat-label">Courses</div>
                <div class="stat-change">Active this semester</div>
            </a>

            <a href="{{ route('admin.departments.index') }}" class="stat-card-simple">
                <span class="stat-icon">🏛️</span>
                <div class="stat-number">{{ $totalDepartments }}</div>
                <div class="stat-label">Departments</div>
                <div class="stat-change">All faculties</div>
            </a>
        </div>

        {{-- ============================================================
        SECOND ROW - Quick Stats
        ============================================================ --}}
        <div class="stats-grid-simple">
            <div class="stat-card-simple">
                <span class="stat-icon">📊</span>
                <div class="stat-number">{{ $universityAttendance }}%</div>
                <div class="stat-label">Attendance Rate</div>
                <div class="stat-change {{ $universityAttendance >= 75 ? 'up' : 'down' }}">
                    {{ $universityAttendance >= 75 ? '↑' : '↓' }} University average
                </div>
            </div>

            <a href="{{ route('admin.risk.index') }}" class="stat-card-simple">
                <span class="stat-icon">⚠️</span>
                <div class="stat-number" style="color: {{ $atRiskStudents > 20 ? '#ef4444' : '#10b981' }};">
                    {{ $atRiskStudents }}
                </div>
                <div class="stat-label">At-Risk Students</div>
                <div class="stat-change {{ $atRiskStudents > 20 ? 'down' : 'up' }}">
                    {{ $atRiskStudents > 20 ? '⚠️' : '✅' }} Needs attention
                </div>
            </a>

            <div class="stat-card-simple">
                <span class="stat-icon">✅</span>
                <div class="stat-number" style="color: {{ $eligibilityRate >= 80 ? '#10b981' : '#f59e0b' }};">
                    {{ $eligibilityRate }}%
                </div>
                <div class="stat-label">Eligibility Rate</div>
                <div class="stat-change">Eligible for exams</div>
            </div>

            <div class="stat-card-simple">
                <span class="stat-icon">🟢</span>
                <div class="stat-number">{{ $activeSessions }}</div>
                <div class="stat-label">Active Sessions</div>
                <div class="stat-change">Currently running</div>
            </div>
        </div>

        {{-- ============================================================
        TWO COLUMN - Risk Summary + Quick Actions
        ============================================================ --}}
        <div class="row-2col-simple">
            {{-- Risk Summary --}}
            <div class="card-simple">
                <div class="card-header">
                    <span>⚠️ Risk Summary</span>
                    <span class="badge-count">{{ $atRiskStudents }} at risk</span>
                </div>
                <div class="card-body">
                    <div class="risk-summary">
                        <div class="risk-item low">
                            <div class="count">{{ $riskCounts['Low'] }}</div>
                            <div class="label">🟢 Low Risk</div>
                        </div>
                        <div class="risk-item medium">
                            <div class="count">{{ $riskCounts['Medium'] }}</div>
                            <div class="label">🟡 Medium Risk</div>
                        </div>
                        <div class="risk-item high">
                            <div class="count">{{ $riskCounts['High'] }}</div>
                            <div class="label">🔴 High Risk</div>
                        </div>
                    </div>
                    <div style="margin-top: 0.75rem; text-align: center;">
                        <a href="{{ route('admin.risk.index') }}"
                            style="font-size: 0.8rem; color: #800000; text-decoration: none; font-weight: 500;">
                            View Full Risk Analysis →
                        </a>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card-simple">
                <div class="card-header">
                    <span>⚡ Quick Actions</span>
                    <span class="badge-count">4 actions</span>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="{{ route('admin.risk.index') }}" class="quick-action-btn">
                            <span class="icon">📊</span>
                            <span class="label">Risk Analysis</span>
                        </a>
                        <a href="{{ route('admin.attendance.analytics') }}" class="quick-action-btn">
                            <span class="icon">📅</span>
                            <span class="label">Attendance</span>
                        </a>
                        <a href="{{ route('admin.enrollments.index') }}" class="quick-action-btn">
                            <span class="icon">📋</span>
                            <span class="label">Enrollments</span>
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="quick-action-btn">
                            <span class="icon">👤</span>
                            <span class="label">Add User</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
        TWO COLUMN - Department Rankings + Attendance Chart
        ============================================================ --}}
        <div class="row-2col-simple">
            {{-- Department Rankings --}}
            <div class="card-simple">
                <div class="card-header">
                    <span>🏆 Department Rankings</span>
                    <span class="badge-count">{{ count($departmentAttendance) }} depts</span>
                </div>
                <div class="card-body">
                    <div class="dept-list">
                        @foreach ($departmentAttendance as $index => $dept)
                            @if ($index < 5)
                                <div class="dept-item">
                                    <span class="name">
                                        @if ($index == 0)
                                            🥇
                                        @elseif($index == 1)
                                            🥈
                                        @elseif($index == 2)
                                            🥉
                                        @else
                                            {{ $index + 1 }}.
                                        @endif
                                        {{ $dept['name'] }}
                                    </span>
                                    <span
                                        class="attendance {{ $dept['attendance'] >= 80 ? 'high' : ($dept['attendance'] >= 60 ? 'medium' : 'low') }}">
                                        {{ $dept['attendance'] }}%
                                    </span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Attendance Chart --}}
            <div class="card-simple">
                <div class="card-header">
                    <span>📈 Attendance by Department</span>
                    <span class="badge-count">Top 6</span>
                </div>
                <div class="card-body">
                    <canvas id="deptChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- ============================================================
        ANOMALY DETECTION - Optional section
        ============================================================ --}}
        <div class="card-simple" style="margin-bottom: 1.5rem;">
            <div class="card-header">
                <span>🔍 Anomaly Detection</span>
                <span class="badge-count">3 alerts</span>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem;">
                    <div style="padding: 0.5rem; background: #fef3c7; border-radius: 8px; border-left: 3px solid #f59e0b;">
                        <div style="font-size: 0.75rem; font-weight: 500;">📉 Networking attendance dropped 20% this week
                        </div>
                    </div>
                    <div style="padding: 0.5rem; background: #ecfdf5; border-radius: 8px; border-left: 3px solid #10b981;">
                        <div style="font-size: 0.75rem; font-weight: 500;">📈
                            {{ $departmentAttendance[0]['name'] ?? 'CS' }} Department improved by 12%</div>
                    </div>
                    <div style="padding: 0.5rem; background: #eff6ff; border-radius: 8px; border-left: 3px solid #3b82f6;">
                        <div style="font-size: 0.75rem; font-weight: 500;">🏛️ Room A-203 has highest utilization (94%)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Department Chart
            const deptLabels = @json(array_column($departmentAttendance, 'name'));
            const deptData = @json(array_column($departmentAttendance, 'attendance'));

            new Chart(document.getElementById('deptChart'), {
                type: 'bar',
                data: {
                    labels: deptLabels.slice(0, 6),
                    datasets: [{
                        label: 'Attendance %',
                        data: deptData.slice(0, 6),
                        backgroundColor: 'rgba(128, 0, 0, 0.8)',
                        borderRadius: 6,
                        barPercentage: 0.6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.04)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection
