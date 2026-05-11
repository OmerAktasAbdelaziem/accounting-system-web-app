@extends('layouts.modern')

@section('title', __('roles.add_role'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        {{ __('roles.add_role') }}
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

                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('roles.role_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('roles.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Permissions Selection -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('roles.assign_permissions') }}</label>
                            
                            @php
                                $groupedPermissions = $permissions->groupBy('category');
                            @endphp

                            @foreach ($groupedPermissions as $category => $categoryPermissions)
                                <div class="card mb-3">
                                        <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-folder me-2"></i>
                                            @php
                                                $catKey = 'permissions.categories.' . \Illuminate\Support\Str::slug($category ?? 'Other', '_');
                                            @endphp
                                            @if(\Illuminate\Support\Facades\Lang::has($catKey))
                                                {{ __($catKey) }}
                                            @else
                                                {{ ucfirst($category ?? 'Other') }}
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($categoryPermissions as $permission)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               name="permissions[]" value="{{ $permission->id }}"
                                                               id="permission_{{ $permission->id }}"
                                                               {{ old('permissions') && in_array($permission->id, old('permissions')) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            @if (\Illuminate\Support\Facades\Lang::has('permissions.' . $permission->name))
                                                                {{ __('permissions.' . $permission->name) }}
                                                            @else
                                                                {{ $permission->name }}
                                                            @endif
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

                        <div class="d-flex gap-2 pt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                {{ __('actions.save') }}
                            </button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                {{ __('actions.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
