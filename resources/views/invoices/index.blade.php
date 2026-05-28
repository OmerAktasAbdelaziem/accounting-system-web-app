@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>{{ __('messages.invoices') }}</h3>
        @feature('invoicing')
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
        @endfeature
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Invoice #') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Total') }}</th>
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
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a>
                            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                            @feature('downloads')
                                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-success">{{ __('PDF') }}</a>
                            @endfeature
                            <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" style="display:inline-block">
                            @endfeature
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
