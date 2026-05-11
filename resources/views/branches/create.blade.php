@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('messages.add_branch') }}</h3>

    <form action="{{ route('branches.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">{{ __('messages.branch_name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.branch_code') }}</label>
            <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.address') }}</label>
            <textarea name="address" class="form-control">{{ old('address') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.city') }}</label>
            <input type="text" name="city" class="form-control" value="{{ old('city') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.phone') }}</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.manager_name') }}</label>
            <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name') }}">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
            <label class="form-check-label">{{ __('messages.active') }}</label>
        </div>
        <button class="btn btn-primary">{{ __('messages.save') }}</button>
    </form>
</div>
@endsection
