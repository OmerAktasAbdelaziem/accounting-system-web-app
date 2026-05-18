@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>{{ $invoice->invoice_number }}</h3>
        <div>
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-primary">{{ __('Edit') }}</a>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-danger">{{ __('messages.download_pdf') }}</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>{{ __('Customer') }}:</strong> {{ $invoice->customer?->name }}</p>
                    <p><strong>{{ __('Date') }}:</strong> {{ $invoice->date?->format('Y-m-d') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Items -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Line Items') }}</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th class="text-end">{{ __('Quantity') }}</th>
                        <th class="text-end">{{ __('Unit Price') }}</th>
                        <th class="text-end">{{ __('Line Total') }}</th>
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
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">{{ __('No line items') }}</td>
                    </tr>
                    @endforelse
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
                        <div class="col-6"><strong>{{ __('Sub Total:') }}</strong></div>
                        <div class="col-6 text-end">{{ $currencySymbol }}{{ number_format($invoice->sub_total, 2) }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><strong>{{ __('Tax:') }}</strong></div>
                        <div class="col-6 text-end">{{ $currencySymbol }}{{ number_format($invoice->tax, 2) }}</div>
                    </div>
                    @if($invoice->vat_rate > 0)
                    <div class="row mb-3">
                        <div class="col-6"><strong>{{ __('VAT') }} ({{ $invoice->vat_rate }}%):</strong></div>
                        <div class="col-6 text-end">{{ $currencySymbol }}{{ number_format($invoice->vat_amount, 2) }}</div>
                    </div>
                    @endif
                    <hr>
                    <div class="row">
                        <div class="col-6"><h5>{{ __('Total:') }}</h5></div>
                        <div class="col-6 text-end"><h5>{{ $currencySymbol }}{{ number_format($invoice->total, 2) }}</h5></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection