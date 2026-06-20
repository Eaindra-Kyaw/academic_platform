@extends('layouts.app')

@section('title', 'Attendance Analytics')
@section('role', 'Admin')
@section('page-title', '📊 Attendance Analytics')
@section('welcome-text', 'Coming Soon')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <div style="display:flex; justify-content:center; align-items:center; min-height:60vh;">
        <div style="text-align:center; max-width:500px;">
            <div style="font-size:4rem; margin-bottom:1rem;">📊</div>
            <h2 style="color:#1f2937; font-weight:700; margin-bottom:0.5rem;">Attendance Analytics</h2>
            <p style="color:#6b7280; font-size:0.95rem; margin-bottom:1.5rem;">
                This feature is currently under development.
                <br>Check back soon for detailed attendance insights!
            </p>
            <div style="display:flex; gap:0.75rem; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('admin.dashboard') }}"
                    style="background:#800000; color:white; padding:0.5rem 1.5rem; border-radius:0.5rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem;">
                    <i class="bi bi-house"></i> Go to Dashboard
                </a>
                <a href="#" onclick="history.back(); return false;"
                    style="background:#f3f4f6; color:#374151; padding:0.5rem 1.5rem; border-radius:0.5rem; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem;">
                    <i class="bi bi-arrow-left"></i> Go Back
                </a>
            </div>
        </div>
    </div>
@endsection
