{{-- resources/views/admin/pending-users.blade.php --}}
@extends('layouts.app')

@section('title', 'Pending Approvals')
@section('role', 'Admin')
@section('page-title', '📋 Pending User Approvals')
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
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
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

        .modal-overlay {
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

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: var(--white);
            border-radius: 12px;
            max-width: 500px;
            width: 95%;
            padding: 1.5rem;
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
            font-weight: 700;
            color: var(--text-dark);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-gray);
        }

        .modal-body textarea {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 6px;
            font-size: 0.85rem;
            resize: vertical;
            min-height: 80px;
            font-family: inherit;
        }

        .modal-footer {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .btn-cancel-modal {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-confirm-reject {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 6px;
            cursor: pointer;
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
        }
    </style>

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
            <div class="stat-label">📊 Total</div>
        </div>
    </div>

    @if (session('success'))
        <div style="background:#d1fae5; color:#166534; padding:0.75rem 1rem; border-radius:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
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
                        <span class="badge-pending">⏳ Pending</span>
                    </div>
                </div>
                <div class="right">
                    <button class="btn-sm btn-approve" onclick="approveUser({{ $user->id }})">
                        <i class="bi bi-check-lg"></i> Approve
                    </button>
                    <button class="btn-sm btn-reject"
                        onclick="openRejectModal({{ $user->id }}, '{{ addslashes($user->name) }}')">
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

    {{-- Reject Modal --}}
    <div class="modal-overlay" id="rejectModal">
        <div class="modal-box">
            <div class="modal-header">
                <h4><i class="bi bi-exclamation-triangle" style="color:#ef4444;"></i> Reject User</h4>
                <button class="modal-close" onclick="closeRejectModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="color:var(--text-gray); margin-bottom:0.5rem;">
                    You are about to reject <strong id="rejectUserName"></strong>.
                    Please provide a reason:
                </p>
                <textarea id="rejectionReason" placeholder="e.g., Student ID not verified, Department mismatch, etc."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel-modal" onclick="closeRejectModal()">Cancel</button>
                <button class="btn-confirm-reject" id="confirmRejectBtn">Confirm Rejection</button>
            </div>
        </div>
    </div>

    <script>
        let currentRejectUserId = null;

        function approveUser(userId) {
            if (confirm('Approve this user? They will receive an email notification.')) {
                window.location.href = `/admin/users/${userId}/approve`;
            }
        }

        function openRejectModal(userId, userName) {
            currentRejectUserId = userId;
            document.getElementById('rejectUserName').textContent = userName;
            document.getElementById('rejectionReason').value = '';
            document.getElementById('rejectModal').classList.add('show');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.remove('show');
            currentRejectUserId = null;
        }

        document.getElementById('confirmRejectBtn').addEventListener('click', function() {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection.');
                return;
            }
            window.location.href =
            `/admin/users/${currentRejectUserId}/reject?reason=${encodeURIComponent(reason)}`;
        });

        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRejectModal();
            }
        });
    </script>
@endsection
