@extends('layouts.app')

@section('title', 'Enrolled Students')
@section('role', 'Lecturer')
@section('page-title', 'My Students')
@section('welcome-text', 'View students enrolled in your courses')

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('lecturer.dashboard') }}" class="nav-item">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('lecturer.attendance.take') }}" class="nav-item">
        <i class="bi bi-qr-code-scan"></i><span>Take Attendance</span>
    </a>
    <a href="{{ route('lecturer.enrollments.index') }}" class="nav-item active">
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
    <a href="{{ route('lecturer.announcements') }}" class="nav-item">
        <i class="bi bi-megaphone"></i><span>Announcements</span>
    </a>
@endsection

@section('content')
    <style>
        /* Search Section - Like Course Management */
        .search-section {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            padding: 0.6rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.8rem;
        }

        .search-input:focus {
            outline: none;
            border-color: #800000;
        }

        .btn-filter {
            background: #800000;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .btn-filter:hover {
            background: #9a0000;
        }

        .btn-reset {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-reset:hover {
            background: #e5e7eb;
        }

        .result-count {
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 1rem;
            text-align: right;
        }

        /* Course Cards */
        .course-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .course-header {
            background: linear-gradient(135deg, #800000 0%, #6b0000 100%);
            padding: 1rem 1.25rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .course-title {
            font-weight: 700;
            font-size: 1rem;
        }

        .student-count {
            background: rgba(255, 215, 0, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        /* Students Table - Clean like Course Management */
        .students-table {
            width: 100%;
            border-collapse: collapse;
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

        .students-table tr:hover td {
            background: #fefce8;
        }

        .badge-enrolled {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .approval-date {
            font-size: 0.65rem;
            color: #10b981;
        }

        .rejection-date {
            font-size: 0.65rem;
            color: #dc2626;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            .students-table {
                min-width: 700px;
            }

            .search-section {
                flex-direction: column;
            }

            .search-input,
            .btn-filter,
            .btn-reset {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div>

        <!-- Search Section - Like Course Management -->
        <div class="search-section">
            <input type="text" id="searchStudentInput" class="search-input"
                placeholder="🔍 Search by student name, email, or student ID...">
            <button class="btn-filter" onclick="filterStudents()">
                <i class="bi bi-search"></i> Apply
            </button>
            <button class="btn-reset" onclick="resetFilters()">
                <i class="bi bi-arrow-repeat"></i> Reset
            </button>
        </div>

        <!-- Result Count -->
        <div id="resultCount" class="result-count" style="display: none;"></div>

        @if ($courses->count() > 0)
            @foreach ($courses as $courseIndex => $course)
                <div class="course-card" id="course-{{ $courseIndex }}">
                    <div class="course-header">
                        <div class="course-title">
                            {{ $course->course_code }} - {{ $course->course_name }}
                        </div>
                        <div class="student-count" id="count-{{ $courseIndex }}">
                            <i class="bi bi-people"></i> {{ $course->enrollments->count() }} students
                        </div>
                    </div>
                    <div style="overflow-x: auto;">
                        @if ($course->enrollments->count() > 0)
                            <table class="students-table" id="table-{{ $courseIndex }}">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Student ID</th>
                                        <th>Email</th>
                                        <th>Request Date</th>
                                        <th>Approved/Rejected Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-{{ $courseIndex }}">
                                    @foreach ($course->enrollments as $enrollmentIndex => $enrollment)
                                        <tr data-student-name="{{ strtolower($enrollment->student->name) }}"
                                            data-student-email="{{ strtolower($enrollment->student->email) }}"
                                            data-student-id="{{ strtolower($enrollment->student->student_id ?? '') }}">
                                            <td>{{ $enrollmentIndex + 1 }}</td>
                                            <td><strong>{{ $enrollment->student->name }}</strong></td>
                                            <td>{{ $enrollment->student->student_id ?? 'N/A' }}</td>
                                            <td>{{ $enrollment->student->email }}</td>
                                            <td>{{ $enrollment->created_at->format('d M Y') }}</td>
                                            <td>
                                                @if ($enrollment->status == 'approved' && $enrollment->approved_at)
                                                    <span class="approval-date">
                                                        <i class="bi bi-check-circle"></i>
                                                        {{ \Carbon\Carbon::parse($enrollment->approved_at)->format('d M Y') }}
                                                    </span>
                                                @elseif ($enrollment->status == 'rejected' && $enrollment->rejected_at)
                                                    <span class="rejection-date">
                                                        <i class="bi bi-x-circle"></i>
                                                        {{ \Carbon\Carbon::parse($enrollment->rejected_at)->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <span style="color: #6b7280; font-size: 0.65rem;">—</span>
                                                @endif
                                            </td>
                                            <td><span class="badge-enrolled">Enrolled</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <i class="bi bi-person-x" style="font-size: 2rem;"></i>
                                <p>No students enrolled in this course yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state" style="background: white; border-radius: 1rem; padding: 3rem;">
                <i class="bi bi-book" style="font-size: 3rem;"></i>
                <p>You are not assigned to any courses yet.</p>
            </div>
        @endif
    </div>

    <script>
        function filterStudents() {
            const searchTerm = document.getElementById('searchStudentInput').value.toLowerCase();
            let totalVisibleStudents = 0;

            @for ($i = 0; $i < $courses->count(); $i++)
                const tbody{{ $i }} = document.getElementById('tbody-{{ $i }}');
                if (tbody{{ $i }}) {
                    const rows = tbody{{ $i }}.querySelectorAll('tr');
                    let visibleCount = 0;

                    rows.forEach(row => {
                        const studentName = row.getAttribute('data-student-name') || '';
                        const studentEmail = row.getAttribute('data-student-email') || '';
                        const studentId = row.getAttribute('data-student-id') || '';
                        const matches = searchTerm === '' ||
                            studentName.includes(searchTerm) ||
                            studentEmail.includes(searchTerm) ||
                            studentId.includes(searchTerm);

                        if (matches) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    const countSpan = document.getElementById('count-{{ $i }}');
                    if (countSpan) {
                        countSpan.innerHTML = '<i class="bi bi-people"></i> ' + visibleCount + ' students';
                    }
                    totalVisibleStudents += visibleCount;

                    const courseCard = document.getElementById('course-{{ $i }}');
                    if (courseCard) {
                        courseCard.style.display = visibleCount === 0 ? 'none' : 'block';
                    }
                }
            @endfor

            // Show result count
            const resultCountDiv = document.getElementById('resultCount');
            if (searchTerm !== '') {
                resultCountDiv.innerHTML =
                    `<i class="bi bi-info-circle"></i> Found <strong>${totalVisibleStudents}</strong> students matching "<strong>${searchTerm}</strong>"`;
                resultCountDiv.style.display = 'block';
            } else {
                resultCountDiv.style.display = 'none';
            }
        }

        function resetFilters() {
            document.getElementById('searchStudentInput').value = '';
            filterStudents();
        }

        const searchInput = document.getElementById('searchStudentInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', filterStudents);
        }
    </script>
@endsection
