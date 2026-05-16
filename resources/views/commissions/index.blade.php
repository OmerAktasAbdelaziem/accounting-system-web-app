@extends('layouts.modern')

@section('title', __('messages.commission_management'))

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

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card" style="border-left: 4px solid #ff8c00;">
            <div class="card-body">
                <small class="text-muted d-block mb-2">Total Commission</small>
                <h3 class="mb-0" style="color: #ff8c00;">{{ $currencySymbol }}{{ number_format($stats['totalCommission'], 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Transaction History Section -->
<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-receipt"></i> Commission Transaction History
            </h5>
            <button class="btn btn-sm btn-outline-light" onclick="printTransactions()">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Employee</th>
                    <th>Sale Amount</th>
                    <th>Rate</th>
                    <th>Commission</th>
                    <th>Reference</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions ?? [] as $commission)
                    <tr>
                        <td>
                            <small>{{ $commission->commission_date?->format('d/m/Y') ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <strong>{{ $commission->employee?->name ?? 'N/A' }}</strong>
                            <br>
                            <small class="text-muted">{{ $commission->employee?->employee_code ?? '' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $currencySymbol }}{{ number_format($commission->sale_amount, 2) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ number_format($commission->commission_rate, 0) }}%</span>
                        </td>
                        <td>
                            <strong style="color: #27ae60;">{{ $currencySymbol }}{{ number_format($commission->commission_amount, 2) }}</strong>
                        </td>
                        <td>
                            <small style="font-family: monospace;">{{ $commission->reference_type ?? '-' }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('commissions.show', $commission) }}" class="btn btn-outline-info" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('commissions.edit', $commission) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteCommission({{ $commission->id }})" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No commission transactions found. <a href="{{ route('commissions.create') }}">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions instanceof \Illuminate\Pagination\Paginator)
        <div class="card-footer">
            {{ $commissions->links() }}
        </div>
    @endif
</div>

<!-- Monthly Commission Aggregation -->
@if($monthlyCommissions->isNotEmpty())
    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
            <h5 class="mb-0">
                <i class="bi bi-graph-up"></i> Monthly Commission Summary
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Month/Year</th>
                        <th>Total Commission</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyCommissions as $monthly)
                        @php
                            $date = \Carbon\Carbon::createFromDate((int)$monthly->year, (int)$monthly->month, 1);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $date->format('F Y') }}</strong>
                            </td>
                            <td>
                                <h6 class="mb-0" style="color: #27ae60;">
                                    {{ $currencySymbol }}{{ number_format($monthly->total, 2) }}
                                </h6>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@section('js')
<script>
    function deleteCommission(id) {
        if (confirm('Are you sure you want to delete this commission record?')) {
            fetch(`/commissions/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    function printTransactions() {
        window.print();
    }
</script>
@endsection
