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
        :root {
            --primary: #0D47A1;
            --primary-dark: #0B2B5B;
            --primary-light: #1565C0;
            --secondary: #42A5F5;
            --accent: #F9A825;
            --bg-main: #E3F2FD;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(13, 71, 161, 0.08);
            --shadow-hover: 0 8px 30px rgba(13, 71, 161, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ============================================================
           NAVBAR
           ============================================================ */
        .navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px);
            padding: 12px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(13, 71, 161, 0.06);
            transition: all 0.3s ease;
        }

        .navbar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar .brand .logo {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-weight: 900;
            font-size: 16px;
        }

        .navbar .brand h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
            line-height: 1.2;
        }

        .navbar .brand span {
            font-size: 10px;
            color: var(--text-gray);
            display: block;
        }

        .navbar .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-nav {
            padding: 8px 24px;
            border-radius: 50px;
            font-size: 13px;
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

        .btn-nav-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-nav-outline:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-2px);
        }

        .btn-nav-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
        }

        .btn-nav-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(13, 71, 161, 0.3);
        }

        /* ============================================================
           HERO SECTION
           ============================================================ */
        .hero {
            padding: 130px 40px 50px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: linear-gradient(180deg, var(--bg-main) 0%, var(--white) 100%);
            position: relative;
            overflow: hidden;
        }

        .hero .bg-orb1 {
            position: absolute;
            top: -30%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(13, 71, 161, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero .bg-orb2 {
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(249, 168, 37, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero .badge {
            display: inline-block;
            background: rgba(249, 168, 37, 0.1);
            color: var(--accent);
            padding: 6px 20px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid rgba(249, 168, 37, 0.15);
            position: relative;
        }

        .hero h1 {
            font-size: 54px;
            font-weight: 900;
            color: var(--primary-dark);
            line-height: 1.08;
            max-width: 800px;
            margin-bottom: 12px;
            position: relative;
        }

        .hero h1 .gold {
            color: var(--accent);
        }

        .hero h1 .light {
            color: var(--primary-light);
        }

        .hero .tagline {
            font-size: 18px;
            color: var(--text-gray);
            max-width: 600px;
            margin: 0 auto 20px;
            line-height: 1.7;
            position: relative;
        }

        .hero .tagline i {
            color: var(--accent);
        }

        .hero .motto {
            font-size: 14px;
            color: var(--text-gray);
            font-style: italic;
            margin-bottom: 28px;
            position: relative;
            letter-spacing: 2px;
        }

        .hero .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: center;
            position: relative;
        }

        .btn-hero {
            padding: 16px 44px;
            border-radius: 50px;
            font-size: 16px;
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
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
        }

        .btn-hero-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(13, 71, 161, 0.3);
        }

        .btn-hero-secondary {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-hero-secondary:hover {
            background: var(--primary);
            color: var(--white);
            transform: translateY(-4px);
        }

        .hero-stats {
            display: flex;
            gap: 50px;
            margin-top: 40px;
            flex-wrap: wrap;
            justify-content: center;
            position: relative;
        }

        .hero-stats .stat {
            text-align: center;
        }

        .hero-stats .stat .number {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary);
        }

        .hero-stats .stat .label {
            font-size: 13px;
            color: var(--text-gray);
            margin-top: 2px;
        }

        /* ============================================================
           ABOUT MTU
           ============================================================ */
        .about-mtu {
            padding: 60px 40px;
            background: var(--white);
        }

        .about-mtu .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .about-mtu .section-title {
            text-align: center;
            margin-bottom: 36px;
        }

        .about-mtu .section-title h2 {
            font-size: 34px;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .about-mtu .section-title p {
            color: var(--text-gray);
            font-size: 16px;
            margin-top: 4px;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .about-text p {
            font-size: 15px;
            color: var(--text-dark);
            line-height: 1.8;
            margin-bottom: 12px;
        }

        .about-text .highlight {
            color: var(--primary);
            font-weight: 600;
        }

        .about-stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .about-stat-card {
            background: var(--bg-main);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(13, 71, 161, 0.04);
        }

        .about-stat-card .number {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary);
        }

        .about-stat-card .label {
            font-size: 13px;
            color: var(--text-gray);
            margin-top: 2px;
        }

        /* ============================================================
           DEPARTMENTS
           ============================================================ */
        .departments {
            padding: 60px 40px;
            background: var(--bg-main);
        }

        .departments .section-title {
            text-align: center;
            margin-bottom: 36px;
        }

        .departments .section-title h2 {
            font-size: 34px;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .departments .section-title p {
            color: var(--text-gray);
            font-size: 16px;
            margin-top: 4px;
        }

        .dept-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .dept-card {
            background: var(--white);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(13, 71, 161, 0.06);
            transition: all 0.3s ease;
        }

        .dept-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: var(--secondary);
        }

        .dept-card .icon {
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .dept-card h4 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .dept-card .code {
            font-size: 12px;
            color: var(--text-gray);
            font-weight: 600;
        }

        /* ============================================================
           FEATURES
           ============================================================ */
        .features {
            padding: 60px 40px;
            background: var(--white);
        }

        .features .section-title {
            text-align: center;
            margin-bottom: 36px;
        }

        .features .section-title h2 {
            font-size: 34px;
            font-weight: 800;
            color: var(--primary-dark);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--bg-main);
            border-radius: 16px;
            padding: 28px 24px;
            transition: all 0.3s ease;
            border: 1px solid rgba(13, 71, 161, 0.04);
            text-align: left;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: var(--secondary);
        }

        .feature-card .icon {
            width: 52px;
            height: 52px;
            background: rgba(13, 71, 161, 0.08);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 14px;
        }

        .feature-card h4 {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .feature-card p {
            font-size: 14px;
            color: var(--text-gray);
            line-height: 1.6;
        }

        /* ============================================================
           FOOTER
           ============================================================ */
        .footer {
            background: var(--primary-dark);
            color: rgba(255, 255, 255, 0.7);
            padding: 36px 40px 20px;
            text-align: center;
        }

        .footer .brand-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--white);
        }

        .footer .brand-text .gold {
            color: var(--accent);
        }

        .footer p {
            font-size: 13px;
            margin: 4px 0;
        }

        .footer .footer-links {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin: 12px 0;
            flex-wrap: wrap;
        }

        .footer .footer-links a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .footer .footer-links a:hover {
            color: var(--accent);
        }

        .footer .copyright {
            font-size: 12px;
            opacity: 0.5;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 1024px) {
            .dept-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .about-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 12px 20px;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .hero {
                padding: 150px 20px 40px;
                min-height: auto;
            }

            .hero h1 {
                font-size: 34px;
            }

            .hero .tagline {
                font-size: 15px;
            }

            .hero-stats {
                gap: 20px;
            }

            .hero-stats .stat .number {
                font-size: 24px;
            }

            .about-mtu {
                padding: 40px 20px;
            }

            .about-mtu .section-title h2 {
                font-size: 26px;
            }

            .about-stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .departments {
                padding: 40px 20px;
            }

            .departments .section-title h2 {
                font-size: 26px;
            }

            .dept-grid {
                grid-template-columns: 1fr 1fr;
            }

            .features {
                padding: 40px 20px;
            }

            .features .section-title h2 {
                font-size: 26px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .btn-hero {
                padding: 14px 28px;
                font-size: 14px;
            }

            .btn-nav {
                padding: 6px 14px;
                font-size: 12px;
            }

            .footer {
                padding: 24px 20px 16px;
            }
        }

        @media (max-width: 480px) {
            .navbar .brand .logo {
                width: 36px;
                height: 36px;
                font-size: 12px;
            }

            .navbar .brand h1 {
                font-size: 14px;
            }

            .hero h1 {
                font-size: 26px;
            }

            .hero .tagline {
                font-size: 13px;
            }

            .hero .hero-buttons {
                flex-direction: column;
                align-items: center;
                width: 100%;
            }

            .btn-hero {
                width: 100%;
                justify-content: center;
            }

            .hero-stats {
                flex-direction: column;
                align-items: center;
                gap: 8px;
            }

            .about-stats-grid {
                grid-template-columns: 1fr;
            }

            .dept-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- ============================================================
    NAVBAR
    ============================================================ -->
    <nav class="navbar">
        <div class="brand">
            <div class="logo">MTU</div>
            <div>
                <h1>Mandalay Technological University</h1>
                <span>Ministry of Science and Technology</span>
            </div>
        </div>
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn-nav btn-nav-outline">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
            <a href="{{ route('register') }}" class="btn-nav btn-nav-primary">
                <i class="bi bi-person-plus"></i> Register
            </a>
        </div>
    </nav>

    <!-- ============================================================
    HERO SECTION
    ============================================================ -->
    <section class="hero">
        <div class="bg-orb1"></div>
        <div class="bg-orb2"></div>

        <div class="badge"><i class="bi bi-stars"></i> Academic Intelligence System</div>
        <h1>
            Intelligent <span class="gold">University</span><br>
            <span class="light">Portal</span>
        </h1>
        <p class="tagline">
            <i class="bi bi-qr-code"></i> QR Attendance ·
            Predictive Analytics ·
            Real-time Insights
        </p>
        <p class="motto">"Technology for Education"</p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">
                <i class="bi bi-rocket-takeoff"></i> Get Started
            </a>
            <a href="#about" class="btn-hero btn-hero-secondary">
                <i class="bi bi-chevron-down"></i> Learn More
            </a>
        </div>
        <div class="hero-stats">
            <div class="stat">
                <div class="number" id="statStudents">0</div>
                <div class="label"><i class="bi bi-people-fill"></i> Students</div>
            </div>
            <div class="stat">
                <div class="number" id="statLecturers">0</div>
                <div class="label"><i class="bi bi-person-badge"></i> Lecturers</div>
            </div>
            <div class="stat">
                <div class="number" id="statCourses">0</div>
                <div class="label"><i class="bi bi-book-fill"></i> Courses</div>
            </div>
            <div class="stat">
                <div class="number" id="statDepartments">0</div>
                <div class="label"><i class="bi bi-building"></i> Departments</div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    ABOUT MTU
    ============================================================ -->
    <section class="about-mtu" id="about">
        <div class="container">
            <div class="section-title">
                <h2>About Mandalay Technological University</h2>
                <p>Excellence in Engineering and Technology Education</p>
            </div>
            <div class="about-grid">
                <div class="about-text">
                    <p>
                        <span class="highlight">Mandalay Technological University (MTU)</span> is one of Myanmar's
                        premier institutions for engineering and technology education. Established with a mission
                        to produce highly skilled engineers, MTU offers undergraduate and postgraduate programs
                        across multiple engineering disciplines.
                    </p>
                    <p>
                        The university is dedicated to fostering innovation, research, and academic excellence,
                        preparing students to meet the challenges of the modern technological landscape. MTU
                        is committed to advancing knowledge through cutting-edge research and industry collaboration.
                    </p>
                    <p>
                        <span class="highlight">Vision:</span> To become a leading technological university in
                        Southeast Asia, recognized for excellence in education, research, and innovation.
                    </p>
                </div>
                <div class="about-stats-grid">
                    <div class="about-stat-card">
                        <div class="number" id="aboutStudents">0</div>
                        <div class="label">Enrolled Students</div>
                    </div>
                    <div class="about-stat-card">
                        <div class="number" id="aboutLecturers">0</div>
                        <div class="label">Faculty Members</div>
                    </div>
                    <div class="about-stat-card">
                        <div class="number" id="aboutDepartments">0</div>
                        <div class="label">Academic Departments</div>
                    </div>
                    <div class="about-stat-card">
                        <div class="number">10+</div>
                        <div class="label">Years of Excellence</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    DEPARTMENTS
    ============================================================ -->
    <section class="departments">
        <div class="section-title">
            <h2>Academic Departments</h2>
            <p>Explore our diverse range of engineering and technology programs</p>
        </div>
        <div class="dept-grid">
            <div class="dept-card">
                <div class="icon"><i class="bi bi-cpu"></i></div>
                <h4>Computer Engineering & IT</h4>
                <div class="code">CEIT</div>
            </div>
            <div class="dept-card">
                <div class="icon"><i class="bi bi-building"></i></div>
                <h4>Civil Engineering</h4>
                <div class="code">CE</div>
            </div>
            <div class="dept-card">
                <div class="icon"><i class="bi bi-gear"></i></div>
                <h4>Mechanical Engineering</h4>
                <div class="code">ME</div>
            </div>
            <div class="dept-card">
                <div class="icon"><i class="bi bi-lightning"></i></div>
                <h4>Electrical Power Engineering</h4>
                <div class="code">EP</div>
            </div>
            <div class="dept-card">
                <div class="icon"><i class="bi bi-radio"></i></div>
                <h4>Electronic Engineering</h4>
                <div class="code">EC</div>
            </div>
            <div class="dept-card">
                <div class="icon"><i class="bi bi-robot"></i></div>
                <h4>Mechatronics Engineering</h4>
                <div class="code">MEC</div>
            </div>
            <div class="dept-card">
                <div class="icon"><i class="bi bi-flask"></i></div>
                <h4>Chemical Engineering</h4>
                <div class="code">CH</div>
            </div>
            <div class="dept-card">
                <div class="icon"><i class="bi bi-tree"></i></div>
                <h4>Agricultural Engineering</h4>
                <div class="code">AE</div>
            </div>
        </div>
    </section>

    <!-- ============================================================
    FEATURES
    ============================================================ -->
    <section class="features" id="features">
        <div class="section-title">
            <h2>Platform Features</h2>
            <p>Everything you need for modern academic management</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="icon"><i class="bi bi-qr-code"></i></div>
                <h4>QR Attendance</h4>
                <p>Dynamic QR codes with configurable expiry and manual fallback for seamless attendance tracking.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="bi bi-graph-up"></i></div>
                <h4>Predictive Analytics</h4>
                <p>Heuristic-based risk prediction identifies at-risk students using attendance patterns and trends.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="bi bi-robot"></i></div>
                <h4>Uni Bot Assistant</h4>
                <p>Conversational AI chatbot that answers student queries on attendance, eligibility, and progress.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="bi bi-clipboard-data"></i></div>
                <h4>Academic Intelligence</h4>
                <p>Real-time dashboards with course performance, department rankings, and risk distribution charts.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="bi bi-people"></i></div>
                <h4>Multi-Role Access</h4>
                <p>Dedicated dashboards for Admin, Lecturer, and Student with role-specific features and permissions.
                </p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="bi bi-shield-lock"></i></div>
                <h4>Secure & Audited</h4>
                <p>Role-based middleware, admin approval workflow, and full audit logging for accountability.</p>
            </div>
        </div>
    </section>

    <!-- ============================================================
    FOOTER
    ============================================================ -->
    <footer class="footer">
        <div class="brand-text">Mandalay Technological <span class="gold">University</span></div>
        <div class="footer-links">
            <a href="#about">About</a>
            <a href="#features">Features</a>
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Register</a>
        </div>
        <p>
            <i class="bi bi-info-circle" style="color: var(--accent);"></i>
            Accounts are created by admins only. Contact <strong style="color: white;">CEIT Department</strong>
        </p>
        <div class="copyright">
            &copy; {{ date('Y') }} <span style="color: var(--accent); font-weight: 600;">Ministry of Science and
                Technology</span>
            &middot; Mandalay Technological University
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const students = {{ \App\Models\User::where('role_id', 3)->count() ?? 0 }};
            const lecturers = {{ \App\Models\User::where('role_id', 2)->count() ?? 0 }};
            const courses = {{ \App\Models\Course::where('is_active', true)->count() ?? 0 }};
            const departments = {{ \App\Models\Department::count() ?? 0 }};

            animateNumber('statStudents', students);
            animateNumber('statLecturers', lecturers);
            animateNumber('statCourses', courses);
            animateNumber('statDepartments', departments);
            animateNumber('aboutStudents', students);
            animateNumber('aboutLecturers', lecturers);
            animateNumber('aboutDepartments', departments);
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

        document.querySelector('.btn-hero-secondary')?.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector('#about');
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    </script>
</body>

</html>
