@extends('layouts.app')

@section('title', 'Create User')
@section('role', 'Admin')
@section('page-title', '👤 Create New User')
@section('welcome-text', 'Add a new user to the system')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .create-wrapper {
            max-width: 700px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7a8f;
            text-decoration: none;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s;
        }

        .back-link:hover {
            color: #800000;
        }

        .create-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e9edf4;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .create-card .header {
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e9edf4;
        }

        .create-card .header h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a2332;
            margin: 0;
        }

        .create-card .header p {
            color: #6b7280;
            font-size: 0.85rem;
            margin: 0.1rem 0 0;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            color: #1a2332;
            margin-bottom: 0.2rem;
        }

        .form-group label .required {
            color: #ef4444;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-submit {
            background: #800000;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            background: #5f0000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .help-text {
            font-size: 0.65rem;
            color: #6b7280;
            margin-top: 0.2rem;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .create-card {
                padding: 1.25rem;
            }
        }
    </style>

    <div class="create-wrapper">
        <a href="{{ route('admin.users.index') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to User Management
        </a>

        <div class="create-card">
            <div class="header">
                <h2><i class="bi bi-person-plus" style="color:#800000;"></i> Create New User</h2>
                <p>Fill in the details to create a new user account</p>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="name" placeholder="Enter full name" value="{{ old('name') }}"
                            required>
                        @error('name')
                            <div style="color:#ef4444; font-size:0.7rem; margin-top:0.2rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="Enter email address" value="{{ old('email') }}"
                            required>
                        @error('email')
                            <div style="color:#ef4444; font-size:0.7rem; margin-top:0.2rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Role <span class="required">*</span></label>
                        <select name="role_id" id="roleSelect" required>
                            <option value="3" {{ old('role_id', 3) == 3 ? 'selected' : '' }}>Student</option>
                            <option value="2" {{ old('role_id') == 2 ? 'selected' : '' }}>Lecturer</option>
                            <option value="1" {{ old('role_id') == 1 ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role_id')
                            <div style="color:#ef4444; font-size:0.7rem; margin-top:0.2rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <select name="department_id">
                            <option value="">Select Department</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->code }} - {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div style="color:#ef4444; font-size:0.7rem; margin-top:0.2rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group" id="yearField"
                    style="{{ old('role_id', 3) == 3 ? 'display:block;' : 'display:none;' }}">
                    <label>Current Year <span class="required">(for students)</span></label>
                    <select name="current_year">
                        <option value="">Select Year</option>
                        <option value="1" {{ old('current_year') == 1 ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ old('current_year') == 2 ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ old('current_year') == 3 ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ old('current_year') == 4 ? 'selected' : '' }}>4th Year</option>
                        <option value="5" {{ old('current_year') == 5 ? 'selected' : '' }}>5th Year</option>
                        <option value="6" {{ old('current_year') == 6 ? 'selected' : '' }}>6th Year</option>
                    </select>
                    <div class="help-text">Required for students</div>
                    @error('current_year')
                        <div style="color:#ef4444; font-size:0.7rem; margin-top:0.2rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex; gap:0.5rem; margin-top:1.5rem;">
                    <a href="{{ route('admin.users.index') }}" class="btn-cancel" style="flex:1;">Cancel</a>
                    <button type="submit" class="btn-submit" style="flex:2;">
                        <i class="bi bi-envelope"></i> Create Account & Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
    </script>
@endsection
