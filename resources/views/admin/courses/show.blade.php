@extends('layouts.app')

@section('title', 'Course Details')
@section('role', 'Admin')
@section('page-title', 'Course Details')
@section('welcome-text', 'View course information')

@section('sidebar')
    <div class="nav-label">Management</div>
    <a href="/admin/dashboard" class="nav-item @if (request()->routeIs('admin.dashboard')) active @endif">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
    </a>
    <a href="/admin/users" class="nav-item @if (request()->routeIs('admin.users')) active @endif">
        <i class="bi bi-people"></i><span>User Management</span>
    </a>
    <a href="/admin/departments" class="nav-item @if (request()->routeIs('admin.departments.*')) active @endif">
        <i class="bi bi-building"></i><span>Departments</span>
    </a>
    <a href="/admin/courses" class="nav-item @if (request()->routeIs('admin.courses.*')) active @endif">
        <i class="bi bi-book"></i><span>Course Management</span>
    </a>
    <div class="nav-label">Analytics</div>
    <a href="#" class="nav-item"><i class="bi bi-calendar"></i><span>Semesters</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up"></i><span>Analytics</span></a>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Reports</span></a>
@endsection

@section('content')
    <style>
        .course-detail-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .detail-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .detail-header {
            background: #f9fafb;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-header i {
            color: #800000;
            font-size: 1.2rem;
        }

        .detail-body {
            padding: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .info-item {
            margin-bottom: 1rem;
        }

        .info-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 500;
            color: #1f2937;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .schedule-time {
            font-family: monospace;
            background: #f3f4f6;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            display: inline-block;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .btn-back {
            background: #6b7280;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .btn-delete {
            background: #dc2626;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .btn-edit:hover {
            background: #d97706;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        .btn-delete:hover {
            background: #b91c1c;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
        }

        .students-table th,
        .students-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .students-table th {
            background: #f9fafb;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .detail-body {
                padding: 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons a,
            .action-buttons form {
                width: 100%;
            }

            .btn-edit,
            .btn-back,
            .btn-delete {
                justify-content: center;
                width: 100%;
            }
        }
    </style>

    <div class="course-detail-container">
        <!-- Course Information Card -->
        <div class="detail-card">
            <div class="detail-header">
                <i class="bi bi-book"></i>
                Course Information
            </div>
            <div class="detail-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Course Code</div>
                        <div class="info-value">{{ $course->course_code }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Course Name</div>
                        <div class="info-value">{{ $course->course_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Department</div>
                        <div class="info-value">{{ $course->department->name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Lecturer</div>
                        <div class="info-value">{{ $course->lecturer_name ?? 'Not Assigned' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Credits</div>
                        <div class="info-value">{{ $course->credits }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Year</div>
                        <div class="info-value">{{ $course->year ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Semester</div>
                        <div class="info-value">{{ $course->semester ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Academic Year</div>
                        <div class="info-value">{{ $course->academic_year ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Room</div>
                        <div class="info-value">{{ $course->room ?? 'Not Assigned' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            @if ($course->is_active)
                                <span class="badge-active">Active</span>
                            @else
                                <span class="badge-inactive">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Schedule</div>
                        <div class="info-value">
                            @if ($course->schedule_day && $course->schedule_time && $course->schedule_end_time)
                                <span class="schedule-time">
                                    {{ $course->schedule_day }}
                                    {{ \Carbon\Carbon::parse($course->schedule_time)->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($course->schedule_end_time)->format('g:i A') }}
                                </span>
                            @else
                                TBA
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Students Card -->
        <div class="detail-card">
            <div class="detail-header">
                <i class="bi bi-people"></i>
                Enrolled Students ({{ $course->enrollments ? $course->enrollments->count() : 0 }})
            </div>
            <div class="detail-body">
                @if ($course->enrollments && $course->enrollments->count() > 0)
                    <div style="overflow-x: auto;">
                        <table class="students-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student Name</th>
                                    <th>Student ID</th>
                                    <th>Email</th>
                                    <th>Enrollment Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($course->enrollments as $index => $enrollment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $enrollment->student->name ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->student->student_id ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->student->email ?? 'N/A' }}</td>
                                        <td>{{ $enrollment->created_at ? $enrollment->created_at->format('d M Y') : 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge-active">Enrolled</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-person-x" style="font-size: 2rem; color: #9ca3af;"></i>
                        <p>No students enrolled in this course yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('admin.courses.index') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Back to Courses
            </a>
            <a href="{{ route('admin.courses.edit', $course) }}" class="btn-edit">
                <i class="bi bi-pencil"></i> Edit Course
            </a>
            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.')"
                style="display: inline-block; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">
                    <i class="bi bi-trash"></i> Delete Course
                </button>
            </form>
        </div>
    </div>
@endsection
