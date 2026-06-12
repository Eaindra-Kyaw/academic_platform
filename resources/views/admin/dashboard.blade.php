@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('role', 'Admin')
@section('page-title', 'University Intelligence Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    <div class="nav-label">Management</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item active">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('admin.users') }}" class="nav-item">
        <i class="bi bi-people"></i><span>User Management</span>
    </a>
    <a href="{{ route('admin.departments.index') }}" class="nav-item">
        <i class="bi bi-building"></i><span>Departments</span>
    </a>
    <a href="{{ route('admin.courses.index') }}" class="nav-item">
        <i class="bi bi-book"></i><span>Course Management</span>
    </a>
    <a href="{{ route('admin.enrollments.index') }}" class="nav-item">
        <i class="bi bi-list-check"></i><span>Enrollments</span>
    </a>
    <div class="nav-label">Analytics</div>
    <a href="#" class="nav-item"><i class="bi bi-calendar"></i><span>Semesters</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up"></i><span>Analytics</span></a>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Reports</span></a>
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
            padding: 1.25rem;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #800000;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .stat-change {
            font-size: 0.7rem;
            margin-top: 0.5rem;
        }

        .stat-change.up {
            color: #10b981;
        }

        .stat-change.down {
            color: #ef4444;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .chart-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            padding: 1rem;
        }

        .chart-title {
            font-weight: 700;
            color: #800000;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        canvas {
            max-height: 250px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .two-col {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div>
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">1,284</div>
                <div class="stat-label">Total Students</div>
                <div class="stat-change up"><i class="bi bi-arrow-up"></i> +124 from last sem</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">48</div>
                <div class="stat-label">Total Lecturers</div>
                <div class="stat-change">Full-time faculty</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">124</div>
                <div class="stat-label">Active Courses</div>
                <div class="stat-change">This semester</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">11</div>
                <div class="stat-label">Departments</div>
                <div class="stat-change">All faculties</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">81%</div>
                <div class="stat-label">University Attendance</div>
                <div class="stat-change down"><i class="bi bi-arrow-down"></i> -2%</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">156</div>
                <div class="stat-label">Students At Risk</div>
                <div class="stat-change down">15% of total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">87%</div>
                <div class="stat-label">Eligibility Rate</div>
                <div class="stat-change">Eligible for exams</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">8</div>
                <div class="stat-label">Active Sessions</div>
                <div class="stat-change">Currently running</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="two-col">
            <div class="chart-card">
                <div class="chart-title"><i class="bi bi-bar-chart"></i> Attendance by Department</div>
                <canvas id="deptChart"></canvas>
            </div>
            <div class="chart-card">
                <div class="chart-title"><i class="bi bi-graph-up"></i> University Attendance Trend</div>
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Department Attendance Chart
        const deptCtx = document.getElementById('deptChart').getContext('2d');
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: ['CS', 'CE', 'ME', 'EP', 'EC', 'MEC', 'CH', 'AE', 'BT', 'AR', 'NT'],
                datasets: [{
                    label: 'Attendance %',
                    data: [88, 85, 82, 79, 76, 81, 84, 86, 75, 72, 70],
                    backgroundColor: '#800000',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Attendance %'
                        }
                    }
                }
            }
        });

        // University Attendance Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'University Attendance %',
                    data: [81.5, 82.0, 80.5, 79.0, 78.5, 81.0],
                    borderColor: '#800000',
                    backgroundColor: 'rgba(128, 0, 0, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#800000',
                    pointBorderColor: 'white',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 70,
                        max: 90,
                        title: {
                            display: true,
                            text: 'Attendance %'
                        }
                    }
                }
            }
        });
    </script>
@endsection
