@extends('layouts.app')

@section('title', 'Department Details')
@section('role', 'Admin')
@section('page-title', 'Department Details')
@section('welcome-text', 'View department information and associated courses')

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
        .detail-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .detail-header {
            padding: 1rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 700;
            color: #800000;
        }

        .detail-body {
            padding: 1.5rem;
        }

        .detail-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-label {
            width: 200px;
            font-weight: 600;
            color: #374151;
        }

        .detail-value {
            flex: 1;
            color: #6b7280;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-block;
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            .detail-row {
                flex-direction: column;
            }

            .detail-label {
                width: 100%;
                margin-bottom: 0.25rem;
            }
        }
    </style>

    <div style="max-width: 1000px; margin: 0 auto;">
        <div class="detail-card">
            <div class="detail-header">
                <i class="bi bi-building"></i> Department Information
            </div>
            <div class="detail-body">
                <div class="detail-row">
                    <div class="detail-label">Department Code:</div>
                    <div class="detail-value"><strong>{{ $department->code }}</strong></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Department Name:</div>
                    <div class="detail-value">{{ $department->name }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Head of Department:</div>
                    <div class="detail-value">{{ $department->head_of_department ?? 'Not Assigned' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Description:</div>
                    <div class="detail-value">{{ $department->description ?? 'No description' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Created:</div>
                    <div class="detail-value">{{ $department->created_at->format('F j, Y') }}</div>
                </div>
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-header">
                <i class="bi bi-book"></i> Courses in this Department
            </div>
            <div class="detail-body">
                @if ($department->courses()->count() > 0)
                    <ul style="margin-left: 1.5rem;">
                        @foreach ($department->courses as $course)
                            <li style="margin-bottom: 0.5rem;">
                                <strong>{{ $course->course_code }}</strong> - {{ $course->course_name }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: #6b7280;">No courses assigned to this department yet.</p>
                @endif
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1rem;">
            <a href="{{ route('admin.departments.edit', $department) }}" class="btn-action"
                style="background: #f59e0b; color: white;">Edit Department</a>
            <a href="{{ route('admin.departments.index') }}" class="btn-action"
                style="background: #6b7280; color: white;">Back to Departments</a>
        </div>
    </div>
@endsection
