<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MTU Academic Intelligence System')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #800000;
            --primary-dark: #5f0000;
            --secondary: #f4c430;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            color: var(--gray-800);
            line-height: 1.5;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 0.75rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--secondary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1rem;
            color: var(--primary);
        }

        .brand-text h2 {
            font-size: 0.9rem;
            font-weight: 700;
            margin: 0;
        }

        .brand-text p {
            font-size: 0.6rem;
            opacity: 0.7;
            margin: 0;
        }

        .nav-label {
            padding: 0.5rem 0.75rem 0.25rem;
            font-size: 0.6rem !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.4);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.45rem 0.75rem !important;
            margin: 0.15rem 0.6rem;
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            font-size: 0.9rem !important;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-item.active {
            background: var(--secondary);
            color: var(--primary);
        }

        .nav-item i {
            font-size: 1rem !important;
            width: 1.3rem;
        }

        .sidebar-note {
            margin: 0.75rem 0.6rem;
            padding: 0.6rem 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-note strong {
            display: block;
            font-size: 0.7rem !important;
        }

        .sidebar-note p {
            font-size: 0.6rem !important;
            opacity: 0.6;
            margin: 0;
        }

        .nav-container {
            flex: 1;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 1.5rem;
            min-height: 100vh;
            width: calc(100% - 280px);
        }

        /* Top Bar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 0.75rem 1.5rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
        }

        .page-title-section h1 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .welcome-text {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.15rem;
        }

        /* Account Dropdown */
        .account-dropdown {
            position: relative;
            display: inline-block;
        }

        .account-button {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            padding: 0.2rem;
            border-radius: 2rem;
            transition: all 0.2s ease;
        }

        .account-button:hover {
            background: var(--gray-100);
        }

        .account-avatar {
            width: 38px;
            height: 38px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .dropdown-arrow {
            font-size: 0.7rem;
            color: var(--gray-500);
            transition: transform 0.2s ease;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 0.5rem;
            background: white;
            border-radius: 0.75rem;
            box-shadow: var(--shadow-lg);
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
            color: var(--gray-700);
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

        /* ===== RESPONSIVE ===== */
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
                font-size: 1.2rem !important;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .account-dropdown {
                align-self: flex-end;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 65px;
                padding: 0.75rem;
                width: calc(100% - 65px);
            }

            .sidebar {
                width: 65px;
            }

            .topbar {
                padding: 0.6rem 0.75rem;
            }

            .page-title-section h1 {
                font-size: 1rem;
            }

            .welcome-text {
                font-size: 0.6rem;
            }

            .account-avatar {
                width: 32px;
                height: 32px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 430px) {
            .main-content {
                margin-left: 55px;
                padding: 0.5rem;
                width: calc(100% - 55px);
            }

            .sidebar {
                width: 55px;
            }

            .sidebar-header {
                padding: 0.6rem;
            }

            .brand-icon {
                width: 32px;
                height: 32px;
                font-size: 0.75rem;
            }

            .page-title-section h1 {
                font-size: 0.85rem;
            }

            .topbar {
                padding: 0.4rem 0.6rem;
            }

            .account-avatar {
                width: 28px;
                height: 28px;
                font-size: 0.6rem;
            }

            .nav-item {
                padding: 0.4rem !important;
                margin: 0.1rem 0.4rem;
            }

            .nav-item i {
                font-size: 0.9rem !important;
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
                    <div class="brand-icon">Uni</div>
                    <div class="brand-text">
                        <h2>Academic Portal</h2>
                        <p>MTU System</p>
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

                <!-- Account Dropdown -->
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

    <!-- Bootstrap 5 JS Bundle -->
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
    </script>

    @stack('scripts')
</body>

</html>
