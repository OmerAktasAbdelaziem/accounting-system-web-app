@extends('layouts.modern')

@section('title', __('messages.commission_details'))

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-percent"></i> {{ __('messages.commission') }} #{{ $commission->id }}</h1>
        <p class="text-muted">{{ $commission->employee->name ?? __('messages.employee') }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
        <a href="{{ route('commissions.edit', $commission->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
        </a>
    </div>
</div>

<!-- Commission Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ff8c00, #ffb347);">
                <i class="bi bi-percent"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.commission_amount') }}</h6>
                <h3>{{ $currencySymbol }}{{ number_format($commission->commission_amount, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <i class="bi bi-person"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.employee') }}</h6>
                <h3 style="font-size: 18px;">{{ $commission->employee->name ?? __('messages.not_available') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                <i class="bi bi-calendar"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.date') }}</h6>
                <h3 style="font-size: 16px;">{{ $commission->commission_date->format('M d, Y') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c, #ec7063);">
                <i class="bi bi-dollar"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.sales_amount') }}</h6>
                <h3 style="font-size: 16px;">{{ $currencySymbol }}{{ number_format($commission->sale_amount, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Commission Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('messages.commission_details') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.employee_name') }}</label>
                        <p class="text-muted">
                            <a href="{{ route('employees.show', $commission->employee_id) }}">{{ $commission->employee->name }}</a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.commission_amount') }}</label>
                        <p class="text-success fw-bold">{{ $currencySymbol }}{{ number_format($commission->commission_amount, 2) }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.commission_date') }}</label>
                        <p class="text-muted">{{ $commission->commission_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.sales_amount') }}</label>
                        <p class="text-muted">{{ $currencySymbol }}{{ number_format($commission->sale_amount, 2) }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.commission_rate') }}</label>
                        <p class="text-muted">{{ $commission->commission_rate ?? '0' }}%</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.reference_type') }}</label>
                        <p class="text-muted">{{ $commission->reference_type ?? __('messages.none') }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">{{ __('messages.notes') }}</label>
                        <p class="text-muted">{{ $commission->notes ?? __('messages.no_notes') }}</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.created') }}</label>
                        <p class="text-muted">{{ $commission->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.last_updated') }}</label>
                        <p class="text-muted">{{ $commission->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Info -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="mb-0"><i class="bi bi-speedometer2"></i> {{ __('messages.quick_info') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>{{ __('messages.commission_id') }}</strong></td>
                        <td class="text-end">#{{ $commission->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.amount') }}</strong></td>
                        <td class="text-end text-success fw-bold">{{ $currencySymbol }}{{ number_format($commission->commission_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.date') }}</strong></td>
                        <td class="text-end">{{ $commission->commission_date->format('M d, Y') }}</td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>{{ __('messages.sales') }}</strong></td>
                        <td class="text-end">{{ $currencySymbol }}{{ number_format($commission->sale_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Employee Info -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #3498db, #5dade2); color: white;">
                <h5 class="mb-0"><i class="bi bi-person"></i> {{ __('messages.employee_information') }}</h5>
            </div>
            <div class="card-body">
                <p><strong>{{ $commission->employee->name }}</strong></p>
                <p class="text-muted small">{{ $commission->employee->position ?? 'N/A' }}</p>
                <hr>
                <a href="{{ route('employees.show', $commission->employee_id) }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-arrow-right"></i> {{ __('messages.view_employee') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
