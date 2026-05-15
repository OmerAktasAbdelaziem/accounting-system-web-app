@extends('layouts.modern')

@section('title', __('messages.dashboard'))

@section('content')
@php
    $dashboardCurrency = \App\Models\Currency::byCode((string) \App\Models\Setting::get('currency', 'AED'));
    $dashboardCurrencySymbol = $dashboardCurrency?->symbol ?? '$';
@endphp
<div class="mb-4">
    <h1 style="font-weight: 900; color: #1a1a1a;">
        <i class="bi bi-speedometer2" style="color: #ff8c00;"></i> {{ __('messages.dashboard') }}
    </h1>
</div>

<!-- Main Statistics Row -->
<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <h6>{{ __('messages.total_products') }}</h6>
            <div class="value">{{ $totalProducts ?? 0 }}</div>
            <div class="icon"><i class="bi bi-box-seam"></i></div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="stat-card green">
            <h6>{{ __('messages.total_sales') }}</h6>
            <div class="value" style="color: var(--primary-green);">{{ $currencySymbol }}{{ number_format($totalSales ?? 0, 0) }}</div>
            <div class="icon"><i class="bi bi-graph-up"></i></div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <h6>{{ __('messages.total_employees') }}</h6>
            <div class="value">{{ $totalEmployees ?? 0 }}</div>
            <div class="icon"><i class="bi bi-people"></i></div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="stat-card green">
            <h6>{{ __('messages.low_stock') }}</h6>
            <div class="value" style="color: #c0392b;">{{ $lowStockCount ?? 0 }}</div>
            <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-graph-up"></i> {{ __('messages.sales_trend') }}
                <span class="badge" style="background: var(--primary-orange);">{{ __('messages.monthly') }}</span>
            </div>
            <div class="card-body">
                <canvas id="salesChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart"></i> {{ __('messages.inventory_status') }}
            </div>
            <div class="card-body">
                <canvas id="inventoryChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Row -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-lightning"></i> {{ __('messages.quick_actions') }}
            </div>
            <div class="card-body">
                <a href="{{ route('products.create') }}" class="btn btn-primary-modern w-100 mb-2">
                    <i class="bi bi-plus-circle"></i> {{ __('messages.add_product') }}
                </a>
                <a href="{{ route('employees.create') }}" class="btn btn-success-modern w-100 mb-2">
                    <i class="bi bi-person-plus"></i> {{ __('messages.add_employee') }}
                </a>
                <a href="{{ route('reports.sales') }}" class="btn btn-primary-modern w-100">
                    <i class="bi bi-file-earmark-pdf"></i> {{ __('messages.generate_report') }}
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle"></i> {{ __('messages.system_overview') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <small class="text-muted">{{ __('messages.database') }}</small>
                        <p><span class="badge bg-success">{{ __('messages.connected') }}</span></p>
                    </div>
                    <div class="col-6 mb-3">
                        <small class="text-muted">{{ __('messages.api') }}</small>
                        <p><span class="badge bg-success">{{ __('messages.active_status') }}</span></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">{{ __('messages.last_update') }}</small>
                        <p><small>{{ now()->format('M d, Y H:i') }}</small></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">{{ __('messages.version') }}</small>
                        <p><small>{{ __('messages.phase_6_v1') }}</small></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Sales Chart
    const salesCtx = document.getElementById('salesChart')?.getContext('2d');
    if (salesCtx) {
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: '{{ __("messages.sales") ?? "Sales" }}',
                    data: {{ json_encode($salesData ?? [0,0,0,0,0,0]) }},
                    borderColor: '#ff8c00',
                    backgroundColor: 'rgba(255, 140, 0, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Inventory Chart
    const inventoryCtx = document.getElementById('inventoryChart')?.getContext('2d');
    if (inventoryCtx) {
        new Chart(inventoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['{{ __("messages.in_stock") ?? "In Stock" }}', '{{ __("messages.low_stock") ?? "Low Stock" }}'],
                datasets: [{
                    data: {{ json_encode($inventoryData ?? [70, 30]) }},
                    backgroundColor: ['#27ae60', '#ff8c00'],
                    borderColor: ['#fff', '#fff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
</script>
@endsection
