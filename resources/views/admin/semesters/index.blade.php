{{-- resources/views/admin/semesters/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Semesters')
@section('role', 'Admin')
@section('page-title', '📅 Semesters')
@section('welcome-text', 'Manage academic semesters')

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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.08);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-number.total {
            color: var(--info);
        }

        .stat-number.active {
            color: var(--success);
        }

        .stat-number.current {
            color: var(--primary);
        }

        .stat-label {
            font-size: 0.65rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .year-group {
            margin-bottom: 1.5rem;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .year-group .year-header {
            padding: 0.6rem 1rem;
            background: #f8fafc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .year-group .year-header .year-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .year-group .year-header .year-title .badge-year {
            background: var(--primary);
            color: var(--white);
            font-size: 0.55rem;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
        }

        .year-group .year-header .year-stats {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .year-group .year-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            padding: 1rem;
        }

        .semester-card {
            background: var(--white);
            border-radius: 8px;
            border: 2px solid rgba(10, 36, 99, 0.06);
            padding: 1rem;
            transition: var(--transition);
        }

        .semester-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.08);
        }

        .semester-card.current {
            border-color: var(--warning);
            background: var(--warning-light);
        }

        .semester-card.active {
            border-color: var(--success);
            background: var(--success-light);
        }

        .semester-card .semester-label {
            font-size: 0.65rem;
            color: var(--text-gray);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .semester-card .semester-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0.1rem 0;
        }

        .semester-card .semester-months {
            font-size: 0.7rem;
            color: var(--text-gray);
        }

        .semester-card .badge-status {
            font-size: 0.5rem;
            padding: 0.05rem 0.4rem;
            border-radius: 1rem;
            display: inline-block;
        }

        .badge-current {
            background: var(--warning);
            color: var(--white);
        }

        .badge-active {
            background: var(--success);
            color: var(--white);
        }

        .badge-inactive {
            background: #e5e7eb;
            color: var(--text-gray);
        }

        .semester-card .course-count {
            font-size: 0.7rem;
            color: var(--text-gray);
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .semester-card .course-count .count-link {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }

        .semester-card .course-count .count-link:hover {
            text-decoration: underline;
        }

        .semester-card .actions {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .btn-sm {
            padding: 0.2rem 0.6rem;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            transition: var(--transition);
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-current {
            background: var(--success-light);
            color: #166534;
        }

        .btn-current:hover {
            background: #bbf7d0;
        }

        .btn-edit {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-edit:hover {
            background: #bfdbfe;
        }

        .btn-view-courses {
            background: var(--primary);
            color: var(--white);
            padding: 0.25rem 0.7rem;
            border-radius: 8px;
            border: none;
            font-size: 0.65rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-view-courses:hover {
            background: var(--primary-light);
            color: var(--white);
            transform: translateY(-1px);
        }

        .btn-generate {
            background: var(--info);
            color: var(--white);
            padding: 0.4rem 1rem;
            border-radius: 8px;
            border: none;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-generate:hover {
            background: #4f46e5;
            color: var(--white);
        }

        .btn-create {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            padding: 0.4rem 1rem;
            border-radius: 8px;
            border: none;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 0.75rem;
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
            }

            .year-group .year-body {
                grid-template-columns: 1fr;
            }

            .top-bar {
                flex-direction: column;
                align-items: stretch;
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

            .year-group .year-body {
                padding: 0.75rem;
            }

            .semester-card {
                padding: 0.75rem;
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

    {{-- Top Bar --}}
    <div class="top-bar">
        <div>
            <span class="text-muted" style="font-size:0.85rem;">
                Total: {{ $semesters->count() }} semesters
            </span>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <a href="{{ route('admin.semesters.generate') }}" class="btn-generate"
                onclick="return confirm('Generate all semesters for current year?')">
                <i class="bi bi-magic"></i> Generate Year
            </a>
            <a href="{{ route('admin.semesters.create') }}" class="btn-create">
                <i class="bi bi-plus-circle"></i> New Semester
            </a>
        </div>
    </div>

    {{-- Semesters Grouped by Academic Year --}}
    @php
        // ✅ FIXED: Group by 'academic_year' instead of 'year'
        $groupedSemesters = $semesters->groupBy('academic_year');
    @endphp

    @foreach ($groupedSemesters as $academicYear => $yearSemesters)
        @php
            $yearName = $yearSemesters->first()->year_name ?? $academicYear;
            $totalCourses = 0;
            foreach ($yearSemesters as $sem) {
                $totalCourses += \App\Models\Course::where('year', $sem->year_name)
                    ->where('semester', $sem->semester_name)
                    ->count();
            }
            $hasCurrent = $yearSemesters->contains('is_current', true);
        @endphp
        <div class="year-group">
            <div class="year-header">
                <div class="year-title">
                    {{ $academicYear }}
                    @if ($hasCurrent)
                        <span
                            style="background:var(--warning); color:var(--white); font-size:0.5rem; padding:0.05rem 0.4rem; border-radius:1rem;">⭐
                            Current</span>
                    @endif
                    <span class="badge-year">{{ $yearSemesters->count() }} semesters</span>
                </div>
                <div class="year-stats">
                    📚 {{ $totalCourses }} courses total
                </div>
            </div>
            <div class="year-body">
                @foreach ($yearSemesters as $semester)
                    @php
                        $isCurrent = $semester->is_current;
                        $isActive = $semester->is_active;
                        $courseCount = \App\Models\Course::where('year', $semester->year_name)
                            ->where('semester', $semester->semester_name)
                            ->count();

                        if ($isCurrent) {
                            $cardClass = 'current';
                            $statusText = '⭐ Current';
                            $statusClass = 'badge-current';
                        } elseif ($isActive && $courseCount > 0) {
                            $cardClass = 'active';
                            $statusText = '✅ Active';
                            $statusClass = 'badge-active';
                        } else {
                            $cardClass = '';
                            $statusText = '⏳ Inactive';
                            $statusClass = 'badge-inactive';
                        }

                        // Format months from dates
                        $startMonth = $semester->start_date
                            ? \Carbon\Carbon::parse($semester->start_date)->format('M')
                            : '';
                        $endMonth = $semester->end_date ? \Carbon\Carbon::parse($semester->end_date)->format('M') : '';
                        $months = $startMonth && $endMonth ? $startMonth . ' – ' . $endMonth : 'Dates not set';
                    @endphp
                    <div class="semester-card {{ $cardClass }}">
                        <div class="semester-label">{{ $semester->semester_name }}</div>
                        <div class="semester-name">
                            {{ $semester->semester_name }}
                            <span class="badge-status {{ $statusClass }}">{{ $statusText }}</span>
                        </div>
                        <div class="semester-months">
                            📅 {{ $months }}
                        </div>
                        <div class="course-count">
                            📚
                            @if ($courseCount > 0)
                                <a href="{{ route('admin.semesters.show', $semester->id) }}" class="count-link">
                                    <strong>{{ $courseCount }}</strong> courses
                                </a>
                            @else
                                <span style="color:#9ca3af;"><strong>0</strong> courses</span>
                            @endif
                        </div>
                        <div class="actions">
                            @if ($courseCount > 0)
                                <a href="{{ route('admin.semesters.show', $semester->id) }}" class="btn-view-courses">
                                    <i class="bi bi-eye"></i> View {{ $courseCount }} Courses
                                </a>
                            @endif
                            @if (!$isCurrent)
                                <a href="{{ route('admin.semesters.set-current', $semester->id) }}"
                                    class="btn-sm btn-current" onclick="return confirm('Set this as current semester?')">
                                    <i class="bi bi-star"></i> Set Current
                                </a>
                            @endif
                            <a href="{{ route('admin.semesters.edit', $semester->id) }}" class="btn-sm btn-edit">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if ($groupedSemesters->count() == 0)
        <div
            style="text-align:center; padding:3rem; background:var(--white); border-radius:var(--radius); border:1px solid rgba(10,36,99,0.06);">
            <i class="bi bi-calendar2-week" style="font-size:3rem; color:#d1d5db; display:block; margin-bottom:1rem;"></i>
            <p style="color:var(--text-gray);">No semesters created yet.</p>
            <a href="{{ route('admin.semesters.create') }}" class="btn-create" style="margin-top:0.5rem;">
                <i class="bi bi-plus-circle"></i> Create First Semester
            </a>
        </div>
    @endif
@endsection
