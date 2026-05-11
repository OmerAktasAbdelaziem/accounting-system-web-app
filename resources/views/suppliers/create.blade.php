@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('Create Supplier') }}</h3>

    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('Name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Email') }}</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Phone') }}</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Address') }}</label>
            <textarea name="address" class="form-control">{{ old('address') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Opening Balance') }}</label>
            <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', 0) }}">
        </div>

        <button class="btn btn-primary">{{ __('Save') }}</button>
    </form>
</div>
@endsection
