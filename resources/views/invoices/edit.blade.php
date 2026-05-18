@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('Edit Invoice') }}</h3>

    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Customer') }}</label>
                    <select name="customer_id" class="form-control">
                        <option value="">{{ __('Select customer') }}</option>
                        @foreach($customers as $id => $name)
                        <option value="{{ $id }}" {{ $invoice->customer_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Date') }}</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', $invoice->date?->format('Y-m-d')) }}">
                </div>
            </div>
        </div>

        @include('branches.partials.multi-select', ['branches' => $branches ?? [], 'selectedBranchIds' => $selectedBranchIds ?? []])

        <!-- Line Items Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('Line Items') }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm" id="itemsTable">
                    <thead>
                        <tr>
                            <th>{{ __('Product') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Unit Price') }}</th>
                            <th>{{ __('Line Total') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @forelse($invoice->items as $index => $item)
                        <tr class="item-row" data-index="{{ $index }}">
                            <td>
                                <input type="text" name="items[{{ $index }}][product_id]" class="form-control form-control-sm product-select" placeholder="{{ __('Product') }}" value="{{ $item->product_id }}">
                            </td>
                            <td>
                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control form-control-sm quantity" placeholder="0" min="1" value="{{ $item->quantity }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control form-control-sm unit-price" placeholder="0.00" min="0" value="{{ $item->unit_price }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control form-control-sm line-total" readonly value="{{ $item->line_total }}">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr class="item-row" data-index="0">
                            <td>
                                <input type="text" name="items[0][product_id]" class="form-control form-control-sm product-select" placeholder="{{ __('Product') }}">
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-sm quantity" placeholder="0" min="1" value="1">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="items[0][unit_price]" class="form-control form-control-sm unit-price" placeholder="0.00" min="0" value="0.00">
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control form-control-sm line-total" readonly value="0.00">
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                    <i class="bi bi-plus"></i> {{ __('Add Item') }}
                </button>
            </div>
        </div>

        <!-- Totals Section -->
        <div class="row">
            <div class="col-md-4 offset-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6"><strong>{{ __('Sub Total:') }}</strong></div>
                            <div class="col-6 text-end"><span id="subTotal">{{ number_format($invoice->sub_total, 2) }}</span></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6"><strong>{{ __('Tax (15%):') }}</strong></div>
                            <div class="col-6 text-end"><span id="taxAmount">{{ number_format($invoice->tax, 2) }}</span></div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6"><h5>{{ __('Total:') }}</h5></div>
                            <div class="col-6 text-end"><h5 id="totalAmount">{{ number_format($invoice->total, 2) }}</h5></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">{{ __('Save Invoice') }}</button>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>

<script>
let itemCount = {{ $invoice->items->count() }};

function updateTotals() {
    let subTotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const lineTotal = quantity * unitPrice;
        row.querySelector('.line-total').value = lineTotal.toFixed(2);
        subTotal += lineTotal;
    });

    const tax = subTotal * 0.15;
    const total = subTotal + tax;

    document.getElementById('subTotal').textContent = subTotal.toFixed(2);
    document.getElementById('taxAmount').textContent = tax.toFixed(2);
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

document.getElementById('addItemBtn').addEventListener('click', function() {
    const tbody = document.getElementById('itemsBody');
    const newRow = document.createElement('tr');
    newRow.classList.add('item-row');
    newRow.setAttribute('data-index', itemCount);
    newRow.innerHTML = `
        <td>
            <input type="text" name="items[${itemCount}][product_id]" class="form-control form-control-sm product-select" placeholder="{{ __('Product') }}">
        </td>
        <td>
            <input type="number" name="items[${itemCount}][quantity]" class="form-control form-control-sm quantity" placeholder="0" min="1" value="1">
        </td>
        <td>
            <input type="number" step="0.01" name="items[${itemCount}][unit_price]" class="form-control form-control-sm unit-price" placeholder="0.00" min="0" value="0.00">
        </td>
        <td>
            <input type="number" step="0.01" class="form-control form-control-sm line-total" readonly value="0.00">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-item">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    itemCount++;
    attachRowListeners(newRow);
});

function attachRowListeners(row) {
    row.querySelector('.quantity').addEventListener('input', updateTotals);
    row.querySelector('.unit-price').addEventListener('input', updateTotals);
    row.querySelector('.remove-item').addEventListener('click', function() {
        row.remove();
        updateTotals();
    });
}

// Attach listeners to all rows
document.querySelectorAll('.item-row').forEach(row => attachRowListeners(row));

// Update totals on form submission
document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    // Remove empty rows before submission
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = row.querySelector('.quantity').value;
        const unitPrice = row.querySelector('.unit-price').value;
        if (!quantity || !unitPrice) {
            row.remove();
        }
    });
});

// Initialize totals display
updateTotals();
</script>
@endsection

