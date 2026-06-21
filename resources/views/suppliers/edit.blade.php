@extends('layouts.modern')

@section('content')
<div class="container">
    <h3>{{ __('messages.edit_supplier') }}</h3>

    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">{{ __('messages.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $supplier->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.address') }}</label>
            <textarea name="address" class="form-control">{{ old('address', $supplier->address) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('messages.opening_balance') }}</label>
            <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $supplier->opening_balance) }}">
        </div>

        @include('branches.partials.multi-select', ['branches' => $branches ?? [], 'selectedBranchIds' => $selectedBranchIds ?? []])

        <button class="btn btn-primary">{{ __('messages.save') }}</button>
    </form>
</div>
@endsection
