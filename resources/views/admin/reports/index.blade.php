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
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.25rem;
            margin-top: 1.5rem;
        }

        .report-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            color: inherit;
            display: block;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .report-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: #800000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .report-card:hover {
            border-color: #800000;
            box-shadow: 0 8px 25px rgba(128, 0, 0, 0.1);
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
            color: #1f2937;
            margin: 0 0 0.3rem;
        }

        .report-card p {
            font-size: 0.8rem;
            color: #6b7280;
            margin: 0;
            line-height: 1.4;
        }

        .report-card .arrow {
            display: inline-block;
            margin-top: 0.75rem;
            color: #800000;
            font-size: 0.8rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .report-card:hover .arrow {
            opacity: 1;
        }

        .alert {
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
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

    {{-- Alerts --}}
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

    <!-- Reports Grid -->
    <div class="reports-grid">
        <!-- Student Report -->
        <a href="{{ route('admin.reports.detail', ['type' => 'students']) }}" class="report-card">
            <span class="icon">👨‍🎓</span>
            <h5>Student Report</h5>
            <p>Export all student data including attendance and enrollment</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <!-- Attendance Report -->
        <a href="{{ route('admin.reports.detail', ['type' => 'attendance']) }}" class="report-card">
            <span class="icon">📋</span>
            <h5>Attendance Report</h5>
            <p>Export attendance records by department and year</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <!-- Enrollment Report -->
        <a href="{{ route('admin.reports.detail', ['type' => 'enrollments']) }}" class="report-card">
            <span class="icon">📚</span>
            <h5>Enrollment Report</h5>
            <p>Export all enrollment data with student and course details</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <!-- Department Report -->
        <a href="{{ route('admin.reports.detail', ['type' => 'departments']) }}" class="report-card">
            <span class="icon">🏛️</span>
            <h5>Department Report</h5>
            <p>Export department statistics and performance metrics</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <!-- Risk Analysis Report -->
        <a href="{{ route('admin.reports.detail', ['type' => 'risk']) }}" class="report-card">
            <span class="icon">⚠️</span>
            <h5>Risk Analysis Report</h5>
            <p>Export list of at-risk students with detailed metrics</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <!-- Academic Health Report -->
        <a href="{{ route('admin.reports.detail', ['type' => 'health']) }}" class="report-card">
            <span class="icon">💚</span>
            <h5>Academic Health Report</h5>
            <p>Export Academic Health Scores and trends</p>
            <span class="arrow">Click to generate →</span>
        </a>

        <!-- Semester Summary -->
        <a href="{{ route('admin.reports.detail', ['type' => 'semester']) }}" class="report-card">
            <span class="icon">📅</span>
            <h5>Semester Summary</h5>
            <p>Export complete semester summary with all statistics</p>
            <span class="arrow">Click to generate →</span>
        </a>
    </div>
@endsection
