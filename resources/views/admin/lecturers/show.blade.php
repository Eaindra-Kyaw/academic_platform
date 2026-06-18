@extends('layouts.app')

@section('title', $lecturer->name)
@section('page-title', $lecturer->name)
@section('welcome-text', 'Lecturer Profile')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <div style="max-width:1000px; margin:0 auto;">
        <!-- Back Link - Goes back to previous page (Department page) -->
        <a href="javascript:history.back()"
            style="display:inline-flex; align-items:center; gap:0.5rem; color:#6b7a8f; text-decoration:none; margin-bottom:1.5rem; font-size:0.85rem; padding:0.3rem 0.8rem; background:white; border:1px solid #e9edf4; border-radius:0.5rem; transition:all 0.2s;">
            <i class="bi bi-arrow-left"></i> Back to Lecturers
        </a>

        <!-- Lecturer Profile Card -->
        <div
            style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; padding:2rem; margin-bottom:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <div style="display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap;">
                <div
                    style="width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg, #800000, #a00000); color:white; display:flex; align-items:center; justify-content:center; font-size:2rem; font-weight:700; flex-shrink:0;">
                    {{ substr($lecturer->name, 0, 2) }}
                </div>
                <div style="flex:1;">
                    <h2 style="font-size:1.5rem; font-weight:700; color:#1a2332; margin:0;">{{ $lecturer->name }}</h2>
                    <p style="color:#6b7a8f; font-size:0.9rem; margin:0.2rem 0;">
                        <i class="bi bi-envelope"></i> {{ $lecturer->email }}
                    </p>
                    <p style="color:#6b7a8f; font-size:0.85rem; margin:0;">
                        <i class="bi bi-building"></i> {{ $lecturer->department->name ?? 'No Department' }}
                    </p>
                </div>
                <div style="display:flex; gap:0.5rem;">
                    <a href="{{ route('admin.lecturers.edit', $lecturer) }}"
                        style="background:#fef3c7; color:#92400e; padding:0.4rem 1rem; border-radius:0.4rem; text-decoration:none; font-size:0.8rem;">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1rem; margin-bottom:1.5rem;">
            <div style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:1rem; text-align:center;">
                <div style="font-size:1.8rem; font-weight:700; color:#800000;">{{ $stats['total_courses'] }}</div>
                <div style="font-size:0.7rem; color:#6b7a8f; text-transform:uppercase;">Courses Taught</div>
            </div>
            <div style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:1rem; text-align:center;">
                <div style="font-size:1.8rem; font-weight:700; color:#10b981;">{{ $stats['total_students'] }}</div>
                <div style="font-size:0.7rem; color:#6b7a8f; text-transform:uppercase;">Total Students</div>
            </div>
            <div style="background:white; border-radius:0.5rem; border:1px solid #e9edf4; padding:1rem; text-align:center;">
                <div style="font-size:1.8rem; font-weight:700; color:#f59e0b;">
                    {{ number_format($stats['avg_attendance'], 1) }}%</div>
                <div style="font-size:0.7rem; color:#6b7a8f; text-transform:uppercase;">Avg Attendance</div>
            </div>
        </div>

        <!-- Courses Table -->
        <div style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; overflow:hidden;">
            <div
                style="padding:1rem 1.25rem; background:#fafbfc; border-bottom:1px solid #e9edf4; font-weight:600; color:#1a2332;">
                <i class="bi bi-book" style="color:#800000;"></i> Courses Taught
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
                                Students</th>
                            <th
                                style="padding:0.5rem 1rem; text-align:center; font-weight:600; color:#6b7a8f; font-size:0.6rem; text-transform:uppercase;">
                                Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr style="border-top:1px solid #f1f5f9;">
                                <td style="padding:0.5rem 1rem;">
                                    <div>
                                        <div style="font-weight:600; color:#1a2332;">{{ $course->course_code }}</div>
                                        <div style="font-size:0.7rem; color:#6b7a8f;">{{ $course->course_name }}</div>
                                    </div>
                                </td>
                                <td style="padding:0.5rem 1rem; color:#6b7a8f;">{{ $course->department->name ?? 'N/A' }}
                                </td>
                                <td style="padding:0.5rem 1rem; text-align:center; font-weight:600; color:#1a2332;">
                                    {{ $course->students()->count() }}</td>
                                <td style="padding:0.5rem 1rem; text-align:center;">
                                    <span
                                        style="font-weight:600; color:{{ $course->average_attendance >= 75 ? '#10b981' : ($course->average_attendance >= 60 ? '#f59e0b' : '#ef4444') }};">
                                        {{ number_format($course->average_attendance, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="padding:2rem; text-align:center; color:#9ca3af;">No courses
                                    assigned</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
