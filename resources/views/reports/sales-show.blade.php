@extends('layouts.modern')

@section('title', __('messages.sales_report'))

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2>{{ __('messages.sales_report') }} - {{ __('messages.details') }}</h2>
        <p class="text-muted mb-0">{{ $sale->reference_number ?? '-' }}</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <small class="text-muted d-block">{{ __('messages.date') }}</small>
                <strong>{{ optional($sale->date)->format('M d, Y') }}</strong>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">{{ __('messages.reference') }}</small>
                <strong>{{ $sale->reference_number ?? '-' }}</strong>
            </div>
            <div class="col-md-4">
                <small class="text-muted d-block">{{ __('messages.created_by') }}</small>
                <strong>{{ $sale->createdBy?->name ?? '-' }}</strong>
            </div>
            <div class="col-md-12">
                <small class="text-muted d-block">{{ __('messages.description') }}</small>
                <strong>{{ $sale->description }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">{{ __('messages.debit') }}</small>
                <strong>{{ $currencySymbol }}{{ number_format((float) $sale->total_debit, 2) }}</strong>
            </div>
            <div class="col-md-6">
                <small class="text-muted d-block">{{ __('messages.credit') }}</small>
                <strong>{{ $currencySymbol }}{{ number_format((float) $sale->total_credit, 2) }}</strong>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">{{ __('messages.details') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>{{ __('messages.account') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.debit') }}</th>
                    <th>{{ __('messages.credit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sale->items as $item)
                    <tr>
                        <td>{{ $item->account?->name ?? '-' }}</td>
                        <td>{{ $item->description ?? '-' }}</td>
                        <td>{{ $item->debit > 0 ? number_format((float) $item->debit, 2) : '-' }}</td>
                        <td>{{ $item->credit > 0 ? number_format((float) $item->credit, 2) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
