@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>{{ __('messages.payroll') }}</h3>
        <a href="{{ route('payroll.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('payroll.index') }}" class="row g-2 align-items-end mb-3">
                <div class="col-md-8">
                    <label for="payroll-search" class="form-label">Search</label>
                    <input id="payroll-search" type="text" name="q" value="{{ $search ?? request('q') }}" class="form-control" placeholder="Search by employee">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card" id="payroll-list-container">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Employee') }}</th>
                        <th>{{ __('Commission') }}</th>
                        <th>{{ __('Net Salary') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $payroll)
                    <tr>
                        <td>{{ $payroll->id }}</td>
                        <td>{{ $payroll->employee ? (is_string($payroll->employee->name) ? $payroll->employee->name : (is_array($payroll->employee->name) ? ($payroll->employee->name[app()->getLocale()] ?? implode(' - ', $payroll->employee->name)) : json_encode($payroll->employee->name))) : '' }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($payroll->commission,2) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($payroll->net_salary,2) }}</td>
                        <td class="action-buttons">
                            <a href="{{ route('payroll.show', $payroll) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a>
                            <a href="{{ route('payroll.edit', $payroll) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                            <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $payrolls->links() }}
        </div>
    </div>
</div>
@include('components.ajax-list')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initAjaxList({ containerId: 'payroll-list-container', searchSelector: '#payroll-search', searchParam: 'q', debounceMs: 300 });
});
</script>
@endpush

@endsection
