@extends('layouts.app')

@section('title', $yearLabel . ' Students - ' . $department->name)
@section('page-title', $yearLabel . ' Students')
@section('welcome-text', $department->name . ' • ' . $department->code . ' • ' . $students->total() . ' students')

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
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --purple: #8b5cf6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .breadcrumb-premium {
            background: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        .breadcrumb-premium .breadcrumb-item {
            font-size: 0.8rem;
        }

        .breadcrumb-premium .breadcrumb-item a {
            color: var(--text-gray);
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .breadcrumb-premium .breadcrumb-item a:hover {
            color: var(--primary);
        }

        .breadcrumb-premium .breadcrumb-item.active {
            color: var(--primary);
            font-weight: 600;
        }

        .breadcrumb-premium .breadcrumb-item+.breadcrumb-item::before {
            color: #d1d5db;
            content: "›";
            font-size: 1.2rem;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .header-actions-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-actions-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-premium {
            padding: 0.4rem 1.2rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-premium-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
        }

        .btn-premium-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .btn-premium-outline {
            background: transparent;
            color: var(--text-gray);
            border: 1px solid rgba(10, 36, 99, 0.1);
        }

        .btn-premium-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(212, 160, 23, 0.08);
            transform: translateY(-2px);
        }

        .stats-premium-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.75rem;
        }

        .stat-premium-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            overflow: hidden;
        }

        .stat-premium-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            border-radius: 0 4px 4px 0;
        }

        .stat-premium-card.blue::before {
            background: var(--info);
        }

        .stat-premium-card.green::before {
            background: var(--success);
        }

        .stat-premium-card.yellow::before {
            background: var(--warning);
        }

        .stat-premium-card.red::before {
            background: var(--danger);
        }

        .stat-premium-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary);
        }

        .stat-premium-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .stat-premium-card .icon.blue {
            background: var(--info-light);
            color: var(--info);
        }

        .stat-premium-card .icon.green {
            background: var(--success-light);
            color: var(--success);
        }

        .stat-premium-card .icon.yellow {
            background: var(--warning-light);
            color: var(--warning);
        }

        .stat-premium-card .icon.red {
            background: var(--danger-light);
            color: var(--danger);
        }

        .stat-premium-card .info {
            flex: 1;
        }

        .stat-premium-card .info .number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .stat-premium-card .info .number.green {
            color: var(--success);
        }

        .stat-premium-card .info .number.yellow {
            color: var(--warning);
        }

        .stat-premium-card .info .number.red {
            color: var(--danger);
        }

        .stat-premium-card .info .label {
            font-size: 0.65rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-premium-wrapper {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .table-premium-header {
            padding: 1rem 1.5rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .table-premium-header .title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-premium-header .title i {
            color: var(--primary);
        }

        .table-premium-header .title .count-badge {
            background: var(--primary);
            color: var(--white);
            font-size: 0.6rem;
            padding: 0.05rem 0.6rem;
            border-radius: 1rem;
        }

        .table-premium-header .toolbar {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .toolbar .search-box {
            display: flex;
            align-items: center;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.1);
            border-radius: 8px;
            padding: 0.2rem 0.6rem;
            transition: var(--transition);
        }

        .toolbar .search-box:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .toolbar .search-box input {
            border: none;
            outline: none;
            padding: 0.3rem 0.4rem;
            font-size: 0.75rem;
            color: var(--text-dark);
            background: transparent;
            width: 150px;
        }

        .toolbar .search-box input::placeholder {
            color: #9ca3af;
        }

        .toolbar .search-box i {
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .table-premium {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .table-premium thead th {
            padding: 0.6rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-gray);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            background: #fafbfc;
        }

        .table-premium tbody td {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .table-premium tbody tr {
            transition: var(--transition);
        }

        .table-premium tbody tr:hover {
            background: #fafbfc;
        }

        .table-premium tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-id {
            background: #f1f5f9;
            color: var(--text-gray);
            padding: 0.1rem 0.6rem;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 600;
            font-family: monospace;
        }

        .badge-risk-premium {
            padding: 0.1rem 0.7rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-risk-premium.low {
            background: var(--success-light);
            color: #166534;
        }

        .badge-risk-premium.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .badge-risk-premium.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .attendance-pill-premium {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.1rem 0.7rem;
            border-radius: 1rem;
            display: inline-block;
        }

        .attendance-pill-premium.high {
            background: var(--success-light);
            color: #166534;
        }

        .attendance-pill-premium.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .attendance-pill-premium.low {
            background: var(--danger-light);
            color: #991b1b;
        }

        .btn-action-premium {
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.65rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-action-premium:hover {
            transform: translateY(-1px);
        }

        .btn-view-premium {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-view-premium:hover {
            background: #bfdbfe;
        }

        .btn-message-premium {
            background: var(--warning-light);
            color: #92400e;
        }

        .btn-message-premium:hover {
            background: #fde68a;
        }

        .pagination-premium-wrapper {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            background: #fafbfc;
        }

        .pagination-premium-wrapper .info {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .pagination-premium-wrapper .info strong {
            color: var(--text-dark);
        }

        .pagination-premium-wrapper .pagination {
            margin: 0;
            gap: 0.25rem;
        }

        .pagination-premium-wrapper .pagination .page-link {
            border: none;
            color: var(--text-gray);
            font-size: 0.75rem;
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            transition: var(--transition);
            background: transparent;
        }

        .pagination-premium-wrapper .pagination .page-link:hover {
            background: rgba(212, 160, 23, 0.1);
            color: var(--primary);
        }

        .pagination-premium-wrapper .pagination .active .page-link {
            background: var(--primary);
            color: var(--white);
        }

        .pagination-premium-wrapper .pagination .disabled .page-link {
            color: #d1d5db;
            cursor: not-allowed;
        }

        .confirm-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .confirm-overlay.show {
            display: flex;
        }

        .confirm-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .confirm-box .icon {
            text-align: center;
            font-size: 2.5rem;
            color: var(--success);
            margin-bottom: 0.5rem;
        }

        .confirm-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.3rem 0;
        }

        .confirm-box p {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-gray);
            margin: 0 0 1.5rem 0;
        }

        .confirm-box .buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .confirm-box .btn-confirm-cancel {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(10, 36, 99, 0.1);
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .confirm-box .btn-confirm-cancel:hover {
            background: #f3f4f6;
        }

        .confirm-box .btn-confirm-export {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: var(--success);
            color: var(--white);
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: var(--transition);
        }

        .confirm-box .btn-confirm-export:hover {
            background: #059669;
        }

        .message-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .message-modal-overlay.show {
            display: flex;
        }

        .message-modal-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        .message-modal-box .icon {
            text-align: center;
            font-size: 2.5rem;
            color: var(--info);
            margin-bottom: 0.5rem;
        }

        .message-modal-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.3rem 0;
        }

        .message-modal-box p {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-gray);
            margin: 0 0 1rem 0;
        }

        .message-modal-box textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 6px;
            font-size: 0.8rem;
            resize: vertical;
            min-height: 80px;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
        }

        .message-modal-box textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .message-modal-box input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 6px;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
        }

        .message-modal-box input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .message-modal-box .buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .message-modal-box .btn-send {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: var(--primary);
            color: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }

        .message-modal-box .btn-send:hover {
            background: var(--primary-light);
        }

        .message-modal-box .btn-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .message-modal-box .btn-close-modal {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(10, 36, 99, 0.1);
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .message-modal-box .btn-close-modal:hover {
            background: #f3f4f6;
        }

        @media (max-width: 1024px) {
            .stats-premium-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .header-actions {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions-left,
            .header-actions-right {
                width: 100%;
            }

            .stats-premium-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-premium-card {
                padding: 1rem;
            }

            .stat-premium-card .info .number {
                font-size: 1.2rem;
            }

            .stat-premium-card .icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .table-premium {
                font-size: 0.7rem;
            }

            .table-premium thead th,
            .table-premium tbody td {
                padding: 0.4rem 0.6rem;
            }

            .toolbar .search-box input {
                width: 100px;
            }

            .pagination-premium-wrapper {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .stats-premium-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-premium-card {
                padding: 0.75rem;
            }

            .stat-premium-card .info .number {
                font-size: 1rem;
            }

            .stat-premium-card .icon {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }

            .table-premium thead th,
            .table-premium tbody td {
                padding: 0.3rem 0.4rem;
                font-size: 0.65rem;
            }

            .toolbar .search-box input {
                width: 80px;
                font-size: 0.65rem;
            }

            .btn-premium {
                font-size: 0.7rem;
                padding: 0.3rem 0.8rem;
            }
        }
    </style>

    <nav aria-label="breadcrumb" class="breadcrumb-premium">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-house"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Departments</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.departments.show', $department) }}">{{ $department->code }}</a></li>
            <li class="breadcrumb-item active">{{ $yearLabel }}</li>
        </ol>
    </nav>

    <div class="header-actions">
        <div class="header-actions-left">
            <a href="{{ route('admin.departments.show', $department) }}" class="btn-premium btn-premium-outline">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
        <div class="header-actions-right">
            <button type="button" class="btn-premium btn-premium-primary" id="exportBtn">
                <i class="bi bi-download"></i> Export CSV
            </button>
        </div>
    </div>

    <div class="stats-premium-grid">
        <div class="stat-premium-card blue">
            <div class="icon blue"><i class="bi bi-people"></i></div>
            <div class="info">
                <div class="number">{{ $students->total() }}</div>
                <div class="label">Total Students</div>
            </div>
        </div>
        <div class="stat-premium-card green">
            <div class="icon green"><i class="bi bi-book"></i></div>
            <div class="info">
                <div class="number">{{ $students->first()->enrollments->where('status', 'approved')->count() ?? 0 }}</div>
                <div class="label">Total Courses</div>
            </div>
        </div>
        <div class="stat-premium-card yellow">
            <div class="icon yellow"><i class="bi bi-graph-up"></i></div>
            <div class="info">
                <div class="number" style="color:var(--warning);">
                    {{ number_format($students->avg('attendance_percentage') ?? 0, 1) }}%
                </div>
                <div class="label">Avg Attendance</div>
            </div>
        </div>
        <div class="stat-premium-card red">
            <div class="icon red"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="info">
                <div class="number red">
                    {{ $students->filter(function ($s) {return ($s->attendance_percentage ?? 0) < 75;})->count() }}
                </div>
                <div class="label">At Risk</div>
            </div>
        </div>
    </div>

    <div class="table-premium-wrapper">
        <div class="table-premium-header">
            <div class="title">
                <i class="bi bi-list-ul"></i> Student List
                <span class="count-badge">{{ $students->total() }}</span>
            </div>
            <div class="toolbar">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search students..." id="searchStudent">
                </div>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-premium" id="studentTable">
                <thead>
                    <tr>
                        <th style="min-width:100px;">Roll No</th>
                        <th style="min-width:140px;">Name</th>
                        <th style="min-width:170px;">Email</th>
                        <th style="text-align:center; min-width:60px;">Courses</th>
                        <th style="text-align:center; min-width:90px;">Attendance</th>
                        <th style="text-align:center; min-width:75px;">Risk</th>
                        <th style="text-align:center; min-width:110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $attendance = $student->attendance_percentage ?? 0;
                            $risk = $attendance >= 75 ? 'Low' : ($attendance >= 60 ? 'Medium' : 'High');
                            $riskClass = strtolower($risk);
                            $attendanceClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                            $coursesCount = $student->enrollments->where('status', 'approved')->count();
                            $nameParts = explode(' ', $student->name);
                            $initials = '';
                            foreach ($nameParts as $part) {
                                if (!in_array($part, ['Dr.', 'Daw', 'Mg', 'U'])) {
                                    $initials .= substr($part, 0, 1);
                                }
                            }
                            $initials = strtoupper(substr($initials, 0, 2));
                        @endphp
                        <tr>
                            <td>
                                <span class="badge-id">{{ $student->student_id ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <div
                                        style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg, var(--primary), var(--primary-light)); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.6rem; flex-shrink:0;">
                                        {{ $initials }}
                                    </div>
                                    <span style="font-weight:500; color:var(--text-dark);">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td style="color:var(--text-gray); font-size:0.75rem;">{{ $student->email }}</td>
                            <td style="text-align:center; font-weight:600; color:var(--text-dark); font-size:0.9rem;">
                                {{ $coursesCount }}
                            </td>
                            <td style="text-align:center;">
                                <span class="attendance-pill-premium {{ $attendanceClass }}">
                                    {{ number_format($attendance, 1) }}%
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span class="badge-risk-premium {{ $riskClass }}">{{ $risk }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex; gap:0.3rem; justify-content:center;">
                                    <a href="{{ route('admin.students.show', $student) }}"
                                        class="btn-action-premium btn-view-premium" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn-action-premium btn-message-premium"
                                        title="Send Message"
                                        onclick="openMessageModal('{{ addslashes($student->name) }}', '{{ $student->id }}', '{{ $student->email }}')">
                                        <i class="bi bi-chat"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:2.5rem; color:var(--text-gray);">
                                <div style="font-size:2rem; margin-bottom:0.5rem;">📚</div>
                                <p style="font-size:0.9rem; margin:0;">No students found in {{ $yearLabel }}</p>
                                <p style="font-size:0.75rem; margin:0.2rem 0 0;">Students will appear here once enrolled</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-premium-wrapper">
            <div class="info">
                <i class="bi bi-info-circle"></i>
                Showing <strong>{{ $students->firstItem() ?? 0 }}</strong> to
                <strong>{{ $students->lastItem() ?? 0 }}</strong>
                of <strong>{{ $students->total() }}</strong> students
            </div>
            <div>
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <div class="confirm-overlay" id="exportConfirm">
        <div class="confirm-box">
            <div class="icon">📊</div>
            <h4>Export Students</h4>
            <p>Are you sure you want to export <strong>{{ $students->total() }}</strong> students from
                {{ $yearLabel }}?<br>This will download a CSV file.</p>
            <div class="buttons">
                <button class="btn-confirm-cancel" onclick="closeExportConfirm()">Cancel</button>
                <a href="{{ route('admin.departments.year.students.export', [$department, $year]) }}"
                    class="btn-confirm-export" onclick="closeExportConfirm()">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>
        </div>
    </div>

    <div class="message-modal-overlay" id="messageModal">
        <div class="message-modal-box">
            <div class="icon">✉️</div>
            <h4>Send Message</h4>
            <p id="messageRecipient">To: <strong></strong></p>
            <div id="messageStatus"></div>
            <form id="messageForm" onsubmit="sendMessage(event)">
                <input type="hidden" id="recipientId" value="">
                <input type="text" id="messageSubject" placeholder="Subject (optional)">
                <textarea id="messageContent" placeholder="Type your message here..." required></textarea>
                <div class="buttons">
                    <button type="button" class="btn-close-modal" onclick="closeMessageModal()">Cancel</button>
                    <button type="submit" class="btn-send" id="sendBtn">
                        <i class="bi bi-send"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Search
        document.getElementById('searchStudent').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#studentTable tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Export
        function showExportConfirm() {
            document.getElementById('exportConfirm').classList.add('show');
        }

        function closeExportConfirm() {
            document.getElementById('exportConfirm').classList.remove('show');
        }

        document.getElementById('exportBtn').addEventListener('click', showExportConfirm);

        document.getElementById('exportConfirm').addEventListener('click', function(e) {
            if (e.target === this) {
                closeExportConfirm();
            }
        });

        // Message
        function openMessageModal(studentName, studentId, studentEmail) {
            document.getElementById('recipientId').value = studentId;
            document.getElementById('messageRecipient').innerHTML = 'To: <strong>' + studentName + ' (' + studentEmail +
                ')</strong>';
            document.getElementById('messageContent').value = '';
            document.getElementById('messageSubject').value = '';
            document.getElementById('messageStatus').className = '';
            document.getElementById('messageStatus').style.display = 'none';
            document.getElementById('sendBtn').disabled = false;
            document.getElementById('sendBtn').innerHTML = '<i class="bi bi-send"></i> Send';
            document.getElementById('messageModal').classList.add('show');
        }

        function closeMessageModal() {
            document.getElementById('messageModal').classList.remove('show');
        }

        function sendMessage(e) {
            e.preventDefault();

            const recipientId = document.getElementById('recipientId').value;
            const subject = document.getElementById('messageSubject').value;
            const message = document.getElementById('messageContent').value;
            const sendBtn = document.getElementById('sendBtn');
            const statusDiv = document.getElementById('messageStatus');

            if (!message.trim()) {
                statusDiv.className = 'error';
                statusDiv.style.display = 'block';
                statusDiv.textContent = 'Please enter a message.';
                return;
            }

            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="bi bi-hourglass"></i> Sending...';

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route('admin.messages.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        recipient_id: recipientId,
                        subject: subject || 'Message from Admin',
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusDiv.className = 'success';
                        statusDiv.style.display = 'block';
                        statusDiv.textContent = '✅ ' + data.message;
                        sendBtn.innerHTML = '<i class="bi bi-check"></i> Sent!';
                        sendBtn.disabled = true;

                        setTimeout(() => {
                            closeMessageModal();
                        }, 2000);
                    } else {
                        statusDiv.className = 'error';
                        statusDiv.style.display = 'block';
                        statusDiv.textContent = '❌ ' + (data.message || 'Failed to send message.');
                        sendBtn.innerHTML = '<i class="bi bi-send"></i> Send';
                        sendBtn.disabled = false;
                    }
                })
                .catch(error => {
                    statusDiv.className = 'error';
                    statusDiv.style.display = 'block';
                    statusDiv.textContent = '❌ Network error. Please try again.';
                    sendBtn.innerHTML = '<i class="bi bi-send"></i> Send';
                    sendBtn.disabled = false;
                    console.error('Error:', error);
                });
        }

        document.getElementById('messageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMessageModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeExportConfirm();
                closeMessageModal();
            }
        });
    </script>
@endsection
