@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ $supplier->name }}</h3>

    <div class="card">
        <div class="card-body">
            <p><strong>{{ __('Email') }}:</strong> {{ $supplier->email }}</p>
            <p><strong>{{ __('Phone') }}:</strong> {{ $supplier->phone }}</p>
            <p><strong>{{ __('Address') }}:</strong> {{ $supplier->address }}</p>
            <p><strong>{{ __('Balance') }}:</strong> {{ number_format($supplier->opening_balance,2) }}</p>
        </div>
    </div>
</div>
@endsection
