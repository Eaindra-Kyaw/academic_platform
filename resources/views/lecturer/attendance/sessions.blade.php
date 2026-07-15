@extends('layouts.app')

@section('title', 'Active QR Sessions')
@section('role', 'Lecturer')
@section('page-title', 'Active QR Sessions')
@section('welcome-text', 'Create and manage active QR attendance sessions')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-gray);
        }

        .create-form {
            background: var(--white);
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .create-form h4 {
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .session-card {
            background: var(--white);
            border-radius: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .session-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 1rem;
            color: var(--white);
        }

        .session-body {
            padding: 1.5rem;
            text-align: center;
        }

        .filter-select,
        .filter-input {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.5rem;
            font-size: 0.8rem;
            background: var(--bg-main);
            transition: all 0.3s ease;
        }

        .filter-select:focus,
        .filter-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .btn-filter {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.25);
        }

        .btn-sm {
            padding: 0.3rem 0.8rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
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
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--text-dark);
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

        .session-sub {
            border: 1px solid rgba(10, 36, 99, 0.06);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .session-sub:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-hover);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .create-form form>div {
                grid-template-columns: 1fr !important;
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
                <div class="stat-number">{{ $sessions->count() }}</div>
                <div class="stat-label">Total Sessions</div>
            </div>
        </div>

        <div class="create-form">
            <h4>➕ Create QR Session</h4>
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
                        <option value="90">90 minutes</option>
                        <option value="120">120 minutes</option>
                    </select>
                    <input type="text" name="room" class="filter-input" placeholder="Room (optional)">
                </div>
                <div style="margin-top: 0.5rem;">
                    <label style="font-weight: 600; font-size: 0.8rem; color: var(--text-dark);">Number of Class
                        Periods</label>
                    <select name="period_count" class="filter-select">
                        <option value="1">1 period (50 min)</option>
                        <option value="2">2 periods (1h 40m)</option>
                        <option value="3">3 periods (2h 30m)</option>
                        <option value="4" selected>4 periods (3h 20m)</option>
                        <option value="5">5 periods (4h 10m)</option>
                        <option value="6">6 periods (5h)</option>
                        <option value="7">7 periods (5h 50m)</option>
                        <option value="8">8 periods (6h 40m)</option>
                    </select>
                    <small style="color: var(--text-gray); font-size: 10px;">
                        How many 50-minute class periods does this session cover?
                    </small>
                </div>
                <button type="submit" class="btn-filter" style="margin-top: 1rem; width: 100%;">
                    <i class="bi bi-qr-code"></i> Generate QR Code
                </button>
            </form>
        </div>

        @if ($activeSessions->count() > 0)
            <div class="session-card">
                <div class="session-header"><i class="bi bi-qr-code"></i> Active QR Sessions</div>
                <div class="session-body">
                    @foreach ($activeSessions as $session)
                        @php
                            $baseUrl = config('app.url');
                            $qrText =
                                $baseUrl .
                                '/student/scan/process?token=' .
                                $session->session_token .
                                '&session=' .
                                $session->id;

                            $present = $session->records->where('status', 'present')->count();
                            $late = $session->records->where('status', 'late')->count();

                            // ✅ FIX: Get total enrolled students from database if $total_students is empty
                            $totalEnrolled = $session->total_students ?? 0;
                            if ($totalEnrolled == 0) {
                                $totalEnrolled = \App\Models\Enrollment::where('course_id', $session->course_id)
                                    ->where('status', 'approved')
                                    ->count();
                            }

                            $periods = $session->conducted_periods ?? 1;
                            $attendedPeriods = ($present + $late) * $periods;
                            $totalPeriods = $totalEnrolled * $periods;
                            $attendancePercentage =
                                $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100) : 0;
                        @endphp
                        <div class="session-sub">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-bottom: 1rem;">
                                <div>
                                    <strong style="color: var(--text-dark);">
                                        {{ $session->course->course_code }} - {{ $session->course->course_name }}
                                    </strong>
                                    <br>
                                    <small style="color: var(--text-gray);">
                                        Room: {{ $session->room ?? 'TBA' }} &bull;
                                        Periods: {{ $periods }}
                                    </small>
                                </div>
                                <span class="status-badge status-pending">
                                    <i class="bi bi-clock-history"></i> Active
                                </span>
                            </div>

                            <div id="qrcode-{{ $session->id }}"
                                style="display: flex; justify-content: center; margin: 1rem 0;"></div>

                            <div style="margin: 1rem 0; font-size: 0.8rem; text-align: center;">
                                <div id="timer-ring-{{ $session->id }}" class="timer-ring"
                                    style="background: conic-gradient(#10b981 0deg 360deg, #e5e7eb 0deg 360deg);">
                                    <div id="timer-{{ $session->id }}" class="timer-inner">--</div>
                                </div>
                                <div style="margin-top: 0.5rem; font-size: 0.7rem; color: var(--text-gray);">
                                    Manual Code: <strong
                                        style="color: var(--primary);">{{ $session->session_code }}</strong>
                                </div>
                            </div>

                            <div
                                style="margin-top: 0.5rem; font-size: 0.7rem; color: var(--text-gray); text-align: center;">
                                <i class="bi bi-people"></i>
                                {{ $present + $late }}/{{ $totalEnrolled }} attended
                                ({{ $attendancePercentage }}%)
                                @if ($periods > 1)
                                    <br>
                                    <small>Periods: {{ $attendedPeriods }}/{{ $totalPeriods }}</small>
                                @endif
                            </div>

                            <div style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 1rem;">
                                <form method="POST" action="{{ route('lecturer.attendance.sessions.end', $session->id) }}"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-sm"
                                        style="background: var(--danger); color: var(--white);">
                                        End Session
                                    </button>
                                </form>
                                <a href="{{ route('lecturer.attendance.sessions.refresh', $session->id) }}" class="btn-sm"
                                    style="background: var(--secondary); color: var(--white); text-decoration: none;">
                                    Refresh QR
                                </a>
                            </div>
                        </div>

                        <script>
                            new QRCode(document.getElementById("qrcode-{{ $session->id }}"), {
                                text: "{{ $qrText }}",
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

                            console.log('QR URL for session {{ $session->id }}: {{ $qrText }}');
                        </script>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
