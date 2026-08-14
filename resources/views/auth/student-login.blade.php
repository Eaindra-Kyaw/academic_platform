<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - MTU</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #3B82F6;
            --accent: #D4A017;
            --bg-light: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
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
            max-width: 450px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        /* ===== MTU LOGO IMAGE ===== */
        .logo-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            overflow: hidden;
            margin: 0 auto 15px;
            /* border: 2px solid var(--accent); */
            box-shadow: 0 4px 16px rgba(13, 71, 161, 0.2);
            background: var(--white);
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Fallback if image doesn't load */
        .logo-icon .fallback {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 22px;
            color: var(--accent);
        }

        .logo h2 {
            color: var(--primary);
            font-size: 20px;
            margin-top: 10px;
            font-weight: 700;
        }

        .logo p {
            color: var(--text-gray);
            font-size: 12px;
            margin-top: 5px;
        }

        .logo p .gold {
            color: var(--accent);
            font-weight: 600;
        }

        .role-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 10px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .role-badge i {
            color: var(--accent);
            margin-right: 6px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(13, 71, 161, 0.08);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(13, 71, 161, 0.3);
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid #dc2626;
        }

        .alert-danger p {
            margin: 0;
        }

        .alert-danger i {
            margin-right: 6px;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .back-link a {
            color: var(--primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .back-link a:hover {
            color: var(--accent);
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="logo">
            <!-- ===== MTU LOGO IMAGE ===== -->
            <div class="logo-icon">
                <img src="{{ asset('images/mtu-logo.png') }}" alt="MTU">
                <!-- <div class="fallback">MTU</div> -->
            </div>
            <h2>Mandalay Technological University</h2>
            <p><span class="gold">✦</span> Ministry of Science and Technology <span class="gold">✦</span></p>
        </div>

        <div class="role-badge">
            <i class="bi bi-mortarboard-fill"></i> Student Login
        </div>

        @if ($errors->any())
            <div class="alert-danger">
                @foreach ($errors->all() as $error)
                    <p><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('student.login.submit') }}">
            @csrf
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" value="{{ old('email') }}"
                    required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Login as Student</button>
        </form>

        <div class="back-link">
            <a href="{{ route('register') }}">
                <i class="bi bi-person-plus"></i> Need an account? Register
            </a>
            <a href="{{ route('home') }}">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>
</body>

</html>
