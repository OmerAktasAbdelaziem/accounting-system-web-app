@forelse($products ?? [] as $product)
    <tr>
        <td><strong>{{ $product->name }}</strong></td>
        <td>{{ $product->category->name ?? 'N/A' }}</td>
        <td>
            <span class="badge {{ $product->{{ __('current_stock') }} <= 0 ? 'bg-danger' : 'bg-success' }}">
                {{ $product->current_stock }}
            </span>
        </td>
        <td>{{ $currencySymbol }}{{ number_format($product->selling_price, 2) }}</td>
        <td>
            @if($product->is_active)
                <span class="badge bg-success">{{ __('messages.active') }}</span>
            {{ __('@else') }}
                <span class="badge bg-secondary">{{ __('messages.inactive') }}</span>
            @endif
        </td>
        <td>
            {{ __('@feature(\'products.view\')') }}
                <a href="{{ route('products.show', $product->{{ __('id) }}" class="btn btn-sm btn-info me-1" title="View Details">') }}
                    <i class="bi bi-eye"></i>
                </a>
            {{ __('@endfeature

            @feature(\'products.edit\')') }}
                <a href="{{ route('products.edit', $product->{{ __('id) }}" class="btn btn-sm btn-warning">') }}
                    <i class="bi bi-pencil"></i>
                </a>
            {{ __('@endfeature

            @feature(\'products.delete\')') }}
                <button onclick="deleteProduct({{ $product->{{ __('id }})" class="btn btn-sm btn-danger">') }}
                    <i class="bi bi-trash"></i>
                </button>
            {{ __('@endfeature') }}
        </td>
    </tr>
{{ __('@empty') }}
    <tr>
        <td colspan="6" class="text-center text-muted">{{ __('messages.no_data') }}</td>
    </tr>
@endforelse
