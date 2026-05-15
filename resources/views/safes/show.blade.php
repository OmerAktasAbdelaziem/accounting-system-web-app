@extends('layouts.modern')

@section('title', $safe->name)

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-safe"></i> {{ $safe->name }}</h1>
        <p class="text-muted">{{ $safe->location }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('safes.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
        <a href="{{ route('safes.edit', $safe->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
        </a>
    </div>
</div>

<!-- Safe Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.current_balance') }}</h6>
                <h3>{{ currencySymbol() }}{{ number_format($safe->balance, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ff8c00, #ffb347);">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.max_balance') }}</h6>
                <h3>{{ currencySymbol() }}{{ number_format($safe->max_balance, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c, #ec7063);">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.today_deposits') }}</h6>
                <h3>{{ currencySymbol() }}{{ number_format($todayDeposits, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                <i class="bi bi-graph-down"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.today_withdrawals') }}</h6>
                <h3>{{ currencySymbol() }}{{ number_format($todayWithdrawals, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Safe Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('messages.safe_information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.safe_name') }}</label>
                        <p class="text-muted">{{ $safe->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.location') }}</label>
                        <p class="text-muted">{{ $safe->location }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.current_balance') }}</label>
                        <p class="text-success fw-bold">{{ currencySymbol() }}{{ number_format($safe->balance, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.max_balance') }}</label>
                        <p class="text-muted">{{ currencySymbol() }}{{ number_format($safe->max_balance, 2) }}</p>
                    </div>
                </div>

                @php
                    $capacityPercentage = ($safe->balance / $safe->max_balance) * 100;
                    $barColor = $capacityPercentage >= 90 ? 'danger' : ($capacityPercentage >= 70 ? 'warning' : 'success');
                @endphp

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('messages.capacity_usage') }}</label>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-{{ $barColor }}" style="width: {{ $capacityPercentage }}%">
                            {{ number_format($capacityPercentage, 1) }}%
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.description') }}</label>
                        <p class="text-muted">{{ $safe->description ?? __('messages.no_description') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.status') }}</label>
                        <p>
                            @if($safe->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ __('messages.active') }}</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> {{ __('messages.inactive') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.created') }}</label>
                        <p class="text-muted">{{ $safe->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.last_updated') }}</label>
                        <p class="text-muted">{{ $safe->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #3498db, #5dade2); color: white;">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> {{ __('messages.recent_transactions') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.balance_after') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->type === 'deposit' ? 'success' : 'danger' }}">
                                        <i class="bi bi-{{ $transaction->type === 'deposit' ? 'arrow-up' : 'arrow-down' }}"></i> {{ $transaction->type === 'deposit' ? __('messages.deposit') : __('messages.withdrawal') }}
                                    </span>
                                </td>
                                <td class="fw-bold">{{ currencySymbol() }}{{ number_format($transaction->amount, 2) }}</td>
                                <td>{{ Str::limit($transaction->description, 40, '...') }}</td>
                                <td>{{ currencySymbol() }}{{ number_format($transaction->balance_after, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">{{ __('messages.no_transactions_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> {{ __('messages.quick_actions') }}</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#depositModal">
                    <i class="bi bi-arrow-up"></i> {{ __('messages.deposit') }}
                </button>
                <button class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <i class="bi bi-arrow-down"></i> {{ __('messages.withdraw') }}
                </button>
                <a href="{{ route('safes.transactions', $safe->id) }}" class="btn btn-info w-100">
                    <i class="bi bi-list"></i> {{ __('messages.view_transactions') }}
                </a>
            </div>
        </div>

        <!-- Today's Summary -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-calendar-day"></i> {{ __('messages.today') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr class="table-success">
                        <td><strong>{{ __('messages.deposits') }}</strong></td>
                        <td class="text-end text-success fw-bold">{{ currencySymbol() }}{{ number_format($todayDeposits, 2) }}</td>
                    </tr>
                    <tr class="table-danger">
                        <td><strong>{{ __('messages.withdrawals') }}</strong></td>
                        <td class="text-end text-danger fw-bold">-{{ currencySymbol() }}{{ number_format($todayWithdrawals, 2) }}</td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>{{ __('messages.net_change') }}</strong></td>
                        <td class="text-end fw-bold">{{ currencySymbol() }}{{ number_format($todayDeposits - $todayWithdrawals, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.transactions_today') }}</strong></td>
                        <td class="text-end">{{ $todayTransactionCount }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Safe Status -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="mb-0"><i class="bi bi-speedometer2"></i> {{ __('messages.status') }}</h5>
            </div>
            <div class="card-body">
                @if($capacityPercentage >= 90)
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> <strong>{{ __('messages.near_capacity') }}</strong><br>
                        <small>{{ __('messages.near_capacity_message') }}</small>
                    </div>
                @elseif($capacityPercentage >= 70)
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-circle"></i> <strong>{{ __('messages.moderate_capacity') }}</strong><br>
                        <small>{{ __('messages.safe_available_percent', ['percent' => number_format(100 - $capacityPercentage, 1)]) }}</small>
                    </div>
                @else
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle"></i> <strong>{{ __('messages.good_capacity') }}</strong><br>
                        <small>{{ __('messages.plenty_space') }}</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="modal-title"><i class="bi bi-arrow-up"></i> {{ __('messages.deposit_money') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('safes.deposit', $safe->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.amount') }} *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> {{ __('messages.deposit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c, #ec7063); color: white;">
                <h5 class="modal-title"><i class="bi bi-arrow-down"></i> {{ __('messages.withdraw_money') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('safes.withdraw', $safe->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small><strong>{{ __('messages.current_balance') }}:</strong> {{ currencySymbol() }}{{ number_format($safe->balance, 2) }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.amount') }} *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" max="{{ $safe->balance }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-check-circle"></i> {{ __('messages.withdraw') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
