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

    <div class="card mt-3">
        <div class="card-header bg-light"><h5 class="mb-0">Feature Definitions</h5></div>
        <div class="card-body">
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
});
</script>
@endsection
