@extends('layouts.super-admin')

@section('title', 'Tax Rate Management')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-percent"></i>
                Tax Rates (VAT)
            </h1>
            <p class="page-subtitle">Configure VAT/tax rates for each merchant</p>
        </div>
        <button class="btn btn-primary-orange" data-bs-toggle="modal" data-bs-target="#addVatModal">
            <i class="bi bi-plus-circle"></i> Add Tax Rate
        </button>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-orange alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filters -->
<div class="form-section mb-4">
    <div class="row g-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Search merchant...">
        </div>
        <div class="col-md-6">
            <select id="statusFilter" class="form-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="no_rate">No Rate Set</option>
            </select>
        </div>
    </div>
</div>

<div class="data-table">
    <table class="table table-hover" id="vatTable">
                <thead class="table-light">
                    <tr>
                        <th>Merchant Name</th>
                        <th>Current VAT Rate</th>
                        <th>Applies To</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($merchants as $merchant)
                    @php $vat = $merchant->vatRates->first(); @endphp
                    <tr class="merchant-row" data-status="{{ $vat && $vat->is_active ? 'active' : ($vat ? 'inactive' : 'no_rate') }}">
                        <td>
                            <a href="{{ route('super-admin.merchants.show', $merchant) }}">
                                {{ $merchant->name }}
                            </a>
                        </td>
                        <td>
                            @if($vat)
                            <span class="badge bg-info">{{ $vat->rate_percentage }}%</span>
                            @else
                            <span class="badge bg-light text-dark">Not Set</span>
                            @endif
                        </td>
                        <td>
                            @if($vat)
                            <small class="text-muted">{{ ucfirst($vat->applies_to) }}</small>
                            @else
                            <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            @if($vat)
                            <span class="badge {{ $vat->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $vat->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @else
                            <span class="badge bg-warning">Not Configured</span>
                            @endif
                        </td>
                        <td>
                            @if($vat)
                            <small class="text-muted">{{ $vat->updated_at->format('M d, Y H:i') }}</small>
                            @else
                            <small class="text-muted">-</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                @if($vat)
                                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#editVatModal" 
                                    onclick="editVat({{ $vat->id }}, '{{ $merchant->id }}', '{{ $merchant->name }}', {{ $vat->rate_percentage }}, '{{ $vat->applies_to }}', {{ $vat->is_active ? 'true' : 'false' }})" title="Edit">
                                    <i class="icon icon-edit"></i>
                                </button>
                                <form method="POST" action="{{ route('super-admin.vat-rates.destroy', $vat) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Delete this VAT rate?')">
                                        <i class="icon icon-trash"></i>
                                    </button>
                                </form>
                                @else
                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#addVatModal" 
                                    onclick="setMerchantForAdd({{ $merchant->id }}, '{{ $merchant->name }}')" title="Add VAT Rate">
                                    <i class="icon icon-plus"></i> Add
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No merchants found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            <div class="d-flex align-items-center">
                <span class="text-muted">Total: {{ $merchants->count() }} merchants</span>
                @php
                    $activeVat = \App\Models\VatRate::where('is_active', true)->count();
                    $inactiveVat = \App\Models\VatRate::where('is_active', false)->count();
                @endphp
                <span class="ms-3 text-muted">Active: {{ $activeVat }} | Inactive: {{ $inactiveVat }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Add VAT Modal -->
<div class="modal fade" id="addVatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('super-admin.vat-rates.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add VAT Rate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Merchant *</label>
                        <select name="merchant_id" id="addMerchantSelect" class="form-select" required>
                            <option value="">-- Choose a Merchant --</option>
                            @foreach($merchants as $merchant)
                            @if(!$merchant->vatRates->first())
                            <option value="{{ $merchant->id }}">{{ $merchant->name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">VAT Rate (%) *</label>
                        <div class="input-group">
                            <input type="number" name="rate_percentage" class="form-control" step="0.01" min="0" max="100" value="0" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Examples: 5, 10, 15, 20</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Applies To *</label>
                        <select name="applies_to" class="form-select" required>
                            <option value="invoices">Invoices Only</option>
                            <option value="all">All Financial Transactions</option>
                        </select>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="addIsActive" value="1" checked>
                        <label class="form-check-label" for="addIsActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add VAT Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit VAT Modal -->
<div class="modal fade" id="editVatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editVatForm" action="" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit VAT Rate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Merchant</label>
                        <input type="text" id="editMerchantName" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">VAT Rate (%) *</label>
                        <div class="input-group">
                            <input type="number" id="editRatePercentage" name="rate_percentage" class="form-control" step="0.01" min="0" max="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Applies To *</label>
                        <select id="editAppliesto" name="applies_to" class="form-select" required>
                            <option value="invoices">Invoices Only</option>
                            <option value="all">All Financial Transactions</option>
                        </select>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="editIsActive" name="is_active" class="form-check-input" value="1">
                        <label class="form-check-label" for="editIsActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update VAT Rate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setMerchantForAdd(merchantId, merchantName) {
    document.getElementById('addMerchantSelect').value = merchantId;
}

function editVat(vatId, merchantId, merchantName, rate, appliesto, isActive) {
    document.getElementById('editMerchantName').value = merchantName;
    document.getElementById('editRatePercentage').value = rate;
    document.getElementById('editAppliesto').value = appliesto;
    document.getElementById('editIsActive').checked = isActive;
    document.getElementById('editVatForm').action = `/super-admin/vat-rates/${vatId}`;
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('vatTable');

    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
            let visible = true;

            if (searchValue) {
                const merchantName = row.cells[0].textContent.toLowerCase();
                visible = visible && merchantName.includes(searchValue);
            }

            if (statusValue) {
                const status = row.dataset.status;
                visible = visible && status === statusValue;
            }

            row.style.display = visible ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>
@endsection
