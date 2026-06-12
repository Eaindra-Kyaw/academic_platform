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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-filter-primary:hover {
            background: #9a0000;
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

        .action-toggle {
            background: #f3e8ff;
            color: #6b21a5;
        }

        .action-toggle:hover {
            background: #e9d5ff;
        }

        .schedule-cell {
            font-size: 0.7rem;
            white-space: nowrap;
            font-family: monospace;
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
        }

        .add-course-btn:hover {
            background: #9a0000;
        }

        /* MODERN PAGINATION STYLES */
        .pagination-wrapper {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .pagination li {
            display: inline-block;
        }

        .pagination a,
        .pagination span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 0.9rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .pagination a {
            background: white;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .pagination a:hover {
            background: #fef3c7;
            color: #800000;
            border-color: #800000;
            transform: translateY(-2px);
        }

        .pagination .active span {
            background: #800000;
            color: white;
            border: 1px solid #800000;
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.25);
        }

        .pagination .disabled span {
            background: #f9fafb;
            color: #d1d5db;
            border: 1px solid #e5e7eb;
            cursor: not-allowed;
        }

        .pagination .disabled span:hover {
            transform: none;
        }

        .pagination a i,
        .pagination span i {
            font-size: 1rem;
            font-weight: bold;
        }

        .pagination-info {
            text-align: center;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #6b7280;
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
                min-width: 900px;
            }

            .action-icons {
                flex-direction: column;
                gap: 0.3rem;
            }

            .pagination a,
            .pagination span {
                min-width: 34px;
                height: 34px;
                padding: 0 0.6rem;
                font-size: 0.75rem;
            }
        }
    </style>

    <div>
        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="color: #800000; margin: 0; font-size: 1.4rem;">📚 Course Management</h3>
                <p style="color: #6b7280; font-size: 0.8rem; margin-top: 0.25rem;">Create, edit, and manage all university
                    courses</p>
            </div>
            <div>
                <a href="{{ route('admin.courses.create') }}" class="add-course-btn">
                    <i class="bi bi-plus-circle"></i> Add New Course
                </a>
            </div>
        </div>

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
                            style="display: block; font-size: 0.7rem; font-weight: 600; color: #6b7280; margin-bottom: 0.25rem;">Academic
                            Year</label>
                        <input type="text" name="academic_year" class="filter-input" placeholder="e.g., 2024-2025"
                            value="{{ request('academic_year') }}">
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
                        <input type="text" name="search" class="filter-input"
                            placeholder="Course code, name, or lecturer..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="action-buttons-group">
                    <button type="submit" class="btn-filter-primary"><i class="bi bi-search"></i> Apply Filters</button>
                    <a href="{{ route('admin.courses.index') }}" class="btn-filter-secondary"><i
                            class="bi bi-arrow-repeat"></i> Reset</a>
                    <a href="{{ route('admin.courses.index', ['trash' => 'only']) }}"
                        class="btn-trash-filter {{ request('trash') == 'only' ? 'active' : '' }}">
                        <i class="bi bi-trash"></i> View Trash
                    </a>
                </div>
            </form>
        </div>

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
                        <th>Acad Year</th>
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
                            <td>{{ $course->department->name ?? 'N/A' }}</td>
                            <td>{{ $course->lecturer_name ?? 'Not Assigned' }}</td>
                            <td style="text-align: center;">{{ $course->credits }}</td>
                            <td>{{ $course->year ?? 'N/A' }}</td>
                            <td>{{ $course->semester ?? 'N/A' }}</td>
                            <td>{{ $course->academic_year ?? 'N/A' }}</td>
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
                                            <button type="submit" class="action-icon action-force"><i
                                                    class="bi bi-trash3"></i> Delete</button>
                                        </form>
                                    </div>
                                @else
                                    <div class="action-icons">
                                        <a href="{{ route('admin.courses.show', $course) }}"
                                            class="action-icon action-view"><i class="bi bi-eye"></i> View</a>
                                        <a href="{{ route('admin.courses.edit', $course) }}"
                                            class="action-icon action-edit"><i class="bi bi-pencil"></i> Edit</a>
                                        <a href="{{ route('admin.courses.toggleStatus', $course) }}"
                                            class="action-icon action-toggle"
                                            onclick="return confirm('{{ $course->is_active ? 'Deactivate' : 'Activate' }} this course?')">
                                            <i class="bi bi-{{ $course->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                                            {{ $course->is_active ? 'Off' : 'On' }}
                                        </a>
                                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                            style="display: inline-block; margin: 0;"
                                            onsubmit="return confirm('Move this course to trash?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-icon action-delete"><i
                                                    class="bi bi-trash"></i> Delete</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="empty-state">
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

        <!-- MODERN PAGINATION -->
        @if ($courses->hasPages())
            <div class="pagination-wrapper">
                <ul class="pagination">
                    @if ($courses->onFirstPage())
                        <li class="disabled">
                            <span><i class="bi bi-chevron-double-left"></i> Previous</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $courses->previousPageUrl() }}" rel="prev"><i
                                    class="bi bi-chevron-double-left"></i> Previous</a>
                        </li>
                    @endif

                    @php
                        $start = max(1, $courses->currentPage() - 2);
                        $end = min($start + 4, $courses->lastPage());
                        if ($end - $start < 4 && $start > 1) {
                            $start = max(1, $end - 4);
                        }
                    @endphp

                    @if ($start > 1)
                        <li><a href="{{ $courses->url(1) }}">1</a></li>
                        @if ($start > 2)
                            <li class="disabled"><span>...</span></li>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $courses->currentPage())
                            <li class="active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $courses->url($page) }}">{{ $page }}</a></li>
                        @endif
                    @endfor

                    @if ($end < $courses->lastPage())
                        @if ($end < $courses->lastPage() - 1)
                            <li class="disabled"><span>...</span></li>
                        @endif
                        <li><a href="{{ $courses->url($courses->lastPage()) }}">{{ $courses->lastPage() }}</a></li>
                    @endif

                    @if ($courses->hasMorePages())
                        <li>
                            <a href="{{ $courses->nextPageUrl() }}" rel="next">Next <i
                                    class="bi bi-chevron-double-right"></i></a>
                        </li>
                    @else
                        <li class="disabled">
                            <span>Next <i class="bi bi-chevron-double-right"></i></span>
                        </li>
                    @endif
                </ul>
            </div>
            <div class="pagination-info">
                Showing {{ $courses->firstItem() }} to {{ $courses->lastItem() }} of {{ $courses->total() }} results
            </div>
        @endif
    </div>
@endsection
