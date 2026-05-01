@extends('layouts.modern')

@section('title', __('errors.500'))

@section('content')
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="row w-100">
        <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 text-center">
            <div class="card border-0 shadow-lg p-5">
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle fa-5x text-danger opacity-50"></i>
                </div>
                
                <h1 class="display-1 fw-bold mb-0">
                    <span class="text-danger">5</span>
                    <span class="text-warning">0</span>
                    <span class="text-danger">0</span>
                </h1>
                
                <h2 class="mt-3 mb-2">{{ __('errors.server_error') }}</h2>
                
                <p class="text-muted mb-4">
                    {{ __('errors.an_unexpected_error_occurred') }}
                </p>
                
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-home me-2"></i>
                        {{ __('errors.go_home') }}
                    </a>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fas fa-arrow-left me-2"></i>
                        {{ __('errors.go_back') }}
                    </a>
                </div>
                
                <hr class="my-4">
                
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ __('errors.error_code') }}: 500 | {{ __('errors.please_try_again_later') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
