@extends('layouts.app')

@section('title', $student->name)
@section('page-title', 'Student Profile')
@section('welcome-text', $student->student_id ?? 'No ID')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <div style="max-width:1000px; margin:0 auto;">
        <a href="{{ url()->previous() }}"
            style="display:inline-flex; align-items:center; gap:0.5rem; color:#6b7a8f; text-decoration:none; margin-bottom:1.5rem; font-size:0.85rem;">
            <i class="bi bi-arrow-left"></i> Back
        </a>

        <div style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; padding:2rem; margin-bottom:1.5rem;">
            <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;">
                <div
                    style="width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg, #800000, #a00000); color:white; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:700;">
                    {{ substr($student->name, 0, 2) }}
                </div>
                <div>
                    <h2 style="font-size:1.3rem; font-weight:700; color:#1a2332; margin:0;">{{ $student->name }}</h2>
                    <p style="color:#6b7a8f; font-size:0.85rem; margin:0.2rem 0;">
                        <i class="bi bi-envelope"></i> {{ $student->email }}
                    </p>
                    <p style="color:#6b7a8f; font-size:0.85rem; margin:0;">
                        <i class="bi bi-building"></i> {{ $student->department->name ?? 'No Department' }}
                        <span style="margin:0 0.5rem;">|</span>
                        <i class="bi bi-mortarboard"></i> {{ $student->getYearLabelAttribute() }}
                    </p>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1rem; margin-bottom:1.5rem;">
            <div style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:1rem; text-align:center;">
                <div style="font-size:1.5rem; font-weight:700; color:#800000;">
                    {{ $student->enrollments->where('status', 'approved')->count() }}</div>
                <div style="font-size:0.7rem; color:#6b7a8f; text-transform:uppercase;">Enrolled Courses</div>
            </div>
            <div style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:1rem; text-align:center;">
                <div style="font-size:1.5rem; font-weight:700; color:#10b981;">
                    {{ number_format($student->enrollments->avg('attendance_percentage') ?? 0, 1) }}%</div>
                <div style="font-size:0.7rem; color:#6b7a8f; text-transform:uppercase;">Avg Attendance</div>
            </div>
            <div style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:1rem; text-align:center;">
                <div style="font-size:1.5rem; font-weight:700; color:#f59e0b;">{{ $student->student_id ?? 'N/A' }}</div>
                <div style="font-size:0.7rem; color:#6b7a8f; text-transform:uppercase;">Student ID</div>
            </div>
        </div>

        <div style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; overflow:hidden;">
            <div
                style="padding:1rem 1.25rem; background:#fafbfc; border-bottom:1px solid #e9edf4; font-weight:600; color:#1a2332;">
                <i class="bi bi-book" style="color:#800000;"></i> Enrolled Courses
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:0.8rem;">
                    <thead>
                        <tr style="background:#f8f9fc;">
                            <th
                                style="padding:0.5rem 1rem; text-align:left; font-weight:600; color:#6b7a8f; font-size:0.6rem; text-transform:uppercase;">
                                Course</th>
                            <th
                                style="padding:0.5rem 1rem; text-align:left; font-weight:600; color:#6b7a8f; font-size:0.6rem; text-transform:uppercase;">
                                Department</th>
                            <th
                                style="padding:0.5rem 1rem; text-align:center; font-weight:600; color:#6b7a8f; font-size:0.6rem; text-transform:uppercase;">
                                Attendance</th>
                            <th
                                style="padding:0.5rem 1rem; text-align:center; font-weight:600; color:#6b7a8f; font-size:0.6rem; text-transform:uppercase;">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->enrollments->where('status', 'approved') as $enrollment)
                            <tr style="border-top:1px solid #f1f5f9;">
                                <td style="padding:0.5rem 1rem;">
                                    <div style="font-weight:600; color:#1a2332;">{{ $enrollment->course->course_code }}
                                    </div>
                                    <div style="font-size:0.7rem; color:#6b7a8f;">{{ $enrollment->course->course_name }}
                                    </div>
                                </td>
                                <td style="padding:0.5rem 1rem; color:#6b7a8f;">
                                    {{ $enrollment->course->department->code ?? 'N/A' }}</td>
                                <td style="padding:0.5rem 1rem; text-align:center;">
                                    <span
                                        style="font-weight:600; color:{{ ($enrollment->attendance_percentage ?? 0) >= 75 ? '#10b981' : (($enrollment->attendance_percentage ?? 0) >= 60 ? '#f59e0b' : '#ef4444') }};">
                                        {{ number_format($enrollment->attendance_percentage ?? 0, 1) }}%
                                    </span>
                                </td>
                                <td style="padding:0.5rem 1rem; text-align:center;">
                                    <span
                                        style="padding:0.1rem 0.6rem; border-radius:1rem; font-size:0.6rem; font-weight:600;
                                    background:{{ $enrollment->eligibility_status == 'eligible' ? '#ecfdf5' : ($enrollment->eligibility_status == 'warning' ? '#fffbeb' : '#fef2f2') }};
                                    color:{{ $enrollment->eligibility_status == 'eligible' ? '#10b981' : ($enrollment->eligibility_status == 'warning' ? '#f59e0b' : '#ef4444') }};">
                                        {{ ucfirst($enrollment->eligibility_status ?? 'Unknown') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:2rem; text-align:center; color:#9ca3af;">No courses
                                    enrolled</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
