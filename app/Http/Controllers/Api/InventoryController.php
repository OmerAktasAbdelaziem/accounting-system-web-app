<?php

namespace App\Http\Controllers\Api;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    /**
     * Get all inventory movements with filters
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);
        $movementType = $request->get('movement_type');
        $productId = $request->get('product_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = InventoryMovement::with('product', 'user');

        if ($movementType) {
            $query->where('movement_type', $movementType);
        }

        if ($productId) {
            $query->where('product_id', $productId);
        }

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $movements = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $movements->items(),
            'pagination' => [
                'total' => $movements->total(),
                'per_page' => $movements->perPage(),
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
            ],
        ]);
    }

    /**
     * Record an inventory movement (incoming, outgoing, waste, etc.)
     */
    public function recordMovement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:incoming,outgoing,waste,return,adjustment,transfer_out,transfer_in',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'notes_ar' => 'nullable|string',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Update stock
        $movement = $product->updateStock(
            $validated['quantity'],
            $validated['movement_type'],
            ['type' => $validated['reference_type'] ?? null, 'id' => $validated['reference_id'] ?? null],
            $validated['notes'] ?? null,
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Inventory movement recorded successfully',
            'data' => $movement->load('product', 'user'),
        ], 201);
    }

    /**
     * Get product inventory history
     */
    public function productHistory(Product $product, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = $product->inventoryMovements();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $movements = $query->with('user')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'product' => $product,
            'data' => $movements->items(),
            'pagination' => [
                'total' => $movements->total(),
                'per_page' => $movements->perPage(),
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
            ],
        ]);
    }

    /**
     * Get inventory summary
     */
    public function summary(): JsonResponse
    {
        $totalProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->count();
        $totalStockValue = Product::where('is_active', true)
            ->selectRaw('SUM(current_stock * purchase_price) as total_value')
            ->first()
            ->total_value ?? 0;

        $recentMovements = InventoryMovement::with('product', 'user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'summary' => [
                'total_products' => $totalProducts,
                'low_stock_products' => $lowStockProducts,
                'total_stock_value' => $totalStockValue,
            ],
            'recent_movements' => $recentMovements,
        ]);
    }

    /**
     * Get movements by type
     */
    public function byType($type, Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);

        $movements = InventoryMovement::byType($type)
            ->with('product', 'user')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'movement_type' => $type,
            'data' => $movements->items(),
            'pagination' => [
                'total' => $movements->total(),
                'per_page' => $movements->perPage(),
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
            ],
        ]);
    }
}
