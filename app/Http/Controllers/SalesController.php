<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSale;
use App\Models\Product;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::orderBy('name')->get();
        $products = Product::with('category')->orderBy('name')->get();

        $sales = EmployeeSale::with(['employee', 'product.category'])
            ->when($request->filled('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->integer('employee_id'));
            })
            ->when($request->filled('product_id'), function ($query) use ($request) {
                $query->where('product_id', $request->integer('product_id'));
            })
            ->when($request->filled('from_date'), function ($query) use ($request) {
                $query->whereDate('sale_date', '>=', $request->input('from_date'));
            })
            ->when($request->filled('to_date'), function ($query) use ($request) {
                $query->whereDate('sale_date', '<=', $request->input('to_date'));
            })
            ->latest('sale_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'count' => EmployeeSale::count(),
            'quantity' => (float) EmployeeSale::sum('quantity'),
            'total' => (float) EmployeeSale::sum('total_amount'),
        ];

        return view('sales.index', compact('employees', 'products', 'sales', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0.01',
            'sale_date' => 'required|date|before_or_equal:today',
            'sale_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'notes_ar' => 'nullable|string|max:500',
        ]);

        $validated['total_amount'] = (float) $validated['quantity'] * (float) $validated['unit_price'];
        EmployeeSale::create($validated);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }
}