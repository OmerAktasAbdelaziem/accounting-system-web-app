@extends('layouts.modern')

@section('title', isset($storage) ? __('messages.edit_storage') : __('messages.new_storage'))

@section('content')
<div class="mb-4">
    <a href="{{ route('storages.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
    </a>
    <h1 style="font-weight: 900; color: #1a1a1a;">
        <i class="bi bi-archive" style="color: #ff8c00;"></i> {{ isset($storage) ? __('messages.edit_storage') : __('messages.new_storage') }}
    </h1>
</div>

<div class="card">
    <form method="POST" action="{{ isset($storage) ? route('storages.update', $storage->id) : route('storages.store') }}">
        @csrf
        @if(isset($storage))
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">{{ __('messages.storage_name') }} *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            id="name" name="name" value="{{ old('name', $storage->name ?? '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="location" class="form-label">{{ __('messages.location') }} *</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                            id="location" name="location" value="{{ old('location', $storage->location ?? '') }}" required>
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="storage_type" class="form-label">{{ __('messages.storage_type') }} *</label>
                        <select class="form-select @error('storage_type') is-invalid @enderror" id="storage_type" name="storage_type" required>
                            <option value="">{{ __('messages.select_type') }}</option>
                            <option value="warehouse" {{ old('storage_type', $storage->storage_type ?? '') === 'warehouse' ? 'selected' : '' }}>{{ __('messages.warehouse') }}</option>
                            <option value="cold_storage" {{ old('storage_type', $storage->storage_type ?? '') === 'cold_storage' ? 'selected' : '' }}>{{ __('messages.cold_storage') }}</option>
                            <option value="rack" {{ old('storage_type', $storage->storage_type ?? '') === 'rack' ? 'selected' : '' }}>{{ __('messages.rack') }}</option>
                            <option value="shelf" {{ old('storage_type', $storage->storage_type ?? '') === 'shelf' ? 'selected' : '' }}>{{ __('messages.shelf') }}</option>
                        </select>
                        @error('storage_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="capacity" class="form-label">{{ __('messages.storage_capacity') }} ({{ __('messages.units') }})</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                            id="capacity" name="capacity" step="0.01" min="0"
                            value="{{ old('capacity', $storage->capacity ?? '') }}">
                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                    id="description" name="description" rows="3">{{ old('description', $storage->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-check form-check-lg mb-3">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', $storage->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">
                    {{ __('messages.active') }}
                </label>
            </div>
        </div>

        <div class="card-footer" style="background: #f9f9f9; padding: 20px;">
            <button type="submit" class="btn btn-primary-modern">
                <i class="bi bi-check-circle"></i> {{ isset($storage) ? __('messages.update') : __('messages.save') }}
            </button>
            <a href="{{ route('storages.index') }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
