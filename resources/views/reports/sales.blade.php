@extends('layouts.modern')

@section('title', __('messages.sales_report'))

@section('content')
<style>
    @media (max-width: 768px) {
        .row.mb-4,
        .row.mb-4 > [class*="col-md-"] {
            width: 100%;
        }

        .row.mb-4 > .col-md-6,
        .row.mb-4 > .col-md-4,
        .row.mb-4 > .col-md-8 {
            width: 100%;
        }

        .row.mb-4 .text-end {
            text-align: left !important;
        }

        .row.mb-4 .btn,
        .row.mb-4 form,
        .row.mb-4 input,
        .row.mb-4 select {
            width: 100%;
        }

        .card-body .row.g-3 .col-md-4,
        .card-body .row.g-3 .col-md-6,
        .card-body .row.g-3 .col-md-12,
        .card-body .row.g-3 .col-md-8 {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            min-width: 760px;
        }

        .d-flex.gap-2 {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 576px) {
        .table {
            min-width: 640px;
        }

        h2 {
            font-size: 22px;
        }
    }
</style>

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
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesData ?? [] as $sale)
                    <tr>
                        <td>{{ $sale->date->format('M d, Y') }}</td>
                        <td><code>{{ $sale->reference_number ?? '-' }}</code></td>
                        <td>{{ $sale->description }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($sale->total_debit, 2) }}</td>
                        <td><strong>{{ $currencySymbol }}{{ number_format($sale->total_credit, 2) }}</strong></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('reports.sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> {{ __('messages.view') }}
                                </a>
                                <form action="{{ route('reports.sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sales report entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> {{ __('messages.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
