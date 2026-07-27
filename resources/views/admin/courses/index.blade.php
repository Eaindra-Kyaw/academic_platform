@extends('layouts.app')

@section('title', 'Courses - ' . $department->name)
@section('page-title', '📚 Courses')
@section('welcome-text', $department->name . ' • ' . $department->code)

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
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.4rem 1.2rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-action-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
        }

        .btn-action-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .btn-action-outline {
            background: transparent;
            color: var(--text-gray);
            border: 1px solid rgba(10, 36, 99, 0.12);
        }

        .btn-action-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(10, 36, 99, 0.04);
        }

        .table-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .table-header {
            padding: 0.75rem 1.25rem;
            background: var(--bg-main);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .table-header .title {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-body {
            padding: 0.5rem 1rem 1rem 1rem;
            overflow-x: auto;
        }

        .course-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            min-width: 950px;
        }

        .course-table thead th {
            padding: 0.6rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-gray);
            border-bottom: 2px solid rgba(10, 36, 99, 0.06);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
            white-space: nowrap;
        }

        .course-table tbody td {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .course-table tbody tr {
            transition: var(--transition);
        }

        .course-table tbody tr:hover {
            background: #fafbfc;
        }

        .course-table tbody tr:last-child td {
            border-bottom: none;
        }

        .course-code-cell {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.7rem;
            background: rgba(212, 160, 23, 0.12);
            padding: 0.05rem 0.6rem;
            border-radius: 6px;
            display: inline-block;
            white-space: nowrap;
        }

        .attendance-pill {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            display: inline-block;
            white-space: nowrap;
        }

        .attendance-pill.high {
            background: var(--success-light);
            color: #166534;
        }

        .attendance-pill.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .attendance-pill.low {
            background: var(--danger-light);
            color: #991b1b;
        }

        .btn-sm {
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.65rem;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            border: none;
            cursor: pointer;
        }

        .btn-view {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-view:hover {
            background: #bfdbfe;
        }

        .btn-edit {
            background: var(--warning-light);
            color: #92400e;
        }

        .btn-edit:hover {
            background: #fde68a;
        }

        .btn-delete {
            background: var(--danger-light);
            color: var(--danger);
        }

        .btn-delete:hover {
            background: #fca5a5;
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 2rem;
            display: block;
            margin-bottom: 0.5rem;
            color: #d1d5db;
        }

        .action-cell {
            display: flex;
            gap: 0.3rem;
            justify-content: center;
            align-items: center;
            flex-wrap: nowrap;
        }

        .confirm-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .confirm-overlay.show {
            display: flex;
        }

        .confirm-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .confirm-box .icon {
            text-align: center;
            font-size: 2.5rem;
            color: var(--danger);
            margin-bottom: 0.5rem;
        }

        .confirm-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.3rem 0;
        }

        .confirm-box p {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-gray);
            margin: 0 0 1.5rem 0;
        }

        .confirm-box .buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .btn-confirm-cancel {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(10, 36, 99, 0.1);
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-confirm-cancel:hover {
            background: #f3f4f6;
        }

        .btn-confirm-delete {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: var(--danger);
            color: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-confirm-delete:hover {
            background: #b91c1c;
        }

        /* ── PAGINATION ── */
        .pagination-wrapper {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            background: #fafbfc;
            border-radius: 0 0 var(--radius) var(--radius);
        }

        .pagination-wrapper .info {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .pagination-wrapper .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 0.2rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination-wrapper .pagination li {
            display: inline-block;
        }

        .pagination-wrapper .pagination li a,
        .pagination-wrapper .pagination li span {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            min-width: 30px;
            text-align: center;
            font-size: 0.75rem;
            border-radius: 6px;
            border: 1px solid rgba(10, 36, 99, 0.1);
            background: var(--white);
            color: var(--text-dark);
            text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1.4;
            font-weight: 500;
        }

        .pagination-wrapper .pagination li a:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(10, 36, 99, 0.15);
        }

        .pagination-wrapper .pagination li.active span {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(10, 36, 99, 0.15);
        }

        .pagination-wrapper .pagination li.disabled span {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f9fafb;
            border-color: #e5e7eb;
        }

        @media (max-width: 768px) {
            .course-table {
                font-size: 0.7rem;
                min-width: 750px;
            }

            .course-table thead th,
            .course-table tbody td {
                padding: 0.3rem 0.4rem;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-sm {
                padding: 0.15rem 0.4rem;
                font-size: 0.6rem;
            }

            .action-cell {
                gap: 0.15rem;
            }

            .pagination-wrapper {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .pagination-wrapper .pagination {
                justify-content: center;
            }

            .pagination-wrapper .pagination li a,
            .pagination-wrapper .pagination li span {
                padding: 0.2rem 0.5rem;
                font-size: 0.7rem;
                min-width: 28px;
            }
        }

        @media (max-width: 480px) {

            .pagination-wrapper .pagination li a,
            .pagination-wrapper .pagination li span {
                padding: 0.15rem 0.4rem;
                font-size: 0.65rem;
                min-width: 24px;
            }
        }
    </style>

    <!-- Back Link -->
    <a href="{{ route('admin.departments.show', $department) }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Department
    </a>

    <!-- Action Bar -->
    <div class="action-bar">
        <div>
            <span style="font-size: 0.85rem; color: var(--text-gray);">
                Total: <strong>{{ $courses->total() }}</strong> courses
            </span>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.departments.courses.create', $department) }}" class="btn-action btn-action-primary">
                <i class="bi bi-plus-circle"></i> Add Course
            </a>
            <a href="{{ route('admin.departments.courses.export', $department) }}" class="btn-action btn-action-outline">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="table-card">
        <div class="table-header">
            <span class="title"><i class="bi bi-list-ul"></i> Course List</span>
            <span style="font-size: 0.75rem; color: var(--text-gray);">
                {{ $courses->total() }} courses
            </span>
        </div>

        <div class="table-body">
            @if ($courses->count() > 0)
                <table class="course-table">
                    <thead>
                        <tr>
                            <th style="width:10%;">Code</th>
                            <th style="width:18%;">Course Name</th>
                            <th style="width:10%;">Year</th>
                            <th style="width:10%;">Semester</th>
                            <th style="width:15%;">Lecturer</th>
                            <th style="width:8%;text-align:center;">Room</th>
                            <th style="width:8%;text-align:center;">Students</th>
                            <th style="width:10%;text-align:center;">Attendance</th>
                            <th style="width:11%;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            @php
                                if (!is_object($course)) {
                                    continue;
                                }
                                $attendance = $course->avg_attendance ?? 0;
                                $attendanceClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                                $lecturerName = $course->lecturer->name ?? ($course->lecturer_name ?? 'Unassigned');
                                $studentCount = $course->student_count ?? 0;
                            @endphp
                            <tr>
                                <td><span class="course-code-cell">{{ $course->course_code }}</span></td>
                                <td style="font-weight:500; color:var(--text-dark);">{{ $course->course_name }}</td>
                                <td>{{ $course->year ?? 'N/A' }}</td>
                                <td>{{ $course->semester ?? 'N/A' }}</td>
                                <td style="color:var(--text-gray); font-size:0.75rem;">
                                    <i class="bi bi-person" style="color:var(--primary); font-size:0.6rem;"></i>
                                    {{ $lecturerName }}
                                </td>
                                <td style="color:var(--text-gray); font-size:0.75rem; text-align:center;">
                                    <i class="bi bi-door-open" style="font-size:0.6rem;"></i> {{ $course->room ?? 'N/A' }}
                                </td>
                                <td style="text-align:center; font-weight:600; color:var(--text-dark);">
                                    {{ $studentCount }}
                                </td>
                                <td style="text-align:center;">
                                    <span class="attendance-pill {{ $attendanceClass }}">
                                        {{ number_format($attendance, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    <div class="action-cell">
                                        <a href="{{ route('admin.departments.courses.show', ['department' => $department->id, 'course' => $course->id]) }}"
                                            class="btn-sm btn-view" title="View Course">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.departments.courses.edit', ['department' => $department->id, 'course' => $course->id]) }}"
                                            class="btn-sm btn-edit" title="Edit Course">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn-sm btn-delete" title="Delete Course"
                                            onclick="showDeleteConfirm('{{ addslashes($course->course_name) }}', '{{ route('admin.departments.courses.destroy', ['department' => $department->id, 'course' => $course->id]) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <i class="bi bi-book"></i>
                    <p style="font-size:0.9rem; margin:0;">No courses found in this department</p>
                    <p style="font-size:0.75rem; margin:0.2rem 0 0.5rem;">Start by adding your first course</p>
                    <a href="{{ route('admin.departments.courses.create', $department) }}"
                        style="display:inline-block; background:linear-gradient(135deg, var(--primary), var(--primary-light)); color:var(--white); padding:0.3rem 1rem; border-radius:6px; text-decoration:none; font-size:0.8rem;">
                        <i class="bi bi-plus-circle"></i> Add Course
                    </a>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if ($courses->hasPages())
            <div class="pagination-wrapper">
                <div class="info">
                    Showing {{ $courses->firstItem() ?? 0 }} to {{ $courses->lastItem() ?? 0 }}
                    of {{ $courses->total() }} courses
                </div>
                <div>
                    {{ $courses->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="confirm-overlay" id="deleteConfirm">
        <div class="confirm-box">
            <div class="icon">🗑️</div>
            <h4>Delete Course</h4>
            <p>Are you sure you want to delete "<span id="confirmCourseName"></span>"?<br>This action cannot be undone.</p>
            <div class="buttons">
                <button class="btn-confirm-cancel" onclick="closeConfirm()">Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-confirm-delete">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteConfirm(courseName, deleteUrl) {
            document.getElementById('confirmCourseName').textContent = courseName;
            document.getElementById('deleteForm').action = deleteUrl;
            document.getElementById('deleteConfirm').classList.add('show');
        }

        function closeConfirm() {
            document.getElementById('deleteConfirm').classList.remove('show');
        }

        document.getElementById('deleteConfirm').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirm();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirm();
            }
        });
    </script>
@endsection
