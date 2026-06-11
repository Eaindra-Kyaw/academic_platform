@extends('layouts.app')

@section('title', 'Student Dashboard | MTU Academic Intelligence')
@section('role', 'Student')
@section('page-title', 'Student Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('student.dashboard') }}" class="nav-item active"><i
            class="bi bi-speedometer2"></i><span>Dashboard</span></a>
    <a href="{{ route('student.scan') }}" class="nav-item"><i class="bi bi-qr-code-scan"></i><span>Scan QR</span></a>
    <a href="#" class="nav-item"><i class="bi bi-calendar3"></i><span>Timetable</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up"></i><span>My Progress</span></a>
    <a href="#" class="nav-item"><i class="bi bi-robot"></i><span>Uni Bot</span></a>
    <a href="#" class="nav-item"><i class="bi bi-bell"></i><span>Notifications</span></a>
@endsection

@section('content')
    <style>
        /* Student Dashboard Styles */
        .welcome-card {
            background: linear-gradient(135deg, #800000 0%, #5f0000 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #800000;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .course-list {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            flex-wrap: wrap;
        }

        .course-item:last-child {
            border-bottom: none;
        }

        .course-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .badge-eligible {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-warning {
            background: #fef9c3;
            color: #854d0e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .progress-bar-custom {
            height: 6px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 0.25rem;
        }

        .progress-fill {
            height: 100%;
            border-radius: 10px;
        }

        .progress-fill.success {
            background: #10b981;
        }

        .progress-fill.warning {
            background: #f59e0b;
        }

        .ahs-ring {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(#800000 0deg 309.6deg, #e5e7eb 309.6deg 360deg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .ahs-inner {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .ahs-score {
            font-size: 1.8rem;
            font-weight: 800;
            color: #800000;
        }

        .recommendation-box {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.8rem;
        }

        .recommendation-success {
            background: #ecfdf5;
            border-left-color: #10b981;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .two-col {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .course-item {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-number {
                font-size: 1.2rem;
            }

            .ahs-ring {
                width: 80px;
                height: 80px;
            }

            .ahs-inner {
                width: 60px;
                height: 60px;
            }

            .ahs-score {
                font-size: 1.2rem;
            }

            .welcome-card {
                padding: 1rem;
            }

            .welcome-card h3 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 375px) {
            .stat-number {
                font-size: 1rem;
            }

            .stat-card {
                padding: 0.5rem;
            }

            .course-name {
                font-size: 0.75rem;
            }

            .recommendation-box {
                font-size: 0.7rem;
                padding: 0.5rem;
            }
        }
    </style>

    <div class="welcome-card">
        <h3>Hello, {{ Auth::user()->name }}! 👋</h3>
        <p style="font-size: 0.85rem; opacity: 0.9;">Here's your academic summary. Keep up the good work!</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">82%</div>
            <div class="stat-label">Attendance Rate</div>
            <div class="progress-bar-custom mt-1">
                <div class="progress-fill success" style="width:82%"></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-number">7.5</div>
            <div class="stat-label">Roll Call Mark</div>
            <div class="stat-label">(out of 10)</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">86</div>
            <div class="stat-label">Health Score</div>
            <div class="stat-label">Stable</div>
        </div>
        <div class="stat-card">
            <div class="stat-number status-eligible">Eligible</div>
            <div class="stat-label">Exam Status</div>
        </div>
    </div>

    <div class="two-col">
        <div class="course-list">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-heart-fill"></i> Academic Health Score</div>
            <div style="padding: 1rem; text-align: center;">
                <div class="ahs-ring">
                    <div class="ahs-inner">
                        <div class="ahs-score">86</div>
                        <div style="font-size: 0.7rem;">Stable</div>
                    </div>
                </div>
                <div style="margin-top: 0.75rem; font-size: 0.7rem;">40% Attendance | 25% Roll Call | 20% Streak | 15% Trend
                </div>
            </div>
        </div>
        <div class="course-list">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-shield-exclamation"></i> Risk & Recovery</div>
            <div style="padding: 1rem;">
                <div>Risk Level: <span class="badge-warning">Medium Risk</span></div>
                <div class="progress-bar-custom mt-1">
                    <div class="progress-fill warning" style="width:42%"></div>
                </div>
                <div class="mt-2">Risk Score: 42/100</div>
                <hr style="margin: 0.5rem 0;">
                <div>Recovery Status: <span class="badge-eligible">Recovering</span></div>
                <div>Streak: <strong>12</strong> consecutive sessions</div>
            </div>
        </div>
    </div>

    <div class="course-list" style="margin-bottom: 1rem;">
        <div
            style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
            <i class="bi bi-book-open"></i> My Courses</div>
        <div class="course-item">
            <div><span class="course-name">CS301 - Database Systems</span><br><small>Dr. Aye Min Thu</small></div>
            <div class="course-attendance">88% <span class="badge-eligible">Eligible</span></div>
        </div>
        <div class="course-item">
            <div><span class="course-name">CS302 - Networking</span><br><small>Dr. Kyaw Kyaw</small></div>
            <div class="course-attendance">67% <span class="badge-warning">Warning</span></div>
        </div>
        <div class="course-item">
            <div><span class="course-name">CS303 - Operating Systems</span><br><small>Dr. Su Mon</small></div>
            <div class="course-attendance">95% <span class="badge-eligible">Eligible</span></div>
        </div>
        <div class="course-item">
            <div><span class="course-name">CS304 - Web Development</span><br><small>Dr. Thida Aung</small></div>
            <div class="course-attendance">73% <span class="badge-warning">Warning</span></div>
        </div>
    </div>

    <div class="two-col">
        <div class="course-list">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-bar-chart"></i> Peer Benchmarking</div>
            <div style="padding: 1rem;">
                <div>Your Rank: <strong>12 / 45</strong> (Top 27%)</div>
                <hr style="margin: 0.5rem 0;">
                <div>Course Avg: 78%</div>
                <div>Dept Avg: 79%</div>
                <div>Uni Avg: 74%</div>
            </div>
        </div>
        <div class="course-list">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-arrow-repeat"></i> Recovery Plan</div>
            <div style="padding: 1rem;">
                <div>Sessions Needed: <strong>2</strong> more sessions</div>
                <div>Target: Reach 75% eligibility</div>
                <div class="progress-bar-custom mt-2">
                    <div class="progress-fill success" style="width:75%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="course-list">
        <div
            style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
            <i class="bi bi-lightbulb"></i> Recommendations</div>
        <div style="padding: 1rem;">
            <div class="recommendation-box"><strong>⚠️ Networking (CS302)</strong><br>Your attendance is 67%. Attend next 2
                sessions to reach eligibility.</div>
            <div class="recommendation-box recommendation-success"><strong>✅ Operating Systems
                    (CS303)</strong><br>Excellent! 95% attendance. Keep it up!</div>
            <div class="recommendation-box"><strong>📖 Web Development (CS304)</strong><br>Two consecutive absences
                detected. Review missed materials.</div>
        </div>
    </div>

    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button onclick="openUniBot()"
            style="background:#800000; color:white; border:none; padding:10px 16px; border-radius:50px; font-weight:600; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.15);"><i
                class="bi bi-robot"></i> Uni Bot</button>
    </div>

    <script>
        function openUniBot() {
            alert(
                '🤖 Uni Bot: How can I help you?\n\n- What is my attendance?\n- Am I eligible for exam?\n- What is my risk level?\n- Show my recommendations');
        }
    </script>
@endsection
