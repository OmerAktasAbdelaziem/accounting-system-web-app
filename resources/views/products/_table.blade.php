@forelse($products ?? [] as $product)
    <tr>
        <td><strong>{{ $product->name }}</strong></td>
        <td>{{ $product->category->name ?? 'N/A' }}</td>
        <td><code>{{ $product->sku }}</code></td>
        <td>
            <span class="badge {{ $product->current_stock <= $product->min_stock ? 'bg-danger' : 'bg-success' }}">
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
        <td colspan="7" class="text-center text-muted">{{ __('messages.no_data') }}</td>
    </tr>
@endforelse
