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
        .announcement-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 1.25rem;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .announcement-card:hover {
            border-color: #800000;
            box-shadow: 0 4px 16px rgba(128, 0, 0, 0.08);
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
        }

        .announcement-card .title a:hover {
            color: #800000;
        }

        .announcement-card .content {
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
        }

        .badge-general {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-important {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-emergency {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-academic {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-all {
            background: #e5e7eb;
            color: #374151;
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

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-inactive {
            background: #f3f4f6;
            color: #6b7280;
        }

        .badge-expired {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-sm {
            padding: 0.2rem 0.6rem;
            border-radius: 0.3rem;
            font-size: 0.7rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-edit:hover {
            background: #bfdbfe;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-delete:hover {
            background: #fecaca;
        }

        .btn-toggle {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-toggle:hover {
            background: #e5e7eb;
        }

        .actions {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
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

        .alert {
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
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
            background: #800000;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 0.5rem;
            border: none;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-create:hover {
            background: #5f0000;
            transform: translateY(-1px);
            color: white;
        }

        .pagination-wrapper {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
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
            <div class="header">
                <div>
                    <h5 class="title">
                        <a href="{{ route('admin.announcements.show', $announcement->id) }}">
                            {{ $announcement->title }}
                        </a>
                    </h5>
                    <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-top:0.2rem;">
                        <span class="badge {{ $announcement->type_badge_class }}">
                            {{ $announcement->type_label }}
                        </span>
                        <span class="badge badge-{{ $announcement->target_audience }}">
                            👥 {{ $announcement->audience_label }}
                        </span>
                        @if ($announcement->is_active && !$announcement->isExpired())
                            <span class="badge badge-active">✅ Active</span>
                        @elseif($announcement->isExpired())
                            <span class="badge badge-expired">⏰ Expired</span>
                        @else
                            <span class="badge badge-inactive">❌ Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="actions">
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

            <div class="content">
                {{ Str::limit($announcement->content, 200) }}
                @if (strlen($announcement->content) > 200)
                    <a href="{{ route('admin.announcements.show', $announcement->id) }}" style="color:#800000;">
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
                @if ($announcement->expires_at)
                    <span>
                        <i class="bi bi-hourglass"></i>
                        Expires: {{ $announcement->expires_at->format('d M Y') }}
                    </span>
                @endif
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
