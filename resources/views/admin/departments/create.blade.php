@extends('layouts.app')

@section('title', 'Add Department')
@section('role', 'Admin')
@section('page-title', 'Add New Department')
@section('welcome-text', 'Create a new university department')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #3B82F6;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --danger: #ef4444;
            --success: #10b981;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-card {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.85rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: var(--transition);
            background: #fafbfc;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .error {
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.1);
            transition: var(--transition);
            margin-bottom: 1.25rem;
        }

        .back-link:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
        }
    </style>

    <a href="{{ route('admin.departments.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Departments
    </a>

    <div class="form-card">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 700;">
            <i class="bi bi-plus-circle"></i> Create New Department
        </h3>

        <form method="POST" action="{{ route('admin.departments.store') }}">
            @csrf

            <div class="form-group">
                <label>Department Code *</label>
                <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g., CS, ME, CE" required>
                @error('code')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Department Name *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="e.g., Department of Computer Engineering" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Head of Department</label>
                <input type="text" name="head_of_department" value="{{ old('head_of_department') }}"
                    placeholder="e.g., Dr. John Doe">
                @error('head_of_department')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Department description...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('admin.departments.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Create Department</button>
            </div>
        </form>
    </div>
@endsection
