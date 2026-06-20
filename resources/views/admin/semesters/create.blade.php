{{-- resources/views/admin/semesters/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Semester')
@section('role', 'Admin')
@section('page-title', '📅 Create Semester')
@section('welcome-text', 'Create a new academic semester')

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

        .form-group .error-text {
            font-size: 0.7rem;
            color: #ef4444;
            margin-top: 0.3rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .checkbox-group {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            padding: 0.5rem 0;
        }

        .checkbox-group .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
                justify-content: center;
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

    <a href="{{ route('admin.semesters.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Semesters
    </a>

    <div class="form-wrapper">
        <div class="form-card">
            <h2 class="form-title">
                <i class="bi bi-calendar-plus" style="color:#800000;"></i> Create New Semester
            </h2>
            <p class="form-subtitle">
                Create a new academic semester
            </p>

            <form action="{{ route('admin.semesters.store') }}" method="POST">
                @csrf

                {{-- Year and Semester --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Year <span class="required">*</span></label>
                        <select name="year" id="year" class="form-control @error('year') error @enderror" required>
                            <option value="">Select Year</option>
                            <option value="1" {{ old('year') == 1 ? 'selected' : '' }}>First Year</option>
                            <option value="2" {{ old('year') == 2 ? 'selected' : '' }}>Second Year</option>
                            <option value="3" {{ old('year') == 3 ? 'selected' : '' }}>Third Year</option>
                            <option value="4" {{ old('year') == 4 ? 'selected' : '' }}>Fourth Year</option>
                            <option value="5" {{ old('year') == 5 ? 'selected' : '' }}>Fifth Year</option>
                            <option value="6" {{ old('year') == 6 ? 'selected' : '' }}>Sixth Year</option>
                        </select>
                        @error('year')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="semester">Semester <span class="required">*</span></label>
                        <select name="semester" id="semester" class="form-control @error('semester') error @enderror"
                            required>
                            <option value="">Select Semester</option>
                            <option value="1" {{ old('semester') == 1 ? 'selected' : '' }}>First Semester</option>
                            <option value="2" {{ old('semester') == 2 ? 'selected' : '' }}>Second Semester</option>
                        </select>
                        @error('semester')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Academic Year --}}
                <div class="form-group">
                    <label for="academic_year">Academic Year</label>
                    <input type="text" name="academic_year" id="academic_year"
                        class="form-control @error('academic_year') error @enderror"
                        value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}"
                        placeholder="e.g., 2025-2026">
                    @error('academic_year')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                    <div class="help-text">
                        <i class="bi bi-info-circle"></i> Example: 2025-2026
                    </div>
                </div>

                {{-- Start and End Date --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date"
                            class="form-control @error('start_date') error @enderror" value="{{ old('start_date') }}">
                        @error('start_date')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date"
                            class="form-control @error('end_date') error @enderror" value="{{ old('end_date') }}">
                        @error('end_date')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label>Status</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active">Active</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="is_current" id="is_current" value="1"
                                {{ old('is_current') ? 'checked' : '' }}>
                            <label for="is_current">Set as Current Semester</label>
                        </div>
                    </div>
                    <div class="help-text">
                        <i class="bi bi-info-circle"></i> Only one semester can be current at a time
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-save"></i> Create Semester
                    </button>
                    <a href="{{ route('admin.semesters.index') }}" class="btn-cancel">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
