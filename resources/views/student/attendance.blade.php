@extends('layouts.app')

@section('title', 'My Attendance')
@section('role', 'Student')
@section('page-title', '📊 My Attendance')
@section('welcome-text', 'View your attendance records')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
            font-size: 1.8rem;
            font-weight: 800;
        }

        .stat-number.green {
            color: #10b981;
        }

        .stat-number.yellow {
            color: #f59e0b;
        }

        .stat-number.red {
            color: #ef4444;
        }

        .stat-number.blue {
            color: #3b82f6;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .course-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .course-card-header {
            padding: 0.75rem 1rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .course-card-header .course-code {
            font-weight: 700;
            color: #1f2937;
            font-size: 1rem;
        }

        .course-card-header .course-name {
            font-size: 0.85rem;
            color: #6b7280;
        }

        .course-card-body {
            padding: 0.75rem 1rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            text-align: center;
        }

        .course-stat {
            padding: 0.5rem;
            border-radius: 0.5rem;
            background: #f8fafc;
        }

        .course-stat .value {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .course-stat .label {
            font-size: 0.6rem;
            color: #6b7280;
            text-transform: uppercase;
        }

        .course-stat .value.green {
            color: #10b981;
        }

        .course-stat .value.yellow {
            color: #f59e0b;
        }

        .course-stat .value.red {
            color: #ef4444;
        }

        .badge-eligible {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-risk {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-low {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-medium {
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-high {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .attendance-table th {
            padding: 0.5rem 0.75rem;
            text-align: left;
            background: #f9fafb;
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }

        .attendance-table td {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .attendance-table tr:hover {
            background: #fafafa;
        }

        .status-badge {
            padding: 0.15rem 0.6rem;
            border-radius: 12px;
            font-size: 0.65rem;
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

        .status-badge.manual {
            background: #dbeafe;
            color: #1e40af;
        }

        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.25rem;
        }

        .progress-bar .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .progress-bar .fill.green {
            background: #10b981;
        }

        .progress-bar .fill.yellow {
            background: #f59e0b;
        }

        .progress-bar .fill.red {
            background: #ef4444;
        }

        .overview-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            text-align: center;
        }

        .overview-item .value {
            font-size: 1.5rem;
            font-weight: 800;
        }

        .overview-item .label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .course-card-body {
                grid-template-columns: 1fr 1fr;
            }

            .overview-card {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .course-card-body {
                grid-template-columns: 1fr 1fr;
            }

            .overview-card {
                grid-template-columns: 1fr 1fr;
            }

            .course-card-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <div>
        <!-- Overall Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number blue">{{ $totalSessions ?? 0 }}</div>
                <div class="stat-label">📋 Total Records</div>
            </div>
            <div class="stat-card">
                <div class="stat-number green">{{ $presentSessions ?? 0 }}</div>
                <div class="stat-label">✅ Present</div>
            </div>
            <div class="stat-card">
                <div class="stat-number yellow">{{ $lateSessions ?? 0 }}</div>
                <div class="stat-label">⏰ Late</div>
            </div>
            <div class="stat-card">
                <div class="stat-number red">{{ $absentSessions ?? 0 }}</div>
                <div class="stat-label">❌ Absent</div>
            </div>
        </div>

        <!-- Overall Attendance Overview -->
        <div class="overview-card">
            <div class="overview-item">
                <div
                    class="value {{ ($overallAttendance ?? 0) >= 75 ? 'green' : (($overallAttendance ?? 0) >= 60 ? 'yellow' : 'red') }}">
                    {{ number_format($overallAttendance ?? 0, 1) }}%
                </div>
                <div class="label">Overall Attendance</div>
                <div class="progress-bar">
                    <div class="fill {{ ($overallAttendance ?? 0) >= 75 ? 'green' : (($overallAttendance ?? 0) >= 60 ? 'yellow' : 'red') }}"
                        style="width: {{ min($overallAttendance ?? 0, 100) }}%;"></div>
                </div>
            </div>
            <div class="overview-item">
                <div class="value">{{ number_format($overallRollCall ?? 0, 1) }}/10</div>
                <div class="label">Overall Roll Call</div>
            </div>
            <div class="overview-item">
                <div class="value">
                    @php
                        $eligibilityStatus =
                            ($overallAttendance ?? 0) >= 75
                                ? '✅ Eligible'
                                : (($overallAttendance ?? 0) >= 60
                                    ? '⚠️ Warning'
                                    : '❌ At Risk');
                        $eligibilityClass =
                            ($overallAttendance ?? 0) >= 75
                                ? 'badge-eligible'
                                : (($overallAttendance ?? 0) >= 60
                                    ? 'badge-warning'
                                    : 'badge-risk');
                    @endphp
                    <span class="{{ $eligibilityClass }}">{{ $eligibilityStatus }}</span>
                </div>
                <div class="label">Eligibility Status</div>
            </div>
            <div class="overview-item">
                <div class="value">
                    @php
                        $riskLevel =
                            ($overallAttendance ?? 0) >= 75
                                ? 'Low'
                                : (($overallAttendance ?? 0) >= 60
                                    ? 'Medium'
                                    : 'High');
                        $riskClass = strtolower($riskLevel);
                    @endphp
                    <span class="badge-{{ $riskClass }}">{{ $riskLevel }} Risk</span>
                </div>
                <div class="label">Risk Level</div>
            </div>
        </div>

        <!-- Course Summary Cards -->
        <h5 style="margin-bottom: 0.75rem; font-weight: 700; color: #1f2937;">📚 Course Summary</h5>

        @forelse($courseSummary ?? [] as $course)
            <div class="course-card">
                <div class="course-card-header">
                    <div>
                        <span class="course-code">{{ $course->course_code }}</span>
                        <span class="course-name">{{ $course->course_name }}</span>
                    </div>
                    <div>
                        @php
                            $eligStatus = $course->eligibility_status ?? 'N/A';
                            $eligClass =
                                $eligStatus == 'Eligible'
                                    ? 'badge-eligible'
                                    : ($eligStatus == 'Warning'
                                        ? 'badge-warning'
                                        : 'badge-risk');
                        @endphp
                        <span class="{{ $eligClass }}">{{ $eligStatus }}</span>
                    </div>
                </div>
                <div class="course-card-body">
                    <div class="course-stat">
                        <div
                            class="value {{ ($course->attendance_percentage ?? 0) >= 75 ? 'green' : (($course->attendance_percentage ?? 0) >= 60 ? 'yellow' : 'red') }}">
                            {{ number_format($course->attendance_percentage ?? 0, 1) }}%
                        </div>
                        <div class="label">Attendance</div>
                        <div class="progress-bar">
                            <div class="fill {{ ($course->attendance_percentage ?? 0) >= 75 ? 'green' : (($course->attendance_percentage ?? 0) >= 60 ? 'yellow' : 'red') }}"
                                style="width: {{ min($course->attendance_percentage ?? 0, 100) }}%;"></div>
                        </div>
                    </div>
                    <div class="course-stat">
                        <div class="value">{{ number_format($course->roll_call_mark ?? 0, 1) }}/10</div>
                        <div class="label">Roll Call</div>
                    </div>
                    <div class="course-stat">
                        <div class="value">
                            @php
                                $risk = $course->risk_level ?? 'Low';
                                $riskClass = strtolower($risk);
                            @endphp
                            <span class="badge-{{ $riskClass }}">{{ $risk }}</span>
                        </div>
                        <div class="label">Risk Level</div>
                    </div>
                    <div class="course-stat">
                        <div class="value">{{ $course->sessions ?? 0 }}</div>
                        <div class="label">Sessions</div>
                        <div style="font-size:0.65rem; color:#6b7280;">
                            P: {{ $course->present_count ?? 0 }}
                            L: {{ $course->late_count ?? 0 }}
                            A: {{ $course->absent_count ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="text-align:center; padding:2rem; color:#9ca3af;">
                <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                No attendance records found.
            </div>
        @endforelse

        <!-- Attendance Records Table -->
        <h5 style="margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 700; color: #1f2937;">
            📋 Attendance Records
            <span style="font-size: 0.8rem; font-weight: 400; color: #6b7280;">Total: {{ $records->total() ?? 0 }}
                records</span>
        </h5>

        <div style="overflow-x: auto; background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb;">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records ?? [] as $record)
                        <tr>
                            <td>
                                {{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('d M Y') : 'N/A' }}
                            </td>
                            <td>
                                <strong>{{ $record->session->course->course_code ?? 'N/A' }}</strong>
                                <br>
                                <small
                                    style="color: #6b7280; font-size: 0.7rem;">{{ $record->session->course->course_name ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="status-badge {{ $record->status ?? 'absent' }}">
                                    {{ ucfirst($record->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td style="color: #6b7280; font-size: 0.75rem;">
                                {{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('h:i A') : 'N/A' }}
                            </td>
                            <td>
                                @if ($record->is_manual ?? false)
                                    <span class="status-badge manual">Manual</span>
                                @else
                                    <span style="font-size:0.65rem; color:#6b7280;">QR Scan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:2rem; color:#9ca3af;">
                                <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if (isset($records) && $records->hasPages())
            <div style="margin-top: 1rem; display: flex; justify-content: center;">
                {{ $records->links() }}
            </div>
        @endif
    </div>
@endsection
