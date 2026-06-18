@extends('layouts.app')

@section('title', 'QR Attendance Sessions')
@section('role', 'Lecturer')
@section('page-title', 'QR Attendance Sessions')
@section('welcome-text', 'Create and manage QR attendance sessions')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #800000;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .create-form {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        .session-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .session-header {
            background: linear-gradient(135deg, #800000 0%, #6b0000 100%);
            padding: 1rem;
            color: white;
        }

        .session-body {
            padding: 1.5rem;
            text-align: center;
        }

        .filter-select,
        .filter-input {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.8rem;
        }

        .btn-filter {
            background: #800000;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-sm {
            padding: 0.3rem 0.8rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            cursor: pointer;
            border: none;
        }

        .timer-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .timer-inner {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $activeSessions->count() }}</div>
                <div class="stat-label">Active Sessions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $courses->count() }}</div>
                <div class="stat-label">Your Courses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $recentSessions->count() }}</div>
                <div class="stat-label">Recent Sessions</div>
            </div>
        </div>

        <div class="create-form">
            <h4 style="color: #800000; margin-bottom: 1rem;">➕ Create QR Session</h4>
            <form method="POST" action="{{ route('lecturer.attendance.sessions.create') }}">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <select name="course_id" class="filter-select" required>
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="duration" class="filter-select" required>
                        <option value="15">15 minutes</option>
                        <option value="30" selected>30 minutes</option>
                        <option value="45">45 minutes</option>
                        <option value="60">60 minutes</option>
                    </select>
                    <input type="text" name="room" class="filter-input" placeholder="Room (optional)">
                </div>
                <button type="submit" class="btn-filter" style="margin-top: 1rem; width: 100%;"><i
                        class="bi bi-qr-code"></i> Generate QR Code</button>
            </form>
        </div>

        @if ($activeSessions->count() > 0)
            <div class="session-card">
                <div class="session-header"><i class="bi bi-qr-code"></i> Active QR Sessions</div>
                <div class="session-body">
                    @foreach ($activeSessions as $session)
                        <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 1rem;">
                                <div><strong>{{ $session->course->course_code }} -
                                        {{ $session->course->course_name }}</strong><br><small>Room:
                                        {{ $session->room ?? 'TBA' }}</small></div>
                                <span class="status-badge status-pending"><i class="bi bi-clock-history"></i> Active</span>
                            </div>

                            <div id="qrcode-{{ $session->id }}"
                                style="display: flex; justify-content: center; margin: 1rem 0;"></div>

                            <div style="margin: 1rem 0; font-size: 0.8rem; text-align: center;">
                                <div id="timer-ring-{{ $session->id }}" class="timer-ring"
                                    style="background: conic-gradient(#10b981 0deg 360deg, #e5e7eb 0deg 360deg);">
                                    <div id="timer-{{ $session->id }}" class="timer-inner">--</div>
                                </div>
                                <div style="margin-top: 0.5rem; font-size: 0.7rem;">Manual Code:
                                    <strong>{{ $session->session_code }}</strong>
                                </div>
                            </div>

                            <div style="margin-top: 0.5rem; font-size: 0.7rem; color: #6b7280; text-align: center;">
                                <i class="bi bi-people"></i> {{ $session->present_count }}/{{ $session->total_students }}
                                present ({{ $session->attendance_percentage }}%)
                            </div>

                            <div style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 1rem;">
                                <form method="POST" action="{{ route('lecturer.attendance.sessions.end', $session->id) }}"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-sm" style="background: #ef4444; color: white;">End
                                        Session</button>
                                </form>
                                <a href="{{ route('lecturer.attendance.sessions.refresh', $session->id) }}" class="btn-sm"
                                    style="background: #f59e0b; color: white; text-decoration: none;">Refresh QR</a>
                            </div>
                        </div>

                        <script>
                            new QRCode(document.getElementById("qrcode-{{ $session->id }}"), {
                                text: "{{ route('student.scan.process') }}?token={{ $session->session_token }}&session={{ $session->id }}",
                                width: 180,
                                height: 180
                            });

                            let expiresAt{{ $session->id }} = new Date('{{ $session->qr_expires_at }}').getTime();

                            function updateTimer{{ $session->id }}() {
                                const now = new Date().getTime();
                                const distance = expiresAt{{ $session->id }} - now;
                                if (distance < 0) {
                                    document.getElementById('timer-{{ $session->id }}').innerText = '0';
                                    return;
                                }
                                const minutes = Math.floor(distance / (1000 * 60));
                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                document.getElementById('timer-{{ $session->id }}').innerText =
                                    `${minutes}:${seconds.toString().padStart(2, '0')}`;
                                const totalSeconds = Math.floor({{ $session->qr_expires_at->diffInSeconds($session->created_at) }});
                                const elapsed = totalSeconds - Math.floor(distance / 1000);
                                const percent = (elapsed / totalSeconds) * 360;
                                document.getElementById('timer-ring-{{ $session->id }}').style.background =
                                    `conic-gradient(#10b981 0deg ${percent}deg, #e5e7eb ${percent}deg 360deg)`;
                            }
                            setInterval(updateTimer{{ $session->id }}, 1000);
                            updateTimer{{ $session->id }}();
                        </script>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
