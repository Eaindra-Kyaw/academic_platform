{{-- resources/views/admin/enrollments/course.blade.php --}}
@extends('layouts.app')

@section('title', 'Enrollment Management - ' . $course->course_code)
@section('role', 'Admin')
@section('page-title', $course->course_code . ' - ' . $course->course_name)
@section('welcome-text', 'Students enrolled in ' . $course->course_code)

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
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

        .breadcrumb-bar .badge-filter {
            background: #800000;
            color: white;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .course-header {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        .course-header .title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1f2937;
        }

        .course-header .code {
            color: #800000;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .course-header .meta {
            display: flex;
            gap: 1.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .course-header .meta span {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .course-header .meta strong {
            color: #1f2937;
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

        .stat-number.pending {
            color: #d97706;
        }

        .stat-number.approved {
            color: #10b981;
        }

        .stat-number.rejected {
            color: #ef4444;
        }

        .stat-number.total {
            color: #6366f1;
        }

        .stat-label {
            font-size: 0.65rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .table-wrapper {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .table-scroll {
            overflow-x: auto;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            min-width: 600px;
        }

        .student-table thead {
            background: #fafafa;
            border-bottom: 2px solid #e5e7eb;
        }

        .student-table th {
            padding: 0.7rem 1rem;
            text-align: left;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .student-table td {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .student-table tbody tr:hover {
            background: #fafafa;
        }

        .student-table tbody tr:last-child td {
            border-bottom: none;
        }

        .student-cell .name {
            font-weight: 600;
            color: #1f2937;
            font-size: 0.9rem;
        }

        .student-cell .email {
            color: #9ca3af;
            font-size: 0.7rem;
            display: block;
        }

        .student-cell .student-id {
            color: #6b7280;
            font-size: 0.65rem;
            display: block;
        }

        .student-cell .year-badge {
            display: inline-block;
            background: #f3f4f6;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            color: #6b7280;
            margin-top: 0.2rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-buttons {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.25rem 0.6rem;
            border-radius: 0.3rem;
            font-size: 0.7rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            transition: all 0.15s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }

        .btn-approve:hover {
            background: #059669;
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
        }

        .btn-view {
            background: #eff6ff;
            color: #2563eb;
        }

        .btn-view:hover {
            background: #dbeafe;
        }

        .row-number {
            text-align: center;
            font-weight: 500;
            color: #9ca3af;
            font-size: 0.75rem;
            width: 30px;
        }

        .pagination-wrapper {
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .pagination-wrapper .info {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #d1d5db;
        }

        .empty-state p {
            margin-top: 0.5rem;
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

        /* ===== MODALS ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            max-width: 600px;
            width: 92%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h4 {
            margin: 0;
            color: #800000;
            font-size: 1.1rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.4rem;
            cursor: pointer;
            color: #9ca3af;
            padding: 0 0.3rem;
            transition: color 0.2s;
            line-height: 1;
        }

        .modal-close:hover {
            color: #1f2937;
        }

        .modal-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .modal-btn {
            flex: 1;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-align: center;
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .modal-btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .modal-btn-cancel:hover {
            background: #e5e7eb;
        }

        .modal-btn-danger {
            background: #dc2626;
            color: white;
        }

        .modal-btn-danger:hover {
            background: #b91c1c;
        }

        .reject-textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            margin: 0.75rem 0;
            resize: vertical;
            font-family: inherit;
        }

        .reject-textarea:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #800000;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0 auto 0.75rem;
        }

        .profile-name {
            text-align: center;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.1rem;
        }

        .profile-email {
            text-align: center;
            color: #6b7280;
            font-size: 0.8rem;
            margin-bottom: 0.75rem;
        }

        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin: 0.75rem 0;
        }

        .profile-stat {
            text-align: center;
            padding: 0.5rem;
            background: #f9fafb;
            border-radius: 0.5rem;
        }

        .profile-stat .number {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .profile-stat .label {
            font-size: 0.6rem;
            color: #6b7280;
            display: block;
            margin-top: 0.1rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem 1.5rem;
            margin: 0.75rem 0;
        }

        .detail-item .label {
            font-size: 0.65rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-item .value {
            font-size: 0.85rem;
            color: #1f2937;
            word-break: break-word;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-number {
                font-size: 1.4rem;
            }

            .course-header .meta {
                gap: 0.75rem;
                flex-direction: column;
            }

            .breadcrumb-bar {
                font-size: 0.75rem;
            }

            .pagination-wrapper {
                flex-direction: column;
                text-align: center;
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
    <a href="{{ route('admin.enrollments.department', ['departmentId' => $course->department_id]) }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to {{ $course->department->name ?? 'Department' }}
    </a>

    {{-- Breadcrumb --}}
    {{-- <div class="breadcrumb-bar">
        <span class="breadcrumb-item">
            <i class="bi bi-house-door"></i>
            <a href="{{ route('admin.enrollments.index') }}">Enrollments</a>
        </span>
        <span class="breadcrumb-item">
            <span class="separator">/</span>
            <a href="{{ route('admin.enrollments.department', ['departmentId' => $course->department_id]) }}">
                {{ $course->department->name ?? 'Department' }}
            </a>
        </span>
        <span class="breadcrumb-item">
            <span class="separator">/</span>
            <span class="badge-filter">{{ $course->course_code }}</span>
        </span>
        <span class="breadcrumb-item active" style="margin-left:auto;">
            {{ $enrollments->total() }} student(s)
        </span>
    </div> --}}

    {{-- Course Header --}}
    {{-- <div class="course-header">
        <div>
            <span class="code">{{ $course->course_code }}</span>
            <span class="title">{{ $course->course_name }}</span>
            @if (!empty($course->year))
                <span class="badge-filter" style="margin-left:0.5rem; font-size:0.7rem;">
                    📅 Year {{ $course->year }}
                </span>
            @endif
        </div>
        <div class="meta">
            @if (!empty($course->credits))
                <span>📚 <strong>{{ $course->credits }}</strong> Credits</span>
            @endif
            <span>🏛️ <strong>{{ $course->department->name ?? 'N/A' }}</strong></span>
            @if (!empty($course->lecturer_name))
                <span>👨‍🏫 <strong>{{ $course->lecturer_name }}</strong></span>
            @endif
            @if (!empty($course->semester))
                <span>📖 <strong>{{ $course->semester }}</strong></span>
            @endif
            @if (!empty($course->academic_year))
                <span>📅 <strong>{{ $course->academic_year }}</strong></span>
            @endif
        </div>
    </div> --}}

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number pending">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label">⏳ Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-number approved">{{ $stats['approved'] ?? 0 }}</div>
            <div class="stat-label">✅ Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-number rejected">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="stat-label">❌ Rejected</div>
        </div>
        <div class="stat-card">
            <div class="stat-number total">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label">📊 Total Students</div>
        </div>
    </div>

    {{-- Students Table --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="student-table">
                <thead>
                    <tr>
                        <th class="row-number">#</th>
                        <th style="min-width:180px;">Student</th>
                        <th style="min-width:100px;">Student ID</th>
                        <th style="min-width:90px;">Enrolled Date</th>
                        <th style="min-width:90px;">Status</th>
                        <th style="min-width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $index => $enrollment)
                        @php
                            $rowNumber = ($enrollments->currentPage() - 1) * $enrollments->perPage() + $index + 1;
                            $student = $enrollment->student;
                        @endphp
                        <tr>
                            <td class="row-number">{{ $rowNumber }}</td>
                            <td class="student-cell">
                                <div class="name">{{ $student->name ?? 'N/A' }}</div>
                                <span class="email">{{ $student->email ?? 'N/A' }}</span>
                                @if (!empty($student->current_year))
                                    <span class="year-badge">Year {{ $student->current_year }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $student->student_id ?? 'N/A' }}</strong>
                            </td>
                            <td style="font-size:0.75rem; white-space:nowrap;">
                                {{ $enrollment->created_at ? $enrollment->created_at->format('d M Y') : 'N/A' }}
                            </td>
                            <td>
                                @if ($enrollment->status == 'pending')
                                    <span class="status-badge status-pending"><i class="bi bi-clock-history"></i>
                                        Pending</span>
                                @elseif($enrollment->status == 'approved')
                                    <span class="status-badge status-approved"><i class="bi bi-check-circle"></i>
                                        Approved</span>
                                @elseif($enrollment->status == 'rejected')
                                    <span class="status-badge status-rejected"><i class="bi bi-x-circle"></i>
                                        Rejected</span>
                                @else
                                    <span class="status-badge" style="background:#f3f4f6; color:#6b7280;">
                                        <i class="bi bi-dash-circle"></i> {{ ucfirst($enrollment->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if ($enrollment->status == 'pending')
                                        <a href="{{ url('/admin/enrollments/' . $enrollment->id . '/approve') }}"
                                            class="btn-sm btn-approve" onclick="return confirm('Approve this enrollment?')"
                                            title="Approve">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <button class="btn-sm btn-reject" onclick="showRejectModal({{ $enrollment->id }})"
                                            title="Reject">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                    <button class="btn-sm btn-view" onclick="showStudentProfile({{ $student->id ?? 0 }})"
                                        title="View Student Profile">
                                        <i class="bi bi-person"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <p>No students enrolled in this course</p>
                                    <small style="color:#9ca3af;">No enrollment requests found for
                                        {{ $course->course_code }}</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($enrollments->hasPages())
            <div class="pagination-wrapper">
                <div class="info">
                    Showing {{ $enrollments->firstItem() ?? 0 }} to {{ $enrollments->lastItem() ?? 0 }}
                    of {{ $enrollments->total() }} students
                </div>
                <div>
                    {{ $enrollments->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- MODALS --}}
    {{-- ============================================================ --}}

    {{-- Student Profile Modal --}}
    <div id="studentProfileModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="bi bi-person-circle"></i> Student Profile</h4>
                <button class="modal-close" onclick="closeModal('studentProfileModal')">&times;</button>
            </div>
            <div id="profileContent">
                <div class="text-center py-3">
                    <div class="spinner-border text-muted" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div id="rejectModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4 style="color: #dc2626;"><i class="bi bi-exclamation-triangle"></i> Reject Enrollment</h4>
                <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <p id="rejectModalMessage" style="font-size: 0.85rem; color: #6b7280;"></p>
            <textarea id="rejectionReason" class="reject-textarea" rows="3"
                placeholder="Please provide a reason for rejection..."></textarea>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="modal-btn modal-btn-danger" id="confirmRejectBtn">Confirm Rejection</button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- JAVASCRIPT --}}
    {{-- ============================================================ --}}
    <script>
        let currentRejectId = null;

        function showStudentProfile(studentId) {
            if (!studentId) {
                alert('Student information not available.');
                return;
            }

            const modal = document.getElementById('studentProfileModal');
            const content = document.getElementById('profileContent');

            content.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-muted" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted" style="font-size:0.8rem;">Loading student profile...</p>
                </div>
            `;

            modal.classList.add('show');

            fetch('/admin/enrollments/student/' + studentId)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.error) throw new Error(data.error);
                    content.innerHTML = `
                        <div class="profile-avatar">${data.name ? data.name.charAt(0).toUpperCase() : '?'}</div>
                        <div class="profile-name">${data.name || 'N/A'}</div>
                        <div class="profile-email">${data.email || 'N/A'}</div>

                        <div class="profile-stats">
                            <div class="profile-stat">
                                <span class="number">${data.current_year || 'N/A'}</span>
                                <span class="label">Year</span>
                            </div>
                            <div class="profile-stat">
                                <span class="number">${data.gpa || 'N/A'}</span>
                                <span class="label">GPA</span>
                            </div>
                            <div class="profile-stat">
                                <span class="number">${data.total_credits || 0}</span>
                                <span class="label">Credits</span>
                            </div>
                        </div>

                        <div class="detail-grid">
                            <div class="detail-item">
                                <span class="label">Student ID</span>
                                <span class="value">${data.student_id || 'N/A'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Department</span>
                                <span class="value">${data.department || 'N/A'}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Enrollments</span>
                                <span class="value">${data.enrollment_count || 0}</span>
                            </div>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="label">Current Courses</span>
                                <span class="value" style="font-size:0.8rem;">${data.current_courses || 'No active courses'}</span>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    content.innerHTML = `
                        <div class="text-center py-4 text-danger">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                            <p style="margin-top:0.5rem;">Failed to load student profile.</p>
                            <small style="color:#6b7280;">${error.message}</small>
                        </div>
                    `;
                    console.error('Error loading student profile:', error);
                });
        }

        function showRejectModal(id) {
            currentRejectId = id;
            document.getElementById('rejectModalMessage').innerHTML =
                'Are you sure you want to reject this enrollment request?';
            document.getElementById('rejectionReason').value = '';
            document.getElementById('rejectModal').classList.add('show');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }

        document.getElementById('confirmRejectBtn')?.addEventListener('click', function() {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/enrollments/' + currentRejectId + '/reject';
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = reason;
            form.appendChild(csrf);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        });

        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });
    </script>
@endsection
