@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title">{{ isset($package) ? 'Edit' : 'Create' }} Package</h1>
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
                <div class="card-header bg-light"><h5 class="mb-0">Package Details</h5></div>
                <form action="{{ isset($package) ? route('super-admin.packages.update', $package) : route('super-admin.packages.store') }}" method="POST">
                    @csrf @if(isset($package)) @method('PUT') @endif
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Package Name *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $package->name ?? '') }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $package->description ?? '') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price ($) *</label>
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $package->price ?? '') }}" step="0.01" min="0" required>
                                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration (days) *</label>
                                <input type="number" name="duration_days" class="form-control @error('duration_days') is-invalid @enderror" value="{{ old('duration_days', $package->duration_days ?? '30') }}" min="1" required>
                                @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Employees (null = unlimited)</label>
                                <input type="number" name="max_employees" class="form-control @error('max_employees') is-invalid @enderror" value="{{ old('max_employees', $package->max_employees ?? '') }}" min="1">
                                @error('max_employees') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Currencies *</label>
                                <input type="number" name="max_currencies" class="form-control @error('max_currencies') is-invalid @enderror" value="{{ old('max_currencies', $package->max_currencies ?? '5') }}" min="1" max="20" required>
                                @error('max_currencies') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Languages *</label>
                                <input type="number" name="max_languages" class="form-control @error('max_languages') is-invalid @enderror" value="{{ old('max_languages', $package->max_languages ?? '3') }}" min="1" max="10" required>
                                @error('max_languages') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', $package->is_active ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Features Included</label>
                            <div class="card">
                                <div class="card-body">
                                    @php
                                        $allFeatures = ['invoicing' => 'Invoicing & Billing', 'payroll' => 'Payroll Management', 'inventory' => 'Inventory Management', 'basic_reporting' => 'Basic Reporting', 'advanced_reporting' => 'Advanced Reporting', 'multi_branch' => 'Multi-Branch Support', 'api_access' => 'API Access', 'custom_integration' => 'Custom Integration', 'dedicated_support' => 'Dedicated Support', 'backup_restore' => 'Backup & Restore', 'audit_logs' => 'Audit Logs', 'user_management' => 'User Management'];
                                        $selectedFeatures = isset($package) ? $package->features->pluck('feature_key')->toArray() : [];
                                    @endphp
                                    <div class="row">
                                        @foreach($allFeatures as $key => $name)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" name="features[]" class="form-check-input" id="feature_{{ $key }}" value="{{ $key }}" {{ in_array($key, $selectedFeatures) || old('features') && in_array($key, old('features')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="feature_{{ $key }}">{{ $name }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-primary">{{ isset($package) ? 'Update' : 'Create' }} Package</button>
                        <a href="{{ route('super-admin.packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
