<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Attendance Result</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #800000 0%, #5f0000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: white;
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
            color: white;
        }

        .icon-success {
            background: #10b981;
        }

        .icon-success::before {
            content: "✓";
        }

        .icon-error {
            background: #ef4444;
        }

        .icon-error::before {
            content: "✗";
        }

        .icon-warning {
            background: #f59e0b;
        }

        .icon-warning::before {
            content: "⚠";
        }

        .icon-info {
            background: #3b82f6;
        }

        .icon-info::before {
            content: "ℹ";
        }

        h2 {
            font-size: 24px;
            margin-bottom: 8px;
            color: #1f2937;
        }

        .message {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .course-name {
            font-weight: 600;
            color: #800000;
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
            background: #dcfce7;
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
            transition: all 0.2s;
            text-decoration: none;
            display: block;
        }

        .btn-primary {
            background: #800000;
            color: white;
        }

        .btn-primary:hover {
            background: #6b0000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
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
