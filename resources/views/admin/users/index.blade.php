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
            background: #800000;
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-create-user:hover {
            background: #5f0000;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        .stats-row {
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
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .stat-card:hover {
            border-color: #800000;
            box-shadow: 0 2px 8px rgba(128, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .stat-card .icon {
            width: 44px;
            height: 44px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .stat-card .icon.admin {
            background: #fef3c7;
            color: #92400e;
        }

        .stat-card .icon.lecturer {
            background: #eff6ff;
            color: #3b82f6;
        }

        .stat-card .icon.student {
            background: #ecfdf5;
            color: #10b981;
        }

        .stat-card .icon.total {
            background: #fef2f2;
            color: #ef4444;
        }

        .stat-card .info .number {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1.2;
        }

        .stat-card .info .label {
            font-size: 0.65rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .role-tabs {
            display: flex;
            gap: 0.25rem;
            background: white;
            border-radius: 0.5rem;
            padding: 0.25rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .role-tab {
            padding: 0.4rem 1.2rem;
            border: none;
            background: transparent;
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7280;
            border-radius: 0.4rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .role-tab:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .role-tab.active {
            background: #800000;
            color: white;
            box-shadow: 0 2px 8px rgba(128, 0, 0, 0.25);
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
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .user-table-wrap .table-header {
            padding: 0.75rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .user-table-wrap .table-header .title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1f2937;
        }

        .user-table-wrap .table-header .title i {
            color: #800000;
            margin-right: 0.4rem;
        }

        .user-table-wrap .table-header .search-box {
            display: flex;
            align-items: center;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.15rem 0.6rem;
            transition: all 0.2s;
        }

        .user-table-wrap .table-header .search-box:focus-within {
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .user-table-wrap .table-header .search-box input {
            border: none;
            outline: none;
            padding: 0.3rem 0.4rem;
            font-size: 0.75rem;
            color: #1a2332;
            background: transparent;
            width: 180px;
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
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
            white-space: nowrap;
        }

        .user-table tbody td {
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .user-table tbody tr {
            transition: all 0.2s;
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
            color: white;
        }

        .user-avatar-sm.admin {
            background: #92400e;
        }

        .user-avatar-sm.lecturer {
            background: #3b82f6;
        }

        .user-avatar-sm.student {
            background: #10b981;
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
            background: #fef3c7;
            color: #92400e;
        }

        .badge-role.lecturer {
            background: #eff6ff;
            color: #3b82f6;
        }

        .badge-role.student {
            background: #ecfdf5;
            color: #10b981;
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
            background: #fef3c7;
            color: #92400e;
        }

        .badge-status.set {
            background: #ecfdf5;
            color: #10b981;
        }

        .btn-action-sm {
            padding: 0.15rem 0.4rem;
            border-radius: 0.3rem;
            font-size: 0.6rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.15rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-action-sm:hover {
            transform: translateY(-1px);
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-edit:hover {
            background: #fde68a;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete:hover {
            background: #fca5a5;
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #9ca3af;
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
            background: white;
            border-radius: 0.75rem;
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
            border-bottom: 1px solid #e5e7eb;
        }

        .edit-modal-header h4 {
            margin: 0;
            color: #800000;
            font-size: 1rem;
            font-weight: 700;
        }

        .edit-modal-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #6b7280;
            transition: all 0.2s;
        }

        .edit-modal-close:hover {
            color: #ef4444;
        }

        .edit-modal .form-group {
            margin-bottom: 0.8rem;
        }

        .edit-modal .form-group label {
            font-size: 0.7rem;
            font-weight: 600;
            color: #374151;
            display: block;
            margin-bottom: 0.2rem;
        }

        .edit-modal .form-group input,
        .edit-modal .form-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.4rem;
            font-size: 0.8rem;
        }

        .edit-modal .btn-save {
            background: #800000;
            color: white;
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 0.4rem;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .edit-modal .btn-save:hover {
            background: #5f0000;
        }

        .edit-modal .btn-cancel-modal {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 0.4rem;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.8rem;
            transition: all 0.2s;
        }

        .edit-modal .btn-cancel-modal:hover {
            background: #e5e7eb;
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
                style="background:#ecfdf5; color:#10b981; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1rem; border-left:3px solid #10b981; display:flex; align-items:center; gap:0.5rem;">
                <i class="bi bi-check-circle"></i> {!! session('success') !!}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background:#fef2f2; color:#ef4444; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1rem; border-left:3px solid #ef4444; display:flex; align-items:center; gap:0.5rem;">
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
                            <th style="min-width:130px; text-align:center;">Actions</th>
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
                                <td style="text-align:center; font-weight:600; color:#6b7280; font-size:0.7rem;">
                                    {{ $loop->iteration }}</td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                        <div class="user-avatar-sm {{ $avatarClass }}">{{ $initials }}</div>
                                        <span style="font-weight:500; color:#1f2937;">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td style="color:#6b7280; font-size:0.75rem;">{{ $user->email }}</td>
                                <td><span class="badge-role {{ $roleClass }}">{{ $roleName }}</span></td>
                                <td style="font-size:0.75rem; color:#6b7280;">{{ $yearDisplay }}</td>
                                <td>
                                    @if ($user->must_change_password)
                                        <span class="badge-status pending">Pending</span>
                                    @else
                                        <span class="badge-status set">Set</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    <div style="display:flex; gap:0.3rem; justify-content:center;">
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
                                <td colspan="7" style="padding:2.5rem 1rem; text-align:center; color:#9ca3af;">
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
    </script>
@endsection
