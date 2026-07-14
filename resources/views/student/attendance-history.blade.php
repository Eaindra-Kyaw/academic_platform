@extends('layouts.app')

@section('title', 'Attendance History')
@section('role', 'Student')
@section('page-title', '📋 Attendance History')
@section('welcome-text', 'View all your past attendance records')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
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

        .pagination-wrap {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            <a href="{{ route('student.attendance.history') }}" class="btn-filter"
                style="background: #f3f4f6; color: #374151; text-decoration: none;">Reset</a>
        </form>
    </div>

    @if ($records->count() > 0)
        <div style="overflow-x: auto;">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Course</th>
                        <th>Session Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $record->session->course->course_name ?? 'N/A' }}</strong>
                                <br>
                                <small style="color: var(--text-gray); font-size: 0.7rem;">
                                    {{ $record->session->course->course_code ?? 'N/A' }}
                                </small>
                            </td>
                            <td>{{ $record->session->session_date ? \Carbon\Carbon::parse($record->session->session_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>{{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('h:i A') : 'N/A' }}
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
        <div class="pagination-wrap">
            <span>Showing {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} of
                {{ $records->total() }}</span>
            {{ $records->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
            <p>No attendance records found.</p>
        </div>
    @endif
@endsection
