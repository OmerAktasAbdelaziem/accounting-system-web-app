<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">{{ __('messages.branch_debts') }}</h5>
            <p class="text-muted mb-0">{{ __('messages.branch_debts_details_text', ['branch' => $branch->name]) }}</p>
        </div>
        <div class="text-end">
            <div class="text-muted small">{{ __('messages.total_suppliers') }}: {{ $branchSuppliers->count() }}</div>
            <div class="fw-bold text-danger">{{ __('messages.total_outstanding') }}: {{ $currencySymbol }}{{ number_format((float) ($branchOutstandingTotal ?? 0), 2) }}</div>
        </div>
    </div>

    @if($branchSuppliers->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-sm table-tight align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('messages.supplier') }}</th>
                        <th class="text-end">{{ __('messages.opening_balance') }}</th>
                        <th class="text-end">{{ __('messages.total_purchased') }}</th>
                        <th class="text-end">{{ __('messages.total_paid') }}</th>
                        <th class="text-end">{{ __('messages.outstanding') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branchSuppliers as $supplier)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $supplier->name }}</div>
                            </td>
                            <td class="text-end text-secondary">{{ $currencySymbol }}{{ number_format((float) ($supplier->opening_balance_amount ?? 0), 2) }}</td>
                            <td class="text-end text-primary">{{ $currencySymbol }}{{ number_format((float) ($supplier->branch_total_purchased ?? 0), 2) }}</td>
                            <td class="text-end text-success">{{ $currencySymbol }}{{ number_format((float) ($supplier->branch_total_paid ?? 0), 2) }}</td>
                            <td class="text-end text-danger">{{ $currencySymbol }}{{ number_format((float) ($supplier->outstanding_amount ?? 0), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5 text-muted">
            {{ __('messages.no_branch_debts') }}
        </div>
    @endif
</div>
