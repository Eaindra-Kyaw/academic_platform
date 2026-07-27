@extends('layouts.app')

@section('title', $message->subject ?? 'Message')
@section('page-title', ' Message')
@section('welcome-text', 'View message details')

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
        }

        .message-detail-box {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .message-detail-box .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.8rem;
            margin-bottom: 1.25rem;
            transition: all 0.3s ease;
        }

        .message-detail-box .back-link:hover {
            color: var(--primary);
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
            color: var(--text-dark);
            margin: 0;
        }

        .message-detail-box .header .meta {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-align: right;
        }

        .message-detail-box .sender-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1rem;
            background: var(--bg-main);
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .message-detail-box .sender-info .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
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
            color: var(--text-dark);
        }

        .message-detail-box .sender-info .email {
            font-size: 0.7rem;
            color: var(--text-gray);
        }

        .message-detail-box .body {
            padding: 1rem 0;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 1.25rem;
        }

        .message-detail-box .body p {
            font-size: 0.95rem;
            color: var(--text-dark);
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
            transition: all 0.3s ease;
        }

        .message-detail-box .actions .btn-back:hover {
            background: #e5e7eb;
        }

        .message-detail-box .actions .btn-reply {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .message-detail-box .actions .btn-reply:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
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
