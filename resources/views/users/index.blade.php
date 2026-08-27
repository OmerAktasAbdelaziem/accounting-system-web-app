@extends('layouts.modern')

@section('title', __('users.users_management'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-users me-2"></i>
                {{ __('users.users_management') }}
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                {{ __('users.add_user') }}
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="{{ __('users.search_users') }}"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select class="form-select" name="role_id">
                        <option value="">{{ __('users.all_roles') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-select" name="is_active">
                        <option value="">{{ __('users.all_status') }}</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>
                            {{ __('messages.active') }}
                        </option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>
                            {{ __('messages.inactive') }}
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>
                        {{ __('actions.search') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>{{ __('users.name') }}</th>
                        <th>{{ __('users.email') }}</th>
                        <th>{{ __('users.role') }}</th>
                        <th>{{ __('users.phone') }}</th>
                        <th>{{ __('users.status') }}</th>
                        <th>{{ __('users.last_login') }}</th>
                        <th style="width: 180px;">{{ __('actions.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>
                                <small>{{ $user->email }}</small>
                            </td>
                            <td>
                                @if ($user->role)
                                    <span class="badge bg-info">{{ $user->role->name }}</span>
                                {{ __('@else') }}
                                    <span class="badge bg-secondary">{{ __('users.no_role') }}</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $user->phone ?? '—' }}</small>
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ __('messages.active') }}
                                    </span>
                                {{ __('@else') }}
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>
                                        {{ __('messages.inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $user->last_login ? $user->last_login->diffForHumans() : '—' }}
                                </small>
                            </td>
                            <td>
                                <div class="action-buttons btn-group" role="group" aria-label="{{ __('actions.action') }}">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary" title="{{ __('actions.edit') }}">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('actions.edit') }}</span>
                                    </a>

                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('users.toggleStatus', $user) }}" method="POST" style="display: inline;">
                                            {{ __('@csrf') }}
                                            <button type="submit" class="btn btn-sm btn-warning" title="{{ $user->is_active ? __('users.deactivate') : __('users.activate') }}">
                                                <i class="fas {{ $user->{{ __('is_active ? \'fa-pause-circle\' : \'fa-play-circle\' }}" aria-hidden="true">') }}</i>
                                                <span class="visually-hidden">{{ $user->is_active ? __('users.deactivate') : __('users.activate') }}</span>
                                            </button>
                                        </form>

                                        <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;" onclick="return confirm('{{ __('users.confirm_delete') }}');">
                                            {{ __('@csrf
                                            @method(\'DELETE\')') }}
                                            <button type="submit" class="btn btn-sm btn-danger" title="{{ __('actions.delete') }}">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                                <span class="visually-hidden">{{ __('actions.delete') }}</span>
                                            </button>
                                        </form>
                                    {{ __('@else') }}
                                        <button class="btn btn-sm btn-secondary" disabled title="{{ __('users.current_user') }}">
                                            <i class="fas fa-lock" aria-hidden="true"></i>
                                            <span class="visually-hidden">{{ __('users.current_user') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    {{ __('@empty') }}
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>{{ __('users.no_users_found') }}</p>
                            </td>
                        </tr>
                    {{ __('@endforelse') }}
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="card-footer bg-light">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
