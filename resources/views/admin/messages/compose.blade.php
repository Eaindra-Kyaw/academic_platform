@extends('layouts.app')

@section('title', 'Compose Message')
@section('page-title', '✉️ Compose Message')
@section('welcome-text', 'Send a message to a student or lecturer')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .compose-box {
            max-width: 700px;
            margin: 0 auto;
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e9edf4;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            color: #1a2332;
            margin-bottom: 0.2rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e9edf4;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .btn-send {
            background: #800000;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-send:hover {
            background: #a00000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }
    </style>

    <div class="compose-box">
        <form method="POST" action="{{ route('admin.messages.send') }}">
            @csrf

            <div class="form-group">
                <label>Recipient *</label>
                <select name="recipient_id" required>
                    <option value="">Select a recipient</option>
                    <optgroup label="Students">
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}
                                ({{ $student->student_id ?? 'Student' }})</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Lecturers">
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}">{{ $lecturer->name }} (Lecturer)</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" placeholder="Enter subject (optional)">
            </div>

            <div class="form-group">
                <label>Message *</label>
                <textarea name="message" placeholder="Type your message here..." required></textarea>
            </div>

            <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                <a href="{{ route('admin.messages.inbox') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-send">
                    <i class="bi bi-send"></i> Send Message
                </button>
            </div>
        </form>
    </div>
@endsection
