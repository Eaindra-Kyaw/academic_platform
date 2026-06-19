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
            max-width: 800px;
        }

        .announcement-detail .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .announcement-detail .title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .announcement-detail .meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            font-size: 0.8rem;
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .announcement-detail .content {
            font-size: 0.95rem;
            color: #1f2937;
            line-height: 1.8;
            margin: 1.5rem 0;
            white-space: pre-wrap;
        }

        .announcement-detail .footer {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            flex-wrap: wrap;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            border-radius: 1rem;
            font-size: 0.7rem;
            font-weight: 600;
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

        .btn-sm {
            padding: 0.3rem 0.8rem;
            border-radius: 0.3rem;
            font-size: 0.75rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
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

        .btn-back {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-back:hover {
            background: #e5e7eb;
        }

        @media (max-width: 768px) {
            .announcement-detail {
                padding: 1.25rem;
            }

            .announcement-detail .header {
                flex-direction: column;
            }

            .announcement-detail .meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }
    </style>

    <a href="{{ route('admin.announcements.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Announcements
    </a>

    <div class="announcement-detail">
        <div class="header">
            <div>
                <h1 class="title">{{ $announcement->title }}</h1>
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.3rem;">
                    <span class="badge badge-{{ $announcement->target_role }}">
                        👥
                        {{ ucfirst($announcement->target_role) == 'All' ? 'All Users' : ucfirst($announcement->target_role) . 's' }}
                    </span>
                    @if ($announcement->is_active)
                        <span class="badge badge-active">✅ Active</span>
                    @else
                        <span class="badge badge-inactive">❌ Inactive</span>
                    @endif
                </div>
            </div>
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="btn-sm btn-edit">
                    <i class="bi bi-pencil"></i> Edit
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
                Target:
                {{ ucfirst($announcement->target_role) == 'All' ? 'All Users' : ucfirst($announcement->target_role) . 's' }}
            </span>
        </div>

        <div class="content">
            {{ $announcement->content }}
        </div>

        <div class="footer">
            <a href="{{ route('admin.announcements.index') }}" class="btn-sm btn-back">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            <a href="{{ route('admin.announcements.edit', $announcement->id) }}" class="btn-sm btn-edit">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>
@endsection
