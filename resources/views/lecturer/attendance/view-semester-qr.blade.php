@extends('layouts.app')

@section('title', 'View Semester QR - ' . ($qr->course->course_code ?? 'N/A'))
@section('role', 'Lecturer')
@section('page-title', 'Semester QR Code')
@section('welcome-text', $qr->course->course_name ?? 'Unknown Course')

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

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            background: var(--gray-100);
            color: var(--gray-600);
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: var(--transition);
            margin-bottom: 1.25rem;
            border: 1px solid transparent;
        }

        .back-link:hover {
            background: var(--gray-200);
            color: var(--gray-800);
            transform: translateX(-2px);
        }

        .qr-display-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
            box-shadow: var(--shadow);
            max-width: 650px;
            margin: 0 auto;
            text-align: center;
            transition: var(--transition);
        }

        .qr-display-card:hover {
            box-shadow: var(--shadow-lg);
        }

        .qr-display-card .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .qr-display-card .header .course-info {
            text-align: left;
        }

        .qr-display-card .header .course-info .name {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--gray-900);
        }

        .qr-display-card .header .course-info .code {
            font-size: 0.8rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .qr-display-card .header .status-badge {
            padding: 0.2rem 1rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .qr-display-card .header .status-badge.active {
            background: var(--success-light);
            color: #166534;
        }

        .qr-display-card .header .status-badge.active::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse-dot 2s infinite;
        }

        .qr-display-card .header .status-badge.ended {
            background: var(--gray-200);
            color: var(--gray-600);
        }

        .qr-display-card .header .badge-no-expiry {
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

        .qr-display-card .header .badge-semester {
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

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .qr-display-card .qr-wrapper {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--radius);
            border: 2px dashed var(--gray-200);
            margin-bottom: 1.5rem;
        }

        .qr-display-card .qr-wrapper img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            background: var(--white);
            padding: 0.5rem;
            border: 1px solid var(--gray-200);
        }

        .qr-display-card .manual-code {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 6px;
            background: var(--gray-50);
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            display: inline-block;
            font-family: monospace;
            color: var(--primary);
            border: 2px solid var(--gray-200);
            margin: 0.5rem 0;
        }

        .qr-display-card .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem 1.5rem;
            text-align: left;
            margin: 1.5rem 0;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: var(--radius);
        }

        .qr-display-card .info-grid .item .label {
            font-size: 0.6rem;
            text-transform: uppercase;
            color: var(--gray-400);
            letter-spacing: 0.3px;
            font-weight: 600;
        }

        .qr-display-card .info-grid .item .value {
            font-weight: 600;
            color: var(--gray-800);
            font-size: 0.9rem;
        }

        .qr-display-card .actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .btn-action {
            padding: 0.5rem 1.5rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-family: 'Inter', sans-serif;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-action.download {
            background: var(--success);
            color: var(--white);
        }

        .btn-action.download:hover {
            background: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-action.primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-action.primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 12px rgba(10, 36, 99, 0.3);
        }

        .btn-action.back {
            background: var(--gray-200);
            color: var(--gray-700);
        }

        .btn-action.back:hover {
            background: var(--gray-300);
        }

        .btn-action.danger {
            background: var(--danger);
            color: var(--white);
        }

        .btn-action.danger:hover {
            background: #b91c1c;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .instruction-box {
            background: var(--info-light);
            border-radius: var(--radius);
            padding: 1rem 1.25rem;
            margin-top: 1.5rem;
            text-align: left;
            border-left: 4px solid var(--info);
        }

        .instruction-box .title {
            font-weight: 600;
            color: var(--gray-800);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .instruction-box ul {
            margin: 0.3rem 0 0 1.5rem;
            color: var(--gray-600);
            font-size: 0.8rem;
            line-height: 1.8;
        }

        .instruction-box ul li {
            list-style-type: disc;
        }

        @media (max-width: 768px) {
            .qr-display-card {
                padding: 1.25rem;
            }

            .qr-display-card .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .qr-display-card .qr-wrapper {
                padding: 0.75rem;
            }

            .qr-display-card .info-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .manual-code {
                font-size: 1.3rem;
                letter-spacing: 4px;
            }

            .qr-display-card .actions {
                flex-direction: column;
            }

            .qr-display-card .actions .btn-action {
                justify-content: center;
                width: 100%;
            }
        }
    </style>

    <!-- Back Link -->
    <a href="{{ route('lecturer.semester-qr.management') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Semester QR Management
    </a>

    <!-- QR Display Card -->
    <div class="qr-display-card">
        <!-- Header -->
        <div class="header">
            <div class="course-info">
                <div class="name">{{ $qr->course->course_name ?? 'Unknown Course' }}</div>
                <div class="code">{{ $qr->course->course_code ?? 'N/A' }}</div>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
                <span class="badge-semester"><i class="bi bi-infinity"></i> Semester</span>
                @if ($qr->status == 'active')
                    <span class="badge-no-expiry"><i class="bi bi-check-circle"></i> No Expiry</span>
                @endif
                <span class="status-badge {{ $qr->status == 'active' ? 'active' : 'ended' }}">
                    {{ $qr->status == 'active' ? 'Active' : 'Deactivated' }}
                </span>
            </div>
        </div>

        <!-- QR Code (Using Base64 from Controller) -->
        <div class="qr-wrapper">
            <img src="{{ $qrImageBase64 }}" alt="Semester QR Code for {{ $qr->course->course_name }}">
        </div>

        <!-- Manual Code -->
        <div>
            <div style="font-size: 0.75rem; color: var(--gray-500); margin-bottom: 0.2rem;">
                <i class="bi bi-keyboard"></i> Manual Entry Code
            </div>
            <div class="manual-code">{{ $qr->manual_code }}</div>
            <div style="font-size: 0.65rem; color: var(--gray-400); margin-top: 0.2rem;">
                <i class="bi bi-info-circle"></i> Students can enter this code manually if they can't scan
            </div>
        </div>

        <!-- Info Grid -->
        <div class="info-grid">
            <div class="item">
                <div class="label"><i class="bi bi-calendar3"></i> Created</div>
                <div class="value">{{ $qr->created_at ? $qr->created_at->format('M d, Y h:i A') : 'N/A' }}</div>
            </div>
            <div class="item">
                <div class="label"><i class="bi bi-door-open"></i> Room</div>
                <div class="value">{{ $qr->room ?? 'Not specified' }}</div>
            </div>
            <div class="item">
                <div class="label"><i class="bi bi-people"></i> Enrolled Students</div>
                <div class="value">{{ $qr->total_students ?? 0 }}</div>
            </div>
            <div class="item">
                <div class="label"><i class="bi bi-check-circle"></i> Status</div>
                <div class="value" style="color: {{ $qr->status == 'active' ? 'var(--success)' : 'var(--gray-500)' }};">
                    {{ $qr->status == 'active' ? 'Active - Students can scan' : 'Deactivated - No longer active' }}
                </div>
            </div>
            <div class="item" style="grid-column: 1 / -1;">
                <div class="label"><i class="bi bi-clock-history"></i> Scan Count</div>
                <div class="value">{{ $qr->records->count() }} total scans recorded</div>
            </div>
            <div class="item" style="grid-column: 1 / -1;">
                <div class="label"><i class="bi bi-infinity"></i> Valid Until</div>
                <div class="value" style="color: var(--success);">
                    @if ($qr->status == 'active')
                        <i class="bi bi-check-circle"></i> No expiry - active until you deactivate
                    @else
                        <i class="bi bi-stop-circle"></i> Deactivated on
                        {{ $qr->ended_at ? $qr->ended_at->format('M d, Y') : 'N/A' }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Instructions -->
        {{-- <div class="instruction-box">
            <div class="title"><i class="bi bi-info-circle"></i> How to Use This QR</div>
            <ul>
                <li>Display this QR code on your screen or print it for students to scan</li>
                <li>Students can scan with their phone camera or enter the manual code</li>
                <li>Each student can scan <strong>once per day</strong> for attendance</li>
                @if ($qr->status == 'active')
                    <li><span style="color: var(--success);"><i class="bi bi-check-circle"></i></span> QR is
                        <strong>ACTIVE</strong> - students can scan
                    </li>
                @else
                    <li><span style="color: var(--danger);"><i class="bi bi-x-circle"></i></span> QR is
                        <strong>DEACTIVATED</strong> - students cannot scan
                    </li>
                @endif
            </ul>
        </div> --}}

        <!-- Actions -->
        <div class="actions">
            <!-- 🟢 Download Button uses a direct route that returns an actual image file -->
            <a href="{{ $downloadUrl }}" class="btn-action download">
                <i class="bi bi-download"></i> Download QR Code
            </a>
            <a href="{{ route('lecturer.attendance.take') }}?session={{ $qr->id }}" class="btn-action primary">
                <i class="bi bi-eye"></i> View in Attendance Page
            </a>
            @if ($qr->status == 'active')
                <form method="POST" action="{{ route('lecturer.semester-qr.end', $qr->id) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-action danger"
                        onclick="return confirm('Are you sure you want to deactivate this Semester QR?\n\nStudents will no longer be able to scan it.\nAll attendance records will be preserved.')">
                        <i class="bi bi-stop-circle"></i> Deactivate QR
                    </button>
                </form>
            @endif
            <a href="{{ route('lecturer.semester-qr.management') }}" class="btn-action back">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
@endsection
