@extends('layouts.app')

@section('title', 'Take Attendance')
@section('role', 'Lecturer')
@section('page-title', 'Take Attendance')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('lecturer.dashboard') }}" class="nav-item">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('lecturer.attendance.take') }}" class="nav-item active">
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
        .qr-container {
            background: linear-gradient(135deg, #800000, #5f0000);
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 20px;
        }

        .qr-box {
            background: white;
            padding: 15px;
            border-radius: 10px;
            display: inline-block;
            margin: 10px auto;
        }

        .manual-code {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 5px;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
            font-family: monospace;
        }

        .session-info {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #800000;
        }

        .stat-label {
            color: #666;
            font-size: 12px;
        }

        .btn-custom {
            background: #800000;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-custom:hover {
            background: #5f0000;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .recent-sessions {
            max-height: 300px;
            overflow-y: auto;
        }

        .session-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .session-item:hover {
            background: #f9fafb;
        }

        .countdown {
            font-size: 14px;
            margin-top: 10px;
        }

        .timer-critical {
            color: #ffcccc;
        }

        .timer-warning {
            color: #ffd700;
        }
    </style>

    <div class="row">
        <!-- Left Column - QR Session -->
        <div class="col-md-6">
            @if ($activeSession)
                <!-- Active Session Display -->
                <div class="qr-container">
                    <h4><i class="bi bi-qr-code"></i> Active QR Session</h4>
                    <div class="qr-box">
                        {!! QrCode::size(180)->generate(
                            route('student.scan.process') . '?token=' . $activeSession->session_token . '&session=' . $activeSession->id,
                        ) !!}
                    </div>
                    <div>
                        <p><strong>Course:</strong> {{ $activeSession->course->course_name ?? 'N/A' }}</p>
                        <p><strong>Room:</strong> {{ $activeSession->room ?? 'Not specified' }}</p>
                        <p><strong>Manual Code:</strong></p>
                        <div class="manual-code">{{ $activeSession->session_code }}</div>
                        <div class="countdown">
                            <span id="countdownTimer"></span>
                        </div>
                    </div>
                    <div style="margin-top: 15px;">
                        <form method="POST" action="{{ route('lecturer.attendance.sessions.end', $activeSession->id) }}"
                            style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-custom" style="background: #dc2626;">End Session</button>
                        </form>
                        <a href="{{ route('lecturer.attendance.sessions.refresh', $activeSession->id) }}"
                            class="btn-custom" style="background: #f59e0b;">Refresh QR</a>
                    </div>
                </div>

                <!-- Live Stats -->
                <div class="session-info">
                    <h5><i class="bi bi-graph-up"></i> Live Attendance Statistics</h5>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number" id="presentCount">{{ $activeSession->present_count ?? 0 }}</div>
                            <div class="stat-label">Present</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="lateCount">{{ $activeSession->late_count ?? 0 }}</div>
                            <div class="stat-label">Late</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="absentCount">
                                {{ ($activeSession->total_students ?? 0) - ($activeSession->present_count ?? 0) }}</div>
                            <div class="stat-label">Absent</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number" id="totalCount">{{ $activeSession->total_students ?? 0 }}</div>
                            <div class="stat-label">Total Enrolled</div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Create New Session Form -->
                <div class="session-info">
                    <h5><i class="bi bi-plus-circle"></i> Create New QR Session</h5>
                    <form method="POST" action="{{ route('lecturer.attendance.sessions.create') }}">
                        @csrf
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_code }} -
                                    {{ $course->course_name }}</option>
                            @endforeach
                        </select>

                        <select name="duration" class="form-control" required>
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">60 minutes</option>
                            <option value="90">90 minutes</option>
                            <option value="120">120 minutes</option>
                        </select>

                        <input type="text" name="room" class="form-control" placeholder="Room (optional)">

                        <button type="submit" class="btn-custom" style="width: 100%;">Generate QR Code</button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Right Column - Manual Attendance -->
        <div class="col-md-6">
            <!-- Manual Attendance Form -->
            <div class="session-info">
                <h5><i class="bi bi-pencil-square"></i> Manual Attendance Entry</h5>
                <form method="POST" action="{{ route('lecturer.attendance.manual') }}">
                    @csrf
                    <select name="course_id" class="form-control" required>
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>

                    <select name="status" class="form-control" required>
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="absent">Absent</option>
                    </select>

                    <button type="submit" class="btn-custom" style="width: 100%;">Save Manual Attendance</button>
                </form>
            </div>

            <!-- Recent Sessions -->
            <div class="session-info">
                <h5><i class="bi bi-clock-history"></i> Recent Sessions</h5>
                <div class="recent-sessions">
                    @if ($recentSessions && $recentSessions->count() > 0)
                        @foreach ($recentSessions as $session)
                            <div class="session-item">
                                <strong>{{ $session->course->course_name ?? 'N/A' }}</strong><br>
                                <small>
                                    {{ $session->created_at->format('M d, Y H:i') }} |
                                    {{ $session->present_count }}/{{ $session->total_students }} present
                                </small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No recent sessions</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Timer for active session
        @if ($activeSession && $activeSession->qr_expires_at)
            let expiresAt = new Date('{{ $activeSession->qr_expires_at }}').getTime();
            let timerInterval = setInterval(function() {
                let now = new Date().getTime();
                let distance = expiresAt - now;

                if (distance < 0) {
                    clearInterval(timerInterval);
                    document.getElementById('countdownTimer').innerHTML = 'QR EXPIRED';
                    location.reload();
                    return;
                }

                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                let timerText = `Time remaining: ${minutes}m ${seconds}s`;
                let timerElement = document.getElementById('countdownTimer');
                timerElement.innerHTML = timerText;

                if (minutes < 1) {
                    timerElement.className = 'timer-critical';
                } else if (minutes < 5) {
                    timerElement.className = 'timer-warning';
                }
            }, 1000);
        @endif
    </script>
@endsection
