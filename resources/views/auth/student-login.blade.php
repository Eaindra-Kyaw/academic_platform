<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - MTU</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0B2B5B 0%, #0D47A1 100%);
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
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h2 {
            color: #0A2463;
            font-size: 22px;
            font-weight: 700;
        }

        .logo p {
            color: #6B7280;
            font-size: 14px;
            margin-top: 4px;
        }

        .role-badge {
            background: #D1FAE5;
            color: #065F46;
            padding: 10px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            color: #1F2937;
            font-size: 14px;
            margin-bottom: 6px;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 15px;
            box-sizing: border-box;
        }

        input:focus {
            outline: none;
            border-color: #0A2463;
            box-shadow: 0 0 0 4px rgba(10, 36, 99, 0.08);
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: #0A2463;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn:hover {
            background: #1E3A8A;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.3);
        }

        .alert-danger {
            background: #FEE2E2;
            color: #991B1B;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #DC2626;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
        }

        .back-link a {
            color: #0A2463;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .test-users {
            margin-top: 16px;
            padding: 12px 16px;
            background: #F3F4F6;
            border-radius: 12px;
            font-size: 12px;
            color: #6B7280;
            text-align: center;
        }

        .test-users strong {
            color: #1F2937;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="logo">
            <h2>🏛️ MTU Academic System</h2>
            <p>Mandalay Technological University</p>
        </div>
        <div class="role-badge"><i class="bi bi-mortarboard-fill"></i> Student Login</div>
        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <div>❌ {{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('student.login.submit') }}">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}"
                    required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Login as Student</button>
        </form>
        <div class="test-users"><strong>Test Account:</strong><br>eaindrakyaw@mtu.edu.mm / student123</div>
        <div class="back-link"><a href="{{ route('home') }}">← Back to Home</a></div>
    </div>
</body>

</html>
