@extends('layouts.modern')

@section('title', __('messages.safe_management'))

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-safe" style="color: #ff8c00;"></i> {{ __('messages.safe_management') }}
        </h1>
        <a href="{{ route('safes.create') }}" class="btn btn-primary-modern">
            <i class="bi bi-plus-circle"></i> {{ __('messages.new_safe') }}
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <h6>{{ __('messages.total_safes') }}</h6>
            <div class="value">{{ $stats['total_safes'] ?? 0 }}</div>
            <div class="icon"><i class="bi bi-safe"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <h6>{{ __('messages.active') }}</h6>
            <div class="value">{{ $stats['active_safes'] ?? 0 }}</div>
            <div class="icon"><i class="bi bi-check-circle"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6>{{ __('messages.total_balance') }}</h6>
            <div class="value">{{ $currencySymbol }}{{ number_format($stats['total_balance'] ?? 0, 2) }}</div>
            <div class="icon"><i class="bi bi-cash-coin"></i></div>
        </div>
    </div>
</div>

<!-- Safes Table -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('safes.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-md-8">
                <label for="safe-search" class="form-label">Search</label>
                <input id="safe-search" type="text" name="q" value="{{ $search ?? request('q') }}" class="form-control" placeholder="Search by name or location">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <a href="{{ route('safes.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card" id="safes-list-container">
    <div class="card-header">
        <i class="bi bi-list"></i> {{ __('messages.all_safes') }}
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.location') }}</th>
                    <th>{{ __('messages.current_balance') }}</th>
                    <th>{{ __('messages.max_balance') }}</th>
                    <th>{{ __('messages.usage') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($safes ?? [] as $safe)
                    @php
                        $usagePercent = ($safe->max_balance && (float) $safe->max_balance > 0)
                            ? min(100, max(0, (($safe->balance ?? 0) / $safe->max_balance) * 100))
                            : 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $safe->name }}</strong></td>
                        <td>{{ $safe->location }}</td>
                        <td><h5 style="color: var(--primary-green); font-weight: 900;">{{ $currencySymbol }}{{ number_format($safe->balance, 2) }}</h5></td>
                        <td>{{ $safe->max_balance ? $currencySymbol . number_format($safe->max_balance, 2) : __('messages.unlimited') }}</td>
                        <td>
                            @if(($safe->max_balance ?? 0) > 0)
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                        style="width: {{ $usagePercent }}%">
                                        {{ round($usagePercent) }}%
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-info">{{ __('messages.not_available') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $safe->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $safe->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('safes.show', $safe->id) }}" class="btn btn-sm btn-info me-1" title="{{ __('messages.view_details') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('safes.transactions', $safe->id) }}" class="btn btn-sm btn-secondary me-1" title="{{ __('messages.view_transactions') }}">
                                <i class="bi bi-arrow-left-right"></i>
                            </a>
                            <a href="{{ route('safes.edit', $safe->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deleteSafe({{ $safe->id }})" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">{{ __('messages.no_safes_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($safes ?? false)
    <div class="mt-3">
        {{ $safes->links() }}
    </div>
@endif
@include('components.ajax-list')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    initAjaxList({ containerId: 'safes-list-container', searchSelector: '#safe-search', searchParam: 'q', debounceMs: 300 });
});
</script>
@endpush

@section('js')
<script>
    function deleteSafe(id) {
        if (confirm('{{ __('messages.delete_safe_confirm') }}')) {
            const url = '{{ url("safes") }}/' + id;
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
