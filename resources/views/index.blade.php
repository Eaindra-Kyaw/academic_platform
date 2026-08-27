<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTU Academic Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
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
            background: #f0f4f9;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 50px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 20px rgba(13, 71, 161, 0.04);
        }

        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar .logo .icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #0B2B5B, #0D47A1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #F9A825;
            font-weight: 800;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.2);
        }

        .navbar .logo h1 {
            font-size: 18px;
            font-weight: 700;
            color: #0B2B5B;
            margin: 0;
            line-height: 1.2;
        }

        .navbar .logo span {
            font-size: 10px;
            color: #64748b;
            display: block;
            font-weight: 400;
            letter-spacing: 0.3px;
        }

        .navbar .buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 28px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-outline {
            background: transparent;
            color: #0D47A1;
            border: 2px solid #0D47A1;
        }

        .btn-outline:hover {
            background: #0D47A1;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(13, 71, 161, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0D47A1, #1565C0);
            color: white;
            box-shadow: 0 4px 16px rgba(13, 71, 161, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(13, 71, 161, 0.35);
        }

        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 80px 50px 30px;
            max-width: 1200px;
            margin: 0 auto;
            gap: 50px;
            min-height: calc(100vh - 80px);
        }

        .hero .left {
            flex: 1.2;
        }

        .hero .left .badge {
            display: inline-block;
            background: rgba(249, 168, 37, 0.12);
            color: #F9A825;
            padding: 4px 18px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
            border: 1px solid rgba(249, 168, 37, 0.15);
        }

        .hero .left h1 {
            font-size: 48px;
            font-weight: 900;
            color: #0B2B5B;
            line-height: 1.08;
            margin-bottom: 12px;
        }

        .hero .left p {
            font-size: 17px;
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 28px;
            max-width: 480px;
        }

        .hero .left p i {
            color: #F9A825;
        }

        .hero .left .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 14px 38px;
            border-radius: 50px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, #0D47A1, #1565C0);
            color: white;
            box-shadow: 0 4px 20px rgba(13, 71, 161, 0.3);
        }

        .btn-hero-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 35px rgba(13, 71, 161, 0.4);
        }

        .btn-hero-secondary {
            background: transparent;
            color: #0D47A1;
            border: 2px solid #0D47A1;
        }

        .btn-hero-secondary:hover {
            background: #0D47A1;
            color: white;
            transform: translateY(-4px);
        }

        .hero .right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero .right .hero-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 24px;
            padding: 35px 30px;
            box-shadow: 0 20px 60px rgba(13, 71, 161, 0.08);
            border: 1px solid rgba(13, 71, 161, 0.04);
            text-align: center;
            transition: all 0.3s ease;
        }

        .hero .right .hero-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 30px 80px rgba(13, 71, 161, 0.12);
        }

        .hero .right .hero-card .icon-big {
            font-size: 56px;
            color: #0D47A1;
            margin-bottom: 12px;
        }

        .hero .right .hero-card h3 {
            font-size: 22px;
            font-weight: 800;
            color: #0B2B5B;
        }

        .hero .right .hero-card p {
            font-size: 14px;
            color: #64748b;
            margin: 6px 0 16px;
            line-height: 1.6;
        }

        .hero .right .hero-card .pill-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .hero .right .hero-card .pill {
            background: #f0f4f9;
            color: #0D47A1;
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .hero .right .hero-card .pill.gold {
            background: rgba(249, 168, 37, 0.12);
            color: #F9A825;
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 60px;
            padding: 20px 50px 40px;
            flex-wrap: wrap;
        }

        .stats .stat {
            text-align: center;
        }

        .stats .stat .number {
            font-size: 30px;
            font-weight: 800;
            color: #0D47A1;
        }

        .stats .stat .label {
            font-size: 13px;
            color: #64748b;
            margin-top: 2px;
        }

        .stats .stat .label i {
            color: #F9A825;
        }

        .about-section {
            padding: 50px 50px 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-section .section-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .about-section .section-title h2 {
            font-size: 30px;
            font-weight: 800;
            color: #0B2B5B;
        }

        .about-section .section-title h2 .gold {
            color: #F9A825;
        }

        .about-section .section-title p {
            color: #64748b;
            font-size: 16px;
            margin-top: 4px;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 30px;
            align-items: start;
        }

        .about-text p {
            font-size: 15px;
            color: #1e293b;
            line-height: 1.8;
            margin-bottom: 12px;
            text-align: justify;
        }

        .about-text .highlight {
            color: #0D47A1;
            font-weight: 600;
        }

        .about-text .highlight-gold {
            color: #F9A825;
            font-weight: 600;
        }

        .about-text .vision-box {
            background: linear-gradient(135deg, rgba(13, 71, 161, 0.04), rgba(13, 71, 161, 0.01));
            border-left: 4px solid #F9A825;
            padding: 14px 18px;
            border-radius: 8px;
            margin-top: 12px;
        }

        .about-text .vision-box .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #F9A825;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .about-text .vision-box p {
            margin: 4px 0 0;
            font-style: italic;
            color: #0D47A1;
        }

        .dept-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-left: auto;
            max-width: 100%;
        }

        .dept-item {
            background: #ffffff;
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            border: 1px solid rgba(13, 71, 161, 0.04);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .dept-item:hover {
            border-color: #0D47A1;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(13, 71, 161, 0.06);
        }

        .dept-item .dept-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .dept-item .dept-icon-wrap {
            width: 40px;
            height: 40px;
            background: #EEF2F7;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #0D47A1;
            flex-shrink: 0;
        }

        .dept-item .dept-info {
            display: flex;
            flex-direction: column;
        }

        .dept-item .dept-info h4 {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        .dept-item .dept-info span {
            font-size: 11px;
            color: #64748b;
            display: block;
        }

        .dept-item .hod {
            font-size: 10px;
            color: #0D47A1;
            font-weight: 600;
            background: rgba(13, 71, 161, 0.08);
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
            margin-left: 8px;
            text-align: center;
            flex-shrink: 0;
        }

        .features-section {
            padding: 40px 50px 50px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .features-section .section-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .features-section .section-title h2 {
            font-size: 30px;
            font-weight: 800;
            color: #0B2B5B;
        }

        .features-section .section-title h2 .gold {
            color: #F9A825;
        }

        .features-section .section-title p {
            color: #64748b;
            font-size: 16px;
            margin-top: 4px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-item {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(13, 71, 161, 0.04);
            border: 1px solid rgba(13, 71, 161, 0.04);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 35px rgba(13, 71, 161, 0.08);
            border-color: rgba(13, 71, 161, 0.1);
        }

        .feature-item .icon {
            font-size: 28px;
            color: #0D47A1;
            margin-bottom: 10px;
        }

        .feature-item h4 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
        }

        .feature-item p {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.5;
        }

        .footer {
            background: #0B2B5B;
            color: #ffffff;
            padding: 40px 50px 30px;
            font-family: 'Inter', sans-serif;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1.5fr 1.5fr 2fr;
            gap: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-column h4 {
            color: #a0b3d9;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-column ul li {
            margin-bottom: 10px;
        }

        .footer-column ul li a {
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 400;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-column ul li a:hover {
            color: #F9A825;
        }

        .footer-column ul li a i {
            font-size: 12px;
            color: #a0b3d9;
        }

        .footer-column ul li a:hover i {
            color: #F9A825;
        }

        .footer-contact p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 8px;
            color: #ffffff;
        }

        .footer-contact p strong {
            font-weight: 600;
        }

        .footer-contact .phone-link {
            color: #ffffff;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .footer-column.connect-col {
            padding-left: 20px;
        }

        .footer-socials {
            display: flex;
            gap: 16px;
            margin-top: 6px;
        }

        .footer-socials a {
            color: #ffffff;
            font-size: 20px;
            transition: color 0.2s ease;
        }

        .footer-socials a:hover {
            color: #F9A825;
        }

        .footer-logo-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            text-align: center;
        }

        .footer-logo-col img {
            width: 100px;
            height: auto;
            object-fit: contain;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .footer-logo-col .address-text {
            font-size: 13px;
            color: #a0b3d9;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
        }

        .footer-logo-col .address-text i {
            color: #F9A825;
            font-size: 14px;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 16px auto 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 6px;
        }

        .footer-bottom .contact-note {
            font-size: 13px;
            color: #ffffff;
            opacity: 0.85;
        }

        .footer-bottom .contact-note i {
            color: #F9A825;
        }

        .footer-bottom .contact-note strong {
            font-weight: 600;
        }

        .footer-bottom-copy {
            font-size: 12px;
            color: #a0b3d9;
        }

        .footer-bottom-copy strong {
            color: #ffffff;
            font-weight: 500;
        }

        @media (max-width: 1024px) {
            .hero {
                flex-direction: column;
                padding: 40px 30px;
                text-align: center;
                min-height: auto;
            }

            .hero .left p {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }

            .hero .left .hero-buttons {
                justify-content: center;
            }

            .hero .left h1 {
                font-size: 38px;
            }

            .about-grid {
                grid-template-columns: 1fr;
            }

            .dept-grid {
                margin-left: 0;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-container {
                grid-template-columns: 1fr 1fr;
                gap: 30px;
            }

            .footer-logo-col {
                grid-column: span 2;
                align-items: center;
            }

            .footer-column.connect-col {
                padding-left: 0;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 14px 20px;
                flex-wrap: wrap;
                justify-content: center;
                text-align: center;
            }

            .hero {
                padding: 30px 20px;
            }

            .hero .left h1 {
                font-size: 30px;
            }

            .hero .left p {
                font-size: 15px;
            }

            .hero .right .hero-card {
                padding: 24px 20px;
            }

            .stats {
                gap: 24px;
                padding: 16px 20px 30px;
            }

            .stats .stat .number {
                font-size: 24px;
            }

            .about-section {
                padding: 30px 20px;
            }

            .about-section .section-title h2 {
                font-size: 26px;
            }

            .dept-grid {
                grid-template-columns: 1fr;
            }

            .features-section {
                padding: 30px 20px;
            }

            .features-section .section-title h2 {
                font-size: 26px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .btn-hero {
                padding: 12px 24px;
                font-size: 14px;
            }

            .btn {
                padding: 8px 16px;
                font-size: 12px;
            }

            .footer {
                padding: 30px 20px 20px;
            }

            .footer-container {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .footer-logo-col {
                grid-column: span 1;
                align-items: center;
            }

            .footer-socials {
                justify-content: flex-start;
            }

            .footer-column.connect-col {
                padding-left: 0;
            }
        }

        @media (max-width: 480px) {
            .navbar .logo h1 {
                font-size: 14px;
            }

            .navbar .logo span {
                font-size: 9px;
            }

            .navbar .logo .icon {
                width: 34px;
                height: 34px;
                font-size: 11px;
            }

            .hero .left h1 {
                font-size: 26px;
            }

            .hero .left .hero-buttons {
                flex-direction: column;
                width: 100%;
            }

            .btn-hero {
                width: 100%;
                justify-content: center;
            }

            .stats {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .hero .right .hero-card .icon-big {
                font-size: 40px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <div class="icon">MTU</div>
            <div>
                <h1>Mandalay Technological University</h1>
                <span>Ministry of Science and Technology</span>
            </div>
        </div>
        <div class="buttons">
            <a href="/login" class="btn btn-outline" id="loginBtn">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
            <a href="/register" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Register
            </a>
        </div>
    </nav>

    <section class="hero">
        <div class="left">
            <div class="badge"><i class="bi bi-stars"></i> Academic Intelligence System</div>
            <h1>
                <nobr>Intelligent University Portal</nobr>
            </h1>
            <p><i class="bi bi-qr-code"></i> QR Attendance &middot; <i class="bi bi-graph-up"></i> Predictive Analytics
                &middot; <i class="bi bi-robot"></i> Uni Bot Assistant</p>
            <div class="hero-buttons">
                <a href="#about" class="btn-hero btn-hero-primary"><i class="bi bi-compass"></i> Explore the Portal</a>
            </div>
        </div>
        <div class="right">
            <div class="hero-card">
                <div class="icon-big"><i class="bi bi-mortarboard-fill"></i></div>
                <h3>Welcome to MTU</h3>
                <p>Your gateway to academic excellence at Mandalay Technological University</p>
                <div class="pill-group">
                    <span class="pill"><i class="bi bi-check-circle-fill" style="color: #F9A825;"></i> 11
                        Departments</span>
                    <span class="pill gold"><i class="bi bi-people-fill"></i> 30+ Courses</span>
                </div>
            </div>
        </div>
    </section>

    <div class="stats">
        <div class="stat">
            <div class="number" id="statStudents">0</div>
            <div class="label"><i class="bi bi-people-fill"></i> Students</div>
        </div>
        <div class="stat">
            <div class="number" id="statLecturers">0</div>
            <div class="label"><i class="bi bi-person-badge"></i> Teachers</div>
        </div>
        <div class="stat">
            <div class="number" id="statCourses">0</div>
            <div class="label"><i class="bi bi-book-fill"></i> Courses</div>
        </div>
        <div class="stat">
            <div class="number" id="statDepartments">11</div>
            <div class="label"><i class="bi bi-building"></i> Departments</div>
        </div>
    </div>

    <section class="about-section" id="about">
        <div class="section-title">
            <h2>For the <span class="gold">MTU Community</span></h2>
            <p>Your connected hub for modern academic life at MTU.</p>
        </div>
        <div class="about-grid">
            <div class="about-text">
                <p>Welcome to the <span class="highlight">Mandalay Technological University Academic Portal</span> — a
                    powerful digital ecosystem developed by the <span class="highlight">CEIT Department</span> to
                    enhance the educational journey of our community.</p>
                <p>Designed with <span class="highlight-gold">admins, teachers, and students</span> in mind, this
                    platform bridges the gap between management and academics. It offers seamless QR attendance
                    tracking, intelligent risk prediction, and real-time analytics, allowing teachers to monitor
                    progress while giving students a clear view of their own academic health.</p>
                <div class="vision-box">
                    <div class="label"><i class="bi bi-heart-fill" style="color: #F9A825;"></i> Built for You</div>
                    <p>"Designed to simplify your daily university journey—from seamless attendance tracking to clear
                        academic insights, we're here to support every step of your growth."</p>
                </div>
            </div>
            <div class="dept-grid">
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-cpu"></i></div>
                        <div class="dept-info">
                            <h4>Computer Engineering & Information Technology</h4><span>CEIT Department</span>
                        </div>
                    </div><span class="hod">Dr. Phyo Thu Zar Tun</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-bricks"></i></div>
                        <div class="dept-info">
                            <h4>Civil Engineering</h4><span>CE Department</span>
                        </div>
                    </div><span class="hod">Dr. Nilar Aye</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-gear-wide-connected"></i></div>
                        <div class="dept-info">
                            <h4>Mechanical Engineering</h4><span>ME Department</span>
                        </div>
                    </div><span class="hod">....</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-lightning-charge-fill"></i></div>
                        <div class="dept-info">
                            <h4>Electrical Power Engineering</h4><span>EP Department</span>
                        </div>
                    </div><span class="hod">...</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-thermometer-half"></i></div>
                        <div class="dept-info">
                            <h4>Electronic Engineering</h4><span>EC Department</span>
                        </div>
                    </div><span class="hod">....</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-magic"></i></div>
                        <div class="dept-info">
                            <h4>Mechatronics Engineering</h4><span>MEC Department</span>
                        </div>
                    </div><span class="hod">....</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-droplet-half"></i></div>
                        <div class="dept-info">
                            <h4>Chemical Engineering</h4><span>CH Department</span>
                        </div>
                    </div><span class="hod">....</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-tree"></i></div>
                        <div class="dept-info">
                            <h4>Agricultural Engineering</h4><span>AE Department</span>
                        </div>
                    </div><span class="hod">....</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-fingerprint"></i></div>
                        <div class="dept-info">
                            <h4>Biotechnology</h4><span>BT Department</span>
                        </div>
                    </div><span class="hod">....</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-pencil"></i></div>
                        <div class="dept-info">
                            <h4>Architecture</h4><span>AR Department</span>
                        </div>
                    </div><span class="hod">....</span>
                </div>
                <div class="dept-item">
                    <div class="dept-left">
                        <div class="dept-icon-wrap"><i class="bi bi-radioactive"></i></div>
                        <div class="dept-info">
                            <h4>Nuclear Technology</h4><span>NT Department</span>
                        </div>
                    </div><span class="hod">....</span>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section" id="features">
        <div class="section-title">
            <h2>Portal <span class="gold">Features</span></h2>
            <p>Everything you need for modern academic management</p>
        </div>
        <div class="features-grid">
            <div class="feature-item">
                <div class="icon"><i class="bi bi-qr-code"></i></div>
                <h4>QR Attendance</h4>
                <p>Dynamic QR codes with configurable expiry and manual fallback for seamless attendance tracking.</p>
            </div>
            <div class="feature-item">
                <div class="icon"><i class="bi bi-graph-up"></i></div>
                <h4>Predictive Analytics</h4>
                <p>Heuristic-based risk prediction identifies at-risk students using attendance patterns and trends.</p>
            </div>
            <div class="feature-item">
                <div class="icon"><i class="bi bi-robot"></i></div>
                <h4>Uni Bot Assistant</h4>
                <p>Conversational AI chatbot that answers student queries on attendance, eligibility, and progress.</p>
            </div>
            <div class="feature-item">
                <div class="icon"><i class="bi bi-clipboard-data"></i></div>
                <h4>Academic Intelligence</h4>
                <p>Real-time dashboards with course performance, department rankings, and risk distribution charts.</p>
            </div>
            <div class="feature-item">
                <div class="icon"><i class="bi bi-people"></i></div>
                <h4>Multi-Role Access</h4>
                <p>Dedicated dashboards for Admin, Lecturer, and Student with role-specific features and permissions.
                </p>
            </div>
            <div class="feature-item">
                <div class="icon"><i class="bi bi-diagram-3"></i></div>
                <h4>Smart Campus Ecosystem</h4>
                <p>Unifying AI-driven analytics, automated attendance, and academic intelligence into one seamless
                    ecosystem tailored for MTU.</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column">
                <ul>
                    <li><a href="/login"><i class="bi bi-arrow-right"></i> Mandalay Technological University</a></li>
                    <li><a href="#about"><i class="bi bi-arrow-right"></i> About</a></li>
                    <li><a href="#features"><i class="bi bi-arrow-right"></i> Features</a></li>
                    <li><a href="/login"><i class="bi bi-arrow-right"></i> Login</a></li>
                    <li><a href="/register"><i class="bi bi-arrow-right"></i> Register</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>Contact Details</h4>
                <div class="footer-contact">
                    <p><strong>Phone</strong> <a href="tel:123456" class="phone-link">+95 2 123 456</a></p>
                    <p><strong>International</strong> <a href="tel:+95912345678" class="phone-link">+95 9 123 456
                            78</a></p>
                    <p style="margin-top: 12px;"><strong>Address</strong><br>Mandalay Technological
                        University<br>Patheingyi Township, Mandalay<br>Myanmar</p>
                </div>
            </div>
            <div class="footer-column connect-col">
                <h4>Connect with us</h4>
                <div class="footer-socials">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-linkedin"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                    <a href="#"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
            <div class="footer-logo-col">
                <img src="{{ asset('images/mtu-logo.png') }}" alt="MTU Logo">
                <div class="address-text"><i class="bi bi-geo-alt-fill"></i> Mandalay Technological University</div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="contact-note"><i class="bi bi-info-circle"></i> Accounts created by admins only. Contact
                <strong>CEIT Department</strong></div>
            <div class="footer-bottom-copy">&copy; {{ date('Y') }} <strong>Ministry of Science and
                    Technology</strong> &middot; Mandalay Technological University</div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const students = {{ \App\Models\User::where('role_id', 3)->count() ?? 0 }};
            const lecturers = {{ \App\Models\User::where('role_id', 2)->count() ?? 0 }};
            const courses = {{ \App\Models\Course::where('is_active', true)->count() ?? 0 }};
            const departments = 11;
            animateNumber('statStudents', students);
            animateNumber('statLecturers', lecturers);
            animateNumber('statCourses', courses);
            document.getElementById('statDepartments').textContent = departments;
        });

        function animateNumber(id, end) {
            const el = document.getElementById(id);
            if (!el) return;
            let start = 0;
            const duration = 1200;
            const stepTime = 20;
            const steps = duration / stepTime;
            const increment = end / steps;
            const interval = setInterval(() => {
                start += increment;
                if (start >= end) {
                    clearInterval(interval);
                    start = end;
                }
                el.textContent = Math.round(start).toLocaleString();
            }, stepTime);
        }

        document.getElementById('loginBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/login';
        });
    </script>
</body>

</html>
