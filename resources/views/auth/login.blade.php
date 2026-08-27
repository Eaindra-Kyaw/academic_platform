<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTU Academic Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #E3F2FD;
            min-height: 100vh;
            padding: 40px 20px 0;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .header {
            text-align: center;
            padding: 20px 0 16px;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 30px;
            font-weight: 900;
            color: #1565C0;
            margin: 8px 0 2px;
        }

        .header h2 .gold {
            color: #F9A825;
        }

        .header .tagline {
            font-size: 15px;
            color: #F9A825;
            font-weight: 500;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin: 30px 0 24px;
        }

        .role-card {
            background: white;
            border-radius: 24px;
            padding: 40px 32px 32px;
            box-shadow: 0 4px 20px rgba(13, 71, 161, 0.08);
            border: 1px solid rgba(13, 71, 161, 0.06);
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
            display: flex;
            flex-direction: column;
        }

        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(13, 71, 161, 0.15);
            border-color: #42A5F5;
        }

        .role-icon {
            width: 90px;
            height: 90px;
            background: #E3F2FD;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 40px;
            color: #0D47A1;
            transition: all 0.3s ease;
        }

        .role-card:hover .role-icon {
            background: #F9A825;
            color: white;
            transform: scale(1.05);
        }

        .role-card h3 {
            font-size: 26px;
            font-weight: 800;
            color: #0D47A1;
            margin-bottom: 8px;
        }

        .role-card .role-sub {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 18px;
            font-weight: 500;
        }

        .role-features {
            list-style: none;
            text-align: left;
            margin: 14px 0 20px;
            flex: 1;
            padding: 0 4px;
        }

        .role-features li {
            font-size: 14px;
            color: #64748b;
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .role-features li i {
            color: #F9A825;
            font-size: 16px;
            width: 22px;
        }

        .btn-role {
            background: #1565C0;
            border: none;
            padding: 15px 28px;
            border-radius: 50px;
            color: white;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-role:hover {
            background: #0D47A1;
            transform: scale(1.02);
        }

        .btn-role:hover i {
            transform: translateX(4px);
        }

        .footer {
            text-align: center;
            padding: 20px 0 10px;
            border-top: 1px solid rgba(13, 71, 161, 0.06);
            margin-top: auto;
        }

        .footer .note {
            font-size: 13px;
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 6px;
        }

        .footer .copyright {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
            opacity: 0.6;
        }

        .footer .copyright .gold {
            color: #F9A825;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .role-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .role-card {
                padding: 32px 24px 26px;
            }
        }

        @media (max-width: 480px) {
            .header h2 {
                font-size: 20px;
            }

            .role-icon {
                width: 68px;
                height: 68px;
                font-size: 30px;
            }

            .role-card h3 {
                font-size: 19px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <h2>Intelligent <span class="gold">University</span> Portal</h2>
            <p class="tagline"><i class="bi bi-qr-code"></i> QR Attendance · Predictive Analytics</p>
        </header>

        <div class="role-grid">
            <div class="role-card" onclick="window.location.href='/admin/login'">
                <div class="role-icon"><i class="bi bi-shield-lock-fill"></i></div>
                <h3>Admin</h3>
                <p class="role-sub">University-wide control &amp; analytics</p>
                <ul class="role-features">
                    <li><i class="bi bi-check-circle-fill"></i> Academic Performance</li>
                    <li><i class="bi bi-check-circle-fill"></i> Risk Forecasting</li>
                    <li><i class="bi bi-check-circle-fill"></i> Department Intelligence</li>
                </ul>
                <button class="btn-role">Access Admin <i class="bi bi-arrow-right"></i></button>
            </div>

            <div class="role-card" onclick="window.location.href='/lecturer/login'">
                <div class="role-icon"><i class="bi bi-person-badge"></i></div>
                <h3>Teacher</h3>
                <p class="role-sub">Manage classes &amp; monitor engagement</p>
                <ul class="role-features">
                    <li><i class="bi bi-check-circle-fill"></i> Generate QR Codes</li>
                    <li><i class="bi bi-check-circle-fill"></i> Risk Analytics</li>
                    <li><i class="bi bi-check-circle-fill"></i> Student Intervention</li>
                </ul>
                <button class="btn-role">Access Teacher <i class="bi bi-arrow-right"></i></button>
            </div>

            <div class="role-card" onclick="window.location.href='/student/login'">
                <div class="role-icon"><i class="bi bi-mortarboard-fill"></i></div>
                <h3>Student</h3>
                <p class="role-sub">Track your academic journey</p>
                <ul class="role-features">
                    <li><i class="bi bi-check-circle-fill"></i> QR Attendance</li>
                    <li><i class="bi bi-check-circle-fill"></i> Recommendations</li>
                    <li><i class="bi bi-check-circle-fill"></i> Uni Bot</li>
                </ul>
                <button class="btn-role">Access Student <i class="bi bi-arrow-right"></i></button>
            </div>
        </div>

        <footer class="footer">
            <p class="note"><i class="bi bi-info-circle"></i> Existing user? Please select your role above to login.
            </p>
            <p class="copyright">© 2026 <span class="gold">Ministry of Science and Technology</span> · Mandalay
                Technological University</p>
        </footer>
    </div>
</body>

</html>
