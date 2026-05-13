@extends('layouts.super-admin')

@section('title', 'Manage Merchants')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-building"></i>
                Merchants Management
            </h1>
            <p class="page-subtitle">Manage your merchant/dealer accounts and subscriptions</p>
        </div>
        <a href="{{ route('super-admin.merchants.create') }}" class="btn btn-primary-orange">
            <i class="bi bi-plus-circle"></i> Add New Merchant
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-orange alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filters -->
<div class="form-section mb-4">
    <form method="GET" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-orange w-100">
                <i class="bi bi-search"></i> Filter
            </button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('super-admin.merchants.index') }}" class="btn btn-outline-orange w-100">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Merchants Table -->
<div class="data-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Merchant Name</th>
                <th>Email</th>
                <th>Package</th>
                <th>Expires</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($merchants as $merchant)
                <tr>
                    <td>
                        <strong>{{ $merchant->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $merchant->address }}</small>
                    </td>
                    <td>{{ $merchant->admin_email }}</td>
                    <td>
                        @php
                            $activeSubscription = $merchant->subscription()->where('is_active', true)->first();
                        @endphp
                        @if($activeSubscription)
                            <span class="badge-orange">
                                {{ $activeSubscription->package->name }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($merchant->subscription_expires_at)
                            {{ $merchant->subscription_expires_at->format('M d, Y') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($merchant->is_active)
                            <span class="badge-success">Active</span>
                        @else
                            <span class="badge-warning">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('super-admin.merchants.show', $merchant->id) }}" class="btn btn-sm btn-outline-orange">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('super-admin.merchants.edit', $merchant->id) }}" class="btn btn-sm btn-outline-orange">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('super-admin.merchants.destroy', $merchant->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-orange" onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 32px; opacity: 0.3;"></i>
                        <p class="mt-2">No merchants found</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4 d-flex justify-content-center">
    {{ $merchants->links() }}
</div>
@endsection
