@extends('layouts.modern')

@section('content')
<div class="container">
    <style>
        @media (max-width: 768px) {
            .suppliers-hero {
                flex-direction: column;
                align-items: stretch !important;
                gap: 12px;
            }

            .suppliers-hero .btn,
            .suppliers-search .btn,
            .suppliers-search .form-control {
                width: 100%;
            }

            .suppliers-desktop-table {
                display: none;
            }

            .suppliers-mobile-list {
                display: grid;
                gap: 12px;
            }

            .supplier-mobile-card {
                background: rgba(255,255,255,.96);
                border: 1px solid rgba(226,232,240,.95);
                border-radius: 20px;
                padding: 14px;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            }

            .supplier-mobile-card .top {
                display: flex;
                justify-content: space-between;
                gap: 10px;
                align-items: flex-start;
                margin-bottom: 10px;
            }

            .supplier-mobile-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                margin-bottom: 12px;
            }
        }

        @media (max-width: 576px) {
            .suppliers-hero h3 {
                font-size: 22px;
            }
        }
    </style>

    <div class="d-flex justify-content-between mb-3 suppliers-hero">
        <h3>{{ __('messages.suppliers') }}</h3>
        @feature('suppliers.create')
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary">{{ __('Create') }}</a>
        @endfeature
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-soft overflow-hidden" style="border-radius: 28px; background: linear-gradient(135deg, #3b78ff 0%, #38c7ab 100%); color: #fff;">
                <div class="card-body position-relative p-4">
                    <div class="position-absolute top-0 end-0 opacity-15" style="font-size: 8rem; line-height: 1; transform: translate(25%, -20%);">
                        <strong>₿</strong>
                    </div>
                    <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
                        <div class="me-3">
                            <span class="badge bg-white bg-opacity-15 text-white rounded-pill px-3 py-2">{{ __('messages.branch_debts') }}</span>
                            <h2 class="mt-3 mb-2 fw-bold">{{ __('messages.branch_debts') }}</h2>
                            <p class="mb-0 opacity-85">{{ __('messages.branch_debts_widget_text') }}</p>
                        </div>

                        <div class="text-end">
                            <div class="text-uppercase small opacity-75 mb-2">{{ __('messages.outstanding') }}</div>
                            <div class="debt-counter display-5 fw-bold">{{ $currencySymbol }}{{ number_format((float) ($allBranchDebtsTotal ?? 0), 2) }}</div>
                            <div class="small mt-1 opacity-75">{{ __('messages.total_branch_debts_summary') }}</div>
                        </div>
                    </div>

                    <div class="row g-3 mt-4 text-white">
                        <div class="col-6 col-md-3">
                            <div class="rounded-4 bg-white bg-opacity-15 p-3">
                                <div class="small opacity-75">{{ __('messages.branches') }}</div>
                                <div class="h4 fw-bold mb-0">{{ $branchCount }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="rounded-4 bg-white bg-opacity-15 p-3">
                                <div class="small opacity-75">{{ __('messages.total_suppliers') }}</div>
                                <div class="h4 fw-bold mb-0">{{ $supplierCount }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="rounded-4 bg-white bg-opacity-15 p-3">
                                <div class="small opacity-75">{{ __('messages.total_purchased') }}</div>
                                <div class="h4 fw-bold mb-0">{{ $currencySymbol }}{{ number_format((float) ($totalPurchasedAcrossBranches ?? 0), 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="rounded-4 bg-white bg-opacity-15 p-3">
                                <div class="small opacity-75">{{ __('messages.total_paid') }}</div>
                                <div class="h4 fw-bold mb-0">{{ $currencySymbol }}{{ number_format((float) ($totalPaidAcrossBranches ?? 0), 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 text-white-75 small">
                            <span>{{ __('Payment coverage') }}</span>
                            <span>{{ $debtCoveragePercent }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px; background: rgba(255,255,255,0.18);">
                            <div class="progress-bar rounded-pill" role="progressbar" aria-valuenow="{{ $debtCoveragePercent }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $debtCoveragePercent }}%; background: rgba(255,255,255,0.95);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body suppliers-search">
            <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label for="supplier-search" class="form-label">Search</label>
                    <input
                        type="text"
                        id="supplier-search"
                        name="q"
                        value="{{ $search ?? request('q') }}"
                        class="form-control"
                        placeholder="Search by name or address"
                    >
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="suppliers-mobile-list d-md-none">
        @foreach($suppliers as $supplier)
            <div class="supplier-mobile-card">
                <div class="top">
                    <div>
                        <strong>{{ is_string($supplier->name) ? $supplier->name : (is_array($supplier->name) ? ($supplier->name[app()->getLocale()] ?? implode(' - ', $supplier->name)) : json_encode($supplier->name)) }}</strong>
                        <div class="small text-muted">ID: {{ $supplier->id }}</div>
                    </div>
                    <div class="text-end fw-bold">{{ $currencySymbol }}{{ number_format(((float)($supplier->opening_balance ?? 0) + (float)($supplier->total_purchased ?? 0) - (float)($supplier->total_paid ?? 0)), 2) }}</div>
                </div>
                <div class="supplier-mobile-grid">
                    <div class="bg-light rounded-4 p-2"><div class="text-muted small">Balance</div><strong>{{ $currencySymbol }}{{ number_format($supplier->opening_balance ?? 0,2) }}</strong></div>
                    <div class="bg-light rounded-4 p-2"><div class="text-muted small">Outstanding</div><strong class="{{ (((float)($supplier->opening_balance ?? 0) + (float)($supplier->total_purchased ?? 0) - (float)($supplier->total_paid ?? 0)) > 0) ? 'text-danger' : 'text-success' }}">{{ $currencySymbol }}{{ number_format(((float)($supplier->opening_balance ?? 0) + (float)($supplier->total_purchased ?? 0) - (float)($supplier->total_paid ?? 0)), 2) }}</strong></div>
                </div>
                <div class="d-grid gap-2">
                    @feature('suppliers.view')
                        <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a>
                    @endfeature
                    @feature('suppliers.edit')
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                    @endfeature
                    @feature('suppliers.delete')
                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                        </form>
                    @endfeature
                </div>
            </div>
        @endforeach
        <div class="mt-2">{{ $suppliers->links() }}</div>
    </div>

    <div class="card suppliers-desktop-table" id="suppliers-list-container">
        <div class="card-body">
            <table class="table table-striped">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Balance') }}</th>
                        <th>Purchased</th>
                        <th>Paid</th>
                        <th>Outstanding</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ is_string($supplier->name) ? $supplier->name : (is_array($supplier->name) ? ($supplier->name[app()->getLocale()] ?? implode(' - ', $supplier->name)) : json_encode($supplier->name)) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format($supplier->opening_balance ?? 0,2) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format((float) ($supplier->total_purchased ?? 0),2) }}</td>
                        <td>{{ $currencySymbol }}{{ number_format((float) ($supplier->total_paid ?? 0),2) }}</td>
                        <td class="fw-bold {{ (((float)($supplier->opening_balance ?? 0) + (float)($supplier->total_purchased ?? 0) - (float)($supplier->total_paid ?? 0)) > 0) ? 'text-danger' : 'text-success' }}">
                            {{ $currencySymbol }}{{ number_format(((float)($supplier->opening_balance ?? 0) + (float)($supplier->total_purchased ?? 0) - (float)($supplier->total_paid ?? 0)), 2) }}
                        </td>
                        <td class="action-buttons">
                            @feature('suppliers.view')
                                <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-sm btn-outline-secondary">{{ __('View') }}</a>
                            @endfeature

                            @feature('suppliers.edit')
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>
                            @endfeature

                            @feature('suppliers.delete')
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                                </form>
                            @endfeature
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $suppliers->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
function animateCounter(element, targetValue, duration = 1200) {
    const startValue = 0;
    const startTime = performance.now();

    function update(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const value = startValue + (targetValue - startValue) * easeOutCubic(progress);
        element.textContent = element.textContent.startsWith('{{ $currencySymbol }}')
            ? '{{ $currencySymbol }}' + value.toFixed(2)
            : value.toFixed(2);

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }

    requestAnimationFrame(update);
}

function easeOutCubic(t) {
    return 1 - Math.pow(1 - t, 3);
}

document.addEventListener('DOMContentLoaded', function () {
    const debtCounter = document.querySelector('.debt-counter');
    if (debtCounter) {
        animateCounter(debtCounter, parseFloat('{{ $allBranchDebtsTotal ?? 0 }}'));
    }

    const input = document.getElementById('supplier-search');
    const desktopContainer = document.getElementById('suppliers-list-container');
    const mobileContainer = document.querySelector('.suppliers-mobile-list');
    if (!input || (!desktopContainer && !mobileContainer)) return;

    let timer = null;
    const debounceMs = 300;

    async function fetchAndRender(urlOrQ) {
        try {
            let url;
            if (typeof urlOrQ === 'string' && (urlOrQ.startsWith('http') || urlOrQ.startsWith('/'))) {
                url = new URL(urlOrQ, window.location.origin);
            } else {
                url = new URL(window.location.href);
                const q = String(urlOrQ || input.value || '').trim();
                if (q) url.searchParams.set('q', q);
                else url.searchParams.delete('q');
            }

            const resp = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!resp.ok) throw new Error('Network response was not ok');

            const html = await resp.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newDesktopContainer = doc.getElementById('suppliers-list-container');
            const newMobileContainer = doc.querySelector('.suppliers-mobile-list');

            if (desktopContainer && newDesktopContainer) {
                desktopContainer.innerHTML = newDesktopContainer.innerHTML;
            }
            if (mobileContainer && newMobileContainer) {
                mobileContainer.innerHTML = newMobileContainer.innerHTML;
            }

            window.history.replaceState({}, '', url);
        } catch (err) {
            console.error('Supplier search failed', err);
        }
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => fetchAndRender(input.value.trim()), debounceMs);
    });

    document.addEventListener('click', function (e) {
        const anchor = e.target.closest('.pagination a');
        if (!anchor) return;

        const href = anchor.getAttribute('href') || '';
        if (!href.includes('page=')) return;

        e.preventDefault();
        fetchAndRender(href);
    });
});
</script>
@endpush

@endsection
