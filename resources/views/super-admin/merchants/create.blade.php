@extends('layouts.super-admin')

@section('content')
<style>
    .create-shell { min-height: 100vh; padding: 32px 0 48px; background: linear-gradient(180deg, #f7f7f8 0%, #eef1f5 100%); }
    .create-hero { background: linear-gradient(135deg, #16181d 0%, #23262d 100%); color: #fff; border-radius: 28px; padding: 28px 30px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.18); }
    .create-card { border: 0; border-radius: 28px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.1); overflow: hidden; }
    .create-field { min-height: 52px; border-radius: 14px; border-color: #d9dde5; }
    .create-field:focus { border-color: #ff8c00; box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1); }
    .create-aside { border: 0; border-radius: 28px; background: linear-gradient(160deg, #ff8c00 0%, #ffb347 100%); color: #fff; box-shadow: 0 18px 50px rgba(255, 140, 0, 0.2); }
    .merchant-slot { background: rgba(255,255,255,.55); border-radius: 20px; }
</style>

<div class="create-shell">
    <div class="container-fluid">
        <div class="create-hero mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">Merchant Management</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ isset($merchant) ? 'Edit' : 'Create' }} Merchant</h1>
                <p class="mb-0 text-white-50">Set up the merchant and, when needed, seed its first admin users in one flow.</p>
            </div>
            <a href="{{ route('super-admin.merchants.index') }}" class="btn btn-light rounded-pill px-3"><i class="bi bi-arrow-left me-2"></i>Back</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-4 mb-4">
                <strong>Validation Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card create-card">
                    <form action="{{ isset($merchant) ? route('super-admin.merchants.update', $merchant) : route('super-admin.merchants.store') }}" method="POST">
                        @csrf
                        @if(isset($merchant)) @method('PUT') @endif
                        <div class="card-body p-4 p-lg-5">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Business Name *</label>
                                    <input type="text" name="business_name" class="form-control create-field @error('business_name') is-invalid @enderror" value="{{ old('business_name', $merchant->business_name ?? '') }}" required>
                                    @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Default Currency *</label>
                                    <select name="default_currency_id" class="form-select create-field @error('default_currency_id') is-invalid @enderror" required>
                                        <option value="">Select a currency</option>
                                        @foreach($currencies as $currency)
                                            <option value="{{ $currency->id }}" {{ old('default_currency_id', $merchant->default_currency_id ?? '') == $currency->id ? 'selected' : '' }}>{{ $currency->code }} - {{ $currency->name }} ({{ $currency->symbol }})</option>
                                        @endforeach
                                    </select>
                                    @error('default_currency_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Max Currencies Allowed *</label>
                                    <input type="number" name="max_currencies" class="form-control create-field @error('max_currencies') is-invalid @enderror" value="{{ old('max_currencies', $merchant->max_currencies ?? 5) }}" min="1" max="20" required>
                                    @error('max_currencies') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Max Languages Allowed *</label>
                                    <input type="number" name="max_languages" class="form-control create-field @error('max_languages') is-invalid @enderror" value="{{ old('max_languages', $merchant->max_languages ?? 3) }}" min="1" max="10" required>
                                    @error('max_languages') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Default Language *</label>
                                    <select name="default_language" class="form-select create-field @error('default_language') is-invalid @enderror" required>
                                        <option value="en" {{ old('default_language', $merchant->default_language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="ar" {{ old('default_language', $merchant->default_language ?? 'en') == 'ar' ? 'selected' : '' }}>Arabic</option>
                                        <option value="tr" {{ old('default_language', $merchant->default_language ?? 'en') == 'tr' ? 'selected' : '' }}>Turkish</option>
                                    </select>
                                    @error('default_language') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Max Employees (Leave empty for unlimited)</label>
                                    <input type="number" name="max_employees" class="form-control create-field @error('max_employees') is-invalid @enderror" value="{{ old('max_employees', $merchant->max_employees ?? '') }}" min="1">
                                    @error('max_employees') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control create-field @error('description') is-invalid @enderror" rows="3">{{ old('description', $merchant->description ?? '') }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                @if(isset($merchant))
                                    <div class="col-12">
                                        <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                                            <input type="checkbox" name="is_active" class="form-check-input ms-0" id="is_active" value="1" {{ old('is_active', $merchant->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="is_active">Active</label>
                                        </div>
                                    </div>
                                @endif

                                @if(!isset($merchant))
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Initial VAT Rate (%)</label>
                                        <input type="number" name="vat_rate" class="form-control create-field" value="{{ old('vat_rate', '') }}" min="0" max="100" step="0.01" placeholder="e.g., 15">
                                    </div>

                                    <div class="col-12">
                                        <h6 class="mb-2">Merchant Admin Users</h6>
                                        <small class="text-muted d-block mb-3">Create admin users that will have access to this merchant</small>
                                        <div id="adminUsersContainer">
                                            <div class="admin-user-slot merchant-slot mb-4 p-3 p-lg-4 border-0 shadow-sm">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Full Name *</label>
                                                        <input type="text" name="admin_users[0][name]" class="form-control create-field" placeholder="e.g., John Doe" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Email *</label>
                                                        <input type="email" name="admin_users[0][email]" class="form-control create-field" placeholder="e.g., admin@merchant.com" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Password *</label>
                                                        <div class="input-group">
                                                            <input type="password" name="admin_users[0][password]" class="form-control create-field" placeholder="Min 8 characters" required>
                                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(this)"><i class="bi bi-eye"></i></button>
                                                        </div>
                                                        <small class="text-muted">Min 8 characters, mix of uppercase, lowercase, numbers</small>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-3" onclick="removeAdminUser(this)" style="display:none;">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-outline-success mb-2" onclick="addAdminUserSlot()">
                                            <i class="bi bi-plus-circle me-1"></i> Add Another Admin User
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 p-4 p-lg-5 pt-0">
                            <button type="submit" class="btn btn-primary px-4">{{ isset($merchant) ? 'Update' : 'Create' }} Merchant</button>
                            <a href="{{ route('super-admin.merchants.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card create-aside h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h5 class="fw-bold mb-3">Merchant setup</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex gap-3"><i class="bi bi-building mt-1"></i><div>Start with the merchant profile, then optionally seed the first admins below.</div></div>
                            <div class="d-flex gap-3"><i class="bi bi-people mt-1"></i><div>The admin user slots stay compact and repeat cleanly when you add more.</div></div>
                            <div class="d-flex gap-3"><i class="bi bi-globe2 mt-1"></i><div>Currency, language, and capacity settings are grouped so they read as one policy block.</div></div>
                        </div>
                        <div class="card border-0 rounded-4 bg-white bg-opacity-10 mt-4">
                            <div class="card-body">
                                <small class="text-white-50 d-block mb-2">Default merchant preview</small>
                                <div id="merchantInfo"><small class="text-white-50">Select a merchant to see details</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-user-slot { transition: background-color 0.2s; }
    .admin-user-slot:hover { background-color: rgba(255,255,255,.72); }

    @media (max-width: 768px) {
        .create-hero {
            padding: 20px !important;
            flex-direction: column;
            align-items: stretch !important;
        }

        .create-hero .btn {
            width: 100%;
        }

        .create-card .card-body,
        .create-aside .card-body {
            padding: 18px !important;
        }

        .create-card .row.g-3 > [class*="col-md-"],
        .create-card .row.g-3 > [class*="col-xl-"] {
            width: 100%;
        }

        .create-field,
        .form-select,
        .form-control {
            min-height: 44px;
        }

        .merchant-slot .row.g-3 > [class*="col-md-"] {
            width: 100%;
        }

        .merchant-slot .btn {
            width: 100%;
        }

        .create-aside {
            margin-top: 0;
        }
    }

    @media (max-width: 576px) {
        .create-hero h1 {
            font-size: 24px;
        }

        .create-hero p {
            font-size: 13px;
        }
    }
</style>

<script>
    let adminUserCount = 1;

    function addAdminUserSlot() {
        const container = document.getElementById('adminUsersContainer');
        const newSlot = document.createElement('div');
        newSlot.className = 'admin-user-slot merchant-slot mb-4 p-3 p-lg-4 border-0 shadow-sm';
        newSlot.innerHTML = `
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Full Name *</label>
                    <input type="text" name="admin_users[${adminUserCount}][name]" class="form-control create-field" placeholder="e.g., John Doe" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email *</label>
                    <input type="email" name="admin_users[${adminUserCount}][email]" class="form-control create-field" placeholder="e.g., admin@merchant.com" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Password *</label>
                    <div class="input-group">
                        <input type="password" name="admin_users[${adminUserCount}][password]" class="form-control create-field" placeholder="Min 8 characters" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(this)"><i class="bi bi-eye"></i></button>
                    </div>
                    <small class="text-muted">Min 8 characters, mix of uppercase, lowercase, numbers</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger mt-3" onclick="removeAdminUser(this)"><i class="bi bi-trash"></i> Remove</button>
        `;
        container.appendChild(newSlot);
        adminUserCount++;
        updateRemoveButtons();
    }

    function removeAdminUser(button) {
        button.closest('.admin-user-slot').remove();
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const slots = document.querySelectorAll('.admin-user-slot');
        slots.forEach((slot) => {
            const removeBtn = slot.querySelector('button[onclick*="removeAdminUser"]');
            if (removeBtn) {
                removeBtn.style.display = slots.length > 1 ? 'block' : 'none';
            }
        });
    }

    function togglePasswordVisibility(button) {
        const input = button.parentElement.querySelector('input');
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', updateRemoveButtons);
</script>
@endsection
