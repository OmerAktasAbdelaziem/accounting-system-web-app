@extends('layouts.modern')

@section('title', $product->name)

@section('content')
<div class="products-shell mb-4">
    <style>
        .products-shell { position: relative; isolation: isolate; }
        .products-hero { border-radius: 20px; padding: 18px; background: linear-gradient(135deg, rgba(17,24,39,0.96), rgba(31,41,55,0.92)); color:#fff; display:flex; justify-content:space-between; gap:12px; align-items:center }
        .products-hero .badge { background: rgba(255,255,255,0.06); color: #fff; padding:6px 10px; border-radius:999px; font-weight:800 }
        .products-hero-title { margin:0; font-size: clamp(1.6rem, 3.2vw, 2.4rem); font-weight:900; color:#fff }
        @media (max-width:768px) { .products-hero { flex-direction:column; align-items:stretch } .products-hero .actions { width:100%; display:flex; gap:8px; justify-content:flex-end } }
    </style>

    <x-section-hero badge="<i class='bi bi-box-seam'></i> {{ __('messages.product') }}"
                   title="<i class='bi bi-box-seam'></i> {{ $product->name }}"
                   description="{{ Illuminate\Support\Str::limit($product->description ?? __('messages.no_description'), 160) }}">
        <x-slot name="actions">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-2"><i class="bi bi-arrow-left"></i> {{ __('messages.back') }}</a>
            @if (\Illuminate\Support\Facades\Blade::check('feature', 'products.edit'))
                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
                </a>
            @endif
        </x-slot>

        <x-slot name="panel">
            <div class="products-hero-panel-top">
                <p class="products-hero-panel-title">{{ __('Summary') }}</p>
                <div class="products-hero-panel-value"><span>{{ $product->current_stock }}</span><small>{{ __('in stock') }}</small></div>
            </div>
            <div class="products-hero-panel-list">
                <div class="products-mini-metric"><div><span class="label">{{ __('Price') }}</span><span class="value">{{ $currencySymbol }}{{ number_format($product->selling_price, 2) }}</span></div><div class="tone"><i class="bi bi-tag"></i></div></div>
                <div class="products-mini-metric"><div><span class="label">{{ __('Status') }}</span><span class="value">{{ $product->is_active ? __('messages.active') : __('messages.inactive') }}</span></div><div class="tone"><i class="bi bi-info-circle"></i></div></div>
            </div>
        </x-slot>
    </x-section-hero>
</div>

<!-- Product Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ff8c00, #ffb347);">
                <i class="bi bi-tag"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.selling_price') }}</h6>
                <h3>{{ $currencySymbol }}{{ number_format($product->selling_price, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <i class="bi bi-box"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.current_stock') }}</h6>
                <h3>{{ $product->current_stock }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #5dade2);">
                <i class="bi bi-percent"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.profit_margin') }}</h6>
                @php
                    $margin = ($product->selling_price - $product->purchase_price) / $product->selling_price * 100;
                @endphp
                <h3>{{ number_format($margin, 1) }}%</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Product Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> {{ __('messages.product_information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.category') }}</label>
                        <p>
                            <a href="{{ route('categories.show', $product->category->id) }}" class="badge" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white; text-decoration: none; padding: 8px 12px; font-size: 14px;">
                                {{ $product->category->name }}
                            </a>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.status') }}</label>
                        <p>
                            @if($product->is_active)
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> {{ __('messages.active') }}</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle"></i> {{ __('messages.inactive') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.purchase_price') }}</label>
                        <p class="text-muted">{{ $currencySymbol }}{{ number_format($product->purchase_price, 2) }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.selling_price') }}</label>
                        <p class="text-success fw-bold">{{ $currencySymbol }}{{ number_format($product->selling_price, 2) }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">{{ __('messages.description') }}</label>
                        <p class="text-muted">{{ $product->description ?? __('messages.no_description') }}</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.created') }}</label>
                        <p class="text-muted">{{ $product->created_at->translatedFormat('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">{{ __('messages.last_updated') }}</label>
                        <p class="text-muted">{{ $product->updated_at->translatedFormat('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Calculation -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="mb-0"><i class="bi bi-calculator"></i> {{ __('messages.stock_value') }}</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>{{ __('messages.current_stock') }}</strong></td>
                        <td>{{ $product->current_stock }} {{ __('messages.units') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.selling_price') }}</strong></td>
                        <td>{{ $currencySymbol }}{{ number_format($product->selling_price, 2) }}</td>
                    </tr>
                    <tr class="table-info">
                        <td><strong>{{ __('messages.total_stock_value') }}</strong></td>
                        <td><strong>{{ $currencySymbol }}{{ number_format($product->current_stock * $product->selling_price, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('messages.purchase_price') }}</strong></td>
                        <td>{{ $currencySymbol }}{{ number_format($product->purchase_price, 2) }}</td>
                    </tr>
                    <tr class="table-warning">
                        <td><strong>{{ __('messages.cost_value') }}</strong></td>
                        <td><strong>{{ $currencySymbol }}{{ number_format($product->current_stock * $product->purchase_price, 2) }}</strong></td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>{{ __('messages.total_profit_potential') }}</strong></td>
                        <td><strong style="color: #27ae60;">{{ $currencySymbol }}{{ number_format($product->current_stock * ($product->selling_price - $product->purchase_price), 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Stock Status & Actions -->
    <div class="col-lg-4">
        <!-- Stock Status Alert -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="mb-0"><i class="bi bi-exclamation-circle"></i> {{ __('messages.stock_status') }}</h5>
            </div>
            <div class="card-body">
                @if($product->current_stock <= 0)
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> <strong>{{ __('messages.low_stock_warning') }}</strong>
                        <br><small>{{ __('messages.current_stock') }}: {{ $product->current_stock }}</small>
                    </div>
                @else
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle"></i> <strong>{{ __('messages.stock_level_good') }}</strong>
                        <br><small>{{ $product->current_stock }} {{ __('messages.units') }}</small>
                    </div>
                @endif

                <div class="progress mb-3">
                    @php
                        $stockPercentage = $product->current_stock > 0 ? 100 : 0;
                        $barColor = $product->current_stock <= 0 ? 'danger' : 'success';
                    @endphp
                    <div class="progress-bar bg-{{ $barColor }}" style="width: {{ min($stockPercentage, 100) }}%"></div>
                </div>

                <p class="text-muted small">
                    <strong>{{ __('messages.current_stock') }}:</strong> {{ $product->current_stock }} {{ __('messages.units') }}
                </p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> {{ __('messages.quick_actions') }}</h5>
            </div>
            <div class="card-body">
                @feature('products.edit')
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i> {{ __('messages.edit_product') }}
                    </a>
                @endfeature
                <button class="btn btn-info w-100 mb-2" data-bs-toggle="modal" data-bs-target="#adjustStockModal">
                    <i class="bi bi-arrow-left-right"></i> {{ __('messages.adjust_stock') }}
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-list"></i> {{ __('messages.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #3498db, #5dade2); color: white;">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right"></i> {{ __('messages.adjust_stock') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustStockForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.current_stock') }}</label>
                        <input type="text" class="form-control" value="{{ $product->current_stock }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.new_quantity') }} *</label>
                        <input type="number" name="new_quantity" class="form-control" min="0" required>
                        <small class="text-muted">{{ __('messages.enter_new_stock_quantity') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.reason') }} *</label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="{{ __('messages.adjust_stock_reason_placeholder') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> {{ __('messages.update_stock') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(document).ready(function() {
    $('#adjustStockForm').on('submit', function(e) {
        e.preventDefault();
        const newQuantity = $('input[name="new_quantity"]').val();
        const reason = $('textarea[name="reason"]').val();
        
        if(confirm('Are you sure you want to update the stock quantity?')) {
            $.ajax({
                url: '{{ route("products.adjustStock", $product->id) }}',
                type: 'POST',
                data: {
                    new_quantity: newQuantity,
                    reason: reason,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    alert('Stock updated successfully!');
                    location.reload();
                },
                error: function(error) {
                    alert('Error updating stock');
                }
            });
        }
    });
});
</script>
@endsection
