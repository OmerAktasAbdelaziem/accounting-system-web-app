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
                    Record daily sales total from employee amounts with one description for the full sale.
                </p>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-3 text-center h-100">
                            <div class="small text-white-50">Entries</div>
                            <div class="fs-4 fw-bold">{{ $stats['count'] }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-white bg-opacity-10 backdrop-blur rounded-4 p-3 text-center h-100">
                            <div class="small text-white-50">Net Income</div>
                            <div class="fs-4 fw-bold">{{ $currencySymbol ?? '$' }}{{ number_format($stats['net_total'], 2) }}</div>
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
                        <div class="field-hint">Select branch, date, and total amount sold.</div>
                    </div>
                    <span class="badge text-bg-light border">Live total calculator</span>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('sales.store') }}" class="row g-3">
                        @csrf
                        @php($employeeRows = old('employee_sales', [['employee_id' => '', 'amount' => '']]))
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Branch</label>
                            <select name="branch_id" class="form-select form-select-lg" required>
                                <option value="">Select branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" @selected(old('branch_id') == $branch->id)>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sale Date</label>
                            <input type="date" name="sale_date" class="form-control form-control-lg" value="{{ old('sale_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Total Amount Sold</label>
                            <input type="number" name="total_amount" class="form-control form-control-lg js-total-amount" min="0.01" step="0.01" value="{{ old('total_amount') }}" readonly>
                            <div class="field-hint mt-1">Auto-calculated from employee amounts.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Amount Spent by Store <span class="text-muted">(Optional)</span></label>
                            <input type="number" name="spent_amount" class="form-control form-control-lg" min="0" step="0.01" value="{{ old('spent_amount', 0) }}" placeholder="Amount the store spent">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Employees Involved</label>
                            <div class="field-hint mb-2">Choose one or more employees and add the amount each one sold today.</div>
                            <div id="employee-sales-list" class="d-grid gap-3">
                                @foreach($employeeRows as $index => $row)
                                    <div class="employee-sale-item border rounded-4 p-3 bg-light">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted mb-1">Employee</label>
                                                <select name="employee_sales[{{ $index }}][employee_id]" class="form-select">
                                                    <option value="">Select employee</option>
                                                    @foreach($employees as $employee)
                                                        <option value="{{ $employee->id }}" @selected((string) ($row['employee_id'] ?? '') === (string) $employee->id)>{{ $employee->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted mb-1">Amount Sold</label>
                                                <input type="number" name="employee_sales[{{ $index }}][amount]" class="form-control employee-sale-amount" min="0.01" step="0.01" value="{{ $row['amount'] ?? '' }}" placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-employee-row">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('employee_sales')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            <button type="button" class="btn btn-outline-secondary btn-sm add-employee-field mt-2">+ Add Employee</button>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Add a description for the whole sale...">{{ old('notes') }}</textarea>
                            <div class="field-hint mt-1">This description applies to the whole sale, not to individual employees.</div>
                        </div>

                        <template id="employee-sale-row-template">
                            <div class="employee-sale-item border rounded-4 p-3 bg-light">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Employee</label>
                                        <select name="employee_sales[__INDEX__][employee_id]" class="form-select">
                                            <option value="">Select employee</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small text-muted mb-1">Amount Sold</label>
                                        <input type="number" name="employee_sales[__INDEX__][amount]" class="form-control employee-sale-amount" min="0.01" step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-employee-row">Remove</button>
                                </div>
                            </div>
                        </template>

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
                    <div class="field-hint">Quick entry for total daily sales amount built from employee amounts.</div>
                </div>
                <div class="card-body p-4">
                    <div class="sales-empty p-4 mb-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,140,0,.12);color:#ff8c00;">
                                <i class="bi bi-cash-coin fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Daily sales tracking</h6>
                                <div class="text-muted small">Record total amount sold by adding each employee's amount. The total updates automatically.</div>
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
            <form id="sales-filter-form" method="GET" class="row g-2 mt-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label mb-0 small">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">All branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label mb-0 small">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label mb-0 small">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-lg-2 col-md-12 d-grid">
                    <button class="btn btn-outline-primary">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body p-0" id="sales-list-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Total Amount</th>
                            <th>Spent</th>
                            <th>Net</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date?->format('Y-m-d') }}</td>
                                <td>{{ $sale->branch?->name ?? '-' }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-link p-0 align-baseline text-decoration-none fw-bold sale-details-btn"
                                        data-sale-date="{{ $sale->sale_date?->format('Y-m-d') }}"
                                        data-branch-name="{{ e($sale->branch?->name ?? '-') }}"
                                        data-total-amount="{{ number_format((float) $sale->total_amount, 2, '.', '') }}"
                                        data-spent-amount="{{ number_format((float) ($sale->spent_amount ?? 0), 2, '.', '') }}"
                                        data-net-amount="{{ number_format((float) $sale->net_income, 2, '.', '') }}"
                                        data-primary-employee="{{ e($sale->employee?->name ?? '-') }}"
                                        data-sale-notes="{{ e($sale->notes ?? '') }}"
                                        data-sale-employees='@json($sale->employeeSaleDetails->map(fn ($detail) => ["name" => $detail->employee?->name ?? "Deleted employee", "amount" => (float) $detail->amount]))'
                                    >
                                        {{ $currencySymbol ?? '$' }}{{ number_format((float) $sale->total_amount, 2) }}
                                    </button>
                                </td>
                                <td>{{ $currencySymbol ?? '$' }}{{ number_format((float) ($sale->spent_amount ?? 0), 2) }}</td>
                                <td><strong>{{ $currencySymbol ?? '$' }}{{ number_format((float) $sale->net_income, 2) }}</strong></td>
                                <td>
                                    @if($sale->notes)
                                        <small class="text-muted">{{ \Illuminate\Support\Str::limit($sale->notes, 100) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-primary edit-sale-btn"
                                            data-id="{{ $sale->id }}"
                                            data-sale_date="{{ $sale->sale_date?->format('Y-m-d') }}"
                                            data-branch_id="{{ $sale->branch_id }}"
                                            data-total_amount="{{ (float) $sale->total_amount }}"
                                            data-spent_amount="{{ (float) ($sale->spent_amount ?? 0) }}"
                                            data-notes="{{ e($sale->notes) }}"
                                                    data-primary-employee-id="{{ $sale->employee_id }}"
                                                    data-employee-sales='@json($sale->employeeSaleDetails->map(fn ($detail) => ["employee_id" => $detail->employee_id, "amount" => (float) $detail->amount]))'
                                            data-update_url="{{ route('sales.update', $sale->id) }}"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </td>
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

        <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title mb-0">Sale Details</h5>
                            <div class="field-hint">Employee breakdown for this sale</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="small text-muted">Date</div>
                                <div class="fw-semibold" id="sale-details-date">-</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Branch</div>
                                <div class="fw-semibold" id="sale-details-branch">-</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Primary Employee</div>
                                <div class="fw-semibold" id="sale-details-primary-employee">-</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Total Amount</div>
                                <div class="fw-semibold" id="sale-details-total">-</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Spent</div>
                                <div class="fw-semibold" id="sale-details-spent">-</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted">Net</div>
                                <div class="fw-semibold" id="sale-details-net">-</div>
                            </div>
                            <div class="col-12">
                                <div class="small text-muted">Description</div>
                                <div class="fw-semibold" id="sale-details-notes">-</div>
                            </div>
                        </div>

                        <div class="card border-0 bg-light">
                            <div class="card-header bg-transparent border-0 pb-0">
                                <h6 class="mb-0">Employees involved</h6>
                            </div>
                            <ul class="list-group list-group-flush" id="sale-details-list"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Sale Modal -->
        <div class="modal fade" id="editSaleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form id="editSaleForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Sale</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Branch</label>
                                    <select name="branch_id" class="form-select" required>
                                        <option value="">Select branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sale Date</label>
                                    <input type="date" name="sale_date" class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Total Amount</label>
                                    <input type="number" name="total_amount" class="form-control js-total-amount" step="0.01" min="0.01" readonly>
                                    <div class="field-hint mt-1">Auto-calculated from employee amounts.</div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Amount Spent by Store <span class="text-muted">(Optional)</span></label>
                                    <input type="number" name="spent_amount" class="form-control" step="0.01" min="0" placeholder="Amount the store spent">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Employees Involved</label>
                                    <div class="field-hint mb-2">Add or change the employees linked to this sale and their sold amounts.</div>
                                    <div id="edit-employee-sales-list" class="d-grid gap-3"></div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2 add-edit-employee-field">+ Add Employee</button>
                                    @error('employee_sales')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea name="notes" rows="4" class="form-control" placeholder="Add a description for the whole sale..."></textarea>
                                    <div class="field-hint mt-1">This description applies to the whole sale, not to individual employees.</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <template id="edit-employee-sale-row-template">
            <div class="employee-sale-item border rounded-4 p-3 bg-light">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Employee</label>
                        <select name="employee_sales[__INDEX__][employee_id]" class="form-select" required>
                            <option value="">Select employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Amount Sold</label>
                        <input type="number" name="employee_sales[__INDEX__][amount]" class="form-control employee-sale-amount" min="0.01" step="0.01" placeholder="0.00" required>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-employee-row">Remove</button>
                </div>
            </div>
        </template>

<script>
(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function updateTotalFromEmployeeList(container, totalInput) {
        if (!container || !totalInput) return;
        const total = Array.from(container.querySelectorAll('.employee-sale-amount'))
            .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
        totalInput.value = total > 0 ? total.toFixed(2) : '';
    }

    function bindEmployeeList(container, totalInput) {
        const addBtn = document.querySelector('.add-employee-field');
        const template = document.getElementById('employee-sale-row-template');
        let nextIndex = container.querySelectorAll('.employee-sale-item').length;

        function bindRemoveButtons() {
            container.querySelectorAll('.remove-employee-row').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (container.querySelectorAll('.employee-sale-item').length > 1) {
                        btn.closest('.employee-sale-item')?.remove();
                    }
                });
            });
        }

        bindRemoveButtons();

        container.addEventListener('input', function (e) {
            if (e.target && e.target.classList.contains('employee-sale-amount')) {
                updateTotalFromEmployeeList(container, totalInput);
            }
        });

        if (addBtn && template) {
            addBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
                container.insertAdjacentHTML('beforeend', html);
                bindRemoveButtons();
                updateTotalFromEmployeeList(container, totalInput);
            });
        }
    }

    function bindEditEmployeeList(container, totalInput) {
        const addBtn = document.querySelector('.add-edit-employee-field');
        const template = document.getElementById('edit-employee-sale-row-template');
        let nextIndex = container.querySelectorAll('.employee-sale-item').length;

        function bindRemoveButtons() {
            container.querySelectorAll('.remove-employee-row').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (container.querySelectorAll('.employee-sale-item').length > 1) {
                        btn.closest('.employee-sale-item')?.remove();
                    }
                });
            });
        }

        bindRemoveButtons();

        container.addEventListener('input', function (e) {
            if (e.target && e.target.classList.contains('employee-sale-amount')) {
                updateTotalFromEmployeeList(container, totalInput);
            }
        });

        if (addBtn && template) {
            addBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
                container.insertAdjacentHTML('beforeend', html);
                bindRemoveButtons();
                updateTotalFromEmployeeList(container, totalInput);
            });
        }
    }

    const employeeList = document.getElementById('employee-sales-list');
    const createTotalInput = document.querySelector('input[name="total_amount"]');
    if (employeeList) {
        bindEmployeeList(employeeList, createTotalInput);
        updateTotalFromEmployeeList(employeeList, createTotalInput);
    }

    // Edit sale modal handling (delegated + safe modal init)
    document.addEventListener('DOMContentLoaded', function () {
        const editModalEl = document.getElementById('editSaleModal');
        const saleDetailsModalEl = document.getElementById('saleDetailsModal');
        let editModal = null;
        let saleDetailsModal = null;
        if (editModalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try { editModal = new bootstrap.Modal(editModalEl); } catch (err) { editModal = null; }
            }
        }
        if (saleDetailsModalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try { saleDetailsModal = new bootstrap.Modal(saleDetailsModalEl); } catch (err) { saleDetailsModal = null; }
            }
        }

        const editEmployeeList = document.getElementById('edit-employee-sales-list');
        const editTotalInput = document.querySelector('#editSaleForm input[name="total_amount"]');
        if (editEmployeeList) {
            bindEditEmployeeList(editEmployeeList, editTotalInput);
        }

        document.addEventListener('click', function (e) {
            const detailsBtn = e.target.closest && e.target.closest('.sale-details-btn');
            if (detailsBtn) {
                e.preventDefault();

                const saleEmployees = (() => {
                    try {
                        return JSON.parse(detailsBtn.getAttribute('data-sale-employees') || '[]');
                    } catch (err) {
                        return [];
                    }
                })();

                const primaryEmployee = detailsBtn.getAttribute('data-primary-employee') || '-';
                const employees = saleEmployees.length > 0
                    ? saleEmployees
                    : (primaryEmployee && primaryEmployee !== '-')
                        ? [{ name: primaryEmployee, amount: detailsBtn.getAttribute('data-total-amount') || 0 }]
                        : [];

                const saleDetailsList = document.getElementById('sale-details-list');
                const saleDetailsDate = document.getElementById('sale-details-date');
                const saleDetailsBranch = document.getElementById('sale-details-branch');
                const saleDetailsPrimary = document.getElementById('sale-details-primary-employee');
                const saleDetailsTotal = document.getElementById('sale-details-total');
                const saleDetailsSpent = document.getElementById('sale-details-spent');
                const saleDetailsNet = document.getElementById('sale-details-net');
                const saleDetailsNotes = document.getElementById('sale-details-notes');

                if (saleDetailsDate) saleDetailsDate.textContent = detailsBtn.getAttribute('data-sale-date') || '-';
                if (saleDetailsBranch) saleDetailsBranch.textContent = detailsBtn.getAttribute('data-branch-name') || '-';
                if (saleDetailsPrimary) saleDetailsPrimary.textContent = primaryEmployee;
                if (saleDetailsTotal) saleDetailsTotal.textContent = '{{ $currencySymbol ?? '$' }}' + (detailsBtn.getAttribute('data-total-amount') || '0.00');
                if (saleDetailsSpent) saleDetailsSpent.textContent = '{{ $currencySymbol ?? '$' }}' + (detailsBtn.getAttribute('data-spent-amount') || '0.00');
                if (saleDetailsNet) saleDetailsNet.textContent = '{{ $currencySymbol ?? '$' }}' + (detailsBtn.getAttribute('data-net-amount') || '0.00');
                if (saleDetailsNotes) saleDetailsNotes.textContent = detailsBtn.getAttribute('data-sale-notes') || '-';

                if (saleDetailsList) {
                    saleDetailsList.innerHTML = employees.length
                        ? employees.map((employee, index) => `
                            <li class="list-group-item d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">${escapeHtml(employee.name || 'Deleted employee')}</div>
                                </div>
                                <div class="fw-semibold">{{ $currencySymbol ?? '$' }}${Number(employee.amount || 0).toFixed(2)}</div>
                                <span class="badge text-bg-light border">#${index + 1}</span>
                            </li>
                        `).join('')
                        : '<li class="list-group-item text-muted">No employee details recorded for this sale.</li>';
                }

                if (saleDetailsModal) {
                    try { saleDetailsModal.show(); return; } catch (err) { /* fallback below */ }
                }
                if (saleDetailsModalEl) {
                    saleDetailsModalEl.classList.add('show');
                    saleDetailsModalEl.style.display = 'block';
                    saleDetailsModalEl.removeAttribute('aria-hidden');
                }
                return;
            }

            const btn = e.target.closest && e.target.closest('.edit-sale-btn');
            if (!btn) return;
            e.preventDefault();

            const saleDate = btn.getAttribute('data-sale_date');
            const branchId = btn.getAttribute('data-branch_id');
            const totalAmount = btn.getAttribute('data-total_amount');
            const spentAmount = btn.getAttribute('data-spent_amount');
            const notes = btn.getAttribute('data-notes');
            const employeeSalesData = (() => {
                try {
                    return JSON.parse(btn.getAttribute('data-employee-sales') || '[]');
                } catch (err) {
                    return [];
                }
            })();
            const updateUrl = btn.getAttribute('data-update_url');

            const form = document.getElementById('editSaleForm');
            if (!form) return;
            form.action = updateUrl || form.action;
            const saleDateInput = form.querySelector('input[name="sale_date"]');
            const branchSelect = form.querySelector('select[name="branch_id"]');
            const totalInput = form.querySelector('input[name="total_amount"]');
            const spentInput = form.querySelector('input[name="spent_amount"]');
            const notesArea = form.querySelector('textarea[name="notes"]');
            const editEmployeeList = document.getElementById('edit-employee-sales-list');
            const editEmployeeTemplate = document.getElementById('edit-employee-sale-row-template');

            if (saleDateInput) saleDateInput.value = saleDate || '';
            if (branchSelect) branchSelect.value = branchId || '';
            if (totalInput) totalInput.value = totalAmount || '';
            if (spentInput) spentInput.value = spentAmount || '';
            if (notesArea) notesArea.value = notes || '';

            if (editEmployeeList && editEmployeeTemplate) {
                const rows = employeeSalesData.length > 0
                    ? employeeSalesData
                    : [{ employee_id: btn.getAttribute('data-primary-employee-id') || '', amount: '' }];

                editEmployeeList.innerHTML = rows.map((row, index) => editEmployeeTemplate.innerHTML.replaceAll('__INDEX__', String(index))).join('');

                editEmployeeList.querySelectorAll('.employee-sale-item').forEach((rowEl, index) => {
                    const rowData = rows[index] || {};
                    const employeeSelect = rowEl.querySelector('select[name^="employee_sales"]');
                    const amountInput = rowEl.querySelector('input[name^="employee_sales"][name$="[amount]"]');
                    if (employeeSelect) employeeSelect.value = String(rowData.employee_id || '');
                    if (amountInput) amountInput.value = String(rowData.amount || '');
                });

                // rebind remove buttons for newly injected rows
                editEmployeeList.querySelectorAll('.remove-employee-row').forEach((removeBtn) => {
                    removeBtn.addEventListener('click', (event) => {
                        event.preventDefault();
                        if (editEmployeeList.querySelectorAll('.employee-sale-item').length > 1) {
                            removeBtn.closest('.employee-sale-item')?.remove();
                            updateTotalFromEmployeeList(editEmployeeList, totalInput);
                        }
                    });
                });

                updateTotalFromEmployeeList(editEmployeeList, totalInput);
            }

            if (editModal) {
                try { editModal.show(); return; } catch (e) { /* fallback below */ }
            }
            // jQuery + Bootstrap v4 fallback
            if (window.jQuery && typeof jQuery(editModalEl).modal === 'function') {
                jQuery(editModalEl).modal('show');
                return;
            }
            // Simple fallback: toggle modal classes
            if (editModalEl) {
                editModalEl.classList.add('show');
                editModalEl.style.display = 'block';
                editModalEl.removeAttribute('aria-hidden');
            }
        });
    });
})();
</script>

@include('components.ajax-list')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initAjaxList({ containerId: 'sales-list-container', formSelector: '#sales-filter-form', debounceMs: 300 });
});
</script>
@endpush
@endsection