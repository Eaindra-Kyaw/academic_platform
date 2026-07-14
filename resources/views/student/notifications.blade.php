{{-- resources/views/student/notifications.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifications')
@section('role', 'Student')
@section('page-title', '🔔 Notifications')
@section('welcome-text', 'Your system notifications')

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
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .coming-soon-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 50vh;
        }

        .coming-soon-content {
            text-align: center;
            max-width: 500px;
            background: var(--white);
            padding: 3rem;
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
        }

        .coming-soon-content .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .coming-soon-content h2 {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .coming-soon-content p {
            color: var(--text-gray);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .coming-soon-content .sub-text {
            font-size: 0.8rem;
            color: var(--text-gray);
        }
    </style>

    <div class="coming-soon-wrapper">
        <div class="coming-soon-content">
            <div class="icon">🔔</div>
            <h2>Notifications</h2>
            <p>Stay updated with your academic notifications.</p>
            <p class="sub-text">This feature is currently under development.</p>
            <p class="sub-text">Check back soon for real-time notifications!</p>
        </div>
    </div>
@endsection
