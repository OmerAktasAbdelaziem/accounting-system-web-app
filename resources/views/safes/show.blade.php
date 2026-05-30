@extends('layouts.modern')

@section('title', $safe->name)

@section('content')
<style>
    .safe-mobile-orange-header {
        background: linear-gradient(135deg, #1a1a1a, #333) !important;
        color: #fff !important;
    }

    @media (max-width: 768px) {
        .safe-mobile-orange-header {
            background: linear-gradient(135deg, #ff8c00, #ffb347) !important;
        }
    }
</style>
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-safe"></i> {{ $safe->name }}</h1>
        <p class="text-muted">{{ $safe->location }}</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('safes.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
        @feature('safes.edit')
        <a href="{{ route('safes.edit', $safe->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
        </a>
        @endfeature
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.current_balance') }}</h6>
                <h3>{{ $currencySymbol }}{{ number_format($safe->balance, 2) }}</h3>
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
                <h3>{{ $currencySymbol }}{{ number_format($safe->max_balance, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c, #ec7063);">
                <i class="bi bi-graph-up"></i>
            </div>
            <div class="stat-content">
                <h6>Today Income</h6>
                <h3>{{ $currencySymbol }}{{ number_format($todayIncome, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                <i class="bi bi-graph-down"></i>
            </div>
            <div class="stat-content">
                <h6>Today Outcome</h6>
                <h3>{{ $currencySymbol }}{{ number_format($todayOutcome, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
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
                        <p class="text-success fw-bold">{{ $currencySymbol }}{{ number_format($safe->balance, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.max_balance') }}</label>
                        <p class="text-muted">{{ $currencySymbol }}{{ number_format($safe->max_balance, 2) }}</p>
                    </div>
                </div>

                @php
                    $hasMaxBalance = (float) $safe->max_balance > 0;
                    $capacityPercentage = $hasMaxBalance ? ($safe->balance / $safe->max_balance) * 100 : null;
                    $barColor = $hasMaxBalance ? ($capacityPercentage >= 90 ? 'danger' : ($capacityPercentage >= 70 ? 'warning' : 'success')) : 'info';
                @endphp

                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('messages.capacity_usage') }}</label>
                    @if($hasMaxBalance)
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-{{ $barColor }}" style="width: {{ $capacityPercentage }}%">
                                {{ number_format($capacityPercentage, 1) }}%
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            {{ __('messages.no_max_balance_set') ?? 'No maximum balance is set for this safe.' }}
                        </div>
                    @endif
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
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header safe-mobile-orange-header">
                <h5 class="mb-0"><i class="bi bi-calendar-day"></i> {{ __('messages.today') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr class="table-success">
                        <td><strong>Income</strong></td>
                        <td class="text-end text-success fw-bold">{{ $currencySymbol }}{{ number_format($todayIncome, 2) }}</td>
                    </tr>
                    <tr class="table-danger">
                        <td><strong>Outcome</strong></td>
                        <td class="text-end text-danger fw-bold">-{{ $currencySymbol }}{{ number_format($todayOutcome, 2) }}</td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>{{ __('messages.net_change') }}</strong></td>
                        <td class="text-end fw-bold">{{ $currencySymbol }}{{ number_format($todayNetChange, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Entries today</strong></td>
                        <td class="text-end">{{ $todayTransactionCount }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="mb-0"><i class="bi bi-speedometer2"></i> {{ __('messages.status') }}</h5>
            </div>
            <div class="card-body">
                @if($hasMaxBalance)
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
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> <strong>{{ __('messages.capacity_usage') }}</strong><br>
                        <small>{{ __('messages.no_max_balance_set') ?? 'This safe has no maximum balance limit.' }}</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="mb-0"><i class="bi bi-arrow-up-circle"></i> Income Tracking</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Total Income: <span class="text-success fw-bold">{{ $currencySymbol }}{{ number_format($totalIncome, 2) }}</span></h6>
                </div>

                @feature('safes.create')
                <button class="btn btn-success btn-sm w-100 mb-3" data-bs-toggle="modal" data-bs-target="#addIncomeModal">
                    <i class="bi bi-plus-circle"></i> Add Income
                </button>
                @endfeature

                @if(count($recentIncomes) > 0)
                    <div class="d-flex mb-2 gap-2">
                        @if(auth()->user()?->canViewMenuItem('downloads'))
                        <button class="btn btn-success btn-sm"
                            id="incomeExportBtn"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#exportPdfModal"
                            data-export-type="income"
                            data-export-title="Income PDF Export">Export PDF</button>
                        @endif
                    </div>
                    <div class="table-responsive" id="incomeTableWrapper" style="max-height: 320px; overflow-y: auto;">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="position: sticky; top: 0; z-index: 2; background: #fff;">Amount</th>
                                    <th style="position: sticky; top: 0; z-index: 2; background: #fff;">Source</th>
                                    <th style="position: sticky; top: 0; z-index: 2; background: #fff;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentIncomes as $income)
                                    <tr class="income-row" style="cursor: pointer;"
                                        data-id="{{ $income->id }}"
                                        data-amount="{{ $income->amount }}"
                                        data-source="{{ $income->source }}"
                                        data-currency_id="{{ $income->currency_id }}"
                                        data-reference="{{ $income->reference }}"
                                        data-notes="{{ $income->notes }}"
                                        data-currency_name="{{ $income->currency?->name ?? '' }}"
                                        data-currency_code="{{ $income->currency?->code ?? $currencySymbol }}"
                                        data-created_at="{{ $income->created_at->format('M d, Y') }}"
                                        data-created_at_iso="{{ $income->created_at->toDateString() }}"
                                        data-update_url="{{ route('safes.income.update', [$safe->id, $income->id]) }}"
                                        data-delete_url="{{ route('safes.income.delete', [$safe->id, $income->id]) }}">
                                        <td>
                                            <strong>{{ $income->currency?->code ?? $currencySymbol }} {{ number_format($income->amount, 2) }}</strong>
                                            @if($income->currency)
                                                <br><small class="text-muted">{{ $income->currency->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $income->source === 'cash' ? 'warning' : 'info' }}">
                                                <i class="bi bi-{{ $income->source === 'cash' ? 'wallet2' : 'bank' }}"></i> {{ ucfirst($income->source) }}
                                            </span>
                                        </td>
                                        <td><small class="text-muted">{{ $income->created_at->format('M d, Y') }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <small>No income records yet</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header" style="background: linear-gradient(135deg, #e74c3c, #ec7063); color: white;">
                <h5 class="mb-0"><i class="bi bi-arrow-down-circle"></i> Outcome Tracking</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6>Total Outcome: <span class="text-danger fw-bold">{{ $currencySymbol }}{{ number_format($totalOutcome, 2) }}</span></h6>
                </div>

                @feature('safes.create')
                <button class="btn btn-danger btn-sm w-100 mb-3" data-bs-toggle="modal" data-bs-target="#addOutcomeModal">
                    <i class="bi bi-plus-circle"></i> Add Outcome
                </button>
                @endfeature

                @if(count($recentOutcomes) > 0)
                    <div class="d-flex mb-2 gap-2">
                        @if(auth()->user()?->canViewMenuItem('downloads'))
                        <button class="btn btn-danger btn-sm"
                            id="outcomeExportBtn"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#exportPdfModal"
                            data-export-type="outcome"
                            data-export-title="Outcome PDF Export">Export PDF</button>
                        @endif
                    </div>
                    <div class="table-responsive" id="outcomeTableWrapper" style="max-height: 320px; overflow-y: auto;">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="position: sticky; top: 0; z-index: 2; background: #fff;">Amount</th>
                                    <th style="position: sticky; top: 0; z-index: 2; background: #fff;">Description</th>
                                    <th style="position: sticky; top: 0; z-index: 2; background: #fff;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOutcomes as $outcome)
                                    <tr class="outcome-row" style="cursor: pointer;"
                                        data-id="{{ $outcome->id }}"
                                        data-amount="{{ $outcome->amount }}"
                                        data-description="{{ $outcome->description }}"
                                        data-currency_id="{{ $outcome->currency_id }}"
                                        data-reference="{{ $outcome->reference }}"
                                        data-currency_name="{{ $outcome->currency?->name ?? '' }}"
                                        data-currency_code="{{ $outcome->currency?->code ?? $currencySymbol }}"
                                        data-created_at="{{ $outcome->created_at->format('M d, Y') }}"
                                        data-created_at_iso="{{ $outcome->created_at->toDateString() }}"
                                        data-supplier_name="{{ $outcome->supplier?->name ?? '' }}"
                                        data-reference_type="{{ $outcome->reference_type }}"
                                        data-update_url="{{ route('safes.outcome.update', [$safe->id, $outcome->id]) }}"
                                        data-delete_url="{{ route('safes.outcome.delete', [$safe->id, $outcome->id]) }}">
                                        <td>
                                            <strong>{{ $outcome->currency?->code ?? $currencySymbol }} {{ number_format($outcome->amount, 2) }}</strong>
                                            @if($outcome->currency)
                                                <br><small class="text-muted">{{ $outcome->currency->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($outcome->description ?? 'N/A', 20) }}</small>
                                            @if($outcome->reference_type === 'supplier' && $outcome->supplier)
                                                <br><small class="text-danger">Supplier: {{ $outcome->supplier->name }}</small>
                                            @endif
                                        </td>
                                        <td><small class="text-muted">{{ $outcome->created_at->format('M d, Y') }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <small>No outcome records yet</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #3498db, #5dade2); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-currency-exchange"></i> Multi-Currency Management</h5>
                    @feature('safes.create')
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addCurrencyModal">
                        <i class="bi bi-plus-circle"></i> Add Currency
                    </button>
                    @endfeature
                </div>
            </div>
            <div class="card-body">
                @if(count($currencies) > 0)
                    <div class="row">
                        @foreach($currencies as $currency)
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light border">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $currency->code }}</h6>
                                        <p class="text-muted small mb-2">{{ $currency->name }}</p>
                                        <h5 class="text-primary">{{ number_format($currency->balance, 2) }} {{ $currency->code }}</h5>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> No currencies added yet. Click "Add Currency" to start tracking different currencies.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Income Detail Modal -->
<div class="modal fade" id="incomeDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="modal-title"><i class="bi bi-arrow-up-circle"></i> Income Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- Detail View -->
            <div id="incomeDetailView">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Amount</label>
                            <h5 id="incomeDetailAmount"></h5>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Source</label>
                            <p id="incomeDetailSource"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Date</label>
                            <p id="incomeDetailDate"></p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Currency</label>
                            <p id="incomeDetailCurrency"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Reference</label>
                        <p id="incomeDetailReference"></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Notes</label>
                        <p id="incomeDetailNotes"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    @feature('safes.edit')
                    <button type="button" class="btn btn-primary" id="incomeDetailEditBtn">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    @endfeature
                    @feature('safes.delete')
                    <button type="button" class="btn btn-danger" id="incomeDetailDeleteBtn">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    @endfeature
                </div>
            </div>

            <!-- Edit Form View -->
            <div id="incomeEditView" style="display: none;">
                <form method="POST" id="incomeDetailEditForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Amount *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Source *</label>
                            <select name="source" class="form-select" required>
                                <option value="">Select Source</option>
                                <option value="cash">Cash</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency (Optional)</label>
                            <select name="currency_id" class="form-select">
                                <option value="">Select Currency</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference (Optional)</label>
                            <input type="text" name="reference" class="form-control" placeholder="Invoice #, Check #, etc.">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes about this income..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="incomeEditCancelBtn">Cancel</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Update Income</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Outcome Detail Modal -->
<div class="modal fade" id="outcomeDetailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c, #ec7063); color: white;">
                <h5 class="modal-title"><i class="bi bi-arrow-down-circle"></i> Outcome Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Detail View -->
            <div id="outcomeDetailView">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Amount</label>
                            <h5 id="outcomeDetailAmount"></h5>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Date</label>
                            <p id="outcomeDetailDate"></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Currency</label>
                            <p id="outcomeDetailCurrency"></p>
                        </div>
                        <div class="col-6" id="outcomeSupplierWrapper" style="display: none;">
                            <label class="text-muted small">Supplier</label>
                            <p id="outcomeDetailSupplier"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Description</label>
                        <p id="outcomeDetailDescription"></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Reference</label>
                        <p id="outcomeDetailReference"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    @feature('safes.edit')
                    <button type="button" class="btn btn-warning" id="outcomeDetailEditBtn">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    @endfeature
                    @feature('safes.delete')
                    <button type="button" class="btn btn-danger" id="outcomeDetailDeleteBtn">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                    @endfeature
                </div>
            </div>

            <!-- Edit Form View -->
            <div id="outcomeEditView" style="display: none;">
                <form method="POST" id="outcomeDetailEditForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Amount *</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Currency (Optional)</label>
                            <select name="currency_id" class="form-select">
                                <option value="">Select Currency</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Outcome description..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference (Optional)</label>
                            <input type="text" name="reference" class="form-control" placeholder="Invoice #, Reference, etc.">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="outcomeEditCancelBtn">Cancel</button>
                        <button type="submit" class="btn btn-warning"><i class="bi bi-check-circle"></i> Update Outcome</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addIncomeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="modal-title"><i class="bi bi-arrow-up-circle"></i> Add Income</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('safes.add-income', $safe->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Source *</label>
                        <select name="source" class="form-select" required>
                            <option value="">Select Source</option>
                            <option value="cash">Cash</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Currency (Optional)</label>
                        <select name="currency_id" class="form-select">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference (Optional)</label>
                        <input type="text" name="reference" class="form-control" placeholder="Invoice #, Check #, etc.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes about this income..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Record Income</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addOutcomeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c, #ec7063); color: white;">
                <h5 class="modal-title"><i class="bi bi-arrow-down-circle"></i> Add Outcome</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('safes.add-outcome', $safe->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small><strong>Current Balance:</strong> {{ $currencySymbol }}{{ number_format($safe->balance, 2) }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Amount *</label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0" max="{{ $safe->balance }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Currency (Optional)</label>
                        <select name="currency_id" class="form-select">
                            <option value="">Select Currency</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference Type</label>
                        <select name="reference_type" id="outcomeReferenceType" class="form-select">
                            <option value="general">General</option>
                            <option value="supplier">Supplier</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="outcomeSupplierWrapper">
                        <label class="form-label">Supplier (With Outstanding Balance)</label>
                        <select name="supplier_id" id="outcomeSupplierId" class="form-select">
                            <option value="">Select supplier</option>
                            @foreach(($suppliersWithOutstanding ?? []) as $supplier)
                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->name }} - {{ $currencySymbol }}{{ number_format($supplier->outstanding_amount, 2) }} due
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="What is this outcome for? (e.g., Supplies, Maintenance, etc.)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference (Optional)</label>
                        <input type="text" name="reference" class="form-control" placeholder="Reference number or code">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-check-circle"></i> Record Outcome</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addCurrencyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #3498db, #5dade2); color: white;">
                <h5 class="modal-title"><i class="bi bi-currency-exchange"></i> Add Currency</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('safes.add-currency', $safe->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Currency Code *</label>
                        <input type="text" name="code" class="form-control text-uppercase" placeholder="USD, EUR, GBP, etc." maxlength="3" required>
                        <small class="text-muted">Enter 3-letter currency code (e.g., USD, EUR, GBP)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Currency Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="Dollar, Euro, British Pound, etc." required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Currency</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(auth()->user()?->canViewMenuItem('downloads'))
<div class="modal fade" id="exportPdfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1f2937, #111827); color: white;">
                <h5 class="modal-title" id="exportPdfModalTitle"><i class="bi bi-file-earmark-pdf"></i> PDF Export</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportPdfForm" method="GET" action="{{ route('safes.export', $safe->id) }}">
                <div class="modal-body">
                    <input type="hidden" name="type" id="exportPdfType" value="income">
                    <div class="alert alert-info">
                        Select a date range to export only filtered records. Leave both dates empty to export all records.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" id="exportPdfFromDate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" id="exportPdfToDate" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download"></i> Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
(function () {
    const referenceType = document.getElementById('outcomeReferenceType');
    const supplierWrapper = document.getElementById('outcomeSupplierWrapper');
    const supplierSelect = document.getElementById('outcomeSupplierId');

    // Toggle supplier field in add outcome modal
    if (referenceType && supplierWrapper && supplierSelect) {
        function toggleSupplierField() {
            const isSupplier = referenceType.value === 'supplier';
            supplierWrapper.classList.toggle('d-none', !isSupplier);
            supplierSelect.required = isSupplier;
            if (!isSupplier) supplierSelect.value = '';
        }
        referenceType.addEventListener('change', toggleSupplierField);
        toggleSupplierField();
    }

    // Initialize modals - wait for Bootstrap to be available
    let incomeDetailModal, outcomeDetailModal;
    
    function initModals() {
        try {
            incomeDetailModal = new bootstrap.Modal(document.getElementById('incomeDetailModal'));
            outcomeDetailModal = new bootstrap.Modal(document.getElementById('outcomeDetailModal'));
            console.log('Modals initialized successfully');
        } catch (error) {
            console.error('Failed to initialize modals:', error);
        }
    }
    
    // Initialize modals when ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModals);
    } else {
        initModals();
    }

    // Handle income row click
    document.addEventListener('click', function (e) {
        const row = e.target.closest('.income-row');
        if (!row) return;

        const amount = row.getAttribute('data-amount');
        const source = row.getAttribute('data-source');
        const currencyId = row.getAttribute('data-currency_id');
        const currencyCode = row.getAttribute('data-currency_code');
        const currencyName = row.getAttribute('data-currency_name');
        const reference = row.getAttribute('data-reference');
        const notes = row.getAttribute('data-notes');
        const createdAt = row.getAttribute('data-created_at');
        const updateUrl = row.getAttribute('data-update_url');
        const deleteUrl = row.getAttribute('data-delete_url');

        // Populate detail modal
        document.getElementById('incomeDetailAmount').textContent = currencyCode + ' ' + parseFloat(amount).toFixed(2);
        document.getElementById('incomeDetailSource').textContent = source.charAt(0).toUpperCase() + source.slice(1);
        document.getElementById('incomeDetailDate').textContent = createdAt;
        document.getElementById('incomeDetailCurrency').textContent = currencyName || 'N/A';
        document.getElementById('incomeDetailReference').textContent = reference || 'N/A';
        document.getElementById('incomeDetailNotes').textContent = notes || 'N/A';

        // Store data for editing
        const detailModal = document.getElementById('incomeDetailModal');
        detailModal.dataset.amount = amount;
        detailModal.dataset.source = source;
        detailModal.dataset.currencyId = currencyId || '';
        detailModal.dataset.reference = reference || '';
        detailModal.dataset.notes = notes || '';
        detailModal.dataset.updateUrl = updateUrl;
        detailModal.dataset.deleteUrl = deleteUrl;

        // Reset to detail view
        document.getElementById('incomeDetailView').style.display = 'block';
        document.getElementById('incomeEditView').style.display = 'none';

        // Handle edit button
        document.getElementById('incomeDetailEditBtn').onclick = function () {
            const detailModal = document.getElementById('incomeDetailModal');
            const editForm = document.getElementById('incomeDetailEditForm');
            editForm.action = detailModal.dataset.updateUrl;
            editForm.querySelector('input[name="amount"]').value = detailModal.dataset.amount;
            editForm.querySelector('select[name="source"]').value = detailModal.dataset.source;
            editForm.querySelector('select[name="currency_id"]').value = detailModal.dataset.currencyId;
            editForm.querySelector('input[name="reference"]').value = detailModal.dataset.reference;
            editForm.querySelector('textarea[name="notes"]').value = detailModal.dataset.notes;
            
            // Switch to edit view
            document.getElementById('incomeDetailView').style.display = 'none';
            document.getElementById('incomeEditView').style.display = 'block';
        };

        // Handle delete button
        document.getElementById('incomeDetailDeleteBtn').onclick = function () {
            if (confirm('Are you sure you want to delete this income record?')) {
                const detailModal = document.getElementById('incomeDetailModal');
                if (incomeDetailModal) incomeDetailModal.hide();
                setTimeout(() => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = detailModal.dataset.deleteUrl;
                    form.innerHTML = '<input type="hidden" name="_method" value="DELETE">' +
                                    '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                    document.body.appendChild(form);
                    form.submit();
                }, 300);
            }
        };

        // Handle cancel button in edit view
        document.getElementById('incomeEditCancelBtn').onclick = function () {
            document.getElementById('incomeDetailView').style.display = 'block';
            document.getElementById('incomeEditView').style.display = 'none';
        };

        if (incomeDetailModal) incomeDetailModal.show();
    });

    // Handle outcome row click
    document.addEventListener('click', function (e) {
        const row = e.target.closest('.outcome-row');
        if (!row) return;

        const amount = row.getAttribute('data-amount');
        const description = row.getAttribute('data-description');
        const currencyId = row.getAttribute('data-currency_id');
        const currencyCode = row.getAttribute('data-currency_code');
        const currencyName = row.getAttribute('data-currency_name');
        const reference = row.getAttribute('data-reference');
        const createdAt = row.getAttribute('data-created_at');
        const supplierName = row.getAttribute('data-supplier_name');
        const referenceType = row.getAttribute('data-reference_type');
        const updateUrl = row.getAttribute('data-update_url');
        const deleteUrl = row.getAttribute('data-delete_url');

        // Populate detail modal
        document.getElementById('outcomeDetailAmount').textContent = currencyCode + ' ' + parseFloat(amount).toFixed(2);
        document.getElementById('outcomeDetailDate').textContent = createdAt;
        document.getElementById('outcomeDetailCurrency').textContent = currencyName || 'N/A';
        document.getElementById('outcomeDetailDescription').textContent = description || 'N/A';
        document.getElementById('outcomeDetailReference').textContent = reference || 'N/A';

        // Show supplier info if applicable
        const supplierWrapper = document.getElementById('outcomeSupplierWrapper');
        if (referenceType === 'supplier' && supplierName) {
            supplierWrapper.style.display = 'block';
            document.getElementById('outcomeDetailSupplier').textContent = supplierName;
        } else {
            supplierWrapper.style.display = 'none';
        }

        // Store data for editing
        const detailModal = document.getElementById('outcomeDetailModal');
        detailModal.dataset.amount = amount;
        detailModal.dataset.currencyId = currencyId || '';
        detailModal.dataset.description = description || '';
        detailModal.dataset.reference = reference || '';
        detailModal.dataset.updateUrl = updateUrl;
        detailModal.dataset.deleteUrl = deleteUrl;

        // Reset to detail view
        document.getElementById('outcomeDetailView').style.display = 'block';
        document.getElementById('outcomeEditView').style.display = 'none';

        // Handle edit button
        document.getElementById('outcomeDetailEditBtn').onclick = function () {
            const detailModal = document.getElementById('outcomeDetailModal');
            const editForm = document.getElementById('outcomeDetailEditForm');
            editForm.action = detailModal.dataset.updateUrl;
            editForm.querySelector('input[name="amount"]').value = detailModal.dataset.amount;
            editForm.querySelector('select[name="currency_id"]').value = detailModal.dataset.currencyId;
            editForm.querySelector('textarea[name="description"]').value = detailModal.dataset.description;
            editForm.querySelector('input[name="reference"]').value = detailModal.dataset.reference;
            
            // Switch to edit view
            document.getElementById('outcomeDetailView').style.display = 'none';
            document.getElementById('outcomeEditView').style.display = 'block';
        };

        // Handle delete button
        document.getElementById('outcomeDetailDeleteBtn').onclick = function () {
            if (confirm('Are you sure you want to delete this outcome record?')) {
                const detailModal = document.getElementById('outcomeDetailModal');
                if (outcomeDetailModal) outcomeDetailModal.hide();
                setTimeout(() => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = detailModal.dataset.deleteUrl;
                    form.innerHTML = '<input type="hidden" name="_method" value="DELETE">' +
                                    '<input type="hidden" name="_token" value="{{ csrf_token() }}">';
                    document.body.appendChild(form);
                    form.submit();
                }, 300);
            }
        };

        // Handle cancel button in edit view
        document.getElementById('outcomeEditCancelBtn').onclick = function () {
            document.getElementById('outcomeDetailView').style.display = 'block';
            document.getElementById('outcomeEditView').style.display = 'none';
        };

        if (outcomeDetailModal) outcomeDetailModal.show();
    });

    function syncExportModal(button) {
        const exportType = button.getAttribute('data-export-type') || 'income';
        const title = button.getAttribute('data-export-title') || 'PDF Export';

        const modalTitle = document.getElementById('exportPdfModalTitle');
        const modalType = document.getElementById('exportPdfType');
        const modalFrom = document.getElementById('exportPdfFromDate');
        const modalTo = document.getElementById('exportPdfToDate');

        if (modalTitle) modalTitle.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> ' + title;
        if (modalType) modalType.value = exportType;
        if (modalFrom) modalFrom.value = '';
        if (modalTo) modalTo.value = '';
    }

    // Income filters & export
    const incomeExportBtn = document.getElementById('incomeExportBtn');
    if (incomeExportBtn) incomeExportBtn.addEventListener('click', function () {
        syncExportModal(incomeExportBtn);
    });

    // Outcome filters & export
    const outcomeExportBtn = document.getElementById('outcomeExportBtn');
    if (outcomeExportBtn) outcomeExportBtn.addEventListener('click', function () {
        syncExportModal(outcomeExportBtn);
    });
})();
</script>
@endsection
