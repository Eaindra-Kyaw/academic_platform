{{-- resources/views/admin/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Reports')
@section('role', 'Admin')
@section('page-title', '📊 Reports')
@section('welcome-text', 'Select a report type to generate')

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
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.25rem;
            margin-top: 1.5rem;
        }

        .report-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1.5rem;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: block;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
            opacity: 0;
            transition: var(--transition);
        }

        .report-card:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 25px rgba(10, 36, 99, 0.1);
            transform: translateY(-4px);
            text-decoration: none;
            color: inherit;
        }

        .report-card:hover::before {
            opacity: 1;
        }

        .report-card .icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .report-card h5 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0 0 0.3rem;
        }

        .report-card p {
            font-size: 0.8rem;
            color: var(--text-gray);
            margin: 0;
            line-height: 1.4;
        }

        .report-card .arrow {
            display: inline-block;
            margin-top: 0.75rem;
            color: var(--primary);
            font-size: 0.8rem;
            opacity: 0;
            transition: var(--transition);
        }

        .report-card:hover .arrow {
            opacity: 1;
        }

        .alert {
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: var(--success-light);
            color: #166534;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: var(--danger-light);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .alert-dismissible {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-close-alert {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
            padding: 0 0.3rem;
            opacity: 0.7;
        }

        .btn-close-alert:hover {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .reports-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .reports-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <div class="reports-grid">
        <a href="{{ route('admin.reports.detail', ['type' => 'students']) }}" class="report-card">
            <span class="icon">👨‍🎓</span>
            <h5>Student Report</h5>
            <p>Export all student data including attendance and enrollment</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <a href="{{ route('admin.reports.detail', ['type' => 'attendance']) }}" class="report-card">
            <span class="icon">📋</span>
            <h5>Attendance Report</h5>
            <p>Export attendance records by department and year</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <a href="{{ route('admin.reports.detail', ['type' => 'enrollments']) }}" class="report-card">
            <span class="icon">📚</span>
            <h5>Enrollment Report</h5>
            <p>Export all enrollment data with student and course details</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <a href="{{ route('admin.reports.detail', ['type' => 'departments']) }}" class="report-card">
            <span class="icon">🏛️</span>
            <h5>Department Report</h5>
            <p>Export department statistics and performance metrics</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <a href="{{ route('admin.reports.detail', ['type' => 'risk']) }}" class="report-card">
            <span class="icon">⚠️</span>
            <h5>Risk Analysis Report</h5>
            <p>Export list of at-risk students with detailed metrics</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <a href="{{ route('admin.reports.detail', ['type' => 'health']) }}" class="report-card">
            <span class="icon">💚</span>
            <h5>Academic Health Report</h5>
            <p>Export Academic Health Scores and trends</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <a href="{{ route('admin.reports.detail', ['type' => 'semester']) }}" class="report-card">
            <span class="icon">📅</span>
            <h5>Semester Summary</h5>
            <p>Export complete semester summary with all statistics</p>
            <span class="arrow">Click to generate →</span>
        </a>
    </div>
@endsection
