@extends('layouts.modern')

@section('title', __('messages.inventory_report'))

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>{{ __('messages.inventory_report') }}</h2>
    </div>
    <div class="col-md-6 text-end">
        <form action="{{ route('reports.generate-pdf') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="report" value="inventory">
            <input type="hidden" name="format" value="csv">
            <input type="hidden" name="branch_id" value="{{ $branchId ?? request('branch_id') }}">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
            </button>
        </form>
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
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.product_name') }}</th>
                    <th>{{ __('messages.sku') }}</th>
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
                        <td><code>{{ $product->sku }}</code></td>
                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                        <td>{{ $product->current_stock }}</td>
                        <td>${{ number_format($product->current_stock * $product->selling_price, 2) }}</td>
                        <td>
                            @if($product->current_stock <= $product->min_stock)
                                <span class="badge bg-danger">{{ __('messages.low_stock') }}</span>
                            @else
                                <span class="badge bg-success">{{ __('messages.in_stock') }}</span>
                            @endif
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
