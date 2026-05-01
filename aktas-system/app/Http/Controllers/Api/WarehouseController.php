<?php

namespace App\Http\Controllers\Api;

use App\Models\Warehouse;
use App\Models\WarehouseInventory;
use App\Models\WarehouseTransfer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class WarehouseController extends Controller
{
    /**
     * Get all warehouses
     */
    public function index(Request $request): JsonResponse
    {
        $active = $request->get('active', true);

        $query = Warehouse::query();

        if ($active) {
            $query->where('is_active', true);
        }

        $warehouses = $query->withCount('inventory')->get();

        return response()->json([
            'success' => true,
            'data' => $warehouses,
        ]);
    }

    /**
     * Get a single warehouse with inventory
     */
    public function show(Warehouse $warehouse): JsonResponse
    {
        $warehouse->load(['inventory.product']);

        return response()->json([
            'success' => true,
            'data' => $warehouse,
        ]);
    }

    /**
     * Create a new warehouse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $warehouse = Warehouse::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Warehouse created successfully',
            'data' => $warehouse,
        ], 201);
    }

    /**
     * Update a warehouse
     */
    public function update(Request $request, Warehouse $warehouse): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'name_ar' => 'string|max:255',
            'location' => 'nullable|string|max:255',
            'location_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $warehouse->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Warehouse updated successfully',
            'data' => $warehouse,
        ]);
    }

    /**
     * Get warehouse inventory
     */
    public function inventory(Warehouse $warehouse): JsonResponse
    {
        $inventory = $warehouse->inventory()
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name_ar ?? $item->product->name,
                    'quantity' => $item->quantity,
                    'reserved_quantity' => $item->reserved_quantity,
                    'available_quantity' => $item->available_quantity,
                ];
            });

        return response()->json([
            'success' => true,
            'warehouse' => $warehouse,
            'inventory' => $inventory,
        ]);
    }

    /**
     * Transfer inventory between warehouses
     */
    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|integer|min:1',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string',
            'notes_ar' => 'nullable|string',
        ]);

        // Check source warehouse has enough inventory
        $sourceInventory = WarehouseInventory::where('warehouse_id', $validated['from_warehouse_id'])
            ->where('product_id', $validated['product_id'])
            ->first();

        if (!$sourceInventory || $sourceInventory->available_quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient inventory in source warehouse',
            ], 400);
        }

        // Create transfer record
        $transfer = WarehouseTransfer::create([
            'product_id' => $validated['product_id'],
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id' => $validated['to_warehouse_id'],
            'quantity' => $validated['quantity'],
            'transfer_date' => $validated['transfer_date'],
            'created_by' => auth()->id(),
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'notes_ar' => $validated['notes_ar'] ?? null,
        ]);

        // Reserve inventory in source warehouse
        $sourceInventory->increment('reserved_quantity', $validated['quantity']);

        return response()->json([
            'success' => true,
            'message' => 'Transfer initiated successfully',
            'data' => $transfer->load('product', 'fromWarehouse', 'toWarehouse'),
        ], 201);
    }

    /**
     * Complete a warehouse transfer
     */
    public function completeTransfer(WarehouseTransfer $transfer): JsonResponse
    {
        if (!$transfer->complete()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot complete transfer. Check transfer status and inventory.',
            ], 400);
        }

        // Release reserved inventory from source warehouse
        $sourceInventory = WarehouseInventory::where('warehouse_id', $transfer->from_warehouse_id)
            ->where('product_id', $transfer->product_id)
            ->first();

        if ($sourceInventory) {
            $sourceInventory->decrement('reserved_quantity', $transfer->quantity);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transfer completed successfully',
            'data' => $transfer,
        ]);
    }

    /**
     * Reject a warehouse transfer
     */
    public function rejectTransfer(WarehouseTransfer $transfer): JsonResponse
    {
        if (!$transfer->reject()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot reject transfer at this status',
            ], 400);
        }

        // Release reserved inventory
        $sourceInventory = WarehouseInventory::where('warehouse_id', $transfer->from_warehouse_id)
            ->where('product_id', $transfer->product_id)
            ->first();

        if ($sourceInventory) {
            $sourceInventory->decrement('reserved_quantity', $transfer->quantity);
        }

        return response()->json([
            'success' => true,
            'message' => 'Transfer rejected successfully',
            'data' => $transfer,
        ]);
    }

    /**
     * Get transfer history
     */
    public function transferHistory(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);
        $warehouseId = $request->get('warehouse_id');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = WarehouseTransfer::with('product', 'fromWarehouse', 'toWarehouse', 'createdBy');

        if ($warehouseId) {
            $query->where(function ($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId)
                    ->orWhere('to_warehouse_id', $warehouseId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $transfers = $query->orderByDesc('transfer_date')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $transfers->items(),
            'pagination' => [
                'total' => $transfers->total(),
                'per_page' => $transfers->perPage(),
                'current_page' => $transfers->currentPage(),
                'last_page' => $transfers->lastPage(),
            ],
        ]);
    }
}
