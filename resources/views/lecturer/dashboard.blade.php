@extends('layouts.app')

@section('title', 'Lecturer Dashboard')
@section('role', 'Lecturer')
@section('page-title', 'Lecturer Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('lecturer.dashboard') }}" class="nav-item active">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('lecturer.attendance.take') }}" class="nav-item">
        <i class="bi bi-qr-code-scan"></i><span>Take Attendance</span>
    </a>
    <a href="{{ route('lecturer.enrollments.index') }}" class="nav-item">
        <i class="bi bi-list-check"></i><span>Enrollments</span>
    </a>
    <a href="{{ route('lecturer.students') }}" class="nav-item">
        <i class="bi bi-people"></i><span>All Students</span>
    </a>
    <a href="{{ route('lecturer.attendance.history') }}" class="nav-item">
        <i class="bi bi-clock-history"></i><span>Session History</span>
    </a>
    <a href="{{ route('lecturer.schedule') }}" class="nav-item">
        <i class="bi bi-calendar3"></i><span>Schedule</span>
    </a>
    <div class="nav-label">Reports</div>
    <a href="{{ route('lecturer.reports') }}" class="nav-item">
        <i class="bi bi-download"></i><span>Export Reports</span>
    </a>
    <a href="{{ route('lecturer.announcements') }}" class="nav-item">
        <i class="bi bi-megaphone"></i><span>Announcements</span>
    </a>
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
            transition: all 0.2s ease;
        }

        .ld-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            width: 100px;
            height: 100px;
            margin: 0.5rem auto;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ld-qr-placeholder i {
            font-size: 3rem;
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

        .ld-course-select {
            width: 100%;
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid #ddd;
            margin-bottom: 1rem;
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
        }
    </style>

    <div class="lecturer-container">
        <!-- Statistics Cards -->
        <div class="ld-stats">
            <div class="ld-stat-card">
                <div class="ld-stat-number">{{ $totalStudents ?? 0 }}</div>
                <div class="ld-stat-label">Total Students</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number" style="color:#dc2626;">{{ $atRiskStudents ?? 0 }}</div>
                <div class="ld-stat-label">At Risk Students</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number">{{ $avgAttendance ?? 0 }}%</div>
                <div class="ld-stat-label">Avg Attendance</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number">{{ $courseEngagement ?? 0 }}</div>
                <div class="ld-stat-label">Course Engagement</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number" style="color:#f59e0b;">{{ $lowAlerts ?? 0 }}</div>
                <div class="ld-stat-label">Low Alerts</div>
            </div>
            <div class="ld-stat-card">
                <div class="ld-stat-number">{{ $activeSessions ?? 0 }}</div>
                <div class="ld-stat-label">Active Sessions</div>
                <a href="{{ route('lecturer.attendance.take') }}" class="ld-btn-primary"
                    style="margin-top:5px; font-size:11px; display:inline-block; text-decoration:none;">+ New</a>
            </div>
        </div>

        <div class="ld-two-col">
            <!-- Active QR Session -->
            <div class="ld-qr">
                <h5 style="margin-bottom:10px;"><i class="bi bi-qr-code"></i> Active QR Session</h5>
                <div class="ld-qr-placeholder">
                    @if ($activeSession)
                        {!! QrCode::size(100)->generate($activeSession->session_token) !!}
                    @else
                        <i class="bi bi-qr-code-scan"></i>
                    @endif
                </div>
                <p><strong>{{ $activeSession ? $activeSession->course->course_name : 'No Active Session' }}</strong></p>
                <p>QR expires: <span id="timer">{{ $activeSession ? $expiresIn : 0 }}</span> sec</p>
                <div style="display:flex; gap:8px; justify-content:center;">
                    <form method="POST" action="{{ route('lecturer.attendance.end', $activeSession->id ?? 0) }}"
                        style="display:inline;">
                        @csrf
                        <button type="submit" class="ld-btn-danger">End</button>
                    </form>
                    <a href="{{ route('lecturer.attendance.generate') }}" class="ld-btn-sm"
                        style="background:rgba(255,255,255,0.2); color:white; text-decoration:none;">Refresh</a>
                </div>
            </div>

            <!-- Manual Attendance -->
            <div class="ld-card">
                <div class="ld-card-header"><i class="bi bi-pencil-square"></i> Manual Attendance</div>
                <div class="ld-card-body">
                    <form method="POST" action="{{ route('lecturer.attendance.manual') }}">
                        @csrf
                        <select name="course_id" class="ld-course-select" required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_code }} -
                                    {{ $course->course_name }}</option>
                            @endforeach
                        </select>
                        <select name="student_id" class="ld-course-select" required>
                            <option value="">Select Student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}
                                    ({{ $student->student_id ?? 'No ID' }})
                                </option>
                            @endforeach
                        </select>
                        <select name="status" class="ld-course-select" required>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                        </select>
                        <button type="submit" class="ld-btn-primary" style="width:100%;">Save Attendance</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Attendance -->
        <div class="ld-card">
            <div class="ld-card-header"><i class="bi bi-clock-history"></i> Live Attendance <span
                    style="background:#10b981; color:white; padding:2px 8px; border-radius:20px; font-size:10px; float:right;">LIVE</span>
            </div>
            <div class="ld-card-body">
                <div class="ld-live-stats">
                    <div class="ld-live-box">
                        <div class="ld-live-number">{{ $presentCount ?? 0 }}</div>
                        <div>Present</div><small>{{ $presentPercent ?? 0 }}%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number" style="color:#dc2626;">{{ $absentCount ?? 0 }}</div>
                        <div>Absent</div><small>{{ $absentPercent ?? 0 }}%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number" style="color:#f59e0b;">{{ $lateCount ?? 0 }}</div>
                        <div>Late</div><small>{{ $latePercent ?? 0 }}%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number">{{ $totalEnrolled ?? 0 }}</div>
                        <div>Total</div>
                    </div>
                </div>
                <div class="ld-progress">
                    <div style="width:{{ $presentPercent ?? 0 }}%; height:100%; background:#10b981;"></div>
                </div>
                @if ($lateStudents->count() > 0)
                    <div style="margin-top:10px; padding:8px; background:#fef9c3; border-radius:6px; font-size:12px;">
                        <strong>Late Arrivals:</strong>
                        @foreach ($lateStudents as $late)
                            {{ $late->student->name }} (+{{ $late->late_minutes ?? '?' }})
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Roll Call Calculation -->
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

        <!-- Charts & Insights -->
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

        <!-- At-Risk Students -->
        <div class="ld-card">
            <div class="ld-card-header">At-Risk Students <span class="ld-badge-danger"
                    style="float:right;">{{ $atRiskStudents ?? 0 }} Students</span></div>
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
                        @forelse($atRiskList as $risk)
                            <tr>
                                <td><strong>{{ $risk->student->name }}</strong></td>
                                <td>{{ $risk->course->course_name }}
            </div>
            <td>{{ $risk->attendance_percentage }}%<div class="ld-progress mt-1">
                    <div style="width:{{ $risk->attendance_percentage }}%; height:100%; background:#dc2626;"></div>
                </div>
            </td>
            <td><span
                    class="ld-badge-{{ $risk->risk_level == 'High' ? 'danger' : 'warning' }}">{{ $risk->risk_level }}</span>
        </div>
        <td><button class="ld-btn-sm" onclick="notifyStudent({{ $risk->student->id }})">Notify</button>
    </div>
    </tr>
