{{-- resources/views/emails/admin-new-user-approval.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>New User Pending Approval</title>
</head>

<body>
    <h2>🔔 New User Registration Pending Approval</h2>

    <p>A new user has registered and is waiting for your approval.</p>

    <div style="background: #f8fafc; padding: 16px; border-radius: 8px;">
        <p><strong>👤 Name:</strong> {{ $user->name }}</p>
        <p><strong>📧 Email:</strong> {{ $user->email }}</p>
        <p><strong>🎓 Student ID:</strong> {{ $user->student_id ?? 'N/A' }}</p>
        <p><strong>🏛️ Department:</strong> {{ $user->department->name ?? 'N/A' }}</p>
        <p><strong>📚 Role:</strong> {{ $user->role->name ?? 'N/A' }}</p>
        <p><strong>📅 Year:</strong> {{ $user->current_year ?? 'N/A' }}</p>
        <p><strong>📅 Registered:</strong> {{ $user->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <div style="display: flex; gap: 10px; margin-top: 20px;">
        <a href="{{ route('admin.pending-users') }}"
            style="background: #0A2463; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
            📋 View Pending Users
        </a>
    </div>

    <p style="color: #6b7280; font-size: 12px; margin-top: 20px;">
        This is an automated notification from MTU Academic Portal.
    </p>
</body>

</html>
