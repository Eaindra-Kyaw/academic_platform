@extends('layouts.app')

@section('title', $student->name)
@section('page-title', 'Student Profile')

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
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }

        .back-link:hover {
            color: var(--primary);
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
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
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
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stats-grid .stat-box .number.green {
            color: var(--success);
        }

        .stats-grid .stat-box .number.yellow {
            color: var(--warning);
        }

        .stats-grid .stat-box .number.red {
            color: var(--danger);
        }

        .stats-grid .stat-box .label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.75rem 1.5rem;
            margin-top: 0.75rem;
        }

        .info-grid .info-item {
            font-size: 0.85rem;
            color: var(--text-gray);
        }

        .info-grid .info-item strong {
            color: var(--text-dark);
            font-weight: 600;
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.1rem;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .quick-actions .btn {
            padding: 0.3rem 0.9rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: var(--bg-main);
            color: var(--text-dark);
            border: 1px solid transparent;
        }

        .quick-actions .btn:hover {
            background: var(--primary);
            color: var(--white);
        }

        .quick-actions .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .quick-actions .btn-primary:hover {
            background: var(--primary-dark);
        }

        .quick-actions .btn-danger {
            background: var(--danger);
            color: var(--white);
        }

        .quick-actions .btn-danger:hover {
            background: #dc2626;
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
            background: #d1fae5;
            color: #166534;
        }

        .attendance-pill.medium {
            background: #fef3c7;
            color: #92400e;
        }

        .attendance-pill.low {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge {
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .status-badge.eligible {
            background: #d1fae5;
            color: #166534;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.not_eligible {
            background: #fee2e2;
            color: #991b1b;
        }

        .risk-badge {
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .risk-badge.low {
            background: #d1fae5;
            color: #166534;
        }

        .risk-badge.medium {
            background: #fef3c7;
            color: #92400e;
        }

        .risk-badge.high {
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .profile-card {
                padding: 1.25rem;
            }

            .info-grid {
                grid-template-columns: 1fr 1fr;
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

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div style="max-width:1100px; margin:0 auto;">
        <a href="{{ url()->previous() }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Back
        </a>

        {{-- Profile Card with Roll Number --}}
        <div class="profile-card">
            <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;">
                <div class="profile-avatar">{{ substr($student->name, 0, 2) }}</div>
                <div style="flex:1;">
                    <div style="display:flex; flex-wrap:wrap; align-items:baseline; gap:0.5rem 1rem;">
                        <h2 style="font-size:1.3rem; font-weight:700; color:var(--text-dark); margin:0;">{{ $student->name }}
                        </h2>
                        <span
                            style="background:var(--primary); color:white; padding:0.1rem 0.7rem; border-radius:1rem; font-size:0.7rem; font-weight:600;">
                            {{ $student->student_id ?? 'No ID' }}
                        </span>
                    </div>
                    <p style="color:var(--text-gray); font-size:0.85rem; margin:0.2rem 0;">
                        <i class="bi bi-envelope"></i> {{ $student->email }}
                    </p>
                    <p style="color:var(--text-gray); font-size:0.85rem; margin:0;">
                        <i class="bi bi-building"></i> {{ $student->department->name ?? 'No Department' }}
                        <span style="margin:0 0.5rem;">|</span>
                        <i class="bi bi-mortarboard"></i> {{ $student->getYearLabelAttribute() }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Enrolled Courses Table --}}
        <div class="courses-table-wrap">
            <div class="table-header">
                <i class="bi bi-book"></i> Enrolled Courses
            </div>
            <div style="overflow-x:auto;">
                <table class="courses-table">
                    <tbody>
                        @forelse($student->enrollments->where('status', 'approved') as $enrollment)
                            {{-- @php
                                $eval = $enrollment->evaluation ?? null;
                                $attendance = $eval ? $eval->attendance_percentage : 0;
                                $eligibility = $eval ? $eval->eligibility_status : 'not_eligible';
                                $riskLevel = $eval ? $eval->risk_level : 'Low';
                                $attClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                                $eligClass = $eligibility;
                                $riskClass = strtolower($riskLevel);
                            @endphp --}}
                            <tr>
                                <td>
                                    <div style="font-weight:600; color:var(--text-dark);">
                                        {{ $enrollment->course->course_code }}
                                    </div>
                                    <div style="font-size:0.7rem; color:var(--text-gray);">
                                        {{ $enrollment->course->course_name }}
                                    </div>
                                </td>
                                {{-- <td style="color:var(--text-gray);">
                                    {{ $enrollment->course->department->code ?? 'N/A' }}
                                </td> --}}
                                {{-- <td style="text-align:center;">
                                    <span class="attendance-pill {{ $attClass }}">
                                        {{ number_format($attendance, 1) }}%
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <span class="status-badge {{ $eligClass }}">
                                        {{ ucfirst($eligibility) }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <span class="risk-badge {{ $riskClass }}">
                                        {{ $riskLevel }}
                                    </span>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:2rem; text-align:center; color:var(--text-gray);">
                                    No courses enrolled
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
