@extends('layouts.modern')

@section('title', $employee->name)

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-person-circle"></i> {{ $employee->name }}</h1>
        <p class="text-muted">{{ $employee->position }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
        </a>
    </div>
</div>

<!-- Employee Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <i class="bi bi-percent"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.total_commissions') }}</h6>
                <h3>{{ $currencySymbol }}{{ number_format($totalCommissions, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.total_sales') }}</h6>
                <h3>{{ $currencySymbol }}{{ number_format($totalSales, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c, #ec7063);">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.status') }}</h6>
                <h3>{{ $employee->is_active ? 'Active' : 'Inactive' }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Employee Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="mb-0"><i class="bi bi-person-vcard"></i> {{ __('messages.personal_information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.name') }}</label>
                        <p class="text-muted">{{ $employee->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.position') }}</label>
                        <p class="text-muted">{{ $employee->position }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.salary') }}</label>
                        <p class="text-success fw-bold">{{ $currencySymbol }}{{ number_format($employee->base_salary, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.status') }}</label>
                        <p>
                            @if($employee->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ __('messages.active') }}</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> {{ __('messages.inactive') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.created') }}</label>
                        <p class="text-muted">{{ $employee->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.last_updated') }}</label>
                        <p class="text-muted">{{ $employee->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales History -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> {{ __('messages.recent_sales') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                <td>{{ $currencySymbol }}{{ number_format($sale->total_amount, 2) }}</td>
                                <td><span class="badge bg-info">{{ __('messages.completed') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">{{ __('messages.no_sales_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Commissions History -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #3498db, #5dade2); color: white;">
                <h5 class="mb-0"><i class="bi bi-percent"></i> {{ __('messages.commissions') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.payment_status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCommissions as $commission)
                            <tr>
                                <td>{{ $commission->created_at->format('M d, Y') }}</td>
                                <td><strong>{{ $currencySymbol }}{{ number_format($commission->commission_earned, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $commission->status === 'approved' ? 'success' : ($commission->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ __('messages.' . $commission->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $commission->paid_at ? 'success' : 'warning' }}">
                                        {{ $commission->paid_at ? __('messages.paid') : __('messages.pending') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">{{ __('messages.no_commissions_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <!-- Salary Summary -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-cash-coin"></i> {{ __('messages.compensation') }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('messages.base_salary') }}</label>
                    <h4 class="text-success">{{ $currencySymbol }}{{ number_format($employee->salary, 2) }}</h4>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('messages.total_commissions') }}</label>
                    <h4 class="text-primary">{{ $currencySymbol }}{{ number_format($totalCommissions, 2) }}</h4>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('messages.commission_rate') }}</label>
                    <h4 class="text-info">{{ $employee->commission_rate ?? '0' }}%</h4>
                </div>

                <div class="alert alert-info" role="alert">
                    <small><strong>{{ __('messages.estimated_commission') }}:</strong> {{ $currencySymbol }}{{ number_format($totalSales * ($employee->commission_rate ?? 0) / 100, 2) }}</small>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #e74c3c, #ec7063); color: white;">
                <h5 class="mb-0"><i class="bi bi-graph-up-arrow"></i> {{ __('messages.statistics') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>{{ __('messages.total_sales') }}</strong></td>
                        <td class="text-end">{{ $currencySymbol }}{{ number_format($totalSales, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.pending_commissions') }}</strong></td>
                        <td class="text-end">{{ $pendingCommissions }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.paid_commissions') }}</strong></td>
                        <td class="text-end">{{ $paidCommissions }}</td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>{{ __('messages.tenure_days') }}</strong></td>
                        <td class="text-end">{{ $employee->hire_date->diffInDays(now()) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> {{ __('messages.actions') }}</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning w-100 mb-2">
                    <i class="bi bi-pencil"></i> {{ __('messages.edit_employee') }}
                </a>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-list"></i> {{ __('messages.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
