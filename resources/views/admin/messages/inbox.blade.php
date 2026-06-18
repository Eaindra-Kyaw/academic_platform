@extends('layouts.app')

@section('title', 'Messages')
@section('page-title', '📨 Messages')
@section('welcome-text', 'View and manage your messages')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .message-tabs {
            display: flex;
            gap: 0.25rem;
            background: white;
            border-radius: 0.5rem;
            padding: 0.25rem;
            border: 1px solid #e9edf4;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .message-tab {
            padding: 0.4rem 1.2rem;
            border: none;
            background: transparent;
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7280;
            border-radius: 0.4rem;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .message-tab:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .message-tab.active {
            background: #800000;
            color: white;
        }

        .message-tab .badge-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
        }

        .message-tab.active .badge-count {
            background: rgba(255, 255, 255, 0.25);
        }

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

        .message-item .badge-sent {
            background: #f59e0b;
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
        <!-- Tabs -->
        <div class="message-tabs">
            <a href="{{ route('admin.messages.inbox') }}" class="message-tab active">
                <i class="bi bi-inbox"></i> Inbox
                @if ($unreadCount > 0)
                    <span class="badge-count">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.messages.sent') }}" class="message-tab">
                <i class="bi bi-send"></i> Sent
            </a>
            <a href="{{ route('admin.messages.compose') }}" class="message-tab"
                style="margin-left:auto; background:#800000; color:white;">
                <i class="bi bi-plus-circle"></i> Compose
            </a>
        </div>

        <!-- Messages List -->
        <div
            style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            @if ($messages->count() > 0)
                @foreach ($messages as $message)
                    @php
                        $isSent = $message->sender_id == Auth::id();
                        $otherUser = $isSent ? $message->recipient : $message->sender;
                        $isUnread = !$isSent && !$message->is_read;
                    @endphp
                    <a href="{{ route('admin.messages.show', $message) }}"
                        style="text-decoration:none; color:inherit; display:block;"
                        class="message-item {{ $isUnread ? 'unread' : '' }}">
                        <div class="avatar">{{ substr($otherUser->name ?? 'U', 0, 2) }}</div>
                        <div class="content">
                            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                                <span class="subject">{{ $message->subject ?? 'No Subject' }}</span>
                                @if ($isSent)
                                    <span class="badge-sent">Sent</span>
                                @endif
                                @if ($isUnread)
                                    <span class="badge-unread">New</span>
                                @endif
                            </div>
                            <div class="sender">
                                @if ($isSent)
                                    To: {{ $otherUser->name ?? 'Unknown' }}
                                @else
                                    From: {{ $otherUser->name ?? 'Unknown' }}
                                @endif
                            </div>
                            <div class="preview">{{ Str::limit($message->message, 80) }}</div>
                        </div>
                        <div class="date">{{ $message->created_at->diffForHumans() }}</div>
                    </a>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No Messages</h5>
                    <p>Your inbox is empty. Send a message to a student or lecturer.</p>
                </div>
            @endif
        </div>

        <!-- Quick Stats -->
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
                    {{ $messages->where('sender_id', Auth::id())->count() }}</div>
                <div style="font-size:0.6rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.3px;">Sent</div>
            </div>
        </div>
    </div>
@endsection
