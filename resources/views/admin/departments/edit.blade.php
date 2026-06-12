@extends('layouts.app')

@section('title', 'Edit Department')
@section('role', 'Admin')
@section('page-title', 'Edit Department')
@section('welcome-text', 'Update department information')

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
        .form-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid #e5e7eb;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #800000;
        }

        .btn-submit {
            background: #800000;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-submit:hover {
            background: #5f0000;
        }

        .btn-cancel {
            background: #9ca3af;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .error {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
    </style>

    <div class="form-card">
        <h3 style="color: #800000; margin-bottom: 1.5rem;">Edit Department: {{ $department->name }}</h3>

        <form method="POST" action="{{ route('admin.departments.update', $department) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Department Code *</label>
                <input type="text" name="code" value="{{ old('code', $department->code) }}"
                    placeholder="e.g., CS, ME, CE" required>
                @error('code')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Department Name *</label>
                <input type="text" name="name" value="{{ old('name', $department->name) }}"
                    placeholder="e.g., Department of Computer Engineering" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Head of Department</label>
                <input type="text" name="head_of_department"
                    value="{{ old('head_of_department', $department->head_of_department) }}"
                    placeholder="e.g., Dr. John Doe">
                @error('head_of_department')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Department description...">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('admin.departments.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Update Department</button>
            </div>
        </form>
    </div>
@endsection
