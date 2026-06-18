@extends('layouts.app')

@section('title', 'Edit Course - ' . $course->course_code)
@section('page-title', 'Edit Course')
@section('welcome-text', $department->name . ' • ' . $course->course_code)

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .form-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e9edf4;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            color: #1a2332;
            margin-bottom: 0.2rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e9edf4;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .form-group .help-text {
            color: #6b7a8f;
            font-size: 0.65rem;
            margin-top: 0.2rem;
        }

        .error {
            color: #ef4444;
            font-size: 0.7rem;
            margin-top: 0.2rem;
        }

        .btn-submit {
            background: #800000;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #a00000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
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

        .grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        @media (max-width: 768px) {
            .grid-2col {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 1.25rem;
            }
        }
    </style>

    <!-- ===== BACK LINK ===== -->
    <a href="{{ route('admin.departments.courses.show', [$department, $course]) }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Course Details
    </a>

    <!-- ===== FORM ===== -->
    <div class="form-card">
        <h3 style="color: #1a2332; font-size: 1.1rem; font-weight: 700; margin: 0 0 1.5rem 0;">
            <i class="bi bi-pencil" style="color: #800000;"></i> Edit Course: {{ $course->course_code }}
        </h3>

        <form method="POST" action="{{ route('admin.departments.courses.update', [$department, $course]) }}">
            @csrf
            @method('PUT')

            <div class="grid-2col">
                <!-- Course Code -->
                <div class="form-group">
                    <label>Course Code *</label>
                    <input type="text" name="course_code" value="{{ old('course_code', $course->course_code) }}" required
                        placeholder="e.g., CS101">
                    @error('course_code')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Credits -->
                <div class="form-group">
                    <label>Credits *</label>
                    <input type="number" name="credits" value="{{ old('credits', $course->credits) }}" required
                        min="1" max="6">
                    @error('credits')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Course Name -->
            <div class="form-group">
                <label>Course Name *</label>
                <input type="text" name="course_name" value="{{ old('course_name', $course->course_name) }}" required
                    placeholder="e.g., Introduction to Programming">
                @error('course_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid-2col">
                <!-- Year -->
                <div class="form-group">
                    <label>Year *</label>
                    <select name="year" required>
                        <option value="">Select Year</option>
                        @foreach ($years as $key => $label)
                            <option value="{{ $key }}" {{ old('year', $course->year) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('year')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Semester -->
                <div class="form-group">
                    <label>Semester *</label>
                    <select name="semester" required>
                        <option value="">Select Semester</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem }}"
                                {{ old('semester', $course->semester) == $sem ? 'selected' : '' }}>
                                {{ $sem }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid-2col">
                <!-- Academic Year -->
                <div class="form-group">
                    <label>Academic Year *</label>
                    <input type="text" name="academic_year" value="{{ old('academic_year', $course->academic_year) }}"
                        required placeholder="e.g., 2025-2026">
                    @error('academic_year')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Room -->
                <div class="form-group">
                    <label>Room</label>
                    <input type="text" name="room" value="{{ old('room', $course->room) }}"
                        placeholder="e.g., Room 8-6">
                    @error('room')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Lecturer -->
            <div class="form-group">
                <label>Lecturer</label>
                <select name="lecturer_id">
                    <option value="">Select Lecturer</option>
                    <optgroup label="Department Lecturers">
                        @foreach ($deptLecturers as $lecturer)
                            <option value="{{ $lecturer->id }}"
                                {{ old('lecturer_id', $course->lecturer_id) == $lecturer->id ? 'selected' : '' }}>
                                {{ $lecturer->name }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="All Lecturers">
                        @foreach ($lecturers as $lecturer)
                            @if ($lecturer->department_id != $department->id)
                                <option value="{{ $lecturer->id }}"
                                    {{ old('lecturer_id', $course->lecturer_id) == $lecturer->id ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                    {{ $lecturer->department_id ? '(' . $lecturer->department->code . ')' : '' }}
                                </option>
                            @endif
                        @endforeach
                    </optgroup>
                </select>
                @error('lecturer_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Lecturer Name (Manual Entry) -->
            <div class="form-group">
                <label>Lecturer Name (Manual Entry)</label>
                <input type="text" name="lecturer_name" value="{{ old('lecturer_name', $course->lecturer_name) }}"
                    placeholder="e.g., Dr. John Doe">
                <div class="help-text">Used if lecturer is not in the dropdown list above</div>
                @error('lecturer_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; justify-content: flex-end;">
                <!-- Cancel: Back to Department Courses -->
                <a href="{{ route('admin.departments.courses.index', $department) }}" class="btn-cancel">Cancel</a>

                <!-- Update: Submit and go to Course Show -->
                <button type="submit" class="btn-submit">
                    <i class="bi bi-save"></i> Update Course
                </button>
            </div>
        </form>
    </div>
@endsection
