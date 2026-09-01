@extends('layouts.app')

@section('title', 'Semester QR Management')
@section('role', 'Lecturer')
@section('page-title', 'Semester QR Management')
@section('welcome-text', 'Create and manage static QR codes for the entire semester')

@section('sidebar')
    @include('layouts.partials.lecturer-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --primary-gradient: linear-gradient(135deg, #0A2463 0%, #1E3A8A 100%);
            --success: #10b981;
            --success-light: #d1fae5;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --info: #3b82f6;
            --info-light: #dbeafe;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-lg: 0 10px 40px rgba(10, 36, 99, 0.12);
            --radius: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 0 0 4px 4px;
        }

        .stat-card.total::before {
            background: var(--primary-gradient);
        }

        .stat-card.active::before {
            background: var(--success);
        }

        .stat-card.ended::before {
            background: var(--gray-400);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(10, 36, 99, 0.12);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-icon.total {
            background: rgba(10, 36, 99, 0.08);
            color: var(--primary);
        }

        .stat-icon.active {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success);
        }

        .stat-icon.ended {
            background: rgba(148, 163, 184, 0.12);
            color: var(--gray-500);
        }

        .stat-info .number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .stat-info .number.active {
            color: var(--success);
        }

        .stat-info .number.ended {
            color: var(--gray-500);
        }

        .stat-info .label {
            font-size: 0.7rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .create-form-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1.75rem 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .create-form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .create-form-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .form-header .title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-header .title h5 {
            margin: 0;
            font-weight: 700;
            color: var(--gray-900);
            font-size: 1.1rem;
        }

        .form-header .title .badge-new {
            background: var(--primary-gradient);
            color: var(--white);
            padding: 0.15rem 0.7rem;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-header .title .badge-no-expiry {
            background: rgba(16, 185, 129, 0.12);
            color: var(--success);
            padding: 0.15rem 0.7rem;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 700;
        }

        .form-header .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            background: var(--gray-100);
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .form-header .btn-back:hover {
            background: var(--gray-200);
            color: var(--gray-800);
            transform: translateX(-2px);
        }

        .form-body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .form-group label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .form-group label .required {
            color: var(--danger);
        }

        .form-group select,
        .form-group input {
            padding: 0.6rem 0.9rem;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.85rem;
            background: var(--gray-50);
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            color: var(--gray-800);
            width: 100%;
        }

        .form-group select:focus,
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(10, 36, 99, 0.06);
        }

        .form-actions {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid var(--gray-100);
        }

        .form-actions .help-text {
            font-size: 0.75rem;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-actions .help-text i {
            color: var(--info);
            font-size: 1rem;
        }

        .form-actions .help-text strong {
            color: var(--gray-700);
        }

        .btn-create-semester {
            background: var(--primary-gradient);
            color: var(--white);
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.2);
            white-space: nowrap;
        }

        .btn-create-semester:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(10, 36, 99, 0.3);
            color: var(--white);
        }

        .btn-create-semester:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .qr-cards-section {
            margin-top: 0.5rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .section-header h5 {
            font-weight: 700;
            color: var(--gray-900);
            font-size: 1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header h5 .count-badge {
            background: var(--gray-100);
            color: var(--gray-600);
            padding: 0.05rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .section-header .filter-tabs {
            display: flex;
            gap: 0.25rem;
            background: var(--gray-100);
            padding: 0.25rem;
            border-radius: 10px;
        }

        .section-header .filter-tabs .tab-btn {
            padding: 0.25rem 1rem;
            border-radius: 8px;
            border: none;
            background: transparent;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-500);
            cursor: pointer;
            transition: var(--transition);
        }

        .section-header .filter-tabs .tab-btn.active {
            background: var(--white);
            color: var(--gray-900);
            box-shadow: var(--shadow-sm);
        }

        .qr-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(10, 36, 99, 0.06);
            overflow: hidden;
            margin-bottom: 1.25rem;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .qr-card:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .qr-card-header {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .qr-card-header.active {
            background: var(--success-light);
        }

        .qr-card-header.ended {
            background: var(--gray-50);
        }

        .qr-card-header .left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .qr-card-header .left .course-name {
            font-weight: 700;
            color: var(--gray-900);
            font-size: 1rem;
        }

        .qr-card-header .left .course-code {
            font-size: 0.8rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .qr-card-header .left .badge-semester {
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.1rem 0.6rem;
            border-radius: 12px;
            background: #dbeafe;
            color: #1e40af;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
        }

        .qr-card-header .left .badge-no-expiry {
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.1rem 0.6rem;
            border-radius: 12px;
            background: var(--success-light);
            color: #166534;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
        }

        .qr-card-header .status-badge {
            padding: 0.2rem 1rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .qr-card-header .status-badge.active {
            background: var(--success-light);
            color: #166534;
        }

        .qr-card-header .status-badge.active::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse-dot 2s infinite;
        }

        .qr-card-header .status-badge.ended {
            background: var(--gray-200);
            color: var(--gray-600);
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .qr-card-body {
            padding: 1.25rem 1.5rem;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 1.5rem;
            align-items: start;
        }

        .qr-card-body .qr-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
        }

        .qr-card-body .qr-preview img {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            background: var(--white);
            padding: 0.5rem;
            border: 1px solid var(--gray-200);
        }

        .qr-card-body .qr-preview .qr-actions-small {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .qr-card-body .qr-preview .qr-actions-small .btn-sm-icon {
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            font-family: 'Inter', sans-serif;
        }

        .qr-card-body .qr-preview .qr-actions-small .btn-sm-icon.download {
            background: var(--success);
            color: var(--white);
        }

        .qr-card-body .qr-preview .qr-actions-small .btn-sm-icon.download:hover {
            background: #059669;
        }

        .qr-card-body .qr-preview .qr-actions-small .btn-sm-icon.view {
            background: var(--info);
            color: var(--white);
        }

        .qr-card-body .qr-preview .qr-actions-small .btn-sm-icon.view:hover {
            background: #2563eb;
        }

        .qr-card-body .qr-preview .qr-status-text {
            font-size: 0.65rem;
            font-weight: 600;
        }

        .qr-card-body .qr-preview .qr-status-text.active {
            color: var(--success);
        }

        .qr-card-body .qr-preview .qr-status-text.ended {
            color: var(--gray-500);
        }

        .qr-card-body .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem 1.5rem;
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .qr-card-body .details .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.05rem;
        }

        .qr-card-body .details .detail-item .label {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--gray-400);
            letter-spacing: 0.3px;
            font-weight: 600;
        }

        .qr-card-body .details .detail-item .value {
            font-weight: 600;
            color: var(--gray-800);
        }

        .qr-card-body .details .detail-item .value.active-text {
            color: var(--success);
        }

        .qr-card-body .details .detail-item .value.ended-text {
            color: var(--gray-500);
        }

        .qr-card-body .details .detail-item .value .scan-count {
            font-weight: 700;
            color: var(--primary);
        }

        .qr-card-body .actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
            padding-top: 0.75rem;
            border-top: 1px solid var(--gray-100);
        }

        .btn-action {
            padding: 0.35rem 1rem;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-family: 'Inter', sans-serif;
        }

        .btn-action:hover {
            transform: translateY(-1px);
        }

        .btn-action.view {
            background: var(--primary);
            color: var(--white);
        }

        .btn-action.view:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
        }

        .btn-action.deactivate {
            background: var(--danger);
            color: var(--white);
        }

        .btn-action.deactivate:hover {
            background: #b91c1c;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-action.regenerate {
            background: var(--warning);
            color: var(--white);
        }

        .btn-action.regenerate:hover {
            background: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-action.deactivated-label {
            background: var(--gray-100);
            color: var(--gray-500);
            cursor: default;
            pointer-events: none;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 2px dashed var(--gray-200);
        }

        .empty-state .icon {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state h4 {
            color: var(--gray-800);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .empty-state p {
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .alert {
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-left: 4px solid transparent;
        }

        .alert i {
            font-size: 1.2rem;
        }

        .alert-success {
            background: var(--success-light);
            color: #166534;
            border-left-color: var(--success);
        }

        .alert-error {
            background: var(--danger-light);
            color: #991b1b;
            border-left-color: var(--danger);
        }

        .alert-info {
            background: var(--info-light);
            color: #1e40af;
            border-left-color: var(--info);
        }

        .alert .close-btn {
            margin-left: auto;
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: 0.6;
            font-size: 1.1rem;
            transition: var(--transition);
            padding: 0 4px;
        }

        .alert .close-btn:hover {
            opacity: 1;
        }

        .footer-links {
            margin-top: 2rem;
            text-align: center;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: var(--radius);
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .footer-links a:hover {
            color: var(--primary);
        }

        .footer-links .divider {
            color: var(--gray-300);
        }

        /* ============================================================
                   CUSTOM CONFIRM MODAL
                   ============================================================ */
        .custom-confirm-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            padding: 20px;
            animation: overlayFadeIn 0.3s ease;
        }

        .custom-confirm-overlay.show {
            display: flex;
        }

        @keyframes overlayFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .custom-confirm-box {
            background: var(--white);
            border-radius: var(--radius-lg);
            max-width: 520px;
            width: 100%;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
            animation: modalSlideUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .custom-confirm-header {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            background: var(--gray-50);
        }

        .custom-confirm-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .custom-confirm-icon.warning {
            background: var(--warning-light);
            color: var(--warning);
        }

        .custom-confirm-icon.danger {
            background: var(--danger-light);
            color: var(--danger);
        }

        .custom-confirm-icon.success {
            background: var(--success-light);
            color: var(--success);
        }

        .custom-confirm-title-group {
            flex: 1;
            min-width: 0;
        }

        .custom-confirm-title-group h4 {
            margin: 0;
            font-weight: 700;
            color: var(--gray-900);
            font-size: 1.05rem;
        }

        .custom-confirm-title-group p {
            margin: 0.15rem 0 0;
            font-size: 0.85rem;
            color: var(--gray-500);
            line-height: 1.5;
        }

        .custom-confirm-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            font-size: 1.1rem;
            padding: 0.2rem;
            transition: var(--transition);
            flex-shrink: 0;
            line-height: 1;
        }

        .custom-confirm-close:hover {
            color: var(--gray-700);
            transform: rotate(90deg);
        }

        .custom-confirm-body {
            padding: 1.25rem 1.5rem;
        }

        .custom-confirm-details {
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .confirm-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.4rem 0.75rem;
            background: var(--gray-50);
            border-radius: 8px;
            font-size: 0.8rem;
        }

        .confirm-detail-row .confirm-detail-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .confirm-detail-row .confirm-detail-value {
            font-weight: 600;
            color: var(--gray-800);
        }

        .custom-confirm-footer {
            padding: 1rem 1.5rem;
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            background: var(--gray-50);
        }

        .custom-confirm-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: 'Inter', sans-serif;
        }

        .custom-confirm-btn.cancel {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .custom-confirm-btn.cancel:hover {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .custom-confirm-btn.primary {
            background: var(--primary);
            color: var(--white);
        }

        .custom-confirm-btn.primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
            transform: translateY(-1px);
        }

        .custom-confirm-btn.danger {
            background: var(--danger);
            color: var(--white);
        }

        .custom-confirm-btn.danger:hover {
            background: #b91c1c;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }

        .custom-confirm-btn.warning {
            background: var(--warning);
            color: var(--white);
        }

        .custom-confirm-btn.warning:hover {
            background: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            transform: translateY(-1px);
        }

        .custom-confirm-btn.success {
            background: var(--success);
            color: var(--white);
        }

        .custom-confirm-btn.success:hover {
            background: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .custom-confirm-box {
                margin: 10px;
                max-height: 95vh;
                overflow-y: auto;
            }

            .custom-confirm-header {
                flex-wrap: wrap;
            }

            .custom-confirm-title-group {
                width: 100%;
                margin-left: 0;
            }

            .confirm-detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.2rem;
            }

            .custom-confirm-footer {
                flex-direction: column-reverse;
            }

            .custom-confirm-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 1200px) {
            .qr-card-body {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .qr-card-body .qr-preview {
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }

            .qr-card-body .qr-preview img {
                width: 120px;
                height: 120px;
            }
        }

        @media (max-width: 992px) {
            .form-body {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .form-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .form-actions .help-text {
                justify-content: center;
                text-align: center;
            }

            .btn-create-semester {
                justify-content: center;
            }

            .qr-card-body .details {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-info .number {
                font-size: 1.5rem;
            }

            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
            }

            .create-form-card {
                padding: 1.25rem;
            }

            .form-header {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }

            .form-header .btn-back {
                align-self: flex-start;
            }

            .qr-card-body {
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }

            .qr-card-body .details {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .qr-card-body .qr-preview img {
                width: 100px;
                height: 100px;
            }

            .qr-card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }

            .qr-card-header .status-badge {
                align-self: flex-start;
            }

            .section-header {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }

            .section-header .filter-tabs {
                align-self: flex-start;
            }

            .qr-card-body .actions {
                justify-content: center;
            }

            .footer-links {
                flex-direction: column;
                gap: 0.5rem;
            }

            .footer-links .divider {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 0.75rem 1rem;
            }

            .stat-info .number {
                font-size: 1.3rem;
            }

            .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .qr-card-header .left {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.3rem;
            }

            .qr-card-body .qr-preview {
                flex-direction: column;
                align-items: center;
            }

            .qr-card-body .qr-preview img {
                width: 130px;
                height: 130px;
            }
        }
    </style>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-icon total"><i class="bi bi-qr-code"></i></div>
            <div class="stat-info">
                <div class="number">{{ $semesterQrs->count() }}</div>
                <div class="label">Total QR Codes</div>
            </div>
        </div>

        <div class="stat-card active">
            <div class="stat-icon active"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-info">
                <div class="number active">{{ $activeCount }}</div>
                <div class="label">Active (Students can scan)</div>
            </div>
        </div>

        <div class="stat-card ended">
            <div class="stat-icon ended"><i class="bi bi-stop-circle"></i></div>
            <div class="stat-info">
                <div class="number ended">{{ $endedCount }}</div>
                <div class="label">Deactivated (Semester Ended)</div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill"></i> {{ session('info') }}
            <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Create Form -->
    <div class="create-form-card">
        <div class="form-header">
            <div class="title">
                <h5><i class="bi bi-plus-circle" style="color: var(--primary);"></i> Create New Semester QR</h5>
                <span class="badge-new">New</span>
                <span class="badge-no-expiry"><i class="bi bi-infinity"></i> No Expiry</span>
            </div>
            <a href="{{ route('lecturer.attendance.take') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Back to Take Attendance
            </a>
        </div>

        <form method="POST" action="{{ route('lecturer.attendance.sessions.create') }}" id="createSemesterForm">
            @csrf
            <input type="hidden" name="qr_mode" value="semester">

            <div class="form-body">
                <div class="form-group">
                    <label><i class="bi bi-book" style="color: var(--primary);"></i> Select Course <span
                            class="required">*</span></label>
                    <select name="course_id" required>
                        <option value="">— Select a Course —</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }} – {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="bi bi-door-open" style="color: var(--primary);"></i> Room (Optional)</label>
                    <input type="text" name="room" placeholder="e.g., Room 8-5">
                </div>

                <div class="form-actions">
                    <div class="help-text">
                        <i class="bi bi-info-circle"></i>
                        <span>This QR has <strong>no expiry</strong> and <strong>no periods</strong>. Students can scan
                            <strong>once per day</strong> for the entire semester.</span>
                    </div>
                    <button type="submit" class="btn-create-semester" id="createSemesterBtn">
                        <i class="bi bi-infinity"></i> Create Semester QR
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- QR Cards -->
    <div class="qr-cards-section">
        <div class="section-header">
            <h5>
                <i class="bi bi-list-ul" style="color: var(--primary);"></i>
                Your Semester QR Codes
                <span class="count-badge">{{ $semesterQrs->count() }} total</span>
            </h5>
            <div class="filter-tabs">
                <button class="tab-btn active" data-filter="all" onclick="filterQrs('all', this)">All</button>
                <button class="tab-btn" data-filter="active" onclick="filterQrs('active', this)">Active</button>
                <button class="tab-btn" data-filter="ended" onclick="filterQrs('ended', this)">Deactivated</button>
            </div>
        </div>

        @if ($semesterQrs->count() > 0)
            @foreach ($semesterQrs as $qr)
                @php
                    $isActive = $qr->status == 'active';
                    $headerClass = $isActive ? 'active' : 'ended';
                    $statusClass = $isActive ? 'active' : 'ended';
                    $statusLabel = $isActive ? 'Active' : 'Deactivated';
                    $statusTextClass = $isActive ? 'active-text' : 'ended-text';

                    $createdAt = $qr->created_at ? $qr->created_at->format('M d, Y') : 'N/A';
                    $createdTime = $qr->created_at ? $qr->created_at->format('h:i A') : 'N/A';
                    $totalStudents = $qr->total_students ?? 0;
                    $room = $qr->room ?? 'Not specified';
                    $courseName = $qr->course->course_name ?? 'Unknown';
                    $courseCode = $qr->course->course_code ?? 'N/A';
                    $scanCount = $qr->records->count();

                    $qrText =
                        route('student.scan.semester') . '?token=' . $qr->session_token . '&course=' . $qr->course_id;
                    $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qrText);
                    $downloadUrl =
                        'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrText);
                @endphp
                <div class="qr-card" data-status="{{ $qr->status }}">
                    <div class="qr-card-header {{ $headerClass }}">
                        <div class="left">
                            <span class="course-name">{{ $courseName }}</span>
                            <span class="course-code">{{ $courseCode }}</span>
                            <span class="badge-semester"><i class="bi bi-infinity"></i> Semester</span>
                            @if ($isActive)
                                <span class="badge-no-expiry"><i class="bi bi-check-circle"></i> No Expiry</span>
                            @endif
                        </div>
                        <span class="status-badge {{ $statusClass }}">
                            @if ($isActive)
                                <span class="live-dot"></span>
                            @endif
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="qr-card-body">
                        <!-- QR Code Preview -->
                        <div class="qr-preview">
                            <img src="{{ $qrImageUrl }}" alt="QR Code for {{ $courseName }}"
                                id="qr-img-{{ $qr->id }}">
                            <div class="qr-actions-small">
                                <a href="{{ $downloadUrl }}" download="semester-qr-{{ $courseCode }}.png"
                                    class="btn-sm-icon download">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <a href="{{ route('lecturer.semester-qr.view', $qr->id) }}" class="btn-sm-icon view">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </div>
                            <span class="qr-status-text {{ $statusTextClass }}">
                                @if ($isActive)
                                    <i class="bi bi-check-circle"></i> Active - Students can scan
                                @else
                                    <i class="bi bi-lock"></i> Deactivated - No longer active
                                @endif
                            </span>
                        </div>

                        <!-- Details -->
                        <div class="details">
                            <div class="detail-item">
                                <span class="label">Created</span>
                                <span class="value">{{ $createdAt }} at {{ $createdTime }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Room</span>
                                <span class="value">{{ $room }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Enrolled Students</span>
                                <span class="value">{{ $totalStudents }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Status</span>
                                <span class="value {{ $statusTextClass }}">
                                    @if ($isActive)
                                        <i class="bi bi-check-circle"></i> Active
                                    @else
                                        <i class="bi bi-x-circle"></i> Deactivated
                                    @endif
                                </span>
                            </div>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="label">Total Scans Recorded</span>
                                <span class="value">
                                    <span class="scan-count">{{ $scanCount }}</span> attendance records
                                </span>
                            </div>
                            <div class="detail-item" style="grid-column: 1 / -1;">
                                <span class="label">Valid Until</span>
                                <span class="value" style="color: var(--success);">
                                    <i class="bi bi-infinity"></i>
                                    @if ($isActive)
                                        No expiry - active until you deactivate
                                    @else
                                        Deactivated on {{ $qr->ended_at ? $qr->ended_at->format('M d, Y') : 'N/A' }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="actions">
                            @if ($isActive)
                                <a href="{{ route('lecturer.semester-qr.view', $qr->id) }}" class="btn-action view">
                                    <i class="bi bi-eye"></i> View QR
                                </a>
                                <button type="button" class="btn-action deactivate"
                                    onclick="confirmDeactivate({{ $qr->id }}, '{{ addslashes($courseName) }}', {{ $scanCount }})">
                                    <i class="bi bi-stop-circle"></i> Deactivate (End Semester)
                                </button>
                            @else
                                <span class="btn-action deactivated-label">
                                    <i class="bi bi-lock"></i> Deactivated
                                </span>
                                <button type="button" class="btn-action regenerate"
                                    onclick="confirmRegenerate({{ $qr->id }}, {{ $qr->course_id }}, '{{ addslashes($courseName) }}', {{ $scanCount }})">
                                    <i class="bi bi-arrow-repeat"></i> Create New for Next Semester
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <span class="icon"><i class="bi bi-inbox"></i></span>
                <h4>No Semester QR Codes</h4>
                <p>You haven't created any semester QR codes yet.</p>
                <p class="sub-text">Use the form above to create your first semester QR code. It will work for the entire
                    semester with no expiry.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer-links">
        <a href="{{ route('lecturer.attendance.take') }}">
            <i class="bi bi-arrow-left"></i> Back to Take Attendance
        </a>
        <span class="divider">|</span>
        <a href="{{ route('lecturer.attendance.sessions') }}">
            <i class="bi bi-clock-history"></i> View Session History
        </a>
        <span class="divider">|</span>
        <a href="{{ route('lecturer.dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
    </div>

    <script>
        // ============================================================
        // FILTER QR CARDS
        // ============================================================
        function filterQrs(filter, button) {
            document.querySelectorAll('.filter-tabs .tab-btn').forEach(function(btn) {
                btn.classList.remove('active');
            });
            button.classList.add('active');

            const cards = document.querySelectorAll('.qr-card');
            cards.forEach(function(card) {
                const status = card.dataset.status;
                card.style.display = (filter === 'all' || status === filter) ? 'block' : 'none';
            });
        }

        // ============================================================
        // FORM SUBMIT HANDLER
        // ============================================================
        document.getElementById('createSemesterForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('createSemesterBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML =
                    '<span class="spinner-border spinner-border-sm" style="width:16px;height:16px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;"></span> Creating...';
            }
        });

        // ============================================================
        // CUSTOM CONFIRM MODAL
        // ============================================================
        function showConfirmModal(options) {
            const {
                title,
                message,
                details,
                confirmText,
                cancelText,
                confirmClass,
                onConfirm,
                onCancel
            } = options;

            const existingModal = document.getElementById('customConfirmModal');
            if (existingModal) {
                existingModal.remove();
            }

            const modal = document.createElement('div');
            modal.id = 'customConfirmModal';
            modal.className = 'custom-confirm-overlay';
            modal.innerHTML = `
                <div class="custom-confirm-box">
                    <div class="custom-confirm-header">
                        <div class="custom-confirm-icon ${confirmClass || 'warning'}">
                            <i class="bi ${confirmClass === 'danger' ? 'bi-exclamation-triangle-fill' : confirmClass === 'success' ? 'bi-check-circle-fill' : 'bi-info-circle-fill'}"></i>
                        </div>
                        <div class="custom-confirm-title-group">
                            <h4>${title}</h4>
                            <p>${message}</p>
                        </div>
                        <button class="custom-confirm-close" onclick="closeConfirmModal()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="custom-confirm-body">
                        ${details ? `<div class="custom-confirm-details">${details}</div>` : ''}
                    </div>
                    <div class="custom-confirm-footer">
                        <button class="custom-confirm-btn cancel" onclick="closeConfirmModal()">
                            <i class="bi bi-x-lg"></i> ${cancelText || 'Cancel'}
                        </button>
                        <button class="custom-confirm-btn ${confirmClass || 'primary'}" id="customConfirmAction">
                            <i class="bi ${confirmClass === 'danger' ? 'bi-stop-circle' : confirmClass === 'success' ? 'bi-check-lg' : 'bi-check-lg'}"></i> ${confirmText || 'Confirm'}
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            setTimeout(() => {
                modal.classList.add('show');
            }, 10);

            const confirmBtn = document.getElementById('customConfirmAction');
            confirmBtn.addEventListener('click', function() {
                closeConfirmModal();
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            });

            const handleEsc = function(e) {
                if (e.key === 'Escape') {
                    closeConfirmModal();
                    if (typeof onCancel === 'function') {
                        onCancel();
                    }
                    document.removeEventListener('keydown', handleEsc);
                }
            };
            document.addEventListener('keydown', handleEsc);

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeConfirmModal();
                    if (typeof onCancel === 'function') {
                        onCancel();
                    }
                }
            });

            return modal;
        }

        function closeConfirmModal() {
            const modal = document.getElementById('customConfirmModal');
            if (modal) {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.remove();
                }, 300);
            }
        }

        // ============================================================
        // CONFIRM WRAPPER FUNCTIONS
        // ============================================================
        function confirmDeactivate(qrId, qrName, scanCount) {
            showConfirmModal({
                title: 'Deactivate Semester QR?',
                message: `You are about to deactivate "${qrName}". Students will no longer be able to scan this QR.`,
                details: `
                    <div class="confirm-detail-row">
                        <span class="confirm-detail-label"><i class="bi bi-check-circle" style="color: var(--success);"></i> Attendance Records</span>
                        <span class="confirm-detail-value"><strong>${scanCount}</strong> records will be preserved</span>
                    </div>
                    <div class="confirm-detail-row">
                        <span class="confirm-detail-label"><i class="bi bi-lock" style="color: var(--danger);"></i> QR Status</span>
                        <span class="confirm-detail-value" style="color: var(--danger);">Will be deactivated</span>
                    </div>
                    <div class="confirm-detail-row">
                        <span class="confirm-detail-label"><i class="bi bi-arrow-repeat" style="color: var(--warning);"></i> Next Steps</span>
                        <span class="confirm-detail-value">Create a new QR for next semester</span>
                    </div>
                `,
                confirmText: 'Deactivate QR',
                cancelText: 'Cancel',
                confirmClass: 'danger',
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/lecturer/semester-qr/${qrId}/end`;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '{{ csrf_token() }}';
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmRegenerate(qrId, courseId, courseName, scanCount) {
            showConfirmModal({
                title: 'Create New Semester QR?',
                message: `This will generate a brand new QR code for "${courseName}".`,
                details: `
                    <div class="confirm-detail-row">
                        <span class="confirm-detail-label"><i class="bi bi-archive" style="color: var(--gray-500);"></i> Old QR</span>
                        <span class="confirm-detail-value">Will be deactivated with <strong>${scanCount}</strong> records</span>
                    </div>
                    <div class="confirm-detail-row">
                        <span class="confirm-detail-label"><i class="bi bi-plus-circle" style="color: var(--success);"></i> New QR</span>
                        <span class="confirm-detail-value" style="color: var(--success);">Will be active for next semester</span>
                    </div>
                    <div class="confirm-detail-row">
                        <span class="confirm-detail-label"><i class="bi bi-infinity" style="color: var(--info);"></i> New QR</span>
                        <span class="confirm-detail-value">Has no expiry, works for entire semester</span>
                    </div>
                `,
                confirmText: 'Create New QR',
                cancelText: 'Cancel',
                confirmClass: 'warning',
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/lecturer/course/${courseId}/regenerate-semester-qr`;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '{{ csrf_token() }}';
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // ============================================================
        // AUTO-CLOSE ALERTS
        // ============================================================
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                alert.style.transition = 'all 0.3s ease';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }, 5000);
        });
    </script>
@endsection
