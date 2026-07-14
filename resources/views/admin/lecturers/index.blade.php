@extends('layouts.app')

@section('title', 'All Lecturers')
@section('page-title', '👨‍🏫 Lecturers')
@section('welcome-text', 'Manage all faculty members')

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

        .lecturer-table-wrap {
            overflow-x: auto;
        }

        .lecturer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .lecturer-table thead th {
            padding: 0.6rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-gray);
            border-bottom: 2px solid rgba(10, 36, 99, 0.06);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
        }

        .lecturer-table tbody td {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .lecturer-table tbody tr {
            transition: var(--transition);
        }

        .lecturer-table tbody tr:hover {
            background: #fafbfc;
        }

        .lecturer-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.65rem;
            flex-shrink: 0;
        }

        .attendance-bar {
            width: 50px;
            height: 4px;
            background: #f1f5f9;
            border-radius: 4px;
            margin: 2px auto 0;
            overflow: hidden;
        }

        .attendance-bar .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .btn-action-icon {
            padding: 0.15rem 0.4rem;
            border-radius: 6px;
            font-size: 0.7rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .btn-action-icon:hover {
            transform: translateY(-1px);
        }

        .btn-view {
            color: var(--info);
            background: var(--info-light);
        }

        .btn-view:hover {
            background: #bfdbfe;
        }

        .btn-edit {
            color: #92400e;
            background: var(--warning-light);
        }

        .btn-edit:hover {
            background: #fde68a;
        }

        .btn-delete {
            color: var(--danger);
            background: var(--danger-light);
        }

        .btn-delete:hover {
            background: #fca5a5;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.1);
            border-radius: 8px;
            padding: 0.2rem 0.6rem;
            transition: var(--transition);
        }

        .search-box:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .search-box input {
            border: none;
            outline: none;
            padding: 0.3rem 0.4rem;
            font-size: 0.75rem;
            color: var(--text-dark);
            background: transparent;
            width: 180px;
        }

        .search-box input::placeholder {
            color: #9ca3af;
        }

        .search-box i {
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .search-box .clear-btn {
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.7rem;
            padding: 0 0.2rem;
            cursor: pointer;
        }

        .search-box .clear-btn:hover {
            color: var(--danger);
        }

        .stats-footer {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .stats-footer .stat-box {
            background: var(--white);
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 0.75rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .stats-footer .stat-box .number {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .stats-footer .stat-box .label {
            font-size: 0.6rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .stats-footer .stat-box .number.blue {
            color: var(--info);
        }

        .stats-footer .stat-box .number.green {
            color: var(--success);
        }

        .stats-footer .stat-box .number.yellow {
            color: var(--warning);
        }

        @media (max-width: 768px) {

            .lecturer-table thead th,
            .lecturer-table tbody td {
                padding: 0.4rem 0.5rem;
                font-size: 0.7rem;
            }

            .search-box input {
                width: 120px;
            }

            .stats-footer {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div style="max-width:1400px; margin:0 auto;">
        <div
            style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
            <div>
                <h4 style="margin:0; font-weight:700; color:var(--text-dark); font-size:1.1rem;">All Lecturers</h4>
                <p style="color:var(--text-gray); font-size:0.85rem; margin:0;">
                    <i class="bi bi-person-badge"></i>
                    Total: <strong>{{ $lecturers->count() }}</strong> faculty members
                </p>
            </div>
            <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                <form method="GET" action="{{ route('admin.lecturers.index') }}" style="display:inline;" id="searchForm">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" id="searchInput" placeholder="Search lecturers..."
                            value="{{ $search ?? '' }}" onkeyup="document.getElementById('searchForm').submit();">
                        @if ($search)
                            <a href="{{ route('admin.lecturers.index') }}" class="clear-btn">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </div>
                </form>
                <a href="{{ route('admin.lecturers.create') }}"
                    style="background:linear-gradient(135deg, var(--primary), var(--primary-light)); color:var(--white); border:none; padding:0.4rem 1.2rem; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem; font-size:0.85rem; transition:var(--transition);">
                    <i class="bi bi-plus-circle"></i> Add Lecturer
                </a>
            </div>
        </div>

        @if (session('success'))
            <div
                style="background:var(--success-light); color:var(--success); padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; border-left:3px solid var(--success); display:flex; align-items:center; gap:0.5rem;">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background:var(--danger-light); color:var(--danger); padding:0.75rem 1rem; border-radius:8px; margin-bottom:1rem; border-left:3px solid var(--danger); display:flex; align-items:center; gap:0.5rem;">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div
            style="background:var(--white); border-radius:var(--radius); border:1px solid rgba(10, 36, 99, 0.06); overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
            <div class="lecturer-table-wrap">
                <table class="lecturer-table">
                    <thead>
                        <tr>
                            <th style="min-width:200px;">Lecturer</th>
                            <th style="min-width:150px;">Department</th>
                            <th style="text-align:center; min-width:60px;">Courses</th>
                            <th style="text-align:center; min-width:60px;">Students</th>
                            <th style="text-align:center; min-width:100px;">Attendance</th>
                            <th style="text-align:right; min-width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lecturers as $lecturer)
                            @php
                                $attendance = $lecturer->avg_attendance ?? 0;
                                $color = $attendance >= 75 ? '#10b981' : ($attendance >= 60 ? '#f59e0b' : '#ef4444');
                                $initials = substr($lecturer->name, 0, 1);
                            @endphp
                            <tr>
                                <td>
                                    <div style="display:flex; align-items:center; gap:0.6rem;">
                                        <div class="lecturer-avatar-sm">{{ $initials }}</div>
                                        <div>
                                            <div style="font-weight:500; color:var(--text-dark); font-size:0.85rem;">
                                                {{ $lecturer->name }}</div>
                                            <div style="font-size:0.65rem; color:var(--text-gray);">
                                                <i class="bi bi-envelope" style="font-size:0.55rem;"></i>
                                                {{ $lecturer->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size:0.75rem; color:var(--text-gray);">
                                        {{ $lecturer->department->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <span style="font-weight:600; color:var(--text-dark); font-size:0.9rem;">
                                        {{ $lecturer->courses_count ?? 0 }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <span style="font-weight:600; color:var(--text-dark); font-size:0.9rem;">
                                        {{ $lecturer->students_count ?? 0 }}
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <div>
                                        <span style="font-weight:600; color:{{ $color }}; font-size:0.85rem;">
                                            {{ number_format($attendance, 1) }}%
                                        </span>
                                    </div>
                                    <div class="attendance-bar">
                                        <div class="fill"
                                            style="width:{{ $attendance }}%; background:{{ $color }};"></div>
                                    </div>
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex; gap:0.3rem; justify-content:flex-end;">
                                        <a href="{{ url('/admin/lecturers/' . $lecturer->id) }}"
                                            class="btn-action-icon btn-view" title="View Profile">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.lecturers.edit', $lecturer) }}"
                                            class="btn-action-icon btn-edit" title="Edit Lecturer">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.lecturers.destroy', $lecturer) }}"
                                            style="display:inline;"
                                            onsubmit="return confirm('Are you sure you want to delete {{ $lecturer->name }}? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-icon btn-delete"
                                                title="Delete Lecturer" style="border:none; cursor:pointer;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:2.5rem 1rem; text-align:center;">
                                    <div style="color:var(--text-gray);">
                                        <i class="bi bi-person-badge"
                                            style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                        <p style="font-size:0.9rem; margin:0;">No lecturers found</p>
                                        @if ($search)
                                            <p style="font-size:0.75rem; margin:0.2rem 0 0.5rem;">No results found for
                                                "{{ $search }}"</p>
                                        @else
                                            <p style="font-size:0.75rem; margin:0.2rem 0 0.5rem;">Click "Add Lecturer" to
                                                create your first faculty member</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="stats-footer">
            <div class="stat-box">
                <div class="number">{{ $lecturers->count() }}</div>
                <div class="label">Total Lecturers</div>
            </div>
            <div class="stat-box">
                <div class="number blue">{{ $lecturers->sum('courses_count') }}</div>
                <div class="label">Total Courses</div>
            </div>
            <div class="stat-box">
                <div class="number green">{{ $lecturers->sum('students_count') }}</div>
                <div class="label">Total Students</div>
            </div>
            <div class="stat-box">
                <div class="number yellow">
                    {{ $lecturers->count() > 0 ? number_format($lecturers->avg('avg_attendance') ?? 0, 1) : 0 }}%
                </div>
                <div class="label">Avg Attendance</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchForm = document.getElementById('searchForm');

            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    searchForm.submit();
                });
            }
        });
    </script>
@endsection
