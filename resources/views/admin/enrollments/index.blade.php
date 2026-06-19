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
        .breadcrumb-bar {
            background: white;
            border-radius: 0.75rem;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .breadcrumb-bar .breadcrumb-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #6b7280;
        }

        .breadcrumb-bar .breadcrumb-item a {
            color: #800000;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-bar .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-bar .breadcrumb-item .separator {
            color: #d1d5db;
        }

        .breadcrumb-bar .breadcrumb-item.active {
            color: #1f2937;
            font-weight: 600;
        }

        /* Simple Stats */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-mini {
            background: white;
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            text-align: center;
        }

        .stat-mini .number {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-mini .number.pending {
            color: #d97706;
        }

        .stat-mini .number.approved {
            color: #10b981;
        }

        .stat-mini .number.rejected {
            color: #ef4444;
        }

        .stat-mini .number.total {
            color: #6366f1;
        }

        .stat-mini .label {
            font-size: 0.65rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.15rem;
        }

        /* Department Cards - Clean Grid */
        .dept-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        .dept-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            border: 2px solid #e5e7eb;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .dept-card:hover {
            border-color: #800000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            text-decoration: none;
            color: inherit;
        }

        .dept-card .dept-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #1f2937;
        }

        .dept-card .dept-code {
            font-size: 0.65rem;
            color: #9ca3af;
            display: inline-block;
            background: #f3f4f6;
            padding: 0.1rem 0.5rem;
            border-radius: 0.25rem;
            margin-top: 0.15rem;
        }

        .dept-card .dept-stats {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid #f3f4f6;
        }

        .dept-card .dept-stats span {
            font-size: 0.65rem;
            color: #6b7280;
        }

        .dept-card .dept-stats .num {
            font-weight: 700;
            color: #1f2937;
        }

        .dept-card .dept-stats .num.pending {
            color: #d97706;
        }

        /* Alerts */
        .alert {
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
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
        <div style="text-align:center; padding:4rem 2rem; background:white; border-radius:1rem; border:1px solid #e5e7eb;">
            <i class="bi bi-building" style="font-size:3rem; color:#d1d5db;"></i>
            <h3 style="color:#374151; margin-top:1rem;">No Departments Found</h3>
            <p style="color:#6b7280;">Please add departments to manage enrollments.</p>
        </div>
    @endif
@endsection
