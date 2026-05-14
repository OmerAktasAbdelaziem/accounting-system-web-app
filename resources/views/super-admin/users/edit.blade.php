@extends('layouts.super-admin')

@section('title', 'Edit System User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="bi bi-person-check"></i> Edit System User
                </h1>
                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('super-admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- User Type -->
                        <div class="mb-3">
                            <label for="user_type" class="form-label">User Type *</label>
                            <select class="form-select @error('user_type') is-invalid @enderror" 
                                    id="user_type" name="user_type" required onchange="updateMerchantField()">
                                <option value="">Select User Type</option>
                                <option value="super_admin" {{ old('user_type', $user->user_type) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="merchant_admin" {{ old('user_type', $user->user_type) === 'merchant_admin' ? 'selected' : '' }}>Merchant Admin</option>
                                <option value="employee" {{ old('user_type', $user->user_type) === 'employee' ? 'selected' : '' }}>Employee</option>
                                <option value="viewer" {{ old('user_type', $user->user_type) === 'viewer' ? 'selected' : '' }}>Viewer</option>
                            </select>
                            @error('user_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Merchant (shown only for non-super-admin) -->
                        <div class="mb-3" id="merchant_field" style="display: none;">
                            <label for="merchant_id" class="form-label">Merchant *</label>
                            <select class="form-select @error('merchant_id') is-invalid @enderror" 
                                    id="merchant_id" name="merchant_id">
                                <option value="">Select Merchant</option>
                                @foreach ($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" {{ old('merchant_id', $user->merchant_id) == $merchant->id ? 'selected' : '' }}>
                                        {{ $merchant->business_name ?? $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('merchant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-3">
                            <label for="role_id" class="form-label">Role</label>
                            <select class="form-select @error('role_id') is-invalid @enderror" 
                                    id="role_id" name="role_id">
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                User is Active
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update User
                            </button>
                            <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
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

// Call on page load
document.addEventListener('DOMContentLoaded', updateMerchantField);
</script>
@endsection
