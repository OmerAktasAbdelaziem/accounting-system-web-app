@extends('layouts.modern')

@section('title', __('messages.profile_settings'))

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.profile_settings') }}</h5>
            </div>
            <div class="card-body text-center">
                @php
                    $avatarPath = $user->profile_photo_path;
                    $avatarUrl = null;
                    if ($avatarPath) {
                        if (\Illuminate\Support\Facades\File::exists(public_path($avatarPath))) {
                            $avatarUrl = asset($avatarPath);
                        } elseif (\Illuminate\Support\Facades\File::exists(public_path('storage/' . ltrim($avatarPath, '/')))) {
                            $avatarUrl = asset('storage/' . ltrim($avatarPath, '/'));
                        } else {
                            $avatarUrl = asset($avatarPath);
                        }
                    }
                    $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
                @endphp
                <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center overflow-hidden border border-4 border-white shadow" style="width:140px;height:140px;background:linear-gradient(135deg,#ff8c00,#27ae60);color:#fff;font-size:42px;font-weight:800;">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover">
                    @else
                        {{ $initial }}
                    @endif
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <div class="text-muted mb-3">{{ $user->email }}</div>
                <div class="small text-muted">{{ $user->role?->name ?? __('No Role') }}</div>
                <div class="small text-muted">{{ $user->merchant?->business_name ?? __('Platform Account') }}</div>
                @if($avatarUrl)
                    <div class="mt-3">
                        <span class="badge bg-success">{{ __('Profile photo uploaded') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">{{ __('messages.profile_settings') }}</h5>
                <span class="badge bg-light text-dark">{{ $user->isSuperAdmin() ? __('Super Admin') : ($user->merchant?->business_name ?? __('User')) }}</span>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">{{ __('messages.name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">{{ __('messages.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">{{ __('messages.phone') }}</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">{{ __('messages.address') }}</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address ?? '') }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="profile_photo" class="form-label">{{ __('Profile Picture') }}</label>
                            <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*">
                            @error('profile_photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">{{ __('Upload a JPG, PNG, or WEBP image to replace the avatar shown in the navbar.') }}</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-modern btn-sm mt-4">
                        <i class="bi bi-save"></i> {{ __('messages.save') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Account Details') }}</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>{{ __('Role:') }}</strong> {{ $user->role?->name ?? __('N/A') }}</div>
                    <div class="col-md-6"><strong>{{ __('Account Type:') }}</strong> {{ $user->user_type ?? __('N/A') }}</div>
                    <div class="col-md-6"><strong>{{ __('Merchant:') }}</strong> {{ $user->merchant?->business_name ?? __('Platform') }}</div>
                    <div class="col-md-6"><strong>{{ __('Last Login:') }}</strong> {{ $user->last_login?->translatedFormat('M d, Y H:i') ?? __('Never') }}</div>
                    <div class="col-md-6"><strong>{{ __('Created:') }}</strong> {{ $user->created_at?->translatedFormat('M d, Y H:i') ?? __('N/A') }}</div>
                    <div class="col-md-6"><strong>{{ __('Updated:') }}</strong> {{ $user->updated_at?->translatedFormat('M d, Y H:i') ?? __('N/A') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.change_password') }}</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('change-password') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label">{{ __('messages.current_password') }}</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="password" class="form-label">{{ __('messages.new_password') }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="password_confirmation" class="form-label">{{ __('messages.confirm_password') }}</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-modern btn-sm mt-4">
                        <i class="bi bi-key"></i> {{ __('messages.update_password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
