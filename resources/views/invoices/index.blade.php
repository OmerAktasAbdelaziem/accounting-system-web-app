@extends('layouts.modern')

@section('content')
<div class="container">
    <style>
        @media (max-width: 768px) {
            .invoice-page-hero {
                background: linear-gradient(160deg, #ffffff 0%, #fff8ef 56%, #fff1df 100%);
                border: 1px solid rgba(255, 140, 0, 0.12);
                border-radius: 24px;
                padding: 16px;
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            }

            .invoice-desktop-table {
                display: none;
            }

            .invoice-mobile-list {
                display: grid;
                gap: 12px;
            }

            .invoice-mobile-card {
                background: rgba(255,255,255,0.95);
                border: 1px solid rgba(226, 232, 240, 0.9);
                border-radius: 20px;
                padding: 14px;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            }

            .invoice-mobile-card .title {
                display: flex;
                justify-content: space-between;
                gap: 8px;
                align-items: flex-start;
                margin-bottom: 10px;
            }

            .invoice-mobile-card .title strong {
                font-size: 14px;
                color: #111827;
            }

            .invoice-mobile-meta {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                margin-bottom: 12px;
            }

            .invoice-mobile-chip {
                background: #f8fafc;
                border-radius: 14px;
                padding: 10px;
            }

            .invoice-mobile-chip span {
                display: block;
                font-size: 11px;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-bottom: 3px;
            }

            .invoice-mobile-actions {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .invoice-mobile-actions .btn {
                width: 100%;
                border-radius: 14px;
            }

            .invoice-mobile-create {
                width: 100%;
                border-radius: 16px;
            }
        }
    </style>
    <div class="invoice-page-hero d-flex justify-content-between mb-3 align-items-center gap-3">
        <h3>{{ __('messages.invoices') }}</h3>
        @feature('invoicing')
        <a href="{{ route('invoices.create') }}" class="btn btn-primary invoice-mobile-create">{{ __('messages.create') }}</a>
        @endfeature
    </div>

    <div class="card invoice-desktop-table">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.invoice_number') }}</th>
                        <th>{{ __('messages.customer') }}</th>
                        <th>{{ __('messages.date') }}</th>
                        <th>{{ __('messages.total') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->id }}</td>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->customer ? (is_string($invoice->customer->name) ? $invoice->customer->name : (is_array($invoice->customer->name) ? ($invoice->customer->name[app()->getLocale()] ?? implode(' - ', $invoice->customer->name)) : json_encode($invoice->customer->name))) : '' }}</td>
                        <td>{{ $invoice->date }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($invoice->total,2) }}</td>
                        <td class="action-buttons">
                            @feature('invoicing')
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.view') }}</a>
                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.edit') }}</a>
                            @feature('downloads')
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-success">{{ __('messages.pdf') }}</a>
                            @endfeature
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('messages.confirm_delete') }}')">{{ __('messages.delete') }}</button>
                            </form>
                            @endfeature
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $invoices->links() }}
        </div>
    </div>

    <div class="invoice-mobile-list d-md-none">
        @foreach($invoices as $invoice)
            <div class="invoice-mobile-card">
                <div class="title">
                    <strong>#{{ $invoice->invoice_number }}</strong>
                    <span class="badge bg-light text-dark">{{ $currencySymbol }}{{ number_format($invoice->total, 2) }}</span>
                </div>
                <div class="invoice-mobile-meta">
                    <div class="invoice-mobile-chip">
                        <span>{{ __('messages.customer') }}</span>
                        <strong>{{ $invoice->customer ? (is_string($invoice->customer->name) ? $invoice->customer->name : (is_array($invoice->customer->name) ? ($invoice->customer->name[app()->getLocale()] ?? implode(' - ', $invoice->customer->name)) : json_encode($invoice->customer->name))) : '' }}</strong>
                    </div>
                    <div class="invoice-mobile-chip">
                        <span>{{ __('messages.date') }}</span>
                        <strong>{{ $invoice->date }}</strong>
                    </div>
                </div>
                <div class="invoice-mobile-actions">
                    @feature('invoicing')
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary btn-sm">{{ __('messages.view') }}</a>
                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-primary btn-sm">{{ __('messages.edit') }}</a>
                        @feature('downloads')
                            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-success btn-sm">{{ __('messages.pdf') }}</a>
                        @endfeature
                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('{{ __('messages.confirm_delete') }}')">{{ __('messages.delete') }}</button>
                        </form>
                    @endfeature
                </div>
            </div>
        @endforeach
        <div class="mt-2">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
