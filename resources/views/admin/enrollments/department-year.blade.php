{{-- resources/views/admin/enrollments/department-year.blade.php --}}
@extends('layouts.app')

@section('title', 'Enrollment Management - ' . $department->name)
@section('role', 'Admin')
@section('page-title', $department->name . ' - ' . ($yearDisplayMap[$year] ?? 'Courses'))
@section('welcome-text', 'Viewing courses for ' . ($yearDisplayMap[$year] ?? ''))

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    @php
        // Map for numeric to full year names
        $yearDisplayMap = [
            '1' => 'First Year',
            '2' => 'Second Year',
            '3' => 'Third Year',
            '4' => 'Fourth Year',
            '5' => 'Fifth Year',
            '6' => 'Sixth Year',
        ];

        // Get the display name for the current year
        $currentYearDisplay = $yearDisplayMap[$year] ?? 'Courses';
    @endphp

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

        .breadcrumb-bar {
            background: white;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .breadcrumb-bar .breadcrumb-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .breadcrumb-bar .breadcrumb-item a {
            color: #800000;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-bar .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-bar .breadcrumb-item .separator {
            color: #d1d5db;
        }

        .breadcrumb-bar .breadcrumb-item.active {
            color: #1f2937;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-number.courses {
            color: #6366f1;
        }

        .stat-number.students {
            color: #10b981;
        }

        .stat-number.pending {
            color: #d97706;
        }

        .stat-number.total {
            color: #800000;
        }

        .stat-label {
            font-size: 0.65rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .year-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            background: white;
            padding: 0.75rem;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            flex-wrap: wrap;
        }

        .year-tab {
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            border: 2px solid transparent;
            background: #f3f4f6;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            color: #6b7280;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .year-tab:hover {
            background: #e5e7eb;
            color: #374151;
            text-decoration: none;
            transform: translateY(-1px);
        }

        .year-tab.active {
            background: #800000;
            color: white;
            border-color: #800000;
        }

        .year-tab .badge-count {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.05rem 0.5rem;
            border-radius: 20px;
            font-size: 0.6rem;
        }

        .year-tab.active .badge-count {
            background: rgba(255, 255, 255, 0.25);
        }

        .year-tab:not(.active) .badge-count {
            background: #d1d5db;
            color: #6b7280;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.25rem;
        }

        .course-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .course-card::before {
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

        .course-card:hover {
            border-color: #800000;
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(128, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
        }

        .course-card:hover::before {
            opacity: 1;
        }

        .course-card .course-code {
            font-weight: 700;
            font-size: 1rem;
            color: #800000;
            display: block;
        }

        .course-card .course-name {
            font-size: 0.85rem;
            color: #374151;
            margin-top: 0.2rem;
            display: block;
        }

        .course-card .course-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f3f4f6;
            flex-wrap: wrap;
        }

        .course-card .course-meta span {
            font-size: 0.7rem;
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .course-card .course-meta .num {
            font-weight: 700;
            color: #1f2937;
        }

        .course-card .course-meta .num.pending {
            color: #d97706;
        }

        .course-card .course-meta .num.approved {
            color: #10b981;
        }

        .course-card .click-hint {
            position: absolute;
            bottom: 0.5rem;
            right: 0.75rem;
            font-size: 0.6rem;
            color: #9ca3af;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .course-card:hover .click-hint {
            opacity: 1;
        }

        .course-card .course-badge {
            display: inline-block;
            background: #f3f4f6;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
        }

        .empty-state h4 {
            color: #374151;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            font-size: 0.85rem;
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
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-number {
                font-size: 1.4rem;
            }

            .year-tabs {
                justify-content: center;
                padding: 0.5rem;
            }

            .year-tab {
                padding: 0.3rem 0.8rem;
                font-size: 0.7rem;
            }

            .course-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-card {
                padding: 0.75rem;
            }

            .stat-number {
                font-size: 1.2rem;
            }

            .course-grid {
                grid-template-columns: 1fr;
            }

            .year-tab {
                padding: 0.25rem 0.6rem;
                font-size: 0.65rem;
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
    <a href="{{ route('admin.enrollments.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Departments
    </a>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number courses">{{ $stats['total_courses'] ?? 0 }}</div>
            <div class="stat-label">📚 Total Courses</div>
        </div>
        <div class="stat-card">
            <div class="stat-number students">{{ $stats['total_students'] ?? 0 }}</div>
            <div class="stat-label">👨‍🎓 Enrolled Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-number pending">{{ $stats['pending_enrollments'] ?? 0 }}</div>
            <div class="stat-label">⏳ Pending Enrollments</div>
        </div>
        <div class="stat-card">
            <div class="stat-number total">{{ $stats['approved_enrollments'] ?? 0 }}</div>
            <div class="stat-label">✅ Approved Enrollments</div>
        </div>
    </div>

    {{-- Year Tabs --}}
    <div class="year-tabs">
        @for ($i = 1; $i <= 6; $i++)
            <a href="{{ route('admin.enrollments.department.year', ['departmentId' => $department->id, 'year' => $i]) }}"
                class="year-tab {{ $year == $i ? 'active' : '' }}">
                {{ $yearDisplayMap[(string) $i] }}
                <span class="badge-count">{{ $yearCounts[$i] ?? 0 }}</span>
            </a>
        @endfor
    </div>

    {{-- Course Grid --}}
    @if ($courses && $courses->count() > 0)
        <div class="course-grid">
            @foreach ($courses as $course)
                <a href="{{ route('admin.enrollments.course', ['courseId' => $course->id]) }}" class="course-card">
                    <span class="course-code">{{ $course->course_code }}</span>
                    <span class="course-name">{{ $course->course_name }}</span>

                    @if (!empty($course->year))
                        <span class="course-badge">📅 {{ $course->year }}</span>
                    @endif

                    <div class="course-meta">
                        <span>👨‍🎓 <span class="num">{{ $course->students_count ?? 0 }}</span> students</span>
                        <span>⏳ <span class="num pending">{{ $course->pending_count ?? 0 }}</span> pending</span>
                        <span>✅ <span class="num approved">{{ $course->approved_count ?? 0 }}</span> approved</span>
                        @if (!empty($course->credits))
                            <span>📊 <span class="num">{{ $course->credits }}</span> credits</span>
                        @endif
                        @if (!empty($course->lecturer_name))
                            <span>👨‍🏫 {{ $course->lecturer_name }}</span>
                        @endif
                    </div>

                    <span class="click-hint">Click to view students →</span>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-book"></i>
            <h4>No Courses Found</h4>
            <p>No courses are available for {{ $currentYearDisplay }} in {{ $department->name }}.</p>
        </div>
    @endif
@endsection
