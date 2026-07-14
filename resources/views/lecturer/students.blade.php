@extends('layouts.app')

@section('title', 'All Students')
@section('role', 'Lecturer')
@section('page-title', 'All Students')
@section('welcome-text', 'View all students in your courses')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }

        .stats-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-card {
            background: var(--white);
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            flex: 1;
            min-width: 120px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-number.green {
            color: var(--success);
        }

        .stat-number.yellow {
            color: var(--warning);
        }

        .stat-number.red {
            color: var(--danger);
        }

        .stat-number.blue {
            color: var(--info);
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            font-size: 0.8rem;
        }

        .students-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            background: var(--bg-main);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-gray);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .students-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f2f4;
            vertical-align: middle;
        }

        .students-table tbody tr {
            transition: all 0.3s ease;
        }

        .students-table tbody tr:hover {
            background: #f8f9fc;
        }

        .status-eligible {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .status-warning {
            background: #fef3c7;
            color: #92400e;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .status-risk {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .risk-badge {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .risk-badge.low {
            background: #dcfce7;
            color: #166534;
        }

        .risk-badge.medium {
            background: #fef3c7;
            color: #92400e;
        }

        .risk-badge.high {
            background: #fee2e2;
            color: #991b1b;
        }

        .search-bar {
            background: var(--white);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            box-shadow: var(--shadow);
        }

        .search-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 2rem;
            font-size: 0.8rem;
            min-width: 150px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .btn-notify {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-notify:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
        }

        .btn-notify i {
            margin-right: 0.2rem;
        }

        .btn-filter {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: var(--primary-dark);
        }

        .btn-reset {
            background: #f3f4f6;
            color: var(--text-dark);
            border: 1px solid rgba(10, 36, 99, 0.1);
            padding: 0.4rem 1rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-reset:hover {
            background: #e5e7eb;
        }

        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 4px;
            width: 100%;
        }

        .progress-bar .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .progress-bar .fill.green {
            background: var(--success);
        }

        .progress-bar .fill.yellow {
            background: var(--warning);
        }

        .progress-bar .fill.red {
            background: var(--danger);
        }

        .no-data {
            color: #9ca3af;
            font-size: 0.75rem;
        }

        /* Message Modal */
        .message-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        .message-modal-overlay.show {
            display: flex;
        }

        .message-modal-box {
            background: var(--white);
            border-radius: 0.75rem;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-modal-box .icon {
            text-align: center;
            font-size: 2.5rem;
            color: var(--info);
            margin-bottom: 0.5rem;
        }

        .message-modal-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.3rem 0;
        }

        .message-modal-box p {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-gray);
            margin: 0 0 1rem 0;
        }

        .message-modal-box #messageStatus {
            display: none;
            padding: 0.5rem 0.75rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .message-modal-box #messageStatus.success {
            display: block;
            background: #ecfdf5;
            color: var(--success);
            border: 1px solid #a7f3d0;
        }

        .message-modal-box #messageStatus.error {
            display: block;
            background: #fef2f2;
            color: var(--danger);
            border: 1px solid #fca5a5;
        }

        .message-modal-box textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.4rem;
            font-size: 0.8rem;
            resize: vertical;
            min-height: 80px;
            transition: all 0.3s ease;
        }

        .message-modal-box textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .message-modal-box input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.4rem;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .message-modal-box input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
        }

        .message-modal-box .buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .message-modal-box .btn-send {
            padding: 0.4rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: var(--primary);
            color: var(--white);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .message-modal-box .btn-send:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .message-modal-box .btn-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .message-modal-box .btn-close-modal {
            padding: 0.4rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(10, 36, 99, 0.1);
            background: var(--white);
            color: #374151;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .message-modal-box .btn-close-modal:hover {
            background: #f3f4f6;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-gray);
        }

        .empty-state i {
            font-size: 2rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .students-table {
                min-width: 700px;
            }

            .stats-row {
                flex-direction: column;
            }

            .stat-card {
                min-width: unset;
            }

            .search-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <div>
        <!-- Stats Cards -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number blue">{{ $totalStudents ?? 0 }}</div>
                <div class="stat-label">👨‍🎓 Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number green">{{ $eligibleCount ?? 0 }}</div>
                <div class="stat-label">✅ Eligible</div>
            </div>
            <div class="stat-card">
                <div class="stat-number yellow">{{ $warningCount ?? 0 }}</div>
                <div class="stat-label">⚠️ Warning</div>
            </div>
            <div class="stat-card">
                <div class="stat-number red">{{ $atRiskCount ?? 0 }}</div>
                <div class="stat-label">🚨 At Risk</div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search by student name or email...">
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <button class="btn-filter" onclick="filterTable()">
                    <i class="bi bi-funnel"></i> Apply Filter
                </button>
                <button class="btn-reset" onclick="resetFilters()">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </div>

        <!-- Students Table -->
        <div style="overflow-x: auto;">
            <table class="students-table" id="studentsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Student ID</th>
                        <th>Email</th>
                        <th>Attendance</th>
                        <th>Roll Call</th>
                        <th>Eligibility</th>
                        <th>Risk Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    @forelse($students ?? [] as $student)
                        @php
                            $hasEvaluation = ($student->total_courses ?? 0) > 0;
                            $attendance = $student->attendance_percentage ?? 0;
                            $rollCall = $student->roll_call_total ?? 0;
                            $status = $student->status ?? 'Not Evaluated';

                            if (!$hasEvaluation) {
                                $badgeClass = '';
                                $riskLevel = '';
                                $statusDisplay = '—';
                                $attDisplay = '—';
                                $rollDisplay = '—';
                                $barWidth = 0;
                                $barClass = '';
                            } else {
                                $statusDisplay = $status;
                                $attDisplay = number_format($attendance, 1) . '%';
                                $rollDisplay = number_format($rollCall, 1) . '/10';
                                $barWidth = min($attendance, 100);
                                $barClass = $attendance >= 75 ? 'green' : ($attendance >= 60 ? 'yellow' : 'red');
                                $badgeClass =
                                    $status == 'Eligible'
                                        ? 'status-eligible'
                                        : ($status == 'Warning'
                                            ? 'status-warning'
                                            : 'status-risk');
                                $riskLevel = $status == 'Eligible' ? 'Low' : ($status == 'Warning' ? 'Medium' : 'High');
                            }
                        @endphp
                        <tr class="student-row" data-name="{{ strtolower($student->name) }}"
                            data-email="{{ strtolower($student->email) }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $student->name }}</strong>
                            </td>
                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                            <td>{{ $student->email }}</td>
                            <td>
                                @if ($hasEvaluation)
                                    <div style="display:flex; align-items:center; gap:0.5rem;">
                                        <span style="font-weight:600; font-size:0.9rem; min-width:45px;">
                                            {{ $attDisplay }}
                                        </span>
                                        <div class="progress-bar" style="flex:1; min-width:60px;">
                                            <div class="fill {{ $barClass }}" style="width: {{ $barWidth }}%;">
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="no-data">—</span>
                                @endif
                            </td>
                            <td style="text-align:center; font-weight:600; font-size:0.9rem;">
                                @if ($hasEvaluation)
                                    {{ $rollDisplay }}
                                @else
                                    <span class="no-data">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($hasEvaluation)
                                    <span class="{{ $badgeClass }}">{{ $statusDisplay }}</span>
                                @else
                                    <span class="no-data">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($hasEvaluation)
                                    <span class="risk-badge {{ strtolower($riskLevel) }}">{{ $riskLevel }}</span>
                                @else
                                    <span class="no-data">—</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn-notify"
                                    onclick="openMessageModal('{{ addslashes($student->name) }}', '{{ $student->id }}', '{{ $student->email }}')">
                                    <i class="bi bi-envelope"></i> Notify
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:2rem; color:#9ca3af;">
                                <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                No students enrolled in your courses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="message-modal-overlay" id="messageModal">
        <div class="message-modal-box">
            <div class="icon">✉️</div>
            <h4>Send Message</h4>
            <p id="messageRecipient">To: <strong></strong></p>
            <div id="messageStatus"></div>
            <form id="messageForm" onsubmit="sendMessage(event)">
                <input type="hidden" id="recipientId" value="">
                <input type="text" id="messageSubject" placeholder="Subject (optional)">
                <textarea id="messageContent" placeholder="Type your message here..." required></textarea>
                <div class="buttons">
                    <button type="button" class="btn-close-modal" onclick="closeMessageModal()">Cancel</button>
                    <button type="submit" class="btn-send" id="sendBtn">
                        <i class="bi bi-send"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Search Functionality
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                filterTable();
            }
        });

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            const rows = document.querySelectorAll('#studentTableBody .student-row');

            let visibleCount = 0;
            rows.forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const text = name + ' ' + email;

                if (!searchTerm || text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            const rows = document.querySelectorAll('#studentTableBody .student-row');
            rows.forEach(row => {
                row.style.display = '';
            });
        }

        // Message Modal
        function openMessageModal(studentName, studentId, studentEmail) {
            document.getElementById('recipientId').value = studentId;
            document.getElementById('messageRecipient').innerHTML = 'To: <strong>' + studentName + ' (' + studentEmail +
                ')</strong>';
            document.getElementById('messageContent').value = '';
            document.getElementById('messageSubject').value = '';
            document.getElementById('messageStatus').className = '';
            document.getElementById('messageStatus').style.display = 'none';
            document.getElementById('sendBtn').disabled = false;
            document.getElementById('sendBtn').innerHTML = '<i class="bi bi-send"></i> Send';
            document.getElementById('messageModal').classList.add('show');
        }

        function closeMessageModal() {
            document.getElementById('messageModal').classList.remove('show');
        }

        function sendMessage(e) {
            e.preventDefault();
            const recipientId = document.getElementById('recipientId').value;
            const subject = document.getElementById('messageSubject').value;
            const message = document.getElementById('messageContent').value;
            const sendBtn = document.getElementById('sendBtn');
            const statusDiv = document.getElementById('messageStatus');

            if (!message.trim()) {
                statusDiv.className = 'error';
                statusDiv.style.display = 'block';
                statusDiv.textContent = 'Please enter a message.';
                return;
            }

            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="bi bi-hourglass"></i> Sending...';

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route('lecturer.messages.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        recipient_id: recipientId,
                        subject: subject || 'Message from Lecturer',
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusDiv.className = 'success';
                        statusDiv.style.display = 'block';
                        statusDiv.textContent = '✅ ' + data.message;
                        sendBtn.innerHTML = '<i class="bi bi-check"></i> Sent!';
                        sendBtn.disabled = true;

                        setTimeout(() => {
                            closeMessageModal();
                        }, 2000);
                    } else {
                        statusDiv.className = 'error';
                        statusDiv.style.display = 'block';
                        statusDiv.textContent = '❌ ' + (data.message || 'Failed to send message.');
                        sendBtn.innerHTML = '<i class="bi bi-send"></i> Send';
                        sendBtn.disabled = false;
                    }
                })
                .catch(error => {
                    statusDiv.className = 'error';
                    statusDiv.style.display = 'block';
                    statusDiv.textContent = '❌ Network error. Please try again.';
                    sendBtn.innerHTML = '<i class="bi bi-send"></i> Send';
                    sendBtn.disabled = false;
                    console.error('Error:', error);
                });
        }

        document.getElementById('messageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMessageModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMessageModal();
            }
        });
    </script>
@endsection
