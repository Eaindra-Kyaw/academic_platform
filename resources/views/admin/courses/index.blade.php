@extends('layouts.app')

@section('title', 'Courses - ' . $department->name)
@section('page-title', '📚 Courses')
@section('welcome-text', $department->name . ' • ' . $department->code)

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .back-link-course {
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

        .back-link-course:hover {
            color: #800000;
            border-color: #800000;
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
            border-radius: 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-action-course-primary {
            background: #800000;
            color: white;
        }

        .btn-action-course-primary:hover {
            background: #a00000;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        /* ===== YEAR GROUP WITH CLEAR GAP ===== */
        .year-group {
            background: white;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .year-group:last-child {
            margin-bottom: 0;
        }

        .year-group .year-header {
            padding: 0.5rem 0.75rem;
            background: #f8f9fc;
            font-weight: 600;
            font-size: 0.85rem;
            color: #1a2332;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .year-group .year-header .badge-count {
            background: #800000;
            color: white;
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
            color: #6b7a8f;
            border-bottom: 2px solid #e9edf4;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
            white-space: nowrap;
        }

        .course-table tbody td {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .course-table tbody tr {
            transition: all 0.2s;
        }

        .course-table tbody tr:hover {
            background: #fafbfc;
        }

        .course-table tbody tr:last-child td {
            border-bottom: none;
        }

        .course-table .course-code-cell {
            font-weight: 700;
            color: #800000;
            font-size: 0.7rem;
            background: #fef3c7;
            padding: 0.05rem 0.6rem;
            border-radius: 0.3rem;
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
            background: #ecfdf5;
            color: #10b981;
        }

        .course-table .attendance-pill.medium {
            background: #fffbeb;
            color: #f59e0b;
        }

        .course-table .attendance-pill.low {
            background: #fef2f2;
            color: #ef4444;
        }

        .course-table .btn-action-sm {
            padding: 0.25rem 0.7rem;
            border-radius: 0.3rem;
            font-size: 0.7rem;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            border: none;
            cursor: pointer;
            min-width: 34px;
            justify-content: center;
        }

        .btn-view-course {
            background: #eff6ff;
            color: #3b82f6;
        }

        .btn-view-course:hover {
            background: #dbeafe;
            color: #2563eb;
        }

        .btn-edit-course {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-edit-course:hover {
            background: #fde68a;
            color: #78350f;
        }

        .btn-delete-course {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete-course:hover {
            background: #fca5a5;
            color: #7f1d1d;
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #9ca3af;
            background: white;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
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

        /* ===== CUSTOM CONFIRM DIALOG ===== */
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
            background: white;
            border-radius: 0.75rem;
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
            color: #ef4444;
            margin-bottom: 0.5rem;
        }

        .confirm-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1a2332;
            margin: 0 0 0.3rem 0;
        }

        .confirm-box p {
            text-align: center;
            font-size: 0.85rem;
            color: #6b7a8f;
            margin: 0 0 1.5rem 0;
        }

        .confirm-box .buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .confirm-box .btn-confirm-cancel {
            padding: 0.4rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid #e9edf4;
            background: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .confirm-box .btn-confirm-cancel:hover {
            background: #f3f4f6;
        }

        .confirm-box .btn-confirm-delete {
            padding: 0.4rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: #dc2626;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
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

        @media (max-width: 480px) {
            .year-group {
                margin-bottom: 0.75rem;
            }

            .course-table {
                font-size: 0.65rem;
                min-width: 650px;
            }

            .course-table .btn-action-sm {
                padding: 0.1rem 0.3rem;
                font-size: 0.55rem;
                min-width: 24px;
            }

            .action-cell {
                min-width: 80px;
                gap: 0.15rem;
            }
        }
    </style>

    <!-- ===== BACK LINK ===== -->
    <a href="{{ route('admin.departments.show', $department) }}" class="back-link-course">
        <i class="bi bi-arrow-left"></i> Back to Department
    </a>

    <!-- ===== ACTION BUTTONS ===== -->
    <div class="action-bar-course">
        <a href="{{ route('admin.departments.courses.create', $department) }}"
            class="btn-action-course btn-action-course-primary">
            <i class="bi bi-plus-circle"></i> Add Course
        </a>
    </div>

    <!-- ===== COURSES TABLE ===== -->
    @if ($courses->count() > 0)
        @foreach ($courses as $year => $yearCourses)
            <!-- Year Group with Gap -->
            <div class="year-group">
                <div class="year-header">
                    <span>{{ $year }}</span>
                    <span class="badge-count">{{ $yearCourses->count() }} courses</span>
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
                        @foreach ($yearCourses as $course)
                            @php
                                $attendance = $course->avg_attendance ?? 0;
                                $attendanceClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                                $lecturerName = $course->lecturer->name ?? ($course->lecturer_name ?? 'Unassigned');
                            @endphp
                            <tr>
                                <td><span class="course-code-cell">{{ $course->course_code }}</span></td>
                                <td style="font-weight:500; color:#1a2332;">{{ $course->course_name }}</td>
                                <td style="color:#6b7a8f; font-size:0.75rem;">
                                    <i class="bi bi-person" style="color:#800000; font-size:0.6rem;"></i>
                                    {{ $lecturerName }}
                                </td>
                                <td style="color:#6b7a8f; font-size:0.75rem; text-align:center;">
                                    <i class="bi bi-door-open" style="font-size:0.6rem;"></i> {{ $course->room ?? 'N/A' }}
                                </td>
                                <td style="text-align:center; font-weight:600; color:#1a2332;">
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
                                        <!-- DELETE BUTTON -->
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
        @endforeach
    @else
        <div class="empty-state">
            <i class="bi bi-book"></i>
            <p style="font-size:0.9rem; margin:0;">No courses found in this department</p>
            <p style="font-size:0.75rem; margin:0.2rem 0 0.5rem;">Start by adding your first course</p>
            <a href="{{ route('admin.departments.courses.create', $department) }}"
                style="display:inline-block; background:#800000; color:white; padding:0.3rem 1rem; border-radius:0.4rem; text-decoration:none; font-size:0.8rem;">
                <i class="bi bi-plus-circle"></i> Add Course
            </a>
        </div>
    @endif

    <!-- ===== CUSTOM CONFIRM DIALOG ===== -->
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

        // Close when clicking outside
        document.getElementById('deleteConfirm').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirm();
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirm();
            }
        });
    </script>

@endsection
