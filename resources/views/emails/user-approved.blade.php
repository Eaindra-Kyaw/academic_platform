{{-- resources/views/emails/user-approved.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>Your Account Has Been Approved</title>
</head>

<body>
    <h2>✅ Your MTU Academic Portal Account Has Been Approved!</h2>

    <p>Dear <strong>{{ $user->name }}</strong>,</p>

    <p>Your registration has been approved by the administrator.</p>

    <div style="background: #f0fdf4; padding: 16px; border-radius: 8px; border-left: 4px solid #10b981;">
        <p><strong>📧 Email:</strong> {{ $user->email }}</p>
        <p><strong>🎓 Student ID:</strong> {{ $user->student_id ?? 'N/A' }}</p>
        <p><strong>🏛️ Department:</strong> {{ $user->department->name ?? 'N/A' }}</p>
        <p><strong>📚 Role:</strong> {{ $user->role->name ?? 'N/A' }}</p>
    </div>

    <div style="margin: 20px 0;">
        <a href="{{ config('app.url') }}/login"
            style="background: #0A2463; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none;">
            🔑 Login to Your Account
        </a>
    </div>

    <p style="color: #6b7280; font-size: 13px;">
        If you haven't set your password yet, click "Forgot Password" on the login page.
    </p>
</body>

</html>
