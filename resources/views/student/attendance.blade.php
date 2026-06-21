@extends('layouts.app')

@section('title', 'My Attendance')
@section('role', 'Student')
@section('page-title', 'My Attendance')
@section('welcome-text', 'View your attendance records')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 16px 20px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
        }

        .stat-card .number.present {
            color: #10b981;
        }

        .stat-card .number.late {
            color: #f59e0b;
        }

        .stat-card .number.absent {
            color: #ef4444;
        }

        .stat-card .number.total {
            color: #3b82f6;
        }

        .stat-card .label {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .card {
            background: white;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            padding: 14px 18px;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            color: #1f2937;
        }

        .card-body {
            padding: 18px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            text-align: left;
            padding: 10px 12px;
            background: #f9fafb;
            color: #374151;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .badge-status {
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-status.present {
            background: #dcfce7;
            color: #166534;
        }

        .badge-status.late {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-status.absent {
            background: #fee2e2;
            color: #991b1b;
        }

        .course-summary-item {
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .course-summary-item:last-child {
            border-bottom: none;
        }

        .pagination-container {
            margin-top: 16px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="number present">{{ $presentSessions ?? 0 }}</div>
            <div class="label">Present</div>
        </div>
        <div class="stat-card">
            <div class="number late">{{ $lateSessions ?? 0 }}</div>
            <div class="label">Late</div>
        </div>
        <div class="stat-card">
            <div class="number absent">{{ $absentSessions ?? 0 }}</div>
            <div class="label">Absent</div>
        </div>
        <div class="stat-card">
            <div class="number total">{{ $totalSessions ?? 0 }}</div>
            <div class="label">Total Sessions</div>
        </div>
    </div>

    <!-- Course Summary -->
    @if (isset($courseSummary) && $courseSummary->count() > 0)
        <div class="card">
            <div class="card-header">Course Summary</div>
            <div class="card-body">
                @foreach ($courseSummary as $course)
                    <div class="course-summary-item">
                        <div>
                            <strong>{{ $course->course_code }}</strong>
                            <span style="color:#6b7280; font-size:13px; margin-left:8px;">{{ $course->course_name }}</span>
                        </div>
                        <div>
                            <span style="color:#10b981;">P: {{ $course->attended }}</span>
                            <span style="color:#ef4444; margin-left:8px;">A: {{ $course->absent }}</span>
                            <span
                                class="badge-status {{ $course->percentage >= 75 ? 'present' : ($course->percentage >= 60 ? 'late' : 'absent') }}"
                                style="margin-left:8px;">
                                {{ $course->percentage }}%
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Attendance Records -->
    <div class="card">
        <div class="card-header">
            <span>Attendance Records</span>
            <span style="font-size:12px; color:#6b7280; font-weight:400;">Total: {{ $totalSessions ?? 0 }} records</span>
        </div>
        <div class="card-body">
            @if ($records && $records->count() > 0)
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $record)
                                <tr>
                                    <td>{{ $record->created_at->format('d M Y') }}</td>
                                    <td>
                                        <strong>{{ $record->session->course->course_code ?? 'N/A' }}</strong>
                                        <br>
                                        <small
                                            style="color:#6b7280;">{{ $record->session->course->course_name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge-status {{ $record->status }}">
                                            {{ ucfirst($record->status) }}
                                            @if ($record->is_manual)
                                                <span style="font-size:9px; opacity:0.6; margin-left:4px;">(Manual)</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $record->created_at->format('h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    {{ $records->links() }}
                </div>
            @else
                <div style="text-align:center; padding:30px; color:#9ca3af;">
                    <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:12px;"></i>
                    <p>No attendance records found</p>
                    <a href="{{ route('student.scan') }}" style="color:#800000; text-decoration:none; font-weight:500;">
                        Scan QR to mark attendance →
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
