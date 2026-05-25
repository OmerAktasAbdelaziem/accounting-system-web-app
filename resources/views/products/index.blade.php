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

            .page-header .btn,
            .page-header a,
            .page-header button,
            .page-header input,
            .page-header select {
                width: 100%;
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
    </style>

    <div class="d-flex justify-content-between align-items-center">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-box-seam" style="color: #ff8c00;"></i> {{ __('messages.products_management') }}
        </h1>
        <div>
            <a href="{{ route('products.create') }}" class="btn btn-primary-modern me-2">
                <i class="bi bi-plus-circle"></i> {{ __('messages.add_product') }}
            </a>
            <button class="btn btn-success-modern" onclick="exportToExcel()">
                <i class="bi bi-download"></i> {{ __('messages.export') }}
            </button>
        </div>
    </div>
</div>

<div class="card">
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
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info me-1" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deleteProduct({{ $product->id }})" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
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
@endsection

@section('js')
<script>
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
