@extends('layouts.modern')

@section('title', isset($commission) ? __('messages.edit_commission') : __('messages.add_commission'))

@section('content')
<style>
    .commission-form-page {
        min-height: 100vh;
        padding: 32px 16px;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
    }
    .commission-form-shell {
        max-width: 1100px;
        margin: 0 auto;
    }
    .commission-form-card {
        border: 0;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
    }
    .commission-form-header {
        padding: 28px 32px;
        background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
        color: #fff;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }
    .commission-form-header h1 {
        margin: 0;
        font-size: 30px;
        font-weight: 800;
    }
    .commission-form-body {
        background: #fff;
        padding: 32px;
    }
    .commission-form-section {
        margin-bottom: 28px;
    }
    .commission-form-section-title {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #475569;
        margin-bottom: 16px;
    }
    .commission-form-footer {
        padding: 24px 32px;
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
</style>

<div class="commission-form-page">
    <div class="commission-form-shell">
        <div class="commission-form-card card">
            <div class="commission-form-header">
                <div>
                    <h1><i class="bi bi-percent me-2"></i>{{ isset($commission) ? __('messages.edit_commission') : __('messages.add_commission') }}</h1>
                    <div class="opacity-75">{{ isset($commission) ? 'Edit an existing commission record.' : 'Use this only to create the first commission profile for an employee.' }}</div>
                </div>
                <a href="{{ route('commissions.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>

            <div class="commission-form-body">
                @if(!$commission && isset($employees) && $employees->isEmpty())
                    <div class="alert alert-warning mb-4">
                        No eligible employees are available for first-time commission creation. Open an existing employee commission profile to add more commissions.
                    </div>
                @else
                    @if(!$commission)
                        <div class="alert alert-info mb-4">
                            This form creates the first commission profile for an employee. After that, add new commissions from the employee profile page.
                        </div>
                    @endif

                    <form method="POST" action="{{ isset($commission) ? route('commissions.update', $commission->id) : route('commissions.store') }}">
                        @csrf
                        @if(isset($commission))
                            @method('PUT')
                        @endif

                        <div class="commission-form-section">
                            <div class="commission-form-section-title">Commission Details</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.employee') }} *</label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees ?? [] as $emp)
                                            <option value="{{ $emp->id }}" {{ old('employee_id', $commission->employee_id ?? '') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.commission_date') }} *</label>
                                    <input type="date" class="form-control @error('commission_date') is-invalid @enderror" name="commission_date" value="{{ old('commission_date', isset($commission->commission_date) ? $commission->commission_date->format('Y-m-d') : '') }}" required>
                                    @error('commission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="commission-form-section">
                            <div class="commission-form-section-title">Financial Details</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.sale_amount') }} *</label>
                                    <input type="text" inputmode="decimal" class="form-control @error('sale_amount') is-invalid @enderror" name="sale_amount" value="{{ old('sale_amount', $commission->sale_amount ?? '') }}" placeholder="0.00" required>
                                    @error('sale_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.commission_rate') }} (%) *</label>
                                    <input type="text" inputmode="decimal" class="form-control @error('commission_rate') is-invalid @enderror" name="commission_rate" value="{{ old('commission_rate', $commission->commission_rate ?? '') }}" placeholder="0" required>
                                    @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="commission-form-section">
                            <div class="commission-form-section-title">Additional Information</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">{{ __('messages.reference_type') }}</label>
                                    <input type="text" class="form-control @error('reference_type') is-invalid @enderror" name="reference_type" value="{{ old('reference_type', $commission->reference_type ?? '') }}" placeholder="Optional reference">
                                    @error('reference_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">{{ __('messages.notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="4" placeholder="Optional notes">{{ old('notes', $commission->notes ?? '') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="commission-form-section">
                            <div class="commission-form-section-title">Branches</div>
                            @include('branches.partials.multi-select', ['branches' => $branches ?? [], 'selectedBranchIds' => $selectedBranchIds ?? []])
                        </div>

                        <div class="commission-form-footer">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i>{{ isset($commission) ? __('messages.update') : __('messages.save') }}
                            </button>
                            <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle me-1"></i>{{ __('messages.cancel') }}
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
