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
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary-modern" data-bs-toggle="modal" data-bs-target="#depositModal">
                        <i class="bi bi-plus-circle"></i> {{ __('messages.deposit_money') }}
                    </button>
                    <button type="button" class="btn btn-success-modern" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        <i class="bi bi-dash-circle"></i> {{ __('messages.withdraw_money') }}
                    </button>
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
                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
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

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('safes.deposit', $safe->id) }}">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%); color: white;">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> {{ __('messages.deposit_money') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="deposit_amount" class="form-label">{{ __('messages.amount') }} *</label>
                        <input type="number" class="form-control" id="deposit_amount" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="deposit_ref" class="form-label">{{ __('messages.reference_type') }}</label>
                        <select class="form-select" id="deposit_ref" name="reference_type">
                            <option value="">{{ __('messages.select_type') }}</option>
                            <option value="cash_register">{{ __('messages.cash_register') }}</option>
                            <option value="bank_transfer">{{ __('messages.bank_transfer') }}</option>
                            <option value="invoice">{{ __('messages.invoice') }}</option>
                            <option value="other">{{ __('messages.other') }}</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="deposit_desc" class="form-label">{{ __('messages.description') }}</label>
                        <textarea class="form-control" id="deposit_desc" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary-modern">
                        <i class="bi bi-check-circle"></i> {{ __('messages.confirm_deposit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('safes.withdraw', $safe->id) }}">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%); color: white;">
                    <h5 class="modal-title"><i class="bi bi-dash-circle"></i> {{ __('messages.withdraw_money') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>{{ __('messages.available_balance') }}:</strong> {{ $currencySymbol }}{{ number_format($safe->balance, 2) }}
                    </div>
                    <div class="form-group mb-3">
                        <label for="withdraw_amount" class="form-label">{{ __('messages.amount') }} *</label>
                        <input type="number" class="form-control" id="withdraw_amount" name="amount" step="0.01" min="0.01" max="{{ $safe->balance }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="withdraw_ref" class="form-label">{{ __('messages.reference_type') }}</label>
                        <select class="form-select" id="withdraw_ref" name="reference_type">
                            <option value="">{{ __('messages.select_type') }}</option>
                            <option value="cash_register">{{ __('messages.cash_register') }}</option>
                            <option value="bank_transfer">{{ __('messages.bank_transfer') }}</option>
                            <option value="commission">{{ __('messages.commission_payment') }}</option>
                            <option value="expense">{{ __('messages.expense') }}</option>
                            <option value="other">{{ __('messages.other') }}</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="withdraw_desc" class="form-label">{{ __('messages.description') }}</label>
                        <textarea class="form-control" id="withdraw_desc" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-success-modern">
                        <i class="bi bi-check-circle"></i> {{ __('messages.confirm_withdrawal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
