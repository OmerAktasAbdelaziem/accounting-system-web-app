@extends('layouts.super-admin')

@section('title', 'Create System User')

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
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">{{ __('System Administration') }}</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ __('Create New System User') }}</h1>
                <p class="mb-0 text-white-50">{{ __('Add a user with explicit access and a cleaner form experience.') }}</p>
            </div>
            <a href="{{ route('super-admin.users.index') }}" class="btn btn-light rounded-pill px-3"><i class="bi bi-arrow-left me-2"></i>{{ __('Back') }}</a>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card create-card">
                    <div class="card-body p-4 p-lg-5">
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4 mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('super-admin.users.store') }}" class="row g-3">
                            {{ __('@csrf') }}
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">{{ __('Full Name *') }}</label>
                                <input type="text" class="form-control create-field @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                {{ __('@error(\'name\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">{{ __('Email *') }}</label>
                                <input type="email" class="form-control create-field @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                {{ __('@error(\'email\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">{{ __('Password *') }}</label>
                                <input type="password" class="form-control create-field @error('password') is-invalid @enderror" id="password" name="password" required>
                                {{ __('@error(\'password\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">{{ __('Confirm Password *') }}</label>
                                <input type="password" class="form-control create-field" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="col-md-6">
                                <label for="user_type" class="form-label fw-semibold">{{ __('User Type *') }}</label>
                                <select class="form-select create-field @error('user_type') is-invalid @enderror" id="user_type" name="user_type" required onchange="updateMerchantField()">
                                    <option value="">{{ __('Select User Type') }}</option>
                                    <option value="super_admin" {{ old('user_type') === 'super_admin' ? 'selected' : '' }}>{{ __('Super Admin') }}</option>
                                    <option value="merchant_admin" {{ old('user_type') === 'merchant_admin' ? 'selected' : '' }}>{{ __('Merchant Admin') }}</option>
                                    <option value="employee" {{ old('user_type') === 'employee' ? 'selected' : '' }}>{{ __('Employee') }}</option>
                                    <option value="viewer" {{ old('user_type') === 'viewer' ? 'selected' : '' }}>{{ __('Viewer') }}</option>
                                </select>
                                {{ __('@error(\'user_type\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                            </div>

                            <div class="col-md-6" id="merchant_field" style="display:none;">
                                <label for="merchant_id" class="form-label fw-semibold">{{ __('Merchant *') }}</label>
                                <select class="form-select create-field @error('merchant_id') is-invalid @enderror" id="merchant_id" name="merchant_id">
                                    <option value="">{{ __('Select Merchant') }}</option>
                                    @foreach ($merchants as $merchant)
                                        <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>{{ $merchant->business_name ?? $merchant->name }}</option>
                                    @endforeach
                                </select>
                                {{ __('@error(\'merchant_id\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                            </div>

                            <div class="col-md-6">
                                <label for="role_id" class="form-label fw-semibold">{{ __('Role') }}</label>
                                <select class="form-select create-field @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                                    <option value="">{{ __('Select Role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" data-description="{{ $role->description ?? 'No description available for this role.' }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                {{ __('@error(\'role_id\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                                <div class="mt-2 p-3 rounded-4 border bg-light" id="roleDescriptionBox">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1">{{ __('Role details') }}</div>
                                    <div id="roleDescriptionText" class="small text-secondary">{{ __('Select a role to see what access it gives.') }}</div>
                                </div>
                            </div>

                            <div class="col-12" id="branchAccessOverrideSection" style="display:none;">
                                <div class="p-4 rounded-4 border bg-white">
                                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                                        <div>
                                            <label class="form-label fw-semibold mb-1">{{ __('Branch access override') }}</label>
                                            <div class="text-muted small">{{ __('Override the role\'s branch visibility for this user.') }}</div>
                                        </div>
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Branch access mode">
                                            <input type="radio" class="btn-check" name="branch_access_mode" id="branch_access_mode_inherit" value="inherit" {{ old('branch_access_mode', 'inherit') === 'inherit' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-secondary" for="branch_access_mode_inherit">{{ __('Inherit') }}</label>

                                            <input type="radio" class="btn-check" name="branch_access_mode" id="branch_access_mode_custom" value="custom" {{ old('branch_access_mode') === 'custom' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-primary" for="branch_access_mode_custom">{{ __('Custom') }}</label>

                                            <input type="radio" class="btn-check" name="branch_access_mode" id="branch_access_mode_all" value="all" {{ old('branch_access_mode') === 'all' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-success" for="branch_access_mode_all">{{ __('All branches') }}</label>
                                        </div>
                                    </div>

                                    <div id="branchAccessCustomPanel" style="display:none;">
                                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                            <div class="text-muted small">{{ __('Pick the branches this user can access. Only the selected merchant\'s branches will be shown.') }}</div>
                                        </div>

                                        <div class="accordion" id="userBranchAccessAccordion">
                                            @foreach ($merchants as $merchant)
                                                <div class="accordion-item border-0 shadow-sm mb-3 rounded-4 overflow-hidden branch-access-merchant" data-merchant-branch-card="{{ $merchant->{{ __('id }}">') }}
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#user-merchant-branches-{{ $merchant->id }}">
                                                            {{ $merchant->name }}
                                                        </button>
                                                    </h2>
                                                    <div id="user-merchant-branches-{{ $merchant->{{ __('id }}" class="accordion-collapse collapse" data-bs-parent="#userBranchAccessAccordion">') }}
                                                        <div class="accordion-body bg-white">
                                                            <div class="row g-2">
                                                                @forelse ($merchant->branches as $branch)
                                                                    <div class="col-md-6">
                                                                        <div class="form-check p-3 border rounded-4 h-100">
                                                                            <input class="form-check-input user-branch-checkbox" type="checkbox" name="branch_access_branch_ids[]" value="{{ $branch->id }}" id="user_branch_{{ $branch->id }}" {{ in_array($branch->id, old('branch_access_branch_ids', [])) ? 'checked' : '' }}>
                                                                            <label class="form-check-label ms-2" for="user_branch_{{ $branch->{{ __('id }}">') }}
                                                                                <strong>{{ $branch->name }}</strong>
                                                                                <small class="d-block text-muted">{{ $branch->city ?? $branch->address ?? 'Branch' }}</small>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                {{ __('@empty') }}
                                                                    <div class="col-12 text-muted">{{ __('No branches found for this merchant.') }}</div>
                                                                {{ __('@endforelse') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">{{ __('Phone') }}</label>
                                <input type="text" class="form-control create-field @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                {{ __('@error(\'phone\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                                    <input type="checkbox" class="form-check-input ms-0" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="is_active">{{ __('User is Active') }}</label>
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-circle me-2"></i>{{ __('Create User') }}</button>
                                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary px-4"><i class="bi bi-x-circle me-2"></i>{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card create-aside h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h5 class="fw-bold mb-3">{{ __('Access rules') }}</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex gap-3"><i class="bi bi-shield-check mt-1"></i><div>{{ __('Choose the user type first so the merchant field appears only when needed.') }}</div></div>
                            <div class="d-flex gap-3"><i class="bi bi-diagram-3 mt-1"></i><div>{{ __('Role and merchant assignments stay visible in one clean workflow.') }}</div></div>
                            <div class="d-flex gap-3"><i class="bi bi-person-badge mt-1"></i><div>{{ __('Keep the form concise enough for quick staff provisioning.') }}</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateMerchantField() {
    const userType = document.getElementById('user_type').value;
    const merchantField = document.getElementById('merchant_field');
    const merchantSelect = document.getElementById('merchant_id');

    if (userType === 'super_admin') {
        merchantField.style.display = 'none';
        merchantSelect.removeAttribute('required');
    } else {
        merchantField.style.display = 'block';
        merchantSelect.setAttribute('required', 'required');
    }
}

function updateRoleDescription() {
    const roleSelect = document.getElementById('role_id');
    const roleDescriptionText = document.getElementById('roleDescriptionText');
    const selectedOption = roleSelect ? roleSelect.options[roleSelect.selectedIndex] : null;

    if (!roleDescriptionText || !selectedOption) {
        return;
    }

    const description = selectedOption.getAttribute('data-description');
    roleDescriptionText.textContent = description || 'Select a role to see what access it gives.';
}

function updateBranchAccessUI() {
    const userType = document.getElementById('user_type').value;
    const merchantId = document.getElementById('merchant_id').value;
    const branchSection = document.getElementById('branchAccessOverrideSection');
    const customPanel = document.getElementById('branchAccessCustomPanel');
    const selectedMode = document.querySelector('input[name="branch_access_mode"]:checked');

    if (!branchSection || !customPanel) {
        return;
    }

    if (userType === 'super_admin') {
        branchSection.style.display = 'none';
        return;
    }

    branchSection.style.display = 'block';
    customPanel.style.display = selectedMode && selectedMode.value === 'custom' ? 'block' : 'none';

    document.querySelectorAll('[data-merchant-branch-card]').forEach((card) => {
        card.style.display = merchantId && card.getAttribute('data-merchant-branch-card') === merchantId ? 'block' : 'none';
    });
}

function selectAllUserBranches() {
    document.querySelectorAll('.user-branch-checkbox:not(:disabled)').forEach((checkbox) => {
        checkbox.checked = true;
    });
}

document.addEventListener('DOMContentLoaded', updateMerchantField);
document.addEventListener('DOMContentLoaded', updateRoleDescription);
document.addEventListener('DOMContentLoaded', updateBranchAccessUI);
document.getElementById('role_id').addEventListener('change', updateRoleDescription);
document.getElementById('user_type').addEventListener('change', updateBranchAccessUI);
document.getElementById('merchant_id').addEventListener('change', updateBranchAccessUI);
document.querySelectorAll('input[name="branch_access_mode"]').forEach((input) => {
    input.addEventListener('change', updateBranchAccessUI);
});
document.getElementById('selectAllUserBranchesBtn')?.addEventListener('click', selectAllUserBranches);
</script>
@endsection
