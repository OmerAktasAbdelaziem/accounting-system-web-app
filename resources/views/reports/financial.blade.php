@extends('layouts.modern')

@section('title', __('messages.financial_report'))

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>{{ __('messages.financial_report') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        @if(auth()->user()?->canViewMenuItem('downloads'))
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="report" value="financial">
            <input type="hidden" name="format" value="pdf">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <input type="hidden" name="from_date" value="{{ $fromDate ?? request('from_date') }}">
            <input type="hidden" name="to_date" value="{{ $toDate ?? request('to_date') }}">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> PDF
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Paid payrolls') }}</div>
                <div class="display-6 fw-bold">{{ ($payrollSettlements ?? collect())->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Paid commissions') }}</div>
                <div class="display-6 fw-bold">{{ ($commissionSettlements ?? collect())->count() }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Payroll safe payouts') }}</div>
                <div class="display-6 fw-bold">{{ ($safePayrollOutcomes ?? collect())->count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label>{{ __('messages.from_date') }}</label>
                <input type="date" class="form-control" name="from_date" value="{{ $fromDate ?? request('from_date') }}">
            </div>
            <div class="col-md-4">
                <label>{{ __('messages.to_date') }}</label>
                <input type="date" class="form-control" name="to_date" value="{{ $toDate ?? request('to_date') }}">
            </div>
            <div class="col-md-4">
                <label>{{ __('messages.branch') }}</label>
                <input type="text" class="form-control" name="branch_id" value="{{ $branchId ?? request('branch_id') }}" placeholder="{{ __('messages.branch_code') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.filter') }}</button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.account') }}</th>
                    <th>{{ __('messages.debit') }}</th>
                    <th>{{ __('messages.credit') }}</th>
                    <th>{{ __('messages.description') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries ?? [] as $entry)
                    @forelse($entry->items ?? [] as $item)
                        <tr>
                            <td>{{ $entry->date->format('M d, Y') }}</td>
                            <td><strong>{{ $item->account->name ?? 'N/A' }}</strong></td>
                            <td class="text-success">{{ $item->debit > 0 ? $currencySymbol . number_format($item->debit, 2) : '-' }}</td>
                            <td class="text-danger">{{ $item->credit > 0 ? $currencySymbol . number_format($item->credit, 2) : '-' }}</td>
                            <td>{{ $item->description ?? $entry->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-white">
        <strong>{{ __('Paid Payrolls') }}</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.employee') }}</th>
                    <th>{{ __('messages.safe') }}</th>
                    <th>{{ __('messages.net_salary') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrollSettlements ?? [] as $payroll)
                    <tr>
                        <td>{{ optional($payroll->processed_at)->format('M d, Y') ?? '-' }}</td>
                        <td>{{ $payroll->employee?->name ?? '-' }}</td>
                        <td>{{ $payroll->safe?->name ?? '-' }}</td>
                        <td class="text-success">{{ $currencySymbol }}{{ number_format((float) $payroll->net_salary, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">{{ __('messages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-white">
        <strong>{{ __('Paid Commissions') }}</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.employee') }}</th>
                    <th>{{ __('messages.commission') }}</th>
                    <th>{{ __('messages.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissionSettlements ?? [] as $commission)
                    <tr>
                        <td>{{ optional($commission->updated_at)->format('M d, Y') ?? '-' }}</td>
                        <td>{{ $commission->employee?->name ?? '-' }}</td>
                        <td class="text-success">{{ $currencySymbol }}{{ number_format((float) $commission->commission_amount, 2) }}</td>
                        <td><span class="badge bg-success">{{ strtoupper($commission->status ?? 'paid') }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">{{ __('messages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-4 mb-4">
    <div class="card-header bg-white">
        <strong>{{ __('Payroll Safe Payouts') }}</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.safe') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th>{{ __('messages.description') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($safePayrollOutcomes ?? [] as $outcome)
                    <tr>
                        <td>{{ optional($outcome->created_at)->format('M d, Y') ?? '-' }}</td>
                        <td>{{ $outcome->safe?->name ?? '-' }}</td>
                        <td class="text-danger">{{ $currencySymbol }}{{ number_format((float) $outcome->amount, 2) }}</td>
                        <td>{{ $outcome->description ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">{{ __('messages.no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
