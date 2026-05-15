@extends('layouts.modern')

@section('title', __('messages.sales_report'))

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>{{ __('messages.sales_report') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="report" value="sales">
            <input type="hidden" name="format" value="pdf">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <input type="hidden" name="from_date" value="{{ $fromDate ?? request('from_date') }}">
            <input type="hidden" name="to_date" value="{{ $toDate ?? request('to_date') }}">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Export PDF
            </button>
        </form>
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline ms-2">
            @csrf
            <input type="hidden" name="report" value="sales">
            <input type="hidden" name="format" value="csv">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <input type="hidden" name="from_date" value="{{ $fromDate ?? request('from_date') }}">
            <input type="hidden" name="to_date" value="{{ $toDate ?? request('to_date') }}">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-3 mb-3">
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
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> {{ __('messages.filter') }}
                </button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.reference') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.debit') }}</th>
                    <th>{{ __('messages.credit') }}</th>
                    <th>{{ __('messages.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesData ?? [] as $sale)
                    <tr>
                        <td>{{ $sale->date->format('M d, Y') }}</td>
                        <td><code>{{ $sale->reference_number ?? '-' }}</code></td>
                        <td>{{ $sale->description }}</td>
                        <td>{{ currencySymbol() }}{{ number_format($sale->total_debit, 2) }}</td>
                        <td><strong>{{ currencySymbol() }}{{ number_format($sale->total_credit, 2) }}</strong></td>
                        <td>
                            <span class="badge {{ $sale->status === 'posted' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
