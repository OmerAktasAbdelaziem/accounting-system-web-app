@extends('layouts.modern')

@section('content')
<style>
    .payroll-page {
        position: relative;
        padding: 24px 0 40px;
    }

    .payroll-page::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        background:
            radial-gradient(circle at top left, rgba(255, 140, 0, 0.14), transparent 28%),
            radial-gradient(circle at top right, rgba(39, 174, 96, 0.10), transparent 24%),
            linear-gradient(180deg, rgba(248,250,252,0.7), rgba(248,250,252,0.95));
        z-index: 0;
    }

    .payroll-page > * {
        position: relative;
        z-index: 1;
    }

    .payroll-hero {
        position: relative;
        overflow: hidden;
        border: 0;
        border-radius: 32px;
        background:
            radial-gradient(circle at top right, rgba(255, 140, 0, 0.22), transparent 26%),
            radial-gradient(circle at left center, rgba(39, 174, 96, 0.18), transparent 28%),
            linear-gradient(135deg, #111827 0%, #1f2937 55%, #0f172a 100%);
        color: #fff;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
    }

    .payroll-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
        background-size: 26px 26px;
        mask-image: linear-gradient(to bottom, rgba(0,0,0,0.35), transparent 92%);
        pointer-events: none;
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .38rem .8rem;
        border-radius: 999px;
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(255,255,255,.65);
        color: #1f2937;
        font-size: .78rem;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .hero-kicker i {
        color: #ff8c00;
    }

    .hero-glow {
        position: absolute;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,140,0,.18), transparent 68%);
        top: -110px;
        right: -40px;
        filter: blur(10px);
        pointer-events: none;
    }

    .mini-stat {
        border: 0;
        border-radius: 22px;
        background: rgba(255,255,255,.82);
        backdrop-filter: blur(18px);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .mini-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 26px 50px rgba(15, 23, 42, 0.12);
    }

    .metric-value {
        font-size: clamp(1.55rem, 3vw, 2.2rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -.03em;
    }

    .metric-badge {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        color: #fff;
        flex: 0 0 auto;
    }

    .search-shell {
        border: 0;
        border-radius: 24px;
        background: #fff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .segment-bar {
        display: inline-flex;
        gap: .45rem;
        flex-wrap: wrap;
    }

    .segment-pill {
        border-radius: 999px;
        padding: .5rem .95rem;
        border: 1px solid rgba(15, 23, 42, .08);
        background: #fff;
        color: #334155;
        text-decoration: none;
        transition: all .18s ease;
    }

    .segment-pill.active,
    .segment-pill:hover {
        background: #111827;
        color: #fff;
        border-color: #111827;
        transform: translateY(-1px);
    }

    .table-shell {
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
    }

    .table-shell thead th {
        background: transparent;
        color: #111827;
        border-bottom: 0;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .payroll-row {
        transition: background-color .18s ease, transform .18s ease;
    }

    .payroll-row:hover {
        background: rgba(255, 140, 0, 0.04);
    }

    .payroll-action {
        display: inline-flex;
        flex-wrap: wrap;
        gap: .45rem;
        justify-content: flex-end;
    }

    .payroll-mobile-list {
        display: none;
    }

    .payroll-mobile-card {
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 24px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 16px 35px rgba(15, 23, 42, 0.08);
        padding: 16px;
        color: #0f172a;
    }

    .payroll-mobile-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .payroll-mobile-title {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .payroll-mobile-subtitle {
        font-size: .84rem;
        color: #475569;
    }

    .payroll-mobile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .payroll-mobile-pill {
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 10px 12px;
    }

    .payroll-mobile-pill .label {
        display: block;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        margin-bottom: 4px;
    }

    .payroll-mobile-pill .value {
        font-size: .95rem;
        font-weight: 800;
        color: #0f172a;
    }

    .payroll-page .table-shell .text-muted,
    .payroll-page .search-shell .text-muted,
    .payroll-page .mini-stat .text-muted,
    .payroll-page .payroll-mobile-card .text-muted,
    .payroll-page .payroll-mobile-card .section-heading,
    .payroll-page .payroll-mobile-card .section-subtitle {
        color: #475569 !important;
    }

    .payroll-page .payroll-mobile-card .badge,
    .payroll-page .table-shell .badge {
        font-weight: 700;
    }

    .payroll-mobile-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .payroll-mobile-actions .btn {
        width: 100%;
        border-radius: 14px;
    }

    .section-heading {
        font-weight: 800;
        letter-spacing: -.03em;
        color: #0f172a;
    }

    .section-subtitle {
        color: #64748b;
    }

    .soft-panel {
        border: 0;
        border-radius: 24px;
        background: linear-gradient(180deg, #fff, #f8fafc);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .snapshot-panel {
        min-width: min(100%, 290px);
        max-width: 320px;
        padding: 1rem !important;
        background: rgba(255,255,255,.92);
        color: #0f172a;
        border: 1px solid rgba(226, 232, 240, .95);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.10);
    }

    .snapshot-panel .h6,
    .snapshot-panel .snapshot-title,
    .snapshot-panel .snapshot-value,
    .snapshot-panel .small {
        color: #0f172a;
    }

    .snapshot-panel .snapshot-title,
    .snapshot-panel .small {
        opacity: 1 !important;
    }

    .snapshot-title {
        font-size: .72rem;
        letter-spacing: .06em;
    }

    .snapshot-value {
        font-size: 1.35rem;
        line-height: 1.05;
        font-weight: 900;
    }

    .snapshot-chip {
        padding: .28rem .65rem;
        font-size: .72rem;
    }

    .hero-note {
        display: inline-flex;
        align-items: center;
        max-width: 54rem;
        padding: .85rem 1rem;
        border-radius: 1rem;
        background: rgba(255,255,255,.9);
        color: #374151;
        border: 1px solid rgba(255,255,255,.55);
        box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
        line-height: 1.65;
    }

    @media (max-width: 768px) {
        .payroll-page {
            padding: 10px 0 26px;
        }

        .payroll-hero {
            border-radius: 26px;
        }

        .search-shell,
        .mini-stat,
        .table-shell,
        .soft-panel {
            border-radius: 22px;
        }

        .payroll-action {
            justify-content: flex-start;
        }

        .hero-buttons .btn {
            width: 100%;
        }

        .payroll-mobile-list {
            display: grid;
            gap: 12px;
        }

        .table-shell .table-responsive,
        .table-shell table {
            display: none;
        }

        .payroll-mobile-grid {
            grid-template-columns: 1fr;
        }

        .payroll-mobile-actions {
            grid-template-columns: 1fr;
        }

        .section-heading {
            font-size: 1.05rem;
        }
    }
</style>

<div class="container-fluid payroll-page">
    <div class="card payroll-hero mb-4">
        <div class="hero-glow"></div>
        <div class="card-body p-4 p-lg-5 position-relative" style="z-index: 1;">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-4">
                <div class="flex-grow-1">
                    <div class="hero-kicker mb-3">
                        <i class="bi bi-stars"></i>
                        Payroll command center
                    </div>
                    <h1 class="display-6 fw-black mb-3 text-white" style="font-weight: 900; letter-spacing: -.05em;">{{ __('messages.payroll') }}</h1>
                    <p class="hero-note mb-4">
                        Track unsettled payrolls, settle payments from safes, and keep a clean history of every payroll movement in one place.
                    </p>
                    <div class="d-flex flex-wrap gap-2 hero-buttons">
                        @feature('payroll')
                        <a href="{{ route('payroll.create') }}" class="btn btn-warning btn-lg">
                            <i class="bi bi-plus-circle"></i> Create payroll
                        </a>
                        @endfeature
                        <a href="#activePayrollPanel" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-grid-1x2"></i> Review active payrolls
                        </a>
                    </div>
                </div>

                <div class="soft-panel snapshot-panel" style="backdrop-filter: blur(18px);">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <div class="text-uppercase fw-semibold snapshot-title">Live snapshot</div>
                            <div class="h6 mb-0">Payroll health</div>
                        </div>
                        <span class="badge rounded-pill bg-success snapshot-chip">Dynamic</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="p-2 rounded-4 bg-white border border-slate-200 h-100 text-center">
                                <div class="small mb-1">Unpaid</div>
                                <div class="snapshot-value">{{ $unpaidPayrollCount ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded-4 bg-white border border-slate-200 h-100 text-center">
                                <div class="small mb-1">Need pay</div>
                                <div class="snapshot-value">{{ $currencySymbol }}{{ number_format($unpaidNetSalaryTotal ?? 0, 0) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded-4 bg-white border border-slate-200 h-100 text-center">
                                <div class="small mb-1">Paid</div>
                                <div class="snapshot-value">{{ $paidPayrollCount ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card mini-stat h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="metric-badge" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Unpaid payrolls</div>
                        <div class="metric-value">{{ $unpaidPayrollCount ?? 0 }}</div>
                        <div class="text-muted small mt-1">Waiting to be settled from a safe.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mini-stat h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="metric-badge" style="background: linear-gradient(135deg, #27ae60, #16a34a);">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Unpaid net salaries</div>
                        <div class="metric-value">{{ $currencySymbol }}{{ number_format($unpaidNetSalaryTotal ?? 0, 2) }}</div>
                        <div class="text-muted small mt-1">Total pending payout amount.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mini-stat h-100">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="metric-badge" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6);">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Paid payrolls</div>
                        <div class="metric-value">{{ $paidPayrollCount ?? 0 }}</div>
                        <div class="text-muted small mt-1">Completed payroll records archived below.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="search-shell p-3 p-lg-4 mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-lg-6">
                <label class="form-label fw-semibold mb-2">Quick search</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" id="payrollSearch" class="form-control" placeholder="Search employee, month, safe, status, or payroll ID">
                </div>
            </div>
            <div class="col-lg-6 text-lg-end">
                <label class="form-label fw-semibold mb-2 d-block">View mode</label>
                <div class="segment-bar" role="tablist" aria-label="Payroll sections">
                    <a class="segment-pill active" href="#activePayrollPanel" data-payroll-tab="active">Active payrolls</a>
                    <a class="segment-pill" href="#paidPayrollPanel" data-payroll-tab="paid">Paid history</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4" id="activePayrollPanel">
        <div class="col-12">
            <div class="card table-shell">
                <div class="card-header border-0 py-4 px-4" style="background: linear-gradient(135deg, #111827, #1f2937); color: #fff;">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h5 class="mb-1 section-heading text-white">Active payrolls</h5>
                            <div class="small opacity-75">Settle pending salaries from an available safe.</div>
                        </div>
                        <div class="small opacity-75">
                            <i class="bi bi-lightning-charge"></i> Payments update safe balances and history instantly.
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="payroll-mobile-list p-3 p-lg-0">
                        @forelse($activePayrolls ?? [] as $payroll)
                            @php
                                $employeeName = '';
                                if ($payroll->employee) {
                                    $employeeName = is_string($payroll->employee->name)
                                        ? $payroll->employee->name
                                        : (is_array($payroll->employee->name)
                                            ? ($payroll->employee->name[app()->getLocale()] ?? implode(' - ', $payroll->employee->name))
                                            : json_encode($payroll->employee->name));
                                }
                                $netAmount = (float) ($payroll->calculated_net_salary ?? $payroll->net_salary);
                                $payUrl = route('payroll.pay', $payroll);
                            @endphp
                            <div class="payroll-mobile-card" data-payroll-row data-row-group="active" data-search="{{ strtolower(trim($employeeName . ' ' . $payroll->month . '/' . $payroll->year . ' ' . $payroll->id . ' ' . ($payroll->safe?->name ?? '') . ' ' . strtoupper($payroll->status ?? 'draft'))) }}">
                                <div class="payroll-mobile-top">
                                    <div>
                                        <div class="payroll-mobile-title">{{ $employeeName }}</div>
                                        <div class="payroll-mobile-subtitle">{{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y') }} · ID #{{ $payroll->id }}</div>
                                    </div>
                                    <span class="badge rounded-pill bg-warning text-dark">{{ strtoupper($payroll->status ?? 'draft') }}</span>
                                </div>
                                <div class="payroll-mobile-grid">
                                    <div class="payroll-mobile-pill">
                                        <span class="label">Net salary</span>
                                        <span class="value">{{ $currencySymbol }}{{ number_format($netAmount, 2) }}</span>
                                    </div>
                                    <div class="payroll-mobile-pill">
                                        <span class="label">Employee code</span>
                                        <span class="value">{{ $payroll->employee?->employee_code ?? 'N/A' }}</span>
                                    </div>
                                    <div class="payroll-mobile-pill">
                                        <span class="label">Safe source</span>
                                        <span class="value">{{ $payroll->safe?->name ?? 'Not selected' }}</span>
                                    </div>
                                    <div class="payroll-mobile-pill">
                                        <span class="label">Payroll period</span>
                                        <span class="value">{{ $payroll->month }}/{{ $payroll->year }}</span>
                                    </div>
                                </div>
                                <div class="payroll-mobile-actions">
                                    @feature('payroll')
                                    <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    @endfeature
                                    @feature('payroll')
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#payrollPayModal"
                                        data-pay-url="{{ $payUrl }}"
                                        data-pay-employee="{{ $employeeName }}"
                                        data-pay-amount="{{ number_format($netAmount, 2, '.', '') }}"
                                    >
                                        Mark paid
                                    </button>
                                    @endfeature
                                    @feature('payroll')
                                    <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    @endfeature
                                    @feature('payroll')
                                    <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this payroll? If it was paid, the linked payment will be reversed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                    </form>
                                    @endfeature
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <div class="fw-semibold mb-1">No unpaid payrolls found</div>
                                <div class="small">Create payrolls or mark existing records as paid to populate this section.</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Employee</th>
                                    <th>Payroll period</th>
                                    <th>Net salary</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activePayrolls ?? [] as $payroll)
                                    @php
                                        $employeeName = '';
                                        if ($payroll->employee) {
                                            $employeeName = is_string($payroll->employee->name)
                                                ? $payroll->employee->name
                                                : (is_array($payroll->employee->name)
                                                    ? ($payroll->employee->name[app()->getLocale()] ?? implode(' - ', $payroll->employee->name))
                                                    : json_encode($payroll->employee->name));
                                        }
                                        $netAmount = (float) ($payroll->calculated_net_salary ?? $payroll->net_salary);
                                        $payUrl = route('payroll.pay', $payroll);
                                        $searchText = trim($employeeName . ' ' . $payroll->month . '/' . $payroll->year . ' ' . $payroll->id . ' ' . ($payroll->safe?->name ?? '') . ' ' . strtoupper($payroll->status ?? 'draft'));
                                    @endphp
                                    <tr class="payroll-row" data-payroll-row data-row-group="active" data-search="{{ strtolower($searchText) }}">
                                        <td class="ps-4 fw-semibold">{{ $payroll->id }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $employeeName }}</div>
                                            <div class="text-muted small">Code: {{ $payroll->employee?->employee_code ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-dark border">{{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y') }}</span>
                                        </td>
                                        <td class="fw-bold fs-6">{{ $currencySymbol }}{{ number_format($netAmount, 2) }}</td>
                                        <td>
                                            <span class="badge rounded-pill bg-warning text-dark">{{ strtoupper($payroll->status ?? 'draft') }}</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="payroll-action">
                                                @feature('payroll')
                                                <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                                @endfeature
                                                @feature('payroll')
                                                <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                @endfeature
                                                @feature('payroll')
                                                <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payroll? If it was paid, the linked payment will be reversed.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                                @endfeature
                                                @feature('payroll')
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#payrollPayModal"
                                                    data-pay-url="{{ $payUrl }}"
                                                    data-pay-employee="{{ $employeeName }}"
                                                    data-pay-amount="{{ number_format($netAmount, 2, '.', '') }}"
                                                >
                                                    Paid
                                                </button>
                                                @endfeature
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="fw-semibold mb-1">No unpaid payrolls found</div>
                                            <div class="small">Create payrolls or mark existing records as paid to populate this section.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 p-lg-4">
                        {{ $activePayrolls->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4" id="paidPayrollPanel">
        <div class="col-12">
            <div class="card table-shell">
                <div class="card-header border-0 py-4 px-4" style="background: linear-gradient(135deg, #0f172a, #334155); color: #fff;">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h5 class="mb-1 section-heading text-white">Paid payroll history</h5>
                            <div class="small opacity-75">Review settled payrolls, safe source, and payment time.</div>
                        </div>
                        <div class="small opacity-75">
                            <i class="bi bi-shield-check"></i> Closed payrolls remain editable only through deletion.
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="payroll-mobile-list p-3 p-lg-0">
                        @forelse($paidPayrolls ?? [] as $payroll)
                            @php
                                $employeeName = '';
                                if ($payroll->employee) {
                                    $employeeName = is_string($payroll->employee->name)
                                        ? $payroll->employee->name
                                        : (is_array($payroll->employee->name)
                                            ? ($payroll->employee->name[app()->getLocale()] ?? implode(' - ', $payroll->employee->name))
                                            : json_encode($payroll->employee->name));
                                }
                                $netAmount = (float) ($payroll->calculated_net_salary ?? $payroll->net_salary);
                                $searchText = trim($employeeName . ' ' . $payroll->month . '/' . $payroll->year . ' ' . $payroll->id . ' ' . ($payroll->safe?->name ?? '') . ' ' . strtoupper($payroll->status ?? 'paid'));
                            @endphp
                            <div class="payroll-mobile-card" data-payroll-row data-row-group="paid" data-search="{{ strtolower($searchText) }}">
                                <div class="payroll-mobile-top">
                                    <div>
                                        <div class="payroll-mobile-title">{{ $employeeName }}</div>
                                        <div class="payroll-mobile-subtitle">{{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y') }} · ID #{{ $payroll->id }}</div>
                                    </div>
                                    <span class="badge rounded-pill bg-success">PAID</span>
                                </div>
                                <div class="payroll-mobile-grid">
                                    <div class="payroll-mobile-pill">
                                        <span class="label">Net salary</span>
                                        <span class="value text-success">{{ $currencySymbol }}{{ number_format($netAmount, 2) }}</span>
                                    </div>
                                    <div class="payroll-mobile-pill">
                                        <span class="label">Safe</span>
                                        <span class="value">{{ $payroll->safe?->name ?? '-' }}</span>
                                    </div>
                                    <div class="payroll-mobile-pill">
                                        <span class="label">Paid at</span>
                                        <span class="value">{{ optional($payroll->processed_at)->format('Y-m-d H:i') ?? '-' }}</span>
                                    </div>
                                    <div class="payroll-mobile-pill">
                                        <span class="label">Code</span>
                                        <span class="value">{{ $payroll->employee?->employee_code ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="payroll-mobile-actions">
                                    @feature('payroll')
                                    <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    @endfeature
                                    @feature('downloads')
                                    <a href="{{ route('payroll.payslip', $payroll) }}" class="btn btn-sm btn-outline-danger">PDF</a>
                                    @endfeature
                                    @feature('payroll')
                                    <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this payroll? If it was paid, the linked payment will be reversed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">Delete</button>
                                    </form>
                                    @endfeature
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <div class="fw-semibold mb-1">No paid payroll history found</div>
                                <div class="small">Paid records will appear here after settlement.</div>
                            </div>
                        @endforelse
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Employee</th>
                                    <th>Safe</th>
                                    <th>Net salary</th>
                                    <th>Paid at</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paidPayrolls ?? [] as $payroll)
                                    @php
                                        $employeeName = '';
                                        if ($payroll->employee) {
                                            $employeeName = is_string($payroll->employee->name)
                                                ? $payroll->employee->name
                                                : (is_array($payroll->employee->name)
                                                    ? ($payroll->employee->name[app()->getLocale()] ?? implode(' - ', $payroll->employee->name))
                                                    : json_encode($payroll->employee->name));
                                        }
                                        $netAmount = (float) ($payroll->calculated_net_salary ?? $payroll->net_salary);
                                        $searchText = trim($employeeName . ' ' . $payroll->month . '/' . $payroll->year . ' ' . $payroll->id . ' ' . ($payroll->safe?->name ?? '') . ' ' . strtoupper($payroll->status ?? 'paid'));
                                    @endphp
                                    <tr class="payroll-row" data-payroll-row data-row-group="paid" data-search="{{ strtolower($searchText) }}">
                                        <td class="ps-4 fw-semibold">{{ $payroll->id }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $employeeName }}</div>
                                            <div class="text-muted small">{{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y') }}</div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-dark border">{{ $payroll->safe?->name ?? '-' }}</span>
                                        </td>
                                        <td class="fw-bold text-success">{{ $currencySymbol }}{{ number_format($netAmount, 2) }}</td>
                                        <td>{{ optional($payroll->processed_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                        <td class="text-end pe-4">
                                            <div class="payroll-action">
                                                @feature('payroll')
                                                <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                                @endfeature
                                                @feature('downloads')
                                                    <a href="{{ route('payroll.payslip', $payroll) }}" class="btn btn-sm btn-outline-danger">PDF</a>
                                                @endfeature
                                                @feature('payroll')
                                                <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this payroll? If it was paid, the linked payment will be reversed.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                                @endfeature
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <div class="fw-semibold mb-1">No paid payroll history found</div>
                                            <div class="small">Paid records will appear here after settlement.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 p-lg-4">
                        {{ $paidPayrolls->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="payrollPayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #111827, #1f2937); color: #fff;">
                <div>
                    <h5 class="modal-title mb-1">{{ __('Pay Payroll') }}</h5>
                    <div class="small opacity-75">Choose the safe that should be deducted for this payroll payment.</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="payrollPayForm" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-4">
                        <div class="fw-semibold" id="payrollPayEmployee"></div>
                        <div class="small" id="payrollPayAmount"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Safe') }}</label>
                        <select name="safe_id" class="form-select form-select-lg" required>
                            <option value="">{{ __('Select safe') }}</option>
                            @foreach($safes ?? [] as $safe)
                                <option value="{{ $safe->id }}">{{ $safe->name }} - {{ $currencySymbol }}{{ number_format((float) $safe->balance, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Confirm Payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('payrollSearch');
    const rows = Array.from(document.querySelectorAll('[data-payroll-row]'));
    const segmentPills = Array.from(document.querySelectorAll('[data-payroll-tab]'));

    function applySearch() {
        const query = (searchInput?.value || '').trim().toLowerCase();
        rows.forEach((row) => {
            const haystack = (row.getAttribute('data-search') || '').toLowerCase();
            const matches = !query || haystack.includes(query);
            row.style.display = matches ? '' : 'none';
        });
    }

    function activateSection(targetId) {
        segmentPills.forEach((pill) => pill.classList.toggle('active', pill.getAttribute('href') === '#' + targetId));
        const target = document.getElementById(targetId);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applySearch);
    }

    segmentPills.forEach((pill) => {
        pill.addEventListener('click', function (event) {
            const href = pill.getAttribute('href');
            if (!href || !href.startsWith('#')) {
                return;
            }
            event.preventDefault();
            activateSection(href.substring(1));
        });
    });

    const modal = document.getElementById('payrollPayModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) {
                return;
            }

            const payUrl = button.getAttribute('data-pay-url');
            const employee = button.getAttribute('data-pay-employee') || '';
            const amount = button.getAttribute('data-pay-amount') || '0.00';

            const form = document.getElementById('payrollPayForm');
            const employeeLabel = document.getElementById('payrollPayEmployee');
            const amountLabel = document.getElementById('payrollPayAmount');

            if (form) {
                form.action = payUrl;
            }
            if (employeeLabel) {
                employeeLabel.textContent = employee;
            }
            if (amountLabel) {
                amountLabel.textContent = 'Amount to deduct: {{ $currencySymbol }}' + amount;
            }
        });
    }

    applySearch();
});
</script>
@endsection
