@extends('layouts.app')

@section('title', 'Student Dashboard | MTU Academic Intelligence')
@section('role', 'Student')
@section('page-title', 'Student Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    <div class="nav-label">Navigation</div>
    <a href="{{ route('student.dashboard') }}" class="nav-item active">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('student.courses.available') }}" class="nav-item">
        <i class="bi bi-book"></i><span>Available Courses</span>
    </a>
    <a href="{{ route('student.my.enrollments') }}" class="nav-item">
        <i class="bi bi-list-check"></i><span>My Enrollments</span>
    </a>
    <a href="{{ route('student.scan') }}" class="nav-item">
        <i class="bi bi-qr-code-scan"></i><span>QR Attendance</span>
    </a>
    <a href="{{ route('student.timetable') }}" class="nav-item">
        <i class="bi bi-calendar"></i><span>Timetable</span>
    </a>
    <a href="{{ route('student.progress') }}" class="nav-item">
        <i class="bi bi-graph-up"></i><span>My Progress</span>
    </a>
    <div class="nav-label">Support</div>
    <a href="#" class="nav-item" onclick="openUniBot()">
        <i class="bi bi-robot"></i><span>Uni Bot</span>
    </a>
    <a href="#" class="nav-item">
        <i class="bi bi-bell"></i><span>Notifications</span>
    </a>
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
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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

        .badge-critical {
            background: #fee2e2;
            color: #991b1b;
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

        .progress-fill.danger {
            background: #ef4444;
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

        .recommendation-warning {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }

        .recommendation-critical {
            background: #fef2f2;
            border-left-color: #ef4444;
        }

        .uni-bot-btn {
            background: #800000;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .uni-bot-btn:hover {
            background: #9a0000;
            transform: scale(1.02);
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

    <div>
        <!-- Welcome Card -->
        <div class="welcome-card">
            <h3>Hello, {{ Auth::user()->name }}! 👋</h3>
            <p style="font-size: 0.85rem; opacity: 0.9;">Here's your academic summary. Keep up the good work!</p>
        </div>

        <!-- Stats Grid -->
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
                <div class="progress-bar-custom mt-1">
                    <div class="progress-fill success" style="width:75%"></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number">86</div>
                <div class="stat-label">Health Score</div>
                <div class="stat-label">Stable</div>
                <div class="progress-bar-custom mt-1">
                    <div class="progress-fill success" style="width:86%"></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="font-size: 1.2rem; color: #10b981;">Eligible</div>
                <div class="stat-label">Exam Status</div>
                <div class="progress-bar-custom mt-1">
                    <div class="progress-fill success" style="width:100%"></div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="two-col">
            <!-- Academic Health Score -->
            <div class="course-list">
                <div
                    style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                    <i class="bi bi-heart-fill"></i> Academic Score
                </div>
                <div style="padding: 1rem; text-align: center;">
                    <div class="ahs-ring">
                        <div class="ahs-inner">
                            <div class="ahs-score">86</div>
                            <div style="font-size: 0.7rem;">Stable</div>
                        </div>
                    </div>
                    <div style="margin-top: 0.75rem; font-size: 0.7rem; color: #6b7280;">
                        40% Attendance | 25% Roll Call | 20% Streak | 15% Trend
                    </div>
                </div>
            </div>

            <!-- Risk & Recovery -->
            <div class="course-list">
                <div
                    style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                    <i class="bi bi-shield-exclamation"></i> Risk & Recovery
                </div>
                <div style="padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                        <span>Risk Level:</span>
                        <span class="badge-warning">Medium Risk</span>
                    </div>
                    <div class="progress-bar-custom mt-1">
                        <div class="progress-fill warning" style="width:42%"></div>
                    </div>
                    <div style="margin-top: 0.5rem; font-size: 0.8rem;">Risk Score: 42/100</div>
                    <hr style="margin: 0.75rem 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                        <span>Recovery Status:</span>
                        <span class="badge-eligible"><i class="bi bi-arrow-up"></i> Recovering</span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; margin-top: 0.5rem;">
                        <span>Streak:</span>
                        <span><strong>12</strong> consecutive sessions</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Courses -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-book-open"></i> My Courses
            </div>
            <div class="course-item">
                <div>
                    <span class="course-name">CS301 - Database Systems</span>
                    <br><small>Dr. Aye Min Thu</small>
                </div>
                <div>
                    88% <span class="badge-eligible">Eligible</span>
                    <div class="progress-bar-custom mt-1">
                        <div class="progress-fill success" style="width:88%"></div>
                    </div>
                </div>
            </div>
            <div class="course-item">
                <div>
                    <span class="course-name">CS302 - Networking</span>
                    <br><small>Dr. Kyaw Kyaw</small>
                </div>
                <div>
                    67% <span class="badge-warning">Warning</span>
                    <div class="progress-bar-custom mt-1">
                        <div class="progress-fill warning" style="width:67%"></div>
                    </div>
                </div>
            </div>
            <div class="course-item">
                <div>
                    <span class="course-name">CS303 - Operating Systems</span>
                    <br><small>Dr. Su Mon</small>
                </div>
                <div>
                    95% <span class="badge-eligible">Eligible</span>
                    <div class="progress-bar-custom mt-1">
                        <div class="progress-fill success" style="width:95%"></div>
                    </div>
                </div>
            </div>
            <div class="course-item">
                <div>
                    <span class="course-name">CS304 - Web Development</span>
                    <br><small>Dr. Thida Aung</small>
                </div>
                <div>
                    73% <span class="badge-warning">Warning</span>
                    <div class="progress-bar-custom mt-1">
                        <div class="progress-fill warning" style="width:73%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Bottom -->
        <div class="two-col">
            <!-- Peer Benchmarking -->
            <div class="course-list">
                <div
                    style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                    <i class="bi bi-bar-chart"></i> Peer Benchmarking
                </div>
                <div style="padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Your Rank:</span>
                        <span><strong>12 / 45</strong> <span style="color: #10b981;">(Top 27%)</span></span>
                    </div>
                    <hr style="margin: 0.5rem 0;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span>Course Avg:</span>
                        <span><strong>78%</strong></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                        <span>Dept Avg:</span>
                        <span><strong>79%</strong></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Uni Avg:</span>
                        <span><strong>74%</strong></span>
                    </div>
                </div>
            </div>

            <!-- Recovery Plan -->
            <div class="course-list">
                <div
                    style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                    <i class="bi bi-arrow-repeat"></i> Recovery Plan
                </div>
                <div style="padding: 1rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Sessions Needed:</span>
                        <span><strong>2</strong> more sessions</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span>Target:</span>
                        <span>Reach 75% eligibility</span>
                    </div>
                    <div class="progress-bar-custom mt-2">
                        <div class="progress-fill success" style="width:75%"></div>
                    </div>
                    <div style="margin-top: 0.5rem; font-size: 0.7rem; color: #6b7280; text-align: center;">
                        Currently at 67% in Networking
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="course-list">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-lightbulb"></i> Recommendations
            </div>
            <div style="padding: 1rem;">
                <div class="recommendation-box recommendation-warning">
                    <strong>⚠️ Networking (CS302)</strong><br>
                    Your attendance is 67%. Attend next 2 sessions to reach eligibility.
                </div>
                <div class="recommendation-box recommendation-success">
                    <strong>✅ Operating Systems (CS303)</strong><br>
                    Excellent! 95% attendance. Keep it up!
                </div>
                <div class="recommendation-box recommendation-warning">
                    <strong>📖 Web Development (CS304)</strong><br>
                    Two consecutive absences detected. Review missed materials.
                </div>
                <div class="recommendation-box recommendation-success">
                    <strong>🎯 Database Systems (CS301)</strong><br>
                    Great consistency! 88% attendance maintained.
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Uni Bot Button -->
    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button onclick="openUniBot()" class="uni-bot-btn">
            <i class="bi bi-robot"></i> Uni Bot
        </button>
    </div>

    <!-- Uni Bot Modal -->
    <div id="uniBotModal"
        style="display:none; position:fixed; bottom:80px; right:20px; width:350px; background:white; border-radius:1rem; box-shadow:0 20px 40px rgba(0,0,0,0.2); z-index:1001; overflow:hidden;">
        <div
            style="background:#800000; padding:12px 15px; color:white; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="bi bi-robot"></i> Uni Bot Assistant</span>
            <button onclick="closeUniBot()"
                style="background:none; border:none; color:white; font-size:1.2rem; cursor:pointer;">&times;</button>
        </div>
        <div style="padding:15px; max-height:400px; overflow-y:auto;">
            <div style="margin-bottom:15px;">
                <div style="background:#f3f4f6; padding:10px; border-radius:12px; margin-bottom:10px;">
                    <i class="bi bi-robot" style="color:#800000;"></i> Hello! How can I help you today?
                </div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <button onclick="askBot('attendance')"
                        style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left; cursor:pointer;">
                        📊 What is my attendance?
                    </button>
                    <button onclick="askBot('eligibility')"
                        style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left; cursor:pointer;">
                        ✅ Am I eligible for exam?
                    </button>
                    <button onclick="askBot('risk')"
                        style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left; cursor:pointer;">
                        ⚠️ What is my risk level?
                    </button>
                    <button onclick="askBot('recommendations')"
                        style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left; cursor:pointer;">
                        💡 Show my recommendations
                    </button>
                    <button onclick="askBot('healthscore')"
                        style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left; cursor:pointer;">
                        ❤️ What is my Academic Health Score?
                    </button>
                    <button onclick="askBot('timetable')"
                        style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left; cursor:pointer;">
                        📅 What is my next class?
                    </button>
                </div>
            </div>
            <div id="botResponse" style="margin-top:15px; display:none;">
                <div style="background:#80000010; padding:10px; border-radius:12px; border-left:3px solid #800000;">
                    <div id="botResponseText"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openUniBot() {
            document.getElementById('uniBotModal').style.display = 'block';
        }

        function closeUniBot() {
            document.getElementById('uniBotModal').style.display = 'none';
            document.getElementById('botResponse').style.display = 'none';
        }

        function askBot(query) {
            const responseDiv = document.getElementById('botResponse');
            const responseText = document.getElementById('botResponseText');

            let response = '';
            if (query === 'attendance') {
                response =
                    '📊 Your current attendance rate is <strong>82%</strong>. You need 75% to be eligible. Keep attending!';
            } else if (query === 'eligibility') {
                response = '✅ You are currently <strong>eligible</strong> for the exam with 82% attendance. Great job!';
            } else if (query === 'risk') {
                response =
                    '⚠️ Your risk level is <strong>Medium Risk (42/100)</strong>. Your attendance in Networking (67%) needs improvement.';
            } else if (query === 'recommendations') {
                response =
                    '💡 <strong>Recommendations:</strong><br>• Attend next 2 Networking sessions<br>• Review missed Web Development materials<br>• Keep up your excellent work in Operating Systems!';
            } else if (query === 'healthscore') {
                response =
                    '❤️ Your Academic Health Score is <strong>86 (Stable)</strong>. This is calculated from: Attendance (40%), Roll Call (25%), Attendance Streak (20%), and Engagement Trend (15%).';
            } else if (query === 'timetable') {
                response =
                    '📅 Your next class is <strong>Database Systems (CS301)</strong> with Dr. Aye Min Thu at <strong>Monday 1:00 PM</strong> in Room A-203.';
            }

            responseText.innerHTML = response;
            responseDiv.style.display = 'block';
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('uniBotModal');
            const botBtn = document.querySelector('.uni-bot-btn');
            if (modal && modal.style.display === 'block' && !modal.contains(event.target) && botBtn && !botBtn
                .contains(event.target)) {
                closeUniBot();
            }
        });
    </script>
@endsection
