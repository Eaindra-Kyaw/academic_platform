@extends('layouts.app')

@section('title', 'Announcements')
@section('role', 'Lecturer')
@section('page-title', 'Send Announcements')
@section('welcome-text', 'Send announcements to your students')

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('lecturer.dashboard') }}" class="nav-item">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('lecturer.attendance.take') }}" class="nav-item">
        <i class="bi bi-qr-code-scan"></i><span>Take Attendance</span>
    </a>
    <a href="{{ route('lecturer.enrollments.index') }}" class="nav-item">
        <i class="bi bi-list-check"></i><span>Enrollments</span>
    </a>
    <a href="{{ route('lecturer.students') }}" class="nav-item">
        <i class="bi bi-people"></i><span>All Students</span>
    </a>
    <a href="{{ route('lecturer.attendance.history') }}" class="nav-item">
        <i class="bi bi-clock-history"></i><span>Session History</span>
    </a>
    <a href="{{ route('lecturer.schedule') }}" class="nav-item">
        <i class="bi bi-calendar3"></i><span>Schedule</span>
    </a>
    <div class="nav-label">Reports</div>
    <a href="{{ route('lecturer.reports') }}" class="nav-item">
        <i class="bi bi-download"></i><span>Export Reports</span>
    </a>
    <a href="{{ route('lecturer.announcements') }}" class="nav-item active">
        <i class="bi bi-megaphone"></i><span>Announcements</span>
    </a>
@endsection

@section('content')
    <style>
        .announcement-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.8rem;
            color: #374151;
        }

        select,
        textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.8rem;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        .btn-send {
            background: #800000;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
        }

        .btn-send:hover {
            background: #9a0000;
        }
    </style>

    <div>
        <h3 style="color: #800000; margin-bottom: 1rem;">📢 Send Announcement</h3>

        <div class="announcement-card">
            <form method="POST" action="#" onsubmit="event.preventDefault(); sendAnnouncement();">
                @csrf
                <div class="form-group">
                    <label>Select Course <span style="color: #ef4444;">*</span></label>
                    <select id="announcementCourse" required>
                        <option value="">Select a course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}
                            </option>
                        @endforeach
                        <option value="all">All My Courses</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Title <span style="color: #ef4444;">*</span></label>
                    <input type="text" id="announcementTitle" class="form-control"
                        placeholder="e.g., Class Update, Exam Reminder"
                        style="width:100%; padding:0.6rem; border:1px solid #e5e7eb; border-radius:0.5rem;">
                </div>

                <div class="form-group">
                    <label>Message <span style="color: #ef4444;">*</span></label>
                    <textarea id="announcementMessage" placeholder="Type your announcement here..."></textarea>
                </div>

                <button type="submit" class="btn-send">
                    <i class="bi bi-send"></i> Send Announcement
                </button>
            </form>
        </div>
    </div>

    <script>
        function sendAnnouncement() {
            const course = document.getElementById('announcementCourse').value;
            const title = document.getElementById('announcementTitle').value;
            const message = document.getElementById('announcementMessage').value;

            if (!course || !title || !message) {
                alert('Please fill all fields');
                return;
            }

            alert('Announcement sent successfully to ' + (course === 'all' ? 'all courses' : 'selected course'));
            document.getElementById('announcementTitle').value = '';
            document.getElementById('announcementMessage').value = '';
            document.getElementById('announcementCourse').value = '';
        }
    </script>
@endsection
