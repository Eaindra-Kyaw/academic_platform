{{-- resources/views/admin/reports/detail.blade.php --}}
@extends('layouts.app')

@section('title', 'Generate Report')
@section('role', 'Admin')
@section('page-title', '📊 ' . $reportTitle)
@section('welcome-text', 'Select filters and generate report')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #800000;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: #fef7f7;
            border-radius: 0.5rem;
            border: 1px solid #fde2e2;
            transition: all 0.2s;
        }

        .back-link:hover {
            background: #fde2e2;
            text-decoration: none;
            color: #800000;
        }

        .report-detail-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .report-detail-card .header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .report-detail-card .header .icon {
            font-size: 3rem;
        }

        .report-detail-card .header h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .report-detail-card .header p {
            color: #6b7280;
            font-size: 0.85rem;
            margin: 0.2rem 0 0;
        }

        .report-detail-card .filters {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .report-detail-card .filters .full-width {
            grid-column: 1 / -1;
        }

        .report-detail-card .filters label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #4b5563;
            display: block;
            margin-bottom: 0.3rem;
        }

        .report-detail-card .filters select,
        .report-detail-card .filters input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            background: white;
            transition: all 0.2s;
        }

        .report-detail-card .filters select:focus,
        .report-detail-card .filters input:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .report-detail-card .actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .btn-generate {
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-generate.csv {
            background: #800000;
            color: white;
        }

        .btn-generate.csv:hover {
            background: #5f0000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.3);
        }

        .btn-generate.pdf {
            background: #dc2626;
            color: white;
        }

        .btn-generate.pdf:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-generate.excel {
            background: #16a34a;
            color: white;
        }

        .btn-generate.excel:hover {
            background: #15803d;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }

        .btn-generate:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
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

        .report-info {
            background: #f8fafc;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        @media (max-width: 768px) {
            .report-detail-card .filters {
                grid-template-columns: 1fr;
            }

            .report-detail-card .filters .full-width {
                grid-column: 1;
            }

            .report-detail-card .actions {
                flex-direction: column;
            }

            .btn-generate {
                justify-content: center;
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

    {{-- Back Link --}}
    <a href="{{ route('admin.reports') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Reports
    </a>

    <div class="report-detail-card">
        <div class="header">
            <span class="icon">{{ $reportIcon }}</span>
            <div>
                <h2>{{ $reportTitle }}</h2>
                <p>{{ $reportDescription }}</p>
            </div>
        </div>

        <div class="report-info">
            <i class="bi bi-info-circle"></i> Select your filters below and click generate to download the report.
        </div>

        <form action="{{ route('admin.reports.export', ['type' => $reportType]) }}" method="GET" target="_blank">
            <div class="filters">
                {{-- Department Filter --}}
                @if (in_array('department', $availableFilters))
                    <div>
                        <label>Department</label>
                        <select name="department_id">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Course Filter --}}
                @if (in_array('course', $availableFilters))
                    <div>
                        <label>Course</label>
                        <select name="course_id">
                            <option value="">All Courses</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_code }} -
                                    {{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Year Filter --}}
                @if (in_array('year', $availableFilters))
                    <div>
                        <label>Year</label>
                        <select name="year">
                            <option value="">All Years</option>
                            <option value="1">First Year</option>
                            <option value="2">Second Year</option>
                            <option value="3">Third Year</option>
                            <option value="4">Fourth Year</option>
                            <option value="5">Fifth Year</option>
                            <option value="6">Sixth Year</option>
                        </select>
                    </div>
                @endif

                {{-- Status Filter --}}
                @if (in_array('status', $availableFilters))
                    <div>
                        <label>Status</label>
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="dropped">Dropped</option>
                        </select>
                    </div>
                @endif

                {{-- Date Range Filters --}}
                @if (in_array('date_range', $availableFilters))
                    <div>
                        <label>Date From</label>
                        <input type="date" name="date_from">
                    </div>
                    <div>
                        <label>Date To</label>
                        <input type="date" name="date_to">
                    </div>
                @endif

                {{-- Risk Level Filter --}}
                @if (in_array('risk_level', $availableFilters))
                    <div>
                        <label>Risk Level</label>
                        <select name="risk_level">
                            <option value="">All Risk Levels</option>
                            <option value="critical">Critical</option>
                            <option value="moderate">Moderate</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                @endif

                {{-- Attendance Below Filter --}}
                @if (in_array('attendance_below', $availableFilters))
                    <div>
                        <label>Attendance Below (%)</label>
                        <input type="number" name="attendance_below" placeholder="e.g. 75" min="0" max="100">
                    </div>
                @endif

                {{-- Health Score Below Filter --}}
                @if (in_array('score_below', $availableFilters))
                    <div>
                        <label>Health Score Below</label>
                        <input type="number" name="score_below" placeholder="e.g. 60" min="0" max="100">
                    </div>
                @endif

                {{-- Academic Year Filter --}}
                @if (in_array('academic_year', $availableFilters))
                    <div>
                        <label>Academic Year</label>
                        <select name="academic_year">
                            <option value="">All Years</option>
                            <option value="2025-2026">2025-2026</option>
                            <option value="2024-2025">2024-2025</option>
                            <option value="2023-2024">2023-2024</option>
                        </select>
                    </div>
                @endif

                {{-- Semester Filter --}}
                @if (in_array('semester', $availableFilters))
                    <div>
                        <label>Semester</label>
                        <select name="semester">
                            <option value="">All Semesters</option>
                            <option value="First Semester">First Semester</option>
                            <option value="Second Semester">Second Semester</option>
                        </select>
                    </div>
                @endif

                {{-- Format Filter (for Semester Summary) --}}
                @if (in_array('format', $availableFilters))
                    <div>
                        <label>Export Format</label>
                        <select name="format">
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                @endif
            </div>

            <div class="actions">
                <button type="submit" class="btn-generate csv">
                    <i class="bi bi-download"></i> Generate Report
                </button>
                @if (in_array('format', $availableFilters))
                    <button type="submit" class="btn-generate pdf"
                        formaction="{{ route('admin.reports.export', ['type' => $reportType, 'format' => 'pdf']) }}">
                        <i class="bi bi-file-pdf"></i> Export as PDF
                    </button>
                @endif
            </div>
        </form>
    </div>
@endsection
