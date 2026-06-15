<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Attendance Result</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .success-icon::before {
            content: "✓";
            font-size: 48px;
            color: white;
            font-weight: bold;
        }

        .error-icon {
            width: 80px;
            height: 80px;
            background: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .error-icon::before {
            content: "✗";
            font-size: 48px;
            color: white;
            font-weight: bold;
        }

        .warning-icon {
            width: 80px;
            height: 80px;
            background: #f59e0b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .warning-icon::before {
            content: "⚠";
            font-size: 48px;
            color: white;
            font-weight: bold;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 12px;
            color: #1f2937;
        }

        .message {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 16px;
            line-height: 1.5;
        }

        .course-name {
            font-weight: 600;
            color: #800000;
            margin-bottom: 8px;
        }

        .time {
            font-size: 14px;
            color: #9ca3af;
            margin-bottom: 24px;
        }

        .btn {
            background: #800000;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 12px;
        }

        .btn-secondary {
            background: #6b7280;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>
    <div class="card">
        @if ($success)
            <div class="success-icon"></div>
            <h2>Attendance Recorded!</h2>
            <div class="message">{{ $message }}</div>
            <div class="course-name">Course: {{ $course_name ?? 'N/A' }}</div>
            <div class="time">Time: {{ $scanned_at ?? now()->format('h:i A') }}</div>
        @else
            <div class="error-icon"></div>
            <h2>Attendance Failed</h2>
            <div class="message">{{ $message }}</div>
        @endif

        <button class="btn" onclick="window.location.href='/student/dashboard'">Go to Dashboard</button>
        <button class="btn btn-secondary" onclick="window.location.href='/student/scan'">Scan Again</button>
    </div>
</body>

</html>
