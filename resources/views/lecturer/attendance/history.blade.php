@extends('layouts.app')

@section('title', 'Attendance History')
@section('role', 'Lecturer')
@section('page-title', 'Session History')
@section('welcome-text', 'View all attendance sessions and records')

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('lecturer.dashboard') }}" class="nav-item">
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
    <a href="{{ route('lecturer.attendance.history') }}" class="nav-item active">
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
        .history-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .history-header {
            background: linear-gradient(135deg, #800000 0%, #6b0000 100%);
            padding: 1rem;
            color: white;
        }

        .history-body {
            padding: 1.5rem;
        }

        .session-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .session-summary-item {
            font-size: 0.9rem;
        }

        .session-summary-item strong {
            color: #800000;
        }

        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .table th {
            background: #f9fafb;
            padding: 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }

        .table td {
            padding: 0.75rem;
            border-bottom: 1px solid #f0f2f4;
        }

        .table tr:hover {
            background: #f9fafb;
        }

        .badge-present {
            background: #dcfce7;
            color: #166534;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-late {
            background: #fef3c7;
            color: #92400e;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-absent {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-manual {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.15rem 0.5rem;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 600;
            margin-left: 0.3rem;
        }

        .notes-text {
            color: #6b7280;
            font-style: italic;
            font-size: 0.85rem;
            max-width: 200px;
            word-wrap: break-word;
        }

        .pagination-container {
            margin-top: 1.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            display: block;
            margin-bottom: 1rem;
        }
    </style>

    <div>
        <div class="history-card">
            <div class="history-header">
                <h5><i class="bi bi-clock-history"></i> Attendance Session History</h5>
            </div>
            <div class="history-body">
                @if ($sessions && $sessions->count() > 0)
                    @foreach ($sessions as $session)
                        <div class="session-summary">
                            <div class="session-summary-item">
                                <strong>Course:</strong> {{ $session->course->course_name ?? 'N/A' }}
                            </div>
                            <div class="session-summary-item">
                                <strong>Date:</strong>
                                {{ $session->session_date ? \Carbon\Carbon::parse($session->session_date)->format('M d, Y') : 'N/A' }}
                            </div>
                            <div class="session-summary-item">
                                <strong>Time:</strong>
                                {{ $session->start_time ? \Carbon\Carbon::parse($session->start_time)->format('h:i A') : 'N/A' }}
                            </div>
                            <div class="session-summary-item">
                                <strong>Status:</strong>
                                <span class="badge {{ $session->status == 'active' ? 'badge-present' : 'badge-absent' }}">
                                    {{ ucfirst($session->status) }}
                                </span>
                            </div>
                            <div class="session-summary-item">
                                <strong>Present:</strong> {{ $session->present_count ?? 0 }}
                            </div>
                            <div class="session-summary-item">
                                <strong>Late:</strong> {{ $session->late_count ?? 0 }}
                            </div>
                            <div class="session-summary-item">
                                <strong>Absent:</strong> {{ $session->absent_count ?? 0 }}
                            </div>
                            <div class="session-summary-item">
                                <strong>Manual Code:</strong> {{ $session->session_code ?? 'N/A' }}
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Scanned At</th>
                                        <th>Method</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($session->records && $session->records->count() > 0)
                                        @foreach ($session->records as $record)
                                            <tr>
                                                <td>
                                                    <strong>{{ $record->student->name ?? 'Unknown' }}</strong>
                                                </td>
                                                <td>{{ $record->student->email ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge-{{ $record->status }}">
                                                        {{ ucfirst($record->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('M d, Y h:i A') : 'N/A' }}
                                                </td>
                                                <td>
                                                    @if ($record->is_manual)
                                                        <span class="badge-manual">Manual</span>
                                                    @else
                                                        <span style="color: #10b981;">QR Scan</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="notes-text">
                                                        {{ $record->notes ?? '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" style="text-align: center; color: #9ca3af; padding: 1.5rem;">
                                                No attendance records for this session
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <hr style="margin: 1rem 0;">
                    @endforeach

                    <div class="pagination-container">
                        {{ $sessions->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No attendance sessions found</p>
                        <a href="{{ route('lecturer.attendance.take') }}" class="btn-custom"
                            style="display: inline-block; margin-top: 1rem; background: #800000; color: white; padding: 0.5rem 1.5rem; border-radius: 0.5rem; text-decoration: none;">
                            Create First Session
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
