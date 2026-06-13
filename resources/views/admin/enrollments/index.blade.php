@extends('layouts.app')

@section('title', 'Manage Enrollments')
@section('role', 'Admin')
@section('page-title', 'Enrollment Management')
@section('welcome-text', 'Manage student enrollment requests')

@section('sidebar')
    <div class="nav-label">Management</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('admin.users') }}" class="nav-item">
        <i class="bi bi-people"></i><span>User Management</span>
    </a>
    <a href="{{ route('admin.departments.index') }}" class="nav-item">
        <i class="bi bi-building"></i><span>Departments</span>
    </a>
    <a href="{{ route('admin.courses.index') }}" class="nav-item">
        <i class="bi bi-book"></i><span>Course Management</span>
    </a>
    <a href="{{ route('admin.enrollments.index') }}" class="nav-item active">
        <i class="bi bi-list-check"></i><span>Enrollments</span>
    </a>
    <div class="nav-label">Analytics</div>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Reports</span></a>
@endsection

@section('content')
    <style>
        .stats-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-card-simple {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            border: 1px solid #e5e7eb;
            flex: 1;
            min-width: 120px;
            text-align: center;
        }

        .stat-number-simple {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .stat-number-simple.pending {
            color: #d97706;
        }

        .stat-number-simple.approved {
            color: #10b981;
        }

        .stat-number-simple.rejected {
            color: #ef4444;
        }

        .stat-label-simple {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .search-bar-simple {
            background: white;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input-simple {
            flex: 1;
            padding: 0.6rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.85rem;
            background: #f9fafb;
        }

        .status-filter-simple {
            padding: 0.6rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.85rem;
            background: #f9fafb;
            min-width: 130px;
            cursor: pointer;
        }

        .btn-reset-simple {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            padding: 0.6rem 1rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
        }

        .btn-add {
            background: #800000;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }

        .btn-batch {
            background: #059669;
        }

        .btn-batch:hover {
            background: #047857;
        }

        .enrollment-table-simple {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .enrollment-table-simple th {
            padding: 0.75rem 1rem;
            text-align: left;
            background: #f9fafb;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .enrollment-table-simple td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f2f4;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
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

        .action-buttons-simple {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.25rem 0.6rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
        }

        .btn-approve-sm {
            background: #10b981;
            color: white;
        }

        .btn-reject-sm {
            background: #ef4444;
            color: white;
        }

        .btn-view-sm {
            background: #eff6ff;
            color: #2563eb;
        }

        .empty-state-simple {
            text-align: center;
            padding: 2rem;
            color: #9ca3af;
            background: white;
            border-radius: 0.75rem;
        }

        .row-number {
            width: 50px;
            text-align: center;
            font-weight: 600;
            color: #6b7280;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            max-width: 750px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
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
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #6b7280;
        }

        .detail-row {
            display: flex;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f2f4;
        }

        .detail-label {
            width: 120px;
            font-weight: 600;
            color: #6b7280;
            font-size: 0.75rem;
        }

        .detail-value {
            flex: 1;
            font-size: 0.8rem;
            color: #1f2937;
        }

        .modal-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .modal-btn {
            flex: 1;
            padding: 0.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .modal-btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .reject-textarea {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            margin: 0.75rem 0;
            resize: vertical;
        }

        /* Batch Modal Styles */
        .filter-group {
            margin-bottom: 1rem;
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.8rem;
            color: #374151;
        }

        .filter-select {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.8rem;
            background: #f9fafb;
        }

        .filter-row-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .students-list {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            max-height: 350px;
            overflow-y: auto;
            padding: 0.5rem;
        }

        .student-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-bottom: 1px solid #f0f2f4;
        }

        .student-item:hover {
            background: #f9fafb;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-checkbox {
            margin-right: 0.5rem;
        }

        .selected-count {
            background: #800000;
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            margin-left: 0.5rem;
        }

        /* Course Search Dropdown */
        .course-dropdown {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .course-option:hover {
            background: #fef3c7;
        }

        .course-option.selected {
            background: #800000;
            color: white;
        }

        .course-option.selected small {
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .stats-row {
                flex-direction: column;
            }

            .search-bar-simple {
                flex-direction: column;
            }

            .search-input-simple,
            .status-filter-simple {
                width: 100%;
            }

            .enrollment-table-simple {
                min-width: 700px;
            }

            .filter-row-3 {
                grid-template-columns: 1fr;
            }

            .modal-content {
                max-width: 95%;
            }
        }
    </style>

    @php
        $allEnrollments = collect();
        $allEnrollments = $allEnrollments->concat($pendingEnrollments);
        $allEnrollments = $allEnrollments->concat($approvedEnrollments);
        $allEnrollments = $allEnrollments->concat($rejectedEnrollments);
        $allEnrollments = $allEnrollments->sortByDesc('created_at');
    @endphp

    <div>
        <div
            style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">

            <button class="btn-add btn-batch" onclick="openBatchEnrollModal()">
                <i class="bi bi-people-fill"></i> Batch Enroll Students
            </button>
        </div>

        <div class="stats-row">
            <div class="stat-card-simple">
                <div class="stat-number-simple pending">{{ $pendingEnrollments->count() }}</div>
                <div class="stat-label-simple">Pending</div>
            </div>
            <div class="stat-card-simple">
                <div class="stat-number-simple approved">{{ $approvedEnrollments->count() }}</div>
                <div class="stat-label-simple">Approved</div>
            </div>
            <div class="stat-card-simple">
                <div class="stat-number-simple rejected">{{ $rejectedEnrollments->count() }}</div>
                <div class="stat-label-simple">Rejected</div>
            </div>
            <div class="stat-card-simple">
                <div class="stat-number-simple">{{ $allEnrollments->count() }}</div>
                <div class="stat-label-simple">Total Requests</div>
            </div>
        </div>

        <div class="search-bar-simple">
            <input type="text" id="searchInput" class="search-input-simple"
                placeholder="Search by student name, email, or course...">
            <select id="statusFilter" class="status-filter-simple">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
            <button class="btn-reset-simple" onclick="resetFilters()"><i class="bi bi-arrow-repeat"></i> Reset</button>
        </div>

        <div style="overflow-x: auto;">
            <table class="enrollment-table-simple">
                <thead>
                    <tr>
                        <th class="row-number">#</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Department</th>
                        <th>Request Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @php $rowCounter = 1; @endphp
                    @foreach ($allEnrollments as $enrollment)
                        @php $searchText = strtolower($enrollment->student->name . ' ' . $enrollment->student->email . ' ' . $enrollment->course->course_name . ' ' . $enrollment->course->course_code); @endphp
                        <tr data-status="{{ $enrollment->status }}" data-search="{{ $searchText }}">
                            <td class="row-number">{{ $rowCounter++ }}</td>
                            <td>
                                <strong>{{ $enrollment->student->name }}</strong>
                                <br><small>{{ $enrollment->student->email }}</small>
                                <br><small>Year {{ $enrollment->student->current_year }}</small>
                            </td>
                            <td>
                                <strong>{{ $enrollment->course->course_code }}</strong>
                                <br><small>{{ $enrollment->course->course_name }}</small>
                            </td>
                            <td>{{ $enrollment->course->department->name ?? 'N/A' }}</td>
                            <td>{{ $enrollment->created_at->format('d M Y') }}</td>
                            <td>
                                @if ($enrollment->status == 'pending')
                                    <span class="status-badge status-pending"><i class="bi bi-clock-history"></i>
                                        Pending</span>
                                @elseif ($enrollment->status == 'approved')
                                    <span class="status-badge status-approved"><i class="bi bi-check-circle"></i>
                                        Approved</span>
                                @else
                                    <span class="status-badge status-rejected"><i class="bi bi-x-circle"></i>
                                        Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if ($enrollment->status == 'pending')
                                    <div class="action-buttons-simple">
                                        <button class="btn-sm btn-approve-sm"
                                            onclick="approveEnrollment({{ $enrollment->id }})"><i
                                                class="bi bi-check-lg"></i> Approve</button>
                                        <button class="btn-sm btn-reject-sm"
                                            onclick="showRejectModal({{ $enrollment->id }})"><i class="bi bi-x-lg"></i>
                                            Reject</button>
                                    </div>
                                @else
                                    <button class="btn-sm btn-view-sm"
                                        onclick="showDetailsModal(
                                        '{{ addslashes($enrollment->student->name) }}',
                                        '{{ addslashes($enrollment->student->email) }}',
                                        '{{ $enrollment->student->current_year }}',
                                        '{{ addslashes($enrollment->course->course_code) }}',
                                        '{{ addslashes($enrollment->course->course_name) }}',
                                        '{{ addslashes($enrollment->course->department->name ?? 'N/A') }}',
                                        '{{ $enrollment->created_at->format('d M Y, h:i A') }}',
                                        '{{ $enrollment->status }}',
                                        '{{ $enrollment->approved_at ? \Carbon\Carbon::parse($enrollment->approved_at)->format('d M Y, h:i A') : '' }}',
                                        '{{ addslashes($enrollment->rejection_reason) }}',
                                        '{{ $enrollment->updated_at ? \Carbon\Carbon::parse($enrollment->updated_at)->format('d M Y, h:i A') : '' }}'
                                    )"><i
                                            class="bi bi-eye"></i> View</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="emptyState" class="empty-state-simple" style="display: none;"><i class="bi bi-inbox"
                style="font-size: 2rem;"></i>
            <p>No enrollment requests found</p>
        </div>
    </div>

    <!-- View Details Modal -->
    <div id="detailsModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="bi bi-info-circle"></i> Enrollment Details</h4><button class="modal-close"
                    onclick="closeDetailsModal()">&times;</button>
            </div>
            <div id="detailsContent"></div>
            <div class="modal-buttons"><button class="modal-btn modal-btn-cancel"
                    onclick="closeDetailsModal()">Close</button></div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4 style="color: #ef4444;"><i class="bi bi-exclamation-triangle"></i> Reject Enrollment</h4><button
                    class="modal-close" onclick="closeRejectModal()">&times;</button>
            </div>
            <p id="rejectModalMessage" style="font-size: 0.8rem; color: #6b7280;"></p>
            <textarea id="rejectionReason" class="reject-textarea" rows="3"
                placeholder="Please provide a reason for rejection..."></textarea>
            <div class="modal-buttons"><button class="modal-btn modal-btn-cancel"
                    onclick="closeRejectModal()">Cancel</button><button class="modal-btn modal-btn-reject"
                    id="confirmRejectBtn" style="background:#ef4444;color:white;">Confirm Rejection</button></div>
        </div>
    </div>

    <!-- Batch Enrollment Modal -->
    <div id="batchEnrollModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h4><i class="bi bi-people-fill"></i> Batch Enroll Students</h4>
                <button class="modal-close" onclick="closeBatchEnrollModal()">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.enrollments.batch') }}">
                @csrf
                <div class="filter-group">
                    <label>📚 Select Course *</label>
                    <div style="position: relative;">
                        <input type="text" id="courseSearchInput" class="filter-select"
                            placeholder="Type course code or name to search..." autocomplete="off"
                            style="width: 100%; padding: 0.6rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.8rem;">
                        <input type="hidden" name="course_id" id="selectedCourseId" required>
                        <div id="courseDropdown" class="course-dropdown"
                            style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            @foreach ($allCourses ?? [] as $course)
                                <div class="course-option" data-id="{{ $course->id }}"
                                    data-code="{{ $course->course_code }}" data-name="{{ $course->course_name }}"
                                    data-dept="{{ $course->department->code ?? 'N/A' }}"
                                    style="padding: 0.5rem; cursor: pointer; border-bottom: 1px solid #f0f2f4;">
                                    <strong>{{ $course->course_code }}</strong> - {{ $course->course_name }}
                                    <small style="color: #6b7280;">({{ $course->department->code ?? 'N/A' }})</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div id="selectedCourseDisplay"
                        style="margin-top: 0.5rem; font-size: 0.75rem; color: #059669; display: none;">
                        <i class="bi bi-check-circle"></i> Selected: <span id="selectedCourseText"></span>
                    </div>
                </div>

                <div class="filter-row-3">
                    <div>
                        <label>🏛️ Department</label>
                        <select id="filterDept" class="filter-select">
                            <option value="all">All Departments</option>
                            @foreach ($allDepartments ?? [] as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->code }} - {{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>🎓 Year</label>
                        <select id="filterYear" class="filter-select">
                            <option value="all">All Years</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="5">5th Year</option>
                            <option value="5">6th Year</option>
                        </select>
                    </div>
                    <div>
                        <label>🔍 Search Students</label>
                        <input type="text" id="filterSearch" class="filter-select" placeholder="Name or email...">
                    </div>
                </div>

                <div class="filter-group">
                    <div
                        style="margin-bottom: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                        <label style="cursor: pointer;">
                            <input type="checkbox" id="selectAllFiltered" onchange="toggleAllFiltered()">
                            <strong>Select All Students</strong>
                        </label>
                        <span id="selectedCountBadge" class="selected-count">0 selected</span>
                    </div>
                    <div class="students-list" id="studentsListContainer">
                        @foreach ($allStudents ?? [] as $student)
                            <div class="student-item" data-dept="{{ $student->department_id }}"
                                data-year="{{ $student->current_year }}" data-name="{{ strtolower($student->name) }}"
                                data-email="{{ strtolower($student->email) }}">
                                <label style="cursor: pointer; width: 100%; display: flex; align-items: center;">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                        class="student-checkbox">
                                    <strong>{{ $student->name }}</strong>
                                    <small style="color: #6b7280; margin-left: 0.5rem;">(Year {{ $student->current_year }}
                                        - {{ $student->email }})</small>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="filter-group">
                    <label>📅 Enrollment Date</label>
                    <input type="date" name="enrollment_date" class="filter-select" value="{{ date('Y-m-d') }}"
                        required>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="modal-btn modal-btn-cancel"
                        onclick="closeBatchEnrollModal()">Cancel</button>
                    <button type="submit" class="modal-btn" style="background: #059669; color: white;" id="submitBtn"
                        disabled>✓ Enroll Selected Students</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentRejectId = null;

        function showDetailsModal(studentName, studentEmail, studentYear, courseCode, courseName, department, requestDate,
            status, approvedAt, rejectionReason, rejectedAt) {
            let statusHtml = status === 'pending' ?
                '<span class="status-badge status-pending"><i class="bi bi-clock-history"></i> Pending</span>' : (status ===
                    'approved' ?
                    '<span class="status-badge status-approved"><i class="bi bi-check-circle"></i> Approved</span>' :
                    '<span class="status-badge status-rejected"><i class="bi bi-x-circle"></i> Rejected</span>');
            let additionalInfo = '';
            if (status === 'approved' && approvedAt) additionalInfo =
                '<div class="detail-row"><div class="detail-label">Approved Date</div><div class="detail-value">' +
                approvedAt + '</div></div>';
            else if (status === 'rejected' && rejectionReason) additionalInfo =
                '<div class="detail-row"><div class="detail-label">Rejection Reason</div><div class="detail-value" style="color:#dc2626;">' +
                rejectionReason +
                '</div></div><div class="detail-row"><div class="detail-label">Rejected Date</div><div class="detail-value">' +
                rejectedAt + '</div></div>';
            document.getElementById('detailsContent').innerHTML =
                `
                <div class="detail-row"><div class="detail-label">Student Name</div><div class="detail-value"><strong>${studentName}</strong></div></div>
                <div class="detail-row"><div class="detail-label">Student Email</div><div class="detail-value">${studentEmail}</div></div>
                <div class="detail-row"><div class="detail-label">Student Year</div><div class="detail-value">Year ${studentYear}</div></div>
                <div class="detail-row"><div class="detail-label">Course</div><div class="detail-value"><strong>${courseCode}</strong> - ${courseName}</div></div>
                <div class="detail-row"><div class="detail-label">Department</div><div class="detail-value">${department}</div></div>
                <div class="detail-row"><div class="detail-label">Request Date</div><div class="detail-value">${requestDate}</div></div>
                <div class="detail-row"><div class="detail-label">Status</div><div class="detail-value">${statusHtml}</div></div>${additionalInfo}`;
            document.getElementById('detailsModal').classList.add('show');
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.remove('show');
        }

        function approveEnrollment(id) {
            if (confirm('Approve this enrollment?')) window.location.href = '/admin/enrollments/' + id + '/approve';
        }

        function showRejectModal(id) {
            currentRejectId = id;
            document.getElementById('rejectModalMessage').innerHTML = 'Are you sure you want to reject this enrollment?';
            document.getElementById('rejectModal').classList.add('show');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('show');
            document.getElementById('rejectionReason').value = '';
            currentRejectId = null;
        }

        document.getElementById('confirmRejectBtn')?.addEventListener('click', function() {
            let reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection');
                return;
            }
            let form = document.createElement('form');
            form.method = 'POST';
            form.action = '/admin/enrollments/' + currentRejectId + '/reject';
            let csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            let reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = reason;
            form.appendChild(csrf);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        });

        // ========== COURSE SEARCH FUNCTIONALITY ==========
        const courseSearchInput = document.getElementById('courseSearchInput');
        const courseDropdown = document.getElementById('courseDropdown');
        const selectedCourseId = document.getElementById('selectedCourseId');
        const selectedCourseDisplay = document.getElementById('selectedCourseDisplay');
        const selectedCourseText = document.getElementById('selectedCourseText');
        const submitBtn = document.getElementById('submitBtn');
        const courseOptions = document.querySelectorAll('.course-option');

        function filterCourses() {
            const searchTerm = courseSearchInput.value.toLowerCase();
            let hasVisible = false;
            courseOptions.forEach(opt => {
                const code = opt.dataset.code.toLowerCase();
                const name = opt.dataset.name.toLowerCase();
                const matches = searchTerm === '' || code.includes(searchTerm) || name.includes(searchTerm);
                opt.style.display = matches ? 'block' : 'none';
                if (matches) hasVisible = true;
            });
            courseDropdown.style.display = searchTerm !== '' && hasVisible ? 'block' : 'none';
        }

        function selectCourse(id, code, name, dept) {
            selectedCourseId.value = id;
            courseSearchInput.value = code + ' - ' + name;
            selectedCourseText.innerHTML = code + ' - ' + name + ' (' + dept + ')';
            selectedCourseDisplay.style.display = 'block';
            courseDropdown.style.display = 'none';
            submitBtn.disabled = false;

            courseOptions.forEach(opt => {
                if (opt.dataset.id == id) {
                    opt.classList.add('selected');
                } else {
                    opt.classList.remove('selected');
                }
            });
        }

        if (courseSearchInput) {
            courseSearchInput.addEventListener('focus', function() {
                if (courseSearchInput.value !== '') {
                    filterCourses();
                }
            });
            courseSearchInput.addEventListener('keyup', filterCourses);
        }

        courseOptions.forEach(opt => {
            opt.addEventListener('click', function() {
                selectCourse(this.dataset.id, this.dataset.code, this.dataset.name, this.dataset.dept);
            });
        });

        document.addEventListener('click', function(e) {
            if (courseSearchInput && !courseSearchInput.contains(e.target) && courseDropdown && !courseDropdown
                .contains(e.target)) {
                courseDropdown.style.display = 'none';
            }
        });

        // ========== BATCH ENROLLMENT FUNCTIONS ==========
        function filterStudentList() {
            const dept = document.getElementById('filterDept').value;
            const year = document.getElementById('filterYear').value;
            const search = document.getElementById('filterSearch').value.toLowerCase();
            const items = document.querySelectorAll('#studentsListContainer .student-item');
            let visibleCount = 0;
            items.forEach(item => {
                const itemDept = item.getAttribute('data-dept');
                const itemYear = item.getAttribute('data-year');
                const itemName = item.getAttribute('data-name');
                const itemEmail = item.getAttribute('data-email');
                const matchesDept = dept === 'all' || itemDept == dept;
                const matchesYear = year === 'all' || itemYear == year;
                const matchesSearch = search === '' || itemName.includes(search) || itemEmail.includes(search);
                if (matchesDept && matchesYear && matchesSearch) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            updateSelectedCount();
        }

        function toggleAllFiltered() {
            const selectAll = document.getElementById('selectAllFiltered');
            const visibleItems = document.querySelectorAll(
                '#studentsListContainer .student-item[style=""] .student-checkbox, #studentsListContainer .student-item:not([style="display: none"]) .student-checkbox'
            );
            visibleItems.forEach(cb => cb.checked = selectAll.checked);
            updateSelectedCount();
        }

        function updateSelectedCount() {
            const checked = document.querySelectorAll('#studentsListContainer .student-checkbox:checked').length;
            const badge = document.getElementById('selectedCountBadge');
            if (badge) badge.innerText = checked + ' selected';
        }

        function openBatchEnrollModal() {
            document.getElementById('batchEnrollModal').style.display = 'flex';
            // Reset course selection
            if (courseSearchInput) courseSearchInput.value = '';
            if (selectedCourseId) selectedCourseId.value = '';
            if (selectedCourseDisplay) selectedCourseDisplay.style.display = 'none';
            if (submitBtn) submitBtn.disabled = true;
            if (courseDropdown) courseDropdown.style.display = 'none';
            // Reset filters
            if (document.getElementById('filterDept')) document.getElementById('filterDept').value = 'all';
            if (document.getElementById('filterYear')) document.getElementById('filterYear').value = 'all';
            if (document.getElementById('filterSearch')) document.getElementById('filterSearch').value = '';
            filterStudentList();
        }

        function closeBatchEnrollModal() {
            document.getElementById('batchEnrollModal').style.display = 'none';
        }

        document.getElementById('filterDept')?.addEventListener('change', filterStudentList);
        document.getElementById('filterYear')?.addEventListener('change', filterStudentList);
        document.getElementById('filterSearch')?.addEventListener('keyup', filterStudentList);
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList && e.target.classList.contains('student-checkbox'))
                updateSelectedCount();
        });

        // ========== MAIN TABLE FILTER ==========
        const searchInput = document.getElementById('searchInput');
        const statusFilterElem = document.getElementById('statusFilter');
        const rows = document.querySelectorAll('#tableBody tr');
        const emptyState = document.getElementById('emptyState');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusValue = statusFilterElem.value;
            let visibleCount = 0;
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const searchText = row.getAttribute('data-search') || '';
                const rowStatus = row.getAttribute('data-status') || '';
                const matchesSearch = searchTerm === '' || searchText.includes(searchTerm);
                const matchesStatus = statusValue === 'all' || rowStatus === statusValue;
                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }

        function resetFilters() {
            searchInput.value = '';
            statusFilterElem.value = 'all';
            filterTable();
        }
        if (searchInput) searchInput.addEventListener('keyup', filterTable);
        if (statusFilterElem) statusFilterElem.addEventListener('change', filterTable);
    </script>
@endsection
