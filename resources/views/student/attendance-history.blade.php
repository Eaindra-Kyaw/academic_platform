@extends('layouts.app')

@section('title', 'Attendance History')
@section('role', 'Student')
@section('page-title', '📋 Attendance History')
@section('welcome-text', 'View your past attendance records')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --white: #ffffff;
            --text-gray: #6b7280;
            --text-dark: #1e293b;
            --bg-main: #f8fafc;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            --radius: 10px;
        }

        .filter-bar {
            background: var(--white);
            border-radius: var(--radius);
            padding: 0.8rem 1.2rem;
            box-shadow: var(--shadow);
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.8rem 1.2rem;
        }

        .filter-bar select,
        .filter-bar input {
            padding: 0.35rem 0.7rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.85rem;
            background: white;
        }

        .filter-bar .btn {
            padding: 0.35rem 1rem;
            border-radius: 6px;
            border: none;
            font-size: 0.85rem;
            cursor: pointer;
            transition: 0.15s;
        }

        .filter-bar .btn-primary {
            background: var(--primary);
            color: white;
        }

        .filter-bar .btn-secondary {
            background: #e5e7eb;
            color: var(--text-dark);
        }

        .filter-bar .btn-primary:hover {
            background: #061840;
        }

        .filter-bar .btn-secondary:hover {
            background: #d1d5db;
        }

        .records-table {
            width: 100%;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid #e5e7eb;
            box-shadow: var(--shadow);
            border-collapse: collapse;
            overflow: hidden;
        }

        .records-table th {
            background: var(--bg-main);
            padding: 0.6rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-gray);
            border-bottom: 1px solid #e5e7eb;
        }

        .records-table td {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid #f1f3f5;
            font-size: 0.85rem;
        }

        .records-table tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            padding: 0.15rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-present {
            background: #d1fae5;
            color: #065f46;
        }

        .status-late {
            background: #fef3c7;
            color: #92400e;
        }

        .status-absent {
            background: #fee2e2;
            color: #991b1b;
        }

        .method-badge {
            padding: 0.1rem 0.6rem;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .method-qr {
            background: #dbeafe;
            color: #1e40af;
        }

        .method-manual {
            background: #e5e7eb;
            color: #4b5563;
        }

        .pagination-wrap {
            padding: 0.8rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: var(--white);
            border-radius: 0 0 var(--radius) var(--radius);
            border: 1px solid #e5e7eb;
            border-top: none;
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem;
            color: var(--text-gray);
        }

        @media (max-width: 600px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .records-table {
                font-size: 0.75rem;
            }

            .records-table th,
            .records-table td {
                padding: 0.4rem 0.6rem;
            }
        }
    </style>

    <div class="filter-bar">
        <form method="GET" style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.8rem 1.2rem; width:100%;">
            <select name="course_id">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }}
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

            <button type="submit" class="btn btn-primary">Apply</button>
            <a href="{{ route('student.attendance.history') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    @if ($records->count() > 0)
        <div style="overflow-x:auto;">
            <table class="records-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Course</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td>{{ $loop->iteration + ($records->currentPage() - 1) * $records->perPage() }}</td>
                            <td>
                                <strong>{{ $record->session->course->course_name ?? 'N/A' }}</strong>
                                <br>
                                <small style="color: var(--text-gray); font-size: 0.7rem;">
                                    {{ $record->session->course->course_code ?? '' }}
                                </small>
                            </td>
                            <td>{{ $record->session->session_date ? \Carbon\Carbon::parse($record->session->session_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>{{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('h:i A') : 'N/A' }}
                            </td>
                            <td><span
                                    class="status-badge status-{{ $record->status }}">{{ ucfirst($record->status) }}</span>
                            </td>
                            <td>
                                <span class="method-badge method-{{ $record->is_manual ? 'manual' : 'qr' }}">
                                    {{ $record->is_manual ? '✏️ Manual' : '📱 QR' }}
                                </span>
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
            <i class="bi bi-inbox" style="font-size: 1.5rem; display: block; margin-bottom: 0.5rem;"></i>
            <p>No attendance records found.</p>
        </div>
    @endif
@endsection
