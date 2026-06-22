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
        .stats-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            flex: 1;
            min-width: 120px;
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: #800000;
        }

        .stat-number.green {
            color: #10b981;
        }

        .stat-number.yellow {
            color: #f59e0b;
        }

        .stat-number.red {
            color: #ef4444;
        }

        .stat-number.blue {
            color: #3b82f6;
        }

        .stat-label {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.75rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .students-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            background: #f9fafb;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }

        .students-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f2f4;
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .students-table tbody tr:hover {
            background: #fafafa;
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
            background: white;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 2rem;
            font-size: 0.8rem;
            min-width: 150px;
        }

        .search-input:focus {
            outline: none;
            border-color: #800000;
        }

        .btn-notify {
            background: #800000;
            color: white;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-notify:hover {
            background: #a00000;
            transform: scale(1.05);
        }

        .btn-notify i {
            margin-right: 0.2rem;
        }

        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 4px;
        }

        .progress-bar .fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .progress-bar .fill.green {
            background: #10b981;
        }

        .progress-bar .fill.yellow {
            background: #f59e0b;
        }

        .progress-bar .fill.red {
            background: #ef4444;
        }

        /* ===== MESSAGE MODAL ===== */
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
        }

        .message-modal-overlay.show {
            display: flex;
        }

        .message-modal-box {
            background: white;
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
            color: #3b82f6;
            margin-bottom: 0.5rem;
        }

        .message-modal-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 0.3rem 0;
        }

        .message-modal-box p {
            text-align: center;
            font-size: 0.85rem;
            color: #6b7280;
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
            color: #10b981;
            border: 1px solid #a7f3d0;
        }

        .message-modal-box #messageStatus.error {
            display: block;
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fca5a5;
        }

        .message-modal-box textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            resize: vertical;
            min-height: 80px;
        }

        .message-modal-box textarea:focus {
            outline: none;
            border-color: #800000;
        }

        .message-modal-box input[type="text"] {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .message-modal-box input[type="text"]:focus {
            outline: none;
            border-color: #800000;
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
            background: #800000;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .message-modal-box .btn-send:hover {
            background: #a00000;
        }

        .message-modal-box .btn-send:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .message-modal-box .btn-close-modal {
            padding: 0.4rem 1.5rem;
            border-radius: 0.4rem;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid #e5e7eb;
            background: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .message-modal-box .btn-close-modal:hover {
            background: #f3f4f6;
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
                <button class="btn-notify" onclick="filterTable()" style="background:#800000; padding:0.4rem 1rem;">
                    <i class="bi bi-funnel"></i> Apply Filter
                </button>
                <button class="btn-notify" onclick="resetFilters()" style="background:#6b7280; padding:0.4rem 1rem;">
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
                            $attendance = $student->attendance_percentage ?? 0;
                            $rollCall = $student->roll_call_mark ?? 0;
                            $status = $student->status ?? 'N/A';
                            $badgeClass =
                                $status == 'Eligible'
                                    ? 'status-eligible'
                                    : ($status == 'Warning'
                                        ? 'status-warning'
                                        : 'status-risk');
                            $riskLevel = $status == 'Eligible' ? 'Low' : ($status == 'Warning' ? 'Medium' : 'High');
                            $barColor = $attendance >= 75 ? 'green' : ($attendance >= 60 ? 'yellow' : 'red');
                        @endphp
                        <tr class="student-row" data-name="{{ strtolower($student->name) }}"
                            data-email="{{ strtolower($student->email) }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $student->name }}</strong>
                                <br>
                                <small style="color: #6b7280; font-size: 0.65rem;">ID:
                                    {{ $student->student_id ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $student->email }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <span style="font-weight:600; font-size:0.9rem; min-width:45px;">
                                        {{ number_format($attendance, 1) }}%
                                    </span>
                                    <div class="progress-bar" style="flex:1; min-width:60px;">
                                        <div class="fill {{ $barColor }}"
                                            style="width: {{ min($attendance, 100) }}%;"></div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center; font-weight:600; font-size:0.9rem;">
                                {{ number_format($rollCall, 1) }}/10
                            </td>
                            <td>
                                <span class="{{ $badgeClass }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td>
                                <span class="risk-badge {{ strtolower($riskLevel) }}">
                                    {{ $riskLevel }}
                                </span>
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
                            <td colspan="8" style="text-align:center; padding:2rem; color:#9ca3af;">
                                <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                No students enrolled in your courses.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MESSAGE MODAL -->
    <!-- ============================================================ -->
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
        // ============================================================
        // SEARCH FUNCTIONALITY
        // ============================================================
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

        // ============================================================
        // MESSAGE MODAL
        // ============================================================
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

            // Disable button and show loading
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="bi bi-hourglass"></i> Sending...';

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send AJAX request
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

                        // Close modal after 2 seconds
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

        // Close modal when clicking outside
        document.getElementById('messageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMessageModal();
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMessageModal();
            }
        });
    </script>
@endsection
