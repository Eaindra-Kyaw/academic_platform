@extends('layouts.app')

@section('title', 'Student Dashboard')
@section('role', 'Student')
@section('page-title', 'Student Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
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

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-approved {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
        }

        .enrollment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f2f4;
        }

        .enrollment-item:last-child {
            border-bottom: none;
        }

        .enrollment-course {
            font-weight: 600;
            font-size: 0.8rem;
        }

        .enrollment-date {
            font-size: 0.65rem;
            color: #6b7280;
        }

        .view-all-link {
            display: block;
            text-align: center;
            padding: 0.5rem;
            background: #f9fafb;
            color: #800000;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
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

        .uni-bot-btn {
            background: #800000;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

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
        }
    </style>

    <div>
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
                <div class="stat-number">Eligible</div>
                <div class="stat-label">Exam Status</div>
            </div>
        </div>

        <div class="two-col">
            <div class="course-list">
                <div
                    style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                    <i class="bi bi-heart-fill"></i> Academic Health Score
                </div>
                <div style="padding: 1rem; text-align: center;">
                    <div class="ahs-ring">
                        <div class="ahs-inner">
                            <div class="ahs-score">86</div>
                            <div style="font-size: 0.7rem;">Stable</div>
                        </div>
                    </div>
                    <div style="margin-top: 0.75rem; font-size: 0.7rem;">40% Attendance | 25% Roll Call | 20% Streak | 15%
                        Trend</div>
                </div>
            </div>
            <div class="course-list">
                <div
                    style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                    <i class="bi bi-shield-exclamation"></i> Risk & Recovery
                </div>
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

        <!-- My Courses -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-book-open"></i> My Courses
            </div>
            <div class="course-item">
                <div><span class="course-name">CS301 - Database Systems</span><br><small>Dr. Aye Min Thu</small></div>
                <div>88% <span class="badge-eligible">Eligible</span></div>
            </div>
            <div class="course-item">
                <div><span class="course-name">CS302 - Networking</span><br><small>Dr. Kyaw Kyaw</small></div>
                <div>67% <span class="badge-warning">Warning</span></div>
            </div>
            <div class="course-item">
                <div><span class="course-name">CS303 - Operating Systems</span><br><small>Dr. Su Mon</small></div>
                <div>95% <span class="badge-eligible">Eligible</span></div>
            </div>
        </div>

        <!-- Attendance History with Notes -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-clock-history"></i> Recent Attendance
            </div>
            <div style="padding: 0.5rem 0;">
                @if (isset($attendanceRecords) && $attendanceRecords->count() > 0)
                    @foreach ($attendanceRecords as $record)
                        <div class="enrollment-item">
                            <div>
                                <div class="enrollment-course">{{ $record->session->course->course_name ?? 'N/A' }}</div>
                                <div class="enrollment-date">
                                    <i class="bi bi-calendar"></i>
                                    {{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('M d, Y h:i A') : 'N/A' }}
                                    @if ($record->is_manual)
                                        <span
                                            style="background: #dbeafe; color: #1e40af; padding: 0.1rem 0.4rem; border-radius: 10px; font-size: 0.6rem; margin-left: 0.3rem;">Manual</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="badge-{{ $record->status }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </div>
                        </div>
                        @if ($record->notes)
                            <div
                                style="padding: 0.1rem 1rem 0.5rem 1rem; font-size: 0.75rem; color: #6b7280; font-style: italic; border-bottom: 1px solid #f0f2f4;">
                                📝 {{ $record->notes }}
                            </div>
                        @endif
                    @endforeach
                    <a href="{{ route('student.attendance') }}" class="view-all-link">
                        View All Attendance <i class="bi bi-arrow-right"></i>
                    </a>
                @else
                    <div style="text-align: center; padding: 1rem; color: #9ca3af;">
                        <i class="bi bi-inbox"></i>
                        <p>No attendance records yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Enrollment History Widget (Recent Enrollments) -->
        <div class="course-list" style="margin-bottom: 1rem;">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-clock-history"></i> Recent Enrollment Activity
            </div>
            <div style="padding: 0.5rem 0;">
                @php
                    $recentEnrollments = \App\Models\Enrollment::where('student_id', Auth::id())
                        ->with('course')
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @if ($recentEnrollments->count() > 0)
                    @foreach ($recentEnrollments as $enrollment)
                        <div class="enrollment-item">
                            <div>
                                <div class="enrollment-course">{{ $enrollment->course->course_code }} -
                                    {{ $enrollment->course->course_name }}</div>
                                <div class="enrollment-date">
                                    <i class="bi bi-calendar"></i> Requested:
                                    {{ $enrollment->created_at->format('d M Y') }}
                                    @if ($enrollment->status == 'approved' && $enrollment->approved_at)
                                        | ✅ Approved:
                                        {{ \Carbon\Carbon::parse($enrollment->approved_at)->format('d M Y') }}
                                    @elseif($enrollment->status == 'rejected' && $enrollment->rejected_at)
                                        | ❌ Rejected:
                                        {{ \Carbon\Carbon::parse($enrollment->rejected_at)->format('d M Y') }}
                                    @endif
                                </div>
                            </div>
                            <div>
                                @if ($enrollment->status == 'pending')
                                    <span class="badge-pending"><i class="bi bi-clock-history"></i> Pending</span>
                                @elseif($enrollment->status == 'approved')
                                    <span class="badge-approved"><i class="bi bi-check-circle"></i> Approved</span>
                                @else
                                    <span class="badge-rejected"><i class="bi bi-x-circle"></i> Rejected</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <a href="{{ route('student.my.enrollments') }}" class="view-all-link">
                        View All Enrollments <i class="bi bi-arrow-right"></i>
                    </a>
                @else
                    <div style="text-align: center; padding: 1rem; color: #9ca3af;">
                        <i class="bi bi-inbox"></i>
                        <p>No enrollment requests yet</p>
                        <a href="{{ route('student.courses.available') }}" class="btn-sm"
                            style="background: #800000; color: white; padding: 0.3rem 0.8rem; border-radius: 0.5rem; text-decoration: none;">Browse
                            Courses</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="course-list">
            <div
                style="padding: 0.75rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #800000;">
                <i class="bi bi-lightbulb"></i> Recommendations
            </div>
            <div style="padding: 1rem;">
                <div class="recommendation-box"><strong>⚠️ Networking (CS302)</strong><br>Your attendance is 67%. Attend
                    next 2 sessions to reach eligibility.</div>
                <div class="recommendation-box recommendation-success"><strong>✅ Operating Systems
                        (CS303)</strong><br>Excellent! 95% attendance. Keep it up!</div>
            </div>
        </div>
    </div>

    <div style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button onclick="openUniBot()" class="uni-bot-btn">
            <i class="bi bi-robot"></i> Uni Bot
        </button>
    </div>

    <div id="uniBotModal"
        style="display:none; position:fixed; bottom:80px; right:20px; width:350px; background:white; border-radius:1rem; box-shadow:0 20px 40px rgba(0,0,0,0.2); z-index:1001; overflow:hidden;">
        <div
            style="background:#800000; padding:12px 15px; color:white; display:flex; justify-content:space-between; align-items:center;">
            <span><i class="bi bi-robot"></i> Uni Bot Assistant</span>
            <button onclick="closeUniBot()"
                style="background:none; border:none; color:white; font-size:1.2rem; cursor:pointer;">&times;</button>
        </div>
        <div style="padding:15px; max-height:400px; overflow-y:auto;">
            <div style="background:#f3f4f6; padding:10px; border-radius:12px; margin-bottom:10px;">
                <i class="bi bi-robot" style="color:#800000;"></i> Hello! How can I help you?
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <button onclick="askBot('attendance')"
                    style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left;">📊
                    What is my attendance?</button>
                <button onclick="askBot('eligibility')"
                    style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left;">✅
                    Am I eligible for exam?</button>
                <button onclick="askBot('risk')"
                    style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left;">⚠️
                    What is my risk level?</button>
                <button onclick="askBot('recommendations')"
                    style="background:#f8f9fa; border:1px solid #e5e7eb; padding:8px; border-radius:8px; text-align:left;">💡
                    Show my recommendations</button>
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
            let response = '';
            if (query === 'attendance') response = '📊 Your current attendance rate is <strong>82%</strong>.';
            else if (query === 'eligibility') response = '✅ You are currently <strong>eligible</strong> for the exam.';
            else if (query === 'risk') response = '⚠️ Your risk level is <strong>Medium Risk (42/100)</strong>.';
            else if (query === 'recommendations') response =
                '💡 <strong>Recommendations:</strong><br>• Attend next 2 Networking sessions<br>• Keep up your excellent work in Operating Systems!';
            document.getElementById('botResponseText').innerHTML = response;
            document.getElementById('botResponse').style.display = 'block';
        }
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
