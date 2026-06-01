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

        .sale-details-modal .modal-content {
            border: 0;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.18);
        }

        .sale-details-modal .modal-header {
            border: 0;
            background: linear-gradient(135deg, #111827 0%, #1f2937 55%, #ff8c00 140%);
            color: #fff;
            padding: 1.25rem 1.5rem;
        }

        .sale-details-modal .modal-header .field-hint {
            color: rgba(255, 255, 255, 0.72);
        }

        .sale-details-modal .modal-body {
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            padding: 1.5rem;
        }

        .detail-tile {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 18px;
            padding: 0.95rem 1rem;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            height: 100%;
        }

        .detail-tile .label {
            display: block;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            margin-bottom: 0.35rem;
        }

        .detail-tile .value {
            font-weight: 700;
            color: #111827;
            word-break: break-word;
        }

        .detail-tile .value.amount {
            font-size: 1.05rem;
        }

        .detail-notes {
            background: linear-gradient(180deg, rgba(255, 140, 0, 0.08), rgba(255, 255, 255, 0.95));
            border: 1px solid rgba(255, 140, 0, 0.14);
            border-radius: 18px;
            padding: 1rem 1.1rem;
        }

        .employee-breakdown-wrap {
            background: #fff;
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 22px;
            overflow: hidden;
        }

        .employee-breakdown-head {
            padding: 1rem 1.1rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            background: linear-gradient(90deg, rgba(255, 140, 0, 0.08), rgba(39, 174, 96, 0.05));
        }

        .employee-breakdown-list {
            margin: 0;
        }

        .employee-breakdown-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.1rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        }

        .employee-breakdown-item:last-child {
            border-bottom: 0;
        }

        .employee-badge {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            background: linear-gradient(135deg, #111827, #ff8c00);
            color: #fff;
            font-weight: 700;
        }

        .employee-breakdown-meta {
            min-width: 0;
            flex: 1;
        }

        .employee-breakdown-name {
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .employee-breakdown-amount {
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            background: rgba(255, 140, 0, 0.12);
            color: #9a3412;
            font-weight: 700;
            white-space: nowrap;
        }

        .export-mode-section {
            display: none;
        }

        .export-mode-section.active {
            display: block;
        }

        @media (max-width: 768px) {
            .sales-hero {
                background: linear-gradient(160deg, #ffffff 0%, #fff8ef 56%, #fff1df 100%);
                color: #111827;
                border: 1px solid rgba(255, 140, 0, 0.12);
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
                padding: 20px !important;
            }

            .sales-hero .text-white-75,
            .sales-hero .text-white-50,
            .sales-hero p,
            .sales-hero .small {
                color: #64748b !important;
            }

            .sales-hero .bg-white.bg-opacity-10 {
                background: rgba(255, 255, 255, 0.84) !important;
                color: #111827 !important;
                border: 1px solid rgba(255, 140, 0, 0.08);
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
            }

            .sales-panel,
            .sales-stat,
            .employee-breakdown-wrap,
            .sale-details-modal .modal-content {
                border-radius: 22px;
            }

            .sales-panel .card-body,
            .sales-panel .card-header {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }

            #employee-sales-list {
                gap: 12px !important;
            }

            .employee-sale-item {
                border-radius: 18px !important;
                background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(248,250,252,0.98)) !important;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
            }

            .employee-sale-item .row {
                --bs-gutter-x: 0.75rem;
            }

            .employee-sale-item .btn {
                width: 100%;
            }

            .sales-stat {
                border: 1px solid rgba(255, 140, 0, 0.12);
            }

            .sales-empty {
                background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(255,249,242,0.98));
            }

            .sales-panel .card-header .badge,
            .sales-panel .card-header .btn {
                width: 100%;
            }

            .sales-panel .card-header .d-flex {
                width: 100%;
            }

            .sales-panel .form-select,
            .sales-panel .form-control {
                min-height: 48px;
                border-radius: 16px;
            }

            #open-sales-export-modal,
            #sales-filter-form .btn,
            .add-employee-field,
            .remove-employee-row,
            .btn.btn-primary.btn-lg {
                border-radius: 16px;
            }

            .sale-details-modal .modal-body {
                padding: 16px;
            }

            .sale-details-modal .employee-breakdown-item {
                padding: 0.9rem 0.95rem;
                flex-direction: column;
                align-items: flex-start;
            }

            .sale-details-modal .employee-breakdown-amount {
                align-self: stretch;
                text-align: center;
            }

            .export-mode-section .card,
            .export-mode-section .alert {
                border-radius: 18px;
            }

            .d-grid.gap-2.mt-4 {
                position: sticky;
                bottom: 14px;
                z-index: 2;
                background: rgba(255,255,255,0.82);
                backdrop-filter: blur(16px);
                padding: 10px;
                border: 1px solid rgba(255,255,255,0.9);
                border-radius: 20px;
                box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
            }

            .d-grid.gap-2.mt-4 .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .sales-hero h2 {
                font-size: 24px;
            }

            .sales-hero .col-6 .fs-4 {
                font-size: 1.1rem !important;
            }

            .sales-panel .card-header {
                gap: 10px;
            }
        }
    </style>

    <div class="sales-hero rounded-4 p-4 p-lg-5 mb-4">
        <div class="row align-items-end g-4 position-relative" style="z-index:1;">
            <div class="col-lg-7">
                <div class="sales-history-chip mb-3">
                    <i class="bi bi-receipt"></i>
                    Daily sales workspace
                </div>
                <h2 class="fw-bold mb-2 text-white">Sales</h2>
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
            @feature('sales.create')
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
                            <input type="number" name="total_amount" class="form-control form-control-lg js-total-amount" min="0.01" step="0.01" value="{{ old('total_amount') }}" required>
                            <div class="field-hint mt-1">Enter total manually.</div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Amount Spent by Store <span class="text-muted">(Optional)</span></label>
                            <input type="number" name="spent_amount" class="form-control form-control-lg" min="0" step="0.01" value="{{ old('spent_amount', 0) }}" placeholder="Amount the store spent">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Employees Involved</label>
                            <div class="field-hint mb-2">Choose one or more employees and add the amount each one sold today.</div>
                            <div class="small text-muted">Optional — leave empty to record the sale without per-employee breakdown.</div>
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
                @endfeature
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
                <div class="d-flex align-items-center gap-2">
                    @feature('downloads')
                    <button type="button" id="open-sales-export-modal" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i>
                        Download PDF
                    </button>
                    @endfeature
                    <span class="badge text-bg-light border">{{ $sales->total() }} results</span>
                </div>
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
                            <th style="width: 44px;">
                                <input type="checkbox" id="select-all-sales" class="form-check-input" title="Select all visible sales">
                            </th>
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
                                <td>
                                    <input type="checkbox" class="form-check-input sale-select-checkbox" value="{{ $sale->id }}" aria-label="Select sale {{ $sale->id }}">
                                </td>
                                <td>{{ $sale->sale_date?->format('Y-m-d') }}</td>
                                <td>{{ $sale->branch?->name ?? '-' }}</td>
                                <td>
                                    @feature('sales.view')
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
                                        data-sale-employees='@json($sale->employeeSaleDetails->map(fn ($detail) => ["name" => $detail->employee?->name ?? "Deleted employee", "amount" => (float) $detail->amount])->values(), JSON_HEX_APOS)'
                                    >
                                        {{ $currencySymbol ?? '$' }}{{ number_format((float) $sale->total_amount, 2) }}
                                    </button>
                                    @endfeature
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
                                        @feature('sales.edit')
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-primary edit-sale-btn"
                                            data-id="{{ $sale->id }}"
                                            data-sale_date="{{ $sale->sale_date?->format('Y-m-d') }}"
                                            data-branch_id="{{ $sale->branch_id }}"
                                            data-total_amount="{{ (float) $sale->total_amount }}"
                                            data-spent_amount="{{ (float) ($sale->spent_amount ?? 0) }}"
                                            data-notes="{{ e($sale->notes) }}"
                                                    data-primary-employee-id="{{ $sale->employee_id }}"
                                                    data-employee-sales='@json($sale->employeeSaleDetails->map(fn ($detail) => ["employee_id" => $detail->employee_id, "amount" => (float) $detail->amount])->values(), JSON_HEX_APOS)'
                                            data-update_url="{{ route('sales.update', $sale->id) }}"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @endfeature
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
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

            @if($sales->hasPages())
                <div class="card-footer bg-white px-4 py-3">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>

        @feature('downloads')
        <div class="modal fade" id="salesExportModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form id="sales-export-form" method="POST" action="{{ route('sales.export-pdf') }}">
                        @csrf
                        <input type="hidden" name="export_mode" id="sales-export-mode-input" value="selected">

                        <div class="modal-header">
                            <h5 class="modal-title">Satış PDF İndir</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sales_export_mode_radio" id="export-mode-selected" value="selected" checked>
                                    <label class="form-check-label" for="export-mode-selected">Seçili satırlar</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="sales_export_mode_radio" id="export-mode-date" value="date">
                                    <label class="form-check-label" for="export-mode-date">Tarih filtresi</label>
                                </div>
                            </div>

                            <div id="export-selected-section" class="export-mode-section active">
                                <div class="alert alert-light border mb-0">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div>
                                            <div class="fw-semibold mb-1">Dışa aktarılacak seçili satışlar</div>
                                            <div class="small text-muted">Tabloda seç kutularını kullanarak satırları seçin, ardından yalnızca bu satırları dışa aktarın.</div>
                                        </div>
                                        <span class="badge text-bg-primary" id="selected-sales-count">0 seçili</span>
                                    </div>
                                </div>
                            </div>

                            <div id="export-date-section" class="export-mode-section">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label"Şube</label>
                                        <select name="branch_id" class="form-select">
                                            <option value="">Tüm şubeler</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" @selected((string) request('branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Başlangıç</label>
                                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Bitiş</label>
                                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                    </div>
                                </div>
                                <div class="small text-muted mt-2">Tarih aralığının tümünü dışa aktarmak için tarihleri boş bırakın (ısteğe bağlı şube filtresi ile).</div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-download"></i>
                                Satış PDF İndir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endfeature
    </div>
