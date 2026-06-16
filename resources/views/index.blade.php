<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uni Academic Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #800000 0%, #4a0000 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 50px 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.1);
            padding: 12px 30px;
            border-radius: 60px;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 50px;
            height: 50px;
            background: #FFD700;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #800000;
            font-weight: bold;
            font-size: 22px;
        }

        .logo-text h1 {
            font-size: 22px;
            color: white;
            font-weight: 700;
        }

        .logo-text p {
            font-size: 12px;
            color: rgba(255, 215, 0, 0.8);
        }

        .header h2 {
            font-size: 42px;
            font-weight: 800;
            color: white;
            margin-bottom: 12px;
        }

        .header p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.8);
        }

        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #FFD700;
        }

        .stat-label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 35px;
            margin: 50px 0;
        }

        .role-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 30px 25px;
            border: 1px solid rgba(255, 215, 0, 0.25);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .role-card:hover {
            transform: translateY(-5px);
            border-color: #FFD700;
            background: rgba(255, 255, 255, 0.12);
        }

        .role-icon {
            width: 80px;
            height: 80px;
            background: #FFD700;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 38px;
            color: #800000;
        }

        .role-card h3 {
            font-size: 24px;
            font-weight: 700;
            color: white;
            text-align: center;
            margin-bottom: 10px;
        }

        .role-card p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 20px;
        }

        .role-features {
            list-style: none;
            margin-top: 15px;
        }

        .role-features li {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-features li i {
            color: #FFD700;
            font-size: 14px;
            width: 20px;
        }

        .btn-role {
            background: #FFD700;
            border: none;
            padding: 12px;
            border-radius: 40px;
            color: #800000;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .btn-role:hover {
            background: #e6b800;
            transform: scale(1.02);
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
        }

        @media (max-width: 1024px) {
            .role-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .role-grid {
                grid-template-columns: 1fr;
            }

            .stats-bar {
                gap: 30px;
            }

            .header h2 {
                font-size: 28px;
            }

            .container {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <div class="logo-icon">Uni</div>
                <div class="logo-text">
                    <p>Academic Portal</p>
                </div>
            </div>
            <h2>Smart Attendance. Predictive Analytics.</h2>
            <p>Secure QR | Real-time Monitoring | AI Risk Prediction</p>
            <div class="stats-bar">
                <div class="stat-item">
                    <div class="stat-number">1,284+</div>
                    <div class="stat-label">Students</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">48+</div>
                    <div class="stat-label">Lecturers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">124+</div>
                    <div class="stat-label">Courses</div>
                </div>
            </div>
        </div>

        <div class="role-grid">
            <div class="role-card" onclick="goToLogin('admin')">
                <div class="role-icon"><i class="bi bi-shield-lock-fill"></i></div>
                <h3>Administrator</h3>
                <p>University-wide control & analytics</p>
                <ul class="role-features">
                    <li><i class="bi bi-check-circle"></i> Academic Performance</li>
                    <li><i class="bi bi-check-circle"></i> Risk Forecasting</li>
                    <li><i class="bi bi-check-circle"></i> Department Intelligence</li>
                </ul>
                <button class="btn-role">Access Admin →</button>
            </div>
            <div class="role-card" onclick="goToLogin('lecturer')">
                <div class="role-icon"><i class="bi bi-person-badge"></i></div>
                <h3>Lecturer</h3>
                <p>Manage classes & monitor engagement</p>
                <ul class="role-features">
                    <li><i class="bi bi-check-circle"></i> Generate QR Codes</li>
                    <li><i class="bi bi-check-circle"></i> Risk Analytics</li>
                    <li><i class="bi bi-check-circle"></i> Student Intervention</li>
                </ul>
                <button class="btn-role">Access Lecturer →</button>
            </div>
            <div class="role-card" onclick="goToLogin('student')">
                <div class="role-icon"><i class="bi bi-mortarboard-fill"></i></div>
                <h3>Student</h3>
                <p>Track your academic journey</p>
                <ul class="role-features">
                    <li><i class="bi bi-check-circle"></i> QR Attendance</li>
                    <li><i class="bi bi-check-circle"></i> Health Score</li>
                    <li><i class="bi bi-check-circle"></i> AI Recommendations</li>
                </ul>
                <button class="btn-role">Access Student →</button>
            </div>
        </div>

        <div class="footer">© 2026 University Academic Intelligence System</div>
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
