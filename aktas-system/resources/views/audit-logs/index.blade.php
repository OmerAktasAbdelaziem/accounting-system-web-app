@extends('layouts.modern')

@section('title', __('audit_logs.title'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">
                <i class="fas fa-history me-2"></i>
                {{ __('audit_logs.audit_logs') }}
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('audit-logs.export', request()->query()) }}" class="btn btn-outline-primary" title="{{ __('audit_logs.export') }}">
                <i class="fas fa-download me-2"></i>
                {{ __('audit_logs.export') }}
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>
                {{ __('audit_logs.filter_by_date') }}
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="date_from" class="form-label">{{ __('audit_logs.from_date') }}</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label">{{ __('audit_logs.to_date') }}</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}">
                </div>

                <div class="col-md-2">
                    <label for="user_id" class="form-label">{{ __('audit_logs.user') }}</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">{{ __('audit_logs.filter_by_user') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="action" class="form-label">{{ __('audit_logs.action') }}</label>
                    <select class="form-select" id="action" name="action">
                        <option value="">{{ __('audit_logs.filter_by_action') }}</option>
                        @foreach ($actions as $actionItem)
                            <option value="{{ $actionItem }}" {{ request('action') == $actionItem ? 'selected' : '' }}>
                                {{ ucfirst($actionItem) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="model_type" class="form-label">{{ __('audit_logs.model_type') }}</label>
                    <select class="form-select" id="model_type" name="model_type">
                        <option value="">{{ __('audit_logs.filter_by_model') }}</option>
                        @foreach ($modelTypes as $modelType)
                            <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                {{ $modelType }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50">
                            <i class="fas fa-search me-2"></i>
                            {{ __('audit_logs.search') }}
                        </button>
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary w-50">
                            <i class="fas fa-redo me-2"></i>
                            {{ __('audit_logs.clear_filters') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>{{ __('audit_logs.date') }}</th>
                        <th>{{ __('audit_logs.time') }}</th>
                        <th>{{ __('audit_logs.user') }}</th>
                        <th>{{ __('audit_logs.action') }}</th>
                        <th>{{ __('audit_logs.model_type') }}</th>
                        <th>{{ __('audit_logs.model_id') }}</th>
                        <th>{{ __('audit_logs.ip_address') }}</th>
                        <th style="width: 100px;">{{ __('actions.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>
                                <small class="text-muted">{{ $log->created_at->format('Y-m-d') }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $log->user?->name ?? 'System' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $actionColor = match($log->action) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'secondary',
                                    };
                                    $actionIcon = match($log->action) {
                                        'created' => 'fa-plus',
                                        'updated' => 'fa-edit',
                                        'deleted' => 'fa-trash',
                                        default => 'fa-circle',
                                    };
                                @endphp
                                <span class="badge bg-{{ $actionColor }}">
                                    <i class="fas {{ $actionIcon }} me-1"></i>
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $log->model_type }}</small>
                            </td>
                            <td>
                                <small class="text-muted">#{{ $log->model_id }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $log->ip_address }}</small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('audit-logs.show', $log) }}" class="btn btn-sm btn-primary" title="{{ __('actions.view') }}">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                        <span class="visually-hidden">{{ __('actions.view') }}</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>{{ __('audit_logs.no_logs_found') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="card-footer bg-light">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
