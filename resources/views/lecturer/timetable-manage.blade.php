@extends('layouts.app')

@section('title', 'Manage Timetable')
@section('role', 'Lecturer')
@section('page-title', 'Manage Timetable')
@section('welcome-text', 'Add or remove your class schedule')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        .manage-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 20px;
        }

        .form-card h5 {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
        }

        .form-card .form-group {
            margin-bottom: 14px;
        }

        .form-card .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 4px;
        }

        .form-card .form-group select,
        .form-card .form-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 13px;
            color: #1f2937;
            outline: none;
            transition: all 0.2s;
            background: white;
        }

        .form-card .form-group select:focus,
        .form-card .form-group input:focus {
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
        }

        .btn-primary {
            background: #800000;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }

        .btn-primary:hover {
            background: #5f0000;
        }

        .btn-back {
            background: #6b7280;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
        }

        .btn-back:hover {
            background: #4b5563;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
            border: none;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .schedule-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .schedule-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s;
        }

        .schedule-item:hover {
            background: #f9fafb;
        }

        .schedule-item .info {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .schedule-item .info .course {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
        }

        .schedule-item .info .day {
            background: #f3f4f6;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            color: #374151;
        }

        .schedule-item .info .time {
            font-size: 13px;
            color: #6b7280;
        }

        .schedule-item .info .room {
            font-size: 13px;
            color: #6b7280;
        }

        .schedule-item .info .invalid {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }

        .schedule-item .actions {
            display: flex;
            gap: 4px;
        }

        .empty-message {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .empty-message .icon {
            font-size: 40px;
            color: #d1d5db;
            margin-bottom: 12px;
        }

        .empty-message h6 {
            color: #1f2937;
            margin-bottom: 4px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        @media (max-width: 992px) {
            .manage-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('lecturer.timetable.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Back to Timetable
    </a>

    <div class="manage-container">
        <!-- Add Schedule Form -->
        <div class="form-card">
            <h5><i class="bi bi-plus-circle"></i> Add to Timetable</h5>
            <form action="{{ route('lecturer.timetable.add') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Select Course</label>
                    <select name="course_id" required>
                        <option value="">-- Choose a course --</option>
                        @foreach ($availableCourses as $course)
                            <option value="{{ $course->id }}">
                                {{ $course->course_code }} - {{ $course->course_name }}
                                @if ($course->schedule_day)
                                    (✓ Already scheduled on {{ $course->schedule_day }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Day</label>
                    <select name="schedule_day" required>
                        <option value="">-- Select Day --</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Start Time</label>
                    <input type="time" name="schedule_time" required>
                </div>

                <div class="form-group">
                    <label>End Time</label>
                    <input type="time" name="schedule_end_time" required>
                </div>

                <div class="form-group">
                    <label>Room (optional)</label>
                    <input type="text" name="room" placeholder="e.g., 1-3-7">
                </div>

                <button type="submit" class="btn-primary">➕ Add to Timetable</button>
            </form>
        </div>

        <!-- Current Schedule List -->
        <div class="form-card">
            <h5><i class="bi bi-list-check"></i> My Timetable ({{ $scheduledCourses->count() }})</h5>
            <div class="schedule-list">
                @if ($scheduledCourses->count() > 0)
                    @foreach ($scheduledCourses as $course)
                        @php
                            $isInvalid = $course->schedule_time === $course->schedule_end_time;
                        @endphp
                        <div class="schedule-item">
                            <div class="info">
                                <span class="course">{{ $course->course_code }}</span>
                                <span class="day">{{ $course->schedule_day }}</span>
                                <span class="time">
                                    {{ date('h:i A', strtotime($course->schedule_time)) }} -
                                    {{ date('h:i A', strtotime($course->schedule_end_time)) }}
                                </span>
                                @if ($course->room)
                                    <span class="room">📍 {{ $course->room }}</span>
                                @endif
                                @if ($isInvalid)
                                    <span class="invalid">⚠️ Invalid (same time)</span>
                                @endif
                            </div>
                            <div class="actions">
                                <form action="{{ route('lecturer.timetable.remove', $course->id) }}" method="POST"
                                    style="display: inline-block;"
                                    onsubmit="return confirm('Remove this from timetable?');">
                                    @csrf
                                    <button type="submit" class="btn-danger">🗑️ Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-message">
                        <div class="icon"><i class="bi bi-calendar-plus"></i></div>
                        <h6>No Schedule Added</h6>
                        <p>Add your courses to the timetable using the form.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
