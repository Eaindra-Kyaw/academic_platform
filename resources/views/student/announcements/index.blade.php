{{-- resources/views/student/announcements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Announcements')
@section('role', 'Student')
@section('page-title', '📢 Announcements')
@section('welcome-text', 'View system announcements')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        .announcement-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 1.25rem;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
            cursor: pointer;
            position: relative;
        }

        .announcement-card:hover {
            border-color: #800000;
            box-shadow: 0 4px 16px rgba(128, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        /* The clickable overlay - covers the entire card */
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

        /* Everything inside the card should be above the overlay but still clickable */
        .announcement-card .card-content {
            position: relative;
            z-index: 1;
            pointer-events: none;
        }

        /* But we want links inside to still work, so we give them pointer-events: auto */
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
            color: #1f2937;
            margin: 0;
        }

        .announcement-card .title a {
            color: #1f2937;
            text-decoration: none;
            pointer-events: auto;
            position: relative;
            z-index: 2;
        }

        .announcement-card .title a:hover {
            color: #800000;
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
            color: #6b7280;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid #f3f4f6;
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

        .badge-unread {
            background: #ef4444;
            color: white;
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
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
        }

        .empty-state h4 {
            color: #374151;
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
            color: #800000;
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
        <h5 style="color: #6b7280; font-size: 0.85rem; font-weight: 400;">
            <i class="bi bi-info-circle"></i>
            Showing announcements from administrators
        </h5>
    </div>

    {{-- Announcements List --}}
    @forelse($announcements as $announcement)
        <div class="announcement-card">
            {{-- Full card clickable link - covers the entire card --}}
            <a href="{{ route('student.announcements.show', $announcement->id) }}" class="card-link"></a>

            <div class="card-content">
                <div class="header">
                    <div>
                        <h5 class="title">
                            <a href="{{ route('student.announcements.show', $announcement->id) }}">
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
                    <small style="color:#9ca3af; font-size:0.7rem; white-space:nowrap;">
                        {{ $announcement->created_at->diffForHumans() }}
                    </small>
                </div>

                <div class="content-preview">
                    {{ Str::limit($announcement->content, 150) }}
                    @if (strlen($announcement->content) > 150)
                        <a href="{{ route('student.announcements.show', $announcement->id) }}" class="read-more">
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

    {{-- Pagination --}}
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
