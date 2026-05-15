@extends('layouts.modern')

@section('title', $category->name . ' - ' . __('messages.category_details'))

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left"></i> {{ __('messages.back_to_categories') }}
            </a>
            <h1 style="font-weight: 900; color: #1a1a1a;">
                <i class="bi bi-tags" style="color: #ff8c00;"></i> {{ $category->name }}
            </h1>
            <p class="text-muted">{{ $category->description ?? __('messages.no_description') }}</p>
        </div>
        <div>
            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
            </a>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>{{ __('messages.total_products') }}</h6>
            <div class="value">{{ $stats['total_products'] }}</div>
            <div class="icon"><i class="bi bi-box-seam"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <h6>{{ __('messages.stock_value') }}</h6>
            <div class="value">{{ currencySymbol() }}{{ number_format($stats['total_stock_value'] ?? 0, 0) }}</div>
            <div class="icon"><i class="bi bi-currency-dollar"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <h6>{{ __('messages.avg_price') }}</h6>
            <div class="value">{{ currencySymbol() }}{{ number_format($stats['avg_price'] ?? 0, 2) }}</div>
            <div class="icon"><i class="bi bi-tag"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>{{ __('messages.total_quantity') }}</h6>
            <div class="value">{{ $stats['total_stock_qty'] ?? 0 }}</div>
            <div class="icon"><i class="bi bi-stack"></i></div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-box-seam"></i> {{ __('messages.products_in_category') }}
        </h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.product_name') }}</th>
                    <th>{{ __('messages.sku') }}</th>
                    <th>
                        <i class="bi bi-tag"></i> {{ __('messages.price') }}
                    </th>
                    <th>
                        <i class="bi bi-stack"></i> {{ __('messages.quantity') }}
                    </th>
                    <th>
                        <i class="bi bi-currency-dollar"></i> {{ __('messages.stock_value') }}
                    </th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products ?? [] as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->name }}</strong>
                        </td>
                        <td>
                            <code>{{ $product->sku }}</code>
                        </td>
                        <td>
                            {{ currencySymbol() }}{{ number_format($product->selling_price, 2) }}
                        </td>
                        <td>
                            <span class="badge {{ $product->current_stock <= $product->min_stock ? 'bg-danger' : 'bg-success' }}">
                                {{ $product->current_stock }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ currencySymbol() }}{{ number_format($product->selling_price * $product->current_stock, 2) }}</strong>
                        </td>
                        <td>
                            @if($product->is_active)
                                <span class="badge bg-success">{{ __('messages.active') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
                            @endif
                        </td>
                        <td>
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
                        <td colspan="7" class="text-center text-muted py-4">
                            {{ __('messages.no_products_in_category') }}
                        </td>
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
</script>
@endsection
