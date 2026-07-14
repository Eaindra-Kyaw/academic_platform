@extends('layouts.app')

@section('title', 'Compose Message')
@section('page-title', '✉️ Compose Message')
@section('welcome-text', 'Send a message to your students')

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
            --danger: #ef4444;
        }

        .compose-box {
            max-width: 700px;
            margin: 0 auto;
            background: var(--white);
            border-radius: 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.4rem;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            background: var(--bg-main);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-group .help-text {
            font-size: 0.65rem;
            color: var(--text-gray);
            margin-top: 0.2rem;
        }

        .btn-send {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-send:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
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
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }
    </style>

    <div class="compose-box">
        <form method="POST" action="{{ route('lecturer.messages.send') }}">
            @csrf

            <div class="form-group">
                <label>Recipient *</label>
                <select name="recipient_id" required>
                    <option value="">Select a student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->name }} ({{ $student->student_id ?? 'Student' }})
                        </option>
                    @endforeach
                </select>
                @if ($students->count() == 0)
                    <div class="help-text">No students found in your department.</div>
                @endif
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
                <a href="{{ route('lecturer.messages.inbox') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-send">
                    <i class="bi bi-send"></i> Send Message
                </button>
            </div>
        </form>
    </div>
@endsection
