@extends('layouts.super-admin')

@section('content')
<div class="container-fluid">
    <div class="page-header merchant-edit-header mb-4">
        <h1 class="page-title">{{ __('Edit Merchant') }}</h1>
    </div>

    <style>
        @media (max-width: 768px) {
            .merchant-edit-header {
                margin-bottom: 16px !important;
            }

            .merchant-edit-header .page-title {
                font-size: 22px;
            }

            .merchant-edit-header .page-title i {
                font-size: 22px;
            }

            .merchant-edit-header + .row > .col-lg-8,
            .merchant-edit-header + .row > .col-lg-4 {
                width: 100%;
            }

            .card-body,
            .card-footer {
                padding: 16px;
            }

            .row .col-md-6,
            .row .col-lg-8,
            .row .col-lg-4 {
                width: 100%;
            }

            .card-footer .btn,
            .card-footer a {
                width: 100%;
                margin-bottom: 8px;
            }
        }
    </style>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>{{ __('Validation Errors:') }}</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Merchant Information') }}</h5>
                </div>
                <form action="{{ route('super-admin.merchants.update', $merchant) }}" method="POST">
                    {{ __('@csrf @method(\'PUT\')') }}

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Business Name *') }}</label>
                            <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror" 
                                   value="{{ old('business_name', $merchant->{{ __('business_name) }}" required>
                            @error(\'business_name\')') }} <div class="invalid-feedback">{{ $message }}</div> {{ __('@enderror') }}
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Max Currencies Allowed *') }}</label>
                                <input type="number" name="max_currencies" class="form-control @error('max_currencies') is-invalid @enderror"
                                       value="{{ old('max_currencies', $merchant->{{ __('max_currencies) }}" min="1" max="20" required>
                                @error(\'max_currencies\')') }} <div class="invalid-feedback">{{ $message }}</div> {{ __('@enderror') }}
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Max Languages Allowed *') }}</label>
                                <input type="number" name="max_languages" class="form-control @error('max_languages') is-invalid @enderror"
                                       value="{{ old('max_languages', $merchant->{{ __('max_languages) }}" min="1" max="10" required>
                                @error(\'max_languages\')') }} <div class="invalid-feedback">{{ $message }}</div> {{ __('@enderror') }}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Default Language *') }}</label>
                                <select name="default_language" class="form-select @error('default_language') is-invalid @enderror" required>
                                    <option value="en" {{ old('default_language', $merchant->{{ __('default_language) == \'en\' ? \'selected\' : \'\' }}>English') }}</option>
                                    <option value="ar" {{ old('default_language', $merchant->{{ __('default_language) == \'ar\' ? \'selected\' : \'\' }}>Arabic') }}</option>
                                    <option value="tr" {{ old('default_language', $merchant->{{ __('default_language) == \'tr\' ? \'selected\' : \'\' }}>Turkish') }}</option>
                                </select>
                                {{ __('@error(\'default_language\')') }} <div class="invalid-feedback">{{ $message }}</div> {{ __('@enderror') }}
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Max Employees (Leave empty for unlimited)') }}</label>
                                <input type="number" name="max_employees" class="form-control @error('max_employees') is-invalid @enderror"
                                       value="{{ old('max_employees', $merchant->{{ __('max_employees) }}" min="1">
                                @error(\'max_employees\')') }} <div class="invalid-feedback">{{ $message }}</div> {{ __('@enderror') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $merchant->description) }}</textarea>
                            {{ __('@error(\'description\')') }} <div class="invalid-feedback">{{ $message }}</div> {{ __('@enderror') }}
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                       value="1" {{ old('is_active', $merchant->{{ __('is_active) ? \'checked\' : \'\' }}>') }}
                                <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <button type="submit" class="btn btn-primary">{{ __('Update Merchant') }}</button>
                        <a href="{{ route('super-admin.merchants.show', $merchant) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
