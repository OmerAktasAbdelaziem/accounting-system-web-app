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
                <h6>{{ __('messages.advances') }}</h6>
                <h3>{{ $currencySymbol }}{{ number_format($employee->advances()->sum('amount'), 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Employee Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header employee-detail-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
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
                <style>
                    .employee-detail-header {
                        color: #fff !important;
                    }

                    .employee-detail-header * {
                        color: #fff !important;
                    }

                    .card-header {
                        background: linear-gradient(135deg, #1a1a1a, #333) !important;
                        color: #fff !important;
                    }

                    @media (max-width: 768px) {
                        .card-header {
                            background: linear-gradient(135deg, #ff8c00, #ffb347) !important;
                        }
                    }
                </style>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.salary') }}</label>
                        <p class="text-success fw-bold">{{ $currencySymbol }}{{ number_format($employee->base_salary, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.branch') }}</label>
                        <div>
                            @forelse($employee->branches as $branch)
                                <span class="badge bg-info text-dark me-1 mb-1">{{ $branch->name }}</span>
                            @empty
                                <p class="text-muted mb-0">No branch assigned</p>
                            @endforelse
                        </div>
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
            <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> {{ __('messages.recent_sales') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.commission_rate') }}</th>
                            <th>{{ __('messages.commission_amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                            <tr>
                                <td>{{ $sale->commission_date->format('M d, Y') }}</td>
                                <td>{{ $currencySymbol }}{{ number_format($sale->sale_amount, 2) }}</td>
                                <td>{{ $sale->commission_rate }}%</td>
                                <td><strong>{{ $currencySymbol }}{{ number_format($sale->commission_amount, 2) }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">{{ __('messages.no_sales_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Commissions History -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-percent"></i> {{ __('messages.commissions') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.sales_amount') }}</th>
                            <th>{{ __('messages.rate') }}</th>
                            <th>{{ __('messages.commission_amount') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCommissions as $commission)
                            <tr>
                                <td>{{ $commission->commission_date->format('M d, Y') }}</td>
                                <td>{{ $currencySymbol }}{{ number_format($commission->sale_amount, 2) }}</td>
                                <td>{{ $commission->commission_rate }}%</td>
                                <td><strong>{{ $currencySymbol }}{{ number_format($commission->commission_amount, 2) }}</strong></td>
                                <td>
                                    <a href="{{ route('commissions.show', $commission->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">{{ __('messages.no_commissions_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Employee Advances -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cash-coin"></i> {{ __('messages.advances') }}</h5>
                    <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addAdvanceModal">
                        <i class="bi bi-plus"></i> {{ __('messages.add') }}
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employee->advances()->orderByDesc('advance_date')->get() as $advance)
                            <tr>
                                <td>{{ $advance->advance_date->format('M d, Y') }}</td>
                                <td><strong>{{ $currencySymbol }}{{ number_format($advance->amount, 2) }}</strong></td>
                                <td>{{ $advance->description ?? '-' }}</td>
                                <td>
                                    <form action="{{ route('advances.destroy', $advance->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this advance?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No advances recorded</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($employee->advances()->sum('amount') > 0)
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.total_advances') }}</label>
                        <h4 class="text-danger">{{ $currencySymbol }}{{ number_format($employee->advances()->sum('amount'), 2) }}</h4>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.total_commissions') }}</label>
                        <h4 class="text-success">{{ $currencySymbol }}{{ number_format($totalCommissions, 2) }}</h4>
                    </div>
                </div>
            </div>
            @endif
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
                    <h4 class="text-success">{{ $currencySymbol }}{{ number_format($employee->base_salary, 2) }}</h4>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('messages.total_commissions') }}</label>
                    <h4 class="text-primary">{{ $currencySymbol }}{{ number_format($totalCommissions, 2) }}</h4>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('messages.pending_advances') }}</label>
                    <h4 class="text-warning">{{ $currencySymbol }}{{ number_format($employee->advances()->sum('amount'), 2) }}</h4>
                </div>

                <div class="alert alert-info" role="alert">
                    <small><strong>{{ __('messages.average_commission_rate') }}:</strong> 
                    @if(count($recentCommissions) > 0)
                        {{ number_format($recentCommissions->avg('commission_rate'), 2) }}%
                    @else
                        0%
                    @endif
                    </small>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-graph-up-arrow"></i> {{ __('messages.statistics') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>{{ __('messages.total_sales') }}</strong></td>
                        <td class="text-end">{{ $currencySymbol }}{{ number_format($totalSales, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.commission_count') }}</strong></td>
                        <td class="text-end">{{ count($recentCommissions) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.average_commission') }}</strong></td>
                        <td class="text-end">
                            @if(count($recentCommissions) > 0)
                                {{ $currencySymbol }}{{ number_format($recentCommissions->avg('commission_amount'), 2) }}
                            @else
                                {{ $currencySymbol }}0.00
                            @endif
                        </td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>{{ __('messages.tenure_days') }}</strong></td>
                        <td class="text-end">{{ $employee->hire_date ? $employee->hire_date->diffInDays(now()) : "N/A" }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
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

<!-- Add Advance Modal -->
<div class="modal fade" id="addAdvanceModal" tabindex="-1" aria-labelledby="addAdvanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAdvanceModalLabel">Add Employee Advance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('advances.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="advance_amount" class="form-label fw-bold">Amount</label>
                        <input type="text" class="form-control" id="advance_amount" name="amount" inputmode="decimal" required>
                        <small class="text-muted">Enter the advance amount</small>
                    </div>

                    <div class="mb-3">
                        <label for="advance_date" class="form-label fw-bold">Advance Date</label>
                        <input type="date" class="form-control" id="advance_date" name="advance_date" value="{{ now()->toDateString() }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="advance_description" class="form-label fw-bold">Description (Optional)</label>
                        <textarea class="form-control" id="advance_description" name="description" rows="2" placeholder="e.g., Emergency advance, Medical expenses"></textarea>
                    </div>

                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Record Advance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
