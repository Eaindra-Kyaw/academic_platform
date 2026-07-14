@extends('layouts.app')

@section('title', 'Student Management')
@section('page-title', '👨‍🎓 Student Management')
@section('welcome-text', 'Manage all students across departments')

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

        .filter-bar {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            background: var(--white);
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            align-items: center;
        }

        .filter-bar .filter-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .filter-bar .filter-group label {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .filter-bar .filter-group select {
            padding: 0.35rem 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.8rem;
            background: #f8fafc;
            transition: var(--transition);
            color: var(--text-dark);
            min-width: 120px;
            cursor: pointer;
        }

        .filter-bar .filter-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .filter-bar .search-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex: 1;
            min-width: 150px;
        }

        .filter-bar .search-group input {
            padding: 0.35rem 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.8rem;
            background: #f8fafc;
            transition: var(--transition);
            color: var(--text-dark);
            width: 100%;
            min-width: 120px;
        }

        .filter-bar .search-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .filter-bar .search-group input::placeholder {
            color: #94a3b8;
            font-size: 0.75rem;
        }

        .btn-premium {
            padding: 0.35rem 1rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-premium-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
        }

        .btn-premium-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .btn-premium-outline {
            background: transparent;
            color: var(--text-gray);
            border: 1px solid rgba(10, 36, 99, 0.12);
        }

        .btn-premium-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(10, 36, 99, 0.04);
        }

        .table-wrapper {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .table-header {
            padding: 1rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .table-header .title {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-header .title i {
            color: var(--primary);
        }

        .table-header .title .count-badge {
            background: var(--primary);
            color: var(--white);
            font-size: 0.6rem;
            padding: 0.05rem 0.6rem;
            border-radius: 1rem;
        }

        .table-premium {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.78rem;
        }

        .table-premium thead th {
            padding: 0.5rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-gray);
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            white-space: nowrap;
        }

        .table-premium tbody td {
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .table-premium tbody tr {
            transition: var(--transition);
        }

        .table-premium tbody tr:hover {
            background: #fafbfc;
        }

        .table-premium tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-id {
            background: #f1f5f9;
            color: var(--text-gray);
            padding: 0.1rem 0.6rem;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 600;
            font-family: monospace;
        }

        .attendance-pill-premium {
            font-weight: 600;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            display: inline-block;
            font-size: 0.7rem;
        }

        .attendance-pill-premium.high {
            background: var(--success-light);
            color: #166534;
        }

        .attendance-pill-premium.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .attendance-pill-premium.low {
            background: var(--danger-light);
            color: #991b1b;
        }

        .roll-call-badge {
            font-weight: 700;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            background: rgba(10, 36, 99, 0.06);
            display: inline-block;
            font-size: 0.7rem;
        }

        .risk-badge-premium {
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .risk-badge-premium.low {
            background: var(--success-light);
            color: #166534;
        }

        .risk-badge-premium.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .risk-badge-premium.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .btn-action {
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.65rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-action:hover {
            transform: translateY(-1px);
        }

        .btn-view {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-view:hover {
            background: #bfdbfe;
        }

        .btn-message {
            background: var(--warning-light);
            color: #92400e;
        }

        .btn-message:hover {
            background: #fde68a;
        }

        .pagination-wrapper {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            background: #fafbfc;
        }

        .pagination-wrapper .info {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .pagination-wrapper .info strong {
            color: var(--text-dark);
        }

        .pagination-wrapper .pagination {
            margin: 0;
            gap: 0.25rem;
        }

        .pagination-wrapper .pagination .page-link {
            border: none;
            color: var(--text-gray);
            font-size: 0.75rem;
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            transition: var(--transition);
            background: transparent;
        }

        .pagination-wrapper .pagination .page-link:hover {
            background: rgba(212, 160, 23, 0.1);
            color: var(--primary);
        }

        .pagination-wrapper .pagination .active .page-link {
            background: var(--primary);
            color: var(--white);
        }

        .pagination-wrapper .pagination .disabled .page-link {
            color: #d1d5db;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
                padding: 0.75rem 1rem;
            }

            .filter-bar .filter-group {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar .filter-group select {
                width: 100%;
                min-width: unset;
            }

            .filter-bar .search-group {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar .search-group input {
                width: 100%;
                min-width: unset;
            }

            .table-premium {
                font-size: 0.7rem;
            }

            .table-premium thead th,
            .table-premium tbody td {
                padding: 0.3rem 0.5rem;
            }
        }
    </style>

    <!-- Filter Bar -->
    <form class="filter-bar" method="GET" action="{{ route('admin.students.index') }}">
        <div class="filter-group">
            <label>Department</label>
            <select name="department_id">
                <option value="">All Departments</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label>Year</label>
            <select name="year">
                <option value="">All Years</option>
                @for ($i = 1; $i <= 6; $i++)
                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>
                        {{ $yearLabels[$i] ?? $i . 'th' }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="filter-group">
            <label>Risk Level</label>
            <select name="risk_level">
                <option value="">All</option>
                <option value="Low" {{ request('risk_level') == 'Low' ? 'selected' : '' }}>Low</option>
                <option value="Medium" {{ request('risk_level') == 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="High" {{ request('risk_level') == 'High' ? 'selected' : '' }}>High</option>
            </select>
        </div>

        <div class="search-group">
            <input type="text" name="search" placeholder="🔍 Search students..." value="{{ request('search') }}">
        </div>

        <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
            <button type="submit" class="btn-premium btn-premium-primary">
                <i class="bi bi-funnel"></i> Apply
            </button>
            <a href="{{ route('admin.students.index') }}" class="btn-premium btn-premium-outline">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </div>
    </form>

    <!-- Table -->
    <div class="table-wrapper">
        <div class="table-header">
            <div class="title">
                <i class="bi bi-list-ul"></i> Student List
                <span class="count-badge">{{ $students->total() }}</span>
            </div>
            <div>
                <a href="{{ route('admin.students.export') }}?{{ http_build_query(request()->all()) }}"
                    class="btn-premium btn-premium-primary">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="table-premium" id="studentTable">
                <thead>
                    <tr>
                        <th style="min-width:100px;">Student ID</th>
                        <th style="min-width:140px;">Name</th>
                        <th style="min-width:170px;">Email</th>
                        <th>Department</th>
                        <th style="text-align:center;">Year</th>
                        <th style="text-align:center;">Attendance</th>
                        <th style="text-align:center;">Roll Call<br><small>/10</small></th>
                        <th style="text-align:center;">Risk</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php
                            $attendance = $student->attendance_percentage ?? 0;
                            $rollCall = $student->roll_call_total ?? 0;
                            $risk = $student->risk_level ?? 'Low';
                            $riskClass = strtolower($risk);
                            $attClass = $attendance >= 75 ? 'high' : ($attendance >= 60 ? 'medium' : 'low');
                            $nameParts = explode(' ', $student->name);
                            $initials = '';
                            foreach ($nameParts as $part) {
                                if (!in_array($part, ['Dr.', 'Daw', 'Mg', 'U'])) {
                                    $initials .= substr($part, 0, 1);
                                }
                            }
                            $initials = strtoupper(substr($initials, 0, 2));
                        @endphp
                        <tr>
                            <td>
                                <span class="badge-id">{{ $student->student_id ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <div
                                        style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg, var(--primary), var(--primary-light)); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.6rem; flex-shrink:0;">
                                        {{ $initials }}
                                    </div>
                                    <span style="font-weight:500; color:var(--text-dark);">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td style="color:var(--text-gray); font-size:0.75rem;">{{ $student->email }}</td>
                            <td style="color:var(--text-gray); font-size:0.75rem;">
                                {{ $student->department->code ?? 'N/A' }}</td>
                            <td style="text-align:center; font-size:0.75rem; color:var(--text-gray);">
                                {{ $student->current_year ?? 'N/A' }}</td>
                            <td style="text-align:center;">
                                <span class="attendance-pill-premium {{ $attClass }}">
                                    {{ number_format($attendance, 1) }}%
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span class="roll-call-badge">{{ number_format($rollCall, 1) }}</span>
                            </td>
                            <td style="text-align:center;">
                                <span class="risk-badge-premium {{ $riskClass }}">{{ $risk }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex; gap:0.3rem; justify-content:center;">
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn-action btn-view"
                                        title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn-action btn-message" title="Send Message"
                                        onclick="openMessageModal('{{ addslashes($student->name) }}', '{{ $student->id }}', '{{ $student->email }}')">
                                        <i class="bi bi-chat"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:2.5rem; color:var(--text-gray);">
                                <div style="font-size:2rem; margin-bottom:0.5rem;">📚</div>
                                <p style="font-size:0.9rem; margin:0;">No students found</p>
                                <p style="font-size:0.75rem; margin:0.2rem 0 0;">Try adjusting your filters</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            <div class="info">
                <i class="bi bi-info-circle"></i>
                Showing <strong>{{ $students->firstItem() ?? 0 }}</strong> to
                <strong>{{ $students->lastItem() ?? 0 }}</strong>
                of <strong>{{ $students->total() }}</strong> students
            </div>
            <div>
                {{ $students->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <div class="modal-header" style="border-bottom:1px solid #f1f5f9; padding:1.25rem 1.5rem;">
                    <h5 class="modal-title" style="font-weight:700; color:var(--text-dark);">
                        <span style="color:var(--primary);">✉️</span> Send Message
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.5rem;">
                    <div id="messageStudentInfo"
                        style="background:#f8fafc; padding:0.75rem 1rem; border-radius:10px; margin-bottom:1.25rem;">
                        <p style="margin:0; font-weight:600;">To: <span id="modalStudentName"
                                style="font-weight:400;"></span></p>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-gray);">
                            <span id="modalStudentEmail"></span>
                        </p>
                    </div>

                    <div id="messageStatus"
                        style="display:none; padding:0.5rem 0.75rem; border-radius:8px; margin-bottom:1rem; font-size:0.85rem;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"
                            style="font-weight:600; font-size:0.85rem; color:var(--text-dark);">Subject</label>
                        <input type="text" id="messageSubject" class="form-control"
                            style="border-radius:8px; border:1px solid rgba(10,36,99,0.12); padding:0.6rem;"
                            placeholder="Enter subject...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"
                            style="font-weight:600; font-size:0.85rem; color:var(--text-dark);">Message</label>
                        <textarea id="messageContent" class="form-control" rows="4"
                            style="border-radius:8px; border:1px solid rgba(10,36,99,0.12); padding:0.6rem; resize:vertical;"
                            placeholder="Type your message here..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:1rem 1.5rem; gap:0.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        style="border-radius:8px; padding:0.5rem 1.5rem; border:1px solid rgba(10,36,99,0.12); background:var(--white); color:var(--text-gray);">
                        Cancel
                    </button>
                    <button type="button" class="btn" id="sendMessageBtn"
                        style="border-radius:8px; padding:0.5rem 1.5rem; background:var(--primary); color:var(--white); border:none; font-weight:600;">
                        📤 Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search filter (client-side)
        document.addEventListener('DOMContentLoaded', function() {
            // Message Modal
            window.openMessageModal = function(studentName, studentId, studentEmail) {
                document.getElementById('modalStudentName').textContent = studentName;
                document.getElementById('modalStudentEmail').textContent = studentEmail;
                document.getElementById('messageSubject').value = '';
                document.getElementById('messageContent').value = '';
                document.getElementById('messageStatus').style.display = 'none';
                document.getElementById('sendMessageBtn').dataset.studentId = studentId;
                document.getElementById('sendMessageBtn').disabled = false;
                document.getElementById('sendMessageBtn').innerHTML = '📤 Send Message';
                document.getElementById('sendMessageBtn').style.background = '#0A2463';

                const modal = new bootstrap.Modal(document.getElementById('messageModal'));
                modal.show();
            };

            // Send Message
            document.getElementById('sendMessageBtn').addEventListener('click', function() {
                const studentId = this.dataset.studentId;
                const subject = document.getElementById('messageSubject').value.trim();
                const message = document.getElementById('messageContent').value.trim();
                const statusDiv = document.getElementById('messageStatus');

                if (!message) {
                    statusDiv.style.display = 'block';
                    statusDiv.style.background = '#fee2e2';
                    statusDiv.style.color = '#991b1b';
                    statusDiv.textContent = '⚠️ Please enter a message.';
                    return;
                }

                this.disabled = true;
                this.innerHTML = '⏳ Sending...';
                this.style.background = '#6b7a8f';

                fetch('{{ route('admin.messages.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            recipient_id: studentId,
                            subject: subject || 'Message from Admin',
                            message: message
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            statusDiv.style.display = 'block';
                            statusDiv.style.background = '#d1fae5';
                            statusDiv.style.color = '#166534';
                            statusDiv.textContent = '✅ ' + data.message;
                            this.innerHTML = '✅ Sent!';
                            this.style.background = '#10b981';

                            setTimeout(() => {
                                const modal = bootstrap.Modal.getInstance(document
                                    .getElementById('messageModal'));
                                modal.hide();
                                this.innerHTML = '📤 Send Message';
                                this.style.background = '#0A2463';
                                this.disabled = false;
                            }, 1500);
                        } else {
                            statusDiv.style.display = 'block';
                            statusDiv.style.background = '#fee2e2';
                            statusDiv.style.color = '#991b1b';
                            statusDiv.textContent = '❌ ' + (data.message || 'Failed to send message.');
                            this.innerHTML = '📤 Send Message';
                            this.style.background = '#0A2463';
                            this.disabled = false;
                        }
                    })
                    .catch(error => {
                        statusDiv.style.display = 'block';
                        statusDiv.style.background = '#fee2e2';
                        statusDiv.style.color = '#991b1b';
                        statusDiv.textContent = '❌ Network error. Please try again.';
                        this.innerHTML = '📤 Send Message';
                        this.style.background = '#0A2463';
                        this.disabled = false;
                        console.error('Error:', error);
                    });
            });
        });
    </script>
@endsection
