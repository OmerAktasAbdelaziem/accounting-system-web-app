@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>{{ __('messages.suppliers') }}</h3>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Balance') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ is_string($supplier->name) ? $supplier->name : (is_array($supplier->name) ? ($supplier->name[app()->getLocale()] ?? implode(' - ', $supplier->name)) : json_encode($supplier->name)) }}</td>
                        <td>{{ is_string($supplier->email) ? $supplier->email : (is_array($supplier->email) ? ($supplier->email[app()->getLocale()] ?? implode(' - ', $supplier->email)) : json_encode($supplier->email)) }}</td>
                        <td>{{ is_string($supplier->phone) ? $supplier->phone : (is_array($supplier->phone) ? ($supplier->phone[app()->getLocale()] ?? implode(' - ', $supplier->phone)) : json_encode($supplier->phone)) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($supplier->opening_balance ?? 0,2) }}</td>
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
