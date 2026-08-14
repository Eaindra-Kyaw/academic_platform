@extends('layouts.app')

@section('title', 'Course Assessments')
@section('role', 'Student')
@section('page-title', ' Course Assessments')
@section('welcome-text', 'Provide feedback for your courses')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --warning: #f59e0b;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

            /* 🟢 PROFESSIONAL ALERT COLORS */
            --alert-bg: #f0fdf4;
            --alert-border: #bbf7d0;
            --alert-text: #166534;
            --alert-icon-bg: #dcfce7;
            --alert-icon-color: #10b981;
        }

        .assessment-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .assessment-card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .assessment-card .meta {
            font-size: 0.8rem;
            color: var(--text-gray);
            margin-bottom: 0.3rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .assessment-card .meta i {
            margin-right: 3px;
            color: var(--primary-light);
        }

        .assessment-card .title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-top: 0.1rem;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.8rem;
            border-radius: 1rem;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-pending i {
            font-size: 0.65rem;
        }

        .btn-evaluate {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }

        .btn-evaluate:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 0.5rem;
        }

        .submitted-count {
            text-align: right;
            font-size: 0.8rem;
            color: var(--text-gray);
            margin-bottom: 1rem;
        }

        /* ============================================================
               🟢 PROFESSIONAL SUCCESS ALERT BANNER (STRICTLY NO EMOJI)
               ============================================================ */
        .success-alert {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 1.25rem;
            border-radius: var(--radius);
            background: var(--white);
            border: 1px solid var(--alert-border);
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.1);
            margin-bottom: 1.5rem;
            animation: slideDownAlert 0.5s cubic-bezier(0.34, 1.56, 0.64, 1),
                fadeOutAlert 0.5s ease 6.5s forwards;
            position: relative;
            overflow: hidden;
        }

        .success-alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--success);
        }

        .success-alert .alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--alert-icon-bg);
            color: var(--alert-icon-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .success-alert .alert-content {
            flex: 1;
        }

        .success-alert .alert-content .alert-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 0.05rem;
        }

        .success-alert .alert-content .alert-message {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .success-alert .alert-close {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0.2rem;
            transition: var(--transition);
        }

        .success-alert .alert-close:hover {
            color: #334155;
            transform: rotate(90deg);
        }

        @keyframes slideDownAlert {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeOutAlert {
            to {
                opacity: 0;
                transform: translateY(-15px) scale(0.96);
            }
        }

        @media (max-width: 768px) {
            .assessment-card .title {
                font-size: 0.95rem;
            }
        }
    </style>

    {{-- 🟢 PROFESSIONAL SUCCESS ALERT (STRICTLY NO EMOJI & CACHE BUSTER) --}}
    @if (session('success'))
        {{-- Check if the string contains emojis and strip them forcefully --}}
        @php
            $cleanMessage = preg_replace('/[\x{2705}\x{1F44D}\x{1F600}-\x{1F64F}]/u', '', session('success'));
            // Remove any remaining accidental whitespace
            $cleanMessage = trim($cleanMessage);
        @endphp

        <div class="success-alert" id="successAlert">
            <div class="alert-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">Success</div>
                {{-- ✅ We output the clean message here WITHOUT any emoji --}}
                <div class="alert-message">{{ $cleanMessage }}</div>
            </div>
            <button class="alert-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div
            style="background:#fef2f2; color:#991b1b; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1rem; border-left:4px solid var(--danger); display:flex; align-items:center; gap:0.5rem;">
            <i class="bi bi-exclamation-triangle-fill" style="color: #ef4444;"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="submitted-count">
        <i class="bi bi-check-circle" style="color:var(--success);"></i>
        Completed: <strong>{{ $submittedCount ?? 0 }}</strong> assessment(s)
    </div>

    @if (isset($pendingAssessments) && $pendingAssessments->count() > 0)
        @foreach ($pendingAssessments as $assessment)
            <div class="assessment-card">
                {{-- 🟢 TOP: Meta Info with Icons --}}
                <div class="meta">
                    <span><i class="bi bi-mortarboard"></i> {{ $assessment->year }} - {{ $assessment->semester }}</span>

                    @if ($assessment->course)
                        <span><i class="bi bi-folder"></i> {{ $assessment->course->course_code ?? '' }}</span>
                    @endif

                    @if ($assessment->lecturer)
                        <span><i class="bi bi-person-vcard"></i> {{ $assessment->lecturer->name ?? '' }}</span>
                    @endif

                    @if ($assessment->closes_at)
                        <span><i class="bi bi-calendar-event"></i> Closes:
                            {{ \Carbon\Carbon::parse($assessment->closes_at)->format('d M Y') }}</span>
                    @endif
                </div>

                {{-- 🟢 BOTTOM: Assessment Name --}}
                <div class="title">{{ $assessment->name }}</div>

                <div
                    style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem; margin-top:0.5rem;">
                    {{-- 🟢 PENDING BADGE WITH ICON --}}
                    <span class="badge-pending"><i class="bi bi-clock-history"></i> Pending</span>
                    <a href="{{ route('student.assessments.show', $assessment->id) }}" class="btn-evaluate">
                        <i class="bi bi-pencil-square"></i> Start Assessment
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="bi bi-check-circle" style="color:var(--success);"></i>
            <p style="font-size:1rem; font-weight:600; color:var(--text-dark);">All assessments completed!</p>
            <p>You have no pending course assessments.</p>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('successAlert');
            if (alert) {
                setTimeout(() => {
                    alert.remove();
                }, 7000);
            }
        });
    </script>
@endsection
