@extends('layouts.modern')

@section('content')
<style>
    @media (max-width: 768px) {
        .d-flex.flex-wrap.justify-content-between.align-items-center.gap-2.mb-4,
        .card-body .row.g-3.align-items-end,
        .d-flex.justify-content-between.align-items-center.mt-3,
        .card-header.d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px;
        }

        .d-flex.flex-wrap.justify-content-between.align-items-center.gap-2.mb-4 .btn,
        .card-body .row.g-3.align-items-end .btn,
        .card-body .row.g-3.align-items-end input,
        .card-body .row.g-3.align-items-end select,
        .d-flex.justify-content-between.align-items-center.mt-3 .btn {
            width: 100%;
        }

        .row > .col-lg-4,
        .row > .col-lg-8,
        .row > .col-md-4 {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            min-width: 760px;
        }
    }

    @media (max-width: 576px) {
        h3 {
            font-size: 22px;
        }

        .table {
            min-width: 640px;
        }
    }
</style>

<style>
    .white-header,
    .white-header * {
        color: #fff !important;
    }
</style>

@php
    $selectedBranch = $selectedBranchId ? $branches->firstWhere('id', $selectedBranchId) : null;
@endphp
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h3 class="mb-1">{{ $supplier->name }}</h3>
            <div class="d-flex gap-2 align-items-center">
                <div class="text-muted">{{ __('messages.supplier_ledger') }}</div>
                @if($selectedBranch)
                    <span class="badge bg-info text-dark">{{ __('messages.showing_branch', ['branch' => $selectedBranch->name]) }}</span>
                @else
                    <span class="badge bg-secondary">{{ __('messages.showing_all_branches') }}</span>
                @endif
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user()?->canViewMenuItem('downloads'))
                <a href="{{ route('suppliers.statement-pdf', array_merge(['supplier' => $supplier], request()->only(['branch_id', 'lang']))) }}" class="btn btn-outline-danger">{{ __('messages.export_pdf') }}</a>
            @endif
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">{{ __('messages.back') }}</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('suppliers.show', $supplier) }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.branch_filter') }}</label>
                    <select name="branch_id" class="form-select">
                        <option value="">{{ __('messages.all_branches') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) $selectedBranchId === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 d-flex gap-2">
                    <button class="btn btn-primary">{{ __('messages.apply_filter') }}</button>
                    @if($selectedBranchId)
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-outline-secondary">{{ __('messages.clear') }}</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">{{ __('messages.opening_balance') }}</small>
                    <h4 class="mb-0">{{ $currencySymbol }}{{ number_format((float) $openingBalance, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">{{ __('messages.total_purchased') }}</small>
                    <h4 class="mb-0 text-primary">{{ $currencySymbol }}{{ number_format($totalPurchased, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">{{ __('messages.total_paid') }}</small>
                    <h4 class="mb-0 text-success">{{ $currencySymbol }}{{ number_format($totalPaid, 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">{{ __('messages.outstanding') }}</small>
                    <h4 class="mb-0 {{ $outstanding > 0 ? 'text-danger' : 'text-success' }}">{{ $currencySymbol }}{{ number_format($outstanding, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 white-header">{{ __('messages.record_purchase') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('suppliers.purchases.store', $supplier) }}">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('messages.purchase_date') }}</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', now()->toDateString()) }}" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">{{ __('messages.note') }}</label>
                                <input type="text" name="note" class="form-control" value="{{ old('note') }}" placeholder="{{ __('messages.optional_note') }}">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle purchase-items-table">
                                <thead>
                                    <tr>
                                        <th style="width: 36%;">{{ __('messages.product_name') }}</th>
                                        <th style="width: 16%;">{{ __('messages.weight_kg') }}</th>
                                        <th style="width: 18%;">{{ __('messages.unit_price') }}</th>
                                        <th style="width: 18%;">{{ __('messages.line_total') }}</th>
                                        <th style="width: 12%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="product_name[]" class="form-control" required></td>
                                        <td><input type="number" name="weight[]" class="form-control weight-input" step="0.001" min="0.001" required></td>
                                        <td><input type="number" name="unit_price[]" class="form-control price-input" step="0.01" min="0.01" required></td>
                                        <td><input type="text" class="form-control line-total" readonly value="0.00"></td>
                                        <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">{{ __('messages.remove') }}</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="btn btn-outline-primary add-row">+ {{ __('messages.add_product_row') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('messages.save_purchase') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0 white-header">{{ __('messages.record_payment') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('suppliers.payments.store', $supplier) }}">
                        @csrf
                        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.payment_date') }}</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.amount') }}</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.note') }}</label>
                            <textarea name="note" rows="3" class="form-control" placeholder="{{ __('messages.optional_note') }}">{{ old('note') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">{{ __('messages.save_payment') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 white-header">{{ __('messages.transaction_timeline') }}</h5>
            <small class="text-muted">{{ __('messages.showing_latest_entries', ['count' => count($timeline)]) }}</small>
        </div>
        <div class="card-body">
            @forelse($timeline as $entry)
                @if($entry['kind'] === 'purchase')
                    @php $purchase = $entry['model']; @endphp
                    <div class="border rounded-3 p-3 mb-3 bg-light">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div>
                                <span class="badge bg-primary">{{ __('messages.purchase') }}</span>
                                <span class="ms-2 text-muted">{{ optional($purchase->branch)->name ?? __('messages.no_branch') }}</span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span>{{ $purchase->purchase_date->format('Y-m-d') }}</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPurchaseModal{{ $purchase->id }}">{{ __('messages.edit') }}</button>
                                <form method="POST" action="{{ route('suppliers.purchases.destroy', [$supplier, $purchase]) }}" onsubmit="return confirm('{{ __('messages.delete_purchase_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                                    <button class="btn btn-sm btn-outline-danger">{{ __('messages.delete') }}</button>
                                </form>
                            </div>
                        </div>
                        <div class="mb-2"><strong>{{ __('messages.total') }}:</strong> {{ $currencySymbol }}{{ number_format((float) $purchase->total_amount, 2) }}</div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.product') }}</th>
                                        <th>{{ __('messages.weight') }}</th>
                                        <th>{{ __('messages.unit_price') }}</th>
                                        <th>{{ __('messages.line_total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->items as $item)
                                        <tr>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ number_format((float) $item->weight, 3) }} kg</td>
                                            <td>{{ $currencySymbol }}{{ number_format((float) $item->unit_price, 2) }}</td>
                                            <td>{{ $currencySymbol }}{{ number_format((float) $item->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($purchase->note)
                            <div class="small text-muted mt-2">{{ __('messages.note') }}: {{ $purchase->note }}</div>
                        @endif
                    </div>

                    <div class="modal fade" id="editPurchaseModal{{ $purchase->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('suppliers.purchases.update', [$supplier, $purchase]) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('messages.edit_purchase') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">{{ __('messages.purchase_date') }}</label>
                                                <input type="date" name="purchase_date" class="form-control" value="{{ $purchase->purchase_date->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-8">
                                                <label class="form-label">{{ __('messages.note') }}</label>
                                                <input type="text" name="note" class="form-control" value="{{ $purchase->note }}">
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle purchase-items-table">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('messages.product_name') }}</th>
                                                        <th>{{ __('messages.weight_kg') }}</th>
                                                        <th>{{ __('messages.unit_price') }}</th>
                                                        <th>{{ __('messages.line_total') }}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchase->items as $item)
                                                        <tr>
                                                            <td><input type="text" name="product_name[]" class="form-control" value="{{ $item->product_name }}" required></td>
                                                            <td><input type="number" name="weight[]" class="form-control weight-input" step="0.001" min="0.001" value="{{ number_format((float) $item->weight, 3) }}" required></td>
                                                            <td><input type="number" name="unit_price[]" class="form-control price-input" step="0.01" min="0.01" value="{{ number_format((float) $item->unit_price, 2) }}" required></td>
                                                            <td><input type="text" class="form-control line-total" readonly value="{{ number_format((float) $item->line_total, 2) }}"></td>
                                                            <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">{{ __('messages.remove') }}</button></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary add-row">+ {{ __('messages.add_product_row') }}</button>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    @php $payment = $entry['model']; @endphp
                    <div class="border rounded-3 p-3 mb-3" style="background:#eefaf1;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div>
                                <span class="badge bg-success">{{ __('messages.payment') }}</span>
                                <span class="ms-2 text-muted">{{ optional($payment->branch)->name ?? __('messages.no_branch') }}</span>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span>{{ $payment->payment_date->format('Y-m-d') }}</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editPaymentModal{{ $payment->id }}">{{ __('messages.edit') }}</button>
                                <form method="POST" action="{{ route('suppliers.payments.destroy', [$supplier, $payment]) }}" onsubmit="return confirm('{{ __('messages.delete_payment_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                                    <button class="btn btn-sm btn-outline-danger">{{ __('messages.delete') }}</button>
                                </form>
                            </div>
                        </div>
                        <div><strong>{{ __('messages.paid') }}:</strong> {{ $currencySymbol }}{{ number_format((float) $payment->amount, 2) }}</div>
                        @if($payment->note)
                            <div class="small text-muted mt-1">{{ __('messages.note') }}: {{ $payment->note }}</div>
                        @endif
                    </div>

                    <div class="modal fade" id="editPaymentModal{{ $payment->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('suppliers.payments.update', [$supplier, $payment]) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('messages.edit_payment') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="branch_id" value="{{ $selectedBranchId }}">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('messages.payment_date') }}</label>
                                            <input type="date" name="payment_date" class="form-control" value="{{ $payment->payment_date->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('messages.amount') }}</label>
                                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="{{ number_format((float) $payment->amount, 2) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('messages.note') }}</label>
                                            <textarea name="note" rows="3" class="form-control">{{ $payment->note }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-muted">{{ __('messages.no_supplier_transactions_yet') }}</div>
            @endforelse
        </div>
    </div>
</div>

<script>
(function () {
    function bindTable(table) {
        const tbody = table.querySelector('tbody');
        const addRowBtn = table.closest('form')?.querySelector('.add-row');

        function recalcRow(row) {
            const weight = parseFloat(row.querySelector('.weight-input')?.value || '0');
            const price = parseFloat(row.querySelector('.price-input')?.value || '0');
            row.querySelector('.line-total').value = (weight * price).toFixed(2);
        }

        function bindRow(row) {
            row.querySelectorAll('.weight-input, .price-input').forEach((input) => {
                input.addEventListener('input', () => recalcRow(row));
            });

            const removeBtn = row.querySelector('.remove-row');
            if (removeBtn) {
                removeBtn.addEventListener('click', () => {
                    if (tbody.querySelectorAll('tr').length > 1) {
                        row.remove();
                    }
                });
            }

            recalcRow(row);
        }

        tbody.querySelectorAll('tr').forEach(bindRow);

        if (addRowBtn) {
            addRowBtn.addEventListener('click', () => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><input type="text" name="product_name[]" class="form-control" required></td>
                    <td><input type="number" name="weight[]" class="form-control weight-input" step="0.001" min="0.001" required></td>
                    <td><input type="number" name="unit_price[]" class="form-control price-input" step="0.01" min="0.01" required></td>
                    <td><input type="text" class="form-control line-total" readonly value="0.00"></td>
                    <td><button type="button" class="btn btn-outline-danger btn-sm remove-row">Remove</button></td>
                `;
                tbody.appendChild(row);
                bindRow(row);
            });
        }
    }

    document.querySelectorAll('.purchase-items-table').forEach(bindTable);
})();
</script>
@endsection
