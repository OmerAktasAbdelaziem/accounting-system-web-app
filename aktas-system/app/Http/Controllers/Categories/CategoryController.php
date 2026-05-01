<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['products'])
            ->with(['products' => function ($query) {
                $query->select('id', 'category_id', 'selling_price', 'current_stock');
            }])
            ->paginate(20);

        // Calculate sales metrics for each category
        foreach ($categories as $category) {
            $category->total_products = $category->products_count;
            $category->total_stock_value = $category->products->sum(function ($product) {
                return $product->selling_price * $product->current_stock;
            });
            $category->avg_price = $category->products->count() > 0 
                ? $category->products->avg('selling_price')
                : 0;
        }

        $stats = [
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
            'avg_products_per_category' => Category::count() > 0 
                ? round(Product::count() / Category::count(), 2)
                : 0,
        ];

        return view('categories.index', compact('categories', 'stats'));
    }

    public function create()
    {
        $category = null;
        return view('categories.form', compact('category'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        Category::create($validated);
        return redirect()->route('categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        return view('categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);
        return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot delete category with products. Please delete or reassign products first.'
            ]);
        }

        $category->delete();
        return response()->json(['success' => true]);
    }

    public function show(Category $category)
    {
        $products = $category->products()->paginate(20);
        $stats = [
            'total_products' => $category->products()->count(),
            'total_stock_value' => $category->products()->sum(DB::raw('selling_price * current_stock')),
            'avg_price' => $category->products()->avg('selling_price'),
            'total_stock_qty' => $category->products()->sum('current_stock'),
        ];

        return view('categories.show', compact('category', 'products', 'stats'));
    }
}
