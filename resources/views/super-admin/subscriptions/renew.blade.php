@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title">{{ __('Renew Subscription') }}</h1>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ __('Validation Errors:') }}</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Current Subscription') }}</h5></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Merchant:') }}</strong><br>
                            {{ $subscription->merchant->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Package:') }}</strong><br>
                            {{ $subscription->package->name }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>{{ __('Current Expiry:') }}</strong><br>
                            <span class="text-danger fw-bold">{{ $subscription->expires_at->translatedFormat('M d, Y H:i') }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Days Until Expiry:') }}</strong><br>
                            @php $daysLeft = now()->diff($subscription->expires_at)->days; @endphp
                            <span class="badge {{ $daysLeft < 0 ? 'bg-danger' : ($daysLeft <= 7 ? 'bg-warning' : 'bg-success') }}">
                                {{ $daysLeft < 0 ? 'EXPIRED' : $daysLeft . ' days' }}
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong>{{ __('Package Price:') }}</strong><br>
                            {{ $currencySymbol }}{{ number_format($subscription->package->price, 2) }}
                        </div>
                        <div class="col-md-6">
                            <strong>{{ __('Package Duration:') }}</strong><br>
                            {{ $subscription->package->duration_days }} days
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Renewal Options') }}</h5></div>
                <form action="{{ route('super-admin.subscriptions.renew.store', $subscription) }}" method="POST">
                    {{ __('@csrf') }}
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label">{{ __('Choose Renewal Option') }}</label>
                            <div class="renewal-options">
                                @php
                                    $renewalOptions = [
                                        ['months' => 1, 'label' => '1 Month (Standard)', 'recommended' => true],
                                        ['months' => 3, 'label' => '3 Months (Save 5%)'],
                                        ['months' => 6, 'label' => '6 Months (Save 10%)'],
                                        ['months' => 12, 'label' => '1 Year (Save 15%)'],
                                    ];
                                    $packageDays = $subscription->package->duration_days;
                                @endphp

                                @foreach($renewalOptions as $option)
                                <div class="form-check mb-3">
                                    <input type="radio" name="renewal_option" class="form-check-input renewal-radio" id="option_{{ $option['months'] }}" value="{{ $option['months'] }}" {{ ($option['recommended'] ?? false) ? 'checked' : '' }} data-months="{{ $option['months'] }}">
                                    <label class="form-check-label" for="option_{{ $option['months'] }}">
                                        <strong>{{ $option['label'] }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            @php
                                                $packageDays = (int) $packageDays;
                                                $totalDays = (int) ($packageDays * $option['months']);
                                                $newExpiry = now()->copy()->addDays((int) $totalDays);
                                            @endphp
                                            New Expiry: {{ $newExpiry->translatedFormat('M d, Y') }} | Total Duration: {{ $totalDays }} days
                                        </small>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Custom Duration (optional)') }}</label>
                            <div class="input-group">
                                <input type="number" name="custom_days" class="form-control" placeholder="Enter number of days" min="1" value="{{ old('custom_days', (int) $subscription->{{ __('package->duration_days) }}">') }}
                                <span class="input-group-text">{{ __('days') }}</span>
                            </div>
                            <small class="text-muted">{{ __('Leave empty to use selected renewal option') }}</small>
                        </div>

                        <div class="alert alert-info">
                            <strong>{{ __('Price Summary:') }}</strong>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <small class="text-muted">{{ __('Base Price:') }}</small><br>
                                    {{ $currencySymbol }}{{ number_format($subscription->package->price, 2) }}
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">{{ __('Renewal Cost:') }}</small><br>
                                    <span id="renewalCost">{{ $currencySymbol }}{{ number_format($subscription->package->price, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="send_confirmation" class="form-check-input" id="sendConfirm" value="1" checked>
                            <label class="form-check-label" for="sendConfirm">{{ __('Send confirmation email to merchant admin') }}</label>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-success">{{ __('Renew Subscription') }}</button>
                        <a href="{{ route('super-admin.subscriptions.show', $subscription) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Renewal Timeline') }}</h5></div>
                <div class="card-body">
                    <div class="timeline-compact">
                        <div class="timeline-event">
                            <div class="event-marker"></div>
                            <div class="event-content">
                                <small class="text-muted">{{ __('Subscription Started') }}</small><br>
                                {{ $subscription->start_date->translatedFormat('M d, Y') }}
                            </div>
                        </div>

                        <div class="timeline-event">
                            <div class="event-marker active"></div>
                            <div class="event-content">
                                <small class="text-muted">{{ __('Currently Expires') }}</small><br>
                                <strong>{{ $subscription->expires_at->translatedFormat('M d, Y') }}</strong>
                            </div>
                        </div>

                        <div class="timeline-event">
                            <div class="event-marker future"></div>
                            <div class="event-content">
                                <small class="text-muted" id="futureLabel">{{ __('New Expiry (1 month)') }}</small><br>
                                <strong id="futureDate">{{ now()->addDays((int) $subscription->package->duration_days)->translatedFormat('M d, Y') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-light"><h5 class="mb-0">{{ __('Package Details') }}</h5></div>
                <div class="card-body">
                    <strong>{{ $subscription->package->name }}</strong>
                    <hr>
                    <strong>{{ __('Features:') }}</strong>
                    <ul class="small mb-3">
                        @forelse($subscription->package->features as $feature)
                        <li>{{ ucfirst(str_replace('_', ' ', $feature->feature_key)) }}</li>
                        {{ __('@empty') }}
                        <li><em>{{ __('No features listed') }}</em></li>
                        {{ __('@endforelse') }}
                    </ul>

                    <strong>{{ __('Limits:') }}</strong>
                    <ul class="small">
                        <li>Employees: {{ $subscription->package->max_employees ?? 'Unlimited' }}</li>
                        <li>Currencies: {{ $subscription->package->max_currencies }}</li>
                        <li>Languages: {{ $subscription->package->max_languages }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline-compact { padding-left: 0; }
    .timeline-event { display: flex; margin-bottom: 20px; }
    .event-marker { width: 12px; height: 12px; border-radius: 50%; background: #dee2e6; margin-right: 15px; margin-top: 3px; flex-shrink: 0; }
    .event-marker.active { background: #0d6efd; }
    .event-marker.future { background: #6c757d; }
    .event-content { flex: 1; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const renewalRadios = document.querySelectorAll('.renewal-radio');
    const customDaysInput = document.querySelector('input[name="custom_days"]');
    const renewalCostSpan = document.getElementById('renewalCost');
    const futureLabel = document.getElementById('futureLabel');
    const futureDate = document.getElementById('futureDate');

    const basePrice = {{ json_encode($subscription->package->price) }};
    const packageDays = {{ (int) $subscription->package->duration_days }};

    function updatePreview() {
        let days = parseInt(customDaysInput.value) || 0;

        if (days === 0) {
            const selectedMonths = document.querySelector('.renewal-radio:checked').value;
            days = packageDays * parseInt(selectedMonths);
        }

        const newExpiry = new Date();
        newExpiry.setDate(newExpiry.getDate() + days);

        futureLabel.textContent = `New Expiry (${days} days)`;
        futureDate.textContent = newExpiry.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

        // Calculate cost
        const cost = (basePrice / packageDays) * days;
        renewalCostSpan.textContent = '$' + cost.toFixed(2);
    }

    renewalRadios.forEach(radio => {
        radio.addEventListener('change', updatePreview);
    });

    customDaysInput.addEventListener('input', updatePreview);

    updatePreview();
});
</script>
@endsection
