{{-- resources/views/student/announcements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Announcements')
@section('role', 'Student')
@section('page-title', '📢 Announcements')
@section('welcome-text', 'Stay updated with the latest announcements')

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
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .announcement-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .announcement-item {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1.25rem 1.5rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .announcement-item:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        .announcement-item .title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.3rem;
        }

        .announcement-item .excerpt {
            font-size: 0.85rem;
            color: var(--text-gray);
            margin-bottom: 0.5rem;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .announcement-item .meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.7rem;
            color: var(--text-gray);
            align-items: center;
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
            background: var(--success-light);
            color: #166534;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .announcement-item {
                padding: 1rem;
            }

            .announcement-item .title {
                font-size: 0.9rem;
            }
        }
    </style>

    <div>
        @if ($announcements->count() > 0)
            <div class="announcement-list">
                @foreach ($announcements as $announcement)
                    <a href="{{ route('student.announcements.show', $announcement) }}" class="announcement-item">
                        <div class="title">{{ $announcement->title }}</div>
                        <div class="excerpt">{{ Str::limit($announcement->content, 150) }}</div>
                        <div class="meta">
                            <span>
                                <i class="bi bi-person"></i>
                                {{ $announcement->creator->name ?? 'Unknown' }}
                            </span>
                            <span>
                                <i class="bi bi-calendar"></i>
                                {{ $announcement->created_at->format('d M Y') }}
                            </span>
                            <span class="badge {{ $announcement->audience_badge_class ?? 'badge-all' }}">
                                👥 {{ $announcement->audience_label ?? 'All Users' }}
                            </span>
                            @if ($announcement->is_active)
                                <span class="badge badge-active">✅ Active</span>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($announcements->hasPages())
                <div class="mt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="bi bi-megaphone"></i>
                <p>No announcements available at the moment.</p>
            </div>
        @endif
    </div>
@endsection
