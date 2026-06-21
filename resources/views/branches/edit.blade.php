@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('messages.edit_branch') }}</h3>

    <form action="{{ route('branches.update', $branch) }}" method="POST">
        {{ __('@csrf
        @method(\'PUT\')') }}
        <div class="mb-3">
            <label class="form-label">{{ __('messages.branch_name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $branch->{{ __('name) }}" required>') }}
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.branch_code') }}</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $branch->{{ __('code) }}" required>') }}
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.address') }}</label>
            <textarea name="address" class="form-control">{{ old('address', $branch->address) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.city') }}</label>
            <input type="text" name="city" class="form-control" value="{{ old('city', $branch->{{ __('city) }}">') }}
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.phone') }}</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->{{ __('phone) }}">') }}
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.manager_name') }}</label>
            <input type="text" name="manager_name" class="form-control" value="{{ old('manager_name', $branch->{{ __('manager_name) }}">') }}
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $branch->{{ __('is_active ? \'checked\' : \'\' }}>') }}
            <label class="form-check-label">{{ __('messages.active') }}</label>
        </div>
        <button class="btn btn-primary">{{ __('messages.save') }}</button>
    </form>
</div>
@endsection
