@extends('layouts.super-admin')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </h1>
            <p class="page-subtitle">Welcome back, {{ auth()->user()->name }}! Here's your system overview.</p>
        </div>
        <a href="{{ route('super-admin.merchants.create') }}" class="btn btn-primary-orange">
            <i class="bi bi-plus-circle"></i> New Merchant
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-content">
                <h6>Total Merchants</h6>
                <div class="stat-value">{{ $totalMerchants }}</div>
            </div>
            <i class="bi bi-building stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-content">
                <h6>Active Subscriptions</h6>
                <div class="stat-value">{{ $activeSubscriptions }}</div>
            </div>
            <i class="bi bi-bookmark-check stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-content">
                <h6>Expiring Soon</h6>
                <div class="stat-value">{{ $expiringSoon }}</div>
            </div>
            <i class="bi bi-exclamation-circle stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-content">
                <h6>Total Revenue</h6>
                <div class="stat-value">${{ number_format($totalRevenue, 2) }}</div>
            </div>
            <i class="bi bi-cash-coin stat-icon"></i>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="form-section">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('super-admin.merchants.create') }}" class="btn btn-primary-orange">
                    <i class="bi bi-building"></i> Add New Merchant
                </a>
                <a href="{{ route('super-admin.packages.create') }}" class="btn btn-primary-orange">
                    <i class="bi bi-box-seam"></i> Create Package
                </a>
                <a href="{{ route('super-admin.subscriptions.create') }}" class="btn btn-primary-orange">
                    <i class="bi bi-bookmark-check"></i> New Subscription
                </a>
                <a href="{{ route('super-admin.feature-access.index') }}" class="btn btn-outline-orange">
                    <i class="bi bi-toggles2"></i> Manage Features
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Subscriptions -->
<div class="row">
    <div class="col-lg-8">
        <div class="form-section">
            <h5>Recent Subscriptions</h5>
            <div class="data-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Merchant</th>
                            <th>Package</th>
                            <th>Status</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubscriptions as $sub)
                            <tr>
                                <td>
                                    <strong>{{ $sub->merchant->name ?? 'N/A' }}</strong>
                                </td>
                                <td>{{ $sub->package->name ?? 'N/A' }}</td>
                                <td>
                                    @if($sub->is_active)
                                        <span class="badge-success">Active</span>
                                    @else
                                        <span class="badge-warning">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $sub->expires_at ? $sub->expires_at->format('M d, Y') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('super-admin.subscriptions.show', $sub->id) }}" class="btn btn-sm btn-outline-orange">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No subscriptions yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Expiring Subscriptions -->
    <div class="col-lg-4">
        <div class="form-section">
            <h5>Expiring Soon (30 days)</h5>
            <div class="data-table">
                @if($expiringSubscriptions->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($expiringSubscriptions as $sub)
                            <div class="list-group-item py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $sub->merchant->name }}</h6>
                                        <small class="text-muted">{{ $sub->package->name }}</small>
                                    </div>
                                    <span class="badge-warning">
                                        {{ $sub->expires_at->diffInDays() }} days
                                    </span>
                                </div>
                                <a href="{{ route('super-admin.subscriptions.show', $sub->id) }}" class="btn btn-sm btn-outline-orange mt-2 w-100">
                                    Renew
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-check-circle" style="font-size: 32px; opacity: 0.3;"></i>
                        <p class="mt-2">All subscriptions are healthy!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Packages Overview -->
<div class="row mt-4">
    <div class="col-12">
        <div class="form-section">
            <h5>Packages & Merchants</h5>
            <div class="data-table">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Package</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Active Merchants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($merchantsByPackage as $pkg)
                            <tr>
                                <td><strong>{{ $pkg->name }}</strong></td>
                                <td>${{ number_format($pkg->price, 2) }}</td>
                                <td>{{ $pkg->duration_days }} days</td>
                                <td>
                                    <span class="badge-orange">{{ $pkg->subscriptions_count }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('super-admin.packages.show', $pkg->id) }}" class="btn btn-sm btn-outline-orange">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No packages available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
