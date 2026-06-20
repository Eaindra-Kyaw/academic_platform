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
        }

        .btn-notify {
            background: #800000;
            color: white;
            border: none;
            padding: 0.2rem 0.6rem;
            border-radius: 0.4rem;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-notify:hover {
            background: #a00000;
            transform: scale(1.05);
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
        }
    </style>

    <div>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-number">{{ $students->count() }}</div>
                <div class="stat-label">Total Students</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $students->where('status', 'At Risk')->count() }}</div>
                <div class="stat-label">At Risk</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $students->where('status', 'Warning')->count() }}</div>
                <div class="stat-label">Warning</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $students->where('status', 'Eligible')->count() }}</div>
                <div class="stat-label">Eligible</div>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="🔍 Search by student name or email...">
        </div>

        <div style="overflow-x: auto;">
            <table class="students-table" id="studentsTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Student ID</th>
                        <th>Year</th>
                        <th>Attendance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td><strong>{{ $student->name }}</strong></td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                            <td> {{ $student->current_year }}th year</td>
                            <td>
                                {{ $student->attendance_percentage ?? 0 }}%
                                <div class="progress-bar"
                                    style="height:4px; background:#e5e7eb; border-radius:4px; margin-top:4px;">
                                    <div
                                        style="width:{{ $student->attendance_percentage ?? 0 }}%; height:4px; background:#800000; border-radius:4px;">
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if (($student->attendance_percentage ?? 0) >= 75)
                                    <span class="status-eligible">Eligible</span>
                                @elseif(($student->attendance_percentage ?? 0) >= 60)
                                    <span class="status-warning">Warning</span>
                                @else
                                    <span class="status-risk">At Risk</span>
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
                            <td colspan="7" style="text-align:center; padding:2rem;">No students found</td>
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
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#studentsTable tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

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
