@extends('layouts.modern')

@section('title', __('messages.commissions_management'))

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-percent" style="color: #ff8c00;"></i> {{ __('messages.commissions_management') }}
        </h1>
        <a href="{{ route('commissions.create') }}" class="btn btn-primary-modern">
            <i class="bi bi-plus-circle"></i> {{ __('messages.add_commission') }}
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <h6>{{ __('messages.total_commissions') }}</h6>
            <div class="value">{{ currencySymbol() }}{{ number_format($stats['total'] ?? 0, 2) }}</div>
            <div class="icon"><i class="bi bi-cash-coin"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card green">
            <h6>{{ __('messages.pending') }}</h6>
            <div class="value">{{ currencySymbol() }}{{ number_format($stats['pending'] ?? 0, 2) }}</div>
            <div class="icon"><i class="bi bi-hourglass"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <h6>{{ __('messages.approved') }}</h6>
            <div class="value">{{ currencySymbol() }}{{ number_format($stats['approved'] ?? 0, 2) }}</div>
            <div class="icon"><i class="bi bi-check-circle"></i></div>
        </div>
    </div>
</div>

<!-- Commissions Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list"></i> {{ __('messages.commissions') }}
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.employee') }}</th>
                    <th>{{ __('messages.commission_rate') }}</th>
                    <th>{{ __('messages.sale_amount') }}</th>
                    <th>{{ __('messages.commission_amount') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions ?? [] as $commission)
                    <tr>
                        <td><strong>{{ $commission->employee->name ?? __('messages.not_available') }}</strong></td>
                        <td><span class="badge badge-orange">{{ $commission->commission_rate }}%</span></td>
                        <td>{{ currencySymbol() }}{{ number_format($commission->sale_amount, 2) }}</td>
                        <td><strong>{{ currencySymbol() }}{{ number_format($commission->commission_amount, 2) }}</strong></td>
                        <td>{{ $commission->commission_date->format('M d, Y') }}</td>
                        <td>
                            <span class="badge {{ $commission->status === 'pending' ? 'bg-warning' : ($commission->status === 'approved' ? 'bg-success' : 'bg-info') }}">
                                {{ __('messages.' . $commission->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('commissions.show', $commission->id) }}" class="btn btn-sm btn-info me-1" title="{{ __('messages.view_details') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('commissions.edit', $commission->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deleteCommission({{ $commission->id }})" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">{{ __('messages.no_commissions_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($commissions ?? false)
    <div class="mt-3">
        {{ $commissions->links() }}
    </div>
@endif
@endsection

@section('js')
<script>
    function deleteCommission(id) {
        if (confirm('{{ __('messages.delete_commission_confirm') }}')) {
            const url = '{{ url("commissions") }}/' + id;
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
