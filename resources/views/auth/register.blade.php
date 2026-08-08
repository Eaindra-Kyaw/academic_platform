<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MTU Academic Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: var(--white);
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
            background: var(--primary);
            border-radius: 10px;
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: var(--accent);
            font-size: 24px;
            font-weight: bold;
        }

        .logo h2 {
            color: var(--primary);
            font-size: 20px;
        }

        .logo p {
            color: var(--text-gray);
            font-size: 12px;
            margin-top: 5px;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            color: var(--text-gray);
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
            color: var(--text-dark);
            font-size: 13px;
        }

        label .required {
            color: var(--danger);
        }

        input,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            background: var(--white);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
        }

        .error {
            color: var(--danger);
            font-size: 12px;
            margin-top: 4px;
        }

        .help-text {
            font-size: 11px;
            color: var(--text-gray);
            margin-top: 4px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid var(--danger);
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
            <div class="logo-icon">MTU</div>
            <h2>Mandalay Technological University</h2>
            <p>Create your account</p>
        </div>

        <h1>Register</h1>
        <p class="subtitle">Create your account to access the portal</p>

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
                <div class="help-text" style="color: var(--primary); font-weight: 500;">
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

            studentFields.style.display = 'none';
            lecturerFields.style.display = 'none';

            if (roleId === 3) {
                studentFields.style.display = 'block';
                departmentField.style.display = 'block';
                deptRequired.textContent = '*';
            } else if (roleId === 2) {
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
