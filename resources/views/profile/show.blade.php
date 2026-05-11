@extends('layouts.modern')

@section('title', __('messages.profile_settings'))

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.profile_settings') }}</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="name" class="form-label">{{ __('messages.name') }}</label>
                        <input 
                            type="text" 
                            class="form-control @error('name') is-invalid @enderror" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->name ?? '') }}"
                            required
                        >
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="email" class="form-label">{{ __('messages.email') }}</label>
                        <input 
                            type="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $user->email ?? '') }}"
                            required
                        >
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ __('messages.save') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">{{ __('messages.change_password') }}</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('change-password') }}">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="current_password" class="form-label">{{ __('messages.current_password') }}</label>
                        <input 
                            type="password" 
                            class="form-control @error('current_password') is-invalid @enderror" 
                            id="current_password" 
                            name="current_password"
                            required
                        >
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password" class="form-label">{{ __('messages.new_password') }}</label>
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            id="password" 
                            name="password"
                            required
                        >
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('messages.confirm_password') }}</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password_confirmation" 
                            name="password_confirmation"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-key"></i> {{ __('messages.update_password') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
