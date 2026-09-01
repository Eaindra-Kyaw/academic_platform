@extends('layouts.app')

@section('title', 'Assessment Results')
@section('role', 'Admin')
@section('page-title', 'Evaluation Results')
@section('welcome-text', $assessment->name)

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --bg-main: #F0F4F8;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 2px 10px rgba(10, 36, 99, 0.06);
            --shadow-hover: 0 8px 25px rgba(10, 36, 99, 0.12);
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--bg-main);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.85rem;
            padding: 0.4rem 1rem;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.08);
            border-radius: 8px;
            transition: var(--transition);
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .back-link:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-4px);
        }

        .results-header {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .results-header h2 {
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.2rem;
        }

        .results-header .subtitle {
            margin: 0.2rem 0 0;
            color: var(--text-gray);
            font-size: 0.85rem;
        }

        .results-header .subtitle .lecturer-name {
            color: var(--primary);
            font-weight: 500;
        }

        .btn-export {
            background: var(--success);
            color: var(--white);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-export:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.2rem 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.04);
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1.2;
        }

        .stat-number.green {
            color: var(--success);
        }

        .stat-number.orange {
            color: var(--warning);
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.2rem;
            font-weight: 600;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .section-header h4 {
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .question-card {
            background: var(--white);
            border-radius: var(--radius);
            border-left: 5px solid var(--primary);
            padding: 1.2rem 1.5rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .question-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .question-card .q-text {
            flex: 1;
            min-width: 200px;
            font-weight: 500;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .question-card .q-text .q-number {
            color: var(--primary);
            font-weight: 700;
            margin-right: 0.3rem;
        }

        .scale-distribution {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            min-width: 250px;
        }

        .scale-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 32px;
        }

        .scale-item .num {
            font-size: 0.6rem;
            font-weight: 600;
            color: var(--text-gray);
            margin-bottom: 2px;
        }

        .scale-item .bar-track {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .scale-item .bar-track .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.6s ease;
        }

        .scale-item .bar-track .fill.c1 {
            background: var(--danger);
        }

        .scale-item .bar-track .fill.c2 {
            background: #f97316;
        }

        .scale-item .bar-track .fill.c3 {
            background: var(--warning);
        }

        .scale-item .bar-track .fill.c4 {
            background: #84cc16;
        }

        .scale-item .bar-track .fill.c5 {
            background: var(--success);
        }

        .scale-item .count {
            font-size: 0.6rem;
            color: var(--text-gray);
            margin-top: 2px;
        }

        .text-responses {
            margin-top: 0.8rem;
            width: 100%;
            padding-top: 0.8rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .text-responses .response-item {
            background: #f8fafc;
            padding: 0.6rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            color: var(--text-dark);
            border-left: 2px solid var(--info);
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .text-responses .response-item i {
            color: var(--info);
            margin-top: 0.15rem;
        }

        .text-responses .empty-text {
            color: var(--text-gray);
            font-size: 0.8rem;
            font-style: italic;
        }

        .empty-state-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: var(--shadow);
            border: 1px dashed #d1d5db;
        }

        .empty-state-box i {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state-box h4 {
            color: var(--text-dark);
            margin: 0 0 0.3rem;
        }

        .empty-state-box p {
            color: var(--text-gray);
            font-size: 0.9rem;
            margin: 0;
        }

        @media (max-width: 992px) {
            .question-card {
                flex-direction: column;
                align-items: stretch;
            }
        }

        @media (max-width: 576px) {
            .results-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .btn-export {
                width: 100%;
                justify-content: center;
            }

            .scale-distribution {
                justify-content: space-around;
            }
        }
    </style>

    <a href="{{ route('admin.assessments.dashboard') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>

    {{-- Header Section --}}
    <div class="results-header">
        <div>
            <h2>{{ $assessment->name }}</h2>
            <p class="subtitle">
                {{ $assessment->year }} - {{ $assessment->semester }}
                @if ($assessment->course)
                    • {{ $assessment->course->course_code }}
                @endif
                {{-- ✅ Only show lecturer if exists --}}
                @if (isset($lecturerName) && $lecturerName)
                    • <span class="lecturer-name">{{ $lecturerName }}</span>
                @endif
            </p>
        </div>
        <div>
            <a href="{{ route('admin.assessments.export', $assessment->id) }}" class="btn-export">
                <i class="bi bi-download"></i> Export
            </a>
        </div>
    </div>

    {{-- Check if there are submissions to show --}}
    @if ($stats['total_submissions'] > 0)

        {{-- Top Statistics --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_submissions'] }}</div>
                <div class="stat-label">Total Submissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['unique_students'] }}</div>
                <div class="stat-label">Submitted Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number green">{{ number_format($stats['overall_average'], 2) }}</div>
                <div class="stat-label">Overall Average (out of 5)</div>
            </div>
        </div>

        {{-- Questions Breakdown --}}
        <div class="section-header">
            <h4>Student Feedbacks</h4>
        </div>

        @foreach ($questionResults as $result)
            @php
                $question = $result['question'];
                $avg = $result['average'];
                $count = $result['count'];
                $distribution = $result['distribution'] ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
                $maxCount = max($distribution);
            @endphp

            <div class="question-card"
                style="border-left-color: {{ $question->type === 'text' ? 'var(--info)' : 'var(--primary)' }};">

                {{-- Question Text --}}
                <div class="q-text">
                    <span class="q-number">{{ $question->order }}.</span>
                    {{ $question->question_text }}
                    @if ($question->type === 'text')
                        <span
                            style="font-size:0.65rem; background:#e0e7ff; color:var(--primary); padding:0.1rem 0.5rem; border-radius:1rem; margin-left:0.3rem; font-weight:600;">
                            Comments
                        </span>
                    @endif
                </div>

                {{-- Rating Scale Distribution --}}
                @if ($question->type !== 'text')
                    <div class="scale-distribution">
                        @for ($i = 1; $i <= 5; $i++)
                            @php
                                $val = $distribution[$i] ?? 0;
                                $percentage = $maxCount > 0 ? round(($val / $maxCount) * 100) : 0;
                            @endphp
                            <div class="scale-item">
                                <span class="num">{{ $i }}</span>
                                <div class="bar-track">
                                    <div class="fill c{{ $i }}" style="width: {{ $percentage }}%;"></div>
                                </div>
                                <span class="count">{{ $val }}</span>
                            </div>
                        @endfor
                    </div>
                @endif

                {{-- Text Responses --}}
                @if ($question->type === 'text')
                    <div class="text-responses">
                        @if (count($result['text_responses']) > 0)
                            @foreach ($result['text_responses'] as $response)
                                <div class="response-item">
                                    <i class="bi bi-chat-square-text-fill"></i>
                                    {{ $response }}
                                </div>
                            @endforeach
                        @else
                            <div class="empty-text">No text responses submitted for this question.</div>
                        @endif
                    </div>
                @endif

            </div>
        @endforeach
    @else
        {{-- Empty State --}}
        <div class="empty-state-box">
            <i class="bi bi-inbox"></i>
            <h4>No Submissions Yet</h4>
            <p>Students haven't submitted any feedback for this assessment yet.<br>Check back later to view detailed
                results.</p>
        </div>
    @endif

@endsection
