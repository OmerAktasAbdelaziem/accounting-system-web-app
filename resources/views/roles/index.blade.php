@extends('layouts.modern')

@section('title', __('roles.roles_management'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-shield-alt me-2"></i>
                {{ __('roles.roles_management') }}
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                {{ __('roles.add_role') }}
            </a>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>{{ __('roles.role_name') }}</th>
                        <th>{{ __('roles.description') }}</th>
                        <th>{{ __('roles.permissions') }}</th>
                        <th>{{ __('Branches') }}</th>
                        <th>{{ __('roles.users_count') }}</th>
                        <th style="width: 150px;">{{ __('actions.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>
                                <strong>{{ $role->name }}</strong>
                                @if (in_array($role->name, ['Admin', 'System']))
                                    <span class="badge bg-danger ms-2">{{ __('roles.system_role') }}</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $role->description ?? '—' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $role->permissions->count() }} {{ __('roles.permissions_assigned') }}
                                </small>
                            </td>
                            <td>
                                @php $branchCount = $role->branchAccesses->count(); @endphp
                                <small class="text-muted">
                                    {{ $branchCount > 0 ? $branchCount . ' branches allowed' : 'All branches' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $role->users_count ?? $role->users->count() }}</span>
                            </td>
                            <td>
                                <div class="action-buttons btn-group" role="group" aria-label="{{ __('actions.action') }}">
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-primary" title="{{ __('actions.edit') }}">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('actions.edit') }}</span>
                                    </a>

                                    @if (!in_array($role->name, ['Admin', 'System']))
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display: inline;" onclick="return confirm('{{ __('roles.confirm_delete') }}');">
                                            {{ __('@csrf
                                            @method(\'DELETE\')') }}
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('actions.delete') }}" {{ $role->{{ __('users()->exists() ? \'disabled\' : \'\' }}>') }}
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('actions.delete') }}</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    {{ __('@empty') }}
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>{{ __('roles.no_roles_found') }}</p>
                            </td>
                        </tr>
                    {{ __('@endforelse') }}
                </tbody>
            </table>
        </div>

        @if ($roles->hasPages())
            <div class="card-footer bg-light">
                {{ $roles->links() }}
            </div>
        @endif
    </div>

    <!-- Permissions Summary Card -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('roles.system_information') }}
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted">
                {{ __('roles.total_roles') }}: <strong>{{ $roles->total() }}</strong><br>
                {{ __('roles.manage_roles_and_permissions_message') }}
            </p>
        </div>
    </div>
</div>
@endsection
