@extends('layouts.modern')

@section('title', __('messages.add_product'))

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>{{ $product ?? false ? __('messages.edit_product') : __('messages.add_product') }}</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
    </div>
</div>

<div class="card">
    <form method="POST" action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($product))
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label">{{ __('messages.product_name') }} *</label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $product->name ?? '') }}"
                            required
                        >
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="sku" class="form-label">{{ __('messages.sku') }} *</label>
                        <input 
                            type="text" 
                            class="form-control @error('sku') is-invalid @enderror" 
                            id="sku" 
                            name="sku" 
                            value="{{ old('sku', $product->sku ?? '') }}"
                            required
                        >
                        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="category_id" class="form-label">{{ __('messages.category') }} *</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">-- {{ __('messages.select') }} --</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="selling_price" class="form-label">{{ __('messages.price') }} *</label>
                        <input 
                            type="number" 
                            class="form-control @error('selling_price') is-invalid @enderror" 
                            id="selling_price" 
                            name="selling_price" 
                            step="0.01"
                            value="{{ old('selling_price', $product->selling_price ?? '') }}"
                            required
                        >
                        @error('selling_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="purchase_price" class="form-label">{{ __('messages.purchase_price') }} *</label>
                        <input 
                            type="number" 
                            class="form-control @error('purchase_price') is-invalid @enderror" 
                            id="purchase_price" 
                            name="purchase_price" 
                            step="0.01"
                            value="{{ old('purchase_price', $product->purchase_price ?? '') }}"
                            required
                        >
                        @error('purchase_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="current_stock" class="form-label">{{ __('messages.quantity') }} *</label>
                        <input 
                            type="number" 
                            class="form-control @error('current_stock') is-invalid @enderror" 
                            id="current_stock" 
                            name="current_stock" 
                            value="{{ old('current_stock', $product->current_stock ?? 0) }}"
                            required
                        >
                        @error('current_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="min_stock" class="form-label">{{ __('messages.reorder_level') }}</label>
                        <input 
                            type="number" 
                            class="form-control @error('min_stock') is-invalid @enderror" 
                            id="min_stock" 
                            name="min_stock" 
                            value="{{ old('min_stock', $product->min_stock ?? 10) }}"
                        >
                        @error('min_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="description" class="form-label">{{ __('messages.description') }}</label>
                <textarea 
                    class="form-control @error('description') is-invalid @enderror" 
                    id="description" 
                    name="description" 
                    rows="4"
                >{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-check mb-3">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="is_active" 
                    name="is_active" 
                    value="1"
                    {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="is_active">
                    {{ __('messages.is_active') }}
                </label>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> {{ __('messages.save') }}
            </button>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>
@endsection
