@extends('layouts.app')

@section('title', 'Profile Settings')
@section('page-title', '👤 Profile Settings')
@section('welcome-text', 'Manage your account settings')

@section('sidebar')
    @if (Auth::user()->role_id == 1)
        @include('layouts.partials.admin-sidebar')
    @elseif(Auth::user()->role_id == 2)
        @include('layouts.partials.lecturer-sidebar')
    @else
        @include('layouts.partials.student-sidebar')
    @endif
@endsection

@section('content')
    <style>
        .profile-container {
            max-width: 700px;
            margin: 0 auto;
        }

        .profile-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            padding: 30px;
            margin-bottom: 20px;
        }

        .profile-card h4 {
            color: #800000;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f3f4f6;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .form-group input:disabled {
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-save {
            background: #800000;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-save:hover {
            background: #5f0000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #dc2626;
        }

        .help-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .required {
            color: #dc2626;
        }

        .password-rules {
            background: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .password-rules ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-rules ul li {
            padding: 4px 0;
            font-size: 13px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .password-rules ul li i {
            font-size: 16px;
        }

        .password-rules ul li.valid {
            color: #10b981;
        }

        .password-rules ul li.invalid {
            color: #ef4444;
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            border-radius: 4px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .password-strength .bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .password-strength .bar.weak {
            width: 25%;
            background: #ef4444;
        }

        .password-strength .bar.medium {
            width: 50%;
            background: #f59e0b;
        }

        .password-strength .bar.strong {
            width: 75%;
            background: #3b82f6;
        }

        .password-strength .bar.very-strong {
            width: 100%;
            background: #10b981;
        }
    </style>

    <div class="profile-container">
        @if (session('success'))
            <div class="alert-success">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert-error">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                @foreach ($errors->all() as $error)
                    <p style="margin: 2px 0;"><i class="bi bi-exclamation-triangle"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Profile Information -->
        <div class="profile-card">
            <h4><i class="bi bi-person-circle" style="color: #800000;"></i> Profile Information</h4>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="{{ Auth::user()->email }}" disabled>
                    <div class="help-text">Email cannot be changed. Contact admin for email updates.</div>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="{{ Auth::user()->role->name ?? 'N/A' }}" disabled>
                </div>

                <div class="form-group">
                    <label>Department</label>
                    <input type="text" value="{{ Auth::user()->department->name ?? 'N/A' }}" disabled>
                </div>

                @if (Auth::user()->isStudent())
                    <div class="form-group">
                        <label>Student ID</label>
                        <input type="text" value="{{ Auth::user()->student_id ?? 'N/A' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>Current Year</label>
                        <input type="text" value="{{ Auth::user()->year_label ?? 'N/A' }}" disabled>
                    </div>
                @endif

                @if (Auth::user()->isLecturer() && Auth::user()->specialization)
                    <div class="form-group">
                        <label>Specialization</label>
                        <input type="text" value="{{ Auth::user()->specialization }}" disabled>
                    </div>
                @endif

                <button type="submit" class="btn-save">
                    <i class="bi bi-save"></i> Update Profile
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="profile-card">
            <h4><i class="bi bi-key" style="color: #800000;"></i> Change Password</h4>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Current Password <span class="required">*</span></label>
                    <input type="password" name="current_password" placeholder="Enter your current password" required>
                </div>

                <div class="form-group">
                    <label>New Password <span class="required">*</span></label>
                    <input type="password" name="password" id="newPassword" placeholder="Enter new password" required>
                    <div class="password-strength">
                        <div class="bar" id="strengthBar"></div>
                    </div>
                    <div class="help-text">Minimum 8 characters with letters, numbers, and symbols</div>
                </div>

                <div class="form-group">
                    <label>Confirm New Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Confirm new password" required>
                </div>

                <!-- Password Rules -->
                <div class="password-rules">
                    <ul>
                        <li id="rule-length"><i class="bi bi-dash-circle"></i> At least 8 characters</li>
                        <li id="rule-lowercase"><i class="bi bi-dash-circle"></i> At least 1 lowercase letter</li>
                        <li id="rule-uppercase"><i class="bi bi-dash-circle"></i> At least 1 uppercase letter</li>
                        <li id="rule-number"><i class="bi bi-dash-circle"></i> At least 1 number</li>
                        <li id="rule-special"><i class="bi bi-dash-circle"></i> At least 1 special character (!@#$%^&*)</li>
                    </ul>
                </div>

                <button type="submit" class="btn-save" style="margin-top: 15px;">
                    <i class="bi bi-key"></i> Change Password
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('newPassword').addEventListener('input', function() {
            const password = this.value;
            const bar = document.getElementById('strengthBar');

            // Check rules
            const rules = {
                length: password.length >= 8,
                lowercase: /[a-z]/.test(password),
                uppercase: /[A-Z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^a-zA-Z0-9]/.test(password)
            };

            // Update rule icons
            document.getElementById('rule-length').className = rules.length ? 'valid' : 'invalid';
            document.getElementById('rule-lowercase').className = rules.lowercase ? 'valid' : 'invalid';
            document.getElementById('rule-uppercase').className = rules.uppercase ? 'valid' : 'invalid';
            document.getElementById('rule-number').className = rules.number ? 'valid' : 'invalid';
            document.getElementById('rule-special').className = rules.special ? 'valid' : 'invalid';

            // Update icons
            document.querySelectorAll('.password-rules ul li').forEach(li => {
                const icon = li.querySelector('i');
                if (li.className === 'valid') {
                    icon.className = 'bi bi-check-circle-fill';
                } else {
                    icon.className = 'bi bi-dash-circle';
                }
            });

            // Calculate strength
            let strength = 0;
            if (rules.length) strength++;
            if (rules.lowercase) strength++;
            if (rules.uppercase) strength++;
            if (rules.number) strength++;
            if (rules.special) strength++;

            if (password.length === 0) {
                bar.className = 'bar';
                return;
            }

            if (strength <= 2) bar.className = 'bar weak';
            else if (strength === 3) bar.className = 'bar medium';
            else if (strength === 4) bar.className = 'bar strong';
            else bar.className = 'bar very-strong';
        });
    </script>
@endsection
