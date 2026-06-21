@extends('layouts.modern')

@section('title', __('audit_logs.details'))

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        {{ __('audit_logs.details') }}
                    </h4>
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        {{ __('actions.back') }}
                    </a>
                </div>

                <div class="card-body">
                    <!-- Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('audit_logs.user') }}</label>
                                <div class="fs-6 fw-semibold">
                                    <span class="badge bg-info text-dark">
                                        {{ $auditLog->user?->name ?? 'System' }} 
                                        <small class="text-muted">({{ $auditLog->user?->email }})</small>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('audit_logs.action') }}</label>
                                <div class="fs-6 fw-semibold">
                                    @php
                                        $actionColor = match($auditLog->action) {
                                            'created' => 'success',
                                            'updated' => 'warning',
                                            'deleted' => 'danger',
                                            default => 'secondary',
                                        };
                                        $actionIcon = match($auditLog->action) {
                                            'created' => 'fa-plus',
                                            'updated' => 'fa-edit',
                                            'deleted' => 'fa-trash',
                                            default => 'fa-circle',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $actionColor }} fs-6">
                                        <i class="fas {{ $actionIcon }} me-2"></i>
                                        {{ ucfirst($auditLog->action) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('audit_logs.model_type') }}</label>
                                <div class="fs-6">{{ $auditLog->model_type }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('audit_logs.model_id') }}</label>
                                <div class="fs-6">#{{ $auditLog->model_id }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('audit_logs.date') }} & {{ __('audit_logs.time') }}</label>
                                <div class="fs-6">
                                    {{ $auditLog->created_at->format('Y-m-d H:i:s') }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">{{ __('audit_logs.ip_address') }}</label>
                                <div class="fs-6">
                                    <code>{{ $auditLog->ip_address }}</code>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Changes Section -->
                    @if ($auditLog->changes)
                        <div class="mt-4">
                            <h5 class="border-bottom pb-2">
                                <i class="fas fa-list-ul me-2"></i>
                                {{ __('audit_logs.changes') }}
                            </h5>

                            @php
                                $changes = is_string($auditLog->changes) ? json_decode($auditLog->changes, true) : $auditLog->changes;
                            @endphp

                            @if (isset($changes['new']))
                                <div class="alert alert-info mt-3">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        @if ($auditLog->action === 'created')
                                            {{ __('audit_logs.new_value') }}
                                        @else
                                            {{ __('audit_logs.old_value') }}
                                        @endif
                                    </h6>
                                    <dl class="row mb-0">
                                        @foreach ($changes['new'] as $key => $value)
                                            @if (!in_array($key, ['_token', '_method']))
                                                <dt class="col-sm-4">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                                <dd class="col-sm-8">
                                                    <code>{{ is_array($value) ? json_encode($value) : $value }}</code>
                                                </dd>
                                            @endif
                                        @endforeach
                                    </dl>
                                </div>
                            @endif

                            @if (isset($changes['old']) && $auditLog->action === 'updated')
                                <div class="alert alert-warning mt-3">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-edit me-2"></i>
                                        {{ __('audit_logs.old_value') }}
                                    </h6>
                                    <dl class="row mb-0">
                                        @foreach ($changes['old'] as $key => $value)
                                            @if (!in_array($key, ['_token', '_method']))
                                                <dt class="col-sm-4">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                                <dd class="col-sm-8">
                                                    <code>{{ is_array($value) ? json_encode($value) : $value }}</code>
                                                </dd>
                                            @endif
                                        @endforeach
                                    </dl>
                                </div>
                            @endif
                        </div>
                    {{ __('@else') }}
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('audit_logs.no_changes') }}
                        </div>
                    @endif

                    <!-- User Agent -->
                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('audit_logs.user_agent') }}: {{ $auditLog->user_agent }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
