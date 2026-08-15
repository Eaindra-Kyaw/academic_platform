@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', ' Departments')
@section('welcome-text', 'Manage university departments and their academic programs')

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
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .page-header-dept {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-primary-dept {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
        }

        .btn-primary-dept:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(10, 36, 99, 0.25);
            color: var(--white);
        }

        .stats-bar-dept {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item-dept {
            background: var(--white);
            padding: 1rem 1.25rem;
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .stat-item-dept:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-hover);
        }

        .stat-icon-dept {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .stat-icon-dept.blue {
            background: var(--info-light);
            color: var(--info);
        }

        .stat-icon-dept.green {
            background: var(--success-light);
            color: var(--success);
        }

        .stat-icon-dept.yellow {
            background: var(--warning-light);
            color: var(--warning);
        }

        .stat-icon-dept.red {
            background: var(--danger-light);
            color: var(--danger);
        }

        .stat-info-dept .number {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .stat-info-dept .label {
            font-size: 0.65rem;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .dept-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.25rem;
        }

        .dept-card {
            background: var(--white);
            border-radius: var(--radius);
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1.25rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .dept-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            border-color: var(--primary);
        }

        .dept-card .dept-code {
            display: inline-block;
            background: var(--primary);
            color: var(--white);
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.6rem;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        .dept-card .dept-name {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0.5rem 0 0.2rem;
        }

        .dept-card .dept-hod {
            font-size: 0.75rem;
            color: var(--text-gray);
            margin-bottom: 0.75rem;
        }

        .dept-card .dept-stats {
            display: flex;
            gap: 1.5rem;
            padding: 0.6rem 0;
            border-top: 1px solid rgba(10, 36, 99, 0.06);
            border-bottom: 1px solid rgba(10, 36, 99, 0.06);
            margin-bottom: 0.75rem;
        }

        .dept-card .dept-stats span {
            font-size: 0.75rem;
            color: var(--text-gray);
        }

        .dept-card .dept-stats strong {
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .dept-card .dept-actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .dept-card .dept-actions a,
        .dept-card .dept-actions button {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-view-dept {
            background: var(--info-light);
            color: var(--info);
        }

        .btn-view-dept:hover {
            background: #bfdbfe;
        }

        .btn-edit-dept {
            background: var(--warning-light);
            color: #92400e;
        }

        .btn-edit-dept:hover {
            background: #fde68a;
        }

        .btn-delete-dept {
            background: var(--danger-light);
            color: var(--danger);
        }

        .btn-delete-dept:hover {
            background: #fca5a5;
        }

        .empty-state-dept {
            text-align: center;
            padding: 3rem 1.5rem;
            background: var(--white);
            border-radius: var(--radius);
            border: 2px dashed rgba(10, 36, 99, 0.1);
        }

        .empty-state-dept i {
            font-size: 3rem;
            color: #d1d5db;
        }

        .empty-state-dept h5 {
            margin-top: 1rem;
            color: var(--text-dark);
        }

        .empty-state-dept p {
            color: var(--text-gray);
            font-size: 0.85rem;
        }

        .confirm-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .confirm-overlay.show {
            display: flex;
        }

        .confirm-box {
            background: var(--white);
            border-radius: var(--radius);
            padding: 2rem;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .confirm-box .icon {
            text-align: center;
            font-size: 2.5rem;
            color: var(--danger);
            margin-bottom: 0.5rem;
        }

        .confirm-box h4 {
            text-align: center;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 0.3rem 0;
        }

        .confirm-box p {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-gray);
            margin: 0 0 1.5rem 0;
        }

        .confirm-box .buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .confirm-box .btn-confirm-cancel {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid rgba(10, 36, 99, 0.1);
            background: var(--white);
            color: var(--text-dark);
            cursor: pointer;
            transition: var(--transition);
        }

        .confirm-box .btn-confirm-cancel:hover {
            background: #f3f4f6;
        }

        .confirm-box .btn-confirm-delete {
            padding: 0.4rem 1.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            background: var(--danger);
            color: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }

        .confirm-box .btn-confirm-delete:hover {
            background: #b91c1c;
        }

        @media (max-width: 768px) {
            .stats-bar-dept {
                grid-template-columns: repeat(2, 1fr);
            }

            .dept-grid {
                grid-template-columns: 1fr;
            }

            .page-header-dept {
                justify-content: center;
            }

            .page-header-dept .btn-primary-dept {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .stats-bar-dept {
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
            }

            .stat-item-dept {
                padding: 0.75rem;
            }

            .stat-icon-dept {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }

            .stat-info-dept .number {
                font-size: 1.1rem;
            }

            .dept-card {
                padding: 1rem;
            }
        }
    </style>

    <div class="page-header-dept">
        <a href="{{ route('admin.departments.create') }}" class="btn-primary-dept">
            <i class="bi bi-plus-circle"></i> Add New Department
        </a>
    </div>

    <div class="stats-bar-dept">
        <div class="stat-item-dept">
            <div class="stat-icon-dept blue"><i class="bi bi-building"></i></div>
            <div class="stat-info-dept">
                <div class="number">{{ $departments->count() }}</div>
                <div class="label">Total Departments</div>
            </div>
        </div>
        <div class="stat-item-dept">
            <div class="stat-icon-dept green"><i class="bi bi-people"></i></div>
            <div class="stat-info-dept">
                <div class="number">{{ $departments->sum('students_count') }}</div>
                <div class="label">Total Students</div>
            </div>
        </div>
        <div class="stat-item-dept">
            <div class="stat-icon-dept yellow"><i class="bi bi-book"></i></div>
            <div class="stat-info-dept">
                <div class="number">{{ $departments->sum('courses_count') }}</div>
                <div class="label">Total Courses</div>
            </div>
        </div>
        <div class="stat-item-dept">
            <div class="stat-icon-dept red"><i class="bi bi-person-badge"></i></div>
            <div class="stat-info-dept">
                <div class="number">{{ $departments->sum('lecturers_count') }}</div>
                <div class="label">Total Teachers</div>
            </div>
        </div>
    </div>

    @if ($departments->count() > 0)
        <div class="dept-grid">
            @foreach ($departments as $dept)
                <div class="dept-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <span class="dept-code">{{ $dept->code }}</span>
                        <span style="font-size:0.7rem; color:var(--success);">
                            <i class="bi bi-graph-up"></i> {{ number_format($dept->avg_attendance ?? 0, 1) }}%
                        </span>
                    </div>
                    <div class="dept-name">{{ $dept->name }}</div>
                    <div class="dept-hod">
                        <i class="bi bi-person" style="font-size:0.7rem;"></i>
                        {{ $dept->head_of_department ?? 'No HOD assigned' }}
                    </div>
                    <div class="dept-stats">
                        <span><strong>{{ $dept->students_count ?? 0 }}</strong> Students</span>
                        <span><strong>{{ $dept->courses_count ?? 0 }}</strong> Courses</span>
                        <span><strong>{{ $dept->lecturers_count ?? 0 }}</strong> Lecturers</span>
                    </div>
                    <div class="dept-actions">
                        <a href="{{ route('admin.departments.show', $dept) }}" class="btn-view-dept">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="{{ route('admin.departments.edit', $dept) }}" class="btn-edit-dept">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <button type="button" class="btn-delete-dept"
                            onclick="showDeleteConfirm('{{ addslashes($dept->name) }}', '{{ route('admin.departments.destroy', $dept) }}')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state-dept">
            <i class="bi bi-building"></i>
            <h5>No Departments Yet</h5>
            <p>Create your first department to start organizing courses and students.</p>
            <a href="{{ route('admin.departments.create') }}" class="btn-primary-dept"
                style="display:inline-flex; margin-top:0.5rem;">
                <i class="bi bi-plus-circle"></i> Create Department
            </a>
        </div>
    @endif

    <div class="confirm-overlay" id="deleteConfirm">
        <div class="confirm-box">
            <div class="icon">🗑️</div>
            <h4>Delete Department</h4>
            <p>Are you sure you want to delete "<span id="confirmDeptName"></span>"?<br>This action cannot be undone.</p>
            <div class="buttons">
                <button class="btn-confirm-cancel" onclick="closeConfirm()">Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-confirm-delete">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showDeleteConfirm(deptName, deleteUrl) {
            document.getElementById('confirmDeptName').textContent = deptName;
            document.getElementById('deleteForm').action = deleteUrl;
            document.getElementById('deleteConfirm').classList.add('show');
        }

        function closeConfirm() {
            document.getElementById('deleteConfirm').classList.remove('show');
        }

        document.getElementById('deleteConfirm').addEventListener('click', function(e) {
            if (e.target === this) {
                closeConfirm();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeConfirm();
            }
        });
    </script>
@endsection
