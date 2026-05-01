@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('Edit Customer') }}</h3>

    <form action="{{ route('customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">{{ __('Name') }}</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', is_string($customer->name) ? $customer->name : (is_array($customer->name) ? ($customer->name[app()->getLocale()] ?? '') : '')) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Email') }}</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', is_string($customer->email) ? $customer->email : (is_array($customer->email) ? ($customer->email[app()->getLocale()] ?? '') : '')) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Phone') }}</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', is_string($customer->phone) ? $customer->phone : (is_array($customer->phone) ? ($customer->phone[app()->getLocale()] ?? '') : '')) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Address') }}</label>
              <textarea name="address" class="form-control">{{ old('address', is_string($customer->address) ? $customer->address : (is_array($customer->address) ? ($customer->address[app()->getLocale()] ?? '') : '')) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Opening Balance') }}</label>
            <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $customer->opening_balance) }}">
        </div>

        <button class="btn btn-primary">{{ __('Save') }}</button>
    </form>
</div>
@endsection
