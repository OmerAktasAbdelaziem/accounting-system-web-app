@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ $customer->name }}</h3>

    <div class="card">
        <div class="card-body">
            <p><strong>{{ __('Email') }}:</strong> {{ is_string($customer->email) ? $customer->email : (is_array($customer->email) ? ($customer->email[app()->getLocale()] ?? implode(' - ', $customer->email)) : json_encode($customer->email)) }}</p>
            <p><strong>{{ __('Phone') }}:</strong> {{ is_string($customer->phone) ? $customer->phone : (is_array($customer->phone) ? ($customer->phone[app()->getLocale()] ?? implode(' - ', $customer->phone)) : json_encode($customer->phone)) }}</p>
            <p><strong>{{ __('Address') }}:</strong> {{ is_string($customer->address) ? $customer->address : (is_array($customer->address) ? ($customer->address[app()->getLocale()] ?? implode(' - ', $customer->address)) : json_encode($customer->address)) }}</p>
            <p><strong>{{ __('Balance') }}:</strong> {{ number_format($customer->opening_balance,2) }}</p>
        </div>
    </div>
</div>
@endsection
