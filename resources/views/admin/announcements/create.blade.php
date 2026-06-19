{{-- resources/views/admin/announcements/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Announcement')
@section('role', 'Admin')
@section('page-title', '📢 Edit Announcement')
@section('welcome-text', 'Update announcement details')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #800000;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: #fef7f7;
            border-radius: 0.5rem;
            border: 1px solid #fde2e2;
            transition: all 0.2s;
        }

        .back-link:hover {
            background: #fde2e2;
            text-decoration: none;
            color: #800000;
        }

        .form-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 2rem;
            max-width: 800px;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            display: block;
            margin-bottom: 0.3rem;
        }

        .form-group .form-control {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .form-group .form-control:focus {
            outline: none;
            border-color: #800000;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .form-group .form-control.error {
            border-color: #ef4444;
        }

        .form-group .help-text {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.2rem;
        }

        .form-group .error-text {
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 0.2rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn-submit {
            background: #800000;
            color: white;
            padding: 0.6rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #5f0000;
            transform: translateY(-1px);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
            padding: 0.6rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .status-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .status-toggle input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #800000;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 1.25rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn-submit,
            .btn-cancel {
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <a href="{{ route('admin.announcements.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Announcements
    </a>

    <div class="form-card">
        <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div class="form-group">
                <label for="title">Title <span style="color:#ef4444;">*</span></label>
                <input type="text" name="title" id="title" class="form-control @error('title') error @enderror"
                    value="{{ old('title', $announcement->title) }}" placeholder="Enter announcement title" required>
                @error('title')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Content --}}
            <div class="form-group">
                <label for="content">Content <span style="color:#ef4444;">*</span></label>
                <textarea name="content" id="content" class="form-control @error('content') error @enderror" rows="6"
                    placeholder="Enter announcement content" required>{{ old('content', $announcement->content) }}</textarea>
                @error('content')
                    <div class="error-text">{{ $message }}</div>
                @enderror
                <div class="help-text">Minimum 10 characters</div>
            </div>

            {{-- Target Audience --}}
            <div class="form-group">
                <label for="target_role">Target Audience <span style="color:#ef4444;">*</span></label>
                <select name="target_role" id="target_role" class="form-control @error('target_role') error @enderror"
                    required>
                    <option value="all" {{ old('target_role', $announcement->target_role) == 'all' ? 'selected' : '' }}>
                        All Users</option>
                    <option value="admin"
                        {{ old('target_role', $announcement->target_role) == 'admin' ? 'selected' : '' }}>Admins Only
                    </option>
                    <option value="lecturer"
                        {{ old('target_role', $announcement->target_role) == 'lecturer' ? 'selected' : '' }}>Lecturers Only
                    </option>
                    <option value="student"
                        {{ old('target_role', $announcement->target_role) == 'student' ? 'selected' : '' }}>Students Only
                    </option>
                </select>
                @error('target_role')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Published At --}}
            <div class="form-group">
                <label for="published_at">Publish Date</label>
                <input type="datetime-local" name="published_at" id="published_at"
                    class="form-control @error('published_at') error @enderror"
                    value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}">
                @error('published_at')
                    <div class="error-text">{{ $message }}</div>
                @enderror
                <div class="help-text">Leave empty to keep current publish date</div>
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label>Status</label>
                <div class="status-toggle">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                    <label for="is_active" style="margin:0; cursor:pointer; font-weight:normal;">
                        <i class="bi {{ $announcement->is_active ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                        {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                    </label>
                </div>
                <div class="help-text">Uncheck to deactivate this announcement</div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-save"></i> Update Announcement
                </button>
                <a href="{{ route('admin.announcements.index') }}" class="btn-cancel">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
