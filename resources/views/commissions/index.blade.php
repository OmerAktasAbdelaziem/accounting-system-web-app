@extends('layouts.modern')

@section('title', __('messages.commission_management'))

@section('content')
<style>
    @media (max-width: 768px) {
        .commissions-hero {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px;
        }

        .commissions-hero .btn {
            width: 100%;
        }

        .commissions-desktop-table {
            display: none;
        }

        .commissions-mobile-list {
            display: grid;
            gap: 12px;
        }

        .commission-mobile-card {
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(226,232,240,.95);
            border-radius: 20px;
            padding: 14px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .commission-mobile-card .top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .commission-mobile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .row.mb-4 .col-md-3,
        .row.g-3 .col-md-6,
        .row.g-3 .col-xl-4 {
            width: 100%;
        }

        .card,
        .card-body {
            border-radius: 16px;
        }

        .quick-action,
        .card-header,
        .card-body {
            padding-left: 16px;
            padding-right: 16px;
        }

        .d-grid.gap-2 .btn,
        .d-grid.gap-2 form,
        .d-grid.gap-2 span {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        h1 {
            font-size: 22px;
        }

        .card-body h3 {
            font-size: 24px;
        }
    }
</style>

<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center commissions-hero">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-percent" style="color: #ff8c00;"></i> {{ __('messages.commissions_management') }}
        </h1>
        @feature('commissions.create')
            <a href="{{ route('commissions.create') }}" class="btn btn-primary-modern">
                <i class="bi bi-plus-circle"></i> Create First Commission Profile
            </a>
        @endfeature
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

<!-- Commission Profiles -->
<div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-person-badge"></i> Commission Profiles</h5>
                <small>Open an employee profile to pay active commissions or add more records</small>
        </div>
    </div>
    <div class="card-body">
        @if(($commissionProfiles ?? collect())->isNotEmpty())
            <div class="row g-3">
                @foreach($commissionProfiles as $profile)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">{{ $profile->name }}</h5>
                                        <small class="text-muted">{{ $profile->employee_code ?? '-' }}</small>
                                    </div>
                                    <span class="badge bg-light text-dark border">{{ $profile->commission_count ?? $profile->commissions->count() }} records</span>
                                </div>
                                <div class="mb-2 text-muted small">Last commission: {{ $profile->last_commission_date?->format('M d, Y') ?? '-' }}</div>
                                <div class="mb-3 fw-bold text-success">{{ $currencySymbol }}{{ number_format($profile->total_commission_amount ?? 0, 2) }}</div>
                                    <div class="d-grid gap-2">
                                        @feature('commissions.view')
                                            <a href="{{ route('commissions.show', $profile->latest_commission) }}" class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i> View Profile
                                            </a>
                                        @endfeature

                                        @if($profile->latest_commission && $profile->latest_commission->status !== 'paid')
                                            @feature('commissions.edit')
                                                <form method="POST" action="{{ route('commissions.pay', $profile->latest_commission) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Mark this commission as paid? It will be hidden from the active list but stay in the database.')">
                                                        <i class="bi bi-cash-coin"></i> Pay Commission
                                                    </button>
                                                </form>
                                            @endfeature
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle py-2">Paid</span>
                                        @endif
                                    </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center text-muted py-4">
                No commission profiles yet. Use <strong>Create First Commission Profile</strong> for the first record of an employee.
            </div>
        @endif
    </div>
</div>

<!-- Monthly Commission Aggregation -->
@if($monthlyCommissions->isNotEmpty())
    <div class="commissions-mobile-list d-md-none mb-3">
        @foreach($monthlyCommissions as $monthly)
            @php
                $date = \Carbon\Carbon::createFromDate((int)$monthly->year, (int)$monthly->month, 1);
            @endphp
            <div class="commission-mobile-card">
                <div class="top">
                    <div>
                        <strong>{{ $date->format('F Y') }}</strong>
                        <div class="small text-muted">Monthly commission summary</div>
                    </div>
                    <span class="badge bg-light text-dark border">{{ $currencySymbol }}{{ number_format($monthly->total, 2) }}</span>
                </div>
                <div class="small text-muted">Use the desktop table for detailed monthly breakdowns.</div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
            <h5 class="mb-0">
                <i class="bi bi-graph-up"></i> Monthly Commission Summary
            </h5>
        </div>
        <div class="table-responsive commissions-desktop-table">
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
 
<!-- Paid Commissions History -->
<div class="card mt-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Paid Commissions History</h5>
    </div>
    <div class="card-body">
        @if(($paidCommissions ?? collect())->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Sale Amount</th>
                            <th>Commission</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paidCommissions as $c)
                            <tr>
                                <td>{{ $c->commission_date?->format('Y-m-d') }}</td>
                                <td>{{ $c->employee?->name ?? '-' }}</td>
                                <td>{{ $currencySymbol ?? '' }}{{ number_format($c->sale_amount ?? 0, 2) }}</td>
                                <td>{{ $currencySymbol ?? '' }}{{ number_format($c->commission_amount ?? 0, 2) }}</td>
                                <td>{{ $c->reference_type ? ($c->reference_type . ' #' . $c->reference_id) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $paidCommissions->appends(request()->except('paid_page'))->links() }}
            </div>
        @else
            <div class="text-center text-muted py-4">No paid commissions yet.</div>
        @endif
    </div>
</div>
@endsection

@section('js')
<script>
</script>
@endsection
