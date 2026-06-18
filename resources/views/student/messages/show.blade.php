@extends('layouts.app')

@section('title', $message->subject ?? 'Message')
@section('page-title', '📄 Message')
@section('welcome-text', 'View message details')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <div style="max-width:800px; margin:0 auto;">
        <a href="{{ route('student.messages.inbox') }}"
            style="display:inline-flex; align-items:center; gap:0.5rem; color:#6b7a8f; text-decoration:none; margin-bottom:1.5rem; font-size:0.85rem;">
            <i class="bi bi-arrow-left"></i> Back to Inbox
        </a>

        <div
            style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; padding:2rem; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div
                style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.5rem; margin-bottom:1.5rem;">
                <div>
                    <h3 style="font-size:1.2rem; font-weight:700; color:#1a2332; margin:0;">
                        {{ $message->subject ?? 'No Subject' }}
                    </h3>
                    <div style="font-size:0.8rem; color:#6b7280; margin:0.2rem 0 0;">
                        <i class="bi bi-person"></i> From: <strong>{{ $message->sender->name ?? 'Unknown' }}</strong>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.7rem; color:#6b7280;">
                        <i class="bi bi-clock"></i> {{ $message->created_at->format('F j, Y g:i A') }}
                    </div>
                    @if (!$message->is_read)
                        <span
                            style="background:#3b82f6; color:white; font-size:0.6rem; padding:0.1rem 0.6rem; border-radius:1rem;">New</span>
                    @endif
                </div>
            </div>

            <div
                style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1rem; background:#fafbfc; border-radius:0.5rem; margin-bottom:1.5rem;">
                <div
                    style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg, #800000, #a00000); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.7rem;">
                    {{ substr($message->sender->name ?? 'A', 0, 2) }}
                </div>
                <div>
                    <div style="font-weight:600; font-size:0.9rem; color:#1a2332;">{{ $message->sender->name ?? 'Unknown' }}
                    </div>
                    <div style="font-size:0.7rem; color:#6b7280;">{{ $message->sender->email ?? '' }}</div>
                </div>
            </div>

            <div
                style="padding:1rem 0; border-top:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9; margin-bottom:1.5rem;">
                <p style="font-size:0.95rem; color:#1a2332; line-height:1.6; white-space:pre-wrap; margin:0;">
                    {{ $message->message }}
                </p>
            </div>

            <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                <a href="{{ route('student.messages.inbox') }}"
                    style="background:#f3f4f6; color:#374151; border:none; padding:0.4rem 1.2rem; border-radius:0.4rem; font-size:0.8rem; text-decoration:none;">
                    Back to Inbox
                </a>
            </div>
        </div>
    </div>
@endsection
