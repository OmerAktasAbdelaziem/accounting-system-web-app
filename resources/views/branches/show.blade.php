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
        ['label' => 'Commissions', 'count' => $branch->commissions_count ?? 0, 'icon' => 'bi-graph-up', 'color' => 'primary'],
        ['label' => 'Payrolls', 'count' => count($branchPayrolls ?? []), 'icon' => 'bi-wallet2', 'color' => 'danger'],
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

    $sections = [
        ['title' => 'Employees', 'items' => $recentEmployees ?? [], 'icon' => 'bi-people'],
        ['title' => 'Products', 'items' => $recentProducts ?? [], 'icon' => 'bi-box'],
        ['title' => 'Categories', 'items' => $recentCategories ?? [], 'icon' => 'bi-tags'],
        ['title' => 'Customers', 'items' => $recentCustomers ?? [], 'icon' => 'bi-person-badge'],
        ['title' => 'Suppliers', 'items' => $recentSuppliers ?? [], 'icon' => 'bi-truck'],
        ['title' => 'Invoices', 'items' => $recentInvoices ?? [], 'icon' => 'bi-receipt'],
        ['title' => 'Storages', 'items' => $recentStorages ?? [], 'icon' => 'bi-database'],
        ['title' => 'Safes', 'items' => $recentSafes ?? [], 'icon' => 'bi-safe'],
        ['title' => 'Commissions', 'items' => $recentCommissions ?? [], 'icon' => 'bi-graph-up'],
    ];
@endphp

<div class="container-fluid py-3">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-lg-5" style="background: linear-gradient(135deg, #101827 0%, #1f2937 55%, #0f766e 100%); color: #fff; border-radius: 1rem;">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <div class="text-uppercase small opacity-75 mb-2">Branch dashboard</div>
                    <h1 class="display-6 fw-bold mb-2">{{ $branch->name }}</h1>
                    <div class="d-flex flex-wrap gap-3 text-white-50">
                        <span><i class="bi bi-hash"></i> {{ $branch->code }}</span>
                        <span><i class="bi bi-geo-alt"></i> {{ $branch->city ?: __('messages.not_available') }}</span>
                        <span><i class="bi bi-telephone"></i> {{ $branch->phone ?: __('messages.not_available') }}</span>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $branch->is_active ? 'success' : 'secondary' }} fs-6 px-3 py-2">
                        {{ $branch->is_active ? __('messages.active') : __('messages.inactive') }}
                    </span>
                    <div class="mt-3">
                        <a href="{{ route('branches.edit', $branch) }}" class="btn btn-light me-2">
                            <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
                        </a>
                        <a href="{{ route('branches.index') }}" class="btn btn-outline-light">
                            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach($statCards as $card)
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="rounded-circle bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }} d-inline-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                <i class="bi {{ $card['icon'] }}"></i>
                            </div>
                            <div class="fs-4 fw-bold">{{ $card['count'] }}</div>
                        </div>
                        <div class="text-muted small">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">Branch outstanding</div>
                    <div class="display-6 fw-bold text-danger">{{ $currencySymbol }}{{ number_format($branchOutstandingTotal ?? 0, 2) }}</div>
                    <div class="text-muted mt-2">Outstanding supplier balance for this branch only.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h4 class="mb-1">Quick actions</h4>
                    <p class="text-muted mb-0">Create new records already scoped to this branch.</p>
                </div>
            </div>
            <div class="row g-2">
                @foreach($quickActions as $action)
                    <div class="col-6 col-md-3 col-xl-2">
                        <a class="btn {{ $action['class'] }} w-100" href="{{ route($action['route'], ['branch_ids' => [$branch->id]]) }}">
                            <i class="bi {{ $action['icon'] }} me-1"></i> New {{ $action['label'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Assign Employees Section -->
    @if(count($recentEmployees) > 0 || count($unassignedEmployees) > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1"><i class="bi bi-people me-2"></i>Manage Branch Employees</h4>
                    <p class="text-muted mb-0">Transfer employees between branches or assign unassigned employees.</p>
                </div>
            </div>

            <div class="row">
                <!-- Unassigned Employees -->
                @if(count($unassignedEmployees) > 0)
                <div class="col-lg-6">
                    <div class="card bg-light border">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-person-plus me-2"></i>Available Employees</h6>
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

                <!-- Current Branch Employees -->
                <div class="col-lg-6">
                    <div class="card bg-light border">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-person-check me-2"></i>Branch Employees</h6>
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

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="bi bi-wallet2 me-2 text-danger"></i>Payrolls</h5>
                        <small class="text-muted">Payrolls for employees assigned to this branch</small>
                    </div>
                    <span class="badge bg-light text-dark">{{ count($branchPayrolls ?? []) }}</span>
                </div>
                <div class="card-body">
                    @if(count($branchPayrolls ?? []))
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
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
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2 text-primary"></i>Commissions</h5>
                        <small class="text-muted">Commission records attached to this branch</small>
                    </div>
                    <span class="badge bg-light text-dark">{{ count($branchCommissions ?? []) }}</span>
                </div>
                <div class="card-body">
                    @if(count($branchCommissions ?? []))
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
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
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><i class="bi bi-truck me-2 text-secondary"></i>Suppliers</h5>
                        <small class="text-muted">Branch suppliers with outstanding balances</small>
                    </div>
                    <span class="badge bg-light text-dark">{{ count($branchSuppliers ?? []) }}</span>
                </div>
                <div class="card-body">
                    @if(count($branchSuppliers ?? []))
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Supplier</th>
                                        <th class="text-end">Outstanding</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($branchSuppliers as $supplier)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $supplier->name }}</div>
                                                <small class="text-muted">Opening {{ $currencySymbol }}{{ number_format((float) ($supplier->opening_balance ?? 0), 2) }}</small>
                                            </td>
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

    <div class="row g-4">
        @foreach($sections as $section)
            <div class="col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="bi {{ $section['icon'] }} me-2 text-primary"></i>{{ $section['title'] }}</h5>
                            <small class="text-muted">Latest records attached to this branch</small>
                        </div>
                        <span class="badge bg-light text-dark">{{ count($section['items']) }}</span>
                    </div>
                    <div class="card-body">
                        @if(count($section['items']))
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
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
                                                <td class="text-end text-muted">
                                                    {{ optional($item->created_at)->format('M d, Y') ?? '-' }}
                                                </td>
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
            </div>
        @endforeach
    </div>
</div>
@endsection
