<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\EmployeeCommission;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeSale;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * List all employees
     */
    public function index(Request $request): JsonResponse
    {
        $query = Employee::query();

        // Filter by department
        if ($request->has('department')) {
            $query->where('department', $request->input('department'));
        }

        // Filter by status
        if ($request->has('active')) {
            $active = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);
            if ($active) {
                $query->active();
            } else {
                $query->where('is_active', false)->orWhereNotNull('termination_date');
            }
        }

        // Pagination
        $perPage = $request->input('per_page', 15);
        $employees = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $employees->items(),
            'pagination' => [
                'total' => $employees->total(),
                'count' => $employees->count(),
                'per_page' => $employees->perPage(),
                'current_page' => $employees->currentPage(),
                'total_pages' => $employees->lastPage(),
            ],
        ]);
    }

    /**
     * Get employee details
     */
    public function show($id): JsonResponse
    {
        $employee = Employee::with(['commissions', 'deductions', 'sales'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $employee,
        ]);
    }

    /**
     * Create employee
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_code' => 'required|string|unique:employees,employee_code',
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'position_ar' => 'required|string|max:255',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'hire_date' => 'required|date',
            'base_salary' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0',
            'commission_type' => 'required|in:percentage,fixed',
            'department' => 'required|in:sales,inventory,accounting,management,other',
        ]);

        $employee = Employee::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee created successfully',
            'data' => $employee,
        ], 201);
    }

    /**
     * Update employee
     */
    public function update(Request $request, $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'position' => 'sometimes|string|max:255',
            'position_ar' => 'sometimes|string|max:255',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'termination_date' => 'nullable|date|after:hire_date',
            'base_salary' => 'sometimes|numeric|min:0',
            'commission_rate' => 'sometimes|numeric|min:0',
            'commission_type' => 'sometimes|in:percentage,fixed',
            'department' => 'sometimes|in:sales,inventory,accounting,management,other',
            'is_active' => 'sometimes|boolean',
        ]);

        $employee->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee,
        ]);
    }

    /**
     * Delete employee
     */
    public function destroy($id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Employee deleted successfully',
        ]);
    }

    /**
     * Get employee commissions
     */
    public function getCommissions($id, Request $request): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        $query = $employee->commissions();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by period
        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        $commissions = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $commissions->items(),
            'pagination' => [
                'total' => $commissions->total(),
                'count' => $commissions->count(),
                'per_page' => $commissions->perPage(),
                'current_page' => $commissions->currentPage(),
                'total_pages' => $commissions->lastPage(),
            ],
        ]);
    }

    /**
     * Calculate commission for a period
     */
    public function calculateCommission($id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
        ]);

        $employee = Employee::findOrFail($id);
        $commission = $employee->getOrCreateCommission($validated['month'], $validated['year']);

        return response()->json([
            'success' => true,
            'message' => 'Commission calculated',
            'data' => $commission,
        ]);
    }

    /**
     * Approve commission
     */
    public function approveCommission($id): JsonResponse
    {
        $commission = EmployeeCommission::findOrFail($id);
        $commission->approve();

        return response()->json([
            'success' => true,
            'message' => 'Commission approved',
            'data' => $commission,
        ]);
    }

    /**
     * Mark commission as paid
     */
    public function payCommission($id): JsonResponse
    {
        $commission = EmployeeCommission::findOrFail($id);
        $commission->markAsPaid();

        return response()->json([
            'success' => true,
            'message' => 'Commission marked as paid',
            'data' => $commission,
        ]);
    }

    /**
     * Add deduction
     */
    public function addDeduction(Request $request, $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'type' => 'required|string',
            'type_ar' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
        ]);

        $deduction = $employee->deductions()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Deduction added',
            'data' => $deduction,
        ], 201);
    }

    /**
     * Get employee deductions
     */
    public function getDeductions($id, Request $request): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        $query = $employee->deductions();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by year
        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        $deductions = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $deductions->items(),
            'pagination' => [
                'total' => $deductions->total(),
                'count' => $deductions->count(),
                'per_page' => $deductions->perPage(),
                'current_page' => $deductions->currentPage(),
                'total_pages' => $deductions->lastPage(),
            ],
        ]);
    }

    /**
     * Record employee sale
     */
    public function recordSale(Request $request, $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'sale_date' => 'required|date',
            'sale_reference' => 'nullable|string',
            'notes' => 'nullable|string',
            'notes_ar' => 'nullable|string',
        ]);

        $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
        $sale = $employee->sales()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sale recorded',
            'data' => $sale,
        ], 201);
    }

    /**
     * Get employee sales
     */
    public function getSales($id, Request $request): JsonResponse
    {
        $employee = Employee::findOrFail($id);

        $query = $employee->sales();

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('sale_date', [
                $request->input('start_date'),
                $request->input('end_date'),
            ]);
        }

        $sales = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $sales->items(),
            'pagination' => [
                'total' => $sales->total(),
                'count' => $sales->count(),
                'per_page' => $sales->perPage(),
                'current_page' => $sales->currentPage(),
                'total_pages' => $sales->lastPage(),
            ],
        ]);
    }

    /**
     * Get all employee sales across all employees (new endpoint for Phase 6 dashboards)
     */
    public function getAllSales(Request $request): JsonResponse
    {
        $query = EmployeeSale::with(['employee', 'product']);

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('sale_date', [
                $request->input('start_date'),
                $request->input('end_date'),
            ]);
        }

        // Filter by employee
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->input('employee_id'));
        }

        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        // Order by date (most recent first)
        $sales = $query->orderBy('sale_date', 'desc')->paginate($request->input('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $sales->items(),
            'pagination' => [
                'total' => $sales->total(),
                'count' => $sales->count(),
                'per_page' => $sales->perPage(),
                'current_page' => $sales->currentPage(),
                'total_pages' => $sales->lastPage(),
            ],
        ]);
    }

    /**
     * Get salary summary for period
     */
    public function getSalarySummary($id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
        ]);

        $employee = Employee::findOrFail($id);

        $commission = $employee->getOrCreateCommission($validated['month'], $validated['year']);
        $deductions = $employee->calculateDeductionsForPeriod($validated['month'], $validated['year']);
        $netSalary = $employee->calculateNetSalary($validated['month'], $validated['year']);

        return response()->json([
            'success' => true,
            'data' => [
                'employee_id' => $employee->id,
                'employee_name' => $employee->name,
                'employee_name_ar' => $employee->name_ar,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'base_salary' => $employee->base_salary,
                'sales_amount' => $commission->sales_amount,
                'commission_earned' => $commission->commission_earned,
                'bonus' => $commission->bonus,
                'total_deductions' => $deductions,
                'net_salary' => $netSalary,
            ],
        ]);
    }

    /**
     * Generate payroll report
     */
    public function payrollReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'department' => 'nullable|in:sales,inventory,accounting,management,other',
        ]);

        $query = Employee::active();

        // Filter by department
        if ($request->has('department')) {
            $query->where('department', $validated['department']);
        }

        $employees = $query->get();

        $report = [];
        $totals = [
            'total_base_salary' => 0,
            'total_commissions' => 0,
            'total_bonuses' => 0,
            'total_deductions' => 0,
            'total_net_salary' => 0,
        ];

        foreach ($employees as $employee) {
            $netSalary = $employee->calculateNetSalary($validated['month'], $validated['year']);
            $commission = $employee->getOrCreateCommission($validated['month'], $validated['year']);
            $deductions = $employee->calculateDeductionsForPeriod($validated['month'], $validated['year']);

            $item = [
                'employee_id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'name' => $employee->name,
                'name_ar' => $employee->name_ar,
                'position' => $employee->position,
                'department' => $employee->department,
                'base_salary' => $employee->base_salary,
                'commission_earned' => $commission->commission_earned,
                'bonus' => $commission->bonus,
                'total_deductions' => $deductions,
                'net_salary' => $netSalary,
            ];

            $report[] = $item;

            $totals['total_base_salary'] += $employee->base_salary;
            $totals['total_commissions'] += $commission->commission_earned;
            $totals['total_bonuses'] += $commission->bonus;
            $totals['total_deductions'] += $deductions;
            $totals['total_net_salary'] += $netSalary;
        }

        return response()->json([
            'success' => true,
            'period' => "{$validated['month']}/{$validated['year']}",
            'report' => $report,
            'totals' => $totals,
        ]);
    }
}
