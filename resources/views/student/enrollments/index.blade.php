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
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .enrollments-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 1rem;
            overflow: hidden;
        }

        .enrollments-table th {
            background: #fafbfc;
            padding: 1rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 700;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .enrollments-table td {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .rejection-reason {
            font-size: 0.7rem;
            color: #dc2626;
            margin-top: 0.25rem;
        }

        .approval-date {
            font-size: 0.7rem;
            color: #166534;
            margin-top: 0.25rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #800000;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        @media (max-width: 768px) {
            .enrollments-table {
                min-width: 600px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div>
        <h3 style="color: #800000; margin-bottom: 1rem;">📋 My Enrollment History</h3>

        @if (session('success'))
            <div
                style="background: #dcfce7; color: #166534; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Summary -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ isset($enrollments) ? $enrollments->where('status', 'pending')->count() : 0 }}
                </div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ isset($enrollments) ? $enrollments->where('status', 'approved')->count() : 0 }}
                </div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ isset($enrollments) ? $enrollments->where('status', 'rejected')->count() : 0 }}
                </div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        @if (isset($enrollments) && $enrollments->count() > 0)
            <div style="overflow-x: auto;">
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
                            <tr>
                                <td><strong>{{ $enrollment->course->course_code }}</strong></td>
                                <td>{{ $enrollment->course->course_name }}</td>
                                <td>{{ $enrollment->created_at->format('d M Y') }}</td>
                                <td>
                                    @if ($enrollment->status == 'pending')
                                        <span class="status-badge status-pending">
                                            <i class="bi bi-clock-history"></i> Pending
                                        </span>
                                    @elseif($enrollment->status == 'approved')
                                        <span class="status-badge status-approved">
                                            <i class="bi bi-check-circle"></i> Approved
                                        </span>
                                    @else
                                        <span class="status-badge status-rejected">
                                            <i class="bi bi-x-circle"></i> Rejected
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($enrollment->status == 'approved' && $enrollment->approved_at)
                                        <span class="approval-date">
                                            <i class="bi bi-calendar-check"></i>
                                            {{ \Carbon\Carbon::parse($enrollment->approved_at)->format('d M Y') }}
                                        </span>
                                    @elseif($enrollment->status == 'rejected' && $enrollment->rejected_at)
                                        <span class="rejection-reason" style="color: #dc2626;">
                                            <i class="bi bi-calendar-x"></i>
                                            {{ \Carbon\Carbon::parse($enrollment->rejected_at)->format('d M Y') }}
                                        </span>
                                    @else
                                        <span style="font-size: 0.7rem; color: #6b7280;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($enrollment->status == 'rejected' && $enrollment->rejection_reason)
                                        <div class="rejection-reason">
                                            <strong>Reason:</strong> {{ $enrollment->rejection_reason }}
                                        </div>
                                    @elseif($enrollment->status == 'approved' && $enrollment->approved_at)
                                        <div class="approval-date">
                                            <i class="bi bi-check-circle"></i> Enrollment confirmed
                                        </div>
                                    @else
                                        <span style="font-size: 0.7rem; color: #6b7280;">Waiting for review</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #9ca3af;"></i>
                <p>You haven't requested any enrollments yet.</p>
                <a href="{{ route('student.courses.available') }}"
                    style="background: #800000; color: white; border: none; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; display: inline-block; margin-top: 1rem;">
                    <i class="bi bi-book"></i> Browse Available Courses
                </a>
            </div>
        @endif
    </div>

    <script>
        function openUniBot() {
            alert(
                '🤖 Uni Bot: How can I help you?\n\n- What is my enrollment status?\n- Check my courses\n- Track my attendance'
            );
        }
    </script>
@endsection
