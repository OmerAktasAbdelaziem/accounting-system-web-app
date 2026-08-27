@extends('layouts.modern')

@section('title', __('messages.inventory_report'))

@section('content')
@feature('inventory_report')
<style>
    @media (max-width: 768px) {
        .inventory-mobile-list { display: block; }
        .table { display: none !important; }
    }
</style>
<div class="row mb-4">
    <div class="col-md-6">
        <h2>{{ __('messages.inventory_report') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        @feature('downloads')
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="report" value="inventory">
            <input type="hidden" name="format" value="pdf">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> {{ __('Export PDF') }}
            </button>
        </form>
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline ms-2">
            @csrf
            <input type="hidden" name="report" value="inventory">
            <input type="hidden" name="format" value="excel">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <button type="submit" class="btn btn-info">
                <i class="bi bi-file-earmark-spreadsheet"></i> {{ __('Export Excel') }}
            </button>
        </form>
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline ms-2">
            @csrf
            <input type="hidden" name="report" value="inventory">
            <input type="hidden" name="format" value="csv">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-file-earmark-spreadsheet"></i> {{ __('Export CSV') }}
            </button>
        </form>
        @endfeature
    </div>
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label>{{ __('messages.branch') }}</label>
                <input type="text" class="form-control" name="branch_id" value="{{ $branchId ?? request('branch_id') }}" placeholder="{{ __('messages.branch_code') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">{{ __('messages.filter') }}</button>
            </div>
        </form>
    </div>
    <!-- Mobile product cards -->
    <div class="inventory-mobile-list d-md-none">
        @forelse($products ?? [] as $product)
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $product->name }}</strong>
                            <div class="small text-muted">{{ $product->category->name ?? 'N/A' }}</div>
                        </div>
                        <div class="text-end">
                            <div>{{ $product->current_stock }}</div>
                            <div class="small text-muted">{{ $currencySymbol }}{{ number_format($product->current_stock * $product->selling_price, 2) }}</div>
                        </div>
                    </div>
                    <div class="mt-2">
                        @if($product->current_stock <= 0)
                            <span class="badge bg-danger">{{ __('messages.low_stock') }}</span>
                        @else
                            <span class="badge bg-success">{{ __('messages.in_stock') }}</span>
                        @endif
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
                    <th>{{ __('messages.product_name') }}</th>
                    <th>{{ __('messages.category') }}</th>
                    <th>{{ __('messages.quantity') }}</th>
                    <th>{{ __('messages.value') }}</th>
                    <th>{{ __('messages.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products ?? [] as $product)
                    <tr>
                        <td><strong>{{ $product->name }}</strong></td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ $product->current_stock }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($product->current_stock * $product->selling_price, 2) }}</td>
                        <td>
                            @if($product->current_stock <= 0)
                                <span class="badge bg-danger">{{ __('messages.low_stock') }}</span>
                            @else
                                <span class="badge bg-success">{{ __('messages.in_stock') }}</span>
                            @endif
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
@endfeature
