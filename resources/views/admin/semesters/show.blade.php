{{-- resources/views/admin/semesters/show.blade.php --}}
@extends('layouts.app')

@section('title', $semester->full_name)
@section('role', 'Admin')
@section('page-title', '📅 ' . $semester->full_name)
@section('welcome-text', 'Semester details')

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

        .detail-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }

        .detail-card .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .detail-card .title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .detail-card .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .detail-card .info-item .label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-card .info-item .value {
            font-size: 0.95rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .badge-current {
            background: var(--success-light);
            color: #166534;
        }

        .badge-active {
            background: var(--info-light);
            color: var(--info);
        }

        .badge-inactive {
            background: #f3f4f6;
            color: var(--text-gray);
        }

        .btn-sm {
            padding: 0.2rem 0.6rem;
            border-radius: 8px;
            font-size: 0.7rem;
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

        .btn-edit {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-edit:hover {
            background: #bfdbfe;
        }

        .btn-back {
            background: #f3f4f6;
            color: var(--text-dark);
        }

        .btn-back:hover {
            background: #e5e7eb;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .course-list {
            margin-top: 1rem;
        }

        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .course-item:last-child {
            border-bottom: none;
        }

        .course-item .course-code {
            font-weight: 600;
            color: var(--text-dark);
        }

        .course-item .course-name {
            color: var(--text-gray);
            font-size: 0.85rem;
            margin-left: 0.5rem;
        }

        .course-item .dept-badge {
            background: #e5e7eb;
            color: var(--text-gray);
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
        }

        @media (max-width: 768px) {
            .detail-card .info-grid {
                grid-template-columns: 1fr;
            }

            .detail-card {
                padding: 1.25rem;
            }
        }
    </style>

    <a href="{{ route('admin.semesters.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Semesters
    </a>

    <div class="detail-card">
        <div class="header">
            <div>
                <h1 class="title">{{ $semester->full_name }}</h1>
                <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-top:0.3rem;">
                    <span
                        class="badge badge-{{ $semester->is_current ? 'current' : ($semester->is_active ? 'active' : 'inactive') }}">
                        {{ $semester->is_current ? '⭐ Current' : ($semester->is_active ? '✅ Active' : '❌ Inactive') }}
                    </span>
                    <span class="badge" style="background:#e5e7eb; color:var(--text-dark);">
                        {{ $semester->code }}
                    </span>
                </div>
            </div>
            <div class="actions">
                <a href="{{ route('admin.semesters.edit', $semester->id) }}" class="btn-sm btn-edit">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('admin.semesters.index') }}" class="btn-sm btn-back">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="label">Year</div>
                <div class="value">{{ $semester->year_label }}</div>
            </div>
            <div class="info-item">
                <div class="label">Semester</div>
                <div class="value">{{ $semester->semester_label }}</div>
            </div>
            <div class="info-item">
                <div class="label">Code</div>
                <div class="value">{{ $semester->code }}</div>
            </div>
            <div class="info-item">
                <div class="label">Academic Year</div>
                <div class="value">{{ $semester->academic_year ?? 'N/A' }}</div>
            </div>
            @if ($semester->start_date && $semester->end_date)
                <div class="info-item">
                    <div class="label">Start Date</div>
                    <div class="value">{{ $semester->start_date->format('d M Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="label">End Date</div>
                    <div class="value">{{ $semester->end_date->format('d M Y') }}</div>
                </div>
            @endif
            <div class="info-item">
                <div class="label">Total Courses</div>
                <div class="value">{{ $courses->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Courses in this semester --}}
    <div class="detail-card">
        <h3 style="font-size:1rem; font-weight:600; margin:0 0 1rem 0; color:var(--text-dark);">
            📚 Courses in this Semester
        </h3>

        @if ($courses->count() > 0)
            <div class="course-list">
                @foreach ($courses as $course)
                    <div class="course-item">
                        <div>
                            <span class="course-code">{{ $course->course_code }}</span>
                            <span class="course-name">{{ $course->course_name }}</span>
                        </div>
                        <div>
                            <span class="dept-badge">
                                {{ $course->department->name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center; padding:2rem; color:var(--text-gray);">
                <i class="bi bi-book" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                No courses found in this semester.
            </div>
        @endif
    </div>
@endsection
