<div class="nav-label">Main</div>
<a href="{{ route('lecturer.dashboard') }}" class="nav-item @if (request()->routeIs('lecturer.dashboard')) active @endif">
    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
</a>

<div class="nav-label">Attendance</div>
<a href="{{ route('lecturer.attendance.take') }}" class="nav-item @if (request()->routeIs('lecturer.attendance.take')) active @endif">
    <i class="bi bi-qr-code"></i><span>Take Attendance</span>
</a>
<a href="{{ route('lecturer.attendance.sessions') }}" class="nav-item @if (request()->routeIs('lecturer.attendance.sessions')) active @endif">
    <i class="bi bi-clock-history"></i><span>Session History</span>
</a>

<div class="nav-label">Management</div>
<a href="{{ route('lecturer.enrollments.index') }}" class="nav-item @if (request()->routeIs('lecturer.enrollments*')) active @endif">
    <i class="bi bi-list-check"></i><span>Enrollments</span>
</a>
<a href="{{ route('lecturer.students') }}" class="nav-item @if (request()->routeIs('lecturer.students')) active @endif">
    <i class="bi bi-people"></i><span>All Students</span>
</a>
<a href="{{ route('lecturer.timetable') }}" class="nav-item @if (request()->routeIs('lecturer.timetable')) active @endif">
    <i class="bi bi-calendar-week"></i><span>Timetable</span>
</a>

<div class="nav-label">Communication</div>
<a href="{{ route('lecturer.messages.inbox') }}" class="nav-item @if (request()->routeIs('lecturer.messages*')) active @endif">
    <i class="bi bi-envelope"></i>
    <span>Messages</span>
    <span id="lecturerUnreadBadge"
        style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto; display:none;">0</span>
</a>
<a href="{{ route('lecturer.announcements') }}" class="nav-item @if (request()->routeIs('lecturer.announcements')) active @endif">
    <i class="bi bi-megaphone"></i>
    <span>Announcements</span>
    <span id="lecturerAnnouncementBadge"
        style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto; display:none;">0</span>
</a>

<div class="nav-label">Reports</div>
<a href="{{ route('lecturer.reports') }}" class="nav-item @if (request()->routeIs('lecturer.reports')) active @endif">
    <i class="bi bi-file-earmark-text"></i><span> Reports</span>
</a>
