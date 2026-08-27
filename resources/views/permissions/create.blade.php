@extends('layouts.modern')

@section('title', __('permissions.add_permission'))

@section('content')
<style>
    .create-shell { min-height: 100vh; padding: 32px 0 48px; background: linear-gradient(180deg, #f7f7f8 0%, #eef1f5 100%); }
    .create-hero { background: linear-gradient(135deg, #16181d 0%, #23262d 100%); color: #fff; border-radius: 28px; padding: 28px 30px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.18); }
    .create-card { border: 0; border-radius: 28px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.1); overflow: hidden; }
    .create-field { min-height: 52px; border-radius: 14px; border-color: #d9dde5; }
    .create-field:focus { border-color: #ff8c00; box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1); }
</style>

<div class="create-shell">
    <div class="container-fluid">
        <div class="create-hero mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">{{ __('Access Control') }}</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ __('permissions.add_permission') }}</h1>
                <p class="mb-0 text-white-50">{{ __('Create a permission with a faster, cleaner workflow.') }}</p>
            </div>
            <a href="{{ route('permissions.index') }}" class="btn btn-light rounded-pill px-3"><i class="fas fa-arrow-left me-2"></i>{{ __('actions.back') }}</a>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-xl-8">
                <div class="card create-card">
                    <div class="card-body p-4 p-lg-5">
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4 mb-4">
                                <h5 class="alert-heading">{{ __('validation.failed') }}</h5>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('permissions.store') }}" method="POST" class="row g-3">
                            {{ __('@csrf') }}

                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">{{ __('permissions.permission_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control create-field @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., create_products" required>
                                {{ __('@error(\'name\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                                <small class="text-muted d-block mt-1">{{ __('permissions.use_snake_case') }}</small>
                            </div>

                            <div class="col-12">
                                <label for="category" class="form-label fw-semibold">{{ __('permissions.category') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control create-field @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" placeholder="e.g., products, users, reports" list="categories" required>
                                <datalist id="categories">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                                    @endforeach
                                </datalist>
                                {{ __('@error(\'category\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">{{ __('permissions.description') }}</label>
                                <textarea class="form-control create-field @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                {{ __('@error(\'description\')') }}<div class="invalid-feedback">{{ $message }}</div>{{ __('@enderror') }}
                            </div>

                            <div class="col-12 d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>{{ __('actions.save') }}</button>
                                <a href="{{ route('permissions.index') }}" class="btn btn-outline-secondary px-4">{{ __('actions.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
