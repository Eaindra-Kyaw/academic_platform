@extends('layouts.app')

@section('title', 'Edit Department')
@section('role', 'Admin')
@section('page-title', 'Edit Department')
@section('welcome-text', 'Update department information')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
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

        .btn-cancel:hover {
            background: #6b7280;
        }

        .error {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7a8f;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            border-radius: 0.5rem;
            background: white;
            border: 1px solid #e9edf4;
            transition: all 0.2s;
            margin-bottom: 1.25rem;
        }

        .back-link:hover {
            color: #800000;
            border-color: #800000;
            transform: translateX(-3px);
        }
    </style>

    <!-- Back Link -->
    <a href="{{ route('admin.departments.show', $department) }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Department
    </a>

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
                <a href="{{ route('admin.departments.show', $department) }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Update Department</button>
            </div>
        </form>
    </div>
@endsection
