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

        .list-group-item {
            flex-direction: column;
            gap: 8px;
        }

        .pill {
            width: fit-content;
        }
    }

    @media (max-width: 576px) {
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
    }
</style>

<div id="merchantDashboardRoot" class="merchant-dashboard-shell" data-analytics-url="{{ route('dashboard.analytics') }}">
    <div class="dashboard-hero">
        <div class="dashboard-hero-inner">
            <div>
                <h1 class="dashboard-title"><i class="bi bi-speedometer2 me-2"></i>{{ __('messages.dashboard') }}</h1>
                <p class="dashboard-subtitle">Welcome back, {{ auth()->user()->name }}. This merchant dashboard is built for day-to-day operations, inventory control, cash flow monitoring, and fast actions.</p>
            </div>
            <div class="hero-kpis">
                <div class="hero-kpi">
                    <span class="hero-kpi-label">Today Sales</span>
                    <div class="hero-kpi-value" id="hero-total-sales">{{ $currencySymbol }}{{ number_format($totalSales ?? 0, 2) }}</div>
                </div>
                <div class="hero-kpi">
                    <span class="hero-kpi-label">Total Balance</span>
                    <div class="hero-kpi-value" id="hero-safe-balance">{{ $currencySymbol }}{{ number_format($safeBalance ?? 0, 2) }}</div>
                </div>
                <div class="hero-kpi">
                    <span class="hero-kpi-label">Products</span>
                    <div class="hero-kpi-value" id="hero-total-products">{{ $totalProducts ?? 0 }}</div>
                </div>
                <div class="hero-kpi">
                    <span class="hero-kpi-label">Low Stock</span>
                    <div class="hero-kpi-value" id="hero-low-stock">{{ $lowStockCount ?? 0 }}</div>
                </div>
            </div>
        </div>
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

    <div class="dashboard-section">
        <div class="section-heading">
            <div>
                <h2 class="section-title"><i class="bi bi-bar-chart-line-fill text-warning"></i>Operational metrics</h2>
                <p class="section-subtitle">Live totals for sales, inventory, storage, commissions, and cash flow.</p>
            </div>
        </div>
        <div class="metrics-grid">
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
        </div>
    </div>

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
                    <h3 class="section-title mb-0">Sales trend</h3>
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
                    <div class="table-responsive">
                        <table class="table align-middle">
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
                    <div class="table-responsive">
                        <table class="table align-middle">
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

        const formatMoney = (value) => `${currencySymbol}${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        const formatInt = (value) => Number(value || 0).toLocaleString();

        const salesTrendCtx = document.getElementById('salesTrendChart')?.getContext('2d');
        const cashFlowCtx = document.getElementById('cashFlowChart')?.getContext('2d');
        const inventoryCtx = document.getElementById('inventoryChart')?.getContext('2d');
        const storageCtx = document.getElementById('storageChart')?.getContext('2d');

        if (!analyticsUrl || !salesTrendCtx || !cashFlowCtx || !inventoryCtx || !storageCtx) {
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

                new Chart(salesTrendCtx, {
                    type: 'line',
                    data: {
                        labels: months,
                        datasets: [{
                            label: 'Sales',
                            data: charts.sales || [],
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
