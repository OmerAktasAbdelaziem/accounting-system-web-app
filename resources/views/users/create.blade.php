@extends('layouts.modern')

@section('title', __('users.add_user'))

@section('content')
<style>
    .create-shell {
        min-height: 100vh;
        padding: 32px 0 48px;
        background:
            radial-gradient(circle at top right, rgba(255, 140, 0, 0.14), transparent 25%),
            linear-gradient(180deg, #f7f7f8 0%, #eef1f5 100%);
    }

    .create-hero {
        background: linear-gradient(135deg, #16181d 0%, #23262d 100%);
        color: #fff;
        border-radius: 28px;
        padding: 28px 30px;
        box-shadow: 0 18px 50px rgba(12, 15, 20, 0.18);
    }

    .create-card {
        border: 0;
        border-radius: 28px;
        box-shadow: 0 18px 50px rgba(12, 15, 20, 0.1);
        overflow: hidden;
    }

    .create-field {
        min-height: 52px;
        border-radius: 14px;
        border-color: #d9dde5;
    }

    .create-field:focus {
        border-color: #ff8c00;
        box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1);
    }

    .create-aside {
        border: 0;
        border-radius: 28px;
        background: linear-gradient(160deg, #ff8c00 0%, #ffb347 100%);
        color: #fff;
        box-shadow: 0 18px 50px rgba(255, 140, 0, 0.2);
    }
</style>

<div class="create-shell">
    <div class="container-fluid">
        <div class="create-hero mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">Administration</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ __('users.add_user') }}</h1>
                <p class="mb-0 text-white-50">Create a new team member with a clean, focused form.</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill px-3">
                <i class="fas fa-arrow-left me-2"></i>{{ __('actions.back') }}
            </a>
        </div>

        <div class="row g-4">
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

                        <form action="{{ route('users.store') }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">{{ __('users.name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control create-field @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">{{ __('users.email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control create-field @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">{{ __('users.password') }} <span class="text-danger">*</span></label>
                                <input type="password" class="form-control create-field @error('password') is-invalid @enderror" id="password" name="password" required>
                                <small class="text-muted d-block mt-1">{{ __('users.min_8_characters') }}</small>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">{{ __('users.confirm_password') }} <span class="text-danger">*</span></label>
                                <input type="password" class="form-control create-field" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="col-md-6">
                                <label for="role_id" class="form-label fw-semibold">{{ __('users.role') }} <span class="text-danger">*</span></label>
                                <select class="form-select create-field @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                                    <option value="">{{ __('users.select_role') }}</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">{{ __('users.phone') }}</label>
                                <input type="tel" class="form-control create-field @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input ms-0" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_active">{{ __('users.active_user') }}</label>
                                </div>
                            </div>

                            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>{{ __('actions.save') }}
                                </button>
                                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4">{{ __('actions.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card create-aside h-100">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px;background:rgba(255,255,255,.16);">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Team access</h5>
                                <small class="text-white-50">Keep the form tight and readable.</small>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex gap-3">
                                <i class="fas fa-check-circle mt-1"></i>
                                <div>Use a clear email address and assign the role that matches the user's workflow.</div>
                            </div>
                            <div class="d-flex gap-3">
                                <i class="fas fa-shield-alt mt-1"></i>
                                <div>Passwords should meet the project's existing validation rules.</div>
                            </div>
                            <div class="d-flex gap-3">
                                <i class="fas fa-toggle-on mt-1"></i>
                                <div>Inactive users can be staged here without losing their profile details.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
