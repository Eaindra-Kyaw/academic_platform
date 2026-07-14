@extends('layouts.app')

@section('title', 'Add Lecturer')
@section('page-title', 'Add New Lecturer')
@section('welcome-text', 'Create a new faculty member')

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
            --danger: #ef4444;
            --success: #10b981;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
            padding: 0.3rem 0.8rem;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.1);
            border-radius: 8px;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        .form-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.85rem;
            transition: var(--transition);
            background: #fafbfc;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .form-group .help-text {
            color: var(--text-gray);
            font-size: 0.65rem;
        }

        .error-text {
            color: var(--danger);
            font-size: 0.7rem;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
        }

        .btn-submit i {
            margin-right: 0.3rem;
        }
    </style>

    <div style="max-width:600px; margin:0 auto;">
        <a href="{{ route('admin.lecturers.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Lecturers
        </a>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.lecturers.store') }}">
                @csrf

                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required>
                    @error('name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" required>
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id">
                        <option value="">Select Department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required minlength="8">
                    <div class="help-text">Minimum 8 characters</div>
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bi bi-plus-circle"></i> Create Lecturer
                </button>
            </form>
        </div>
    </div>
@endsection
