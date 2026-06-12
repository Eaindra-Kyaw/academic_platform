@extends('layouts.app')

@section('title', 'Manage Departments')
@section('role', 'Admin')
@section('page-title', 'Department Management')
@section('welcome-text', 'Create, edit, and manage university departments')

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
    <a href="{{ route('admin.enrollments.index') }}" class="nav-item @if (request()->routeIs('admin.enrollments.*')) active @endif">
        <i class="bi bi-list-check"></i><span>Enrollments</span>
    </a>
    <div class="nav-label">Analytics</div>
    <a href="#" class="nav-item"><i class="bi bi-calendar"></i><span>Semesters</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up"></i><span>Analytics</span></a>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Reports</span></a>
@endsection

@section('content')
    <style>
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
            border-radius: 0.3rem;
            margin: 0 0.2rem;
        }

        .table-departments {
            width: 100%;
            border-collapse: collapse;
        }

        .table-departments th,
        .table-departments td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-departments th {
            background: #f9fafb;
            font-weight: 600;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 3px solid #10b981;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border-left: 3px solid #dc2626;
        }

        @media (max-width: 768px) {

            .table-departments th,
            .table-departments td {
                padding: 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>

    <div style="max-width: 1400px; margin: 0 auto;">
        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <h3 style="color: #800000; margin: 0;">Departments</h3>
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary"
                style="background: #800000; border: none; padding: 0.5rem 1rem;">
                <i class="bi bi-plus-circle"></i> Add New Department
            </a>
        </div>

        @if (session('success'))
            <div class="alert-success">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-danger">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow-x: auto;">
            <table class="table-departments">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Department Name</th>
                        <th>Head of Department</th>
                        <th>Courses</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $index => $department)
                        <tr>
                            <td>{{ $index + 1 }}
        </div>
        <td><strong>{{ $department->code }}</strong>
    </div>
    <td>{{ $department->name }}</div>
    <td>{{ $department->head_of_department ?? 'Not Assigned' }}</div>
    <td>{{ $department->courses()->count() }}</div>
    <td>
        <a href="{{ route('admin.departments.show', $department) }}" class="btn-action"
            style="background: #3b82f6; color: white; text-decoration: none; display: inline-block;">View</a>
        <a href="{{ route('admin.departments.edit', $department) }}" class="btn-action"
            style="background: #f59e0b; color: white; text-decoration: none; display: inline-block;">Edit</a>
        <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" style="display: inline-block;"
            onsubmit="return confirm('Are you sure you want to delete this department?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-action"
                style="background: #dc2626; color: white; border: none;">Delete</button>
        </form>
        </div>
        </tr>
    @empty
        <tr>
            <td colspan="6" style="text-align: center; padding: 2rem;">No departments found. <a
                    href="{{ route('admin.departments.create') }}">Create one</a></td>
        </tr>
        @endforelse
        </tbody>
        </table>
        </div>
        </div>
    @endsection
