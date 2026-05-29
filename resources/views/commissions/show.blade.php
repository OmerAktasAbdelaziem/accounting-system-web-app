@extends('layouts.modern')

@section('title', __('messages.commission_details'))

@section('content')
@php
    $selectedBranchIds = $commission->branches()->pluck('branches.id')->all();
@endphp

<style>
    .commission-page-header {
        color: #fff !important;
    }

    .commission-page-header * {
        color: #fff !important;
    }

    .commission-mobile-list {
        display: none;
    }

    .commission-mobile-card {
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        padding: 14px;
    }

    .commission-mobile-card .meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 12px;
    }

    .commission-mobile-card .meta-item {
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 10px 12px;
    }

    .commission-mobile-card .label {
        display: block;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        margin-bottom: 4px;
    }

    .commission-mobile-card .value {
        font-weight: 700;
        color: #0f172a;
    }

    .commission-mobile-actions {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-top: 12px;
    }

    .commission-mobile-actions .btn,
    .commission-mobile-actions form {
        width: 100%;
    }

    .commission-mobile-actions .btn {
        min-height: 42px;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .mb-4.d-flex.justify-content-between.align-items-start.flex-wrap.gap-3 {
            flex-direction: column;
            align-items: stretch !important;
        }

        .mb-4.d-flex.justify-content-between.align-items-start.flex-wrap.gap-3 .d-flex.gap-2 {
            width: 100%;
        }

        .mb-4.d-flex.justify-content-between.align-items-start.flex-wrap.gap-3 .btn,
        .mb-4.d-flex.justify-content-between.align-items-start.flex-wrap.gap-3 form {
            width: 100%;
        }

        .row.mb-4 .col-md-3,
        .row.g-4 .col-lg-4,
        .row.g-4 .col-lg-8 {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            min-width: 760px;
        }

        .btn-group {
            flex-wrap: wrap;
        }

        .commission-desktop-table {
            display: none;
        }

        .commission-mobile-list {
            display: grid;
            gap: 12px;
        }

        .commission-mobile-actions {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        h1 {
            font-size: 22px;
        }

        .table {
            min-width: 640px;
        }

        .commission-mobile-card .meta {
            grid-template-columns: 1fr;
        }
    }

    /* Force all card header text on this page to white */
    .card-header {
        background: linear-gradient(135deg, #1a1a1a, #333) !important;
        color: #fff !important;
    }

    @media (max-width: 768px) {
        .card-header {
            background: linear-gradient(135deg, #ff8c00, #ffb347) !important;
        }
    }
</style>

<div class="mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
        <h1 class="mb-2" style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-person-badge" style="color: #ff8c00;"></i> {{ $employee->name }}
        </h1>
        <div class="text-muted">
            {{ $employee->employee_code ?? __('messages.employee') }}
            @if($employee->position)
                · {{ $employee->position }}
            @endif
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-outline-primary">
            <i class="bi bi-person"></i> {{ __('messages.view_employee') }}
        </a>
        @if($commission->status !== 'paid')
            <form method="POST" action="{{ route('commissions.pay', $commission) }}">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Mark this commission as paid? It will be hidden from the active list but stay in the database.')">
                    <i class="bi bi-cash-coin"></i> Pay Commission
                </button>
            </form>
        @else
            <span class="badge bg-success align-self-center">Paid</span>
        @endif
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="border-left: 4px solid #ff8c00;">
            <div class="card-body">
                <small class="text-muted d-block mb-2">{{ __('messages.total_commissions') }}</small>
                <h3 class="mb-0" style="color: #ff8c00;">{{ $currencySymbol }}{{ number_format($totalCommissions, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="border-left: 4px solid #27ae60;">
            <div class="card-body">
                <small class="text-muted d-block mb-2">{{ __('messages.sales_amount') }}</small>
                <h3 class="mb-0" style="color: #27ae60;">{{ $currencySymbol }}{{ number_format($totalSales, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="border-left: 4px solid #3498db;">
            <div class="card-body">
                <small class="text-muted d-block mb-2">{{ __('messages.commission_rate') }}</small>
                <h3 class="mb-0" style="color: #3498db;">{{ number_format($averageRate, 2) }}%</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card h-100" style="border-left: 4px solid #6f42c1;">
            <div class="card-body">
                <small class="text-muted d-block mb-2">{{ __('messages.commission_amount') }}</small>
                <h3 class="mb-0" style="color: #6f42c1;">{{ $currencySymbol }}{{ number_format($latestCommission?->commission_amount ?? 0, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header commission-page-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-person"></i> {{ __('messages.employee_information') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: linear-gradient(135deg, #ff8c00, #ffb347); color: white; font-size: 24px; font-weight: 800;">
                        {{ strtoupper(substr($employee->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $employee->name }}</h5>
                        <div class="text-muted small">{{ $employee->position ?? '-' }}</div>
                        <div class="text-muted small">{{ $employee->employee_code ?? '-' }}</div>
                    </div>
                </div>
                <hr>
                <div class="small text-muted mb-2">{{ __('messages.branches') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($employee->branches ?? [] as $branch)
                        <span class="badge bg-light text-dark border">{{ $branch->name }}</span>
                    @empty
                        <span class="text-muted">{{ __('messages.none') }}</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header commission-page-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> {{ __('messages.add_commission') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-4">Add a new commission for this employee from here. The general create page is only for first-time commission profiles.</p>

                <form method="POST" action="{{ route('commissions.append', $commission) }}">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.commission_date') }} *</label>
                        <input type="date" class="form-control @error('commission_date') is-invalid @enderror" name="commission_date" value="{{ old('commission_date', now()->format('Y-m-d')) }}" required>
                        @error('commission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.sale_amount') }} *</label>
                        <input type="text" inputmode="decimal" class="form-control @error('sale_amount') is-invalid @enderror" name="sale_amount" value="{{ old('sale_amount') }}" placeholder="0.00" required>
                        @error('sale_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.commission_rate') }} (%) *</label>
                        <input type="text" inputmode="decimal" class="form-control @error('commission_rate') is-invalid @enderror" name="commission_rate" value="{{ old('commission_rate', $employee->commission_rate ?? '') }}" placeholder="0" required>
                        @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.reference_type') }}</label>
                        <input type="text" class="form-control @error('reference_type') is-invalid @enderror" name="reference_type" value="{{ old('reference_type') }}" placeholder="Optional reference">
                        @error('reference_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.notes') }}</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3" placeholder="Optional notes">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.branches') }}</label>
                        @include('branches.partials.multi-select', ['branches' => $branches ?? [], 'selectedBranchIds' => $selectedBranchIds ?? []])
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle"></i> {{ __('messages.save') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header commission-page-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> {{ __('messages.commissions') }}</h5>
                    <span class="badge bg-light text-dark">{{ $commissions->count() }} records</span>
                </div>
            </div>
            <div class="table-responsive commission-desktop-table">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.sales_amount') }}</th>
                            <th>{{ __('messages.rate') }}</th>
                            <th>{{ __('messages.commission_amount') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $item)
                            <tr>
                                <td>{{ $item->commission_date?->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $currencySymbol }}{{ number_format($item->sale_amount, 2) }}</td>
                                <td>{{ number_format($item->commission_rate, 2) }}%</td>
                                <td><strong>{{ $currencySymbol }}{{ number_format($item->commission_amount, 2) }}</strong></td>
                                <td>
                                    <div class="commission-mobile-actions commission-action-grid">
                                        <a href="{{ route('commissions.edit', $item) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="bi bi-pencil"></i>
                                            <span class="ms-1">Edit</span>
                                        </a>
                                        @if($item->status !== 'paid')
                                            <form method="POST" action="{{ route('commissions.pay', $item) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success btn-sm" onclick="return confirm('Mark this commission as paid? It will disappear from the active list.')">
                                                    <i class="bi bi-cash-coin"></i>
                                                    <span class="ms-1">Pay</span>
                                                </button>
                                            </form>
                                        @else
                                            <span class="btn btn-outline-success disabled btn-sm"><i class="bi bi-check2-circle"></i><span class="ms-1">Paid</span></span>
                                        @endif
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteCommission({{ $item->id }})">
                                            <i class="bi bi-trash"></i>
                                            <span class="ms-1">Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No commissions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="commission-mobile-list mt-3">
                @forelse($commissions as $item)
                    <div class="commission-mobile-card">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-bold">{{ $item->commission_date?->format('M d, Y') ?? '-' }}</div>
                                <div class="text-muted small">{{ $currencySymbol }}{{ number_format($item->sale_amount, 2) }} sale · {{ number_format($item->commission_rate, 2) }}%</div>
                            </div>
                            <span class="badge {{ $item->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($item->status ?? 'draft') }}</span>
                        </div>

                        <div class="meta">
                            <div class="meta-item">
                                <span class="label">Commission</span>
                                <span class="value">{{ $currencySymbol }}{{ number_format($item->commission_amount, 2) }}</span>
                            </div>
                            <div class="meta-item">
                                <span class="label">Reference</span>
                                <span class="value">{{ $item->reference_type ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="commission-mobile-actions">
                            <a href="{{ route('commissions.edit', $item) }}" class="btn btn-outline-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            @if($item->status !== 'paid')
                                <form method="POST" action="{{ route('commissions.pay', $item) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-success" onclick="return confirm('Mark this commission as paid? It will disappear from the active list.')">
                                        <i class="bi bi-cash-coin"></i> Pay
                                    </button>
                                </form>
                            @else
                                <span class="btn btn-outline-success disabled"><i class="bi bi-check2-circle"></i> Paid</span>
                            @endif
                            <button type="button" class="btn btn-outline-danger" onclick="deleteCommission({{ $item->id }})">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                @empty
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header commission-page-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('messages.commission_details') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.commission_amount') }}</label>
                        <div class="text-success fw-bold">{{ $currencySymbol }}{{ number_format($latestCommission?->commission_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.commission_date') }}</label>
                        <div class="text-muted">{{ $latestCommission?->commission_date?->format('M d, Y') ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.sales_amount') }}</label>
                        <div class="text-muted">{{ $currencySymbol }}{{ number_format($latestCommission?->sale_amount ?? 0, 2) }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.reference_type') }}</label>
                        <div class="text-muted">{{ $latestCommission?->reference_type ?? '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status</label>
                        <div>
                            <span class="badge {{ $commission->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($commission->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">{{ __('messages.notes') }}</label>
                        <div class="text-muted">{{ $latestCommission?->notes ?? __('messages.no_notes') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function deleteCommission(id) {
        if (!confirm('Are you sure you want to delete this commission record?')) {
            return;
        }

        fetch(`/commissions/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection
