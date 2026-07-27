@extends('layouts.app')

@section('title', 'Predictive Risk Analytics')
@section('page-title', 'Predictive Risk Analytics')
@section('welcome-text', 'Student success prediction & intervention system')

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

        .btn-reset-student {
            background: #f3f4f6;
            color: var(--text-dark);
            border: 1px solid rgba(10, 36, 99, 0.1);
            padding: 0.3rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-reset-student:hover {
            background: #e5e7eb;
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
            overflow: visible !important;
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
            overflow: visible !important;
        }

        .chart-container-premium {
            position: relative;
            height: 280px;
        }

        .risk-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .risk-summary-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-align: center;
        }

        .risk-summary-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }

        .risk-summary-card .risk-icon {
            font-size: 2rem;
            margin-bottom: 0.25rem;
        }

        .risk-summary-card .risk-count {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .risk-summary-card .risk-count.low {
            color: var(--success);
        }

        .risk-summary-card .risk-count.medium {
            color: var(--warning);
        }

        .risk-summary-card .risk-count.high {
            color: var(--danger);
        }

        .risk-summary-card .risk-label {
            font-size: 0.7rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .risk-summary-card .risk-percentage {
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .risk-summary-card .risk-status {
            display: inline-block;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .risk-summary-card .risk-status.low {
            background: var(--success-light);
            color: #166534;
        }

        .risk-summary-card .risk-status.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .risk-summary-card .risk-status.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        /* ===== RISK ALERTS TABS ===== */
        .risk-alerts-section {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .risk-alerts-section .alerts-header {
            padding: 0.75rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .risk-alerts-section .alerts-header .title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .risk-alerts-section .alerts-header .title .bell-icon {
            color: var(--danger);
        }

        .risk-alerts-section .alerts-header .title .course-badge {
            font-size: 0.6rem;
            font-weight: 400;
            color: var(--text-gray);
            margin-left: 0.5rem;
            background: #f1f5f9;
            padding: 0.05rem 0.6rem;
            border-radius: 1rem;
        }

        .alert-tabs {
            display: flex;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: #fafbfc;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .alert-tab {
            padding: 0.3rem 1rem;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-gray);
            background: transparent;
            border: none;
            transition: var(--transition);
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .alert-tab:hover {
            color: var(--text-dark);
            background: #f1f5f9;
        }

        .alert-tab.active {
            color: var(--primary);
            background: var(--white);
        }

        .alert-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary);
        }

        .alert-panel {
            display: none;
        }

        .alert-panel.active {
            display: block;
        }

        .alerts-body {
            padding: 0.5rem;
        }

        /* ===== ALERT ITEMS - Overall ===== */
        .alert-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            transition: var(--transition);
        }

        .alert-item:hover {
            background: #fafbfc;
        }

        .alert-item:last-child {
            border-bottom: none;
        }

        .alert-item .alert-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            color: white;
            flex-shrink: 0;
        }

        .alert-item .alert-avatar.high {
            background: var(--danger);
        }

        .alert-item .alert-avatar.medium {
            background: var(--warning);
        }

        .alert-item .alert-avatar.low {
            background: var(--success);
        }

        .alert-item .alert-info {
            flex: 1;
            min-width: 0;
        }

        .alert-item .alert-info .student-name {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
        }

        .alert-item .alert-info .student-detail {
            font-size: 0.7rem;
            color: var(--text-gray);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.3rem;
        }

        .alert-item .alert-info .student-detail .level-badge {
            display: inline-block;
            padding: 0.05rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .alert-item .alert-info .student-detail .level-badge.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .alert-item .alert-info .student-detail .level-badge.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .alert-item .alert-info .student-detail .level-badge.low {
            background: var(--success-light);
            color: #166534;
        }

        .alert-item .alert-info .alert-recommendation {
            font-size: 0.6rem;
            color: var(--text-gray);
            margin-top: 0.1rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .alert-item .alert-action {
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.65rem;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            font-weight: 500;
            background: rgba(10, 36, 99, 0.08);
            color: var(--primary);
            white-space: nowrap;
        }

        .alert-item .alert-action:hover {
            background: var(--primary);
            color: white;
        }

        /* ===== WEEKLY/MONTHLY ALERT ITEMS ===== */
        .alert-item-small {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.6rem 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.04);
            transition: var(--transition);
        }

        .alert-item-small:hover {
            background: #fafbfc;
        }

        .alert-item-small:last-child {
            border-bottom: none;
        }

        .alert-item-small .alert-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.7rem;
            color: white;
            flex-shrink: 0;
        }

        .alert-item-small .alert-avatar-sm.high {
            background: var(--danger);
        }

        .alert-item-small .alert-avatar-sm.medium {
            background: var(--warning);
        }

        .alert-item-small .alert-info-sm {
            flex: 1;
            min-width: 0;
        }

        .alert-item-small .alert-info-sm .student-name {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--text-dark);
        }

        .alert-item-small .alert-info-sm .student-detail {
            font-size: 0.65rem;
            color: var(--text-gray);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.3rem;
        }

        .alert-item-small .alert-risk-badge {
            display: inline-block;
            padding: 0.1rem 0.6rem;
            border-radius: 1rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .alert-item-small .alert-risk-badge.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .alert-item-small .alert-risk-badge.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .alert-item-small .alert-risk-badge.low {
            background: var(--success-light);
            color: #166534;
        }

        .alert-item-small .alert-period {
            font-size: 0.6rem;
            color: var(--text-gray);
            white-space: nowrap;
        }

        .alert-empty {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-gray);
        }

        .alert-empty .icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        /* ===== SEARCHABLE DROPDOWN ===== */
        .searchable-dropdown {
            position: relative;
            flex: 2;
            min-width: 200px;
            z-index: 9999;
        }

        .searchable-dropdown input {
            width: 100%;
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.12);
            background: #fff;
            font-size: 0.9rem;
            box-sizing: border-box;
        }

        .searchable-dropdown .dropdown-list {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid rgba(10, 36, 99, 0.15);
            border-radius: 8px;
            max-height: 240px;
            overflow-y: auto;
            z-index: 10000;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .searchable-dropdown .dropdown-list.show {
            display: block;
        }

        .searchable-dropdown .dropdown-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-size: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .searchable-dropdown .dropdown-item:hover {
            background: #f1f5f9;
        }

        .searchable-dropdown .dropdown-item.selected {
            background: #e2e8f0;
        }

        .searchable-dropdown .no-results {
            padding: 0.5rem 1rem;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .quick-action-wrapper {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .quick-action-wrapper .searchable-dropdown {
            flex: 2;
            min-width: 220px;
        }

        .btn-view-risk {
            background: #fef3c7;
            color: #92400e;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
        }

        .btn-view-risk:hover {
            background: #fde68a;
        }

        /* ===== MODAL STYLES ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: var(--white);
            border-radius: var(--radius);
            max-width: 750px;
            width: 100%;
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

        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafbfc;
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .modal-header h4 {
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .modal-header h4 .student-name {
            color: var(--primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--text-gray);
            cursor: pointer;
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

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            background: #fafbfc;
            border-radius: 0 0 var(--radius) var(--radius);
        }

        .btn-close-modal {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.4rem 1.2rem;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-close-modal:hover {
            background: #e5e7eb;
        }

        .risk-modal-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .risk-modal-stat {
            background: #fafbfc;
            padding: 0.75rem;
            border-radius: 8px;
            text-align: center;
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .risk-modal-stat .number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .risk-modal-stat .label {
            font-size: 0.6rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .risk-modal-stat .status-badge {
            display: inline-block;
            padding: 0.15rem 0.8rem;
            border-radius: 1rem;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .risk-modal-stat .status-badge.high {
            background: var(--danger-light);
            color: #991b1b;
        }

        .risk-modal-stat .status-badge.medium {
            background: var(--warning-light);
            color: #92400e;
        }

        .risk-modal-stat .status-badge.low {
            background: var(--success-light);
            color: #166534;
        }

        .modal-chart-container {
            position: relative;
            height: 200px;
            margin: 0.5rem 0 1rem 0;
        }

        .modal-monthly-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .modal-monthly-stat {
            background: #fafbfc;
            border-radius: 6px;
            padding: 0.4rem 0.5rem;
            text-align: center;
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .modal-monthly-stat .m-label {
            font-size: 0.55rem;
            color: var(--text-gray);
        }

        .modal-monthly-stat .m-status {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .modal-monthly-stat .m-status.high {
            color: var(--danger);
        }

        .modal-monthly-stat .m-status.medium {
            color: var(--warning);
        }

        .modal-monthly-stat .m-status.low {
            color: var(--success);
        }

        .loading-spinner {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border: 3px solid #f3f4f6;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spinner 0.8s linear infinite;
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
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

        @media (max-width: 1200px) {
            .chart-grid-premium {
                grid-template-columns: 1fr;
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

            .risk-summary-grid {
                grid-template-columns: 1fr 1fr;
            }

            .chart-grid-premium {
                grid-template-columns: 1fr;
            }

            .alert-item {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .alert-item-small {
                flex-wrap: wrap;
                gap: 0.3rem;
            }

            .alert-tabs {
                flex-wrap: wrap;
            }

            .alert-tab {
                font-size: 0.7rem;
                padding: 0.2rem 0.6rem;
            }

            .modal-monthly-stats {
                grid-template-columns: 1fr 1fr;
            }

            .risk-modal-stats {
                grid-template-columns: 1fr 1fr;
            }

            .modal-content {
                margin: 10px;
                max-height: 95vh;
            }

            .quick-action-wrapper .searchable-dropdown {
                flex: 1 1 100%;
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

            .risk-summary-grid {
                grid-template-columns: 1fr;
            }

            .modal-monthly-stats {
                grid-template-columns: 1fr;
            }

            .risk-modal-stats {
                grid-template-columns: 1fr 1fr;
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

        .animate-in:nth-child(1) {
            animation-delay: 0.03s;
        }

        .animate-in:nth-child(2) {
            animation-delay: 0.06s;
        }

        .animate-in:nth-child(3) {
            animation-delay: 0.09s;
        }

        .animate-in:nth-child(4) {
            animation-delay: 0.12s;
        }
    </style>

    {{-- Stats Cards --}}
    <div class="stats-grid-compact">
        <div class="stat-card-compact animate-in">
            <div class="stat-icon primary"><i class="bi bi-people-fill"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['total_students'] ?? 0) }}</div>
                <div class="stat-label">Total Students</div>
            </div>
        </div>
        <div class="stat-card-compact animate-in">
            <div class="stat-icon success"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['low_risk'] ?? 0) }}</div>
                <div class="stat-label">Low Risk</div>
            </div>
        </div>
        <div class="stat-card-compact animate-in">
            <div class="stat-icon warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['medium_risk'] ?? 0) }}</div>
                <div class="stat-label">Medium Risk</div>
            </div>
        </div>
        <div class="stat-card-compact animate-in">
            <div class="stat-icon danger"><i class="bi bi-exclamation-octagon-fill"></i></div>
            <div class="stat-info">
                <div class="stat-number">{{ number_format($stats['high_risk'] ?? 0) }}</div>
                <div class="stat-label">High Risk</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar with Course Filter --}}
    <form class="filter-bar-premium" method="GET" action="{{ route('admin.risk.index') }}">
        <div class="filter-group">
            <label><i class="bi bi-building"></i> Department</label>
            <select name="department" id="departmentFilter" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Course Filter --}}
        <div class="filter-group">
            <label><i class="bi bi-book"></i> Course</label>
            <select name="course_id" id="courseFilter" onchange="this.form.submit()">
                <option value="">All Courses</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ $courseId == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }} - {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <label><i class="bi bi-calendar3"></i> Year</label>
            <select name="year">
                <option value="">All</option>
                @for ($i = 1; $i <= 6; $i++)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>
                        {{ $yearLabels[$i] ?? $i . 'th' }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="filter-group">
            <label><i class="bi bi-shield-exclamation"></i> Risk</label>
            <select name="risk_level">
                <option value="">All</option>
                <option value="Low" {{ $riskLevel == 'Low' ? 'selected' : '' }}>Low</option>
                <option value="Medium" {{ $riskLevel == 'Medium' ? 'selected' : '' }}>Medium</option>
                <option value="High" {{ $riskLevel == 'High' ? 'selected' : '' }}>High</option>
            </select>
        </div>

        <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
            <button type="submit" class="btn-premium btn-premium-primary"><i class="bi bi-funnel"></i> Apply</button>
            <a href="{{ route('admin.risk.index') }}" class="btn-premium btn-premium-outline"><i
                    class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </div>
    </form>

    {{-- Student Quick Actions with Reset Button --}}
    <div class="chart-card-premium" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span class="title">
                <i class="bi bi-person" style="color:var(--primary);"></i>
                View each student's risk level
                @if ($courseId)
                    <span class="course-badge">
                        <i class="bi bi-book"></i>
                        {{ $courses->firstWhere('id', $courseId)->course_code ?? 'Selected Course' }}
                    </span>
                @endif
            </span>
        </div>
        <div class="card-body">
            <div class="quick-action-wrapper">
                <div class="searchable-dropdown" id="riskStudentDropdown">
                    <input type="text" id="riskSearchInput" placeholder="Search by name or ID..." autocomplete="off">
                    <div class="dropdown-list" id="riskDropdownList">
                        @foreach ($students as $s)
                            <div class="dropdown-item" data-id="{{ $s->id }}"
                                data-label="{{ $s->name }} ({{ $s->student_id ?? 'N/A' }})">
                                {{ $s->name }} ({{ $s->student_id ?? 'N/A' }})
                            </div>
                        @endforeach
                        <div class="no-results" style="display:none;">No students found</div>
                    </div>
                </div>
                <button class="btn-view-risk" onclick="viewQuickRisk()">
                    <i class="bi bi-shield-exclamation"></i> View Risk
                </button>
                {{-- Reset Button --}}
                <button class="btn-reset-student" onclick="resetStudentSelection()">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="chart-grid-premium">
        <div class="chart-card-premium">
            <div class="card-header">
                <span class="title">
                    <i class="bi bi-pie-chart-fill" style="color:var(--primary);"></i>
                    Risk Distribution
                    <span class="badge">
                        @if ($courseId)
                            {{ $courses->firstWhere('id', $courseId)->course_code ?? 'Current' }}
                        @else
                            Current snapshot
                        @endif
                    </span>
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

    {{-- ===== RISK ALERTS (Red bell icon, no attendance %, no helper text) ===== --}}
    <div class="risk-alerts-section">
        <div class="alerts-header">
            <span class="title">
                <i class="bi bi-bell-fill bell-icon"></i>
                Risk Alerts
                @if ($courseId)
                    <span class="course-badge">
                        <i class="bi bi-book"></i> {{ $courses->firstWhere('id', $courseId)->course_code ?? '' }}
                    </span>
                @endif
            </span>
            <span style="font-size:0.65rem; color:var(--text-gray);">
                <i class="bi bi-info-circle"></i>
                <span id="alertTabLabel">Current risk status</span>
            </span>
        </div>

        {{-- Tabs --}}
        <div class="alert-tabs">
            <button class="alert-tab active" data-tab="overall" onclick="switchAlertTab('overall')">
                <i class="bi bi-bar-chart-fill"></i> Overall
            </button>
            <button class="alert-tab" data-tab="weekly" onclick="switchAlertTab('weekly')">
                <i class="bi bi-calendar-week"></i> Weekly
            </button>
            <button class="alert-tab" data-tab="monthly" onclick="switchAlertTab('monthly')">
                <i class="bi bi-calendar-month"></i> Monthly
            </button>
        </div>

        {{-- Panel: Overall (No attendance %) --}}
        <div class="alert-panel active" id="panel-overall">
            <div class="alerts-body">
                @if (count($overallAlerts) > 0)
                    @foreach ($overallAlerts as $alert)
                        <div class="alert-item">
                            <div class="alert-avatar {{ strtolower($alert['level']) }}">
                                {{ Str::upper(substr($alert['student']->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="alert-info">
                                <div class="student-name">{{ $alert['student']->name ?? 'Unknown' }}</div>
                                <div class="student-detail">
                                    <span>{{ $alert['student']->student_id ?? 'N/A' }}</span>
                                    <span>•</span>
                                    <span>{{ $alert['student']->department->name ?? 'N/A' }}</span>
                                    @if ($courseId && isset($alert['course']))
                                        <span>•</span>
                                        <span>{{ $alert['course']->course_code ?? '' }}</span>
                                    @endif
                                    <span
                                        class="level-badge {{ strtolower($alert['level']) }}">{{ $alert['level'] }}</span>
                                </div>
                                <div class="alert-recommendation">
                                    @if ($alert['level'] == 'High')
                                        <i class="bi bi-exclamation-octagon-fill" style="color:var(--danger);"></i>
                                        Immediate meeting required. Contact student urgently.
                                    @else
                                        <i class="bi bi-chat-dots-fill" style="color:var(--warning);"></i>
                                        Schedule a meeting to discuss attendance improvement.
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.students.show', $alert['student']->id) }}" class="alert-action"
                                title="View Profile">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <button class="alert-action"
                                onclick="openQuickMessage('{{ $alert['student']->id }}', '{{ addslashes($alert['student']->name) }}', '{{ addslashes($alert['student']->email) }}', '{{ $alert['level'] }}', 0)"
                                title="Send Message">
                                <i class="bi bi-envelope"></i> Message
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="alert-empty">
                        <div class="icon"><i class="bi bi-check-circle-fill"
                                style="font-size:2rem; color:var(--success);"></i></div>
                        <div style="font-weight:600; color:var(--success);">No at-risk students</div>
                        <div style="font-size:0.85rem;">All students are on track.</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Panel: Weekly (No helper text) --}}
        <div class="alert-panel" id="panel-weekly">
            <div class="alerts-body" style="padding:0.5rem;">
                @if (count($weeklyAlerts) > 0)
                    @foreach ($weeklyAlerts as $alert)
                        <div class="alert-item-small">
                            <div class="alert-avatar-sm {{ strtolower($alert['current_level']) }}">
                                {{ Str::upper(substr($alert['student']->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="alert-info-sm">
                                <div class="student-name">{{ $alert['student']->name ?? 'Unknown' }}</div>
                                <div class="student-detail">
                                    <span>{{ $alert['student']->student_id ?? 'N/A' }}</span>
                                    <span>•</span>
                                    <span>{{ $alert['student']->department->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <span class="alert-risk-badge {{ strtolower($alert['current_level']) }}">
                                {{ $alert['current_level'] }}
                            </span>
                            <div class="alert-period">{{ $alert['period_label'] }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="alert-empty">
                        <div class="icon"><i class="bi bi-check-circle-fill"
                                style="font-size:2rem; color:var(--success);"></i></div>
                        <div style="font-weight:600; color:var(--success);">No at-risk students this week</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Panel: Monthly (No helper text) --}}
        <div class="alert-panel" id="panel-monthly">
            <div class="alerts-body" style="padding:0.5rem;">
                @if (count($monthlyAlerts) > 0)
                    @foreach ($monthlyAlerts as $alert)
                        <div class="alert-item-small">
                            <div class="alert-avatar-sm {{ strtolower($alert['current_level']) }}">
                                {{ Str::upper(substr($alert['student']->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="alert-info-sm">
                                <div class="student-name">{{ $alert['student']->name ?? 'Unknown' }}</div>
                                <div class="student-detail">
                                    <span>{{ $alert['student']->student_id ?? 'N/A' }}</span>
                                    <span>•</span>
                                    <span>{{ $alert['student']->department->name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <span class="alert-risk-badge {{ strtolower($alert['current_level']) }}">
                                {{ $alert['current_level'] }}
                            </span>
                            <div class="alert-period">{{ $alert['period_label'] }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="alert-empty">
                        <div class="icon"><i class="bi bi-check-circle-fill"
                                style="font-size:2rem; color:var(--success);"></i></div>
                        <div style="font-weight:600; color:var(--success);">No at-risk students this month</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- RISK MODAL -->
    <!-- ============================================================ -->
    <div class="modal-overlay" id="riskModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i class="bi bi-shield-exclamation" style="color:var(--warning);"></i>
                    Risk Analysis: <span class="student-name" id="riskStudentName">Student</span>
                    <span style="font-size:0.7rem; font-weight:400; color:var(--text-gray);" id="riskStudentId"></span>
                </h4>
                <button class="modal-close" onclick="closeModal('riskModal')">&times;</button>
            </div>
            <div class="modal-body" id="riskModalBody">
                <div class="text-center" style="padding:2rem;">
                    <div class="loading-spinner"></div>
                    <p style="margin-top:0.5rem; color:var(--text-gray);">Loading risk data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-close-modal" onclick="closeModal('riskModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- QUICK MESSAGE MODAL -->
    <!-- ============================================================ -->
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
                            placeholder="Type your message here..." required></textarea>
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
                        style="border-radius:8px; padding:0.5rem 1.5rem; border:1px solid rgba(10,36,99,0.12); background:var(--white); color:var(--text-gray);">Cancel</button>
                    <button type="button" class="btn" id="qmSendBtn">📤 Send Message</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div id="toast-container"></div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // ====== SEARCHABLE DROPDOWN ======
                const input = document.getElementById('riskSearchInput');
                const list = document.getElementById('riskDropdownList');
                const items = list.querySelectorAll('.dropdown-item');
                const noResults = list.querySelector('.no-results');
                let selectedId = null;

                function filterItems(query) {
                    const q = query.toLowerCase().trim();
                    let visible = 0;
                    items.forEach(item => {
                        const label = item.dataset.label.toLowerCase();
                        const match = label.indexOf(q) > -1;
                        item.style.display = match ? '' : 'none';
                        if (match) visible++;
                    });
                    noResults.style.display = visible === 0 ? '' : 'none';
                    list.classList.add('show');
                }

                input.addEventListener('input', function() {
                    const val = this.value;
                    if (val.length === 0) {
                        items.forEach(item => item.style.display = '');
                        noResults.style.display = 'none';
                        list.classList.remove('show');
                        selectedId = null;
                        return;
                    }
                    filterItems(val);
                });

                items.forEach(item => {
                    item.addEventListener('click', function() {
                        const id = this.dataset.id;
                        const label = this.dataset.label;
                        input.value = label;
                        selectedId = id;
                        list.classList.remove('show');
                        items.forEach(i => i.classList.remove('selected'));
                        this.classList.add('selected');
                    });
                });

                input.addEventListener('blur', function() {
                    setTimeout(() => list.classList.remove('show'), 200);
                });

                input.addEventListener('focus', function() {
                    if (this.value.length > 0) filterItems(this.value);
                });

                window.getRiskSelectedStudentId = function() {
                    if (selectedId) return selectedId;
                    const val = input.value.trim();
                    if (val) {
                        for (let item of items) {
                            if (item.dataset.label === val) return item.dataset.id;
                        }
                    }
                    return null;
                };

                // ====== Reset Student Selection ======
                window.resetStudentSelection = function() {
                    input.value = '';
                    selectedId = null;
                    items.forEach(i => i.classList.remove('selected'));
                    list.classList.remove('show');
                };

                // ====== Quick Risk Action ======
                window.viewQuickRisk = function() {
                    const id = window.getRiskSelectedStudentId();
                    if (!id) {
                        alert('Please select a student first.');
                        return;
                    }
                    const label = input.value.trim();
                    const match = label.match(/\(([^)]+)\)/);
                    const studentIdNumber = match ? match[1] : 'N/A';
                    const studentName = label.replace(/\([^)]*\)/, '').trim() || 'Student';
                    openRiskModal(id, studentName, studentIdNumber);
                };

                // ====== Modal functions ======
                function closeModal(id) {
                    document.getElementById(id).classList.remove('show');
                }
                window.closeModal = closeModal;

                document.querySelectorAll('.modal-overlay').forEach(overlay => {
                    overlay.addEventListener('click', function(e) {
                        if (e.target === this) this.classList.remove('show');
                    });
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove(
                            'show'));
                    }
                });

                let riskModalChart = null;

                function openRiskModal(studentId, studentName, studentIdNumber) {
                    const modal = document.getElementById('riskModal');
                    const body = document.getElementById('riskModalBody');

                    document.getElementById('riskStudentName').textContent = studentName;
                    document.getElementById('riskStudentId').textContent = '(' + studentIdNumber + ')';

                    modal.classList.add('show');

                    body.innerHTML = `
                        <div class="text-center" style="padding:2rem;">
                            <div class="loading-spinner"></div>
                            <p style="margin-top:0.5rem; color:var(--text-gray);">Loading risk data...</p>
                        </div>
                    `;

                    fetch(`/admin/risk/student-risk/${studentId}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network error');
                            return response.json();
                        })
                        .then(data => {
                            if (!data.success) throw new Error(data.message || 'Failed to load');

                            let html = '';

                            const riskLevels = data.weekly.map(w => w.risk_level);
                            const levelCounts = {};
                            riskLevels.forEach(l => {
                                levelCounts[l] = (levelCounts[l] || 0) + 1;
                            });
                            let overallRisk = 'Low';
                            let maxCount = 0;
                            for (const [level, count] of Object.entries(levelCounts)) {
                                if (count > maxCount) {
                                    maxCount = count;
                                    overallRisk = level;
                                }
                            }
                            const riskClass = overallRisk.toLowerCase();

                            html += `
                            <div class="risk-modal-stats">
                                <div class="risk-modal-stat">
                                    <div class="number">${data.weekly.length}</div>
                                    <div class="label">Weeks Tracked</div>
                                </div>
                                <div class="risk-modal-stat">
                                    <div class="number">${data.monthly ? data.monthly.length : 0}</div>
                                    <div class="label">Months Tracked</div>
                                </div>
                                <div class="risk-modal-stat">
                                    <div>
                                        <span class="status-badge ${riskClass}">${overallRisk}</span>
                                    </div>
                                    <div class="label">Overall Risk Level</div>
                                </div>
                            </div>

                            <div style="margin-bottom:0.5rem;">
                                <strong style="font-size:0.8rem; color:var(--text-dark);">Monthly Risk Summary</strong>
                            </div>
                            <div class="modal-monthly-stats">`;

                            if (data.monthly && data.monthly.length > 0) {
                                data.monthly.forEach(m => {
                                    const mClass = m.risk_level.toLowerCase();
                                    html += `
                                    <div class="modal-monthly-stat">
                                        <div class="m-label">${m.label}</div>
                                        <div class="m-status ${mClass}">${m.risk_level}</div>
                                    </div>
                                `;
                                });
                            } else {
                                html +=
                                    `<div class="modal-monthly-stat" style="grid-column:1/-1; text-align:center; color:var(--text-gray);">No monthly data</div>`;
                            }

                            html += `
                            </div>
                            <div class="modal-chart-container">
                                <canvas id="riskModalChart"></canvas>
                            </div>`;

                            body.innerHTML = html;

                            if (riskModalChart) {
                                riskModalChart.destroy();
                                riskModalChart = null;
                            }
                            const ctx = document.getElementById('riskModalChart');
                            if (ctx && data.weekly && data.weekly.length > 0) {
                                const colors = data.weekly.map(w => {
                                    if (w.risk_level === 'High') return '#ef4444';
                                    if (w.risk_level === 'Medium') return '#f59e0b';
                                    return '#10b981';
                                });
                                const levelValues = data.weekly.map(w => {
                                    if (w.risk_level === 'High') return 100;
                                    if (w.risk_level === 'Medium') return 50;
                                    return 0;
                                });
                                riskModalChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: data.weekly.map(w => w.label),
                                        datasets: [{
                                            label: 'Risk Level',
                                            data: levelValues,
                                            borderColor: '#0A2463',
                                            backgroundColor: 'rgba(10,36,99,0.08)',
                                            borderWidth: 2.5,
                                            fill: true,
                                            tension: 0.3,
                                            pointBackgroundColor: colors,
                                            pointBorderColor: 'white',
                                            pointBorderWidth: 2,
                                            pointRadius: 5,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: {
                                                display: false
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        const value = context.parsed.y;
                                                        const level = value >= 75 ? 'High' : (value >=
                                                            25 ? 'Medium' : 'Low');
                                                        return 'Risk Level: ' + level;
                                                    }
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                ticks: {
                                                    font: {
                                                        size: 9
                                                    },
                                                    stepSize: 25,
                                                    callback: function(value) {
                                                        if (value === 0) return 'Low';
                                                        if (value === 50) return 'Medium';
                                                        if (value === 100) return 'High';
                                                        return '';
                                                    }
                                                },
                                                grid: {
                                                    color: 'rgba(0,0,0,0.05)'
                                                }
                                            },
                                            x: {
                                                grid: {
                                                    display: false
                                                },
                                                ticks: {
                                                    font: {
                                                        size: 9
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            body.innerHTML = `
                            <div style="text-align:center; padding:2rem; color:var(--danger);">
                                <i class="bi bi-exclamation-triangle" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
                                <p>${error.message || 'Failed to load risk data'}</p>
                                <button class="btn-close-modal" onclick="closeModal('riskModal')">Close</button>
                            </div>
                        `;
                        });
                }

                // ====== Quick Message Functions ======
                window.openQuickMessage = function(studentId, studentName, studentEmail, riskLevel, riskScore) {
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
                        'High': 'URGENT: Academic Intervention Required',
                        'Medium': 'Academic Support Needed',
                        'Low': 'Academic Check-in'
                    };
                    document.getElementById('qmSubject').value = subjects[riskLevel] || 'Academic Support';

                    const templates = {
                        'High': `Dear ${studentName},\n\nThis is an urgent message regarding your academic progress. I need to meet with you as soon as possible to discuss your current situation and create a support plan.\n\nYour current risk level is ${riskLevel} which requires immediate attention.\n\nPlease contact me immediately to schedule a meeting.\n\nBest regards,\nAcademic Support Team`,
                        'Medium': `Dear ${studentName},\n\nI'm reaching out because our system has identified that you may need some additional academic support. Your current risk level is ${riskLevel}.\n\nI'd like to schedule a meeting to discuss your progress and how we can help you succeed. Please let me know a convenient time to meet.\n\nBest regards,\nAcademic Support Team`,
                        'Low': `Dear ${studentName},\n\nI hope you're doing well. I noticed you might benefit from some academic support. Your current risk level is ${riskLevel}.\n\nPlease feel free to reach out if you need any assistance with your studies or if you'd like to schedule a meeting.\n\nBest regards,\nAcademic Support Team`
                    };
                    document.getElementById('qmBody').value = templates[riskLevel] || templates['Low'];

                    document.getElementById('qmSendBtn').dataset.studentId = studentId;
                    const sendBtn = document.getElementById('qmSendBtn');
                    sendBtn.textContent = 'Send Message';
                    sendBtn.style.background = '#0A2463';
                    sendBtn.disabled = false;

                    document.querySelectorAll('.btn-template').forEach(b => b.classList.remove('active'));

                    const modal = new bootstrap.Modal(document.getElementById('quickMessageModal'));
                    modal.show();
                };

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

                const sendBtn = document.getElementById('qmSendBtn');
                if (sendBtn) {
                    sendBtn.addEventListener('click', function() {
                        const studentId = this.dataset.studentId;
                        const subject = document.getElementById('qmSubject').value.trim();
                        const body = document.getElementById('qmBody').value.trim();

                        if (!body) {
                            showToast('Please enter a message.', 'warning');
                            return;
                        }

                        this.textContent = 'Sending...';
                        this.disabled = true;
                        this.style.background = '#6b7a8f';

                        fetch('{{ route('admin.messages.send') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
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
                                    this.textContent = 'Sent!';
                                    this.style.background = '#10b981';
                                    showToast('Message sent to ' + document.getElementById('qmStudentName')
                                        .textContent, 'success');
                                    setTimeout(() => {
                                        const modal = bootstrap.Modal.getInstance(document
                                            .getElementById('quickMessageModal'));
                                        modal.hide();
                                        this.textContent = 'Send Message';
                                        this.style.background = '#0A2463';
                                        this.disabled = false;
                                    }, 1500);
                                } else {
                                    this.textContent = 'Failed';
                                    this.style.background = '#ef4444';
                                    showToast('Failed: ' + (data.message || 'Unknown error'), 'error');
                                    setTimeout(() => {
                                        this.textContent = 'Send Message';
                                        this.style.background = '#0A2463';
                                        this.disabled = false;
                                    }, 2000);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                this.textContent = 'Error';
                                this.style.background = '#ef4444';
                                showToast('Network error. Please try again.', 'error');
                                setTimeout(() => {
                                    this.textContent = 'Send Message';
                                    this.style.background = '#0A2463';
                                    this.disabled = false;
                                }, 2000);
                            });
                    });
                }

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

                // ====== Tab Switcher ======
                window.switchAlertTab = function(tab) {
                    document.querySelectorAll('.alert-tab').forEach(t => t.classList.remove('active'));
                    document.querySelector(`.alert-tab[data-tab="${tab}"]`).classList.add('active');

                    document.querySelectorAll('.alert-panel').forEach(p => p.classList.remove('active'));
                    document.getElementById(`panel-${tab}`).classList.add('active');

                    const label = document.getElementById('alertTabLabel');
                    const labels = {
                        'overall': 'Current risk status',
                        'weekly': 'Weekly risk levels (current week)',
                        'monthly': 'Monthly risk levels (current month)'
                    };
                    label.textContent = labels[tab] || 'Current risk status';
                };

                // ====== Charts: Risk Distribution ======
                const riskCounts = @json($riskCounts);
                const ctx1 = document.getElementById('riskDistributionChart');
                if (ctx1) {
                    new Chart(ctx1, {
                        type: 'doughnut',
                        data: {
                            labels: ['Low Risk', 'Medium Risk', 'High Risk'],
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
                                            let percentage = total > 0 ? Math.round((value / total) * 100) :
                                                0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
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
                }

                // ====== Risk Trend Chart ======
                const riskTrend = @json($riskTrend);
                const ctx2 = document.getElementById('riskTrendChart');
                if (ctx2 && riskTrend && riskTrend.length > 0) {
                    new Chart(ctx2, {
                        type: 'line',
                        data: {
                            labels: riskTrend.map(item => item.month),
                            datasets: [{
                                    label: 'High Risk',
                                    data: riskTrend.map(item => item.high_risk),
                                    borderColor: '#ef4444',
                                    backgroundColor: 'rgba(239,68,68,0.08)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#ef4444',
                                    borderWidth: 3,
                                },
                                {
                                    label: 'Medium Risk',
                                    data: riskTrend.map(item => item.medium_risk),
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
            });
        </script>
    @endpush
@endsection
