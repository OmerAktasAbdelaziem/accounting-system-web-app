@extends('layouts.modern')

@section('title', __('messages.safe_transactions') . ' - ' . ($safe->name ?? __('messages.safe')))

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-arrow-left-right" style="color: #ff8c00;"></i> {{ __('messages.safe_transactions') }} - {{ $safe->name }}
        </h1>
        <a href="{{ route('safes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<!-- Current Balance Card -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card green">
            <h6>{{ __('messages.current_balance') }}</h6>
            <div class="value" style="color: var(--primary-green);">{{ $currencySymbol }}{{ number_format($safe->balance, 2) }}</div>
            <div class="icon"><i class="bi bi-cash-coin"></i></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    {{ __('Deposit and withdrawal actions were removed in favor of income and outcome tracking.') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list"></i> {{ __('messages.transaction_history') }}
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.type') }}</th>
                    <th>{{ __('messages.amount') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>{{ __('messages.user') }}</th>
                    <th>{{ __('messages.reference') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions ?? [] as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at->translatedFormat('M d, Y H:i') }}</td>
                        <td>
                            @if($transaction->type === 'deposit')
                                <span class="badge bg-success"><i class="bi bi-plus-circle"></i> {{ __('messages.deposit') }}</span>
                            @elseif($transaction->type === 'withdrawal')
                                <span class="badge bg-danger"><i class="bi bi-dash-circle"></i> {{ __('messages.withdrawal') }}</span>
                            @else
                                <span class="badge bg-info">{{ ucfirst($transaction->type) }}</span>
                            @endif
                        </td>
                        <td>
                            <strong style="color: {{ $transaction->type === 'deposit' ? 'var(--primary-green)' : '#c0392b' }};">
                                {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ $currencySymbol }}{{ number_format($transaction->amount, 2) }}
                            </strong>
                        </td>
                        <td>{{ $transaction->description ?? '-' }}</td>
                        <td>{{ $transaction->user->name ?? '-' }}</td>
                        <td>{{ $transaction->reference_type ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">{{ __('messages.no_transactions_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($transactions ?? false)
    <div class="mt-3">
        {{ $transactions->links() }}
    </div>
@endif

@endsection
