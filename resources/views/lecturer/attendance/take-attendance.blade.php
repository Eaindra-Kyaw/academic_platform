@extends('layouts.app')

@section('title', 'Take Attendance')
@section('role', 'Lecturer')
@section('page-title', 'Take Attendance')
@section('welcome-text', 'Welcome, ' . Auth::user()->name)

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

        /* ============================================================
                                   PROFESSIONAL NOTIFICATIONS
                                   ============================================================ */
        .notification-success,
        .notification-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.9rem 1.25rem;
            margin-bottom: 1.25rem;
            border-radius: var(--radius-lg);
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .notification-success::before,
        .notification-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
        }

        .notification-success::before {
            background: var(--success);
        }

        .notification-info::before {
            background: var(--info);
        }

        .notification-success:hover,
        .notification-info:hover {
            box-shadow: var(--shadow);
        }

        .notification-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        .notification-success .notification-icon {
            background: var(--success-light);
            color: var(--success);
        }

        .notification-info .notification-icon {
            background: var(--info-light);
            color: var(--info);
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-content .notification-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-400);
            margin-bottom: 0.05rem;
        }

        .notification-content .notification-message {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--gray-800);
            line-height: 1.4;
        }

        .notification-close {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            font-size: 0.9rem;
            padding: 0.2rem;
            transition: var(--transition);
            flex-shrink: 0;
            line-height: 1;
        }

        .notification-close:hover {
            color: var(--gray-700);
            transform: rotate(90deg);
        }

        .notification-success,
        .notification-info {
            animation: slideInNotification 0.4s cubic-bezier(0.34, 1.56, 0.64, 1),
                fadeOutNotification 0.5s ease 4.8s forwards;
        }

        @keyframes slideInNotification {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.96);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeOutNotification {
            to {
                opacity: 0;
                transform: translateY(-15px) scale(0.96);
            }
        }

        /* ============================================================
                                   QR CONTAINER
                                   ============================================================ */
        .qr-container {
            background: var(--primary-gradient);
            color: var(--white);
            padding: 1.5rem 2rem;
            border-radius: var(--radius-lg);
            text-align: center;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .qr-container::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        .qr-container .mode-badge {
            display: inline-block;
            padding: 0.2rem 1rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.15);
            color: #fcd34d;
            margin-bottom: 0.75rem;
        }

        .qr-container h4 {
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 0.25rem;
        }

        .qr-container .sub-text {
            font-size: 0.85rem;
            opacity: 0.8;
            margin-bottom: 1rem;
        }

        .qr-box {
            background: var(--white);
            padding: 1rem;
            border-radius: var(--radius);
            display: inline-block;
            margin: 0.5rem auto;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .qr-box img {
            width: 200px;
            height: 200px;
            display: block;
        }

        .qr-box .download-btn {
            display: inline-block;
            margin-top: 0.5rem;
            padding: 0.3rem 1rem;
            background: var(--success);
            color: var(--white);
            border-radius: 8px;
            font-size: 0.75rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .qr-box .download-btn:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .qr-details {
            margin: 1rem 0;
        }

        .qr-details p {
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }

        .qr-details .manual-code {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 4px;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.3rem 1.5rem;
            border-radius: 8px;
            display: inline-block;
            font-family: monospace;
            margin-top: 0.3rem;
        }

        .qr-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn-qr {
            padding: 0.4rem 1.2rem;
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
            text-decoration: none;
        }

        .btn-qr:hover {
            transform: translateY(-2px);
        }

        .btn-qr.danger {
            background: var(--danger);
            color: var(--white);
        }

        .btn-qr.danger:hover {
            background: #b91c1c;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-qr.warning {
            background: var(--warning);
            color: var(--white);
        }

        .btn-qr.warning:hover {
            background: #d97706;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-qr.info {
            background: var(--info);
            color: var(--white);
        }

        .btn-qr.info:hover {
            background: #2563eb;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-qr.primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-qr.primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
        }

        .btn-qr.secondary {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-qr.secondary:hover {
            background: var(--gray-300);
        }

        .countdown {
            font-size: 0.9rem;
            margin-top: 0.5rem;
            opacity: 0.9;
        }

        .mode-selector {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .mode-selector:hover {
            box-shadow: var(--shadow);
        }

        .mode-selector h5 {
            color: var(--gray-800);
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .mode-selector h5 i {
            color: var(--primary);
        }

        .form-control {
            width: 100%;
            padding: 0.6rem 0.9rem;
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            font-size: 0.85rem;
            background: var(--gray-50);
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
            color: var(--gray-800);
            margin-bottom: 0.75rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(10, 36, 99, 0.06);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--gray-700);
            display: block;
            margin-bottom: 0.2rem;
        }

        .btn-submit {
            background: var(--primary-gradient);
            color: var(--white);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.2);
            width: 100%;
            justify-content: center;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(10, 36, 99, 0.3);
            color: var(--white);
        }

        .semester-link-card {
            background: var(--gray-50);
            border: 2px dashed var(--primary);
            border-radius: var(--radius-lg);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            transition: var(--transition);
            cursor: default;
        }

        .semester-link-card:hover {
            background: var(--white);
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .semester-link-card .left h5 {
            margin: 0;
            font-weight: 700;
            color: var(--gray-800);
            font-size: 1rem;
        }

        .semester-link-card .left h5 i {
            color: var(--primary);
        }

        .semester-link-card .left p {
            margin: 0.2rem 0 0;
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }

        .stat-box {
            text-align: center;
            padding: 0.5rem;
            border-radius: var(--radius);
            background: var(--gray-50);
            transition: var(--transition);
        }

        .stat-box:hover {
            background: var(--gray-100);
        }

        .stat-box .number {
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .stat-box .number.present {
            color: var(--success);
        }

        .stat-box .number.late {
            color: var(--warning);
        }

        .stat-box .number.absent {
            color: var(--danger);
        }

        .stat-box .number.total {
            color: var(--primary);
        }

        .stat-box .label {
            font-size: 0.65rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 600;
        }

        .live-attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .live-attendance-table thead {
            background: var(--gray-50);
        }

        .live-attendance-table th {
            padding: 0.5rem 0.75rem;
            text-align: left;
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--gray-500);
            font-weight: 700;
            letter-spacing: 0.3px;
            border-bottom: 2px solid var(--gray-200);
        }

        .live-attendance-table td {
            padding: 0.4rem 0.75rem;
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }

        .live-attendance-table tbody tr {
            transition: var(--transition);
        }

        .live-attendance-table tbody tr:hover {
            background: var(--gray-50);
        }

        .live-attendance-table .status-badge {
            display: inline-block;
            padding: 0.1rem 0.6rem;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .status-badge.present {
            background: var(--success-light);
            color: #166534;
        }

        .status-badge.late {
            background: var(--warning-light);
            color: #92400e;
        }

        .status-badge.absent {
            background: var(--danger-light);
            color: #991b1b;
        }

        .live-badge {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse-dot 1.5s infinite;
            margin-right: 0.3rem;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .attendance-counter {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .attendance-counter strong {
            color: var(--gray-800);
        }

        .live-attendance-table .new-row {
            animation: highlightRow 1.5s ease;
        }

        @keyframes highlightRow {
            0% {
                background: #fef3c7;
            }

            50% {
                background: #fde68a;
            }

            100% {
                background: transparent;
            }
        }

        .qr-list {
            margin-top: 0.5rem;
        }

        .qr-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0.75rem;
            background: var(--white);
            border-radius: 8px;
            margin-bottom: 0.3rem;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .qr-item:hover {
            border-color: var(--primary);
        }

        .qr-item .qr-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .qr-item .qr-info .code {
            font-weight: 600;
            color: var(--gray-800);
            font-size: 0.85rem;
        }

        .qr-item .qr-info .name {
            color: var(--gray-500);
            font-size: 0.8rem;
        }

        .qr-item .qr-info .badge-semester-sm {
            font-size: 0.55rem;
            font-weight: 700;
            padding: 0.05rem 0.4rem;
            border-radius: 8px;
            background: #dbeafe;
            color: #1e40af;
            display: inline-flex;
            align-items: center;
            gap: 0.15rem;
        }

        .qr-item .qr-actions-sm {
            display: flex;
            gap: 0.3rem;
        }

        .btn-sm-custom {
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

        .btn-sm-custom.info {
            background: var(--info);
            color: var(--white);
        }

        .btn-sm-custom.info:hover {
            background: #2563eb;
        }

        .btn-sm-custom.primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-sm-custom.primary:hover {
            background: var(--primary-dark);
        }

        .btn-sm-custom.danger {
            background: var(--danger);
            color: var(--white);
        }

        .btn-sm-custom.danger:hover {
            background: #b91c1c;
        }

        .qr-badge.dynamic {
            font-size: 0.55rem;
            padding: 0.05rem 0.4rem;
            border-radius: 8px;
            background: #fef3c7;
            color: #92400e;
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

        @media (max-width: 992px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .qr-container {
                padding: 1.25rem;
            }

            .qr-box img {
                width: 150px;
                height: 150px;
            }

            .qr-details .manual-code {
                font-size: 1.2rem;
                padding: 0.2rem 1rem;
            }

            .semester-link-card {
                flex-direction: column;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-box .number {
                font-size: 1.3rem;
            }

            .live-attendance-table {
                font-size: 0.7rem;
            }

            .live-attendance-table th,
            .live-attendance-table td {
                padding: 0.3rem 0.5rem;
            }

            .qr-actions {
                flex-direction: column;
                align-items: center;
            }

            .qr-actions .btn-qr {
                width: 100%;
                justify-content: center;
            }

            .qr-item {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }

            .qr-item .qr-actions-sm {
                justify-content: flex-start;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .stat-box {
                padding: 0.3rem;
            }

            .stat-box .number {
                font-size: 1.1rem;
            }

            .qr-container h4 {
                font-size: 1.1rem;
            }

            .mode-selector {
                padding: 1rem;
            }
        }
    </style>

    <!-- ============================================================
                            NOTIFICATIONS
                            ============================================================ -->
    @if (session('success'))
        <div class="notification-success" id="notificationSuccess">
            <div class="notification-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">Success</div>
                <div class="notification-message">{{ session('success') }}</div>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div class="notification-info" id="notificationInfo">
            <div class="notification-icon">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">Info</div>
                <div class="notification-message">{{ session('info') }}</div>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <!-- ============================================================
                                    ACTIVE SESSION DISPLAY (Always shown if exists)
                                    ============================================================ -->
            @if ($activeSession)
                <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
                    <a href="{{ route('lecturer.attendance.take') }}?back=1" class="btn-qr secondary">
                        <i class="bi bi-arrow-left"></i> Back to Create New QR
                    </a>
                </div>

                <!-- Dynamic QR Display -->
                <div class="qr-container">
                    <span class="mode-badge"><i class="bi bi-arrow-repeat"></i> Session QR (Dynamic)</span>
                    <h4><i class="bi bi-qr-code"></i> Dynamic QR Code</h4>
                    <p class="sub-text">Changes every session - expires in {{ $activeSession->duration }} minutes</p>

                    <div class="qr-box">
                        @php
                            $dynamicQrText =
                                route('student.scan.process') .
                                '?token=' .
                                $activeSession->session_token .
                                '&session=' .
                                $activeSession->id;
                        @endphp
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($dynamicQrText) }}"
                            alt="Dynamic QR">
                        <div>
                            <a href="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($dynamicQrText) }}"
                                download="dynamic-qr.png" class="download-btn">
                                <i class="bi bi-download"></i> Download QR
                            </a>
                        </div>
                    </div>

                    <div class="qr-details">
                        <p><i class="bi bi-book"></i> <strong>Course:</strong>
                            {{ $activeSession->course->course_name ?? 'N/A' }}</p>
                        <p><i class="bi bi-door-open"></i> <strong>Room:</strong>
                            {{ $activeSession->room ?? 'Not specified' }}</p>
                        <p><i class="bi bi-layers"></i> <strong>Periods:</strong>
                            {{ $activeSession->conducted_periods ?? 4 }}</p>
                        <p><strong>Manual Code:</strong></p>
                        <div class="manual-code">{{ $activeSession->manual_code }}</div>
                        <p style="font-size: 0.75rem; opacity: 0.7; margin-top: 0.3rem;">
                            <i class="bi bi-info-circle"></i> Students can enter this code manually if they can't scan
                        </p>
                        <div class="countdown" id="countdownTimer"></div>
                    </div>

                    <div class="qr-actions">
                        <button type="button" class="btn-qr danger" onclick="confirmEndSession({{ $activeSession->id }})">
                            <i class="bi bi-stop-circle"></i> End Session
                        </button>
                        <a href="{{ route('lecturer.attendance.sessions.refresh', $activeSession->id) }}"
                            class="btn-qr warning">
                            <i class="bi bi-arrow-repeat"></i> Refresh QR
                        </a>
                    </div>
                </div>

                <!-- Live Attendance Statistics -->
                <div class="mode-selector">
                    <h5><i class="bi bi-graph-up"></i> Live Attendance Statistics</h5>
                    <div class="stats-grid">
                        <div class="stat-box">
                            <div class="number present" id="livePresentCount">{{ $activeSession->present_count ?? 0 }}
                            </div>
                            <div class="label">Present</div>
                        </div>
                        <div class="stat-box">
                            <div class="number late" id="liveLateCount">{{ $activeSession->late_count ?? 0 }}</div>
                            <div class="label">Late</div>
                        </div>
                        <div class="stat-box">
                            <div class="number absent" id="liveAbsentCount">
                                {{ max(0, ($activeSession->total_students ?? 0) - ($activeSession->present_count ?? 0) - ($activeSession->late_count ?? 0)) }}
                            </div>
                            <div class="label">Absent</div>
                        </div>
                        <div class="stat-box">
                            <div class="number total" id="liveTotalCount">{{ $activeSession->total_students ?? 0 }}</div>
                            <div class="label">Total</div>
                        </div>
                    </div>
                </div>

                <!-- Real-Time Attendance -->
                <div class="mode-selector">
                    <h5>
                        <i class="bi bi-clock-history"></i> Real-Time Attendance
                        <span class="live-badge"></span>
                        <span class="attendance-counter">
                            <strong id="totalScanned">0</strong> students scanned so far
                        </span>
                    </h5>
                    <div style="overflow-x: auto; max-height: 400px; overflow-y: auto;">
                        <table class="live-attendance-table" id="liveAttendanceTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                @php
                                    $existingRecords = $activeSession->records ?? collect();
                                    $counter = 0;
                                @endphp
                                @if ($existingRecords && $existingRecords->count() > 0)
                                    @foreach ($existingRecords as $record)
                                        @php
                                            $counter++;
                                            $statusClass = $record->status ?? 'absent';
                                        @endphp
                                        <tr id="attendance-row-{{ $record->id }}">
                                            <td>{{ $counter }}</td>
                                            <td>
                                                <strong>{{ $record->student->name ?? 'Unknown' }}</strong>
                                                <br>
                                                <small
                                                    style="color: var(--gray-500); font-size: 0.6rem;">{{ $record->student->email ?? 'N/A' }}</small>
                                            </td>
                                            <td><span
                                                    class="status-badge {{ $statusClass }}">{{ ucfirst($statusClass) }}</span>
                                            </td>
                                            <td style="font-size: 0.7rem; color: var(--gray-500);">
                                                {{ $record->scanned_at ? \Carbon\Carbon::parse($record->scanned_at)->format('h:i:s A') : 'N/A' }}
                                            </td>
                                            <td>
                                                @if ($record->is_manual)
                                                    <span
                                                        style="font-size: 0.6rem; background: #dbeafe; color: #1e40af; padding: 0.1rem 0.4rem; border-radius: 4px;">Manual</span>
                                                @else
                                                    <span
                                                        style="font-size: 0.6rem; background: #dcfce7; color: #166534; padding: 0.1rem 0.4rem; border-radius: 4px;">QR
                                                        Scan</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 1.5rem; color: #9ca3af;">
                                            <i class="bi bi-inbox"
                                                style="font-size: 1.5rem; display: block; margin-bottom: 0.3rem;"></i>
                                            No students have scanned yet
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- ============================================================
                                    CREATE DYNAMIC QR FORM - Always visible if no active session OR if "Back" was clicked
                                    ============================================================ -->
            @if (!$activeSession || $showCreateForm)
                <div class="mode-selector">
                    <h5><i class="bi bi-sliders2"></i> Create Dynamic QR Session</h5>

                    <form method="POST" action="{{ route('lecturer.attendance.sessions.create') }}">
                        @csrf
                        <input type="hidden" name="qr_mode" value="session">

                        <select name="course_id" class="form-control" required>
                            <option value="">— Select Course —</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_code }} -
                                    {{ $course->course_name }}</option>
                            @endforeach
                        </select>

                        <div style="margin-bottom: 0.75rem;">
                            <label class="form-label">Number of Class Periods</label>
                            <select name="period_count" class="form-control" required>
                                <option value="1">1 period (50 min)</option>
                                <option value="2">2 periods (1h 40m)</option>
                                <option value="3">3 periods (2h 30m)</option>
                                <option value="4" selected>4 periods (3h 20m)</option>
                                <option value="5">5 periods (4h 10m)</option>
                                <option value="6">6 periods (5h)</option>
                                <option value="7">7 periods (5h 50m)</option>
                                <option value="8">8 periods (6h 40m)</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 0.75rem;">
                            <label class="form-label">QR Active Duration</label>
                            <select name="duration" class="form-control" required>
                                <option value="15">15 minutes</option>
                                <option value="30" selected>30 minutes</option>
                                <option value="45">45 minutes</option>
                                <option value="60">60 minutes</option>
                                <option value="90">90 minutes</option>
                                <option value="120">120 minutes</option>
                            </select>
                        </div>

                        <input type="text" name="room" class="form-control" placeholder="Room (optional)">

                        <button type="submit" class="btn-submit"><i class="bi bi-qr-code"></i> Start QR Session</button>
                    </form>
                </div>
            @endif

            <!-- ============================================================
                                    SEMESTER QR MANAGEMENT LINK - Always visible
                                    ============================================================ -->
            {{-- <div class="semester-link-card">
                <div class="left">
                    <h5><i class="bi bi-infinity"></i> Semester QR (Static)</h5>
                    <p>Create QR codes that last for the entire semester. Students can scan anytime - no expiry.</p>
                </div>
                <a href="{{ route('lecturer.semester-qr.management') }}" class="btn-qr info">
                    <i class="bi bi-gear"></i> Manage Semester QRs
                </a>
            </div> --}}

            <!-- Active Semester QR Notice -->
            @if (isset($existingStaticQrs) && $existingStaticQrs->count() > 0)
                <div class="mode-selector">
                    <h5 style="color: var(--gray-800);">
                        <i class="bi bi-infinity" style="color: var(--primary);"></i> Active Semester QR Codes
                        <span style="font-size: 0.75rem; font-weight: 400; color: var(--gray-500); margin-left: 0.5rem;">
                            {{ $existingStaticQrs->count() }} active
                        </span>
                    </h5>
                    @foreach ($existingStaticQrs as $session)
                        <div class="qr-item">
                            <div class="qr-info">
                                <span class="code">{{ $session->course->course_code ?? 'N/A' }}</span>
                                <span class="name">{{ $session->course->course_name ?? 'Unknown' }}</span>
                                <span class="badge-semester-sm"><i class="bi bi-infinity"></i> Semester</span>
                            </div>
                            <div class="qr-actions-sm">
                                <a href="{{ route('lecturer.semester-qr.view', $session->id) }}"
                                    class="btn-sm-custom info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('lecturer.semester-qr.management') }}" class="btn-sm-custom primary">
                                    <i class="bi bi-list"></i> Manage
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- ============================================================
                                RIGHT COLUMN - MANUAL ATTENDANCE
                                ============================================================ -->
        <div class="col-md-6">
            <div class="mode-selector">
                <h5><i class="bi bi-pencil-square"></i> Manual Attendance Entry</h5>
                <form method="POST" action="{{ route('lecturer.attendance.manual') }}">
                    @csrf

                    <select name="course_id" class="form-control" required>
                        <option value="">Select Course</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>

                    <select name="status" class="form-control" required>
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="absent">Absent</option>
                    </select>

                    <div style="margin-top: 0.5rem;">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g., Student arrived 15 minutes late"
                            style="resize: vertical;"></textarea>
                    </div>

                    <button type="submit" class="btn-submit"><i class="bi bi-save"></i> Save Manual Attendance</button>
                </form>
            </div>

            <!-- Session History Link -->
            <div class="mode-selector" style="text-align: center; background: var(--gray-50);">
                <a href="{{ route('lecturer.attendance.sessions') }}" class="btn-qr primary"
                    style="width: 100%; justify-content: center; background: transparent; color: var(--primary); border: 2px solid var(--primary);">
                    <i class="bi bi-clock-history"></i> View Full Session History
                </a>
                <div style="font-size: 0.7rem; color: var(--gray-400); margin-top: 0.5rem;">
                    View all past sessions, attendance statistics, and detailed reports
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // TIMER FOR DYNAMIC QR
        // ============================================================
        @if ($activeSession && $activeSession->expires_at)
            let expiresAt = new Date('{{ $activeSession->expires_at }}').getTime();

            function updateTimer() {
                let now = new Date().getTime();
                let distance = expiresAt - now;
                if (distance < 0) {
                    document.getElementById('countdownTimer').innerHTML = '⏹ EXPIRED';
                    return;
                }
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.getElementById('countdownTimer').innerHTML = '⏱ Time remaining: ' + minutes + 'm ' + seconds + 's';
            }
            updateTimer();
            setInterval(updateTimer, 1000);
        @endif

        // ============================================================
        // LIVE ATTENDANCE POLLING
        // ============================================================
        @if ($activeSession)
            const sessionId = {{ $activeSession->id }};
            const pollInterval = 3000;

            function fetchLiveAttendance() {
                fetch(`/lecturer/session-stats/${sessionId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('livePresentCount').innerText = data.present || 0;
                            document.getElementById('liveLateCount').innerText = data.late || 0;
                            document.getElementById('liveTotalCount').innerText = data.total || 0;
                            const absent = (data.total || 0) - (data.present || 0) - (data.late || 0);
                            document.getElementById('liveAbsentCount').innerText = Math.max(0, absent);

                            const tbody = document.getElementById('attendanceTableBody');
                            if (data.records && data.records.length > 0) {
                                let html = '';
                                data.records.forEach((record, index) => {
                                    const statusClass = record.status || 'absent';
                                    const isManual = record.is_manual || false;
                                    const methodLabel = isManual ? 'Manual' : 'QR Scan';
                                    html += `
                                        <tr id="attendance-row-${index}" class="${index === data.records.length - 1 ? 'new-row' : ''}">
                                            <td>${index + 1}</td>
                                            <td>
                                                <strong>${record.student_name || 'Unknown'}</strong>
                                                <br>
                                                <small style="color: var(--gray-500); font-size: 0.6rem;">${record.student_email || 'N/A'}</small>
                                            </td>
                                            <td><span class="status-badge ${statusClass}">${statusClass.charAt(0).toUpperCase() + statusClass.slice(1)}</span></td>
                                            <td style="font-size: 0.7rem; color: var(--gray-500);">${record.scanned_at ? new Date(record.scanned_at).toLocaleTimeString() : 'N/A'}</td>
                                            <td>
                                                <span style="font-size: 0.6rem; background: ${isManual ? '#dbeafe' : '#dcfce7'}; color: ${isManual ? '#1e40af' : '#166534'}; padding: 0.1rem 0.4rem; border-radius: 4px;">
                                                    ${methodLabel}
                                                </span>
                                            </td>
                                        </tr>
                                    `;
                                });
                                if (tbody.querySelector('tr td[colspan]')) {
                                    tbody.innerHTML = html;
                                } else {
                                    const existingRows = tbody.querySelectorAll('tr');
                                    if (data.records.length > existingRows.length) {
                                        const newRecords = data.records.slice(existingRows.length);
                                        let newHtml = '';
                                        newRecords.forEach((record, idx) => {
                                            const globalIndex = existingRows.length + idx;
                                            const statusClass = record.status || 'absent';
                                            const isManual = record.is_manual || false;
                                            const methodLabel = isManual ? 'Manual' : 'QR Scan';
                                            newHtml += `
                                                <tr class="new-row">
                                                    <td>${globalIndex + 1}</td>
                                                    <td><strong>${record.student_name || 'Unknown'}</strong></td>
                                                    <td><span class="status-badge ${statusClass}">${statusClass.charAt(0).toUpperCase() + statusClass.slice(1)}</span></td>
                                                    <td style="font-size: 0.7rem; color: var(--gray-500);">${record.scanned_at ? new Date(record.scanned_at).toLocaleTimeString() : 'N/A'}</td>
                                                    <td><span style="font-size: 0.6rem; background: ${isManual ? '#dbeafe' : '#dcfce7'}; color: ${isManual ? '#1e40af' : '#166534'}; padding: 0.1rem 0.4rem; border-radius: 4px;">${methodLabel}</span></td>
                                                </tr>
                                            `;
                                        });
                                        tbody.insertAdjacentHTML('beforeend', newHtml);
                                    } else {
                                        const rows = tbody.querySelectorAll('tr');
                                        rows.forEach((row, idx) => {
                                            const td = row.querySelector('td:first-child');
                                            if (td) td.textContent = idx + 1;
                                        });
                                    }
                                }
                                document.getElementById('totalScanned').innerText = data.records.length;
                            } else {
                                tbody.innerHTML = `
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 1.5rem; color: #9ca3af;">
                                            <i class="bi bi-inbox" style="font-size: 1.5rem; display: block; margin-bottom: 0.3rem;"></i>
                                            No students have scanned yet
                                        </td>
                                    </tr>
                                `;
                                document.getElementById('totalScanned').innerText = 0;
                            }
                        }
                    })
                    .catch(error => console.error('Error fetching live attendance:', error));
            }
            fetchLiveAttendance();
            setInterval(fetchLiveAttendance, pollInterval);
        @endif

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
        // CONFIRM END SESSION
        // ============================================================
        function confirmEndSession(sessionId) {
            const totalScanned = document.getElementById('totalScanned')?.innerText || '0';
            showConfirmModal({
                title: 'End Session?',
                message: 'This will end the current QR session. Students will no longer be able to scan.',
                details: `
                    <div class="confirm-detail-row">
                        <span class="confirm-detail-label"><i class="bi bi-people" style="color: var(--info);"></i> Scans Recorded</span>
                        <span class="confirm-detail-value"><strong>${totalScanned}</strong> students</span>
                    </div>
                    <div class="confirm-detail-row">
                        <span class="confirm-detail-label"><i class="bi bi-clock" style="color: var(--warning);"></i> Session Duration</span>
                        <span class="confirm-detail-value">{{ $activeSession->duration ?? 'N/A' }} minutes</span>
                    </div>
                `,
                confirmText: 'End Session',
                cancelText: 'Cancel',
                confirmClass: 'danger',
                onConfirm: function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/lecturer/attendance/sessions/${sessionId}/end`;
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
        document.querySelectorAll('.notification-success, .notification-info').forEach(function(notification) {
            setTimeout(function() {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                notification.style.transition = 'all 0.3s ease';
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 5000);
        });
    </script>
@endsection
