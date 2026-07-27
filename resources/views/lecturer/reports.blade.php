@extends('layouts.app')

@section('title', 'Export Reports')
@section('role', 'Lecturer')
@section('page-title', ' Reports')
@section('welcome-text', 'Generate and export attendance reports with roll call data')

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
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        .report-card {
            background: var(--white);
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .report-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .report-card h4 {
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .report-card p {
            font-size: 0.8rem;
            color: var(--text-gray);
        }

        .stats-mini-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.5rem;
            margin: 0.5rem 0;
        }

        .stats-mini-box {
            background: var(--bg-main);
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            text-align: center;
        }

        .stats-mini-box .num {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .stats-mini-box .label {
            font-size: 0.6rem;
            color: var(--text-gray);
        }

        .export-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .btn-export {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.25);
        }

        .btn-export.pdf {
            background: #dc2626;
        }

        .btn-export.pdf:hover {
            background: #b91c1c;
        }

        .btn-export.excel {
            background: #16a34a;
        }

        .btn-export.excel:hover {
            background: #15803d;
        }

        .btn-export.csv {
            background: #0ea5e9;
        }

        .btn-export.csv:hover {
            background: #0284c7;
        }

        .course-stats-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .course-stats-table th {
            text-align: left;
            padding: 0.4rem 0.5rem;
            background: var(--bg-main);
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--text-gray);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .course-stats-table td {
            padding: 0.4rem 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .course-stats-table tr:hover {
            background: var(--bg-main);
        }

        select,
        input {
            padding: 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.5rem;
            font-size: 0.8rem;
            min-width: 200px;
            background: var(--bg-main);
            transition: all 0.3s ease;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .btn-at-risk {
            background: var(--danger);
            color: var(--white);
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-at-risk:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }

        /* ============================================================
                       CONFIRMATION MODAL STYLES
                       ============================================================ */
        .confirmation-modal-overlay {
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

        .confirmation-modal-overlay.show {
            display: flex;
        }

        .confirmation-modal {
            background: var(--white);
            border-radius: 1rem;
            max-width: 500px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .confirmation-modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--gray-50);
            border-radius: 1rem 1rem 0 0;
        }

        .confirmation-modal-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .confirmation-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-gray);
            cursor: pointer;
            transition: all 0.2s;
            padding: 0 4px;
        }

        .confirmation-modal-close:hover {
            color: var(--text-dark);
            transform: rotate(90deg);
        }

        .confirmation-modal-body {
            padding: 1.5rem;
        }

        .confirmation-modal-body .info-icon {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .confirmation-modal-body .info-text {
            text-align: center;
            color: var(--text-gray);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .confirmation-modal-body .info-text strong {
            color: var(--text-dark);
        }

        .confirmation-modal-body .comment-group {
            margin-top: 0.5rem;
        }

        .confirmation-modal-body .comment-group label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
            margin-bottom: 0.3rem;
        }

        .confirmation-modal-body .comment-group textarea {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.5rem;
            font-size: 0.85rem;
            resize: vertical;
            min-height: 80px;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .confirmation-modal-body .comment-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .confirmation-modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            background: var(--gray-50);
            border-radius: 0 0 1rem 1rem;
        }

        .btn-cancel-modal {
            padding: 0.5rem 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.5rem;
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .btn-cancel-modal:hover {
            background: var(--gray-100);
        }

        .btn-confirm-modal {
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            background: var(--primary);
            color: var(--white);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-confirm-modal:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.25);
        }

        .btn-confirm-modal:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-confirm-modal.danger {
            background: var(--danger);
        }

        .btn-confirm-modal.danger:hover {
            background: #b91c1c;
        }

        .btn-confirm-modal.success {
            background: var(--success);
        }

        .btn-confirm-modal.success:hover {
            background: #059669;
        }

        @media (max-width: 768px) {
            .export-buttons {
                flex-direction: column;
            }

            .btn-export {
                width: 100%;
                justify-content: center;
            }

            .course-stats-table {
                font-size: 0.7rem;
            }

            .confirmation-modal {
                width: 98%;
                margin: 10px;
            }
        }
    </style>

    <div>

        <!-- Course Statistics -->
        <div class="report-card">
            <h4>📈 Course Performance Summary</h4>
            @if (isset($courseStats) && count($courseStats) > 0)
                <table class="course-stats-table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Students</th>
                            <th>Attendance</th>
                            <th>Avg Roll Call</th>
                            <th>Eligible</th>
                            <th>Warning</th>
                            <th>Not Eligible</th>
                            <th>High Risk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courseStats as $stat)
                            <tr>
                                <td>
                                    <strong>{{ $stat['code'] }}</strong>
                                    <br>
                                    <small style="color: var(--text-gray);">{{ $stat['name'] }}</small>
                                </td>
                                <td>{{ $stat['students'] }}</td>
                                <td>
                                    <span
                                        class="stat-badge {{ $stat['attendance'] >= 75 ? 'good' : ($stat['attendance'] >= 60 ? 'warning' : 'danger') }}">
                                        {{ $stat['attendance'] }}%
                                    </span>
                                </td>
                                <td>{{ $stat['avg_roll_call'] ?? 'N/A' }}/10</td>
                                <td>{{ $stat['eligible'] ?? 0 }}</td>
                                <td>{{ $stat['warning'] ?? 0 }}</td>
                                <td>{{ $stat['not_eligible'] ?? 0 }}</td>
                                <td>
                                    @if (($stat['high_risk'] ?? 0) > 0)
                                        <span class="stat-badge danger">{{ $stat['high_risk'] }}</span>
                                    @else
                                        <span style="color: #9ca3af;">0</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: var(--text-gray);">No course data available.</p>
            @endif
        </div>

        <!-- Attendance Report -->
        <div class="report-card">
            <h4>📋 Attendance Report</h4>
            <p>Generate detailed attendance report with roll call breakdown</p>

            <div style="margin: 1rem 0;">
                <select id="reportCourse" style="width: 100%; max-width: 300px;">
                    <option value="">Select Course</option>
                    @foreach ($courses ?? [] as $course)
                        <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <input type="date" id="startDate" placeholder="Start Date">
                <input type="date" id="endDate" placeholder="End Date">
            </div>

            <div class="export-buttons">
                <button class="btn-export pdf" onclick="openExportModal('pdf')">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </button>
                <button class="btn-export excel" onclick="openExportModal('excel')">
                    <i class="bi bi-file-excel"></i> Export Excel
                </button>
                <button class="btn-export csv" onclick="openExportModal('csv')">
                    <i class="bi bi-file-spreadsheet"></i> Export CSV
                </button>
            </div>
        </div>

        <!-- At-Risk Report -->
        <div class="report-card">
            <h4>🚨 At-Risk Students Report</h4>
            <p>Generate report of students with low attendance or high risk</p>
            <button class="btn-at-risk" onclick="openAtRiskModal()">
                <i class="bi bi-exclamation-triangle"></i> Generate At-Risk Report
            </button>
        </div>

        <!-- Quick Stats (commented out) -->
        {{-- <div class="report-card">
            <h4>📊 Quick Stats</h4>
            <div class="stats-mini-grid">
                <div class="stats-mini-box">
                    <div class="num">{{ $totalSessions ?? 0 }}</div>
                    <div class="label">Total Sessions</div>
                </div>
                <div class="stats-mini-box">
                    <div class="num">{{ $activeSessions ?? 0 }}</div>
                    <div class="label">Active Sessions</div>
                </div>
                <div class="stats-mini-box">
                    <div class="num">{{ $averageAttendance ?? 0 }}%</div>
                    <div class="label">Avg Attendance</div>
                </div>
                <div class="stats-mini-box">
                    <div class="num">{{ $totalStudents ?? 0 }}</div>
                    <div class="label">Total Students</div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- ============================================================
                     CONFIRMATION MODAL
                     ============================================================ -->
    <div class="confirmation-modal-overlay" id="confirmationModal">
        <div class="confirmation-modal">
            <div class="confirmation-modal-header">
                <h5>
                    <span id="modalIcon">📋</span>
                    <span id="modalTitle">Confirm Export</span>
                </h5>
                <button class="confirmation-modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="confirmation-modal-body">
                <div class="info-icon" id="modalInfoIcon">📊</div>
                <div class="info-text" id="modalInfoText">
                    <strong>Are you sure you want to export this report?</strong>
                    <br>
                    <span id="modalDetails">This will download a CSV file.</span>
                </div>

                <div class="comment-group">
                    <label for="exportComment">📝 Add a comment or note (optional):</label>
                    <textarea id="exportComment" placeholder="e.g., Report generated for weekly review..." rows="3"></textarea>
                </div>
            </div>
            <div class="confirmation-modal-footer">
                <button class="btn-cancel-modal" onclick="closeModal()">Cancel</button>
                <button class="btn-confirm-modal" id="confirmExportBtn" onclick="confirmExport()">
                    <i class="bi bi-download"></i> Confirm & Export
                </button>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // STATE VARIABLES
        // ============================================================
        let pendingExportType = null; // 'pdf', 'excel', 'csv', or 'at-risk'
        let pendingExportData = null; // Stores parameters for the export

        // ============================================================
        // OPEN MODAL FUNCTIONS
        // ============================================================

        /**
         * Open modal for attendance report export (PDF, Excel, CSV)
         */
        function openExportModal(type) {
            const courseId = document.getElementById('reportCourse').value;
            if (!courseId) {
                alert('⚠️ Please select a course first.');
                return;
            }

            // Store the export type and parameters
            pendingExportType = type;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            pendingExportData = {
                course_id: courseId,
                start_date: startDate,
                end_date: endDate
            };

            // Get course name for display
            const courseSelect = document.getElementById('reportCourse');
            const courseName = courseSelect.options[courseSelect.selectedIndex].text;

            // Configure modal based on export type
            const icons = {
                'pdf': {
                    icon: '📄',
                    color: '#dc2626'
                },
                'excel': {
                    icon: '📊',
                    color: '#16a34a'
                },
                'csv': {
                    icon: '📋',
                    color: '#0ea5e9'
                }
            };

            const typeInfo = icons[type] || icons['csv'];

            document.getElementById('modalIcon').textContent = typeInfo.icon;
            document.getElementById('modalTitle').textContent = 'Export ' + type.toUpperCase() + ' Report';
            document.getElementById('modalInfoIcon').textContent = '📊';
            document.getElementById('modalDetails').innerHTML = `
                <strong>Course:</strong> ${courseName}<br>
                <strong>Format:</strong> ${type.toUpperCase()}<br>
                ${startDate ? `<strong>From:</strong> ${startDate}<br>` : ''}
                ${endDate ? `<strong>To:</strong> ${endDate}` : ''}
            `;

            // Set confirm button style
            const confirmBtn = document.getElementById('confirmExportBtn');
            confirmBtn.className = 'btn-confirm-modal';
            confirmBtn.style.background = typeInfo.color;

            // Clear previous comment
            document.getElementById('exportComment').value = '';

            // Show modal
            document.getElementById('confirmationModal').classList.add('show');
        }

        /**
         * Open modal for At-Risk report export
         */
        function openAtRiskModal() {
            const courseId = document.getElementById('reportCourse').value;

            pendingExportType = 'at-risk';
            pendingExportData = {
                course_id: courseId || ''
            };

            // Get course name for display
            let courseName = 'All Courses';
            if (courseId) {
                const courseSelect = document.getElementById('reportCourse');
                courseName = courseSelect.options[courseSelect.selectedIndex].text;
            }

            document.getElementById('modalIcon').textContent = '🚨';
            document.getElementById('modalTitle').textContent = 'Export At-Risk Report';
            document.getElementById('modalInfoIcon').textContent = '⚠️';
            document.getElementById('modalDetails').innerHTML = `
                <strong>Course:</strong> ${courseName}<br>
                <strong>Risk Level:</strong> Medium & High Risk Students<br>
                <span style="color: #dc2626; font-weight: 600;">⚠️ This report will include all at-risk students.</span>
            `;

            // Set confirm button to danger style
            const confirmBtn = document.getElementById('confirmExportBtn');
            confirmBtn.className = 'btn-confirm-modal danger';
            confirmBtn.style.background = '';

            // Clear previous comment
            document.getElementById('exportComment').value = '';

            // Show modal
            document.getElementById('confirmationModal').classList.add('show');
        }

        // ============================================================
        // MODAL ACTIONS
        // ============================================================

        /**
         * Close the confirmation modal
         */
        function closeModal() {
            document.getElementById('confirmationModal').classList.remove('show');
            pendingExportType = null;
            pendingExportData = null;
        }

        /**
         * Confirm and execute the export
         */
        function confirmExport() {
            const comment = document.getElementById('exportComment').value.trim();
            const confirmBtn = document.getElementById('confirmExportBtn');

            // Disable button to prevent double-clicks
            confirmBtn.disabled = true;
            confirmBtn.innerHTML =
                '<span class="loading-spinner" style="display:inline-block;width:16px;height:16px;border:2px solid #fff;border-top:2px solid transparent;border-radius:50%;animation:spin 0.8s linear infinite;margin-right:8px;"></span> Exporting...';

            // Log the comment (you can save this to database if needed)
            if (comment) {
                console.log('📝 Export Comment:', comment);
                // Optional: You can send this comment to the server
                // pendingExportData.comment = comment;
            }

            // Execute the actual export based on type
            try {
                if (pendingExportType === 'at-risk') {
                    executeAtRiskExport();
                } else {
                    executeAttendanceExport();
                }
            } catch (error) {
                console.error('Export error:', error);
                alert('❌ Error generating report. Please try again.');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="bi bi-download"></i> Confirm & Export';
            }

            // Close modal after a short delay
            setTimeout(() => {
                closeModal();
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="bi bi-download"></i> Confirm & Export';
            }, 1000);
        }

        /**
         * Execute attendance report export (PDF, Excel, CSV)
         */
        function executeAttendanceExport() {
            const type = pendingExportType;
            const data = pendingExportData;

            let url = '{{ route('lecturer.reports.export') }}?type=' + type + '&course_id=' + data.course_id;
            if (data.start_date) url += '&start_date=' + data.start_date;
            if (data.end_date) url += '&end_date=' + data.end_date;

            // Open in new tab for download
            window.open(url, '_blank');
        }

        /**
         * Execute At-Risk report export
         */
        function executeAtRiskExport() {
            const data = pendingExportData;
            let url = '{{ route('lecturer.reports.at-risk') }}';
            if (data.course_id) {
                url += '?course_id=' + data.course_id;
            }

            // Open in new tab for download
            window.open(url, '_blank');
        }

        // ============================================================
        // ADD SPINNER KEYFRAMES (injected dynamically)
        // ============================================================
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        // ============================================================
        // KEYBOARD SHORTCUT: ESC to close modal
        // ============================================================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // ============================================================
        // CLICK OUTSIDE MODAL TO CLOSE
        // ============================================================
        document.getElementById('confirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
@endsection
