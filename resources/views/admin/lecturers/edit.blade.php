@extends('layouts.app')

@section('title', 'Edit ' . $lecturer->name)
@section('page-title', 'Edit Lecturer')
@section('welcome-text', $lecturer->name)

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <div style="max-width:600px; margin:0 auto;">
        <!-- Back Link - Goes back to previous page -->
        <a href="javascript:history.back()"
            style="display:inline-flex; align-items:center; gap:0.5rem; color:#6b7a8f; text-decoration:none; margin-bottom:1.5rem; font-size:0.85rem; padding:0.3rem 0.8rem; background:white; border:1px solid #e9edf4; border-radius:0.5rem; transition:all 0.2s;">
            <i class="bi bi-arrow-left"></i> Back to Profile
        </a>

        <!-- Form -->
        <div
            style="background:white; border-radius:0.75rem; border:1px solid #e9edf4; padding:2rem; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <form method="POST" action="{{ route('admin.lecturers.update', $lecturer) }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:1rem;">
                    <label
                        style="display:block; font-weight:600; font-size:0.8rem; color:#1a2332; margin-bottom:0.2rem;">Full
                        Name *</label>
                    <input type="text" name="name" value="{{ $lecturer->name }}" required
                        style="width:100%; padding:0.5rem; border:1px solid #e9edf4; border-radius:0.4rem; font-size:0.85rem;">
                    @error('name')
                        <span style="color:#ef4444; font-size:0.7rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom:1rem;">
                    <label
                        style="display:block; font-weight:600; font-size:0.8rem; color:#1a2332; margin-bottom:0.2rem;">Email
                        *</label>
                    <input type="email" name="email" value="{{ $lecturer->email }}" required
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
                            <option value="{{ $dept->id }}"
                                {{ $lecturer->department_id == $dept->id ? 'selected' : '' }}>
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
                        style="display:flex; align-items:center; gap:0.5rem; font-weight:600; font-size:0.8rem; color:#1a2332;">
                        <input type="checkbox" name="is_active" value="1" {{ $lecturer->is_active ? 'checked' : '' }}>
                        Active
                    </label>
                </div>

                <button type="submit"
                    style="background:#800000; color:white; border:none; padding:0.5rem 1.5rem; border-radius:0.4rem; font-size:0.85rem; cursor:pointer; width:100%;">
                    <i class="bi bi-save"></i> Update Lecturer
                </button>
            </form>
        </div>
    </div>
@endsection
