{{-- resources/views/admin/risk/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Student Risk Analytics Dashboard')
@section('role', 'Admin')
@section('page-title', '🎯 Predictive Risk Analytics')
@section('welcome-text', 'AI-powered student success prediction & intervention system')

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
            --danger-light: #fee2e2;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --info: #3b82f6;
            --info-light: #dbeafe;
            --purple: #8b5cf6;
            --purple-light: #ede9fe;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-grid-compact {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card-compact {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
        }

        .stat-card-compact:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(10, 36, 99, 0.15);
        }

        .stat-card-compact .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .stat-card-compact:hover .stat-icon {
            transform: scale(1.05) rotate(-3deg);
        }

        .stat-card-compact .stat-icon.primary {
            background: rgba(10, 36, 99, 0.08);
            color: var(--primary);
        }

        .stat-card-compact .stat-icon.success {
            background: rgba(16, 185, 129, 0.08);
            color: var(--success);
        }

        .stat-card-compact .stat-icon.warning {
            background: rgba(245, 158, 11, 0.08);
            color: var(--warning);
        }

        .stat-card-compact .stat-icon.danger {
            background: rgba(239, 68, 68, 0.08);
            color: var(--danger);
        }

        .stat-card-compact .stat-info {
            flex: 1;
            min-width: 0;
        }

        .stat-card-compact .stat-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .stat-card-compact .stat-number .suffix {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-gray);
            margin-left: 0.15rem;
        }

        .stat-card-compact .stat-label {
            font-size: 0.6rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .filter-bar-premium {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            background: var(--white);
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            align-items: center;
        }

        .filter-bar-premium .filter-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .filter-bar-premium .filter-group label {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .filter-bar-premium .filter-group select {
            padding: 0.35rem 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.8rem;
            background: #f8fafc;
            transition: var(--transition);
            color: var(--text-dark);
            min-width: 120px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
        }

        .filter-bar-premium .filter-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
            background: var(--white);
        }

        .filter-bar-premium .search-group {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex: 1;
            min-width: 150px;
        }

        .filter-bar-premium .search-group input {
            padding: 0.35rem 0.6rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.8rem;
            background: #f8fafc;
            transition: var(--transition);
            color: var(--text-dark);
            width: 100%;
            min-width: 120px;
            font-family: 'Inter', sans-serif;
        }

        .filter-bar-premium .search-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
            background: var(--white);
        }

        .filter-bar-premium .search-group input::placeholder {
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
            font-family: 'Inter', sans-serif;
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

        .btn-premium-success {
            background: linear-gradient(135deg, #059669, var(--success));
            color: var(--white);
        }

        .btn-premium-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.25);
            color: var(--white);
        }

        .chart-grid-premium {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card-premium {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .chart-card-premium:hover {
            border-color: rgba(10, 36, 99, 0.12);
            box-shadow: var(--shadow-hover);
        }

        .chart-card-premium .card-header {
            padding: 0.75rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .chart-card-premium .card-header .title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-card-premium .card-header .title .badge {
            font-size: 0.6rem;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
            background: rgba(10, 36, 99, 0.08);
            color: var(--primary);
            font-weight: 600;
        }

        .chart-card-premium .card-header .subtitle {
            font-size: 0.7rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .chart-card-premium .card-body {
            padding: 1.25rem;
        }

        .chart-container-premium {
            position: relative;
            height: 280px;
        }

        .factors-grid-premium {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.6rem;
        }

        .factor-item-premium {
            background: #fafbfc;
            border: 1px solid rgba(10, 36, 99, 0.06);
            border-radius: 8px;
            padding: 0.4rem 0.6rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
            font-size: 0.75rem;
        }

        .factor-item-premium:hover {
            border-color: var(--primary);
            background: rgba(10, 36, 99, 0.02);
            transform: translateX(4px);
        }

        .factor-item-premium .factor-name {
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.75rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .factor-item-premium .factor-count {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
            min-width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .dept-bars-premium {
            max-height: 260px;
            overflow-y: auto;
            padding: 0.25rem;
        }

        .dept-bars-premium::-webkit-scrollbar {
            width: 3px;
        }

        .dept-bars-premium::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .dept-bar-item {
            margin-bottom: 0.6rem;
        }

        .dept-bar-item .dept-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.15rem;
            gap: 0.5rem;
        }

        .dept-bar-item .dept-header .dept-name {
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--text-dark);
        }

        .dept-bar-item .dept-header .dept-counts {
            font-size: 0.65rem;
            color: var(--text-gray);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .dept-bar-item .dept-header .dept-counts .high {
            color: var(--danger);
            font-weight: 600;
        }

        .dept-bar-item .dept-header .dept-counts .medium {
            color: var(--warning);
            font-weight: 600;
        }

        .dept-bar-item .dept-header .dept-counts .low {
            color: var(--success);
            font-weight: 600;
        }

        .dept-bar-track {
            display: flex;
            gap: 2px;
            height: 6px;
            border-radius: 3px;
            overflow: hidden;
            background: #f1f5f9;
        }

        .dept-bar-track .segment {
            height: 100%;
            transition: width 0.8s ease;
        }

        .dept-bar-track .segment.high {
            background: var(--danger);
        }

        .dept-bar-track .segment.medium {
            background: var(--warning);
        }

        .dept-bar-track .segment.low {
            background: var(--success);
        }

        .table-wrapper-premium {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .table-premium {
            width: 100%;
            font-size: 0.78rem;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .table-premium thead {
            background: linear-gradient(135deg, #fafbfc, #f1f5f9);
        }

        .table-premium th {
            text-align: left;
            padding: 0.5rem 0.75rem;
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--text-gray);
            font-weight: 700;
            letter-spacing: 0.3px;
            border-bottom: 2px solid rgba(10, 36, 99, 0.06);
            background: #fafbfc;
            white-space: nowrap;
        }

        .table-premium td {
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            vertical-align: middle;
        }

        .table-premium tbody tr {
            transition: var(--transition);
        }

        .table-premium tbody tr:hover {
            background: rgba(10, 36, 99, 0.02);
        }

        .risk-badge-premium {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .risk-badge-premium.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .risk-badge-premium.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .risk-badge-premium.low {
            background: var(--success-light);
            color: #166534;
        }

        .attendance-cell .attendance-value {
            font-weight: 700;
            font-size: 0.8rem;
        }

        .attendance-cell .attendance-value.high {
            color: var(--success);
        }

        .attendance-cell .attendance-value.medium {
            color: var(--warning);
        }

        .attendance-cell .attendance-value.low {
            color: var(--danger);
        }

        .roll-call-cell {
            text-align: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .roll-call-cell .roll-value {
            display: inline-block;
            padding: 0.1rem 0.5rem;
            border-radius: 1rem;
            background: rgba(10, 36, 99, 0.06);
        }

        .roll-call-breakdown {
            font-size: 0.6rem;
            color: var(--text-gray);
            display: flex;
            gap: 0.3rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .roll-call-breakdown span {
            display: inline-block;
            padding: 0.05rem 0.3rem;
            border-radius: 0.5rem;
            background: #f1f5f9;
        }

        .risk-score-cell .score-value {
            font-weight: 800;
            font-size: 0.85rem;
            color: var(--primary);
        }

        .risk-score-bar {
            width: 100%;
            height: 3px;
            border-radius: 2px;
            background: #e2e8f0;
            overflow: hidden;
            margin-top: 0.1rem;
        }

        .risk-score-bar .fill {
            height: 100%;
            border-radius: 2px;
            transition: width 1s ease;
        }

        .risk-score-bar .fill.high {
            background: linear-gradient(135deg, var(--danger), #f87171);
        }

        .risk-score-bar .fill.medium {
            background: linear-gradient(135deg, var(--warning), #fbbf24);
        }

        .risk-score-bar .fill.low {
            background: linear-gradient(135deg, #059669, var(--success));
        }

        .factors-cell {
            display: flex;
            gap: 0.2rem;
            flex-wrap: wrap;
        }

        .risk-factor-pill {
            display: inline-block;
            padding: 0.05rem 0.4rem;
            border-radius: 1rem;
            font-size: 0.55rem;
            font-weight: 500;
            background: #f1f5f9;
            color: var(--text-gray);
            border: 1px solid transparent;
            transition: var(--transition);
            white-space: nowrap;
        }

        .risk-factor-pill:hover {
            border-color: var(--primary);
            background: rgba(10, 36, 99, 0.04);
        }

        .risk-factor-pill.critical {
            background: var(--danger-light);
            color: #991b1b;
            border-color: #fecaca;
        }

        .risk-factor-pill.warning {
            background: var(--warning-light);
            color: #92400e;
            border-color: #fde68a;
        }

        .btn-action {
            padding: 0.2rem 0.5rem;
            border-radius: 8px;
            font-size: 0.65rem;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }

        .btn-action.view {
            background: rgba(59, 130, 246, 0.08);
            color: var(--info);
        }

        .btn-action.view:hover {
            background: rgba(59, 130, 246, 0.15);
            transform: translateY(-1px);
        }

        .btn-action.intervene {
            background: rgba(10, 36, 99, 0.08);
            color: var(--primary);
        }

        .btn-action.intervene:hover {
            background: rgba(10, 36, 99, 0.15);
            transform: translateY(-1px);
        }

        .btn-action .btn-icon {
            font-size: 0.7rem;
        }

        .btn-action .btn-text {
            font-size: 0.6rem;
        }

        .btn-template {
            padding: 0.2rem 0.6rem;
            border-radius: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            background: var(--white);
            font-size: 0.65rem;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-dark);
            font-family: 'Inter', sans-serif;
        }

        .btn-template:hover {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .btn-template.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        #qmSendBtn {
            background: var(--primary);
            color: var(--white);
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            transition: var(--transition);
            border: none;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
        }

        #qmSendBtn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(10, 36, 99, 0.25);
        }

        #qmSendBtn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .toast-notification {
            background: var(--white);
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            border-left: 4px solid var(--primary);
            margin-bottom: 10px;
            animation: slideIn 0.3s ease;
            font-size: 0.9rem;
            max-width: 400px;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toast-notification.success {
            border-left-color: var(--success);
        }

        .toast-notification.error {
            border-left-color: var(--danger);
        }

        .toast-notification.warning {
            border-left-color: var(--warning);
        }

        .toast-notification .toast-icon {
            font-size: 1.2rem;
        }

        .toast-notification .toast-close {
            margin-left: auto;
            cursor: pointer;
            color: #94a3b8;
            font-size: 0.8rem;
            padding: 0 4px;
        }

        .toast-notification .toast-close:hover {
            color: var(--text-dark);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(50px);
            }
        }

        @media (max-width: 1200px) {
            .chart-grid-premium {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 992px) {
            .stats-grid-compact {
                grid-template-columns: 1fr 1fr;
            }

            .table-premium {
                font-size: 0.7rem;
                min-width: 900px;
            }
        }

        @media (max-width: 768px) {
            .filter-bar-premium {
                flex-direction: column;
                align-items: stretch;
                padding: 0.75rem 1rem;
            }

            .filter-bar-premium .filter-group {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar-premium .filter-group select {
                width: 100%;
                min-width: unset;
            }

            .filter-bar-premium .search-group {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-bar-premium .search-group input {
                width: 100%;
                min-width: unset;
            }

            .stats-grid-compact {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-card-compact {
                padding: 0.75rem 1rem;
            }

            .stat-card-compact .stat-number {
                font-size: 1.2rem;
            }

            .stat-card-compact .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .chart-container-premium {
                height: 200px;
            }

            .table-premium {
                font-size: 0.65rem;
                min-width: 800px;
            }

            .table-premium th,
            .table-premium td {
                padding: 0.3rem 0.4rem;
            }

            .btn-action .btn-text {
                display: none;
            }

            .btn-action {
                padding: 0.15rem 0.35rem;
                font-size: 0.6rem;
            }

            .btn-action .btn-icon {
                font-size: 0.65rem;
            }

            .factors-grid-premium {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .table-premium {
                min-width: 700px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid-compact {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-card-compact {
                padding: 0.5rem 0.75rem;
                gap: 0.5rem;
            }

            .stat-card-compact .stat-number {
                font-size: 1rem;
            }

            .stat-card-compact .stat-label {
                font-size: 0.5rem;
            }

            .stat-card-compact .stat-icon {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
                border-radius: 8px;
            }

            .factors-grid-premium {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.6s ease forwards;
        }

        .animate-in:nth-child(2) {
            animation-delay: 0.1s;
        }

        .animate-in:nth-child(3) {
            animation-delay: 0.2s;
        }

        .animate-in:nth-child(4) {
            animation-delay: 0.3s;
        }
    </style>

    {{-- Stats --}}
    <div class="stats-grid-compact">
        <div class="stat-card-compact animate-in">
            <div class="stat-icon primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['total_students'] ?? 0) }}</div>
                <div class="stat-label">👨‍🎓 Total Students</div>
            </div>
        </div>

        <div class="stat-card-compact animate-in">
            <div class="stat-icon success">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">
                    {{ number_format(($stats['total_students'] ?? 0) - ($stats['high_risk'] ?? 0) - ($stats['medium_risk'] ?? 0)) }}
                </div>
                <div class="stat-label">🟢 Low Risk</div>
            </div>
        </div>

        <div class="stat-card-compact animate-in">
            <div class="stat-icon warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['medium_risk'] ?? 0) }}</div>
                <div class="stat-label">🟡 Medium Risk</div>
            </div>
        </div>

        <div class="stat-card-compact animate-in">
            <div class="stat-icon danger">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['high_risk'] ?? 0) }}</div>
                <div class="stat-label">🔴 High Risk</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form class="filter-bar-premium" method="GET" action="{{ route('admin.risk.index') }}" id="filterForm">
        <div class="filter-group">
            <label><i class="bi bi-building"></i> Dept</label>
            <select name="department_id" id="departmentFilter">
                <option value="">All Departments</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label><i class="bi bi-calendar3"></i> Year</label>
            <select name="year" id="yearFilter">
                <option value="">All Years</option>
                @for ($i = 1; $i <= 6; $i++)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                        {{ $yearLabels[$i] ?? $i . 'th' }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="filter-group">
            <label><i class="bi bi-shield-exclamation"></i> Risk</label>
            <select name="risk_level" id="riskLevelFilter">
                <option value="">All Levels</option>
                <option value="Low" {{ $riskLevel == 'Low' ? 'selected' : '' }}>🟢 Low</option>
                <option value="Medium" {{ $riskLevel == 'Medium' ? 'selected' : '' }}>🟡 Medium</option>
                <option value="High" {{ $riskLevel == 'High' ? 'selected' : '' }}>🔴 High</option>
            </select>
        </div>

        <div class="search-group">
            <input type="text" id="searchInput" placeholder="🔍 Search students..." autocomplete="off">
        </div>

        <div class="btn-group" style="display:flex; gap:0.4rem; flex-wrap:wrap;">
            <button type="submit" class="btn-premium btn-premium-primary">
                <i class="bi bi-funnel"></i> Apply
            </button>
            <a href="{{ route('admin.risk.index') }}" class="btn-premium btn-premium-outline">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
            <a href="{{ route('admin.risk.export') }}" class="btn-premium btn-premium-success">
                <i class="bi bi-download"></i> Export
            </a>
        </div>
    </form>

    {{-- Charts --}}
    <div class="chart-grid-premium">
        <div class="chart-card-premium">
            <div class="card-header">
                <span class="title">
                    <i class="bi bi-pie-chart-fill" style="color:var(--primary);"></i>
                    Risk Distribution
                    <span class="badge">KG+12 Full Risk</span>
                </span>
                <span class="subtitle">Student risk level breakdown</span>
            </div>
            <div class="card-body">
                <div class="chart-container-premium">
                    <canvas id="riskDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-card-premium">
            <div class="card-header">
                <span class="title">
                    <i class="bi bi-graph-up-arrow" style="color:var(--primary);"></i>
                    Risk Trend Analysis
                    <span class="badge">6 Months</span>
                </span>
                <span class="subtitle">Historical risk pattern tracking</span>
            </div>
            <div class="card-body">
                <div class="chart-container-premium">
                    <canvas id="riskTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Risk Factors & Department Breakdown --}}
    <div class="chart-grid-premium">
        <div class="chart-card-premium">
            <div class="card-header">
                <span class="title">
                    <i class="bi bi-list-check" style="color:var(--primary);"></i>
                    Risk Indicators
                    <span class="badge">{{ count(array_filter($riskFactors)) }} active</span>
                </span>
                <span class="subtitle">Most common risk drivers</span>
            </div>
            <div class="card-body">
                @if (count(array_filter($riskFactors)) > 0)
                    <div class="factors-grid-premium">
                        @foreach ($riskFactors as $factor => $count)
                            @if ($count > 0)
                                <div class="factor-item-premium">
                                    <span class="factor-name" title="{{ $factor }}">
                                        @if (str_contains($factor, 'Attendance'))
                                            📉
                                        @elseif(str_contains($factor, 'Roll'))
                                            📝
                                        @elseif(str_contains($factor, 'consecutive'))
                                            🔄
                                        @elseif(str_contains($factor, 'Declining'))
                                            📊
                                        @else
                                            ⚠️
                                        @endif
                                        {{ $factor }}
                                    </span>
                                    <span class="factor-count">{{ $count }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:1.5rem; color:#94a3b8;">
                        <div style="font-size:2.5rem; margin-bottom:0.5rem;">✅</div>
                        <div style="font-weight:500; font-size:1rem;">No significant risk factors identified</div>
                        <div style="font-size:0.85rem;">All metrics are within acceptable ranges</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="chart-card-premium">
            <div class="card-header">
                <span class="title">
                    <i class="bi bi-building" style="color:var(--primary);"></i>
                    Department Risk Analysis
                    <span class="badge">{{ count($riskByDepartment) }} departments</span>
                </span>
                <span class="subtitle">Risk distribution by department</span>
            </div>
            <div class="card-body">
                @if (count($riskByDepartment) > 0)
                    <div class="dept-bars-premium">
                        {{-- FIXED: use $deptData instead of $deptName => $counts --}}
                        @foreach ($riskByDepartment as $deptData)
                            @php
                                $total = $deptData['total']; // pre-computed total
                                $highPct = $total > 0 ? round(($deptData['High'] / $total) * 100) : 0;
                                $medPct = $total > 0 ? round(($deptData['Medium'] / $total) * 100) : 0;
                                $lowPct = $total > 0 ? round(($deptData['Low'] / $total) * 100) : 0;
                            @endphp
                            <div class="dept-bar-item">
                                <div class="dept-header">
                                    <span class="dept-name"
                                        title="{{ $deptData['name'] }}">{{ $deptData['name'] }}</span>
                                    <span class="dept-counts">
                                        <span class="high">{{ $deptData['High'] }}H</span>
                                        <span class="medium">{{ $deptData['Medium'] }}M</span>
                                        <span class="low">{{ $deptData['Low'] }}L</span>
                                        <span
                                            style="font-weight:600; color:var(--text-dark);">({{ $total }})</span>
                                    </span>
                                </div>
                                <div class="dept-bar-track">
                                    @if ($highPct > 0)
                                        <div class="segment high" style="width:{{ $highPct }}%;"></div>
                                    @endif
                                    @if ($medPct > 0)
                                        <div class="segment medium" style="width:{{ $medPct }}%;"></div>
                                    @endif
                                    @if ($lowPct > 0)
                                        <div class="segment low" style="width:{{ $lowPct }}%;"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center; padding:1.5rem; color:#94a3b8;">
                        No department data available
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- At-Risk Students Table with KG+12 Roll Call --}}
    <div class="chart-card-premium" style="margin-bottom:2rem;">
        <div class="card-header">
            <span class="title">
                <i class="bi bi-person-exclamation" style="color:var(--primary);"></i>
                At-Risk Student Registry
                <span class="badge" id="rowCount">{{ count($riskData) }} identified</span>
            </span>
            <span class="subtitle">KG+12 Roll Call & Full Risk Score | Immediate intervention recommended</span>
        </div>
        <div class="card-body" style="padding:0;">
            @if (count($riskData) > 0)
                <div class="table-wrapper-premium">
                    <table class="table-premium" id="riskTable">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Department</th>
                                <th style="text-align:center;">Year</th>
                                <th style="text-align:center;">Attendance</th>
                                <th style="text-align:center;">Roll Call<br><small>/10</small></th>
                                <th style="text-align:center;">Consistency<br><small>6 max</small></th>
                                <th style="text-align:center;">Punctuality<br><small>2 max</small></th>
                                <th style="text-align:center;">Participation<br><small>2 max</small></th>
                                <th style="text-align:center;">Risk Level</th>
                                <th>Risk Factors</th>
                                <th style="text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="riskTableBody">
                            @foreach ($riskData as $data)
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:0.5rem;">
                                            <div
                                                style="width:30px; height:30px; border-radius:50%; background:linear-gradient(135deg, var(--primary), var(--primary-light)); color:var(--white); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.7rem; flex-shrink:0;">
                                                {{ Str::upper(substr($data['student']->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div
                                                    style="font-weight:600; color:var(--text-dark); font-size:0.78rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px;">
                                                    {{ $data['student']->name ?? 'Unknown' }}
                                                </div>
                                                <div
                                                    style="font-size:0.6rem; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:140px;">
                                                    {{ $data['student']->email ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="font-size:0.75rem; color:var(--text-gray);">
                                        {{ $data['student']->department->name ?? 'N/A' }}
                                    </td>
                                    <td style="text-align:center; font-size:0.7rem; color:var(--text-gray);">
                                        {{ $data['student']->current_year ?? 'N/A' }}
                                    </td>
                                    <td class="attendance-cell" style="text-align:center;">
                                        <div
                                            class="attendance-value {{ $data['attendance'] >= 70 ? 'high' : ($data['attendance'] >= 60 ? 'medium' : 'low') }}">
                                            {{ $data['attendance'] }}%
                                        </div>
                                    </td>
                                    <td class="roll-call-cell" style="text-align:center;">
                                        <span class="roll-value">{{ $data['roll_call'] ?? '0' }}</span>
                                    </td>
                                    <td style="text-align:center; font-size:0.7rem; color:var(--text-gray);">
                                        {{ $data['consistency'] ?? '0' }}
                                    </td>
                                    <td style="text-align:center; font-size:0.7rem; color:var(--text-gray);">
                                        {{ $data['punctuality'] ?? '0' }}
                                    </td>
                                    <td style="text-align:center; font-size:0.7rem; color:var(--text-gray);">
                                        {{ $data['participation'] ?? '0' }}
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="risk-badge-premium {{ strtolower($data['level']) }}">
                                            @if ($data['level'] == 'High')
                                                🚨
                                            @endif
                                            @if ($data['level'] == 'Medium')
                                                ⚠️
                                            @endif
                                            @if ($data['level'] == 'Low')
                                                ✅
                                            @endif
                                            {{ $data['level'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:0.2rem; flex-wrap:wrap;">
                                            @foreach ($data['factors'] as $factor)
                                                <span
                                                    class="risk-factor-pill {{ str_contains($factor, 'critical') || str_contains($factor, '3+') ? 'critical' : 'warning' }}">
                                                    {{ $factor }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex; gap:0.3rem; justify-content:center;">
                                            <a href="{{ route('admin.students.show', $data['student']->id) }}"
                                                class="btn-action view" title="View Student Profile">
                                                <span class="btn-icon">👤</span>
                                                <span class="btn-text">Profile</span>
                                            </a>
                                            <button class="btn-action intervene" title="Send Intervention Message"
                                                onclick="openQuickMessage('{{ $data['student']->id }}', '{{ addslashes($data['student']->name) }}', '{{ addslashes($data['student']->email) }}', '{{ $data['level'] }}', '{{ $data['score'] }}')">
                                                <span class="btn-icon">✉️</span>
                                                <span class="btn-text">Message</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align:center; padding:3rem 2rem;">
                    <div style="font-size:3.5rem; margin-bottom:1rem;">🎉</div>
                    <div style="color:var(--text-dark); font-weight:700; font-size:1.25rem;">No Students Currently At Risk
                    </div>
                    <div style="color:var(--text-gray); font-size:0.9rem; margin-top:0.25rem;">
                        All students have good attendance records and are performing well
                    </div>
                    <div
                        style="margin-top:1rem; padding:0.5rem 1.5rem; background:var(--success-light); border-radius:8px; display:inline-block; border:1px solid #a7f3d0;">
                        <span style="color:#166534; font-weight:500;">✅ Risk Rate: 0% — Excellent</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Message Modal --}}
    <div class="modal fade" id="quickMessageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:16px; border:none; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <div class="modal-header" style="border-bottom:1px solid #f1f5f9; padding:1.25rem 1.5rem;">
                    <h5 class="modal-title" style="font-weight:700; color:var(--text-dark);">
                        <span style="color:var(--primary);">✉️</span> Send Message
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding:1.5rem;">
                    <div id="quickMessageStudentInfo"
                        style="background:#f8fafc; padding:0.75rem 1rem; border-radius:10px; margin-bottom:1.25rem;">
                        <p style="margin:0; font-weight:600;">To: <span id="qmStudentName"
                                style="font-weight:400;"></span></p>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-gray);">
                            <span id="qmStudentEmail"></span>
                            <span style="margin:0 0.5rem;">•</span>
                            Risk: <span id="qmRiskLevel"></span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"
                            style="font-weight:600; font-size:0.85rem; color:var(--text-dark);">Subject</label>
                        <input type="text" id="qmSubject" class="form-control"
                            style="border-radius:8px; border:1px solid rgba(10,36,99,0.12); padding:0.6rem;"
                            placeholder="Enter subject...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label"
                            style="font-weight:600; font-size:0.85rem; color:var(--text-dark);">Message</label>
                        <textarea id="qmBody" class="form-control" rows="4"
                            style="border-radius:8px; border:1px solid rgba(10,36,99,0.12); padding:0.6rem; resize:vertical;"
                            placeholder="Type your message here..."></textarea>
                    </div>

                    <div class="mb-2">
                        <label style="font-weight:600; font-size:0.75rem; color:var(--text-gray);">Quick Templates</label>
                        <div style="display:flex; gap:0.4rem; flex-wrap:wrap; margin-top:0.3rem;">
                            <button type="button" class="btn-template" data-template="intervention">📋
                                Intervention</button>
                            <button type="button" class="btn-template" data-template="checkin">✅ Check-in</button>
                            <button type="button" class="btn-template" data-template="warning">⚠️ Warning</button>
                            <button type="button" class="btn-template" data-template="meeting">📅 Meeting</button>
                            <button type="button" class="btn-template" data-template="custom">✏️ Custom</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:1rem 1.5rem; gap:0.5rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        style="border-radius:8px; padding:0.5rem 1.5rem; border:1px solid rgba(10,36,99,0.12); background:var(--white); color:var(--text-gray);">
                        Cancel
                    </button>
                    <button type="button" class="btn" id="qmSendBtn">
                        📤 Send Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div id="toast-container"></div>

    {{-- JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Risk Distribution Chart
            const riskCounts = @json($riskCounts);
            const ctx1 = document.getElementById('riskDistributionChart').getContext('2d');

            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['🟢 Low Risk', '🟡 Medium Risk', '🔴 High Risk'],
                    datasets: [{
                        data: [riskCounts.Low, riskCounts.Medium, riskCounts.High],
                        backgroundColor: ['rgba(16,185,129,0.85)', 'rgba(245,158,11,0.85)',
                            'rgba(239,68,68,0.85)'
                        ],
                        borderColor: 'white',
                        borderWidth: 3,
                        hoverOffset: 12,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600'
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.9)',
                            titleFont: {
                                size: 13,
                                weight: '600'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return label.replace(/[🟢🟡🔴]\s*/, '') + ': ' + value +
                                        ' students (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '65%',
                    animation: {
                        animateRotate: true,
                        duration: 1500
                    }
                }
            });

            // Risk Trend Chart
            const riskTrend = @json($riskTrend);
            const ctx2 = document.getElementById('riskTrendChart').getContext('2d');

            if (riskTrend && riskTrend.length > 0) {
                const labels = riskTrend.map(item => item.month);
                const highRisk = riskTrend.map(item => item.high_risk);
                const mediumRisk = riskTrend.map(item => item.medium_risk);

                new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: '🔴 High Risk',
                                data: highRisk,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239,68,68,0.08)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#ef4444',
                                borderWidth: 3,
                            },
                            {
                                label: '🟡 Medium Risk',
                                data: mediumRisk,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245,158,11,0.08)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#f59e0b',
                                borderWidth: 3,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 11,
                                        weight: '500'
                                    },
                                    usePointStyle: true,
                                    pointStyle: 'line'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15,23,42,0.9)',
                                titleFont: {
                                    size: 13,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                padding: 12,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0,0,0,0.04)'
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    },
                                    stepSize: 1
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1500
                        }
                    }
                });
            }

            // Client-side filtering (updated for 11 columns)
            const departmentFilter = document.getElementById('departmentFilter');
            const yearFilter = document.getElementById('yearFilter');
            const riskLevelFilter = document.getElementById('riskLevelFilter');
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('riskTableBody');
            const rowCountBadge = document.getElementById('rowCount');

            if (tableBody) {
                const rows = tableBody.querySelectorAll('tr');
                const originalRows = Array.from(rows);

                function filterTable() {
                    const deptValue = departmentFilter ? departmentFilter.value : '';
                    const yearValue = yearFilter ? yearFilter.value : '';
                    const riskValue = riskLevelFilter ? riskLevelFilter.value : '';
                    const searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';

                    let visibleCount = 0;

                    originalRows.forEach(row => {
                        let show = true;
                        const cells = row.querySelectorAll('td');

                        // Expecting 11 columns (index 0..10)
                        if (cells.length >= 11) {
                            // Department: index 1
                            if (deptValue && deptValue !== '' && show) {
                                const deptText = cells[1] ? cells[1].textContent.trim().toLowerCase() : '';
                                const deptOption = departmentFilter.querySelector(
                                    `option[value="${deptValue}"]`);
                                const deptName = deptOption ? deptOption.textContent.trim().toLowerCase() :
                                    '';
                                if (!deptText.includes(deptName) && !deptName.includes(deptText)) {
                                    show = false;
                                }
                            }

                            // Year: index 2
                            if (yearValue && yearValue !== '' && show) {
                                const yearText = cells[2] ? cells[2].textContent.trim() : '';
                                if (yearText !== yearValue && !yearText.includes('All')) {
                                    show = false;
                                }
                            }

                            // Risk Level: index 8
                            if (riskValue && riskValue !== '' && show) {
                                const riskText = cells[8] ? cells[8].textContent.trim().toLowerCase() : '';
                                if (!riskText.includes(riskValue.toLowerCase())) {
                                    show = false;
                                }
                            }

                            // Search: all cells
                            if (searchValue && searchValue !== '' && show) {
                                let found = false;
                                cells.forEach(cell => {
                                    if (cell.textContent.toLowerCase().includes(searchValue)) {
                                        found = true;
                                    }
                                });
                                if (!found) {
                                    show = false;
                                }
                            }
                        } else {
                            // Fallback: if column count mismatch, show all
                            show = true;
                        }

                        row.style.display = show ? '' : 'none';
                        if (show) visibleCount++;
                    });

                    if (rowCountBadge) {
                        rowCountBadge.textContent = visibleCount + ' identified';
                    }
                }

                if (departmentFilter) departmentFilter.addEventListener('change', filterTable);
                if (yearFilter) yearFilter.addEventListener('change', filterTable);
                if (riskLevelFilter) riskLevelFilter.addEventListener('change', filterTable);
                if (searchInput) {
                    searchInput.addEventListener('input', filterTable);
                    searchInput.addEventListener('search', filterTable);
                }

                // Initial filter
                filterTable();
            }
        });

        // Quick Message Functions (unchanged)
        function openQuickMessage(studentId, studentName, studentEmail, riskLevel, riskScore) {
            document.getElementById('qmStudentName').textContent = studentName;
            document.getElementById('qmStudentEmail').textContent = studentEmail;

            const riskSpan = document.getElementById('qmRiskLevel');
            const colors = {
                'High': '#ef4444',
                'Medium': '#f59e0b',
                'Low': '#10b981'
            };
            riskSpan.textContent = riskLevel;
            riskSpan.style.color = colors[riskLevel] || '#64748b';

            const subjects = {
                'High': '🚨 URGENT: Academic Intervention Required',
                'Medium': '⚠️ Academic Support Needed',
                'Low': '📋 Academic Check-in'
            };
            document.getElementById('qmSubject').value = subjects[riskLevel] || 'Academic Support';

            const templates = {
                'High': `Dear ${studentName},\n\nThis is an urgent message regarding your academic progress. I need to meet with you as soon as possible to discuss your current situation and create a support plan.\n\nYour current risk score is ${riskScore} which requires immediate attention.\n\nPlease contact me immediately to schedule a meeting.\n\nBest regards,\nAcademic Support Team`,

                'Medium': `Dear ${studentName},\n\nI'm reaching out because our system has identified that you may need some additional academic support. Your current risk score is ${riskScore}.\n\nI'd like to schedule a meeting to discuss your progress and how we can help you succeed. Please let me know a convenient time to meet.\n\nBest regards,\nAcademic Support Team`,

                'Low': `Dear ${studentName},\n\nI hope you're doing well. I noticed you might benefit from some academic support. Your current risk score is ${riskScore}.\n\nPlease feel free to reach out if you need any assistance with your studies or if you'd like to schedule a meeting.\n\nBest regards,\nAcademic Support Team`
            };
            document.getElementById('qmBody').value = templates[riskLevel] || templates['Low'];

            document.getElementById('qmSendBtn').dataset.studentId = studentId;

            const sendBtn = document.getElementById('qmSendBtn');
            sendBtn.textContent = '📤 Send Message';
            sendBtn.style.background = '#0A2463';
            sendBtn.disabled = false;

            document.querySelectorAll('.btn-template').forEach(b => b.classList.remove('active'));

            const modal = new bootstrap.Modal(document.getElementById('quickMessageModal'));
            modal.show();
        }

        // Template buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-template').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.template;
                    const studentName = document.getElementById('qmStudentName').textContent;

                    const templates = {
                        intervention: `Dear ${studentName},\n\nI'm writing to offer academic support and intervention. I've noticed some areas where we can work together to improve your academic standing.\n\nPlease schedule a meeting with me at your earliest convenience so we can develop a plan together.\n\nBest regards,\nAcademic Support Team`,

                        checkin: `Dear ${studentName},\n\nJust checking in to see how things are going with your studies. I hope everything is going well. If you need any support or have questions, please don't hesitate to reach out.\n\nBest regards,\nAcademic Support Team`,

                        warning: `Dear ${studentName},\n\nThis is an important message regarding your academic progress. Your attendance and/or performance needs immediate attention.\n\nPlease meet with me as soon as possible to discuss this matter.\n\nBest regards,\nAcademic Support Team`,

                        meeting: `Dear ${studentName},\n\nI'd like to schedule a meeting to discuss your academic progress and performance.\n\nPlease suggest some times that work for you, and I'll confirm availability.\n\nBest regards,\nAcademic Support Team`,

                        custom: `Dear ${studentName},\n\n`
                    };

                    document.getElementById('qmBody').value = templates[type] || '';

                    document.querySelectorAll('.btn-template').forEach(b => b.classList.remove(
                        'active'));
                    this.classList.add('active');
                });
            });
        });

        // Send message
        document.addEventListener('DOMContentLoaded', function() {
            const sendBtn = document.getElementById('qmSendBtn');

            sendBtn.addEventListener('click', function() {
                const studentId = this.dataset.studentId;
                const subject = document.getElementById('qmSubject').value.trim();
                const body = document.getElementById('qmBody').value.trim();

                if (!body) {
                    showToast('⚠️ Please enter a message.', 'warning');
                    return;
                }

                this.textContent = '⏳ Sending...';
                this.disabled = true;
                this.style.background = '#6b7a8f';

                fetch('{{ route('admin.messages.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            recipient_id: studentId,
                            subject: subject || 'Academic Support',
                            message: body
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.textContent = '✅ Sent!';
                            this.style.background = '#10b981';

                            showToast('✅ Message sent successfully to ' + document.getElementById(
                                'qmStudentName').textContent, 'success');

                            setTimeout(() => {
                                const modal = bootstrap.Modal.getInstance(document
                                    .getElementById('quickMessageModal'));
                                modal.hide();
                                this.textContent = '📤 Send Message';
                                this.style.background = '#0A2463';
                                this.disabled = false;
                            }, 1500);
                        } else {
                            this.textContent = '❌ Failed';
                            this.style.background = '#ef4444';
                            showToast('❌ Failed to send message: ' + (data.message || 'Unknown error'),
                                'error');
                            setTimeout(() => {
                                this.textContent = '📤 Send Message';
                                this.style.background = '#0A2463';
                                this.disabled = false;
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.textContent = '❌ Error';
                        this.style.background = '#ef4444';
                        showToast('❌ Error sending message. Please try again.', 'error');
                        setTimeout(() => {
                            this.textContent = '📤 Send Message';
                            this.style.background = '#0A2463';
                            this.disabled = false;
                        }, 2000);
                    });
            });
        });

        // Toast Notification System
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };

            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            toast.innerHTML = `
                <span class="toast-icon">${icons[type] || 'ℹ️'}</span>
                <span>${message}</span>
                <span class="toast-close" onclick="this.parentElement.remove()">✕</span>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }

        // Reset modal on close
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('quickMessageModal');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function() {
                    document.querySelectorAll('.btn-template').forEach(b => b.classList.remove('active'));
                    const sendBtn = document.getElementById('qmSendBtn');
                    sendBtn.textContent = '📤 Send Message';
                    sendBtn.style.background = '#0A2463';
                    sendBtn.disabled = false;
                });
            }
        });
    </script>
@endsection
