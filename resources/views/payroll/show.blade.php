@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h3>{{ __('Payroll') }} - {{ $payroll->employee?->name }}</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('payroll.payslip', $payroll) }}" class="btn btn-outline-danger">{{ __('messages.download_pdf') }}</a>
            @if($payroll->status === 'draft')
                <form action="{{ route('payroll.process', $payroll) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-success">{{ __('messages.process') }}</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p><strong>{{ __('Employee') }}:</strong> {{ $payroll->employee?->name }}</p>
                    <p><strong>{{ __('Month/Year') }}:</strong> {{ $payroll->month }}/{{ $payroll->year }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>{{ __('Status') }}:</strong> <span class="badge bg-{{ $payroll->status === 'processed' ? 'success' : 'warning' }}">{{ $payroll->status }}</span></p>
                    @if($payroll->processed_at)
                    <p><strong>{{ __('Processed At') }}:</strong> {{ $payroll->processed_at->format('Y-m-d H:i') }}</p>
                    @endif
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <p><strong>{{ __('Basic Salary') }}:</strong> {{ currencySymbol() }}{{ number_format($payroll->basic_salary,2) }}</p>
                    <p><strong>{{ __('Allowances') }}:</strong> {{ currencySymbol() }}{{ number_format($payroll->allowances,2) }}</p>
                    <p><strong>{{ __('Deductions') }}:</strong> {{ currencySymbol() }}{{ number_format($payroll->deductions,2) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>{{ __('Net Salary') }}:</strong> <strong>{{ currencySymbol() }}{{ number_format($payroll->net_salary,2) }}</strong></p>
                </div>
            </div>

            @if($payroll->notes)
            <p><strong>{{ __('Notes') }}:</strong> {{ $payroll->notes }}</p>
            @endif
        </div>
    </div>
</div>
@endsection