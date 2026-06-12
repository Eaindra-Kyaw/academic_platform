@extends('layouts.app')

@section('title', 'Manage Courses')
@section('role', 'Admin')
@section('page-title', 'Course Management')
@section('welcome-text', 'Create, edit, and manage university courses')

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
        /* Modern Course Management Styles */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 600;
            color: #6b7280;
            letter-spacing: 0.5px;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #800000;
            margin-top: 0.25rem;
        }

        .filter-section {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
        }

        .filter-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-title i {
            color: #800000;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #800000;
            ring: 2px solid rgba(128, 0, 0, 0.1);
        }

        .filter-input label {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .action-buttons-group {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-filter-primary {
            background: #800000;
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            border: none;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-filter-primary:hover {
            background: #9a0000;
            transform: translateY(-1px);
        }

        .btn-filter-secondary {
            background: #f3f4f6;
            color: #374151;
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-filter-secondary:hover {
            background: #e5e7eb;
        }

        .btn-trash-filter {
            background: #fef2f2;
            color: #dc2626;
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            border: 1px solid #fecaca;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-trash-filter.active {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .btn-trash-filter:hover:not(.active) {
            background: #fee2e2;
        }

        .table-wrapper {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            overflow-x: auto;
        }

        .courses-table {
            width: 100%;
            border-collapse: collapse;
        }

        .courses-table th {
            padding: 1rem 0.75rem;
            text-align: left;
            background: #fafbfc;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .courses-table td {
            padding: 0.9rem 0.75rem;
            border-bottom: 1px solid #f0f2f4;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .courses-table tr:hover td {
            background: #fefce8;
        }

        .trashed-row td {
            background: #fef2f2;
            opacity: 0.85;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-deleted {
            background: #f3f4f6;
            color: #6b7280;
        }

        .action-icons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: nowrap;
        }

        .action-icon {
            padding: 0.35rem 0.65rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.15s;
            cursor: pointer;
            border: none;
        }

        .action-view {
            background: #eff6ff;
            color: #2563eb;
        }

        .action-view:hover {
            background: #dbeafe;
        }

        .action-edit {
            background: #fef3c7;
            color: #d97706;
        }

        .action-edit:hover {
            background: #fde68a;
        }

        .action-delete {
            background: #fef2f2;
            color: #dc2626;
        }

        .action-delete:hover {
            background: #fee2e2;
        }

        .action-restore {
            background: #d1fae5;
            color: #059669;
        }

        .action-restore:hover {
            background: #a7f3d0;
        }

        .action-force {
            background: #fef2f2;
            color: #991b1b;
        }

        .action-force:hover {
            background: #fee2e2;
        }

        .schedule-cell {
            font-size: 0.7rem;
            white-space: nowrap;
            font-family: monospace;
        }

        .dept-cell {
            max-width: 200px;
            white-space: normal;
            word-break: break-word;
            line-height: 1.4;
        }

        .add-course-btn {
            background: #800000;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .add-course-btn:hover {
            background: #9a0000;
            transform: translateY(-1px);
        }

        .pagination-wrapper {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons-group {
                flex-direction: column;
            }

            .action-buttons-group a,
            .action-buttons-group button {
                width: 100%;
                justify-content: center;
            }

            .courses-table {
                min-width: 800px;
            }

            .action-icons {
                flex-direction: column;
                gap: 0.3rem;
            }
        }
    </style>

    <div>
        <!-- Header with Add Button -->
        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="color: #800000; margin: 0; font-size: 1.4rem;">📚 Course Management</h3>
                <p style="color: #6b7280; font-size: 0.8rem; margin-top: 0.25rem;">Create, edit, and manage all university
                    courses</p>
            </div>
            <a href="{{ route('admin.courses.create') }}" class="add-course-btn">
                <i class="bi bi-plus-circle"></i> Add New Course
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div
                style="background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border-left: 3px solid #10b981;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border-left: 3px solid #dc2626;">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-title">Total Courses</div>
                <div class="stat-number">{{ $courses->total() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Active Courses</div>
                <div class="stat-number">{{ $courses->where('is_active', true)->count() }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Departments</div>
                <div class="stat-number">{{ $departments->count() }}</div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="bi bi-sliders2"></i> Filter Courses
            </div>
            <form method="GET" action="{{ route('admin.courses.index') }}">
                <div class="filter-grid">
                    <div>
                        <label
                            style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Department</label>
                        <select name="department_id" class="filter-input">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label
                            style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Year</label>
                        <select name="year" class="filter-input">
                            <option value="">All Years</option>
                            <option value="First Year" {{ request('year') == 'First Year' ? 'selected' : '' }}>First Year
                            </option>
                            <option value="Second Year" {{ request('year') == 'Second Year' ? 'selected' : '' }}>Second
                                Year</option>
                            <option value="Third Year" {{ request('year') == 'Third Year' ? 'selected' : '' }}>Third Year
                            </option>
                            <option value="Fourth Year" {{ request('year') == 'Fourth Year' ? 'selected' : '' }}>Fourth
                                Year</option>
                            <option value="Fifth Year" {{ request('year') == 'Fifth Year' ? 'selected' : '' }}>Fifth Year
                            </option>
                            <option value="Final Year" {{ request('year') == 'Final Year' ? 'selected' : '' }}>Final Year
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Semester</label>
                        <select name="semester" class="filter-input">
                            <option value="">All Semesters</option>
                            <option value="First Semester" {{ request('semester') == 'First Semester' ? 'selected' : '' }}>
                                First Semester</option>
                            <option value="Second Semester"
                                {{ request('semester') == 'Second Semester' ? 'selected' : '' }}>Second Semester</option>
                        </select>
                    </div>
                    <div>
                        <label
                            style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Status</label>
                        <select name="status" class="filter-input">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div>
                        <label
                            style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Search</label>
                        <input type="text" name="search" class="filter-input" placeholder="Course code or name..."
                            value="{{ request('search') }}">
                    </div>
                </div>
                <div class="action-buttons-group">
                    <button type="submit" class="btn-filter-primary">
                        <i class="bi bi-search"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.courses.index') }}" class="btn-filter-secondary">
                        <i class="bi bi-arrow-repeat"></i> Reset
                    </a>
                    <a href="{{ route('admin.courses.index', ['trash' => 'only']) }}"
                        class="btn-trash-filter {{ request('trash') == 'only' ? 'active' : '' }}">
                        <i class="bi bi-trash"></i> View Trash
                    </a>
                </div>
            </form>
        </div>

        <!-- Courses Table -->
        <div class="table-wrapper">
            <table class="courses-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Department</th>
                        <th>Lecturer</th>
                        <th>Credits</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $index => $course)
                        @php
                            $scheduleDisplay = 'TBA';
                            if ($course->schedule_day && $course->schedule_time && $course->schedule_end_time) {
                                $startTime = \Carbon\Carbon::parse($course->schedule_time)->format('g:i A');
                                $endTime = \Carbon\Carbon::parse($course->schedule_end_time)->format('g:i A');
                                $scheduleDisplay = $course->schedule_day . ' ' . $startTime . ' - ' . $endTime;
                            }
                            $isTrashed = $course->trashed();
                        @endphp
                        <tr class="{{ $isTrashed ? 'trashed-row' : '' }}">
                            <td>{{ $courses->firstItem() + $index }}</td>
                            <td><strong>{{ $course->course_code }}</strong></td>
                            <td>{{ $course->course_name }}</td>
                            <td class="dept-cell">{{ $course->department->name ?? 'N/A' }}</td>
                            <td>{{ $course->lecturer_name ?? 'Not Assigned' }}</td>
                            <td style="text-align: center;">{{ $course->credits }}</td>
                            <td>{{ $course->year ?? 'N/A' }}</td>
                            <td>{{ $course->semester ?? 'N/A' }}</td>
                            <td class="schedule-cell">{{ $scheduleDisplay }}</td>
                            <td>
                                @if ($isTrashed)
                                    <span class="badge badge-deleted">Deleted</span>
                                @elseif ($course->is_active)
                                    <span class="badge badge-active">Active</span>
                                @else
                                    <span class="badge badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if ($isTrashed)
                                    <div class="action-icons">
                                        <a href="{{ route('admin.courses.restore', $course->id) }}"
                                            class="action-icon action-restore"
                                            onclick="return confirm('Restore this course?')">
                                            <i class="bi bi-arrow-repeat"></i> Restore
                                        </a>
                                        <form method="POST"
                                            action="{{ route('admin.courses.force-delete', $course->id) }}"
                                            style="display: inline-block; margin: 0;"
                                            onsubmit="return confirm('Permanently delete? This cannot be undone!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-icon action-force">
                                                <i class="bi bi-trash3"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="action-icons">
                                        <a href="{{ route('admin.courses.show', $course) }}"
                                            class="action-icon action-view">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course) }}"
                                            class="action-icon action-edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                            style="display: inline-block; margin: 0;"
                                            onsubmit="return confirm('Move this course to trash?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-icon action-delete">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="empty-state">
                                @if (request('trash') == 'only')
                                    <i class="bi bi-trash"></i>
                                    <p>No deleted courses in trash.</p>
                                    <a href="{{ route('admin.courses.index') }}" class="btn-filter-secondary">Back to
                                        Courses</a>
                                @else
                                    <i class="bi bi-book"></i>
                                    <p>No courses found.</p>
                                    <a href="{{ route('admin.courses.create') }}" class="add-course-btn"
                                        style="display: inline-flex;">Create your first course</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $courses->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
