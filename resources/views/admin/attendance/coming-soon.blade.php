@extends('layouts.app')

@section('title', 'Attendance Analytics')
@section('role', 'Admin')
@section('page-title', '📊 Attendance Analytics')
@section('welcome-text', 'Coming Soon')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --accent: #D4A017;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
        }

        .coming-soon-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
        }

        .coming-soon-content {
            text-align: center;
            max-width: 500px;
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
            margin-bottom: 1.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: var(--text-dark);
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .btn-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>

    <div class="coming-soon-wrapper">
        <div class="coming-soon-content">
            <div class="icon">📊</div>
            <h2>Attendance Analytics</h2>
            <p>
                This feature is currently under development.
                <br>Check back soon for detailed attendance insights!
            </p>
            <div class="btn-group">
                <a href="{{ route('admin.dashboard') }}" class="btn-primary">
                    <i class="bi bi-house"></i> Go to Dashboard
                </a>
                <a href="#" onclick="history.back(); return false;" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>
@endsection
