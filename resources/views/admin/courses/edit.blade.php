@extends('layouts.app')

@section('title', 'Edit Course - ' . $course->course_code)
@section('page-title', 'Edit Course')
@section('welcome-text', $department->name . ' • ' . $course->course_code)

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
        .form-group select,
        .form-group textarea {
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
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .form-group .help-text {
            color: var(--text-gray);
            font-size: 0.65rem;
            margin-top: 0.2rem;
        }

        .error {
            color: var(--danger);
            font-size: 0.7rem;
            margin-top: 0.2rem;
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
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: #e5e7eb;
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

        .grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .section-divider {
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            margin: 1.5rem 0 1rem 0;
            padding-top: 0.5rem;
        }

        .section-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .section-title i {
            color: var(--primary);
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

    {{-- FIXED: Explicit route parameters --}}
    <a href="{{ route('admin.departments.courses.show', ['department' => $department->id, 'course' => $course->id]) }}"
        class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Course Details
    </a>

    <div class="form-card">
        <h3 style="color: var(--text-dark); font-size: 1.1rem; font-weight: 700; margin: 0 0 1.5rem 0;">
            <i class="bi bi-pencil" style="color: var(--primary);"></i> Edit Course: {{ $course->course_code }}
        </h3>

        <form method="POST" action="{{ route('admin.departments.courses.update', [$department, $course]) }}">
            @csrf
            @method('PUT')

            <div class="grid-2col">
                <div class="form-group">
                    <label>Course Code *</label>
                    <input type="text" name="course_code" value="{{ old('course_code', $course->course_code) }}" required
                        placeholder="e.g., CS101">
                    @error('course_code')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Credits *</label>
                    <input type="number" name="credits" value="{{ old('credits', $course->credits) }}" required
                        min="1" max="6">
                    @error('credits')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Course Name *</label>
                <input type="text" name="course_name" value="{{ old('course_name', $course->course_name) }}" required
                    placeholder="e.g., Introduction to Programming">
                @error('course_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid-2col">
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

                <div class="form-group">
                    <label>Semester *</label>
                    <select name="semester" required>
                        <option value="">Select Semester</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->semester_name }}"
                                {{ old('semester', $course->semester) == $sem->semester_name ? 'selected' : '' }}>
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

            <div class="grid-2col">
                <div class="form-group">
                    <label>Academic Year *</label>
                    <input type="text" name="academic_year" value="{{ old('academic_year', $course->academic_year) }}"
                        required placeholder="e.g., 2025-2026">
                    @error('academic_year')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Lecturer Dropdown --}}
            <div class="form-group">
                <label>Lecturer</label>
                <select name="lecturer_id">
                    <option value="">Select Lecturer</option>
                    @if (isset($deptLecturers) && $deptLecturers->count() > 0)
                        <optgroup label="Department Lecturers">
                            @foreach ($deptLecturers as $lecturer)
                                <option value="{{ $lecturer->id }}"
                                    {{ old('lecturer_id', $course->lecturer_id) == $lecturer->id ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endif
                    @if (isset($lecturers) && $lecturers->count() > 0)
                        <optgroup label="All Lecturers">
                            @foreach ($lecturers as $lecturer)
                                @if (!isset($deptLecturers) || !$deptLecturers->contains('id', $lecturer->id))
                                    <option value="{{ $lecturer->id }}"
                                        {{ old('lecturer_id', $course->lecturer_id) == $lecturer->id ? 'selected' : '' }}>
                                        {{ $lecturer->name }}
                                        {{ $lecturer->department_id ? '(' . ($lecturer->department->code ?? '') . ')' : '' }}
                                    </option>
                                @endif
                            @endforeach
                        </optgroup>
                    @endif
                </select>
                @error('lecturer_id')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="help-text">Leave blank to manually enter the lecturer's name below</div>
            </div>

            <div class="form-group">
                <label>Lecturer Name (Manual Entry)</label>
                <input type="text" name="lecturer_name" value="{{ old('lecturer_name', $course->lecturer_name) }}"
                    placeholder="e.g., Dr. John Doe">
                <div class="help-text">Used if the lecturer is not in the dropdown list above</div>
                @error('lecturer_name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="section-divider"></div>
            <div class="section-title">
                <i class="bi bi-calendar-week"></i> Timetable Schedule
                <span
                    style="font-weight: 400; color: var(--text-gray); font-size: 0.7rem; display: block; margin-top: 2px;">
                    This data will automatically appear in the lecturer's timetable
                </span>
            </div>

            <div class="grid-2col">
                <div class="form-group">
                    <label>Day</label>
                    <select name="schedule_day">
                        <option value="">-- Select Day --</option>
                        <option value="Monday"
                            {{ old('schedule_day', $course->schedule_day) == 'Monday' ? 'selected' : '' }}>Monday</option>
                        <option value="Tuesday"
                            {{ old('schedule_day', $course->schedule_day) == 'Tuesday' ? 'selected' : '' }}>Tuesday
                        </option>
                        <option value="Wednesday"
                            {{ old('schedule_day', $course->schedule_day) == 'Wednesday' ? 'selected' : '' }}>Wednesday
                        </option>
                        <option value="Thursday"
                            {{ old('schedule_day', $course->schedule_day) == 'Thursday' ? 'selected' : '' }}>Thursday
                        </option>
                        <option value="Friday"
                            {{ old('schedule_day', $course->schedule_day) == 'Friday' ? 'selected' : '' }}>Friday</option>
                        <option value="Saturday"
                            {{ old('schedule_day', $course->schedule_day) == 'Saturday' ? 'selected' : '' }}>Saturday
                        </option>
                        <option value="Sunday"
                            {{ old('schedule_day', $course->schedule_day) == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                    </select>
                    <div class="help-text">When this course is taught</div>
                    @error('schedule_day')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Room</label>
                    <input type="text" name="room" value="{{ old('room', $course->room) }}"
                        placeholder="e.g., 1-3-7">
                    <div class="help-text">Room number or building</div>
                    @error('room')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid-2col">
                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="schedule_time" value="{{ old('schedule_time', $course->schedule_time) }}"
                        step="60">
                    <div class="help-text">e.g., 08:00 for 8:00 AM</div>
                    @error('schedule_time')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="schedule_end_time"
                        value="{{ old('schedule_end_time', $course->schedule_end_time) }}" step="60">
                    <div class="help-text">e.g., 08:50 for 8:50 AM (50-minute classes)</div>
                    @error('schedule_end_time')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem; margin-top: 1.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.departments.courses.index', $department) }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">
                    <i class="bi bi-save"></i> Update Course
                </button>
            </div>
        </form>
    </div>
@endsection
