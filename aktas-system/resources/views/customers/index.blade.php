@extends('layouts.modern')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>{{ __('messages.customers') }}</h3>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
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
                    @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer->id }}</td>
                        <td>{{ is_string($customer->name) ? $customer->name : (is_array($customer->name) ? ($customer->name[app()->getLocale()] ?? implode(' - ', $customer->name)) : json_encode($customer->name)) }}</td>
                        <td>{{ is_string($customer->email) ? $customer->email : (is_array($customer->email) ? ($customer->email[app()->getLocale()] ?? implode(' - ', $customer->email)) : json_encode($customer->email)) }}</td>
                        <td>{{ is_string($customer->phone) ? $customer->phone : (is_array($customer->phone) ? ($customer->phone[app()->getLocale()] ?? implode(' - ', $customer->phone)) : json_encode($customer->phone)) }}</td>
                        <td>{{ number_format($customer->opening_balance ?? 0,2) }}</td>
                        <td class="action-buttons text-nowrap">
                            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a>
                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
