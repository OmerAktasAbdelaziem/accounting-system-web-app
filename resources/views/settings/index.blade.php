@extends('layouts.modern')

@section('title', __('settings.title'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        {{ __('settings.system_settings') }}
                    </h4>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ __('messages.error') }}!</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Application Settings -->
                        <div class="mb-4">
                            <h5 class="card-title text-primary border-bottom pb-2">
                                <i class="fas fa-sliders-h me-2"></i>
                                {{ __('settings.application_settings') }}
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="app_name" class="form-label">
                                            {{ __('settings.app_name') }}
                                        </label>
                                        <input type="text" class="form-control @error('app_name') is-invalid @enderror" 
                                               id="app_name" name="app_name" 
                                               value="{{ old('app_name', $currentSettings['app_name']) }}" 
                                               required>
                                        @error('app_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="language" class="form-label">
                                            {{ __('settings.default_language') }}
                                        </label>
                                        <select class="form-select @error('language') is-invalid @enderror" 
                                                id="language" name="language" required>
                                            <option value="en" {{ old('language', $currentSettings['language']) === 'en' ? 'selected' : '' }}>
                                                English
                                            </option>
                                            <option value="ar" {{ old('language', $currentSettings['language']) === 'ar' ? 'selected' : '' }}>
                                                العربية
                                            </option>
                                        </select>
                                        @error('language')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="timezone" class="form-label">
                                            {{ __('settings.timezone') }}
                                        </label>
                                        <select class="form-select @error('timezone') is-invalid @enderror" 
                                                id="timezone" name="timezone" required>
                                            <option value="">{{ __('settings.select_timezone') }}</option>
                                            @foreach (timezone_identifiers_list() as $tz)
                                                <option value="{{ $tz }}" {{ old('timezone', $currentSettings['timezone']) === $tz ? 'selected' : '' }}>
                                                    {{ $tz }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('timezone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_format" class="form-label">
                                            {{ __('settings.date_format') }}
                                        </label>
                                        <select class="form-select @error('date_format') is-invalid @enderror" 
                                                id="date_format" name="date_format" required>
                                            <option value="Y-m-d" {{ old('date_format', $currentSettings['date_format']) === 'Y-m-d' ? 'selected' : '' }}>
                                                2026-04-30
                                            </option>
                                            <option value="d/m/Y" {{ old('date_format', $currentSettings['date_format']) === 'd/m/Y' ? 'selected' : '' }}>
                                                30/04/2026
                                            </option>
                                            <option value="m/d/Y" {{ old('date_format', $currentSettings['date_format']) === 'm/d/Y' ? 'selected' : '' }}>
                                                04/30/2026
                                            </option>
                                            <option value="d-m-Y" {{ old('date_format', $currentSettings['date_format']) === 'd-m-Y' ? 'selected' : '' }}>
                                                30-04-2026
                                            </option>
                                        </select>
                                        @error('date_format')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Settings -->
                        <div class="mb-4">
                            <h5 class="card-title text-primary border-bottom pb-2">
                                <i class="fas fa-money-bill me-2"></i>
                                {{ __('settings.financial_settings') }}
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="currency" class="form-label">
                                            {{ __('settings.currency') }}
                                        </label>
                                        <select class="form-select @error('currency') is-invalid @enderror" 
                                                id="currency" name="currency" required>
                                            <option value="AED" {{ old('currency', $currentSettings['currency']) === 'AED' ? 'selected' : '' }}>
                                                AED - {{ __('settings.united_arab_emirates') }}
                                            </option>
                                            <option value="USD" {{ old('currency', $currentSettings['currency']) === 'USD' ? 'selected' : '' }}>
                                                USD - {{ __('settings.united_states') }}
                                            </option>
                                            <option value="EGP" {{ old('currency', $currentSettings['currency']) === 'EGP' ? 'selected' : '' }}>
                                                EGP - {{ __('settings.egypt') }}
                                            </option>
                                            <option value="SAR" {{ old('currency', $currentSettings['currency']) === 'SAR' ? 'selected' : '' }}>
                                                SAR - {{ __('settings.saudi_arabia') }}
                                            </option>
                                            <option value="KWD" {{ old('currency', $currentSettings['currency']) === 'KWD' ? 'selected' : '' }}>
                                                KWD - {{ __('settings.kuwait') }}
                                            </option>
                                            <option value="QAR" {{ old('currency', $currentSettings['currency']) === 'QAR' ? 'selected' : '' }}>
                                                QAR - {{ __('settings.qatar') }}
                                            </option>
                                            <option value="BHD" {{ old('currency', $currentSettings['currency']) === 'BHD' ? 'selected' : '' }}>
                                                BHD - {{ __('settings.bahrain') }}
                                            </option>
                                            <option value="OMR" {{ old('currency', $currentSettings['currency']) === 'OMR' ? 'selected' : '' }}>
                                                OMR - {{ __('settings.oman') }}
                                            </option>
                                            <option value="JOD" {{ old('currency', $currentSettings['currency']) === 'JOD' ? 'selected' : '' }}>
                                                JOD - {{ __('settings.jordan') }}
                                            </option>
                                        </select>
                                        @error('currency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="decimal_places" class="form-label">
                                            {{ __('settings.decimal_places') }}
                                        </label>
                                        <input type="number" class="form-control @error('decimal_places') is-invalid @enderror" 
                                               id="decimal_places" name="decimal_places" 
                                               value="{{ old('decimal_places', $currentSettings['decimal_places']) }}" 
                                               min="0" max="4" required>
                                        <small class="text-muted">{{ __('settings.decimal_places_help') }}</small>
                                        @error('decimal_places')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="financial_year_start" class="form-label">
                                            {{ __('settings.financial_year_start') }}
                                        </label>
                                        <input type="text" class="form-control @error('financial_year_start') is-invalid @enderror" 
                                               id="financial_year_start" name="financial_year_start" 
                                               value="{{ old('financial_year_start', $currentSettings['financial_year_start']) }}" 
                                               placeholder="MM-DD" maxlength="5" required>
                                        <small class="text-muted">{{ __('settings.financial_year_start_help') }}</small>
                                        @error('financial_year_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tax_rate" class="form-label">
                                            {{ __('settings.tax_rate') }} (%)
                                        </label>
                                        <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" 
                                               id="tax_rate" name="tax_rate" 
                                               value="{{ old('tax_rate', $currentSettings['tax_rate']) }}" 
                                               min="0" max="100" step="0.01" required>
                                        @error('tax_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="mb-4">
                            <h5 class="card-title text-primary border-bottom pb-2">
                                <i class="fas fa-shield-alt me-2"></i>
                                {{ __('settings.system_features') }}
                            </h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_notifications" 
                                               name="enable_notifications" value="1"
                                               {{ old('enable_notifications', $currentSettings['enable_notifications']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_notifications">
                                            {{ __('settings.enable_notifications') }}
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        {{ __('settings.enable_notifications_help') }}
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_audit_logs" 
                                               name="enable_audit_logs" value="1"
                                               {{ old('enable_audit_logs', $currentSettings['enable_audit_logs']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_audit_logs">
                                            {{ __('settings.enable_audit_logs') }}
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        {{ __('settings.enable_audit_logs_help') }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('system.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                {{ __('actions.back') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                {{ __('actions.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-check-input {
        width: 2.5em;
        height: 1.5em;
    }

    .card {
        transition: box-shadow 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.2) !important;
    }
</style>
@endsection
