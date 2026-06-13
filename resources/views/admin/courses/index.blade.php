@extends('layouts.app')

@section('title', 'Manage Courses')
@section('role', 'Admin')
@section('page-title', 'Course Management')
@section('welcome-text', 'Manage courses by department')

@section('sidebar')
    <div class="nav-label">Management</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('admin.users') }}" class="nav-item">
        <i class="bi bi-people"></i><span>User Management</span>
    </a>
    <a href="{{ route('admin.departments.index') }}" class="nav-item">
        <i class="bi bi-building"></i><span>Departments</span>
    </a>
    <a href="{{ route('admin.courses.index') }}" class="nav-item active">
        <i class="bi bi-book"></i><span>Course Management</span>
    </a>
    <a href="{{ route('admin.enrollments.index') }}" class="nav-item">
        <i class="bi bi-list-check"></i><span>Enrollments</span>
    </a>
    <div class="nav-label">Analytics</div>
    <a href="#" class="nav-item"><i class="bi bi-calendar"></i><span>Semesters</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up"></i><span>Analytics</span></a>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Reports</span></a>
@endsection

@section('content')
    <style>
        /* Department Tabs */
        .dept-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }

        .dept-tab {
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #e5e7eb;
        }

        .dept-tab:hover {
            background: #fef3c7;
            color: #800000;
            border-color: #800000;
        }

        .dept-tab.active {
            background: #800000;
            color: white;
            border-color: #800000;
        }

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

        .filter-select {
            padding: 0.6rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.8rem;
            background: #f9fafb;
            min-width: 130px;
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

        .btn-add {
            background: #800000;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .courses-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .courses-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            background: #f9fafb;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .courses-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f2f4;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .courses-table tr:hover td {
            background: #fefce8;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .action-icons {
            display: flex;
            gap: 0.5rem;
        }

        .action-icon {
            padding: 0.3rem 0.6rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            cursor: pointer;
            border: none;
        }

        .action-view {
            background: #eff6ff;
            color: #2563eb;
        }

        .action-edit {
            background: #fef3c7;
            color: #d97706;
        }

        .action-delete {
            background: #fef2f2;
            color: #dc2626;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #9ca3af;
        }

        /* ============================================ */
        /* CUSTOM MODERN PAGINATION - CLEAN DESIGN */
        /* ============================================ */
        .pagination-wrapper {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 1rem;
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

        .page-item {
            display: inline-block;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            min-width: 70px;
            height: 40px;
            padding: 0 1rem;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .page-item:not(.disabled):hover .page-link {
            background: #fef3c7;
            color: #800000;
            border-color: #800000;
            transform: translateY(-1px);
        }

        .page-item.active .page-link {
            background: #800000;
            color: white;
            border-color: #800000;
        }

        .page-item.disabled .page-link {
            background: #f9fafb;
            color: #d1d5db;
            cursor: not-allowed;
        }

        .pagination-info {
            text-align: center;
            font-size: 0.75rem;
            color: #6b7280;
            padding: 0.5rem 1rem;
            background: #f9fafb;
            border-radius: 2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pagination-info i {
            color: #800000;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .dept-tabs {
                flex-wrap: wrap;
            }

            .search-section {
                flex-direction: column;
            }

            .search-input,
            .filter-select,
            .btn-filter,
            .btn-reset {
                width: 100%;
            }

            .courses-table {
                min-width: 700px;
            }

            .page-link {
                min-width: 55px;
                height: 36px;
                padding: 0 0.75rem;
                font-size: 0.75rem;
            }

            .pagination-info {
                font-size: 0.65rem;
            }
        }
    </style>

    <div>
        <!-- Header -->
        <div
            style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <a href="{{ route('admin.courses.create') }}" class="btn-add">
                <i class="bi bi-plus-circle"></i> Add New Course
            </a>
        </div>

        <!-- Department Tabs -->
        <div class="dept-tabs" id="deptTabs">
            <button class="dept-tab active" data-dept="all">📋 All Departments</button>
            @foreach ($departments as $dept)
                <button class="dept-tab" data-dept="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->name }}</button>
            @endforeach
        </div>

        <!-- Search & Filter Section -->
        <div class="search-section">
            <input type="text" id="searchInput" class="search-input"
                placeholder="🔍 Search by course code, name, or lecturer...">
            <select id="yearFilter" class="filter-select">
                <option value="all">All Years</option>
                <option value="First Year">First Year</option>
                <option value="Second Year">Second Year</option>
                <option value="Third Year">Third Year</option>
                <option value="Fourth Year">Fourth Year</option>
                <option value="Fifth Year">Fifth Year</option>
            </select>
            <select id="semesterFilter" class="filter-select">
                <option value="all">All Semesters</option>
                <option value="First Semester">First Semester</option>
                <option value="Second Semester">Second Semester</option>
            </select>
            <button class="btn-filter" onclick="applyFilters()">
                <i class="bi bi-search"></i> Apply
            </button>
            <button class="btn-reset" onclick="resetFilters()">
                <i class="bi bi-arrow-repeat"></i> Reset
            </button>
        </div>

        <!-- Courses Table -->
        <div style="overflow-x: auto;">
            <table class="courses-table" id="coursesTable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Course Name</th>
                        <th>Department</th>
                        <th>Lecturer</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="coursesBody">
                    @foreach ($courses as $course)
                        <tr data-dept="{{ $course->department_id }}"
                            data-search="{{ strtolower($course->course_code . ' ' . $course->course_name . ' ' . $course->lecturer_name) }}"
                            data-year="{{ $course->year }}" data-semester="{{ $course->semester }}">
                            <td><strong>{{ $course->course_code }}</strong></td>
                            <td>{{ $course->course_name }}</td>
                            <td>{{ $course->department->code ?? 'N/A' }}</td>
                            <td>{{ $course->lecturer_name ?? 'Not Assigned' }}</td>
                            <td>{{ $course->year ?? 'N/A' }}</td>
                            <td>{{ $course->semester ?? 'N/A' }}</td>
                            <td>
                                <div class="action-icons">
                                    <a href="{{ route('admin.courses.show', $course) }}" class="action-icon action-view">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.courses.edit', $course) }}" class="action-icon action-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                                        style="display: inline;" onsubmit="return confirm('Move this course to trash?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon action-delete">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="emptyState" class="empty-state" style="display: none;">
            <i class="bi bi-book" style="font-size: 2rem;"></i>
            <p>No courses found in this department</p>
        </div>

        <!-- CUSTOM MODERN PAGINATION -->
        @if ($courses->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination">
                    @if ($courses->onFirstPage())
                        <span class="page-item disabled">
                            <span class="page-link"><i class="bi bi-chevron-left"></i> Prev</span>
                        </span>
                    @else
                        <a class="page-item" href="{{ $courses->previousPageUrl() }}">
                            <span class="page-link"><i class="bi bi-chevron-left"></i> Prev</span>
                        </a>
                    @endif

                    @foreach ($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
                        @if ($page == $courses->currentPage())
                            <span class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </span>
                        @else
                            <a class="page-item" href="{{ $url }}">
                                <span class="page-link">{{ $page }}</span>
                            </a>
                        @endif
                    @endforeach

                    @if ($courses->hasMorePages())
                        <a class="page-item" href="{{ $courses->nextPageUrl() }}">
                            <span class="page-link">Next <i class="bi bi-chevron-right"></i></span>
                        </a>
                    @else
                        <span class="page-item disabled">
                            <span class="page-link">Next <i class="bi bi-chevron-right"></i></span>
                        </span>
                    @endif
                </div>

                <div class="pagination-info">
                    <i class="bi bi-info-circle"></i>
                    Showing <strong>{{ $courses->firstItem() }}</strong> to <strong>{{ $courses->lastItem() }}</strong>
                    of <strong>{{ $courses->total() }}</strong> courses
                </div>
            </div>
        @endif
    </div>

    <script>
        let currentDept = 'all';

        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const yearValue = document.getElementById('yearFilter').value;
            const semesterValue = document.getElementById('semesterFilter').value;
            const rows = document.querySelectorAll('#coursesBody tr');
            let visibleCount = 0;

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const rowDept = row.getAttribute('data-dept');
                const rowSearch = row.getAttribute('data-search') || '';
                const rowYear = row.getAttribute('data-year') || '';
                const rowSemester = row.getAttribute('data-semester') || '';

                const matchesDept = currentDept === 'all' || rowDept == currentDept;
                const matchesSearch = searchTerm === '' || rowSearch.includes(searchTerm);
                const matchesYear = yearValue === 'all' || rowYear === yearValue;
                const matchesSemester = semesterValue === 'all' || rowSemester === semesterValue;

                if (matchesDept && matchesSearch && matchesYear && matchesSemester) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }

            document.getElementById('emptyState').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('yearFilter').value = 'all';
            document.getElementById('semesterFilter').value = 'all';
            applyFilters();
        }

        // Department tab switching
        const tabs = document.querySelectorAll('.dept-tab');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                currentDept = this.getAttribute('data-dept');
                applyFilters();
            });
        });

        // Event listeners
        document.getElementById('searchInput').addEventListener('keyup', applyFilters);
        document.getElementById('yearFilter').addEventListener('change', applyFilters);
        document.getElementById('semesterFilter').addEventListener('change', applyFilters);

        // Initial filter
        applyFilters();
    </script>
@endsection
