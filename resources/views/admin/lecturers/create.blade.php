@extends('layouts.app')

@section('title', 'Add Lecturer')
@section('page-title', 'Add New Lecturer')
@section('welcome-text', 'Create a new faculty member')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <div style="max-width:600px; margin:0 auto;">
        <!-- Back Link -->
        <a href="{{ route('admin.lecturers.index') }}"
            style="display:inline-flex; align-items:center; gap:0.5rem; color:#6b7a8f; text-decoration:none; margin-bottom:1.5rem; font-size:0.85rem;">
            <i class="bi bi-arrow-left"></i> Back to Lecturers
        </a>

        <!-- Form -->
        <div
            style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; padding:2rem; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <form method="POST" action="{{ route('admin.lecturers.store') }}">
                @csrf

                <div style="margin-bottom:1rem;">
                    <label
                        style="display:block; font-weight:600; font-size:0.8rem; color:#1a2332; margin-bottom:0.2rem;">Full
                        Name *</label>
                    <input type="text" name="name" required
                        style="width:100%; padding:0.5rem; border:1px solid #e9edf4; border-radius:0.4rem; font-size:0.85rem;">
                    @error('name')
                        <span style="color:#ef4444; font-size:0.7rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:1rem;">
                    <label
                        style="display:block; font-weight:600; font-size:0.8rem; color:#1a2332; margin-bottom:0.2rem;">Email
                        *</label>
                    <input type="email" name="email" required
                        style="width:100%; padding:0.5rem; border:1px solid #e9edf4; border-radius:0.4rem; font-size:0.85rem;">
                    @error('email')
                        <span style="color:#ef4444; font-size:0.7rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:1rem;">
                    <label
                        style="display:block; font-weight:600; font-size:0.8rem; color:#1a2332; margin-bottom:0.2rem;">Department</label>
                    <select name="department_id"
                        style="width:100%; padding:0.5rem; border:1px solid #e9edf4; border-radius:0.4rem; font-size:0.85rem;">
                        <option value="">Select Department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span style="color:#ef4444; font-size:0.7rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:1.5rem;">
                    <label
                        style="display:block; font-weight:600; font-size:0.8rem; color:#1a2332; margin-bottom:0.2rem;">Password
                        *</label>
                    <input type="password" name="password" required minlength="8"
                        style="width:100%; padding:0.5rem; border:1px solid #e9edf4; border-radius:0.4rem; font-size:0.85rem;">
                    <small style="color:#6b7a8f; font-size:0.65rem;">Minimum 8 characters</small>
                    @error('password')
                        <span style="color:#ef4444; font-size:0.7rem;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    style="background:#800000; color:white; border:none; padding:0.5rem 1.5rem; border-radius:0.4rem; font-size:0.85rem; cursor:pointer; width:100%;">
                    <i class="bi bi-plus-circle"></i> Create Lecturer
                </button>
            </form>
        </div>
    </div>
@endsection
