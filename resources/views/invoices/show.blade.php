@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>{{ $invoice->invoice_number }}</h3>
        <div>
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary">{{ __('messages.edit') }}</a>
                @if(auth()->user()?->canViewMenuItem('downloads'))
                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-pdf"></i> {{ __('Download PDF') }}
                </a>
                <a href="{{ route('invoices.excel', $invoice) }}" class="btn btn-info" target="_blank">
                    <i class="bi bi-file-earmark-spreadsheet"></i> {{ __('Download Excel') }}
                </a>
                @endif
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>{{ __('messages.customer') }}:</strong> {{ $invoice->customer?->name }}</p>
                    <p><strong>{{ __('messages.date') }}:</strong> {{ $invoice->date?->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('messages.line_items') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.product') }}</th>
                        <th class="text-end">{{ __('messages.quantity') }}</th>
                        <th class="text-end">{{ __('messages.unit_price') }}</th>
                        <th class="text-end">{{ __('messages.line_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product?->name ?? 'Item ' . $item->id }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">{{ $currencySymbol }}{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    {{ __('@empty') }}
                    <tr>
                        <td colspan="4" class="text-center text-muted">{{ __('messages.no_line_items') }}</td>
                    </tr>
                    {{ __('@endforelse') }}
                </tbody>
            </table>
        </div>
    </div>

    <!-- Totals -->
    <div class="row">
        <div class="col-md-4 offset-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-6"><strong>{{ __('messages.sub_total') }}:</strong></div>
                        <div class="col-6 text-end">{{ $currencySymbol }}{{ number_format($invoice->sub_total, 2) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>{{ __('messages.tax') }}:</strong></div>
                        <div class="col-6 text-end">{{ $currencySymbol }}{{ number_format($invoice->tax, 2) }}</div>
                    </div>
                    @if($invoice->vat_rate > 0)
                    <div class="row mb-3">
                        <div class="col-6"><strong>{{ __('messages.vat') }} ({{ $invoice->vat_rate }}%):</strong></div>
                        <div class="col-6 text-end">{{ $currencySymbol }}{{ number_format($invoice->vat_amount, 2) }}</div>
                    </div>
                    @endif
                    <hr>
                    <div class="row">
                        <div class="col-6"><h5>{{ __('messages.total') }}:</h5></div>
                        <div class="col-6 text-end"><h5>{{ $currencySymbol }}{{ number_format($invoice->total, 2) }}</h5></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection