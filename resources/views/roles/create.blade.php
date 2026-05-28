@extends('layouts.modern')

@section('title', __('roles.add_role'))

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
                <div class="text-uppercase text-white-50 small fw-semibold mb-2">Access Control</div>
                <h1 class="mb-2 fw-bold" style="letter-spacing: -0.03em;">{{ __('roles.add_role') }}</h1>
                <p class="mb-0 text-white-50">Define a role and attach permissions in a clearer flow.</p>
            </div>
            <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill px-3"><i class="fas fa-arrow-left me-2"></i>{{ __('actions.back') }}</a>
        </div>

        <div class="row justify-content-center g-4">
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

                        <form action="{{ route('roles.store') }}" method="POST" class="row g-3">
                            @csrf
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold">{{ __('roles.role_name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control create-field @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">{{ __('roles.description') }}</label>
                                <textarea class="form-control create-field @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">{{ __('roles.assign_permissions') }}</label>
                                @php
                                    use Illuminate\Support\Str;

                                    $groupedPermissions = $permissions->groupBy(function($permission) {
                                        $name = $permission->name;
                                        if (Str::contains($name, '.')) return Str::before($name, '.');
                                        if (Str::contains($name, '_')) return Str::before($name, '_');
                                        return $name;
                                    });
                                @endphp

                                <div class="accordion" id="permissionsAccordion">
                                    @foreach ($groupedPermissions as $group => $groupPermissions)
                                        <div class="accordion-item border-0 shadow-sm mb-3 rounded-4 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#perm-{{ \Illuminate\Support\Str::slug($group ?? 'Other', '-') }}">
                                                    {{ ucfirst(str_replace(['_', '.'], ' ', $group)) }}
                                                </button>
                                            </h2>
                                            <div id="perm-{{ \Illuminate\Support\Str::slug($group ?? 'Other', '-') }}" class="accordion-collapse collapse" data-bs-parent="#permissionsAccordion">
                                                <div class="accordion-body bg-white">
                                                    <div class="row g-2">
                                                        @foreach ($groupPermissions as $permission)
                                                            <div class="col-md-6">
                                                                <div class="form-check p-3 border rounded-4 h-100">
                                                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission_{{ $permission->id }}" {{ old('permissions') && in_array($permission->id, old('permissions')) ? 'checked' : '' }}>
                                                                    <label class="form-check-label ms-2" for="permission_{{ $permission->id }}">
                                                                        @php
                                                                            $label = $permission->name;
                                                                            if (Str::contains($label, '.')) $label = Str::after($label, '.');
                                                                            if (Str::contains($label, '_')) $label = Str::after($label, '_');
                                                                            $label = ucfirst(str_replace(['_', '.'], ' ', $label));
                                                                        @endphp
                                                                        {{ $label }}
                                                                        @if ($permission->description && app()->getLocale() === 'en')
                                                                            <small class="d-block text-muted">{{ $permission->description }}</small>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Feature Toggles</label>
                                <div class="small text-muted mb-2">Enable system features for this role (applies to all merchants).</div>
                                @php
                                    $features = $availableFeatures ?? [];
                                @endphp
                                <div class="row g-2">
                                    @foreach($features as $featureKey => $featureLabel)
                                        <div class="col-md-4">
                                            <div class="form-check p-3 border rounded-4 h-100">
                                                <input class="form-check-input" type="checkbox" name="features[]" value="{{ $featureKey }}" id="feature_{{ $featureKey }}">
                                                <label class="form-check-label ms-2" for="feature_{{ $featureKey }}">
                                                    <strong>{{ $featureLabel }}</strong>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                    <div>
                                        <label class="form-label fw-semibold mb-1">Branch visibility</label>
                                        <div class="text-muted small">Select the branches this role can see inside the merchant. If nothing is selected, the role keeps full branch visibility.</div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllRoleBranchesBtn">
                                        <i class="bi bi-check2-square me-1"></i>Select all branches
                                    </button>
                                </div>

                                <div class="accordion" id="branchAccessAccordion">
                                    @foreach ($merchants as $merchant)
                                        <div class="accordion-item border-0 shadow-sm mb-3 rounded-4 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#merchant-branches-{{ $merchant->id }}">
                                                    {{ $merchant->name }}
                                                </button>
                                            </h2>
                                            <div id="merchant-branches-{{ $merchant->id }}" class="accordion-collapse collapse" data-bs-parent="#branchAccessAccordion">
                                                <div class="accordion-body bg-white">
                                                    <div class="row g-2">
                                                        @forelse ($merchant->branches as $branch)
                                                            <div class="col-md-6">
                                                                <div class="form-check p-3 border rounded-4 h-100">
                                                                    <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch_{{ $branch->id }}" {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label ms-2" for="branch_{{ $branch->id }}">
                                                                        <strong>{{ $branch->name }}</strong>
                                                                        <small class="d-block text-muted">{{ $branch->city ?? $branch->address ?? 'Branch' }}</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-12 text-muted">No branches found for this merchant.</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>{{ __('actions.save') }}</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary px-4">{{ __('actions.cancel') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('selectAllRoleBranchesBtn').addEventListener('click', function () {
    document.querySelectorAll('input[name="branch_ids[]"]:not(:disabled)').forEach(function (checkbox) {
        checkbox.checked = true;
    });
});
</script>
@endsection
