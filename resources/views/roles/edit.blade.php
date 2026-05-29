@extends('layouts.modern')

@section('title', __('roles.edit_role'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        {{ __('roles.edit_role') }}
                    </h4>
                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-light">
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

                    @if (in_array($role->name, ['Admin', 'System']))
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ __('roles.system_role_warning') }}
                        </div>
                    @endif

                    <form action="{{ route('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('roles.role_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $role->name) }}" required
                                   {{ in_array($role->name, ['Admin', 'System']) ? 'disabled' : '' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('roles.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3"
                                      {{ in_array($role->name, ['Admin', 'System']) ? 'disabled' : '' }}>{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permissions Selection -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('roles.assign_permissions') }}</label>
                            
                            @php
                                use Illuminate\Support\Str;

                                $groupedPermissions = $permissions->groupBy(function($permission) {
                                    $name = $permission->name;
                                    if (Str::contains($name, '.')) return Str::before($name, '.');
                                    if (Str::contains($name, '_')) return Str::before($name, '_');
                                    return $name;
                                });
                            @endphp

                            @foreach ($groupedPermissions as $group => $groupPermissions)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-folder me-2"></i>
                                            {{ ucfirst(str_replace(['_', '.'], ' ', $group)) }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($groupPermissions as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="permissions[]" value="{{ $permission->id }}"
                                                               id="permission_{{ $permission->id }}"
                                                               {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}
                                                               {{ in_array($role->name, ['Admin', 'System']) ? 'disabled' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
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
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <div>
                                    <label class="form-label fw-semibold mb-1">Branch visibility</label>
                                    <div class="text-muted small">Choose which branches this role can access. Leave everything empty to keep full visibility.</div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllRoleBranchesBtn" {{ in_array($role->name, ['Admin', 'System']) ? 'disabled' : '' }}>
                                    <i class="fas fa-check-square me-1"></i>Select all branches
                                </button>
                            </div>

                            <div class="accordion" id="branchAccessAccordionEdit">
                                @foreach ($merchants as $merchant)
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-header bg-white">
                                            <button class="btn btn-link text-decoration-none p-0 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#edit-merchant-branches-{{ $merchant->id }}">
                                                {{ $merchant->name }}
                                            </button>
                                        </div>
                                        <div id="edit-merchant-branches-{{ $merchant->id }}" class="collapse" data-bs-parent="#branchAccessAccordionEdit">
                                            <div class="card-body">
                                                <div class="row">
                                                    @forelse ($merchant->branches as $branch)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="edit_branch_{{ $branch->id }}" {{ in_array($branch->id, $selectedBranchIds ?? []) ? 'checked' : '' }} {{ in_array($role->name, ['Admin', 'System']) ? 'disabled' : '' }}>
                                                                <label class="form-check-label" for="edit_branch_{{ $branch->id }}">
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

                        @if (!in_array($role->name, ['Admin', 'System']))
                            <div class="d-flex gap-2 pt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    {{ __('actions.save_changes') }}
                                </button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                    {{ __('actions.cancel') }}
                                </a>
                            </div>
                        @else
                            <div class="pt-3">
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                    {{ __('actions.back') }}
                                </a>
                            </div>
                        @endif
                    </form>
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
<script>
// Confirmation when saving role with zero permissions & features
const rolesEditForm = document.querySelector('form[action="{{ route('roles.update', $role) }}"]');
if (rolesEditForm) {
    rolesEditForm.addEventListener('submit', function (e) {
        const anyPermission = document.querySelectorAll('input[name="permissions[]"]:checked').length > 0;
        const anyFeature = document.querySelectorAll('input[name="features[]"]:checked').length > 0;
        if (!anyPermission && !anyFeature) {
            const ok = confirm('You are about to save this role without any permissions or features. Users assigned this role will have no access to pages. Do you want to continue?');
            if (!ok) e.preventDefault();
        }
    });
}
</script>
@endsection
