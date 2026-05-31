@extends('layouts.modern')

@section('title', $category->name . ' - ' . __('messages.category_details'))

@section('content')
<div class="categories-shell">
    <style>
        .categories-shell { position: relative; isolation: isolate; }
        .categories-hero { border-radius: 20px; padding: 18px; background: linear-gradient(135deg, rgba(17,24,39,0.96), rgba(31,41,55,0.92)); color:#fff; display:grid; grid-template-columns: 1fr 220px; gap:12px; align-items:center }
        .categories-hero-title { margin:0; font-size: clamp(1.6rem, 3.2vw, 2.4rem); font-weight:900; color:#fff }
        .categories-hero-badge { display:inline-flex; gap:8px; align-items:center; padding:6px 10px; border-radius:999px; background:rgba(255,255,255,0.06); color:#fff; font-weight:800; font-size:12px }
        .stat-grid { display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap:12px }
        @media (max-width: 992px) { .categories-hero { grid-template-columns:1fr } .stat-grid { grid-template-columns: 1fr; } }
    </style>

    <section class="mb-4">
        <div>
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> {{ __('messages.back_to_categories') }}</a>
            <x-section-hero :badge="'<i class=\"bi bi-tags\"></i> ' . __('messages.categories_management')"
                           title="<i class='bi bi-tags'></i> {{ $category->name }}"
                           :description="e($category->description ?? __('messages.no_description'))">
                <x-slot name="actions">
                    @feature('categories.edit')<a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> {{ __('messages.edit') }}</a>@endfeature
                </x-slot>

                <x-slot name="panel">
                    <div class="categories-hero-panel-top">
                        <p class="categories-hero-panel-title">Overview</p>
                        <div class="categories-hero-panel-value"><span>{{ $stats['total_products'] ?? 0 }}</span><small>products</small></div>
                    </div>
                    <div class="products-hero-panel-list mt-2">
                        <div class="products-mini-metric"><div><span class="label">Stock Value</span><span class="value">{{ $currencySymbol }}{{ number_format($stats['total_stock_value'] ?? 0, 2) }}</span></div><div class="tone"><i class="bi bi-currency-dollar"></i></div></div>
                        <div class="products-mini-metric"><div><span class="label">Avg Price</span><span class="value">{{ $currencySymbol }}{{ number_format($stats['avg_price'] ?? 0, 2) }}</span></div><div class="tone"><i class="bi bi-tag"></i></div></div>
                    </div>
                </x-slot>
            </x-section-hero>
        </div>

        <div class="stat-grid mb-3">
            <div class="stat-card">
                <h6>{{ __('messages.total_products') }}</h6>
                <div class="value">{{ $stats['total_products'] ?? 0 }}</div>
                <div class="icon"><i class="bi bi-box-seam"></i></div>
            </div>
            <div class="stat-card green">
                <h6>{{ __('messages.stock_value') }}</h6>
                <div class="value">{{ $currencySymbol }}{{ number_format($stats['total_stock_value'] ?? 0, 2) }}</div>
                <div class="icon"><i class="bi bi-currency-dollar"></i></div>
            </div>
            <div class="stat-card green">
                <h6>{{ __('messages.avg_price') }}</h6>
                <div class="value">{{ $currencySymbol }}{{ number_format($stats['avg_price'] ?? 0, 2) }}</div>
                <div class="icon"><i class="bi bi-tag"></i></div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #fff, #fff6ec); border-bottom:1px solid rgba(255,140,0,0.08);">
                        <h5 class="mb-0"><i class="bi bi-box-seam"></i> {{ __('messages.products_in_category') }}</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.product_name') }}</th>
                                    <th>{{ __('messages.price') }}</th>
                                    <th>{{ __('messages.quantity') }}</th>
                                    <th>{{ __('messages.stock_value') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th>{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products ?? [] as $product)
                                    <tr>
                                        <td><strong>{{ $product->name }}</strong></td>
                                        <td>{{ $currencySymbol }}{{ number_format($product->selling_price, 2) }}</td>
                                        <td><span class="badge {{ $product->current_stock <= 0 ? 'bg-danger' : 'bg-success' }}">{{ $product->current_stock }}</span></td>
                                        <td><strong>{{ $currencySymbol }}{{ number_format($product->selling_price * $product->current_stock, 2) }}</strong></td>
                                        <td>@if($product->is_active)<span class="badge bg-success">{{ __('messages.active') }}</span>@else<span class="badge bg-secondary">{{ __('messages.inactive') }}</span>@endif</td>
                                        <td>
                                            <div class="d-inline-flex gap-2">
                                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                                                @feature('products.edit')<a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>@endfeature
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-4">{{ __('messages.no_products_in_category') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($products ?? false)
                        <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">{{ $products->links() }}</div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('messages.category_overview') }}</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>{{ __('messages.category_name') }}:</strong> {{ $category->name }}</p>
                        <p><strong>{{ __('messages.products') }}:</strong> {{ $stats['total_products'] ?? 0 }}</p>
                        <p><strong>{{ __('messages.total_stock_value') }}:</strong> {{ $currencySymbol }}{{ number_format($stats['total_stock_value'] ?? 0, 2) }}</p>
                        <p><strong>{{ __('messages.avg_price') }}:</strong> {{ $currencySymbol }}{{ number_format($stats['avg_price'] ?? 0, 2) }}</p>
                        <hr>
                        <div class="d-grid gap-2">
                            @feature('categories.edit')<a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning w-100"><i class="bi bi-pencil"></i> {{ __('messages.edit_category') }}</a>@endfeature
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-list"></i> {{ __('messages.back_to_products') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
    function deleteProduct(id) {
        if (!confirm('{{ __('messages.confirm_delete') }}')) return;
        showLoading();
        fetch(`{{ url('products') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json' } })
            .then(r=>r.json()).then(data=>{ if (data.success) location.reload(); hideLoading(); }).catch(()=>{ hideLoading(); });
    }
</script>
@endsection
