@extends('layouts.modern')

@section('title', __('messages.dashboard'))

@section('content')
<style>
    :root {
        --primary: #ff8c00;
        --primary-dark: #e67e00;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --surface: #ffffff;
        --text-dark: #111827;
        --text-light: #6b7280;
        --border-light: #e5e7eb;
        --bg-soft: #f8fafc;
    }

    .merchant-dashboard-shell {
        min-height: 100vh;
        padding: 28px 16px 40px;
        background:
            radial-gradient(circle at top right, rgba(255, 140, 0, 0.08), transparent 26%),
            linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
    }

    .dashboard-hero {
        background: linear-gradient(135deg, #111827 0%, #1f2937 68%, #ff8c00 180%);
        color: #fff;
        border-radius: 28px;
        padding: 28px;
        box-shadow: 0 18px 50px rgba(17, 24, 39, 0.18);
        margin-bottom: 22px;
        overflow: hidden;
        position: relative;
    }

    /* Redesigned hero layout */
    .dashboard-hero .redesigned-hero {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 22px;
        align-items: center;
    }

    .hero-left .dashboard-title {
        font-size: 40px;
        line-height: 1.02;
        font-weight: 900;
        color: #fff;
    }

    .hero-left .dashboard-subtitle {
        color: rgba(255,255,255,0.9);
        font-size: 15px;
        max-width: 680px;
        margin-top: 8px;
    }

    .hero-actions { margin-top: 16px; display:flex; gap:12px; flex-wrap:wrap; }

    .kpi-grid { display: grid; gap: 12px; }

    .kpi-card {
        background: rgba(255,255,255,0.09);
        border: 1px solid rgba(255,255,255,0.12);
        padding: 12px 14px;
        border-radius: 14px;
        text-align: left;
        min-width: 260px;
    }

    .kpi-card .kpi-label { font-size: 11px; color: rgba(255,255,255,0.8); text-transform:uppercase; letter-spacing:0.06em }
    .kpi-card .kpi-value { font-size: 22px; font-weight:900; color: #fff; margin-top:6px }

    /* decorative accent */
    .hero-decor {
        position: absolute;
        right: -80px;
        bottom: -80px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.14), rgba(255,255,255,0) 40%);
        transform: rotate(12deg);
        pointer-events: none;
        filter: blur(6px);
    }

    .hero-wave {
        position: absolute;
        left: 0;
        right: 0;
        bottom: -1px;
        display: none;
        opacity: 0.18;
        pointer-events: none;
    }

    .hero-wave svg {
        display: block;
        width: 100%;
        height: 56px;
    }

    @keyframes heroGradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes dashboardFadeUp {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 900px) {
        .dashboard-hero .redesigned-hero { grid-template-columns: 1fr; }
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        right: -60px;
        bottom: -70px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.14) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }

    .dashboard-hero-inner {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .dashboard-title {
        font-size: 34px;
        font-weight: 900;
        margin-bottom: 10px;
        letter-spacing: -0.04em;
    }

    .dashboard-subtitle {
        margin: 0;
        color: rgba(255,255,255,0.78);
        max-width: 760px;
        line-height: 1.7;
    }

    .hero-kpis {
        min-width: min(100%, 360px);
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .hero-kpi {
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        backdrop-filter: blur(10px);
    }

    .hero-kpi-label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,0.64);
        margin-bottom: 4px;
    }

    .hero-kpi-value {
        font-size: 18px;
        font-weight: 800;
    }

    .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 16px;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
        letter-spacing: -0.02em;
    }

    .section-subtitle {
        margin: 4px 0 0;
        font-size: 13px;
        color: var(--text-light);
    }

    .dashboard-section {
        margin-bottom: 22px;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(155px, 1fr));
        gap: 12px;
    }

    .quick-action {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: var(--surface);
        border: 1px solid var(--border-light);
        border-radius: 18px;
        text-decoration: none;
        color: var(--text-dark);
        box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        transition: all 0.25s ease;
        width: 100%;
    }

    .quick-action:hover {
        transform: translateY(-3px);
        border-color: rgba(255,140,0,0.35);
        box-shadow: 0 12px 30px rgba(255,140,0,0.12);
        color: var(--text-dark);
    }

    .quick-action-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(255,140,0,0.12), rgba(255,179,71,0.18));
        color: var(--primary);
        font-size: 20px;
    }

    .quick-action-text strong {
        display: block;
        font-size: 13px;
        margin-bottom: 2px;
    }

    .quick-action-text span {
        display: block;
        font-size: 12px;
        color: var(--text-light);
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .metric-card,
    .panel-card,
    .chart-card {
        background: var(--surface);
        border: 1px solid var(--border-light);
        border-radius: 22px;
        padding: 20px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }

    .metric-card:hover {
        transform: translateY(-3px);
        transition: all 0.25s ease;
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
    }

    .metric-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }

    .metric-label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-light);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 28px;
        font-weight: 900;
        color: var(--text-dark);
        line-height: 1.1;
        letter-spacing: -0.04em;
    }

    .metric-note {
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--text-light);
        font-size: 12px;
    }

    .metric-badge {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .metric-badge.primary { background: rgba(255,140,0,0.12); color: var(--primary); }
    .metric-badge.success { background: rgba(16,185,129,0.12); color: var(--success); }
    .metric-badge.info { background: rgba(59,130,246,0.12); color: var(--info); }
    .metric-badge.warning { background: rgba(245,158,11,0.12); color: var(--warning); }
    .metric-badge.danger { background: rgba(239,68,68,0.12); color: var(--danger); }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 16px;
    }

    .chart-box {
        position: relative;
        height: 320px;
    }

    .panel-card .table {
        margin-bottom: 0;
    }

    .panel-card .table thead th {
        border-top: 0;
        background: #fafafa;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-light);
        border-bottom: 1px solid var(--border-light);
    }

    .panel-card .table tbody td {
        vertical-align: middle;
        font-size: 13px;
        color: var(--text-dark);
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .pill.success { background: rgba(16,185,129,0.12); color: var(--success); }
    .pill.warning { background: rgba(245,158,11,0.12); color: var(--warning); }
    .pill.danger { background: rgba(239,68,68,0.12); color: var(--danger); }
    .pill.info { background: rgba(59,130,246,0.12); color: var(--info); }

    .empty-state {
        padding: 26px 16px;
        text-align: center;
        color: var(--text-light);
    }

    .empty-state i {
        display: block;
        font-size: 40px;
        opacity: 0.3;
        margin-bottom: 10px;
    }

    .progress-line {
        width: 100%;
        height: 10px;
        background: #edf2f7;
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-line > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #ff8c00, #ffb347);
    }

    @media (max-width: 768px) {
        .dashboard-hero {
            background: linear-gradient(135deg, #ff8c00 0%, #ff9e33 40%, #ffd089 100%);
            background-size: 200% 200%;
            animation: heroGradientShift 30s ease-in-out infinite;
            padding: 16px;
            border-radius: 18px;
            box-shadow: 0 18px 50px rgba(255, 140, 0, 0.12);
        }

        .dashboard-hero,
        .dashboard-section {
            animation: dashboardFadeUp .9s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .dashboard-section:nth-of-type(1) { animation-delay: .08s; }
        .dashboard-section:nth-of-type(2) { animation-delay: .16s; }
        .dashboard-section:nth-of-type(3) { animation-delay: .24s; }

        .dashboard-hero .redesigned-hero {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .hero-right,
        .kpi-grid {
            width: 100%;
        }

        .kpi-grid {
            grid-template-columns: 1fr;
        }

        .kpi-card {
            min-width: 0;
            padding: 11px 12px;
        }

        .hero-actions .btn {
            flex: 1 1 100%;
        }

        .dashboard-subtitle {
            display: block;
            font-size: 13px;
            line-height: 1.55;
            max-width: 100%;
        }

        .hero-wave svg {
            height: 40px;
        }

        .dashboard-title {
            font-size: 26px;
        }

        .dashboard-hero {
            padding: 22px;
            border-radius: 22px;
        }

        .merchant-dashboard-shell {
            padding: 16px 12px 28px;
        }

        .dashboard-hero-inner {
            flex-direction: column;
        }

        .hero-kpis {
            min-width: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .metric-value {
            font-size: 24px;
        }

        .quick-actions-grid,
        .metrics-grid,
        .chart-grid {
            grid-template-columns: 1fr;
        }

        .chart-box {
            height: 260px;
        }

        .section-heading {
            flex-direction: column;
            align-items: stretch;
        }

        .section-heading .btn {
            width: 100%;
        }

        .panel-card,
        .chart-card,
        .metric-card {
            padding: 16px;
            border-radius: 18px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .panel-card .table,
        .table {
            min-width: 640px;
        }

        .recent-sales-table thead th:nth-child(2),
        .recent-sales-table tbody td:nth-child(2) {
            display: none;
        }

        .low-stock-table thead th:nth-child(3),
        .low-stock-table tbody td:nth-child(3) {
            display: none;
        }

        .recent-sales-table thead th,
        .low-stock-table thead th {
            font-size: 10px;
            letter-spacing: 0.6px;
        }

        .panel-card .table tbody td,
        .table tbody td {
            padding: 8px 6px;
            font-size: 12px;
            white-space: normal;
            word-break: break-word;
        }

        .list-group-item {
            flex-direction: column;
            gap: 8px;
        }

        .pill {
            width: fit-content;
        }
    }

    @media (max-width: 576px) {
        .dashboard-hero {
            padding: 14px;
        }

        .hero-wave svg {
            height: 32px;
        }

        .hero-wave {
            display: block;
        }

        .dashboard-title {
            font-size: 22px;
        }

        .hero-kpis {
            grid-template-columns: 1fr;
        }

        .dashboard-hero,
        .metric-card,
        .chart-card,
        .panel-card {
            border-radius: 16px;
        }

        .quick-action {
            padding: 14px;
        }

        .hero-kpi-value {
            font-size: 16px;
        }

        .table-responsive {
            overflow-x: hidden;
        }

        .panel-card .table,
        .table {
            min-width: 0 !important;
            width: 100% !important;
            table-layout: fixed !important;
        }

        .recent-sales-table thead th:nth-child(2),
        .recent-sales-table tbody td:nth-child(2) {
            display: none !important;
        }

        .low-stock-table thead th:nth-child(3),
        .low-stock-table tbody td:nth-child(3) {
            display: none !important;
        }

        .recent-sales-table thead th,
        .low-stock-table thead th {
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 10px 8px;
        }
    }
</style>

<style>
    @media (max-width: 768px) {
        .dashboard-subtitle {
            display: none !important;
        }

        .table-responsive {
            overflow-x: hidden !important;
        }

        .recent-sales-table,
        .low-stock-table {
            width: 100% !important;
            min-width: 0 !important;
            table-layout: fixed !important;
        }

        .recent-sales-table thead th:nth-child(2),
        .recent-sales-table tbody td:nth-child(2) {
            display: none !important;
        }

        .low-stock-table thead th:nth-child(3),
        .low-stock-table tbody td:nth-child(3) {
            display: none !important;
        }

        .recent-sales-table thead th,
        .low-stock-table thead th {
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 10px 8px;
        }

        .panel-card .table tbody td,
        .table tbody td {
            padding: 8px 6px;
            font-size: 12px;
            white-space: normal;
            word-break: break-word;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .dashboard-hero,
        .dashboard-section,
        .quick-action,
        .metric-card,
        .panel-card,
        .chart-card {
            animation: none !important;
        }

        .dashboard-hero {
            animation: none !important;
        }
    }
</style>

<div id="merchantDashboardRoot" class="merchant-dashboard-shell" data-analytics-url="{{ route('dashboard.analytics') }}">
    <div class="dashboard-hero">
        <div class="dashboard-hero-inner redesigned-hero">
            <div class="hero-left">
                <h1 class="dashboard-title"><i class="bi bi-speedometer2 me-2"></i>{{ __('messages.dashboard') }}</h1>
                <p class="dashboard-subtitle">Welcome back, <strong>{{ auth()->user()->name }}</strong>. This merchant dashboard surfaces daily KPIs, quick actions, and summarized insights for fast decision making.</p>

                <div class="hero-actions">
                    <a href="{{ route('sales.index') }}" class="btn btn-primary-modern">New Sale</a>
                    <a href="{{ route('reports.sales') }}" class="btn btn-outline-light">Reports</a>
                </div>
            </div>

            <div class="hero-right">
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-label">Today Sales</div>
                            <div class="kpi-value"><span class="kpi-value-num" data-currency="1" data-target="{{ $todaySales ?? 0 }}">0</span></div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-label">Total Balance</div>
                        <div class="kpi-value"><span class="kpi-value-num" data-currency="1" data-target="{{ $safeBalance ?? 0 }}">0</span></div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-label">Products</div>
                        <div class="kpi-value"><span class="kpi-value-num" data-target="{{ $totalProducts ?? 0 }}">0</span></div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-label">Low Stock</div>
                        <div class="kpi-value"><span class="kpi-value-num" data-target="{{ $lowStockCount ?? 0 }}">0</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-decor"></div>
        <div class="hero-wave" aria-hidden="true">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0,40 C180,110 360,0 540,55 C720,110 900,10 1080,52 C1140,66 1170,76 1200,84 L1200,120 L0,120 Z" fill="rgba(255,255,255,0.65)"></path>
            </svg>
        </div>

        <script>
            (function(){
                const currency = "{{ $currencySymbol }}";
                function easeOutCubic(t){return 1-Math.pow(1-t,3)}
                function formatNumber(n, isCurrency){
                    if(isCurrency){
                        return currency + Number(n).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
                    }
                    return Number(n).toLocaleString();
                }

                document.querySelectorAll('.kpi-value-num').forEach(el=>{
                    const raw = parseFloat(el.dataset.target) || 0;
                    const isCurrency = el.dataset.currency === '1';
                    const duration = 900;
                    const start = 0;
                    const t0 = performance.now();
                    function step(t){
                        const p = Math.min((t - t0)/duration,1);
                        const v = start + (raw - start) * easeOutCubic(p);
                        el.textContent = isCurrency ? formatNumber(v, true) : formatNumber(Math.round(v), false);
                        if(p<1) requestAnimationFrame(step);
                    }
                    requestAnimationFrame(step);
                });
            })();
        </script>
    </div>

    <div class="dashboard-section">
        <div class="section-heading">
            <div>
                <h2 class="section-title"><i class="bi bi-lightning-charge-fill text-warning"></i>Quick actions</h2>
                <p class="section-subtitle">Open the most important operational screens fast.</p>
            </div>
        </div>
            <div class="quick-actions-grid">
            @feature('products.create')
            <a href="{{ route('products.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-box-seam"></i></div>
                <div class="quick-action-text"><strong>Add Product</strong><span>Create inventory item</span></div>
            </a>
            @endfeature

            @feature('sales')
            <a href="{{ route('sales.index') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-cart-check"></i></div>
                <div class="quick-action-text"><strong>Sales</strong><span>Open sales list</span></div>
            </a>
            @endfeature

            @feature('invoicing')
            <a href="{{ route('invoices.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-receipt"></i></div>
                <div class="quick-action-text"><strong>New Invoice</strong><span>Create invoice</span></div>
            </a>
            @endfeature

            @feature('employees')
            <a href="{{ route('employees.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-person-plus"></i></div>
                <div class="quick-action-text"><strong>Add Employee</strong><span>Register staff</span></div>
            </a>
            @endfeature

            @feature('suppliers.create')
            <a href="{{ route('suppliers.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-truck"></i></div>
                <div class="quick-action-text"><strong>Add Supplier</strong><span>Register supplier</span></div>
            </a>
            @endfeature

            @feature('payroll')
            <a href="{{ route('payroll.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-wallet2"></i></div>
                <div class="quick-action-text"><strong>Payroll</strong><span>Payroll entry</span></div>
            </a>
            @endfeature

            @feature('safes')
            <a href="{{ route('safes.index') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-safe"></i></div>
                <div class="quick-action-text"><strong>Safes</strong><span>Cash management</span></div>
            </a>
            @endfeature

            @feature('commissions')
            <a href="{{ route('commissions.index') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-percent"></i></div>
                <div class="quick-action-text"><strong>Commissions</strong><span>Review payouts</span></div>
            </a>
            @endfeature

            @feature('storages')
            <a href="{{ route('storages.index') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-boxes"></i></div>
                <div class="quick-action-text"><strong>Storages</strong><span>Warehouse overview</span></div>
            </a>
            @endfeature

            @feature('branches.create')
            <a href="{{ route('branches.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-diagram-3"></i></div>
                <div class="quick-action-text"><strong>New Branch</strong><span>Expand locations</span></div>
            </a>
            @endfeature

            @feature('sales_report')
            <a href="{{ route('reports.sales') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="quick-action-text"><strong>Sales Report</strong><span>View analytics</span></div>
            </a>
            @endfeature

            @feature('financial_report')
            <a href="{{ route('reports.financial') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-bar-chart-line"></i></div>
                <div class="quick-action-text"><strong>Financial Report</strong><span>Review accounts</span></div>
            </a>
            @endfeature
        </div>
    </div>

    @featureAny(['products', 'sales', 'employees', 'commissions', 'storages', 'safes'])
    <div class="dashboard-section">
        <div class="section-heading">
            <div>
                <h2 class="section-title"><i class="bi bi-bar-chart-line-fill text-warning"></i>Operational metrics</h2>
                <p class="section-subtitle">Live totals for sales, inventory, storage, commissions, and cash flow.</p>
            </div>
        </div>
        <div class="metrics-grid">
            @feature('products')
            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Products</span>
                        <div class="metric-value" id="metric-total-products">{{ $totalProducts ?? 0 }}</div>
                    </div>
                    <div class="metric-badge primary"><i class="bi bi-box-seam"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-exclamation-triangle"></i><span id="metric-low-stock-count">{{ $lowStockCount ?? 0 }}</span> low stock</div>
            </div>
            @endfeature

            @feature('sales')
            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Sales</span>
                        <div class="metric-value" id="metric-total-sales">{{ $currencySymbol }}{{ number_format($totalSales ?? 0, 2) }}</div>
                    </div>
                    <div class="metric-badge success"><i class="bi bi-cash-coin"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-receipt"></i><span id="metric-sales-count">{{ $salesCount ?? 0 }}</span> invoices</div>
            </div>
            @endfeature

            @feature('employees')
            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Employees</span>
                        <div class="metric-value" id="metric-total-employees">{{ $totalEmployees ?? 0 }}</div>
                    </div>
                    <div class="metric-badge info"><i class="bi bi-people"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-person-check"></i>Team coverage</div>
            </div>
            @endfeature

            @feature('commissions')
            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Pending Commissions</span>
                        <div class="metric-value" id="metric-pending-commissions">{{ $pendingCommissions ?? 0 }}</div>
                    </div>
                    <div class="metric-badge warning"><i class="bi bi-percent"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-currency-dollar"></i>{{ $currencySymbol }}{{ number_format($commissionAmount ?? 0, 2) }} pending</div>
            </div>
            @endfeature

            @feature('storages')
            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Storage Usage</span>
                        <div class="metric-value" id="metric-storage-usage">{{ number_format($storageUsage ?? 0, 2) }}%</div>
                    </div>
                    <div class="metric-badge primary"><i class="bi bi-boxes"></i></div>
                </div>
                <div class="progress-line"><span style="width: {{ min(100, $storageUsage ?? 0) }}%;"></span></div>
                <div class="metric-note"><i class="bi bi-hdd-stack"></i>{{ number_format($totalStorageUsage ?? 0, 2) }} / {{ number_format($totalStorageCapacity ?? 0, 2) }}</div>
            </div>
            @endfeature

            @feature('safes')
            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Safe Balance</span>
                        <div class="metric-value" id="metric-safe-balance">{{ $currencySymbol }}{{ number_format($safeBalance ?? 0, 2) }}</div>
                    </div>
                    <div class="metric-badge success"><i class="bi bi-safe"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-arrow-left-right"></i>{{ $safeCount ?? 0 }} safes</div>
            </div>
            @endfeature

            @feature('safes')
            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Income</span>
                        <div class="metric-value" id="metric-safe-income">{{ $currencySymbol }}{{ number_format($safeIncomeTotal ?? 0, 2) }}</div>
                    </div>
                    <div class="metric-badge info"><i class="bi bi-arrow-down-circle"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-lightning-charge"></i>Cash inflow</div>
            </div>

            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Outcome</span>
                        <div class="metric-value" id="metric-safe-outcome">{{ $currencySymbol }}{{ number_format($safeOutcomeTotal ?? 0, 2) }}</div>
                    </div>
                    <div class="metric-badge danger"><i class="bi bi-arrow-up-circle"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-lightning-charge"></i>Cash outflow</div>
            </div>
            @endfeature
        </div>
    </div>
    @endfeatureAny

    <div class="dashboard-section">
        <div class="section-heading">
            <div>
                <h2 class="section-title"><i class="bi bi-graph-up-arrow text-warning"></i>Sales and finance reports</h2>
                <p class="section-subtitle">The charts are loaded from a live analytics endpoint.</p>
            </div>
        </div>
        <div class="chart-grid">
            <div class="chart-card">
                <div class="section-heading mb-3">
                    <div>
                        <h3 class="section-title mb-0">Sales trend</h3>
                        <p class="section-subtitle">Live monthly sales from the sales page records.</p>
                    </div>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">Open sales page</a>
                </div>
                <div class="chart-box"><canvas id="salesTrendChart"></canvas></div>
            </div>

            <div class="chart-card">
                <div class="section-heading mb-3">
                    <h3 class="section-title mb-0">Income vs outcome</h3>
                </div>
                <div class="chart-box"><canvas id="cashFlowChart"></canvas></div>
            </div>

            <div class="chart-card">
                <div class="section-heading mb-3">
                    <h3 class="section-title mb-0">Inventory health</h3>
                </div>
                <div class="chart-box"><canvas id="inventoryChart"></canvas></div>
            </div>

            <div class="chart-card">
                <div class="section-heading mb-3">
                    <h3 class="section-title mb-0">Storage mix</h3>
                </div>
                <div class="chart-box"><canvas id="storageChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="dashboard-section">
        <div class="row g-4">
            <div class="col-xl-7">
                <div class="panel-card h-100">
                    <div class="section-heading">
                        <div>
                            <h2 class="section-title mb-1"><i class="bi bi-bag-check text-warning"></i>Recent sales</h2>
                            <p class="section-subtitle">Latest invoice activity and product movement.</p>
                        </div>
                        @feature('sales')
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                        @endfeature
                    </div>
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle recent-sales-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Reference</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->description ?? 'Invoice' }}</td>
                                        <td>{{ $transaction->reference_number ?? 'N/A' }}</td>
                                        <td>{{ optional($transaction->date)->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>{{ $currencySymbol }}{{ number_format($transaction->total_credit ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                No sales records found
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile friendly stacked list for Recent Sales -->
                    <div id="recent-sales-mobile" class="d-block d-md-none">
                        @forelse($recentTransactions as $transaction)
                            <div class="panel-card mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ $transaction->description ?? 'Invoice' }}</div>
                                        <small class="text-muted">{{ optional($transaction->date)->format('M d, Y') ?? 'N/A' }}</small>
                                    </div>
                                    <div class="fw-semibold">{{ $currencySymbol }}{{ number_format($transaction->total_credit ?? 0, 2) }}</div>
                                </div>
                                @if(!empty($transaction->reference_number))
                                    <small class="text-muted">Ref: {{ $transaction->reference_number }}</small>
                                @endif
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                No sales records found
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="panel-card h-100">
                    <div class="section-heading">
                        <div>
                            <h2 class="section-title mb-1"><i class="bi bi-cash-stack text-warning"></i>Recent cash entries</h2>
                            <p class="section-subtitle">Income and outcome entries from safe modules.</p>
                        </div>
                        @feature('safes')
                        <a href="{{ route('safes.index') }}" class="btn btn-sm btn-outline-secondary">Safes</a>
                        @endfeature
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentIncomeEntries->take(3) as $income)
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">Income · {{ $income->reference ?: $income->source }}</div>
                                    <small class="text-muted">{{ $income->safe->name ?? 'N/A' }} · {{ optional($income->created_at)->format('M d, Y h:i A') }}</small>
                                </div>
                                <span class="pill success">{{ $currencySymbol }}{{ number_format($income->amount, 2) }}</span>
                            </div>
                        @endforeach

                        @forelse($recentOutcomeEntries->take(3) as $outcome)
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">Outcome · {{ $outcome->description ?: ($outcome->reference ?: 'Cash out') }}</div>
                                    <small class="text-muted">{{ $outcome->safe->name ?? 'N/A' }} · {{ optional($outcome->created_at)->format('M d, Y h:i A') }}</small>
                                </div>
                                <span class="pill danger">{{ $currencySymbol }}{{ number_format($outcome->amount, 2) }}</span>
                            </div>
                        @endforeach

                        @if($recentIncomeEntries->isEmpty() && $recentOutcomeEntries->isEmpty())
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                No cash entries found
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="panel-card h-100">
                    <div class="section-heading">
                        <div>
                            <h2 class="section-title mb-1"><i class="bi bi-exclamation-triangle text-warning"></i>Low stock products</h2>
                            <p class="section-subtitle">Items that need replenishing soon.</p>
                        </div>
                        @feature('products')
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Inventory</a>
                        @endfeature
                    </div>
                    <div class="table-responsive d-none d-md-block">
                        <table class="table align-middle low-stock-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Stock</th>
                                    <th>Alert</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockProducts as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->current_stock ?? 0 }}</td>
                                        <td>
                                            @if(($product->current_stock ?? 0) <= 0)
                                                <span class="pill danger">Out of stock</span>
                                            @elseif(($product->current_stock ?? 0) <= 10)
                                                <span class="pill warning">Low stock</span>
                                            @else
                                                <span class="pill success">Healthy</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                No products available
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile friendly stacked list for Low Stock Products -->
                    <div id="low-stock-mobile" class="d-block d-md-none">
                        @forelse($lowStockProducts as $product)
                            <div class="panel-card mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $product->name }}</div>
                                        <small class="text-muted">Stock: {{ $product->current_stock ?? 0 }}</small>
                                    </div>
                                    <div>
                                        @if(($product->current_stock ?? 0) <= 0)
                                            <span class="pill danger">Out of stock</span>
                                        @elseif(($product->current_stock ?? 0) <= 10)
                                            <span class="pill warning">Low stock</span>
                                        @else
                                            <span class="pill success">Healthy</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                No products available
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="panel-card h-100">
                    <div class="section-heading">
                        <div>
                            <h2 class="section-title mb-1"><i class="bi bi-hdd-stack text-warning"></i>Storage snapshot</h2>
                            <p class="section-subtitle">Storage utilization and current load.</p>
                        </div>
                        @feature('storages')
                        <a href="{{ route('storages.index') }}" class="btn btn-sm btn-outline-secondary">Storages</a>
                        @endfeature
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($storageSnapshot as $storage)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="fw-semibold">{{ $storage->name }}</div>
                                    <span class="pill info">{{ number_format($storage->current_usage ?? 0, 2) }} used</span>
                                </div>
                                <div class="progress-line mb-2"><span style="width: {{ min(100, $storage->current_usage ?? 0) }}%;"></span></div>
                                <small class="text-muted">Items: {{ $storage->items_count ?? 0 }} · Capacity: {{ number_format($storage->capacity ?? 0, 2) }}</small>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                No storages found
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const root = document.getElementById('merchantDashboardRoot');
        const analyticsUrl = root?.dataset.analyticsUrl;
        const currencySymbol = @json($currencySymbol);
        const serverMonths = @json($months);
        const serverSalesData = @json($salesData);

        const formatMoney = (value) => `${currencySymbol}${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        const formatInt = (value) => Number(value || 0).toLocaleString();

        const applyMobileTweaks = () => {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;

            const subtitle = document.querySelector('.dashboard-subtitle');
            if (subtitle) {
                subtitle.style.display = isMobile ? 'none' : '';
            }

            document.querySelectorAll('.table-responsive').forEach((wrapper) => {
                wrapper.style.overflowX = isMobile ? 'hidden' : '';
            });

            document.querySelectorAll('.recent-sales-table').forEach((table) => {
                table.style.width = isMobile ? '100%' : '';
                table.style.minWidth = isMobile ? '0' : '';
                table.style.tableLayout = isMobile ? 'fixed' : '';
                const secondHeader = table.querySelector('thead th:nth-child(2)');
                const secondCells = table.querySelectorAll('tbody td:nth-child(2)');
                if (secondHeader) secondHeader.style.display = isMobile ? 'none' : '';
                secondCells.forEach((cell) => { cell.style.display = isMobile ? 'none' : ''; });
            });

            document.querySelectorAll('.low-stock-table').forEach((table) => {
                table.style.width = isMobile ? '100%' : '';
                table.style.minWidth = isMobile ? '0' : '';
                table.style.tableLayout = isMobile ? 'fixed' : '';
                const thirdHeader = table.querySelector('thead th:nth-child(3)');
                const thirdCells = table.querySelectorAll('tbody td:nth-child(3)');
                if (thirdHeader) thirdHeader.style.display = isMobile ? 'none' : '';
                thirdCells.forEach((cell) => { cell.style.display = isMobile ? 'none' : ''; });
            });
        };

        applyMobileTweaks();
        window.addEventListener('resize', applyMobileTweaks);

        const salesTrendCtx = document.getElementById('salesTrendChart')?.getContext('2d');
        const cashFlowCtx = document.getElementById('cashFlowChart')?.getContext('2d');
        const inventoryCtx = document.getElementById('inventoryChart')?.getContext('2d');
        const storageCtx = document.getElementById('storageChart')?.getContext('2d');

        let salesTrendChart = null;

        if (salesTrendCtx) {
            salesTrendChart = new Chart(salesTrendCtx, {
                type: 'line',
                data: {
                    labels: serverMonths,
                    datasets: [{
                        label: 'Sales',
                        data: serverSalesData,
                        borderColor: '#ff8c00',
                        backgroundColor: 'rgba(255, 140, 0, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        if (!analyticsUrl || !cashFlowCtx || !inventoryCtx || !storageCtx) {
            return;
        }

        fetch(analyticsUrl, { headers: { Accept: 'application/json' } })
            .then(response => response.json())
            .then(payload => {
                if (!payload.success) return;

                const summary = payload.summary || {};
                document.getElementById('hero-total-sales').textContent = formatMoney(summary.total_sales || 0);
                document.getElementById('hero-safe-balance').textContent = formatMoney(summary.safe_balance || 0);
                document.getElementById('hero-total-products').textContent = formatInt(summary.total_products || 0);
                document.getElementById('hero-low-stock').textContent = formatInt(summary.low_stock_count || 0);

                document.getElementById('metric-total-products').textContent = formatInt(summary.total_products || 0);
                document.getElementById('metric-low-stock-count').textContent = formatInt(summary.low_stock_count || 0);
                document.getElementById('metric-total-sales').textContent = formatMoney(summary.total_sales || 0);
                document.getElementById('metric-sales-count').textContent = formatInt(summary.sales_count || 0);
                document.getElementById('metric-total-employees').textContent = formatInt(summary.total_employees || 0);
                document.getElementById('metric-pending-commissions').textContent = formatInt(summary.pending_commissions || 0);
                document.getElementById('metric-storage-usage').textContent = `${Number(summary.storage_usage || 0).toFixed(2)}%`;
                document.getElementById('metric-safe-balance').textContent = formatMoney(summary.safe_balance || 0);
                document.getElementById('metric-safe-income').textContent = formatMoney(summary.safe_income_total || 0);
                document.getElementById('metric-safe-outcome').textContent = formatMoney(summary.safe_outcome_total || 0);

                const charts = payload.charts || {};
                const months = charts.months || [];

                if (salesTrendChart && months.length) {
                    salesTrendChart.data.labels = months;
                    salesTrendChart.data.datasets[0].data = charts.sales || [];
                    salesTrendChart.update();
                }

                new Chart(cashFlowCtx, {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            { label: 'Income', data: charts.income || [], backgroundColor: 'rgba(16, 185, 129, 0.8)', borderRadius: 10 },
                            { label: 'Outcome', data: charts.outcome || [], backgroundColor: 'rgba(239, 68, 68, 0.8)', borderRadius: 10 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } },
                        scales: { y: { beginAtZero: true } }
                    }
                });

                new Chart(inventoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['In Stock', 'Low/Out Stock'],
                        datasets: [{
                            data: charts.inventory || [],
                            backgroundColor: ['#10b981', '#ff8c00'],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });

                new Chart(storageCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Usage', 'Free Space'],
                        datasets: [{
                            label: 'Storage %',
                            data: [summary.storage_usage || 0, Math.max(0, 100 - (summary.storage_usage || 0))],
                            backgroundColor: ['#3b82f6', '#e5e7eb'],
                            borderRadius: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, max: 100 } }
                    }
                });
            })
            .catch(() => {
                // Keep the server-rendered metrics if analytics cannot be fetched.
            });

    });
</script>
@endsection
