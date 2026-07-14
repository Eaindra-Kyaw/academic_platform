@extends('layouts.app')

@section('title', 'Sent Messages')
@section('page-title', '📤 Sent Messages')
@section('welcome-text', 'Messages you have sent')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
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
            --info: #3b82f6;
        }

        .message-tabs {
            display: flex;
            gap: 0.25rem;
            background: var(--white);
            border-radius: 0.5rem;
            padding: 0.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .message-tab {
            padding: 0.4rem 1.2rem;
            border: none;
            background: transparent;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-gray);
            border-radius: 0.4rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .message-tab:hover {
            background: #f3f4f6;
            color: var(--text-dark);
        }

        .message-tab.active {
            background: var(--primary);
            color: var(--white);
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
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .message-item:last-child {
            border-bottom: none;
        }

        .message-item:hover {
            background: #fafbfc;
        }

        .message-item .avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .message-item .content {
            flex: 1;
            min-width: 0;
        }

        .message-item .content .top-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 0.1rem;
        }

        .message-item .content .subject {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .message-item .content .recipient {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .message-item .content .preview {
            font-size: 0.8rem;
            color: var(--text-gray);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 0.05rem;
        }

        .message-item .badge-read {
            background: var(--success);
            color: var(--white);
            font-size: 0.55rem;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            white-space: nowrap;
            font-weight: 600;
        }

        .message-item .badge-unread {
            background: var(--info);
            color: var(--white);
            font-size: 0.55rem;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            white-space: nowrap;
            font-weight: 600;
        }

        .message-item .time {
            font-size: 0.65rem;
            color: #9ca3af;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            display: block;
            margin-bottom: 0.5rem;
            color: #d1d5db;
        }

        .empty-state h5 {
            color: var(--text-dark);
            font-size: 1rem;
            margin: 0;
        }

        .empty-state p {
            font-size: 0.85rem;
            margin: 0.2rem 0 0;
        }

        @media (max-width: 768px) {
            .message-item {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .message-item .time {
                width: 100%;
                text-align: left;
                padding-left: 3.5rem;
            }

            .message-item .content .preview {
                white-space: normal;
            }

            .message-tabs {
                gap: 0.15rem;
                padding: 0.25rem;
            }

            .message-tab {
                padding: 0.3rem 0.8rem;
                font-size: 0.7rem;
            }
        }
    </style>

    <div style="max-width:900px; margin:0 auto;">
        <div class="message-tabs">
            <a href="{{ route('lecturer.messages.inbox') }}" class="message-tab">
                <i class="bi bi-inbox"></i> Inbox
            </a>
            <a href="{{ route('lecturer.messages.sent') }}" class="message-tab active">
                <i class="bi bi-send"></i> Sent
            </a>
            <a href="{{ route('lecturer.messages.compose') }}" class="message-tab"
                style="margin-left:auto; background:var(--primary); color:var(--white);">
                <i class="bi bi-plus-circle"></i> Compose
            </a>
        </div>

        <div
            style="background:var(--white); border-radius:0.75rem; border:1px solid rgba(10, 36, 99, 0.06); overflow:hidden; box-shadow:var(--shadow);">
            @if ($messages->count() > 0)
                @foreach ($messages as $message)
                    @php
                        $isRead = $message->is_read;
                        $recipient = $message->recipient;
                        $recipientInitials = strtoupper(substr($recipient->name ?? 'U', 0, 2));
                    @endphp
                    <a href="{{ route('lecturer.messages.show', $message) }}" class="message-item">
                        <div class="avatar">{{ $recipientInitials }}</div>
                        <div class="content">
                            <div class="top-row">
                                <span class="subject">{{ $message->subject ?? 'No Subject' }}</span>
                                @if ($isRead)
                                    <span class="badge-read">Read</span>
                                @else
                                    <span class="badge-unread">Unread</span>
                                @endif
                            </div>
                            <div class="recipient">To: {{ $recipient->name ?? 'Unknown' }}</div>
                            <div class="preview">{{ Str::limit($message->message, 80) }}</div>
                        </div>
                        <div class="time">{{ $message->created_at->diffForHumans() }}</div>
                    </a>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="bi bi-send"></i>
                    <h5>No Sent Messages</h5>
                    <p>You haven't sent any messages yet.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
