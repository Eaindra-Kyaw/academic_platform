<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MTU Academic System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #800000 0%, #4a0000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            max-width: 550px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            max-height: 90vh;
            overflow-y: auto;
        }

        .card::-webkit-scrollbar {
            width: 6px;
        }

        .card::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .card::-webkit-scrollbar-thumb {
            background: #800000;
            border-radius: 10px;
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: #800000;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: #FFD700;
            font-size: 24px;
            font-weight: bold;
        }

        .logo h2 {
            color: #800000;
            font-size: 20px;
        }

        .logo p {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #800000;
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }

        label .required {
            color: #dc2626;
        }

        input,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: #800000;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn:hover {
            background: #5f0000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #800000;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
        }

        .help-text {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .field-hint {
            font-size: 11px;
            color: #6b7280;
            font-weight: 400;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }

        .alert-danger p {
            margin: 2px 0;
            font-size: 13px;
        }

        .alert-danger p i {
            margin-right: 6px;
        }

        .domain-info {
            background: #fef3c7;
            color: #92400e;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 15px;
            border: 1px solid #fde68a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .domain-info i {
            font-size: 18px;
        }

        @media (max-width: 480px) {
            .card {
                padding: 24px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="logo">
            <div class="logo-icon">Uni</div>
            <h2>Academic Portal</h2>
            <p class="subtitle">Register to access your academic dashboard</p>
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <p><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-group">
                <label>Full Name <span class="required">*</span></label>
                <input type="text" name="name" placeholder="Enter your full name" value="{{ old('name') }}"
                    required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" name="email" placeholder="Enter your university email (@mtu.edu.mm)"
                    value="{{ old('email') }}" required>
                <div class="help-text" style="color: #800000; font-weight: 500;">
                    <i class="bi bi-info-circle"></i> Must be @mtu.edu.mm
                </div>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" placeholder="Create a password" required>
                    <div class="help-text">Minimum 8 characters</div>
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="input-group">
                    <label>Confirm Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Confirm your password" required>
                </div>
            </div>

            <div class="input-group">
                <label>Role <span class="required">*</span></label>
                <select name="role_id" id="roleSelect" required>
                    <option value="">Select your role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group" id="departmentField">
                <label>Department <span class="required" id="deptRequired">*</span></label>
                <select name="department_id" id="departmentSelect">
                    <option value="">Select Department</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->code }} - {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div id="studentFields" style="display: none;">
                <div class="input-group">
                    <label>Student ID <span class="required">*</span></label>
                    <input type="text" name="student_id" placeholder="Enter your student ID (e.g., CS-2024-001)"
                        value="{{ old('student_id') }}">
                    <div class="help-text">Format: Department-Year-Number (e.g., CS-2024-001)</div>
                    @error('student_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group">
                    <label>Current Year <span class="required">*</span></label>
                    <select name="current_year">
                        <option value="">Select Year</option>
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('current_year') == $i ? 'selected' : '' }}>
                                {{ $i }}{{ $i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th')) }}
                                Year
                            </option>
                        @endfor
                    </select>
                    @error('current_year')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div id="lecturerFields" style="display: none;">
                <div class="input-group">
                    <label>Specialization <span class="field-hint">(Optional)</span></label>
                    <input type="text" name="specialization" placeholder="e.g., Computer Science, Mathematics"
                        value="{{ old('specialization') }}">
                    @error('specialization')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn">Create Account</button>
        </form>

        <div class="login-link">
            <a href="{{ route('home') }}"><i class="bi bi-arrow-left"></i> Back to Home</a>
            <span style="color: #ccc; margin: 0 8px;">|</span>
            <a href="{{ route('login') }}">Already have an account? Login</a>
        </div>
    </div>

    <script>
        const roleSelect = document.getElementById('roleSelect');
        const studentFields = document.getElementById('studentFields');
        const lecturerFields = document.getElementById('lecturerFields');
        const departmentField = document.getElementById('departmentField');
        const deptRequired = document.getElementById('deptRequired');

        function toggleFields() {
            const roleId = parseInt(roleSelect.value);

            // Hide all first
            studentFields.style.display = 'none';
            lecturerFields.style.display = 'none';

            // Show relevant fields
            if (roleId === 3) { // Student
                studentFields.style.display = 'block';
                departmentField.style.display = 'block';
                deptRequired.textContent = '*';
            } else if (roleId === 2) { // Lecturer
                lecturerFields.style.display = 'block';
                departmentField.style.display = 'block';
                deptRequired.textContent = '*';
            } else {
                departmentField.style.display = 'block';
                deptRequired.textContent = '';
            }
        }

        roleSelect.addEventListener('change', toggleFields);
        toggleFields();
    </script>
</body>

</html>
