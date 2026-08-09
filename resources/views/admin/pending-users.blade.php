@extends('layouts.app')

@section('title', 'Pending Approvals')
@section('role', 'Admin')
@section('page-title', ' Pending User Approvals')
@section('welcome-text', 'Review and approve new user registrations')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
            box-shadow: var(--shadow);
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .stat-number.pending {
            color: #f59e0b;
        }

        .stat-number.approved {
            color: #10b981;
        }

        .stat-number.rejected {
            color: #ef4444;
        }

        .stat-number.total {
            color: #0A2463;
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-card {
            background: var(--white);
            border-radius: 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .user-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-hover);
        }

        .user-card .left .name {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--text-dark);
        }

        .user-card .left .meta {
            font-size: 0.7rem;
            color: var(--text-gray);
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.1rem;
        }

        .user-card .right {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-approve {
            background: #10b981;
            color: white;
        }

        .btn-approve:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-view {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-view:hover {
            background: #bfdbfe;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 0.15rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.65rem;
            font-weight: 600;
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

        /* ============================================================
                                       🟢 BEAUTIFUL COMMENT MODAL - FIXED
                                       ============================================================ */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(6px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: var(--white);
            border-radius: 16px;
            max-width: 520px;
            width: 100%;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
            animation: modalSlideIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            background: #fafbfc;
        }

        .modal-header .modal-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .modal-header .modal-icon.approve {
            background: #d1fae5;
            color: #10b981;
        }

        .modal-header .modal-icon.reject {
            background: #fee2e2;
            color: #ef4444;
        }

        .modal-header .modal-title-group {
            flex: 1;
            margin-left: 0.75rem;
        }

        .modal-header .modal-title-group h4 {
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.05rem;
        }

        .modal-header .modal-title-group p {
            margin: 0;
            font-size: 0.8rem;
            color: var(--text-gray);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-gray);
            transition: var(--transition);
            padding: 0 4px;
            line-height: 1;
        }

        .modal-close:hover {
            color: var(--text-dark);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-body .user-info-preview {
            background: #f8fafc;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .modal-body .user-info-preview .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .modal-body .user-info-preview .details .name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .modal-body .user-info-preview .details .email {
            font-size: 0.7rem;
            color: var(--text-gray);
        }

        .modal-body .comment-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--text-dark);
            display: block;
            margin-bottom: 0.3rem;
        }

        .modal-body .comment-label .optional {
            font-weight: 400;
            color: var(--text-gray);
            font-size: 0.7rem;
        }

        .modal-body textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 10px;
            font-size: 0.85rem;
            resize: vertical;
            min-height: 90px;
            font-family: inherit;
            transition: var(--transition);
            background: #fafbfc;
        }

        .modal-body textarea:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(10, 36, 99, 0.06);
        }

        .modal-body textarea::placeholder {
            color: #9ca3af;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            background: #fafbfc;
        }

        .btn-cancel-modal {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.12);
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.85rem;
        }

        .btn-cancel-modal:hover {
            background: #f3f4f6;
        }

        .btn-confirm {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            border: none;
            color: var(--white);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-confirm.approve {
            background: #10b981;
        }

        .btn-confirm.approve:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-confirm.reject {
            background: #ef4444;
        }

        .btn-confirm.reject:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-confirm:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .user-card {
                flex-direction: column;
                align-items: stretch;
            }

            .user-card .right {
                justify-content: flex-start;
            }

            .modal-box {
                margin: 10px;
                max-height: 95vh;
                overflow-y: auto;
            }

            .modal-header {
                flex-wrap: wrap;
            }

            .modal-header .modal-title-group {
                margin-left: 0;
                width: 100%;
                margin-top: 0.3rem;
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
                font-size: 1.3rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                flex-direction: column-reverse;
            }

            .btn-cancel-modal,
            .btn-confirm {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    {{-- Stats --}}
    {{-- <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number pending">{{ $stats['pending'] ?? 0 }}</div>
            <div class="stat-label"> Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-number approved">{{ $stats['approved'] ?? 0 }}</div>
            <div class="stat-label"> Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-number rejected">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="stat-label"> Rejected</div>
        </div>
        <div class="stat-card">
            <div class="stat-number total">{{ $stats['total'] ?? 0 }}</div>
            <div class="stat-label"> Total</div>
        </div>
    </div> --}}

    @if (session('success'))
        <div
            style="background:#d1fae5; color:#166534; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div
            style="background:#fee2e2; color:#991b1b; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1rem; display:flex; align-items:center; gap:0.5rem;">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    <h5 style="font-weight:600; color:var(--text-dark); margin-bottom:1rem;">
        <i class="bi bi-clock-history" style="color:var(--primary);"></i> Pending Approvals
        <span style="font-size:0.7rem; font-weight:400; color:var(--text-gray); margin-left:0.5rem;">
            ({{ $pendingUsers->total() }} waiting)
        </span>
    </h5>

    @if ($pendingUsers->count() > 0)
        @foreach ($pendingUsers as $user)
            <div class="user-card">
                <div class="left">
                    <div class="name">{{ $user->name }}</div>
                    <div class="meta">
                        <span><i class="bi bi-envelope"></i> {{ $user->email }}</span>
                        <span><i class="bi bi-person-badge"></i> {{ $user->role->name ?? 'N/A' }}</span>
                        @if ($user->department)
                            <span><i class="bi bi-building"></i> {{ $user->department->name }}</span>
                        @endif
                        @if ($user->student_id)
                            <span><i class="bi bi-card-text"></i> {{ $user->student_id }}</span>
                        @endif
                        <span><i class="bi bi-clock"></i> {{ $user->created_at->diffForHumans() }}</span>
                        <span class="badge-pending"> Pending</span>
                    </div>
                </div>
                <div class="right">
                    <button class="btn-sm btn-approve"
                        onclick="openApproveModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                        <i class="bi bi-check-lg"></i> Approve
                    </button>
                    <button class="btn-sm btn-reject"
                        onclick="openRejectModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                        <i class="bi bi-x-lg"></i> Reject
                    </button>
                    <a href="#" class="btn-sm btn-view" onclick="alert('Full profile coming soon!')">
                        <i class="bi bi-eye"></i> View
                    </a>
                </div>
            </div>
        @endforeach

        @if ($pendingUsers->hasPages())
            <div style="margin-top:1rem;">
                {{ $pendingUsers->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <i class="bi bi-check-circle" style="color:#10b981;"></i>
            <p style="font-size:0.95rem; color:var(--text-dark); font-weight:500;">No pending approvals</p>
            <p style="font-size:0.85rem;">All users have been processed.</p>
        </div>
    @endif

    {{-- ============================================================
    🟢 BEAUTIFUL APPROVE MODAL
    ============================================================ --}}
    <div class="modal-overlay" id="approveModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon approve">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div class="modal-title-group">
                    <h4>Approve User</h4>
                    <p>Confirm approval and send notification</p>
                </div>
                <button class="modal-close" onclick="closeModal('approveModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="user-info-preview">
                    <div class="avatar-sm" id="approveAvatar">U</div>
                    <div class="details">
                        <div class="name" id="approveUserName">User Name</div>
                        <div class="email" id="approveUserEmail">user@example.com</div>
                    </div>
                </div>

                <label class="comment-label">📝 Add a comment <span class="optional">(optional - will be included in the
                        email)</span></label>
                <textarea id="approveComment"
                    placeholder="e.g., Welcome to the MTU Academic Portal! Please login with your registered credentials."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel-modal" onclick="closeModal('approveModal')">Cancel</button>
                <button class="btn-confirm approve" id="confirmApproveBtn">
                    <i class="bi bi-check-lg"></i> Confirm Approval
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================
    🟢 BEAUTIFUL REJECT MODAL
    ============================================================ --}}
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon reject">
                    <i class="bi bi-x-lg"></i>
                </div>
                <div class="modal-title-group">
                    <h4>Reject User</h4>
                    <p>Provide a reason for rejection</p>
                </div>
                <button class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="user-info-preview">
                    <div class="avatar-sm" id="rejectAvatar">U</div>
                    <div class="details">
                        <div class="name" id="rejectUserName">User Name</div>
                        <div class="email" id="rejectUserEmail">user@example.com</div>
                    </div>
                </div>

                <label class="comment-label">📝 Reason for Rejection <span style="color: #ef4444;">*</span></label>
                <textarea id="rejectComment"
                    placeholder="e.g., Student ID not verified, Department mismatch, Invalid email domain, etc."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel-modal" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="btn-confirm reject" id="confirmRejectBtn">
                    <i class="bi bi-x-lg"></i> Confirm Rejection
                </button>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // STATE VARIABLES
        // ============================================================
        let currentUserId = null;
        let currentAction = null; // 'approve' or 'reject'

        // ============================================================
        // OPEN APPROVE MODAL
        // ============================================================
        function openApproveModal(userId, userName, userEmail) {
            currentUserId = userId;
            currentAction = 'approve';

            document.getElementById('approveAvatar').textContent = userName.charAt(0).toUpperCase();
            document.getElementById('approveUserName').textContent = userName;
            document.getElementById('approveUserEmail').textContent = userEmail;
            document.getElementById('approveComment').value = '';

            document.getElementById('approveModal').classList.add('show');

            // Focus the textarea after animation
            setTimeout(() => {
                document.getElementById('approveComment').focus();
            }, 400);
        }

        // ============================================================
        // OPEN REJECT MODAL
        // ============================================================
        function openRejectModal(userId, userName, userEmail) {
            currentUserId = userId;
            currentAction = 'reject';

            document.getElementById('rejectAvatar').textContent = userName.charAt(0).toUpperCase();
            document.getElementById('rejectUserName').textContent = userName;
            document.getElementById('rejectUserEmail').textContent = userEmail;
            document.getElementById('rejectComment').value = '';

            document.getElementById('rejectModal').classList.add('show');

            setTimeout(() => {
                document.getElementById('rejectComment').focus();
            }, 400);
        }

        // ============================================================
        // CLOSE MODAL
        // ============================================================
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            currentUserId = null;
            currentAction = null;
        }

        // ============================================================
        // CONFIRM APPROVE
        // ============================================================
        document.getElementById('confirmApproveBtn').addEventListener('click', function() {
            const comment = document.getElementById('approveComment').value.trim();
            const btn = this;

            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm" style="width:16px;height:16px;"></span> Processing...';

            // Build URL with comment as query parameter
            let url = `/admin/users/${currentUserId}/approve`;
            if (comment) {
                url += `?comment=${encodeURIComponent(comment)}`;
            }

            window.location.href = url;
        });

        // ============================================================
        // CONFIRM REJECT
        // ============================================================
        document.getElementById('confirmRejectBtn').addEventListener('click', function() {
            const reason = document.getElementById('rejectComment').value.trim();
            const btn = this;

            if (!reason) {
                document.getElementById('rejectComment').style.borderColor = '#ef4444';
                document.getElementById('rejectComment').style.background = '#fef2f2';
                alert('Please provide a reason for rejection.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm" style="width:16px;height:16px;"></span> Processing...';

            // Build URL with reason as query parameter
            let url = `/admin/users/${currentUserId}/reject?reason=${encodeURIComponent(reason)}`;

            window.location.href = url;
        });

        // ============================================================
        // CLEAR ERROR STATE ON INPUT
        // ============================================================
        document.getElementById('rejectComment').addEventListener('input', function() {
            this.style.borderColor = '';
            this.style.background = '';
        });

        // ============================================================
        // CLOSE MODAL ON OVERLAY CLICK
        // ============================================================
        document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                    currentUserId = null;
                    currentAction = null;
                }
            });
        });

        // ============================================================
        // CLOSE MODAL ON ESC KEY
        // ============================================================
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.show').forEach(function(modal) {
                    modal.classList.remove('show');
                });
                currentUserId = null;
                currentAction = null;
            }
        });

        // ============================================================
        // KEYBOARD SHORTCUT: ENTER to submit
        // ============================================================
        document.getElementById('approveComment').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                document.getElementById('confirmApproveBtn').click();
            }
        });

        document.getElementById('rejectComment').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                document.getElementById('confirmRejectBtn').click();
            }
        });
    </script>
@endsection
