@extends('layouts.app')

@section('title', 'All Students')
@section('role', 'Lecturer')
@section('page-title', 'All Students')
@section('welcome-text', 'View all students in your courses')

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
    <a href="{{ route('lecturer.students') }}" class="nav-item active">
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
    <a href="{{ route('lecturer.announcements') }}" class="nav-item">
        <i class="bi bi-megaphone"></i><span>Announcements</span>
    </a>
@endsection

@section('content')
    <style>
        .stats-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            flex: 1;
            min-width: 120px;
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #800000;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .students-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            background: #f9fafb;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .students-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f2f4;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .status-eligible {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .status-warning {
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .status-risk {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .search-bar {
            background: white;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.8rem;
        }

        .btn-notify {
            background: #800000;
            color: white;
            border: none;
            padding: 0.2rem 0.6rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .students-table {
                min-width: 700px;
            }
        }
    </style>

    <div>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number">{{ $students->count() }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $students->where('status', 'At Risk')->count() }}</div>
                <div class="stat-label">At Risk</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $students->where('status', 'Warning')->count() }}</div>
                <div class="stat-label">Warning</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $students->where('status', 'Eligible')->count() }}</div>
                <div class="stat-label">Eligible</div>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search by student name or email...">
        </div>

        <div style="overflow-x: auto;">
            <table class="students-table" id="studentsTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Student ID</th>
                        <th>Year</th>
                        <th>Attendance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td><strong>{{ $student->name }}</strong></td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                            <td>Year {{ $student->current_year }}</td>
                            <td>
                                {{ $student->attendance_percentage ?? 0 }}%
                                <div class="progress-bar"
                                    style="height:4px; background:#e5e7eb; border-radius:4px; margin-top:4px;">
                                    <div
                                        style="width:{{ $student->attendance_percentage ?? 0 }}%; height:4px; background:#800000; border-radius:4px;">
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if (($student->attendance_percentage ?? 0) >= 75)
                                    <span class="status-eligible">Eligible</span>
                                @elseif(($student->attendance_percentage ?? 0) >= 60)
                                    <span class="status-warning">Warning</span>
                                @else
                                    <span class="status-risk">At Risk</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn-notify" onclick="notifyStudent('{{ $student->name }}')">Notify</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:2rem;">No students found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function notifyStudent(name) {
            alert('Notification sent to ' + name);
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#studentsTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
@endsection
