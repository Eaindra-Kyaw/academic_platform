@extends('layouts.app')

@section('title', 'My Messages')
@section('page-title', ' My Messages')
@section('welcome-text', 'View messages from administrators and lecturers')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
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
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stats-row .stat-box {
            background: var(--white);
            border-radius: 0.5rem;
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
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .stats-row .stat-box.active {
            border-color: var(--primary);
            background: #f8f9fc;
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
            border-bottom: 1px solid #f1f5f9;
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .message-item:last-child {
            border-bottom: none;
        }

        .message-item:hover {
            background: var(--bg-main);
        }

        .message-item.unread {
            background: #f0f7ff;
            border-left: 3px solid var(--info);
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

        .empty-state .btn-reset-filter {
            display: inline-block;
            margin-top: 0.5rem;
            background: var(--primary);
            color: var(--white);
            padding: 0.3rem 1rem;
            border-radius: 0.4rem;
            text-decoration: none;
            font-size: 0.8rem;
            transition: var(--transition);
        }

        .empty-state .btn-reset-filter:hover {
            background: var(--primary-dark);
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
        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
            <div>
                <h4 style="margin:0; font-weight:700; color:var(--text-dark); font-size:1.1rem;">
                    <i class="bi bi-envelope" style="color:var(--primary);"></i> My Messages
                </h4>
                <p style="font-size:0.8rem; color:var(--text-gray); margin:0;">
                    @if (request()->get('filter') == 'unread')
                        Showing <strong>unread</strong> messages
                    @elseif(request()->get('filter') == 'read')
                        Showing <strong>read</strong> messages
                    @else
                        Total: <strong>{{ $messages->count() }}</strong> messages
                    @endif
                    @if ($unreadCount > 0 && !request()->get('filter'))
                        <span
                            style="background:var(--info); color:var(--white); padding:0.1rem 0.6rem; border-radius:1rem; font-size:0.65rem; margin-left:0.3rem;">
                            {{ $unreadCount }} unread
                        </span>
                    @endif
                </p>
            </div>
            @if (request()->get('filter'))
                <a href="{{ route('student.messages.inbox') }}"
                    style="font-size:0.75rem; color:var(--primary); text-decoration:none;">
                    <i class="bi bi-x-circle"></i> Clear filter
                </a>
            @endif
        </div>

        <div class="stats-row">
            <a href="{{ route('student.messages.inbox') }}"
                class="stat-box {{ !request()->get('filter') ? 'active' : '' }}">
                <div class="number">{{ $messages->count() }}</div>
                <div class="label">Total</div>
                <span class="filter-badge">All</span>
            </a>
            <a href="{{ route('student.messages.inbox', ['filter' => 'unread']) }}"
                class="stat-box {{ request()->get('filter') == 'unread' ? 'active' : '' }}">
                <div class="number blue">{{ $unreadCount }}</div>
                <div class="label">Unread</div>
                <span class="filter-badge">Filter</span>
            </a>
            <a href="{{ route('student.messages.inbox', ['filter' => 'read']) }}"
                class="stat-box {{ request()->get('filter') == 'read' ? 'active' : '' }}">
                <div class="number green">{{ $messages->where('is_read', true)->count() }}</div>
                <div class="label">Read</div>
                <span class="filter-badge">Filter</span>
            </a>
        </div>

        <div
            style="background:var(--white); border-radius:0.75rem; border:1px solid rgba(10, 36, 99, 0.06); overflow:hidden; box-shadow:var(--shadow);">
            @php
                $filter = request()->get('filter');
                $filteredMessages = $messages;
                if ($filter == 'unread') {
                    $filteredMessages = $messages->filter(function ($m) {
                        return !$m->is_read;
                    });
                } elseif ($filter == 'read') {
                    $filteredMessages = $messages->filter(function ($m) {
                        return $m->is_read;
                    });
                }
            @endphp

            @if ($filteredMessages->count() > 0)
                @foreach ($filteredMessages as $message)
                    @php
                        $sender = $message->sender;
                        $isUnread = !$message->is_read;
                        $senderName = $sender->name ?? 'Unknown';
                        $senderInitials = strtoupper(substr($senderName, 0, 2));
                    @endphp
                    <a href="{{ route('student.messages.show', $message) }}"
                        class="message-item {{ $isUnread ? 'unread' : '' }}">
                        <div class="avatar">{{ $senderInitials }}</div>
                        <div class="content">
                            <div class="top-row">
                                <span class="subject">{{ $message->subject ?? 'No Subject' }}</span>
                                @if ($isUnread)
                                    <span class="badge-new">New</span>
                                @else
                                    <span class="badge-read">Read</span>
                                @endif
                            </div>
                            <div class="from">
                                <i class="bi bi-person"></i>
                                {{ $senderName }}
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
                    <a href="{{ route('student.messages.inbox') }}" class="btn-reset-filter">
                        <i class="bi bi-arrow-left"></i> View all messages
                    </a>
                </div>
            @endif
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
