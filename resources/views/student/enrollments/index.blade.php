@extends('layouts.app')

@section('title', 'My Enrollments')
@section('role', 'Student')
@section('page-title', 'My Enrollments')
@section('welcome-text', 'Track your enrollment requests and status')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ── Stats Row ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem 1.2rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .stat-card .number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1.2;
        }

        .stat-card .label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 600;
        }

        /* ── Table ── */
        .table-wrap {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .enrollments-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .enrollments-table thead {
            background: var(--bg-main);
        }

        .enrollments-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-gray);
            border-bottom: 2px solid rgba(10, 36, 99, 0.06);
        }

        .enrollments-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .enrollments-table tbody tr {
            transition: background 0.15s;
        }

        .enrollments-table tbody tr:hover {
            background: #f8f9fc;
        }

        .enrollments-table tbody tr:last-child td {
            border-bottom: none;
        }

        .enrollments-table .course-code {
            font-weight: 700;
            color: var(--primary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #d1fae5;
            color: #166534;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .date-info {
            font-size: 0.75rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .date-info.approved {
            color: var(--success);
        }

        .date-info.rejected {
            color: var(--danger);
        }

        .details-text {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .details-text .reason {
            color: var(--danger);
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-gray);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .empty-state .btn-primary {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
            font-weight: 500;
        }

        .empty-state .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .table-wrap {
                overflow-x: auto;
            }

            .enrollments-table {
                min-width: 700px;
                font-size: 0.8rem;
            }

            .enrollments-table th,
            .enrollments-table td {
                padding: 0.5rem 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Alerts --}}
    @if (session('success'))
        <div
            style="background: #d1fae5; color: #166534; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div
            style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="number">{{ isset($enrollments) ? $enrollments->where('status', 'pending')->count() : 0 }}</div>
            <div class="label">⏳ Pending</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ isset($enrollments) ? $enrollments->where('status', 'approved')->count() : 0 }}</div>
            <div class="label">✅ Approved</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ isset($enrollments) ? $enrollments->where('status', 'rejected')->count() : 0 }}</div>
            <div class="label">❌ Rejected</div>
        </div>
    </div>

    {{-- Table --}}
    @if (isset($enrollments) && $enrollments->count() > 0)
        <div class="table-wrap">
            <table class="enrollments-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Approved/Rejected Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enrollments as $enrollment)
                        @php
                            $status = $enrollment->status;
                            $statusClass = $status;
                            $icon =
                                $status == 'pending'
                                    ? 'bi-clock-history'
                                    : ($status == 'approved'
                                        ? 'bi-check-circle'
                                        : 'bi-x-circle');
                            $date =
                                $status == 'approved' && $enrollment->approved_at
                                    ? \Carbon\Carbon::parse($enrollment->approved_at)->format('d M Y')
                                    : ($status == 'rejected' && $enrollment->rejected_at
                                        ? \Carbon\Carbon::parse($enrollment->rejected_at)->format('d M Y')
                                        : null);
                        @endphp
                        <tr>
                            <td><span class="course-code">{{ $enrollment->course->course_code }}</span></td>
                            <td>{{ $enrollment->course->course_name }}</td>
                            <td>{{ $enrollment->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="status-badge status-{{ $statusClass }}">
                                    <i class="bi {{ $icon }}"></i> {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td>
                                @if ($date)
                                    <span
                                        class="date-info {{ $status == 'approved' ? 'approved' : ($status == 'rejected' ? 'rejected' : '') }}">
                                        <i
                                            class="bi {{ $status == 'approved' ? 'bi-calendar-check' : ($status == 'rejected' ? 'bi-calendar-x' : 'bi-calendar3') }}"></i>
                                        {{ $date }}
                                    </span>
                                @else
                                    <span style="font-size:0.75rem;color:var(--text-gray);">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($status == 'rejected' && $enrollment->rejection_reason)
                                    <span class="details-text reason">
                                        <i class="bi bi-info-circle"></i> {{ $enrollment->rejection_reason }}
                                    </span>
                                @elseif($status == 'approved')
                                    <span class="details-text" style="color:var(--success);">
                                        <i class="bi bi-check-circle"></i> Enrollment confirmed
                                    </span>
                                @else
                                    <span class="details-text">Waiting for review</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>You haven't requested any enrollments yet.</p>
            <a href="{{ route('student.courses.available') }}" class="btn-primary">
                <i class="bi bi-book"></i> Browse Available Courses
            </a>
        </div>
    @endif
@endsection
