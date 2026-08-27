@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="page-title">Package: {{ $package->name }}</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('super-admin.packages.edit', $package) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                <a href="{{ route('super-admin.packages.index') }}" class="btn btn-outline-secondary">{{ __('Back to Packages') }}</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Package Details') }}</h5></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Price:') }}</strong><br>
                            <span class="text-primary fs-5">{{ $currencySymbol }}{{ number_format($package->price, 2) }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Duration:') }}</strong><br>
                            {{ $package->duration_days }} days
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>{{ __('Max Employees:') }}</strong><br>
                            {{ $package->max_employees ?? 'Unlimited' }}
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('Max Currencies:') }}</strong><br>
                            {{ $package->max_currencies }}
                        </div>
                        <div class="col-md-4">
                            <strong>{{ __('Max Languages:') }}</strong><br>
                            {{ $package->max_languages }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>{{ __('Status:') }}</strong><br>
                        <span class="badge {{ $package->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $package->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    @if($package->description)
                    <div>
                        <strong>{{ __('Description:') }}</strong><br>
                        {{ $package->description }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Features Included') }}</h5></div>
                <div class="card-body">
                    @if($package->features->count() > 0)
                    <div class="list-group">
                        @foreach($package->features as $feature)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $feature->feature_key)) }}</h6>
                                <small class="text-muted">Added: {{ $feature->created_at->translatedFormat('M d, Y') }}</small>
                            </div>
                            @if($feature->description)
                            <p class="mb-0 text-muted">{{ $feature->description }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    {{ __('@else') }}
                    <div class="alert alert-info mb-0">{{ __('No features assigned to this package yet.') }}</div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Merchants Using This Package') }}</h5></div>
                <div class="card-body">
                    @php
                        $subscriptions = $package->subscriptions()->with('merchant')->latest()->get();
                    @endphp
                    @if($subscriptions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Merchant Name') }}</th>
                                    <th>{{ __('Subscribed Since') }}</th>
                                    <th>{{ __('Expires At') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <a href="{{ route('super-admin.merchants.show', $subscription->merchant) }}">
                                            {{ $subscription->merchant->name }}
                                        </a>
                                    </td>
                                    <td>{{ $subscription->start_date->translatedFormat('M d, Y') }}</td>
                                    <td>{{ $subscription->expires_at->translatedFormat('M d, Y') }}</td>
                                    <td>
                                        <span class="badge {{ $subscription->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $subscription->is_active ? 'Active' : 'Expired' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ __('@else') }}
                    <div class="alert alert-info mb-0">{{ __('No merchants currently using this package.') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Quick Actions') }}</h5></div>
                <div class="card-body">
                    <a href="{{ route('super-admin.packages.edit', $package) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                        <i class="icon icon-edit"></i> {{ __('Edit Package') }}
                    </a>
                    <button class="btn btn-outline-success btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#duplicateModal">
                        <i class="icon icon-copy"></i> {{ __('Duplicate Package') }}
                    </button>
                    @if($subscriptions->count() === 0)
                    <form method="POST" action="{{ route('super-admin.packages.destroy', $package) }}" style="display:inline-block; width:100%;">
                        {{ __('@csrf @method(\'DELETE\')') }}
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Delete this package?')">
                            <i class="icon icon-trash"></i> {{ __('Delete Package') }}
                        </button>
                    </form>
                    {{ __('@else') }}
                    <button class="btn btn-outline-danger btn-sm w-100" disabled title="Cannot delete package with active subscriptions">
                        <i class="icon icon-trash"></i> {{ __('Delete Package') }}
                    </button>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Package Statistics') }}</h5></div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <h3 class="text-primary">{{ $subscriptions->count() }}</h3>
                        <small class="text-muted">{{ __('Active Subscriptions') }}</small>
                    </div>
                    <div class="mb-3">
                        <h3 class="text-info">{{ $package->features->count() }}</h3>
                        <small class="text-muted">{{ __('Features Included') }}</small>
                    </div>
                    <div>
                        <h3 class="text-warning">{{ $currencySymbol }}{{ number_format($package->price, 2) }}</h3>
                        <small class="text-muted">{{ __('Monthly Price') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Duplicate Modal -->
<div class="modal fade" id="duplicateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('super-admin.packages.store') }}" method="POST">
                {{ __('@csrf') }}
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Duplicate Package') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('New Package Name *') }}</label>
                        <input type="text" name="name" class="form-control" value="Copy of {{ $package->{{ __('name }}" required>') }}
                    </div>
                    <input type="hidden" name="duplicate_from" value="{{ $package->{{ __('id }}">') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Duplicate') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
