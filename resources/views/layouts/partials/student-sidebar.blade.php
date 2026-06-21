{{-- resources/views/layouts/partials/student-sidebar.blade.php --}}

<div class="nav-label">Main</div>
<a href="{{ route('student.dashboard') }}" class="nav-item @if (request()->routeIs('student.dashboard')) active @endif">
    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
</a>

<div class="nav-label">Courses</div>
<a href="{{ route('student.courses.available') }}" class="nav-item @if (request()->routeIs('student.courses.available')) active @endif">
    <i class="bi bi-book"></i><span>Available Courses</span>
</a>
<a href="{{ route('student.my.enrollments') }}" class="nav-item @if (request()->routeIs('student.my.enrollments')) active @endif">
    <i class="bi bi-list-check"></i><span>My Enrollments</span>
</a>

<div class="nav-label">Attendance</div>
<a href="{{ route('student.scan') }}" class="nav-item @if (request()->routeIs('student.scan')) active @endif">
    <i class="bi bi-qr-code"></i><span>QR Attendance</span>
</a>
<a href="{{ route('student.attendance') }}" class="nav-item @if (request()->routeIs('student.attendance')) active @endif">
    <i class="bi bi-calendar-check"></i><span>My Attendance</span>
</a>

<div class="nav-label">Academic</div>
<a href="{{ route('student.timetable') }}" class="nav-item @if (request()->routeIs('student.timetable')) active @endif">
    <i class="bi bi-clock"></i><span>Timetable</span>
</a>
<a href="{{ route('student.progress') }}" class="nav-item @if (request()->routeIs('student.progress')) active @endif">
    <i class="bi bi-graph-up"></i><span>My Progress</span>
</a>

<div class="nav-label">Communication</div>
{{-- FIXED: Changed from student.announcements to student.announcements.index --}}
<a href="{{ route('student.announcements.index') }}" class="nav-item @if (request()->routeIs('student.announcements*')) active @endif">
    <i class="bi bi-megaphone"></i>
    <span>Announcements</span>
    <span id="studentAnnouncementBadge"
        style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto; display:none;">0</span>
</a>
<a href="{{ route('student.messages.inbox') }}" class="nav-item @if (request()->routeIs('student.messages*')) active @endif">
    <i class="bi bi-envelope"></i>
    <span>Messages</span>
    <span id="unreadBadge"
        style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto; display:none;">0</span>
</a>
<a href="{{ route('student.notifications') }}" class="nav-item @if (request()->routeIs('student.notifications')) active @endif">
    <i class="bi bi-bell"></i>
    <span>Notifications</span>
    <span id="notificationBadge"
        style="background:#ef4444; color:white; font-size:0.55rem; padding:0.05rem 0.4rem; border-radius:1rem; margin-left:auto; display:none;">0</span>
</a>

<div class="nav-label">Support</div>
<a href="{{ route('student.chatbot') }}" class="nav-item @if (request()->routeIs('student.chatbot')) active @endif">
    <i class="bi bi-robot"></i><span>Uni Bot</span>
</a>
