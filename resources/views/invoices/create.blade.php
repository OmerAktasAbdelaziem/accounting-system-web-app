@extends('layouts.modern')

@section('content')
<style>
    .create-shell { min-height: 100vh; padding: 32px 0 48px; background: linear-gradient(180deg, #f7f7f8 0%, #eef1f5 100%); }
    .create-hero { background: linear-gradient(135deg, #16181d 0%, #23262d 100%); color: #fff; border-radius: 28px; padding: 28px 30px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.18); }
    .create-card { border: 0; border-radius: 28px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.1); overflow: hidden; }
    .create-field { min-height: 52px; border-radius: 14px; border-color: #d9dde5; }
    .create-field:focus { border-color: #ff8c00; box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1); }

    @media (max-width: 768px) {
        .create-shell {
            padding: 16px 0 28px;
            background: linear-gradient(180deg, #fff9f3 0%, #f5f7fb 46%, #eef3f8 100%);
        }

        .create-hero {
            padding: 20px;
            border-radius: 22px;
            background: linear-gradient(160deg, #ffffff 0%, #fff8ef 56%, #fff1df 100%);
            color: #111827;
            border: 1px solid rgba(255, 140, 0, 0.12);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .create-hero,
        .create-hero > div {
            width: 100%;
        }

        .create-hero .text-white-50 {
            color: #64748b !important;
        }

        .create-card .card-body {
            padding: 18px !important;
        }

        .create-card {
            border-radius: 22px;
            box-shadow: 0 16px 38px rgba(15, 23, 42, 0.08);
        }

        .create-card .row.g-3 > [class*="col-md-"] {
            width: 100%;
        }

        .create-card .row.g-3 > .col-lg-4.ms-lg-auto {
            order: 3;
            margin-top: 8px;
        }

        .create-card .row.g-3 > .col-12.d-flex.gap-2.pt-2 {
            order: 4;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 18px;
        }

        .table {
            min-width: 620px;
        }

        .table thead th {
            white-space: nowrap;
        }

        .col-lg-4.ms-lg-auto {
            width: 100%;
            margin-left: 0 !important;
        }

        .col-12.d-flex.gap-2.pt-2 {
            flex-direction: column;
        }

        .col-12.d-flex.gap-2.pt-2 .btn,
        .col-12.d-flex.gap-2.pt-2 a {
            width: 100%;
        }

        .col-12.d-flex.gap-2.pt-2 {
            position: sticky;
            bottom: 12px;
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.9);
            border-radius: 18px;
            padding: 10px;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        }

        .col-lg-4.ms-lg-auto .card-body {
            background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.98));
            border-radius: 20px;
        }
    }

    @media (max-width: 576px) {
        .create-hero h1 {
            font-size: 24px;
        }

        .table {
            min-width: 640px;
        }

        .create-hero p {
            font-size: 13px;
        }
    }
</style>

<div class="create-shell">
    <div class="container-fluid">
        <div class="create-hero mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">{{ __('messages.billing') }}</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ __('messages.create_invoice') }}</h1>
                <p class="mb-0 text-white-50">{{ __('messages.create_invoice_description') }}</p>
            </div>
        </div>

        <div class="card create-card">
            <div class="card-body p-4 p-lg-5">
                <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('messages.customer') }}</label>
                            <select name="customer_id" class="form-select create-field">
                                <option value="">{{ __('messages.select_customer') }}</option>
                                @foreach($customers as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('messages.date') }}</label>
                            <input type="date" name="date" class="form-control create-field" value="{{ old('date', now()->toDateString()) }}">
                        </div>

                        <div class="col-12">
                            @include('branches.partials.multi-select', ['branches' => $branches ?? [], 'selectedBranchIds' => $selectedBranchIds ?? []])
                        </div>

                        <div class="col-12">
                            <div class="card border-0 rounded-4 bg-light">
                                <div class="card-header bg-transparent border-0 pt-4 px-4">
                                    <h5 class="card-title mb-0">{{ __('messages.line_items') }}</h5>
                                </div>
                                <div class="card-body px-4 pb-4">
                                    <div class="table-responsive">
                                        <table class="table table-sm" id="itemsTable">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('messages.product') }}</th>
                                                    <th>{{ __('messages.quantity') }}</th>
                                                    <th>{{ __('messages.unit_price') }}</th>
                                                    <th>{{ __('messages.line_total') }}</th>
                                                    <th>{{ __('messages.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemsBody">
                                                <tr class="item-row" data-index="0">
                                                    <td><input type="text" name="items[0][product_id]" class="form-control form-control-sm product-select create-field" placeholder="{{ __('messages.product') }}"></td>
                                                    <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm quantity create-field" placeholder="0" min="1" value="1"></td>
                                                    <td><input type="number" step="0.01" name="items[0][unit_price]" class="form-control form-control-sm unit-price create-field" placeholder="0.00" min="0" value="0.00"></td>
                                                    <td><input type="number" step="0.01" class="form-control form-control-sm line-total create-field" readonly value="0.00"></td>
                                                    <td><button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success" id="addItemBtn"><i class="bi bi-plus"></i> {{ __('messages.add_item') }}</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 ms-lg-auto">
                            <div class="card border-0 rounded-4 shadow-sm">
                                <div class="card-body">
                                    <div class="row mb-2"><div class="col-6"><strong>{{ __('messages.sub_total') }}</strong></div><div class="col-6 text-end"><span id="subTotal">0.00</span></div></div>
                                    <div class="row mb-3"><div class="col-6"><strong>{{ __('messages.tax_15_percent') }}</strong></div><div class="col-6 text-end"><span id="taxAmount">0.00</span></div></div>
                                    <hr>
                                    <div class="row"><div class="col-6"><h5>{{ __('messages.total') }}</h5></div><div class="col-6 text-end"><h5 id="totalAmount">0.00</h5></div></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 pt-2">
                            <button type="submit" class="btn btn-primary px-4">{{ __('messages.save_invoice') }}</button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary px-4">{{ __('messages.cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let itemCount = 1;

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
            <input type="text" name="items[${itemCount}][product_id]" class="form-control form-control-sm product-select" placeholder="{{ __('messages.product') }}">
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

// Attach listeners to initial row
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
</script>
@endsection
