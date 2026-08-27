@extends('layouts.modern')

@section('title', __('messages.products'))

@section('content')
@php
    $totalProducts = (int) ($stats['total_products'] ?? 0);
    $activeProducts = (int) ($stats['active_products'] ?? 0);
    $lowStockProductsCount = (int) ($stats['low_stock_products'] ?? 0);
    $categoriesCount = (int) ($stats['categories_count'] ?? 0);
    $avgPrice = (float) ($stats['avg_price'] ?? 0);
    $activeRate = $totalProducts > 0 ? round(($activeProducts / $totalProducts) * 100) : 0;
    $stockAlertRate = $totalProducts > 0 ? round(($lowStockProductsCount / max($totalProducts, 1)) * 100) : 0;
@endphp

<div class="products-shell">
    {{ __('<style>
        .products-shell {
            position: relative;
            isolation: isolate;
        }

        .products-shell::before,
        .products-shell::after {
            content: '';
            position: absolute;
            inset: auto;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(20px);
            opacity: 0.14;
            z-index: 0;
        }

        .products-shell::before {
            top: -40px;
            right: -120px;
            background: radial-gradient(circle, rgba(255, 140, 0, 0.8) 0%, rgba(255, 140, 0, 0) 70%);
        }

        .products-shell::after {
            bottom: 80px;
            left: -130px;
            background: radial-gradient(circle, rgba(39, 174, 96, 0.7) 0%, rgba(39, 174, 96, 0) 70%);
        }

        .products-page {
            position: relative;
            z-index: 1;
            display: grid;
            gap: 18px;
        }

        .products-hero {
            border-radius: 30px;
            padding: 24px;
            background:
                linear-gradient(135deg, rgba(17,24,39,0.96), rgba(31,41,55,0.92)),
                radial-gradient(circle at top right, rgba(255,140,0,0.24), transparent 36%),
                radial-gradient(circle at bottom left, rgba(39,174,96,0.20), transparent 34%);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.20);
            color: #fff;
            overflow: hidden;
        }

        .products-hero-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
            gap: 18px;
            align-items: start;
        }

        .products-hero-copy {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 10px;
        }

        .products-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.10);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .products-hero-title {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.4rem);
            line-height: 0.95;
            letter-spacing: -0.05em;
            font-weight: 900;
            color: #fff;
        }

        .products-hero-title i {
            color: #ffb15c;
            margin-right: 8px;
        }

        .products-hero-text {
            max-width: 760px;
            margin: 0;
            margin-top: 8px;
            color: rgba(255,255,255,0.78);
            font-size: 15px;
            line-height: 1.7;
        }

        .products-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            margin-top: 6px;
        }

        .products-hero-actions .btn {
            border-radius: 14px;
            padding: 0.72rem 1rem;
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
        }

        .products-hero-actions .btn.btn-primary-modern {
            box-shadow: 0 12px 24px rgba(255, 140, 0, 0.22);
        }

        @media (min-width: 769px) {
            .products-hero-copy {
                gap: 6px;
                align-content: start;
            }

            .products-hero-title {
                margin-bottom: 0;
            }

            .products-hero-text {
                margin-top: 4px;
            }

            .products-hero-actions {
                margin-top: 2px;
            }
        }

        .products-hero-panel {
            border-radius: 26px;
            padding: 18px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.10);
            backdrop-filter: none;
            display: grid;
            gap: 12px;
            align-content: space-between;
        }

        .products-hero-panel-top {
            display: grid;
            gap: 8px;
        }

        .products-hero-panel-title {
            margin: 0;
            font-size: 13px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.72);
            font-weight: 800;
        }

        .products-hero-panel-value {
            display: flex;
            align-items: baseline;
            gap: 10px;
            font-size: 2.2rem;
            line-height: 1;
            font-weight: 900;
            color: #fff;
        }

        .products-hero-panel-value small {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.68);
            font-weight: 700;
        }

        .products-hero-panel-list {
            display: grid;
            gap: 10px;
        }

        .products-mini-metric {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 18px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.08);
        }

        .products-mini-metric .label {
            display: block;
            font-size: 12px;
            color: rgba(255,255,255,0.68);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
        }

        .products-mini-metric .value {
            font-size: 16px;
            color: #fff;
            font-weight: 900;
        }

        .products-mini-metric .tone {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(255,255,255,0.09);
            color: #fff;
        }

        .products-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .products-stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 18px;
            background: linear-gradient(180deg, rgba(255,255,255,0.96), rgba(247,250,255,0.94));
            border: 1px solid rgba(148,163,184,0.16);
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            min-height: 134px;
        }

        .products-stat-card::after {
            content: '';
            position: absolute;
            right: -22px;
            bottom: -22px;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,140,0,0.22), transparent 68%);
        }

        .products-stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .10em;
            color: #64748b;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .products-stat-value {
            font-size: 2rem;
            line-height: 1;
            font-weight: 900;
            color: #111827;
            margin-bottom: 10px;
        }

        .products-stat-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
        }

        .products-stat-ring {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: conic-gradient(var(--ring, #ff8c00) var(--percent, 0%), rgba(226,232,240,1) 0);
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .products-stat-ring span {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #fff;
            box-shadow: inset 0 0 0 1px rgba(148,163,184,0.12);
            display: block;
        }

        .products-toolbar {
            border-radius: 28px;
            padding: 18px;
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(148,163,184,0.18);
            box-shadow: 0 14px 38px rgba(15, 23, 42, 0.08);
        }

        .products-toolbar-inner {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) 220px 220px auto;
            gap: 12px;
            align-items: end;
        }

        .products-toolbar .form-label {
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 8px;
        }

        .products-toolbar .form-control,
        .products-toolbar .form-select {
            min-height: 48px;
            border-radius: 14px;
            border: 1px solid rgba(148,163,184,0.24);
            background: #fff;
            box-shadow: none;
        }

        .products-toolbar .btn {
            min-height: 48px;
            border-radius: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .products-card {
            border: 1px solid rgba(148,163,184,0.16);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
            background: rgba(255,255,255,0.95);
        }

        .products-card .card-header {
            background: linear-gradient(135deg, #ffffff 0%, #fff6ec 100%);
            border-bottom: 1px solid rgba(255, 140, 0, 0.12);
            padding: 18px 20px;
            color: #111827;
        }

        .products-card .card-header .header-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: -.02em;
            margin: 0;
        }

        .products-card .table {
            margin-bottom: 0;
        }

        .products-card thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 800;
            border-top: none;
            border-bottom: 1px solid rgba(226,232,240,0.9);
            padding: 16px 18px;
            background: rgba(248,250,252,0.75);
        }

        .products-card tbody td {
            padding: 16px 18px;
            vertical-align: middle;
            border-color: rgba(226,232,240,0.75);
            color: #334155;
        }

        .products-card tbody tr:hover {
            background: rgba(255, 248, 239, 0.85);
        }

        .product-name {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-name .avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ff8c00, #ffb15c);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            box-shadow: 0 10px 20px rgba(255,140,0,0.20);
            flex-shrink: 0;
        }

        .product-name strong {
            display: block;
            font-size: 15px;
            color: #0f172a;
        }

        .product-name small {
            color: #64748b;
            display: block;
            margin-top: 2px;
        }

        .product-actions {
            display: inline-flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .product-actions .btn {
            border-radius: 12px;
            min-width: 40px;
            min-height: 40px;
            padding: 0 .65rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .product-mobile-list {
            display: none;
        }

        .product-mobile-card {
            background: rgba(255,255,255,0.96);
            border: 1px solid rgba(226,232,240,0.9);
            border-radius: 24px;
            padding: 16px;
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.06);
        }

        .product-mobile-card .head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .product-mobile-card .head strong {
            font-size: 15px;
            color: #111827;
        }

        .product-mobile-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 12px;
        }

        .product-mobile-chip {
            background: #f8fafc;
            border-radius: 16px;
            padding: 12px;
            border: 1px solid rgba(226,232,240,0.9);
        }

        .product-mobile-chip span {
            display: block;
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 4px;
            font-weight: 800;
        }

        .product-mobile-chip strong {
            color: #0f172a;
            font-size: 14px;
        }

        .product-mobile-actions {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
        }

        .product-mobile-actions .btn {
            width: 100%;
            min-height: 42px;
            border-radius: 14px;
        }

        .products-empty {
            padding: 36px 18px;
            text-align: center;
            color: #64748b;
        }

        .products-empty i {
            font-size: 34px;
            color: #ff8c00;
            margin-bottom: 10px;
            display: inline-block;
        }

        @media (max-width: 1200px) {
            .products-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .products-toolbar-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .products-shell::before,
            .products-shell::after {
                width: 180px;
                height: 180px;
            }

            .products-hero {
                padding: 18px;
                border-radius: 24px;
            }

            .products-hero-grid {
                grid-template-columns: 1fr;
            }

            .products-hero-copy {
                gap: 14px;
            }

            .products-hero-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .products-stats {
                grid-template-columns: 1fr;
            }

            .products-toolbar-inner {
                grid-template-columns: 1fr;
            }

            .products-card {
                display: none;
            }

            .product-mobile-list {
                display: grid;
                gap: 12px;
            }
        }
    </style>

    <section class="products-page">
        <x-section-hero badge="<i class='bi bi-box-seam'></i> {{ __('messages.products_management') }}"
                   title="<i class='bi bi-box-seam'></i> {{ __('messages.products_management') }}"
                   description="A sharper product command center for fast lookup, stock awareness, and clean actions. Search, filter, export, and manage products from one intentionally designed surface.">
            <x-slot name="actions">
                @feature('products.create')
                    <a href="{{ route('products.create') }}" class="btn btn-primary-modern"><i class="bi bi-plus-circle"></i> {{ __('messages.add_product') }}</a>
                @endfeature

                @feature('downloads')
                    <button class="btn btn-success-modern" onclick="exportToExcel()"><i class="bi bi-download"></i> {{ __('messages.export') }}</button>
                @endfeature
            </x-slot>

            <x-slot name="panel">
                <div class="products-hero-panel-top">
                    <p class="products-hero-panel-title">{{ __('Live snapshot') }}</p>
                    <div class="products-hero-panel-value"><span>{{ $totalProducts }}</span><small>{{ __('products') }}</small></div>
                </div>
                <div class="products-hero-panel-list">
                    <div class="products-mini-metric"><div><span class="label">{{ __('Active rate') }}</span><span class="value">{{ $activeRate }}%</span></div><div class="tone"><i class="bi bi-bolt"></i></div></div>
                    <div class="products-mini-metric"><div><span class="label">{{ __('Stock alerts') }}</span><span class="value">{{ $lowStockProductsCount }}</span></div><div class="tone"><i class="bi bi-exclamation-triangle"></i></div></div>
                    <div class="products-mini-metric"><div><span class="label">{{ __('Categories') }}</span><span class="value">{{ $categoriesCount }}</span></div><div class="tone"><i class="bi bi-tags"></i></div></div>
                </div>
            </x-slot>
        </x-section-hero>

        <div class="products-stats">
            <div class="products-stat-card">
                <div class="products-stat-label">{{ __('Total products') }}</div>
                <div class="products-stat-value">{{ $totalProducts }}</div>
                <div class="products-stat-meta">
                    <span>{{ __('messages.products') }}</span>
                    <div class="products-stat-ring" style="--percent: 100%; --ring: #ff8c00;"><span></span></div>
                </div>
            </div>

            <div class="products-stat-card">
                <div class="products-stat-label">{{ __('Active products') }}</div>
                <div class="products-stat-value">{{ $activeProducts }}</div>
                <div class="products-stat-meta">
                    <span>{{ $activeRate }}% active</span>
                    <div class="products-stat-ring" style="--percent: {{ $activeRate }}%; --ring: #27ae60;"><span></span></div>
                </div>
            </div>

            <div class="products-stat-card">
                <div class="products-stat-label">{{ __('Low stock') }}</div>
                <div class="products-stat-value">{{ $lowStockProductsCount }}</div>
                <div class="products-stat-meta">
                    <span>{{ $stockAlertRate }}% of catalog</span>
                    <div class="products-stat-ring" style="--percent: {{ $stockAlertRate }}%; --ring: #ef4444;"><span></span></div>
                </div>
            </div>

            <div class="products-stat-card">
                <div class="products-stat-label">{{ __('Average price') }}</div>
                <div class="products-stat-value">{{ $currencySymbol }}{{ number_format($avgPrice, 2) }}</div>
                <div class="products-stat-meta">
                    <span>{{ $categoriesCount }} categories</span>
                    <div class="products-stat-ring" style="--percent: 72%; --ring: #2563eb;"><span></span></div>
                </div>
            </div>
        </div>

        <div class="products-toolbar">
            <div class="products-toolbar-inner">
                <div>
                    <label class="form-label" for="searchInput">{{ __('messages.search') }}</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search products by name...">
                </div>
                <div>
                    <label class="form-label" for="categoryFilter">{{ __('messages.category') }}</label>
                    <select class="form-select" id="categoryFilter">
                        <option value="">{{ __('messages.all_categories') }}</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">{{ __('Quick filter') }}</label>
                    <button class="btn btn-outline-secondary w-100" onclick="filterProducts()">
                        <i class="bi bi-funnel"></i> {{ __('messages.filter') }}
                    </button>
                </div>
                <div class="d-grid gap-2">
                    <label class="form-label" style="visibility:hidden">{{ __('Actions') }}</label>
                    <button class="btn btn-outline-dark" type="button" onclick="resetFilters()">
                        <i class="bi bi-arrow-counterclockwise"></i> {{ __('Reset') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="products-card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="header-title mb-0"><i class="bi bi-grid-1x2-fill text-warning me-2"></i>{{ __('messages.products') }}</h2>
                    <div class="text-muted small">{{ $totalProducts }} items loaded</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('messages.product_name') }}</th>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.quantity') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="productsTable">
                        @forelse($products ?? [] as $product)
                            <tr>
                                <td>
                                    <div class="product-name">
                                        <div class="avatar">{{ strtoupper(mb_substr($product->name ?? 'P', 0, 1)) }}</div>
                                        <div>
                                            <strong>{{ $product->name }}</strong>
                                            <small>ID #{{ $product->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $product->current_stock <= 0 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->current_stock }}
                                    </span>
                                </td>
                                <td>{{ $currencySymbol }}{{ number_format($product->selling_price, 2) }}</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="product-actions">
                                        @feature('products.view')
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endfeature

                                        @feature('products.delete')
                                            <button onclick="deleteProduct({{ $product->id }})" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endfeature
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="products-empty">
                                        <i class="bi bi-box2-heart"></i>
                                        <div>{{ __('messages.no_data') }}</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products ?? false)
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

        <div class="product-mobile-list d-md-none">
            @forelse($products ?? [] as $product)
                <div class="product-mobile-card">
                    <div class="head">
                        <div>
                            <strong>{{ $product->name }}</strong>
                            <div class="text-muted small">{{ $product->category->name ?? 'N/A' }}</div>
                        </div>
                        <span class="badge {{ $product->current_stock <= 0 ? 'bg-danger' : 'bg-success' }}">{{ $product->current_stock }}</span>
                    </div>
                    <div class="product-mobile-meta">
                        <div class="product-mobile-chip">
                            <span>{{ __('messages.price') }}</span>
                            <strong>{{ $currencySymbol }}{{ number_format($product->selling_price, 2) }}</strong>
                        </div>
                        <div class="product-mobile-chip">
                            <span>{{ __('messages.status') }}</span>
                            <strong>{{ $product->is_active ? __('messages.active') : __('messages.inactive') }}</strong>
                        </div>
                    </div>
                    <div class="product-mobile-actions">
                        @feature('products.view')
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        @endfeature
                        @feature('products.edit')
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endfeature
                        @feature('products.delete')
                            <button onclick="deleteProduct({{ $product->id }})" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endfeature
                    </div>
                </div>
            @empty
                <div class="products-empty">
                    <i class="bi bi-box2-heart"></i>
                    <div>{{ __('messages.no_data') }}</div>
                </div>
            @endforelse

            @if($products ?? false)
                <div class="mt-2">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
    const canDownloadExports = @json(auth()->user()?->canViewMenuItem('downloads') ?? false);

    function filterProducts() {
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        const params = new URLSearchParams({ search, category });

        showLoading();
        fetch(`{{ route('products.filter') }}?${params.toString()}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('productsTable').innerHTML = html;
                hideLoading();
            })
            .catch(() => hideLoading());
    }

    function resetFilters() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = '';
        filterProducts();
    }

    function exportToExcel() {
        if (!canDownloadExports) {
            return;
        }

        showLoading();
        fetch('{{ route('products.export') }}')
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'products.xlsx';
                a.click();
                hideLoading();
            })
            .catch(() => hideLoading());
    }

    function deleteProduct(id) {
        if (confirm('{{ __('messages.confirm_delete') }}')) {
            showLoading();
            const url = '{{ url('products') }}/' + id;
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
                hideLoading();
            })
            .catch(error => {
                console.error('Error:', error);
                hideLoading();
            });
        }
    }

    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(filterProducts, 250);
    });
</script>
@endsection
