@extends('layouts.app')

@section('title', 'My Messages')
@section('page-title', '📨 My Messages')
@section('welcome-text', 'View messages from administrators and lecturers')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        .message-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .message-item:hover {
            background: #fafbfc;
        }

        .message-item.unread {
            background: #f0f7ff;
            border-left: 3px solid #3b82f6;
        }

        .message-item .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #800000, #a00000);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        .message-item .content {
            flex: 1;
            min-width: 0;
        }

        .message-item .content .subject {
            font-weight: 600;
            color: #1a2332;
            font-size: 0.9rem;
        }

        .message-item .content .sender {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .message-item .content .preview {
            font-size: 0.75rem;
            color: #6b7280;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .message-item .date {
            font-size: 0.65rem;
            color: #6b7280;
            white-space: nowrap;
        }

        .message-item .badge-unread {
            background: #3b82f6;
            color: white;
            font-size: 0.55rem;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            white-space: nowrap;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            display: block;
            margin-bottom: 0.5rem;
            color: #d1d5db;
        }

        .empty-state h5 {
            color: #374151;
            margin: 0;
        }

        .empty-state p {
            margin: 0.2rem 0 0;
        }
    </style>

    <div style="max-width:900px; margin:0 auto;">
        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
            <div>
                <h4 style="margin:0; font-weight:700; color:#1f2937; font-size:1.1rem;">
                    <i class="bi bi-envelope" style="color:#800000;"></i> My Messages
                </h4>
                <p style="font-size:0.8rem; color:#6b7280; margin:0;">
                    @if ($unreadCount > 0)
                        <span
                            style="background:#3b82f6; color:white; padding:0.1rem 0.5rem; border-radius:1rem; font-size:0.65rem;">
                            {{ $unreadCount }} unread
                        </span>
                    @endif
                    Total: {{ $messages->count() }} messages
                </p>
            </div>
        </div>

        <div
            style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            @if ($messages->count() > 0)
                @foreach ($messages as $message)
                    <a href="{{ route('student.messages.show', $message) }}"
                        style="text-decoration:none; color:inherit; display:block;"
                        class="message-item {{ $message->is_read ? '' : 'unread' }}">
                        <div class="avatar">{{ substr($message->sender->name ?? 'A', 0, 2) }}</div>
                        <div class="content">
                            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                                <span class="subject">{{ $message->subject ?? 'No Subject' }}</span>
                                @if (!$message->is_read)
                                    <span class="badge-unread">New</span>
                                @endif
                            </div>
                            <div class="sender">From: {{ $message->sender->name ?? 'Unknown' }}</div>
                            <div class="preview">{{ Str::limit($message->message, 80) }}</div>
                        </div>
                        <div class="date">{{ $message->created_at->diffForHumans() }}</div>
                    </a>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No Messages</h5>
                    <p>Your inbox is empty. Messages from administrators and lecturers will appear here.</p>
                </div>
            @endif
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1rem; margin-top:1.5rem;">
            <div
                style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:0.75rem; text-align:center;">
                <div style="font-size:1.2rem; font-weight:700; color:#800000;">{{ $messages->count() }}</div>
                <div style="font-size:0.6rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.3px;">Total Messages
                </div>
            </div>
            <div
                style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:0.75rem; text-align:center;">
                <div style="font-size:1.2rem; font-weight:700; color:#3b82f6;">{{ $unreadCount }}</div>
                <div style="font-size:0.6rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.3px;">Unread</div>
            </div>
            <div
                style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:0.75rem; text-align:center;">
                <div style="font-size:1.2rem; font-weight:700; color:#10b981;">
                    {{ $messages->where('is_read', true)->count() }}</div>
                <div style="font-size:0.6rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.3px;">Read</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('unreadBadge');
            if (badge) {
                const count = {{ $unreadCount }};
                if (count > 0) {
                    badge.style.display = 'inline';
                    badge.textContent = count;
                }
            }
        });
    </script>
@endsection
