@extends('layouts.app')

@section('title', 'Create Course Assessment')
@section('role', 'Admin')
@section('page-title', 'Create Course Assessment')
@section('welcome-text', 'Create a new course evaluation form')

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

        .form-card {
            max-width: 850px;
            margin: 0 auto;
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 2rem;
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
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-dark);
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* ============================================================
                           🟢 FIXED SCOPE SELECTION - Perfect side-by-side layout
                           ============================================================ */
        .scope-toggle-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 0.3rem 0;
        }

        .scope-option {
            display: flex !important;
            flex-direction: row !important;
            /* Forces horizontal alignment */
            align-items: center !important;
            gap: 0.75rem;
            min-width: 160px;
            /* Forces a minimum width so text never squishes */
            padding: 0.5rem 1.2rem 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #e2e8f0;
            background: #f8fafc;
            transition: all 0.2s;
            margin: 0 !important;
            /* Resets any default margins */
        }

        .scope-option:hover {
            border-color: var(--primary-light);
            background: #f1f5f9;
        }

        .scope-option input[type="radio"] {
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
            cursor: pointer;
            margin: 0 !important;
            flex-shrink: 0;
            /* Prevents circle from shrinking */
        }

        .scope-option .option-text {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            white-space: nowrap;
            /* Forces text to stay on the SAME line */
        }

        /* Selected State styling */
        .scope-option.selected {
            border-color: var(--primary);
            background: #eef2ff;
            box-shadow: 0 0 0 2px rgba(10, 36, 99, 0.1);
        }

        /* ============================================================
                           QUESTIONS
                           ============================================================ */
        .question-group {
            background: #fafbfc;
            border: 1px solid rgba(10, 36, 99, 0.06);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 0.6rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            transition: var(--transition);
        }

        .question-group:hover {
            border-color: var(--primary);
        }

        .question-group .question-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            background: var(--primary);
            color: var(--white);
            border-radius: 50%;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 0.15rem;
        }

        .question-group .question-fields {
            flex: 1;
        }

        .question-group .question-fields input {
            width: 100%;
            padding: 0.5rem 0.7rem;
            border: 1px solid rgba(10, 36, 99, 0.12);
            border-radius: 6px;
            font-size: 0.85rem;
            transition: var(--transition);
            background: var(--white);
            font-family: 'Inter', sans-serif;
        }

        .question-group .question-fields input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .question-group .btn-remove-question {
            background: none;
            border: none;
            color: var(--text-gray);
            cursor: pointer;
            font-size: 1.2rem;
            transition: var(--transition);
            padding: 0.2rem 0.3rem;
            line-height: 1;
        }

        .question-group .btn-remove-question:hover {
            color: var(--danger);
            transform: scale(1.2);
        }

        .btn-add-question {
            background: var(--bg-main);
            color: var(--text-dark);
            border: 2px dashed rgba(10, 36, 99, 0.2);
            padding: 0.7rem 1.2rem;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.85rem;
        }

        .btn-add-question:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: rgba(10, 36, 99, 0.03);
        }

        /* Status Options */
        .status-options {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 0.3rem 0;
        }

        .status-options .status-option {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            padding: 0.3rem 0.8rem;
            border-radius: 6px;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .status-options .status-option:hover {
            background: var(--bg-main);
        }

        .status-options .status-option input[type="radio"] {
            accent-color: var(--primary);
            cursor: pointer;
        }

        .status-options .status-option label {
            font-size: 0.8rem;
            color: var(--text-dark);
            cursor: pointer;
            margin: 0;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
        }

        .status-options .status-option.draft {
            border-color: var(--text-gray);
        }

        .status-options .status-option.active {
            border-color: var(--success);
        }

        .status-options .status-option.closed {
            border-color: var(--danger);
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
            background: var(--success-light);
            color: #166534;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: var(--danger-light);
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

            .status-options {
                flex-direction: column;
                gap: 0.3rem;
            }

            .scope-toggle-wrapper {
                flex-direction: column;
                gap: 0.5rem;
            }

            .scope-option {
                width: 100%;
                justify-content: flex-start;
                min-width: unset;
            }
        }
    </style>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible"><i class="bi bi-check-circle"></i> {{ session('success') }} <button
                class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible"><i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button class="btn-close-alert" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> Please fix the following errors:
            <ul style="margin-top:0.3rem; margin-bottom:0; padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li style="font-size:0.8rem;">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('admin.assessments.dashboard') }}" class="back-link"><i class="bi bi-arrow-left"></i> Back to
        Dashboard</a>

    <div class="form-card">
        <h2 class="form-title"><i class="bi bi-plus-circle"></i> Create Course Assessment</h2>
        <p class="form-subtitle">Create a new course evaluation form for students</p>

        <form action="{{ route('admin.assessments.store') }}" method="POST" id="assessmentForm">
            @csrf

            <div class="form-group">
                <label>Academic Year <span class="required">*</span></label>
                <select name="year" class="form-control @error('year') error @enderror" required id="yearSelect">
                    <option value="">Select Year</option>
                    @foreach ($years as $key => $label)
                        <option value="{{ $key }}" {{ old('year') == $key ? 'selected' : '' }}>{{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('year')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Semester <span class="required">*</span></label>
                <select name="semester" class="form-control @error('semester') error @enderror" required
                    id="semesterSelect">
                    <option value="">Select Semester</option>
                    @foreach ($semesterOptions as $sem)
                        <option value="{{ $sem }}" {{ old('semester') == $sem ? 'selected' : '' }}>
                            {{ $sem }}</option>
                    @endforeach
                </select>
                @error('semester')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- ✅ FIXED: CIRCLE AND TEXT ON SAME LINE, WIDER BOXES --}}
            <div class="form-group">
                <label>Courses <span class="required">*</span></label>
                <div class="scope-toggle-wrapper">
                    {{-- Specific Course Box --}}
                    <label class="scope-option" id="singleScopeLabel">
                        <input type="radio" name="scope" value="single" id="scopeSingle" checked>
                        <span class="option-text">
                            <i class="bi bi-book"></i> Specific Course
                        </span>
                    </label>

                    {{-- All Courses Box --}}
                    <label class="scope-option" id="allScopeLabel">
                        <input type="radio" name="scope" value="all" id="scopeAll">
                        <span class="option-text">
                            <i class="bi bi-stack"></i> All Courses
                        </span>
                    </label>
                </div>
                <div class="help-text">
                    <i class="bi bi-info-circle"></i>
                    <span id="scopeHelpSingle">Choose a specific subject to evaluate.</span>
                    <span id="scopeHelpAll" style="display: none; font-weight: 600; color: var(--primary);">
                        This will automatically create individual forms for ALL courses in the selected Year & Semester.
                    </span>
                </div>
            </div>

            {{-- Specific Course Selector (Shown for Single Selection) --}}
            <div class="form-group" id="courseSelectorGroup">
                <label>Select Specific Course <span class="required">*</span></label>
                <select name="course_id" class="form-control @error('course_id') error @enderror" id="courseSelect">
                    <option value="">-- Select a Course --</option>
                </select>
                @error('course_id')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            {{-- Lecturer Selector (Hidden for All Selection) --}}
            <div class="form-group" id="lecturerGroup">
                <label>Specific Lecturer (Optional)</label>
                <select name="lecturer_id" class="form-control @error('lecturer_id') error @enderror" id="lecturerSelect">
                    <option value="">All Lecturers</option>
                </select>
                @error('lecturer_id')
                    <div class="error-text">{{ $message }}</div>
                @enderror
                <div class="help-text"><i class="bi bi-info-circle"></i> Select a specific lecturer for this single course.
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Open Date <span class="required">*</span></label>
                    <input type="datetime-local" name="opens_at" class="form-control @error('opens_at') error @enderror"
                        value="{{ old('opens_at') }}" required>
                    @error('opens_at')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Close Date <span class="required">*</span></label>
                    <input type="datetime-local" name="closes_at" class="form-control @error('closes_at') error @enderror"
                        value="{{ old('closes_at') }}" required>
                    @error('closes_at')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- STATUS SELECTION --}}
            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <div class="status-options">
                    <div class="status-option draft">
                        <input type="radio" name="status" value="draft"
                            {{ old('status', 'draft') == 'draft' ? 'checked' : '' }} id="status_draft">
                        <label for="status_draft">
                            <i class="bi bi-pencil-square"></i> Draft
                        </label>
                    </div>
                    <div class="status-option active">
                        <input type="radio" name="status" value="active"
                            {{ old('status') == 'active' ? 'checked' : '' }} id="status_active">
                        <label for="status_active">
                            <i class="bi bi-check-circle-fill"></i> Active
                        </label>
                    </div>
                    <div class="status-option closed">
                        <input type="radio" name="status" value="closed"
                            {{ old('status') == 'closed' ? 'checked' : '' }} id="status_closed">
                        <label for="status_closed">
                            <i class="bi bi-lock-fill"></i> Closed
                        </label>
                    </div>
                </div>
                @error('status')
                    <div class="error-text">{{ $message }}</div>
                @enderror
                <div class="help-text"><i class="bi bi-info-circle"></i> <strong>Draft:</strong> Not visible &nbsp;|&nbsp;
                    <strong>Active:</strong> Visible to students &nbsp;|&nbsp; <strong>Closed:</strong> No longer accepting
                </div>
            </div>

            <div class="form-group">
                <label>Evaluation Questions <span class="required">*</span></label>
                <div id="questionsContainer">
                    @php
                        $defaultQuestions = [
                            'ဘာသာရပ်နယ်ပယ်နှင့် ပတ်သက်၍ ကောင်းစွာတတ်ကျွမ်း နှံ့စပ်ပုံ ပေါ် ပါသလား။ *',
                            'ဘာသာရပ်ကိုသင်ပြရာတွင်စိတ်ထက်သန်မှုရှိပါသလား။ *',
                            'စာသင်ကြားရန် ကောင်းမွန်စွာ ပြင်ဆင် လာပါသလား။ *',
                            'စာသင်ကြားရာတွင် နားလည်အောင် ရှင်းလင်းပြတ်သားစွာ သင်ကြား နိုင်ပါသလား။ *',
                            'မရှင်းလင်းသောနားမလည်သော သင်ခန်းစာများ မေးမြန်း ဆွေးနွေးရန် လွယ်ကူ ပါသလား။ *',
                            'အသုံးဝင်သည့် တုံ့ပြန်မှု နှင့် ကောင်းသော အကြံဉာဏ် ပေးတတ် ပါသလား။ *',
                            'ဤဘာသာရပ်ကိုထိရောက်စွာ သင်ကြားနိုင်သောဆရာ/မ တစ်ဦးဖြစ်ပါသလား။ *',
                            'ကျောင်းသား/ သူအားလုံး အပေါ် မျှတစွာ ဆက်ဆံပါသလား။ *',
                            'အတန်းကိုကောင်းစွာ ကိုင်တွယ် ထိန်းကျောင်းနိုင်ပါသလား။ *',
                            'အကဲဖြတ် စစ်ဆေးမှု ဆောင်ရွက် ပါသလား။ *',
                        ];
                        $questions = old('questions', $defaultQuestions);
                    @endphp
                    @foreach ($questions as $index => $question)
                        <div class="question-group" data-index="{{ $index }}">
                            <span class="question-number">{{ $index + 1 }}</span>
                            <div class="question-fields">
                                <input type="text" name="questions[]" id="question_{{ $index }}"
                                    class="form-control" value="{{ $question }}"
                                    placeholder="Enter question {{ $index + 1 }}..." required>
                            </div>
                            @if ($index > 0)
                                <button type="button" class="btn-remove-question" onclick="removeQuestion(this)"
                                    title="Remove question"><i class="bi bi-x-circle"></i></button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn-add-question" onclick="addQuestion()"><i
                        class="bi bi-plus-circle"></i> Add Question</button>
                @error('questions')
                    <div class="error-text">{{ $message }}</div>
                @enderror
                @error('questions.*')
                    <div class="error-text">{{ $message }}</div>
                @enderror
                <div class="help-text"><i class="bi bi-info-circle"></i> Minimum <strong>5</strong> questions required.
                    "Other Comments" will be auto-added.</div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.assessments.dashboard') }}" class="btn-cancel"><i class="bi bi-x-circle"></i>
                    Cancel</a>
                <button type="submit" class="btn-submit" id="submitBtn"><i class="bi bi-save"></i> Create
                    Assessment</button>
            </div>
        </form>
    </div>

    <script>
        let questionCount = {{ count($questions) }};

        function addQuestion() {
            questionCount++;
            const container = document.getElementById('questionsContainer');
            const div = document.createElement('div');
            div.className = 'question-group';
            div.dataset.index = questionCount - 1;
            div.innerHTML = `
                <span class="question-number">${questionCount}</span>
                <div class="question-fields">
                    <input type="text" id="question_${questionCount - 1}" name="questions[]" class="form-control" placeholder="Enter question ${questionCount}..." required>
                </div>
                <button type="button" class="btn-remove-question" onclick="removeQuestion(this)" title="Remove question"><i class="bi bi-x-circle"></i></button>
            `;
            container.appendChild(div);
            updateQuestionNumbers();
        }

        function removeQuestion(button) {
            const group = button.closest('.question-group');
            const container = document.getElementById('questionsContainer');
            if (container.querySelectorAll('.question-group').length <= 1) {
                alert('You need at least 1 question.');
                return;
            }
            group.remove();
            updateQuestionNumbers();
        }

        function updateQuestionNumbers() {
            const groups = document.querySelectorAll('.question-group');
            groups.forEach((group, index) => {
                const numberSpan = group.querySelector('.question-number');
                if (numberSpan) numberSpan.textContent = index + 1;
                const input = group.querySelector('input');
                if (input) {
                    input.id = `question_${index}`;
                    input.placeholder = `Enter question ${index + 1}...`;
                }
            });
            questionCount = groups.length;
        }

        // ============================================================
        // LOAD COURSES BASED ON YEAR & SEMESTER
        // ============================================================
        document.getElementById('yearSelect').addEventListener('change', loadCourses);
        document.getElementById('semesterSelect').addEventListener('change', loadCourses);

        function loadCourses() {
            const year = document.getElementById('yearSelect').value;
            const semester = document.getElementById('semesterSelect').value;

            if (!year || !semester) {
                const courseSelect = document.getElementById('courseSelect');
                courseSelect.innerHTML = '<option value="">-- Select a Course --</option>';
                return;
            }

            const courseSelect = document.getElementById('courseSelect');
            courseSelect.innerHTML = '<option value="">Loading courses...</option>';

            fetch(`/admin/assessments/courses?year=${encodeURIComponent(year)}&semester=${encodeURIComponent(semester)}`)
                .then(response => response.json())
                .then(data => {
                    courseSelect.innerHTML = '<option value="">-- Select a Course --</option>';
                    data.forEach(course => {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.course_code + ' - ' + course.course_name;
                        courseSelect.appendChild(option);
                    });

                    if (courseSelect.value) {
                        loadLecturers(courseSelect.value);
                    }
                })
                .catch(() => {
                    courseSelect.innerHTML = '<option value="">Error loading courses</option>';
                });
        }

        // ============================================================
        // TOGGLE UI BASED ON SCOPE SELECTION
        // ============================================================
        const scopeSingle = document.getElementById('scopeSingle');
        const scopeAll = document.getElementById('scopeAll');
        const singleLabel = document.getElementById('singleScopeLabel');
        const allLabel = document.getElementById('allScopeLabel');
        const courseSelectorGroup = document.getElementById('courseSelectorGroup');
        const lecturerGroup = document.getElementById('lecturerGroup');
        const helpSingle = document.getElementById('scopeHelpSingle');
        const helpAll = document.getElementById('scopeHelpAll');

        function toggleScopeUI() {
            if (scopeAll.checked) {
                // ALL Courses Mode
                singleLabel.classList.remove('selected');
                allLabel.classList.add('selected');

                courseSelectorGroup.style.display = 'none';
                lecturerGroup.style.display = 'none';
                helpSingle.style.display = 'none';
                helpAll.style.display = 'block';
            } else {
                // Specific Course Mode
                singleLabel.classList.add('selected');
                allLabel.classList.remove('selected');

                courseSelectorGroup.style.display = 'block';
                lecturerGroup.style.display = 'block';
                helpSingle.style.display = 'block';
                helpAll.style.display = 'none';
            }
        }

        // Attach events
        scopeSingle.addEventListener('change', toggleScopeUI);
        scopeAll.addEventListener('change', toggleScopeUI);

        // Initialize on load
        toggleScopeUI();

        // ============================================================
        // LOAD LECTURERS
        // ============================================================
        document.getElementById('courseSelect').addEventListener('change', function() {
            loadLecturers(this.value);
        });

        function loadLecturers(courseId) {
            const lecturerSelect = document.getElementById('lecturerSelect');

            if (!courseId) {
                lecturerSelect.innerHTML = '<option value="">All Lecturers</option>';
                return;
            }

            lecturerSelect.innerHTML = '<option value="">Loading lecturers...</option>';

            fetch(`/admin/assessments/lecturers?course_id=${courseId}`)
                .then(response => response.json())
                .then(data => {
                    lecturerSelect.innerHTML = '<option value="">All Lecturers</option>';
                    data.forEach(lecturer => {
                        const option = document.createElement('option');
                        option.value = lecturer.id;
                        option.textContent = lecturer.name;
                        lecturerSelect.appendChild(option);
                    });
                })
                .catch(() => {
                    lecturerSelect.innerHTML = '<option value="">All Lecturers</option>';
                });
        }

        // ============================================================
        // FORM VALIDATION
        // ============================================================
        document.getElementById('assessmentForm').addEventListener('submit', function(e) {
            const questions = document.querySelectorAll('input[name="questions[]"]');
            let empty = false;

            questions.forEach(input => {
                if (!input.value.trim()) {
                    empty = true;
                    input.style.borderColor = '#ef4444';
                    input.style.background = '#fef2f2';
                } else {
                    input.style.borderColor = '';
                    input.style.background = '';
                }
            });

            if (empty) {
                e.preventDefault();
                alert('Please fill in all question fields.');
                return;
            }

            if (questions.length < 5) {
                e.preventDefault();
                alert('Please add at least 5 questions.');
                return;
            }

            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass"></i> Creating...';
        });

        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name="questions[]"]')) {
                e.target.style.borderColor = '';
                e.target.style.background = '';
            }
        });
    </script>
@endsection
