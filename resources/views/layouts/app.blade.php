<!DOCTYPE html>
@php
    $locale = session('locale');
    if (!is_string($locale) || !in_array($locale, ['en', 'ar'], true)) {
        $locale = config('app.locale', 'en');
    }
    $appCurrency = \App\Models\Currency::byCode((string) \App\Models\Setting::get('currency', 'AED'));
    $currencySymbol = $appCurrency?->symbol ?? '$';
@endphp
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Aktaš System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap RTL CSS -->
    @if($locale === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @endif
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- html2pdf -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- SheetJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js"></script>
    
    <style>
        :root {
            --primary-color: #7c3aed;
            --secondary-color: #ec4899;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-bg: #0f172a;
            --light-bg: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body.dark-theme {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .dashboard-container {
            padding: 20px;
        }

        .navbar-custom {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-bottom: 2px solid var(--primary-color);
            padding: 15px 20px;
        }

        .navbar-custom .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 24px;
        }

        .nav-link {
            color: #666 !important;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-link.active {
            color: var(--primary-color) !important;
            border-bottom: 3px solid var(--primary-color);
        }

        .sidebar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .sidebar h5 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 15px;
        }

        .sidebar a {
            display: block;
            padding: 10px 15px;
            color: #666;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background: #f0f0f0;
            color: var(--primary-color);
        }

        .sidebar a.active {
            background: var(--primary-color);
            color: white;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 20px;
            border-radius: 12px 12px 0 0;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #6d28d9;
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: .28rem .55rem;
            font-size: .78rem;
            border-radius: 6px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }

        .stat-card h5 {
            font-size: 14px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-card.success {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        .stat-card.danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
        }

        .table {
            border-collapse: collapse;
        }

        .table thead {
            background: var(--light-bg);
            font-weight: 600;
            color: var(--primary-color);
        }

        .table tbody tr:hover {
            background: #f9f9f9;
        }

        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 12px 12px 0 0;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .alert {
            border: none;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading.active {
            display: flex;
        }

        footer {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            text-align: center;
            color: #666;
            margin-top: 40px;
            border-top: 1px solid #e0e0e0;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
            }

            .stat-card .number {
                font-size: 24px;
            }

            .navbar-custom .navbar-brand {
                font-size: 18px;
            }
        }

        /* Fix small-layout sidebar to viewport on larger screens */
        @media (min-width: 992px) {
            .sidebar { position: fixed; top: 100px; left: 20px; width: 260px; z-index: 1050; overflow-y:auto; max-height:calc(100vh - 140px); }
            .content-area { margin-left: 300px; }
        }
    </style>

    @yield('css')
</head>
<body>
    @include('components.top-navbar')

    <!-- Loading -->
    <div class="loading" id="loading">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="dashboard-container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                <strong>{{ __('messages.success') }}!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ __('messages.error') }}!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Warning!</strong> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Info!</strong> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <strong>Status:</strong> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-3">
                <div class="sidebar">
                    <x-sidebar />
                </div>
            </div>
            <div class="col-lg-9 content-area">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toggle Language
        function toggleLanguage() {
            const url = new URL(window.location.href);
            const locale = url.searchParams.get('lang') || '{{ app()->getLocale() }}';
            const newLocale = locale === 'ar' ? 'en' : 'ar';
            url.searchParams.set('lang', newLocale);
            window.location.href = url.toString();
        }

        // Show Loading
        function showLoading() {
            $('#loading').addClass('active');
        }

        // Hide Loading
        function hideLoading() {
            $('#loading').removeClass('active');
        }

        // Auto-hide alerts
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
