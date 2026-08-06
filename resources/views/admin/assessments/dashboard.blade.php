@extends('layouts.app')

@section('title', 'Course Assessment Dashboard')
@section('role', 'Admin')
@section('page-title', 'Course Assessment Dashboard')
@section('welcome-text', 'Manage course evaluations and monitor student feedback')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-light: #1E3A8A;
            --bg-main: #F4F6F9;
            --white: #FFFFFF;
            --text-dark: #1e293b;
            --text-gray: #64748b;
            --danger: #ef4444;
            --success: #10b981;
            --radius: 12px;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            --transition: all 0.2s ease;
        }

        body {
            background-color: var(--bg-main);
        }

        /* 🟢 CLEAN HEADER (White box removed) */
        .header-wrapper {
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: flex-end;
        }

        .header-wrapper .btn-primary {
            background: var(--primary);
            color: var(--white);
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            cursor: pointer;
        }

        .header-wrapper .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.2);
        }

        /* Stats Row */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            background: #EEF2F7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .stat-content h4 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .stat-content p {
            margin: 0;
            font-size: 0.75rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Main Table */
        .table-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        thead {
            border-bottom: 2px solid #e2e8f0;
        }

        th {
            padding: 0.8rem 0.5rem 0.8rem 0;
            color: var(--text-gray);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.5px;
        }

        /* 🟢 COLUMN ALIGNMENT CLASSES */
        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        td {
            padding: 1rem 0.5rem 1rem 0;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* 🟢 FIXED: LEFT ALIGNED TITLE & COURSE */
        .course-detail {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .course-detail .main-title {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            justify-content: flex-start;
            /* Left aligned */
            align-items: center;
            gap: 0.5rem;
        }

        .course-detail .main-title .teacher-separator {
            color: var(--text-gray);
            font-weight: 400;
        }

        .course-detail .sub-title {
            color: var(--text-gray);
            font-size: 0.8rem;
            margin-top: 0.1rem;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .badge-draft {
            background: #f1f5f9;
            color: #475569;
        }

        .badge-closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .date-info {
            font-size: 0.75rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn {
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            border: none;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .action-btn.view {
            background: #e0e7ff;
            color: #4338ca;
        }

        .action-btn.toggle {
            background: #fef3c7;
            color: #92400e;
        }

        .action-btn.delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
            display: block;
        }

        @media (max-width: 768px) {
            .header-wrapper {
                justify-content: center;
            }

            .btn-primary {
                width: 100%;
                justify-content: center;
            }

            .stats-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- 🟢 FIXED HEADER (No white background, button right) --}}
    <div class="header-wrapper">
        <a href="{{ route('admin.assessments.create') }}" class="btn-primary">
            <i class="bi bi-plus-lg"></i> New Assessment
        </a>
    </div>

    {{-- Stats Boxes --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="bi bi-files"></i></div>
            <div class="stat-content">
                <h4>{{ $totalAssessments ?? 0 }}</h4>
                <p>Total Forms</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color: var(--success); background: #dcfce7;"><i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-content">
                @php
                    $realActiveCount = 0;
                    foreach ($assessments as $assess) {
                        $status = is_object($assess)
                            ? $assess->status
                            : (isset($assess['status'])
                                ? $assess['status']
                                : 'draft');
                        if ($status == 'active') {
                            $realActiveCount++;
                        }
                    }
                @endphp
                <h4>{{ $realActiveCount }}</h4>
                <p>Active</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="color: #6b7280;"><i class="bi bi-people"></i></div>
            <div class="stat-content">
                <h4>{{ $totalSubmissions ?? 0 }}</h4>
                <p>Submissions</p>
            </div>
        </div>
    </div>

    {{-- The Assessment List Table --}}
    <div class="table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        {{-- 🟢 TITLE HEADER LEFT ALIGNED --}}
                        <th class="text-left" style="width: 35%;">Title & Course</th>
                        <th class="text-left" style="width: 20%;">Dates</th>
                        <th class="text-center" style="width: 10%;">Questions</th>
                        <th class="text-center" style="width: 10%;">Submissions</th>
                        <th class="text-left" style="width: 10%;">Status</th>
                        {{-- 🟢 ACTIONS HEADER CENTERED --}}
                        <th class="text-center" style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assessments as $assessment)
                        <tr>
                            {{-- 🟢 TITLE & COURSE DATA LEFT ALIGNED (Correct variables used) --}}
                            <td class="text-left">
                                <div class="course-detail">
                                    @php
                                        $courseObj = is_object($assessment)
                                            ? $assessment->course
                                            : (isset($assessment['course'])
                                                ? $assessment['course']
                                                : null);
                                        $courseCode = is_object($courseObj)
                                            ? $courseObj->course_code
                                            : (is_array($courseObj)
                                                ? $courseObj['course_code'] ?? ''
                                                : '');
                                        $courseName = is_object($courseObj)
                                            ? $courseObj->course_name
                                            : (is_array($courseObj)
                                                ? $courseObj['course_name'] ?? ''
                                                : '');
                                        $lecturerObj = is_object($assessment)
                                            ? $assessment->lecturer
                                            : (isset($assessment['lecturer'])
                                                ? $assessment['lecturer']
                                                : null);
                                        $lecturerName = is_object($lecturerObj)
                                            ? $lecturerObj->name
                                            : (is_array($lecturerObj)
                                                ? $lecturerObj['name'] ?? ''
                                                : '');
                                        $id = is_object($assessment) ? $assessment->id : $assessment['id'] ?? 0;
                                    @endphp

                                    <div class="main-title">
                                        <span>
                                            {{ $courseCode }}
                                            <span class="teacher-separator">|</span>
                                            {{ $lecturerName }}
                                        </span>
                                    </div>

                                    {{-- 🟢 FIX: Using $courseName instead of Assessment Name --}}
                                    <div class="sub-title">
                                        {{ $courseName }}
                                    </div>
                                </div>
                            </td>

                            <td class="text-left">
                                <div class="date-info">
                                    @php
                                        $opensAt = is_object($assessment)
                                            ? $assessment->opens_at
                                            : $assessment['opens_at'] ?? '';
                                        $closesAt = is_object($assessment)
                                            ? $assessment->closes_at
                                            : $assessment['closes_at'] ?? '';
                                    @endphp
                                    <i class="bi bi-calendar3"></i>
                                    {{ $opensAt ? \Carbon\Carbon::parse($opensAt)->format('M d') : '' }}
                                    - {{ $closesAt ? \Carbon\Carbon::parse($closesAt)->format('M d, Y') : '' }}
                                </div>
                            </td>

                            <td class="text-center">
                                @php
                                    $questionCount = is_object($assessment)
                                        ? $assessment->questions_count
                                        : (isset($assessment['questions_count'])
                                            ? $assessment['questions_count']
                                            : 0);
                                @endphp
                                {{ $questionCount }}
                            </td>

                            <td class="text-center">
                                @php
                                    $submittedCount = is_object($assessment)
                                        ? $assessment->submitted
                                        : (isset($assessment['submitted'])
                                            ? $assessment['submitted']
                                            : 0);
                                @endphp
                                {{ $submittedCount }}
                            </td>

                            <td class="text-left">
                                @php
                                    $status = is_object($assessment)
                                        ? $assessment->status
                                        : (isset($assessment['status'])
                                            ? $assessment['status']
                                            : 'draft');
                                @endphp
                                @if ($status == 'active')
                                    <span class="badge badge-active">Active</span>
                                @elseif($status == 'draft')
                                    <span class="badge badge-draft">Draft</span>
                                @else
                                    <span class="badge badge-closed">Closed</span>
                                @endif
                            </td>

                            {{-- 🟢 ACTIONS DATA CENTERED --}}
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="{{ route('admin.assessments.results', $id) }}" class="action-btn view"><i
                                        class="bi bi-bar-chart"></i> Results</a>

                                {{-- Toggle Button --}}
                                @if ($status == 'active')
                                    <form action="{{ route('admin.assessments.toggle', $id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="action-btn toggle"
                                            onclick="return confirm('Close this assessment? Students will no longer be able to submit.')">
                                            <i class="bi bi-lock"></i> Close
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.assessments.toggle', $id) }}" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="action-btn toggle"
                                            style="background:#dcfce7; color:#166534;">
                                            <i class="bi bi-unlock"></i> Open
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete Button --}}
                                <form action="{{ route('admin.assessments.destroy', $id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete"
                                        onclick="return confirm('Are you sure you want to delete this assessment? This cannot be undone.')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-folder2-open"></i>
                                    <h4>No Assessments Created Yet</h4>
                                    <p>Click the <strong>"New Assessment"</strong> button above to create your first
                                        evaluation form.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
