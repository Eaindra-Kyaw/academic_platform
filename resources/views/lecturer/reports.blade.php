@extends('layouts.app')

@section('title', 'Export Reports')
@section('role', 'Lecturer')
@section('page-title', 'Export Reports')
@section('welcome-text', 'Generate and export attendance reports')

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
    <a href="{{ route('lecturer.reports') }}" class="nav-item active">
        <i class="bi bi-download"></i><span>Export Reports</span>
    </a>
    <a href="{{ route('lecturer.announcements') }}" class="nav-item">
        <i class="bi bi-megaphone"></i><span>Announcements</span>
    </a>
@endsection

@section('content')
    <style>
        .report-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1rem;
        }

        .export-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .btn-export {
            background: #800000;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-export:hover {
            background: #9a0000;
        }

        select,
        input {
            padding: 0.6rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            min-width: 200px;
        }
    </style>

    <div>
        <h3 style="color: #800000; margin-bottom: 1rem;">📊 Generate Reports</h3>

        <div class="report-card">
            <h4>Attendance Report</h4>
            <p style="font-size: 0.8rem; color: #6b7280;">Generate attendance report for a specific course</p>

            <div style="margin: 1rem 0;">
                <select id="reportCourse" style="width: 100%; max-width: 300px;">
                    <option value="">Select Course</option>
                    @foreach ($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <input type="date" id="startDate" placeholder="Start Date">
                <input type="date" id="endDate" placeholder="End Date">
            </div>

            <div class="export-buttons">
                <button class="btn-export" onclick="exportReport('pdf')">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </button>
                <button class="btn-export" onclick="exportReport('excel')">
                    <i class="bi bi-file-excel"></i> Export Excel
                </button>
                <button class="btn-export" onclick="exportReport('csv')">
                    <i class="bi bi-file-spreadsheet"></i> Export CSV
                </button>
            </div>
        </div>

        <div class="report-card">
            <h4>At-Risk Students Report</h4>
            <p style="font-size: 0.8rem; color: #6b7280;">Generate report of students with low attendance</p>
            <button class="btn-export" onclick="exportAtRiskReport()">
                <i class="bi bi-exclamation-triangle"></i> Generate At-Risk Report
            </button>
        </div>
    </div>

    <script>
        function exportReport(type) {
            const courseId = document.getElementById('reportCourse').value;
            if (!courseId) {
                alert('Please select a course');
                return;
            }
            alert('Exporting ' + type.toUpperCase() + ' report...');
        }

        function exportAtRiskReport() {
            alert('Generating At-Risk Students Report...');
        }
    </script>
@endsection
