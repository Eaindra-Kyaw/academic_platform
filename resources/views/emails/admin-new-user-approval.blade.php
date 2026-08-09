<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration - MTU</title>
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
            border-bottom: 3px solid #D4A017;
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

        .details-box {
            background: #f8fafc;
            border-left: 4px solid #0A2463;
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
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
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
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

        .footer .gold {
            color: #D4A017;
        }

        .urgent-badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
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
            <h1>🏛️ MTU Academic Portal</h1>
            <p class="sub">Mandalay Technological University</p>
        </div>

        <div class="content">
            <p>Hello <strong>Administrator</strong>,</p>
            <p>
                <span class="urgent-badge">⏳ Pending Approval</span>
                A new user has just registered on the MTU Academic Portal and is awaiting your approval.
            </p>

            <div class="details-box">
                <p><strong>👤 Name:</strong> {{ $user->name }}</p>
                <p><strong>📧 Email:</strong> {{ $user->email }}</p>
                <p><strong>🎭 Role:</strong> {{ $user->role->name ?? 'N/A' }}</p>
                <p><strong>🏛️ Department:</strong> {{ $user->department->name ?? 'N/A' }}</p>
                @if ($user->student_id)
                    <p><strong>🆔 Student ID:</strong> {{ $user->student_id }}</p>
                @endif
                @if ($user->current_year)
                    <p><strong>📚 Year:</strong>
                        {{ $user->current_year }}{{ $user->current_year <= 3 ? ['th', 'st', 'nd', 'rd'][$user->current_year] : 'th' }}
                        Year</p>
                @endif
                <p><strong>📅 Registered:</strong>
                    {{ \Carbon\Carbon::parse($user->registered_at)->format('F d, Y g:i A') }}</p>
            </div>

            <p style="text-align: center;">
                <a href="{{ url('/admin/users/pending') }}" class="btn">
                    👀 View Pending Approvals
                </a>
            </p>

            <p style="font-size: 13px; color: #64748b; text-align: center; margin-top: 16px;">
                Or copy this link into your browser:<br>
                <span style="word-break: break-all; font-size: 12px;">{{ url('/admin/users/pending') }}</span>
            </p>
        </div>

        <div class="footer">
            <p>Mandalay Technological University · <span class="gold">Ministry of Science and Technology</span></p>
            <p style="margin-top: 4px;">This is an automated notification from the MTU Academic Portal.</p>
        </div>
    </div>
</body>

</html>
