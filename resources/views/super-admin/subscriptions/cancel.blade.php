@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title">{{ __('Deactivate Subscription') }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">{{ __('⚠️ Confirm Subscription Deactivation') }}</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-4">
                        <strong>{{ __('Warning:') }}</strong> {{ __('This action will deactivate the subscription and lock merchant access until reactivation.') }}
                    </div>

                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Merchant:') }}</strong><br>
                                    <span class="fs-5">{{ $subscription->merchant->name }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Package:') }}</strong><br>
                                    <span class="fs-5">{{ $subscription->package->name }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>{{ __('Subscription Expires:') }}</strong><br>
                                    {{ $subscription->expires_at->translatedFormat('M d, Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Status:') }}</strong><br>
                                    <span class="badge {{ $subscription->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <strong>{{ __('Price:') }}</strong><br>
                                    {{ $currencySymbol }}{{ number_format($subscription->package->price, 2) }}
                                </div>
                                <div class="col-md-6">
                                    <strong>{{ __('Duration:') }}</strong><br>
                                    {{ $subscription->package->duration_days }} days
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6>{{ __('Impact of Deactivation:') }}</h6>
                    <ul class="mb-4">
                        <li>{{ __('All features included in this package will be disabled') }}</li>
                        <li>{{ __('The merchant will lose access to advanced features') }}</li>
                        <li>{{ __('Current data will remain but limited functionality will be available') }}</li>
                        <li>{{ __('To restore service, a new subscription must be created') }}</li>
                    </ul>

                    <form action="{{ route('super-admin.subscriptions.destroy', $subscription) }}" method="POST">
                        {{ __('@csrf @method(\'DELETE\')') }}

                        <div class="mb-3">
                            <label class="form-label">{{ __('Deactivation Reason (optional)') }}</label>
                            <textarea name="cancellation_reason" class="form-control" rows="3" placeholder="Document why this subscription is being deactivated..."></textarea>
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" name="send_notification" class="form-check-input" id="sendNotif" value="1" checked>
                            <label class="form-check-label" for="sendNotif">{{ __('Send deactivation notification email to merchant admin') }}</label>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" class="btn btn-outline-secondary">{{ __('Back') }}</a>
                            <button type="submit" class="btn btn-danger ms-auto">
                                <i class="bi bi-pause-circle"></i> {{ __('Confirm Deactivation') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Alternative Actions') }}</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>{{ __('Pause Instead:') }}</strong>
                            <p class="text-muted small">{{ __('Deactivate subscription temporarily without full cancellation') }}</p>
                            <form action="{{ route('super-admin.subscriptions.update', $subscription) }}" method="POST" style="display:inline;">
                                {{ __('@csrf @method(\'PUT\')') }}
                                <input type="hidden" name="status" value="pause">
                                <button type="submit" class="btn btn-outline-warning btn-sm">{{ __('Pause') }}</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Schedule Cancellation:') }}</strong>
                            <p class="text-muted small">{{ __('Cancel subscription at specific date') }}</p>
                            <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" class="btn btn-outline-info btn-sm">{{ __('Schedule') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
