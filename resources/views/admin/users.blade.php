@extends('layouts.app')

@section('title', 'User Management')
@section('role', 'Admin')
@section('page-title', 'User Management')
@section('welcome-text', 'Create and manage user accounts')

@section('sidebar')
    <div class="nav-label">Management</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="nav-item active"><i class="bi bi-people"></i><span>User Management</span></a>
    <a href="#" class="nav-item"><i class="bi bi-building"></i><span>Departments</span></a>
    <a href="#" class="nav-item"><i class="bi bi-book"></i><span>Course Management</span></a>
    <div class="nav-label">Analytics</div>
    <a href="#" class="nav-item"><i class="bi bi-calendar"></i><span>Semesters</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up"></i><span>Analytics</span></a>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Reports</span></a>
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
            }

            .btn-create:hover {
                background: #5f0000;
            }

            .alert-success {
                background: #dcfce7;
                color: #166534;
                padding: 0.75rem;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
                border-left: 3px solid #10b981;
                word-break: break-all;
            }

            .user-table {
                width: 100%;
                border-collapse: collapse;
            }

            .user-table th,
            .user-table td {
                padding: 0.75rem;
                text-align: left;
                border-bottom: 1px solid #e5e7eb;
            }

            .user-table th {
                background: #f9fafb;
                font-weight: 600;
                font-size: 0.7rem;
                text-transform: uppercase;
            }

            .badge {
                padding: 0.2rem 0.6rem;
                border-radius: 20px;
                font-size: 0.7rem;
                font-weight: 600;
                display: inline-block;
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

            .btn-sm {
                padding: 0.2rem 0.5rem;
                font-size: 0.7rem;
                border-radius: 0.3rem;
                border: 1px solid #ddd;
                background: none;
                cursor: pointer;
                margin: 0 0.2rem;
            }

            .btn-sm:hover {
                background: #f9fafb;
            }

            .two-columns {
                display: grid;
                grid-template-columns: 1fr 2fr;
                gap: 1.5rem;
            }

            @media (max-width: 768px) {
                .two-columns {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        @if (session('success'))
            <div class="alert-success">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1rem; border-left: 3px solid #dc2626;">
                <i class="bi bi-exclamation-triangle"></i> Please fix the errors below.
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
                        <input type="text" name="name" placeholder="Enter full name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" placeholder="Enter email" required>
                    </div>
                    <div class="form-group">
                        <label>Role *</label>
                        <select name="role_id" id="roleSelect" required>
                            <option value="3">Student</option>
                            <option value="2">Lecturer</option>
                            <option value="1">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department_id">
                            <option value="">Select Department</option>
                            @foreach (\App\Models\Department::all() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="yearField">
                        <label>Current Year (for students)</label>
                        <select name="current_year">
                            <option value="">Select Year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="5">5th Year (Engineering only)</option>
                            <option value="6">6th Year (Engineering only)</option>
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
                <div style="overflow-x: auto;">
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
                                <tr>
                                    <td>{{ $user->name }}
                </div>
                <td>{{ $user->email }}
            </div>
            <td>
                @if ($user->role_id == 1)
                    <span class="badge badge-admin">Admin</span>
                @elseif($user->role_id == 2)
                    <span class="badge badge-lecturer">Lecturer</span>
                @else
                    <span class="badge badge-student">Student</span>
                @endif
        </div>
        <td>{{ $user->current_year ? $user->current_year . ' Year' : '-' }}
    </div>
    <td>
        @if ($user->must_change_password)
            <span class="badge badge-pending">Pending</span>
        @else
            <span class="badge badge-set">Set</span>
        @endif
        </div>
    <td>
        <button class="btn-sm" onclick="alert('Edit feature coming soon for {{ $user->name }}')">Edit</button>
        @if ($user->must_change_password)
            <button class="btn-sm" onclick="resendSetupLink('{{ $user->email }}')">Resend Link</button>
        @endif
        </div>
        </tr>
        @endforeach
        </tbody>
        </table>
        </div>
        </div>
        </div>
        </div>

        <script>
            // Show/hide year field based on role
            const roleSelect = document.getElementById('roleSelect');
            const yearField = document.getElementById('yearField');

            function toggleYearField() {
                if (roleSelect.value == '3') { // Student role
                    yearField.style.display = 'block';
                } else {
                    yearField.style.display = 'none';
                }
            }

            roleSelect.addEventListener('change', toggleYearField);
            toggleYearField(); // Run on page load

            // Resend setup link function
            function resendSetupLink(email) {
                fetch('/admin/users/resend-link', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.link) {
                            alert('Setup link for ' + data.email + ':\n\n' + data.link);
                        } else {
                            alert('Error: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        alert('Error generating setup link');
                    });
            }
        </script>
    @endsection
