@extends('layouts.super-admin')

@section('content')
<style>
    .create-shell { min-height: 100vh; padding: 32px 0 48px; background: linear-gradient(180deg, #f7f7f8 0%, #eef1f5 100%); }
    .create-hero { background: linear-gradient(135deg, #16181d 0%, #23262d 100%); color: #fff; border-radius: 28px; padding: 28px 30px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.18); }
    .create-card { border: 0; border-radius: 28px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.1); overflow: hidden; }
    .create-field { min-height: 52px; border-radius: 14px; border-color: #d9dde5; }
    .create-field:focus { border-color: #ff8c00; box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1); }
    .create-aside { border: 0; border-radius: 28px; background: linear-gradient(160deg, #ff8c00 0%, #ffb347 100%); color: #fff; box-shadow: 0 18px 50px rgba(255, 140, 0, 0.2); }
</style>

<div class="create-shell">
    <div class="container-fluid">
        <div class="create-hero mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">Subscriptions</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">Create New Subscription</h1>
                <p class="mb-0 text-white-50">Tie a merchant to a package in a cleaner, more guided flow.</p>
            </div>
            <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-light rounded-pill px-3"><i class="bi bi-arrow-left me-2"></i>Back</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-4 mb-4">
                <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card create-card">
                    <form action="{{ route('super-admin.subscriptions.store') }}" method="POST">
                        @csrf
                        <div class="card-body p-4 p-lg-5">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Select Merchant *</label>
                                    <select name="merchant_id" class="form-select create-field @error('merchant_id') is-invalid @enderror" id="merchantSelect" required>
                                        <option value="">-- Choose a Merchant --</option>
                                        @foreach(\App\Models\Merchant::where('is_active', true)->get() as $merchant)
                                            <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>{{ $merchant->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('merchant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Select Package *</label>
                                    <select name="package_id" class="form-select create-field @error('package_id') is-invalid @enderror" id="packageSelect" required>
                                        <option value="">-- Choose a Package --</option>
                                        @foreach(\App\Models\Package::where('is_active', true)->get() as $package)
                                            <option value="{{ $package->id }}" data-price="{{ $package->price }}" data-duration="{{ $package->duration_days }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>{{ $package->name }} - {{ $currencySymbol }}{{ number_format($package->price, 2) }}/{{ $package->duration_days }} days</option>
                                        @endforeach
                                    </select>
                                    @error('package_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Start Date *</label>
                                    <input type="datetime-local" name="start_date" class="form-control create-field @error('start_date') is-invalid @enderror" value="{{ old('start_date', now()->format('Y-m-d\TH:i')) }}" required>
                                    @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Duration (days) *</label>
                                    <input type="number" name="duration_months" class="form-control create-field @error('duration_months') is-invalid @enderror" value="{{ old('duration_months', 1) }}" min="1" required>
                                    <small class="text-muted">Package duration will be multiplied by this value</small>
                                    @error('duration_months') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <div class="card border-0 rounded-4 bg-light">
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
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="send_notification" class="form-check-input" id="sendNotif" value="1" checked>
                                        <label class="form-check-label" for="sendNotif">Send notification email to merchant admin</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 p-4 p-lg-5 pt-0">
                            <button type="submit" class="btn btn-primary px-4">Create Subscription</button>
                            <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card create-aside h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h5 class="fw-bold mb-3">Helpful panels</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex gap-3"><i class="bi bi-box-seam mt-1"></i><div>Package and merchant details are surfaced beside the form so the decision stays visible.</div></div>
                            <div class="d-flex gap-3"><i class="bi bi-bell mt-1"></i><div>Notification is opt-in here, with the checkbox styled to stand out without shouting.</div></div>
                            <div class="d-flex gap-3"><i class="bi bi-calendar3 mt-1"></i><div>The preview keeps duration and expiry calculation easy to verify before saving.</div></div>
                        </div>
                        <div class="card border-0 rounded-4 bg-white bg-opacity-10 mt-4">
                            <div class="card-body">
                                <div id="packageFeatures"><small class="text-white-50">Select a package to see features</small></div>
                                <hr class="border-white border-opacity-25">
                                <div id="merchantInfo"><small class="text-white-50">Select a merchant to see details</small></div>
                            </div>
                        </div>
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
