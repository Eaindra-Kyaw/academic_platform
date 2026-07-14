{{-- resources/views/admin/enrollments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Enrollment Management')
@section('role', 'Admin')
@section('page-title', 'Enrollment Overview')
@section('welcome-text', 'Select a department to manage enrollments')

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
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-mini {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .stat-mini:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .stat-mini .number {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-mini .number.pending {
            color: var(--warning);
        }

        .stat-mini .number.approved {
            color: var(--success);
        }

        .stat-mini .number.rejected {
            color: var(--danger);
        }

        .stat-mini .number.total {
            color: var(--info);
        }

        .stat-mini .label {
            font-size: 0.65rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        .dept-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        .dept-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem;
            border: 2px solid rgba(10, 36, 99, 0.06);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
            display: block;
            box-shadow: var(--shadow);
        }

        .dept-card:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            text-decoration: none;
            color: inherit;
        }

        .dept-card .dept-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .dept-card .dept-code {
            font-size: 0.65rem;
            color: var(--text-gray);
            display: inline-block;
            background: #f3f4f6;
            padding: 0.1rem 0.5rem;
            border-radius: 6px;
            margin-top: 0.15rem;
        }

        .dept-card .dept-stats {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .dept-card .dept-stats span {
            font-size: 0.65rem;
            color: var(--text-gray);
        }

        .dept-card .dept-stats .num {
            font-weight: 700;
            color: var(--text-dark);
        }

        .dept-card .dept-stats .num.pending {
            color: var(--warning);
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

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
        }

        .empty-state h3 {
            color: var(--text-dark);
            margin-top: 1rem;
        }

        .empty-state p {
            color: var(--text-gray);
        }

        @media (max-width: 768px) {
            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-mini .number {
                font-size: 1.4rem;
            }

            .dept-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-row {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-mini {
                padding: 0.75rem;
            }

            .stat-mini .number {
                font-size: 1.2rem;
            }

            .dept-grid {
                grid-template-columns: 1fr;
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

    {{-- Stats --}}
    <div class="stats-row">
        <div class="stat-mini">
            <div class="number pending">{{ $stats['pending'] ?? 0 }}</div>
            <div class="label">⏳ Pending</div>
        </div>
        <div class="stat-mini">
            <div class="number approved">{{ $stats['approved'] ?? 0 }}</div>
            <div class="label">✅ Approved</div>
        </div>
        <div class="stat-mini">
            <div class="number rejected">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="label">❌ Rejected</div>
        </div>
        <div class="stat-mini">
            <div class="number total">{{ $stats['total'] ?? 0 }}</div>
            <div class="label">📊 Total</div>
        </div>
    </div>

    {{-- Department Grid --}}
    <div class="dept-grid">
        @foreach ($departments as $dept)
            <a href="{{ route('admin.enrollments.department', ['departmentId' => $dept->id]) }}" class="dept-card">
                <div class="dept-name">{{ $dept->name }}</div>
                <div class="dept-code">{{ $dept->code }}</div>
                <div class="dept-stats">
                    <span>📚 <span class="num">{{ $dept->enrollments_count ?? 0 }}</span> enrollments</span>
                    <span>⏳ <span class="num pending">{{ $dept->pending_count ?? 0 }}</span> pending</span>
                </div>
            </a>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if ($departments->isEmpty())
        <div class="empty-state">
            <i class="bi bi-building"></i>
            <h3>No Departments Found</h3>
            <p>Please add departments to manage enrollments.</p>
        </div>
    @endif
@endsection
