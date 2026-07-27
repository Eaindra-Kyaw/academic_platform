@extends('layouts.app')

@section('title', $announcement->title)
@section('role', 'Student')
@section('page-title', ' ' . $announcement->title)
@section('welcome-text', 'Announcement details')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --danger: #ef4444;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(10, 36, 99, 0.05);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.1);
            transition: var(--transition);
        }

        .back-link:hover {
            background: rgba(10, 36, 99, 0.08);
            text-decoration: none;
            color: var(--primary);
        }

        .announcement-detail {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .announcement-detail .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .announcement-detail .content {
            font-size: 0.95rem;
            color: var(--text-dark);
            line-height: 1.8;
            white-space: pre-wrap;
            margin: 1.5rem 0;
        }

        .announcement-detail .meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            font-size: 0.8rem;
            color: var(--text-gray);
            padding-top: 1rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            margin-top: 1rem;
        }

        .badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .badge-all {
            background: #e5e7eb;
            color: var(--text-dark);
        }

        .badge-student {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-multiple {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-active {
            background: #d1fae5;
            color: #166534;
        }

        @media (max-width: 768px) {
            .announcement-detail {
                padding: 1.25rem;
            }

            .announcement-detail .title {
                font-size: 1.2rem;
            }

            .announcement-detail .meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>

    <a href="{{ route('student.announcements.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Announcements
    </a>

    <div class="announcement-detail">
        <h1 class="title">{{ $announcement->title }}</h1>

        <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-bottom:0.5rem;">
            <span class="badge {{ $announcement->audience_badge_class ?? 'badge-all' }}">
                👥 {{ $announcement->audience_label ?? 'All Users' }}
            </span>
            @if ($announcement->is_active)
                <span class="badge badge-active">✅ Active</span>
            @endif
        </div>

        <div class="content">
            {{ $announcement->content }}
        </div>

        <div class="meta">
            <span>
                <i class="bi bi-person"></i>
                Posted by: {{ $announcement->creator->name ?? 'Unknown' }}
            </span>
            <span>
                <i class="bi bi-calendar"></i>
                Created: {{ $announcement->created_at->format('d M Y, H:i') }}
            </span>
            @if ($announcement->published_at)
                <span>
                    <i class="bi bi-clock"></i>
                    Published: {{ $announcement->published_at->format('d M Y, H:i') }}
                </span>
            @endif
            <span>
                <i class="bi bi-people"></i>
                Audience: {{ $announcement->audience_label ?? 'All Users' }}
            </span>
        </div>
    </div>
@endsection