@empty
    <tr>
        <td colspan="5" style="text-align:center;">No at-risk students detected</td>
    </tr>
    @endforelse
    </tbody>
    </table>
    </div>
    <div style="padding:12px; border-top:1px solid #e5e7eb;">
        <button class="ld-btn-primary" style="width:100%;" onclick="sendAnnouncement()">Send Announcement to All</button>
    </div>
    </div>

    <!-- Export Reports -->
    <div class="ld-card">
        <div class="ld-card-header">Export Reports</div>
        <div class="ld-card-body">
            <div class="ld-export">
                <select id="exportCourse">
                    <option value="all">All Courses</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
                <button class="ld-btn-primary" onclick="exportReport('pdf')">PDF</button>
                <button class="ld-btn-sm" onclick="exportReport('excel')">Excel</button>
                <button class="ld-btn-sm" onclick="exportReport('csv')">CSV</button>
            </div>
        </div>
    </div>
    </div>

    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button onclick="openUniBot()"
            style="background:#800000; color:white; border:none; padding:10px 16px; border-radius:50px; font-weight:600; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
            <i class="bi bi-robot"></i> Uni Bot
        </button>
    </div>

    <script>
        let timer = {{ $expiresIn ?? 0 }};
        const timerInterval = setInterval(() => {
            if (timer > 0) {
                timer--;
                document.getElementById('timer').innerText = timer;
            } else {
                clearInterval(timerInterval);
            }
        }, 1000);

        function openUniBot() {
            alert('🤖 Uni Bot: Lecturer Help\n\n- Show at-risk students\n- Attendance summary\n- Export report');
        }

        function notifyStudent(studentId) {
            if (confirm('Send notification to this student?')) {
                fetch('/lecturer/notify-student/' + studentId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => alert('Notification sent!'));
            }
        }

        function sendAnnouncement() {
            let message = prompt('Enter announcement message:');
            if (message) {
                fetch('/lecturer/send-announcement', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message
                    })
                }).then(() => alert('Announcement sent to all students!'));
            }
        }

        function exportReport(type) {
            const courseId = document.getElementById('exportCourse').value;
            alert('Exporting ' + type.toUpperCase() + ' report for ' + (courseId === 'all' ? 'all courses' :
                'selected course'));
            // window.location.href = '/lecturer/export/' + type + '?course_id=' + courseId;
        }

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
