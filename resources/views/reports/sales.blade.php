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

    /* Mobile card list */
    @media (max-width: 768px) {
        .mobile-report-list { display: block; }
        .table { display: none !important; }
    }
</style>

@feature('sales_report')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>{{ __('messages.sales_report') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        @feature('downloads')
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="report" value="sales">
            <input type="hidden" name="format" value="pdf">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <input type="hidden" name="from_date" value="{{ $fromDate ?? request('from_date') }}">
            <input type="hidden" name="to_date" value="{{ $toDate ?? request('to_date') }}">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> {{ __('Export PDF') }}
            </button>
        </form>
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline ms-2">
            @csrf
            <input type="hidden" name="report" value="sales">
            <input type="hidden" name="format" value="excel">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <input type="hidden" name="from_date" value="{{ $fromDate ?? request('from_date') }}">
            <input type="hidden" name="to_date" value="{{ $toDate ?? request('to_date') }}">
            <button type="submit" class="btn btn-info">
                <i class="bi bi-file-earmark-spreadsheet"></i> {{ __('Export Excel') }}
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
                <i class="bi bi-file-earmark-spreadsheet"></i> {{ __('Export CSV') }}
            </button>
        </form>
        @endfeature
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

    <!-- Mobile list (cards) -->
    <div class="mobile-report-list d-md-none">
        @forelse($salesData ?? [] as $sale)
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-muted">{{ $sale->date->translatedFormat('M d, Y') }}</div>
                            <strong>{{ $sale->reference_number ?? '-' }}</strong>
                            <div class="small text-muted">{{ $sale->description }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold">{{ $currencySymbol }}{{ number_format($sale->total_credit,2) }}</div>
                            <div class="small text-muted">{{ $currencySymbol }}{{ number_format($sale->total_debit,2) }}</div>
                        </div>
                    </div>
                    <div class="mt-2 d-flex gap-2">
                        @feature('sales_report')
                        <a href="{{ route('reports.sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <form action="{{ route('reports.sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this sales report entry?') }}');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endfeature
                    </div>
                </div>
            </div>
        @empty
            <div class="card mb-2"><div class="card-body text-center text-muted">{{ __('messages.no_data') }}</div></div>
        @endforelse
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
                        <td>{{ $sale->date->translatedFormat('M d, Y') }}</td>
                        <td><code>{{ $sale->reference_number ?? '-' }}</code></td>
                        <td>{{ $sale->description }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($sale->total_debit, 2) }}</td>
                        <td><strong>{{ $currencySymbol }}{{ number_format($sale->total_credit, 2) }}</strong></td>
                        <td>
                            <div class="d-flex gap-2">
                            @feature('sales_report')
                            <a href="{{ route('reports.sales.show', $sale) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> {{ __('messages.view') }}
                            </a>
                            <form action="{{ route('reports.sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this sales report entry?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i> {{ __('messages.delete') }}
                                </button>
                            </form>
                            @endfeature
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
@endfeature
