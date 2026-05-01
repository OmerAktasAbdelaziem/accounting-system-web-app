@extends('layouts.modern')

@section('title', isset($category) ? __('messages.edit_category') : __('messages.add_category'))

@section('content')
<div class="mb-4">
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
    </a>
    <h1 style="font-weight: 900; color: #1a1a1a;">
        <i class="bi bi-tags" style="color: #ff8c00;"></i> {{ isset($category) ? __('messages.edit_category') : __('messages.add_category') }}
    </h1>
</div>

<div class="card">
    <form method="POST" action="{{ isset($category) ? route('categories.update', $category->id) : route('categories.store') }}">
        @csrf
        @if(isset($category) && $category)
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">{{ __('messages.category_name') }} *</label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', isset($category) && $category ? $category->name : '') }}"
                            placeholder="{{ __('messages.example_category_placeholder') }}"
                            required
                        >
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="is_active" class="form-label">{{ __('messages.status') }}</label>
                        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                            <option value="1" {{ old('is_active', isset($category) && $category ? $category->is_active : true) ? 'selected' : '' }}>
                                {{ __('messages.active') }}
                            </option>
                            <option value="0" {{ old('is_active', isset($category) && $category ? $category->is_active : true) ? '' : 'selected' }}>
                                {{ __('messages.inactive') }}
                            </option>
                        </select>
                        @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">{{ __('messages.description') }}</label>
                        <textarea 
                            class="form-control @error('description') is-invalid @enderror" 
                            id="description" 
                            name="description" 
                            rows="4"
                            placeholder="{{ __('messages.enter_category_description') }}"
                        >{{ old('description', isset($category) && $category ? $category->description : '') }}</textarea>
                        <small class="text-muted">{{ __('messages.max_characters_1000') }}</small>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            @if(isset($category) && $category)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>{{ __('messages.category_information') }}</strong>
                    <ul class="mb-0 mt-2">
                        <li>{{ __('messages.total_products') }}: <strong>{{ $category->products()->count() }}</strong></li>
                        <li>{{ __('messages.created') }}: <strong>{{ $category->created_at->format('M d, Y') }}</strong></li>
                        <li>{{ __('messages.last_updated') }}: <strong>{{ $category->updated_at->format('M d, Y') }}</strong></li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> {{ isset($category) ? __('messages.update') : __('messages.save') }}
            </button>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
