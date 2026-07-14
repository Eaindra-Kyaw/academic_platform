@extends('layouts.app')

@section('title', $yearLabel . ' Courses - ' . $department->name)
@section('page-title', '📚 ' . $yearLabel . ' Courses')
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

        .breadcrumb-custom {
            background: transparent;
            padding: 0;
            margin-bottom: 1.25rem;
        }

        .breadcrumb-custom .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.8rem;
        }

        .breadcrumb-custom .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--text-gray);
            font-size: 0.8rem;
        }

        .back-link-course {
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

        .back-link-course:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
        }

        .action-bar-course {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-action-course {
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

        .btn-action-course-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
        }

        .btn-action-course-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .course-table-wrap {
            overflow-x: auto;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
        }

        .year-group {
            margin-bottom: 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            border-radius: 8px;
            overflow: hidden;
        }

        .year-group:last-child {
            margin-bottom: 0;
        }

        .year-group .year-header {
            padding: 0.5rem 0.75rem;
            background: #f8f9fc;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .year-group .year-header .badge-count {
            background: var(--primary);
            color: var(--white);
            font-size: 0.6rem;
            padding: 0.05rem 0.6rem;
            border-radius: 1rem;
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

        .course-table .course-code-cell {
            font-weight: 700;
            color: var(--primary);
            font-size: 0.7rem;
            background: rgba(212, 160, 23, 0.12);
            padding: 0.05rem 0.6rem;
            border-radius: 6px;
            display: inline-block;
            white-space: nowrap;
        }

        .course-table .attendance-pill {
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            display: inline-block;
            white-space: nowrap;
        }

        .course-table .attendance-pill.high {
            background: var(--success-light);
            color: #166534;
        }

        .course-table .attendance-pill.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .course-table .attendance-pill.low {
            background: var(--danger-light);
            color: #991b1b;
        }

        .course-table .btn-action-sm {
            padding: 0.25rem 0.7rem;
            border-radius: 6px;
            font-size: 0.7rem;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            border: none;
            cursor: pointer;
            min-width: 34px;
            justify-content: center;
        }

        .btn-view-course {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-view-course:hover {
            background: #bfdbfe;
        }

        .btn-edit-course {
            background: var(--warning-light);
            color: #92400e;
        }

        .btn-edit-course:hover {
            background: #fde68a;
        }

        .btn-delete-course {
            background: var(--danger-light);
            color: var(--danger);
        }

        .btn-delete-course:hover {
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
            gap: 0.4rem;
            justify-content: center;
            align-items: center;
            flex-wrap: nowrap;
            min-width: 130px;
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

        .confirm-box .btn-confirm-cancel {
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

        .confirm-box .btn-confirm-cancel:hover {
            background: #f3f4f6;
        }

        .confirm-box .btn-confirm-delete {
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

        .confirm-box .btn-confirm-delete:hover {
            background: #b91c1c;
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

            .action-bar-course {
                justify-content: flex-start;
            }

            .course-table .btn-action-sm {
                padding: 0.15rem 0.4rem;
                font-size: 0.6rem;
                min-width: 28px;
            }

            .action-cell {
                min-width: 100px;
                gap: 0.2rem;
            }

            .year-group {
                margin-bottom: 1rem;
            }
        }
    </style>

    <nav aria-label="breadcrumb" class="breadcrumb-custom">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departments</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.departments.show', $department) }}">{{ $department->code }}</a></li>
            <li class="breadcrumb-item active">{{ $yearLabel }}</li>
        </ol>
    </nav>

    <a href="{{ route('admin.departments.show', $department) }}" class="back-link-course">
        <i class="bi bi-arrow-left"></i> Back to Department
    </a>

    <div class="action-bar-course">
        <a href="{{ route('admin.departments.courses.create', $department) }}"
            class="btn-action-course btn-action-course-primary">
            <i class="bi bi-plus-circle"></i> Add Course
        </a>
    </div>

    <div class="course-table-wrap">
        @if ($courses->count() > 0)
            <div class="year-group">
                <div class="year-header">
                    <span>{{ $yearLabel }}</span>
                    <span class="badge-count">{{ $courses->count() }} courses</span>
                </div>
                <table class="course-table">
                    <thead>
                        <tr>
                            <th style="width:10%;">Code</th>
                            <th style="width:20%;">Course Name</th>
                            <th style="width:18%;">Lecturer</th>
                            <th style="width:8%;text-align:center;">Room</th>
                            <th style="width:8%;text-align:center;">Students</th>
                            <th style="width:12%;text-align:center;">Attendance</th>
                            <th style="width:24%;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            @php
                                $attendance = $course->avg_attendance ?? 0;
                                $attendanceClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                                $lecturerName = $course->lecturer->name ?? ($course->lecturer_name ?? 'Unassigned');
                            @endphp
                            <tr>
                                <td><span class="course-code-cell">{{ $course->course_code }}</span></td>
                                <td style="font-weight:500; color:var(--text-dark);">{{ $course->course_name }}</td>
                                <td style="color:var(--text-gray); font-size:0.75rem;">
                                    <i class="bi bi-person" style="color:var(--primary); font-size:0.6rem;"></i>
                                    {{ $lecturerName }}
                                </td>
                                <td style="color:var(--text-gray); font-size:0.75rem; text-align:center;">
                                    <i class="bi bi-door-open" style="font-size:0.6rem;"></i> {{ $course->room ?? 'N/A' }}
                                </td>
                                <td style="text-align:center; font-weight:600; color:var(--text-dark);">
                                    {{ $course->student_count ?? 0 }}
                                </td>
                                <td style="text-align:center;">
                                    <span class="attendance-pill {{ $attendanceClass }}">
                                        {{ number_format($attendance, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    <div class="action-cell">
                                        <a href="{{ route('admin.departments.courses.show', [$department, $course]) }}"
                                            class="btn-action-sm btn-view-course" title="View Course">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.departments.courses.edit', [$department, $course]) }}"
                                            class="btn-action-sm btn-edit-course" title="Edit Course">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn-action-sm btn-delete-course" title="Delete Course"
                                            onclick="showDeleteConfirm('{{ addslashes($course->course_name) }}', '{{ route('admin.departments.courses.destroy', [$department, $course]) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-book"></i>
                <p style="font-size:0.9rem; margin:0;">No courses found in {{ $yearLabel }}</p>
                <p style="font-size:0.75rem; margin:0.2rem 0 0.5rem;">Start by adding your first course</p>
                <a href="{{ route('admin.departments.courses.create', $department) }}"
                    style="display:inline-block; background:linear-gradient(135deg, var(--primary), var(--primary-light)); color:var(--white); padding:0.3rem 1rem; border-radius:6px; text-decoration:none; font-size:0.8rem;">
                    <i class="bi bi-plus-circle"></i> Add Course
                </a>
            </div>
        @endif
    </div>

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
