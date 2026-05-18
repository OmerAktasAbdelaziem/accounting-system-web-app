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
        $sales = EmployeeSale::when($request->filled('from_date'), function ($query) use ($request) {
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
            'total' => (float) EmployeeSale::sum('total_amount'),
        ];

        return view('sales.index', compact('sales', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'total_amount' => 'required|numeric|min:0.01',
            'product_sold' => 'nullable|array',
            'product_sold.*' => 'nullable|string|max:255',
        ]);

        // Create a base sale record with nullable employee/product for simplified entry
        $sale = EmployeeSale::create([
            'employee_id' => null,
            'product_id' => null,
            'quantity' => 1,
            'unit_price' => $validated['total_amount'],
            'total_amount' => (float) $validated['total_amount'],
            'sale_date' => $validated['sale_date'],
            'sale_reference' => null,
            'notes' => !empty($validated['product_sold']) ? implode('\n', array_filter($validated['product_sold'])) : null,
            'notes_ar' => null,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }
}