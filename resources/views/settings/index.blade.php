@extends('layouts.modern')

@section('title', __('settings.title'))

@section('css')
<style>
    .settings-hero {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 55%, #ff8c00 140%);
        color: #fff;
        border-radius: 24px;
        padding: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
    }

    .settings-hero::after {
        content: '';
        position: absolute;
        inset: auto -120px -120px auto;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    .settings-hero .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .settings-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-top: 20px;
    }

    .settings-summary .summary-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 18px;
        padding: 16px;
        backdrop-filter: blur(8px);
    }

    .settings-summary .summary-card h6 {
        margin: 0 0 6px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .08em;
        opacity: .8;
    }

    .settings-summary .summary-card strong {
        display: block;
        font-size: 18px;
    }

    .settings-layout {
        margin-top: 24px;
    }

    .settings-panel,
    .settings-side {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .settings-panel .card-header,
    .settings-side .card-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        color: #fff;
        border: none;
        padding: 18px 22px;
    }

    .settings-panel .card-body,
    .settings-side .card-body {
        background: #fff;
        padding: 22px;
    }

    .settings-section {
        background: #fff;
        border: 1px solid #eef0f3;
        border-radius: 18px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .settings-section .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        margin-bottom: 18px;
        color: #1a1a1a;
    }

    .settings-help {
        display: block;
        color: #7a7a7a;
        font-size: 12px;
        margin-top: 6px;
    }

    .settings-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .settings-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #f4f6f8;
        color: #1a1a1a;
        font-size: 12px;
        font-weight: 700;
    }

    @media (max-width: 991px) {
        .settings-summary { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="settings-hero">
        <span class="eyebrow"><i class="fas fa-cog"></i> {{ __('settings.system_settings') }}</span>
        <div class="mt-3">
            <h2 class="fw-bold mb-2">Business settings, branding, and system controls</h2>
            <p class="mb-0" style="max-width: 720px; opacity: .9;">
                Keep the merchant identity, financial defaults, and feature switches in one place. The application name here also drives the left-top navbar.
            </p>
        </div>
        <div class="settings-summary">
            <div class="summary-card">
                <h6>Brand Name</h6>
                <strong>{{ $currentSettings['app_name'] }}</strong>
            </div>
            <div class="summary-card">
                <h6>Language</h6>
                <strong>{{ strtoupper($currentSettings['language']) }}</strong>
            </div>
            <div class="summary-card">
                <h6>Currency</h6>
                <strong>{{ $currentSettings['currency'] }}</strong>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
            <strong>{{ __('messages.error') }}!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row settings-layout g-4">
        <div class="col-lg-8">
            <div class="settings-panel card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-sliders-h me-2"></i>{{ __('settings.application_settings') }}</h4>
                    <span class="settings-badge"><i class="fas fa-bolt"></i> Live branding</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="settings-section">
                            <div class="section-title"><i class="fas fa-building text-primary"></i> Business identity</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="app_name" class="form-label">Business Name / Application Name</label>
                                    <input type="text" class="form-control @error('app_name') is-invalid @enderror" id="app_name" name="app_name" value="{{ old('app_name', $currentSettings['app_name']) }}" required>
                                    <span class="settings-help">This value appears in the top-left navbar and on invoices/reports that use the app title.</span>
                                    @error('app_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="language" class="form-label">{{ __('settings.default_language') }}</label>
                                    <select class="form-select @error('language') is-invalid @enderror" id="language" name="language" required>
                                        <option value="en" {{ old('language', $currentSettings['language']) === 'en' ? 'selected' : '' }}>English</option>
                                        <option value="ar" {{ old('language', $currentSettings['language']) === 'ar' ? 'selected' : '' }}>???????</option>
                                    </select>
                                    @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="timezone" class="form-label">{{ __('settings.timezone') }}</label>
                                    <select class="form-select @error('timezone') is-invalid @enderror" id="timezone" name="timezone" required>
                                        <option value="">{{ __('settings.select_timezone') }}</option>
                                        @foreach (timezone_identifiers_list() as $tz)
                                            <option value="{{ $tz }}" {{ old('timezone', $currentSettings['timezone']) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                        @endforeach
                                    </select>
                                    @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="date_format" class="form-label">{{ __('settings.date_format') }}</label>
                                    <select class="form-select @error('date_format') is-invalid @enderror" id="date_format" name="date_format" required>
                                        <option value="Y-m-d" {{ old('date_format', $currentSettings['date_format']) === 'Y-m-d' ? 'selected' : '' }}>2026-04-30</option>
                                        <option value="d/m/Y" {{ old('date_format', $currentSettings['date_format']) === 'd/m/Y' ? 'selected' : '' }}>30/04/2026</option>
                                        <option value="m/d/Y" {{ old('date_format', $currentSettings['date_format']) === 'm/d/Y' ? 'selected' : '' }}>04/30/2026</option>
                                        <option value="d-m-Y" {{ old('date_format', $currentSettings['date_format']) === 'd-m-Y' ? 'selected' : '' }}>30-04-2026</option>
                                    </select>
                                    @error('date_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="settings-section">
                            <div class="section-title"><i class="fas fa-coins text-primary"></i> Financial defaults</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="currency" class="form-label">{{ __('settings.currency') }}</label>
                                    <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                        <option value="AED" {{ old('currency', $currentSettings['currency']) === 'AED' ? 'selected' : '' }}>AED - {{ __('settings.united_arab_emirates') }}</option>
                                        <option value="USD" {{ old('currency', $currentSettings['currency']) === 'USD' ? 'selected' : '' }}>USD - {{ __('settings.united_states') }}</option>
                                        <option value="EGP" {{ old('currency', $currentSettings['currency']) === 'EGP' ? 'selected' : '' }}>EGP - {{ __('settings.egypt') }}</option>
                                        <option value="SAR" {{ old('currency', $currentSettings['currency']) === 'SAR' ? 'selected' : '' }}>SAR - {{ __('settings.saudi_arabia') }}</option>
                                        <option value="KWD" {{ old('currency', $currentSettings['currency']) === 'KWD' ? 'selected' : '' }}>KWD - {{ __('settings.kuwait') }}</option>
                                        <option value="QAR" {{ old('currency', $currentSettings['currency']) === 'QAR' ? 'selected' : '' }}>QAR - {{ __('settings.qatar') }}</option>
                                        <option value="BHD" {{ old('currency', $currentSettings['currency']) === 'BHD' ? 'selected' : '' }}>BHD - {{ __('settings.bahrain') }}</option>
                                        <option value="OMR" {{ old('currency', $currentSettings['currency']) === 'OMR' ? 'selected' : '' }}>OMR - {{ __('settings.oman') }}</option>
                                        <option value="JOD" {{ old('currency', $currentSettings['currency']) === 'JOD' ? 'selected' : '' }}>JOD - {{ __('settings.jordan') }}</option>
                                    </select>
                                    @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="decimal_places" class="form-label">{{ __('settings.decimal_places') }}</label>
                                    <input type="number" class="form-control @error('decimal_places') is-invalid @enderror" id="decimal_places" name="decimal_places" value="{{ old('decimal_places', $currentSettings['decimal_places']) }}" min="0" max="4" required>
                                    <small class="settings-help">{{ __('settings.decimal_places_help') }}</small>
                                    @error('decimal_places')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="financial_year_start" class="form-label">{{ __('settings.financial_year_start') }}</label>
                                    <input type="text" class="form-control @error('financial_year_start') is-invalid @enderror" id="financial_year_start" name="financial_year_start" value="{{ old('financial_year_start', $currentSettings['financial_year_start']) }}" placeholder="MM-DD" maxlength="5" required>
                                    <small class="settings-help">{{ __('settings.financial_year_start_help') }}</small>
                                    @error('financial_year_start')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="tax_rate" class="form-label">{{ __('settings.tax_rate') }} (%)</label>
                                    <input type="number" class="form-control @error('tax_rate') is-invalid @enderror" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $currentSettings['tax_rate']) }}" min="0" max="100" step="0.01" required>
                                    @error('tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="settings-section mb-0">
                            <div class="section-title"><i class="fas fa-shield-alt text-primary"></i> System features</div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_notifications" name="enable_notifications" value="1" {{ old('enable_notifications', $currentSettings['enable_notifications']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_notifications">{{ __('settings.enable_notifications') }}</label>
                                    </div>
                                    <small class="settings-help">{{ __('settings.enable_notifications_help') }}</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="enable_audit_logs" name="enable_audit_logs" value="1" {{ old('enable_audit_logs', $currentSettings['enable_audit_logs']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_audit_logs">{{ __('settings.enable_audit_logs') }}</label>
                                    </div>
                                    <small class="settings-help">{{ __('settings.enable_audit_logs_help') }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="settings-actions mt-4">
                            <button type="submit" class="btn btn-primary-modern btn-sm">
                                <i class="fas fa-save me-2"></i>
                                {{ __('messages.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="settings-side card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i> Preview</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">How the current branding will appear in the interface:</p>
                    <div class="p-3 rounded-4" style="background: linear-gradient(135deg, #f8f9fb 0%, #eef2f7 100%); border: 1px solid #e8edf3;">
                        <div class="fw-bold mb-1">{{ $currentSettings['app_name'] }}</div>
                        <div class="small text-muted">Top-left navbar brand</div>
                    </div>
                </div>
            </div>

            <div class="settings-side card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-circle-info me-2"></i> Notes</h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3 text-muted">
                        <li>Merchant accounts can use this page to rename their business branding.</li>
                        <li>The same name is shown in the top navbar across the app.</li>
                        <li>Audit logs can be disabled here if a merchant does not need them.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
