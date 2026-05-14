@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title">{{ isset($merchant) ? 'Edit' : 'Create' }} Merchant</h1>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Merchant Information</h5>
                </div>
                <form action="{{ isset($merchant) ? route('super-admin.merchants.update', $merchant) : route('super-admin.merchants.store') }}" method="POST">
                    @csrf
                    @if(isset($merchant)) @method('PUT') @endif

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Business Name *</label>
                            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                   value="{{ old('business_name', $merchant->business_name ?? '') }}" required>
                            @error('business_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Currency *</label>
                            <select name="default_currency_id" class="form-select @error('default_currency_id') is-invalid @enderror" required>
                                <option value="">Select a currency</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" 
                                        {{ old('default_currency_id', $merchant->default_currency_id ?? '') == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->code }} - {{ $currency->name }} ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('default_currency_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Currencies Allowed *</label>
                                <input type="number" name="max_currencies" class="form-control @error('max_currencies') is-invalid @enderror"
                                       value="{{ old('max_currencies', $merchant->max_currencies ?? 5) }}" min="1" max="20" required>
                                @error('max_currencies') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Languages Allowed *</label>
                                <input type="number" name="max_languages" class="form-control @error('max_languages') is-invalid @enderror"
                                       value="{{ old('max_languages', $merchant->max_languages ?? 3) }}" min="1" max="10" required>
                                @error('max_languages') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Default Language *</label>
                                <select name="default_language" class="form-select @error('default_language') is-invalid @enderror" required>
                                    <option value="en" {{ old('default_language', $merchant->default_language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="ar" {{ old('default_language', $merchant->default_language ?? 'en') == 'ar' ? 'selected' : '' }}>Arabic</option>
                                    <option value="tr" {{ old('default_language', $merchant->default_language ?? 'en') == 'tr' ? 'selected' : '' }}>Turkish</option>
                                </select>
                                @error('default_language') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Employees (Leave empty for unlimited)</label>
                                <input type="number" name="max_employees" class="form-control @error('max_employees') is-invalid @enderror"
                                       value="{{ old('max_employees', $merchant->max_employees ?? '') }}" min="1">
                                @error('max_employees') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $merchant->description ?? '') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if(isset($merchant))
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                       value="1" {{ old('is_active', $merchant->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        @endif

                        @if(!isset($merchant))
                        <div class="mb-3">
                            <label class="form-label">Initial VAT Rate (%)</label>
                            <input type="number" name="vat_rate" class="form-control" value="{{ old('vat_rate', '') }}" 
                                   min="0" max="100" step="0.01" placeholder="e.g., 15">
                        </div>

                        <hr>
                        <h6 class="mb-3">Merchant Admin Users</h6>
                        <small class="text-muted d-block mb-3">Create admin users that will have access to this merchant</small>
                        
                        <div id="adminUsersContainer">
                            <div class="admin-user-slot mb-4 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" name="admin_users[0][name]" class="form-control" placeholder="e.g., John Doe" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="admin_users[0][email]" class="form-control" placeholder="e.g., admin@merchant.com" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Password *</label>
                                        <div class="input-group">
                                            <input type="password" name="admin_users[0][password]" class="form-control" placeholder="Min 8 characters" required>
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(this)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Min 8 characters, mix of uppercase, lowercase, numbers</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAdminUser(this)" style="display:none;">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-outline-success mb-3" onclick="addAdminUserSlot()">
                            <i class="bi bi-plus-circle"></i> Add Another Admin User
                        </button>
                        @endif
                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($merchant) ? 'Update' : 'Create' }} Merchant
                        </button>
                        <a href="{{ route('super-admin.merchants.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .admin-user-slot {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    .admin-user-slot:hover {
        background-color: #e9ecef;
    }
</style>

<script>
    let adminUserCount = 1;

    function addAdminUserSlot() {
        const container = document.getElementById('adminUsersContainer');
        const newSlot = document.createElement('div');
        newSlot.className = 'admin-user-slot mb-4 p-3 border rounded';
        newSlot.innerHTML = `
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="admin_users[${adminUserCount}][name]" class="form-control" placeholder="e.g., John Doe" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="admin_users[${adminUserCount}][email]" class="form-control" placeholder="e.g., admin@merchant.com" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Password *</label>
                    <div class="input-group">
                        <input type="password" name="admin_users[${adminUserCount}][password]" class="form-control" placeholder="Min 8 characters" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility(this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Min 8 characters, mix of uppercase, lowercase, numbers</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAdminUser(this)">
                <i class="bi bi-trash"></i> Remove
            </button>
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
        slots.forEach((slot, index) => {
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

    // Initialize remove buttons on page load
    document.addEventListener('DOMContentLoaded', updateRemoveButtons);
</script>
@endsection
