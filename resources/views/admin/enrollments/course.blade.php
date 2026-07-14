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
            --purple: #8b5cf6;
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

        .stat-number.pending {
            color: var(--warning);
        }

        .stat-number.approved {
            color: var(--success);
        }

        .stat-number.rejected {
            color: var(--danger);
        }

        .stat-number.total {
            color: var(--info);
        }

        .stat-label {
            font-size: 0.65rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .table-wrapper {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            box-shadow: var(--shadow);
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
            background: #fafbfc;
            border-bottom: 2px solid rgba(10, 36, 99, 0.06);
        }

        .student-table th {
            padding: 0.7rem 1rem;
            text-align: left;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-gray);
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .student-table td {
            padding: 0.6rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .student-table tbody tr:hover {
            background: #fafbfc;
        }

        .student-table tbody tr:last-child td {
            border-bottom: none;
        }

        .student-cell .name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .student-cell .email {
            color: var(--text-gray);
            font-size: 0.7rem;
            display: block;
        }

        .student-cell .student-id {
            color: var(--text-gray);
            font-size: 0.65rem;
            display: block;
        }

        .student-cell .year-badge {
            display: inline-block;
            background: #f3f4f6;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            color: var(--text-gray);
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
            background: var(--warning-light);
            color: #92400e;
        }

        .status-approved {
            background: var(--success-light);
            color: #166534;
        }

        .status-rejected {
            background: var(--danger-light);
            color: #991b1b;
        }

        .action-buttons {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-approve {
            background: var(--success);
            color: var(--white);
        }

        .btn-approve:hover {
            background: #059669;
        }

        .btn-reject {
            background: var(--danger);
            color: var(--white);
        }

        .btn-reject:hover {
            background: #b91c1c;
        }

        .btn-view {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-view:hover {
            background: #bfdbfe;
        }

        .row-number {
            text-align: center;
            font-weight: 500;
            color: var(--text-gray);
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
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .pagination-wrapper .info {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-gray);
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
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            max-width: 600px;
            width: 92%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: modalSlideIn 0.25s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .modal-header h4 {
            margin: 0;
            color: var(--primary);
            font-size: 1.1rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.4rem;
            cursor: pointer;
            color: var(--text-gray);
            padding: 0 0.3rem;
            transition: var(--transition);
            line-height: 1;
        }

        .modal-close:hover {
            color: var(--text-dark);
        }

        .modal-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .modal-btn {
            flex: 1;
            padding: 0.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-align: center;
            transition: var(--transition);
            font-size: 0.85rem;
        }

        .modal-btn-cancel {
            background: #f3f4f6;
            color: var(--text-dark);
        }

        .modal-btn-cancel:hover {
            background: #e5e7eb;
        }

        .modal-btn-danger {
            background: var(--danger);
            color: var(--white);
        }

        .modal-btn-danger:hover {
            background: #b91c1c;
        }

        .reject-textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.85rem;
            margin: 0.75rem 0;
            resize: vertical;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
        }

        .reject-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--primary);
            color: var(--white);
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
            color: var(--text-gray);
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
            border-radius: 8px;
        }

        .profile-stat .number {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .profile-stat .label {
            font-size: 0.6rem;
            color: var(--text-gray);
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
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .detail-item .value {
            font-size: 0.85rem;
            color: var(--text-dark);
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

    <a href="{{ route('admin.enrollments.department', ['departmentId' => $course->department_id]) }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to {{ $course->department->name ?? 'Department' }}
    </a>

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
                                    <span class="status-badge" style="background:#f3f4f6; color:var(--text-gray);">
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
                                    <small style="color:var(--text-gray);">No enrollment requests found for
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
                <h4 style="color: var(--danger);"><i class="bi bi-exclamation-triangle"></i> Reject Enrollment</h4>
                <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <p id="rejectModalMessage" style="font-size: 0.85rem; color: var(--text-gray);"></p>
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
                            <small style="color:var(--text-gray);">${error.message}</small>
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
