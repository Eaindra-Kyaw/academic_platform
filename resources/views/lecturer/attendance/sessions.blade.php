@extends('layouts.app')

@section('title', 'Session History')
@section('role', 'Teacher')
@section('page-title', 'Session History')
@section('welcome-text', 'View all attendance sessions with student details')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        /* ============================================================
                       COMPACT DESIGN SYSTEM
                       ============================================================ */
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --info: #3b82f6;
            --info-light: #dbeafe;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 16px rgba(10, 36, 99, 0.08);
            --shadow-lg: 0 8px 32px rgba(10, 36, 99, 0.12);
            --radius: 10px;
            --radius-sm: 6px;
            --transition: all 0.2s ease;
        }

        /* ============================================================
                       STATS ROW
                       ============================================================ */
        .stats-grid-compact {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-box-compact {
            background: var(--white);
            border-radius: var(--radius);
            padding: 0.9rem 1.2rem;
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stat-box-compact:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .stat-box-compact .stat-left .number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1.2;
        }

        .stat-box-compact .stat-left .number.primary {
            color: var(--primary);
        }

        .stat-box-compact .stat-left .number.green {
            color: var(--success);
        }

        .stat-box-compact .stat-left .label {
            font-size: 0.65rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 600;
        }

        .stat-box-compact .stat-icon {
            font-size: 1.4rem;
            opacity: 0.6;
            color: var(--gray-400);
        }

        /* ============================================================
                       FILTER BAR
                       ============================================================ */
        .filter-bar-compact {
            background: var(--white);
            border-radius: var(--radius);
            padding: 0.75rem 1.25rem;
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            margin-bottom: 1.25rem;
        }

        .filter-bar-compact .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 1rem;
            align-items: center;
        }

        .filter-bar-compact .filter-row .filter-group {
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .filter-bar-compact .filter-row .filter-group label {
            font-size: 0.6rem;
            font-weight: 600;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .filter-bar-compact .filter-row .filter-group input,
        .filter-bar-compact .filter-row .filter-group select {
            padding: 0.25rem 0.5rem;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            background: var(--gray-50);
            transition: var(--transition);
            color: var(--gray-700);
            min-width: 100px;
            height: 32px;
        }

        .filter-bar-compact .filter-row .filter-group input:focus,
        .filter-bar-compact .filter-row .filter-group select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.06);
        }

        .filter-bar-compact .filter-actions {
            display: flex;
            gap: 0.4rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--gray-100);
        }

        .btn-filter-compact {
            padding: 0.25rem 0.8rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            height: 32px;
        }

        .btn-filter-compact.primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-filter-compact.primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.2);
        }

        .btn-filter-compact.secondary {
            background: var(--gray-100);
            color: var(--gray-600);
            border: 1px solid var(--gray-200);
        }

        .btn-filter-compact.secondary:hover {
            background: var(--gray-200);
        }

        .active-filters-compact {
            display: flex;
            flex-wrap: wrap;
            gap: 0.3rem;
            margin-top: 0.4rem;
        }

        .filter-badge-compact {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: var(--gray-100);
            padding: 0.1rem 0.5rem;
            border-radius: 12px;
            font-size: 0.65rem;
            color: var(--gray-600);
            border: 1px solid var(--gray-200);
        }

        .filter-badge-compact .remove {
            cursor: pointer;
            color: var(--gray-400);
            font-size: 0.6rem;
            transition: var(--transition);
        }

        .filter-badge-compact .remove:hover {
            color: var(--danger);
        }

        /* ============================================================
                       TABLE - COMPACT ONE ROW PER SESSION
                       ============================================================ */
        .table-wrap-compact {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table-compact {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            table-layout: fixed;
        }

        .table-compact thead {
            background: var(--gray-50);
        }

        .table-compact th {
            padding: 0.5rem 0.6rem;
            text-align: left;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--gray-500);
            letter-spacing: 0.4px;
            border-bottom: 2px solid var(--gray-200);
            white-space: nowrap;
        }

        /* Column widths */
        .table-compact .col-course {
            width: 20%;
        }

        .table-compact .col-date {
            width: 12%;
        }

        .table-compact .col-time {
            width: 10%;
        }

        .table-compact .col-room {
            width: 8%;
        }

        .table-compact .col-periods {
            width: 8%;
            text-align: center;
        }

        .table-compact .col-attendance {
            width: 12%;
        }

        .table-compact .col-status {
            width: 10%;
        }

        .table-compact .col-actions {
            width: 16%;
            text-align: center;
        }

        .table-compact td {
            padding: 0.4rem 0.6rem;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }

        .table-compact tbody tr {
            transition: background 0.15s ease;
            cursor: pointer;
        }

        .table-compact tbody tr:hover {
            background: var(--gray-50);
        }

        .table-compact tbody tr:last-child td {
            border-bottom: none;
        }

        /* Course cell */
        .course-cell .code {
            font-weight: 600;
            color: var(--gray-800);
            font-size: 0.8rem;
        }

        .course-cell .name {
            font-size: 0.65rem;
            color: var(--gray-500);
            display: block;
            margin-top: 0.05rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* QR Mode badge */
        .qr-badge-compact {
            display: inline-block;
            padding: 0.05rem 0.4rem;
            border-radius: 10px;
            font-size: 0.55rem;
            font-weight: 600;
        }

        .qr-badge-compact.dynamic {
            background: #fef3c7;
            color: #92400e;
        }

        .qr-badge-compact.semester {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Status badge */
        .status-badge-compact {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.15rem 0.6rem;
            border-radius: 12px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .status-badge-compact.active {
            background: var(--success-light);
            color: #166534;
        }

        .status-badge-compact.active::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse-dot 1.5s infinite;
        }

        .status-badge-compact.ended {
            background: var(--gray-100);
            color: var(--gray-500);
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        /* Attendance bar */
        .attendance-cell {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .attendance-cell .percent {
            font-weight: 700;
            font-size: 0.8rem;
            min-width: 36px;
            text-align: right;
        }

        .attendance-cell .bar-track {
            flex: 1;
            height: 4px;
            background: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
            min-width: 40px;
        }

        .attendance-cell .bar-track .bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        /* Action buttons */
        .action-group {
            display: flex;
            gap: 0.25rem;
            justify-content: center;
            align-items: center;
        }

        .btn-action-compact {
            padding: 0.15rem 0.5rem;
            border-radius: var(--radius-sm);
            font-size: 0.65rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.15rem;
            height: 26px;
            white-space: nowrap;
        }

        .btn-action-compact.view {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-action-compact.view:hover {
            background: #bfdbfe;
        }

        .btn-action-compact.export {
            background: var(--success-light);
            color: #166534;
        }

        .btn-action-compact.export:hover {
            background: #a7f3d0;
        }

        .btn-action-compact.end {
            background: var(--danger-light);
            color: var(--danger);
        }

        .btn-action-compact.end:hover {
            background: #fca5a5;
        }

        /* ============================================================
                       PAGINATION
                       ============================================================ */
        .pagination-wrap-compact {
            padding: 0.6rem 1rem;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: var(--gray-50);
        }

        .pagination-wrap-compact .info {
            font-size: 0.7rem;
            color: var(--gray-500);
        }

        .pagination-wrap-compact .pagination {
            display: flex;
            flex-wrap: wrap;
            gap: 0.15rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination-wrap-compact .pagination li {
            display: inline-block;
        }

        .pagination-wrap-compact .pagination li a,
        .pagination-wrap-compact .pagination li span {
            display: inline-block;
            padding: 0.15rem 0.5rem;
            min-width: 28px;
            text-align: center;
            font-size: 0.7rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
            background: var(--white);
            color: var(--gray-600);
            text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1.4;
            font-weight: 500;
        }

        .pagination-wrap-compact .pagination li a:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination-wrap-compact .pagination li.active span {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination-wrap-compact .pagination li.disabled span {
            opacity: 0.4;
            cursor: not-allowed;
            background: var(--gray-50);
            border-color: var(--gray-200);
        }

        /* ============================================================
                       EMPTY STATE
                       ============================================================ */
        .empty-state-compact {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--gray-500);
        }

        .empty-state-compact .icon {
            font-size: 2.5rem;
            color: var(--gray-300);
            display: block;
            margin-bottom: 0.5rem;
        }

        .empty-state-compact p {
            font-size: 0.85rem;
            margin: 0;
        }

        /* ============================================================
                       MODAL
                       ============================================================ */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            border-radius: var(--radius);
            max-width: 850px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--gray-50);
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .modal-header h4 {
            margin: 0;
            font-weight: 700;
            color: var(--gray-900);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-header h4 .session-info {
            font-weight: 400;
            color: var(--gray-500);
            font-size: 0.75rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--gray-400);
            cursor: pointer;
            transition: var(--transition);
            padding: 0 4px;
            line-height: 1;
        }

        .modal-close:hover {
            color: var(--gray-700);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 1.25rem;
        }

        .modal-body .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .modal-body .stats-row .stat-box {
            background: var(--gray-50);
            padding: 0.5rem;
            border-radius: var(--radius-sm);
            text-align: center;
            border: 1px solid var(--gray-200);
        }

        .modal-body .stats-row .stat-box .number {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .modal-body .stats-row .stat-box .number.green {
            color: var(--success);
        }

        .modal-body .stats-row .stat-box .number.yellow {
            color: var(--warning);
        }

        .modal-body .stats-row .stat-box .number.red {
            color: var(--danger);
        }

        .modal-body .stats-row .stat-box .number.blue {
            color: var(--primary);
        }

        .modal-body .stats-row .stat-box .label {
            font-size: 0.55rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .student-table-wrap {
            overflow-x: auto;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.75rem;
        }

        .student-table thead {
            background: var(--gray-50);
        }

        .student-table th {
            padding: 0.3rem 0.5rem;
            text-align: left;
            font-size: 0.55rem;
            text-transform: uppercase;
            color: var(--gray-500);
            font-weight: 600;
            border-bottom: 2px solid var(--gray-200);
        }

        .student-table td {
            padding: 0.25rem 0.5rem;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }

        .student-table tbody tr:hover {
            background: var(--gray-50);
        }

        .status-badge-sm {
            padding: 0.05rem 0.4rem;
            border-radius: 10px;
            font-size: 0.55rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge-sm.present {
            background: var(--success-light);
            color: #166534;
        }

        .status-badge-sm.late {
            background: var(--warning-light);
            color: #92400e;
        }

        .status-badge-sm.absent {
            background: var(--danger-light);
            color: #991b1b;
        }

        .modal-footer {
            padding: 0.6rem 1.25rem;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            background: var(--gray-50);
            border-radius: 0 0 var(--radius) var(--radius);
        }

        .btn-close-modal {
            padding: 0.25rem 1rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
            background: var(--white);
            color: var(--gray-600);
            cursor: pointer;
            font-weight: 500;
            font-size: 0.75rem;
            transition: var(--transition);
        }

        .btn-close-modal:hover {
            background: var(--gray-100);
        }

        .btn-export-modal {
            padding: 0.25rem 1rem;
            border-radius: var(--radius-sm);
            border: none;
            background: var(--success);
            color: var(--white);
            cursor: pointer;
            font-weight: 500;
            font-size: 0.75rem;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-export-modal:hover {
            background: #059669;
        }

        .modal-body .summary-text {
            font-size: 0.7rem;
            color: var(--gray-500);
            margin-bottom: 0.5rem;
        }

        /* ============================================================
                       RESPONSIVE
                       ============================================================ */
        @media (max-width: 1200px) {
            .table-compact .col-periods {
                width: 6%;
            }

            .table-compact .col-actions {
                width: 18%;
            }
        }

        @media (max-width: 992px) {
            .stats-grid-compact {
                grid-template-columns: repeat(2, 1fr);
            }

            .table-compact {
                font-size: 0.7rem;
                min-width: 700px;
            }

            .table-compact .col-course {
                width: 18%;
            }

            .table-compact .col-date {
                width: 10%;
            }

            .table-compact .col-time {
                width: 8%;
            }

            .table-compact .col-room {
                width: 7%;
            }

            .table-compact .col-attendance {
                width: 10%;
            }

            .table-compact .col-actions {
                width: 20%;
            }
        }

        @media (max-width: 768px) {
            .filter-bar-compact .filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar-compact .filter-row .filter-group {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar-compact .filter-row .filter-group input,
            .filter-bar-compact .filter-row .filter-group select {
                width: 100%;
                min-width: unset;
            }

            .filter-bar-compact .filter-actions {
                flex-direction: column;
            }

            .btn-filter-compact {
                justify-content: center;
            }

            .modal-body .stats-row {
                grid-template-columns: 1fr 1fr;
            }

            .pagination-wrap-compact {
                flex-direction: column;
                text-align: center;
            }

            .pagination-wrap-compact .pagination {
                justify-content: center;
            }

            .student-table {
                min-width: 500px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid-compact {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-box-compact {
                padding: 0.6rem 0.8rem;
            }

            .stat-box-compact .stat-left .number {
                font-size: 1.2rem;
            }

            .table-compact {
                min-width: 600px;
            }

            .modal-body .stats-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid var(--gray-200);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    {{-- ============================================================
    STATS
    ============================================================ --}}
    <div class="stats-grid-compact">
        <div class="stat-box-compact">
            <div class="stat-left">
                <div class="number primary">{{ $totalSessions ?? 0 }}</div>
                <div class="label">Total Sessions</div>
            </div>
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
        </div>
        <div class="stat-box-compact">
            <div class="stat-left">
                <div class="number green">{{ $activeSessions ?? 0 }}</div>
                <div class="label">Active Sessions</div>
            </div>
            <div class="stat-icon"><i class="bi bi-circle-fill" style="color:var(--success);"></i></div>
        </div>
        <div class="stat-box-compact">
            <div class="stat-left">
                <div class="number">{{ $courses->count() }}</div>
                <div class="label">Courses</div>
            </div>
            <div class="stat-icon"><i class="bi bi-book"></i></div>
        </div>
        <div class="stat-box-compact">
            <div class="stat-left">
                <div class="number">{{ $sessions->total() }}</div>
                <div class="label">Showing</div>
            </div>
            <div class="stat-icon"><i class="bi bi-list-ul"></i></div>
        </div>
    </div>

    {{-- ============================================================
    FILTER BAR
    ============================================================ --}}
    <div class="filter-bar-compact">
        <form method="GET" action="{{ route('lecturer.attendance.sessions') }}" id="filterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <label><i class="bi bi-calendar3"></i> From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-calendar3"></i> To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-book"></i> Course</label>
                    <select name="course_id">
                        <option value="">All</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_code }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-circle"></i> Status</label>
                    <select name="status">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>Ended</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label><i class="bi bi-qr-code"></i> Type</label>
                    <select name="qr_mode">
                        <option value="">All</option>
                        <option value="session" {{ request('qr_mode') == 'session' ? 'selected' : '' }}>Dynamic</option>
                        <option value="semester" {{ request('qr_mode') == 'semester' ? 'selected' : '' }}>Semester</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter-compact primary">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                <a href="{{ route('lecturer.attendance.sessions') }}" class="btn-filter-compact secondary">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>

        {{-- Active Filters --}}
        @if (request()->has('date_from') ||
                request()->has('date_to') ||
                request()->has('course_id') ||
                request()->has('status') ||
                request()->has('qr_mode'))
            <div class="active-filters-compact">
                @if (request('date_from'))
                    <span class="filter-badge-compact">
                        From: {{ request('date_from') }}
                        <span class="remove" onclick="removeFilter('date_from')">&times;</span>
                    </span>
                @endif
                @if (request('date_to'))
                    <span class="filter-badge-compact">
                        To: {{ request('date_to') }}
                        <span class="remove" onclick="removeFilter('date_to')">&times;</span>
                    </span>
                @endif
                @if (request('course_id'))
                    <span class="filter-badge-compact">
                        Course: {{ $courses->firstWhere('id', request('course_id'))->course_code ?? 'N/A' }}
                        <span class="remove" onclick="removeFilter('course_id')">&times;</span>
                    </span>
                @endif
                @if (request('status') && request('status') != 'all')
                    <span class="filter-badge-compact">
                        Status: {{ ucfirst(request('status')) }}
                        <span class="remove" onclick="removeFilter('status')">&times;</span>
                    </span>
                @endif
                @if (request('qr_mode'))
                    <span class="filter-badge-compact">
                        Type: {{ request('qr_mode') == 'session' ? 'Dynamic' : 'Semester' }}
                        <span class="remove" onclick="removeFilter('qr_mode')">&times;</span>
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- ============================================================
    TABLE - COMPACT ONE ROW PER SESSION
    ============================================================ --}}
    @if ($sessions->count() > 0)
        <div class="table-wrap-compact">
            <table class="table-compact">
                <thead>
                    <tr>
                        <th class="col-course">Course</th>
                        <th class="col-date">Date</th>
                        <th class="col-time">Time</th>
                        <th class="col-room">Room</th>
                        <th class="col-periods">Periods</th>
                        <th class="col-attendance">Attendance</th>
                        <th class="col-status">Status</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $session)
                        @php
                            $stats = $session->student_attendance_stats;
                            $present = $stats['present'];
                            $late = $stats['late'];
                            $absent = $stats['absent'];
                            $total = $stats['total'];
                            $percentage = $stats['percentage'];

                            $barColor = $percentage >= 75 ? '#10b981' : ($percentage >= 50 ? '#f59e0b' : '#ef4444');
                            $dateDisplay = $session->started_at ?? $session->created_at;
                            $isActive = $session->status == 'active';
                            $isSemester = $session->qr_mode == 'semester';
                        @endphp
                        <tr onclick="viewSessionStudents({{ $session->id }})" title="Click to view students">
                            <td class="col-course">
                                <div class="course-cell">
                                    <span class="code">{{ $session->course->course_code ?? 'N/A' }}</span>
                                    <span
                                        class="name">{{ Str::limit($session->course->course_name ?? 'Unknown', 30) }}</span>
                                    @if ($isSemester)
                                        <span class="qr-badge-compact semester"><i class="bi bi-infinity"></i>
                                            Semester</span>
                                    @else
                                        <span class="qr-badge-compact dynamic"><i class="bi bi-qr-code"></i> Dynamic</span>
                                    @endif
                                </div>
                            </td>
                            <td class="col-date">
                                {{ $dateDisplay ? $dateDisplay->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="col-time">
                                {{ $dateDisplay ? $dateDisplay->format('h:i A') : 'N/A' }}
                            </td>
                            <td class="col-room">
                                {{ $session->room ?? '—' }}
                            </td>
                            <td class="col-periods">
                                {{ $session->conducted_periods ?? 1 }}
                            </td>
                            <td class="col-attendance">
                                <div class="attendance-cell">
                                    <div class="bar-track">
                                        <div class="bar-fill"
                                            style="width: {{ $percentage }}%; background: {{ $barColor }};"></div>
                                    </div>
                                    <span class="percent"
                                        style="color: {{ $barColor }};">{{ $percentage }}%</span>
                                </div>
                                <div
                                    style="display:flex; gap:0.5rem; font-size:0.6rem; color:var(--gray-400); margin-top:0.1rem;">
                                    <span class="text-success">P: {{ $present }}</span>
                                    <span class="text-warning">L: {{ $late }}</span>
                                    <span class="text-danger">A: {{ $absent }}</span>
                                    <span>T: {{ $total }}</span>
                                </div>
                            </td>
                            <td class="col-status">
                                <span class="status-badge-compact {{ $isActive ? 'active' : 'ended' }}">
                                    {{ $isActive ? 'Active' : 'Ended' }}
                                </span>
                            </td>
                            <td class="col-actions">
                                <div class="action-group">
                                    <button class="btn-action-compact view"
                                        onclick="event.stopPropagation(); viewSessionStudents({{ $session->id }})">
                                        <i class="bi bi-people"></i>
                                    </button>
                                    <a href="{{ route('lecturer.attendance.sessions.export', $session->id) }}"
                                        class="btn-action-compact export" onclick="event.stopPropagation();">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    @if ($isActive)
                                        <form method="POST"
                                            action="{{ route('lecturer.attendance.sessions.end', $session->id) }}"
                                            style="display:inline;" onclick="event.stopPropagation();">
                                            @csrf
                                            <button type="submit" class="btn-action-compact end"
                                                onclick="return confirm('End this session?')">
                                                <i class="bi bi-stop-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            @if ($sessions->hasPages())
                <div class="pagination-wrap-compact">
                    <span class="info">
                        Showing {{ $sessions->firstItem() ?? 0 }} to {{ $sessions->lastItem() ?? 0 }}
                        of {{ $sessions->total() }} sessions
                    </span>
                    {{ $sessions->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @else
        <div class="empty-state-compact">
            <span class="icon"><i class="bi bi-inbox"></i></span>
            <p>No sessions found matching your filters.</p>
            <a href="{{ route('lecturer.attendance.sessions') }}" class="btn-filter-compact secondary"
                style="margin-top:0.5rem;">
                <i class="bi bi-arrow-counterclockwise"></i> Clear Filters
            </a>
        </div>
    @endif

    {{-- ============================================================
    SESSION STUDENTS MODAL
    ============================================================ --}}
    <div class="modal-overlay" id="sessionStudentsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i class="bi bi-people" style="color:var(--primary);"></i>
                    Session Students
                    <span class="session-info" id="modalSessionInfo"
                        style="font-weight:400; color:var(--gray-500); font-size:0.75rem;">
                        Loading...
                    </span>
                </h4>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="text-center" style="padding:2rem;">
                    <div class="loading-spinner"></div>
                    <p style="margin-top:0.5rem; color:var(--gray-500); font-size:0.85rem;">Loading student data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-close-modal" onclick="closeModal()">Close</button>
                <button class="btn-export-modal" id="modalExportBtn" style="display:none;">
                    <i class="bi bi-download"></i> Export CSV
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ============================================================
            // REMOVE FILTER
            // ============================================================
            function removeFilter(filterName) {
                const form = document.getElementById('filterForm');
                const input = form.querySelector(`[name="${filterName}"]`);
                if (input) {
                    input.value = '';
                    form.submit();
                }
            }

            // ============================================================
            // VIEW SESSION STUDENTS (AJAX) - FIXED VERSION
            // ============================================================
            let currentSessionId = null;

            function viewSessionStudents(sessionId) {
                currentSessionId = sessionId;
                const modal = document.getElementById('sessionStudentsModal');
                const body = document.getElementById('modalBody');
                const exportBtn = document.getElementById('modalExportBtn');

                modal.classList.add('show');
                body.innerHTML = `
                    <div class="text-center" style="padding:2rem;">
                        <div class="loading-spinner"></div>
                        <p style="margin-top:0.5rem; color:var(--gray-500); font-size:0.85rem;">Loading student data...</p>
                    </div>
                `;
                exportBtn.style.display = 'none';

                fetch(`/lecturer/attendance/sessions/${sessionId}/students`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        if (!data.success) throw new Error(data.message || 'Failed to load');

                        // ✅ SIMPLIFIED: Show only course code and date
                        const sessionInfo = `
                            <span style="font-weight:600; color:var(--gray-700); font-size:0.8rem;">
                                ${data.session.course_code || 'N/A'}
                            </span>
                            <span style="color:var(--gray-400); margin:0 4px;">•</span>
                            <span style="color:var(--gray-500);">
                                <i class="bi bi-calendar3"></i> ${data.session.session_date || 'N/A'}
                            </span>
                        `;

                        document.getElementById('modalSessionInfo').innerHTML = sessionInfo;

                        // Stats
                        let html = `
                            <div class="stats-row">
                                <div class="stat-box">
                                    <div class="number blue">${data.stats.total}</div>
                                    <div class="label">Total</div>
                                </div>
                                <div class="stat-box">
                                    <div class="number green">${data.stats.present}</div>
                                    <div class="label">Present</div>
                                </div>
                                <div class="stat-box">
                                    <div class="number yellow">${data.stats.late}</div>
                                    <div class="label">Late</div>
                                </div>
                                <div class="stat-box">
                                    <div class="number red">${data.stats.absent}</div>
                                    <div class="label">Absent</div>
                                </div>
                            </div>

                            <div class="summary-text">
                                <i class="bi bi-info-circle"></i>
                                <span id="modalStudentCount">${data.students.length}</span> students •
                                ${data.stats.present_percentage}% attendance rate
                            </div>

                            <div class="student-table-wrap">
                                <table class="student-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student</th>
                                            <th>ID</th>
                                            <th>Status</th>
                                            <th>Time</th>
                                            <th>Method</th>
                                            <th>Risk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                        data.students.forEach((student, index) => {
                            const statusClass = student.status;
                            const riskClass = student.risk_level ? student.risk_level.toLowerCase() : 'low';

                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>
                                        <div style="font-weight:500; color:var(--gray-800); font-size:0.8rem;">${student.name}</div>
                                        <div style="font-size:0.6rem; color:var(--gray-500);">${student.email}</div>
                                    </td>
                                    <td><span style="font-family:monospace; font-size:0.65rem; background:var(--gray-100); padding:0.05rem 0.3rem; border-radius:3px;">${student.student_id}</span></td>
                                    <td>
                                        <span class="status-badge-sm ${statusClass}">
                                            ${statusClass.charAt(0).toUpperCase() + statusClass.slice(1)}
                                        </span>
                                    </td>
                                    <td style="font-size:0.65rem; color:var(--gray-500);">
                                        ${student.scanned_at ? new Date(student.scanned_at).toLocaleTimeString() : '—'}
                                    </td>
                                    <td>
                                        ${student.is_manual
                                            ? '<span style="font-size:0.55rem; background:#dbeafe; color:#1e40af; padding:0.05rem 0.3rem; border-radius:3px;">Manual</span>'
                                            : student.status !== 'absent'
                                                ? '<span style="font-size:0.55rem; background:#dcfce7; color:#166534; padding:0.05rem 0.3rem; border-radius:3px;">QR</span>'
                                                : '<span style="font-size:0.55rem; color:var(--gray-400);">—</span>'
                                        }
                                    </td>
                                    <td>
                                        <span style="padding:0.05rem 0.4rem; border-radius:8px; font-size:0.55rem; font-weight:600; background:${student.risk_level == 'High' ? 'var(--danger-light)' : (student.risk_level == 'Medium' ? 'var(--warning-light)' : 'var(--success-light)')}; color:${student.risk_level == 'High' ? 'var(--danger)' : (student.risk_level == 'Medium' ? 'var(--warning)' : 'var(--success)')};">
                                            ${student.risk_level || 'Low'}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        body.innerHTML = html;

                        // Show export button
                        exportBtn.style.display = 'inline-flex';
                        exportBtn.onclick = function() {
                            window.location.href = `/lecturer/attendance/sessions/${currentSessionId}/export`;
                        };
                    })
                    .catch(error => {
                        body.innerHTML = `
                            <div style="text-align:center; padding:2rem; color:var(--danger);">
                                <i class="bi bi-exclamation-triangle" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                <p>${error.message || 'Failed to load student data'}</p>
                                <button class="btn-close-modal" onclick="closeModal()">Close</button>
                            </div>
                        `;
                    });
            }

            // ============================================================
            // MODAL HELPERS
            // ============================================================
            function closeModal() {
                document.getElementById('sessionStudentsModal').classList.remove('show');
            }

            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) closeModal();
                });
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });
        </script>
    @endpush

@endsection
