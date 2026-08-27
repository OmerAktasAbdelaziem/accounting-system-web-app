@extends('layouts.modern')

@section('title', __('messages.transfer_history'))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">{{ $storage->name }} - Transfer History</h3>
            <div class="text-muted">{{ $storage->location }}</div>
        </div>
        <a href="{{ route('storages.items', $storage->{{ __('id) }}" class="btn btn-outline-secondary">Back') }}</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">{{ __('Outgoing Transfers') }}</small>
                    <h4 class="mb-0">{{ $transferStats['outgoing'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">{{ __('Incoming Transfers') }}</small>
                    <h4 class="mb-0">{{ $transferStats['incoming'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Weight') }}</th>
                            <th>{{ __('Unit Price') }}</th>
                            <th>{{ __('Total Price') }}</th>
                            <th>{{ __('From') }}</th>
                            <th>{{ __('To') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('By') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            <tr>
                                <td><strong>{{ $transfer->product_name }}</strong></td>
                                <td>{{ number_format((float) $transfer->quantity, 2) }}</td>
                                <td>{{ number_format((float) $transfer->weight, 2) }}</td>
                                <td>{{ $currencySymbol ?? '$' }}{{ number_format((float) $transfer->unit_price, 2) }}</td>
                                <td>{{ $currencySymbol ?? '$' }}{{ number_format((float) $transfer->total_price, 2) }}</td>
                                <td>{{ $transfer->fromStorage?->name ?? '-' }}</td>
                                <td>{{ $transfer->toStorage?->name ?? '-' }}</td>
                                <td>{{ $transfer->transfer_date?->format('Y-m-d H:i') }}</td>
                                <td>{{ $transfer->transferredBy?->name ?? __('messages.system') }}</td>
                            </tr>
                        {{ __('@empty') }}
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">{{ __('No transfer records yet.') }}</td>
                            </tr>
                        {{ __('@endforelse') }}
                    </tbody>
                </table>
            </div>
        </div>

        @if($transfers->hasPages())
            <div class="card-footer bg-white">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection