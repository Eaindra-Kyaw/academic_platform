{{-- resources/views/admin/announcements/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Announcement')
@section('role', 'Admin')
@section('page-title', '📢 Create Announcement')
@section('welcome-text', 'Create a new announcement')

@section('sidebar')
    @include('layouts.partials.admin-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #3B82F6;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --danger: #ef4444;
            --success: #10b981;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1.5rem;
            padding: 0.5rem 1rem;
            background: rgba(212, 160, 23, 0.08);
            border-radius: var(--radius);
            border: 1px solid rgba(212, 160, 23, 0.15);
            transition: var(--transition);
        }

        .back-link:hover {
            background: rgba(212, 160, 23, 0.15);
            text-decoration: none;
            color: var(--primary-dark);
        }

        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 70vh;
            padding: 1rem 0;
        }

        .form-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem 2.5rem;
            max-width: 700px;
            width: 100%;
            box-shadow: var(--shadow);
        }

        .form-card .form-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.25rem 0;
        }

        .form-card .form-title i {
            color: var(--primary);
        }

        .form-card .form-subtitle {
            font-size: 0.8rem;
            color: var(--text-gray);
            margin: 0 0 1.5rem 0;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-dark);
            display: block;
            margin-bottom: 0.3rem;
        }

        .form-group label .required {
            color: var(--danger);
            margin-left: 0.1rem;
        }

        .form-group .form-control {
            width: 100%;
            padding: 0.6rem 0.8rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 8px;
            font-size: 0.85rem;
            transition: var(--transition);
            background: #fafbfc;
            font-family: 'Inter', sans-serif;
        }

        .form-group .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .form-group .form-control.error {
            border-color: var(--danger);
            background: #fef2f2;
        }

        .form-group .help-text {
            font-size: 0.7rem;
            color: var(--text-gray);
            margin-top: 0.3rem;
        }

        .form-group .help-text i {
            color: var(--secondary);
        }

        .form-group .error-text {
            font-size: 0.7rem;
            color: var(--danger);
            margin-top: 0.3rem;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            background: #fafbfc;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border: 1px solid rgba(10, 36, 99, 0.1);
        }

        .checkbox-group .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.3rem 0.5rem;
            border-radius: 6px;
            transition: var(--transition);
        }

        .checkbox-group .checkbox-item:hover {
            background: rgba(10, 36, 99, 0.04);
        }

        .checkbox-group .checkbox-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .checkbox-group .checkbox-item label {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-dark);
            margin: 0;
            cursor: pointer;
        }

        .checkbox-group .checkbox-item .role-icon {
            font-size: 0.9rem;
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            padding: 0.6rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
        }

        .btn-cancel {
            background: #f3f4f6;
            color: var(--text-dark);
            padding: 0.6rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .alert {
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            margin-bottom: 1rem;
            font-size: 0.85rem;
        }

        .alert-success {
            background: #ecfdf5;
            color: #166534;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fca5a5;
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
                <i class="bi bi-megaphone"></i> Create New Announcement
            </h2>
            <p class="form-subtitle">
                Fill in the details below to create a new announcement
            </p>

            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf

                {{-- Title --}}
                <div class="form-group">
                    <label for="title">Title <span class="required">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') error @enderror"
                        value="{{ old('title') }}" placeholder="Enter announcement title" required>
                    @error('title')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Content --}}
                <div class="form-group">
                    <label for="content">Content <span class="required">*</span></label>
                    <textarea name="content" id="content" class="form-control @error('content') error @enderror" rows="6"
                        placeholder="Enter announcement content" required>{{ old('content') }}</textarea>
                    @error('content')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                    <div class="help-text">
                        <i class="bi bi-info-circle"></i> Minimum 10 characters
                    </div>
                </div>

                {{-- Target Audience --}}
                <div class="form-group">
                    <label>Target Audience <span class="required">*</span></label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="target_roles[]" id="role_all" value="all"
                                {{ in_array('all', old('target_roles', [])) ? 'checked' : '' }}
                                onclick="toggleAllRoles(this)">
                            <label for="role_all">
                                <span class="role-icon">👥</span> All Users
                            </label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="target_roles[]" id="role_admin" value="admin"
                                class="role-checkbox" {{ in_array('admin', old('target_roles', [])) ? 'checked' : '' }}>
                            <label for="role_admin">
                                <span class="role-icon">👤</span> Admins
                            </label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="target_roles[]" id="role_lecturer" value="lecturer"
                                class="role-checkbox" {{ in_array('lecturer', old('target_roles', [])) ? 'checked' : '' }}>
                            <label for="role_lecturer">
                                <span class="role-icon">👨‍🏫</span> Lecturers
                            </label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="target_roles[]" id="role_student" value="student"
                                class="role-checkbox" {{ in_array('student', old('target_roles', [])) ? 'checked' : '' }}>
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
                        class="form-control @error('published_at') error @enderror" value="{{ old('published_at') }}">
                    @error('published_at')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                    <div class="help-text">
                        <i class="bi bi-clock"></i> Leave empty to publish immediately
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-send"></i> Publish Announcement
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
