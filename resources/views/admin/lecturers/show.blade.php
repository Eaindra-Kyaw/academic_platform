@extends('layouts.app')

@section('title', $lecturer->name)
@section('page-title', $lecturer->name)
@section('welcome-text', 'Lecturer Profile')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #3B82F6;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.85rem;
            padding: 0.3rem 0.8rem;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.1);
            border-radius: 8px;
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }

        .back-link:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        .profile-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stats-grid .stat-box {
            background: var(--white);
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .stats-grid .stat-box .number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stats-grid .stat-box .number.green {
            color: var(--success);
        }

        .stats-grid .stat-box .number.yellow {
            color: var(--warning);
        }

        .stats-grid .stat-box .label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .courses-table-wrap {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .courses-table-wrap .table-header {
            padding: 1rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            font-weight: 600;
            color: var(--text-dark);
        }

        .courses-table-wrap .table-header i {
            color: var(--primary);
            margin-right: 0.4rem;
        }

        .courses-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .courses-table thead th {
            padding: 0.5rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-gray);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .courses-table tbody td {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
        }

        .courses-table tbody tr {
            transition: var(--transition);
        }

        .courses-table tbody tr:hover {
            background: #fafbfc;
        }

        .courses-table tbody tr:last-child td {
            border-bottom: none;
        }

        .attendance-pill {
            font-weight: 600;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            display: inline-block;
            font-size: 0.75rem;
        }

        .attendance-pill.high {
            background: var(--success-light);
            color: #166534;
        }

        .attendance-pill.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .attendance-pill.low {
            background: var(--danger-light);
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .profile-card {
                padding: 1.25rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .profile-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>

    <div style="max-width:1000px; margin:0 auto;">
        <a href="javascript:history.back()" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Lecturers
        </a>

        <div class="profile-card">
            <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;">
                <div class="profile-avatar">{{ substr($lecturer->name, 0, 2) }}</div>
                <div style="flex:1;">
                    <h2 style="font-size:1.5rem; font-weight:700; color:var(--text-dark); margin:0;">{{ $lecturer->name }}
                    </h2>
                    <p style="color:var(--text-gray); font-size:0.9rem; margin:0.2rem 0;">
                        <i class="bi bi-envelope"></i> {{ $lecturer->email }}
                    </p>
                    <p style="color:var(--text-gray); font-size:0.85rem; margin:0;">
                        <i class="bi bi-building"></i> {{ $lecturer->department->name ?? 'No Department' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.lecturers.edit', $lecturer) }}"
                        style="background:var(--bg-main); color:#92400e; padding:0.4rem 1rem; border-radius:8px; text-decoration:none; font-size:0.8rem; transition:var(--transition);">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-box">
                <div class="number">{{ $stats['total_courses'] }}</div>
                <div class="label">Courses Taught</div>
            </div>
            <div class="stat-box">
                <div class="number green">{{ $stats['total_students'] }}</div>
                <div class="label">Total Students</div>
            </div>
            <div class="stat-box">
                <div class="number yellow">{{ number_format($stats['avg_attendance'], 1) }}%</div>
                <div class="label">Avg Attendance</div>
            </div>
        </div>

        <div class="courses-table-wrap">
            <div class="table-header">
                <i class="bi bi-book"></i> Courses Taught
            </div>
            <div style="overflow-x:auto;">
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Department</th>
                            <th style="text-align:center;">Students</th>
                            <th style="text-align:center;">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            @php
                                $attendance = $course->average_attendance ?? 0;
                                $attClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:600; color:var(--text-dark);">{{ $course->course_code }}</div>
                                    <div style="font-size:0.7rem; color:var(--text-gray);">{{ $course->course_name }}</div>
                                </td>
                                <td style="color:var(--text-gray);">{{ $course->department->name ?? 'N/A' }}</td>
                                <td style="text-align:center; font-weight:600; color:var(--text-dark);">
                                    {{ $course->students()->count() }}</td>
                                <td style="text-align:center;">
                                    <span class="attendance-pill {{ $attClass }}">
                                        {{ number_format($attendance, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:2rem; text-align:center; color:var(--text-gray);">
                                    No courses assigned
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
