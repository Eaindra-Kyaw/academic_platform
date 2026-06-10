@extends('layouts.app')

@section('title', 'Student Dashboard | MTU Academic Intelligence')
@section('role', 'Student')
@section('page-title', 'Student Academic Intelligence Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('student.dashboard') }}" class="nav-item active"><i
            class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    <a href="{{ route('student.scan') }}" class="nav-item"><i class="bi bi-qr-code-scan"></i><span>Scan Attendance</span></a>
    <a href="#" class="nav-item"><i class="bi bi-calendar-week"></i><span>Timetable</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up-arrow"></i><span>Progress</span></a>
    <a href="#" class="nav-item"><i class="bi bi-lightbulb"></i><span>Recommendations</span></a>
    <div class="nav-label">Support</div>
    <a href="#" class="nav-item"><i class="bi bi-robot"></i><span>Uni Bot</span></a>
    <a href="#" class="nav-item"><i class="bi bi-bell"></i><span>Notifications</span></a>
@endsection

@section('content')
    <style>
        .status-panel {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .hero-card,
        .metric-card,
        .card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .hero-card {
            padding: 1.5rem;
            position: relative;
        }

        .hero-card:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #800000, #f4c430);
        }

        .hero-grid {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .score-ring {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            background: conic-gradient(#800000 0deg 309.6deg, #e5e7eb 309.6deg 360deg);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .score-ring-inner {
            width: 110px;
            height: 110px;
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .score-ring-inner strong {
            font-size: 2rem;
            font-weight: 800;
            color: #800000;
        }

        .metric-card {
            padding: 1rem;
            text-align: center;
        }

        .metric-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #800000;
        }

        .progress {
            height: 6px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-bar {
            height: 100%;
            border-radius: 10px;
        }

        .progress-bar.success {
            background: #10b981;
        }

        .progress-bar.warning {
            background: #f59e0b;
        }

        .card-header {
            padding: 0.8rem 1rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            color: #800000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body {
            padding: 1rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef9c3;
            color: #854d0e;
        }

        .btn {
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #800000;
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #e5e7eb;
            color: #374151;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.8rem;
        }

        th {
            background: #f9fafb;
            font-weight: 600;
        }

        .recommendation-item {
            padding: 0.8rem;
            border-radius: 0.5rem;
            margin-bottom: 0.8rem;
            border-left: 3px solid;
        }

        .recommendation-critical {
            background: #fef2f2;
            border-left-color: #ef4444;
        }

        .recommendation-warning {
            background: #fffbeb;
            border-left-color: #f59e0b;
        }

        .recommendation-success {
            background: #ecfdf5;
            border-left-color: #10b981;
        }

        @media (max-width: 768px) {
            .status-panel {
                grid-template-columns: 1fr;
            }

            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }

            .hero-grid {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

    <!-- Status Panel -->
    <div class="status-panel">
        <div class="hero-card">
            <div class="hero-grid">
                <div class="score-ring">
                    <div class="score-ring-inner"><strong>86</strong><span style="font-size: 0.7rem;">AHS</span></div>
                </div>
                <div class="hero-content">
                    <span class="badge badge-success"><i class="bi bi-check-circle"></i> Stable Standing</span>
                    <h2>Academic Health Score: 86/100</h2>
                    <p style="color: #6b7280; font-size: 0.8rem;">Strong overall engagement, with Networking flagged for
                        recovery.</p>
                    <a href="{{ route('student.scan') }}" class="btn btn-primary"><i class="bi bi-camera"></i> Mark
                        Attendance</a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div style="display: flex; gap: 0.8rem; margin-bottom: 1rem;">
                    <div
                        style="width: 35px; height: 35px; background: #fff3f3; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-clipboard-check" style="color: #800000;"></i></div>
                    <div><strong>Exam Eligibility</strong><br><span style="color: #6b7280;">Eligible overall, with warning
                            in Networking</span></div>
                </div>
                <div style="display: flex; gap: 0.8rem;">
                    <div
                        style="width: 35px; height: 35px; background: #fff3f3; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-shield-exclamation" style="color: #800000;"></i></div>
                    <div><strong>Risk Level</strong><br><span style="color: #6b7280;">Medium Risk | Risk Score:
                            42/100</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-value">82%</div>
            <div class="progress">
                <div class="progress-bar success" style="width:82%"></div>
            </div><span>Overall Attendance</span>
        </div>
        <div class="metric-card">
            <div class="metric-value">7.5</div><span>Roll Call Mark</span>
            <div style="font-size: 0.6rem; color: #6b7280;">/10</div>
        </div>
        <div class="metric-card">
            <div class="metric-value">74%</div>
            <div class="progress">
                <div class="progress-bar warning" style="width:74%"></div>
            </div><span>Semester Forecast</span>
        </div>
        <div class="metric-card">
            <div class="metric-value">12/45</div><span>Class Rank</span>
            <div style="font-size: 0.6rem; color: #10b981;">Top 27%</div>
        </div>
    </div>

    <!-- Recovery Status + Sessions Needed -->
    <div style="background: white; border-radius: 12px; padding: 15px; margin-bottom: 1.5rem; border: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div>
                <h4 style="color: #800000; margin-bottom: 5px;">Recovery Status</h4>
                <div style="font-size: 28px; font-weight: 800; color: #10b981;">Recovering</div>
                <div style="color: #666;">↑ +8 points this month</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 28px; font-weight: 800; color: #800000;">2</div>
                <div style="color: #666;">Sessions Needed</div>
                <small>To Reach 75% Eligibility</small>
            </div>
            <div>
                <div style="font-size: 12px; color: #666;">Recovery Categories</div>
                <div style="display: flex; gap: 8px; margin-top: 5px;">
                    <span
                        style="background: #10b981; color: white; padding: 2px 8px; border-radius: 20px;">Recovering</span>
                    <span style="background: #e5e7eb; color: #666; padding: 2px 8px; border-radius: 20px;">Stable</span>
                    <span style="background: #e5e7eb; color: #666; padding: 2px 8px; border-radius: 20px;">Declining</span>
                    <span style="background: #e5e7eb; color: #666; padding: 2px 8px; border-radius: 20px;">Critical</span>
                </div>
            </div>
        </div>
        <div style="margin-top: 10px; height: 6px; background: #e5e7eb; border-radius: 10px;">
            <div style="width: 75%; height: 100%; background: #10b981; border-radius: 10px;"></div>
        </div>
    </div>

    <!-- AHS Breakdown + Risk Analysis -->
    <div class="grid-2">
        <div class="card">
            <div class="card-header">Academic Health Score Breakdown</div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(4,1fr); gap: 0.5rem; text-align: center;">
                    <div><strong>40%</strong>
                        <div style="font-size:0.7rem;">Attendance</div>
                        <div class="progress mt-1">
                            <div class="progress-bar success" style="width:82%"></div>
                        </div>
                    </div>
                    <div><strong>25%</strong>
                        <div style="font-size:0.7rem;">Roll Call</div>
                        <div class="progress mt-1">
                            <div class="progress-bar success" style="width:75%"></div>
                        </div>
                    </div>
                    <div><strong>20%</strong>
                        <div style="font-size:0.7rem;">Streak</div>
                        <div class="progress mt-1">
                            <div class="progress-bar success" style="width:80%"></div>
                        </div>
                    </div>
                    <div><strong>15%</strong>
                        <div style="font-size:0.7rem;">Trend</div>
                        <div class="progress mt-1">
                            <div class="progress-bar warning" style="width:75%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Risk Analysis <span class="badge badge-warning">Medium Risk</span></div>
            <div class="card-body">
                <div class="progress mb-2">
                    <div class="progress-bar warning" style="width:42%"></div>
                </div>
                <div style="font-size:0.75rem;"><i class="bi bi-exclamation-circle"></i> Networking attendance is below
                    75%</div>
                <div style="font-size:0.75rem;"><i class="bi bi-exclamation-circle"></i> Web Development has 2 consecutive
                    absences</div>
            </div>
        </div>
    </div>

    <!-- Risk Explanation Engine -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">Risk Explanation Engine</div>
        <div class="card-body">
            <div style="background: #fef2f2; padding: 15px; border-radius: 12px; border-left: 4px solid #dc2626;">
                <h4 style="color: #dc2626; margin-bottom: 10px;">⚠️ High Risk Detected</h4>
                <div style="color: #666; margin-bottom: 10px;">Reasons:</div>
                <ul style="margin-left: 20px; color: #666;">
                    <li>Attendance below 75% (currently 67%)</li>
                    <li>Roll call score below 5 (currently 4.5)</li>
                    <li>2 consecutive absences detected</li>
                    <li>Attendance trend declining</li>
                </ul>
                <div style="margin-top: 10px; color: #800000; font-weight: 600;">Recommendations:</div>
                <ul style="margin-left: 20px; color: #666;">
                    <li>Attend next 2 sessions consecutively</li>
                    <li>Contact course lecturer for catch-up materials</li>
                    <li>Review missed topics before next class</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-header">My Courses</div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Attendance</th>
                        <th>Roll Call</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>CS301</strong><br><span style="font-size:0.7rem;">Database Systems</span>
        </div>
        <td>88%<div class="progress mt-1">
                <div class="progress-bar success" style="width:88%"></div>
            </div>
    </div>
    <td>8.5</div>
    <td><span class="badge badge-success">Eligible</span></div>
    <td><button class="btn btn-outline btn-sm">Details</button></div>
        </tr>
        <tr>
            <td><strong>CS302</strong><br><span style="font-size:0.7rem;">Networking</span></div>
            <td>67%<div class="progress mt-1">
                    <div class="progress-bar warning" style="width:67%"></div>
                </div>
                </div>
            <td>4.5</div>
            <td><span class="badge badge-warning">Warning</span></div>
            <td><button class="btn btn-primary btn-sm">Prioritize</button></div>
        </tr>
        <tr>
            <td><strong>CS303</strong><br><span style="font-size:0.7rem;">Operating Systems</span></div>
            <td>95%<div class="progress mt-1">
                    <div class="progress-bar success" style="width:95%"></div>
                </div>
                </div>
            <td>9.5</div>
            <td><span class="badge badge-success">Eligible</span></div>
            <td><button class="btn btn-outline btn-sm">Details</button></div>
        </tr>
        <tr>
            <td><strong>CS304</strong><br><span style="font-size:0.7rem;">Web Development</span></div>
            <td>73%<div class="progress mt-1">
                    <div class="progress-bar warning" style="width:73%"></div>
                </div>
                </div>
            <td>5.5</div>
            <td><span class="badge badge-warning">Warning</span></div>
            <td><button class="btn btn-outline btn-sm">Details</button></div>
        </tr>
        </tbody>
        </table>
        </div>
        </div>

        <!-- Chart + Peer Benchmarking -->
        <div class="grid-2">
            <div class="card">
                <div class="card-header">Attendance Progress (6 Weeks)</div>
                <div class="card-body"><canvas id="attendanceChart" height="200"></canvas></div>
            </div>
            <div class="card">
                <div class="card-header">Peer Benchmarking</div>
                <div class="card-body">
                    <div><span>Your Attendance: 82%</span>
                        <div class="progress mt-1">
                            <div class="progress-bar" style="width:82%; background:#800000;"></div>
                        </div>
                    </div>
                    <div class="mt-2"><span>Course Average: 78%</span>
                        <div class="progress mt-1">
                            <div class="progress-bar" style="width:78%; background:#9ca3af;"></div>
                        </div>
                    </div>
                    <div class="mt-2"><span>Department Avg: 79%</span>
                        <div class="progress mt-1">
                            <div class="progress-bar" style="width:79%; background:#9ca3af;"></div>
                        </div>
                    </div>
                    <div class="mt-2"><span>University Avg: 74%</span>
                        <div class="progress mt-1">
                            <div class="progress-bar" style="width:74%; background:#9ca3af;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Health History -->
        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-header">Academic Health History (Monthly Tracking)</div>
            <div class="card-body">
                <canvas id="ahsHistoryChart" height="150"></canvas>
                <div
                    style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 12px; color: #666;">
                    <span>Jan: 72</span><span>Feb: 76</span><span>Mar: 81</span><span>Apr: 85</span><span>May: 86</span>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="card">
            <div class="card-header">Smart Recommendations</div>
            <div class="card-body">
                <div class="recommendation-item recommendation-critical"><strong>⚠️ Networking (CS302)</strong><br>Your
                    attendance is 67%. Attend next 2 sessions to reach 75% eligibility.</div>
                <div class="recommendation-item recommendation-warning"><strong>📖 Web Development (CS304)</strong><br>Two
                    consecutive absences detected. Review missed materials before next session.</div>
                <div class="recommendation-item recommendation-success"><strong>✅ Operating Systems
                        (CS303)</strong><br>Excellent! 95% attendance. Keep up the great work!</div>
            </div>
        </div>

        <!-- Floating Uni Bot Button -->
        <div style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
            <button onclick="openUniBot()"
                style="background: #800000; color: white; border: none; padding: 14px 20px; border-radius: 50px; font-weight: 600; cursor: pointer; box-shadow: 0 5px 20px rgba(128,0,0,0.3); display: flex; align-items: center; gap: 8px;">
                <i class="bi bi-robot" style="font-size: 1.2rem;"></i> Uni Bot
            </button>
        </div>

        <script>
            function openUniBot() {
                alert(
                    '🤖 Uni Bot: How can I help you?\n\nYou can ask me:\n- What is my attendance?\n- Am I eligible for exam?\n- What is my risk level?\n- Show my recommendations\n- When is my next class?');
            }
        </script>

        @push('scripts')
            <script>
                // Attendance Chart
                new Chart(document.getElementById('attendanceChart'), {
                    type: 'line',
                    data: {
                        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
                        datasets: [{
                                label: 'Your Attendance',
                                data: [85, 82, 78, 80, 83, 86],
                                borderColor: '#800000',
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Class Average',
                                data: [83, 81, 79, 80, 81, 82],
                                borderColor: '#f4c430',
                                borderDash: [5, 5],
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });

                // Academic Health History Chart
                new Chart(document.getElementById('ahsHistoryChart'), {
                    type: 'line',
                    data: {
                        labels: ['January', 'February', 'March', 'April', 'May'],
                        datasets: [{
                            label: 'Academic Health Score',
                            data: [72, 76, 81, 85, 86],
                            borderColor: '#800000',
                            backgroundColor: 'rgba(128,0,0,0.05)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            </script>
        @endpush
    @endsection
