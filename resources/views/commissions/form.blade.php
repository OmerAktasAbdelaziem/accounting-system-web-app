@extends('layouts.modern')

@section('title', isset($commission) ? __('messages.edit_commission') : __('messages.add_commission'))

@section('content')
<div class="mb-4">
    <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
    </a>
    <h1 style="font-weight: 900; color: #1a1a1a;">
        <i class="bi bi-percent" style="color: #ff8c00;"></i> {{ isset($commission) ? __('messages.edit_commission') : __('messages.add_commission') }}
    </h1>
</div>

<div class="card">
    <form method="POST" action="{{ isset($commission) ? route('commissions.update', $commission->id) : route('commissions.store') }}">
        @csrf
        @if(isset($commission))
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="employee_id" class="form-label">{{ __('messages.employee') }} *</label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                            <option value="">{{ __('messages.select_employee') }}</option>
                            @foreach($employees ?? [] as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id', $commission->employee_id ?? '') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="commission_rate" class="form-label">{{ __('messages.commission_rate') }} *</label>
                        <input type="number" class="form-control @error('commission_rate') is-invalid @enderror" 
                            id="commission_rate" name="commission_rate" step="0.01" min="0" max="100"
                            value="{{ old('commission_rate', $commission->commission_rate ?? '') }}" required>
                        @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="sale_amount" class="form-label">{{ __('messages.sale_amount') }} *</label>
                        <input type="number" class="form-control @error('sale_amount') is-invalid @enderror" 
                            id="sale_amount" name="sale_amount" step="0.01" min="0"
                            value="{{ old('sale_amount', $commission->sale_amount ?? '') }}" required>
                        @error('sale_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="commission_date" class="form-label">{{ __('messages.commission_date') }} *</label>
                        <input type="date" class="form-control @error('commission_date') is-invalid @enderror" 
                            id="commission_date" name="commission_date"
                            value="{{ old('commission_date', $commission->commission_date ?? '') }}" required>
                        @error('commission_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">{{ __('messages.status') }} *</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="pending" {{ old('status', $commission->status ?? '') === 'pending' ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                            <option value="approved" {{ old('status', $commission->status ?? '') === 'approved' ? 'selected' : '' }}>{{ __('messages.approved') }}</option>
                            <option value="paid" {{ old('status', $commission->status ?? '') === 'paid' ? 'selected' : '' }}>{{ __('messages.paid') }}</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="reference_type" class="form-label">{{ __('messages.reference_type') }}</label>
                        <input type="text" class="form-control @error('reference_type') is-invalid @enderror" 
                            id="reference_type" name="reference_type"
                            value="{{ old('reference_type', $commission->reference_type ?? '') }}">
                        @error('reference_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="notes" class="form-label">{{ __('messages.notes') }}</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                    id="notes" name="notes" rows="3">{{ old('notes', $commission->notes ?? '') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="card-footer" style="background: #f9f9f9; padding: 20px;">
            <button type="submit" class="btn btn-primary-modern">
                <i class="bi bi-check-circle"></i> {{ isset($commission) ? __('messages.update') : __('messages.save') }}
            </button>
            <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
