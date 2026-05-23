<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
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
            ->paginate(6)
            ->withQueryString();

        $stats = [
            'count' => EmployeeSale::count(),
            'gross_total' => (float) EmployeeSale::sum('total_amount'),
            'spent_total' => (float) EmployeeSale::sum('spent_amount'),
        ];
        $stats['net_total'] = $stats['gross_total'] - $stats['spent_total'];

        $branches = Branch::orderBy('name')->get();
        $employees = Employee::withTrashed()->orderBy('name')->get();

        return view('sales.index', compact('sales', 'stats', 'branches', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'branch_id' => 'required|exists:branches,id',
            'total_amount' => 'required|numeric|min:0.01',
            'spent_amount' => 'nullable|numeric|min:0',
            'employee_assignments' => 'required|array|min:1',
            'employee_assignments.*.employee_id' => 'required|exists:employees,id',
            'employee_assignments.*.description' => 'nullable|string|max:1000',
            'product_sold' => 'nullable|array',
            'product_sold.*' => 'nullable|string|max:255',
        ]);

        $employeeAssignments = collect($validated['employee_assignments'] ?? [])
            ->filter(fn ($assignment) => ! empty($assignment['employee_id']))
            ->map(function ($assignment) {
                return [
                    'employee_id' => (int) $assignment['employee_id'],
                    'description' => isset($assignment['description']) && trim((string) $assignment['description']) !== ''
                        ? trim((string) $assignment['description'])
                        : null,
                ];
            })
            ->values()
            ->all();

        $primaryEmployeeId = $employeeAssignments[0]['employee_id'] ?? null;

        // Create a base sale record with nullable employee/product for simplified entry
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
            'employee_assignments' => $employeeAssignments,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(EmployeeSale $sale)
    {
        $branches = Branch::orderBy('name')->get();
        $employees = Employee::withTrashed()->orderBy('name')->get();
        return view('sales.edit', compact('sale', 'branches', 'employees'));
    }

    public function update(Request $request, EmployeeSale $sale)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'branch_id' => 'required|exists:branches,id',
            'total_amount' => 'required|numeric|min:0.01',
            'spent_amount' => 'nullable|numeric|min:0',
            'employee_assignments' => 'required|array|min:1',
            'employee_assignments.*.employee_id' => 'required|exists:employees,id',
            'employee_assignments.*.description' => 'nullable|string|max:1000',
            'product_sold_text' => 'nullable|string|max:2000',
            'product_sold' => 'nullable|array',
            'product_sold.*' => 'nullable|string|max:255',
        ]);

        $employeeAssignments = collect($validated['employee_assignments'] ?? [])
            ->filter(fn ($assignment) => ! empty($assignment['employee_id']))
            ->map(function ($assignment) {
                return [
                    'employee_id' => (int) $assignment['employee_id'],
                    'description' => isset($assignment['description']) && trim((string) $assignment['description']) !== ''
                        ? trim((string) $assignment['description'])
                        : null,
                ];
            })
            ->values()
            ->all();

        $primaryEmployeeId = $employeeAssignments[0]['employee_id'] ?? null;

        $notes = null;
        if ($request->filled('product_sold_text')) {
            $notes = $request->input('product_sold_text');
        } elseif ($request->has('product_sold') && is_array($request->input('product_sold'))) {
            $notes = implode('\n', array_filter($request->input('product_sold')));
        }

        $sale->update([
            'employee_id' => $primaryEmployeeId,
            'quantity' => 1,
            'unit_price' => $validated['total_amount'],
            'total_amount' => (float) $validated['total_amount'],
            'spent_amount' => (float) ($validated['spent_amount'] ?? 0),
            'sale_date' => $validated['sale_date'],
            'branch_id' => $validated['branch_id'],
            'notes' => $notes,
            'employee_assignments' => $employeeAssignments,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }
}