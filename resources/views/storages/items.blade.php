@extends('layouts.modern')

@section('title', __('messages.storage_items'))

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">{{ $storage->name }}</h3>
            <div class="text-muted">{{ $storage->location }} | {{ __('messages.storage_type') }}: <strong>{{ __('messages.' . $storage->storage_type) }}</strong></div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('storages.transferHistory', $storage->id) }}" class="btn btn-outline-info">Transfer History</a>
            <a href="{{ route('storages.index') }}" class="btn btn-outline-secondary">Back</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">+ Add Transaction</button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Transaction Entries</small>
                    <h4 class="mb-0">{{ $summary['entry_count'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Total Quantity</small>
                    <h4 class="mb-0">{{ number_format($summary['total_quantity'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Total Weight</small>
                    <h4 class="mb-0">{{ number_format($summary['total_weight'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <small class="text-muted d-block">Total Value</small>
                    <h4 class="mb-0">{{ $currencySymbol ?? '$' }}{{ number_format($summary['total_value'], 2) }}</h4>
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
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Weight</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><strong>{{ $item->product_name }}</strong></td>
                                <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                <td>{{ number_format((float) $item->weight, 2) }}</td>
                                <td>{{ $currencySymbol ?? '$' }}{{ number_format((float) $item->unit_price, 2) }}</td>
                                <td>{{ $currencySymbol ?? '$' }}{{ number_format((float) $item->total_price, 2) }}</td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-success transfer-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#transferItemModal"
                                            data-item-id="{{ $item->id }}"
                                            data-product-name="{{ $item->product_name }}"
                                            data-quantity="{{ $item->quantity }}"
                                            data-weight="{{ $item->weight }}"
                                            data-unit-price="{{ $item->unit_price }}"
                                        >Transfer</button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editItemModal"
                                            data-item-id="{{ $item->id }}"
                                            data-product-name="{{ $item->product_name }}"
                                            data-quantity="{{ $item->quantity }}"
                                            data-weight="{{ $item->weight }}"
                                            data-unit-price="{{ $item->unit_price }}"
                                        >Edit</button>
                                        <form method="POST" action="{{ route('storages.destroyItem', $item->id) }}" onsubmit="return confirm('Delete this transaction?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">No storage transactions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($items->hasPages())
            <div class="card-footer bg-white">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('storages.storeItem', $storage->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Storage Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Weight</label>
                            <input type="number" name="weight" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price of Product</label>
                            <input type="number" name="unit_price" class="form-control unit-price-input" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Total Price</label>
                            <input type="text" class="form-control total-price-input" readonly value="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="editItemForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Storage Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" id="editProductName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" id="editQuantity" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Weight</label>
                            <input type="number" name="weight" id="editWeight" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price of Product</label>
                            <input type="number" name="unit_price" id="editUnitPrice" class="form-control unit-price-input" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Total Price</label>
                            <input type="text" class="form-control total-price-input" readonly value="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="transferItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('storages.transfer', $storage->id) }}" id="transferItemForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Transfer Between Storages</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="item_id" id="transferItemId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" id="transferProductName" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Available Quantity</label>
                            <input type="text" id="transferAvailableQuantity" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Available Weight</label>
                            <input type="text" id="transferAvailableWeight" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">To Storage</label>
                            <select name="to_storage_id" class="form-select" required>
                                <option value="">Choose storage</option>
                                @foreach($otherStorages as $otherStorage)
                                    <option value="{{ $otherStorage->id }}">{{ $otherStorage->name }} - {{ $otherStorage->location }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transfer Quantity</label>
                            <input type="number" name="quantity" id="transferQuantity" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transfer Weight</label>
                            <input type="number" name="weight" id="transferWeight" class="form-control" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Unit Price</label>
                            <input type="text" id="transferUnitPrice" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Price</label>
                            <input type="text" id="transferTotalPrice" class="form-control" readonly value="0.00">
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Total price is calculated as transfer quantity x unit price.</small>
                            <div id="transferError" class="text-danger small mt-1"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    function recalcTotal(quantityInput, unitPriceInput, totalOutput) {
        const quantity = parseFloat(quantityInput.value || '0');
        const unitPrice = parseFloat(unitPriceInput.value || '0');
        totalOutput.value = (quantity * unitPrice).toFixed(2);
    }

    const addModal = document.getElementById('addItemModal');
    if (addModal) {
        const quantityInput = addModal.querySelector('[name="quantity"]');
        const priceInput = addModal.querySelector('[name="unit_price"]');
        const totalOutput = addModal.querySelector('.total-price-input');
        [quantityInput, priceInput].forEach((input) => input.addEventListener('input', () => recalcTotal(quantityInput, priceInput, totalOutput)));
        recalcTotal(quantityInput, priceInput, totalOutput);
    }

    document.querySelectorAll('.edit-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = document.getElementById('editItemModal');
            modal.querySelector('#editItemForm').action = `{{ url('storages/items') }}/${button.dataset.itemId}`;
            modal.querySelector('#editProductName').value = button.dataset.productName;
            modal.querySelector('#editQuantity').value = button.dataset.quantity;
            modal.querySelector('#editWeight').value = button.dataset.weight;
            modal.querySelector('#editUnitPrice').value = button.dataset.unitPrice;
            const totalOutput = modal.querySelector('.total-price-input');
            recalcTotal(modal.querySelector('#editQuantity'), modal.querySelector('#editUnitPrice'), totalOutput);
            modal.querySelector('#editQuantity').addEventListener('input', () => recalcTotal(modal.querySelector('#editQuantity'), modal.querySelector('#editUnitPrice'), totalOutput), { once: true });
            modal.querySelector('#editUnitPrice').addEventListener('input', () => recalcTotal(modal.querySelector('#editQuantity'), modal.querySelector('#editUnitPrice'), totalOutput), { once: true });
        });
    });

    document.querySelectorAll('.transfer-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = document.getElementById('transferItemModal');
            modal.querySelector('#transferItemId').value = button.dataset.itemId;
            modal.querySelector('#transferProductName').value = button.dataset.productName;
            modal.querySelector('#transferAvailableQuantity').value = button.dataset.quantity;
            modal.querySelector('#transferAvailableWeight').value = button.dataset.weight;
            modal.querySelector('#transferUnitPrice').value = parseFloat(button.dataset.unitPrice || '0').toFixed(2);
            modal.querySelector('#transferQuantity').value = '';
            modal.querySelector('#transferWeight').value = '';
            modal.querySelector('#transferTotalPrice').value = '0.00';
            modal.querySelector('#transferError').textContent = '';

            const quantityInput = modal.querySelector('#transferQuantity');
            const weightInput = modal.querySelector('#transferWeight');
            const unitPriceInput = modal.querySelector('#transferUnitPrice');
            const totalOutput = modal.querySelector('#transferTotalPrice');
            const errorOutput = modal.querySelector('#transferError');

            const updateTransferTotal = () => {
                recalcTotal(quantityInput, unitPriceInput, totalOutput);
                const availableQuantity = parseFloat(button.dataset.quantity || '0');
                const availableWeight = parseFloat(button.dataset.weight || '0');
                const transferQuantity = parseFloat(quantityInput.value || '0');
                const transferWeight = parseFloat(weightInput.value || '0');
                if (transferQuantity > availableQuantity || transferWeight > availableWeight) {
                    errorOutput.textContent = 'Transfer quantity or weight cannot exceed the source transaction.';
                } else {
                    errorOutput.textContent = '';
                }
            };

            quantityInput.addEventListener('input', updateTransferTotal, { once: true });
            weightInput.addEventListener('input', updateTransferTotal, { once: true });
        });
    });
})();
</script>
@endsection