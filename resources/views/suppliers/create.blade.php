@extends('layouts.modern')

@section('content')
<style>
    .create-shell { min-height: 100vh; padding: 32px 0 48px; background: linear-gradient(180deg, #f7f7f8 0%, #eef1f5 100%); }
    .create-hero { background: linear-gradient(135deg, #16181d 0%, #23262d 100%); color: #fff; border-radius: 28px; padding: 28px 30px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.18); }
    .create-card { border: 0; border-radius: 28px; box-shadow: 0 18px 50px rgba(12, 15, 20, 0.1); overflow: hidden; }
    .create-field { min-height: 52px; border-radius: 14px; border-color: #d9dde5; }
    .create-field:focus { border-color: #ff8c00; box-shadow: 0 0 0 4px rgba(255, 140, 0, 0.1); }

    @media (max-width: 768px) {
        .create-shell { padding: 16px 0 28px; }
        .create-hero { padding: 20px; border-radius: 22px; }
        .create-card { border-radius: 22px; }
        .create-card .card-body { padding: 18px !important; }
        .create-field { min-height: 48px; border-radius: 12px; }
        .col-12.d-flex.gap-2.pt-2 { flex-direction: column; }
        .col-12.d-flex.gap-2.pt-2 .btn, .col-12.d-flex.gap-2.pt-2 a { width: 100%; }
    }

    @media (max-width: 576px) {
        .create-hero h1 { font-size: 24px; }
        .create-hero p { font-size: 13px; }
    }
</style>

<div class="create-shell">
    <div class="container-fluid">
        <div class="create-hero mb-4 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">Procurement</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ __('Create Supplier') }}</h1>
                <p class="mb-0 text-white-50">Capture supplier details in a cleaner, guided layout.</p>
            </div>
        </div>

        <div class="row justify-content-center g-4">
            <div class="col-xl-8">
                <div class="card create-card">
                    <div class="card-body p-4 p-lg-5">
                        <form action="{{ route('suppliers.store') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('Name') }}</label>
                                <input type="text" name="name" class="form-control create-field" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('Address') }}</label>
                                <textarea name="address" class="form-control create-field" rows="3">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Opening Balance') }}</label>
                                <input type="number" step="0.01" name="opening_balance" class="form-control create-field" value="{{ old('opening_balance', 0) }}">
                            </div>

                            <div class="col-12">
                                @include('branches.partials.multi-select', ['branches' => $branches ?? [], 'selectedBranchIds' => $selectedBranchIds ?? []])
                            </div>

                            <div class="col-12 d-flex gap-2 pt-2">
                                <button class="btn btn-primary px-4">{{ __('Save') }}</button>
                                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary px-4">{{ __('Cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
