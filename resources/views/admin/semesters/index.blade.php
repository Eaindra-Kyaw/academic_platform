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

        .stat-number.total {
            color: #6366f1;
        }

        .stat-number.active {
            color: #10b981;
        }

        .stat-number.current {
            color: #800000;
        }

        .stat-label {
            font-size: 0.65rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .year-group {
            margin-bottom: 1.5rem;
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .year-group .year-header {
            padding: 0.6rem 1rem;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .year-group .year-header .year-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .year-group .year-header .year-title .badge-year {
            background: #800000;
            color: white;
            font-size: 0.55rem;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
        }

        .year-group .year-header .year-stats {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .year-group .year-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            padding: 1rem;
        }

        .semester-card {
            background: white;
            border-radius: 0.5rem;
            border: 2px solid #e5e7eb;
            padding: 1rem;
            transition: all 0.2s;
        }

        .semester-card:hover {
            border-color: #800000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.08);
        }

        .semester-card.current {
            border-color: #f59e0b;
            background: #fffbeb;
        }

        .semester-card.active {
            border-color: #10b981;
            background: #f0fdf4;
        }

        .semester-card .semester-label {
            font-size: 0.65rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .semester-card .semester-name {
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0.1rem 0;
        }

        .semester-card .semester-months {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .semester-card .badge-status {
            font-size: 0.5rem;
            padding: 0.05rem 0.4rem;
            border-radius: 1rem;
            display: inline-block;
        }

        .badge-current {
            background: #f59e0b;
            color: white;
        }

        .badge-active {
            background: #10b981;
            color: white;
        }

        .badge-inactive {
            background: #e5e7eb;
            color: #6b7280;
        }

        .semester-card .course-count {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid #f3f4f6;
        }

        .semester-card .course-count .count-link {
            color: #800000;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
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
            border-top: 1px solid #f3f4f6;
        }

        .btn-sm {
            padding: 0.2rem 0.6rem;
            border-radius: 0.3rem;
            font-size: 0.65rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-current {
            background: #dcfce7;
            color: #166534;
        }

        .btn-current:hover {
            background: #bbf7d0;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-edit:hover {
            background: #bfdbfe;
        }

        .btn-view-courses {
            background: #800000;
            color: white;
            padding: 0.25rem 0.7rem;
            border-radius: 0.3rem;
            border: none;
            font-size: 0.65rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-view-courses:hover {
            background: #a00000;
            color: white;
            transform: translateY(-1px);
        }

        .btn-generate {
            background: #6366f1;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 0.4rem;
            border: none;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .btn-generate:hover {
            background: #4f46e5;
            color: white;
        }

        .btn-create {
            background: #800000;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 0.4rem;
            border: none;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .btn-create:hover {
            background: #5f0000;
            color: white;
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

        .pagination-wrapper {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
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
                Total: {{ $semesters->total() }} semesters
            </span>
        </div>
        <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
            <a href="{{ route('admin.semesters.generate') }}" class="btn-generate"
                onclick="return confirm('Generate all 12 semesters?')">
                <i class="bi bi-magic"></i> Generate All
            </a>
            <a href="{{ route('admin.semesters.create') }}" class="btn-create">
                <i class="bi bi-plus-circle"></i> New Semester
            </a>
        </div>
    </div>

    {{-- Semesters Grouped by Year --}}
    @php
        $groupedSemesters = $semesters->groupBy('year');
    @endphp

    @foreach ($groupedSemesters as $year => $yearSemesters)
        @php
            $yearName = $yearSemesters->first()->year_name;
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
                    {{ $yearName }}
                    @if ($hasCurrent)
                        <span
                            style="background:#f59e0b; color:white; font-size:0.5rem; padding:0.05rem 0.4rem; border-radius:1rem;">⭐
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
                    @endphp
                    <div class="semester-card {{ $cardClass }}">
                        <div class="semester-label">{{ $semester->semester_name }}</div>
                        <div class="semester-name">
                            {{ $semester->semester_name }}
                            <span class="badge-status {{ $statusClass }}">{{ $statusText }}</span>
                        </div>
                        <div class="semester-months">
                            📅 {{ $semester->semester_months }}
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
                                @if (!$isCurrent)
                                    <a href="{{ route('admin.semesters.set-current', $semester->id) }}"
                                        class="btn-sm btn-current"
                                        onclick="return confirm('Set this as current semester?')">
                                        <i class="bi bi-star"></i> Set Current
                                    </a>
                                @endif
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

    {{-- Pagination --}}
    @if ($semesters->hasPages())
        <div class="pagination-wrapper">
            <div class="text-muted" style="font-size:0.75rem;">
                Showing {{ $semesters->firstItem() ?? 0 }} to {{ $semesters->lastItem() ?? 0 }}
                of {{ $semesters->total() }} semesters
            </div>
            <div>
                {{ $semesters->links() }}
            </div>
        </div>
    @endif
@endsection
