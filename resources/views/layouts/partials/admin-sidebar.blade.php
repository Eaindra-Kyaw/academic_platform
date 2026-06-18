{{-- resources/views/layouts/partials/admin-sidebar.blade.php --}}

<div class="nav-label">Main</div>
<a href="{{ route('admin.dashboard') }}" class="nav-item @if (request()->routeIs('admin.dashboard')) active @endif">
    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
</a>

<div class="nav-label">Management</div>
<a href="{{ route('admin.departments.index') }}" class="nav-item @if (request()->routeIs('admin.departments*')) active @endif">
    <i class="bi bi-building"></i><span>Departments</span>
</a>
<a href="{{ route('admin.users') }}" class="nav-item @if (request()->routeIs('admin.users*')) active @endif">
    <i class="bi bi-people"></i><span>User Management</span>
</a>
<a href="{{ route('admin.enrollments.index') }}" class="nav-item @if (request()->routeIs('admin.enrollments*')) active @endif">
    <i class="bi bi-list-check"></i><span>Enrollments</span>
</a>
<a href="{{ route('admin.messages.inbox') }}" class="nav-item @if (request()->routeIs('admin.messages*')) active @endif">
    <i class="bi bi-envelope"></i>
    <span>Messages</span>
    <span id="adminUnreadBadge"
        style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto; display:none;">0</span>
</a>
<a href="#" class="nav-item">
    <i class="bi bi-calendar-check"></i><span>Attendance</span>
</a>

<div class="nav-label">Analytics</div>
<a href="#" class="nav-item">
    <i class="bi bi-exclamation-triangle"></i><span>Risk Analysis</span>
</a>
<a href="{{ route('admin.reports') }}" class="nav-item">
    <i class="bi bi-file-earmark-text"></i><span>Reports</span>
</a>
<a href="#" class="nav-item">
    <i class="bi bi-calendar"></i><span>Semesters</span>
</a>
<a href="#" class="nav-item">
    <i class="bi bi-megaphone"></i><span>Announcements</span>
</a>
