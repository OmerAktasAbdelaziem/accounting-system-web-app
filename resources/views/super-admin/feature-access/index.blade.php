@extends('layouts.super-admin')

@section('title', 'Feature Access Control')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-toggles2"></i>
                Feature Access Control
            </h1>
            <p class="page-subtitle">Manage feature availability per role and merchant</p>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-orange alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Merchant Selection -->
<div class="form-section mb-4">
    <h5 class="mb-3">Select Merchant</h5>
    <div class="row g-3">
        <div class="col-md-8">
            <form id="merchantForm" method="GET" action="{{ route('super-admin.feature-access.index') }}">
                <select name="merchant_id" id="merchantSelect" class="form-select" required onchange="document.getElementById('merchantForm').submit();">
                    <option value="">-- Choose a Merchant --</option>
                    @foreach($merchants as $merchant)
                    <option value="{{ $merchant->id }}" {{ request('merchant_id') == $merchant->id ? 'selected' : '' }}>
                        {{ $merchant->name }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
        @if($selectedMerchant)
        <div class="col-md-4 d-flex gap-2">
            <form action="{{ route('super-admin.feature-access.reset') }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="merchant_id" value="{{ $selectedMerchant->id }}">
                <button type="submit" class="btn btn-outline-warning btn-sm" onclick="return confirm('Reset all features to package defaults?')">
                    <i class="icon icon-refresh-cw"></i> Reset to Package Defaults
                </button>
            </form>
            <a href="{{ route('super-admin.merchants.show', $selectedMerchant) }}" class="btn btn-outline-info btn-sm">
                <i class="icon icon-external-link"></i> View Merchant
            </a>
        </div>
        @endif
    </div>
</div>

    @if($selectedMerchant && $rows)
    <div class="card">
        <div class="card-header bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0">{{ $selectedMerchant->name }} - Feature Access Matrix</h5>
                </div>
                <div class="col-auto">
                    @php
                        $activeSubscription = $selectedMerchant->subscription()->where('is_active', true)->first();
                        $packageName = $activeSubscription?->package?->name ?? 'None';
                    @endphp
                    <small class="text-muted">Current Subscription: {{ $packageName }}</small>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" style="table-layout: fixed;">
                <thead class="table-light sticky-top">
                    <tr>
                        <th style="width: 200px;">Role / Feature</th>
                        @foreach($columns as $role)
                        <th class="text-center" style="width: 120px;">
                            <div class="text-truncate" title="{{ $role }}">{{ $role }}</div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $feature)
                    <tr>
                        <td class="fw-bold bg-light">
                            {{ ucfirst(str_replace('_', ' ', $feature)) }}
                        </td>
                        @foreach($columns as $role)
                        <td class="text-center">
                            @php
                                $access = $featureAccess[$feature][$role] ?? false;
                                $canEdit = true; // Check authorization if needed
                            @endphp
                            <form method="POST" action="{{ route('super-admin.feature-access.update') }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="merchant_id" value="{{ $selectedMerchant->id }}">
                                <input type="hidden" name="role" value="{{ $role }}">
                                <input type="hidden" name="feature" value="{{ $feature }}">
                                <input type="hidden" name="action" value="{{ $access ? 'disable' : 'enable' }}">

                                <button type="submit" class="btn btn-sm {{ $access ? 'btn-success' : 'btn-outline-secondary' }} toggle-btn" 
                                    title="Click to toggle" {{ !$canEdit ? 'disabled' : '' }}>
                                    @if($access)
                                    <i class="icon icon-check"></i> Enabled
                                    @else
                                    <i class="icon icon-x"></i> Disabled
                                    @endif
                                </button>
                            </form>
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col">
                    <strong>Legend:</strong>
                    <span class="badge bg-success ms-2">Enabled</span>
                    <span class="badge bg-secondary ms-2">Disabled</span>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        Matrix updated: {{ now()->format('M d, Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Merchant Employees</h5>
            <small class="text-muted">Special access overrides for individual employees</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Special Access</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        @php
                            $employeeGrantedFeatureKeys = ($employeeOverrides[$employee->id] ?? collect())->where('is_enabled', true)->pluck('feature_key')->toArray();
                            $employeeDeniedFeatureKeys = ($employeeOverrides[$employee->id] ?? collect())->where('is_enabled', false)->pluck('feature_key')->toArray();
                        @endphp
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>{{ $employee->role?->name ?? ucfirst($employee->user_type) }}</td>
                            <td>
                                @if(!empty($employeeGrantedFeatureKeys) || !empty($employeeDeniedFeatureKeys))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($employeeGrantedFeatureKeys as $featureKey)
                                            <span class="badge bg-info text-dark">{{ ucfirst(str_replace('_', ' ', $featureKey)) }}</span>
                                        @endforeach
                                        @foreach($employeeDeniedFeatureKeys as $featureKey)
                                            <span class="badge bg-danger">Denied: {{ ucfirst(str_replace('_', ' ', $featureKey)) }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No special access</span>
                                @endif
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-outline-orange edit-employee-access-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#employeeAccessModal"
                                        data-employee-id="{{ $employee->id }}"
                                        data-employee-name="{{ $employee->name }}"
                                        data-granted-feature-keys='@json($employeeGrantedFeatureKeys)'
                                        data-denied-feature-keys='@json($employeeDeniedFeatureKeys)'>
                                    Manage Access
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No employees found for this merchant</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header bg-light"><h5 class="mb-0">Feature Definitions</h5></div>
        <div class="card-body">
            <div class="alert alert-warning py-2">
                <strong>Note:</strong> employee-specific denies override role access for the selected pages.
            </div>
            <div class="row">
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li><strong>invoicing</strong> - Create and manage invoices</li>
                        <li><strong>payroll</strong> - Employee payroll management</li>
                        <li><strong>inventory</strong> - Inventory tracking and management</li>
                        <li><strong>basic_reporting</strong> - Basic financial reports</li>
                        <li><strong>advanced_reporting</strong> - Advanced analytics and custom reports</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="mb-0">
                        <li><strong>multi_branch</strong> - Support for multiple branches</li>
                        <li><strong>api_access</strong> - REST API access</li>
                        <li><strong>custom_integration</strong> - Third-party integrations</li>
                        <li><strong>dedicated_support</strong> - Priority support</li>
                        <li><strong>backup_restore</strong> - Automated backups</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @else
    <div class="alert alert-info">
        <strong>Select a merchant to view their feature access matrix</strong>
    </div>
    @endif
</div>

<div class="modal fade" id="employeeAccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('super-admin.feature-access.employee.update') }}" id="employeeAccessForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Manage Employee Special Access</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="merchant_id" value="{{ $selectedMerchant?->id }}">
                    <input type="hidden" name="user_id" id="employeeAccessUserId">
                    <div class="mb-3">
                        <strong id="employeeAccessName">Employee</strong>
                        <div class="text-muted small">Select the pages this employee should access in addition to their role access.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="decision" id="employeeAccessDecision" class="form-select" required>
                            <option value="grant">Grant selected pages</option>
                            <option value="deny">Deny selected pages</option>
                        </select>
                    </div>

                    <div class="row">
                        @foreach($availableFeatures as $featureKey => $featureLabel)
                            <div class="col-md-6 mb-2">
                                <div class="form-check border rounded p-2">
                                    <input class="form-check-input employee-feature-checkbox" type="checkbox" name="features[]" value="{{ $featureKey }}" id="employee_feature_{{ $featureKey }}">
                                    <label class="form-check-label" for="employee_feature_{{ $featureKey }}">
                                        <strong>{{ $featureLabel }}</strong>
                                        <div class="small text-muted">{{ $featureKey }}</div>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-orange">Save Access</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .toggle-btn {
        transition: all 0.3s ease;
        min-width: 100px;
    }
    .toggle-btn:hover {
        transform: scale(1.05);
    }
    .table-responsive {
        max-height: 70vh;
        overflow: auto;
    }
    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth form submission for toggle buttons
    const toggleForms = document.querySelectorAll('.toggle-btn');
    toggleForms.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            // Add loading state
            this.disabled = true;
            this.innerHTML = '<i class="icon icon-loader"></i> Updating...';
            // Submit form
            form.submit();
        });
    });

    const employeeAccessModal = document.getElementById('employeeAccessModal');
    if (employeeAccessModal) {
        employeeAccessModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) {
                return;
            }

            const employeeId = button.getAttribute('data-employee-id');
            const employeeName = button.getAttribute('data-employee-name');
            const grantedFeatureKeys = JSON.parse(button.getAttribute('data-granted-feature-keys') || '[]');
            const deniedFeatureKeys = JSON.parse(button.getAttribute('data-denied-feature-keys') || '[]');

            document.getElementById('employeeAccessUserId').value = employeeId;
            document.getElementById('employeeAccessName').textContent = employeeName;

            const decisionSelect = document.getElementById('employeeAccessDecision');
            decisionSelect.value = deniedFeatureKeys.length > 0 && grantedFeatureKeys.length === 0 ? 'deny' : 'grant';

            document.querySelectorAll('.employee-feature-checkbox').forEach(function (checkbox) {
                checkbox.checked = (decisionSelect.value === 'grant' ? grantedFeatureKeys : deniedFeatureKeys).includes(checkbox.value);
            });

            decisionSelect.addEventListener('change', function () {
                const activeKeys = this.value === 'grant' ? grantedFeatureKeys : deniedFeatureKeys;
                document.querySelectorAll('.employee-feature-checkbox').forEach(function (checkbox) {
                    checkbox.checked = activeKeys.includes(checkbox.value);
                });
            }, { once: true });
        });
    }
});
</script>
@endsection
