@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('Edit Payroll') }}</h3>

    <form action="{{ route('payroll.update', $payroll) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">{{ __('Employee') }}</label>
            <input type="text" class="form-control" value="{{ $payroll->employee?->name }}" disabled>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Month/Year') }}</label>
                    <input type="text" class="form-control" value="{{ $payroll->month }}/{{ $payroll->year }}" disabled>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Basic Salary') }} <span class="text-danger">*</span></label>
            <input type="text" name="basic_salary" inputmode="numeric" class="form-control @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary', $payroll->basic_salary) }}" placeholder="Enter basic salary" required>
            @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Commission (Auto-Calculated)') }}</label>
            <div class="alert alert-info">
                <strong>{{ $currencySymbol }}{{ number_format($payroll->calculated_commission ?? $payroll->commission, 2) }}</strong>
                <br><small>This is automatically pulled from employee commission records.</small>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Allowances') }}</label>
            <input type="text" name="allowances" inputmode="numeric" class="form-control @error('allowances') is-invalid @enderror" value="{{ old('allowances', $payroll->allowances ?? 0) }}">
            @error('allowances')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">Additional benefits/bonuses (optional)</small>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Notes') }}</label>
            <textarea name="notes" class="form-control">{{ old('notes', $payroll->notes) }}</textarea>
        </div>

        <button class="btn btn-primary">{{ __('Save') }}</button>
        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
