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
                            <label class="form-label fw-semibold">Sale Date</label>
                            <input type="date" name="sale_date" class="form-control form-control-lg" value="{{ old('sale_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Amount Sold</label>
                            <input type="number" name="total_amount" class="form-control form-control-lg" min="0.01" step="0.01" value="{{ old('total_amount') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Products Sold <span class="text-muted">(Optional)</span></label>
                            <div class="products-list">
                                @if(old('product_sold'))
                                    @foreach(old('product_sold') as $product)
                                        <div class="input-group mb-2">
                                            <input type="text" name="product_sold[]" class="form-control" value="{{ $product }}" placeholder="e.g., Product name, quantity, details...">
                                            <button type="button" class="btn btn-outline-danger remove-product">Remove</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2">
                                        <input type="text" name="product_sold[]" class="form-control" placeholder="e.g., Product name, quantity, details...">
                                        <button type="button" class="btn btn-outline-danger remove-product">Remove</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm add-product-field mt-2">+ Add Product Field</button>
                        </div>

                        <div class="col-12 d-grid d-md-flex justify-content-md-end gap-2 mt-4">
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
                    <div class="field-hint">Quick entry for total daily sales amount with optional product notes.</div>
                </div>
                <div class="card-body p-4">
                    <div class="sales-empty p-4 mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,140,0,.12);color:#ff8c00;">
                                <i class="bi bi-cash-coin fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Daily sales tracking</h6>
                                <div class="text-muted small">Record total amount sold for the day with optional product notes. Add more product fields as needed.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="sales-stat h-100 d-flex">
                                <div class="accent"></div>
                                <div class="p-3 flex-grow-1">
                                    <small class="text-muted d-block">Total sales recorded</small>
                                    <h5 class="mb-0">{{ $stats['count'] }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Enter the total amount sold for the day. Optionally add product details using the "+ Add Product Field" button.
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
                    <div class="field-hint">Filter by date range.</div>
                </div>
                <span class="badge text-bg-light border">{{ $sales->total() }} results</span>
            </div>
            <form method="GET" class="row g-2 mt-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label mb-0 small">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label mb-0 small">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-lg-4 col-md-12 d-grid">
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
                            <th>Total Amount</th>
                            <th>Products Sold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date?->format('Y-m-d') }}</td>
                                <td><strong>{{ $currencySymbol ?? '$' }}{{ number_format((float) $sale->total_amount, 2) }}</strong></td>
                                <td>
                                    @if($sale->notes)
                                        <small class="text-muted">{{ \Illuminate\Support\Str::limit($sale->notes, 100) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-5">
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
    // Handle product field management for simplified sales form
    function bindProductList(container) {
        const addBtn = container.closest('form')?.querySelector('.add-product-field');
        const productsList = container;

        function bindRemoveButtons() {
            container.querySelectorAll('.remove-product').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    // Only remove if there's more than one field
                    if (container.querySelectorAll('.input-group').length > 1) {
                        btn.closest('.input-group').remove();
                    }
                });
            });
        }

        bindRemoveButtons();

        if (addBtn) {
            addBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const newField = document.createElement('div');
                newField.className = 'input-group mb-2';
                newField.innerHTML = `
                    <input type="text" name="product_sold[]" class="form-control" placeholder="e.g., Product name, quantity, details...">
                    <button type="button" class="btn btn-outline-danger remove-product">Remove</button>
                `;
                productsList.appendChild(newField);
                bindRemoveButtons();
            });
        }
    }

    document.querySelectorAll('.products-list').forEach(bindProductList);
})();
</script>
@endsection