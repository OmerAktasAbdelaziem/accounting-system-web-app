@extends('layouts.super-admin')

@section('title', 'Super Admin Dashboard')

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
        --text-dark: #1f2937;
        --text-light: #6b7280;
        --border-light: #e5e7eb;
        --bg-light: #f8fafc;
    }

    .dashboard-shell {
        min-height: 100vh;
        padding: 28px 16px 40px;
        background:
            radial-gradient(circle at top right, rgba(255, 140, 0, 0.08), transparent 26%),
            linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
    }

    .dashboard-hero {
        background: linear-gradient(135deg, #ff8c00 0%, #ffb347 100%);
        background-size: 200% 200%;
        animation: heroGradientShift 30s ease-in-out infinite;
        color: #fff;
        border-radius: 28px;
        padding: 28px;
        box-shadow: 0 18px 50px rgba(255, 140, 0, 0.14);
        margin-bottom: 24px;
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
        color: rgba(255,255,255,0.92);
        font-size: 15px;
        margin-top: 8px;
    }

    .hero-actions { margin-top: 16px; display:flex; gap:12px; flex-wrap:wrap; }

    .hero-meta { display:grid; gap:12px; }

    .hero-meta-item { padding:12px 14px; border-radius:14px; background: rgba(255,255,255,0.09); border:1px solid rgba(255,255,255,0.12); }

    .hero-meta-label { font-size:11px; color: rgba(255,255,255,0.82); text-transform:uppercase }
    .hero-meta-value { font-size:20px; font-weight:800; color:#fff }

    .hero-decor { position:absolute; right:-80px; bottom:-80px; width:320px; height:320px; border-radius:50%; background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.12), rgba(255,255,255,0) 40%); pointer-events:none; filter: blur(6px); }

    .hero-wave {
        position: absolute;
        left: 0;
        right: 0;
        bottom: -1px;
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

    .dashboard-hero,
    .quick-actions-section,
    .metrics-section,
    .charts-section,
    .reports-section {
        animation: dashboardFadeUp .9s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .quick-actions-section { animation-delay: .08s; }
    .metrics-section { animation-delay: .16s; }
    .charts-section { animation-delay: .24s; }
    .reports-section { animation-delay: .32s; }

    @media (max-width:900px){ .dashboard-hero .redesigned-hero{ grid-template-columns:1fr } .hero-meta{ grid-template-columns:repeat(2,1fr) } }

    @media (max-width: 768px) {
        .dashboard-hero {
            padding: 16px;
            border-radius: 18px;
        }

        .dashboard-hero .redesigned-hero {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .hero-meta {
            grid-template-columns: 1fr;
            width: 100%;
            min-width: 0;
        }

        .hero-actions .btn {
            flex: 1 1 100%;
        }

        .dashboard-subtitle {
            font-size: 13px;
            line-height: 1.55;
            max-width: 100%;
        }

        .hero-wave svg {
            height: 40px;
        }
    }

    @media (max-width: 576px) {
        .dashboard-hero {
            padding: 14px;
        }

        .dashboard-title {
            font-size: 22px;
        }

        .hero-wave svg {
            height: 32px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .dashboard-hero,
        .quick-actions-section,
        .metrics-section,
        .charts-section,
        .reports-section,
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

    .dashboard-hero::after {
        content: '';
        position: absolute;
        inset: auto -80px -120px auto;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
        pointer-events: none;
    }

    .dashboard-hero-inner {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: flex-start;
        position: relative;
        z-index: 1;
        flex-wrap: wrap;
    }

    .dashboard-title {
        font-size: 34px;
        font-weight: 900;
        letter-spacing: -0.04em;
        margin-bottom: 10px;
    }

    .dashboard-subtitle {
        color: rgba(255, 255, 255, 0.78);
        margin: 0;
        max-width: 700px;
        line-height: 1.7;
    }

    .hero-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        min-width: min(100%, 360px);
    }

    .hero-meta-item {
        padding: 14px 16px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
    }

    .hero-meta-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.62);
        margin-bottom: 4px;
        display: block;
    }

    .hero-meta-value {
        font-size: 18px;
        font-weight: 800;
        color: #fff;
    }

    .quick-actions-section,
    .metrics-section,
    .charts-section,
    .reports-section {
        margin-bottom: 24px;
    }

    .section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .section-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.02em;
    }

    .section-subtitle {
        font-size: 13px;
        color: var(--text-light);
        margin: 0;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
        transition: all 0.25s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        width: 100%;
    }

    .quick-action:hover {
        transform: translateY(-3px);
        border-color: rgba(255, 140, 0, 0.35);
        box-shadow: 0 12px 30px rgba(255, 140, 0, 0.12);
        color: var(--text-dark);
    }

    .quick-action-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(255, 140, 0, 0.12), rgba(255, 179, 71, 0.18));
        color: var(--primary);
        font-size: 20px;
        flex-shrink: 0;
    }

    .quick-action-text {
        min-width: 0;
    }

    .quick-action-text strong {
        display: block;
        font-size: 13px;
        margin-bottom: 2px;
    }

    .quick-action-text span {
        font-size: 12px;
        color: var(--text-light);
    }

    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }

    .metric-card {
        background: var(--surface);
        border: 1px solid var(--border-light);
        border-radius: 22px;
        padding: 22px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: all 0.25s ease;
    }

    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
    }

    .metric-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .metric-label {
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-light);
        font-weight: 800;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 28px;
        font-weight: 900;
        color: var(--text-dark);
        letter-spacing: -0.04em;
        line-height: 1.1;
    }

    .metric-note {
        margin-top: 8px;
        font-size: 12px;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 6px;
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

    .metric-badge.revenue { background: rgba(16, 185, 129, 0.12); color: var(--success); }
    .metric-badge.sales { background: rgba(255, 140, 0, 0.12); color: var(--primary); }
    .metric-badge.income { background: rgba(59, 130, 246, 0.12); color: var(--info); }
    .metric-badge.outcome { background: rgba(239, 68, 68, 0.12); color: var(--danger); }
    .metric-badge.cash { background: rgba(245, 158, 11, 0.12); color: var(--warning); }
    .metric-badge.default { background: rgba(107, 114, 128, 0.12); color: var(--text-light); }

    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 16px;
    }

    .chart-card,
    .panel-card {
        background: var(--surface);
        border: 1px solid var(--border-light);
        border-radius: 22px;
        padding: 20px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
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

    .pill.success { background: rgba(16, 185, 129, 0.12); color: var(--success); }
    .pill.warning { background: rgba(245, 158, 11, 0.12); color: var(--warning); }
    .pill.danger { background: rgba(239, 68, 68, 0.12); color: var(--danger); }
    .pill.info { background: rgba(59, 130, 246, 0.12); color: var(--info); }

    .empty-state {
        text-align: center;
        padding: 28px 16px;
        color: var(--text-light);
    }

    .empty-state i {
        font-size: 40px;
        opacity: 0.35;
        display: block;
        margin-bottom: 10px;
    }

    .modal-content {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, #111827 0%, #1f2937 70%, #ff8c00 160%);
        color: white;
    }

    .modal-title {
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .form-control,
    .form-select {
        min-height: 48px;
        border-radius: 14px;
        border: 1.5px solid #d9dce3;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(255, 140, 0, 0.12);
    }

    .btn-dashboard-primary {
        background: linear-gradient(135deg, var(--primary) 0%, #ffb347 100%);
        border: 0;
        color: white;
        font-weight: 700;
        box-shadow: 0 12px 24px rgba(255, 140, 0, 0.18);
    }

    .btn-dashboard-primary:hover {
        color: white;
        transform: translateY(-1px);
    }

    .small-links {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }

    .small-links a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .dashboard-title {
            font-size: 26px;
        }

        .dashboard-hero {
            padding: 22px;
            border-radius: 22px;
        }

        .hero-meta {
            min-width: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .metric-value {
            font-size: 24px;
        }
    }
</style>

<div id="dashboardRoot" class="dashboard-shell" data-analytics-url="{{ route('super-admin.dashboard.analytics') }}">
    <div class="dashboard-hero">
        <div class="dashboard-hero-inner redesigned-hero">
            <div class="hero-left">
                <h1 class="dashboard-title"><i class="bi bi-speedometer2 me-2"></i>Super Admin Dashboard</h1>
                <p class="dashboard-subtitle">Welcome back, <strong>{{ auth()->user()->name }}</strong>. This view tracks live sales, cash flow, subscriptions and operational shortcuts.</p>
                <div class="hero-actions small-links">
                    <a href="{{ route('reports.sales') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-graph-up me-1"></i>Sales</a>
                    <a href="{{ route('reports.financial') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-journal-text me-1"></i>Financial</a>
                    <a href="{{ route('super-admin.feature-access.index') }}" class="btn btn-outline-light btn-sm"><i class="bi bi-shield-lock me-1"></i>Features</a>
                </div>
            </div>

            <div class="hero-right">
                <div class="hero-meta">
                    <div class="hero-meta-item">
                        <span class="hero-meta-label">Active Merchants</span>
                        <div class="hero-meta-value"><span class="kpi-value-num" data-target="{{ $activeMerchants }}">0</span></div>
                    </div>
                    <div class="hero-meta-item">
                        <span class="hero-meta-label">Net Cash Flow</span>
                        <div class="hero-meta-value"><span class="kpi-value-num" data-currency="1" data-target="{{ $netCashFlow }}">0</span></div>
                    </div>
                    <div class="hero-meta-item">
                        <span class="hero-meta-label">Sales Count</span>
                        <div class="hero-meta-value"><span class="kpi-value-num" data-target="{{ $salesCount }}">0</span></div>
                    </div>
                    <div class="hero-meta-item">
                        <span class="hero-meta-label">Active Safes</span>
                        <div class="hero-meta-value"><span class="kpi-value-num" data-target="{{ $activeSafes }}">0</span></div>
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

    <div class="quick-actions-section">
        <div class="section-heading">
            <div>
                <h2 class="section-title"><i class="bi bi-lightning-charge-fill text-warning"></i>Quick actions</h2>
                <p class="section-subtitle">Fast access to the most used admin, sales, and finance flows.</p>
            </div>
        </div>
        <div class="quick-actions-grid">
            <a href="{{ route('super-admin.merchants.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-building"></i></div>
                <div class="quick-action-text"><strong>New Merchant</strong><span>Create merchant account</span></div>
            </a>
            <a href="{{ route('super-admin.users.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-person-plus"></i></div>
                <div class="quick-action-text"><strong>New User</strong><span>Register admin user</span></div>
            </a>
            <a href="{{ route('super-admin.packages.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-box-seam"></i></div>
                <div class="quick-action-text"><strong>New Package</strong><span>Build subscription plan</span></div>
            </a>
            <a href="{{ route('super-admin.subscriptions.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-bookmark-check"></i></div>
                <div class="quick-action-text"><strong>New Subscription</strong><span>Activate package</span></div>
            </a>
            <a href="{{ route('products.create') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-box"></i></div>
                <div class="quick-action-text"><strong>Add Product</strong><span>Open product form</span></div>
            </a>
            <a href="{{ route('sales.index') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-cart-check"></i></div>
                <div class="quick-action-text"><strong>Sales</strong><span>View sales entries</span></div>
            </a>
            <a href="{{ route('reports.sales') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="quick-action-text"><strong>Sales Report</strong><span>Open report page</span></div>
            </a>
            <a href="{{ route('reports.financial') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-receipt"></i></div>
                <div class="quick-action-text"><strong>Financial Report</strong><span>Review accounts</span></div>
            </a>
            <a href="{{ route('safes.index') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-safe"></i></div>
                <div class="quick-action-text"><strong>Safes</strong><span>Open safes module</span></div>
            </a>
            <a href="{{ route('super-admin.feature-access.index') }}" class="quick-action">
                <div class="quick-action-icon"><i class="bi bi-shield-lock"></i></div>
                <div class="quick-action-text"><strong>Features</strong><span>Manage access</span></div>
            </a>
            <button type="button" class="quick-action text-start" data-bs-toggle="modal" data-bs-target="#incomeModal">
                <div class="quick-action-icon"><i class="bi bi-arrow-down-circle"></i></div>
                <div class="quick-action-text"><strong>Add Income</strong><span>Record cash in</span></div>
            </button>
            <button type="button" class="quick-action text-start" data-bs-toggle="modal" data-bs-target="#outcomeModal">
                <div class="quick-action-icon"><i class="bi bi-arrow-up-circle"></i></div>
                <div class="quick-action-text"><strong>Add Outcome</strong><span>Record cash out</span></div>
            </button>
        </div>
    </div>

    <div class="metrics-section">
        <div class="section-heading">
            <div>
                <h2 class="section-title"><i class="bi bi-bar-chart-line-fill text-warning"></i>Key metrics</h2>
                <p class="section-subtitle">Live financial and operational totals refreshed from controller data.</p>
            </div>
        </div>
        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Subscription Revenue</span>
                        <div class="metric-value" id="metric-total-revenue">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</div>
                    </div>
                    <div class="metric-badge revenue"><i class="bi bi-cash-coin"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-arrow-up-right"></i>Active plan income</div>
            </div>

            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Sales</span>
                        <div class="metric-value" id="metric-total-sales">{{ $currencySymbol }}{{ number_format($totalSales, 2) }}</div>
                    </div>
                    <div class="metric-badge sales"><i class="bi bi-bag-check"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-receipt"></i><span id="metric-sales-count">{{ $salesCount }}</span> transactions</div>
            </div>

            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Income</span>
                        <div class="metric-value" id="metric-total-income">{{ $currencySymbol }}{{ number_format($totalIncome, 2) }}</div>
                    </div>
                    <div class="metric-badge income"><i class="bi bi-arrow-down-circle"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-safe"></i>Safe income entries</div>
            </div>

            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Outcome</span>
                        <div class="metric-value" id="metric-total-outcome">{{ $currencySymbol }}{{ number_format($totalOutcome, 2) }}</div>
                    </div>
                    <div class="metric-badge outcome"><i class="bi bi-arrow-up-circle"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-credit-card"></i>Safe outcome entries</div>
            </div>

            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Net Cash Flow</span>
                        <div class="metric-value" id="metric-net-cash-flow">{{ $currencySymbol }}{{ number_format($netCashFlow, 2) }}</div>
                    </div>
                    <div class="metric-badge cash"><i class="bi bi-activity"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-graph-up"></i>Income + sales - outcome</div>
            </div>

            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Merchants</span>
                        <div class="metric-value" id="metric-total-merchants">{{ $totalMerchants }}</div>
                    </div>
                    <div class="metric-badge default"><i class="bi bi-building"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-check-circle"></i><span id="metric-active-merchants">{{ $activeMerchants }}</span> active</div>
            </div>

            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Subscriptions</span>
                        <div class="metric-value" id="metric-active-subscriptions">{{ $activeSubscriptions }}</div>
                    </div>
                    <div class="metric-badge default"><i class="bi bi-bookmark-check"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-exclamation-circle"></i><span id="metric-expiring-soon">{{ $expiringSoon }}</span> expiring soon</div>
            </div>

            <div class="metric-card">
                <div class="metric-top">
                    <div>
                        <span class="metric-label">Safes</span>
                        <div class="metric-value" id="metric-total-safes">{{ $totalSafes }}</div>
                    </div>
                    <div class="metric-badge default"><i class="bi bi-safe"></i></div>
                </div>
                <div class="metric-note"><i class="bi bi-wallet2"></i><span id="metric-active-safes">{{ $activeSafes }}</span> active safes</div>
            </div>
        </div>
    </div>

    <div class="charts-section">
        <div class="section-heading">
            <div>
                <h2 class="section-title"><i class="bi bi-graph-up-arrow text-warning"></i>Sales and finance reports</h2>
                <p class="section-subtitle">These charts are loaded from the dashboard analytics endpoint.</p>
            </div>
        </div>
        <div class="chart-grid">
            <div class="chart-card">
                <div class="section-heading mb-3">
                    <h3 class="section-title mb-0">Monthly sales and income</h3>
                </div>
                <div class="chart-box"><canvas id="salesTrendChart"></canvas></div>
            </div>

            <div class="chart-card">
                <div class="section-heading mb-3">
                    <h3 class="section-title mb-0">Income, outcome, profit</h3>
                </div>
                <div class="chart-box"><canvas id="cashFlowChart"></canvas></div>
            </div>

            <div class="chart-card">
                <div class="section-heading mb-3">
                    <h3 class="section-title mb-0">Top products</h3>
                </div>
                <div class="chart-box"><canvas id="topProductsChart"></canvas></div>
            </div>

            <div class="chart-card">
                <div class="section-heading mb-3">
                    <h3 class="section-title mb-0">Subscriptions status</h3>
                </div>
                <div class="chart-box"><canvas id="subscriptionChart"></canvas></div>
            </div>
        </div>
    </div>

    <div class="reports-section">
        <div class="row g-4">
            <div class="col-xl-7">
                <div class="panel-card h-100">
                    <div class="section-heading">
                        <div>
                            <h2 class="section-title mb-1"><i class="bi bi-bag-check text-warning"></i>Recent sales</h2>
                            <p class="section-subtitle">Latest recorded employee sales.</p>
                        </div>
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Employee</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSales as $sale)
                                    <tr>
                                        <td>{{ $sale->product->name ?? 'N/A' }}</td>
                                        <td>{{ $sale->employee->name ?? 'N/A' }}</td>
                                        <td>{{ $sale->quantity }}</td>
                                        <td>{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</td>
                                        <td>{{ optional($sale->sale_date)->format('M d, Y') ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                No sales found
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="panel-card h-100">
                    <div class="section-heading">
                        <div>
                            <h2 class="section-title mb-1"><i class="bi bi-cash-stack text-warning"></i>Recent cash movements</h2>
                            <p class="section-subtitle">Income and outcome entries from safes.</p>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#incomeModal">Add cash</button>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($recentCashMovements as $movement)
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold">{{ $movement['label'] ?? 'Entry' }}</div>
                                    <small class="text-muted">{{ $movement['safe'] ?? 'N/A' }} · {{ $movement['date'] ? $movement['date']->format('M d, Y h:i A') : '' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="pill {{ $movement['type'] === 'income' ? 'success' : 'danger' }}">
                                        <i class="bi bi-{{ $movement['type'] === 'income' ? 'arrow-down-circle' : 'arrow-up-circle' }}"></i>
                                        {{ $movement['currency'] ? $movement['currency'].' ' : '' }}{{ number_format($movement['amount'], 2) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                No cash movements found
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-7">
                <div class="panel-card h-100">
                    <div class="section-heading">
                        <div>
                            <h2 class="section-title mb-1"><i class="bi bi-bookmark-check text-warning"></i>Recent subscriptions</h2>
                            <p class="section-subtitle">Recently created or renewed subscriptions.</p>
                        </div>
                        <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Merchant</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                    <th>Expires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSubscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->merchant->name ?? 'N/A' }}</td>
                                        <td>{{ $subscription->package->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($subscription->is_active)
                                                <span class="pill success">Active</span>
                                            @else
                                                <span class="pill warning">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $subscription->expires_at ? $subscription->expires_at->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                No subscriptions yet
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="panel-card h-100">
                    <div class="section-heading">
                        <div>
                            <h2 class="section-title mb-1"><i class="bi bi-box-seam text-warning"></i>Top packages</h2>
                            <p class="section-subtitle">Package performance and merchant adoption.</p>
                        </div>
                        <a href="{{ route('super-admin.packages.index') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Package</th>
                                    <th>Merchants</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($merchantsByPackage as $package)
                                    <tr>
                                        <td>{{ $package->name }}</td>
                                        <td><span class="pill info">{{ $package->subscriptions_count }}</span></td>
                                        <td>{{ $currencySymbol }}{{ number_format($package->price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                No packages available
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="incomeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="bi bi-arrow-down-circle me-2"></i>Add Income</h5>
                    <small class="text-white-50">Record money entering a safe.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="incomeForm" method="POST" action="{{ url('/safes/__SAFE_ID__/add-income') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Safe</label>
                            <select class="form-select" id="incomeSafeSelect" name="safe_selector" required>
                                <option value="">Select safe</option>
                                @foreach($safes as $safe)
                                    <option value="{{ $safe->id }}" data-currencies='@json($safe->currencies->map(fn ($currency) => ["id" => $currency->id, "label" => trim(($currency->code ?? '') . " - " . ($currency->name ?? ''))]))'>{{ $safe->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <select class="form-select" id="incomeCurrencySelect" name="currency_id">
                                <option value="">Select a safe first</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Source</label>
                            <select class="form-select" name="source" required>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reference</label>
                            <input type="text" class="form-control" name="reference" placeholder="Optional reference">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="4" placeholder="Optional notes"></textarea>
                        </div>
                    </div>
                    @if($safes->isEmpty())
                        <div class="alert alert-warning mt-3 mb-0">Create a safe first before recording income.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dashboard-primary">Save Income</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="outcomeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title"><i class="bi bi-arrow-up-circle me-2"></i>Add Outcome</h5>
                    <small class="text-white-50">Record money leaving a safe.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="outcomeForm" method="POST" action="{{ url('/safes/__SAFE_ID__/add-outcome') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Safe</label>
                            <select class="form-select" id="outcomeSafeSelect" name="safe_selector" required>
                                <option value="">Select safe</option>
                                @foreach($safes as $safe)
                                    <option value="{{ $safe->id }}" data-currencies='@json($safe->currencies->map(fn ($currency) => ["id" => $currency->id, "label" => trim(($currency->code ?? '') . " - " . ($currency->name ?? ''))]))'>{{ $safe->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Currency</label>
                            <select class="form-select" id="outcomeCurrencySelect" name="currency_id">
                                <option value="">Select a safe first</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reference Type</label>
                            <select class="form-select" name="reference_type">
                                <option value="general">General</option>
                                <option value="supplier">Supplier</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Supplier</label>
                            <select class="form-select" name="supplier_id">
                                <option value="">Optional</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference</label>
                            <input type="text" class="form-control" name="reference" placeholder="Optional reference">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="description" placeholder="Short description">
                        </div>
                    </div>
                    @if($safes->isEmpty())
                        <div class="alert alert-warning mt-3 mb-0">Create a safe first before recording outcome.</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dashboard-primary">Save Outcome</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const analyticsUrl = document.getElementById('dashboardRoot').dataset.analyticsUrl;
        const currencySymbol = @json($currencySymbol);

        const formatMoney = (value) => `${currencySymbol}${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        const formatInt = (value) => Number(value || 0).toLocaleString();

        const renderCurrencyOptions = (safeSelect, currencySelect, form) => {
            const selectedOption = safeSelect.selectedOptions[0];
            const safeId = safeSelect.value;
            form.action = safeId ? form.dataset.actionTemplate.replace('__SAFE_ID__', safeId) : form.dataset.actionTemplate;

            const currencies = selectedOption && selectedOption.dataset.currencies ? JSON.parse(selectedOption.dataset.currencies) : [];
            currencySelect.innerHTML = '';

            if (!currencies.length) {
                currencySelect.innerHTML = '<option value="">No currencies available</option>';
                currencySelect.disabled = true;
                return;
            }

            currencySelect.disabled = false;
            currencySelect.innerHTML = '<option value="">Select currency</option>';
            currencies.forEach(currency => {
                const option = document.createElement('option');
                option.value = currency.id;
                option.textContent = currency.label || 'Currency';
                currencySelect.appendChild(option);
            });
        };

        const bindSafeModal = (safeSelectId, currencySelectId, formId) => {
            const safeSelect = document.getElementById(safeSelectId);
            const currencySelect = document.getElementById(currencySelectId);
            const form = document.getElementById(formId);
            if (!safeSelect || !currencySelect || !form) return;

            form.dataset.actionTemplate = form.getAttribute('action');
            safeSelect.addEventListener('change', () => renderCurrencyOptions(safeSelect, currencySelect, form));
            renderCurrencyOptions(safeSelect, currencySelect, form);
        };

        bindSafeModal('incomeSafeSelect', 'incomeCurrencySelect', 'incomeForm');
        bindSafeModal('outcomeSafeSelect', 'outcomeCurrencySelect', 'outcomeForm');

        const charts = {
            salesTrend: null,
            cashFlow: null,
            topProducts: null,
            subscriptions: null,
        };

        const destroyChart = (chart) => {
            if (chart) chart.destroy();
        };

        fetch(analyticsUrl, {
            headers: { 'Accept': 'application/json' }
        })
            .then(response => response.json())
            .then(payload => {
                if (!payload.success) return;

                const summary = payload.summary || {};
                document.getElementById('hero-active-merchants').textContent = formatInt(summary.active_merchants);
                document.getElementById('hero-net-cash-flow').textContent = formatMoney(summary.net_cash_flow);
                document.getElementById('hero-sales-count').textContent = formatInt(summary.sales_count);
                document.getElementById('hero-active-safes').textContent = formatInt(summary.active_safes);

                document.getElementById('metric-total-revenue').textContent = formatMoney(summary.total_revenue || {{ $totalRevenue }});
                document.getElementById('metric-total-sales').textContent = formatMoney(summary.total_sales);
                document.getElementById('metric-total-income').textContent = formatMoney(summary.total_income);
                document.getElementById('metric-total-outcome').textContent = formatMoney(summary.total_outcome);
                document.getElementById('metric-net-cash-flow').textContent = formatMoney(summary.net_cash_flow);
                document.getElementById('metric-total-merchants').textContent = formatInt(summary.total_merchants);
                document.getElementById('metric-active-merchants').textContent = formatInt(summary.active_merchants);
                document.getElementById('metric-active-subscriptions').textContent = formatInt(summary.active_subscriptions);
                document.getElementById('metric-expiring-soon').textContent = formatInt(summary.expiring_soon);
                document.getElementById('metric-total-safes').textContent = formatInt(summary.total_safes || summary.active_safes);
                document.getElementById('metric-active-safes').textContent = formatInt(summary.active_safes);
                document.getElementById('metric-sales-count').textContent = formatInt(summary.sales_count);

                const chartsData = payload.charts || {};
                const months = chartsData.months || [];

                destroyChart(charts.salesTrend);
                charts.salesTrend = new Chart(document.getElementById('salesTrendChart'), {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Sales',
                                data: chartsData.sales || [],
                                borderColor: '#ff8c00',
                                backgroundColor: 'rgba(255, 140, 0, 0.12)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Income',
                                data: chartsData.income || [],
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.12)',
                                fill: true,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                destroyChart(charts.cashFlow);
                charts.cashFlow = new Chart(document.getElementById('cashFlowChart'), {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            {
                                label: 'Income',
                                data: chartsData.income || [],
                                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                                borderRadius: 10
                            },
                            {
                                label: 'Outcome',
                                data: chartsData.outcome || [],
                                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                                borderRadius: 10
                            },
                            {
                                label: 'Profit',
                                data: chartsData.profit || [],
                                backgroundColor: 'rgba(245, 158, 11, 0.8)',
                                borderRadius: 10
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } },
                        scales: { y: { beginAtZero: true } }
                    }
                });

                destroyChart(charts.topProducts);
                charts.topProducts = new Chart(document.getElementById('topProductsChart'), {
                    type: 'doughnut',
                    data: {
                        labels: (chartsData.top_products || []).map(item => item.name),
                        datasets: [{
                            data: (chartsData.top_products || []).map(item => item.amount),
                            backgroundColor: ['#ff8c00', '#3b82f6', '#10b981', '#f59e0b', '#ef4444']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                destroyChart(charts.subscriptions);
                charts.subscriptions = new Chart(document.getElementById('subscriptionChart'), {
                    type: 'bar',
                    data: {
                        labels: chartsData.subscription_labels || [],
                        datasets: [{
                            label: 'Subscriptions',
                            data: chartsData.subscription_values || [],
                            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                            borderRadius: 12
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: { x: { beginAtZero: true } }
                    }
                });
            })
            .catch(() => {
                // Leave the server-rendered values in place if analytics cannot be loaded.
            });
    });
</script>
@endsection
