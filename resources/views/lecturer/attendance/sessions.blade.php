@extends('layouts.app')

@section('title', 'Active QR Sessions')
@section('role', 'Lecturer')
@section('page-title', '📱 Active QR Sessions')
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
            --primary-gradient: linear-gradient(135deg, #0A2463 0%, #1E3A8A 100%);
            --accent: #D4A017;
            --success: #10b981;
            --success-light: #d1fae5;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --info: #3b82f6;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-lg: 0 10px 40px rgba(10, 36, 99, 0.12);
            --radius: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ============================================================
               STATS ROW
               ============================================================ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }

        .stat-card.total::before {
            background: var(--primary-gradient);
        }

        .stat-card.active::before {
            background: var(--success);
        }

        .stat-card.courses::before {
            background: var(--info);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-icon.total {
            background: rgba(10, 36, 99, 0.08);
            color: var(--primary);
        }

        .stat-icon.active {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success);
        }

        .stat-icon.courses {
            background: rgba(59, 130, 246, 0.12);
            color: var(--info);
        }

        .stat-info .number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .stat-info .number.active {
            color: var(--success);
        }

        .stat-info .label {
            font-size: 0.7rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* ============================================================
               CREATE FORM
               ============================================================ */
        .create-form-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1.75rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .create-form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .create-form-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .create-form-card h4 {
            color: var(--gray-900);
            font-weight: 700;
            margin-bottom: 1.25rem;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .create-form-card h4 i {
            color: var(--primary);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .form-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .form-group select,
        .form-group input {
            padding: 0.6rem 0.9rem;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.85rem;
            background: var(--gray-50);
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            color: var(--gray-800);
            width: 100%;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(10, 36, 99, 0.06);
        }

        .period-section {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--gray-100);
        }

        .period-section label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--gray-700);
            display: block;
            margin-bottom: 0.25rem;
        }

        .period-section .help-text {
            font-size: 0.65rem;
            color: var(--gray-400);
            margin-top: 0.2rem;
        }

        .btn-generate {
            background: var(--primary-gradient);
            color: var(--white);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.2);
            width: 100%;
            justify-content: center;
            margin-top: 0.75rem;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(10, 36, 99, 0.3);
            color: var(--white);
        }

        /* ============================================================
               SESSION CARDS
               ============================================================ */
        .session-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .session-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .session-header {
            background: var(--primary-gradient);
            padding: 1rem 1.5rem;
            color: var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .session-header .title {
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .session-header .title i {
            font-size: 1.2rem;
        }

        .session-header .badge-live {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .session-header .badge-live::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #34d399;
            animation: pulse-dot 1.5s infinite;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .session-body {
            padding: 1.5rem;
        }

        .session-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .session-info .course {
            font-weight: 600;
            color: var(--gray-800);
        }

        .session-info .course small {
            font-weight: 400;
            color: var(--gray-500);
            font-size: 0.8rem;
        }

        .session-info .meta {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .session-info .meta i {
            margin-right: 0.2rem;
        }

        .qr-wrapper {
            display: flex;
            justify-content: center;
            margin: 0.5rem 0 1rem;
        }

        .qr-wrapper img,
        .qr-wrapper canvas {
            border-radius: var(--radius);
            border: 2px solid var(--gray-200);
            padding: 8px;
            background: var(--white);
        }

        .timer-section {
            text-align: center;
            margin: 1rem 0;
        }

        .timer-ring {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto;
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
            color: var(--gray-800);
        }

        .manual-code-label {
            font-size: 0.7rem;
            color: var(--gray-500);
            margin-top: 0.5rem;
        }

        .manual-code-label strong {
            color: var(--primary);
            font-family: monospace;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .attendance-stats {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            font-size: 0.8rem;
            color: var(--gray-500);
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .attendance-stats .stat {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .attendance-stats .stat .num {
            font-weight: 700;
        }

        .attendance-stats .stat .num.present {
            color: var(--success);
        }

        .attendance-stats .stat .num.late {
            color: var(--warning);
        }

        .attendance-stats .stat .num.absent {
            color: var(--danger);
        }

        .attendance-stats .stat .num.total {
            color: var(--primary);
        }

        .session-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.3rem 1rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-family: 'Inter', sans-serif;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
        }

        .btn-sm.end {
            background: var(--danger);
            color: var(--white);
        }

        .btn-sm.end:hover {
            background: #b91c1c;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-sm.refresh {
            background: var(--warning);
            color: var(--white);
        }

        .btn-sm.refresh:hover {
            background: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* ============================================================
               EMPTY STATE
               ============================================================ */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 2px dashed var(--gray-200);
        }

        .empty-state .icon {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state h4 {
            color: var(--gray-800);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .empty-state p {
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        /* ============================================================
               RESPONSIVE
               ============================================================ */
        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-info .number {
                font-size: 1.5rem;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .create-form-card {
                padding: 1.25rem;
            }

            .session-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .session-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .session-actions .btn-sm {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 0.75rem 1rem;
            }

            .stat-info .number {
                font-size: 1.3rem;
            }

            .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .attendance-stats {
                gap: 0.75rem;
                justify-content: center;
            }
        }
    </style>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon total"><i class="bi bi-clock-history"></i></div>
            <div class="stat-info">
                <div class="number">{{ $sessions->count() }}</div>
                <div class="label">Total Sessions</div>
            </div>
        </div>

        <div class="stat-card active">
            <div class="stat-icon active"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-info">
                <div class="number active">{{ $activeSessions->count() }}</div>
                <div class="label">Active Sessions</div>
            </div>
        </div>

        <div class="stat-card courses">
            <div class="stat-icon courses"><i class="bi bi-book-fill"></i></div>
            <div class="stat-info">
                <div class="number">{{ $courses->count() }}</div>
                <div class="label">Your Courses</div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div class="create-form-card">
        <h4><i class="bi bi-plus-circle"></i> Create QR Session</h4>

        <form method="POST" action="{{ route('lecturer.attendance.sessions.create') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Course <span style="color: var(--danger);">*</span></label>
                    <select name="course_id" required>
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Duration <span style="color: var(--danger);">*</span></label>
                    <select name="duration" required>
                        <option value="15">15 minutes</option>
                        <option value="30" selected>30 minutes</option>
                        <option value="45">45 minutes</option>
                        <option value="60">60 minutes</option>
                        <option value="90">90 minutes</option>
                        <option value="120">120 minutes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Room</label>
                    <input type="text" name="room" placeholder="e.g., Room 8-5">
                </div>
            </div>

            <div class="period-section">
                <label>Number of Class Periods</label>
                <select name="period_count"
                    style="width: 100%; padding: 0.6rem 0.9rem; border: 2px solid var(--gray-200); border-radius: 10px; font-size: 0.85rem; background: var(--gray-50); font-family: 'Inter', sans-serif;">
                    <option value="1">1 period (50 min)</option>
                    <option value="2">2 periods (1h 40m)</option>
                    <option value="3">3 periods (2h 30m)</option>
                    <option value="4" selected>4 periods (3h 20m)</option>
                    <option value="5">5 periods (4h 10m)</option>
                    <option value="6">6 periods (5h)</option>
                    <option value="7">7 periods (5h 50m)</option>
                    <option value="8">8 periods (6h 40m)</option>
                </select>
                <div class="help-text">
                    <i class="bi bi-info-circle"></i> How many 50-minute class sessions does this cover?
                </div>
            </div>

            <button type="submit" class="btn-generate">
                <i class="bi bi-qr-code"></i> Generate QR Code
            </button>
        </form>
    </div>

    <!-- Active Sessions -->
    @if ($activeSessions->count() > 0)
        @foreach ($activeSessions as $session)
            @php
                $present = $session->records->where('status', 'present')->count();
                $late = $session->records->where('status', 'late')->count();
                $totalEnrolled = $session->total_students ?? 0;
                if ($totalEnrolled == 0) {
                    $totalEnrolled = \App\Models\Enrollment::where('course_id', $session->course_id)
                        ->where('status', 'approved')
                        ->count();
                }
                $periods = $session->conducted_periods ?? 1;
                $attendedPeriods = ($present + $late) * $periods;
                $totalPeriods = $totalEnrolled * $periods;
                $attendancePercentage = $totalPeriods > 0 ? round(($attendedPeriods / $totalPeriods) * 100) : 0;
                $absent = max(0, $totalEnrolled - $present - $late);
                $qrText =
                    config('app.url') .
                    '/student/scan/process?token=' .
                    $session->session_token .
                    '&session=' .
                    $session->id;
            @endphp

            <div class="session-card">
                <div class="session-header">
                    <div class="title">
                        <i class="bi bi-qr-code"></i>
                        {{ $session->course->course_code }} - {{ $session->course->course_name }}
                    </div>
                    <span class="badge-live">Live</span>
                </div>

                <div class="session-body">
                    <div class="session-info">
                        <div class="course">
                            {{ $session->course->course_name }}
                            <small>{{ $session->course->course_code }}</small>
                        </div>
                        <div class="meta">
                            <i class="bi bi-door-open"></i> {{ $session->room ?? 'TBA' }}
                            <span style="margin-left: 0.75rem;">
                                <i class="bi bi-layers"></i> {{ $periods }} periods
                            </span>
                        </div>
                    </div>

                    <div class="qr-wrapper">
                        <div id="qrcode-{{ $session->id }}"></div>
                    </div>

                    <div class="timer-section">
                        <div class="timer-ring" id="timer-ring-{{ $session->id }}"
                            style="background: conic-gradient(#10b981 0deg 360deg, #e5e7eb 0deg 360deg);">
                            <div class="timer-inner" id="timer-{{ $session->id }}">--</div>
                        </div>
                        <div class="manual-code-label">
                            Manual Code: <strong>{{ $session->session_code }}</strong>
                        </div>
                    </div>

                    <div class="attendance-stats">
                        <span class="stat"><i class="bi bi-check-circle" style="color: var(--success);"></i> <span
                                class="num present">{{ $present }}</span></span>
                        <span class="stat"><i class="bi bi-clock" style="color: var(--warning);"></i> <span
                                class="num late">{{ $late }}</span></span>
                        <span class="stat"><i class="bi bi-x-circle" style="color: var(--danger);"></i> <span
                                class="num absent">{{ $absent }}</span></span>
                        <span class="stat"><i class="bi bi-people" style="color: var(--primary);"></i> <span
                                class="num total">{{ $totalEnrolled }}</span></span>
                        <span class="stat">
                            <i class="bi bi-percent" style="color: var(--info);"></i>
                            <span class="num"
                                style="color: {{ $attendancePercentage >= 75 ? 'var(--success)' : ($attendancePercentage >= 50 ? 'var(--warning)' : 'var(--danger)') }};">
                                {{ $attendancePercentage }}%
                            </span>
                        </span>
                    </div>

                    <div class="session-actions">
                        <form method="POST" action="{{ route('lecturer.attendance.sessions.end', $session->id) }}"
                            style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-sm end" onclick="return confirm('End this session?')">
                                <i class="bi bi-stop-circle"></i> End Session
                            </button>
                        </form>
                        <a href="{{ route('lecturer.attendance.sessions.refresh', $session->id) }}"
                            class="btn-sm refresh">
                            <i class="bi bi-arrow-repeat"></i> Refresh QR
                        </a>
                    </div>
                </div>
            </div>

            <script>
                // Generate QR Code
                new QRCode(document.getElementById("qrcode-{{ $session->id }}"), {
                    text: "{{ $qrText }}",
                    width: 180,
                    height: 180
                });

                // Timer
                let expiresAt{{ $session->id }} = new Date('{{ $session->qr_expires_at }}').getTime();

                function updateTimer{{ $session->id }}() {
                    const now = new Date().getTime();
                    const distance = expiresAt{{ $session->id }} - now;
                    if (distance < 0) {
                        document.getElementById('timer-{{ $session->id }}').innerText = '0:00';
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
    @else
        <div class="empty-state">
            <span class="icon"><i class="bi bi-inbox"></i></span>
            <h4>No Active Sessions</h4>
            <p>You don't have any active QR sessions right now.</p>
            <p style="font-size: 0.8rem; color: var(--gray-400);">
                Use the form above to create a new session.
            </p>
        </div>
    @endif
@endsection
