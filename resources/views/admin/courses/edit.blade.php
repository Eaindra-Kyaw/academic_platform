@extends('layouts.app')

@section('title', 'Edit Course')
@section('role', 'Admin')
@section('page-title', 'Edit Course')
@section('welcome-text', 'Update course information')

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
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid #e5e7eb;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #800000;
        }

        .full-width {
            grid-column: span 2;
        }

        .btn-submit {
            background: #800000;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            background: #9a0000;
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
            transition: all 0.2s ease;
        }

        .btn-cancel:hover {
            background: #6b7280;
        }

        .error {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .help-text {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: span 1;
            }

            .form-card {
                padding: 1.5rem;
            }
        }
    </style>

    <div class="form-card">
        <h3 style="color: #800000; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-pencil-square"></i> Edit Course: {{ $course->course_code }} - {{ $course->course_name }}
        </h3>

        <form method="POST" action="{{ route('admin.courses.update', $course) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label>Course Code *</label>
                    <input type="text" name="course_code" value="{{ old('course_code', $course->course_code) }}"
                        placeholder="e.g., CS301" required>
                    @error('course_code')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Course Name *</label>
                    <input type="text" name="course_name" value="{{ old('course_name', $course->course_name) }}"
                        placeholder="e.g., Database Systems" required>
                    @error('course_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Department *</label>
                    <select name="department_id" required>
                        <option value="">Select Department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ old('department_id', $course->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->code }} - {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Lecturer *</label>
                    <input type="text" name="lecturer_name" value="{{ old('lecturer_name', $course->lecturer_name) }}"
                        placeholder="e.g., Dr. Phyo Thu Zar Tun" required>
                    <div class="help-text">Enter the lecturer's full name</div>
                    @error('lecturer_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Credits *</label>
                    <input type="number" name="credits" value="{{ old('credits', $course->credits) }}" min="1"
                        max="6" required>
                    @error('credits')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Year *</label>
                    <select name="year" required>
                        <option value="">Select Year</option>
                        <option value="First Year" {{ old('year', $course->year) == 'First Year' ? 'selected' : '' }}>First
                            Year</option>
                        <option value="Second Year" {{ old('year', $course->year) == 'Second Year' ? 'selected' : '' }}>
                            Second Year</option>
                        <option value="Third Year" {{ old('year', $course->year) == 'Third Year' ? 'selected' : '' }}>Third
                            Year</option>
                        <option value="Fourth Year" {{ old('year', $course->year) == 'Fourth Year' ? 'selected' : '' }}>
                            Fourth Year</option>
                        <option value="Fifth Year" {{ old('year', $course->year) == 'Fifth Year' ? 'selected' : '' }}>Fifth
                            Year</option>
                        <option value="Final Year" {{ old('year', $course->year) == 'Final Year' ? 'selected' : '' }}>Final
                            Year</option>
                    </select>
                    @error('year')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Semester *</label>
                    <select name="semester" required>
                        <option value="">Select Semester</option>
                        <option value="First Semester"
                            {{ old('semester', $course->semester) == 'First Semester' ? 'selected' : '' }}>First Semester
                        </option>
                        <option value="Second Semester"
                            {{ old('semester', $course->semester) == 'Second Semester' ? 'selected' : '' }}>Second Semester
                        </option>
                    </select>
                    @error('semester')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Academic Year *</label>
                    <input type="text" name="academic_year" value="{{ old('academic_year', $course->academic_year) }}"
                        placeholder="e.g., 2024-2025" required>
                    <div class="help-text">Format: 2024-2025</div>
                    @error('academic_year')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Room</label>
                    <input type="text" name="room" value="{{ old('room', $course->room) }}"
                        placeholder="e.g., Room A-203">
                    @error('room')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Schedule Day</label>
                    <select name="schedule_day">
                        <option value="">Select Day</option>
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
                    </select>
                </div>

                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="schedule_time"
                        value="{{ old('schedule_time', $course->schedule_time ? \Carbon\Carbon::parse($course->schedule_time)->format('H:i') : '') }}">
                </div>

                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="schedule_end_time"
                        value="{{ old('schedule_end_time', $course->schedule_end_time ? \Carbon\Carbon::parse($course->schedule_end_time)->format('H:i') : '') }}">
                </div>

                <div class="form-group full-width">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                        <span>Active (visible to students)</span>
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="{{ route('admin.courses.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Update Course</button>
            </div>
        </form>
    </div>
@endsection
