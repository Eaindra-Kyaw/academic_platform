@extends('layouts.app')

@section('title', 'Reports')
@section('role', 'Admin')
@section('page-title', '📊 Reports')
@section('welcome-text', 'Generate and download reports')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .report-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            transition: all 0.2s;
            text-align: center;
        }

        .report-card:hover {
            border-color: #800000;
            box-shadow: 0 4px 16px rgba(128, 0, 0, 0.08);
            transform: translateY(-3px);
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
            margin: 0 0 1rem;
        }

        .report-card .btn-generate {
            background: #800000;
            color: white;
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s;
        }

        .report-card .btn-generate:hover {
            background: #5f0000;
            transform: translateY(-1px);
        }

        .report-card .btn-generate.outline {
            background: transparent;
            color: #800000;
            border: 1px solid #800000;
        }

        .report-card .btn-generate.outline:hover {
            background: #800000;
            color: white;
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

    <div style="max-width:1200px; margin:0 auto;">
        <!-- Header -->
        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:0.5rem;">
            <div>
                <h4 style="margin:0; font-weight:700; color:#1f2937; font-size:1.1rem;">
                    <i class="bi bi-file-earmark-text" style="color:#800000;"></i> Generate Reports
                </h4>
                <p style="font-size:0.85rem; color:#6b7280; margin:0.1rem 0 0;">
                    Download reports in CSV or PDF format
                </p>
            </div>
        </div>

        <!-- Reports Grid -->
        <div class="reports-grid">
            <!-- Student Report -->
            <div class="report-card">
                <span class="icon">👨‍🎓</span>
                <h5>Student Report</h5>
                <p>Export all student data including attendance and enrollment</p>
                <a href="#" class="btn-generate"
                    onclick="alert('Student report will be downloaded as CSV'); return false;">
                    <i class="bi bi-download"></i> Generate CSV
                </a>
            </div>

            <!-- Attendance Report -->
            <div class="report-card">
                <span class="icon">📋</span>
                <h5>Attendance Report</h5>
                <p>Export attendance records by department and year</p>
                <a href="#" class="btn-generate"
                    onclick="alert('Attendance report will be downloaded as CSV'); return false;">
                    <i class="bi bi-download"></i> Generate CSV
                </a>
            </div>

            <!-- Department Report -->
            <div class="report-card">
                <span class="icon">🏛️</span>
                <h5>Department Report</h5>
                <p>Export department statistics and performance metrics</p>
                <a href="#" class="btn-generate"
                    onclick="alert('Department report will be downloaded as CSV'); return false;">
                    <i class="bi bi-download"></i> Generate CSV
                </a>
            </div>

            <!-- Risk Analysis Report -->
            <div class="report-card">
                <span class="icon">⚠️</span>
                <h5>Risk Analysis Report</h5>
                <p>Export list of at-risk students with detailed metrics</p>
                <a href="#" class="btn-generate"
                    onclick="alert('Risk analysis report will be downloaded as CSV'); return false;">
                    <i class="bi bi-download"></i> Generate CSV
                </a>
            </div>

            <!-- Academic Health Report -->
            <div class="report-card">
                <span class="icon">💚</span>
                <h5>Academic Health Report</h5>
                <p>Export Academic Health Scores and trends</p>
                <a href="#" class="btn-generate"
                    onclick="alert('Academic health report will be downloaded as CSV'); return false;">
                    <i class="bi bi-download"></i> Generate CSV
                </a>
            </div>

            <!-- Semester Summary -->
            <div class="report-card">
                <span class="icon">📅</span>
                <h5>Semester Summary</h5>
                <p>Export complete semester summary with all statistics</p>
                <a href="#" class="btn-generate outline"
                    onclick="alert('Semester summary will be downloaded as PDF'); return false;">
                    <i class="bi bi-file-pdf"></i> Generate PDF
                </a>
            </div>
        </div>
    </div>
@endsection
