@extends('layouts.modern')

@section('title', __('messages.storage_management'))

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 style="font-weight: 900;">
            <i class="bi bi-archive" style="color: #ff8c00;"></i>
            <span class="storages-title">{{ __('messages.storage_management') }}</span>
        </h1>
        @feature('storages.create')
            <a href="{{ route('storages.create') }}" class="btn btn-primary-modern">
                <i class="bi bi-plus-circle"></i> {{ __('messages.new_storage') }}
            </a>
        @endfeature
    </div>
</div>
<style>
    .storages-title { color: #000 !important; }
</style>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <h6>{{ __('messages.total_storages') }}</h6>
            <div class="value">{{ $stats['total_storages'] ?? 0 }}</div>
            <div class="icon"><i class="bi bi-warehouse"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <h6>{{ __('messages.active_storages') }}</h6>
            <div class="value">{{ $stats['active_storages'] ?? 0 }}</div>
            <div class="icon"><i class="bi bi-check-circle"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6>{{ __('messages.total_items') }}</h6>
            <div class="value">{{ $stats['total_items'] ?? 0 }}</div>
            <div class="icon"><i class="bi bi-box"></i></div>
        </div>
    </div>
</div>

<!-- Storages Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list"></i> {{ __('messages.all_storages') }}
    </div>
    <style>
        @media (max-width: 768px) {
            .storages-mobile-list { display: block; }
            .table { display: none !important; }
        }
    </style>

    <!-- Mobile storages cards -->
    <div class="storages-mobile-list d-md-none">
        @forelse($storages ?? [] as $storage)
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>{{ $storage->name }}</strong>
                            <div class="small text-muted">{{ $storage->location }}</div>
                        </div>
                        <div class="text-end">
                            <div>{{ $storage->items->count() }} {{ __('messages.items') }}</div>
                            <div class="small text-muted">{{ $storage->is_active ? __('messages.active') : __('messages.inactive') }}</div>
                        </div>
                    </div>
                    <div class="mt-2 d-flex gap-2">
                        @feature('storages.view')
                        <a href="{{ route('storages.items', $storage->id) }}" class="btn btn-sm btn-info"><i class="bi bi-box"></i></a>
                        @endfeature
                    </div>
                </div>
            </div>
        @empty
            <div class="card mb-2"><div class="card-body text-center text-muted">{{ __('messages.no_storages_found') }}</div></div>
        @endforelse
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.location') }}</th>
                    <th>{{ __('messages.type') }}</th>
                    <th>{{ __('messages.items') }}</th>
                    <th>{{ __('messages.storage_capacity') }}</th>
                    <th>{{ __('messages.usage') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($storages ?? [] as $storage)
                    <tr>
                        <td><strong>{{ $storage->name }}</strong></td>
                        <td>{{ $storage->location }}</td>
                        <td><span class="badge badge-orange">{{ $storage->storage_type }}</span></td>
                        <td>{{ $storage->items->count() }}</td>
                        <td>{{ $storage->capacity ? $storage->capacity . ' ' . __('messages.units') : __('messages.unlimited') }}</td>
                        <td>
                            @if($storage->capacity)
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" 
                                        style="width: {{ ($storage->current_usage / $storage->capacity) * 100 }}%">
                                        {{ round(($storage->current_usage / $storage->capacity) * 100) }}%
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-info">{{ __('messages.not_available') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $storage->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $storage->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </td>
                        <td>
                            @feature('storages.view')
                                <a href="{{ route('storages.items', $storage->id) }}" class="btn btn-sm btn-info" title="{{ __('messages.view_items') }}">
                                    <i class="bi bi-box"></i>
                                </a>
                            @endfeature

                            @feature('storages.edit')
                                <a href="{{ route('storages.edit', $storage->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endfeature

                            @feature('storages.delete')
                                <button onclick="deleteStorage({{ $storage->id }})" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endfeature
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">{{ __('messages.no_storages_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($storages ?? false)
    <div class="mt-3">
        {{ $storages->links() }}
    </div>
@endif
@endsection

@section('js')
<script>
    function deleteStorage(id) {
        if (confirm('{{ __('messages.delete_storage_confirm') }}')) {
            const url = '{{ url("storages") }}/' + id;
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>
@endsection
