<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - MTU</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f4f6f9;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #0A2463;
            font-size: 24px;
            margin: 0;
        }

        .header .sub {
            color: #64748b;
            font-size: 14px;
            margin: 4px 0 0;
        }

        .content p {
            color: #475569;
            font-size: 15px;
            line-height: 1.7;
        }

        .content .highlight {
            color: #0A2463;
            font-weight: 600;
        }

        .info-box {
            background: #f8fafc;
            border-left: 4px solid #10b981;
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .info-box p {
            margin: 6px 0;
            font-size: 14px;
        }

        .info-box strong {
            color: #0A2463;
        }

        .btn {
            display: inline-block;
            background: #0A2463;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px 0;
        }

        .btn:hover {
            background: #1E3A8A;
        }

        .btn-success {
            background: #10b981;
        }

        .btn-success:hover {
            background: #059669;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }

        .footer .gold {
            color: #D4A017;
        }

        .steps {
            background: #f0f9ff;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 16px 0;
            border-left: 4px solid #0A2463;
        }

        .steps ol {
            margin: 8px 0 0 20px;
            color: #1e293b;
            font-size: 14px;
        }

        .steps ol li {
            margin-bottom: 4px;
        }

        .login-link {
            background: #f0fdf4;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 12px 0;
            border: 1px solid #bbf7d0;
            text-align: center;
            font-size: 14px;
            color: #1e293b;
        }

        .login-link strong {
            color: #0A2463;
        }

        .badge-approved {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 2px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>✅ Account Approved!</h1>
            <p class="sub">Mandalay Technological University</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>
            <p>
                <span class="badge-approved">✅ Approved</span>
                🎉 <span class="highlight">Congratulations!</span> Your account on the <strong>MTU Academic
                    Portal</strong> has been officially approved by the administrator.
            </p>

            <div class="info-box">
                <p><strong>👤 Name:</strong> {{ $user->name }}</p>
                <p><strong>📧 Email:</strong> {{ $user->email }}</p>
                <p><strong>🎭 Role:</strong> {{ $user->role->name ?? 'N/A' }}</p>
                @if ($user->student_id)
                    <p><strong>🆔 Student ID:</strong> {{ $user->student_id }}</p>
                @endif
            </div>

            <p><strong>🔐 You can now log in using the password you created during registration.</strong></p>

            <div class="steps">
                <strong>📝 Instructions:</strong>
                <ol>
                    <li>Go to the login page: <strong>{{ url('/login') }}</strong></li>
                    <li>Select your role (Admin, Teacher, or Student)</li>
                    <li>Enter your email and the password you created during registration</li>
                    <li>Click Login to access your dashboard</li>
                </ol>
            </div>

            <div class="login-link">
                🔗 <strong>Login URL:</strong> <a href="{{ url('/login') }}"
                    style="color: #0A2463; text-decoration: underline;">{{ url('/login') }}</a>
            </div>

            <p style="text-align: center; margin-top: 16px;">
                <a href="{{ url('/login') }}" class="btn btn-success">
                    🔑 Go to Login
                </a>
            </p>

            <p style="font-size: 13px; color: #64748b; text-align: center; margin-top: 8px;">
                If you forgot your password, click "Forgot Password" on the login page to reset it.
            </p>
        </div>

        <div class="footer">
            <p>Mandalay Technological University · <span class="gold">Ministry of Science and Technology</span></p>
            <p style="margin-top: 4px;">This is an automated notification from the MTU Academic Portal.</p>
        </div>
    </div>
</body>

</html>
