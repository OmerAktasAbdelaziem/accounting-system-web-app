@extends('layouts.modern')

@section('title', isset($safe) ? __('messages.edit_safe') : __('messages.new_safe'))

@section('content')
<div class="mb-4">
    <a href="{{ route('safes.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
    </a>
    <h1 style="font-weight: 900; color: #1a1a1a;">
        <i class="bi bi-safe" style="color: #ff8c00;"></i> {{ isset($safe) ? __('messages.edit_safe') : __('messages.new_safe') }}
    </h1>
</div>

<div class="card">
    <form method="POST" action="{{ isset($safe) ? route('safes.update', $safe->id) : route('safes.store') }}">
        @csrf
        @if(isset($safe))
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">{{ __('messages.safe_name') }} *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            id="name" name="name" value="{{ old('name', $safe->name ?? '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="location" class="form-label">{{ __('messages.location') }} *</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                            id="location" name="location" value="{{ old('location', $safe->location ?? '') }}" required>
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="max_balance" class="form-label">{{ __('messages.max_balance') }}</label>
                        <input type="number" class="form-control @error('max_balance') is-invalid @enderror" 
                            id="max_balance" name="max_balance" step="0.01" min="0"
                            value="{{ old('max_balance', $safe->max_balance ?? '') }}">
                        <small class="text-muted">{{ __('messages.leave_empty_unlimited') }}</small>
                        @error('max_balance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                @if(isset($safe))
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('messages.current_balance') }}</label>
                        <h4 style="color: var(--primary-green); font-weight: 900;">{{ $currencySymbol }}{{ number_format($safe->balance, 2) }}</h4>
                    </div>
                </div>
                @endif
            </div>

            <div class="form-check form-check-lg mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', $safe->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    {{ __('messages.active') }}
                </label>
            </div>
        </div>

        <div class="card-footer" style="background: #f9f9f9; padding: 20px;">
            <button type="submit" class="btn btn-primary-modern">
                <i class="bi bi-check-circle"></i> {{ isset($safe) ? __('messages.update') : __('messages.save') }}
            </button>
            <a href="{{ route('safes.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
