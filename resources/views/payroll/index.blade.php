@extends('layouts.modern')

@section('content')
<style>
    @media (max-width: 768px) {
        .d-flex.flex-wrap.justify-content-between.align-items-center.gap-3.mb-4 {
            flex-direction: column;
            align-items: stretch !important;
        }

        .d-flex.flex-wrap.justify-content-between.align-items-center.gap-3.mb-4 .btn {
            width: 100%;
        }

        .row.g-3.mb-4 .col-md-4,
        .row.g-4.mb-4 .col-12,
        .row.g-4 .col-md-6,
        .row.g-4 .col-md-12 {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            min-width: 760px;
        }

        .btn-group {
            flex-wrap: wrap;
            gap: 6px;
        }

        .btn-group .btn {
            flex: 1 1 auto;
        }
    }

    @media (max-width: 576px) {
        h3 {
            font-size: 22px;
        }

        .table {
            min-width: 640px;
        }
    }
</style>

<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">{{ __('messages.payroll') }}</h3>
            <div class="text-muted">{{ __('Track unpaid salaries, settle payrolls, and keep payment history.') }}</div>
        </div>
        <a href="{{ route('payroll.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Unpaid payrolls') }}</div>
                    <div class="display-6 fw-bold">{{ $unpaidPayrollCount ?? 0 }}</div>
                    <div class="text-muted mt-2">{{ __('Payroll records still waiting for settlement.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Unpaid net salaries') }}</div>
                    <div class="display-6 fw-bold">{{ $currencySymbol }}{{ number_format($unpaidNetSalaryTotal ?? 0, 2) }}</div>
                    <div class="text-muted mt-2">{{ __('Total amount that still needs to be paid.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-semibold mb-2">{{ __('Paid payrolls') }}</div>
                    <div class="display-6 fw-bold">{{ $paidPayrollCount ?? 0 }}</div>
                    <div class="text-muted mt-2">{{ __('Completed payroll settlements archived below.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-1">{{ __('Active Payrolls') }}</h5>
                    <div class="text-muted small">{{ __('Use the Paid action to settle payroll from a selected safe.') }}</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Net Salary') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activePayrolls ?? [] as $payroll)
                                    @php
                                        $employeeName = '';
                                        if ($payroll->employee) {
                                            $employeeName = is_string($payroll->employee->name)
                                                ? $payroll->employee->name
                                                : (is_array($payroll->employee->name)
                                                    ? ($payroll->employee->name[app()->getLocale()] ?? implode(' - ', $payroll->employee->name))
                                                    : json_encode($payroll->employee->name));
                                        }
                                        $payUrl = route('payroll.pay', $payroll);
                                    @endphp
                                    <tr>
                                        <td>{{ $payroll->id }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $employeeName }}</div>
                                            <div class="text-muted small">{{ $payroll->month }}/{{ $payroll->year }}</div>
                                        </td>
                                        <td class="fw-bold">{{ $currencySymbol }}{{ number_format($payroll->calculated_net_salary ?? $payroll->net_salary, 2) }}</td>
                                        <td><span class="badge bg-warning text-dark">{{ strtoupper($payroll->status ?? 'draft') }}</span></td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-outline-secondary">{{ __('View') }}</a>
                                                <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-outline-primary">{{ __('Edit') }}</a>
                                                <button
                                                    type="button"
                                                    class="btn btn-success"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#payrollPayModal"
                                                    data-pay-url="{{ $payUrl }}"
                                                    data-pay-employee="{{ $employeeName }}"
                                                    data-pay-amount="{{ number_format((float) ($payroll->calculated_net_salary ?? $payroll->net_salary), 2, '.', '') }}"
                                                >
                                                    {{ __('Paid') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">{{ __('No unpaid payrolls found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">{{ $activePayrolls->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pb-0">
            <h5 class="mb-1">{{ __('Paid Payroll History') }}</h5>
            <div class="text-muted small">{{ __('Completed payrolls stay here for review and reporting.') }}</div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('Employee') }}</th>
                            <th>{{ __('Safe') }}</th>
                            <th>{{ __('Net Salary') }}</th>
                            <th>{{ __('Paid At') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paidPayrolls ?? [] as $payroll)
                            @php
                                $employeeName = '';
                                if ($payroll->employee) {
                                    $employeeName = is_string($payroll->employee->name)
                                        ? $payroll->employee->name
                                        : (is_array($payroll->employee->name)
                                            ? ($payroll->employee->name[app()->getLocale()] ?? implode(' - ', $payroll->employee->name))
                                            : json_encode($payroll->employee->name));
                                }
                            @endphp
                            <tr>
                                <td>{{ $payroll->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $employeeName }}</div>
                                    <div class="text-muted small">{{ $payroll->month }}/{{ $payroll->year }}</div>
                                </td>
                                <td>{{ $payroll->safe?->name ?? '-' }}</td>
                                <td class="fw-bold text-success">{{ $currencySymbol }}{{ number_format($payroll->calculated_net_salary ?? $payroll->net_salary, 2) }}</td>
                                <td>{{ optional($payroll->processed_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-outline-secondary">{{ __('View') }}</a>
                                        <a href="{{ route('payroll.payslip', $payroll) }}" class="btn btn-outline-danger">{{ __('PDF') }}</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">{{ __('No paid payroll history found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $paidPayrolls->links() }}</div>
        </div>
    </div>
</div>

<div class="modal fade" id="payrollPayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1">{{ __('Pay Payroll') }}</h5>
                    <div class="text-muted small">{{ __('Choose the safe that should be deducted for this payroll payment.') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="payrollPayForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <div class="fw-semibold" id="payrollPayEmployee"></div>
                        <div class="small" id="payrollPayAmount"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Safe') }}</label>
                        <select name="safe_id" class="form-select" required>
                            <option value="">{{ __('Select safe') }}</option>
                            @foreach($safes ?? [] as $safe)
                                <option value="{{ $safe->id }}">{{ $safe->name }} - {{ $currencySymbol }}{{ number_format((float) $safe->balance, 2) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Confirm Payment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('payrollPayModal');
    if (!modal) {
        return;
    }

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) {
            return;
        }

        const payUrl = button.getAttribute('data-pay-url');
        const employee = button.getAttribute('data-pay-employee') || '';
        const amount = button.getAttribute('data-pay-amount') || '0.00';

        const form = document.getElementById('payrollPayForm');
        const employeeLabel = document.getElementById('payrollPayEmployee');
        const amountLabel = document.getElementById('payrollPayAmount');

        if (form) {
            form.action = payUrl;
        }
        if (employeeLabel) {
            employeeLabel.textContent = employee;
        }
        if (amountLabel) {
            amountLabel.textContent = '{{ __('Amount to deduct:') }} {{ $currencySymbol }}' + amount;
        }
    });
});
</script>
@endsection
