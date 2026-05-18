@extends('layouts.modern')

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
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">Operations</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ __('messages.add_branch') }}</h1>
                <p class="mb-0 text-white-50">Create a branch with the details that matter first.</p>
            </div>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-xl-8">
                <div class="card create-card">
                    <div class="card-body p-4 p-lg-5">
                        <form action="{{ route('branches.store') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('messages.branch_name') }}</label>
                                <input type="text" name="name" class="form-control create-field" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('messages.branch_code') }}</label>
                                <input type="text" name="code" class="form-control create-field" value="{{ old('code') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('messages.address') }}</label>
                                <textarea name="address" class="form-control create-field" rows="3">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">{{ __('messages.city') }}</label>
                                <input type="text" name="city" class="form-control create-field" value="{{ old('city') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">{{ __('messages.phone') }}</label>
                                <input type="text" name="phone" class="form-control create-field" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">{{ __('messages.manager_name') }}</label>
                                <input type="text" name="manager_name" class="form-control create-field" value="{{ old('manager_name') }}">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                                    <input class="form-check-input ms-0" type="checkbox" name="is_active" value="1" checked>
                                    <label class="form-check-label fw-semibold">{{ __('messages.active') }}</label>
                                </div>
                            </div>
                            <div class="col-12 d-flex gap-2 pt-2">
                                <button class="btn btn-primary px-4">{{ __('messages.save') }}</button>
                                <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary px-4">{{ __('messages.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
