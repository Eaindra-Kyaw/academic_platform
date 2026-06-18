@extends('layouts.app')

@section('title', 'User Management')
@section('role', 'Admin')
@section('page-title', 'User Management')
@section('welcome-text', 'Create and manage user accounts')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto;">
        <style>
            .form-card {
                background: white;
                border-radius: 1rem;
                padding: 1.5rem;
                border: 1px solid #e5e7eb;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                font-size: 0.8rem;
                color: #374151;
            }

            .form-group input,
            .form-group select {
                width: 100%;
                padding: 0.6rem;
                border: 1px solid #d1d5db;
                border-radius: 0.5rem;
                font-size: 0.8rem;
            }

            .btn-create {
                background: #800000;
                color: white;
                border: none;
                padding: 0.6rem 1rem;
                border-radius: 0.5rem;
                cursor: pointer;
                font-weight: 600;
                width: 100%;
                margin-top: 0.5rem;
            }

            .btn-create:hover {
                background: #5f0000;
            }

            .btn-save {
                background: #800000;
                color: white;
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                cursor: pointer;
                font-weight: 600;
            }

            .btn-save:hover {
                background: #5f0000;
            }

            .alert-success {
                background: #dcfce7;
                color: #166534;
                padding: 0.75rem;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
                border-left: 3px solid #10b981;
            }

            .alert-success a {
                color: #166534;
                text-decoration: underline;
                font-weight: 600;
            }

            .alert-success a:hover {
                color: #0a3d2a;
            }

            .alert-danger {
                background: #fee2e2;
                color: #991b1b;
                padding: 0.75rem;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
                border-left: 3px solid #dc2626;
            }

            /* Table Styles - OPTIMIZED COLUMN WIDTHS */
            .user-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
                font-size: 0.7rem;
            }

            .user-table th,
            .user-table td {
                padding: 0.5rem 0.4rem;
                text-align: left;
                border-bottom: 1px solid #e5e7eb;
                vertical-align: middle;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .user-table th {
                background: #f9fafb;
                font-weight: 600;
                font-size: 0.65rem;
                text-transform: uppercase;
                color: #6b7280;
            }

            /* OPTIMIZED COLUMN WIDTHS - More space for Name and Email */
            .user-table th:nth-child(1) {
                width: 20%;
            }

            /* Name - increased */
            .user-table th:nth-child(2) {
                width: 25%;
            }

            /* Email - increased */
            .user-table th:nth-child(3) {
                width: 10%;
            }

            /* Role */
            .user-table th:nth-child(4) {
                width: 10%;
            }

            /* Year */
            .user-table th:nth-child(5) {
                width: 10%;
            }

            /* Password */
            .user-table th:nth-child(6) {
                width: 15%;
            }

            /* Actions - reduced */

            .badge {
                padding: 0.15rem 0.4rem;
                border-radius: 20px;
                font-size: 0.6rem;
                font-weight: 600;
                display: inline-block;
                white-space: nowrap;
            }

            .badge-admin {
                background: #800000;
                color: white;
            }

            .badge-lecturer {
                background: #3b82f6;
                color: white;
            }

            .badge-student {
                background: #10b981;
                color: white;
            }

            .badge-pending {
                background: #f59e0b;
                color: white;
            }

            .badge-set {
                background: #10b981;
                color: white;
            }

            .action-buttons {
                display: flex;
                gap: 0.3rem;
                align-items: center;
                flex-wrap: nowrap;
                justify-content: flex-start;
            }

            .btn-edit-action {
                background: #fef3c7;
                color: #d97706;
                border: none;
                padding: 0.2rem 0.5rem;
                border-radius: 0.3rem;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.15rem;
                font-size: 0.6rem;
                font-weight: 500;
                transition: all 0.2s;
                white-space: nowrap;
            }

            .btn-edit-action:hover {
                background: #fde68a;
            }

            .btn-delete-action {
                background: #fef2f2;
                color: #dc2626;
                border: none;
                padding: 0.2rem 0.5rem;
                border-radius: 0.3rem;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.15rem;
                font-size: 0.6rem;
                font-weight: 500;
                transition: all 0.2s;
                white-space: nowrap;
            }

            .btn-delete-action:hover {
                background: #fee2e2;
            }

            .two-columns {
                display: grid;
                grid-template-columns: 1fr 2fr;
                gap: 1.5rem;
            }

            /* Modal Styles */
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
                border-radius: 1rem;
                padding: 1.5rem;
                max-width: 500px;
                width: 90%;
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
            }

            .edit-modal-close {
                background: none;
                border: none;
                font-size: 1.2rem;
                cursor: pointer;
                color: #6b7280;
            }

            .setup-link-box {
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
                border-radius: 0.5rem;
                padding: 0.75rem;
                margin-top: 0.5rem;
                word-break: break-all;
            }

            .setup-link-box a {
                color: #166534;
                text-decoration: underline;
                font-weight: 500;
            }

            .setup-link-box a:hover {
                color: #0a3d2a;
            }

            @media (max-width: 1024px) {
                .two-columns {
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }

                .user-table th:nth-child(1) {
                    width: 18%;
                }

                .user-table th:nth-child(2) {
                    width: 22%;
                }

                .user-table th:nth-child(6) {
                    width: 18%;
                }
            }

            @media (max-width: 768px) {
                .user-table th:nth-child(1) {
                    width: 16%;
                }

                .user-table th:nth-child(2) {
                    width: 20%;
                }

                .user-table th:nth-child(6) {
                    width: 20%;
                }

                .btn-edit-action,
                .btn-delete-action {
                    padding: 0.15rem 0.35rem;
                    font-size: 0.55rem;
                }
            }
        </style>

        @if (session('success'))
            <div class="alert-success">
                <i class="bi bi-check-circle"></i>
                {!! session('success') !!}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-danger">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-danger">
                <i class="bi bi-exclamation-triangle"></i> Please fix the errors below.
                <ul style="margin-top: 0.5rem; margin-bottom: 0; padding-left: 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="two-columns">
            <!-- Create User Form -->
            <div class="form-card">
                <h4 style="color: #800000; margin-bottom: 1rem;"><i class="bi bi-person-plus"></i> Create New User</h4>
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="name" placeholder="Enter full name" value="{{ old('name') }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <select name="role_id" id="roleSelect" required>
                            <option value="3" {{ old('role_id', 3) == 3 ? 'selected' : '' }}>Student</option>
                            <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Lecturer</option>
                            <option value="1" {{ old('role_id') == 1 ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department_id">
                            <option value="">Select Department</option>
                            @foreach (\App\Models\Department::orderBy('code')->get() as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->code }} - {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="yearField"
                        style="{{ old('role_id', 3) == 3 ? 'display: block;' : 'display: none;' }}">
                        <label>Current Year (for students)</label>
                        <select name="current_year">
                            <option value="">Select Year</option>
                            <option value="1" {{ old('current_year') == 1 ? 'selected' : '' }}>1st Year</option>
                            <option value="2" {{ old('current_year') == 2 ? 'selected' : '' }}>2nd Year</option>
                            <option value="3" {{ old('current_year') == 3 ? 'selected' : '' }}>3rd Year</option>
                            <option value="4" {{ old('current_year') == 4 ? 'selected' : '' }}>4th Year</option>
                            <option value="5" {{ old('current_year') == 5 ? 'selected' : '' }}>5th Year</option>
                            <option value="6" {{ old('current_year') == 6 ? 'selected' : '' }}>6th Year</option>
                        </select>
                        <small style="color: #666;">Required for students</small>
                    </div>
                    <button type="submit" class="btn-create">
                        <i class="bi bi-envelope"></i> Create Account & Send Invitation
                    </button>
                </form>
            </div>

            <!-- Users List -->
            <div class="form-card">
                <h4 style="color: #800000; margin-bottom: 1rem;"><i class="bi bi-people"></i> Existing Users</h4>
                <div style="overflow-x: visible;">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Year</th>
                                <th>Password</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (\App\Models\User::with('role')->orderBy('id')->get() as $user)
                                @php
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
                                @endphp
                                <tr>
                                    <td title="{{ $user->name }}"><strong>{{ $user->name }}</strong></td>
                                    <td title="{{ $user->email }}">{{ $user->email }}</td>
                                    <td>
                                        @if ($user->role_id == 1)
                                            <span class="badge badge-admin">Admin</span>
                                        @elseif($user->role_id == 2)
                                            <span class="badge badge-lecturer">Lecturer</span>
                                        @else
                                            <span class="badge badge-student">Student</span>
                                        @endif
                                    </td>
                                    <td>{{ $yearDisplay }}</td>
                                    <td>
                                        @if ($user->must_change_password)
                                            <span class="badge badge-pending">Pending</span>
                                        @else
                                            <span class="badge badge-set">Set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-edit-action"
                                                onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', {{ $user->role_id }}, {{ $user->department_id ?? 'null' }}, {{ $user->current_year ?? 'null' }})">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button class="btn-delete-action"
                                                onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
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
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role_id" id="edit_role_id" class="form-control">
                        <option value="1">Admin</option>
                        <option value="2">Lecturer</option>
                        <option value="3">Student</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id" id="edit_department_id" class="form-control">
                        <option value="">Select Department</option>
                        @foreach (\App\Models\Department::all() as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" id="edit_year_field">
                    <label>Current Year (for students)</label>
                    <select name="current_year" id="edit_current_year" class="form-control">
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
                    <select name="is_active" id="edit_is_active" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="button" class="btn-delete-action" onclick="closeEditModal()"
                        style="background: #f3f4f6; color: #374151;">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
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

        const roleSelect = document.getElementById('roleSelect');
        const yearField = document.getElementById('yearField');

        function toggleYearField() {
            if (roleSelect.value == '3') {
                yearField.style.display = 'block';
            } else {
                yearField.style.display = 'none';
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', toggleYearField);
            toggleYearField();
        }

        document.getElementById('edit_role_id')?.addEventListener('change', function() {
            const yearField = document.getElementById('edit_year_field');
            if (this.value == 3) {
                yearField.style.display = 'block';
            } else {
                yearField.style.display = 'none';
            }
        });

        document.addEventListener('click', function(event) {
            const modal = document.getElementById('editModal');
            if (modal && modal.classList.contains('show')) {
                if (!modal.contains(event.target) && !event.target.closest('.btn-edit-action')) {
                    closeEditModal();
                }
            }
        });
    </script>
@endsection
