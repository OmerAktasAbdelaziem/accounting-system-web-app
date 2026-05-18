@extends('layouts.modern')

@section('content')
<style>
    .create-shell { min-height: 100vh; padding: 32px 0 48px; background: linear-gradient(180deg, #f7f7f8 0%, #eef1f5 100%); }
    .create-hero { background: linear-gradient(135deg, #16181d 0%, #23262d 100%); color: #fff; border-radius: 28px; padding: 28px 30px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.18); }
    .create-card { border: 0; border-radius: 28px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.1); overflow: hidden; }
    .create-field { min-height: 52px; border-radius: 14px; border-color: #d9dde5; }
    .create-field:focus { border-color: #ff8c00; box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1); }
</style>

<div class="create-shell">
    <div class="container-fluid">
        <div class="create-hero mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">Payroll</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ __('Create Payroll') }}</h1>
                <p class="mb-0 text-white-50">Build payroll records in a more focused working area.</p>
            </div>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-xl-8">
                <div class="card create-card">
                    <div class="card-body p-4 p-lg-5">
                        <form action="{{ route('payroll.store') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('Employee') }}</label>
                                <select name="employee_id" class="form-select create-field @error('employee_id') is-invalid @enderror" required>
                                    <option value="">{{ __('Select employee') }}</option>
                                    @foreach($employees as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Year') }}</label>
                                <input type="number" name="year" class="form-control create-field @error('year') is-invalid @enderror" value="{{ old('year', $currentYear) }}" required>
                                @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Month') }}</label>
                                <select name="month" class="form-select create-field @error('month') is-invalid @enderror" required>
                                    <option value="">Select Month</option>
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>{{ $m }}</option>
                                    @endfor
                                </select>
                                @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('Basic Salary') }} <span class="text-danger">*</span></label>
                                <input type="text" name="basic_salary" inputmode="numeric" class="form-control create-field @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary') }}" placeholder="Enter basic salary" required>
                                @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('Allowances') }}</label>
                                <input type="text" name="allowances" inputmode="numeric" class="form-control create-field @error('allowances') is-invalid @enderror" value="{{ old('allowances', 0) }}">
                                @error('allowances')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="form-text text-muted">Additional benefits/bonuses (optional)</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('Notes') }}</label>
                                <textarea name="notes" class="form-control create-field">{{ old('notes') }}</textarea>
                            </div>

                            <div class="col-12 d-flex gap-2 pt-2">
                                <button class="btn btn-primary px-4">{{ __('Save') }}</button>
                                <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary px-4">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
