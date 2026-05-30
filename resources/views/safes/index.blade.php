@extends('layouts.modern')

@section('title', __('messages.safe_management'))

@section('content')
<div class="mb-4">
    <style>
        @media (max-width: 768px) {
            .safes-hero {
                flex-direction: column;
                align-items: stretch !important;
                gap: 12px;
            }

            .safes-hero .btn {
                width: 100%;
            }

            .safes-desktop-table {
                display: none;
            }

            .safes-mobile-list {
                display: grid;
                gap: 12px;
            }

            .safe-mobile-card {
                background: rgba(255,255,255,.96);
                border: 1px solid rgba(226,232,240,.95);
                border-radius: 20px;
                padding: 14px;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            }

            .safe-mobile-card .top {
                display: flex;
                justify-content: space-between;
                gap: 10px;
                align-items: flex-start;
                margin-bottom: 10px;
            }

            .safe-mobile-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                margin-bottom: 12px;
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center safes-hero">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-safe" style="color: #ff8c00;"></i> {{ __('messages.safe_management') }}
        </h1>
        @feature('safes.create')
        <a href="{{ route('safes.create') }}" class="btn btn-primary-modern">
            <i class="bi bi-plus-circle"></i> {{ __('messages.new_safe') }}
        </a>
        @endfeature
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
<div class="card">
    <div class="card-header">
        <i class="bi bi-list"></i> {{ __('messages.all_safes') }}
    </div>
    <style>
        @media (max-width: 768px) {
            .safes-mobile-list { display: block; }
            .table { display: none !important; }
        }
    </style>

    <!-- Mobile safes cards -->
    <div class="safes-mobile-list d-md-none">
        @forelse($safes ?? [] as $safe)
            @php
                $usagePercent = ($safe->max_balance && (float) $safe->max_balance > 0)
                    ? min(100, max(0, (($safe->balance ?? 0) / $safe->max_balance) * 100))
                    : 0;
            @endphp
            <div class="safe-mobile-card">
                <div class="top">
                        <div>
                            <strong>{{ $safe->name }}</strong>
                            <div class="small text-muted">{{ $safe->location }}</div>
                        </div>
                    <div class="text-end fw-bold">{{ $currencySymbol }}{{ number_format($safe->balance, 2) }}</div>
                    </div>
                <div class="safe-mobile-grid">
                    <div class="bg-light rounded-4 p-2"><div class="text-muted small">{{ __('messages.current_balance') }}</div><strong>{{ $currencySymbol }}{{ number_format($safe->balance, 2) }}</strong></div>
                    <div class="bg-light rounded-4 p-2"><div class="text-muted small">{{ __('messages.usage') }}</div><strong>{{ round($usagePercent) }}%</strong></div>
                </div>
                <div class="d-grid gap-2">
                        @feature('safes')
                        <a href="{{ route('safes.show', $safe->id) }}" class="btn btn-sm btn-info">{{ __('messages.view_details') }}</a>
                        <a href="{{ route('safes.transactions', $safe->id) }}" class="btn btn-sm btn-secondary">{{ __('messages.view_transactions') }}</a>
                        @endfeature
                </div>
            </div>
        @empty
            <div class="safe-mobile-card text-center text-muted">{{ __('messages.no_safes_found') }}</div>
        @endforelse
    </div>

    <div class="table-responsive safes-desktop-table">
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
                            @feature('safes')
                            <a href="{{ route('safes.show', $safe->id) }}" class="btn btn-sm btn-info me-1" title="{{ __('messages.view_details') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('safes.transactions', $safe->id) }}" class="btn btn-sm btn-secondary me-1" title="{{ __('messages.view_transactions') }}">
                                <i class="bi bi-arrow-left-right"></i>
                            </a>
                            @endfeature

                            @feature('safes.edit')
                            <a href="{{ route('safes.edit', $safe->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endfeature

                            @feature('safes.delete')
                            <button onclick="deleteSafe({{ $safe->id }})" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endfeature
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
@endsection

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
