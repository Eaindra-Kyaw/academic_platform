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
        :root {
            --primary: #0D47A1;
            --primary-dark: #0B2B5B;
            --primary-light: #1565C0;
            --secondary: #42A5F5;
            --accent: #F9A825;
            --bg-main: #E3F2FD;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --shadow: 0 4px 20px rgba(13, 71, 161, 0.08);
            --shadow-hover: 0 8px 30px rgba(13, 71, 161, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
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

        /* ===== HEADER - NO CARD ===== */
        .header {
            text-align: center;
            padding: 20px 0 16px;
            margin-bottom: 30px;
        }

        .header-top {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 4px;
        }

        /* MTU Logo - NO BORDER */
        .mtu-logo {
            flex-shrink: 0;
        }

        .mtu-logo img {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            object-fit: cover;
            display: block;
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.12);
        }

        /* Fallback */
        .mtu-logo .logo-fallback {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 18px;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.12);
        }

        .header-title {
            text-align: left;
        }

        .header-title h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .header-title .ministry {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            display: block;
        }

        .header-title .ministry .gold {
            color: var(--accent);
        }

        .header h2 {
            font-size: 30px;
            font-weight: 900;
            color: var(--primary-light);
            margin: 8px 0 2px;
            letter-spacing: -0.5px;
        }

        .header h2 .gold {
            color: var(--accent);
        }

        .header .tagline {
            font-size: 15px;
            color: var(--accent);
            font-weight: 500;
        }

        .header .tagline .sep {
            color: var(--secondary);
            margin: 0 6px;
        }

        .header .tagline i {
            color: var(--secondary);
        }

        /* ===== ROLE CARDS - EXTRA BIG ===== */
        .role-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            margin: 30px 0 24px;
        }

        .role-card {
            background: var(--white);
            border-radius: 24px;
            padding: 40px 32px 32px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(13, 71, 161, 0.06);
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
            display: flex;
            flex-direction: column;
            min-height: 420px;
        }

        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
            border-color: var(--secondary);
        }

        .role-icon {
            width: 90px;
            height: 90px;
            background: var(--bg-main);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            font-size: 40px;
            color: var(--primary);
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .role-card:hover .role-icon {
            background: var(--accent);
            color: var(--white);
            transform: scale(1.05);
        }

        .role-card h3 {
            font-size: 26px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .role-card .role-sub {
            font-size: 15px;
            color: var(--text-gray);
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
            color: var(--text-gray);
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .role-features li i {
            color: var(--accent);
            font-size: 16px;
            width: 22px;
        }

        .btn-role {
            background: var(--primary-light);
            border: none;
            padding: 15px 28px;
            border-radius: 50px;
            color: var(--white);
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            flex-shrink: 0;
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-role:hover {
            background: var(--primary);
            transform: scale(1.02);
        }

        .btn-role i {
            transition: all 0.3s ease;
        }

        .btn-role:hover i {
            transform: translateX(4px);
        }

        /* ===== FOOTER - REDUCED BOTTOM PADDING (FITS PAGE) ===== */
        .footer {
            text-align: center;
            padding: 20px 0 10px;
            /* ← REDUCED from 80px to 10px */
            border-top: 1px solid rgba(13, 71, 161, 0.06);
            margin-top: auto;
            margin-bottom: 0;
            width: 100%;
        }

        .footer .note {
            font-size: 13px;
            color: var(--text-gray);
            line-height: 1.8;
            margin-bottom: 6px;
        }

        .footer .note .icon {
            color: var(--accent);
        }

        .footer .note .dept {
            color: var(--primary);
            font-weight: 700;
        }

        .footer .copyright {
            font-size: 12px;
            color: var(--text-gray);
            margin-top: 6px;
            opacity: 0.6;
            letter-spacing: 0.3px;
        }

        .footer .copyright .gold {
            color: var(--accent);
            font-weight: 600;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .role-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 20px 16px 0;
            }

            .header {
                padding: 16px 0 12px;
            }

            .header-top {
                gap: 12px;
            }

            .mtu-logo img,
            .mtu-logo .logo-fallback {
                width: 48px;
                height: 48px;
                border-radius: 12px;
            }

            .header-title h1 {
                font-size: 20px;
            }

            .header-title .ministry {
                font-size: 11px;
            }

            .header h2 {
                font-size: 24px;
            }

            .header .tagline {
                font-size: 13px;
            }

            .role-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .role-card {
                padding: 32px 24px 26px;
                min-height: auto;
            }

            .role-icon {
                width: 76px;
                height: 76px;
                font-size: 34px;
                border-radius: 20px;
            }

            .role-card h3 {
                font-size: 22px;
            }

            .role-features li {
                font-size: 13px;
            }

            .btn-role {
                font-size: 15px;
                padding: 13px 24px;
            }

            .footer {
                padding: 16px 0 8px;
            }

            .footer .note {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .header-top {
                flex-direction: column;
                text-align: center;
            }

            .header-title {
                text-align: center;
            }

            .mtu-logo img,
            .mtu-logo .logo-fallback {
                width: 52px;
                height: 52px;
                border-radius: 14px;
            }

            .header-title h1 {
                font-size: 17px;
            }

            .header-title .ministry {
                font-size: 10px;
            }

            .header h2 {
                font-size: 20px;
            }

            .header .tagline {
                font-size: 12px;
            }

            .role-card {
                padding: 28px 18px 22px;
            }

            .role-icon {
                width: 68px;
                height: 68px;
                font-size: 30px;
                border-radius: 18px;
            }

            .role-card h3 {
                font-size: 19px;
            }

            .role-features li {
                font-size: 12px;
                padding: 4px 0;
            }

            .btn-role {
                font-size: 14px;
                padding: 11px 18px;
            }

            .footer {
                padding: 12px 0 6px;
            }

            .footer .note {
                font-size: 11px;
                line-height: 1.6;
            }

            .footer .copyright {
                font-size: 10px;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        <!-- ===== HEADER - NO CARD ===== -->
        <header class="header">

            <div class="header-top">
                <!-- MTU LOGO - NO BORDER -->
                <div class="mtu-logo">
                    <img src="{{ asset('images/mtu-logo.png') }}" alt="MTU">
                    <!-- <div class="logo-fallback">MTU</div> -->
                </div>

                <div class="header-title">
                    <h1>Mandalay Technological University</h1>
                    {{-- <span class="ministry"><span class="gold">✦</span> Ministry of Science and Technology <span
                            class="gold">✦</span></span> --}}
                </div>
            </div>

            <h2>Intelligent <span>University</span> Portal</h2>
            <p class="tagline"><i class="bi bi-qr-code"></i> QR Attendance <span class="sep">·</span> Predictive
                Analytics</p>

        </header>

        <!-- ===== ROLE CARDS - EXTRA BIG ===== -->
        <div class="role-grid">

            <!-- Admin -->
            <div class="role-card" onclick="goToLogin('admin')">
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

            <!-- Lecturer -->
            <div class="role-card" onclick="goToLogin('lecturer')">
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

            <!-- Student -->
            <div class="role-card" onclick="goToLogin('student')">
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

        <!-- ===== FOOTER - FITS PAGE ===== -->
        <footer class="footer">
            <p class="note">
                <i class="bi bi-info-circle icon"></i> Accounts are created by admins only. Please contact <span
                    class="dept">CEIT Department</span> for access.
            </p>
            <p class="copyright">
                © 2026 <span class="gold">Ministry of Science and Technology</span> · Mandalay Technological
                University
            </p>
        </footer>

    </div>

    <script>
        function goToLogin(role) {
            switch (role) {
                case 'admin':
                    window.location.href = "{{ route('admin.login') }}";
                    break;
                case 'lecturer':
                    window.location.href = "{{ route('lecturer.login') }}";
                    break;
                case 'student':
                    window.location.href = "{{ route('student.login') }}";
                    break;
            }
        }
    </script>

</body>

</html>
