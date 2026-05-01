@extends('layouts.modern')

@section('title', __('users.edit_user'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        {{ __('users.edit_user') }}
                    </h4>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        {{ __('actions.back') }}
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">{{ __('validation.failed') }}</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('users.name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('users.email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role_id" class="form-label">{{ __('users.role') }} <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role_id') is-invalid @enderror" 
                                            id="role_id" name="role_id" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('users.phone') }}</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    {{ __('users.active_user') }}
                                </label>
                            </div>
                        </div>

                        <!-- Password Reset Section -->
                        <hr class="my-4">
                        <h5 class="mb-3">{{ __('users.change_password') }}</h5>

                        <form id="passwordForm" action="{{ route('users.resetPassword', $user) }}" method="POST" style="display: none;">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">{{ __('users.new_password') }} <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="new_password" 
                                               name="password" required>
                                        <small class="text-muted d-block mt-1">{{ __('users.min_8_characters') }}</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">{{ __('users.confirm_password') }} <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" id="new_password_confirmation" 
                                               name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <button type="button" class="btn btn-warning btn-sm mb-3" id="resetPasswordBtn">
                            <i class="fas fa-key me-2"></i>
                            {{ __('users.reset_password') }}
                        </button>

                        <div class="d-flex gap-2 pt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                {{ __('actions.save_changes') }}
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                {{ __('actions.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('resetPasswordBtn').addEventListener('click', function() {
        const form = document.getElementById('passwordForm');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            this.classList.add('active');
        } else {
            form.style.display = 'none';
            this.classList.remove('active');
        }
    });
</script>
@endsection
