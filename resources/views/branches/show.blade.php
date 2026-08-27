@extends('layouts.modern')

@section('title', $branch->name)

@section('content')
@php
    $summaryCards = [
        ['label' => __('Employees'), 'count' => $branch->employees_count ?? 0, 'icon' => 'bi-people', 'tone' => 'primary'],
        ['label' => __('Products'), 'count' => $branch->products_count ?? 0, 'icon' => 'bi-box-seam', 'tone' => 'success'],
        ['label' => __('Categories'), 'count' => $branch->categories_count ?? 0, 'icon' => 'bi-tags', 'tone' => 'warning'],
        ['label' => __('Customers'), 'count' => $branch->customers_count ?? 0, 'icon' => 'bi-person-badge', 'tone' => 'info'],
        ['label' => __('Suppliers'), 'count' => $branch->suppliers_count ?? 0, 'icon' => 'bi-truck', 'tone' => 'secondary'],
        ['label' => __('Invoices'), 'count' => $branch->invoices_count ?? 0, 'icon' => 'bi-receipt', 'tone' => 'danger'],
        ['label' => __('Storages'), 'count' => $branch->storages_count ?? 0, 'icon' => 'bi-database', 'tone' => 'dark'],
        ['label' => __('Safes'), 'count' => $branch->safes_count ?? 0, 'icon' => 'bi-safe', 'tone' => 'success'],
    ];

    $quickActions = [
        ['label' => __('New Employee'), 'route' => 'employees.create', 'icon' => 'bi-people', 'class' => 'btn-primary'],
        ['label' => __('New Product'), 'route' => 'products.create', 'icon' => 'bi-box-seam', 'class' => 'btn-success'],
        ['label' => __('New Category'), 'route' => 'categories.create', 'icon' => 'bi-tags', 'class' => 'btn-warning'],
        ['label' => __('New Supplier'), 'route' => 'suppliers.create', 'icon' => 'bi-truck', 'class' => 'btn-secondary'],
        ['label' => __('New Invoice'), 'route' => 'invoices.create', 'icon' => 'bi-receipt', 'class' => 'btn-danger'],
        ['label' => __('New Safe'), 'route' => 'safes.create', 'icon' => 'bi-safe', 'class' => 'btn-success'],
        ['label' => __('New Payroll'), 'route' => 'payroll.create', 'icon' => 'bi-wallet2', 'class' => 'btn-dark'],
        ['label' => __('New Commission'), 'route' => 'commissions.create', 'icon' => 'bi-graph-up', 'class' => 'btn-primary'],
    ];

    $overviewStats = [
        ['label' => __('Branch outstanding'), 'value' => $branchOutstandingTotal ?? 0, 'icon' => 'bi-cash-stack', 'tone' => 'danger', 'format' => 'money'],
        ['label' => __('Payrolls'), 'value' => $branchPayrolls->total() ?? count($branchPayrolls ?? []), 'icon' => 'bi-wallet2', 'tone' => 'warning', 'format' => 'count'],
        ['label' => __('Commissions'), 'value' => $branchCommissions->total() ?? count($branchCommissions ?? []), 'icon' => 'bi-graph-up', 'tone' => 'primary', 'format' => 'count'],
        ['label' => __('Suppliers'), 'value' => $branchSuppliers->total() ?? count($branchSuppliers ?? []), 'icon' => 'bi-truck', 'tone' => 'secondary', 'format' => 'count'],
    ];

    $directorySections = [
        ['key' => 'employees', 'title' => __('Employees'), 'items' => $recentEmployees ?? [], 'icon' => 'bi-people', 'tone' => 'primary', 'empty' => __('No employees are linked to this branch yet.')],
        ['key' => 'products', 'title' => __('Products'), 'items' => $recentProducts ?? [], 'icon' => 'bi-box-seam', 'tone' => 'success', 'empty' => __('No products are linked to this branch yet.')],
        ['key' => 'categories', 'title' => __('Categories'), 'items' => $recentCategories ?? [], 'icon' => 'bi-tags', 'tone' => 'warning', 'empty' => __('No categories are linked to this branch yet.')],
        ['key' => 'customers', 'title' => __('Customers'), 'items' => $recentCustomers ?? [], 'icon' => 'bi-person-badge', 'tone' => 'info', 'empty' => __('No customers are linked to this branch yet.')],
        ['key' => 'invoices', 'title' => __('Invoices'), 'items' => $recentInvoices ?? [], 'icon' => 'bi-receipt', 'tone' => 'danger', 'empty' => __('No invoices are linked to this branch yet.')],
        ['key' => 'storages', 'title' => __('Storages'), 'items' => $recentStorages ?? [], 'icon' => 'bi-database', 'tone' => 'dark', 'empty' => __('No storages are linked to this branch yet.')],
        ['key' => 'safes', 'title' => __('Safes'), 'items' => $recentSafes ?? [], 'icon' => 'bi-safe', 'tone' => 'success', 'empty' => __('No safes are linked to this branch yet.')],
    ];

    $selectedTab = 'overview';
