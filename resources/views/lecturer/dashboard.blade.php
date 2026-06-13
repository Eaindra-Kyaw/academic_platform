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
            width: 120px;
            height: 120px;
            margin: 0.5rem auto;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .ld-qr-placeholder img {
            max-width: 100%;
            max-height: 100%;
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

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            border-radius: 0.3rem;
            border: 1px solid #ddd;
            background: none;
            cursor: pointer;
        }

        .btn-primary-sm {
            background: #800000;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 0.3rem;
            cursor: pointer;
            font-size: 0.7rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-danger-sm {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 0.3rem;
            cursor: pointer;
            font-size: 0.7rem;
        }

        .btn-success-sm {
            background: #10b981;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 0.3rem;
            cursor: pointer;
            font-size: 0.7rem;
        }

        .form-control {
            width: 100%;
            padding: 6px;
            margin-bottom: 8px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .badge-warning {
            background: #fef3c7;
            color: #d97706;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #800000;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
                <a href="{{ route('lecturer.attendance.take') }}" class="btn-primary-sm"
                    style="margin-top:5px; display:inline-block; text-decoration:none;">+ New</a>
            </div>
        </div>

        <div class="ld-two-col">
            <!-- Active QR Session -->
            <div class="ld-qr">
                <h5 style="margin-bottom:10px;"><i class="bi bi-qr-code"></i> Active QR Session</h5>
                <div class="ld-qr-placeholder" id="qrPlaceholder">
                    @if ($activeSession && isset($qrCode))
                        {!! $qrCode !!}
                    @else
                        <i class="bi bi-qr-code-scan"></i>
                    @endif
                </div>
                <p><strong
                        id="courseName">{{ $activeSession && $activeSession->course ? $activeSession->course->course_name : 'No Active Session' }}</strong>
                </p>
                @if ($activeSession)
                    <p>Room: <strong id="roomName">{{ $activeSession->room ?? 'Not specified' }}</strong></p>
                    <p>Manual Code: <strong id="manualCodeDisplay"
                            style="background:rgba(255,255,255,0.2); padding:2px 8px; border-radius:4px;">{{ $activeSession->manual_code ?? 'N/A' }}</strong>
                    </p>
                @endif
                <p>QR expires: <span id="timer">{{ $expiresIn ?? 0 }}</span> sec</p>
                <div style="display:flex; gap:8px; justify-content:center;">
                    @if ($activeSession)
                        <button onclick="endSession({{ $activeSession->id }})" class="btn-danger-sm">End</button>
                        <button onclick="refreshQr({{ $activeSession->id }})" class="btn-sm"
                            style="background:rgba(255,255,255,0.2); color:white;">Refresh</button>
                    @else
                        <button class="btn-danger-sm" disabled>End</button>
                        <button class="btn-sm" disabled>Refresh</button>
                    @endif
                </div>
            </div>

            <!-- Create Session Form (shown when no active session) -->
            @if (!$activeSession)
                <div class="ld-card">
                    <div class="ld-card-header"><i class="bi bi-plus-circle"></i> Create QR Session</div>
                    <div class="ld-card-body">
                        <form id="createSessionForm">
                            @csrf
                            <select name="course_id" id="course_id" class="form-control" required>
                                <option value="">Select Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->course_code }} -
                                        {{ $course->course_name }}</option>
                                @endforeach
                            </select>
                            <select name="duration" id="duration" class="form-control" required>
                                <option value="30">30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                                <option value="90">90 minutes</option>
                            </select>
                            <input type="text" name="room" id="room" class="form-control"
                                placeholder="Room (optional)">
                            <button type="submit" class="btn-success-sm" style="width:100%;">Generate QR Code</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Manual Attendance -->
            <div class="ld-card">
                <div class="ld-card-header"><i class="bi bi-pencil-square"></i> Manual Attendance</div>
                <div class="ld-card-body">
                    <form method="POST" action="{{ route('lecturer.attendance.manual') }}">
                        @csrf
                        <select name="course_id" class="form-control" required>
                            <option value="">Select Course</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_code }} -
                                    {{ $course->course_name }}</option>
                            @endforeach
                        </select>
                        <select name="student_id" class="form-control" required>
                            <option value="">Select Student</option>
                            @foreach ($students ?? [] as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} (Year
                                    {{ $student->current_year ?? 'N/A' }})</option>
                            @endforeach
                        </select>
                        <select name="status" class="form-control" required>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                        </select>
                        <button type="submit" class="btn-primary-sm" style="width:100%;">Save Attendance</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Attendance -->
        <div class="ld-card">
            <div class="ld-card-header"><i class="bi bi-clock-history"></i> Live Attendance</div>
            <div class="ld-card-body">
                <div class="ld-live-stats">
                    <div class="ld-live-box">
                        <div class="ld-live-number" id="presentCount">{{ $presentCount ?? 0 }}</div>
                        <div>Present</div><small id="presentPercent">{{ $presentPercent ?? 0 }}%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number" style="color:#dc2626;" id="absentCount">{{ $absentCount ?? 0 }}
                        </div>
                        <div>Absent</div><small id="absentPercent">{{ $absentPercent ?? 0 }}%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number" style="color:#f59e0b;" id="lateCount">{{ $lateCount ?? 0 }}</div>
                        <div>Late</div><small id="latePercent">{{ $latePercent ?? 0 }}%</small>
                    </div>
                    <div class="ld-live-box">
                        <div class="ld-live-number" id="totalInSession">{{ $totalInSession ?? 0 }}</div>
                        <div>Total</div>
                    </div>
                </div>
                @if (isset($lateStudents) && $lateStudents && $lateStudents->count() > 0)
                    <div style="margin-top:10px; padding:8px; background:#fef9c3; border-radius:6px; font-size:12px;">
                        <strong>Late Arrivals:</strong>
                        <div id="lateList">
                            @foreach ($lateStudents as $late)
                                {{ $late->student->name }}@if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- At-Risk Students -->
        <div class="ld-card">
            <div class="ld-card-header">At-Risk Students</div>
            <div class="ld-card-body">
                @if (isset($atRiskList) && count($atRiskList) > 0)
                    <div style="overflow-x: auto;">
                        <table style="width:100%; border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align:left; padding:8px;">Student</th>
                                    <th style="text-align:left; padding:8px;">Attendance</th>
                                    <th style="text-align:left; padding:8px;">Risk</th>
                                    <th style="text-align:left; padding:8px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($atRiskList as $risk)
                                    <tr>
                                        <td style="padding:8px;">{{ $risk->student->name ?? 'N/A' }}</td>
                                        <td style="padding:8px;">{{ $risk->attendance_percentage ?? 0 }}%</td>
                                        <td style="padding:8px;"><span
                                                class="badge-warning">{{ $risk->risk_level ?? 'Low' }}</span></td>
                                        <td style="padding:8px;"><button class="btn-sm"
                                                onclick="alert('Notify student functionality coming soon')">Notify</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align:center; padding:20px; color:#9ca3af;">No at-risk students detected</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="modal">
        <div class="modal-content" style="text-align:center;">
            <div class="loading-spinner"></div>
            <p style="margin-top:10px;">Processing...</p>
        </div>
    </div>

    <script>
        let timer = {{ $expiresIn ?? 0 }};
        let timerInterval;

        function showLoading() {
            document.getElementById('loadingModal').classList.add('show');
        }

        function hideLoading() {
            document.getElementById('loadingModal').classList.remove('show');
        }

        function startTimer() {
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                if (timer > 0) {
                    timer--;
                    document.getElementById('timer').innerText = timer;
                } else {
                    clearInterval(timerInterval);
                    if (timer === 0) {
                        location.reload();
                    }
                }
            }, 1000);
        }

        // Create new session
        document.getElementById('createSessionForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            showLoading();

            const formData = {
                course_id: document.getElementById('course_id').value,
                duration: document.getElementById('duration').value,
                room: document.getElementById('room').value,
                _token: '{{ csrf_token() }}'
            };

            fetch('{{ route('lecturer.generateQr') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error creating session');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    alert('Error creating session. Please try again.');
                });
        });

        // End session
        function endSession(sessionId) {
            if (confirm('Are you sure you want to end this attendance session?')) {
                showLoading();

                fetch(`/lecturer/end-session/${sessionId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        console.error('Error:', error);
                        alert('Error ending session');
                    });
            }
        }

        // Refresh QR code
        function refreshQr(sessionId) {
            showLoading();

            fetch(`/lecturer/refresh-qr/${sessionId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        // Update QR code image
                        const qrPlaceholder = document.getElementById('qrPlaceholder');
                        qrPlaceholder.innerHTML = data.qr_code;
                        // Update manual code
                        document.getElementById('manualCodeDisplay').innerText = data.manual_code;
                        // Reset timer
                        timer = {{ $expiresIn ?? 0 }};
                        startTimer();
                        alert('QR code refreshed successfully!');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    alert('Error refreshing QR code');
                });
        }

        // Update attendance stats every 10 seconds
        @if (isset($activeSession) && $activeSession)
            function updateStats() {
                fetch(`/lecturer/session-stats/{{ $activeSession->id }}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('presentCount').innerText = data.present;
                            document.getElementById('presentPercent').innerText = data.percentage;
                            document.getElementById('absentCount').innerText = data.total - data.present;
                            document.getElementById('absentPercent').innerText = (data.total - data.present) > 0 ?
                                ((data.total - data.present) / data.total * 100).toFixed(1) : 0;
                            document.getElementById('lateCount').innerText = data.late;
                            document.getElementById('totalInSession').innerText = data.total;

                            // Update late list
                            if (data.records && data.records.length > 0) {
                                const lateRecords = data.records.filter(r => r.status === 'late');
                                if (lateRecords.length > 0) {
                                    const lateList = document.getElementById('lateList');
                                    if (lateList) {
                                        lateList.innerHTML = lateRecords.map(r => r.student_name).join(', ');
                                    }
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching stats:', error));
            }

            // Start auto-refresh
            setInterval(updateStats, 10000);
        @endif

        // Initialize timer
        if (timer > 0) {
            startTimer();
        }
    </script>
@endsection
