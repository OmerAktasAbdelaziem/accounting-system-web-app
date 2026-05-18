<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Get all products with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $isActive = $request->get('is_active');

        $query = Product::with('category');

        if ($search) {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('name_ar', 'like', '%' . $search . '%');
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        $products = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    /**
     * Get a single product
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $product->load('category', 'inventoryMovements'),
        ]);
    }

    /**
     * Create a new product
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'barcode' => 'nullable|unique:products|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'unit' => 'required|string',
            'unit_ar' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'profit_margin' => 'nullable|numeric',
            'is_active' => 'boolean',
            'track_inventory' => 'boolean',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }

    /**
     * Update a product
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'barcode' => 'nullable|unique:products,barcode,' . $product->id . '|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'unit' => 'string',
            'unit_ar' => 'nullable|string',
            'purchase_price' => 'numeric|min:0',
            'selling_price' => 'numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'profit_margin' => 'nullable|numeric',
            'is_active' => 'boolean',
            'track_inventory' => 'boolean',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Delete a product
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Get low stock products
     */
    public function lowStock(): JsonResponse
    {
        $products = Product::where('is_active', true)
            ->where('current_stock', '<=', 0)
            ->with('category')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'count' => $products->count(),
        ]);
    }

    /**
     * Get products by category
     */
    public function byCategory(Category $category): JsonResponse
    {
        $products = $category->products()
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'category' => $category,
        ]);
    }
}
