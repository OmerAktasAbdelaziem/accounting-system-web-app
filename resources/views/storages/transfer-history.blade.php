@extends('layouts.modern')

@section('title', __('messages.transfer_history'))

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-arrow-left-right"></i> {{ $storage->name }} - {{ __('messages.transfer_history') }}</h1>
        <p class="text-muted">{{ $storage->location }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('storages.items', $storage->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<!-- Transfer Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <i class="bi bi-box-arrow-out"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.total_outgoing') }}</h6>
                <h3>{{ $transfers->where('from_storage_id', $storage->id)->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                <i class="bi bi-box-arrow-in"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.total_incoming') }}</h6>
                <h3>{{ $transfers->where('to_storage_id', $storage->id)->count() }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Transfer History Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-header-modern">
                <tr>
                    <th><i class="bi bi-box"></i> {{ __('messages.product_name') }}</th>
                    <th>{{ __('messages.quantity') }}</th>
                    <th>{{ __('messages.transferred_from') }}</th>
                    <th>{{ __('messages.transferred_to') }}</th>
                    <th>{{ __('messages.transfer_date') }}</th>
                    <th>{{ __('messages.transferred_by') }}</th>
                    <th>{{ __('messages.description') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transfers as $transfer)
                    <tr>
                        <td><strong>{{ $transfer->product->name }}</strong></td>
                        <td>
                            <span class="badge" style="background: linear-gradient(135deg, #ff8c00, #ffb347);">
                                {{ $transfer->quantity }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $transfer->fromStorage->name }}</small>
                        </td>
                        <td>
                            <small>{{ $transfer->toStorage->name }}</small>
                        </td>
                        <td>{{ $transfer->transfer_date->format('M d, Y H:i') }}</td>
                        <td>
                            <small>{{ $transfer->transferredBy?->name ?? __('messages.system') }}</small>
                        </td>
                        <td>
                            @if($transfer->description)
                                <small class="text-muted">{{ Str::limit($transfer->description, 50, '...') }}</small>
                            @else
                                <span class="text-muted">{{ __('messages.not_available') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">{{ __('messages.no_transfers') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transfers->hasPages())
    <div class="card-footer text-muted">
        {{ $transfers->render('pagination::bootstrap-4') }}
    </div>
    @endif
</div>

@endsection
