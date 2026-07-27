@extends('layouts.app')

@section('title', 'All Attendance Records')
@section('role', 'Lecturer')
@section('page-title', 'All Attendance Records')
@section('welcome-text', 'View all attendance records across your courses')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        .filter-bar {
            background: var(--white);
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            box-shadow: var(--shadow);
        }

        .filter-bar select,
        .filter-bar input {
            padding: 0.4rem 0.8rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.4rem;
            font-size: 0.8rem;
        }

        .filter-bar .btn-filter {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 0.4rem;
            cursor: pointer;
        }

        .records-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .records-table th {
            padding: 0.75rem 1rem;
            background: var(--bg-main);
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--text-gray);
            font-weight: 700;
        }

        .records-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f2f4;
            font-size: 0.85rem;
        }

        .records-table tr:hover td {
            background: #f8f9fc;
        }

        .status-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-present {
            background: #dcfce7;
            color: #166534;
        }

        .status-late {
            background: #fef3c7;
            color: #92400e;
        }

        .status-absent {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-gray);
        }

        /* ============================================================
               PAGINATION – FIXED & RESPONSIVE
               ============================================================ */
        .pagination-wrap {
            padding: 1rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem 1rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            background: #fafafa;
            border-radius: 0 0 0.75rem 0.75rem;
        }

        .pagination-wrap .info {
            font-size: 0.85rem;
            color: var(--text-gray);
            white-space: nowrap;
        }

        /* Force the pagination to be flexible */
        .pagination-wrap nav {
            display: flex;
            flex: 1 1 auto;
            justify-content: flex-end;
        }

        .pagination-wrap .pagination {
            display: flex;
            flex-wrap: wrap !important;
            gap: 0.25rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination-wrap .pagination .page-item {
            display: inline-flex;
            margin: 0;
        }

        .pagination-wrap .pagination .page-link {
            padding: 0.3rem 0.7rem;
            border-radius: 0.3rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            color: var(--text-gray);
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.2s ease;
            background: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            min-height: 2rem;
        }

        .pagination-wrap .pagination .page-link:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #f3f4f6;
        }

        .pagination-wrap .pagination .active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            pointer-events: none;
        }

        .pagination-wrap .pagination .disabled .page-link {
            opacity: 0.4;
            pointer-events: none;
        }

        .student-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .student-link:hover {
            text-decoration: underline;
        }

        /* ============================================================
               RESPONSIVE
               ============================================================ */
        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar select,
            .filter-bar input,
            .filter-bar .btn-filter {
                width: 100%;
            }

            .pagination-wrap {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 0.75rem;
            }

            .pagination-wrap .info {
                white-space: normal;
            }

            .pagination-wrap nav {
                justify-content: center;
                width: 100%;
            }

            .pagination-wrap .pagination {
                justify-content: center;
            }

            .pagination-wrap .pagination .page-link {
                min-width: 2.2rem;
                min-height: 2.2rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {

            .records-table th,
            .records-table td {
                padding: 0.4rem 0.5rem;
                font-size: 0.75rem;
            }

            .records-table {
                font-size: 0.75rem;
            }

            .status-badge {
                font-size: 0.6rem;
                padding: 0.1rem 0.4rem;
            }

            .pagination-wrap .pagination .page-link {
                min-width: 1.8rem;
                min-height: 1.8rem;
                font-size: 0.75rem;
                padding: 0.2rem 0.4rem;
            }
        }
    </style>

    <div class="filter-bar">
        <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; width: 100%;">
            <select name="course_id">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }} - {{ $course->course_name }}
                    </option>
                @endforeach
            </select>

            <select name="status">
                <option value="">All Status</option>
                <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To">

            <button type="submit" class="btn-filter">Apply Filter</button>
            <a href="{{ route('lecturer.attendance.records') }}" class="btn-filter"
                style="background: #f3f4f6; color: #374151; text-decoration: none;">Reset</a>
        </form>
    </div>

    @if ($records->count() > 0)
        <div style="overflow-x: auto;">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Session Date</th>
                        <th>Status</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('lecturer.students') }}?search={{ $record->student->name }}"
                                    class="student-link">
                                    {{ $record->student->name ?? 'Unknown' }}
                                </a>
                                <br>
                                <small style="color: var(--text-gray); font-size: 0.7rem;">
                                    {{ $record->student->student_id ?? 'N/A' }}
                                </small>
                            </td>
                            <td>
                                <strong>{{ $record->session->course->course_name ?? 'N/A' }}</strong>
                                <br>
                                <small style="color: var(--text-gray); font-size: 0.7rem;">
                                    {{ $record->session->course->course_code ?? 'N/A' }}
                                </small>
                            </td>
                            <td>{{ $record->session->session_date ? \Carbon\Carbon::parse($record->session->session_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                <span class="status-badge status-{{ $record->status }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td>
                                @if ($record->is_manual)
                                    <span
                                        style="font-size: 0.7rem; background: #dbeafe; color: #1e40af; padding: 0.1rem 0.5rem; border-radius: 10px;">Manual</span>
                                @else
                                    <span
                                        style="font-size: 0.7rem; background: #dcfce7; color: #166534; padding: 0.1rem 0.5rem; border-radius: 10px;">QR
                                        Scan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ============================================================
        PAGINATION – Uses custom Bootstrap 5 view
        ============================================================ --}}
        <div class="pagination-wrap">
            <span class="info">
                Showing {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} of
                {{ $records->total() }}
            </span>
            {{ $records->links('pagination::bootstrap-5') }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
            <p>No attendance records found.</p>
        </div>
    @endif
@endsection
