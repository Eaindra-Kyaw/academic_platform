{{-- resources/views/admin/announcements/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Announcements')
@section('role', 'Admin')
@section('page-title', '📢 Announcements')
@section('welcome-text', 'Manage system announcements')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
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
            z-index: 5;
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
        .announcement-card .card-content .actions a,
        .announcement-card .card-content .actions button,
        .announcement-card .card-content .read-more {
            pointer-events: auto;
            position: relative;
            z-index: 6;
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
            z-index: 6;
        }

        .announcement-card .title a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .announcement-card .content-preview {
            font-size: 0.85rem;
            color: #4b5563;
            margin: 0.5rem 0;
            line-height: 1.5;
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
            z-index: 6;
        }

        .badge-all {
            background: #e5e7eb;
            color: var(--text-dark);
        }

        .badge-admin {
            background: #fce7f3;
            color: #9d174d;
        }

        .badge-lecturer {
            background: #e0e7ff;
            color: #3730a3;
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

        .badge-inactive {
            background: #f3f4f6;
            color: var(--text-gray);
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

        .btn-sm {
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            transition: var(--transition);
            text-decoration: none;
            pointer-events: auto;
            position: relative;
            z-index: 6;
        }

        .btn-edit {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-edit:hover {
            background: #bfdbfe;
        }

        .btn-delete {
            background: var(--danger-light);
            color: var(--danger);
        }

        .btn-delete:hover {
            background: #fca5a5;
        }

        .btn-toggle {
            background: #f3f4f6;
            color: var(--text-dark);
        }

        .btn-toggle:hover {
            background: #e5e7eb;
        }

        .btn-view {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-view:hover {
            background: #bfdbfe;
        }

        .actions {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
            pointer-events: none;
        }

        .actions a,
        .actions button {
            pointer-events: auto;
            position: relative;
            z-index: 6;
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

        .alert {
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: var(--success-light);
            color: #166534;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: var(--danger-light);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-dismissible {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-close-alert {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
            padding: 0 0.3rem;
            opacity: 0.7;
        }

        .btn-close-alert:hover {
            opacity: 1;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .btn-create {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            border: none;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
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
            pointer-events: auto;
            position: relative;
            z-index: 6;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .announcement-card .header {
                flex-direction: column;
            }

            .top-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-create {
                justify-content: center;
            }

            .pagination-wrapper {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    {{-- Top Bar --}}
    <div class="top-bar">
        <div>
            <span class="text-muted" style="font-size:0.85rem;">
                Total: {{ $announcements->total() }} announcements
            </span>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="btn-create">
            <i class="bi bi-plus-circle"></i> New Announcement
        </a>
    </div>

    {{-- Announcements List --}}
    @forelse($announcements as $announcement)
        <div class="announcement-card">
            <a href="{{ route('admin.announcements.show', $announcement->id) }}" class="card-link"></a>

            <div class="card-content">
                <div class="header">
                    <div>
                        <h5 class="title">
                            <a href="{{ route('admin.announcements.show', $announcement->id) }}">
                                {{ $announcement->title }}
                            </a>
                        </h5>
                        <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-top:0.2rem;">
                            <span class="badge {{ $announcement->audience_badge_class ?? 'badge-all' }}">
                                👥 {{ $announcement->audience_label ?? 'All Users' }}
                            </span>
                            @if ($announcement->is_active)
                                <span class="badge badge-active">✅ Active</span>
                            @else
                                <span class="badge badge-inactive">❌ Inactive</span>
                            @endif
                            @if (!$announcement->isReadBy(Auth::id()))
                                <span class="badge badge-unread">● New</span>
                            @endif
                        </div>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.announcements.show', $announcement->id) }}" class="btn-sm btn-view">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="btn-sm btn-edit">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('admin.announcements.toggle', $announcement->id) }}" class="btn-sm btn-toggle"
                            onclick="return confirm('Toggle announcement status?')">
                            @if ($announcement->is_active)
                                <i class="bi bi-eye-slash"></i> Deactivate
                            @else
                                <i class="bi bi-eye"></i> Activate
                            @endif
                        </a>
                        <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST"
                            style="display:inline;" onsubmit="return confirm('Delete this announcement?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-sm btn-delete">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>

                <div class="content-preview">
                    {{ Str::limit($announcement->content, 200) }}
                    @if (strlen($announcement->content) > 200)
                        <a href="{{ route('admin.announcements.show', $announcement->id) }}" class="read-more">
                            Read more
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
            <p>No announcements have been created yet.</p>
            <a href="{{ route('admin.announcements.create') }}" class="btn-create" style="margin-top:1rem;">
                <i class="bi bi-plus-circle"></i> Create First Announcement
            </a>
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
