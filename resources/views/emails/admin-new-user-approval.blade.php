<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
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
            border-bottom: 2px solid #F9A825;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #0A2463;
            font-size: 22px;
            margin: 0;
        }

        .content p {
            color: #475569;
            font-size: 15px;
            line-height: 1.6;
        }

        .details-box {
            background: #f8fafc;
            border-left: 4px solid #0A2463;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
        }

        .details-box p {
            margin: 6px 0;
            font-size: 14px;
        }

        .details-box strong {
            color: #0A2463;
        }

        .btn {
            display: inline-block;
            background: #0A2463;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
        }

        .btn:hover {
            background: #1E3A8A;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🛡️ MTU Academic Portal</h1>
        </div>

        <div class="content">
            <p>Hello <strong>Administrator</strong>,</p>
            <p>A new user has just registered on the MTU Academic Portal and is awaiting your approval.</p>

            <div class="details-box">
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> {{ $user->role->name ?? 'N/A' }}</p>
                <p><strong>Department:</strong> {{ $user->department->name ?? 'N/A' }}</p>
                @if ($user->student_id)
                    <p><strong>Student ID:</strong> {{ $user->student_id }}</p>
                @endif
                <p><strong>Registration Date:</strong>
                    {{ \Carbon\Carbon::parse($user->registered_at)->format('F d, Y g:i A') }}</p>
            </div>

            <p>To review and approve this account, please click the button below:</p>

            <div style="text-align: center;">
                <a href="{{ url('/admin/users/pending') }}" class="btn">
                    <i class="bi bi-check-circle"></i> Go to Pending Approvals
                </a>
            </div>
        </div>

        <div class="footer">
            <p>Mandalay Technological University · Ministry of Science and Technology</p>
        </div>
    </div>
</body>

</html>
