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

        .qr-container {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }

        .qr-container-semester {
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: var(--white);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }

        .qr-box {
            background: var(--white);
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
            color: var(--white);
        }

        .mode-selector {
            background: var(--white);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
        }

        .btn-custom {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-custom:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 6px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
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
            color: var(--text-gray);
        }

        .download-btn {
            display: inline-block;
            background: var(--success);
            color: var(--white);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-top: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .download-btn:hover {
            background: #059669;
            color: var(--white);
        }

        .live-attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .live-attendance-table thead {
            background: var(--bg-main);
        }

        .live-attendance-table th {
            padding: 8px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-gray);
            font-weight: 600;
            border-bottom: 2px solid rgba(10, 36, 99, 0.06);
        }

        .live-attendance-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .live-attendance-table tbody tr {
            transition: all 0.3s ease;
        }

        .live-attendance-table tbody tr:hover {
            background: var(--bg-main);
        }

        .live-attendance-table .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-badge.present {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.late {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.absent {
            background: #fee2e2;
            color: #991b1b;
        }

        .live-attendance-table .new-row {
            animation: highlightRow 1.5s ease;
        }

        @keyframes highlightRow {
            0% {
                background: #fef3c7;
            }

            50% {
                background: #fde68a;
            }

            100% {
                background: transparent;
            }
        }

        .live-badge {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 1.5s infinite;
            margin-right: 6px;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(0.8);
            }
        }

        .attendance-counter {
            font-size: 14px;
            color: var(--text-gray);
        }

        .attendance-counter strong {
            color: var(--text-dark);
        }

        .btn-back-to-form {
            background: var(--text-gray);
            color: var(--white);
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back-to-form:hover {
            background: #4b5563;
            color: var(--white);
            transform: translateY(-1px);
        }

        .btn-deactivate {
            background: var(--danger);
            color: var(--white);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-deactivate:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }

        .btn-regenerate {
            background: var(--warning);
            color: var(--white);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-regenerate:hover {
            background: #d97706;
            transform: translateY(-1px);
        }

        .top-actions {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 15px;
        }

        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            border-left: 4px solid var(--info);
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            border-left: 4px solid var(--success);
            font-size: 14px;
        }

        .qr-list {
            margin-top: 10px;
        }

        .qr-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            background: var(--bg-main);
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 8px;
            transition: all 0.2s;
        }

        .qr-item:hover {
            background: #e8edf5;
        }

        .qr-item .course-info .name {
            font-weight: 600;
            color: var(--text-dark);
        }

        .qr-item .course-info .code {
            font-size: 12px;
            color: var(--text-gray);
        }

        .qr-item .status-badge-sm {
            font-size: 11px;
            padding: 2px 10px;
            border-radius: 12px;
            font-weight: 600;
        }

        .status-badge-sm.active {
            background: #dcfce7;
            color: #166534;
        }

        .btn-view-qr {
            background: var(--primary-light);
            color: var(--white);
            border: none;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-view-qr:hover {
            background: var(--primary);
            color: var(--white);
        }

        .btn-end-qr {
            background: var(--danger);
            color: var(--white);
            border: none;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-end-qr:hover {
            background: #b91c1c;
            color: var(--white);
        }

        .qr-badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
        }

        .qr-badge.semester {
            background: #dbeafe;
            color: #1e40af;
        }

        .qr-badge.dynamic {
            background: #fef3c7;
            color: #92400e;
        }

        .empty-state {
            text-align: center;
            padding: 15px;
            color: var(--text-gray);
            font-size: 13px;
        }

        .empty-state i {
            font-size: 24px;
            display: block;
            margin-bottom: 8px;
            color: #d1d5db;
        }

        .mode-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .mode-badge.dynamic {
            background: rgba(255, 255, 255, 0.2);
            color: #fcd34d;
        }

        .mode-badge.semester {
            background: rgba(255, 255, 255, 0.2);
            color: #93c5fd;
        }

        .rollcall-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-top: 10px;
        }

        .rollcall-box {
            background: var(--bg-main);
            border-radius: 8px;
            padding: 8px;
            text-align: center;
            border: 1px solid rgba(10, 36, 99, 0.06);
            transition: all 0.3s ease;
        }

        .rollcall-box:hover {
            border-color: var(--primary);
        }

        .rollcall-box .value {
            font-size: 18px;
            font-weight: 800;
            color: var(--primary);
        }

        .rollcall-box .value.high {
            color: var(--success);
        }

        .rollcall-box .value.medium {
            color: var(--warning);
        }

        .rollcall-box .value.low {
            color: var(--danger);
        }

        .rollcall-box .label {
            font-size: 10px;
            color: var(--text-gray);
            font-weight: 500;
        }

        .eligibility-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        .eligibility-badge.eligible {
            background: #dcfce7;
            color: #166534;
        }

        .eligibility-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .eligibility-badge.not_eligible {
            background: #fee2e2;
            color: #991b1b;
        }

        .risk-badge-sm {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .risk-badge-sm.low {
            background: #dcfce7;
            color: #166534;
        }

        .risk-badge-sm.medium {
            background: #fef3c7;
            color: #92400e;
        }

        .risk-badge-sm.high {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .rollcall-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .qr-box img {
                width: 150px;
                height: 150px;
            }

            .manual-code {
                font-size: 18px;
            }

            .live-attendance-table {
                font-size: 11px;
            }

            .live-attendance-table th,
            .live-attendance-table td {
                padding: 4px 6px;
            }

            .top-actions {
                justify-content: center;
            }

            .qr-item {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }
        }
    </style>

    @if (session('info'))
        <div class="alert-info">
            <i class="bi bi-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert-success">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            @if ($activeSession && !$showCreateForm)
                <!-- Active session display – unchanged -->
                <div class="top-actions">
                    <a href="{{ route('lecturer.attendance.take') }}?back=1" class="btn-back-to-form">
                        <i class="bi bi-arrow-left"></i> Back to Create New QR
                    </a>
                </div>

                @if ($activeSession->qr_mode == 'semester')
                    <!-- Semester QR display – unchanged -->
                    <div class="qr-container-semester">
                        <span class="mode-badge semester"><i class="bi bi-book"></i> Semester QR (Static)</span>
                        <h4><i class="bi bi-qr-code"></i> Semester QR Code</h4>
                        <p style="font-size: 12px;">Same QR for whole semester - put on PowerPoint once</p>
                        <div class="qr-box">
                            @php
                                $semesterQrText =
                                    route('student.scan.semester') .
                                    '?token=' .
                                    $activeSession->course->semester_qr_token .
                                    '&course=' .
                                    $activeSession->course->id;
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($semesterQrText) }}"
                                alt="Semester QR">
                            <div>
                                <a href="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($semesterQrText) }}"
                                    download="semester-qr.png" class="download-btn">Download QR Code</a>
                            </div>
                        </div>
                        <div>
                            <p><strong>Course:</strong> {{ $activeSession->course->course_name ?? 'N/A' }}</p>
                            <p><strong>Room:</strong> {{ $activeSession->room ?? 'Not specified' }}</p>
                            <p style="font-size: 12px; opacity: 0.8;">
                                <i class="bi bi-info-circle"></i>
                                Students can scan anytime during the semester
                            </p>
                            <p style="margin-top: 10px;">
                                <strong>Manual Code:</strong>
                            </p>
                            <div class="manual-code">{{ $activeSession->manual_code }}</div>
                            <p style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                                <i class="bi bi-info-circle"></i> Students can enter this code manually if they can't scan
                            </p>
                        </div>
                        <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <form method="POST"
                                action="{{ route('lecturer.course.regenerate-semester-qr', $activeSession->course->id) }}"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-regenerate">
                                    Regenerate QR
                                </button>
                            </form>
                            <form method="POST"
                                action="{{ route('lecturer.attendance.sessions.end', $activeSession->id) }}"
                                style="display: inline;"
                                onsubmit="return confirm('End this semester QR? Students will no longer be able to scan.')">
                                @csrf
                                <button type="submit" class="btn-deactivate">
                                    End QR
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Dynamic QR display – unchanged -->
                    <div class="qr-container">
                        <span class="mode-badge dynamic"><i class="bi bi-arrow-repeat"></i> Session QR (Dynamic)</span>
                        <h4><i class="bi bi-qr-code"></i> Dynamic QR Code</h4>
                        <p style="font-size: 12px;">Changes every session - expires in {{ $activeSession->duration }}
                            minutes</p>
                        <div class="qr-box">
                            @php
                                $dynamicQrText =
                                    route('student.scan.process') .
                                    '?token=' .
                                    $activeSession->session_token .
                                    '&session=' .
                                    $activeSession->id;
                            @endphp
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($dynamicQrText) }}"
                                alt="Dynamic QR">
                            <div>
                                <a href="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($dynamicQrText) }}"
                                    download="dynamic-qr.png" class="download-btn">Download QR Code</a>
                            </div>
                        </div>
                        <div>
                            <p><strong>Course:</strong> {{ $activeSession->course->course_name ?? 'N/A' }}</p>
                            <p><strong>Room:</strong> {{ $activeSession->room ?? 'Not specified' }}</p>
                            <p><strong>Manual Code:</strong></p>
                            <div class="manual-code">{{ $activeSession->manual_code }}</div>
                            <p style="font-size: 12px; opacity: 0.8; margin-top: 5px;">
                                <i class="bi bi-info-circle"></i> Students can enter this code manually if they can't scan
                            </p>
                            <div class="countdown"><span id="countdownTimer"></span></div>
                        </div>
                        <div style="margin-top: 15px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                            <form method="POST"
                                action="{{ route('lecturer.attendance.sessions.end', $activeSession->id) }}"
                                style="display: inline;" onsubmit="return confirm('End this session?')">
                                @csrf
                                <button type="submit" class="btn-custom" style="background: var(--danger);">
                                    End Session
                                </button>
                            </form>
                            <a href="{{ route('lecturer.attendance.sessions.refresh', $activeSession->id) }}"
                                class="btn-custom" style="background: var(--secondary); text-decoration: none;">
                                Refresh QR
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Live Attendance Statistics – unchanged -->
                <div class="mode-selector">
                    <h5 style="color: var(--primary);"><i class="bi bi-graph-up"></i> Live Attendance Statistics</h5>
                    <div class="stats-grid">
                        <div>
                            <div class="stat-number" style="color:var(--success);" id="livePresentCount">
                                {{ $activeSession->present_count ?? 0 }}
                            </div>
                            <div class="stat-label">Present</div>
                        </div>
                        <div>
                            <div class="stat-number" style="color:var(--warning);" id="liveLateCount">
                                {{ $activeSession->late_count ?? 0 }}
                            </div>
                            <div class="stat-label">Late</div>
                        </div>
                        <div>
                            <div class="stat-number" style="color:var(--danger);" id="liveAbsentCount">
                                {{ max(0, ($activeSession->total_students ?? 0) - ($activeSession->present_count ?? 0) - ($activeSession->late_count ?? 0)) }}
                            </div>
                            <div class="stat-label">Absent</div>
                        </div>
                        <div>
                            <div class="stat-number" style="color:var(--primary);" id="liveTotalCount">
                                {{ $activeSession->total_students ?? 0 }}
                            </div>
                            <div class="stat-label">Total</div>
                        </div>
                    </div>
                </div>

                <div class="mode-selector" style="margin-top: 1rem;">
                    <h5 style="color: var(--primary);">
                        <i class="bi bi-clock-history"></i> Real-Time Attendance
                        <span class="live-badge"></span>
                        <span class="attendance-counter">
                            <strong id="totalScanned">0</strong> students scanned so far
                        </span>
                    </h5>
                    <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
                        <table class="live-attendance-table" id="liveAttendanceTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                @php
                                    $existingRecords = $activeSession->records ?? collect();
                                    $counter = 0;
                                @endphp
                                @if ($existingRecords && $existingRecords->count() > 0)
                                    @foreach ($existingRecords as $record)
                                        @php
                                            $counter++;
                                            $statusClass = $record->status ?? 'absent';
                                        @endphp
                                        <tr id="attendance-row-{{ $record->id }}">
                                            <td>{{ $counter }}</td>
                                            <td>
                                                <strong>{{ $record->student->name ?? 'Unknown' }}</strong>
                                                <br>
                                                <small
                                                    style="color: var(--text-gray); font-size: 10px;">{{ $record->student->email ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="status-badge {{ $statusClass }}">
                                                    {{ ucfirst($statusClass) }}
                                                </span>
                                            </td>
                                            <td style="font-size: 12px; color: var(--text-gray);">
                                                {{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('h:i:s A') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if ($record->is_manual)
                                                    <span
                                                        style="font-size: 10px; background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px;">Manual</span>
                                                @else
                                                    <span
                                                        style="font-size: 10px; background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 4px;">QR
                                                        Scan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 20px; color: #9ca3af;">
                                            <i class="bi bi-inbox"
                                                style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                                            No students have scanned yet
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- QR Creation Form -->
                <div class="mode-selector">
                    <h5 style="color: var(--primary); margin-bottom: 15px;">
                        <i class="bi bi-sliders2"></i> Select QR Mode
                    </h5>

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
                                style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid rgba(10, 36, 99, 0.12); border-radius: 8px; margin-bottom: 10px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="qr_mode" value="session" checked>
                                <span>
                                    <strong>Session QR (Dynamic)</strong><br>
                                    <small style="color: var(--text-gray);">New QR every session - expires after set
                                        time</small>
                                </span>
                            </label>
                            <label
                                style="display: flex; align-items: center; gap: 10px; padding: 10px; border: 1px solid rgba(10, 36, 99, 0.12); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" name="qr_mode" value="semester">
                                <span>
                                    <strong>Semester QR (Static)</strong><br>
                                    <small style="color: var(--text-gray);">Same QR all semester - put on PowerPoint
                                        once</small>
                                </span>
                            </label>
                        </div>

                        {{-- ⭐ Number of Class Periods (for attendance calculation) --}}
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label style="font-weight: 600; color: var(--text-dark); display: block; margin-bottom: 4px;">
                                Number of Class Periods
                            </label>
                            <select name="period_count" class="form-control" required>
                                <option value="1">1 period (50 min)</option>
                                <option value="2">2 periods (1h 40m)</option>
                                <option value="3">3 periods (2h 30m)</option>
                                <option value="4" selected>4 periods (3h 20m)</option>
                                <option value="5">5 periods (4h 10m)</option>
                                <option value="6">6 periods (5h)</option>
                                <option value="7">7 periods (5h 50m)</option>
                                <option value="8">8 periods (6h 40m)</option>
                            </select>
                            {{-- <small style="color: var(--text-gray); font-size: 12px;">
                                <i class="bi bi-info-circle"></i>
                                How many class periods (50 min each) does this session cover?
                                This affects attendance calculation.
                            </small> --}}
                        </div>

                        {{-- ⭐ QR Duration (how long QR is active for scanning) --}}
                        <div id="durationField">
                            <label style="font-weight: 600; color: var(--text-dark); display: block; margin-bottom: 4px;">
                                QR Active Duration
                            </label>
                            <select name="duration" class="form-control" required>
                                <option value="15">15 minutes</option>
                                <option value="30" selected>30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                                <option value="90">90 minutes</option>
                                <option value="120">120 minutes</option>
                            </select>
                            {{-- <small style="color: var(--text-gray); font-size: 12px;">
                                <i class="bi bi-info-circle"></i>
                                How long will the QR code be available for students to scan?
                            </small> --}}
                        </div>

                        <input type="text" name="room" class="form-control" placeholder="Room (optional)">

                        <button type="submit" class="btn-custom" style="width: 100%; margin-top: 10px;">
                            Start QR Session
                        </button>
                    </form>
                </div>

                @if (isset($allActiveSessions) && $allActiveSessions->count() > 0)
                    <div class="mode-selector"
                        style="margin-top: 16px; background: var(--bg-main); border-color: var(--primary);">
                        <h5 style="color: var(--primary);">
                            <i class="bi bi-qr-code"></i> Your Active QR Sessions
                        </h5>
                        <p style="font-size: 12px; color: var(--text-gray); margin-bottom: 12px;">
                            You have {{ $allActiveSessions->count() }} active QR session(s).
                            Click "View" to switch between them.
                        </p>
                        <div class="qr-list">
                            @foreach ($allActiveSessions as $session)
                                <div class="qr-item">
                                    <div class="course-info">
                                        <span class="name">{{ $session->course->course_name ?? 'Unknown' }}</span>
                                        <span class="code">
                                            {{ $session->course->course_code ?? 'N/A' }}
                                            Room: {{ $session->room ?? 'N/A' }}
                                            <span
                                                class="qr-badge {{ $session->qr_mode == 'semester' ? 'semester' : 'dynamic' }}">
                                                {{ $session->qr_mode == 'semester' ? ' Semester' : ' Dynamic' }}
                                            </span>
                                        </span>
                                        @if ($activeSession && $activeSession->id == $session->id)
                                            <span style="font-size: 10px; color: var(--success); font-weight: 600;">
                                                <i class="bi bi-check-circle"></i> Currently viewing
                                            </span>
                                        @endif
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                        <span class="status-badge-sm active"> Active</span>
                                        @if (!$activeSession || $activeSession->id != $session->id)
                                            <a href="{{ route('lecturer.attendance.take') }}?session={{ $session->id }}"
                                                class="btn-view-qr">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        @else
                                            <span style="font-size: 11px; color: var(--success); font-weight: 600;">
                                                <i class="bi bi-check-circle"></i> Active
                                            </span>
                                        @endif
                                        <form method="POST"
                                            action="{{ route('lecturer.attendance.sessions.end', $session->id) }}"
                                            style="display: inline;"
                                            onsubmit="return confirm('End this QR session? Students will no longer be able to scan this QR.')">
                                            @csrf
                                            <button type="submit" class="btn-end-qr">
                                                <i class="bi bi-stop-circle"></i> End
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mode-selector" style="margin-top: 16px; background: var(--bg-main);">
                        <h5 style="color: var(--primary);">
                            <i class="bi bi-qr-code"></i> Your Active QR Sessions
                        </h5>
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p>No active QR sessions found.</p>
                            <p style="font-size: 11px;">Create one using the form above.</p>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <div class="col-md-6">
            <div class="mode-selector">
                <h5 style="color: var(--primary);"><i class="bi bi-pencil-square"></i> Manual Attendance Entry</h5>
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

                    <div style="margin-top: 10px;">
                        <label style="font-size: 12px; color: var(--text-gray);">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2"
                            placeholder="e.g., Student arrived 15 minutes late due to traffic" style="resize: vertical;"></textarea>
                    </div>

                    <button type="submit" class="btn-custom" style="width: 100%; margin-top: 10px;">
                        Save Manual Attendance
                    </button>
                </form>
            </div>

            <div class="mode-selector" style="text-align: center; background: var(--bg-main);">
                <a href="{{ route('lecturer.attendance.sessions') }}" class="btn-custom"
                    style="width: 100%; display: inline-block; padding: 8px; background: transparent; color: var(--primary); border: 1px solid var(--primary); border-radius: 6px; text-decoration: none; transition: all 0.3s ease; text-align: center;"
                    onmouseover="this.style.background='var(--primary)'; this.style.color='white';"
                    onmouseout="this.style.background='transparent'; this.style.color='var(--primary)';">
                    <i class="bi bi-clock-history"></i> View Full Session History
                </a>
                <small class="text-muted"
                    style="display: block; margin-top: 8px; color: var(--text-gray); font-size: 12px;">
                    View all past sessions, attendance statistics, and detailed reports
                </small>
            </div>
        </div>
    </div>

    <script>
        @if ($activeSession && $activeSession->qr_mode == 'session' && $activeSession->expires_at)
            let expiresAt = new Date('{{ $activeSession->expires_at }}').getTime();

            function updateTimer() {
                let now = new Date().getTime();
                let distance = expiresAt - now;

                if (distance < 0) {
                    document.getElementById('countdownTimer').innerHTML = '⏹ EXPIRED';
                    return;
                }

                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById('countdownTimer').innerHTML = '⏱ Time remaining: ' + minutes + 'm ' + seconds + 's';
            }

            updateTimer();
            setInterval(updateTimer, 1000);
        @endif

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

        document.addEventListener('DOMContentLoaded', function() {
            const selected = document.querySelector('input[name="qr_mode"]:checked');
            if (selected && selected.value === 'semester') {
                const durationField = document.getElementById('durationField');
                if (durationField) {
                    durationField.style.display = 'none';
                }
            }
        });

        @if ($activeSession)
            const sessionId = {{ $activeSession->id }};
            const pollInterval = 3000;

            function fetchLiveAttendance() {
                fetch(`/lecturer/session-stats/${sessionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('livePresentCount').innerText = data.present || 0;
                            document.getElementById('liveLateCount').innerText = data.late || 0;
                            document.getElementById('liveTotalCount').innerText = data.total || 0;
                            const absent = (data.total || 0) - (data.present || 0) - (data.late || 0);
                            document.getElementById('liveAbsentCount').innerText = Math.max(0, absent);

                            const tbody = document.getElementById('attendanceTableBody');
                            if (data.records && data.records.length > 0) {
                                let html = '';
                                data.records.forEach((record, index) => {
                                    const statusClass = record.status || 'absent';
                                    const isManual = record.is_manual || false;
                                    const methodLabel = isManual ? 'Manual' : 'QR Scan';

                                    html += `
                                        <tr id="attendance-row-${index}" class="${index === data.records.length - 1 ? 'new-row' : ''}">
                                            <td>${index + 1}</td>
                                            <td>
                                                <strong>${record.student_name || 'Unknown'}</strong>
                                                <br>
                                                <small style="color: var(--text-gray); font-size: 10px;">${record.student_email || 'N/A'}</small>
                                            </td>
                                            <td>
                                                <span class="status-badge ${statusClass}">
                                                    ${statusClass.charAt(0).toUpperCase() + statusClass.slice(1)}
                                                </span>
                                            </td>
                                            <td style="font-size: 12px; color: var(--text-gray);">
                                                ${record.scanned_at ? new Date(record.scanned_at).toLocaleTimeString() : 'N/A'}
                                            </td>
                                            <td>
                                                <span style="font-size: 10px; background: ${isManual ? '#dbeafe' : '#dcfce7'}; color: ${isManual ? '#1e40af' : '#166534'}; padding: 2px 8px; border-radius: 4px;">
                                                    ${methodLabel}
                                                </span>
                                            </td>
                                        </tr>
                                    `;
                                });

                                if (tbody.querySelector('tr td[colspan]')) {
                                    tbody.innerHTML = html;
                                } else {
                                    const existingRows = tbody.querySelectorAll('tr');
                                    if (data.records.length > existingRows.length) {
                                        const newRecords = data.records.slice(existingRows.length);
                                        let newHtml = '';
                                        newRecords.forEach((record, idx) => {
                                            const globalIndex = existingRows.length + idx;
                                            const statusClass = record.status || 'absent';
                                            const isManual = record.is_manual || false;
                                            const methodLabel = isManual ? 'Manual' : 'QR Scan';
                                            newHtml += `
                                                <tr class="new-row">
                                                    <td>${globalIndex + 1}</td>
                                                    <td>
                                                        <strong>${record.student_name || 'Unknown'}</strong>
                                                        <br>
                                                        <small style="color: var(--text-gray); font-size: 10px;">${record.student_email || 'N/A'}</small>
                                                    </td>
                                                    <td>
                                                        <span class="status-badge ${statusClass}">
                                                            ${statusClass.charAt(0).toUpperCase() + statusClass.slice(1)}
                                                        </span>
                                                    </td>
                                                    <td style="font-size: 12px; color: var(--text-gray);">
                                                        ${record.scanned_at ? new Date(record.scanned_at).toLocaleTimeString() : 'N/A'}
                                                    </td>
                                                    <td>
                                                        <span style="font-size: 10px; background: ${isManual ? '#dbeafe' : '#dcfce7'}; color: ${isManual ? '#1e40af' : '#166534'}; padding: 2px 8px; border-radius: 4px;">
                                                            ${methodLabel}
                                                        </span>
                                                    </td>
                                                </tr>
                                            `;
                                        });
                                        tbody.insertAdjacentHTML('beforeend', newHtml);
                                    } else {
                                        const rows = tbody.querySelectorAll('tr');
                                        rows.forEach((row, idx) => {
                                            const td = row.querySelector('td:first-child');
                                            if (td) td.textContent = idx + 1;
                                        });
                                    }
                                }

                                document.getElementById('totalScanned').innerText = data.records.length;
                            } else {
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 20px; color: #9ca3af;">
                                            <i class="bi bi-inbox" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                                            No students have scanned yet
                                        </td>
                                    </tr>
                                `;
                                document.getElementById('totalScanned').innerText = 0;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching live attendance:', error);
                    });
            }

            fetchLiveAttendance();
            setInterval(fetchLiveAttendance, pollInterval);
        @endif
    </script>
@endsection
