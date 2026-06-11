@extends('layouts.app')

@section('title', 'Lecturer Dashboard')
@section('role', 'Lecturer')
@section('page-title', 'Lecturer Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="#" class="nav-item active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
    <a href="#" class="nav-item"><i class="bi bi-qr-code-scan"></i><span>Take Attendance</span></a>
    <a href="#" class="nav-item"><i class="bi bi-clock-history"></i><span>Session History</span></a>
    <a href="#" class="nav-item"><i class="bi bi-people"></i><span>All Students</span></a>
    <a href="#" class="nav-item"><i class="bi bi-calendar3"></i><span>Schedule</span></a>
    <div class="nav-label">Reports</div>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Export Reports</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
@endsection

@section('content')
    <style>
        .lecturer-container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
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
            background: white;
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .ld-stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #800000;
        }

        .ld-stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .ld-two-col {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .ld-two-col>* {
            flex: 1 1 calc(50% - 0.5rem);
            min-width: 250px;
        }

        .ld-qr {
            background: linear-gradient(135deg, #800000, #5f0000);
            color: white;
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
        }

        .ld-qr-placeholder {
            background: white;
            width: 80px;
            height: 80px;
            margin: 0.5rem auto;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ld-qr-placeholder i {
            font-size: 2rem;
            color: #800000;
        }

        .ld-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .ld-card-header {
            padding: 0.75rem 1rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            color: #800000;
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
            background: #f9fafb;
            padding: 0.75rem;
            border-radius: 0.75rem;
            text-align: center;
        }

        .ld-live-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #800000;
        }

        .ld-rollcall {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            text-align: center;
        }

        .ld-rollcall-item {
            flex: 1 1 calc(20% - 0.5rem);
            min-width: 65px;
            padding: 0.25rem;
            background: #f9fafb;
            border-radius: 0.5rem;
            font-size: 0.7rem;
        }

        .ld-progress {
            height: 6px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
        }

        .ld-btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            border-radius: 0.3rem;
            border: 1px solid #ddd;
            background: none;
            cursor: pointer;
        }

        .ld-btn-primary {
            background: #800000;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        .ld-btn-danger {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 0.3rem;
            cursor: pointer;
        }

        .ld-table-responsive {
            overflow-x: auto;
        }

        .ld-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
            min-width: 500px;
        }

        .ld-table th,
        .ld-table td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .ld-table th {
            background: #f9fafb;
        }

        .ld-badge-danger {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            font-size: 0.65rem;
        }

        .ld-badge-warning {
            background: #fef9c3;
            color: #854d0e;
            padding: 0.2rem 0.5rem;
            border-radius: 20px;
            font-size: 0.65rem;
        }

        .ld-insight {
            padding: 0.5rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            border-left: 3px solid;
            font-size: 0.75rem;
        }

        .ld-export {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }

        .ld-export select {
            padding: 0.4rem;
            border-radius: 0.5rem;
            border: 1px solid #ddd;
            flex: 1;
            min-width: 100px;
        }

        @media (max-width: 992px) {
            .ld-stat-card {
                flex: 1 1 calc(33.333% - 1rem);
            }
        }

        @media (max-width: 768px) {
            .ld-stat-card {
                flex: 1 1 calc(50% - 1rem);
            }

            .ld-two-col>* {
                flex: 1 1 100%;
            }

            .ld-live-box {
                flex: 1 1 calc(50% - 0.75rem);
            }

            .ld-rollcall-item {
                flex: 1 1 calc(33.333% - 0.5rem);
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

            .ld-rollcall-item {
                flex: 1 1 calc(50% - 0.5rem);
            }

            .ld-export {
                flex-direction: column;
            }

            .ld-export select {
                width: 100%;
            }

            .ld-card-header {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 375px) {
            .ld-stat-number {
                font-size: 1rem;
            }

            .ld-stat-card {
                padding: 0.5rem;
            }

            .ld-live-number {
                font-size: 1.1rem;
            }

            .ld-table th,
            .ld-table td {
                padding: 0.3rem;
                font-size: 0.65rem;
            }
        }
    </style>

    <div class="lecturer-container">
        <div class="ld-stats">
            <div class="ld-stat-card">
                <div class="ld-stat-number">156</div>
                <div class="ld-stat-label">Total Students</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number" style="color:#dc2626;">23</div>
                <div class="ld-stat-label">At Risk Students</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number">78%</div>
                <div class="ld-stat-label">Avg Attendance</div>
                <div style="color:#dc2626; font-size:10px;">↓ 3%</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number">84</div>
                <div class="ld-stat-label">Course Engagement</div>
                <div style="color:#10b981; font-size:10px;">↑ +5</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number" style="color:#f59e0b;">7</div>
                <div class="ld-stat-label">Low Alerts</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number">2</div>
                <div class="ld-stat-label">Active Sessions</div><button class="ld-btn-primary"
                    style="margin-top:5px; font-size:11px;" onclick="alert('New QR Session')">+ New</button>
            </div>
        </div>

        <div class="ld-two-col">
            <div class="ld-qr">
                <h5 style="margin-bottom:10px;"><i class="bi bi-qr-code"></i> Active QR Session</h5>
                <div class="ld-qr-placeholder"><i class="bi bi-qr-code-scan"></i></div>
                <p>Database Systems (CS301)</p>
                <p>QR expires: <span id="timer">45</span> sec</p>
                <div style="display:flex; gap:8px; justify-content:center;">
                    <button class="ld-btn-danger" onclick="alert('Session ended')">End</button>
                    <button class="ld-btn-sm" style="background:rgba(255,255,255,0.2); color:white;"
                        onclick="alert('QR Refreshed')">Refresh</button>
                </div>
            </div>
            <div class="ld-card">
                <div class="ld-card-header"><i class="bi bi-pencil-square"></i> Manual Attendance</div>
                <div class="ld-card-body">
                    <select style="width:100%; padding:6px; margin-bottom:8px; border-radius:6px; border:1px solid #ddd;">
                        <option>Database Systems (CS301)</option>
                        <option>Networking (CS302)</option>
                    </select>
                    <select style="width:100%; padding:6px; margin-bottom:8px; border-radius:6px; border:1px solid #ddd;">
                        <option>Select Student</option>
                        <option>Eaindra Kyaw</option>
                        <option>Su Mon Kyaw</option>
                    </select>
                    <select style="width:100%; padding:6px; margin-bottom:12px; border-radius:6px; border:1px solid #ddd;">
                        <option>Present</option>
                        <option>Absent</option>
                        <option>Late</option>
                    </select>
                    <button class="ld-btn-primary" style="width:100%;"
                        onclick="alert('Manual attendance saved')">Save</button>
                </div>
            </div>
        </div>

        <div class="ld-card">
            <div class="ld-card-header"><i class="bi bi-clock-history"></i> Live Attendance <span
                    style="background:#10b981; color:white; padding:2px 8px; border-radius:20px; font-size:10px; float:right;">LIVE</span>
            </div>
            <div class="ld-card-body">
                <div class="ld-live-stats">
                    <div class="ld-live-box">
                        <div class="ld-live-number">28</div>
                        <div>Present</div><small>71%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number" style="color:#dc2626;">8</div>
                        <div>Absent</div><small>21%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number" style="color:#f59e0b;">3</div>
                        <div>Late</div><small>8%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number">39</div>
                        <div>Total</div>
                    </div>
                </div>
                <div class="ld-progress">
                    <div style="width:72%; height:100%; background:#10b981;"></div>
                </div>
                <div style="margin-top:10px; padding:8px; background:#fef9c3; border-radius:6px; font-size:12px;">
                    <strong>Late Arrivals:</strong> Eaindra Kyaw (+8), Su Mon Kyaw (+12), Phone Myint (+5)</div>
            </div>
        </div>

        <div class="ld-card">
            <div class="ld-card-header"><i class="bi bi-star-fill"></i> Roll Call Calculation</div>
            <div class="ld-card-body">
                <div class="ld-rollcall">
                    <div class="ld-rollcall-item"><strong>95-100%</strong><br><span
                            style="background:#10b981; color:white; padding:2px 6px; border-radius:20px;">10</span></div>
                    <div class="ld-rollcall-item"><strong>90-94%</strong><br><span
                            style="background:#10b981; color:white; padding:2px 6px; border-radius:20px;">9</span></div>
                    <div class="ld-rollcall-item"><strong>85-89%</strong><br><span
                            style="background:#10b981; color:white; padding:2px 6px; border-radius:20px;">8</span></div>
                    <div class="ld-rollcall-item"><strong>80-84%</strong><br><span
                            style="background:#3b82f6; color:white; padding:2px 6px; border-radius:20px;">7</span></div>
                    <div class="ld-rollcall-item"><strong>75-79%</strong><br><span
                            style="background:#3b82f6; color:white; padding:2px 6px; border-radius:20px;">6</span></div>
                    <div class="ld-rollcall-item"><strong>70-74%</strong><br><span
                            style="background:#f59e0b; color:white; padding:2px 6px; border-radius:20px;">5</span></div>
                    <div class="ld-rollcall-item"><strong>65-69%</strong><br><span
                            style="background:#f59e0b; color:white; padding:2px 6px; border-radius:20px;">4</span></div>
                    <div class="ld-rollcall-item"><strong>60-64%</strong><br><span
                            style="background:#dc2626; color:white; padding:2px 6px; border-radius:20px;">3</span></div>
                    <div class="ld-rollcall-item"><strong>55-59%</strong><br><span
                            style="background:#dc2626; color:white; padding:2px 6px; border-radius:20px;">2</span></div>
                    <div class="ld-rollcall-item"><strong>Below55%</strong><br><span
                            style="background:#dc2626; color:white; padding:2px 6px; border-radius:20px;">0-1</span></div>
                </div>
            </div>
        </div>

        <div class="ld-two-col">
            <div class="ld-card">
                <div class="ld-card-header">Course Engagement Trend</div>
                <div class="ld-card-body"><canvas id="engagementChart" height="200"></canvas></div>
            </div>
            <div class="ld-card">
                <div class="ld-card-header">Smart Insights</div>
                <div class="ld-card-body">
                    <div class="ld-insight" style="border-left-color:#f59e0b; background:#fffbeb;"><strong>📊 Low
                            Attendance Pattern</strong><br>Tuesday 8 AM: 62% | Thursday: 81%</div>
                    <div class="ld-insight" style="border-left-color:#dc2626; background:#fef2f2;"><strong>📉 Engagement
                            Drop</strong><br>Networking dropped 18% this month</div>
                    <div class="ld-insight" style="border-left-color:#3b82f6; background:#eff6ff;"><strong>🔮 Attendance
                            Prediction</strong><br>8 students likely at-risk next week</div>
                </div>
            </div>
        </div>

        <div class="ld-card">
            <div class="ld-card-header">At-Risk Students <span class="ld-badge-danger" style="float:right;">23
                    Students</span></div>
            <div class="ld-table-responsive">
                <table class="ld-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Attendance</th>
                            <th>Risk</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Eaindra Kyaw</strong></td>
                            <td>Networking</td>
                            <td>58%<div class="ld-progress mt-1">
                                    <div style="width:58%; height:100%; background:#dc2626;"></div>
                                </div>
                            </td>
                            <td><span class="ld-badge-danger">High</span></td>
                            <td><button class="ld-btn-sm" onclick="alert('Notify')">Notify</button></td>
                        </tr>
                        <tr>
                            <td><strong>Su Mon Kyaw</strong></td>
                            <td>Database</td>
                            <td>62%<div class="ld-progress mt-1">
                                    <div style="width:62%; height:100%; background:#f59e0b;"></div>
                                </div>
                            </td>
                            <td><span class="ld-badge-warning">Medium</span></td>
                            <td><button class="ld-btn-sm" onclick="alert('Notify')">Notify</button></td>
                        </tr>
                        <tr>
                            <td><strong>Phone Myint</strong></td>
                            <td>Web Dev</td>
                            <td>55%<div class="ld-progress mt-1">
                                    <div style="width:55%; height:100%; background:#dc2626;"></div>
                                </div>
                            </td>
                            <td><span class="ld-badge-danger">High</span></td>
                            <td><button class="ld-btn-sm" onclick="alert('Notify')">Notify</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding:12px; border-top:1px solid #e5e7eb;"><button class="ld-btn-primary" style="width:100%;"
                    onclick="alert('Announcement sent')">Send Announcement to All</button></div>
        </div>

        <div class="ld-card">
            <div class="ld-card-header">Export Reports</div>
            <div class="ld-card-body">
                <div class="ld-export">
                    <select>
                        <option>All Courses</option>
                        <option>Database</option>
                        <option>Networking</option>
                    </select>
                    <button class="ld-btn-primary" onclick="alert('PDF Export')">PDF</button>
                    <button class="ld-btn-sm" onclick="alert('Excel Export')">Excel</button>
                    <button class="ld-btn-sm" onclick="alert('CSV Export')">CSV</button>
                </div>
            </div>
        </div>
    </div>

    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button onclick="openUniBot()"
            style="background:#800000; color:white; border:none; padding:10px 16px; border-radius:50px; font-weight:600; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15);"><i
                class="bi bi-robot"></i> Uni Bot</button>
    </div>

    <script>
        function openUniBot() {
            alert('🤖 Uni Bot: Lecturer Help\n\n- Show at-risk students\n- Attendance summary\n- Export report');
        }
        let timer = 45;
        setInterval(() => {
            if (timer > 0) {
                timer--;
                document.getElementById('timer').innerText = timer;
            }
        }, 1000);
        new Chart(document.getElementById('engagementChart'), {
            type: 'line',
            data: {
                labels: ['W1', 'W2', 'W3', 'W4', 'W5', 'W6'],
                datasets: [{
                        label: 'Database',
                        data: [85, 83, 80, 78, 76, 74],
                        borderColor: '#800000',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Networking',
                        data: [82, 78, 74, 70, 68, 62],
                        borderColor: '#dc2626',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Web Dev',
                        data: [88, 87, 86, 85, 84, 83],
                        borderColor: '#10b981',
                        fill: false,
                        tension: 0.3
                    }
                ]
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
@endsection
