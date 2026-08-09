<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Update - MTU</title>
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
            border-bottom: 3px solid #dc2626;
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

        .reason-box {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 16px 20px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .reason-box p {
            margin: 0;
            color: #1e293b;
            font-size: 14px;
        }

        .reason-box .label {
            font-weight: 600;
            color: #0A2463;
            display: block;
            margin-bottom: 4px;
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

        .badge-rejected {
            display: inline-block;
            background: #fee2e2;
            color: #991b1b;
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
            <h1>❌ Registration Update</h1>
            <p class="sub">Mandalay Technological University</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $user->name }}</strong>,</p>

            <p>
                <span class="badge-rejected">❌ Rejected</span>
                We regret to inform you that your registration request for the <strong>MTU Academic Portal</strong> has
                been rejected.
            </p>

            <div class="reason-box">
                <span class="label">📝 Reason for Rejection:</span>
                <p>{{ $reason ?? 'Please contact the administrator for more information.' }}</p>
            </div>

            <p>If you believe this is a mistake or would like to appeal this decision, please contact:</p>
            <p style="font-weight: 600; color: #0A2463;">
                📧 MTU CEIT Department<br>
                <span style="font-weight: 400; font-size: 14px; color: #475569;">ceit@mtu.edu.mm</span>
            </p>

            <p style="text-align: center; margin-top: 20px;">
                <a href="mailto:ceit@mtu.edu.mm" class="btn">
                    📧 Contact Department
                </a>
            </p>
        </div>

        <div class="footer">
            <p>Mandalay Technological University · <span class="gold">Ministry of Science and Technology</span></p>
            <p style="margin-top: 4px;">This is an automated notification from the MTU Academic Portal.</p>
        </div>
    </div>
</body>

</html>
