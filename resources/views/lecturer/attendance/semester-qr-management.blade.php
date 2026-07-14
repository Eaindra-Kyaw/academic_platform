@extends('layouts.app')

@section('title', 'Semester QR Management')
@section('role', 'Lecturer')
@section('page-title', '📚 Semester QR Management')
@section('welcome-text', 'Manage all your semester QR codes')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
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
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .stat-number.active {
            color: var(--success);
        }

        .stat-number.ended {
            color: var(--text-gray);
        }

        .stat-number.total {
            color: var(--primary);
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .qr-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            margin-bottom: 1rem;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .qr-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .qr-card .card-header {
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            flex-wrap: wrap;
            gap: 8px;
        }

        .qr-card .card-header.active {
            background: var(--success-light);
        }

        .qr-card .card-header.ended {
            background: #f3f4f6;
        }

        .qr-card .card-body {
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .qr-card .course-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 16px;
        }

        .qr-card .course-code {
            font-size: 13px;
            color: var(--text-gray);
        }

        .qr-card .course-details {
            font-size: 13px;
            color: var(--text-gray);
        }

        .qr-card .course-details i {
            margin-right: 4px;
        }

        .badge-status {
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-status.active {
            background: var(--success-light);
            color: #166534;
        }

        .badge-status.ended {
            background: #f3f4f6;
            color: var(--text-gray);
        }

        .badge-status::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .badge-status.active::before {
            background: var(--success);
        }

        .badge-status.ended::before {
            background: #9ca3af;
        }

        .btn-sm {
            padding: 4px 14px;
            border-radius: 8px;
            font-size: 12px;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-family: 'Inter', sans-serif;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
        }

        .btn-view {
            background: var(--primary);
            color: var(--white);
        }

        .btn-view:hover {
            background: var(--primary-dark);
            color: var(--white);
        }

        .btn-deactivate {
            background: var(--danger);
            color: var(--white);
        }

        .btn-deactivate:hover {
            background: #b91c1c;
            color: var(--white);
        }

        .btn-regenerate {
            background: var(--warning);
            color: var(--white);
        }

        .btn-regenerate:hover {
            background: #d97706;
            color: var(--white);
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 48px;
            color: #d1d5db;
            display: block;
            margin-bottom: 15px;
        }

        .btn-create {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
        }

        .btn-create:hover {
            background: var(--primary-dark);
            color: var(--white);
        }

        .alert-success {
            background: var(--success-light);
            color: #166534;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            border-left: 4px solid var(--success);
        }

        .alert-success i {
            margin-right: 8px;
        }

        .alert-error {
            background: var(--danger-light);
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            border-left: 4px solid var(--danger);
        }

        .alert-error i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .qr-card .card-body {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .qr-card .card-body .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }
        }
    </style>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number total">{{ $semesterQrs->count() }}</div>
            <div class="stat-label">Total Semester QR</div>
        </div>
        <div class="stat-card">
            <div class="stat-number active">{{ $activeCount }}</div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-card">
            <div class="stat-number ended">{{ $endedCount }}</div>
            <div class="stat-label">Ended</div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert-success">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert-error">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom: 20px;">
        <a href="{{ route('lecturer.attendance.take') }}" class="btn-create">
            <i class="bi bi-plus-circle"></i> Create New Semester QR
        </a>
    </div>

    @if ($semesterQrs->count() > 0)
        @foreach ($semesterQrs as $qr)
            @php
                $isActive = $qr->status == 'active';
                $headerClass = $isActive ? 'active' : 'ended';
                $badgeClass = $isActive ? 'active' : 'ended';
            @endphp
            <div class="qr-card">
                <div class="card-header {{ $headerClass }}">
                    <div>
                        <span class="course-name">{{ $qr->course->course_name ?? 'Unknown' }}</span>
                        <span class="course-code">({{ $qr->course->course_code ?? 'N/A' }})</span>
                    </div>
                    <span class="badge-status {{ $badgeClass }}">
                        {{ ucfirst($qr->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="course-details">
                        <div><i class="bi bi-calendar"></i> Created: {{ $qr->created_at->format('M d, Y') }}</div>
                        <div><i class="bi bi-clock"></i> {{ $qr->created_at->format('h:i A') }}</div>
                        <div><i class="bi bi-door-open"></i> Room: {{ $qr->room ?? 'N/A' }}</div>
                        @if ($isActive)
                            <div style="color: var(--success); font-size: 12px; margin-top: 4px;">
                                <i class="bi bi-check-circle"></i> Students can scan this QR
                            </div>
                        @else
                            <div style="color: var(--text-gray); font-size: 12px; margin-top: 4px;">
                                <i class="bi bi-x-circle"></i> QR is deactivated
                            </div>
                        @endif
                    </div>
                    <div class="actions">
                        @if ($isActive)
                            <a href="{{ route('lecturer.attendance.take') }}?session={{ $qr->id }}"
                                class="btn-sm btn-view">
                                <i class="bi bi-eye"></i> View QR
                            </a>
                            <form method="POST" action="{{ route('lecturer.semester-qr.end', $qr->id) }}"
                                style="display:inline;"
                                onsubmit="return confirm('Are you sure you want to deactivate this semester QR? Students will no longer be able to scan it.')">
                                @csrf
                                <button type="submit" class="btn-sm btn-deactivate">
                                    <i class="bi bi-stop-circle"></i> End QR
                                </button>
                            </form>
                        @else
                            <span style="font-size: 13px; color: var(--text-gray);">Deactivated</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <h4>No Semester QR Codes</h4>
            <p>You haven't created any semester QR codes yet.</p>
            <a href="{{ route('lecturer.attendance.take') }}" class="btn-create" style="margin-top: 10px;">
                <i class="bi bi-plus-circle"></i> Create Your First Semester QR
            </a>
        </div>
    @endif

    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('lecturer.attendance.take') }}" style="color: var(--primary); text-decoration: none;">
            <i class="bi bi-arrow-left"></i> Back to Take Attendance
        </a>
    </div>
@endsection
