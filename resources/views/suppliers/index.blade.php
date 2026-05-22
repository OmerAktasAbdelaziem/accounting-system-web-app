@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>{{ __('messages.suppliers') }}</h3>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label for="supplier-search" class="form-label">Search</label>
                    <input
                        type="text"
                        id="supplier-search"
                        name="q"
                        value="{{ $search ?? request('q') }}"
                        class="form-control"
                        placeholder="Search by name or address"
                    >
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Balance') }}</th>
                        <th>Purchased</th>
                        <th>Paid</th>
                        <th>Outstanding</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ is_string($supplier->name) ? $supplier->name : (is_array($supplier->name) ? ($supplier->name[app()->getLocale()] ?? implode(' - ', $supplier->name)) : json_encode($supplier->name)) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($supplier->opening_balance ?? 0,2) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format((float) ($supplier->total_purchased ?? 0),2) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format((float) ($supplier->total_paid ?? 0),2) }}</td>
                        <td class="fw-bold {{ (((float)($supplier->opening_balance ?? 0) + (float)($supplier->total_purchased ?? 0) - (float)($supplier->total_paid ?? 0)) > 0) ? 'text-danger' : 'text-success' }}">
                            {{ $currencySymbol }}{{ number_format(((float)($supplier->opening_balance ?? 0) + (float)($supplier->total_purchased ?? 0) - (float)($supplier->total_paid ?? 0)), 2) }}
                        </td>
                        <td class="action-buttons">
                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a>
                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $suppliers->links() }}
        </div>
    </div>
</div>
@endsection
