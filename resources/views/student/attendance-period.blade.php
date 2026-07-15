@extends('layouts.app')

@section('title', 'Attendance - ' . ucfirst($period))
@section('role', 'Student')
@section('page-title', '📋 ' . ucfirst($period) . ' Attendance')
@section('welcome-text', 'Period-based tracking: ' . $start->format('M d, Y') . ' – ' . $end->format('M d, Y'))

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        .period-tabs .btn {
            border-radius: 20px;
            padding: 5px 18px;
            font-weight: 600;
            font-size: 13px;
        }

        .period-tabs .btn-primary {
            background: #0A2463;
            border-color: #0A2463;
        }

        .period-tabs .btn-outline-secondary {
            color: #6b7280;
            border-color: #d1d5db;
        }

        .period-tabs .btn-outline-secondary:hover {
            background: #f3f4f6;
        }

        .elig-badge {
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .elig-eligible {
            background: #d1fae5;
            color: #065f46;
        }

        .elig-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .elig-not_eligible {
            background: #fee2e2;
            color: #991b1b;
        }

        .overall-card {
            background: white;
            border-radius: 12px;
            padding: 16px 24px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .overall-card .big-number {
            font-size: 32px;
            font-weight: 800;
            color: #0A2463;
        }

        .table-wrap {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }

        .table-wrap table {
            margin-bottom: 0;
        }

        .table-wrap th {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
        }

        .progress-bar-sm {
            height: 6px;
            border-radius: 10px;
            background: #e5e7eb;
            overflow: hidden;
            width: 100px;
            display: inline-block;
            vertical-align: middle;
        }

        .progress-bar-sm .fill {
            height: 100%;
            border-radius: 10px;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="period-tabs">
            <a href="?period=weekly" class="btn {{ $period == 'weekly' ? 'btn-primary' : 'btn-outline-secondary' }}">📅 This
                Week</a>
            <a href="?period=monthly" class="btn {{ $period == 'monthly' ? 'btn-primary' : 'btn-outline-secondary' }}">📅 This
                Month</a>
        </div>
        <a href="{{ route('student.attendance') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Semester View
        </a>
    </div>

    <div class="overall-card mb-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <span class="text-muted" style="font-size:14px;">Overall Attendance</span>
                <div class="big-number">{{ $overall }}%</div>
                <span class="text-muted" style="font-size:12px;">
                    {{ array_sum(array_column($courseData, 'attended')) }} /
                    {{ array_sum(array_column($courseData, 'total')) }} periods
                </span>
            </div>
            <div class="col-md-8">
                <div class="progress-bar-sm" style="width:100%; height:8px;">
                    <div class="fill"
                        style="width:{{ $overall }}%; background: {{ $overall >= 75 ? '#10b981' : ($overall >= 60 ? '#f59e0b' : '#ef4444') }};">
                    </div>
                </div>
                <div class="d-flex justify-content-between mt-1 text-muted" style="font-size:12px;">
                    <span>0%</span>
                    <span>Target: 75%</span>
                    <span>100%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="table-wrap">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Attended</th>
                    <th>Total Periods</th>
                    <th>Attendance</th>
                    <th>Eligibility</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courseData as $data)
                    @php
                        $att = $data['attendance'];
                        $color = $att >= 75 ? '#10b981' : ($att >= 60 ? '#f59e0b' : '#ef4444');
                        $elig = $data['eligibility'];
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $data['course']->course_code }}</strong>
                            <br><small class="text-muted">{{ $data['course']->course_name }}</small>
                        </td>
                        <td>{{ $data['attended'] }}</td>
                        <td>{{ $data['total'] }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-weight:700; min-width:45px;">{{ $att }}%</span>
                                <div class="progress-bar-sm">
                                    <div class="fill"
                                        style="width:{{ $att }}%; background:{{ $color }};"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="elig-badge elig-{{ $elig }}">
                                {{ ucfirst(str_replace('_', ' ', $elig)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            No courses or no sessions conducted in this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
