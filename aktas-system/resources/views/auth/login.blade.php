@extends('layouts.modern')

@section('title', __('messages.login'))

@section('content')
<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; background: linear-gradient(135deg, #0d0d0d, #1a1a1a);">
    <div class="card" style="max-width: 450px; width: 100%; border: 2px solid #ff8c00; box-shadow: 0 20px 60px rgba(255, 140, 0, 0.2);">
        <div class="card-header text-center" style="background: linear-gradient(135deg, #1a1a1a, #2a2a2a); border-bottom: 2px solid #ff8c00; padding: 40px 30px;">
            <h2 style="color: #ff8c00; margin: 0; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 24px;">
                <i class="bi bi-briefcase-fill"></i> {{ __('messages.accounting_system') }}
            </h2>
            <p style="color: #999; margin-top: 10px; margin-bottom: 0; font-size: 14px;">{{ __('messages.login_to_continue') }}</p>
        </div>

        <div class="card-body" style="padding: 40px 35px;">
            <!-- Language Toggle -->
            <div style="position: absolute; top: 20px; right: 20px;">
                @if(app()->getLocale() === 'ar')
                    <a href="{{ route('locale.switch', 'en') }}" class="btn btn-sm" style="background: #ff8c00; color: white; border-radius: 8px; border: none; text-decoration: none;">EN</a>
                @else
                    <a href="{{ route('locale.switch', 'ar') }}" class="btn btn-sm" style="background: #ff8c00; color: white; border-radius: 8px; border: none; text-decoration: none;">العربية</a>
                @endif
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="alert alert-danger" style="background: #ffe8cc; color: #d32f2f; border-left: 4px solid #d32f2f; border-radius: 8px; margin-bottom: 20px;">
                    @foreach($errors->all() as $error)
                        <div><i class="bi bi-exclamation-triangle"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="background: #ffe8cc; color: #d32f2f; border-left: 4px solid #d32f2f; border-radius: 8px; margin-bottom: 20px;">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label" style="color: #1a1a1a; font-weight: 600; margin-bottom: 10px;">{{ __('messages.email') }}</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        required
                        style="border: 2px solid #e0e0e0; border-radius: 8px; padding: 12px 15px; height: 48px; font-size: 14px;"
                        placeholder="admin@hamid.com"
                    >
                    @error('email')
                        <div style="display: block; color: #d32f2f; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label class="form-label" style="color: #1a1a1a; font-weight: 600; margin-bottom: 10px;">{{ __('messages.password') }}</label>
                    <input 
                        type="password" 
                        name="password" 
                        class="form-control @error('password') is-invalid @enderror"
                        required
                        style="border: 2px solid #e0e0e0; border-radius: 8px; padding: 12px 15px; height: 48px; font-size: 14px;"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <div style="display: block; color: #d32f2f; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-check mb-3">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="remember" 
                        id="remember"
                        style="width: 18px; height: 18px; cursor: pointer; accent-color: #ff8c00; border: 2px solid #ddd;"
                    >
                    <label class="form-check-label" for="remember" style="cursor: pointer; color: #666; margin-left: 8px; margin-top: 2px;">
                        {{ __('messages.remember_me') }}
                    </label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="btn w-100"
                    style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white; height: 48px; font-size: 15px; font-weight: 600; margin-top: 10px; border: none; border-radius: 8px; cursor: pointer; transition: all 0.3s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(255, 140, 0, 0.3)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                >
                    <i class="bi bi-box-arrow-in-right"></i> {{ __('messages.login') }}
                </button>
            </form>

            <!-- Demo Credentials Info -->
            <div style="background: #f5f5f5; border-left: 4px solid #27ae60; padding: 15px; border-radius: 8px; margin-top: 25px; font-size: 13px;">
                <p style="margin: 0 0 8px 0; color: #1a1a1a; font-weight: 600;">
                    <i class="bi bi-info-circle"></i> {{ __('messages.demo_credentials') }}
                </p>
                <p style="margin: 0 0 3px 0; color: #666;">
                    <strong>Email:</strong> admin@hamid.com
                </p>
                <p style="margin: 0; color: #666;">
                    <strong>Password:</strong> admin123456
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Add focus animation to inputs
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#ff8c00';
            this.style.boxShadow = '0 0 0 3px rgba(255, 140, 0, 0.1)';
        });
        input.addEventListener('blur', function() {
            if(!this.classList.contains('is-invalid')) {
                this.style.borderColor = '#e0e0e0';
                this.style.boxShadow = 'none';
            }
        });
    });
</script>
@endsection
