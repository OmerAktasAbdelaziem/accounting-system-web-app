@extends('layouts.modern')

@section('title', __('messages.payroll') . ' - ' . $payroll->employee?->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 style="font-weight: 900; color: #1a1a1a;">
                    <i class="bi bi-receipt" style="color: #ff8c00;"></i> 
                    {{ __('messages.payroll') }} - {{ $payroll->employee?->name }}
                </h1>
                <p class="text-muted">{{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('payroll.payslip', $payroll) }}" class="btn btn-outline-danger" title="Download Payslip">
                    <i class="bi bi-file-pdf"></i> {{ __('messages.download_pdf') }}
                </a>
                <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i>
                </a>
                <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Payroll Details -->
    <div class="row mb-4">
        <!-- Salary Breakdown -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-calculator"></i> {{ __('messages.salary_breakdown') }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Basic Salary -->
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">{{ __('messages.basic_salary') }}</span>
                                <span class="fs-5 fw-bold">{{ $currencySymbol }}{{ number_format($payroll->basic_salary, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Base compensation</small>
                        </div>
                    </div>

                    <!-- Commission -->
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">{{ __('messages.commission') }}</span>
                                <span class="fs-5 fw-bold text-success">+{{ $currencySymbol }}{{ number_format($payroll->commission ?? 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Sales/performance bonus</small>
                        </div>
                    </div>

                    <!-- Allowances -->
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">{{ __('messages.allowances') }}</span>
                                <span class="fs-5 fw-bold text-info">+{{ $currencySymbol }}{{ number_format($payroll->allowances ?? 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Additional benefits</small>
                        </div>
                    </div>

                    <!-- Advances Deduction -->
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-muted">{{ __('messages.advances') }}</span>
                                <span class="fs-5 fw-bold text-danger">-{{ $currencySymbol }}{{ number_format($payroll->advances_deducted ?? 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Employee advances (deducted)</small>
                        </div>
                    </div>

                    <!-- Net Salary (Total) -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold" style="font-size: 1.1rem;">{{ __('messages.net_salary') }}</span>
                                <span class="fs-4 fw-bold text-success" style="color: #27ae60 !important;">
                                    {{ $currencySymbol }}{{ number_format($payroll->net_salary, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted fw-bold">Total payable</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee & Period Info -->
        <div class="col-lg-4">
            <!-- Employee Card -->
            <div class="card mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle"></i> {{ __('messages.employee_information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('messages.name') }}</small>
                        <h6 class="mb-0">{{ $payroll->employee?->name }}</h6>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('messages.position') }}</small>
                        <h6 class="mb-0">{{ $payroll->employee?->position ?? 'N/A' }}</h6>
                    </div>
                    <div>
                        <small class="text-muted d-block">{{ __('messages.code') }}</small>
                        <h6 class="mb-0" style="font-family: monospace;">{{ $payroll->employee?->employee_code ?? 'N/A' }}</h6>
                    </div>
                </div>
            </div>

            <!-- Period Card -->
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #3498db, #5dade2); color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-event"></i> {{ __('messages.period') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">{{ __('messages.month') }}/{{ __('messages.year') }}</small>
                        <h5 class="mb-0">
                            {{ \Carbon\Carbon::createFromDate($payroll->year, $payroll->month, 1)->format('F Y') }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    @if($payroll->notes)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-chat-left-text"></i> {{ __('messages.notes') }}
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $payroll->notes }}</p>
            </div>
        </div>
    @endif

    <!-- Commissions Section -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #52be80); color: white;">
            <h5 class="mb-0">
                <i class="bi bi-graph-up"></i> Commission Transactions
            </h5>
        </div>
        <div class="card-body">
            @php
                $allCommissions = \App\Models\Commission::where('employee_id', $payroll->employee_id)
                    ->orderBy('commission_date', 'desc')
                    ->get();
                $commissions = $allCommissions->filter(function($commission) use ($payroll) {
                    return $commission->commission_date->month == $payroll->month && 
                           $commission->commission_date->year == $payroll->year;
                });
            @endphp
            
            @if($commissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Sale Amount') }}</th>
                                <th>{{ __('Commission Rate') }}</th>
                                <th>{{ __('Commission Amount') }}</th>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commissions as $commission)
                            <tr>
                                <td>
                                    <small class="fw-bold">{{ $commission->commission_date->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $currencySymbol }}{{ number_format($commission->sale_amount, 2) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $commission->commission_rate }}%</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">{{ $currencySymbol }}{{ number_format($commission->commission_amount, 2) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $commission->reference_type ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($commission->notes, 30) ?? 'N/A' }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No commission transactions for this period.
                </div>
            @endif
        </div>
    </div>

    <!-- Advances Section -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #e74c3c, #ec7063); color: white;">
            <h5 class="mb-0">
                <i class="bi bi-cash-coin"></i> Advances Transactions
            </h5>
        </div>
        <div class="card-body">
            @php
                $advances = \App\Models\EmployeeAdvance::where('employee_id', $payroll->employee_id)
                    ->orderBy('advance_date', 'desc')
                    ->get();
            @endphp
            
            @if($advances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Created By') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advances as $advance)
                            <tr>
                                <td>
                                    <small class="fw-bold">{{ $advance->advance_date->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-danger">{{ $currencySymbol }}{{ number_format($advance->amount, 2) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($advance->description, 40) ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $advance->createdBy?->name ?? 'System' }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No advance transactions for this employee.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection