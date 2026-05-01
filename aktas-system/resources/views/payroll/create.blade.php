@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('Create Payroll') }}</h3>

    <form action="{{ route('payroll.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('Employee') }}</label>
            <select name="employee_id" class="form-control" required>
                <option value="">{{ __('Select employee') }}</option>
                @foreach($employees as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Year') }}</label>
                    <input type="number" name="year" class="form-control" value="{{ old('year', $currentYear) }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Month') }}</label>
                    <select name="month" class="form-control" required>
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Basic Salary') }}</label>
            <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ old('basic_salary', 0) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Allowances') }}</label>
            <input type="number" step="0.01" name="allowances" class="form-control" value="{{ old('allowances', 0) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Deductions') }}</label>
            <input type="number" step="0.01" name="deductions" class="form-control" value="{{ old('deductions', 0) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Notes') }}</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>

        <button class="btn btn-primary">{{ __('Save') }}</button>
    </form>
</div>
@endsection
