{{-- resources/views/student/announcements/show.blade.php --}}
@extends('layouts.app')

@section('title', $announcement->title)
@section('role', 'Student')
@section('page-title', '📢 ' . $announcement->title)
@section('welcome-text', 'Announcement details')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #800000;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: #fef7f7;
            border-radius: 0.5rem;
            border: 1px solid #fde2e2;
            transition: all 0.2s;
        }

        .back-link:hover {
            background: #fde2e2;
            text-decoration: none;
            color: #800000;
        }

        .announcement-detail {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 2rem;
        }

        .announcement-detail .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .announcement-detail .content {
            font-size: 0.95rem;
            color: #1f2937;
            line-height: 1.8;
            white-space: pre-wrap;
            margin: 1.5rem 0;
        }

        .announcement-detail .meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            font-size: 0.8rem;
            color: #6b7280;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
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
            color: #374151;
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
            background: #dcfce7;
            color: #166534;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            border-radius: 0.3rem;
            font-size: 0.75rem;
            font-weight: 500;
            background: #f3f4f6;
            color: #374151;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #e5e7eb;
            color: #374151;
            text-decoration: none;
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
