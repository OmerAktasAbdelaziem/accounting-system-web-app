@extends('layouts.super-admin')

@section('title', 'Data Recovery')

@section('content')
<div class="page-header mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="bi bi-arrow-repeat"></i>
                {{ __('Data Recovery') }}
            </h1>
            <p class="page-subtitle">{{ __('Move records that are missing merchant or branch linkage into the correct merchant branch.') }}</p>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ __('Please fix the following:') }}</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="alert alert-info">
    <strong>{{ __('How it works:') }}</strong> {{ __('select the rows you want to recover, choose a merchant, then transfer them. For branch-based records, a branch will be used automatically or created if needed.') }}
</div>

@foreach ($recoverySections as $section)
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0">{{ $section['title'] }}</h5>
                <small class="text-muted">{{ $section['description'] }}</small>
            </div>
            <span class="badge bg-secondary">{{ $section['records']->count() }} records</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('super-admin.data-recovery.transfer') }}" class="data-recovery-form">
                {{ __('@csrf') }}
                <input type="hidden" name="group" value="{{ $section['key'] }}">

                <div class="row g-3 align-items-end mb-3">
                    <div class="col-lg-6">
                        <label class="form-label">{{ __('Target Merchant') }}</label>
                        <select name="merchant_id" class="form-select" required>
                            <option value="">{{ __('Choose merchant...') }}</option>
                            @foreach ($merchants as $merchant)
                                <option value="{{ $merchant->id }}">{{ $merchant->business_name ?? $merchant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($section['mode'] === 'branch')
                        <div class="col-lg-6">
                            <label class="form-label">{{ __('Target Branch') }}</label>
                            <select name="branch_id" class="form-select">
                                <option value="">{{ __('Auto-pick/create branch') }}</option>
                                @foreach ($merchants as $merchant)
                                    <optgroup label="{{ $merchant->business_name ?? $merchant->name }}">
                                        @forelse ($merchant->branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }} ({{ $branch->code }})</option>
                                        {{ __('@empty') }}
                                            <option value="">{{ __('No branch yet') }}</option>
                                        {{ __('@endforelse') }}
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 44px;">
                                    <input type="checkbox" class="select-all" data-target="section-{{ $section['key'] }}">
                                </th>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Details') }}</th>
                            </tr>
                        </thead>
                        <tbody id="section-{{ $section['key'] }}">
                            @forelse ($section['records'] as $record)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $record->{{ __('id }}" class="row-checkbox">') }}
                                    </td>
                                    <td>{{ $record->id }}</td>
                                    <td>
                                        <strong>
                                            @if ($section['key'] === 'invoices')
                                                {{ $record->invoice_number }}
                                            @elseif ($section['key'] === 'commissions')
                                                Commission #{{ $record->id }}
                                            @else
                                                {{ $record->name ?? $record->title ?? ('Record #' . $record->id) }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td>
                                        @if ($section['key'] === 'branches')
                                            Code: {{ $record->code }} | City: {{ $record->city ?? '-' }}
                                        @elseif ($section['key'] === 'users')
                                            Email: {{ $record->email }} | Type: {{ $record->user_type }}
                                        @elseif ($section['key'] === 'employees')
                                            Email: {{ $record->email ?? '-' }} | Position: {{ $record->position ?? '-' }}
                                        @elseif ($section['key'] === 'products')
                                            Barcode: {{ $record->barcode ?? '-' }} | Category: {{ $record->category->name ?? '-' }}
                                        @elseif ($section['key'] === 'categories')
                                            Code: {{ $record->code ?? '-' }} | Order: {{ $record->display_order ?? '-' }}
                                        @elseif ($section['key'] === 'suppliers')
                                            Email: {{ $record->email ?? '-' }} | Phone: {{ $record->phone ?? '-' }}
                                        @elseif ($section['key'] === 'customers')
                                            Email: {{ $record->email ?? '-' }} | Phone: {{ $record->phone ?? '-' }}
                                        @elseif ($section['key'] === 'invoices')
                                            Customer: {{ $record->customer->name ?? '-' }} | Total: {{ $record->total ?? '-' }}
                                        @elseif ($section['key'] === 'storages')
                                            Type: {{ $record->storage_type ?? '-' }} | Location: {{ $record->location ?? '-' }}
                                        @elseif ($section['key'] === 'safes')
                                            Balance: {{ $record->balance ?? '-' }} | Location: {{ $record->location ?? '-' }}
                                        @elseif ($section['key'] === 'commissions')
                                            Employee: {{ $record->employee->name ?? '-' }} | Amount: {{ $record->commission_amount ?? '-' }} | Status: {{ $record->status ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            {{ __('@empty') }}
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        {{ $section['emptyLabel'] }}
                                    </td>
                                </tr>
                            {{ __('@endforelse') }}
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary-orange" @disabled($section['records']->{{ __('isEmpty())>
                        Transfer selected') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<script>
    document.querySelectorAll('.select-all').forEach(function (selectAll) {
        selectAll.addEventListener('change', function () {
            const target = document.getElementById(this.dataset.target);
            if (!target) {
                return;
            }

            target.querySelectorAll('input.row-checkbox').forEach(function (checkbox) {
                checkbox.checked = selectAll.checked;
            });
        });
    });
</script>
@endsection
