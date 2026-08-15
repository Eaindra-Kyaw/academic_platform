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
            --radius: 12px;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

        /* 🟢 SEARCH & FILTER BAR */
        .control-bar {
            background: var(--white);
            border-radius: var(--radius);
            padding: 0.75rem 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: var(--shadow);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.04);
        }

        .control-bar .search-wrap {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            min-width: 200px;
        }

        .control-bar .search-wrap i {
            color: var(--text-gray);
            font-size: 1.1rem;
        }

        .control-bar .search-wrap input {
            flex: 1;
            padding: 0.4rem 0;
            border: none;
            background: transparent;
            font-size: 0.9rem;
            outline: none;
            color: var(--text-dark);
            font-family: 'Inter', sans-serif;
        }

        .control-bar .search-wrap input::placeholder {
            color: var(--text-gray);
        }

        .control-bar .status-tabs {
            display: flex;
            gap: 0.3rem;
        }

        .control-bar .status-tabs .tab-btn {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            border: 1px solid transparent;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            background: #f1f5f9;
            color: var(--text-gray);
        }

        .control-bar .status-tabs .tab-btn:hover {
            background: #e2e8f0;
        }

        .control-bar .status-tabs .tab-btn.active {
            background: var(--primary);
            color: var(--white);
            box-shadow: 0 2px 8px rgba(10, 36, 99, 0.15);
        }

        /* 🟢 CARD GRID VIEW */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.25rem;
        }

        .assess-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem;
            border: 1px solid rgba(10, 36, 99, 0.04);
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }

        .assess-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary-light);
        }

        .assess-card .top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }

        .assess-card .top-row .course-code {
            font-weight: 700;
            font-size: 1rem;
            color: var(--primary);
        }

        .assess-card .top-row .year-badge {
            font-size: 0.6rem;
            font-weight: 600;
            background: #e2e8f0;
            color: var(--text-gray);
            padding: 0.15rem 0.6rem;
            border-radius: 12px;
            white-space: nowrap;
        }

        .assess-card .course-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
            margin-bottom: 0.1rem;
        }

        .assess-card .lecturer {
            color: var(--text-gray);
            font-size: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .assess-card .meta-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            font-size: 0.75rem;
            color: var(--text-gray);
            margin-bottom: 0.8rem;
            align-items: center;
        }

        .assess-card .meta-row i {
            margin-right: 0.2rem;
            color: var(--primary-light);
        }

        .assess-card .meta-row .q-count {
            background: #f1f5f9;
            padding: 0.1rem 0.5rem;
            border-radius: 10px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .assess-card .bottom-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 0.8rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .assess-card .bottom-row .status-badge {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .assess-card .bottom-row .status-badge.active {
            background: #dcfce7;
            color: #166534;
        }

        .assess-card .bottom-row .status-badge.draft {
            background: #f1f5f9;
            color: #475569;
        }

        .assess-card .bottom-row .status-badge.closed {
            background: #fee2e2;
            color: #991b1b;
        }

        .assess-card .bottom-row .actions {
            display: flex;
            gap: 0.3rem;
        }

        .btn-sm {
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
        }

        .btn-sm.view {
            background: #e0e7ff;
            color: #4338ca;
        }

        .btn-sm.toggle {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-sm.delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-gray);
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
            display: block;
        }

        /* Custom Modal */
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
            background: #fef3c7;
            color: #92400e;
        }

        .custom-confirm-icon.danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .custom-confirm-icon.success {
            background: #d1fae5;
            color: #166534;
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

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .control-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }

            .control-bar .search-wrap {
                width: 100%;
            }
        }
    </style>

    {{-- HEADER --}}
    <div class="header-wrapper">
        <a href="{{ route('admin.assessments.create') }}" class="btn-primary">
            <i class="bi bi-plus-lg"></i> New Assessment
        </a>
    </div>

    {{-- Stats --}}
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
                <h4>{{ $statusCounts['active'] ?? 0 }}</h4>
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

    {{-- Control Bar: Search + Status Filter --}}
    <div class="control-bar">
        <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Search course code, name, or lecturer..."
                autocomplete="off">
        </div>
        <div class="status-tabs">
            <button class="tab-btn active" data-status="all">All</button>
            <button class="tab-btn" data-status="active">Active</button>
            <button class="tab-btn" data-status="draft">Draft</button>
            <button class="tab-btn" data-status="closed">Closed</button>
        </div>
    </div>

    {{-- Cards Grid --}}
    <div class="cards-grid" id="cardsContainer">
        @forelse($assessments as $assessment)
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
                $lecturerName = is_object($assessment)
                    ? $assessment->lecturer['name'] ?? ''
                    : (isset($assessment['lecturer'])
                        ? $assessment['lecturer']['name'] ?? ''
                        : '');
                $year = is_object($assessment)
                    ? $assessment->year
                    : (isset($assessment['year'])
                        ? $assessment['year']
                        : '');
                $status = is_object($assessment)
                    ? $assessment->status
                    : (isset($assessment['status'])
                        ? $assessment['status']
                        : 'draft');
                $id = is_object($assessment) ? $assessment->id : $assessment['id'] ?? 0;
                $displayName = is_object($assessment)
                    ? $assessment->name
                    : (is_array($assessment) && isset($assessment['name'])
                        ? $assessment['name']
                        : '');
                $questionCount = is_object($assessment)
                    ? $assessment->questions_count
                    : (isset($assessment['questions_count'])
                        ? $assessment['questions_count']
                        : 0);
                $submittedCount = is_object($assessment)
                    ? $assessment->submitted
                    : (isset($assessment['submitted'])
                        ? $assessment['submitted']
                        : 0);
                $opensAt = is_object($assessment) ? $assessment->opens_at : $assessment['opens_at'] ?? '';
                $closesAt = is_object($assessment) ? $assessment->closes_at : $assessment['closes_at'] ?? '';
            @endphp

            <div class="assess-card search-item" data-status="{{ $status }}"
                data-search="{{ strtolower($courseCode . ' ' . $courseName . ' ' . $lecturerName) }}">
                <div class="top-row">
                    <span class="course-code">{{ $courseCode }}</span>
                    <span class="year-badge"><i class="bi bi-calendar"></i> {{ $year }}</span>
                </div>
                <div class="course-name">{{ $courseName }}</div>
                <div class="lecturer"><i class="bi bi-person-vcard"></i> {{ $lecturerName }}</div>

                <div class="meta-row">
                    <span><i class="bi bi-calendar-event"></i>
                        {{ $opensAt ? \Carbon\Carbon::parse($opensAt)->format('M d') : '' }} -
                        {{ $closesAt ? \Carbon\Carbon::parse($closesAt)->format('M d, Y') : '' }}</span>
                    <span class="q-count"><i class="bi bi-question-circle"></i> {{ $questionCount }}</span>
                    <span><i class="bi bi-people"></i> {{ $submittedCount }} subs</span>
                </div>

                <div class="bottom-row">
                    <span class="status-badge {{ $status }}">{{ ucfirst($status) }}</span>
                    <div class="actions">
                        <a href="{{ route('admin.assessments.results', $id) }}" class="btn-sm view"><i
                                class="bi bi-bar-chart"></i> Results</a>
                        @if ($status == 'active')
                            <button class="btn-sm toggle"
                                onclick="confirmCloseAssessment({{ $id }}, '{{ addslashes($displayName) }}')"><i
                                    class="bi bi-lock"></i> Close</button>
                        @else
                            <form action="{{ route('admin.assessments.toggle', $id) }}" method="POST"
                                style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn-sm toggle" style="background:#dcfce7; color:#166534;"><i
                                        class="bi bi-unlock"></i> Open</button>
                            </form>
                        @endif
                        <button class="btn-sm delete"
                            onclick="confirmDeleteAssessment({{ $id }}, '{{ addslashes($displayName) }}')"><i
                                class="bi bi-trash3"></i></button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>No Assessments Created Yet</h4>
                <p>Click the <strong>"New Assessment"</strong> button to create your first form.</p>
            </div>
        @endforelse
    </div>

    {{-- Custom Confirm Modal --}}
    <div class="custom-confirm-overlay" id="customConfirmModal">
        <div class="custom-confirm-box">
            <div class="custom-confirm-header">
                <div class="custom-confirm-icon warning" id="modalIcon"><i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div class="custom-confirm-title-group">
                    <h4 id="modalTitle">Are you sure?</h4>
                    <p id="modalMessage">This action cannot be undone.</p>
                </div>
                <button class="custom-confirm-close" onclick="closeCustomModal()"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="custom-confirm-body">
                <div class="custom-confirm-details" id="modalDetails"></div>
            </div>
            <div class="custom-confirm-footer">
                <button class="custom-confirm-btn cancel" onclick="closeCustomModal()"><i class="bi bi-x-lg"></i>
                    Cancel</button>
                <button class="custom-confirm-btn danger" id="modalConfirmBtn"><i class="bi bi-check-lg"></i>
                    Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // 🟢 LIVE SEARCH & STATUS FILTER
        const searchInput = document.getElementById('searchInput');
        const statusBtns = document.querySelectorAll('.tab-btn');
        const cards = document.querySelectorAll('.assess-card');

        function filterCards() {
            const query = searchInput.value.toLowerCase().trim();
            const activeStatus = document.querySelector('.tab-btn.active').dataset.status;

            cards.forEach(card => {
                const matchStatus = activeStatus === 'all' || card.dataset.status === activeStatus;
                const matchSearch = card.dataset.search.includes(query);
                card.style.display = (matchStatus && matchSearch) ? 'block' : 'none';
            });
        }

        searchInput.addEventListener('input', filterCards);

        statusBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                statusBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                filterCards();
            });
        });

        // 🟢 MODAL FUNCTIONS
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
            icon.className = 'custom-confirm-icon ' + confirmClass;
            modalAction = actionCallback;
            modal.classList.add('show');
        }

        function closeCustomModal() {
            document.getElementById('customConfirmModal').classList.remove('show');
            modalAction = null;
        }
        document.getElementById('modalConfirmBtn').addEventListener('click', function() {
            if (modalAction) {
                modalAction();
            }
            closeCustomModal();
        });

        function confirmCloseAssessment(id, name) {
            openCustomModal('Close Assessment?', 'Students will no longer be able to submit.',
                `<div class="confirm-detail-row"><span class="confirm-detail-label">Assessment</span><span class="confirm-detail-value"><strong>${name}</strong></span></div>`,
                'Close', 'danger',
                function() {
                    document.getElementById('closeForm_' + id).submit();
                });
        }

        function confirmDeleteAssessment(id, name) {
            openCustomModal('Delete Assessment?', 'This permanently deletes all questions and submissions.',
                `<div class="confirm-detail-row"><span class="confirm-detail-label">Assessment</span><span class="confirm-detail-value"><strong>${name}</strong></span></div>`,
                'Delete', 'danger',
                function() {
                    document.getElementById('deleteForm_' + id).submit();
                });
        }
    </script>
@endsection
