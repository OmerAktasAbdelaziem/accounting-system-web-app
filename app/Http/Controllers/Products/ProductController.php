<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $categoryId = $request->input('category');

        $products = Product::with('category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->paginate(6)
            ->withQueryString();

        $categories = Category::all();
        return view('products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function create()
    {
        $product = null;
        $categories = Category::all();
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = request()->input('branch_ids', []);
        return view('products.form', compact('product', 'categories', 'branches', 'selectedBranchIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'selling_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $product = Product::create($validated);
        $product->syncBranches($validated['branch_ids'] ?? []);
        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = $product->branches()->pluck('branches.id')->all();
        return view('products.form', compact('product', 'categories', 'branches', 'selectedBranchIds'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'selling_price' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $product->update($validated);
        $product->syncBranches($validated['branch_ids'] ?? []);
        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['success' => true]);
    }

    public function filter(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(20);
        return view('products._table', compact('products'));
    }

    public function export()
    {
        return Excel::download(new ProductsExport, 'products.xlsx');
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'new_quantity' => 'required|integer|min:0',
            'reason' => 'required|string',
        ]);

        $oldQuantity = $product->current_stock;
        $product->update(['current_stock' => $validated['new_quantity']]);

        return response()->json([
            'success' => true,
            'message' => 'Stock adjusted successfully',
            'old_quantity' => $oldQuantity,
            'new_quantity' => $validated['new_quantity']
        ]);
    }
}
