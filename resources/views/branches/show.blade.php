@extends('layouts.modern')

@section('title', $branch->name)

@section('content')
@php
    $statCards = [
        ['label' => 'Employees', 'count' => $branch->employees_count ?? 0, 'icon' => 'bi-people', 'color' => 'primary'],
        ['label' => 'Products', 'count' => $branch->products_count ?? 0, 'icon' => 'bi-box', 'color' => 'success'],
        ['label' => 'Categories', 'count' => $branch->categories_count ?? 0, 'icon' => 'bi-tags', 'color' => 'warning'],
        ['label' => 'Customers', 'count' => $branch->customers_count ?? 0, 'icon' => 'bi-person-badge', 'color' => 'info'],
        ['label' => 'Suppliers', 'count' => $branch->suppliers_count ?? 0, 'icon' => 'bi-truck', 'color' => 'secondary'],
        ['label' => 'Invoices', 'count' => $branch->invoices_count ?? 0, 'icon' => 'bi-receipt', 'color' => 'danger'],
        ['label' => 'Storages', 'count' => $branch->storages_count ?? 0, 'icon' => 'bi-database', 'color' => 'dark'],
        ['label' => 'Safes', 'count' => $branch->safes_count ?? 0, 'icon' => 'bi-safe', 'color' => 'success'],
        ['label' => 'Payrolls', 'count' => count($branchPayrolls ?? []), 'icon' => 'bi-wallet2', 'color' => 'danger'],
        ['label' => 'Commissions', 'count' => count($branchCommissions ?? []), 'icon' => 'bi-graph-up', 'color' => 'primary'],
    ];

    $quickActions = [
        ['label' => 'Employee', 'route' => 'employees.create', 'icon' => 'bi-people', 'class' => 'btn-primary'],
        ['label' => 'Product', 'route' => 'products.create', 'icon' => 'bi-box', 'class' => 'btn-success'],
        ['label' => 'Category', 'route' => 'categories.create', 'icon' => 'bi-tags', 'class' => 'btn-warning'],
        ['label' => 'Supplier', 'route' => 'suppliers.create', 'icon' => 'bi-truck', 'class' => 'btn-secondary'],
        ['label' => 'Invoice', 'route' => 'invoices.create', 'icon' => 'bi-receipt', 'class' => 'btn-danger'],
        ['label' => 'Storage', 'route' => 'storages.create', 'icon' => 'bi-database', 'class' => 'btn-dark'],
        ['label' => 'Safe', 'route' => 'safes.create', 'icon' => 'bi-safe', 'class' => 'btn-success'],
        ['label' => 'Commission', 'route' => 'commissions.create', 'icon' => 'bi-graph-up', 'class' => 'btn-primary'],
        ['label' => 'Payroll', 'route' => 'payroll.create', 'icon' => 'bi-wallet2', 'class' => 'btn-danger'],
    ];

    $coreSections = [
        ['title' => 'Employees', 'items' => $recentEmployees ?? [], 'icon' => 'bi-people', 'tone' => 'primary'],
        ['title' => 'Products', 'items' => $recentProducts ?? [], 'icon' => 'bi-box', 'tone' => 'success'],
        ['title' => 'Categories', 'items' => $recentCategories ?? [], 'icon' => 'bi-tags', 'tone' => 'warning'],
        ['title' => 'Customers', 'items' => $recentCustomers ?? [], 'icon' => 'bi-person-badge', 'tone' => 'info'],
        ['title' => 'Invoices', 'items' => $recentInvoices ?? [], 'icon' => 'bi-receipt', 'tone' => 'danger'],
        ['title' => 'Storages', 'items' => $recentStorages ?? [], 'icon' => 'bi-database', 'tone' => 'dark'],
        ['title' => 'Safes', 'items' => $recentSafes ?? [], 'icon' => 'bi-safe', 'tone' => 'success'],
    ];

    $financeMetrics = [
        ['label' => 'Branch outstanding', 'value' => $branchOutstandingTotal ?? 0, 'icon' => 'bi-cash-stack', 'tone' => 'danger', 'format' => 'currency'],
        ['label' => 'Payrolls', 'value' => count($branchPayrolls ?? []), 'icon' => 'bi-wallet2', 'tone' => 'warning', 'format' => 'count'],
        ['label' => 'Commissions', 'value' => count($branchCommissions ?? []), 'icon' => 'bi-graph-up', 'tone' => 'primary', 'format' => 'count'],
        ['label' => 'Suppliers', 'value' => count($branchSuppliers ?? []), 'icon' => 'bi-truck', 'tone' => 'secondary', 'format' => 'count'],
    ];
