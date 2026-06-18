@extends('layouts.app')

@section('title', $message->subject ?? 'Message')
@section('page-title', '📄 Message')
@section('welcome-text', 'View message details')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
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

        .message-detail-box .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: #6b7a8f;
            text-decoration: none;
            font-size: 0.8rem;
            margin-bottom: 1.25rem;
            transition: color 0.2s;
        }

        .message-detail-box .back-link:hover {
            color: #800000;
        }

        .message-detail-box .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .message-detail-box .header h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1a2332;
            margin: 0;
        }

        .message-detail-box .header .meta {
            font-size: 0.7rem;
            color: #6b7280;
            text-align: right;
        }

        .message-detail-box .sender-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            background: #fafbfc;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .message-detail-box .sender-info .avatar {
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

        .message-detail-box .sender-info .name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #1a2332;
        }

        .message-detail-box .sender-info .email {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .message-detail-box .body {
            padding: 1rem 0;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 1.25rem;
        }

        .message-detail-box .body p {
            font-size: 0.95rem;
            color: #1a2332;
            line-height: 1.7;
            white-space: pre-wrap;
            margin: 0;
        }

        .message-detail-box .actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .message-detail-box .actions .btn-back {
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

        .message-detail-box .actions .btn-back:hover {
            background: #e5e7eb;
        }

        .message-detail-box .actions .btn-reply {
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

        .message-detail-box .actions .btn-reply:hover {
            background: #a00000;
        }
    </style>

    <div class="message-detail-box">
        <a href="{{ route('lecturer.messages.inbox') }}" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Inbox
        </a>

        @php
            $isSent = $message->sender_id == Auth::id();
            $otherUser = $isSent ? $message->recipient : $message->sender;
            $userName = $otherUser->name ?? 'Unknown';
            $userInitials = strtoupper(substr($userName, 0, 2));
        @endphp

        <div class="header">
            <h3>{{ $message->subject ?? 'No Subject' }}</h3>
            <div class="meta">
                <div>{{ $message->created_at->format('F j, Y g:i A') }}</div>
            </div>
        </div>

        <div class="sender-info">
            <div class="avatar">{{ $userInitials }}</div>
            <div>
                <div class="name">
                    @if ($isSent)
                        To: {{ $userName }}
                    @else
                        From: {{ $userName }}
                    @endif
                </div>
                <div class="email">{{ $otherUser->email ?? '' }}</div>
            </div>
        </div>

        <div class="body">
            <p>{{ $message->message }}</p>
        </div>

        <div class="actions">
            <a href="{{ route('lecturer.messages.inbox') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Back to Inbox
            </a>
            @if (!$isSent)
                <a href="{{ route('lecturer.messages.compose') }}?recipient={{ $otherUser->id }}" class="btn-reply">
                    <i class="bi bi-reply"></i> Reply
                </a>
            @endif
        </div>
    </div>
@endsection
