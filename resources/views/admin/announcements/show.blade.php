{{-- resources/views/admin/announcements/show.blade.php --}}
@extends('layouts.app')

@section('title', $announcement->title)
@section('role', 'Admin')
@section('page-title', '📢 ' . $announcement->title)
@section('welcome-text', 'Announcement details')

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
            background: rgba(212, 160, 23, 0.08);
            border-radius: var(--radius);
            border: 1px solid rgba(212, 160, 23, 0.15);
            transition: var(--transition);
        }

        .back-link:hover {
            background: rgba(212, 160, 23, 0.15);
            text-decoration: none;
            color: var(--primary-dark);
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

        .announcement-detail .actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
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
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: var(--transition);
            text-decoration: none;
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

        .btn-back {
            background: #f3f4f6;
            color: var(--text-dark);
        }

        .btn-back:hover {
            background: #e5e7eb;
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

            .announcement-detail .actions {
                flex-direction: column;
            }

            .btn-sm {
                justify-content: center;
            }
        }
    </style>

    <a href="{{ route('admin.announcements.index') }}" class="back-link">
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
            @else
                <span class="badge badge-inactive">❌ Inactive</span>
            @endif
            @if (!$announcement->isReadBy(Auth::id()))
                <span class="badge badge-unread">● New</span>
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
            <a href="{{ route('admin.announcements.index') }}" class="btn-sm btn-back">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
@endsection