@endphp

<style>
    .branch-page {
        padding: 24px 0 40px;
    }

    .branch-hero {
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        background: linear-gradient(135deg, #0f172a 0%, #111827 35%, #0f766e 100%);
        color: #fff;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.2);
    }

    .branch-hero::after {
        content: '';
        position: absolute;
        inset: auto -20% -40% auto;
        width: 280px;
        height: 280px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    .branch-hero-body {
        position: relative;
        z-index: 1;
        padding: 30px;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        color: #e5e7eb;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .glass-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
    }

    .panel-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .panel-head {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #e9edf3;
    }

    .metric-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        height: 100%;
    }

    .metric-icon {
        width: 46px;
        height: 46px;
        border-radius: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .section-title {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
    }

    .nav-surface {
        background: #f8fafc;
        border: 1px solid #e9edf3;
        border-radius: 18px;
        padding: 8px;
    }

    .nav-surface .nav-link {
        border-radius: 14px;
        color: #475569;
        font-weight: 700;
    }

    .nav-surface .nav-link.active {
        background: #0f172a;
        color: #fff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.18);
    }

    .tab-block {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .table-tight th,
    .table-tight td {
        vertical-align: middle;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
    }

    .metric-money {
        font-size: 1.35rem;
        line-height: 1;
        white-space: nowrap;
    }

    .sticky-summary {
        position: sticky;
        top: 1rem;
    }

    @media (max-width: 992px) {
        .branch-hero-body {
            padding: 22px;
        }

        .sticky-summary {
            position: static;
        }
    }
</style>

<div class="branch-page">
    <div class="container-fluid">
        <div class="branch-hero mb-4">
            <div class="branch-hero-body">
                <div class="row g-4 align-items-start">
                    <div class="col-xl-8">
                        <div class="hero-badge mb-3">
                            <i class="bi bi-diagram-3"></i>
                            {{ __('Branch workspace') }}
                        </div>
                        <h1 class="display-5 fw-bold mb-2">{{ $branch->name }}</h1>
                        <p class="lead text-white-75 mb-4">{{ __('A focused control room for staffing, finance, inventory, and branch-specific supplier balances.') }}</p>

                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="glass-pill"><i class="bi bi-hash"></i>{{ $branch->code }}</span>
                            <span class="glass-pill"><i class="bi bi-geo-alt"></i>{{ $branch->city ?: __('messages.not_available') }}</span>
                            <span class="glass-pill"><i class="bi bi-telephone"></i>{{ $branch->phone ?: __('messages.not_available') }}</span>
                            <span class="glass-pill"><i class="bi bi-person-badge"></i>{{ $branch->manager_name ?: __('messages.not_available') }}</span>
                        </div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-danger" data-action="branch-debts" data-url="{{ route('branches.debts', $branch) }}">
                                <i class="bi bi-journal-text me-2"></i>{{ __('messages.branch_debts') }}
                            </button>
                        </div>

                        <div class="row g-3">
                            @foreach($summaryCards as $card)
                                <div class="col-6 col-md-4 col-xl-3">
                                    <div class="card metric-card bg-white">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="metric-icon bg-{{ $card['tone'] }} bg-opacity-10 text-{{ $card['tone'] }}">
                                                    <i class="bi {{ $card['icon'] }}"></i>
                                                </div>
                                                <div class="stat-value text-{{ $card['tone'] }}">{{ $card['count'] }}</div>
                                            </div>
                                            <div class="text-muted small fw-semibold">{{ $card['label'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="sticky-summary">
                            <div class="card panel-card mb-3">
                                <div class="card-header panel-head py-3 px-4">
                                    <div class="section-title mb-1">{{ __('Branch status') }}</div>
                                    <h4 class="mb-0">{{ __('Current state') }}</h4>
                                </div>
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-{{ $branch->is_active ? 'success' : 'secondary' }} px-3 py-2">
                                            {{ $branch->is_active ? __('messages.active') : __('messages.inactive') }}
                                        </span>
                                        <div class="text-muted small">{{ __('Updated branch overview') }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="fw-semibold">{{ __('Outstanding') }}</span>
                                            <span class="fw-bold text-danger">{{ $currencySymbol }}{{ number_format((float) ($branchOutstandingTotal ?? 0), 2) }}</span>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-danger" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column gap-2 text-muted small">
                                        <div><i class="bi bi-people me-2 text-primary"></i>{{ $branch->employees_count ?? 0 }} {{ __('employees on this branch') }}</div>
                                        <div><i class="bi bi-truck me-2 text-secondary"></i>{{ $branch->suppliers_count ?? 0 }} {{ __('suppliers linked') }}</div>
                                        <div><i class="bi bi-wallet2 me-2 text-warning"></i>{{ $branchPayrolls->total() ?? 0 }} {{ __('payroll records') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="card panel-card">
                                @php
                                    use Illuminate\Support\Str;
                                    $showQuickActions = false;
                                    foreach ($quickActions as $action) {
                                        $routeParts = explode('.', $action['route']);
                                        $page = $routeParts[0] ?? null;
                                        $featureKey = $page ? $page . '.create' : null;
                                        if ($featureKey && auth()->user()?->canViewMenuItem($featureKey)) { $showQuickActions = true; break; }
                                    }
                                @endphp

                                @if($showQuickActions)
                                    <div class="card-body p-4">
                                        <div class="section-title mb-3">{{ __('Quick actions') }}</div>
                                        <div class="d-grid gap-2">
                                            @foreach($quickActions as $action)
                                                @php $page = explode('.', $action['route'])[0] ?? null; @endphp
                                                @feature($page . '.create')
                                                    <a class="btn {{ $action['class'] }} py-3" href="{{ route($action['route'], ['branch_ids' => [$branch->id]]) }}">
                                                        <i class="bi {{ $action['icon'] }} me-2"></i> {{ __($action['label']) }}
                                                    </a>
                                                @endfeature
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="section-title mb-1">{{ __('Branch analytics') }}</div>
                    <h3 class="h4 mb-1">{{ __('Branch performance summary') }}</h3>
                    <p class="text-muted mb-0">{{ __('Monthly sales and cash flow metrics for this branch only.') }}</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-6">
                <div class="card panel-card h-100">
                    <div class="card-header panel-head py-3 px-4">
                        <div class="section-title mb-1">{{ __('Branch analytics') }}</div>
                        <h5 class="mb-0">{{ __('Sales trend') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="chart-box" style="min-height: 320px;">
                            <canvas id="branchSalesTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card panel-card h-100">
                    <div class="card-header panel-head py-3 px-4">
                        <div class="section-title mb-1">{{ __('Branch analytics') }}</div>
                        <h5 class="mb-0">{{ __('Income vs outcome') }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="chart-box" style="min-height: 320px;">
                            <canvas id="branchCashFlowChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card panel-card mb-4">
            <div class="card-body p-3 p-md-4">
                <div class="nav-surface">
                    <ul class="nav nav-pills nav-fill gap-2" id="branchDashboardTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview-pane" type="button" role="tab" aria-controls="overview-pane" aria-selected="true">{{ __('Overview') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="operations-tab" data-bs-toggle="pill" data-bs-target="#operations-pane" type="button" role="tab" aria-controls="operations-pane" aria-selected="false">{{ __('Operations') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="records-tab" data-bs-toggle="pill" data-bs-target="#records-pane" type="button" role="tab" aria-controls="records-pane" aria-selected="false">{{ __('Records') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="directory-tab" data-bs-toggle="pill" data-bs-target="#directory-pane" type="button" role="tab" aria-controls="directory-pane" aria-selected="false">{{ __('Directory') }}</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content pt-4">
                    <div class="tab-pane fade show active" id="overview-pane" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="row g-4">
                            @foreach($overviewStats as $metric)
                                <div class="col-md-6 col-xl-3">
                                    <div class="card metric-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <div class="metric-icon bg-{{ $metric['tone'] }} bg-opacity-10 text-{{ $metric['tone'] }}">
                                                    <i class="bi {{ $metric['icon'] }}"></i>
                                                </div>
                                                <div class="text-muted small fw-semibold">{{ $metric['label'] }}</div>
                                            </div>
                                            @if($metric['format'] === 'money')
                                                <div class="stat-value metric-money text-{{ $metric['tone'] }}">{{ $currencySymbol }}{{ number_format((float) $metric['value'], 2) }}</div>
                                            @else
                                                <div class="stat-value text-{{ $metric['tone'] }}">{{ $metric['value'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="tab-pane fade" id="operations-pane" role="tabpanel" aria-labelledby="operations-tab">
                        <div class="row g-4">
                            <div class="col-xl-6">
                                <div class="card tab-block h-100">
                                    <div class="card-header panel-head py-3 px-4">
                                        <div class="section-title mb-1">{{ __('People') }}</div>
                                        <h5 class="mb-0">{{ __('Branch staffing') }}</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="small text-muted mb-2">{{ __('Available employees') }}</div>
                                                @if(count($unassignedEmployees) > 0)
                                                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                                                        @foreach($unassignedEmployees as $emp)
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="fw-semibold">{{ $emp->name }}</div>
                                                                    <div class="text-muted small">{{ $emp->position }}</div>
                                                                </div>
                                                                <form action="{{ route('branches.assign-employee', $branch) }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i></button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-muted">{{ __('No unassigned employees available.') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <div class="small text-muted mb-2">{{ __('Branch employees') }}</div>
                                                @if(count($recentEmployees) > 0)
                                                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                                                        @foreach($recentEmployees as $emp)
                                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div class="fw-semibold">{{ $emp->name }}</div>
                                                                    <div class="text-muted small">{{ $emp->position }}</div>
                                                                </div>
                                                                <form action="{{ route('branches.remove-employee', $branch) }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ addslashes(__('Remove :name from this branch?', ['name' => $emp->name])) }}')"><i class="bi bi-x-lg"></i></button>
                                                                </form>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-muted">{{ __('No employees assigned to this branch yet.') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="card tab-block h-100">
                                    <div class="card-header panel-head py-3 px-4">
                                        <div class="section-title mb-1">{{ __('Finance') }}</div>
                                        <h5 class="mb-0">{{ __('Branch outstanding') }}</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="display-5 fw-bold text-danger mb-2">{{ $currencySymbol }}{{ number_format((float) ($branchOutstandingTotal ?? 0), 2) }}</div>
                                        <p class="text-muted mb-4">{{ __('This figure is calculated only from purchases and payments linked to this branch.') }}</p>
                                        <div class="row g-3">
                                            <div class="col-12 col-md-4">
                                                <div class="p-3 rounded-4 bg-light h-100">
                                                    <div class="text-muted small">{{ __('Suppliers') }}</div>
                                                    <div class="fw-bold fs-4">{{ $branchSuppliers->total() ?? 0 }}</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="p-3 rounded-4 bg-light h-100">
                                                    <div class="text-muted small">{{ __('Payrolls') }}</div>
                                                    <div class="fw-bold fs-4">{{ $branchPayrolls->total() ?? 0 }}</div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <div class="p-3 rounded-4 bg-light h-100">
                                                    <div class="text-muted small">{{ __('Commissions') }}</div>
                                                    <div class="fw-bold fs-4">{{ $branchCommissions->total() ?? 0 }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <button type="button" class="btn btn-outline-danger" data-action="branch-debts" data-url="{{ route('branches.debts', $branch) }}">
                                                <i class="bi bi-journal-text me-2"></i>{{ __('messages.view_branch_debts') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="records-pane" role="tabpanel" aria-labelledby="records-tab">
                        <div class="d-grid gap-4">
                            <div class="card tab-block" data-pagination-section="branch-payrolls" id="branch-payrolls-section">
                                <div class="card-header panel-head py-3 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="section-title mb-1">{{ __('Payroll records') }}</div>
                                        <h5 class="mb-0">{{ __('Latest payrolls') }}</h5>
                                    </div>
                                    <span class="badge bg-light text-dark">{{ $branchPayrolls->total() ?? 0 }}</span>
                                </div>
                                <div class="card-body p-4">
                                    @if(($branchPayrolls->count() ?? 0) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-tight align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('Employee') }}</th>
                                                        <th>{{ __('Period') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                        <th class="text-end">{{ __('Net salary') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($branchPayrolls as $payroll)
                                                        <tr>
                                                            <td class="fw-semibold">{{ $payroll->employee?->name ?? '-' }}</td>
                                                            <td>{{ $payroll->month }}/{{ $payroll->year }}</td>
                                                            <td><span class="badge bg-{{ ($payroll->status ?? 'draft') === 'paid' ? 'success' : 'secondary' }}">{{ strtoupper($payroll->status ?? 'draft') }}</span></td>
                                                            <td class="text-end text-success">{{ $currencySymbol }}{{ number_format((float) ($payroll->net_salary ?? 0), 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">{{ $branchPayrolls->links() }}</div>
                                    @else
                                        <div class="text-muted">{{ __('No payrolls found for this branch yet.') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="card tab-block" data-pagination-section="branch-commissions" id="branch-commissions-section">
                                <div class="card-header panel-head py-3 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="section-title mb-1">{{ __('Commission records') }}</div>
                                        <h5 class="mb-0">{{ __('Latest commissions') }}</h5>
                                    </div>
                                    <span class="badge bg-light text-dark">{{ $branchCommissions->total() ?? 0 }}</span>
                                </div>
                                <div class="card-body p-4">
                                    @if(($branchCommissions->count() ?? 0) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-tight align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('Employee') }}</th>
                                                        <th>{{ __('Date') }}</th>
                                                        <th>{{ __('Status') }}</th>
                                                        <th class="text-end">{{ __('Amount') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($branchCommissions as $commission)
                                                        <tr>
                                                            <td class="fw-semibold">{{ $commission->employee?->name ?? '-' }}</td>
                                                            <td>{{ optional($commission->commission_date)->translatedFormat('M d, Y') ?? '-' }}</td>
                                                            <td><span class="badge bg-{{ ($commission->status ?? 'pending') === 'paid' ? 'success' : 'secondary' }}">{{ strtoupper($commission->status ?? 'pending') }}</span></td>
                                                            <td class="text-end text-success">{{ $currencySymbol }}{{ number_format((float) ($commission->commission_amount ?? 0), 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">{{ $branchCommissions->links() }}</div>
                                    @else
                                        <div class="text-muted">{{ __('No commissions found for this branch yet.') }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="card tab-block" data-pagination-section="branch-suppliers" id="branch-suppliers-section">
                                <div class="card-header panel-head py-3 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="section-title mb-1">{{ __('Supplier ledger') }}</div>
                                        <h5 class="mb-0">{{ __('Branch suppliers') }}</h5>
                                    </div>
                                    <span class="badge bg-light text-dark">{{ $branchSuppliers->total() ?? 0 }}</span>
                                </div>
                                <div class="card-body p-4">
                                    @if(($branchSuppliers->count() ?? 0) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-tight align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>{{ __('Supplier') }}</th>
                                                        <th class="text-end">{{ __('Purchased') }}</th>
                                                        <th class="text-end">{{ __('Paid') }}</th>
                                                        <th class="text-end">{{ __('Outstanding') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($branchSuppliers as $supplier)
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">{{ $supplier->name }}</div>
                                                                <div class="text-muted small">{{ __('Branch opening') }} {{ $currencySymbol }}{{ number_format(((int) ($supplier->branch_id ?? 0) === (int) $branch->id || $supplier->branches()->whereKey($branch->id)->exists()) ? (float) ($supplier->opening_balance ?? 0) : 0, 2) }}</div>
                                                            </td>
                                                            <td class="text-end text-primary">{{ $currencySymbol }}{{ number_format((float) ($supplier->branch_total_purchased ?? 0), 2) }}</td>
                                                            <td class="text-end text-success">{{ $currencySymbol }}{{ number_format((float) ($supplier->branch_total_paid ?? 0), 2) }}</td>
                                                            <td class="text-end text-danger">{{ $currencySymbol }}{{ number_format((float) ($supplier->outstanding_amount ?? 0), 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">{{ $branchSuppliers->links() }}</div>
                                    @else
                                        <div class="text-muted">{{ __('No suppliers found for this branch yet.') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="directory-pane" role="tabpanel" aria-labelledby="directory-tab">
                        <div class="d-grid gap-4">
                            @foreach($directorySections as $section)
                                <div class="card tab-block" data-pagination-section="branch-directory-{{ $section['key'] }}" id="branch-directory-{{ $section['key'] }}-section">
                                    <div class="card-header panel-head py-3 px-4 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="section-title mb-1">{{ __('Branch directory') }}</div>
                                            <h5 class="mb-0"><i class="bi {{ $section['icon'] }} me-2 text-{{ $section['tone'] }}"></i>{{ $section['title'] }}</h5>
                                        </div>
                                        <span class="badge bg-light text-dark">{{ count($section['items']) }}</span>
                                    </div>
                                    <div class="card-body p-4">
                                        @if(count($section['items']))
                                            <div class="table-responsive">
                                                <table class="table table-sm table-tight align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>{{ __('Name') }}</th>
                                                            <th class="text-end">{{ __('Created') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($section['items'] as $item)
                                                            <tr>
                                                                <td>
                                                                    <div class="fw-semibold">{{ $item->name ?? $item->reference ?? $item->invoice_number ?? $item->code ?? ('#' . $item->id) }}</div>
                                                                    <div class="text-muted small">{{ __('Record #:id', ['id' => $item->id]) }}</div>
                                                                </td>
                                                                <td class="text-end text-muted">{{ optional($item->created_at)->translatedFormat('M d, Y') ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="mt-3">{{ $section['items']->links() }}</div>
                                        @else
                                            <div class="text-muted">{{ __($section['empty']) }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('branches.partials.debts-modal')

<script>
document.addEventListener('DOMContentLoaded', function () {
    function normalizeToCurrentOrigin(url) {
        const parsedUrl = new URL(url, window.location.href);
        parsedUrl.protocol = window.location.protocol;
        parsedUrl.host = window.location.host;
        return parsedUrl.toString();
    }

    async function loadPaginatedSection(link, updateHistory = true) {
        const section = link.closest('[data-pagination-section]');
        if (!section) {
            return;
        }

        const sectionKey = section.getAttribute('data-pagination-section');
        const sectionId = section.id;
        const normalizedUrl = normalizeToCurrentOrigin(link.href);
        section.classList.add('opacity-50');

        try {
            const response = await fetch(normalizedUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to load records page.');
            }

            const html = await response.text();
            const parsedDocument = new DOMParser().parseFromString(html, 'text/html');
            const nextSection = parsedDocument.querySelector('[data-pagination-section="' + sectionKey + '"]');

            if (!nextSection) {
                throw new Error('Paginated section not found in response.');
            }

            section.outerHTML = nextSection.outerHTML;

            if (updateHistory) {
                window.history.pushState({ paginatedUrl: normalizedUrl, sectionId: sectionId, sectionKey: sectionKey }, '', normalizedUrl);
            }
        } catch (error) {
            console.error(error);
        } finally {
            const liveSection = document.querySelector('[data-pagination-section="' + sectionKey + '"]');
            if (liveSection) {
                liveSection.classList.remove('opacity-50');
            }
        }
    }

    document.addEventListener('click', function (event) {
        const link = event.target.closest('[data-pagination-section] .pagination a');
        if (!link) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        loadPaginatedSection(link, true);
    }, true);

    const branchMonths = @json($branchChartMonths ?? []);
    const branchSalesTrendData = @json($branchSalesTrendData ?? []);
    const branchIncomeData = @json($branchIncomeData ?? []);
    const branchOutcomeData = @json($branchOutcomeData ?? []);

    const branchSalesTrendCtx = document.getElementById('branchSalesTrendChart')?.getContext('2d');
    const branchCashFlowCtx = document.getElementById('branchCashFlowChart')?.getContext('2d');

    if (branchSalesTrendCtx) {
        new Chart(branchSalesTrendCtx, {
            type: 'line',
            data: {
                labels: branchMonths,
                datasets: [{
                    label: '{{ __('Sales') }}',
                    data: branchSalesTrendData,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.16)',
                    fill: true,
                    tension: 0.32,
                    pointRadius: 4,
                    pointBackgroundColor: '#0d6efd',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    if (branchCashFlowCtx) {
        new Chart(branchCashFlowCtx, {
            type: 'bar',
            data: {
                labels: branchMonths,
                datasets: [
                    {
                        label: '{{ __('Income') }}',
                        data: branchIncomeData,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderRadius: 10,
                    },
                    {
                        label: '{{ __('Outcome') }}',
                        data: branchOutcomeData,
                        backgroundColor: 'rgba(220, 38, 38, 0.85)',
                        borderRadius: 10,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    window.addEventListener('popstate', function (event) {
        if (event.state && event.state.paginatedUrl) {
            const activeLink = document.querySelector('[data-pagination-section="' + event.state.sectionKey + '"] .pagination a[href="' + event.state.paginatedUrl + '"]');
            if (activeLink) {
                loadPaginatedSection(activeLink, false);
            } else {
                fetch(normalizeToCurrentOrigin(event.state.paginatedUrl), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                    },
                    credentials: 'same-origin',
                })
                .then(function (response) { return response.text(); })
                .then(function (html) {
                    const parsedDocument = new DOMParser().parseFromString(html, 'text/html');
                    const nextSection = parsedDocument.querySelector('[data-pagination-section="' + event.state.sectionKey + '"]');
                    const currentSection = document.querySelector('[data-pagination-section="' + event.state.sectionKey + '"]');
                    if (nextSection && currentSection) {
                        currentSection.outerHTML = nextSection.outerHTML;
                    }
                })
                .catch(function (error) {
                    console.error(error);
                });
            }
        }
    });
});
</script>

@endsection