@extends('layouts.modern')

@section('title', __('messages.products'))

@section('content')
<div class="mb-4">
    <style>
        @media (max-width: 768px) {
            .page-header > .d-flex,
            .d-flex.justify-content-between.align-items-center.mb-4 {
                flex-direction: column;
                align-items: stretch !important;
                gap: 12px;
            }

            .products-mobile-hero > div:last-child {
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
                width: 100%;
            }

            .products-mobile-hero > div:last-child .btn,
            .page-header .btn,
            .page-header a,
            .page-header button,
            .page-header input,
            .page-header select {
                width: 100%;
            }

            .products-mobile-hero > div:last-child .btn {
                padding: .55rem .8rem;
                font-size: .85rem;
                min-height: 42px;
                border-radius: 14px;
            }

            .products-mobile-hero > div:last-child .btn i {
                font-size: .9rem;
            }

            .products-mobile-hero > div:last-child .btn + .btn {
                margin-top: 0;
            }

            .card {
                border-radius: 16px;
            }

            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table {
                min-width: 760px;
            }

            .btn-group {
                flex-wrap: wrap;
                gap: 6px;
            }

            .btn-group .btn {
                flex: 1 1 auto;
            }
        }

        @media (max-width: 576px) {
            .page-title {
                font-size: 22px;
            }

            .page-title i {
                font-size: 22px;
            }

            .table {
                min-width: 640px;
            }
        }

        @media (max-width: 768px) {
            .products-mobile-hero {
                background: linear-gradient(160deg, #ffffff 0%, #fff8ef 56%, #fff1df 100%);
                border: 1px solid rgba(255, 140, 0, 0.12);
                border-radius: 24px;
                padding: 16px;
                box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
                margin-bottom: 16px;
            }

            .products-desktop-table {
                display: none;
            }

            .products-mobile-list {
                display: grid;
                gap: 12px;
            }

            .product-mobile-card {
                background: rgba(255,255,255,0.96);
                border: 1px solid rgba(226,232,240,0.9);
                border-radius: 20px;
                padding: 14px;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
            }

            .product-mobile-card .head {
                display: flex;
                justify-content: space-between;
                gap: 10px;
                align-items: flex-start;
                margin-bottom: 10px;
            }

            .product-mobile-card .head strong {
                font-size: 14px;
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
                border-radius: 14px;
                padding: 10px;
            }

            .product-mobile-chip span {
                display: block;
                font-size: 11px;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-bottom: 3px;
            }

            .product-mobile-actions {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 8px;
            }

            .product-mobile-actions .btn {
                width: 100%;
                border-radius: 14px;
            }
        }
    </style>

    <div class="products-mobile-hero d-flex justify-content-between align-items-center">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-box-seam" style="color: #ff8c00;"></i> {{ __('messages.products_management') }}
        </h1>
        <div class="products-mobile-actions">
            @feature('products.create')
                <a href="{{ route('products.create') }}" class="btn btn-primary-modern">
                    <i class="bi bi-plus-circle"></i> {{ __('messages.add_product') }}
                </a>
            @endfeature

            @feature('downloads')
                <button class="btn btn-success-modern" onclick="exportToExcel()">
                    <i class="bi bi-download"></i> {{ __('messages.export') }}
                </button>
            @endfeature
        </div>
    </div>
</div>

<div class="card products-desktop-table">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <input type="text" class="form-control" id="searchInput" placeholder="{{ __('messages.search') }}...">
            </div>
            <div class="col-md-3">
                <select class="form-select" id="categoryFilter">
                    <option value="">{{ __('messages.all_categories') }}</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-secondary w-100" onclick="filterProducts()">
                    <i class="bi bi-funnel"></i> {{ __('messages.filter') }}
                </button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
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
                        <td><strong>{{ $product->name }}</strong></td>
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
                            @feature('products.view')
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info me-1" title="View Details">
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
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($products ?? false)
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    @endif
</div>

<div class="products-mobile-list d-md-none">
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
                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                @endfeature
                @feature('products.edit')
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                @endfeature
                @feature('products.delete')
                    <button onclick="deleteProduct({{ $product->id }})" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                @endfeature
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-4">{{ __('messages.no_data') }}</div>
    @endforelse
    @if($products ?? false)
        <div class="mt-2">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection

@section('js')
<script>
    const canDownloadExports = @json(auth()->user()?->canViewMenuItem('downloads') ?? false);

    function filterProducts() {
        const search = document.getElementById('searchInput').value;
        const category = document.getElementById('categoryFilter').value;
        
        showLoading();
        fetch(`{{ route('products.filter') }}?search=${search}&category=${category}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('productsTable').innerHTML = html;
                hideLoading();
            });
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
            });
    }

    function deleteProduct(id) {
        if (confirm('{{ __("messages.confirm_delete") }}')) {
            showLoading();
            const url = '{{ url("products") }}/' + id;
            fetch(url, {
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
                hideLoading();
            })
            .catch(error => {
                console.error('Error:', error);
                hideLoading();
            });
        }
    }

    // Real-time search
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(filterProducts, 300);
    });
</script>
@endsection
