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
                    Record daily sales total with optional product notes.
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
                        <div class="field-hint">Select branch, date, employees, and total amount sold.</div>
                    </div>
                    <span class="badge text-bg-light border">Live total calculator</span>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('sales.store') }}" class="row g-3">
                        @csrf
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
                            <input type="number" name="total_amount" class="form-control form-control-lg" min="0.01" step="0.01" value="{{ old('total_amount') }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Amount Spent by Store <span class="text-muted">(Optional)</span></label>
                            <input type="number" name="spent_amount" class="form-control form-control-lg" min="0" step="0.01" value="{{ old('spent_amount', 0) }}" placeholder="Amount the store spent">
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

                        @php
                            $employeeAssignmentsOld = old('employee_assignments');
                            if (! is_array($employeeAssignmentsOld) || empty($employeeAssignmentsOld)) {
                                $employeeAssignmentsOld = [['employee_id' => '', 'description' => '']];
                            }
                        @endphp

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">Employees Involved</label>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="add-employee-assignment">+ Add Employee</button>
                            </div>
                            <div class="employee-assignments-list" id="create-employee-assignments-list">
                                @foreach($employeeAssignmentsOld as $index => $assignment)
                                    <div class="employee-assignment-row border rounded-3 p-3 mb-2">
                                        <div class="row g-2 align-items-start">
                                            <div class="col-md-5">
                                                <label class="form-label small text-muted mb-1">Employee</label>
                                                <select name="employee_assignments[{{ $index }}][employee_id]" class="form-select employee-assignment-employee" required>
                                                    <option value="">Select employee</option>
                                                    @foreach($employees as $employee)
                                                        <option value="{{ $employee->id }}" @selected((string) ($assignment['employee_id'] ?? '') === (string) $employee->id)>{{ $employee->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted mb-1">Description</label>
                                                <textarea name="employee_assignments[{{ $index }}][description]" class="form-control employee-assignment-description" rows="2" placeholder="What did this employee sell today?">{{ $assignment['description'] ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-1 d-grid">
                                                <label class="form-label small text-muted mb-1 d-none d-md-block">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-danger remove-employee-assignment">Remove</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
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
                            <th>Products Sold</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date?->format('Y-m-d') }}</td>
                                <td>{{ $sale->branch?->name ?? '-' }}</td>
                                @php
                                    $saleEmployeeAssignments = $sale->employee_assignments ?? [];
                                    if (empty($saleEmployeeAssignments) && $sale->employee_id) {
                                        $saleEmployeeAssignments = [[
                                            'employee_id' => $sale->employee_id,
                                            'description' => '',
                                        ]];
                                    }
                                @endphp
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-link p-0 sale-details-btn text-decoration-none fw-bold"
                                        data-sale-date="{{ $sale->sale_date?->format('Y-m-d') }}"
                                        data-branch="{{ e($sale->branch?->name ?? '-') }}"
                                        data-total="{{ (float) $sale->total_amount }}"
                                        data-spent="{{ (float) ($sale->spent_amount ?? 0) }}"
                                        data-notes='@json($sale->notes)'
                                        data-employee-assignments='@json($saleEmployeeAssignments)'
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
                                            data-employee_assignments='@json($saleEmployeeAssignments)'
                                            data-update_url="{{ route('sales.update', $sale->id) }}"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
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
                                    <input type="number" name="total_amount" class="form-control" step="0.01" min="0.01" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Amount Spent by Store <span class="text-muted">(Optional)</span></label>
                                    <input type="number" name="spent_amount" class="form-control" step="0.01" min="0" placeholder="Amount the store spent">
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Employees Involved</label>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="edit-add-employee-assignment">+ Add Employee</button>
                                    </div>
                                    <div class="employee-assignments-list" id="edit-employee-assignments-list"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Products / Notes</label>
                                    <textarea name="product_sold_text" rows="4" class="form-control" placeholder="One product per line or notes..."></textarea>
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

        <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sale Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Date</small>
                                <strong id="sale-details-date">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Branch</small>
                                <strong id="sale-details-branch">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Total Amount</small>
                                <strong id="sale-details-total">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Spent</small>
                                <strong id="sale-details-spent">-</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Net</small>
                                <strong id="sale-details-net">-</strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-2">Employees Involved</small>
                                <div id="sale-details-employees" class="vstack gap-2"></div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-2">Products / Notes</small>
                                <div id="sale-details-notes" class="border rounded-3 p-3 bg-light">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template id="employee-assignment-template">
            <div class="employee-assignment-row border rounded-3 p-3 mb-2">
                <div class="row g-2 align-items-start">
                    <div class="col-md-5">
                        <label class="form-label small text-muted mb-1">Employee</label>
                        <select class="form-select employee-assignment-employee" required>
                            <option value="">Select employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Description</label>
                        <textarea class="form-control employee-assignment-description" rows="2" placeholder="What did this employee sell today?"></textarea>
                    </div>
                    <div class="col-md-1 d-grid">
                        <label class="form-label small text-muted mb-1 d-none d-md-block">&nbsp;</label>
                        <button type="button" class="btn btn-outline-danger remove-employee-assignment">Remove</button>
                    </div>
                </div>
            </div>
        </template>

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

    // Edit sale modal handling (delegated + safe modal init)
    document.addEventListener('DOMContentLoaded', function () {
        const editModalEl = document.getElementById('editSaleModal');
        let editModal = null;
        if (editModalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try { editModal = new bootstrap.Modal(editModalEl); } catch (err) { editModal = null; }
            }
        }

        const saleDetailsModalEl = document.getElementById('saleDetailsModal');
        let saleDetailsModal = null;
        if (saleDetailsModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try { saleDetailsModal = new bootstrap.Modal(saleDetailsModalEl); } catch (err) { saleDetailsModal = null; }
        }

        const employeeTemplate = document.getElementById('employee-assignment-template');
        const createAssignmentsList = document.getElementById('create-employee-assignments-list');
        const editAssignmentsList = document.getElementById('edit-employee-assignments-list');
        const addCreateAssignmentBtn = document.getElementById('add-employee-assignment');
        const addEditAssignmentBtn = document.getElementById('edit-add-employee-assignment');
        const employeeNames = @json($employees->pluck('name', 'id')->all());
        const currencySymbol = @json($currencySymbol ?? '$');

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function formatMoney(value) {
            const amount = Number(value || 0);
            return currencySymbol + amount.toFixed(2);
        }

        function updateEmployeeAssignmentNames(container) {
            if (!container) return;

            container.querySelectorAll('.employee-assignment-row').forEach(function (row, index) {
                const select = row.querySelector('.employee-assignment-employee');
                const description = row.querySelector('.employee-assignment-description');

                if (select) {
                    select.name = `employee_assignments[${index}][employee_id]`;
                }

                if (description) {
                    description.name = `employee_assignments[${index}][description]`;
                }
            });
        }

        function createEmployeeAssignmentRow(assignment) {
            if (!employeeTemplate) return null;

            const fragment = employeeTemplate.content.cloneNode(true);
            const row = fragment.querySelector('.employee-assignment-row');
            const select = row ? row.querySelector('.employee-assignment-employee') : null;
            const description = row ? row.querySelector('.employee-assignment-description') : null;

            if (select && assignment && assignment.employee_id) {
                select.value = String(assignment.employee_id);
            }

            if (description && assignment && assignment.description) {
                description.value = assignment.description;
            }

            return row;
        }

        function renderEmployeeAssignments(container, assignments) {
            if (!container) return;

            container.innerHTML = '';

            const list = Array.isArray(assignments) && assignments.length
                ? assignments
                : [{ employee_id: '', description: '' }];

            list.forEach(function (assignment) {
                const row = createEmployeeAssignmentRow(assignment);
                if (row) {
                    container.appendChild(row);
                }
            });

            updateEmployeeAssignmentNames(container);
        }

        function bindEmployeeAssignmentControls(container, addButton) {
            if (!container || container.dataset.bound === '1') return;

            container.dataset.bound = '1';

            container.addEventListener('click', function (e) {
                const removeBtn = e.target.closest('.remove-employee-assignment');
                if (!removeBtn || !container.contains(removeBtn)) return;

                e.preventDefault();

                if (container.querySelectorAll('.employee-assignment-row').length > 1) {
                    removeBtn.closest('.employee-assignment-row')?.remove();
                    updateEmployeeAssignmentNames(container);
                }
            });

            if (addButton) {
                addButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    const row = createEmployeeAssignmentRow({ employee_id: '', description: '' });
                    if (row) {
                        container.appendChild(row);
                        updateEmployeeAssignmentNames(container);
                    }
                });
            }
        }

        bindEmployeeAssignmentControls(createAssignmentsList, addCreateAssignmentBtn);
        bindEmployeeAssignmentControls(editAssignmentsList, addEditAssignmentBtn);
        updateEmployeeAssignmentNames(createAssignmentsList);

        function openSaleDetails(btn) {
            if (!saleDetailsModalEl) return;

            const saleDate = btn.getAttribute('data-sale-date') || '-';
            const branch = btn.getAttribute('data-branch') || '-';
            const total = btn.getAttribute('data-total') || '0';
            const spent = btn.getAttribute('data-spent') || '0';
            const notesAttr = btn.getAttribute('data-notes') || 'null';
            const assignmentsAttr = btn.getAttribute('data-employee-assignments') || '[]';

            let notes = '';
            let assignments = [];

            try {
                notes = JSON.parse(notesAttr) || '';
            } catch (err) {
                notes = notesAttr;
            }

            try {
                assignments = JSON.parse(assignmentsAttr) || [];
            } catch (err) {
                assignments = [];
            }

            const detailsDate = saleDetailsModalEl.querySelector('#sale-details-date');
            const detailsBranch = saleDetailsModalEl.querySelector('#sale-details-branch');
            const detailsTotal = saleDetailsModalEl.querySelector('#sale-details-total');
            const detailsSpent = saleDetailsModalEl.querySelector('#sale-details-spent');
            const detailsNet = saleDetailsModalEl.querySelector('#sale-details-net');
            const detailsEmployees = saleDetailsModalEl.querySelector('#sale-details-employees');
            const detailsNotes = saleDetailsModalEl.querySelector('#sale-details-notes');

            if (detailsDate) detailsDate.textContent = saleDate;
            if (detailsBranch) detailsBranch.textContent = branch;
            if (detailsTotal) detailsTotal.textContent = formatMoney(total);
            if (detailsSpent) detailsSpent.textContent = formatMoney(spent);
            if (detailsNet) detailsNet.textContent = formatMoney(Number(total || 0) - Number(spent || 0));

            if (detailsEmployees) {
                if (assignments.length) {
                    detailsEmployees.innerHTML = assignments.map(function (assignment) {
                        const employeeName = employeeNames[String(assignment.employee_id)] || `Employee #${assignment.employee_id}`;
                        const description = assignment.description ? assignment.description : '-';
                        return `
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="fw-semibold">${escapeHtml(employeeName)}</div>
                                <div class="text-muted small mt-1">${escapeHtml(description)}</div>
                            </div>
                        `;
                    }).join('');
                } else {
                    detailsEmployees.innerHTML = '<div class="text-muted">-</div>';
                }
            }

            if (detailsNotes) {
                detailsNotes.textContent = notes ? notes : '-';
            }

            if (saleDetailsModal) {
                saleDetailsModal.show();
            } else if (window.jQuery && typeof jQuery(saleDetailsModalEl).modal === 'function') {
                jQuery(saleDetailsModalEl).modal('show');
            } else {
                saleDetailsModalEl.classList.add('show');
                saleDetailsModalEl.style.display = 'block';
                saleDetailsModalEl.removeAttribute('aria-hidden');
            }
        }

        const table = document.querySelector('.table');
        (table || document).addEventListener('click', function (e) {
            const detailsBtn = e.target.closest && e.target.closest('.sale-details-btn');
            if (detailsBtn) {
                e.preventDefault();
                openSaleDetails(detailsBtn);
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
            const employeeAssignmentsAttr = btn.getAttribute('data-employee_assignments') || '[]';
            const updateUrl = btn.getAttribute('data-update_url');

            let employeeAssignments = [];
            try {
                employeeAssignments = JSON.parse(employeeAssignmentsAttr) || [];
            } catch (err) {
                employeeAssignments = [];
            }

            const form = document.getElementById('editSaleForm');
            if (!form) return;
            form.action = updateUrl || form.action;
            const saleDateInput = form.querySelector('input[name="sale_date"]');
            const branchSelect = form.querySelector('select[name="branch_id"]');
            const totalInput = form.querySelector('input[name="total_amount"]');
            const spentInput = form.querySelector('input[name="spent_amount"]');
            const notesArea = form.querySelector('textarea[name="product_sold_text"]');
            const assignmentsContainer = form.querySelector('#edit-employee-assignments-list');

            if (saleDateInput) saleDateInput.value = saleDate || '';
            if (branchSelect) branchSelect.value = branchId || '';
            if (totalInput) totalInput.value = totalAmount || '';
            if (spentInput) spentInput.value = spentAmount || '';
            if (notesArea) notesArea.value = notes || '';
            renderEmployeeAssignments(assignmentsContainer, employeeAssignments.length ? employeeAssignments : [{ employee_id: '', description: '' }]);

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