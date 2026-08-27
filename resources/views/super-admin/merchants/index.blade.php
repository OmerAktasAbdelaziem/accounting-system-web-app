@extends('layouts.super-admin')

@section('title', 'Manage Merchants')

@section('content')
<div class="page-header">
    <div class="merchant-header-row d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="page-title">
        {{ __('<style>
            @media (max-width: 768px) {
                .merchant-header-row {
                    flex-direction: column;
                    align-items: stretch !important;
                    gap: 12px;
                }

                .merchant-header-row .btn {
                    width: 100%;
                }

                .form-section form .col-md-4,
                .form-section form .col-md-3,
                .form-section form .col-md-2 {
                    width: 100%;
                }

                .data-table {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                .data-table .table {
                    min-width: 860px;
                }

                .btn-group {
                    flex-wrap: wrap;
                    gap: 6px;
                }

                .btn-group .btn,
                .btn-group form {
                    flex: 1 1 auto;
                }
            }

            @media (max-width: 576px) {
                .page-title {
                    font-size: 22px;
                }

                .page-title i {
                    font-size: 22px;
                }

                .page-subtitle {
                    font-size: 12px;
                }

                .form-section,
                .data-table {
                    border-radius: 14px;
                }
            }
        </style>') }}

                <i class="bi bi-building"></i>
                {{ __('Merchants Management') }}
            </h1>
            <p class="page-subtitle">{{ __('Manage your merchant/dealer accounts and subscriptions') }}</p>
        </div>
        <a href="{{ route('super-admin.merchants.create') }}" class="btn btn-primary-orange">
            <i class="bi bi-plus-circle"></i> {{ __('Add New Merchant') }}
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-orange alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filters -->
<div class="form-section mb-4">
    <form method="GET" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">{{ __('All Status') }}</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-orange w-100">
                <i class="bi bi-search"></i> {{ __('Filter') }}
            </button>
        </div>
        <div class="col-md-3">
            <a href="{{ route('super-admin.merchants.index') }}" class="btn btn-outline-orange w-100">
                <i class="bi bi-arrow-clockwise"></i> {{ __('Reset') }}
            </a>
        </div>
    </form>
</div>

<!-- Merchants Table -->
<div class="data-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>{{ __('Merchant Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Package') }}</th>
                <th>{{ __('Expires') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($merchants as $merchant)
                <tr>
                    <td>
                        <strong>{{ $merchant->name }}</strong>
                        <br>
                        <small class="text-muted">{{ $merchant->address }}</small>
                    </td>
                    <td>{{ $merchant->admin_email }}</td>
                    <td>
                        @php
                            $activeSubscription = $merchant->subscription()->where('is_active', true)->first();
                        @endphp
                        @if($activeSubscription)
                            <span class="badge-orange">
                                {{ $activeSubscription->package->name }}
                            </span>
                        {{ __('@else') }}
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($merchant->subscription_expires_at)
                            {{ $merchant->subscription_expires_at->translatedFormat('M d, Y') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($merchant->is_active)
                            <span class="badge-success">{{ __('Active') }}</span>
                        {{ __('@else') }}
                            <span class="badge-warning">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('super-admin.merchants.show', $merchant->{{ __('id) }}" class="btn btn-sm btn-outline-orange" title="View">') }}
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('super-admin.merchants.inspect', $merchant) }}" method="POST" style="display:inline;" title="Inspect (Login as Merchant)">
                                {{ __('@csrf') }}
                                <button type="submit" class="btn btn-sm btn-outline-info" title="Inspect merchant" onclick="return confirm('Login as this merchant?')">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                            <a href="{{ route('super-admin.merchants.edit', $merchant->{{ __('id) }}" class="btn btn-sm btn-outline-orange" title="Edit">') }}
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('super-admin.merchants.destroy', $merchant->{{ __('id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method(\'DELETE\')') }}
                                <button type="submit" class="btn btn-sm btn-outline-orange" onclick="return confirm('Are you sure?')" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            {{ __('@empty') }}
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 32px; opacity: 0.3;"></i>
                        <p class="mt-2">{{ __('No merchants found') }}</p>
                    </td>
                </tr>
            {{ __('@endforelse') }}
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-4 d-flex justify-content-center">
    {{ $merchants->links() }}
</div>
@endsection
