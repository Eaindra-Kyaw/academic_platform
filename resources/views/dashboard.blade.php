{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('role', 'Admin')
@section('page-title', 'University Intelligence Dashboard')
@section('welcome-text', 'Welcome back, {{ Auth::user()->name }}')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .stat-card .label {
            font-size: 0.7rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .change {
            font-size: 0.65rem;
            margin-top: 0.15rem;
        }

        .stat-card .change.positive {
            color: #10b981;
        }

        .stat-card .change.negative {
            color: #ef4444;
        }

        .stat-card .icon {
            font-size: 1.5rem;
            opacity: 0.7;
        }

        .stat-card .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            background: #fafafa;
        }

        .card-body {
            padding: 1.25rem;
        }

        .table {
            width: 100%;
            font-size: 0.85rem;
        }

        .table th {
            text-align: left;
            padding: 0.5rem 0.75rem;
            font-size: 0.65rem;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-primary {
            background: #e0e7ff;
            color: #3730a3;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-card .number {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-card {
                padding: 0.75rem;
            }

            .stat-card .number {
                font-size: 1.2rem;
            }
        }
    </style>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="number">{{ $totalStudents ?? 0 }}</div>
                    <div class="label">Total Students</div>
                </div>
                <span class="icon">👨‍🎓</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="number">{{ $totalLecturers ?? 0 }}</div>
                    <div class="label">Total Lecturers</div>
                </div>
                <span class="icon">👨‍🏫</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="number">{{ $totalCourses ?? 0 }}</div>
                    <div class="label">Active Courses</div>
                </div>
                <span class="icon">📚</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="number">{{ $totalDepartments ?? 0 }}</div>
                    <div class="label">Departments</div>
                </div>
                <span class="icon">🏛️</span>
            </div>
        </div>
    </div>

    {{-- Recent Enrollments --}}
    <div class="card">
        <div class="card-header">📋 Recent Enrollments</div>
        <div class="card-body" style="padding: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $recentEnrollments = \App\Models\Enrollment::with(['student', 'course.department'])
                            ->orderBy('created_at', 'desc')
                            ->limit(10)
                            ->get();
                    @endphp
                    @forelse($recentEnrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->student->name ?? 'N/A' }}</td>
                            <td>{{ $enrollment->course->course_code ?? 'N/A' }}</td>
                            <td>{{ $enrollment->course->department->name ?? 'N/A' }}</td>
                            <td>
                                @if ($enrollment->status == 'approved')
                                    <span class="badge badge-success">✅ Approved</span>
                                @elseif($enrollment->status == 'pending')
                                    <span class="badge badge-warning">⏳ Pending</span>
                                @else
                                    <span class="badge badge-danger">❌ Rejected</span>
                                @endif
                            </td>
                            <td style="font-size:0.75rem;">{{ $enrollment->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:2rem; color:#9ca3af;">No enrollments yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
