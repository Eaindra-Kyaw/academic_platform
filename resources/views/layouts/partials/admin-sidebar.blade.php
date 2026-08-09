{{-- resources/views/layouts/partials/admin-sidebar.blade.php --}}

<div class="nav-label">Main</div>
<a href="{{ route('admin.dashboard') }}" class="nav-item @if (request()->routeIs('admin.dashboard')) active @endif">
    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
</a>

<div class="nav-label">Management</div>
<a href="{{ route('admin.departments.index') }}" class="nav-item @if (request()->routeIs('admin.departments*')) active @endif">
    <i class="bi bi-building"></i><span>Departments</span>
</a>

{{-- ✅ FIXED: User Management - Only active on users.* routes, NOT on pending --}}
<a href="{{ route('admin.users.index') }}" class="nav-item @if (request()->routeIs('admin.users.index') ||
        request()->routeIs('admin.users.create') ||
        request()->routeIs('admin.users.edit') ||
        request()->routeIs('admin.users.show')) active @endif">
    <i class="bi bi-people"></i>
    <span>User Management</span>
</a>

{{--  Pending Approvals - Only active on pending route --}}
<a href="{{ route('admin.users.pending') }}" class="nav-item @if (request()->routeIs('admin.users.pending')) active @endif">
    <i class="bi bi-clock-history"></i>
    <span>Pending Approvals</span>
    @php
        $pendingCount = \App\Models\User::where('registration_status', 'pending')->count();
    @endphp
    {{-- @if ($pendingCount > 0)
        <span
            style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto;">{{ $pendingCount }}</span>
    @endif --}}
</a>

<a href="{{ route('admin.enrollments.index') }}" class="nav-item @if (request()->routeIs('admin.enrollments*')) active @endif">
    <i class="bi bi-list-check"></i><span>Enrollments</span>
</a>

<div class="nav-label">Analytics</div>
<a href="{{ route('admin.attendance.analytics') }}" class="nav-item @if (request()->routeIs('admin.attendance.analytics')) active @endif">
    <i class="bi bi-calendar-check"></i><span>Attendance</span>
</a>

<a href="{{ route('admin.risk.index') }}" class="nav-item @if (request()->routeIs('admin.risk*')) active @endif">
    <i class="bi bi-exclamation-triangle"></i><span>Risk Analysis</span>
</a>

<a href="{{ route('admin.reports') }}" class="nav-item @if (request()->routeIs('admin.reports')) active @endif">
    <i class="bi bi-file-earmark-text"></i><span>Reports</span>
</a>

{{-- ✅ EVALUATION SECTION --}}
<div class="nav-label">Evaluation</div>

{{-- 1. Course Assessment --}}
<a href="{{ route('admin.assessments.dashboard') }}" class="nav-item @if (request()->routeIs('admin.assessments*')) active @endif">
    <i class="bi bi-clipboard-check"></i>
    <span>Course Assessments</span>
</a>

<div class="nav-label">Communication</div>
<a href="{{ route('admin.messages.inbox') }}" class="nav-item @if (request()->routeIs('admin.messages*')) active @endif">
    <i class="bi bi-envelope"></i>
    <span>Messages</span>
    <span id="adminUnreadBadge"
        style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto; display:none;">0</span>
</a>
<a href="{{ route('admin.announcements.index') }}" class="nav-item @if (request()->routeIs('admin.announcements*')) active @endif">
    <i class="bi bi-megaphone"></i>
    <span>Announcements</span>
    <span id="adminAnnouncementBadge"
        style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto; display:none;">0</span>
</a>
