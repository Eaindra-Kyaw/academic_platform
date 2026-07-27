@extends('layouts.app')

@section('title', 'Compose Message')
@section('page-title', ' Compose Message')
@section('welcome-text', 'Send a message to a student or lecturer')

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
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .compose-box {
            max-width: 700px;
            margin: 0 auto;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
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
            color: var(--text-dark);
            margin-bottom: 0.2rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.85rem;
            transition: var(--transition);
            background: #fafbfc;
            font-family: 'Inter', sans-serif;
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

        .btn-send {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-send:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
        }

        .btn-send i {
            margin-right: 0.3rem;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: var(--transition);
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
                                ({{ $student->student_id ?? 'Student' }})
                            </option>
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
