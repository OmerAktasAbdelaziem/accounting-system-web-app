<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}" dir="{{ session('locale') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aktaš System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap RTL CSS -->
    @if(session('locale') === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @endif
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome (icons used across the app) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        :root {
            --primary-black: #1a1a1a;
            --primary-orange: #ff8c00;
            --primary-green: #27ae60;
            --dark-bg: #0d0d0d;
            --light-bg: #f5f5f5;
            --orange-light: #ffe8cc;
            --green-light: #d5f4e6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--primary-black);
        }

        /* Navbar Styling */
        .navbar-modern {
            background: linear-gradient(135deg, var(--primary-black) 0%, #2a2a2a 100%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 15px 30px;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-modern .navbar-brand {
            font-weight: 900;
            color: var(--primary-orange) !important;
            font-size: 26px;
            letter-spacing: 1px;
        }

        .navbar-modern .nav-link {
            color: #ffffff !important;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 10px;
            position: relative;
        }

        .navbar-modern .nav-link:hover,
        .navbar-modern .nav-link.active {
            color: var(--primary-orange) !important;
        }

        .navbar-modern .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-orange), var(--primary-green));
            border-radius: 2px;
        }

        /* Dashboard Container */
        .dashboard-container {
            padding: 30px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 5px solid var(--primary-orange);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(255, 140, 0, 0.1), transparent);
            border-radius: 50%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 140, 0, 0.15);
            border-left-color: var(--primary-green);
        }

        .stat-card.green {
            border-left-color: var(--primary-green);
        }

        .stat-card.green:hover {
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.15);
        }

        .stat-card h6 {
            color: #999;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .stat-card .value {
            color: var(--primary-black);
            font-size: 32px;
            font-weight: 900;
        }

        .stat-card .icon {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 40px;
            color: var(--primary-orange);
            opacity: 0.2;
        }

        .stat-card.green .icon {
            color: var(--primary-green);
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 25px;
            overflow: hidden;
        }

        .card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-black) 0%, #2a2a2a 100%);
            color: white;
            border: none;
            padding: 20px 25px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .card-header .badge {
            background: var(--primary-orange);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            margin-left: auto;
        }

        .card-body {
            padding: 25px;
        }

        /* Buttons */
        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary-orange) 0%, #ff7700 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 140, 0, 0.3);
            color: white;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, var(--primary-green) 0%, #229954 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-success-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
            color: white;
        }

        /* Action Buttons: compact, icon-focused buttons used in tables */
        .action-buttons .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            min-width: 38px;
            padding: .35rem .5rem;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .action-buttons .btn i {
            font-size: 1rem;
            line-height: 1;
        }

        [dir="rtl"] .action-buttons .btn {
            flex-direction: row-reverse;
        }

        /* Table Styling */
        .table {
            margin-bottom: 0;
        }

        /* Apply light header only to non-dark tables so .table-dark retains Bootstrap styling */
        .table:not(.table-dark) thead {
            background: #f9f9f9;
        }

        .table:not(.table-dark) thead th {
            color: var(--primary-black);
            font-weight: 700;
            border: none;
            padding: 15px;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--primary-orange);
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: var(--orange-light);
        }

        .table tbody td {
            vertical-align: middle;
            padding: 15px;
            border-top: 1px solid #f0f0f0;
        }

        /* Form Styling */
        .form-label {
            font-weight: 600;
            color: var(--primary-black);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-orange);
            box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1);
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 11px;
        }

        .badge-orange {
            background: var(--orange-light);
            color: var(--primary-orange);
        }

        .badge-green {
            background: var(--green-light);
            color: var(--primary-green);
        }

        .badge-black {
            background: #e8e8e8;
            color: var(--primary-black);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid var(--primary-orange);
            margin-bottom: 20px;
        }

        .alert-success {
            background: var(--green-light);
            color: var(--primary-green);
            border-left-color: var(--primary-green);
        }

        .alert-danger {
            background: #ffe8e8;
            color: #c0392b;
            border-left-color: #c0392b;
        }

        /* Loading Spinner */
        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading.active {
            display: flex;
        }

        .spinner-border {
            color: var(--primary-orange) !important;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--primary-black) 0%, #2a2a2a 100%);
            color: #999;
            padding: 30px;
            text-align: center;
            margin-top: 50px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }

            .navbar-modern {
                padding: 10px 15px;
            }

            .navbar-modern .navbar-brand {
                font-size: 20px;
            }

            .stat-card {
                padding: 15px;
                margin-bottom: 15px;
            }

            .stat-card .value {
                font-size: 24px;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
                color: #ffffff;
            }

            .card {
                background: #2a2a2a;
                color: #ffffff;
            }

            .table {
                color: #ffffff;
            }

            .table thead th {
                background: #1a1a1a;
                color: #ffffff;
            }

            .form-control,
            .form-select {
                background: #2a2a2a;
                color: #ffffff;
                border-color: #444;
            }
        }
    </style>

    @yield('css')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-modern">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('system.dashboard') }}">
                <i class="bi bi-graph-up-arrow"></i> Aktaš
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('system.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> {{ __('messages.dashboard') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                            <i class="bi bi-box-seam"></i> {{ __('messages.products') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="bi bi-tags"></i> {{ __('messages.categories') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('employees*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                            <i class="bi bi-people"></i> {{ __('messages.employees') }}
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-building"></i> {{ __('messages.customers') }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('customers.index') }}"><i class="bi bi-people-fill"></i> {{ __('messages.customers') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('suppliers.index') }}"><i class="bi bi-truck"></i> {{ __('messages.suppliers') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('invoices.index') }}"><i class="bi bi-receipt"></i> {{ __('messages.invoices') }}</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('payroll.index') }}"><i class="bi bi-cash-coin"></i> {{ __('messages.payroll') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('branches.index') }}"><i class="bi bi-shop"></i> {{ __('messages.branches') }}</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-text"></i> {{ __('messages.reports') }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('reports.sales') }}">{{ __('messages.sales_report') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.inventory') }}">{{ __('messages.inventory_report') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('reports.financial') }}">{{ __('messages.financial_report') }}</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear"></i> {{ __('messages.systems') }}
                        </a>
                        <ul class="dropdown-menu">
                            @if (auth()->check() && auth()->user()?->role?->name === 'Admin')
                                <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="bi bi-people"></i> {{ __('messages.admin_user') }}</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('commissions.index') }}"><i class="bi bi-percent"></i> {{ __('messages.commissions') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('storages.index') }}"><i class="bi bi-archive"></i> {{ __('messages.storages') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('safes.index') }}"><i class="bi bi-safe"></i> {{ __('messages.safes') }}</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleLanguage(); return false;">
                            <i class="bi bi-globe"></i> {{ app()->getLocale() === 'ar' ? 'EN' : 'AR' }}
                        </a>
                    </li>
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person"></i> {{ __('messages.profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="bi bi-gear"></i> {{ __('messages.settings') }}</a></li>
                            @if (auth()->user()?->role?->name === 'Admin')
                                <li><a class="dropdown-item" href="{{ route('users.index') }}"><i class="bi bi-people"></i> {{ __('users.users_management') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('roles.index') }}"><i class="bi bi-shield-alt"></i> {{ __('roles.roles_management') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('permissions.index') }}"><i class="bi bi-key"></i> {{ __('permissions.permissions_management') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('audit-logs.index') }}"><i class="bi bi-file-earmark-text"></i> {{ __('audit_logs.audit_logs') }}</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right"></i> {{ __('messages.logout') }}</a></li>
                        </ul>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> {{ __('messages.login') }}
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Loading -->
    <div class="loading" id="loading">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>{{ __('messages.error') }}!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} Aktaš System | Modern Accounting Management</p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function toggleLanguage() {
            const url = new URL(window.location.href);
            const locale = url.searchParams.get('lang') || '{{ app()->getLocale() }}';
            const newLocale = locale === 'ar' ? 'en' : 'ar';
            url.searchParams.set('lang', newLocale);
            window.location.href = url.toString();
        }

        function showLoading() {
            $('#loading').addClass('active');
        }

        function hideLoading() {
            $('#loading').removeClass('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>

    @yield('js')
</body>
</html>
