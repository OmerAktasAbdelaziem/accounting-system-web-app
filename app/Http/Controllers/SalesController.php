<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeSale;
use App\Models\EmployeeSaleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $sales = EmployeeSale::with(['branch', 'employee', 'employeeSaleDetails.employee'])
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
            ->paginate(6)
            ->withQueryString();

        $stats = [
            'count' => EmployeeSale::count(),
            'gross_total' => (float) EmployeeSale::sum('total_amount'),
            'spent_total' => (float) EmployeeSale::sum('spent_amount'),
        ];
        $stats['net_total'] = $stats['gross_total'] - $stats['spent_total'];

        $branches = Branch::orderBy('name')->get();
        $employees = Employee::active()->orderBy('name')->get();

        return view('sales.index', compact('sales', 'stats', 'branches', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'branch_id' => 'required|exists:branches,id',
            'total_amount' => 'required|numeric|min:0.01',
            'spent_amount' => 'nullable|numeric|min:0',
            'employee_sales' => 'required|array|min:1',
            'employee_sales.*.employee_id' => 'required|exists:employees,id',
            'employee_sales.*.description' => 'nullable|string|max:1000',
            'product_sold' => 'nullable|array',
            'product_sold.*' => 'nullable|string|max:255',
        ]);

        $employeeSales = collect($validated['employee_sales'])
            ->filter(fn ($row) => !empty($row['employee_id']))
            ->values();

        if ($employeeSales->isEmpty()) {
            return back()
                ->withErrors(['employee_sales' => 'Select at least one employee.'])
                ->withInput();
        }

        $sale = DB::transaction(function () use ($validated, $employeeSales) {
            // Keep a primary employee on the sale for compatibility with existing employee views.
            $primaryEmployeeId = (int) $employeeSales->first()['employee_id'];

            $sale = EmployeeSale::create([
                'employee_id' => $primaryEmployeeId,
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

            foreach ($employeeSales as $row) {
                $sale->employeeSaleDetails()->create([
                    'employee_id' => (int) $row['employee_id'],
                    'description' => $row['description'] ?? null,
                ]);
            }

            return $sale;
        });

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(EmployeeSale $sale)
    {
        $branches = Branch::orderBy('name')->get();
        $employees = Employee::active()->orderBy('name')->get();
        return view('sales.edit', compact('sale', 'branches'));
    }

    public function update(Request $request, EmployeeSale $sale)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'branch_id' => 'required|exists:branches,id',
            'total_amount' => 'required|numeric|min:0.01',
            'spent_amount' => 'nullable|numeric|min:0',
            'employee_sales' => 'required|array|min:1',
            'employee_sales.*.employee_id' => 'required|exists:employees,id',
            'employee_sales.*.description' => 'nullable|string|max:1000',
            'product_sold_text' => 'nullable|string|max:2000',
            'product_sold' => 'nullable|array',
            'product_sold.*' => 'nullable|string|max:255',
        ]);

        $employeeSales = collect($validated['employee_sales'])
            ->filter(fn ($row) => !empty($row['employee_id']))
            ->values();

        if ($employeeSales->isEmpty()) {
            return back()
                ->withErrors(['employee_sales' => 'Select at least one employee.'])
                ->withInput();
        }

        $notes = null;
        if ($request->filled('product_sold_text')) {
            $notes = $request->input('product_sold_text');
        } elseif ($request->has('product_sold') && is_array($request->input('product_sold'))) {
            $notes = implode('\n', array_filter($request->input('product_sold')));
        }

        DB::transaction(function () use ($sale, $validated, $employeeSales, $notes) {
            $sale->update([
                'employee_id' => (int) $employeeSales->first()['employee_id'],
                'quantity' => 1,
                'unit_price' => $validated['total_amount'],
                'total_amount' => (float) $validated['total_amount'],
                'spent_amount' => (float) ($validated['spent_amount'] ?? 0),
                'sale_date' => $validated['sale_date'],
                'branch_id' => $validated['branch_id'],
                'notes' => $notes,
            ]);

            $sale->employeeSaleDetails()->delete();

            foreach ($employeeSales as $row) {
                $sale->employeeSaleDetails()->create([
                    'employee_id' => (int) $row['employee_id'],
                    'description' => $row['description'] ?? null,
                ]);
            }
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }
}