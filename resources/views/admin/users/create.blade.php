@extends('layouts.app')

@section('title', 'Create User')
@section('role', 'Admin')
@section('page-title', ' Create New User')
@section('welcome-text', 'Add a new user to the system')

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

        .create-wrapper {
            max-width: 700px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary);
        }

        .create-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .create-card .header {
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .create-card .header h2 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .create-card .header h2 i {
            color: var(--primary);
        }

        .create-card .header p {
            color: var(--text-gray);
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
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-group label .field-hint {
            font-weight: 400;
            color: var(--text-gray);
            font-size: 0.7rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.85rem;
            transition: var(--transition);
            background: #fafbfc;
            font-family: 'Inter', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .help-text {
            font-size: 0.65rem;
            color: var(--text-gray);
            margin-top: 0.2rem;
        }

        .error-text {
            color: var(--danger);
            font-size: 0.7rem;
            margin-top: 0.2rem;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
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
                <h2><i class="bi bi-person-plus"></i> Create New User</h2>
                <p>Fill in the details to create a new user account</p>
            </div>

            @if ($errors->any())
                <div
                    style="background:var(--danger-light); color:#991b1b; padding:12px; border-radius:8px; margin-bottom:1.5rem; border-left:4px solid var(--danger);">
                    @foreach ($errors->all() as $error)
                        <p style="margin:2px 0; font-size:0.85rem;"><i class="bi bi-exclamation-triangle-fill"></i>
                            {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label>Full Name <span class="required">*</span></label>
                        <input type="text" name="name" placeholder="Enter full name" value="{{ old('name') }}"
                            required>
                        @error('name')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" name="email" placeholder="Enter email address" value="{{ old('email') }}"
                            required>
                        @error('email')
                            <div class="error-text">{{ $message }}</div>
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
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Department <span class="required" id="deptRequired">*</span></label>
                        <select name="department_id" id="departmentSelect">
                            <option value="">Select Department</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->code }} - {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="studentFields" style="display: {{ old('role_id', 3) == 3 ? 'block' : 'none' }};">
                    <div class="form-group">
                        <label>Student ID <span class="required">*</span></label>
                        <input type="text" name="student_id" placeholder="Enter student ID (e.g., CS-2024-001)"
                            value="{{ old('student_id') }}">
                        <div class="help-text">Required for students - Format: Department-Year-Number</div>
                        @error('student_id')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="lecturerFields" style="display: {{ old('role_id') == 2 ? 'block' : 'none' }};">
                    <div class="form-group">
                        <label>Specialization <span class="field-hint">(Optional)</span></label>
                        <input type="text" name="specialization" placeholder="e.g., Computer Science, Mathematics"
                            value="{{ old('specialization') }}">
                        <div class="help-text">For lecturers</div>
                        @error('specialization')
                            <div class="error-text">{{ $message }}</div>
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
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex; gap:0.5rem; margin-top:1.5rem;">
                    <a href="{{ route('admin.users.index') }}" class="btn-cancel" style="flex:1;">Cancel</a>
                    <button type="submit" class="btn-submit" style="flex:2;">
                        <i class="bi bi-person-plus"></i> Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const roleSelect = document.getElementById('roleSelect');
        const yearField = document.getElementById('yearField');
        const studentFields = document.getElementById('studentFields');
        const lecturerFields = document.getElementById('lecturerFields');
        const deptRequired = document.getElementById('deptRequired');
        const departmentSelect = document.getElementById('departmentSelect');

        function toggleFields() {
            const roleId = roleSelect.value;

            yearField.style.display = 'none';
            studentFields.style.display = 'none';
            lecturerFields.style.display = 'none';

            if (roleId == '3') {
                yearField.style.display = 'block';
                studentFields.style.display = 'block';
                lecturerFields.style.display = 'none';
                deptRequired.textContent = '*';
                document.querySelector('label[for="departmentSelect"]')?.textContent = 'Department *';
            } else if (roleId == '2') {
                yearField.style.display = 'none';
                studentFields.style.display = 'none';
                lecturerFields.style.display = 'block';
                deptRequired.textContent = '*';
                document.querySelector('label[for="departmentSelect"]')?.textContent = 'Department *';
            } else {
                yearField.style.display = 'none';
                studentFields.style.display = 'none';
                lecturerFields.style.display = 'none';
                deptRequired.textContent = '';
                document.querySelector('label[for="departmentSelect"]')?.textContent = 'Department';
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', toggleFields);
            toggleFields();
        }
    </script>
@endsection