@endphp

<style>
    .branch-shell {
        padding: 24px 0 40px;
    }

    .branch-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1f2937 45%, #0f766e 100%);
        color: #fff;
        border-radius: 28px;
        padding: 28px;
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.18);
    }

    .branch-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .branch-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .branch-card-header {
        background: linear-gradient(180deg, #ffffff 0%, #fbfbfc 100%);
        border-bottom: 1px solid #eef1f5;
    }

    .branch-section-title {
        font-size: 14px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #334155;
    }

    .metric-card {
        border: 0;
        border-radius: 22px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
        height: 100%;
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .soft-surface {
        background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    }

    .table-tight th,
    .table-tight td {
        vertical-align: middle;
    }

    .timeline-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 700;
    }

    @media (max-width: 992px) {
        .branch-hero {
            padding: 22px;
        }
    }
</style>

<div class="branch-shell">
    <div class="container-fluid">
        <div class="branch-hero mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-4">
                <div class="flex-grow-1">
                    <div class="branch-hero-badge mb-3">
                        <i class="bi bi-diagram-3"></i>
                        Branch dashboard
                    </div>
                    <h1 class="display-6 fw-bold mb-2">{{ $branch->name }}</h1>
                    <div class="d-flex flex-wrap gap-3 text-white-75 mb-4">
                        <span><i class="bi bi-hash me-1"></i>{{ $branch->code }}</span>
                        <span><i class="bi bi-geo-alt me-1"></i>{{ $branch->city ?: __('messages.not_available') }}</span>
                        <span><i class="bi bi-telephone me-1"></i>{{ $branch->phone ?: __('messages.not_available') }}</span>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="timeline-pill">
                            <i class="bi bi-people"></i> {{ $branch->employees_count ?? 0 }} employees
                        </span>
                        <span class="timeline-pill">
                            <i class="bi bi-truck"></i> {{ $branch->suppliers_count ?? 0 }} suppliers
                        </span>
                        <span class="timeline-pill">
                            <i class="bi bi-wallet2"></i> {{ count($branchPayrolls ?? []) }} payrolls
                        </span>
                    </div>
                </div>

                <div class="text-end">
                    <span class="badge bg-{{ $branch->is_active ? 'success' : 'secondary' }} fs-6 px-3 py-2 mb-3">
                        {{ $branch->is_active ? __('messages.active') : __('messages.inactive') }}
                    </span>
                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        <a href="{{ route('branches.edit', $branch) }}" class="btn btn-light">
                            <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
                        </a>
                        <a href="{{ route('branches.index') }}" class="btn btn-outline-light">
                            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            @foreach($statCards as $card)
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="card metric-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="metric-icon bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }}">
                                    <i class="bi {{ $card['icon'] }}"></i>
                                </div>
                                <div class="fs-4 fw-bold">{{ $card['count'] }}</div>
                            </div>
                            <div class="text-muted small fw-semibold">{{ $card['label'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card branch-card h-100">
                    <div class="card-header branch-card-header py-3 px-4">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <div class="branch-section-title mb-1">Branch overview</div>
                                <h4 class="mb-0">Fast actions and live status</h4>
                            </div>
                            <div class="text-muted small text-end">
                                Organized for quick branch navigation and updates.
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 soft-surface">
                        <div class="row g-3 mb-4">
                            @foreach($financeMetrics as $metric)
                                <div class="col-md-6 col-xl-3">
                                    <div class="card metric-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <div class="metric-icon bg-{{ $metric['tone'] }} bg-opacity-10 text-{{ $metric['tone'] }}">
                                                    <i class="bi {{ $metric['icon'] }}"></i>
                                                </div>
                                                <div class="text-muted small fw-semibold">{{ $metric['label'] }}</div>
                                            </div>
                                            @if($metric['format'] === 'currency')
                                                <div class="fs-4 fw-bold text-{{ $metric['tone'] }}">{{ $currencySymbol }}{{ number_format((float) $metric['value'], 2) }}</div>
                                            @else
                                                <div class="fs-4 fw-bold text-{{ $metric['tone'] }}">{{ $metric['value'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <div class="branch-section-title mb-3">Quick actions</div>
                            <div class="row g-2">
                                @foreach($quickActions as $action)
                                    <div class="col-6 col-md-4 col-xl-3">
                                        <a class="btn {{ $action['class'] }} w-100 py-3" href="{{ route($action['route'], ['branch_ids' => [$branch->id]]) }}">
                                            <i class="bi {{ $action['icon'] }} me-1"></i> New {{ $action['label'] }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card branch-card h-100">
                    <div class="card-header branch-card-header py-3 px-4">
                        <div class="branch-section-title mb-1">Branch outstanding</div>
                        <h4 class="mb-0">Supplier balance summary</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="display-5 fw-bold text-danger mb-2">{{ $currencySymbol }}{{ number_format((float) ($branchOutstandingTotal ?? 0), 2) }}</div>
                        <p class="text-muted mb-4">Calculated from the branch-only supplier purchases and payments.</p>

                        <div class="alert alert-light border mb-0">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-info-circle text-primary"></i>
                                <strong>What this includes</strong>
                            </div>
                            <div class="small text-muted">Only transactions linked to this branch are counted. Multi-branch suppliers are split by branch ledger, not by supplier-wide totals.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(count($recentEmployees) > 0 || count($unassignedEmployees) > 0)
            <div class="card branch-card mb-4">
                <div class="card-header branch-card-header py-3 px-4">
                    <div class="branch-section-title mb-1">Branch staffing</div>
                    <h4 class="mb-0">Manage employees</h4>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        @if(count($unassignedEmployees) > 0)
                            <div class="col-xl-6">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-header bg-info text-white">
                                        <div class="fw-semibold"><i class="bi bi-person-plus me-2"></i>Available Employees</div>
                                        <small>Employees without any branch assignment</small>
                                    </div>
                                    <div class="card-body">
                                        @forelse($unassignedEmployees as $emp)
                                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                <div>
                                                    <strong>{{ $emp->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $emp->position }}</small>
                                                </div>
                                                <form action="{{ route('branches.assign-employee', $branch) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                    <button type="submit" class="btn btn-sm btn-success" title="Assign to this branch">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @empty
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-xl-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-header bg-success text-white">
                                    <div class="fw-semibold"><i class="bi bi-person-check me-2"></i>Branch Employees</div>
                                    <small>Employees assigned to this branch ({{ count($recentEmployees) }})</small>
                                </div>
                                <div class="card-body">
                                    @forelse($recentEmployees as $emp)
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <div>
                                                <strong>{{ $emp->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $emp->position }}</small>
                                            </div>
                                            <form action="{{ route('branches.remove-employee', $branch) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Remove from this branch" onclick="return confirm('Remove {{ $emp->name }} from this branch?')">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="text-muted mb-0">No employees assigned to this branch yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card branch-card mb-4">
            <div class="card-header branch-card-header py-3 px-4">
                <div class="branch-section-title mb-1">Financial records</div>
                <h4 class="mb-0">Payrolls, commissions, and suppliers</h4>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0"><i class="bi bi-wallet2 me-2 text-danger"></i>Payrolls</h5>
                                    <small class="text-muted">Payrolls for employees assigned to this branch</small>
                                </div>
                                <span class="badge bg-light text-dark">{{ count($branchPayrolls ?? []) }}</span>
                            </div>
                            @if(count($branchPayrolls ?? []))
                                <div class="table-responsive">
                                    <table class="table table-sm table-tight align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Employee</th>
                                                <th>Period</th>
                                                <th class="text-end">Net</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($branchPayrolls as $payroll)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $payroll->employee?->name ?? '-' }}</div>
                                                        <small class="text-muted">{{ strtoupper($payroll->status ?? 'draft') }}</small>
                                                    </td>
                                                    <td>{{ $payroll->month }}/{{ $payroll->year }}</td>
                                                    <td class="text-end text-success">{{ $currencySymbol }}{{ number_format((float) ($payroll->net_salary ?? 0), 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">{{ $branchPayrolls->links() }}</div>
                            @else
                                <div class="text-muted">No payrolls found for this branch yet.</div>
                            @endif
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Commissions</h5>
                                    <small class="text-muted">Commission records attached to this branch</small>
                                </div>
                                <span class="badge bg-light text-dark">{{ count($branchCommissions ?? []) }}</span>
                            </div>
                            @if(count($branchCommissions ?? []))
                                <div class="table-responsive">
                                    <table class="table table-sm table-tight align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Employee</th>
                                                <th>Date</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($branchCommissions as $commission)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $commission->employee?->name ?? '-' }}</div>
                                                        <small class="text-muted">{{ strtoupper($commission->status ?? 'pending') }}</small>
                                                    </td>
                                                    <td>{{ optional($commission->commission_date)->format('M d, Y') ?? '-' }}</td>
                                                    <td class="text-end text-success">{{ $currencySymbol }}{{ number_format((float) ($commission->commission_amount ?? 0), 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">{{ $branchCommissions->links() }}</div>
                            @else
                                <div class="text-muted">No commissions found for this branch yet.</div>
                            @endif
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="mb-0"><i class="bi bi-truck me-2 text-secondary"></i>Suppliers</h5>
                                    <small class="text-muted">Branch suppliers with branch-only outstanding balances</small>
                                </div>
                                <span class="badge bg-light text-dark">{{ count($branchSuppliers ?? []) }}</span>
                            </div>
                            @if(count($branchSuppliers ?? []))
                                <div class="table-responsive">
                                    <table class="table table-sm table-tight align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Supplier</th>
                                                <th class="text-end">Purchased</th>
                                                <th class="text-end">Paid</th>
                                                <th class="text-end">Outstanding</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($branchSuppliers as $supplier)
                                                <tr>
                                                    <td>
                                                        <div class="fw-semibold">{{ $supplier->name }}</div>
                                                        <small class="text-muted">Opening {{ $currencySymbol }}{{ number_format(((int) ($supplier->branch_id ?? 0) === (int) $branch->id || $supplier->branches()->whereKey($branch->id)->exists()) ? (float) ($supplier->opening_balance ?? 0) : 0, 2) }}</small>
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
                                <div class="text-muted">No suppliers found for this branch yet.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card branch-card">
            <div class="card-header branch-card-header py-3 px-4">
                <div class="branch-section-title mb-1">Branch directory</div>
                <h4 class="mb-0">Recent branch-linked records</h4>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-4">
                    @foreach($coreSections as $section)
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h5 class="mb-0"><i class="bi {{ $section['icon'] }} me-2 text-{{ $section['tone'] }}"></i>{{ $section['title'] }}</h5>
                                        <small class="text-muted">Latest records attached to this branch</small>
                                    </div>
                                    <span class="badge bg-light text-dark">{{ count($section['items']) }}</span>
                                </div>

                                @if(count($section['items']))
                                    <div class="table-responsive">
                                        <table class="table table-sm table-tight align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Name</th>
                                                    <th class="text-end">Created</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($section['items'] as $item)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold">{{ $item->name ?? $item->reference ?? $item->invoice_number ?? $item->code ?? ('#' . $item->id) }}</div>
                                                            <small class="text-muted">#{{ $item->id }}</small>
                                                        </td>
                                                        <td class="text-end text-muted">{{ optional($item->created_at)->format('M d, Y') ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">{{ $section['items']->links() }}</div>
                                @else
                                    <div class="text-muted">No records found for this branch yet.</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection