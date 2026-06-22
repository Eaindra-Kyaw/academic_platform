@extends('layouts.app')

@section('title', 'Take Attendance')
@section('role', 'Lecturer')
@section('page-title', 'Take Attendance')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
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

        .qr-container-semester {
            background: linear-gradient(135deg, #1a5f7a, #0d3b4f);
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

        .qr-box img {
            width: 220px;
            height: 220px;
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

        .mode-selector {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .btn-custom {
            background: #800000;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-custom:hover {
            background: #5f0000;
            transform: translateY(-1px);
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 10px;
        }

        .countdown {
            font-size: 14px;
            margin-top: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            text-align: center;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
        }

        .stat-label {
            font-size: 12px;
        }

        .download-btn {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-top: 8px;
            text-decoration: none;
        }

        .download-btn:hover {
            background: #059669;
            color: white;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .qr-box img {
                width: 150px;
                height: 150px;
            }

            .manual-code {
                font-size: 18px;
            }
        }
    </style>

    <div class="row">
        <div class="col-md-6">
            @if ($activeSession)
                @if ($activeSession->course->qr_mode == 'semester')
                    <!-- Semester QR Mode Display -->
                    <div class="qr-container-semester">
                        <h4><i class="bi bi-qr-code"></i> Semester QR (Static)</h4>
                        <p style="font-size: 12px;">Same QR for whole semester - put on PowerPoint once</p>
                        <div class="qr-box">
                            @php
                                // FIXED: Use HTTPS base URL
                                $baseUrl = config('app.url');
                                $semesterQrText =
                                    $baseUrl .
                                    '/student/scan/semester?token=' .
                                    $activeSession->course->semester_qr_token .
                                    '&course=' .
                                    $activeSession->course->id;
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($semesterQrText) }}"
                                alt="Semester QR">
                            <div>
                                <a href="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($semesterQrText) }}"
                                    download="semester-qr.png" class="download-btn">💾 Download QR Code</a>
                            </div>
                        </div>
                        <div>
                            <p><strong>Course:</strong> {{ $activeSession->course->course_name ?? 'N/A' }}</p>
                            <p><strong>Room:</strong> {{ $activeSession->room ?? 'Not specified' }}</p>
                        </div>
                        <div style="margin-top: 15px;">
                            <form method="POST"
                                action="{{ route('lecturer.course.regenerate-semester-qr', $activeSession->course->id) }}"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-custom" style="background: #f59e0b;">⟳ Regenerate
                                    QR</button>
                            </form>
                            <form method="POST"
                                action="{{ route('lecturer.attendance.sessions.end', $activeSession->id) }}"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-custom" style="background: #dc2626;">⏹ End
                                    Session</button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Dynamic Session QR Mode Display -->
                    <div class="qr-container">
                        <h4><i class="bi bi-qr-code"></i> Session QR (Dynamic)</h4>
                        <p style="font-size: 12px;">Changes every session - expires in {{ $activeSession->duration }}
                            minutes</p>
                        <div class="qr-box">
                            @php
                                // FIXED: Use config('app.url') which should be HTTPS
                                $baseUrl = config('app.url');
                                $dynamicQrText =
                                    $baseUrl .
                                    '/student/scan/process?token=' .
                                    $activeSession->session_token .
                                    '&session=' .
                                    $activeSession->id;
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($dynamicQrText) }}"
                                alt="Dynamic QR">
                            <div>
                                <a href="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($dynamicQrText) }}"
                                    download="dynamic-qr.png" class="download-btn">💾 Download QR Code</a>
                            </div>
                        </div>
                        <div>
                            <p><strong>Course:</strong> {{ $activeSession->course->course_name ?? 'N/A' }}</p>
                            <p><strong>Room:</strong> {{ $activeSession->room ?? 'Not specified' }}</p>
                            <p><strong>Manual Code:</strong></p>
                            <div class="manual-code">{{ $activeSession->manual_code }}</div>
                            <div class="countdown"><span id="countdownTimer"></span></div>
                        </div>
                        <div style="margin-top: 15px;">
                            <form method="POST"
                                action="{{ route('lecturer.attendance.sessions.end', $activeSession->id) }}"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-custom" style="background: #dc2626;">End Session</button>
                            </form>
                            <a href="{{ route('lecturer.attendance.sessions.refresh', $activeSession->id) }}"
                                class="btn-custom" style="background: #f59e0b; text-decoration: none;">Refresh QR</a>
                        </div>
                    </div>
                @endif

                <!-- Live Stats -->
                <div class="mode-selector">
                    <h5><i class="bi bi-graph-up"></i> Live Attendance Statistics</h5>
                    <div class="stats-grid">
                        <div>
                            <div class="stat-number" style="color:#10b981;">{{ $activeSession->present_count ?? 0 }}</div>
                            <div class="stat-label">Present</div>
                        </div>
                        <div>
                            <div class="stat-number" style="color:#f59e0b;">{{ $activeSession->late_count ?? 0 }}</div>
                            <div class="stat-label">Late</div>
                        </div>
                        <div>
                            <div class="stat-number" style="color:#ef4444;">
                                {{ ($activeSession->total_students ?? 0) - ($activeSession->present_count ?? 0) - ($activeSession->late_count ?? 0) }}
                            </div>
                            <div class="stat-label">Absent</div>
                        </div>
                        <div>
                            <div class="stat-number" style="color:#800000;">{{ $activeSession->total_students ?? 0 }}</div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Create New Session Form -->
                <div class="mode-selector">
                    <h5 style="margin-bottom: 15px;"><i class="bi bi-sliders2"></i> Select QR Mode</h5>

                    <form method="POST" action="{{ route('lecturer.attendance.sessions.create') }}">
                        @csrf
                        <select name="course_id" class="form-control" required>
                            <option value="">-- Select Course --</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_code }} -
                                    {{ $course->course_name }}</option>
                            @endforeach
                        </select>

                        <div style="margin: 15px 0;">
                            <label
                                style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 10px; cursor: pointer;">
                                <input type="radio" name="qr_mode" value="session" checked> 📱 Session QR (Dynamic) - New
                                QR every session
                            </label>
                            <label
                                style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer;">
                                <input type="radio" name="qr_mode" value="semester"> 📚 Semester QR (Static) - Same QR
                                all semester
                            </label>
                        </div>

                        <div id="durationField">
                            <select name="duration" class="form-control" required>
                                <option value="15">15 minutes</option>
                                <option value="30" selected>30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                                <option value="90">90 minutes</option>
                                <option value="120">120 minutes</option>
                            </select>
                        </div>

                        <input type="text" name="room" class="form-control" placeholder="Room (optional)">

                        <button type="submit" class="btn-custom" style="width: 100%; margin-top: 10px;">🚀 Start QR
                            Session</button>
                    </form>
                </div>
            @endif
        </div>

        <div class="col-md-6">
            <!-- Manual Attendance Form -->
            <div class="mode-selector">
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

                    <!-- Notes Field -->
                    <div style="margin-top: 10px;">
                        <label style="font-size: 12px; color: #6b7280;">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2"
                            placeholder="e.g., Student arrived 15 minutes late due to traffic" style="resize: vertical;"></textarea>
                    </div>

                    <button type="submit" class="btn-custom" style="width: 100%; margin-top: 10px;">Save Manual
                        Attendance</button>
                </form>
            </div>

            <!-- Link to Session History -->
            <div class="mode-selector" style="text-align: center; background: #f8fafc;">
                <a href="{{ route('lecturer.attendance.sessions') }}" class="btn btn-outline-primary"
                    style="width: 100%; display: inline-block; padding: 8px; border: 1px solid #800000; color: #800000; border-radius: 6px; text-decoration: none; transition: all 0.2s;"
                    onmouseover="this.style.background='#800000'; this.style.color='white';"
                    onmouseout="this.style.background='transparent'; this.style.color='#800000';">
                    <i class="bi bi-clock-history"></i> View Full Session History
                </a>
                <small class="text-muted" style="display: block; margin-top: 8px; color: #6b7280; font-size: 12px;">
                    View all past sessions, attendance statistics, and detailed reports
                </small>
            </div>
        </div>
    </div>

    <script>
        @if ($activeSession && $activeSession->expires_at && $activeSession->course->qr_mode == 'session')
            let expiresAt = new Date('{{ $activeSession->expires_at }}').getTime();
            setInterval(function() {
                let now = new Date().getTime();
                let distance = expiresAt - now;
                if (distance < 0) {
                    document.getElementById('countdownTimer').innerHTML = 'EXPIRED';
                    return;
                }
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById('countdownTimer').innerHTML = `Time remaining: ${minutes}m ${seconds}s`;
            }, 1000);
        @endif

        // Hide/show duration field based on QR mode selection
        document.querySelectorAll('input[name="qr_mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const durationField = document.getElementById('durationField');
                if (this.value === 'semester') {
                    durationField.style.display = 'none';
                } else {
                    durationField.style.display = 'block';
                }
            });
        });

        // Auto-select duration field visibility on page load
        document.addEventListener('DOMContentLoaded', function() {
            const selected = document.querySelector('input[name="qr_mode"]:checked');
            if (selected && selected.value === 'semester') {
                document.getElementById('durationField').style.display = 'none';
            }
        });
    </script>
@endsection
