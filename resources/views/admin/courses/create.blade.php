@extends('layouts.app')

@section('title', 'Add Course')
@section('role', 'Admin')
@section('page-title', 'Add New Course')
@section('welcome-text', 'Create a new course for ' . $department->name)

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

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.4rem;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.85rem;
        }

        .form-group .required {
            color: var(--danger);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: var(--transition);
            background: #fafbfc;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .error {
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: 0.25rem;
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

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-top: 0.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin: 0;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .checkbox-group label {
            margin-bottom: 0;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
            color: var(--text-dark);
        }

        .help-text {
            color: var(--text-gray);
            font-size: 0.7rem;
            margin-top: 0.2rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 1.25rem;
            }

            .checkbox-group {
                justify-content: flex-start;
            }
        }
    </style>

    <a href="{{ route('admin.departments.courses.index', $department) }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Courses
    </a>

    <div class="form-card">
        <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 700;">
            <i class="bi bi-plus-circle"></i> Add Course to {{ $department->name }}
        </h3>

        <form method="POST" action="{{ route('admin.departments.courses.store', $department) }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label>Course Code <span class="required">*</span></label>
                    <input type="text" name="course_code" value="{{ old('course_code') }}" placeholder="e.g., CEIT-52033"
                        required>
                    @error('course_code')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Course Name <span class="required">*</span></label>
                    <input type="text" name="course_name" value="{{ old('course_name') }}"
                        placeholder="e.g., Machine Learning" required>
                    @error('course_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Year <span class="required">*</span></label>
                    <select name="year" required>
                        <option value="">Select Year</option>
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}" {{ old('year') == $yearOption ? 'selected' : '' }}>
                                {{ $yearOption }}
                            </option>
                        @endforeach
                    </select>
                    @error('year')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Semester <span class="required">*</span></label>
                    <select name="semester" required>
                        <option value="">Select Semester</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->semester_name }}"
                                {{ old('semester') == $sem->semester_name ? 'selected' : '' }}>
                                {{ $sem->year_name }} - {{ $sem->semester_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester')
                        <div class="error">{{ $message }}</div>
                    @enderror
                    <div class="help-text">Choose the year and semester combination</div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Academic Year</label>
                    <input type="text" name="academic_year" value="{{ old('academic_year', '2025-2026') }}"
                        placeholder="e.g., 2025-2026">
                    @error('academic_year')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Credits <span class="required">*</span></label>
                    <input type="number" name="credits" value="{{ old('credits', 3) }}" min="1" max="6"
                        required>
                    @error('credits')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Room</label>
                    <input type="text" name="room" value="{{ old('room') }}" placeholder="e.g., Room 8-6">
                    @error('room')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Lecturer</label>
                <select name="lecturer_id">
                    <option value="">Select Lecturer</option>
                    @foreach ($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" {{ old('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                            {{ $lecturer->name }} ({{ $lecturer->email }})
                        </option>
                    @endforeach
                </select>
                @if ($lecturers->isEmpty())
                    <small style="color: var(--text-gray);">No lecturers found. Please create lecturers first.</small>
                @endif
                @error('lecturer_id')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="help-text">Leave blank to manually enter the lecturer's name below</div>
            </div>

            <div class="form-group">
                <label>Lecturer Name (Manual Entry)</label>
                <input type="text" name="lecturer_name" value="{{ old('lecturer_name') }}"
                    placeholder="e.g., Dr. John Doe">
                <div class="help-text">Used if the lecturer is not in the dropdown list above</div>
                @error('lecturer_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active">Active</label>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.departments.show', $department) }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Create Course</button>
            </div>
        </form>
    </div>
@endsection
