@extends('layouts.super-admin')

@section('title', 'Manage Packages')

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-box-seam"></i>
                {{ __('Subscription Packages') }}
            </h1>
            <p class="page-subtitle">{{ __('Create and manage subscription plans for your merchants') }}</p>
        </div>
        <a href="{{ route('super-admin.packages.create') }}" class="btn btn-primary-orange">
            <i class="bi bi-plus-circle"></i> {{ __('Create Package') }}
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-orange alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="data-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>{{ __('Package Name') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Duration') }}</th>
                <th>{{ __('Features') }}</th>
                <th>{{ __('Max Employees') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($packages as $package)
            <tr>
                <td><strong>{{ $package->name }}</strong></td>
                <td>{{ $currencySymbol }}{{ number_format($package->price, 2) }}</td>
                <td>{{ $package->duration_days }} days</td>
                <td>
                    <span class="badge-orange">{{ $package->features()->count() }} features</span>
                </td>
                <td>{{ $package->max_employees ?? 'Unlimited' }}</td>
                <td>
                    <span class="badge-{{ $package->is_active ? 'success' : 'warning' }}">
                        {{ $package->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="{{ route('super-admin.packages.show', $package) }}" class="btn btn-sm btn-outline-orange">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('super-admin.packages.edit', $package) }}" class="btn btn-sm btn-outline-orange">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('super-admin.packages.destroy', $package) }}" method="POST" style="display:inline;">
                            {{ __('@csrf @method(\'DELETE\')') }}
                            <button type="submit" class="btn btn-sm btn-outline-orange" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            {{ __('@empty') }}
            <tr>
                <td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox" style="font-size: 32px; opacity: 0.3;"></i>
                    <p class="mt-2">{{ __('No packages created yet') }}</p>
                </td>
            </tr>
            {{ __('@endforelse') }}
        </tbody>
    </table>
</div>
@endsection
