@extends('layouts.modern')

@section('title', 'Sales')

@section('content')
<div class="container-fluid py-3">
    <style>
        .sales-hero {
            position: relative;
            overflow: hidden;
            border: 0;
            color: #fff;
            background: linear-gradient(135deg, #111827 0%, #1f2937 46%, #ff8c00 100%);
            box-shadow: 0 16px 40px rgba(17, 24, 39, 0.18);
        }

        .sales-hero::before,
        .sales-hero::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            pointer-events: none;
        }

        .sales-hero::before {
            width: 220px;
            height: 220px;
            right: -60px;
            top: -60px;
        }

        .sales-hero::after {
            width: 140px;
            height: 140px;
            right: 120px;
            bottom: -40px;
        }

        .sales-stat {
            position: relative;
            overflow: hidden;
            border: 0;
            border-radius: 20px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
            background: #fff;
        }

        .sales-stat .accent {
            width: 12px;
            border-radius: 999px;
            background: linear-gradient(180deg, #ff8c00, #27ae60);
        }

        .sales-panel {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.10);
            overflow: hidden;
        }

        .sales-panel .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        }

        .field-hint {
            font-size: 0.8rem;
            color: #6b7280;
        }

        .product-preview {
            background: linear-gradient(180deg, rgba(255, 140, 0, 0.08), rgba(39, 174, 96, 0.05));
            border: 1px solid rgba(255, 140, 0, 0.12);
            border-radius: 18px;
        }

        .product-preview .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
        }

        .sales-history-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 999px;
            padding: 0.45rem 0.8rem;
            background: rgba(255, 140, 0, 0.08);
            color: #b45309;
            font-size: 0.85rem;
        }

        .sales-empty {
            border: 1px dashed rgba(107, 114, 128, 0.35);
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(248, 250, 252, 1), rgba(255, 255, 255, 1));
        }
    </style>

    <div class="sales-hero rounded-4 p-4 p-lg-5 mb-4">
        <div class="row align-items-end g-4 position-relative" style="z-index:1;">
            <div class="col-lg-7">
                <div class="sales-history-chip mb-3">
                    <i class="bi bi-receipt"></i>
                    Daily sales workspace
                </div>
                <h2 class="fw-bold mb-2">Sales</h2>
                <p class="mb-0 text-white-75" style="max-width: 720px;">
                    Record every sale with the employee, product details, quantity, price, and reference in one focused screen.
                </p>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-4">
                        <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-3 text-center h-100">
                            <div class="small text-white-50">Entries</div>
                            <div class="fs-4 fw-bold">{{ $stats['count'] }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-3 text-center h-100">
                            <div class="small text-white-50">Qty</div>
                            <div class="fs-4 fw-bold">{{ number_format($stats['quantity'], 0) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-3 text-center h-100">
                            <div class="small text-white-50">Value</div>
                            <div class="fs-4 fw-bold">{{ $currencySymbol ?? '$' }}{{ number_format($stats['total'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card sales-panel h-100">
                <div class="card-header px-4 py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-1">Record a Sale</h5>
                        <div class="field-hint">Select the employee and product, then confirm price and quantity.</div>
                    </div>
                    <span class="badge text-bg-light border">Live total calculator</span>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('sales.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Employee</label>
                            <select name="employee_id" class="form-select form-select-lg" required>
                                <option value="">Select employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sale Date</label>
                            <input type="date" name="sale_date" class="form-control form-control-lg" value="{{ old('sale_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Product</label>
                            <select name="product_id" id="saleProductId" class="form-select form-select-lg" required>
                                <option value="">Select product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-category="{{ $product->category?->name ?? '-' }}"
                                            data-unit="{{ $product->unit }}"
                                            data-purchase="{{ $product->purchase_price }}"
                                            data-selling="{{ $product->selling_price }}"
                                            @selected(old('product_id') == $product->id)>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quantity</label>
                            <input type="number" name="quantity" id="saleQuantity" class="form-control form-control-lg" min="1" step="1" value="{{ old('quantity', 1) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Unit Price</label>
                            <input type="number" name="unit_price" id="saleUnitPrice" class="form-control form-control-lg" min="0.01" step="0.01" value="{{ old('unit_price') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Reference</label>
                            <input type="text" name="sale_reference" class="form-control form-control-lg" value="{{ old('sale_reference') }}" placeholder="Invoice / receipt no.">
                        </div>

                        <div class="col-12">
                            <div class="product-preview p-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <div class="label mb-1">Sale total</div>
                                        <div class="fs-2 fw-bold" id="saleTotalAmount">0.00</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="label mb-1">Price formula</div>
                                        <div class="fw-semibold"><span id="saleQuantityEcho">1</span> x <span id="saleUnitPriceEcho">0.00</span></div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6"><strong>Product:</strong> <span id="detailName" class="text-muted">-</span></div>
                                    <div class="col-md-6"><strong>Category:</strong> <span id="detailCategory" class="text-muted">-</span></div>
                                    <div class="col-md-6"><strong>Unit:</strong> <span id="detailUnit" class="text-muted">-</span></div>
                                    <div class="col-md-6"><strong>Purchase:</strong> <span id="detailPurchase" class="text-muted">-</span></div>
                                    <div class="col-md-6"><strong>Selling:</strong> <span id="detailSelling" class="text-muted">-</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes">{{ old('notes') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Arabic Notes</label>
                            <textarea name="notes_ar" class="form-control" rows="3" placeholder="ملاحظات اختيارية">{{ old('notes_ar') }}</textarea>
                        </div>
                        <div class="col-12 d-grid d-md-flex justify-content-md-end gap-2 mt-2">
                            <button type="submit" class="btn btn-primary btn-lg px-4">Save Sale</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card sales-panel h-100">
                <div class="card-header px-4 py-3">
                    <h5 class="mb-1">What this screen captures</h5>
                    <div class="field-hint">The product card updates as you choose an item, so you can verify the exact sale before saving.</div>
                </div>
                <div class="card-body p-4">
                    <div class="sales-empty p-4 mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,140,0,.12);color:#ff8c00;">
                                <i class="bi bi-clipboard-data fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Employee-linked sales entry</h6>
                                <div class="text-muted small">Store the employee, product, quantity, price, date, and a reference in one record.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="sales-stat h-100 d-flex">
                                <div class="accent"></div>
                                <div class="p-3 flex-grow-1">
                                    <small class="text-muted d-block">Employees in selector</small>
                                    <h5 class="mb-0">{{ $employees->count() }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="sales-stat h-100 d-flex">
                                <div class="accent"></div>
                                <div class="p-3 flex-grow-1">
                                    <small class="text-muted d-block">Products in selector</small>
                                    <h5 class="mb-0">{{ $products->count() }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Quantity multiplies by unit price automatically. The table below keeps the latest sales visible for quick review.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card sales-panel">
        <div class="card-header px-4 py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1">Recent Sales</h5>
                    <div class="field-hint">Filter by employee, product, and date range.</div>
                </div>
                <span class="badge text-bg-light border">{{ $sales->total() }} results</span>
            </div>
            <form method="GET" class="row g-2 mt-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label mb-0 small">Employee</label>
                    <select name="employee_id" class="form-select">
                        <option value="">All employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label mb-0 small">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="">All products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label mb-0 small">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label mb-0 small">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-lg-2 col-md-4 d-grid">
                    <button class="btn btn-outline-primary">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date?->format('Y-m-d') }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $sale->employee?->name ?? '-' }}</div>
                                    <small class="text-muted">{{ $sale->employee?->department ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $sale->product?->name ?? '-' }}</div>
                                    <small class="text-muted">{{ $sale->product?->category?->name ?? '-' }}</small>
                                </td>
                                <td><span class="badge text-bg-light border">{{ number_format((float) $sale->quantity, 0) }}</span></td>
                                <td>{{ $currencySymbol ?? '$' }}{{ number_format((float) $sale->unit_price, 2) }}</td>
                                <td><strong>{{ $currencySymbol ?? '$' }}{{ number_format((float) $sale->total_amount, 2) }}</strong></td>
                                <td>{{ $sale->sale_reference ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="py-3">
                                        <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                                        No sales recorded yet.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sales->hasPages())
            <div class="card-footer bg-white px-4 py-3">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>

<script>
(function () {
    const productSelect = document.getElementById('saleProductId');
    const quantityInput = document.getElementById('saleQuantity');
    const unitPriceInput = document.getElementById('saleUnitPrice');
    const totalOutput = document.getElementById('saleTotalAmount');
    const quantityEcho = document.getElementById('saleQuantityEcho');
    const priceEcho = document.getElementById('saleUnitPriceEcho');

    function recalcTotal() {
        const quantity = parseFloat(quantityInput.value || '0');
        const unitPrice = parseFloat(unitPriceInput.value || '0');
        const total = (quantity * unitPrice).toFixed(2);
        totalOutput.textContent = total;
        quantityEcho.textContent = Number.isFinite(quantity) ? quantity.toFixed(0) : '0';
        priceEcho.textContent = Number.isFinite(unitPrice) ? unitPrice.toFixed(2) : '0.00';
    }

    function syncProductDetails() {
        const option = productSelect.options[productSelect.selectedIndex];
        if (!option || !option.value) {
            document.getElementById('detailName').textContent = '-';
            document.getElementById('detailCategory').textContent = '-';
            document.getElementById('detailUnit').textContent = '-';
            document.getElementById('detailPurchase').textContent = '-';
            document.getElementById('detailSelling').textContent = '-';
            return;
        }

        document.getElementById('detailName').textContent = option.dataset.name || '-';
        document.getElementById('detailCategory').textContent = option.dataset.category || '-';
        document.getElementById('detailUnit').textContent = option.dataset.unit || '-';
        document.getElementById('detailPurchase').textContent = '{{ $currencySymbol ?? '$' }}' + (parseFloat(option.dataset.purchase || '0')).toFixed(2);
        document.getElementById('detailSelling').textContent = '{{ $currencySymbol ?? '$' }}' + (parseFloat(option.dataset.selling || '0')).toFixed(2);
    }

    productSelect.addEventListener('change', syncProductDetails);
    quantityInput.addEventListener('input', recalcTotal);
    unitPriceInput.addEventListener('input', recalcTotal);

    syncProductDetails();
    recalcTotal();
})();
</script>
@endsection