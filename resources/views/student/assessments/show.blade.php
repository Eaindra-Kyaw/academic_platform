@extends('layouts.app')

@section('title', 'Student Evaluation Sheet')
@section('role', 'Student')
@section('page-title', 'Student Evaluation Sheet')
@section('welcome-text', 'Provide feedback for your course')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --danger: #ef4444;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 0.85rem;
            padding: 0.3rem 0.8rem;
            background: var(--white);
            border: 1px solid rgba(10, 36, 99, 0.1);
            border-radius: 8px;
            transition: var(--transition);
            margin-bottom: 1.25rem;
        }

        .back-link:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
        }

        .form-container {
            max-width: 820px;
            margin: 0 auto;
        }

        .form-header {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(10, 36, 99, 0.06);
            text-align: center;
        }

        .form-header h2 {
            margin: 0 0 0.25rem 0;
            font-weight: 700;
            color: var(--primary);
            font-size: 1.4rem;
        }

        .form-header h3 {
            margin: 0;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1.1rem;
        }

        .form-header .subtitle {
            color: var(--text-gray);
            font-size: 0.85rem;
            margin: 0.5rem 0 0;
        }

        .form-header .meta-info {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .selection-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .selection-section .form-group {
            margin-bottom: 1rem;
        }

        .selection-section .form-group:last-child {
            margin-bottom: 0;
        }

        .selection-section label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
            margin-bottom: 0.3rem;
        }

        .selection-section label .required-star {
            color: var(--danger);
        }

        .selection-section select {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.5rem;
            font-size: 0.85rem;
            background: var(--white);
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .selection-section select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .auto-filled-lecturer {
            padding: 0.6rem 0.75rem;
            background: var(--bg-main);
            border-radius: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        /* ============================================================
                                           QUESTIONS SECTION - SINGLE COLUMN LAYOUT
                                           ============================================================ */
        .questions-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .questions-section .section-title {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--primary);
        }

        .question-item {
            padding: 1rem 0;
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
        }

        .question-item:last-child {
            border-bottom: none;
        }

        .question-item .question-text {
            font-weight: 500;
            font-size: 0.9rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }

        .question-item .question-text .q-number {
            display: inline-block;
            font-weight: 700;
            color: var(--primary);
            margin-right: 0.3rem;
        }

        .question-item .question-text .required-star {
            color: var(--danger);
            margin-left: 0.2rem;
        }

        /* Rating options - horizontal */
        .rating-group {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
            padding-left: 0.5rem;
        }

        .rating-option {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            cursor: pointer;
            padding: 0.2rem 0.4rem;
            border-radius: 0.3rem;
            transition: var(--transition);
        }

        .rating-option:hover {
            background: var(--bg-main);
        }

        .rating-option input[type="radio"] {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .rating-option label {
            font-size: 0.85rem;
            color: var(--text-dark);
            cursor: pointer;
            margin: 0;
            min-width: 20px;
            text-align: center;
        }

        .comments-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(10, 36, 99, 0.06);
        }

        .comments-section label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
            margin-bottom: 0.3rem;
        }

        .comments-section textarea {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.5rem;
            font-size: 0.85rem;
            resize: vertical;
            font-family: inherit;
            transition: var(--transition);
            min-height: 80px;
        }

        .comments-section textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .form-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .btn-submit {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
        }

        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: var(--text-dark);
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            font-family: 'Inter', sans-serif;
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
            background: var(--success-light);
            color: #166534;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: var(--danger-light);
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @media (max-width: 768px) {
            .form-header h2 {
                font-size: 1.1rem;
            }

            .form-header h3 {
                font-size: 0.95rem;
            }

            .question-item .question-text {
                font-size: 0.8rem;
            }

            .rating-group {
                gap: 0.8rem;
                padding-left: 0;
            }

            .rating-option label {
                font-size: 0.75rem;
                min-width: 16px;
            }

            .selection-section {
                padding: 1rem;
            }

            .questions-section {
                padding: 1rem;
            }

            .comments-section {
                padding: 1rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn-submit,
            .form-actions .btn-cancel {
                width: 100%;
                justify-content: center;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .rating-group {
                gap: 0.4rem;
            }

            .rating-option {
                padding: 0.1rem 0.2rem;
            }

            .rating-option input[type="radio"] {
                width: 13px;
                height: 13px;
            }

            .rating-option label {
                font-size: 0.7rem;
                min-width: 14px;
            }

            .form-header .meta-info {
                flex-direction: column;
                gap: 0.3rem;
                align-items: center;
            }
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            Please fix the following errors:
            <ul style="margin-top:0.3rem; margin-bottom:0; padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li style="font-size:0.8rem;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('student.assessments.index') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to Assessments
    </a>

    <div class="form-container">
        {{-- Header --}}
        <div class="form-header">
            <h2>Mandalay Technological University</h2>
            <h3>Student Evaluation Sheet</h3>
            <p class="subtitle">Please provide your honest feedback for this course and lecturer.</p>
            <div class="meta-info">
                <span><strong>Year:</strong> {{ $assessment->year ?? 'N/A' }}</span>
                <span><strong>Semester:</strong> {{ $assessment->semester ?? 'N/A' }}</span>
                @if ($assessment->course)
                    <span><strong>Course:</strong> {{ $assessment->course->course_code ?? '' }}</span>
                @endif
                {{-- <span><strong>Questions:</strong> {{ $assessment->questions->where('type', 'rating')->count() }}</span> --}}
                <span><i class="bi bi-info-circle"></i> Rate 1–5 (1 = သဘောမတူ၊ 5 = အလွန်သဘောတူ)</span>
            </div>
        </div>

        <form action="{{ route('student.assessments.submit') }}" method="POST" id="assessmentForm">
            @csrf
            <input type="hidden" name="assessment_id" value="{{ $assessment->id }}">

            {{-- Selection Section: Teacher & Subject --}}
            <div class="selection-section">
                <div class="form-group">
                    <label>Teaching Subject (Course) <span class="required-star">*</span></label>
                    <select name="course_id" id="courseSelect" required>
                        <option value="">-- Select Subject --</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" data-lecturer-id="{{ $course->lecturer_id ?? '' }}">
                                {{ $course->course_code }} - {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Teacher Name <span class="required-star">*</span></label>
                    <select name="lecturer_id" id="lecturerSelect" required>
                        <option value="">-- Select Teacher --</option>
                        @foreach (\App\Models\User::where('role_id', 2)->orderBy('name')->get() as $lecturer)
                            <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                        @endforeach
                    </select>
                    <div id="autoLecturerDisplay" style="display:none;" class="auto-filled-lecturer">
                        <span id="autoLecturerName"></span>
                    </div>
                </div>
            </div>

            {{-- Questions Section --}}
            <div class="questions-section">
                <div class="section-title">Course Evaluation Questions</div>

                @php
                    $ratingQuestions = $assessment->questions->where('type', 'rating');
                @endphp

                @foreach ($ratingQuestions as $question)
                    <div class="question-item">
                        <div class="question-text">
                            <span class="q-number">{{ $question->order }}.</span>
                            {{ $question->question_text }}
                            <span class="required-star">*</span>
                        </div>
                        <div class="rating-group">
                            @for ($i = 1; $i <= 5; $i++)
                                <div class="rating-option">
                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $i }}"
                                        id="q{{ $question->id }}_{{ $i }}" required>
                                    <label for="q{{ $question->id }}_{{ $i }}">{{ $i }}</label>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Comments Section --}}
            @php
                $textQuestion = $assessment->questions->where('type', 'text')->first();
            @endphp
            @if ($textQuestion)
                <div class="comments-section">
                    <label for="comments">{{ $textQuestion->question_text }}</label>
                    <textarea name="answers[{{ $textQuestion->id }}]" id="comments" rows="3"
                        placeholder="သင်၏မှတ်ချက်များကို ဤနေရာတွင် ရေးပါ... (Optional)"></textarea>
                </div>
            @endif

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('student.assessments.index') }}" class="btn-cancel">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="bi bi-send"></i> Submit
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseSelect = document.getElementById('courseSelect');
            const lecturerSelect = document.getElementById('lecturerSelect');
            const autoDisplay = document.getElementById('autoLecturerDisplay');
            const autoName = document.getElementById('autoLecturerName');

            // Store all lecturer options for filtering
            const allOptions = Array.from(lecturerSelect.options);

            // ============================================================
            // When course changes, auto-select lecturer
            // ============================================================
            courseSelect.addEventListener('change', function() {
                const selectedCourse = this.options[this.selectedIndex];
                const lecturerId = selectedCourse.getAttribute('data-lecturer-id');

                lecturerSelect.innerHTML = '';

                // Add default option
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = '-- Select Teacher --';
                lecturerSelect.appendChild(defaultOpt);

                if (!lecturerId) {
                    // No lecturer assigned - show all
                    allOptions.forEach(opt => {
                        if (opt.value !== '') {
                            const clone = opt.cloneNode(true);
                            lecturerSelect.appendChild(clone);
                        }
                    });
                    lecturerSelect.style.display = 'block';
                    autoDisplay.style.display = 'none';
                    return;
                }

                // Find the matching lecturer
                const matchedOption = allOptions.find(opt => opt.value == lecturerId);

                if (matchedOption) {
                    // Auto-select
                    autoName.textContent = matchedOption.textContent;
                    autoDisplay.style.display = 'block';
                    lecturerSelect.style.display = 'none';
                    // Add hidden option for form submission
                    const hiddenOpt = document.createElement('option');
                    hiddenOpt.value = matchedOption.value;
                    hiddenOpt.textContent = matchedOption.textContent;
                    hiddenOpt.selected = true;
                    lecturerSelect.appendChild(hiddenOpt);
                } else {
                    // Show all lecturers
                    allOptions.forEach(opt => {
                        if (opt.value !== '') {
                            const clone = opt.cloneNode(true);
                            lecturerSelect.appendChild(clone);
                        }
                    });
                    lecturerSelect.style.display = 'block';
                    autoDisplay.style.display = 'none';
                }
            });

            // ============================================================
            // Form validation
            // ============================================================
            document.getElementById('assessmentForm').addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass"></i> Submitting...';

                if (!courseSelect.value) {
                    e.preventDefault();
                    alert('Please select a subject (course).');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit';
                    return;
                }

                if (!lecturerSelect.value) {
                    e.preventDefault();
                    alert('Please select a teacher.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit';
                    return;
                }

                const ratings = document.querySelectorAll('input[type="radio"]:checked');
                const requiredQuestions = document.querySelectorAll('.question-item input[type="radio"]');
                const requiredCount = requiredQuestions.length;

                if (ratings.length < requiredCount) {
                    e.preventDefault();
                    alert('Please answer all rating questions.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send"></i> Submit';
                    return;
                }
            });
        });
    </script>
@endsection
