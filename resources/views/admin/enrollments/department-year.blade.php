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
        $yearDisplayMap = [
            '1' => 'First Year',
            '2' => 'Second Year',
            '3' => 'Third Year',
            '4' => 'Fourth Year',
            '5' => 'Fifth Year',
            '6' => 'Sixth Year',
        ];
        $currentYearDisplay = $yearDisplayMap[$year] ?? 'Courses';
    @endphp

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
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
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
            background: rgba(212, 160, 23, 0.08);
            border-radius: var(--radius);
            border: 1px solid rgba(212, 160, 23, 0.15);
            transition: var(--transition);
        }

        .back-link:hover {
            background: rgba(212, 160, 23, 0.15);
            text-decoration: none;
            color: var(--primary-dark);
        }

        .breadcrumb-bar {
            background: var(--white);
            border-radius: var(--radius);
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            box-shadow: var(--shadow);
        }

        .breadcrumb-bar .breadcrumb-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--text-gray);
        }

        .breadcrumb-bar .breadcrumb-item a {
            color: var(--primary);
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
            color: var(--text-dark);
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-number.courses {
            color: var(--info);
        }

        .stat-number.students {
            color: var(--success);
        }

        .stat-number.pending {
            color: var(--warning);
        }

        .stat-number.total {
            color: var(--primary);
        }

        .stat-label {
            font-size: 0.65rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .year-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            background: var(--white);
            padding: 0.75rem;
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            flex-wrap: wrap;
            box-shadow: var(--shadow);
        }

        .year-tab {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            border: 2px solid transparent;
            background: #f3f4f6;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-gray);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .year-tab:hover {
            background: #e5e7eb;
            color: var(--text-dark);
            text-decoration: none;
            transform: translateY(-1px);
        }

        .year-tab.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
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
            color: var(--text-gray);
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.25rem;
        }

        .course-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem;
            border: 2px solid rgba(10, 36, 99, 0.06);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
            display: block;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .course-card::before {
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

        .course-card:hover {
            border-color: var(--primary);
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            text-decoration: none;
            color: inherit;
        }

        .course-card:hover::before {
            opacity: 1;
        }

        .course-card .course-code {
            font-weight: 700;
            font-size: 1rem;
            color: var(--primary);
            display: block;
        }

        .course-card .course-name {
            font-size: 0.85rem;
            color: var(--text-dark);
            margin-top: 0.2rem;
            display: block;
        }

        .course-card .course-meta {
            display: flex;
            gap: 1rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            flex-wrap: wrap;
        }

        .course-card .course-meta span {
            font-size: 0.7rem;
            color: var(--text-gray);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .course-card .course-meta .num {
            font-weight: 700;
            color: var(--text-dark);
        }

        .course-card .course-meta .num.pending {
            color: var(--warning);
        }

        .course-card .course-meta .num.approved {
            color: var(--success);
        }

        .course-card .click-hint {
            position: absolute;
            bottom: 0.5rem;
            right: 0.75rem;
            font-size: 0.6rem;
            color: var(--text-gray);
            opacity: 0;
            transition: var(--transition);
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
            color: var(--text-gray);
            margin-top: 0.25rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
        }

        .empty-state h4 {
            color: var(--text-dark);
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            font-size: 0.85rem;
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
