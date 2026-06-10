@extends('layouts.app')

@section('title', 'Admin Dashboard | MTU Academic Intelligence')
@section('role', 'Admin')
@section('page-title', 'University Intelligence Dashboard')
@section('welcome-text', 'Welcome back, Administrator')

@section('sidebar')
    <div class="nav-label">Management</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item active"><i
            class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    <a href="#" class="nav-item"><i class="bi bi-people"></i><span>User Management</span></a>
    <a href="#" class="nav-item"><i class="bi bi-building"></i><span>Departments</span></a>
    <a href="#" class="nav-item"><i class="bi bi-book"></i><span>Course Management</span></a>
    <div class="nav-label">Analytics</div>
    <a href="#" class="nav-item"><i class="bi bi-calendar"></i><span>Semesters</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up"></i><span>Analytics</span></a>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Reports</span></a>
@endsection

@section('content')
    <style>
        .stats-grid-modern {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card-modern {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            border-left: 4px solid var(--secondary);
            box-shadow: var(--shadow-sm);
        }

        .stat-value-modern {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-800);
        }

        .row-2col {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .insight-card {
            background: white;
            border-radius: 1rem;
            padding: 1rem;
            border-left: 4px solid;
            margin-bottom: 0.75rem;
        }

        .insight-warning {
            border-left-color: var(--warning);
            background: #fffbeb;
        }

        .insight-info {
            border-left-color: var(--info);
            background: #eff6ff;
        }

        .anomaly-card {
            padding: 0.75rem;
            background: #fef9c3;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
            border-left: 3px solid var(--warning);
        }

        .rank-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .rank-1 {
            background: rgba(244, 196, 48, 0.2);
            padding: 0.75rem;
            border-radius: 0.75rem;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 1024px) {
            .stats-grid-modern {
                grid-template-columns: repeat(2, 1fr);
            }

            .row-2col {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="stats-grid-modern">
        <div class="stat-card-modern">
            <div class="stat-value-modern">1,284</div>
            <div class="stat-label">Total Students</div><small>+124 from last sem</small>
        </div>
        <div class="stat-card-modern">
            <div class="stat-value-modern">48</div>
            <div class="stat-label">Total Lecturers</div><small>Full-time faculty</small>
        </div>
        <div class="stat-card-modern">
            <div class="stat-value-modern">124</div>
            <div class="stat-label">Active Courses</div><small>This semester</small>
        </div>
        <div class="stat-card-modern">
            <div class="stat-value-modern">8</div>
            <div class="stat-label">Departments</div><small>All faculties</small>
        </div>
    </div>

    <div class="stats-grid-modern">
        <div class="stat-card-modern">
            <div class="stat-value-modern">81%</div>
            <div class="stat-label">University Attendance</div><small style="color: var(--danger);">↓ 2% from last
                month</small>
        </div>
        <div class="stat-card-modern">
            <div class="stat-value-modern" style="color: var(--danger);">156</div>
            <div class="stat-label">Students At Risk</div><small>15% of total</small>
        </div>
        <div class="stat-card-modern">
            <div class="stat-value-modern" style="color: var(--success);">87%</div>
            <div class="stat-label">Eligibility Rate</div><small>Eligible for exams</small>
        </div>
        <div class="stat-card-modern">
            <div class="stat-value-modern">8</div>
            <div class="stat-label">Active Sessions</div><small>Currently running</small>
        </div>
    </div>

    <div class="row-2col">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-bar-chart"></i> Attendance by Department</h4>
            </div>
            <div class="card-body"><canvas id="deptChart" height="200"></canvas></div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-graph-up"></i> University Attendance Trend</h4>
            </div>
            <div class="card-body"><canvas id="trendChart" height="200"></canvas></div>
        </div>
    </div>

    <div class="row-2col">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-trophy"></i> Department Rankings</h4>
            </div>
            <div class="card-body">
                <div class="rank-1"><strong>🥇 1. Computer Engineering</strong> <span class="float-end">89% <span
                            class="text-success">▲ +5%</span></span></div>
                <div class="rank-item"><strong>2. Electronic Engineering</strong> <span class="float-end">84% <span
                            class="text-danger">▼ -2%</span></span></div>
                <div class="rank-item"><strong>3. Mechanical Engineering</strong> <span class="float-end">81% <span
                            class="text-secondary">→ 0%</span></span></div>
                <div class="rank-item"><strong>4. Civil Engineering</strong> <span class="float-end">76% <span
                            class="text-success">▲ +3%</span></span></div>
                <div class="rank-item"><strong>5. Agricultural Engineering</strong> <span class="float-end">72% <span
                            class="text-danger">▼ -4%</span></span></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-pie-chart"></i> Risk Distribution</h4>
            </div>
            <div class="card-body"><canvas id="riskChart" height="180"></canvas></div>
        </div>
    </div>

    <div class="row-2col">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-lightbulb"></i> University Intelligence Insights</h4>
            </div>
            <div class="card-body">
                <div class="insight-card insight-warning"><i class="bi bi-trophy"></i> <strong>🏆 Top Department:</strong>
                    Computer Engineering (89%)</div>
                <div class="insight-card insight-success"><i class="bi bi-arrow-up"></i> <strong>📈 Most
                        Improved:</strong> Civil Engineering (+12%)</div>
                <div class="insight-card insight-warning"><i class="bi bi-arrow-down"></i> <strong>📉 Needs
                        Attention:</strong> Agricultural Engineering (62% eligibility)</div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-exclamation-triangle"></i> Anomaly Detection</h4>
            </div>
            <div class="card-body">
                <div class="anomaly-card"><i class="bi bi-graph-down"></i> Networking attendance dropped 20% this week
                </div>
                <div class="anomaly-card"><i class="bi bi-arrow-up"></i> Civil Engineering improved by 12% this month
                </div>
                <div class="anomaly-card"><i class="bi bi-building"></i> Room A-203 has highest utilization (94%)</div>
                <div class="anomaly-card"><i class="bi bi-clock"></i> Monday 8 AM sessions have 23% lower attendance</div>
            </div>
        </div>
    </div>

    <div class="row-2col">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-building"></i> Busiest Classrooms</h4>
            </div>
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
                        <div class="progress-bar success" style="width:82%"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-file-text"></i> Smart Session Summaries</h4>
            </div>
            <div class="card-body">
                <div class="p-2 bg-light rounded mb-2"><strong>📊 Database Systems:</strong> 89% | Late: 4 | Improving ▲
                </div>
                <div class="p-2 bg-light rounded mb-2"><strong>📊 Networking:</strong> 67% | Late: 2 | Declining ▼</div>
                <div class="p-2 bg-light rounded"><strong>📊 Operating Systems:</strong> 91% | Late: 1 | Stable →</div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">
            <h4><i class="bi bi-people"></i> User Account Management</h4><button class="btn btn-primary btn-sm"
                onclick="alert('Add user form')">+ Add User</button>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Admin User</td>
                        <td>admin1@mtu.edu.mm</td>
                        <td><span class="badge badge-success">Admin</span></td>
                        <td>Active</td>
                        <td><button class="btn btn-outline btn-sm">Edit</button></td>
                    </tr>
                    <tr>
                        <td>Dr. Phyo Thu Zar Tun</td>
                        <td>phyothuzartun@mtu.edu.mm</td>
                        <td><span class="badge badge-info">Lecturer</span></td>
                        <td>Active</td>
                        <td><button class="btn btn-outline btn-sm">Edit</button></td>
                    </tr>
                    <tr>
                        <td>Eaindra Kyaw</td>
                        <td>eaindrakyaw@mtu.edu.mm</td>
                        <td><span class="badge badge-warning">Student</span></td>
                        <td>Active</td>
                        <td><button class="btn btn-outline btn-sm">Edit</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4><i class="bi bi-download"></i> Export Reports</h4>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(5,1fr); gap: 1rem;">
                <button class="btn btn-outline" onclick="alert('Attendance Report')"><i class="bi bi-file-pdf"></i>
                    Attendance</button>
                <button class="btn btn-outline" onclick="alert('Eligibility Report')"><i class="bi bi-file-pdf"></i>
                    Eligibility</button>
                <button class="btn btn-outline" onclick="alert('Risk Analysis')"><i class="bi bi-file-pdf"></i> Risk
                    Analysis</button>
                <button class="btn btn-outline" onclick="alert('Dept Comparison')"><i class="bi bi-file-pdf"></i> Dept
                    Comparison</button>
                <button class="btn btn-outline" onclick="alert('Semester Summary')"><i class="bi bi-file-pdf"></i>
                    Semester Summary</button>
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
