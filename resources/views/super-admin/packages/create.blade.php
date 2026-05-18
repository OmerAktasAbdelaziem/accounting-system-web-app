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
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">Subscription Plans</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ isset($package) ? 'Edit' : 'Create' }} Package</h1>
                <p class="mb-0 text-white-50">Define pricing and features in a clearer workspace.</p>
            </div>
            <a href="{{ route('super-admin.packages.index') }}" class="btn btn-light rounded-pill px-3"><i class="bi bi-arrow-left me-2"></i>Back</a>
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
                    <form action="{{ isset($package) ? route('super-admin.packages.update', $package) : route('super-admin.packages.store') }}" method="POST">
                        @csrf @if(isset($package)) @method('PUT') @endif
                        <div class="card-body p-4 p-lg-5">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Package Name *</label>
                                    <input type="text" name="name" class="form-control create-field @error('name') is-invalid @enderror" value="{{ old('name', $package->name ?? '') }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control create-field" rows="3">{{ old('description', $package->description ?? '') }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Price ($) *</label>
                                    <input type="number" name="price" class="form-control create-field @error('price') is-invalid @enderror" value="{{ old('price', $package->price ?? '') }}" step="0.01" min="0" required>
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Duration (days) *</label>
                                    <input type="number" name="duration_days" class="form-control create-field @error('duration_days') is-invalid @enderror" value="{{ old('duration_days', $package->duration_days ?? '30') }}" min="1" required>
                                    @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Max Employees (null = unlimited)</label>
                                    <input type="number" name="max_employees" class="form-control create-field @error('max_employees') is-invalid @enderror" value="{{ old('max_employees', $package->max_employees ?? '') }}" min="1">
                                    @error('max_employees') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Max Currencies *</label>
                                    <input type="number" name="max_currencies" class="form-control create-field @error('max_currencies') is-invalid @enderror" value="{{ old('max_currencies', $package->max_currencies ?? '5') }}" min="1" max="20" required>
                                    @error('max_currencies') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Max Languages *</label>
                                    <input type="number" name="max_languages" class="form-control create-field @error('max_languages') is-invalid @enderror" value="{{ old('max_languages', $package->max_languages ?? '3') }}" min="1" max="10" required>
                                    @error('max_languages') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                                        <input type="checkbox" name="is_active" class="form-check-input ms-0" id="is_active" value="1" {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="is_active">Active</label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Features Included</label>
                                    <div class="border rounded-4 p-3 p-lg-4 bg-white">
                                        @php
                                            $allFeatures = ['invoicing' => 'Invoicing & Billing', 'payroll' => 'Payroll Management', 'inventory' => 'Inventory Management', 'basic_reporting' => 'Basic Reporting', 'advanced_reporting' => 'Advanced Reporting', 'multi_branch' => 'Multi-Branch Support', 'api_access' => 'API Access', 'custom_integration' => 'Custom Integration', 'dedicated_support' => 'Dedicated Support', 'backup_restore' => 'Backup & Restore', 'audit_logs' => 'Audit Logs', 'user_management' => 'User Management'];
                                            $selectedFeatures = isset($package) ? $package->features->pluck('feature_key')->toArray() : [];
                                        @endphp
                                        <div class="row">
                                            @foreach($allFeatures as $key => $name)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check p-3 border rounded-4 h-100">
                                                        <input type="checkbox" name="features[]" class="form-check-input" id="feature_{{ $key }}" value="{{ $key }}" {{ in_array($key, $selectedFeatures) || old('features') && in_array($key, old('features')) ? 'checked' : '' }}>
                                                        <label class="form-check-label ms-2" for="feature_{{ $key }}">{{ $name }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-0 p-4 p-lg-5 pt-0">
                            <button type="submit" class="btn btn-primary px-4">{{ isset($package) ? 'Update' : 'Create' }} Package</button>
                            <a href="{{ route('super-admin.packages.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card create-aside h-100">
                    <div class="card-body p-4 p-lg-5">
                        <h5 class="fw-bold mb-3">Plan guidance</h5>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex gap-3"><i class="bi bi-cash-coin mt-1"></i><div>Keep pricing and duration together so the plan reads like a single decision.</div></div>
                            <div class="d-flex gap-3"><i class="bi bi-grid-1x2-fill mt-1"></i><div>Feature groups are displayed as cards to make the option scan faster.</div></div>
                            <div class="d-flex gap-3"><i class="bi bi-lightning-charge mt-1"></i><div>Use this structure for both create and edit, so the experience stays consistent.</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
