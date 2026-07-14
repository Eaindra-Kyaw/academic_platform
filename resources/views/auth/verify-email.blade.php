<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - MTU Academic System</title>
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
            max-width: 500px;
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

        .message {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 25px;
            line-height: 1.6;
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

        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #10b981;
            font-size: 14px;
        }

        .alert-success i {
            margin-right: 6px;
        }

        .email-display {
            background: #f3f4f6;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin: 15px 0;
            font-weight: 600;
            color: #800000;
        }

        .logout-link {
            text-align: center;
            margin-top: 20px;
        }

        .logout-link a {
            color: #800000;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-link a:hover {
            text-decoration: underline;
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

        <h1>Verify Your Email</h1>

        @if (session('status'))
            <div class="alert-success">
                <i class="bi bi-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        <div class="email-display">
            📧 {{ Auth::user()->email }}
        </div>

        <div class="message">
            <p>Thanks for signing up! Before getting started, could you verify your email address by clicking on the
                link we just emailed to you?</p>
            <br>
            <p style="font-size: 13px; color: #9ca3af;">If you didn't receive the email, we will gladly send you
                another.</p>
        </div>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn">
                <i class="bi bi-envelope"></i> Resend Verification Email
            </button>
        </form>

        <div class="logout-link">
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 15px;">
                @csrf
                <button type="submit"
                    style="background: none; border: none; color: #800000; cursor: pointer; font-size: 14px;">
                    <i class="bi bi-box-arrow-right"></i> Log Out
                </button>
            </form>
        </div>
    </div>
</body>

</html>
