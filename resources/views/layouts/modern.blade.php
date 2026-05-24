<!DOCTYPE html>
<html lang="{{ session('locale', 'en') }}" dir="{{ session('locale') === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    @php
        $appCurrency = \App\Models\Currency::byCode((string) \App\Models\Setting::get('currency', 'AED'));
        $currencySymbol = $appCurrency?->symbol ?? '$';
    @endphp
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

        .nav-user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.6);
            flex: 0 0 auto;
        }

        .nav-user-avatar-fallback {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-orange), var(--primary-green));
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            border: 2px solid rgba(255,255,255,0.6);
            flex: 0 0 auto;
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

        .btn-sm {
            padding: .28rem .55rem;
            font-size: .78rem;
            border-radius: 6px;
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

            /* Sidebar layout for modern pages */
            .modern-container {
                display: flex;
                gap: 20px;
                max-width: 1400px;
                margin: 0 auto;
                padding: 30px;
            }

                .modern-sidebar {
                width: 260px;
                background: #ffffff;
                border-right: 1px solid #eee;
                border-radius: 8px;
                padding: 12px;
                flex-shrink: 0;
                min-height: calc(100vh - 120px);
                overflow-y: auto;
                position: sticky;
                top: 100px;
            }

            .modern-sidebar .sidebar-menu { list-style: none; padding: 0; margin: 0; }
            .modern-sidebar .sidebar-menu li { margin: 6px 0; }
            .modern-sidebar .sidebar-menu a { display:flex; gap:12px; align-items:center; padding:10px; color:var(--primary-black); text-decoration:none; border-radius:6px; }
            .modern-sidebar .sidebar-menu a:hover { background: var(--orange-light); color: var(--primary-orange); transform: translateX(4px); }
            .modern-sidebar .sidebar-menu a.active { background: linear-gradient(135deg, var(--primary-orange), var(--primary-green)); color:#fff; }

            .modern-content { flex:1; padding: 0; }

            .modern-sidebar .sidebar-logout { margin-top: auto; padding: 10px; }
            .modern-sidebar .sidebar-logout-link {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 10px 12px;
                width: 100%;
                text-decoration: none;
                color: #c33;
                background: rgba(200,0,0,0.06);
                border-radius: 8px;
                border: 1px solid #f8dede;
                font-weight: 600;
            }
            .modern-sidebar .sidebar-logout-link:hover { background: rgba(200,0,0,0.1); transform: translateX(-4px); }

            @media (max-width: 992px) {
                .modern-sidebar { display: none; }
                .modern-container { padding: 15px; }
            }

            /* Make sidebar fixed on larger screens so it stays visible while scrolling */
            @media (min-width: 992px) {
                .modern-container { position: relative; }
                .modern-sidebar {
                    position: fixed;
                    top: 100px;
                    left: 20px;
                    z-index: 1050;
                }
                .modern-content { margin-left: 320px; }
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
    @auth
        <!-- Slim Top Navbar (brand + toggle) -->
        <nav class="navbar navbar-expand-lg navbar-dark navbar-modern">
            <div class="container-fluid">
                <div class="d-flex align-items-center w-100">
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        {{ \App\Models\Setting::getApplicationName() }}
                    </a>
                    <div class="ms-auto d-flex align-items-center gap-3">
                        <button class="btn btn-outline-light d-lg-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @php
                                    $avatarPath = auth()->user()->profile_photo_path;
                                    $navbarAvatar = null;
                                    if ($avatarPath) {
                                        if (\Illuminate\Support\Facades\File::exists(public_path($avatarPath))) {
                                            $navbarAvatar = asset($avatarPath);
                                        } elseif (\Illuminate\Support\Facades\File::exists(public_path('storage/' . ltrim($avatarPath, '/')))) {
                                            $navbarAvatar = asset('storage/' . ltrim($avatarPath, '/'));
                                        } else {
                                            $navbarAvatar = asset($avatarPath);
                                        }
                                    }
                                    $navbarInitial = strtoupper(substr(auth()->user()->name ?? 'U', 0, 1));
                                @endphp
                                @if($navbarAvatar)
                                    <img src="{{ $navbarAvatar }}" alt="{{ auth()->user()->name }}" class="nav-user-avatar me-2">
                                @else
                                    <span class="nav-user-avatar-fallback me-2">{{ $navbarInitial }}</span>
                                @endif
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person"></i> {{ __('messages.profile') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="bi bi-gear"></i> {{ __('messages.settings') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item btn btn-link p-0 m-0" style="text-decoration:none; color:inherit;"><i class="bi bi-box-arrow-right"></i> {{ __('messages.logout') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar + Content container -->
        <div class="modern-container">
            <aside class="modern-sidebar" id="modernSidebar">
                <x-sidebar />
            </aside>

            <main class="modern-content">
    @endauth

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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <i class="bi bi-bell"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    @auth
            </main>
        </div>
    @endauth

    @auth
        @include('components.floating-chat')
    @endauth

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
        // Sidebar toggle for small screens
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('modernSidebar');
            if (toggle && sidebar) {
                toggle.addEventListener('click', function() {
                    if (sidebar.style.display === 'block') {
                        sidebar.style.display = 'none';
                    } else {
                        sidebar.style.display = 'block';
                        sidebar.style.position = 'fixed';
                        sidebar.style.zIndex = '1050';
                        sidebar.style.left = '10px';
                        sidebar.style.top = '80px';
                        sidebar.style.boxShadow = '0 8px 30px rgba(0,0,0,0.2)';
                    }
                });
            }
        });
    </script>

    @stack('scripts')

    @yield('js')
</body>
</html>
