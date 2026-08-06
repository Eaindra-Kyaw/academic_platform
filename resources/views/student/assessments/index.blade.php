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

        @media (max-width: 768px) {
            .assessment-card .title {
                font-size: 0.95rem;
            }
        }
    </style>

    @if (session('success'))
        <div
            style="background:var(--success-light); color:#166534; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1rem; border-left:4px solid var(--success);">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div
            style="background:var(--danger-light); color:#991b1b; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1rem; border-left:4px solid var(--danger);">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
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
@endsection
