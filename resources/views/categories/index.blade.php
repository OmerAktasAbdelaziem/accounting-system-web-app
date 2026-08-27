@extends('layouts.modern')

@section('title', __('messages.categories'))

@section('content')
@php
    $totalCategories = (int) ($stats['total_categories'] ?? 0);
    $totalProducts = (int) ($stats['total_products'] ?? 0);
    $avgPerCategory = $stats['avg_products_per_category'] ?? 0;
@endphp

<div class="categories-shell">
    <style>
        .categories-shell {
            position: relative;
            isolation: isolate;
        }

        .categories-shell::before,
        .categories-shell::after {
            content: '';
            position: absolute;
            inset: auto;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(20px);
            opacity: 0.12;
            z-index: 0;
        }

        .categories-shell::before { top: -30px; right: -100px; background: radial-gradient(circle, rgba(255, 140, 0, 0.8) 0%, rgba(255, 140, 0, 0) 70%); }
        .categories-shell::after { bottom: 60px; left: -120px; background: radial-gradient(circle, rgba(37, 99, 235, 0.6) 0%, rgba(37, 99, 235, 0) 70%); }

        .categories-page { position: relative; z-index: 1; display: grid; gap: 18px; }

        .categories-mobile-list { display: none; }

        .categories-hero {
            border-radius: 30px;
            padding: 24px;
            background:
                linear-gradient(135deg, rgba(17,24,39,0.96), rgba(31,41,55,0.92)),
                radial-gradient(circle at top right, rgba(255,140,0,0.14), transparent 36%),
                radial-gradient(circle at bottom left, rgba(37,99,235,0.12), transparent 34%);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.20);
            color: #fff;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
            gap: 18px;
            align-items: stretch;
        }

        .categories-hero-badge { display:inline-flex; gap:8px; align-items:center; padding:8px 14px; border-radius:999px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.10); color:#fff; font-weight:700; font-size:12px; letter-spacing:.06em; text-transform:uppercase }
        .categories-hero-title { margin:0; font-size:clamp(2rem,4vw,3.4rem); line-height:0.95; letter-spacing:-0.05em; font-weight:900; color:#fff }
        .categories-hero-text { max-width:760px; margin:0; color: rgba(255,255,255,0.78); font-size:15px; line-height:1.7 }

        .categories-hero-panel { border-radius:26px; padding:18px; background: rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.10); display:grid; gap:12px; align-content:space-between }
        .categories-hero-panel-top { display:grid; gap:8px }
        .categories-hero-panel-title { margin:0; font-size:13px; letter-spacing:.12em; text-transform:uppercase; color:rgba(255,255,255,0.72); font-weight:800 }
        .categories-hero-panel-value { display:flex; align-items:baseline; gap:10px; font-size:2.2rem; line-height:1; font-weight:900; color:#fff }

        /* mini metrics used in hero panel */
        .products-hero-panel-list { display: grid; gap:10px }
        .products-mini-metric { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:12px 14px; border-radius:18px; background: rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.08); }
        .products-mini-metric .label { display:block; font-size:12px; color: rgba(255,255,255,0.68); font-weight:700; text-transform:uppercase; letter-spacing:.08em }
        .products-mini-metric .value { font-size:16px; color:#fff; font-weight:900 }
        .products-mini-metric .tone { width:40px; height:40px; border-radius:14px; display:inline-flex; align-items:center; justify-content:center; background: rgba(255,255,255,0.09); color:#fff }
        .products-hero-actions .btn { border-radius:14px; padding:0.72rem 1rem; min-height:46px; font-weight:700 }
        .categories-stats { display:grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap:14px }

        .categories-toolbar { border-radius: 18px; padding: 14px; background: rgba(255,255,255,0.96); border: 1px solid rgba(148,163,184,0.12); }

        .categories-card { border-radius: 20px; overflow: hidden; border:1px solid rgba(148,163,184,0.12); background: rgba(255,255,255,0.98); }
        .categories-card .card-header { padding: 14px 18px; background: linear-gradient(135deg,#fff,#fff6ec); border-bottom:1px solid rgba(255,140,0,0.08); }

        @media (max-width: 992px) {
            .categories-hero { grid-template-columns: 1fr; }
            .categories-stats { grid-template-columns: repeat(2, minmax(0,1fr)); }
            .categories-card { display: none; }
            .categories-mobile-list { display: grid; gap:12px }
        }
    </style>

    <section class="categories-page">
        <x-section-hero
            badge="<i class='bi bi-tags'></i> {{ __('messages.categories_management') }}"
            title="<i class='bi bi-tags'></i> {{ __('messages.categories_management') }}"
            :description="'Organize and review categories, see inventory value and per-category averages at a glance.'"
        >
            <x-slot name="actions">
                @feature('categories.create')
                    <a href="{{ route('categories.create') }}" class="btn btn-primary-modern"><i class="bi bi-plus-circle"></i> {{ __('messages.add_category') }}</a>
                @endfeature
            </x-slot>

            <x-slot name="panel">
                <div class="categories-hero-panel-top">
                    <p class="categories-hero-panel-title">{{ __('Live snapshot') }}</p>
                    <div class="categories-hero-panel-value"><span>{{ $totalCategories }}</span><small>{{ __('categories') }}</small></div>
                </div>
                <div class="products-hero-panel-list mt-2">
                    <div class="products-mini-metric"><div><span class="label">{{ __('Total products') }}</span><span class="value">{{ $totalProducts }}</span></div><div class="tone"><i class="bi bi-box-seam"></i></div></div>
                    <div class="products-mini-metric"><div><span class="label">{{ __('Avg / category') }}</span><span class="value">{{ $avgPerCategory }}</span></div><div class="tone"><i class="bi bi-graph-up"></i></div></div>
                </div>
            </x-slot>
        </x-section-hero>

        <div class="categories-stats">
            <div class="stat-card">
                <h6>{{ __('messages.total_categories') }}</h6>
                <div class="value">{{ $totalCategories }}</div>
                <div class="icon"><i class="bi bi-tags"></i></div>
            </div>
            <div class="stat-card">
                <h6>{{ __('messages.total_products') }}</h6>
                <div class="value">{{ $totalProducts }}</div>
                <div class="icon"><i class="bi bi-box-seam"></i></div>
            </div>
            <div class="stat-card green">
                <h6>{{ __('messages.avg_products_per_category') }}</h6>
                <div class="value">{{ $avgPerCategory }}</div>
                <div class="icon"><i class="bi bi-graph-up"></i></div>
            </div>
            <div class="stat-card green">
                <h6>{{ __('messages.status') }}</h6>
                <div class="value" style="font-size:20px;color:#27ae60">{{ __('messages.active') }}</div>
                <div class="icon"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>

        <div class="categories-toolbar">
            <div class="row g-2">
                <div class="col-md-8">
                    <label class="form-label">{{ __('messages.search') }}</label>
                    <input id="searchInput" class="form-control" placeholder="Search categories...">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-outline-secondary w-100" onclick="filterCategories()"><i class="bi bi-funnel"></i> {{ __('messages.filter') }}</button>
                </div>
            </div>
        </div>

        <div class="categories-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0"><i class="bi bi-list-ul text-warning me-2"></i>{{ __('messages.all_categories') }}</h2>
                    <div class="text-muted small">{{ $totalCategories }} items</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('messages.category_name') }}</th>
                            <th>{{ __('messages.description') }}</th>
                            <th>{{ __('messages.total_products') }}</th>
                            <th>{{ __('messages.stock_value') }}</th>
                            <th>{{ __('messages.avg_price') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTable">
                        @forelse($categories ?? [] as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td><small class="text-muted">{{ Str::limit($category->description ?? __('messages.not_available'), 80) }}</small></td>
                                <td><span class="badge bg-primary">{{ $category->total_products }} {{ __('messages.products') }}</span></td>
                                <td><strong>{{ $currencySymbol }}{{ number_format($category->total_stock_value ?? 0, 2) }}</strong></td>
                                <td>{{ $currencySymbol }}{{ number_format($category->avg_price ?? 0, 2) }}</td>
                                <td>
                                    <div class="d-inline-flex gap-2">
                                        @feature('categories.view')
                                            <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                        @endfeature
                                        @feature('categories.edit')
                                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                        @endfeature
                                        @feature('categories.delete')
                                            <button onclick="deleteCategory({{ $category->id }})" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                                        @endfeature
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($categories ?? false)
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">{{ $categories->links() }}</div>
            @endif
        </div>

        <div class="categories-mobile-list d-md-none">
            @forelse($categories ?? [] as $category)
                <div class="category-mobile-card">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $category->name }}</strong>
                            <div class="text-muted small">{{ Str::limit($category->description ?? __('messages.not_available'), 60) }}</div>
                        </div>
                        <span class="badge bg-primary">{{ $category->total_products }}</span>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <div class="bg-light rounded-3 p-2 flex-fill"><div class="small text-muted">{{ __('Stock Value') }}</div><strong>{{ $currencySymbol }}{{ number_format($category->total_stock_value ?? 0, 2) }}</strong></div>
                        <div class="bg-light rounded-3 p-2 flex-fill"><div class="small text-muted">{{ __('Avg Price') }}</div><strong>{{ $currencySymbol }}{{ number_format($category->avg_price ?? 0, 2) }}</strong></div>
                    </div>
                    <div class="d-grid gap-2">
                        @feature('categories.view')
                            <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-outline-secondary">{{ __('messages.view_details') }}</a>
                        @endfeature
                        @feature('categories.edit')
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary">{{ __('messages.edit') }}</a>
                        @endfeature
                        @feature('categories.delete')
                            <button onclick="deleteCategory({{ $category->id }})" class="btn btn-sm btn-outline-danger">{{ __('messages.delete') }}</button>
                        @endfeature
                    </div>
                </div>
            @empty
                <div class="text-center text-muted">{{ __('messages.no_data') }}</div>
            @endforelse

            @if($categories ?? false)
                <div class="mt-2">{{ $categories->links() }}</div>
            @endif
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
    function filterCategories() {
        const q = document.getElementById('searchInput')?.value || '';
        const params = new URLSearchParams({ search: q });
        showLoading();
        fetch(`{{ route('categories.index') }}?${params.toString()}`)
            .then(r => r.text())
            .then(html => {
                // replace table body for quick client-side update
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.getElementById('categoriesTable');
                if (newTbody) document.getElementById('categoriesTable').innerHTML = newTbody.innerHTML;
                hideLoading();
            }).catch(()=>hideLoading());
    }

    function deleteCategory(id) {
        if (!confirm('{{ __('messages.confirm_delete') }}')) return;
        showLoading();
        fetch(`{{ url('categories') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json' } })
            .then(r=>r.json()).then(data=>{ if (data.success) location.reload(); else alert(data.message || '{{ __('messages.error_deleting_category') }}'); hideLoading(); }).catch(()=>{ hideLoading(); alert('{{ __('messages.error_deleting_category') }}'); });
    }
</script>
@endsection
