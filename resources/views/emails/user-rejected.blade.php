{{-- resources/views/emails/user-rejected.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <title>Your Registration Status</title>
</head>

<body>
    <h2>❌ Registration Update</h2>

    <p>Dear <strong>{{ $user->name }}</strong>,</p>

    <p>Your registration request for the MTU Academic Portal has been <strong style="color: #dc2626;">rejected</strong>.
    </p>

    <div style="background: #fef2f2; padding: 16px; border-radius: 8px; border-left: 4px solid #dc2626;">
        <p><strong>📝 Reason:</strong></p>
        <p style="color: #1e293b;">{{ $reason ?? 'Please contact the administrator for more information.' }}</p>
    </div>

    <p>If you believe this is a mistake, please contact:</p>
    <p><strong>MTU CEIT Department</strong></p>

    <p style="color: #6b7280; font-size: 12px; margin-top: 20px;">
        This is an automated notification from MTU Academic Portal.
    </p>
</body>

</html>
