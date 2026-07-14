<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Your Password - MTU Academic System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
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
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 25px;
        }

        .input-group {
            margin-bottom: 20px;
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

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s;
        }

        input:focus {
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

        .error {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
        }

        .help-text {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            border-radius: 4px;
            background: #e5e7eb;
            overflow: hidden;
            transition: all 0.3s;
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

        .strength-text {
            font-size: 11px;
            margin-top: 4px;
            color: #6b7280;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #10b981;
        }

        .alert-success i {
            margin-right: 6px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="logo">
            <div class="logo-icon">Uni</div>
            <h2>Academic Portal</h2>
            <p>Mandalay Technological University</p>
        </div>

        <h1>Set Your Password</h1>
        <p class="subtitle">Create a secure password for your account</p>

        @if (session('status'))
            <div class="alert-success">
                <i class="bi bi-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:10px; margin-bottom:20px; border-left:4px solid #dc2626;">
                @foreach ($errors->all() as $error)
                    <p style="margin:2px 0; font-size:13px;"><i class="bi bi-exclamation-triangle-fill"></i>
                        {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.setup') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="input-group">
                <label>Email Address</label>
                <input type="text" value="{{ $email }}" disabled style="background: #f3f4f6; color: #6b7280;">
            </div>

            <div class="input-group">
                <label>New Password <span class="required">*</span></label>
                <input type="password" name="password" id="password" placeholder="Create a strong password" required>
                <div class="password-strength">
                    <div class="bar" id="strengthBar"></div>
                </div>
                <div class="strength-text" id="strengthText">Enter a password to check strength</div>
                <div class="help-text">Minimum 8 characters with letters, numbers, and symbols</div>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group">
                <label>Confirm Password <span class="required">*</span></label>
                <input type="password" name="password_confirmation" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="btn">
                <i class="bi bi-check-circle"></i> Set Password & Activate Account
            </button>
        </form>
    </div>

    <script>
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');

            let strength = 0;
            let level = '';

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;

            if (password.length === 0) {
                bar.className = 'bar';
                text.textContent = 'Enter a password to check strength';
                text.style.color = '#6b7280';
                return;
            }

            if (strength <= 2) {
                bar.className = 'bar weak';
                level = 'Weak';
                text.style.color = '#ef4444';
            } else if (strength === 3) {
                bar.className = 'bar medium';
                level = 'Medium';
                text.style.color = '#f59e0b';
            } else if (strength === 4) {
                bar.className = 'bar strong';
                level = 'Strong';
                text.style.color = '#3b82f6';
            } else {
                bar.className = 'bar very-strong';
                level = 'Very Strong';
                text.style.color = '#10b981';
            }

            text.textContent = 'Password Strength: ' + level;
        });
    </script>
</body>

</html>
