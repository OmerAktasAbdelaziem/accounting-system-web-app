@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ $branch->name }}</h3>

    <div class="card">
        <div class="card-body">
            <p><strong>{{ __('messages.branch_code') }}:</strong> {{ $branch->code }}</p>
            <p><strong>{{ __('messages.address') }}:</strong> {{ $branch->address }}</p>
            <p><strong>{{ __('messages.city') }}:</strong> {{ $branch->city }}</p>
            <p><strong>{{ __('messages.phone') }}:</strong> {{ $branch->phone }}</p>
            <p><strong>{{ __('messages.manager_name') }}:</strong> {{ $branch->manager_name }}</p>
            <p><strong>{{ __('messages.status') }}:</strong> {{ $branch->is_active ? __('messages.active') : __('messages.inactive') }}</p>
        </div>
    </div>
</div>
@endsection
