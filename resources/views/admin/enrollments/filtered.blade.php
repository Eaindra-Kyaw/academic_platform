{{-- resources/views/admin/enrollments/filtered.blade.php --}}
@extends('layouts.app')

@section('title', 'Enrollment Management - Filtered Results')
@section('role', 'Admin')
@section('page-title', 'Filtered Enrollment Overview')
@section('welcome-text', 'Viewing filtered enrollment requests')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        /* ===== BREADCRUMB ===== */
        .breadcrumb-bar {
            background: white;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .breadcrumb-bar .breadcrumb-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .breadcrumb-bar .breadcrumb-item a {
            color: #800000;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-bar .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-bar .breadcrumb-item .separator {
            color: #d1d5db;
        }

        .breadcrumb-bar .breadcrumb-item.active {
            color: #1f2937;
            font-weight: 600;
        }

        .breadcrumb-bar .badge-filter {
            background: #800000;
            color: white;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .breadcrumb-bar .badge-filter .remove {
            cursor: pointer;
            color: #fca5a5;
            font-weight: 700;
            text-decoration: none;
        }

        .breadcrumb-bar .badge-filter .remove:hover {
            color: #ffffff;
        }

        /* ===== BACK LINK ===== */
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #800000;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: #fef7f7;
            border-radius: 0.5rem;
            border: 1px solid #fde2e2;
            transition: all 0.2s;
        }

        .back-link:hover {
            background: #fde2e2;
            text-decoration: none;
            color: #800000;
        }

        .back-link i {
            font-size: 1.1rem;
        }

        /* ===== STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .stat-card.pending::before {
            background: #d97706;
        }

        .stat-card.approved::before {
            background: #10b981;
        }

        .stat-card.rejected::before {
            background: #ef4444;
        }

        .stat-card.total::before {
            background: #6366f1;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .stat-number.pending {
            color: #d97706;
        }

        .stat-number.approved {
            color: #10b981;
        }

        .stat-number.rejected {
            color: #ef4444;
        }

        .stat-number.total {
            color: #6366f1;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        /* ===== ACTION BAR ===== */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .action-bar .left-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .action-bar .right-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-bulk {
            padding: 0.5rem 1.2rem;
            border-radius: 2rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .btn-bulk-approve {
            background: #10b981;
            color: white;
        }

        .btn-bulk-approve:hover:not(:disabled) {
            background: #059669;
            transform: scale(1.02);
        }

        .btn-bulk-reject {
            background: #ef4444;
            color: white;
        }

        .btn-bulk-reject:hover:not(:disabled) {
            background: #dc2626;
            transform: scale(1.02);
        }

        .btn-bulk:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-export {
            background: #f3f4f6;
            color: #374151;
            padding: 0.5rem 1.2rem;
            border-radius: 2rem;
            border: 1px solid #e5e7eb;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-export:hover {
            background: #e5e7eb;
            text-decoration: none;
            color: #374151;
        }

        /* ===== FILTER BAR ===== */
        .filter-bar {
            background: white;
            border-radius: 1rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .filter-bar .search-input {
            flex: 1;
            min-width: 200px;
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.85rem;
            background: #f9fafb;
            transition: all 0.2s;
        }

        .filter-bar .search-input:focus {
            outline: none;
            border-color: #800000;
            background: white;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .filter-bar select {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.8rem;
            background: #f9fafb;
            min-width: 130px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-bar select:focus {
            outline: none;
            border-color: #800000;
        }

        .filter-bar .btn-reset {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 1.2rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
            white-space: nowrap;
            text-decoration: none;
        }

        .filter-bar .btn-reset:hover {
            background: #e5e7eb;
            text-decoration: none;
            color: #374151;
        }

        /* ===== TABLE ===== */
        .table-wrapper {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .table-scroll {
            overflow-x: auto;
            padding: 0 1px;
        }

        .enrollment-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
            font-size: 0.8rem;
        }

        .enrollment-table thead {
            background: #fafafa;
            border-bottom: 2px solid #e5e7eb;
        }

        .enrollment-table th {
            padding: 0.7rem 0.8rem;
            text-align: left;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .enrollment-table td {
            padding: 0.6rem 0.8rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .enrollment-table tbody tr {
            transition: background 0.15s;
        }

        .enrollment-table tbody tr:hover {
            background: #fafafa;
        }

        .enrollment-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ===== CHECKBOX ===== */
        .checkbox-cell {
            width: 32px;
            text-align: center;
        }

        .checkbox-cell input[type="checkbox"] {
            cursor: pointer;
            width: 14px;
            height: 14px;
            accent-color: #800000;
        }

        /* ===== STUDENT CELL ===== */
        .student-cell .name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.85rem;
        }

        .student-cell .email {
            color: #9ca3af;
            font-size: 0.65rem;
            display: block;
        }

        /* ===== COURSE CELL ===== */
        .course-cell .code {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.8rem;
        }

        .course-cell .name {
            color: #6b7280;
            font-size: 0.7rem;
            display: block;
        }

        /* ===== STATUS ===== */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.15rem 0.6rem;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 600;
            white-space: nowrap;
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

        /* ===== ACTION BUTTONS ===== */
        .action-buttons {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.2rem 0.5rem;
            border-radius: 0.3rem;
            font-size: 0.65rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            transition: all 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }

        .btn-approve:hover {
            background: #059669;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
        }

        .btn-view {
            background: #eff6ff;
            color: #2563eb;
        }

        .btn-view:hover {
            background: #dbeafe;
        }

        /* ===== ROW NUMBER ===== */
        .row-number {
            width: 28px;
            text-align: center;
            font-weight: 500;
            color: #9ca3af;
            font-size: 0.7rem;
        }

        /* ===== PAGINATION ===== */
        .pagination-wrapper {
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .pagination-wrapper .info {
            font-size: 0.75rem;
            color: #6b7280;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #d1d5db;
        }

        .empty-state p {
            margin-top: 0.5rem;
            font-size: 0.85rem;
        }

        /* ===== ALERTS ===== */
        .alert {
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-dismissible {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-close-alert {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
            padding: 0 0.3rem;
            opacity: 0.7;
        }

        .btn-close-alert:hover {
            opacity: 1;
        }

        /* ===== BULK REJECT INFO ===== */
        .bulk-info {
            background: #fef3c7;
            padding: 0.6rem 1rem;
            border-radius: 0.5rem;
            margin: 0.75rem 0;
            font-size: 0.8rem;
            color: #92400e;
        }

        /* ===== MODALS ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            max-width: 600px;
            width: 92%;
            max-height: 85vh;
            overflow-y: auto;
            animation: modalSlideIn 0.25s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px) scale(0.96);
                opacity: 0;
            }

            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h4 {
            margin: 0;
            color: #800000;
            font-size: 1.1rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.4rem;
            cursor: pointer;
            color: #9ca3af;
            padding: 0 0.3rem;
            transition: color 0.2s;
            line-height: 1;
        }

        .modal-close:hover {
            color: #1f2937;
        }

        .modal-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .modal-btn {
            flex: 1;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-align: center;
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .modal-btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .modal-btn-cancel:hover {
            background: #e5e7eb;
        }

        .modal-btn-danger {
            background: #dc2626;
            color: white;
        }

        .modal-btn-danger:hover {
            background: #b91c1c;
        }

        .reject-textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            margin: 0.75rem 0;
            resize: vertical;
            font-family: inherit;
        }

        .reject-textarea:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        /* ===== PROFILE ===== */
        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #800000;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 auto 0.75rem;
        }

        .profile-name {
            text-align: center;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.1rem;
        }

        .profile-email {
            text-align: center;
            color: #6b7280;
            font-size: 0.8rem;
            margin-bottom: 0.75rem;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin: 0.75rem 0;
        }

        .profile-stat {
            text-align: center;
            padding: 0.5rem;
            background: #f9fafb;
            border-radius: 0.5rem;
        }

        .profile-stat .number {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .profile-stat .label {
            font-size: 0.6rem;
            color: #6b7280;
            display: block;
            margin-top: 0.1rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem 1.5rem;
            margin: 0.75rem 0;
        }

        .detail-item .label {
            font-size: 0.65rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-item .value {
            font-size: 0.85rem;
            color: #1f2937;
            word-break: break-word;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar .search-input,
            .filter-bar select {
                width: 100%;
                min-width: unset;
            }

            .filter-bar .btn-reset {
                justify-content: center;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .action-bar .left-actions,
            .action-bar .right-actions {
                justify-content: center;
            }

            .action-bar .btn-bulk,
            .action-bar .btn-export {
                flex: 1;
                justify-content: center;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .profile-stats {
                grid-template-columns: 1fr 1fr;
            }

            .modal-content {
                padding: 1rem;
                width: 95%;
            }

            .pagination-wrapper {
                flex-direction: column;
                text-align: center;
            }

            .breadcrumb-bar {
                font-size: 0.75rem;
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

            .stat-number {
                font-size: 1.3rem;
            }
        }
    </style>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    {{-- Back Link --}}
    <a href="{{ route('admin.enrollments.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to All Enrollments
    </a>

    {{-- Breadcrumb / Active Filters --}}
    <div class="breadcrumb-bar">
        <span class="breadcrumb-item">
            <i class="bi bi-house-door"></i>
            <a href="{{ route('admin.enrollments.index') }}">Enrollments</a>
        </span>

        @if ($selectedYear != 'all')
            <span class="breadcrumb-item">
                <span class="separator">/</span>
                <span class="badge-filter">
                    Year {{ $selectedYear }}
                    <a href="{{ route('admin.enrollments.index') }}" class="remove" title="Remove filter">&times;</a>
                </span>
            </span>
        @endif

        @if ($selectedDepartment != 'all' && $selectedDeptName)
            <span class="breadcrumb-item">
                <span class="separator">/</span>
                <span class="badge-filter">
                    {{ $selectedDeptName }}
                    <a href="{{ route('admin.enrollments.index') }}" class="remove" title="Remove filter">&times;</a>
                </span>
            </span>
        @endif

        @if ($selectedCourse != 'all' && $selectedCourseName)
            <span class="breadcrumb-item">
                <span class="separator">/</span>
                <span class="badge-filter">
                    {{ $selectedCourseName }}
                    <a href="{{ route('admin.enrollments.index') }}" class="remove" title="Remove filter">&times;</a>
                </span>
            </span>
        @endif

        @if ($selectedStatus != 'all')
            <span class="breadcrumb-item">
                <span class="separator">/</span>
                <span class="badge-filter">
                    {{ ucfirst($selectedStatus) }}
                    <a href="{{ route('admin.enrollments.index') }}" class="remove" title="Remove filter">&times;</a>
                </span>
            </span>
        @endif

        @if (request()->filled('search'))
            <span class="breadcrumb-item">
                <span class="separator">/</span>
                <span class="badge-filter">
                    "{{ request('search') }}"
                    <a href="{{ route('admin.enrollments.index') }}" class="remove" title="Remove search">&times;</a>
                </span>
            </span>
        @endif

        <span class="breadcrumb-item active" style="margin-left:auto;">
            {{ $enrollments->total() }} result(s)
        </span>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card pending">
            <div class="stat-number pending">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label">⏳ Pending Requests</div>
        </div>
        <div class="stat-card approved">
            <div class="stat-number approved">{{ $stats['approved'] ?? 0 }}</div>
            <div class="stat-label">✅ Approved</div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-number rejected">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="stat-label">❌ Rejected</div>
        </div>
        <div class="stat-card total">
            <div class="stat-number total">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">📊 Total Requests</div>
        </div>
    </div>

    {{-- Action Bar --}}
    <div class="action-bar">
        <div class="left-actions">
            <button class="btn-bulk btn-bulk-approve" id="bulkApproveBtn" disabled onclick="bulkAction('approve')">
                <i class="bi bi-check-all"></i> Approve Selected
            </button>
            <button class="btn-bulk btn-bulk-reject" id="bulkRejectBtn" disabled onclick="openBulkRejectModal()">
                <i class="bi bi-x-circle"></i> Reject Selected
            </button>
        </div>
        <div class="right-actions">
            <a href="{{ route('admin.enrollments.index') }}" class="btn-export" title="Clear all filters">
                <i class="bi bi-arrow-counterclockwise"></i> Clear Filters
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
        <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search by name, email, ID, or course..."
            value="{{ request('search') }}">

        <select id="statusFilter">
            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>📌 All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <select id="deptFilter">
            <option value="all" {{ request('department') == 'all' ? 'selected' : '' }}>🏛️ All Departments</option>
            @foreach ($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                    {{ $dept->code }}
                </option>
            @endforeach
        </select>

        <select id="courseFilter">
            <option value="all" {{ request('course') == 'all' ? 'selected' : '' }}>📚 All Courses</option>
            @foreach ($courses as $course)
                <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>
                    {{ $course->course_code }}
                </option>
            @endforeach
        </select>

        <a href="{{ route('admin.enrollments.index') }}" class="btn-reset">
            <i class="bi bi-arrow-repeat"></i> Reset
        </a>
    </div>

    {{-- Table --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="enrollment-table">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                        </th>
                        <th class="row-number">#</th>
                        <th style="min-width:140px;">Student</th>
                        <th style="min-width:120px;">Course</th>
                        <th style="min-width:130px;">Department</th>
                        <th style="min-width:80px;">Date</th>
                        <th style="min-width:80px;">Status</th>
                        <th style="min-width:90px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($enrollments as $index => $enrollment)
                        @php
                            $rowNumber = ($enrollments->currentPage() - 1) * $enrollments->perPage() + $index + 1;
                            $student = $enrollment->student;
                            $course = $enrollment->course;
                            $dept = $course->department ?? null;
                        @endphp
                        <tr data-id="{{ $enrollment->id }}" data-status="{{ $enrollment->status }}"
                            data-dept="{{ $dept->id ?? 'all' }}" data-year="{{ $student->current_year ?? 'all' }}">

                            <td class="checkbox-cell">
                                @if ($enrollment->status == 'pending')
                                    <input type="checkbox" class="row-checkbox" value="{{ $enrollment->id }}"
                                        onchange="updateBulkButtons()">
                                @endif
                            </td>

                            <td class="row-number">{{ $rowNumber }}</td>

                            <td class="student-cell">
                                <div class="name">{{ $student->name ?? 'N/A' }}</div>
                                <span class="email">{{ $student->email ?? 'N/A' }}</span>
                            </td>

                            <td class="course-cell">
                                <span class="code">{{ $course->course_code ?? 'N/A' }}</span>
                                <span class="name">{{ Str::limit($course->course_name ?? '', 30) }}</span>
                            </td>

                            <td>{{ $dept->name ?? 'N/A' }}</td>

                            <td style="font-size:0.7rem; white-space:nowrap;">
                                {{ $enrollment->created_at ? $enrollment->created_at->format('d M Y') : 'N/A' }}
                            </td>

                            <td>
                                @if ($enrollment->status == 'pending')
                                    <span class="status-badge status-pending"><i class="bi bi-clock-history"></i>
                                        Pending</span>
                                @elseif($enrollment->status == 'approved')
                                    <span class="status-badge status-approved"><i class="bi bi-check-circle"></i>
                                        Approved</span>
                                @else
                                    <span class="status-badge status-rejected"><i class="bi bi-x-circle"></i>
                                        Rejected</span>
                                @endif
                            </td>

                            <td>
                                <div class="action-buttons">
                                    @if ($enrollment->status == 'pending')
                                        <a href="{{ url('/admin/enrollments/' . $enrollment->id . '/approve') }}"
                                            class="btn-sm btn-approve"
                                            onclick="return confirm('Approve this enrollment?')" title="Approve">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <button class="btn-sm btn-reject"
                                            onclick="showRejectModal({{ $enrollment->id }})" title="Reject">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif

                                    <button class="btn-sm btn-view" onclick="showStudentProfile({{ $student->id ?? 0 }})"
                                        title="View Student">
                                        <i class="bi bi-person"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <p>No enrollment requests found</p>
                                    <small style="color:#9ca3af;">Try adjusting your filters</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($enrollments->hasPages())
            <div class="pagination-wrapper">
                <div class="info">
                    Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }}
                    of {{ $enrollments->total() }} results
                </div>
                <div>
                    {{ $enrollments->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- MODALS --}}
    {{-- ============================================================ --}}

    {{-- Student Profile Modal --}}
    <div id="studentProfileModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="bi bi-person-circle"></i> Student Profile</h4>
                <button class="modal-close" onclick="closeModal('studentProfileModal')">&times;</button>
            </div>
            <div id="profileContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-muted" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4 style="color: #dc2626;"><i class="bi bi-exclamation-triangle"></i> Reject Enrollment</h4>
                <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <p id="rejectModalMessage" style="font-size: 0.85rem; color: #6b7280;"></p>
            <textarea id="rejectionReason" class="reject-textarea" rows="3"
                placeholder="Please provide a reason for rejection..."></textarea>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="modal-btn modal-btn-danger" id="confirmRejectBtn">Confirm Rejection</button>
            </div>
        </div>
    </div>

    {{-- Bulk Reject Modal --}}
    <div id="bulkRejectModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4 style="color: #dc2626;"><i class="bi bi-x-circle"></i> Bulk Reject</h4>
                <button class="modal-close" onclick="closeModal('bulkRejectModal')">&times;</button>
            </div>
            <div class="bulk-info">
                <i class="bi bi-info-circle"></i>
                You are about to reject <strong id="bulkRejectCount">0</strong> enrollment request(s).
            </div>
            <textarea id="bulkRejectionReason" class="reject-textarea" rows="3"
                placeholder="Please provide a reason for rejecting these enrollments..."></textarea>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="closeModal('bulkRejectModal')">Cancel</button>
                <button class="modal-btn modal-btn-danger" id="confirmBulkRejectBtn">Reject All</button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- JAVASCRIPT --}}
    {{-- ============================================================ --}}
    <script>
        // ========== VARIABLES ==========
        let currentRejectId = null;
        let currentBulkIds = [];

        // ========== STUDENT PROFILE ==========
        function showStudentProfile(studentId) {
            if (!studentId) {
                alert('Student information not available.');
                return;
            }

            const modal = document.getElementById('studentProfileModal');
            const content = document.getElementById('profileContent');

            content.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-muted" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted" style="font-size:0.8rem;">Loading student profile...</p>
                </div>
            `;

            modal.classList.add('show');

            fetch('/admin/enrollments/student/' + studentId)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.error) throw new Error(data.error);
                    content.innerHTML = `
                        <div class="profile-avatar">${data.name ? data.name.charAt(0).toUpperCase() : '?'}</div>
                        <div class="profile-name">${data.name || 'N/A'}</div>
                        <div class="profile-email">${data.email || 'N/A'}</div>

                        <div class="profile-stats">
                            <div class="profile-stat">
                                <span class="number">${data.current_year || 'N/A'}</span>
                                <span class="label">Year</span>
                            </div>
                            <div class="profile-stat">
                                <span class="number">${data.gpa || 'N/A'}</span>
                                <span class="label">GPA</span>
                            </div>
                            <div class="profile-stat">
                                <span class="number">${data.total_credits || 0}</span>
                                <span class="label">Credits</span>
                            </div>
                        </div>

                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="label">Student ID</span>
                                <span class="value">${data.student_id || 'N/A'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Department</span>
                                <span class="value">${data.department || 'N/A'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Enrollments</span>
                                <span class="value">${data.enrollment_count || 0}</span>
                            </div>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="label">Current Courses</span>
                                <span class="value" style="font-size:0.8rem;">${data.current_courses || 'No active courses'}</span>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    content.innerHTML = `
                        <div class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                            <p style="margin-top:0.5rem;">Failed to load student profile.</p>
                            <small style="color:#6b7280;">${error.message}</small>
                        </div>
                    `;
                    console.error('Error loading student profile:', error);
                });
        }

        // ========== REJECT ==========
        function showRejectModal(id) {
            currentRejectId = id;
            document.getElementById('rejectModalMessage').innerHTML =
                'Are you sure you want to reject this enrollment request?';
            document.getElementById('rejectionReason').value = '';
            document.getElementById('rejectModal').classList.add('show');
        }

        document.getElementById('confirmRejectBtn')?.addEventListener('click', function() {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/enrollments/' + currentRejectId + '/reject';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = reason;

            form.appendChild(csrf);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        });

        // ========== BULK REJECT ==========
        function openBulkRejectModal() {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length === 0) return;

            currentBulkIds = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById('bulkRejectCount').textContent = currentBulkIds.length;
            document.getElementById('bulkRejectionReason').value = '';
            document.getElementById('bulkRejectModal').classList.add('show');
        }

        document.getElementById('confirmBulkRejectBtn')?.addEventListener('click', function() {
            const reason = document.getElementById('bulkRejectionReason').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/enrollments/bulk/reject';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            currentBulkIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'enrollment_ids[]';
                input.value = id;
                form.appendChild(input);
            });

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = reason;
            form.appendChild(reasonInput);

            document.body.appendChild(form);
            form.submit();
        });

        // ========== BULK APPROVE ==========
        function bulkAction(action) {
            const checkboxes = document.querySelectorAll('.row-checkbox:checked');
            if (checkboxes.length === 0) return;

            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (action === 'approve') {
                if (!confirm(`Are you sure you want to approve ${ids.length} enrollment(s)?`)) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/enrollments/bulk/approve';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'enrollment_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        }

        // ========== CHECKBOX MANAGEMENT ==========
        function toggleAllCheckboxes() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkButtons();
        }

        function updateBulkButtons() {
            const checked = document.querySelectorAll('.row-checkbox:checked');
            const approveBtn = document.getElementById('bulkApproveBtn');
            const rejectBtn = document.getElementById('bulkRejectBtn');

            const hasChecked = checked.length > 0;
            if (approveBtn) approveBtn.disabled = !hasChecked;
            if (rejectBtn) rejectBtn.disabled = !hasChecked;
        }

        // ========== SEARCH FILTER ==========
        function filterTable() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const deptFilter = document.getElementById('deptFilter');
            const courseFilter = document.getElementById('courseFilter');

            const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
            const status = statusFilter ? statusFilter.value : 'all';
            const dept = deptFilter ? deptFilter.value : 'all';
            const course = courseFilter ? courseFilter.value : 'all';

            const rows = document.querySelectorAll('#tableBody tr');
            let visibleCount = 0;

            rows.forEach(row => {
                if (row.querySelector('.empty-state')) {
                    row.style.display = '';
                    return;
                }

                const rowStatus = row.dataset.status || '';
                const rowDept = row.dataset.dept || '';
                const searchText = row.textContent.toLowerCase();

                const matchesSearch = searchTerm === '' || searchText.includes(searchTerm);
                const matchesStatus = status === 'all' || rowStatus === status;
                const matchesDept = dept === 'all' || rowDept == dept;

                if (matchesSearch && matchesStatus && matchesDept) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide empty state
            const emptyState = document.querySelector('.empty-state');
            if (emptyState) {
                const parentRow = emptyState.closest('tr');
                if (parentRow) {
                    parentRow.style.display = visibleCount === 0 ? '' : 'none';
                }
            }
        }

        // ========== MODAL HELPERS ==========
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('show');
        }

        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });

        // ========== EVENT LISTENERS ==========
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const deptFilter = document.getElementById('deptFilter');
            const courseFilter = document.getElementById('courseFilter');

            if (searchInput) searchInput.addEventListener('keyup', filterTable);
            if (statusFilter) statusFilter.addEventListener('change', filterTable);
            if (deptFilter) deptFilter.addEventListener('change', filterTable);
            if (courseFilter) courseFilter.addEventListener('change', filterTable);

            filterTable();
            updateBulkButtons();
        });
    </script>
@endsection
