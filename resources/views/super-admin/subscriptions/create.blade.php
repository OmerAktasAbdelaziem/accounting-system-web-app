@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title">Create New Subscription</h1>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">Subscription Details</h5></div>
                <form action="{{ route('super-admin.subscriptions.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Select Merchant *</label>
                            <select name="merchant_id" class="form-select @error('merchant_id') is-invalid @enderror" id="merchantSelect" required>
                                <option value="">-- Choose a Merchant --</option>
                                @foreach(\App\Models\Merchant::where('is_active', true)->get() as $merchant)
                                <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>
                                    {{ $merchant->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('merchant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Package *</label>
                            <select name="package_id" class="form-select @error('package_id') is-invalid @enderror" id="packageSelect" required>
                                <option value="">-- Choose a Package --</option>
                                @foreach(\App\Models\Package::where('is_active', true)->get() as $package)
                                <option value="{{ $package->id }}" data-price="{{ $package->price }}" data-duration="{{ $package->duration_days }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} - {{ $currencySymbol }}{{ number_format($package->price, 2) }}/{{ $package->duration_days }} days
                                </option>
                                @endforeach
                            </select>
                            @error('package_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date *</label>
                                <input type="datetime-local" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration (days) *</label>
                                <input type="number" name="duration_months" class="form-control @error('duration_months') is-invalid @enderror" value="{{ old('duration_months', 1) }}" min="1" required>
                                <small class="text-muted">Package duration will be multiplied by this value</small>
                                @error('duration_months') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <strong>Preview:</strong>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <small class="text-muted">Package Price:</small><br>
                                        <span id="previewPrice">$0.00</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Total Duration:</small><br>
                                        <span id="previewDuration">0 days</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Estimated Expiry:</small><br>
                                    <span id="previewExpiry">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="send_notification" class="form-check-input" id="sendNotif" value="1" checked>
                            <label class="form-check-label" for="sendNotif">Send notification email to merchant admin</label>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-primary">Create Subscription</button>
                        <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light"><h5 class="mb-0">Package Features</h5></div>
                <div class="card-body">
                    <div id="packageFeatures">
                        <small class="text-muted">Select a package to see features</small>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-light"><h5 class="mb-0">Merchant Info</h5></div>
                <div class="card-body">
                    <div id="merchantInfo">
                        <small class="text-muted">Select a merchant to see details</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const packageSelect = document.getElementById('packageSelect');
    const startDateInput = document.querySelector('input[name="start_date"]');
    const durationInput = document.querySelector('input[name="duration_months"]');
    const currencySymbol = @json($currencySymbol);

    function updatePreview() {
        const option = packageSelect.selectedOptions[0];
        if (option && option.value) {
            const price = parseFloat(option.dataset.price);
            const baseDuration = parseInt(option.dataset.duration);
            const durationMultiplier = parseInt(durationInput.value) || 1;
            const totalDuration = baseDuration * durationMultiplier;

            document.getElementById('previewPrice').textContent = currencySymbol + price.toFixed(2);
            document.getElementById('previewDuration').textContent = totalDuration + ' days';

            const startDate = new Date(startDateInput.value);
            const expiryDate = new Date(startDate);
            expiryDate.setDate(expiryDate.getDate() + totalDuration);
            document.getElementById('previewExpiry').textContent = expiryDate.toLocaleDateString();

            // Load package features
            fetch(`/super-admin/packages/${option.value}/features`)
                .then(r => r.json())
                .then(data => {
                    let html = '<ul class="mb-0">';
                    if (data.features && data.features.length > 0) {
                        data.features.forEach(f => {
                            html += '<li>' + f + '</li>';
                        });
                    } else {
                        html = '<small class="text-muted">No features for this package</small>';
                    }
                    html += '</ul>';
                    document.getElementById('packageFeatures').innerHTML = html;
                })
                .catch(e => console.log('Features load error:', e));
        }
    }

    packageSelect.addEventListener('change', updatePreview);
    durationInput.addEventListener('change', updatePreview);
    startDateInput.addEventListener('change', updatePreview);

    // Load merchant info on selection
    const merchantSelect = document.getElementById('merchantSelect');
    merchantSelect.addEventListener('change', function() {
        if (this.value) {
            fetch(`/super-admin/merchants/${this.value}/details`)
                .then(r => r.json())
                .then(data => {
                    let html = `
                        <strong>${data.name}</strong><br>
                        <small class="text-muted">Email:</small> ${data.admin_email}<br>
                        <small class="text-muted">Phone:</small> ${data.phone || 'N/A'}<br>
                        <small class="text-muted">Max Employees:</small> ${data.max_employees || 'Unlimited'}<br>
                        <small class="text-muted">Max Currencies:</small> ${data.max_currencies}
                    `;
                    document.getElementById('merchantInfo').innerHTML = html;
                })
                .catch(e => console.log('Merchant load error:', e));
        }
    });

    updatePreview();
});
</script>
@endsection
