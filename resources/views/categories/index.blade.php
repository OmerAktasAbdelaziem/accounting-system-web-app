@extends('layouts.modern')

@section('title', __('messages.categories'))

@section('content')
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 style="font-weight: 900; color: #1a1a1a;">
            <i class="bi bi-tags" style="color: #ff8c00;"></i> {{ __('messages.categories_management') }}
        </h1>
        @feature('categories.create')
            <a href="{{ route('categories.create') }}" class="btn btn-primary-modern">
                <i class="bi bi-plus-circle"></i> {{ __('messages.add_category') }}
            </a>
        @endfeature
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <h6>{{ __('messages.total_categories') }}</h6>
            <div class="value">{{ $stats['total_categories'] }}</div>
            <div class="icon"><i class="bi bi-tags"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h6>{{ __('messages.total_products') }}</h6>
            <div class="value">{{ $stats['total_products'] }}</div>
            <div class="icon"><i class="bi bi-box-seam"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <h6>{{ __('messages.avg_products_per_category') }}</h6>
            <div class="value">{{ $stats['avg_products_per_category'] }}</div>
            <div class="icon"><i class="bi bi-graph-up"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card green">
            <h6>{{ __('messages.status') }}</h6>
            <div class="value" style="font-size: 20px; color: #27ae60;">{{ __('messages.active') }}</div>
            <div class="icon"><i class="bi bi-check-circle"></i></div>
        </div>
    </div>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="bi bi-list-ul"></i> {{ __('messages.all_categories') }}
        </h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('messages.category_name') }}</th>
                    <th>{{ __('messages.description') }}</th>
                    <th>
                        <i class="bi bi-box-seam"></i> {{ __('messages.total_products') }}
                    </th>
                    <th>
                        <i class="bi bi-currency-dollar"></i> {{ __('messages.stock_value') }}
                    </th>
                    <th>
                        <i class="bi bi-tag"></i> {{ __('messages.avg_price') }}
                    </th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories ?? [] as $category)
                    <tr>
                        <td>
                            <strong>{{ $category->name }}</strong>
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ Str::limit($category->description ?? __('messages.not_available'), 50) }}
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $category->total_products }} {{ __('messages.products') }}</span>
                        </td>
                        <td>
                            <strong>{{ $currencySymbol }}{{ number_format($category->total_stock_value ?? 0, 2) }}</strong>
                        </td>
                        <td>
                            {{ $currencySymbol }}{{ number_format($category->avg_price ?? 0, 2) }}
                        </td>
                        <td>
                            @feature('categories.view')
                                <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> {{ __('messages.view_details') }}
                                </a>
                            @endfeature

                            @feature('categories.edit')
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> {{ __('messages.edit') }}
                                </a>
                            @endfeature

                            @feature('categories.delete')
                                <button onclick="deleteCategory({{ $category->id }})" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> {{ __('messages.delete') }}
                                </button>
                            @endfeature
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            {{ __('messages.no_data') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($categories ?? false)
        <div class="card-footer">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection

@section('js')
<script>
    function deleteCategory(id) {
        if (confirm('{{ __("messages.confirm_delete") }}')) {
            showLoading();
            const url = '{{ url("categories") }}/' + id;
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
                } else {
                    alert(data.message || '{{ __('messages.error_deleting_category') }}');
                }
                hideLoading();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('{{ __('messages.error_deleting_category') }}');
                hideLoading();
            });
        }
    }
</script>
@endsection
