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
            <label class="form-label">{{ __('Basic Salary') }}</label>
            <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ old('basic_salary', $payroll->basic_salary) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Allowances') }}</label>
            <input type="number" step="0.01" name="allowances" class="form-control" value="{{ old('allowances', $payroll->allowances) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Deductions') }}</label>
            <input type="number" step="0.01" name="deductions" class="form-control" value="{{ old('deductions', $payroll->deductions) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Notes') }}</label>
            <textarea name="notes" class="form-control">{{ old('notes', $payroll->notes) }}</textarea>
        </div>

        <button class="btn btn-primary">{{ __('Save') }}</button>
    </form>
</div>
@endsection
