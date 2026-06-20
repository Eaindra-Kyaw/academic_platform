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
            margin-bottom: 1.5rem;
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

        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 70vh;
            padding: 1rem 0;
        }

        .form-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 2rem 2.5rem;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }

        .form-card .form-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 0.25rem 0;
        }

        .form-card .form-subtitle {
            font-size: 0.8rem;
            color: #6b7280;
            margin: 0 0 1.5rem 0;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f3f4f6;
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

        .form-group label .required {
            color: #ef4444;
            margin-left: 0.1rem;
        }

        .form-group .form-control {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            transition: all 0.2s;
            background: #fafafa;
        }

        .form-group .form-control:focus {
            outline: none;
            border-color: #800000;
            background: white;
            box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.08);
        }

        .form-group .form-control.error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .form-group .help-text {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.3rem;
        }

        .form-group .help-text i {
            font-size: 0.65rem;
        }

        .form-group .error-text {
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 0.3rem;
        }

        /* Multi-select checkbox styling */
        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            background: #fafafa;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .checkbox-group .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.3rem 0.5rem;
            border-radius: 0.3rem;
            transition: all 0.2s;
        }

        .checkbox-group .checkbox-item:hover {
            background: #f3f4f6;
        }

        .checkbox-group .checkbox-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #800000;
            cursor: pointer;
        }

        .checkbox-group .checkbox-item label {
            font-size: 0.8rem;
            font-weight: 500;
            color: #374151;
            margin: 0;
            cursor: pointer;
        }

        .checkbox-group .checkbox-item .role-icon {
            font-size: 0.9rem;
        }

        .status-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            background: #fafafa;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        .status-toggle input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #800000;
            cursor: pointer;
        }

        .status-toggle label {
            margin: 0;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
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
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background: #5f0000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(128, 0, 0, 0.25);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
            padding: 0.6rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .alert {
            padding: 0.6rem 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-dismissible {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-close-alert {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
            padding: 0 0.3rem;
            opacity: 0.7;
        }

        .btn-close-alert:hover {
            opacity: 1;
        }

        @media (max-width: 768px) {
            .checkbox-group {
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
                justify-content: center;
            }

            .form-wrapper {
                padding: 0.5rem 0;
                min-height: auto;
            }
        }
    </style>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <a href="{{ route('admin.announcements.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Announcements
    </a>

    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title">
                <i class="bi bi-pencil-square" style="color:#800000;"></i> Edit Announcement
            </h2>
            <p class="form-subtitle">
                Update the announcement details below
            </p>

            <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Title --}}
                <div class="form-group">
                    <label for="title">Title <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') error @enderror"
                        value="{{ old('title', $announcement->title) }}" placeholder="Enter announcement title" required>
                    @error('title')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Content --}}
                <div class="form-group">
                    <label for="content">Content <span class="required">*</span></label>
                    <textarea name="content" id="content" class="form-control @error('content') error @enderror" rows="6"
                        placeholder="Enter announcement content" required>{{ old('content', $announcement->content) }}</textarea>
                    @error('content')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                    <div class="help-text">
                        <i class="bi bi-info-circle"></i> Minimum 10 characters
                    </div>
                </div>

                {{-- Target Audience (Multi-select Checkboxes) --}}
                <div class="form-group">
                    <label>Target Audience <span class="required">*</span></label>
                    @php
                        $selectedRoles = explode(',', $announcement->target_role);
                    @endphp
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="target_roles[]" id="role_all" value="all"
                                {{ in_array('all', old('target_roles', $selectedRoles)) ? 'checked' : '' }}
                                onclick="toggleAllRoles(this)">
                            <label for="role_all">
                                <span class="role-icon">👥</span> All Users
                            </label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="target_roles[]" id="role_admin" value="admin"
                                class="role-checkbox"
                                {{ in_array('admin', old('target_roles', $selectedRoles)) ? 'checked' : '' }}>
                            <label for="role_admin">
                                <span class="role-icon">👤</span> Admins
                            </label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="target_roles[]" id="role_lecturer" value="lecturer"
                                class="role-checkbox"
                                {{ in_array('lecturer', old('target_roles', $selectedRoles)) ? 'checked' : '' }}>
                            <label for="role_lecturer">
                                <span class="role-icon">👨‍🏫</span> Lecturers
                            </label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="target_roles[]" id="role_student" value="student"
                                class="role-checkbox"
                                {{ in_array('student', old('target_roles', $selectedRoles)) ? 'checked' : '' }}>
                            <label for="role_student">
                                <span class="role-icon">👨‍🎓</span> Students
                            </label>
                        </div>
                    </div>
                    @error('target_roles')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                    <div class="help-text">
                        <i class="bi bi-info-circle"></i> Select one or more roles (or "All Users" for everyone)
                    </div>
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
                    <div class="help-text">
                        <i class="bi bi-clock"></i> Leave empty to keep current publish date
                    </div>
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label>Status</label>
                    <div class="status-toggle">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                        <label for="is_active">
                            <i class="bi {{ $announcement->is_active ? 'bi-eye' : 'bi-eye-slash' }}"></i>
                            {{ $announcement->is_active ? 'Active' : 'Inactive' }}
                        </label>
                    </div>
                    <div class="help-text">
                        <i class="bi bi-info-circle"></i> Uncheck to deactivate this announcement
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-save"></i> Update Announcement
                    </button>
                    <a href="{{ route('admin.announcements.index') }}" class="btn-cancel">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAllRoles(checkbox) {
            const roleCheckboxes = document.querySelectorAll('.role-checkbox');
            if (checkbox.checked) {
                roleCheckboxes.forEach(cb => cb.checked = false);
            }
        }

        document.querySelectorAll('.role-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const allCheckbox = document.getElementById('role_all');
                const checkedRoles = document.querySelectorAll('.role-checkbox:checked');
                if (checkedRoles.length > 0) {
                    allCheckbox.checked = false;
                }
            });
        });
    </script>
@endsection
