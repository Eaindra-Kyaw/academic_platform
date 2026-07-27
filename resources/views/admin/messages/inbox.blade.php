@extends('layouts.app')

@section('title', 'Messages')
@section('page-title', 'Messages')
@section('welcome-text', 'View and manage your messages')

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
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .message-tabs {
            display: flex;
            gap: 0.25rem;
            background: var(--white);
            border-radius: 8px;
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
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
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

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stats-row .stat-box {
            background: var(--white);
            border-radius: 8px;
            border: 2px solid rgba(10, 36, 99, 0.06);
            padding: 0.75rem;
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .stats-row .stat-box:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(10, 36, 99, 0.08);
            transform: translateY(-2px);
        }

        .stats-row .stat-box.active {
            border-color: var(--primary);
            background: rgba(212, 160, 23, 0.04);
        }

        .stats-row .stat-box .number {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stats-row .stat-box .number.blue {
            color: var(--info);
        }

        .stats-row .stat-box .number.green {
            color: var(--success);
        }

        .stats-row .stat-box .number.orange {
            color: var(--warning);
        }

        .stats-row .stat-box .label {
            font-size: 0.6rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stats-row .stat-box .filter-badge {
            font-size: 0.5rem;
            background: #f3f4f6;
            color: var(--text-gray);
            padding: 0.05rem 0.4rem;
            border-radius: 1rem;
            margin-top: 0.1rem;
            display: inline-block;
        }

        .stats-row .stat-box.active .filter-badge {
            background: var(--primary);
            color: var(--white);
        }

        .message-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            transition: var(--transition);
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

        .message-item.unread {
            background: rgba(59, 130, 246, 0.04);
            border-left: 3px solid var(--info);
        }

        .message-item.sent-item {
            border-left: 3px solid var(--warning);
        }

        .message-item .avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
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

        .message-item .content .from {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .message-item .content .from i {
            font-size: 0.6rem;
        }

        .message-item .content .preview {
            font-size: 0.8rem;
            color: var(--text-gray);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 0.05rem;
        }

        .message-item .badge-new {
            background: var(--info);
            color: var(--white);
            font-size: 0.55rem;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            white-space: nowrap;
            font-weight: 600;
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

        .message-item .badge-sent {
            background: var(--warning);
            color: var(--white);
            font-size: 0.55rem;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            white-space: nowrap;
            font-weight: 600;
        }

        .message-item .time {
            font-size: 0.65rem;
            color: var(--text-gray);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: var(--text-gray);
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

        .empty-state .btn-reset-filter {
            display: inline-block;
            margin-top: 0.5rem;
            background: var(--primary);
            color: var(--white);
            padding: 0.3rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.8rem;
            transition: var(--transition);
        }

        .empty-state .btn-reset-filter:hover {
            background: var(--primary-light);
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

            .stats-row {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
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

        @media (max-width: 480px) {
            .message-item .avatar {
                width: 36px;
                height: 36px;
                font-size: 0.7rem;
            }

            .message-item .content .subject {
                font-size: 0.8rem;
            }

            .message-item .content .preview {
                font-size: 0.7rem;
            }

            .stats-row {
                grid-template-columns: 1fr 1fr;
            }

            .stats-row .stat-box:last-child {
                grid-column: span 2;
            }
        }
    </style>

    <div style="max-width:900px; margin:0 auto;">
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
                style="margin-left:auto; background:var(--primary); color:var(--white);">
                <i class="bi bi-plus-circle"></i> Compose
            </a>
        </div>

        @php
            $sentCount = $messages->where('sender_id', Auth::id())->count();
            $receivedCount = $messages->where('recipient_id', Auth::id())->count();
            $unreadReceived = $messages->where('recipient_id', Auth::id())->where('is_read', false)->count();
            $readReceived = $messages->where('recipient_id', Auth::id())->where('is_read', true)->count();
        @endphp

        <div class="stats-row">
            <a href="{{ route('admin.messages.inbox') }}" class="stat-box {{ !request()->get('filter') ? 'active' : '' }}">
                <div class="number">{{ $messages->count() }}</div>
                <div class="label">Total Messages</div>
                <span class="filter-badge">All</span>
            </a>
            <a href="{{ route('admin.messages.inbox', ['filter' => 'unread']) }}"
                class="stat-box {{ request()->get('filter') == 'unread' ? 'active' : '' }}">
                <div class="number blue">{{ $unreadReceived }}</div>
                <div class="label">Unread</div>
            </a>
            <a href="{{ route('admin.messages.inbox', ['filter' => 'sent']) }}"
                class="stat-box {{ request()->get('filter') == 'sent' ? 'active' : '' }}">
                <div class="number orange">{{ $sentCount }}</div>
                <div class="label">Sent</div>
            </a>
        </div>

        @php
            $filter = request()->get('filter');
            $filteredMessages = $messages;
            if ($filter == 'unread') {
                $filteredMessages = $messages->filter(function ($m) {
                    return $m->recipient_id == Auth::id() && !$m->is_read;
                });
            } elseif ($filter == 'sent') {
                $filteredMessages = $messages->filter(function ($m) {
                    return $m->sender_id == Auth::id();
                });
            }
        @endphp

        <div
            style="background:var(--white); border-radius:var(--radius); border:1px solid rgba(10, 36, 99, 0.06); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            @if ($filteredMessages->count() > 0)
                @foreach ($filteredMessages as $message)
                    @php
                        $isSent = $message->sender_id == Auth::id();
                        $otherUser = $isSent ? $message->recipient : $message->sender;
                        $isUnread = !$isSent && !$message->is_read;
                        $userName = $otherUser->name ?? 'Unknown';
                        $userInitials = strtoupper(substr($userName, 0, 2));
                    @endphp
                    <a href="{{ route('admin.messages.show', $message) }}"
                        class="message-item {{ $isUnread ? 'unread' : '' }} {{ $isSent ? 'sent-item' : '' }}">
                        <div class="avatar">{{ $userInitials }}</div>
                        <div class="content">
                            <div class="top-row">
                                <span class="subject">{{ $message->subject ?? 'No Subject' }}</span>
                                @if ($isSent)
                                    <span class="badge-sent">Sent</span>
                                @elseif($isUnread)
                                    <span class="badge-new">New</span>
                                @else
                                    <span class="badge-read">Read</span>
                                @endif
                            </div>
                            <div class="from">
                                <i class="bi bi-person"></i>
                                @if ($isSent)
                                    To: {{ $userName }}
                                @else
                                    From: {{ $userName }}
                                @endif
                            </div>
                            <div class="preview">{{ Str::limit($message->message, 80) }}</div>
                        </div>
                        <div class="time">{{ $message->created_at->diffForHumans() }}</div>
                    </a>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>No {{ $filter }} Messages</h5>
                    <p>You don't have any {{ $filter }} messages in your inbox.</p>
                    <a href="{{ route('admin.messages.inbox') }}" class="btn-reset-filter">
                        <i class="bi bi-arrow-left"></i> View all messages
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('adminUnreadBadge');
            if (badge) {
                const count = {{ $unreadReceived }};
                if (count > 0) {
                    badge.style.display = 'inline';
                    badge.textContent = count;
                }
            }
        });
    </script>
@endsection
