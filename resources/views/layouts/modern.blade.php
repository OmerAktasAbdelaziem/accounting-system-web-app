<!DOCTYPE html>
@php
    $locale = session('locale');
    if (!is_string($locale) || !in_array($locale, ['en', 'ar', 'tr'], true)) {
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
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=noto-sans:400,500,600,700" rel="stylesheet" />
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

        /* Ensure all top-level headings are legible on dark sections */
        h1 {
            color: #fff !important;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body,
        button,
        input,
        select,
        textarea,
        table,
        th,
        td,
        .btn,
        .nav,
        .navbar,
        .dropdown-menu,
        .card,
        .modal,
        .form-control,
        .form-select,
        .alert,
        .badge {
            font-family: 'Noto Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            min-height: 100vh;
            font-weight: 500;
            color: var(--primary-black);
            overflow-x: hidden;
        }

        @media (max-width: 900px) {
            body {
                background: #191917;
                background-color: #191917;
                color: #f3f4f6;
            }

            html {
                background: #191917;
            }
        }

        body,
        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        button,
        input,
        select,
        textarea {
            font-weight: 500;
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

        .btn,
        .btn-sm,
        .btn-lg {
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-sm {
            padding: .42rem .75rem;
            font-size: .85rem;
        }

        .btn-lg {
            padding: .9rem 1.2rem;
        }

        .dashboard-container h1,
        .dashboard-container h2,
        .dashboard-container h3,
        .dashboard-container h4,
        .dashboard-container h5,
        .dashboard-container h6 {
            letter-spacing: -0.03em;
            color: var(--primary-black);
        }

        .dashboard-container .table-responsive {
            border-radius: 16px;
        }

        .dashboard-container .table {
            color: var(--primary-black);
        }

        .dashboard-container .table thead th {
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .dashboard-container .table tbody td {
            font-size: 13px;
            vertical-align: middle;
        }

        .dashboard-container .card,
        .dashboard-container .modal-content,
        .dashboard-container .alert,
        .dashboard-container .page-header,
        .dashboard-container .form-section {
            border-radius: 18px;
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

            .dashboard-container .container-fluid,
            .dashboard-container .container,
            .dashboard-container .row {
                max-width: 100%;
                margin-left: 0;
                margin-right: 0;
            }

            .navbar-modern {
                padding: 10px 15px;
                background: linear-gradient(135deg, #23211d 0%, #191917 100%);
                border-bottom-color: rgba(255,255,255,0.06);
                color: #f5f3ee;
            }

            .navbar-modern .navbar-brand {
                font-size: 20px;
                color: #f5f3ee !important;
            }

            .stat-card {
                padding: 15px;
                margin-bottom: 15px;
            }

            .stat-card .value {
                font-size: 24px;
            }

            .modern-container {
                flex-direction: column;
                gap: 12px;
                padding: 12px;
            }

            .modern-content {
                margin-left: 0 !important;
            }

            .modern-sidebar {
                width: min(86vw, 320px);
                min-height: 100vh;
                position: fixed;
                top: 0;
                left: 0;
                border-right: 0;
                border-radius: 0;
                box-shadow: 0 6px 18px rgba(0,0,0,.08);
                transform: translateX(-105%);
                transition: transform .25s ease, box-shadow .25s ease;
                z-index: 1060;
            }

            body.sidebar-open .modern-sidebar {
                transform: translateX(0);
                overflow: hidden;
            }

            body.sidebar-open .modern-sidebar,
            .modern-sidebar {
                display: block;
            }

            .card-body {
                padding: 18px;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                min-width: 640px;
            }

            .modal-dialog {
                margin: 0.75rem;
            }

            .modal-content {
                border-radius: 16px;
            }
        }

        @media (max-width: 576px) {
            .dashboard-container {
                padding: 10px;
            }

            .card-body {
                padding: 14px;
            }

            .table {
                min-width: 560px;
            }

            .btn,
            .btn-sm {
                white-space: normal;
            }

            .navbar-modern {
                padding: 10px 12px;
            }

            .navbar-modern .navbar-brand {
                font-size: 16px;
            }

            .modern-sidebar {
                width: 88vw;
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
                    height: calc(100vh - 120px);
                    max-height: calc(100vh - 120px);
                    overflow-y: auto;
                    overflow-x: hidden;
                    overscroll-behavior: contain;
                    -webkit-overflow-scrolling: touch;
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
                    height: calc(100vh - 120px);
                    max-height: calc(100vh - 120px);
                    overflow-y: auto;
                    overflow-x: hidden;
                    overscroll-behavior: contain;
                    -webkit-overflow-scrolling: touch;
                }
                .modern-content { margin-left: 320px; }
                /* hide mobile dock on larger screens */
                .mobile-merchant-dock { display: none !important; }
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

        @media (max-width: 768px) {
            :root {
                color-scheme: dark;
            }

            body {
                background: #191917;
                color: #f5f3ee;
            }

            body::before {
                content: '';
                position: fixed;
                inset: 0;
                pointer-events: none;
                background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
                background-size: 24px 24px;
                opacity: 0.12;
                z-index: 0;
            }

            .dashboard-container,
            .modern-container {
                position: relative;
                z-index: 1;
            }

            .dashboard-container {
                padding: 12px 12px 96px;
            }

            .dashboard-container .page-header,
            .dashboard-container .form-section,
            .dashboard-container .card,
            .dashboard-container .alert,
            .dashboard-container .modal-content,
            .dashboard-container .table-responsive,
            .dashboard-container .list-group,
            .dashboard-container .dropdown-menu {
                border-radius: 22px !important;
            }

            .dashboard-container .page-header,
            .dashboard-container .form-section,
            .dashboard-container .card,
            .dashboard-container .alert,
            .dashboard-container .table-responsive,
            .dashboard-container .modal-content {
                background: rgba(255, 255, 255, 0.82) !important;
                border: 1px solid rgba(255, 255, 255, 0.85);
                box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
            }

            .dashboard-container .card,
            .dashboard-container .form-section {
                overflow: hidden;
            }

            .dashboard-container .card-header {
                background: linear-gradient(135deg, #ffffff 0%, #fff4e8 100%);
                color: var(--primary-black);
                border-bottom: 1px solid rgba(255, 140, 0, 0.12);
                padding: 16px 18px;
            }

            .dashboard-container .card-body {
                padding: 16px;
            }

            .dashboard-container .page-title,
            .dashboard-container h1,
            .dashboard-container h2,
            .dashboard-container h3,
            .dashboard-container h4,
            .dashboard-container h5 {
                letter-spacing: -0.03em;
            }

            .dashboard-container .page-title {
                font-size: 1.5rem;
                line-height: 1.05;
            }

            .dashboard-container .page-subtitle,
            .dashboard-container .text-muted {
                color: #64748b !important;
            }

            .dashboard-container .btn,
            .dashboard-container .btn-sm {
                border-radius: 14px;
                box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
            }

            .dashboard-container .d-flex.flex-wrap.justify-content-between.align-items-start.gap-3,
            .dashboard-container .d-flex.gap-2,
            .dashboard-container .d-flex.flex-wrap.gap-2,
            .dashboard-container .d-flex.gap-3,
            .dashboard-container .d-flex.flex-wrap.gap-3,
            .dashboard-container .col-12.d-flex.gap-2.pt-2,
            .dashboard-container .col-12.d-flex.flex-wrap.gap-2.pt-2,
            .dashboard-container .btn-group,
            .dashboard-container .btn-toolbar,
            .dashboard-container .form-footer,
            .dashboard-container .card-footer,
            .dashboard-container .modal-footer,
            .dashboard-container .page-actions,
            .dashboard-container .hero-actions,
            .dashboard-container .header-actions,
            .dashboard-container .filter-actions,
            .dashboard-container .content-actions,
            .dashboard-container .action-buttons,
            .dashboard-container .stacked-actions,
            .modern-container .btn-group,
            .modern-container .btn-toolbar,
            .modern-container .form-footer,
            .modern-container .card-footer,
            .modern-container .modal-footer,
            .modern-container .page-actions,
            .modern-container .hero-actions,
            .modern-container .header-actions,
            .modern-container .filter-actions,
            .modern-container .content-actions,
            .modern-container .action-buttons,
            .modern-container .stacked-actions,
            .modern-container .d-flex.flex-wrap.justify-content-between.align-items-start.gap-3,
            .modern-container .d-flex.gap-2,
            .modern-container .d-flex.flex-wrap.gap-2,
            .modern-container .d-flex.gap-3,
            .modern-container .d-flex.flex-wrap.gap-3,
            .modern-container .col-12.d-flex.gap-2.pt-2,
            .modern-container .col-12.d-flex.flex-wrap.gap-2.pt-2 {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .dashboard-container .btn-group > .btn,
            .dashboard-container .btn-toolbar > .btn,
            .dashboard-container .form-footer > .btn,
            .dashboard-container .card-footer > .btn,
            .dashboard-container .modal-footer > .btn,
            .dashboard-container .page-actions > .btn,
            .dashboard-container .hero-actions > .btn,
            .dashboard-container .header-actions > .btn,
            .dashboard-container .filter-actions > .btn,
            .dashboard-container .content-actions > .btn,
            .dashboard-container .action-buttons > .btn,
            .dashboard-container .stacked-actions > .btn,
            .dashboard-container .d-flex.flex-wrap.justify-content-between.align-items-start.gap-3 > .btn,
            .dashboard-container .d-flex.gap-2 > .btn,
            .dashboard-container .d-flex.flex-wrap.gap-2 > .btn,
            .dashboard-container .d-flex.gap-3 > .btn,
            .dashboard-container .d-flex.flex-wrap.gap-3 > .btn,
            .dashboard-container .col-12.d-flex.gap-2.pt-2 > .btn,
            .dashboard-container .col-12.d-flex.flex-wrap.gap-2.pt-2 > .btn,
            .modern-container .btn-group > .btn,
            .modern-container .btn-toolbar > .btn,
            .modern-container .form-footer > .btn,
            .modern-container .card-footer > .btn,
            .modern-container .modal-footer > .btn,
            .modern-container .page-actions > .btn,
            .modern-container .hero-actions > .btn,
            .modern-container .header-actions > .btn,
            .modern-container .filter-actions > .btn,
            .modern-container .content-actions > .btn,
            .modern-container .action-buttons > .btn,
            .modern-container .stacked-actions > .btn,
            .modern-container .d-flex.flex-wrap.justify-content-between.align-items-start.gap-3 > .btn,
            .modern-container .d-flex.gap-2 > .btn,
            .modern-container .d-flex.flex-wrap.gap-2 > .btn,
            .modern-container .d-flex.gap-3 > .btn,
            .modern-container .d-flex.flex-wrap.gap-3 > .btn,
            .modern-container .col-12.d-flex.gap-2.pt-2 > .btn,
            .modern-container .col-12.d-flex.flex-wrap.gap-2.pt-2 > .btn {
                width: 100%;
                justify-content: center;
                margin-left: 0 !important;
            }

            .dashboard-container .table-responsive {
                padding: 6px;
                border: 1px solid rgba(255, 255, 255, 0.86);
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .dashboard-container .table {
                min-width: 720px;
            }

            .dashboard-container .table thead th {
                background: transparent;
                color: #0f172a;
                border-bottom: 1px solid rgba(148, 163, 184, 0.18);
            }

            .dashboard-container .table tbody tr {
                background: rgba(255, 255, 255, 0.86);
            }

            .dashboard-container .table tbody td {
                border-top: 1px solid rgba(226, 232, 240, 0.75);
            }

            .dashboard-container .form-control,
            .dashboard-container .form-select {
                background: rgba(255,255,255,.96);
                border: 1px solid rgba(148,163,184,.28);
                border-radius: 16px;
                padding: 12px 14px;
                min-height: 48px;
            }

            .dashboard-container .input-group-text {
                border-radius: 16px;
                background: rgba(255,255,255,.96);
                border: 1px solid rgba(148,163,184,.28);
            }

            .dashboard-container .badge {
                border-radius: 999px;
                padding: 7px 11px;
            }

            .dashboard-container .alert {
                border-left-width: 0;
                border-top: 3px solid var(--primary-orange);
            }

            .dashboard-container .alert-success { background: linear-gradient(135deg, #ecfdf5 0%, #f8fffb 100%); }
            .dashboard-container .alert-danger { background: linear-gradient(135deg, #fff1f2 0%, #fff8f8 100%); }
            .dashboard-container .alert-warning { background: linear-gradient(135deg, #fff7ed 0%, #fffdf7 100%); }
            .dashboard-container .alert-info { background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%); }

            .dashboard-container .modal-content {
                border: 1px solid rgba(255,255,255,.9);
            }

            .dashboard-container .modal-header,
            .dashboard-container .modal-footer {
                border-color: rgba(148,163,184,.12);
            }

            .dashboard-container .spinner-border {
                color: var(--primary-orange) !important;
            }

            .navbar-modern {
                padding: 10px 12px;
                background: linear-gradient(135deg, rgba(255,255,255,0.92), rgba(248,251,255,0.92));
                border-bottom: 1px solid rgba(255,255,255,0.9);
                box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            }

            .navbar-modern .navbar-brand {
                font-size: 18px;
            }

            .modern-sidebar {
                display: none !important;
            }

            body.sidebar-open .modern-sidebar {
                display: block !important;
                position: fixed;
                top: 72px;
                left: 10px;
                width: min(86vw, 320px);
                height: calc(100vh - 92px);
                z-index: 2005;
                overflow: hidden;
                background: linear-gradient(180deg, #23211d 0%, #191917 100%);
                border: 1px solid rgba(255,255,255,0.08);
                border-radius: 20px;
                box-shadow: 0 18px 40px rgba(0,0,0,0.28);
                padding: 10px;
            }

            body.sidebar-open .modern-sidebar .component-sidebar {
                max-height: none;
                height: 100%;
                overflow-y: auto;
                overflow-x: hidden;
                overscroll-behavior: contain;
                -webkit-overflow-scrolling: touch;
            }

            .modern-content {
                margin-left: 0 !important;
                width: 100%;
            }

            .modern-container {
                padding: 0;
            }


            /* Card entrance animations */
            .animate-card {
                opacity: 0;
                transform: translateY(10px) scale(0.995);
                transition: opacity 420ms cubic-bezier(.2,.9,.2,1), transform 420ms cubic-bezier(.2,.9,.2,1);
                will-change: opacity, transform;
            }

            .animate-card.visible {
                opacity: 1;
                transform: none;
            }

            footer {
                background: linear-gradient(135deg, #23211d 0%, #191917 100%);
                color: rgba(245, 243, 238, 0.82);
                border-top: 1px solid rgba(255,255,255,0.06);
                padding: 18px 16px;
            }
        }

        @media (max-width: 768px) and (prefers-color-scheme: dark) {
            body {
                background: #191917 !important;
                color: #f5f3ee !important;
            }

            .dashboard-container .card,
            .dashboard-container .form-section,
            .dashboard-container .alert,
            .dashboard-container .table-responsive,
            .dashboard-container .modal-content {
                background: linear-gradient(180deg, rgba(34, 33, 29, 0.96), rgba(42, 40, 35, 0.96)) !important;
                color: #f5f3ee !important;
            }
        }
    </style>

    @yield('css')
</head>
<body>
    @auth
        <!-- Slim Top Navbar (brand + toggle) -->
        @include('components.top-navbar')

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
        @if(auth()->user()?->canViewMenuItem('live_chat_floating'))
            @include('components.floating-chat')
        @endif
    @endauth

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- html2pdf (client-side PDF export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function toggleLanguage() {
            const url = new URL(window.location.href);
            const locale = url.searchParams.get('lang') || '{{ app()->getLocale() }}';
            const newLocale = locale === 'en' ? 'ar' : (locale === 'ar' ? 'tr' : 'en');
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
        // Mobile card animations
        document.addEventListener('DOMContentLoaded', function() {
            // Entrance animations using IntersectionObserver
            try {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12 });

                document.querySelectorAll('.dashboard-container .card, .mobile-report-list .card, .safes-mobile-list .card, .storages-mobile-list .card, .employees-mobile-list .card').forEach(el => {
                    el.classList.add('animate-card');
                    observer.observe(el);
                });
            } catch (e) {
                // IntersectionObserver not supported — skip animations silently
            }
        });
    </script>

    @stack('scripts')

    @yield('js')
</body>
</html>
