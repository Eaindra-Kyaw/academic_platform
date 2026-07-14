<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance Result</title>
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
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
            padding: 32px 24px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .icon-container {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 48px;
            font-weight: bold;
            color: var(--white);
        }

        .icon-success {
            background: var(--success);
        }

        .icon-success::before {
            content: "✓";
        }

        .icon-error {
            background: var(--danger);
        }

        .icon-error::before {
            content: "✗";
        }

        .icon-warning {
            background: var(--warning);
        }

        .icon-warning::before {
            content: "⚠";
        }

        .icon-info {
            background: var(--info);
        }

        .icon-info::before {
            content: "ℹ";
        }

        h2 {
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .message {
            font-size: 16px;
            color: var(--text-gray);
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .course-name {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 4px;
            font-size: 18px;
        }

        .time {
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 16px;
        }

        .status-present {
            background: #d1fae5;
            color: #166534;
        }

        .status-late {
            background: #fef3c7;
            color: #92400e;
        }

        .status-absent {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 16px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            text-decoration: none;
            display: block;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="card">
        @if (isset($success) && $success)
            <div class="icon-container icon-success"></div>
            <h2>Attendance Recorded! 🎉</h2>
            <div class="message">{{ $message ?? 'Attendance marked successfully.' }}</div>
            <div class="course-name">📚 {{ $course_name ?? 'N/A' }}</div>
            <div class="time">🕐 {{ $scanned_at ?? now()->format('h:i A') }}</div>

            @if (isset($status))
                <div class="status-badge status-{{ $status }}">
                    {{ strtoupper($status) }}
                </div>
            @endif
        @else
            <div class="icon-container icon-error"></div>
            <h2>Attendance Failed</h2>
            <div class="message">{{ $message ?? 'Something went wrong. Please try again.' }}</div>
        @endif

        <div class="btn-group">
            <a href="{{ route('student.dashboard') }}" class="btn btn-primary">📊 Go to Dashboard</a>
            <a href="{{ route('student.scan') }}" class="btn btn-secondary">📷 Scan Again</a>
        </div>
    </div>
</body>

</html>
