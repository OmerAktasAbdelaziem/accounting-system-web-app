@extends('layouts.modern')

@section('title', __('messages.storage_items'))

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-box2-heart"></i> {{ $storage->name }} - {{ __('messages.storage_items') }}</h1>
        <p class="text-muted">{{ $storage->location }} | {{ __('messages.storage_type') }}: <strong>{{ __('messages.' . $storage->storage_type) }}</strong></p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('storages.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
        <a href="{{ route('storages.transferHistory', $storage->id) }}" class="btn btn-info me-2">
            <i class="bi bi-clock-history"></i> {{ __('messages.transfer_history') }}
        </a>
        <button class="btn btn-primary-modern" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus-circle"></i> {{ __('messages.add_item') }}
        </button>
    </div>
</div>

<!-- Storage Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ff8c00, #ffb347);">
                <i class="bi bi-boxes"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.total_items') }}</h6>
                <h3>{{ $items->total() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #2ecc71);">
                <i class="bi bi-pie-chart"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.capacity_usage') }}</h6>
                <h3>{{ $storage->capacity ? round((($items->total() / $storage->capacity) * 100), 1) : __('messages.not_available') }}%</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #1a1a1a, #333);">
                <i class="bi bi-database"></i>
            </div>
            <div class="stat-content">
                <h6>{{ __('messages.storage_capacity') }}</h6>
                <h3>{{ $storage->capacity ?? __('messages.unlimited') }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Items Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-header-modern">
                <tr>
                    <th><i class="bi bi-box"></i> {{ __('messages.product_name') }}</th>
                    <th>{{ __('messages.sku') }}</th>
                    <th><i class="bi bi-hash"></i> {{ __('messages.quantity') }}</th>
                    <th>{{ __('messages.location_code') }}</th>
                    <th>{{ __('messages.entry_date') }}</th>
                    <th>{{ __('messages.expiry_date') }}</th>
                    <th>{{ __('messages.notes') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td><strong>{{ $item->product->name ?? __('messages.not_available') }}</strong></td>
                        <td><code>{{ $item->product->sku ?? __('messages.not_available') }}</code></td>
                        <td>
                            <span class="badge" style="background: linear-gradient(135deg, #ff8c00, #ffb347);">
                                {{ $item->quantity }}
                            </span>
                        </td>
                        <td>{{ $item->location_code ?? __('messages.not_available') }}</td>
                        <td>{{ $item->entry_date ? $item->entry_date->format('M d, Y') : __('messages.not_available') }}</td>
                        <td>
                            @if($item->expiry_date)
                                <span class="@if($item->expiry_date->isPast()) text-danger @else text-success @endif">
                                    {{ $item->expiry_date->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-muted">{{ __('messages.not_available') }}</span>
                            @endif
                        </td>
                        <td>{{ Str::limit($item->notes, 30, '...') ?? __('messages.not_available') }}</td>
                        <td>
                            <button class="btn btn-sm btn-success transfer-item-btn" data-id="{{ $item->id }}" data-product-id="{{ $item->product_id }}" data-product-name="{{ $item->product->name }}" data-quantity="{{ $item->quantity }}" data-bs-toggle="modal" data-bs-target="#transferItemModal" title="{{ __('messages.transfer') }}">
                                <i class="bi bi-arrow-left-right"></i>
                            </button>
                            <button class="btn btn-sm btn-info edit-item-btn" data-id="{{ $item->id }}" data-bs-toggle="modal" data-bs-target="#editItemModal" title="{{ __('messages.edit') }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-item-btn" data-id="{{ $item->id }}" title="{{ __('messages.delete') }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mt-2">{{ __('messages.no_items_in_storage') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($items->hasPages())
    <div class="card-footer text-muted">
        {{ $items->render('pagination::bootstrap-4') }}
    </div>
    @endif
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1a1a1a, #333); color: white;">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> {{ __('messages.add_item') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addItemForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.product') }} *</label>
                        <select name="product_id" id="productId" class="form-select" required>
                            <option value="">{{ __('messages.select_product') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.quantity') }} *</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.location_code') }}</label>
                        <input type="text" name="location_code" class="form-control" placeholder="{{ __('messages.location_code_placeholder') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.entry_date') }}</label>
                        <input type="date" name="entry_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.expiry_date') }}</label>
                        <input type="date" name="expiry_date" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.notes') }}</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('messages.optional_notes') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary-modern"><i class="bi bi-save"></i> {{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #ff8c00, #ffb347); color: white;">
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> {{ __('messages.edit_item') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editItemForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.quantity') }} *</label>
                        <input type="number" name="quantity" id="editQuantity" class="form-control" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.location_code') }}</label>
                        <input type="text" name="location_code" id="editLocationCode" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.entry_date') }}</label>
                        <input type="date" name="entry_date" id="editEntryDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.expiry_date') }}</label>
                        <input type="date" name="expiry_date" id="editExpiryDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.notes') }}</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-warning-modern"><i class="bi bi-save"></i> {{ __('messages.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Item Modal -->
<div class="modal fade" id="transferItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white;">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right"></i> {{ __('messages.transfer_product') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="transferItemForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>{{ __('messages.product_name') }}</strong></label>
                        <input type="text" id="transferProductName" class="form-control" readonly>
                        <input type="hidden" id="transferProductId" name="product_id">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.current_quantity') }}</label>
                                <input type="text" id="transferCurrentQty" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.transfer_quantity') }} *</label>
                                <input type="number" name="quantity" id="transferQuantity" class="form-control" min="1" required>
                                <small class="text-danger" id="quantityError"></small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.to_storage') }} *</label>
                        <select name="to_storage_id" id="toStorageId" class="form-select" required>
                            <option value="">{{ __('messages.select') }}...</option>
                            @php
                                $allStorages = \App\Models\Storage::where('is_active', true)->get();
                            @endphp
                            @foreach($allStorages as $s)
                                @if($s->id != $storage->id)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->location }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.transfer_description') }}</label>
                        <textarea name="description" id="transferDescription" class="form-control" rows="3" placeholder="{{ __('messages.transfer_reason_placeholder') }}"></textarea>
                    </div>

                    <input type="hidden" name="from_storage_id" value="{{ $storage->id }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> {{ __('messages.confirm_transfer') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
$(document).ready(function() {
    // Add Item
    $('#addItemForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("storages.items", $storage->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                location.reload();
            },
            error: function(error) {
                alert('{{ __('messages.error_adding_item') }}');
            }
        });
    });

    // Edit Item - Load data
    $('.edit-item-btn').on('click', function() {
        const itemId = $(this).data('id');
        const row = $(this).closest('tr');
        
        $('#editQuantity').val(row.find('td:eq(2)').text().trim());
        $('#editLocationCode').val(row.find('td:eq(3)').text().trim());
        
        $('#editItemForm').attr('action', `/storages/items/${itemId}`);
    });

    // Edit Item Submit
    $('#editItemForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = $(this).attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                location.reload();
            },
            error: function(error) {
                alert('{{ __('messages.error_updating_item') }}');
            }
        });
    });

    // Transfer Item - Populate modal
    $('.transfer-item-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const quantity = $(this).data('quantity');

        $('#transferProductId').val(productId);
        $('#transferProductName').val(productName);
        $('#transferCurrentQty').val(quantity);
        $('#transferQuantity').val('');
        $('#toStorageId').val('');
        $('#transferDescription').val('');
        $('#quantityError').text('');
    });

    // Validate transfer quantity
    $('#transferQuantity').on('change', function() {
        const maxQty = parseInt($('#transferCurrentQty').val());
        const inputQty = parseInt($(this).val());

        if (inputQty > maxQty) {
            $('#quantityError').text('{{ __("messages.insufficient_quantity") }}');
            $(this).val('');
        } else {
            $('#quantityError').text('');
        }
    });

    // Submit Transfer Form
    $('#transferItemForm').on('submit', function(e) {
        e.preventDefault();

        const maxQty = parseInt($('#transferCurrentQty').val());
        const inputQty = parseInt($('#transferQuantity').val());

        if (inputQty > maxQty) {
            $('#quantityError').text('{{ __("messages.insufficient_quantity") }}');
            return;
        }

        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("storages.transfer", $storage->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('{{ __("messages.transfer_successful") }}');
                    location.reload();
                } else {
                    alert('{{ __('messages.error') }}: ' + response.message);
                }
            },
            error: function(error) {
                const message = error.responseJSON?.message || '{{ __('messages.error_transferring_product') }}';
                alert(message);
            }
        });
    });

    // Delete Item
    $('.delete-item-btn').on('click', function() {
        if(confirm('{{ __("messages.confirm_delete") }}')) {
            const itemId = $(this).data('id');
            
            $.ajax({
                url: `/storages/items/${itemId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    location.reload();
                },
                error: function() {
                    alert('{{ __('messages.error_deleting_item') }}');
                }
            });
        }
    });
});
</script>
@endsection
