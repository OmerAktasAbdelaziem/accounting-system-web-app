@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header d-flex align-items-center justify-content-between mb-4">
        <h1 class="page-title">{{ $merchant->business_name }}</h1>
        <div>
            <a href="{{ route('super-admin.merchants.edit', $merchant) }}" class="btn btn-secondary">
                <i class="bi bi-pencil"></i> Edit
            </a>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('error') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        @if($errors->any())
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Basic Info -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Business Name</label>
                        <p class="mb-0">{{ $merchant->business_name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Merchant Slug</label>
                        <p class="mb-0"><code>{{ $merchant->slug }}</code></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Status</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $merchant->is_active ? 'success' : 'danger' }}">
                                {{ $merchant->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Default Language</label>
                        <p class="mb-0">{{ $merchant->default_language === 'en' ? 'English' : ($merchant->default_language === 'ar' ? 'Arabic' : 'Turkish') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Max Employees</label>
                        <p class="mb-0">{{ $merchant->max_employees ?? 'Unlimited' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Info -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Subscription</h5>
                </div>
                <div class="card-body">
                    @if($activeSubscription)
                        <div class="mb-3">
                            <label class="text-muted small">Package</label>
                            <p class="mb-0"><strong>{{ $activeSubscription->package->name }}</strong></p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Status</label>
                            <p class="mb-0">
                                <span class="badge bg-success">Active</span>
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Expires</label>
                            <p class="mb-0">
                                {{ $activeSubscription->expires_at->format('F d, Y') }}
                                @if($daysRemaining)
                                    <br><small class="text-muted">({{ $daysRemaining }} days remaining)</small>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Amount Paid</label>
                            <p class="mb-0">{{ $currencySymbol }}{{ number_format($activeSubscription->amount_paid, 2) }}</p>
                        </div>
                        <a href="{{ route('super-admin.subscriptions.renew', $activeSubscription) }}" class="btn btn-sm btn-primary" onclick="return confirm('Renew this subscription?')">
                            <i class="bi bi-arrow-repeat"></i> Renew
                        </a>
                    @else
                        <p class="text-muted">No active subscription</p>
                        <a href="{{ route('super-admin.subscriptions.create', ['merchant_id' => $merchant->id]) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i> Add Subscription
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Currencies -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Currencies ({{ $merchant->currencies()->count() }}/{{ $merchant->max_currencies }})</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCurrencyModal">
                        <i class="bi bi-plus"></i> Add
                    </button>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($merchant->currencies as $currency)
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $currency->code }}</strong> - {{ $currency->name }}
                            <br>
                            <small>{{ $currency->symbol }}</small>
                            @if($merchant->defaultCurrency->id === $currency->id)
                                <br><span class="badge bg-success">Default</span>
                            @endif
                        </div>
                        @if($merchant->defaultCurrency->id !== $currency->id)
                        <form action="{{ route('super-admin.merchants.removeCurrency', [$merchant, $currency]) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this currency?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- VAT Rate -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">VAT Configuration</h5>
                </div>
                <div class="card-body">
                    @php
                        $vatRate = $merchant->vatRates()->first();
                    @endphp
                    <form action="{{ route('super-admin.merchants.updateVat', $merchant) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">VAT Rate (%)</label>
                            <input type="number" name="rate" class="form-control" value="{{ $vatRate?->rate ?? 0 }}" min="0" max="100" step="0.01">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_enabled" class="form-check-input" id="vat_enabled" 
                                       value="1" {{ $vatRate?->is_enabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="vat_enabled">Enable VAT</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update VAT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Users & Employees</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($merchant->users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</td>
                                <td>{{ $user->role?->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No users</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Currency Modal -->
<div class="modal fade" id="addCurrencyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Currency</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('super-admin.merchants.addCurrency', $merchant) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Currency</label>
                        <select name="currency_id" class="form-select" required>
                            <option value="">Choose a currency</option>
                            @php
                                $usedCurrencyIds = $merchant->currencies()->pluck('currency_id')->toArray();
                            @endphp
                            @foreach(\App\Models\Currency::all() as $currency)
                                @if(!in_array($currency->id, $usedCurrencyIds))
                                    <option value="{{ $currency->id }}">{{ $currency->code }} - {{ $currency->name }} ({{ $currency->symbol }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_default" class="form-check-input" id="is_default">
                        <label class="form-check-label" for="is_default">Set as default currency</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Currency</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
