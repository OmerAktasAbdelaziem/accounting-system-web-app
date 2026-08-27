@extends('layouts.modern')

@section('content')
<style>
    .payroll-edit-shell {
        min-height: 100vh;
        padding: 24px 0 40px;
        background:
            radial-gradient(circle at top left, rgba(255, 140, 0, 0.12), transparent 28%),
            linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
    }

    .payroll-edit-hero {
        border: 0;
        border-radius: 30px;
        color: #fff;
        background: linear-gradient(135deg, #111827 0%, #1f2937 55%, #0f172a 100%);
        box-shadow: 0 22px 56px rgba(15, 23, 42, 0.16);
    }

    .payroll-edit-card {
        border: 0;
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.10);
        background: #fff;
    }

    .payroll-edit-header {
        background: linear-gradient(135deg, #ff8c00 0%, #ffb347 100%);
        color: #fff;
        padding: 28px 32px;
    }

    .payroll-edit-chip,
    .payroll-edit-note {
        display: inline-flex;
        align-items: center;
        padding: .38rem .8rem;
        border-radius: 999px;
        background: rgba(255,255,255,.92);
        border: 1px solid rgba(255,255,255,.55);
        color: #1f2937;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
    }

    .payroll-edit-chip {
        font-size: .78rem;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .payroll-edit-note {
        max-width: 44rem;
        line-height: 1.6;
        padding: .8rem 1rem;
        border-radius: 1rem;
    }

    .payroll-edit-body {
        padding: 32px;
    }

    .payroll-edit-footer {
        padding: 22px 32px 32px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .payroll-edit-label {
        color: #1f2937;
        font-weight: 700;
    }

    .payroll-edit-help {
        color: #64748b;
        font-size: .84rem;
    }

    .payroll-edit-field {
        min-height: 48px;
        border-radius: 14px;
        border-color: #d7dce6;
    }

    .payroll-edit-field:focus {
        border-color: #ff8c00;
        box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.10);
    }

    .payroll-readonly-box {
        border-radius: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 16px 18px;
        color: #0f172a;
    }

    .payroll-readonly-box .value {
        font-weight: 800;
        color: #0f172a;
    }

    @media (max-width: 768px) {
        .payroll-edit-shell { padding: 12px 0 28px; }
        .payroll-edit-hero { border-radius: 24px; }
        .payroll-edit-card { border-radius: 24px; }
        .payroll-edit-header { padding: 22px 20px; }
        .payroll-edit-body { padding: 20px; }
        .payroll-edit-footer { padding: 18px 20px 22px; }
        .payroll-edit-footer .btn { width: 100%; }
    }
</style>

<div class="payroll-edit-shell">
    <div class="container-fluid">
        <div class="card payroll-edit-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
                    <div>
                        <div class="payroll-edit-chip mb-3">{{ __('messages.payroll') }}</div>
                        <h1 class="mb-2 fw-bold" style="letter-spacing: -.04em;">{{ __('messages.edit_payroll') }}</h1>
                        <p class="mb-0 payroll-edit-note">{{ __('Adjust payroll values with a clearer, more readable layout.') }}</p>
                    </div>
                    <a href="{{ route('payroll.index') }}" class="btn btn-outline-light rounded-pill px-3">
                        <i class="bi bi-arrow-left me-2"></i>{{ __('messages.cancel') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card payroll-edit-card">
            <div class="payroll-edit-header">
                <div class="small text-uppercase fw-semibold text-dark opacity-75">{{ __('Current payroll') }}</div>
                <h5 class="mb-0 fw-bold">{{ $payroll->employee?->name }} · {{ $payroll->month }}/{{ $payroll->year }}</h5>
            </div>

            <div class="payroll-edit-body">
                <form action="{{ route('payroll.update', $payroll) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label payroll-edit-label">{{ __('messages.employee') }}</label>
                            <div class="payroll-readonly-box">
                                <div class="small text-uppercase text-muted fw-semibold mb-1">{{ __('Employee name') }}</div>
                                <div class="value">{{ $payroll->employee?->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label payroll-edit-label">{{ __('messages.month_year') }}</label>
                            <div class="payroll-readonly-box">
                                <div class="small text-uppercase text-muted fw-semibold mb-1">{{ __('Payroll period') }}</div>
                                <div class="value">{{ $payroll->month }}/{{ $payroll->year }}</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label payroll-edit-label">{{ __('messages.basic_salary') }} <span class="text-danger">*</span></label>
                            <input type="text" name="basic_salary" inputmode="numeric" class="form-control payroll-edit-field @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary', $payroll->basic_salary) }}" placeholder="Enter basic salary" required>
                            @error('basic_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label payroll-edit-label">{{ __('messages.commission_auto_calculated') }}</label>
                            <div class="payroll-readonly-box">
                                <div class="small text-uppercase text-muted fw-semibold mb-1">{{ __('Auto-calculated from commission records') }}</div>
                                <div class="value">{{ $currencySymbol }}{{ number_format($payroll->calculated_commission ?? $payroll->commission, 2) }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label payroll-edit-label">{{ __('messages.allowances') }}</label>
                            <input type="text" name="allowances" inputmode="numeric" class="form-control payroll-edit-field @error('allowances') is-invalid @enderror" value="{{ old('allowances', $payroll->allowances ?? 0) }}">
                            @error('allowances')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="payroll-edit-help mt-1">{{ __('Additional benefits or bonuses.') }}</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label payroll-edit-label">{{ __('messages.deductions') }}</label>
                            <input type="text" name="deductions" inputmode="numeric" class="form-control payroll-edit-field @error('deductions') is-invalid @enderror" value="{{ old('deductions', $payroll->deductions ?? 0) }}">
                            @error('deductions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="payroll-edit-help mt-1">{{ __('Manual deductions or advances.') }}</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label payroll-edit-label">{{ __('messages.notes') }}</label>
                            <textarea name="notes" class="form-control payroll-edit-field" rows="4" placeholder="Add any payroll notes">{{ old('notes', $payroll->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="payroll-edit-footer">
                        <button class="btn btn-primary px-4">{{ __('messages.save') }}</button>
                        <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary px-4">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
