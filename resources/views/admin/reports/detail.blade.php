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
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(10, 36, 99, 0.05);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.1);
            transition: var(--transition);
        }

        .back-link:hover {
            background: rgba(10, 36, 99, 0.08);
            text-decoration: none;
            color: var(--primary);
        }

        .report-detail-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }

        .report-detail-card .header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .report-detail-card .header .icon {
            font-size: 3rem;
        }

        .report-detail-card .header h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .report-detail-card .header p {
            color: var(--text-gray);
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
            color: var(--text-dark);
            display: block;
            margin-bottom: 0.3rem;
        }

        .report-detail-card .filters select,
        .report-detail-card .filters input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.85rem;
            background: var(--white);
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .report-detail-card .filters select:focus,
        .report-detail-card .filters input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .report-detail-card .actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            padding-top: 1rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .btn-generate {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            text-decoration: none;
        }

        .btn-generate.csv {
            background: var(--primary);
            color: var(--white);
        }

        .btn-generate.csv:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
        }

        .btn-generate.pdf {
            background: var(--danger);
            color: var(--white);
        }

        .btn-generate.pdf:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-generate.excel {
            background: var(--success);
            color: var(--white);
        }

        .btn-generate.excel:hover {
            background: #059669;
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

        .report-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            color: var(--text-gray);
            border: 1px solid rgba(10, 36, 99, 0.06);
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

                @if (in_array('attendance_below', $availableFilters))
                    <div>
                        <label>Attendance Below (%)</label>
                        <input type="number" name="attendance_below" placeholder="e.g. 75" min="0" max="100">
                    </div>
                @endif

                @if (in_array('score_below', $availableFilters))
                    <div>
                        <label>Health Score Below</label>
                        <input type="number" name="score_below" placeholder="e.g. 60" min="0" max="100">
                    </div>
                @endif

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
