@extends('layouts.modern')

@section('title', __('messages.login'))

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        overflow-x: hidden;
    }

    .merchant-login-shell {
        height: 100vh;
        width: 100vw;
        position: fixed;
        top: 0;
        left: 0;
        background:
            radial-gradient(circle at top left, rgba(255, 140, 0, 0.25), transparent 40%),
            radial-gradient(circle at bottom right, rgba(255, 179, 71, 0.2), transparent 35%),
            linear-gradient(135deg, #0e0f12 0%, #17191f 48%, #101216 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        z-index: 0;
    }

    .merchant-login-wrapper {
        position: relative;
        z-index: 10;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .merchant-login-container {
        width: 100%;
        max-width: 900px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
    }

    .merchant-login-hero {
        background: linear-gradient(160deg, #17191f 0%, #23262d 50%, #ff8c00 150%);
        color: #fff;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .merchant-login-form-section {
        background: #ffffff;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .merchant-login-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.15);
        font-size: 13px;
        width: fit-content;
        margin-bottom: 24px;
    }

    .merchant-login-hero h1 {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 16px;
        letter-spacing: -0.03em;
    }

    .merchant-login-hero p {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 28px;
        line-height: 1.6;
    }

    .merchant-login-features {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .merchant-login-feature {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .merchant-login-feature-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 18px;
    }

    .merchant-login-feature-text {
        flex: 1;
    }

    .merchant-login-feature-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 2px;
    }

    .merchant-login-feature-desc {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
    }

    .merchant-login-footer {
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 13px;
    }

    .merchant-login-footer a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .merchant-login-footer a:hover {
        color: #ffffff;
    }

    .merchant-form-header {
        margin-bottom: 28px;
    }

    .merchant-form-header-badge {
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #999;
        margin-bottom: 8px;
        display: block;
    }

    .merchant-form-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 8px;
        letter-spacing: -0.03em;
    }

    .merchant-form-header p {
        font-size: 13px;
        color: #6b7280;
    }

    .merchant-form-group {
        margin-bottom: 20px;
    }

    .merchant-form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .merchant-form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #d9dce3;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .merchant-form-control:hover {
        border-color: #bbc0c7;
    }

    .merchant-form-control:focus {
        outline: none;
        border-color: #ff8c00;
        box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.12);
    }

    .merchant-form-control.is-invalid {
        border-color: #ef4444;
    }

    .merchant-form-control.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12);
    }

    .merchant-form-checkbox {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .merchant-form-checkbox input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #ff8c00;
    }

    .merchant-form-checkbox label {
        font-size: 13px;
        color: #1f2937;
        cursor: pointer;
        margin: 0;
    }

    .merchant-form-button {
        width: 100%;
        padding: 12px 16px;
        background: linear-gradient(135deg, #ff8c00, #ffb347);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(255, 140, 0, 0.25);
    }

    .merchant-form-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(255, 140, 0, 0.35);
    }

    .merchant-form-button:active {
        transform: translateY(0);
    }

    .merchant-invalid-feedback {
        font-size: 12px;
        color: #ef4444;
        margin-top: 6px;
        display: block;
    }

    .merchant-error-alert {
        background: #fee;
        border: 1px solid #fcc;
        color: #c33;
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .merchant-language-toggle {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: white;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .merchant-language-toggle:hover {
        background: rgba(255, 255, 255, 0.25);
        color: white;
    }

    @media (max-width: 768px) {
        body {
            overflow: auto;
        }

        .merchant-login-shell {
            position: relative;
            min-height: 100vh;
            height: auto;
            padding: 12px 0;
        }

        .merchant-login-wrapper {
            padding: 12px;
            height: auto;
            align-items: stretch;
        }

        .merchant-login-container {
            grid-template-columns: 1fr;
            max-width: 100%;
        }

        .merchant-login-hero {
            padding: 30px;
            min-height: 300px;
        }

        .merchant-login-form-section {
            padding: 30px;
        }

        .merchant-login-hero h1 {
            font-size: 22px;
        }

        .merchant-form-header h2 {
            font-size: 20px;
        }

        .merchant-language-toggle {
            top: 15px;
            right: 15px;
            padding: 5px 10px;
            font-size: 11px;
        }

        .merchant-login-footer {
            justify-content: center;
        }

        .merchant-login-footer a {
            width: 100%;
            text-align: center;
        }

        .merchant-form-control,
        .merchant-form-button {
            min-height: 44px;
        }
    }

    @media (max-width: 480px) {
        .merchant-login-hero {
            padding: 24px;
            min-height: 280px;
        }

        .merchant-login-form-section {
            padding: 24px;
        }

        .merchant-login-hero h1 {
            font-size: 20px;
        }

        .merchant-form-header h2 {
            font-size: 18px;
        }

        .merchant-login-features {
            gap: 16px;
        }

        .merchant-login-feature-icon {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .merchant-login-feature-title {
            font-size: 13px;
        }

        .merchant-login-feature-desc {
            font-size: 11px;
        }

        .merchant-login-container {
            border-radius: 18px;
        }

        .merchant-login-hero,
        .merchant-login-form-section {
            padding: 20px;
        }
    }
</style>

<div class="merchant-login-shell">
    <div class="merchant-login-wrapper">
        <div class="merchant-login-container">
            <!-- Hero Section -->
            <div class="merchant-login-hero">
                <div>
                    <div class="merchant-login-pill">
                        <i class="bi bi-briefcase-fill"></i>
                        {{ __('messages.accounting_system') }}
                    </div>
                    <h1>{{ __('messages.login_to_continue') }}</h1>
                    <p>Access merchant workflows, reporting, and operational screens from one place.</p>

                    <div class="merchant-login-features">
                        <div class="merchant-login-feature">
                            <div class="merchant-login-feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="merchant-login-feature-text">
                                <div class="merchant-login-feature-title">Secure sign-in</div>
                                <div class="merchant-login-feature-desc">Protected merchant workspace access</div>
                            </div>
                        </div>
                        <div class="merchant-login-feature">
                            <div class="merchant-login-feature-icon">
                                <i class="bi bi-diagram-3"></i>
                            </div>
                            <div class="merchant-login-feature-text">
                                <div class="merchant-login-feature-title">Branch-aware data</div>
                                <div class="merchant-login-feature-desc">Work with the data tied to your merchant</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="merchant-login-footer">
                    <a href="{{ route('landing') }}">
                        <i class="bi bi-house me-1"></i> Back to Home
                    </a>
                    <a href="{{ route('super-admin.login') }}">
                        Admin Login <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- Form Section -->
            <div class="merchant-login-form-section">
                <a href="{{ app()->getLocale() === 'ar' ? route('locale.switch', 'en') : route('locale.switch', 'ar') }}" class="merchant-language-toggle">
                    {{ app()->getLocale() === 'ar' ? 'EN' : 'العربية' }}
                </a>

                <div class="merchant-form-header">
                    <span class="merchant-form-header-badge">Merchant Portal</span>
                    <h2>Sign in to your workspace</h2>
                    <p>Use your merchant credentials to continue.</p>
                </div>

                @if($errors->any())
                    <div class="merchant-error-alert">
                        @foreach($errors->all() as $error)
                            <div><i class="bi bi-exclamation-triangle me-1"></i> {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if(session('error'))
                    <div class="merchant-error-alert">
                        <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="merchant-form-group">
                        <label class="merchant-form-label">{{ __('messages.email') }}</label>
                        <input type="email" name="email" class="merchant-form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="admin@merchant.com">
                        @error('email')
                            <span class="merchant-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="merchant-form-group">
                        <label class="merchant-form-label">{{ __('messages.password') }}</label>
                        <input type="password" name="password" class="merchant-form-control @error('password') is-invalid @enderror" required placeholder="••••••••">
                        @error('password')
                            <span class="merchant-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="merchant-form-group">
                        <div class="merchant-form-checkbox">
                            <input type="checkbox" name="remember" id="remember">
                            <label for="remember">{{ __('messages.remember_me') }}</label>
                        </div>
                    </div>

                    <button type="submit" class="merchant-form-button">
                        <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('messages.login') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.querySelectorAll('.merchant-form-control').forEach(input => {
        input.addEventListener('focus', function() {
            if(!this.classList.contains('is-invalid')) {
                this.style.borderColor = '#ff8c00';
            }
        });
        input.addEventListener('blur', function() {
            if(!this.classList.contains('is-invalid')) {
                this.style.borderColor = '#d9dce3';
            }
        });
    });
</script>
@endsection
