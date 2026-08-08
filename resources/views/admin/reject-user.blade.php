@extends('layouts.app')

@section('title', 'Reject User')
@section('role', 'Admin')
@section('page-title', '❌ Reject User')
@section('welcome-text', 'Provide a reason for rejecting this user')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.85rem;
            padding: 0.3rem 0.8rem;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.1);
            border-radius: 8px;
            transition: var(--transition);
            margin-bottom: 1.25rem;
        }

        .back-link:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
        }

        .reject-card {
            max-width: 600px;
            margin: 0 auto;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .user-info {
            background: #fef2f2;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #ef4444;
        }

        .user-info .label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
        }

        .user-info .value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
            margin-bottom: 0.3rem;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.85rem;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .btn-submit {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-submit:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }
    </style>

    <a href="{{ route('admin.users.pending') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to Pending Users</a>

    <div class="reject-card">
        <h4><i class="bi bi-exclamation-triangle" style="color:#ef4444;"></i> Reject User</h4>
        <p style="color:var(--text-gray); font-size:0.85rem;">Please provide a reason for rejecting this user registration.
        </p>

        <div class="user-info">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                <div>
                    <div class="label">Name</div>
                    <div class="value">{{ $user->name }}</div>
                </div>
                <div>
                    <div class="label">Email</div>
                    <div class="value">{{ $user->email }}</div>
                </div>
                <div>
                    <div class="label">Role</div>
                    <div class="value">{{ $user->role->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="label">Student ID</div>
                    <div class="value">{{ $user->student_id ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.users.process-reject', $user->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Rejection Reason <span style="color:#ef4444;">*</span></label>
                <textarea name="reason" placeholder="e.g., Student ID not verified, Department mismatch, etc." required></textarea>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.users.pending') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit"><i class="bi bi-x-lg"></i> Confirm Rejection</button>
            </div>
        </form>
    </div>
@endsection
