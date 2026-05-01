@extends('layouts.modern')

@section('title', __('permissions.permissions_management'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-key me-2"></i>
                {{ __('permissions.permissions_management') }}
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                {{ __('permissions.add_permission') }}
            </a>
        </div>
    </div>

    <!-- Permissions Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>{{ __('permissions.permission_name') }}</th>
                        <th>{{ __('permissions.category') }}</th>
                        <th>{{ __('permissions.description') }}</th>
                        <th>{{ __('permissions.roles_count') }}</th>
                        <th style="width: 150px;">{{ __('actions.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissions as $permission)
                        <tr>
                            <td>
                                <strong>{{ $permission->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($permission->category ?? 'Other') }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $permission->description ?? '—' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $permission->roles->count() }}</span>
                            </td>
                            <td>
                                <div class="action-buttons btn-group" role="group" aria-label="{{ __('actions.action') }}">
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-primary" title="{{ __('actions.edit') }}">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('actions.edit') }}</span>
                                    </a>

                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" style="display: inline;" onclick="return confirm('{{ __('permissions.confirm_delete') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('actions.delete') }}" {{ $permission->roles()->exists() ? 'disabled' : '' }}>
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('actions.delete') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>{{ __('permissions.no_permissions_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($permissions->hasPages())
            <div class="card-footer bg-light">
                {{ $permissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
