@extends('layouts.app')

@section('title', $message->subject ?? 'Message')
@section('page-title', '📄 Message')
@section('welcome-text', 'View message details')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .message-detail-box {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e9edf4;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .message-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1a2332;
            margin: 0;
        }

        .message-header .meta {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .sender-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background: #fafbfc;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .sender-info .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #800000, #a00000);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.7rem;
            flex-shrink: 0;
        }

        .sender-info .name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #1a2332;
        }

        .sender-info .email {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .message-body {
            padding: 1rem 0;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 1.5rem;
        }

        .message-body p {
            font-size: 0.95rem;
            color: #1a2332;
            line-height: 1.6;
            white-space: pre-wrap;
            margin: 0;
        }

        .message-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .btn-reply {
            background: #800000;
            color: white;
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-reply:hover {
            background: #a00000;
            transform: translateY(-1px);
        }

        .btn-back {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: #e5e7eb;
        }
    </style>

    <div class="message-detail-box">
        <div class="message-header">
            <h3>{{ $message->subject ?? 'No Subject' }}</h3>
            <div class="meta">
                <i class="bi bi-clock"></i> {{ $message->created_at->format('F j, Y g:i A') }}
                @if (!$message->is_read && $message->recipient_id == Auth::id())
                    <span
                        style="background:#3b82f6; color:white; font-size:0.6rem; padding:0.1rem 0.6rem; border-radius:1rem; margin-left:0.5rem;">New</span>
                @endif
            </div>
        </div>

        @php
            $isSent = $message->sender_id == Auth::id();
            $otherUser = $isSent ? $message->recipient : $message->sender;
        @endphp

        <div class="sender-info">
            <div class="avatar">{{ substr($otherUser->name ?? 'U', 0, 2) }}</div>
            <div>
                <div class="name">
                    @if ($isSent)
                        To: {{ $otherUser->name ?? 'Unknown' }}
                    @else
                        From: {{ $otherUser->name ?? 'Unknown' }}
                    @endif
                </div>
                <div class="email">{{ $otherUser->email ?? '' }}</div>
            </div>
        </div>

        <div class="message-body">
            <p>{{ $message->message }}</p>
        </div>

        <div class="message-actions">
            <a href="{{ route('admin.messages.inbox') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Back to Inbox
            </a>
            @if (!$isSent)
                <a href="{{ route('admin.messages.compose') }}?recipient={{ $otherUser->id }}" class="btn-reply">
                    <i class="bi bi-reply"></i> Reply
                </a>
            @endif
        </div>
    </div>
@endsection
