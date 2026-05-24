@extends('layouts.super-admin')

@section('title', 'Manage Subscriptions')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-bookmark-check"></i>
                Subscriptions
            </h1>
            <p class="page-subtitle">Manage merchant subscriptions and licenses</p>
        </div>
        <a href="{{ route('super-admin.subscriptions.create') }}" class="btn btn-primary-orange">
            <i class="bi bi-plus-circle"></i> New Subscription
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
    <div class="row g-3">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Search merchant..." >
        </div>
        <div class="col-md-4">
            <select id="statusFilter" class="form-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="expiring_soon">Expiring Soon (7 days)</option>
            </select>
        </div>
        <div class="col-md-4">
            <select id="packageFilter" class="form-select">
                <option value="">All Packages</option>
                @foreach(\App\Models\Package::where('is_active', true)->get() as $pkg)
                <option value="{{ $pkg->id }}">{{ $pkg->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="data-table">
    <table class="table table-hover" id="subscriptionsTable">
        <thead>
            <tr>
                <th>Merchant</th>
                <th>Package</th>
                <th>Started</th>
                <th>Expires</th>
                <th>Days Left</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $subscription)
            <tr>
                <td>
                    <strong>{{ $subscription->merchant->name }}</strong>
                </td>
                <td>{{ $subscription->package->name }}</td>
                <td>{{ $subscription->start_date->format('M d, Y') }}</td>
                <td>{{ $subscription->expires_at->format('M d, Y') }}</td>
                <td>
                    @php $daysLeft = now()->diff($subscription->expires_at)->days; @endphp
                    <span class="badge {{ $daysLeft < 0 ? 'bg-danger' : ($daysLeft <= 7 ? 'bg-warning' : 'bg-success') }}">
                        {{ $daysLeft < 0 ? 'EXPIRED' : $daysLeft . ' days' }}
                    </span>
                </td>
                <td>
                    <span class="badge-{{ $subscription->is_active ? 'success' : 'warning' }}">
                        {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" class="btn btn-sm btn-outline-orange" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        
                        <a href="{{ route('super-admin.subscriptions.recipients_preview', $subscription->merchant->id) }}" class="btn btn-sm btn-outline-secondary" title="Preview Recipients">
                            <i class="bi bi-people"></i>
                        </a>
                        @if($subscription->is_active && $subscription->expires_at < now()->addDays(30))
                        <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" class="btn btn-sm btn-outline-orange" title="Renew">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                        @endif
                        <form method="POST" action="{{ route('super-admin.subscriptions.destroy', $subscription) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-orange" title="Cancel" onclick="return confirm('Cancel this subscription?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 32px; opacity: 0.3;"></i>
                    <p class="mt-2">No subscriptions found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 d-flex justify-content-between align-items-center">
    <span class="text-muted">Total: {{ $subscriptions->total() }} subscriptions</span>
    @if($subscriptions->hasPages())
        <div>{{ $subscriptions->links() }}</div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const packageFilter = document.getElementById('packageFilter');
    const table = document.getElementById('subscriptionsTable');

    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const packageValue = packageFilter.value;

        Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
            let visible = true;

            if (searchValue) {
                const merchantName = row.cells[0].textContent.toLowerCase();
                visible = visible && merchantName.includes(searchValue);
            }

            if (statusValue) {
                const status = row.cells[5].textContent.toLowerCase();
                if (statusValue === 'expiring_soon') {
                    const daysLeft = parseInt(row.cells[4].textContent);
                    visible = visible && (daysLeft >= 0 && daysLeft <= 7);
                } else {
                    visible = visible && status.includes(statusValue);
                }
            }

            if (packageValue) {
                const packageName = row.cells[1].textContent;
                visible = visible && packageName === packageValue;
            }

            row.style.display = visible ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    statusFilter.addEventListener('change', filterTable);
    packageFilter.addEventListener('change', filterTable);
});
</script>
@endsection
