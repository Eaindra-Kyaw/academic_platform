<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="user-role" content="{{ Auth::user()->role->name ?? 'student' }}">

    <title>@yield('title', 'MTU Academic Portal')</title>



    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



    <style>
        /* ============================================================
                   MOST OFFICIAL COLORS - From Logo
                   ============================================================ */
        :root {
            --most-navy: #0A2463;
            --most-blue: #1E3A8A;
            --most-light: #3B82F6;
            --most-gold: #D4A017;
            --most-white: #FFFFFF;

            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #3B82F6;
            --accent: #D4A017;
            --accent-light: #E8B831;
            --bg-main: #EEF2F7;
            --bg-sidebar: linear-gradient(180deg, #1E3A8A 0%, #0A2463 100%);
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-500: #64748b;
            --gray-700: #334155;
            --gray-800: #1e293b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            color: var(--text-dark);
            line-height: 1.5;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* ============================================================
                   SIDEBAR - CLEAN & READABLE
                   ============================================================ */
        .sidebar {
            width: 260px;
            background: var(--bg-sidebar);
            color: var(--white);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            box-shadow: 2px 0 24px rgba(10, 36, 99, 0.12);
        }

        /* ===== SIDEBAR HEADER ===== */
        .sidebar-header {
            padding: 1rem 1.25rem 0.8rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 0.5rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--most-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--primary-dark);
            flex-shrink: 0;
        }

        .brand-text h2 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
            color: var(--white);
            letter-spacing: -0.3px;
        }

        .brand-text .sub {
            font-size: 0.5rem;
            opacity: 0.6;
            margin: 0;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.5px;
            display: block;
            margin-top: 1px;
        }

        .brand-text .sub .gold {
            color: var(--most-gold);
            font-weight: 700;
        }

        /* ===== NAV LABELS - MORE VISIBLE ===== */
        .nav-label {
            padding: 0.8rem 1.25rem 0.3rem;
            font-size: 0.65rem !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.35);
        }

        /* ===== NAV ITEMS - BIGGER & CLEARER ===== */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.6rem 1rem !important;
            margin: 0.1rem 0.7rem;
            border-radius: 10px;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.95rem !important;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: var(--white);
        }

        /* ===== ACTIVE SIDEBAR - GOLD ===== */
        .nav-item.active {
            background: var(--most-gold);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-item.active i {
            color: var(--primary);
        }

        /* ===== ICONS - BIGGER ===== */
        .nav-item i {
            font-size: 1.15rem !important;
            width: 1.4rem;
            flex-shrink: 0;
            color: rgba(255, 255, 255, 0.5);
        }

        .nav-item.active i {
            color: var(--primary);
        }

        /* ===== SIDEBAR NOTE ===== */
        .sidebar-note {
            margin: 0.5rem 0.8rem 0.8rem;
            padding: 0.7rem 0.9rem;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-note strong {
            display: block;
            font-size: 0.7rem !important;
            color: var(--white);
        }

        .sidebar-note p {
            font-size: 0.6rem !important;
            opacity: 0.5;
            margin: 0;
            color: rgba(255, 255, 255, 0.6);
        }

        .nav-container {
            flex: 1;
            overflow-y: auto;
        }

        /* ============================================================
                   SIDEBAR SCROLLBAR
                   ============================================================ */
        .sidebar::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* ============================================================
                   MAIN CONTENT
                   ============================================================ */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 1.5rem;
            min-height: 100vh;
            width: calc(100% - 260px);
            max-width: 100%;
            overflow-x: hidden;
            background: var(--bg-main);
        }

        /* ============================================================
                   TOP BAR
                   ============================================================ */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--white);
            padding: 0.7rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(10, 36, 99, 0.04);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .page-title-section h1 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .page-title-section h1 .gold {
            color: var(--most-gold);
        }

        .welcome-text {
            font-size: 0.75rem;
            color: var(--text-gray);
            margin-top: 0.1rem;
        }

        /* ============================================================
                   ACCOUNT DROPDOWN
                   ============================================================ */
        .account-dropdown {
            position: relative;
            display: inline-block;
        }

        .account-button {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            padding: 0.2rem 0.5rem;
            border-radius: 2rem;
            transition: all 0.2s ease;
        }

        .account-button:hover {
            background: var(--gray-100);
        }

        .account-avatar {
            width: 34px;
            height: 34px;
            background: var(--most-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: 700;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .dropdown-arrow {
            font-size: 0.65rem;
            color: var(--text-gray);
            transition: transform 0.2s ease;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-hover);
            border: 1px solid var(--gray-200);
            min-width: 160px;
            z-index: 1000;
            display: none;
            overflow: hidden;
        }

        .dropdown-menu.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 1rem;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 0.75rem;
            transition: all 0.2s ease;
            cursor: pointer;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
        }

        .dropdown-item:hover {
            background: var(--gray-50);
            color: var(--primary);
        }

        .dropdown-item i {
            width: 1rem;
            font-size: 0.85rem;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 0.25rem 0;
        }

        .text-danger {
            color: var(--danger);
        }

        .text-danger:hover {
            background: #fef2f2;
            color: var(--danger);
        }

        /* ============================================================
                   RESPONSIVE
                   ============================================================ */
        @media (max-width: 1024px) {
            .sidebar {
                width: 72px;
            }

            .sidebar .brand-text,
            .sidebar .nav-label,
            .sidebar .nav-item span,
            .sidebar-note {
                display: none;
            }

            .main-content {
                margin-left: 72px;
                padding: 1rem;
                width: calc(100% - 72px);
            }

            .nav-item {
                justify-content: center;
                padding: 0.6rem !important;
            }

            .nav-item i {
                width: auto;
                font-size: 1.3rem !important;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .account-dropdown {
                align-self: flex-end;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 60px;
                padding: 0.75rem;
                width: calc(100% - 60px);
            }

            .sidebar {
                width: 60px;
            }

            .sidebar-header {
                padding: 0.6rem;
            }

            .brand-icon {
                width: 30px;
                height: 30px;
                font-size: 0.7rem;
            }

            .topbar {
                padding: 0.5rem 0.75rem;
            }

            .page-title-section h1 {
                font-size: 1rem;
            }

            .welcome-text {
                font-size: 0.65rem;
            }

            .account-avatar {
                width: 28px;
                height: 28px;
                font-size: 0.65rem;
            }
        }

        @media (max-width: 430px) {
            .main-content {
                margin-left: 52px;
                padding: 0.5rem;
                width: calc(100% - 52px);
            }

            .sidebar {
                width: 52px;
            }

            .sidebar-header {
                padding: 0.4rem;
            }

            .brand-icon {
                width: 26px;
                height: 26px;
                font-size: 0.6rem;
                border-radius: 6px;
            }

            .page-title-section h1 {
                font-size: 0.85rem;
            }

            .topbar {
                padding: 0.4rem 0.6rem;
            }

            .account-avatar {
                width: 24px;
                height: 24px;
                font-size: 0.55rem;
            }

            .nav-item {
                padding: 0.4rem !important;
                margin: 0.05rem 0.3rem;
            }

            .nav-item i {
                font-size: 0.95rem !important;
            }
        }
    </style>

    @stack('styles')

</head>

<body>

    <div class="app">

        <aside class="sidebar">

            <div class="sidebar-header">

                <div class="brand">

                    <div class="brand-icon">MTU</div>

                    <div class="brand-text">

                        <h2>University Portal</h2>

                    </div>

                </div>

            </div>



            <div class="nav-container">

                @yield('sidebar')

            </div>

        </aside>



        <main class="main-content">

            <div class="topbar">

                <div class="page-title-section">

                    <h1>@yield('page-title')</h1>

                    <p class="welcome-text">@yield('welcome-text')</p>

                </div>



                <div class="account-dropdown" id="accountDropdown">

                    <div class="account-button" onclick="toggleDropdown()">

                        <div class="account-avatar">{{ substr(Auth::user()->name ?? 'U', 0, 2) }}</div>

                        <i class="bi bi-chevron-down dropdown-arrow" id="dropdownArrow"></i>

                    </div>

                    <div class="dropdown-menu" id="dropdownMenu">

                        <form method="POST" action="{{ route('logout') }}" id="logout-form">

                            @csrf

                            <button type="submit" class="dropdown-item text-danger">

                                <i class="bi bi-box-arrow-right"></i>

                                <span>Logout</span>

                            </button>

                        </form>

                    </div>

                </div>

            </div>



            @yield('content')

        </main>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



    <script>
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            const arrow = document.getElementById('dropdownArrow');
            menu.classList.toggle('show');
            arrow.style.transform = menu.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('accountDropdown');
            if (!dropdown.contains(event.target)) {
                document.getElementById('dropdownMenu').classList.remove('show');
                document.getElementById('dropdownArrow').style.transform = 'rotate(0deg)';
            }
        });

        function updateAnnouncementBadge() {
            const userRole = document.querySelector('meta[name="user-role"]')?.content || 'student';
            let url = '';
            if (userRole === 'admin') url = '/admin/announcements/unread-count';
            else if (userRole === 'lecturer') url = '/lecturer/announcements/unread-count';
            else url = '/student/announcements/unread-count';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    cache: 'no-cache'
                })
                .then(response => response.json())
                .then(data => {
                    const badges = document.querySelectorAll('.nav-item span[style*="background:#ef4444"]');
                    badges.forEach(badge => {
                        if (data.count && data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = 'inline-block';
                            badge.style.background = '#ef4444';
                            badge.style.color = 'white';
                            badge.style.fontSize = '0.55rem';
                            badge.style.padding = '0.05rem 0.4rem';
                            badge.style.borderRadius = '1rem';
                            badge.style.minWidth = '1.2rem';
                            badge.style.textAlign = 'center';
                            badge.style.marginLeft = 'auto';
                        } else {
                            badge.style.display = 'none';
                        }
                    });
                })
                .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(updateAnnouncementBadge, 500);
        });

        setInterval(updateAnnouncementBadge, 30000);

        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setTimeout(updateAnnouncementBadge, 200);
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
