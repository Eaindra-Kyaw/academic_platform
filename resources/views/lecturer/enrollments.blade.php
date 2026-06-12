@extends('layouts.app')

@section('title', 'Enrolled Students')
@section('role', 'Lecturer')
@section('page-title', 'My Students')
@section('welcome-text', 'View students enrolled in your courses')

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('lecturer.dashboard') }}" class="nav-item">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('lecturer.attendance.take') }}" class="nav-item">
        <i class="bi bi-qr-code-scan"></i><span>Take Attendance</span>
    </a>
    <a href="{{ route('lecturer.enrollments.index') }}" class="nav-item active">
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
        .course-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .course-header {
            background: linear-gradient(135deg, #800000 0%, #6b0000 100%);
            padding: 1rem 1.25rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .course-title {
            font-weight: 700;
            font-size: 1rem;
        }

        .student-count {
            background: rgba(255, 215, 0, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
        }

        .students-table th {
            background: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 700;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .students-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            font-size: 0.85rem;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #9ca3af;
        }

        .badge-enrolled {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
        }

        @media (max-width: 768px) {
            .students-table {
                min-width: 500px;
            }
        }
    </style>

    <div>
        <h3 style="color: #800000; margin-bottom: 1.5rem;">👥 My Students</h3>

        @if ($courses->count() > 0)
            @foreach ($courses as $course)
                <div class="course-card">
                    <div class="course-header">
                        <div class="course-title">
                            {{ $course->course_code }} - {{ $course->course_name }}
                        </div>
                        <div class="student-count">
                            <i class="bi bi-people"></i> {{ $course->enrollments->count() }} students
                        </div>
                    </div>
                    <div style="overflow-x: auto;">
                        @if ($course->enrollments->count() > 0)
                            <table class="students-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Student ID</th>
                                        <th>Email</th>
                                        <th>Enrolled Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($course->enrollments as $index => $enrollment)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $enrollment->student->name }}</td>
                                            <td>{{ $enrollment->student->student_id ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->student->email }}</td>
                                            <td>{{ $enrollment->created_at->format('d M Y') }}</td>
                                            <td><span class="badge-enrolled">Enrolled</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <i class="bi bi-person-x" style="font-size: 2rem;"></i>
                                <p>No students enrolled in this course yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state" style="background: white; border-radius: 1rem; padding: 3rem;">
                <i class="bi bi-book" style="font-size: 3rem;"></i>
                <p>You are not assigned to any courses yet.</p>
            </div>
        @endif
    </div>
@endsection