</div>

        <div class="modal fade sale-details-modal" id="saleDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge text-bg-light text-dark border">Sale details</span>
                                <span class="badge text-bg-warning">Employee breakdown</span>
                            </div>
                            <h5 class="modal-title mb-0 fw-bold">Employees Involved</h5>
                            <div class="field-hint">Review who contributed to the sale and the amounts they sold.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 col-xl-3">
                                <div class="detail-tile">
                                    <span class="label">Date</span>
                                    <div class="value" id="sale-details-date">-</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="detail-tile">
                                    <span class="label">Branch</span>
                                    <div class="value" id="sale-details-branch">-</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="detail-tile">
                                    <span class="label">Primary Employee</span>
                                    <div class="value" id="sale-details-primary-employee">-</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="detail-tile">
                                    <span class="label">Total Amount</span>
                                    <div class="value amount" id="sale-details-total">-</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="detail-tile">
                                    <span class="label">Spent</span>
                                    <div class="value amount" id="sale-details-spent">-</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-3">
                                <div class="detail-tile">
                                    <span class="label">Net</span>
                                    <div class="value amount" id="sale-details-net">-</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-notes">
                                    <div class="label mb-2">Description</div>
                                    <div class="value" id="sale-details-notes">-</div>
                                </div>
                            </div>
                        </div>

                        <div class="employee-breakdown-wrap">
                            <div class="employee-breakdown-head d-flex justify-content-between align-items-center gap-2">
                                <h6 class="mb-0">Employees involved</h6>
                                <span class="badge text-bg-light border" id="sale-details-count-badge">0 employees</span>
                            </div>
                            <div id="sale-details-list" class="employee-breakdown-list"></div>
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
                                    <input type="number" name="total_amount" class="form-control js-total-amount" step="0.01" min="0.01" required>
                                    <div class="field-hint mt-1">Enter total manually.</div>
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

    function formatMoney(value) {
        const number = Number(value || 0);
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(number);
    }

    function parseJsonDataAttribute(raw, fallback = []) {
        if (!raw) return fallback;

        try {
            return JSON.parse(raw);
        } catch (e1) {
            try {
                const decoded = String(raw)
                    .replaceAll('&quot;', '"')
                    .replaceAll('&#34;', '"')
                    .replaceAll('&apos;', "'")
                    .replaceAll('&#39;', "'");
                return JSON.parse(decoded);
            } catch (e2) {
                return fallback;
            }
        }
    }

    function updateTotalFromEmployeeList(container, totalInput) {
        // Total amount is manual now; keep function as no-op for existing bindings.
        return;
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
        const exportModalEl = document.getElementById('salesExportModal');
        let editModal = null;
        let saleDetailsModal = null;
        let exportModal = null;
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
        if (exportModalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try { exportModal = new bootstrap.Modal(exportModalEl); } catch (err) { exportModal = null; }
            }
        }

        function getSelectedSaleCheckboxes() {
            return Array.from(document.querySelectorAll('.sale-select-checkbox:checked'));
        }

        function updateSelectedSalesCount() {
            const countBadge = document.getElementById('selected-sales-count');
            if (!countBadge) return;
            const count = getSelectedSaleCheckboxes().length;
            countBadge.textContent = `${count} selected`;
        }

        function syncExportModeUI(mode) {
            const selectedSection = document.getElementById('export-selected-section');
            const dateSection = document.getElementById('export-date-section');
            const modeInput = document.getElementById('sales-export-mode-input');

            if (modeInput) {
                modeInput.value = mode;
            }
            if (selectedSection) {
                selectedSection.classList.toggle('active', mode === 'selected');
            }
            if (dateSection) {
                dateSection.classList.toggle('active', mode === 'date');
            }
            
            // When switching to date mode, ensure fields are properly set
            if (mode === 'date') {
                const fromDateInput = document.querySelector('input[name="from_date"]');
                const toDateInput = document.querySelector('input[name="to_date"]');
                // Keep the existing values or leave empty to export all
                if (fromDateInput && toDateInput) {
                    // Fields are ready for input
                }
            }
        }

        function openExportModal() {
            updateSelectedSalesCount();
            syncExportModeUI(document.querySelector('input[name="sales_export_mode_radio"]:checked')?.value || 'selected');

            if (exportModal) {
                try { exportModal.show(); return; } catch (e) { /* fallback below */ }
            }
            if (window.jQuery && typeof jQuery(exportModalEl).modal === 'function') {
                jQuery(exportModalEl).modal('show');
                return;
            }
            if (exportModalEl) {
                exportModalEl.classList.add('show');
                exportModalEl.style.display = 'block';
                exportModalEl.removeAttribute('aria-hidden');
            }
        }

        const openExportBtn = document.getElementById('open-sales-export-modal');
        if (openExportBtn) {
            openExportBtn.addEventListener('click', function (e) {
                e.preventDefault();
                openExportModal();
            });
        }

        document.querySelectorAll('input[name="sales_export_mode_radio"]').forEach((radio) => {
            radio.addEventListener('change', function () {
                syncExportModeUI(radio.value);
            });
        });

        const selectAllSales = document.getElementById('select-all-sales');
        if (selectAllSales) {
            selectAllSales.addEventListener('change', function () {
                const allCheckboxes = Array.from(document.querySelectorAll('.sale-select-checkbox'));
                allCheckboxes.forEach((checkbox) => {
                    checkbox.checked = selectAllSales.checked;
                });
                updateSelectedSalesCount();
            });
        }

        document.addEventListener('change', function (event) {
            if (event.target && event.target.classList && event.target.classList.contains('sale-select-checkbox')) {
                const all = Array.from(document.querySelectorAll('.sale-select-checkbox'));
                const checked = all.filter((checkbox) => checkbox.checked);
                const selectAll = document.getElementById('select-all-sales');
                if (selectAll) {
                    selectAll.checked = all.length > 0 && checked.length === all.length;
                }
                updateSelectedSalesCount();
            }
        });

        const salesExportForm = document.getElementById('sales-export-form');
        if (salesExportForm) {
            salesExportForm.addEventListener('submit', function (event) {
                salesExportForm.querySelectorAll('input[name="sale_ids[]"]').forEach((input) => input.remove());

                const mode = document.getElementById('sales-export-mode-input')?.value || 'selected';

                if (mode === 'selected') {
                    const selectedIds = getSelectedSaleCheckboxes().map((checkbox) => checkbox.value).filter(Boolean);
                    if (selectedIds.length === 0) {
                        event.preventDefault();
                        alert('Lütfen indirmeden önce en az bir satış seçin.');
                        return;
                    }

                    selectedIds.forEach((id) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'sale_ids[]';
                        input.value = id;
                        salesExportForm.appendChild(input);
                    });
                } else if (mode === 'date') {
                    // Validate that at least one date is provided for date export
                    const fromDate = document.querySelector('input[name="from_date"]')?.value?.trim();
                    const toDate = document.querySelector('input[name="to_date"]')?.value?.trim();
                    
                    if (!fromDate && !toDate) {
                        // Allow empty dates to export all records
                        console.log('No date range specified - exporting all records');
                    }
                }
            });
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

                const saleEmployees = parseJsonDataAttribute(detailsBtn.getAttribute('data-sale-employees'), []);

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
                const saleDetailsCountBadge = document.getElementById('sale-details-count-badge');

                if (saleDetailsDate) saleDetailsDate.textContent = detailsBtn.getAttribute('data-sale-date') || '-';
                if (saleDetailsBranch) saleDetailsBranch.textContent = detailsBtn.getAttribute('data-branch-name') || '-';
                if (saleDetailsPrimary) saleDetailsPrimary.textContent = primaryEmployee;
                if (saleDetailsTotal) saleDetailsTotal.textContent = '{{ $currencySymbol ?? '$' }}' + formatMoney(detailsBtn.getAttribute('data-total-amount'));
                if (saleDetailsSpent) saleDetailsSpent.textContent = '{{ $currencySymbol ?? '$' }}' + formatMoney(detailsBtn.getAttribute('data-spent-amount'));
                if (saleDetailsNet) saleDetailsNet.textContent = '{{ $currencySymbol ?? '$' }}' + formatMoney(detailsBtn.getAttribute('data-net-amount'));
                if (saleDetailsNotes) saleDetailsNotes.textContent = detailsBtn.getAttribute('data-sale-notes') || '-';
                if (saleDetailsCountBadge) saleDetailsCountBadge.textContent = `${employees.length} ${employees.length === 1 ? 'employee' : 'employees'}`;

                if (saleDetailsList) {
                    saleDetailsList.innerHTML = employees.length
                        ? employees.map((employee, index) => `
                            <div class="employee-breakdown-item">
                                <div class="d-flex align-items-center gap-3 min-w-0">
                                    <div class="employee-badge">${String(index + 1).padStart(2, '0')}</div>
                                    <div class="employee-breakdown-meta">
                                        <div class="employee-breakdown-name">${escapeHtml(employee.name || 'Deleted employee')}</div>
                                        <div class="text-muted small">Sale contributor</div>
                                    </div>
                                </div>
                                <div class="employee-breakdown-amount">{{ $currencySymbol ?? '$' }}${formatMoney(employee.amount)}</div>
                            </div>
                        `).join('')
                        : '<div class="p-4 text-center text-muted">No employee details recorded for this sale.</div>';
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
            const employeeSalesData = parseJsonDataAttribute(btn.getAttribute('data-employee-sales'), []);
            const updateUrl = btn.getAttribute('data-update_url');
            const parsedTotalAmount = Number(totalAmount || 0);

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
                    : [{ employee_id: btn.getAttribute('data-primary-employee-id') || '', amount: parsedTotalAmount }];

                editEmployeeList.innerHTML = rows.map((row, index) => editEmployeeTemplate.innerHTML.replaceAll('__INDEX__', String(index))).join('');

                editEmployeeList.querySelectorAll('.employee-sale-item').forEach((rowEl, index) => {
                    const rowData = rows[index] || {};
                    const employeeSelect = rowEl.querySelector('select[name^="employee_sales"]');
                    const amountInput = rowEl.querySelector('.employee-sale-amount');
                    const amountValue = Number(rowData.amount ?? 0);
                    if (employeeSelect) employeeSelect.value = String(rowData.employee_id || '');
                    if (amountInput) amountInput.value = amountValue > 0 ? String(amountValue) : (rows.length === 1 ? String(parsedTotalAmount || '') : String(rowData.amount ?? ''));
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