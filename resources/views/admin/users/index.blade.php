@extends('layouts.app')

@section('title', 'User Management')
@section('role', 'Admin')
@section('page-title', '👥 User Management')
@section('welcome-text', 'Manage user accounts by role')

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
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .page-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-create-user {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .btn-create-user:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
            cursor: pointer;
            box-shadow: var(--shadow);
        }

        .stat-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .stat-card .icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .stat-card .icon.admin {
            background: var(--warning-light);
            color: #92400e;
        }

        .stat-card .icon.lecturer {
            background: var(--info-light);
            color: var(--info);
        }

        .stat-card .icon.student {
            background: var(--success-light);
            color: var(--success);
        }

        .stat-card .icon.total {
            background: var(--danger-light);
            color: var(--danger);
        }

        .stat-card .info .number {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .stat-card .info .label {
            font-size: 0.65rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .role-tabs {
            display: flex;
            gap: 0.25rem;
            background: var(--white);
            border-radius: 8px;
            padding: 0.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            box-shadow: var(--shadow);
        }

        .role-tab {
            padding: 0.4rem 1.2rem;
            border: none;
            background: transparent;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-gray);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: 'Inter', sans-serif;
        }

        .role-tab:hover {
            background: #f3f4f6;
            color: var(--text-dark);
        }

        .role-tab.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(10, 36, 99, 0.25);
        }

        .role-tab .badge-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .role-tab.active .badge-count {
            background: rgba(255, 255, 255, 0.25);
        }

        .user-table-wrap {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .user-table-wrap .table-header {
            padding: 0.75rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .user-table-wrap .table-header .title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .user-table-wrap .table-header .title i {
            color: var(--primary);
            margin-right: 0.4rem;
        }

        .user-table-wrap .table-header .search-box {
            display: flex;
            align-items: center;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            padding: 0.15rem 0.6rem;
            transition: var(--transition);
        }

        .user-table-wrap .table-header .search-box:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .user-table-wrap .table-header .search-box input {
            border: none;
            outline: none;
            padding: 0.3rem 0.4rem;
            font-size: 0.75rem;
            color: var(--text-dark);
            background: transparent;
            width: 180px;
            font-family: 'Inter', sans-serif;
        }

        .user-table-wrap .table-header .search-box i {
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .user-table thead th {
            padding: 0.5rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-gray);
            border-bottom: 2px solid rgba(10, 36, 99, 0.06);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
            white-space: nowrap;
        }

        .user-table tbody td {
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .user-table tbody tr {
            transition: var(--transition);
        }

        .user-table tbody tr:hover {
            background: #fafbfc;
        }

        .user-table tbody tr:last-child td {
            border-bottom: none;
        }

        .user-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.65rem;
            flex-shrink: 0;
            color: var(--white);
        }

        .user-avatar-sm.admin {
            background: #92400e;
        }

        .user-avatar-sm.lecturer {
            background: var(--info);
        }

        .user-avatar-sm.student {
            background: var(--success);
        }

        .badge-role {
            padding: 0.15rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .badge-role.admin {
            background: var(--warning-light);
            color: #92400e;
        }

        .badge-role.lecturer {
            background: var(--info-light);
            color: var(--info);
        }

        .badge-role.student {
            background: var(--success-light);
            color: var(--success);
        }

        .badge-status {
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.55rem;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .badge-status.pending {
            background: var(--warning-light);
            color: #92400e;
        }

        .badge-status.set {
            background: var(--success-light);
            color: var(--success);
        }

        .btn-action-sm {
            padding: 0.15rem 0.4rem;
            border-radius: 6px;
            font-size: 0.6rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.15rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .btn-action-sm:hover {
            transform: translateY(-1px);
        }

        .btn-edit {
            background: var(--warning-light);
            color: #92400e;
        }

        .btn-edit:hover {
            background: #fde68a;
        }

        .btn-delete {
            background: var(--danger-light);
            color: var(--danger);
        }

        .btn-delete:hover {
            background: #fca5a5;
        }

        .btn-telegram {
            background: #0088cc;
            color: var(--white);
        }

        .btn-telegram:hover {
            background: #006699;
        }

        .btn-copy {
            background: var(--primary);
            color: var(--white);
        }

        .btn-copy:hover {
            background: var(--primary-dark);
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 2rem;
            display: block;
            margin-bottom: 0.5rem;
            color: #d1d5db;
        }

        .edit-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .edit-modal.show {
            display: flex;
        }

        .edit-modal-content {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            max-width: 500px;
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

        .edit-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .edit-modal-header h4 {
            margin: 0;
            color: var(--primary);
            font-size: 1rem;
            font-weight: 700;
        }

        .edit-modal-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: var(--text-gray);
            transition: var(--transition);
        }

        .edit-modal-close:hover {
            color: var(--danger);
        }

        .edit-modal .form-group {
            margin-bottom: 0.8rem;
        }

        .edit-modal .form-group label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-dark);
            display: block;
            margin-bottom: 0.2rem;
        }

        .edit-modal .form-group input,
        .edit-modal .form-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.8rem;
            background: #fafbfc;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
        }

        .edit-modal .form-group input:focus,
        .edit-modal .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .edit-modal .btn-save {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .edit-modal .btn-save:hover {
            background: var(--primary-light);
        }

        .edit-modal .btn-cancel-modal {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.8rem;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .edit-modal .btn-cancel-modal:hover {
            background: #e5e7eb;
        }

        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            padding: 15px 25px;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            margin-bottom: 10px;
            animation: slideIn 0.5s ease;
            color: var(--white);
        }

        .toast.success {
            background: var(--success);
        }

        .toast.error {
            background: var(--danger);
        }

        .toast.info {
            background: var(--info);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 1024px) {
            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-card {
                padding: 0.75rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-table-wrap .table-header {
                flex-direction: column;
                align-items: stretch;
            }

            .user-table-wrap .table-header .search-box input {
                width: 100%;
            }

            .role-tabs {
                gap: 0.15rem;
                padding: 0.25rem;
            }

            .role-tab {
                padding: 0.3rem 0.8rem;
                font-size: 0.7rem;
            }

            .user-table {
                font-size: 0.7rem;
                min-width: 650px;
            }
        }

        @media (max-width: 480px) {
            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-card {
                padding: 0.6rem;
            }

            .stat-card .icon {
                width: 34px;
                height: 34px;
                font-size: 0.9rem;
            }

            .stat-card .info .number {
                font-size: 1.1rem;
            }
        }
    </style>

    <div class="page-wrapper">
        @if (session('success'))
            <div
                style="background:var(--success-light); border:2px solid var(--success); border-radius:12px; padding:20px; margin-bottom:20px;">
                {!! session('success') !!}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background:var(--danger-light); color:var(--danger); padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; border-left:3px solid var(--danger); display:flex; align-items:center; gap:0.5rem;">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- ===== HEADER ===== -->
        <div class="page-header">
            <a href="{{ route('admin.users.create') }}" class="btn-create-user">
                <i class="bi bi-plus-circle"></i> Create New User
            </a>
        </div>

        <!-- ===== STATS ROW ===== -->
        @php
            $users = \App\Models\User::all();
            $admins = $users->where('role_id', 1);
            $lecturers = $users->where('role_id', 2);
            $students = $users->where('role_id', 3);
        @endphp

        <div class="stats-row">
            <div class="stat-card" onclick="filterUsers('all')">
                <div class="icon total"><i class="bi bi-people"></i></div>
                <div class="info">
                    <div class="number">{{ $users->count() }}</div>
                    <div class="label">Total Users</div>
                </div>
            </div>
            <div class="stat-card" onclick="filterUsers('admin')">
                <div class="icon admin"><i class="bi bi-shield-lock"></i></div>
                <div class="info">
                    <div class="number">{{ $admins->count() }}</div>
                    <div class="label">Admins</div>
                </div>
            </div>
            <div class="stat-card" onclick="filterUsers('lecturer')">
                <div class="icon lecturer"><i class="bi bi-person-badge"></i></div>
                <div class="info">
                    <div class="number">{{ $lecturers->count() }}</div>
                    <div class="label">Lecturers</div>
                </div>
            </div>
            <div class="stat-card" onclick="filterUsers('student')">
                <div class="icon student"><i class="bi bi-person"></i></div>
                <div class="info">
                    <div class="number">{{ $students->count() }}</div>
                    <div class="label">Students</div>
                </div>
            </div>
        </div>

        <!-- ===== ROLE TABS ===== -->
        <div class="role-tabs" id="roleTabs">
            <button class="role-tab active" data-role="all" onclick="filterUsers('all')">
                <i class="bi bi-people"></i> All Users
                <span class="badge-count">{{ $users->count() }}</span>
            </button>
            <button class="role-tab" data-role="admin" onclick="filterUsers('admin')">
                <i class="bi bi-shield-lock"></i> Admins
                <span class="badge-count">{{ $admins->count() }}</span>
            </button>
            <button class="role-tab" data-role="lecturer" onclick="filterUsers('lecturer')">
                <i class="bi bi-person-badge"></i> Lecturers
                <span class="badge-count">{{ $lecturers->count() }}</span>
            </button>
            <button class="role-tab" data-role="student" onclick="filterUsers('student')">
                <i class="bi bi-person"></i> Students
                <span class="badge-count">{{ $students->count() }}</span>
            </button>
        </div>

        <!-- ===== USER TABLE ===== -->
        <div class="user-table-wrap">
            <div class="table-header">
                <div class="title">
                    <i class="bi bi-list-ul"></i> <span id="tableTitle">All Users</span>
                </div>
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" placeholder="Search users...">
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="user-table" id="userTable">
                    <thead>
                        <tr>
                            <th style="min-width:40px;">#</th>
                            <th style="min-width:150px;">Name</th>
                            <th style="min-width:180px;">Email</th>
                            <th style="min-width:80px;">Role</th>
                            <th style="min-width:80px;">Year</th>
                            <th style="min-width:80px;">Password</th>
                            <th style="min-width:180px; text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        @forelse($users as $user)
                            @php
                                $roleName = '';
                                $roleClass = '';
                                $avatarClass = '';
                                if ($user->role_id == 1) {
                                    $roleName = 'Admin';
                                    $roleClass = 'admin';
                                    $avatarClass = 'admin';
                                } elseif ($user->role_id == 2) {
                                    $roleName = 'Lecturer';
                                    $roleClass = 'lecturer';
                                    $avatarClass = 'lecturer';
                                } else {
                                    $roleName = 'Student';
                                    $roleClass = 'student';
                                    $avatarClass = 'student';
                                }

                                $yearDisplay = '-';
                                if ($user->current_year) {
                                    $suffix = 'th';
                                    if ($user->current_year == 1) {
                                        $suffix = 'st';
                                    } elseif ($user->current_year == 2) {
                                        $suffix = 'nd';
                                    } elseif ($user->current_year == 3) {
                                        $suffix = 'rd';
                                    }
                                    $yearDisplay = $user->current_year . $suffix . ' Year';
                                }
                                $initials = strtoupper(substr($user->name, 0, 2));
                            @endphp
                            <tr data-role="{{ $roleName }}"
                                data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                                <td style="text-align:center; font-weight:600; color:var(--text-gray); font-size:0.7rem;">
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                        <div class="user-avatar-sm {{ $avatarClass }}">{{ $initials }}</div>
                                        <span style="font-weight:500; color:var(--text-dark);">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td style="color:var(--text-gray); font-size:0.75rem;">{{ $user->email }}</td>
                                <td><span class="badge-role {{ $roleClass }}">{{ $roleName }}</span></td>
                                <td style="font-size:0.75rem; color:var(--text-gray);">{{ $yearDisplay }}</td>
                                <td>
                                    @if ($user->must_change_password)
                                        <span class="badge-status pending">Pending</span>
                                    @else
                                        <span class="badge-status set">Set</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; gap:0.3rem; justify-content:center; flex-wrap:wrap;">
                                        @if ($user->must_change_password)
                                            <button class="btn-action-sm btn-telegram"
                                                onclick="shareTelegramUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                style="background:#0088cc; color:var(--white); border:none; padding:4px 8px; border-radius:4px; cursor:pointer; font-size:10px; display:inline-flex; align-items:center; gap:3px;">
                                                📱 TG
                                            </button>
                                            <button class="btn-action-sm btn-copy"
                                                onclick="copyUserLink({{ $user->id }})"
                                                style="background:var(--primary); color:var(--white); border:none; padding:4px 8px; border-radius:4px; cursor:pointer; font-size:10px; display:inline-flex; align-items:center; gap:3px;">
                                                📋 Copy
                                            </button>
                                        @endif
                                        <button class="btn-action-sm btn-edit"
                                            onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', {{ $user->role_id }}, {{ $user->department_id ?? 'null' }}, {{ $user->current_year ?? 'null' }})">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button class="btn-action-sm btn-delete"
                                            onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    style="padding:2.5rem 1rem; text-align:center; color:var(--text-gray);">
                                    <i class="bi bi-people"
                                        style="font-size:2rem; display:block; margin-bottom:0.5rem; color:#d1d5db;"></i>
                                    <p style="font-size:0.9rem; margin:0;">No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ===== EDIT MODAL ===== -->
    <div id="editModal" class="edit-modal">
        <div class="edit-modal-content">
            <div class="edit-modal-header">
                <h4><i class="bi bi-pencil-square"></i> Edit User</h4>
                <button class="edit-modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editUserForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role_id" id="edit_role_id">
                        <option value="1">Admin</option>
                        <option value="2">Lecturer</option>
                        <option value="3">Student</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id" id="edit_department_id">
                        <option value="">Select Department</option>
                        @foreach (\App\Models\Department::all() as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="edit_year_field">
                    <label>Current Year (Students)</label>
                    <select name="current_year" id="edit_current_year">
                        <option value="">Select Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                        <option value="5">5th Year</option>
                        <option value="6">6th Year</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" id="edit_is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div style="display:flex; gap:0.5rem; margin-top:1rem; justify-content:flex-end;">
                    <button type="button" class="btn-cancel-modal" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== TOAST CONTAINER ===== -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        let currentFilter = 'all';

        function filterUsers(role) {
            currentFilter = role;
            const rows = document.querySelectorAll('#userTableBody tr');
            const tabs = document.querySelectorAll('.role-tab');
            const title = document.getElementById('tableTitle');

            tabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.getAttribute('data-role') === role) {
                    tab.classList.add('active');
                }
            });

            const roleNames = {
                'all': 'All Users',
                'admin': 'Admins',
                'lecturer': 'Lecturers',
                'student': 'Students'
            };
            title.textContent = roleNames[role] || 'All Users';

            let visibleCount = 0;
            rows.forEach(row => {
                const rowRole = row.getAttribute('data-role');
                if (role === 'all' || rowRole === role.charAt(0).toUpperCase() + role.slice(1)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#userTableBody tr');

            rows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                if (searchTerm === '' || searchData.includes(searchTerm)) {
                    const rowRole = row.getAttribute('data-role');
                    const role = currentFilter;
                    if (role === 'all' || rowRole === role.charAt(0).toUpperCase() + role.slice(1)) {
                        row.style.display = '';
                    }
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function openEditModal(id, name, email, roleId, departmentId, currentYear) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editUserForm');
            form.action = '/admin/users/' + id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role_id').value = roleId;
            document.getElementById('edit_department_id').value = departmentId || '';
            document.getElementById('edit_current_year').value = currentYear || '';
            document.getElementById('edit_is_active').value = 1;

            const yearField = document.getElementById('edit_year_field');
            if (roleId == 3) {
                yearField.style.display = 'block';
            } else {
                yearField.style.display = 'none';
            }

            modal.classList.add('show');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('show');
        }

        document.getElementById('edit_role_id')?.addEventListener('change', function() {
            const yearField = document.getElementById('edit_year_field');
            if (this.value == 3) {
                yearField.style.display = 'block';
            } else {
                yearField.style.display = 'none';
            }
        });

        document.getElementById('editModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });

        function deleteUser(id, name) {
            if (confirm('Are you sure you want to delete user: ' + name + '? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/admin/users/' + id;
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // ============================================================
        // TOAST NOTIFICATION
        // ============================================================
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.textContent = message;
            container.appendChild(toast);

            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.5s';
                setTimeout(function() {
                    toast.remove();
                }, 500);
            }, 5000);
        }

        // ============================================================
        // COPY LINK FUNCTION (for new user creation success)
        // ============================================================
        function copyLink() {
            var copyText = document.getElementById('setupLink');
            if (!copyText) {
                var linkInput = document.querySelector('input[value*="password/setup"]');
                if (linkInput) {
                    copyText = linkInput;
                }
            }
            if (copyText) {
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand('copy');
                showToast('✅ Link copied to clipboard! Share it with the user.', 'success');
            } else {
                showToast('❌ Setup link not found. Please create a user first.', 'error');
            }
        }

        // ============================================================
        // SHARE TELEGRAM (for new user creation)
        // ============================================================
        function shareTelegram() {
            var link = document.getElementById('setupLink')?.value;
            var userName = '{{ session('user_name') ?? 'User' }}';

            if (!link) {
                var linkInput = document.querySelector('input[value*="password/setup"]');
                if (linkInput) {
                    link = linkInput.value;
                } else {
                    showToast('❌ Setup link not found. Please create a user first.', 'error');
                    return;
                }
            }

            var message = `📢 MTU Academic System Account Ready!

Dear ${userName},

Your account has been created for the MTU Academic System.

🔗 Password Setup Link:
${link}

📝 Instructions:
1. Click the link above (or copy and paste in browser)
2. Create your secure password (minimum 8 characters)
3. Login at: ${window.location.origin}/login

🔐 This link will expire in 48 hours for security.

Thank you,
MTU Academic System Team`;

            var telegramUrl = `https://t.me/share/url?url=${encodeURIComponent(link)}&text=${encodeURIComponent(message)}`;
            window.open(telegramUrl, '_blank');
        }

        // ============================================================
        // SHARE WHATSAPP (for new user creation)
        // ============================================================
        function shareWhatsApp() {
            var link = document.getElementById('setupLink')?.value;
            var userName = '{{ session('user_name') ?? 'User' }}';

            if (!link) {
                var linkInput = document.querySelector('input[value*="password/setup"]');
                if (linkInput) {
                    link = linkInput.value;
                } else {
                    showToast('❌ Setup link not found. Please create a user first.', 'error');
                    return;
                }
            }

            var message = `📢 MTU Academic System Account Ready!

Dear ${userName},

Your account has been created. Please set your password here:
${link}

After setting password, login at: ${window.location.origin}/login

Thank you,
MTU Academic System Team`;

            var whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }

        // ============================================================
        // PRINT LINK (for new user creation)
        // ============================================================
        function printLink() {
            var link = document.getElementById('setupLink')?.value;
            var userName = '{{ session('user_name') ?? 'User' }}';
            var userEmail = '{{ session('user_email') ?? '' }}';

            if (!link) {
                var linkInput = document.querySelector('input[value*="password/setup"]');
                if (linkInput) {
                    link = linkInput.value;
                } else {
                    showToast('❌ Setup link not found. Please create a user first.', 'error');
                    return;
                }
            }

            var printWindow = window.open('', '_blank', 'width=600,height=600');
            printWindow.document.write(`
                <html>
                <head>
                    <title>Password Setup Link - MTU Academic System</title>
                    <style>
                        body { font-family: 'Inter', Arial, sans-serif; padding: 40px; max-width: 600px; margin: 0 auto; }
                        .header { text-align: center; border-bottom: 3px solid #0A2463; padding-bottom: 20px; margin-bottom: 30px; }
                        .header h1 { color: #0A2463; font-size: 24px; margin: 0; }
                        .header p { color: #6b7280; font-size: 14px; margin: 5px 0 0; }
                        .user-info { background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0; }
                        .user-info label { font-weight: 600; color: #374151; }
                        .link-box { background: #fef3c7; padding: 20px; border-radius: 8px; border: 2px dashed #f59e0b; margin: 20px 0; word-break: break-all; font-family: monospace; font-size: 14px; }
                        .instructions { background: #eff6ff; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #3b82f6; }
                        .instructions ol { margin: 10px 0 0 20px; color: #1e40af; }
                        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #6b7280; }
                        .expiry { color: #dc2626; font-weight: 600; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>🏛️ MTU Academic System</h1>
                        <p>Mandalay Technological University</p>
                    </div>

                    <h2>📋 Password Setup</h2>

                    <div class="user-info">
                        <label>👤 User:</label> ${userName}<br>
                        <label>📧 Email:</label> ${userEmail}
                    </div>

                    <p><strong>🔗 Your Password Setup Link:</strong></p>
                    <div class="link-box">${link}</div>

                    <div class="instructions">
                        <strong>📝 Instructions:</strong>
                        <ol>
                            <li>Type the link above in your browser</li>
                            <li>Create a secure password (minimum 8 characters)</li>
                            <li>Click "Set Password" to activate your account</li>
                            <li>Login at: ${window.location.origin}/login</li>
                        </ol>
                    </div>

                    <p><span class="expiry">🔐 This link expires in 48 hours</span></p>

                    <div class="footer">
                        <p>Generated: ${new Date().toLocaleString()}</p>
                        <p>© ${new Date().getFullYear()} Mandalay Technological University</p>
                    </div>

                    <script>
                        window.print();
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        // ============================================================
        // SHARE TELEGRAM FOR EXISTING USER
        // ============================================================
        function shareTelegramUser(userId, userName) {
            fetch('/admin/users/' + userId + '/setup-link')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var link = data.link;
                        var message = `📢 MTU Academic System Account Ready!

Dear ${userName},

Your account has been created for the MTU Academic System.

🔗 Password Setup Link:
${link}

📝 Instructions:
1. Click the link above (or copy and paste in browser)
2. Create your secure password (minimum 8 characters)
3. Login at: ${window.location.origin}/login

🔐 This link will expire in 48 hours for security.

Thank you,
MTU Academic System Team`;

                        var telegramUrl =
                            `https://t.me/share/url?url=${encodeURIComponent(link)}&text=${encodeURIComponent(message)}`;
                        window.open(telegramUrl, '_blank');
                    } else {
                        showToast('❌ Failed to get setup link', 'error');
                    }
                })
                .catch(() => {
                    showToast('❌ Error fetching setup link', 'error');
                });
        }

        // ============================================================
        // COPY LINK FOR EXISTING USER
        // ============================================================
        function copyUserLink(userId) {
            fetch('/admin/users/' + userId + '/setup-link')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        var link = data.link;
                        if (navigator.clipboard) {
                            navigator.clipboard.writeText(link).then(() => {
                                showToast('✅ Link copied to clipboard! Share it with ' + data.user.name,
                                    'success');
                            });
                        } else {
                            var textarea = document.createElement('textarea');
                            textarea.value = link;
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand('copy');
                            document.body.removeChild(textarea);
                            showToast('✅ Link copied to clipboard! Share it with ' + data.user.name, 'success');
                        }
                    } else {
                        showToast('❌ Failed to get setup link', 'error');
                    }
                })
                .catch(() => {
                    showToast('❌ Error fetching setup link', 'error');
                });
        }
    </script>
@endsection
