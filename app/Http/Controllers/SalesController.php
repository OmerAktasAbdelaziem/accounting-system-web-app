<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\EmployeeSale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $sales = EmployeeSale::with('branch')
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->integer('branch_id'));
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
            'gross_total' => (float) EmployeeSale::sum('total_amount'),
            'spent_total' => (float) EmployeeSale::sum('spent_amount'),
        ];
        $stats['net_total'] = $stats['gross_total'] - $stats['spent_total'];

        $branches = Branch::orderBy('name')->get();

        return view('sales.index', compact('sales', 'stats', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'branch_id' => 'required|exists:branches,id',
            'total_amount' => 'required|numeric|min:0.01',
            'spent_amount' => 'nullable|numeric|min:0',
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
            'spent_amount' => (float) ($validated['spent_amount'] ?? 0),
            'sale_date' => $validated['sale_date'],
            'branch_id' => $validated['branch_id'],
            'sale_reference' => null,
            'notes' => !empty($validated['product_sold']) ? implode('\n', array_filter($validated['product_sold'])) : null,
            'notes_ar' => null,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }
}