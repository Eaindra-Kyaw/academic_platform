{{-- resources/views/lecturer/announcements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Announcements')
@section('role', 'Lecturer')
@section('page-title', '📢 Announcements')
@section('welcome-text', 'View system announcements')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #3B82F6;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .announcement-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1.25rem;
            transition: var(--transition);
            margin-bottom: 1rem;
            cursor: pointer;
            position: relative;
            box-shadow: var(--shadow);
        }

        .announcement-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .announcement-card .card-link {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10;
            display: block;
            text-indent: -9999px;
            overflow: hidden;
        }

        .announcement-card .card-content {
            position: relative;
            z-index: 1;
            pointer-events: none;
        }

        .announcement-card .card-content a,
        .announcement-card .card-content .read-more {
            pointer-events: auto;
            position: relative;
            z-index: 2;
        }

        .announcement-card .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .announcement-card .title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .announcement-card .title a {
            color: var(--text-dark);
            text-decoration: none;
            pointer-events: auto;
            position: relative;
            z-index: 2;
        }

        .announcement-card .title a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .announcement-card .content-preview {
            font-size: 0.85rem;
            color: #4b5563;
            margin: 0.5rem 0;
            line-height: 1.6;
        }

        .announcement-card .meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            font-size: 0.7rem;
            color: var(--text-gray);
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
            pointer-events: auto;
            position: relative;
            z-index: 2;
        }

        .badge-all {
            background: #e5e7eb;
            color: var(--text-dark);
        }

        .badge-lecturer {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-multiple {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-active {
            background: var(--success-light);
            color: #166534;
        }

        .badge-unread {
            background: var(--danger);
            color: var(--white);
            font-size: 0.5rem;
            padding: 0.1rem 0.4rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
        }

        .empty-state h4 {
            color: var(--text-dark);
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            font-size: 0.85rem;
        }

        .pagination-wrapper {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .read-more {
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            margin-top: 0.2rem;
            pointer-events: auto;
            position: relative;
            z-index: 2;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .announcement-card .header {
                flex-direction: column;
            }

            .pagination-wrapper {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

    <div class="mb-4">
        <h5 style="color: var(--text-gray); font-size: 0.85rem; font-weight: 400;">
            <i class="bi bi-info-circle"></i>
            Showing announcements from administrators
        </h5>
    </div>

    @forelse($announcements as $announcement)
        <div class="announcement-card">
            <a href="{{ route('lecturer.announcements.show', $announcement->id) }}" class="card-link"></a>

            <div class="card-content">
                <div class="header">
                    <div>
                        <h5 class="title">
                            <a href="{{ route('lecturer.announcements.show', $announcement->id) }}">
                                {{ $announcement->title }}
                            </a>
                        </h5>
                        <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-top:0.2rem;">
                            <span class="badge {{ $announcement->audience_badge_class ?? 'badge-all' }}">
                                👥 {{ $announcement->audience_label ?? 'All Users' }}
                            </span>
                            @if ($announcement->is_active)
                                <span class="badge badge-active">✅ Active</span>
                            @endif
                            @if (!$announcement->isReadBy(Auth::id()))
                                <span class="badge badge-unread">● New</span>
                            @endif
                        </div>
                    </div>
                    <small style="color:var(--text-gray); font-size:0.7rem; white-space:nowrap;">
                        {{ $announcement->created_at->diffForHumans() }}
                    </small>
                </div>

                <div class="content-preview">
                    {{ Str::limit($announcement->content, 150) }}
                    @if (strlen($announcement->content) > 150)
                        <a href="{{ route('lecturer.announcements.show', $announcement->id) }}" class="read-more">
                            Read more →
                        </a>
                    @endif
                </div>

                <div class="meta">
                    <span>
                        <i class="bi bi-person"></i>
                        {{ $announcement->creator->name ?? 'Unknown' }}
                    </span>
                    <span>
                        <i class="bi bi-calendar"></i>
                        {{ $announcement->created_at->format('d M Y, H:i') }}
                    </span>
                    @if ($announcement->published_at)
                        <span>
                            <i class="bi bi-clock"></i>
                            Published: {{ $announcement->published_at->format('d M Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="bi bi-megaphone"></i>
            <h4>No Announcements</h4>
            <p>No announcements have been published for you yet.</p>
        </div>
    @endforelse

    @if ($announcements->hasPages())
        <div class="pagination-wrapper">
            <div class="text-muted" style="font-size:0.75rem;">
                Showing {{ $announcements->firstItem() ?? 0 }} to {{ $announcements->lastItem() ?? 0 }}
                of {{ $announcements->total() }} announcements
            </div>
            <div>
                {{ $announcements->links() }}
            </div>
        </div>
    @endif
@endsection
