<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome to MTU Academic System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f0e8;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #800000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .logo {
            background: #800000;
            color: #FFD700;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: bold;
        }

        .btn {
            background: #800000;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 8px;
            display: inline-block;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">MTU Academic Intelligence System</div>
            <h2 style="color: #800000;">Welcome, {{ $user->name }}!</h2>
        </div>

        <p>Your account has been created in the Mandalay Technological University Academic Intelligence System.</p>

        <p><strong>Account Details:</strong></p>
        <ul>
            <li><strong>Name:</strong> {{ $user->name }}</li>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Role:</strong> {{ $user->role->name ?? 'User' }}</li>
        </ul>

        <p>Please click the button below to set your password and activate your account:</p>

        <div style="text-align: center;">
            <a href="{{ $setupUrl }}" class="btn">Set Your Password</a>
        </div>

        <p>This link will expire in 48 hours. If you did not request this account, please ignore this email.</p>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Mandalay Technological University. All rights reserved.</p>
            <p>Mandalay Technological University | Academic Intelligence System</p>
        </div>
    </div>
</body>

</html>
