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
            --warning-light: #fef3c7;
            --radius: 12px;
            --shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
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

        /* ============================================================
                       🟢 CUSTOM CONFIRM MODAL
                       ============================================================ */
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

        .custom-confirm-btn.warning {
            background: var(--warning);
            color: var(--white);
        }

        .custom-confirm-btn.warning:hover {
            background: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
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
        }
    </style>

    {{-- 🟢 FIXED HEADER --}}
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
                        <th class="text-left" style="width: 35%;">Title & Course</th>
                        <th class="text-left" style="width: 20%;">Dates</th>
                        <th class="text-center" style="width: 10%;">Questions</th>
                        <th class="text-center" style="width: 10%;">Submissions</th>
                        <th class="text-left" style="width: 10%;">Status</th>
                        <th class="text-center" style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assessments as $assessment)
                        <tr>
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
                                            {{ $courseCode }} <span class="teacher-separator">|</span> {{ $lecturerName }}
                                        </span>
                                    </div>
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
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="{{ route('admin.assessments.results', $id) }}" class="action-btn view"><i
                                        class="bi bi-bar-chart"></i> Results</a>

                                {{-- Toggle Button --}}
                                @if ($status == 'active')
                                    <form action="{{ route('admin.assessments.toggle', $id) }}" method="POST"
                                        style="display:inline;" id="closeForm_{{ $id }}">
                                        @csrf
                                        @method('PUT')
                                        {{-- 🟢 FIX: Extracting name safely for both Object and Array --}}
                                        @php
                                            $displayName = is_object($assessment)
                                                ? $assessment->name
                                                : (is_array($assessment) && isset($assessment['name'])
                                                    ? $assessment['name']
                                                    : '');
                                        @endphp
                                        <button type="button" class="action-btn toggle"
                                            onclick="confirmCloseAssessment({{ $id }}, '{{ addslashes($displayName) }}')">
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
                                    style="display:inline;" id="deleteForm_{{ $id }}">
                                    @csrf
                                    @method('DELETE')
                                    {{-- 🟢 FIX: Extracting name safely for both Object and Array --}}
                                    @php
                                        $displayName = is_object($assessment)
                                            ? $assessment->name
                                            : (is_array($assessment) && isset($assessment['name'])
                                                ? $assessment['name']
                                                : '');
                                    @endphp
                                    <button type="button" class="action-btn delete"
                                        onclick="confirmDeleteAssessment({{ $id }}, '{{ addslashes($displayName) }}')">
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

            // Update icon based on class
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
