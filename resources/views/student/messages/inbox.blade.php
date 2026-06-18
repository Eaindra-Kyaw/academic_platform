@extends('layouts.app')

@section('title', 'My Messages')
@section('page-title', '📨 My Messages')
@section('welcome-text', 'View messages from administrators and lecturers')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        /* ===== STATS ROW ===== */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stats-row .stat-box {
            background: white;
            border-radius: 0.5rem;
            border: 2px solid #e9edf4;
            padding: 0.75rem;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .stats-row .stat-box:hover {
            border-color: #800000;
            box-shadow: 0 2px 8px rgba(128, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .stats-row .stat-box.active {
            border-color: #800000;
            background: #fefce8;
        }

        .stats-row .stat-box .number {
            font-size: 1.3rem;
            font-weight: 700;
            color: #800000;
        }

        .stats-row .stat-box .number.blue {
            color: #3b82f6;
        }

        .stats-row .stat-box .number.green {
            color: #10b981;
        }

        .stats-row .stat-box .label {
            font-size: 0.6rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stats-row .stat-box .filter-badge {
            font-size: 0.5rem;
            background: #f3f4f6;
            color: #6b7280;
            padding: 0.05rem 0.4rem;
            border-radius: 1rem;
            margin-top: 0.1rem;
            display: inline-block;
        }

        .stats-row .stat-box.active .filter-badge {
            background: #800000;
            color: white;
        }

        /* ===== MESSAGE LIST ===== */
        .message-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
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
            background: #f0f7ff;
            border-left: 3px solid #3b82f6;
        }

        .message-item .avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #800000, #a00000);
            color: white;
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
            color: #1a2332;
        }

        .message-item .content .from {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .message-item .content .from i {
            font-size: 0.6rem;
        }

        .message-item .content .preview {
            font-size: 0.8rem;
            color: #6b7280;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 0.05rem;
        }

        .message-item .badge-new {
            background: #3b82f6;
            color: white;
            font-size: 0.55rem;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            white-space: nowrap;
            font-weight: 600;
        }

        .message-item .badge-read {
            background: #10b981;
            color: white;
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

        /* ===== EMPTY STATE ===== */
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
            color: #374151;
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
            background: #800000;
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 0.4rem;
            text-decoration: none;
            font-size: 0.8rem;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
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
        <!-- Header -->
        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
            <div>
                <h4 style="margin:0; font-weight:700; color:#1f2937; font-size:1.1rem;">
                    <i class="bi bi-envelope" style="color:#800000;"></i> My Messages
                </h4>
                <p style="font-size:0.8rem; color:#6b7280; margin:0;">
                    @if (request()->get('filter') == 'unread')
                        Showing <strong>unread</strong> messages
                    @elseif(request()->get('filter') == 'read')
                        Showing <strong>read</strong> messages
                    @else
                        Total: <strong>{{ $messages->count() }}</strong> messages
                    @endif
                    @if ($unreadCount > 0 && !request()->get('filter'))
                        <span
                            style="background:#3b82f6; color:white; padding:0.1rem 0.6rem; border-radius:1rem; font-size:0.65rem; margin-left:0.3rem;">
                            {{ $unreadCount }} unread
                        </span>
                    @endif
                </p>
            </div>
            @if (request()->get('filter'))
                <a href="{{ route('student.messages.inbox') }}"
                    style="font-size:0.75rem; color:#800000; text-decoration:none;">
                    <i class="bi bi-x-circle"></i> Clear filter
                </a>
            @endif
        </div>

        <!-- ===== STATS ROW (CLICKABLE FILTERS) ===== -->
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

        <!-- ===== MESSAGES LIST ===== -->
        <div
            style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
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
                        <!-- Avatar -->
                        <div class="avatar">{{ $senderInitials }}</div>

                        <!-- Content -->
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

                        <!-- Time -->
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
            // Update unread badge in sidebar
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
