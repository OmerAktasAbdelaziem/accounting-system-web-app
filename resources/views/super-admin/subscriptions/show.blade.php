@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Subscription Details</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-outline-secondary">Back to Subscriptions</a>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-light"><h5 class="mb-0">Subscription Overview</h5></div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Merchant:</strong><br>
                            <a href="{{ route('super-admin.merchants.show', $subscription->merchant) }}">
                                {{ $subscription->merchant->name }}
                            </a>
                        </div>
                        <div class="col-md-6">
                            <strong>Package:</strong><br>
                            <a href="{{ route('super-admin.packages.show', $subscription->package) }}">
                                {{ $subscription->package->name }}
                            </a>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>Subscription Start:</strong><br>
                            {{ $subscription->start_date->format('M d, Y H:i') }}
                        </div>
                        <div class="col-md-4">
                            <strong>Subscription Expires:</strong><br>
                            <span class="text-danger fw-bold">{{ $subscription->expires_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Days Remaining:</strong><br>
                            @php $daysLeft = now()->diff($subscription->expires_at)->days; @endphp
                            <span class="badge {{ $daysLeft < 0 ? 'bg-danger' : ($daysLeft <= 7 ? 'bg-warning' : 'bg-success') }} fs-6">
                                {{ $daysLeft < 0 ? 'EXPIRED' : $daysLeft . ' days' }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <strong>Status:</strong><br>
                            <span class="badge {{ $subscription->is_active ? 'bg-success' : 'bg-danger' }} fs-6">
                                {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Price:</strong><br>
                            {{ $currencySymbol }}{{ number_format($subscription->package->price, 2) }}
                        </div>
                        <div class="col-md-4">
                            <strong>Duration:</strong><br>
                            {{ $subscription->package->duration_days }} days
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-light"><h5 class="mb-0">Package Features</h5></div>
                <div class="card-body">
                    @if($subscription->package->features->count() > 0)
                    <div class="list-group">
                        @foreach($subscription->package->features as $feature)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $feature->feature_key)) }}</h6>
                                @if($feature->description)
                                <small class="text-muted">{{ $feature->description }}</small>
                                @endif
                            </div>
                            <span class="badge bg-success">Included</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-info mb-0">No features in this package.</div>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-light"><h5 class="mb-0">Limits</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Max Employees:</strong><br>
                            {{ $subscription->package->max_employees ?? 'Unlimited' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Max Currencies:</strong><br>
                            {{ $subscription->package->max_currencies }}
                        </div>
                        <div class="col-md-4">
                            <strong>Max Languages:</strong><br>
                            {{ $subscription->package->max_languages }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">Current Usage</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Active Employees:</strong><br>
                            @php $employeeCount = $subscription->merchant->users()->where('user_type', 'employee')->count(); @endphp
                            {{ $employeeCount }}/{{ $subscription->package->max_employees ?? '∞' }}
                            @if($employeeCount > 0)
                            <div class="progress mt-2" style="height: 20px;">
                                @php $percentage = $subscription->package->max_employees ? ($employeeCount / $subscription->package->max_employees * 100) : 0; @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <strong>Active Currencies:</strong><br>
                            {{ $subscription->merchant->currencies->count() }}/{{ $subscription->package->max_currencies }}
                            <div class="progress mt-2" style="height: 20px;">
                                @php $percentage = ($subscription->merchant->currencies->count() / $subscription->package->max_currencies * 100); @endphp
                                <div class="progress-bar" role="progressbar" style="width: {{ min($percentage, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <strong>Languages:</strong><br>
                            N/A
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-light"><h5 class="mb-0">Quick Actions</h5></div>
                <div class="card-body">
                    @if($subscription->is_active && $subscription->expires_at < now()->addDays(30))
                    <a href="{{ route('super-admin.subscriptions.renew', $subscription) }}" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="icon icon-refresh-cw"></i> Renew Subscription
                    </a>
                    @endif

                    <a href="{{ route('super-admin.subscriptions.recipients_preview', $subscription->merchant->id) }}" class="btn btn-outline-secondary btn-sm w-100 mb-2">
                        <i class="bi bi-people"></i> Preview Recipients
                    </a>

                    <a href="{{ route('super-admin.merchants.show', $subscription->merchant) }}" class="btn btn-info btn-sm w-100 mb-2">
                        <i class="icon icon-user"></i> View Merchant
                    </a>

                    @if($subscription->is_active)
                    <button class="btn btn-warning btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#extendModal">
                        <i class="icon icon-clock"></i> Extend Subscription
                    </button>
                    @endif

                    @if($subscription->is_active)
                        <form method="POST" action="{{ route('super-admin.subscriptions.destroy', $subscription) }}" style="display:inline-block; width:100%;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Deactivate this subscription? Merchant admins, employees, and users will be locked until reactivated.')">
                                <i class="bi bi-pause-circle"></i> Deactivate Subscription
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('super-admin.subscriptions.reactivate', $subscription) }}" style="display:inline-block; width:100%;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Reactivate this subscription and restore merchant access?')">
                                <i class="bi bi-play-circle"></i> Reactivate Subscription
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">Timeline</h5></div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <small class="text-muted">Started</small><br>
                                {{ $subscription->start_date->format('M d, Y') }}
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker {{ $subscription->is_active ? 'bg-info' : 'bg-danger' }}"></div>
                            <div class="timeline-content">
                                <small class="text-muted">{{ $subscription->is_active ? 'Expires' : 'Expired' }}</small><br>
                                {{ $subscription->expires_at->format('M d, Y') }}
                            </div>
                        </div>

                        @if($subscription->created_at != $subscription->updated_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <small class="text-muted">Last Updated</small><br>
                                {{ $subscription->updated_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Extend Subscription Modal -->
<div class="modal fade" id="extendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('super-admin.subscriptions.update', $subscription) }}">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Extend Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Extend by (days) *</label>
                        <input type="number" name="days" class="form-control" min="1" value="30" required>
                    </div>
                    <div class="alert alert-info">
                        Current expiry: <strong>{{ $subscription->expires_at->format('M d, Y') }}</strong><br>
                        <span id="newExpiry">New expiry: calculating...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Extend</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const extendInput = document.querySelector('input[name="extend_days"]');
    const newExpirySpan = document.getElementById('newExpiry');
    const currentExpiry = new Date('{{ $subscription->expires_at->toIso8601String() }}');

    function updateNewExpiry() {
        const days = parseInt(extendInput.value) || 0;
        const newDate = new Date(currentExpiry);
        newDate.setDate(newDate.getDate() + days);
        newExpirySpan.textContent = 'New expiry: ' + newDate.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    extendInput.addEventListener('input', updateNewExpiry);
    updateNewExpiry();
});

// Timeline styling
const style = document.createElement('style');
style.textContent = `
    .timeline { position: relative; padding-left: 20px; }
    .timeline-item { position: relative; padding-bottom: 20px; }
    .timeline-marker { position: absolute; left: -28px; width: 16px; height: 16px; border-radius: 50%; }
    .timeline-item::before { content: ''; position: absolute; left: -22px; top: 16px; width: 2px; height: 100%; background: #dee2e6; }
    .timeline-item:last-child::before { display: none; }
`;
document.head.appendChild(style);
</script>
@endsection
