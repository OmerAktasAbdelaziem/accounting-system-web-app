<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}" dir="{{ session('locale') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin - Aktaš System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap RTL CSS -->
    @if(session('locale') === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @endif
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        :root {
            --primary-orange: #ff6b35;
            --dark-orange: #e55a2b;
            --light-orange: #ffb366;
            --black: #1a1a1a;
            --white: #ffffff;
            --light-gray: #f5f5f5;
            --border-gray: #e0e0e0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            background: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        /* ============ TOP NAVBAR ============ */
        .super-admin-navbar {
            background: linear-gradient(135deg, var(--black) 0%, #2a2a2a 100%);
            color: var(--white);
            padding: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border-bottom: 3px solid var(--primary-orange);
        }

        .super-admin-navbar .navbar-brand {
            font-weight: 800;
            font-size: 24px;
            color: var(--white);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 20px;
        }

        .super-admin-navbar .navbar-brand i {
            color: var(--primary-orange);
            font-size: 28px;
        }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.7) !important;
            margin: 0 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-orange) !important;
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-orange) !important;
            border-bottom-color: var(--primary-orange);
        }

        .navbar-end {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 20px;
            padding-right: 20px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: bold;
            cursor: pointer;
        }

        /* ============ MAIN CONTAINER ============ */
        .super-admin-container {
            display: flex;
            flex: 1;
            min-height: calc(100vh - 80px);
        }

        /* ============ SIDEBAR ============ */
        .super-admin-sidebar {
            width: 260px;
            background: var(--white);
            padding: 20px 0;
            border-right: 2px solid var(--border-gray);
            overflow-y: auto;
            min-height: calc(100vh - 80px);
            position: sticky;
            top: 80px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-section {
            padding: 0 15px;
            margin-bottom: 20px;
        }

        .sidebar-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--black);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 15px 15px;
            color: #999;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin: 5px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            color: var(--black);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            margin: 0 10px;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary-orange);
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
        }

        .sidebar-menu i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .sidebar-logout {
            margin-top: auto;
            padding: 20px 10px 0 10px;
            border-top: 2px solid var(--border-gray);
        }

        .sidebar-logout a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            background: rgba(200, 0, 0, 0.1);
            color: #c33;
            text-align: left;
            margin: 0 !important;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            width: 100%;
            border: 2px solid #fdd;
        }

        .sidebar-logout a:hover {
            background: rgba(200, 0, 0, 0.15);
            color: #c33;
            border-color: #c33;
            transform: translateX(-5px);
        }

        .sidebar-logout i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        /* ============ MAIN CONTENT ============ */
        .super-admin-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: var(--light-gray);
            min-height: calc(100vh - 80px);
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--black);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .page-title i {
            color: var(--primary-orange);
            font-size: 36px;
        }

        .page-subtitle {
            color: #666;
            font-size: 14px;
        }

        /* ============ CARDS ============ */
        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 5px solid var(--primary-orange);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-content h6 {
            color: #666;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--black);
        }

        .stat-icon {
            font-size: 40px;
            color: var(--primary-orange);
            opacity: 0.2;
        }

        /* ============ BUTTONS ============ */
        .btn-primary-orange {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            border: none;
            color: var(--white);
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
        }

        .btn-primary-orange:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
            color: var(--white);
        }

        .btn-outline-orange {
            border: 2px solid var(--primary-orange);
            color: var(--primary-orange);
            background: transparent;
            font-weight: 600;
            padding: 8px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-orange:hover {
            background: var(--primary-orange);
            color: var(--white);
        }

        .btn-sm {
            padding: 6px 16px;
            font-size: 12px;
        }

        /* ============ TABLES ============ */
        .data-table {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-top: 20px;
        }

        .table {
            margin: 0;
            border-collapse: collapse;
        }

        .table thead th {
            background: linear-gradient(135deg, var(--black) 0%, #2a2a2a 100%);
            color: var(--white);
            border: none;
            font-weight: 600;
            padding: 16px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .table tbody tr {
            border-bottom: 1px solid var(--border-gray);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(255, 107, 53, 0.05);
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            color: var(--black);
        }

        .badge-orange {
            background: linear-gradient(135deg, var(--primary-orange) 0%, var(--dark-orange) 100%);
            color: var(--white);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background: #10b981;
            color: var(--white);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-warning {
            background: #f59e0b;
            color: var(--white);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* ============ FORMS ============ */
        .form-section {
            background: var(--white);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .form-section h5 {
            color: var(--black);
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border-gray);
        }

        .form-label {
            color: var(--black);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-gray);
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            color: var(--black);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        /* ============ MODALS ============ */
        .modal-header {
            background: linear-gradient(135deg, var(--black) 0%, #2a2a2a 100%);
            color: var(--white);
            border: none;
        }

        .modal-title {
            font-weight: 700;
        }

        .btn-close-white {
            filter: invert(1);
        }

        /* ============ ALERTS ============ */
        .alert-orange {
            background: rgba(255, 107, 53, 0.1);
            border: 2px solid var(--primary-orange);
            color: var(--black);
            border-radius: 8px;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 768px) {
            .super-admin-sidebar {
                width: 200px;
            }

            .page-title {
                font-size: 24px;
            }

            .stat-card {
                flex-direction: column;
            }

            .navbar-nav {
                display: none;
            }
        }

        /* ============ SCROLL ============ */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-gray);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-orange);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--dark-orange);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- ============ TOP NAVBAR ============ -->
    <nav class="super-admin-navbar">
        <div class="d-flex align-items-center w-100">
            <a href="{{ route('super-admin.dashboard') }}" class="navbar-brand">
                <i class="bi bi-gem"></i>
                <span>Aktaš Admin</span>
            </a>
            
            <div class="navbar-end">
                <div class="user-menu">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <small class="d-block" style="color: rgba(255,255,255,0.7);">{{ auth()->user()->name }}</small>
                        <small style="color: var(--primary-orange); font-weight: 600;">Super Admin</small>
                    </div>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============ MAIN CONTAINER ============ -->
    <div class="super-admin-container">
        <!-- SIDEBAR -->
        <aside class="super-admin-sidebar">
            <!-- Dashboard -->
            <div class="sidebar-section">
                <div class="sidebar-title">Main</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('super-admin.dashboard') }}" 
                           class="@if(request()->routeIs('super-admin.dashboard')) active @endif">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Management -->
            <div class="sidebar-section">
                <div class="sidebar-title">Management</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('super-admin.merchants.index') }}" 
                           class="@if(request()->routeIs('super-admin.merchants*')) active @endif">
                            <i class="bi bi-building"></i>
                            <span>Clients (Merchants)</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.packages.index') }}" 
                           class="@if(request()->routeIs('super-admin.packages*')) active @endif">
                            <i class="bi bi-box-seam"></i>
                            <span>Packages</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.subscriptions.index') }}" 
                           class="@if(request()->routeIs('super-admin.subscriptions*')) active @endif">
                            <i class="bi bi-bookmark-check"></i>
                            <span>Subscriptions</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Configuration -->
            <div class="sidebar-section">
                <div class="sidebar-title">Configuration</div>
                <ul class="sidebar-menu">
                    <li>
                        <a href="{{ route('super-admin.feature-access.index') }}" 
                           class="@if(request()->routeIs('super-admin.feature-access*')) active @endif">
                            <i class="bi bi-toggles2"></i>
                            <span>Feature Access</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('super-admin.vat-rates.index') }}" 
                           class="@if(request()->routeIs('super-admin.vat-rates*')) active @endif">
                            <i class="bi bi-percent"></i>
                            <span>Tax Rates</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Logout -->
            <div class="sidebar-logout">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-bottom').submit();">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form-bottom" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="super-admin-content">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
