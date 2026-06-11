@extends('layouts.app')

@section('title', 'Admin Dashboard | MTU Academic Intelligence')
@section('role', 'Admin')
@section('page-title', 'University Intelligence Dashboard')
@section('welcome-text', 'Welcome back, Administrator')

@section('sidebar')
    <div class="nav-label">Management</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item active"><i
            class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="nav-item"><i class="bi bi-people"></i><span>User Management</span></a>
    <a href="{{ route('admin.departments.index') }}" class="nav-item"><i
            class="bi bi-building"></i><span>Departments</span></a>
    <a href="#" class="nav-item"><i class="bi bi-book"></i><span>Course Management</span></a>
    <div class="nav-label">Analytics</div>
    <a href="#" class="nav-item"><i class="bi bi-calendar"></i><span>Semesters</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up"></i><span>Analytics</span></a>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Reports</span></a>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto;">
        <style>
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .stat-card {
                background: white;
                border-radius: 1rem;
                padding: 1.25rem;
                border-left: 4px solid #f4c430;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            .stat-value {
                font-size: 2rem;
                font-weight: 800;
                color: #1f2937;
            }

            .stat-label {
                color: #6b7280;
                font-size: 0.8rem;
                margin-top: 0.25rem;
            }

            .row-2col {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .card {
                background: white;
                border-radius: 1rem;
                border: 1px solid #e5e7eb;
                overflow: hidden;
            }

            .card-header {
                padding: 1rem 1.25rem;
                background: #f9fafb;
                border-bottom: 1px solid #e5e7eb;
                font-weight: 700;
                color: #800000;
            }

            .card-body {
                padding: 1.25rem;
            }

            .insight-card {
                padding: 0.75rem;
                border-radius: 0.5rem;
                margin-bottom: 0.5rem;
                border-left: 3px solid;
            }

            .insight-warning {
                background: #fffbeb;
                border-left-color: #f59e0b;
            }

            .insight-success {
                background: #ecfdf5;
                border-left-color: #10b981;
            }

            .anomaly-card {
                padding: 0.75rem;
                background: #fef9c3;
                border-radius: 0.5rem;
                margin-bottom: 0.5rem;
                border-left: 3px solid #f59e0b;
            }

            .rank-1 {
                background: rgba(244, 196, 48, 0.2);
                padding: 0.75rem;
                border-radius: 0.75rem;
                margin-bottom: 0.5rem;
            }

            .rank-item {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .progress {
                height: 6px;
                background: #e5e7eb;
                border-radius: 10px;
                overflow: hidden;
            }

            .progress-bar {
                height: 100%;
                border-radius: 10px;
                background: #800000;
            }

            @media (max-width: 768px) {
                .stats-grid {
                    grid-template-columns: repeat(2, 1fr);
                }

                .row-2col {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <!-- Stats Row 1 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">1,284</div>
                <div class="stat-label">Total Students</div><small>+124 from last sem</small>
            </div>
            <div class="stat-card">
                <div class="stat-value">48</div>
                <div class="stat-label">Total Lecturers</div><small>Full-time faculty</small>
            </div>
            <div class="stat-card">
                <div class="stat-value">124</div>
                <div class="stat-label">Active Courses</div><small>This semester</small>
            </div>
            <div class="stat-card">
                <div class="stat-value">8</div>
                <div class="stat-label">Departments</div><small>All faculties</small>
            </div>
        </div>

        <!-- Stats Row 2 -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">81%</div>
                <div class="stat-label">University Attendance</div><small style="color:#dc2626;">↓ 2%</small>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color:#dc2626;">156</div>
                <div class="stat-label">Students At Risk</div><small>15% of total</small>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color:#10b981;">87%</div>
                <div class="stat-label">Eligibility Rate</div><small>Eligible for exams</small>
            </div>
            <div class="stat-card">
                <div class="stat-value">8</div>
                <div class="stat-label">Active Sessions</div><small>Currently running</small>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row-2col">
            <div class="card">
                <div class="card-header"><i class="bi bi-bar-chart"></i> Attendance by Department</div>
                <div class="card-body"><canvas id="deptChart" height="200"></canvas></div>
            </div>
            <div class="card">
                <div class="card-header"><i class="bi bi-graph-up"></i> University Attendance Trend</div>
                <div class="card-body"><canvas id="trendChart" height="200"></canvas></div>
            </div>
        </div>

        <!-- Rankings + Risk -->
        <div class="row-2col">
            <div class="card">
                <div class="card-header"><i class="bi bi-trophy"></i> Department Rankings</div>
                <div class="card-body">
                    <div class="rank-1"><strong>🥇 1. Computer Engineering</strong> <span class="float-end">89% ▲ +5%</span>
                    </div>
                    <div class="rank-item"><strong>2. Electronic Engineering</strong> <span class="float-end">84% ▼
                            -2%</span></div>
                    <div class="rank-item"><strong>3. Mechanical Engineering</strong> <span class="float-end">81% →
                            0%</span></div>
                    <div class="rank-item"><strong>4. Civil Engineering</strong> <span class="float-end">76% ▲ +3%</span>
                    </div>
                    <div class="rank-item"><strong>5. Agricultural Engineering</strong> <span class="float-end">72% ▼
                            -4%</span></div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><i class="bi bi-pie-chart"></i> Risk Distribution</div>
                <div class="card-body"><canvas id="riskChart" height="180"></canvas></div>
            </div>
        </div>

        <!-- Insights + Anomaly -->
        <div class="row-2col">
            <div class="card">
                <div class="card-header"><i class="bi bi-lightbulb"></i> University Intelligence Insights</div>
                <div class="card-body">
                    <div class="insight-card insight-warning"><i class="bi bi-trophy"></i> <strong>🏆 Top
                            Department:</strong> Computer Engineering (89%)</div>
                    <div class="insight-card insight-success"><i class="bi bi-arrow-up"></i> <strong>📈 Most
                            Improved:</strong> Civil Engineering (+12%)</div>
                    <div class="insight-card insight-warning"><i class="bi bi-arrow-down"></i> <strong>📉 Needs
                            Attention:</strong> Agricultural Engineering (62% eligibility)</div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><i class="bi bi-exclamation-triangle"></i> Anomaly Detection</div>
                <div class="card-body">
                    <div class="anomaly-card"><i class="bi bi-graph-down"></i> Networking attendance dropped 20% this week
                    </div>
                    <div class="anomaly-card"><i class="bi bi-arrow-up"></i> Civil Engineering improved by 12% this month
                    </div>
                    <div class="anomaly-card"><i class="bi bi-building"></i> Room A-203 has highest utilization (94%)
                    </div>
                </div>
            </div>
        </div>

        <!-- Busiest Classrooms + Session Summaries -->
        <div class="row-2col">
            <div class="card">
                <div class="card-header"><i class="bi bi-building"></i> Busiest Classrooms</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Room A-203</span><span>94%</span>
                        <div class="progress w-50">
                            <div class="progress-bar" style="width:94%"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2"><span>Room B-101</span><span>87%</span>
                        <div class="progress w-50">
                            <div class="progress-bar" style="width:87%"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2"><span>Lab 301</span><span>82%</span>
                        <div class="progress w-50">
                            <div class="progress-bar success" style="width:82%; background:#10b981;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><i class="bi bi-file-text"></i> Smart Session Summaries</div>
                <div class="card-body">
                    <div class="p-2 bg-light rounded mb-2"><strong>📊 Database Systems:</strong> 89% | Late: 4 | Improving
                        ▲</div>
                    <div class="p-2 bg-light rounded mb-2"><strong>📊 Networking:</strong> 67% | Late: 2 | Declining ▼
                    </div>
                    <div class="p-2 bg-light rounded"><strong>📊 Operating Systems:</strong> 91% | Late: 1 | Stable →</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            new Chart(document.getElementById('deptChart'), {
                type: 'bar',
                data: {
                    labels: ['CS', 'EC', 'ME', 'CE', 'Agri'],
                    datasets: [{
                        label: 'Attendance %',
                        data: [89, 84, 81, 76, 72],
                        backgroundColor: '#800000'
                    }]
                },
                options: {
                    responsive: true
                }
            });
            new Chart(document.getElementById('trendChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'University Attendance',
                        data: [78, 79, 80, 81, 80, 81],
                        borderColor: '#800000',
                        fill: true
                    }]
                },
                options: {
                    responsive: true
                }
            });
            new Chart(document.getElementById('riskChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Low Risk', 'Medium Risk', 'High Risk'],
                    datasets: [{
                        data: [850, 278, 156],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
                    }]
                },
                options: {
                    responsive: true
                }
            });
        </script>
    @endpush
@endsection
