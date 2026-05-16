@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('Create Payroll') }}</h3>

    <form action="{{ route('payroll.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('Employee') }}</label>
            <select name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                <option value="">{{ __('Select employee') }}</option>
                @foreach($employees as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Year') }}</label>
                    <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $currentYear) }}" required>
                    @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('Month') }}</label>
                    <select name="month" class="form-control @error('month') is-invalid @enderror" required>
                        <option value="">Select Month</option>
                        @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>{{ $m }}</option>
                        @endfor
                    </select>
                    @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Basic Salary') }}</label>
            <input type="number" step="1" name="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary', 0) }}" required>
            @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="alert alert-info">
            <small><strong>{{ __('Commission') }}</strong> will be automatically calculated from employee commission records for the selected month/year.</small>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Allowances') }}</label>
            <input type="number" step="1" name="allowances" class="form-control @error('allowances') is-invalid @enderror" value="{{ old('allowances', 0) }}">
            @error('allowances')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="form-text text-muted">Additional benefits/bonuses (optional)</small>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Notes') }}</label>
            <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
        </div>

        <button class="btn btn-primary">{{ __('Save') }}</button>
        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
