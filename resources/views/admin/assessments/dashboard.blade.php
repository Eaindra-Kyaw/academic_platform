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
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --radius: 12px;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-hover: 0 8px 25px rgba(10, 36, 99, 0.08);
            --transition: all 0.2s ease;
        }

        body {
            background-color: var(--bg-main);
        }

        /* 🟢 CLEAN HEADER */
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

        /* ============================================================
               🟢 YEAR GROUPED CARDS (WITH DEPT TABS)
               ============================================================ */
        .year-section {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            overflow: hidden;
            border: 1px solid rgba(10, 36, 99, 0.04);
            transition: var(--transition);
        }

        .year-section:hover {
            box-shadow: var(--shadow-hover);
        }

        .year-header {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .year-header h3 {
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .year-header h3 i {
            color: var(--primary);
        }

        .year-header .year-stats {
            font-size: 0.8rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .year-header .year-stats .count-badge {
            background: var(--primary);
            color: var(--white);
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .year-header .year-stats .count-badge.active-badge {
            background: #dcfce7;
            color: #166534;
        }

        .year-body {
            padding: 1rem 1.5rem 1.5rem 1.5rem;
        }

        /* 🟢 DEPARTMENT TABS */
        .dept-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }

        .dept-tab {
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            background: #f1f5f9;
            color: var(--text-gray);
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .dept-tab:hover {
            background: #e2e8f0;
        }

        .dept-tab.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary-light);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.2);
        }

        .dept-tab .tab-count {
            font-size: 0.6rem;
            background: rgba(0, 0, 0, 0.1);
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
        }

        .dept-tab.active .tab-count {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Inner Table */
        .year-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .year-table thead tr {
            border-bottom: 1px solid #e2e8f0;
        }

        .year-table th {
            padding: 0.6rem 0.5rem 0.6rem 0;
            color: var(--text-gray);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 0.5px;
            text-align: left;
        }

        .year-table td {
            padding: 0.8rem 0.5rem 0.8rem 0;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .year-table tbody tr:last-child td {
            border-bottom: none;
        }

        .year-table .course-detail {
            display: flex;
            flex-direction: column;
        }

        .year-table .course-detail .main-title {
            color: var(--text-dark);
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .year-table .course-detail .main-title .teacher-separator {
            color: var(--text-gray);
            font-weight: 400;
        }

        .year-table .course-detail .sub-title {
            color: var(--text-gray);
            font-size: 0.75rem;
            margin-top: 0.1rem;
        }

        .year-table .badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .year-table .badge-active {
            background: #dcfce7;
            color: #166534;
        }

        .year-table .badge-draft {
            background: #f1f5f9;
            color: #475569;
        }

        .year-table .badge-closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .year-table .date-info {
            font-size: 0.7rem;
            color: var(--text-gray);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .year-table .action-btn {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            border: none;
            font-size: 0.7rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .year-table .action-btn.view {
            background: #e0e7ff;
            color: #4338ca;
        }

        .year-table .action-btn.toggle {
            background: #fef3c7;
            color: #92400e;
        }

        .year-table .action-btn.delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .year-table .action-btn:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        /* Custom Modal Styles */
        .custom-confirm-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            padding: 20px;
            animation: overlayFadeIn 0.3s ease;
        }

        .custom-confirm-overlay.show {
            display: flex;
        }

        @keyframes overlayFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .custom-confirm-box {
            background: var(--white);
            border-radius: 16px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
            animation: modalSlideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .custom-confirm-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            background: #f8fafc;
        }

        .custom-confirm-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .custom-confirm-icon.warning {
            background: var(--warning-light);
            color: var(--warning);
        }

        .custom-confirm-icon.danger {
            background: #fee2e2;
            color: var(--danger);
        }

        .custom-confirm-icon.success {
            background: #d1fae5;
            color: var(--success);
        }

        .custom-confirm-title-group {
            flex: 1;
            min-width: 0;
        }

        .custom-confirm-title-group h4 {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
            font-size: 1.05rem;
        }

        .custom-confirm-title-group p {
            margin: 0.15rem 0 0;
            font-size: 0.85rem;
            color: var(--text-gray);
            line-height: 1.5;
        }

        .custom-confirm-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-gray);
            font-size: 1.1rem;
            padding: 0.2rem;
            transition: var(--transition);
            flex-shrink: 0;
            line-height: 1;
        }

        .custom-confirm-close:hover {
            color: #334155;
            transform: rotate(90deg);
        }

        .custom-confirm-body {
            padding: 1.25rem 1.5rem;
        }

        .custom-confirm-details {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .confirm-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0.75rem;
            background: #f8fafc;
            border-radius: 8px;
            font-size: 0.8rem;
        }

        .confirm-detail-row .confirm-detail-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--text-gray);
            font-weight: 500;
        }

        .confirm-detail-row .confirm-detail-value {
            font-weight: 600;
            color: #1e293b;
        }

        .custom-confirm-footer {
            padding: 1rem 1.5rem;
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            background: #f8fafc;
        }

        .custom-confirm-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: 'Inter', sans-serif;
        }

        .custom-confirm-btn.cancel {
            background: #f1f5f9;
            color: var(--text-gray);
        }

        .custom-confirm-btn.cancel:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .custom-confirm-btn.primary {
            background: var(--primary);
            color: var(--white);
        }

        .custom-confirm-btn.primary:hover {
            background: #061840;
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
            transform: translateY(-1px);
        }

        .custom-confirm-btn.danger {
            background: var(--danger);
            color: var(--white);
        }

        .custom-confirm-btn.danger:hover {
            background: #b91c1c;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .custom-confirm-box {
                margin: 10px;
                max-height: 95vh;
                overflow-y: auto;
            }

            .custom-confirm-header {
                flex-wrap: wrap;
            }

            .custom-confirm-title-group {
                width: 100%;
                margin-left: 0;
            }

            .confirm-detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.2rem;
            }

            .custom-confirm-footer {
                flex-direction: column-reverse;
            }

            .custom-confirm-btn {
                width: 100%;
                justify-content: center;
            }
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

            .year-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .dept-tabs {
                flex-wrap: wrap;
                gap: 0.3rem;
            }

            .dept-tab {
                padding: 0.3rem 0.8rem;
                font-size: 0.7rem;
            }

            .year-table {
                font-size: 0.75rem;
            }

            .year-table th,
            .year-table td {
                padding: 0.4rem 0.3rem;
            }

            .year-table .action-btn {
                padding: 0.2rem 0.4rem;
                font-size: 0.6rem;
            }
        }
    </style>

    {{-- 🟢 HEADER --}}
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

    {{-- 🟢 YEAR GROUPED SECTIONS WITH DEPT TABS --}}
    @php
        $years = ['First Year', 'Second Year', 'Third Year', 'Fourth Year', 'Fifth Year', 'Sixth Year'];

        // 1. Group by Year, then by Department ID
        $groupedData = [];
        foreach ($years as $year) {
            $groupedData[$year] = [];
        }

        foreach ($assessments as $assess) {
            $year = is_object($assess) ? $assess->year : (isset($assess['year']) ? $assess['year'] : '');

            // Safely extract department name from the course array/object
            $courseData = is_object($assess) ? $assess->course : (isset($assess['course']) ? $assess['course'] : null);
            $deptName = 'General';
            if (is_object($courseData) && isset($courseData->department) && is_object($courseData->department)) {
                $deptName = $courseData->department->name ?? ($courseData->department->code ?? 'General');
            } elseif (
                is_array($courseData) &&
                isset($courseData['department']) &&
                is_array($courseData['department'])
            ) {
                $deptName = $courseData['department']['name'] ?? ($courseData['department']['code'] ?? 'General');
            }

            if (!isset($groupedData[$year][$deptName])) {
                $groupedData[$year][$deptName] = [];
            }
            $groupedData[$year][$deptName][] = $assess;
        }
    @endphp

    @foreach ($years as $year)
        @if (count($groupedData[$year]) > 0)
            <div class="year-section">
                {{-- Year Header --}}
                <div class="year-header">
                    <h3>
                        <i class="bi bi-calendar4-range"></i> {{ $year }} Assessments
                    </h3>
                    <div class="year-stats">
                        @php
                            $totalInYear = 0;
                            $activeInYear = 0;
                            foreach ($groupedData[$year] as $dept => $items) {
                                $totalInYear += count($items);
                                foreach ($items as $a) {
                                    $stat = is_object($a) ? $a->status : (isset($a['status']) ? $a['status'] : 'draft');
                                    if ($stat == 'active') {
                                        $activeInYear++;
                                    }
                                }
                            }
                        @endphp
                        <span class="count-badge">{{ $totalInYear }} Total</span>
                        @if ($activeInYear > 0)
                            <span class="count-badge active-badge">{{ $activeInYear }} Active</span>
                        @endif
                    </div>
                </div>

                <div class="year-body">
                    {{-- Department Tabs (JavaScript will handle visibility) --}}
                    @php
                        $deptKeys = array_keys($groupedData[$year]);
                        $firstDept = $deptKeys[0] ?? null;
                    @endphp

                    <div class="dept-tabs">
                        @foreach ($groupedData[$year] as $dept => $items)
                            <button class="dept-tab {{ $loop->first ? 'active' : '' }}"
                                onclick="switchDept('{{ Str::slug($year) }}-{{ Str::slug($dept) }}', this)"
                                data-target="dept-{{ Str::slug($year) }}-{{ Str::slug($dept) }}">
                                {{ $dept }}
                                <span class="tab-count">{{ count($items) }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Department Tables --}}
                    @foreach ($groupedData[$year] as $dept => $items)
                        <div id="dept-{{ Str::slug($year) }}-{{ Str::slug($dept) }}"
                            class="dept-content {{ $loop->first ? '' : 'd-none' }}">
                            <table class="year-table">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Course & Lecturer</th>
                                        <th style="width: 20%;">Dates</th>
                                        <th style="width: 10%;">Q's</th>
                                        <th style="width: 10%;">Subs</th>
                                        <th style="width: 10%;">Status</th>
                                        <th style="width: 15%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $assessment)
                                        <tr>
                                            <td>
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
                                                        $id = is_object($assessment)
                                                            ? $assessment->id
                                                            : $assessment['id'] ?? 0;
                                                        $displayName = is_object($assessment)
                                                            ? $assessment->name
                                                            : (is_array($assessment) && isset($assessment['name'])
                                                                ? $assessment['name']
                                                                : '');
                                                    @endphp
                                                    <div class="main-title">
                                                        <span>
                                                            {{ $courseCode }} <span class="teacher-separator">|</span>
                                                            {{ $lecturerName }}
                                                        </span>
                                                    </div>
                                                    <div class="sub-title">
                                                        {{ $courseName }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
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
                                                    -
                                                    {{ $closesAt ? \Carbon\Carbon::parse($closesAt)->format('M d, Y') : '' }}
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $questionCount = is_object($assessment)
                                                        ? $assessment->questions_count
                                                        : (isset($assessment['questions_count'])
                                                            ? $assessment['questions_count']
                                                            : 0);
                                                @endphp
                                                {{ $questionCount }}
                                            </td>
                                            <td>
                                                @php
                                                    $submittedCount = is_object($assessment)
                                                        ? $assessment->submitted
                                                        : (isset($assessment['submitted'])
                                                            ? $assessment['submitted']
                                                            : 0);
                                                @endphp
                                                {{ $submittedCount }}
                                            </td>
                                            <td>
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
                                            <td style="white-space: nowrap;">
                                                <a href="{{ route('admin.assessments.results', $id) }}"
                                                    class="action-btn view"><i class="bi bi-bar-chart"></i> Results</a>

                                                @if ($status == 'active')
                                                    <form action="{{ route('admin.assessments.toggle', $id) }}"
                                                        method="POST" style="display:inline;"
                                                        id="closeForm_{{ $id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="button" class="action-btn toggle"
                                                            onclick="confirmCloseAssessment({{ $id }}, '{{ addslashes($displayName) }}')">
                                                            <i class="bi bi-lock"></i> Close
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.assessments.toggle', $id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-btn toggle"
                                                            style="background:#dcfce7; color:#166534;">
                                                            <i class="bi bi-unlock"></i> Open
                                                        </button>
                                                    </form>
                                                @endif

                                                <form action="{{ route('admin.assessments.destroy', $id) }}" method="POST"
                                                    style="display:inline;" id="deleteForm_{{ $id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="action-btn delete"
                                                        onclick="confirmDeleteAssessment({{ $id }}, '{{ addslashes($displayName) }}')">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    {{-- 🟢 CUSTOM CONFIRM MODAL HTML --}}
    <div class="custom-confirm-overlay" id="customConfirmModal">
        <div class="custom-confirm-box">
            <div class="custom-confirm-header">
                <div class="custom-confirm-icon warning" id="modalIcon">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="custom-confirm-title-group">
                    <h4 id="modalTitle">Are you sure?</h4>
                    <p id="modalMessage">This action cannot be undone.</p>
                </div>
                <button class="custom-confirm-close" onclick="closeCustomModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="custom-confirm-body">
                <div class="custom-confirm-details" id="modalDetails"></div>
            </div>
            <div class="custom-confirm-footer">
                <button class="custom-confirm-btn cancel" onclick="closeCustomModal()">
                    <i class="bi bi-x-lg"></i> Cancel
                </button>
                <button class="custom-confirm-btn danger" id="modalConfirmBtn">
                    <i class="bi bi-check-lg"></i> Confirm
                </button>
            </div>
        </div>
    </div>

    <script>
        // 🟢 SWITCH DEPARTMENT TABS
        function switchDept(targetId, btnElement) {
            // Hide all contents in the same year section
            const parentSection = btnElement.closest('.year-body');
            const allContents = parentSection.querySelectorAll('.dept-content');
            allContents.forEach(el => el.classList.add('d-none'));

            // Show the target
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.remove('d-none');
            }

            // Update active state on tabs
            const allTabs = parentSection.querySelectorAll('.dept-tab');
            allTabs.forEach(tab => tab.classList.remove('active'));
            btnElement.classList.add('active');
        }

        // 🟢 GLOBAL MODAL FUNCTIONS
        let modalAction = null;

        function openCustomModal(title, message, detailsHtml, confirmText, confirmClass, actionCallback) {
            const modal = document.getElementById('customConfirmModal');
            document.getElementById('modalTitle').innerText = title;
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('modalDetails').innerHTML = detailsHtml;

            const btn = document.getElementById('modalConfirmBtn');
            btn.innerText = confirmText;
            btn.className = 'custom-confirm-btn ' + confirmClass;

            const icon = document.getElementById('modalIcon');
            if (confirmClass === 'danger') {
                icon.className = 'custom-confirm-icon danger';
                icon.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>';
            } else if (confirmClass === 'warning') {
                icon.className = 'custom-confirm-icon warning';
                icon.innerHTML = '<i class="bi bi-info-circle-fill"></i>';
            } else {
                icon.className = 'custom-confirm-icon success';
                icon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
            }

            modalAction = actionCallback;
            modal.classList.add('show');
        }

        function closeCustomModal() {
            document.getElementById('customConfirmModal').classList.remove('show');
            modalAction = null;
        }

        // 🟢 CONFIRM CLOSE ASSESSMENT
        function confirmCloseAssessment(id, name) {
            const detailsHtml = `
                <div class="confirm-detail-row">
                    <span class="confirm-detail-label"><i class="bi bi-file-earmark-text" style="color: var(--primary);"></i> Assessment</span>
                    <span class="confirm-detail-value"><strong>${name}</strong></span>
                </div>
                <div class="confirm-detail-row">
                    <span class="confirm-detail-label"><i class="bi bi-lock" style="color: var(--danger);"></i> Action</span>
                    <span class="confirm-detail-value" style="color: var(--danger);"><strong>Close permanently</strong></span>
                </div>
            `;
            openCustomModal(
                'Close Assessment?',
                'Students will no longer be able to submit responses for this assessment.',
                detailsHtml,
                'Close Assessment',
                'danger',
                function() {
                    document.getElementById('closeForm_' + id).submit();
                }
            );
        }

        // 🟢 CONFIRM DELETE ASSESSMENT
        function confirmDeleteAssessment(id, name) {
            const detailsHtml = `
                <div class="confirm-detail-row">
                    <span class="confirm-detail-label"><i class="bi bi-file-earmark-x" style="color: var(--danger);"></i> Assessment</span>
                    <span class="confirm-detail-value"><strong>${name}</strong></span>
                </div>
                <div class="confirm-detail-row">
                    <span class="confirm-detail-label"><i class="bi bi-trash3" style="color: var(--danger);"></i> Action</span>
                    <span class="confirm-detail-value" style="color: var(--danger);"><strong>Delete permanently (Cannot be undone)</strong></span>
                </div>
            `;
            openCustomModal(
                'Delete Assessment?',
                'This will permanently delete the assessment, all questions, and all student submissions. This cannot be undone.',
                detailsHtml,
                'Delete Permanently',
                'danger',
                function() {
                    document.getElementById('deleteForm_' + id).submit();
                }
            );
        }

        // 🟢 Auto-initialize the confirm button
        document.getElementById('modalConfirmBtn').addEventListener('click', function() {
            if (modalAction) {
                modalAction();
            }
            closeCustomModal();
        });
    </script>
@endsection
