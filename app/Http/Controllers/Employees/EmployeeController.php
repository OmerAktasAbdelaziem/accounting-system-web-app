<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeSale;
use App\Models\EmployeeCommission;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    private function generateEmployeeCode(): string
    {
        do {
            $employeeCode = 'EMP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (Employee::where('employee_code', $employeeCode)->exists());

        return $employeeCode;
    }

    public function index()
    {
        $employees = Employee::paginate(20);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $employee = null;
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = request()->input('branch_ids', []);
        return view('employees.form', compact('employee', 'branches', 'selectedBranchIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string',
            'base_salary' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $validated['name_ar'] = $validated['name'];
        $validated['position_ar'] = $validated['position'];
        $validated['address_ar'] = $validated['address'] ?? null;
        $validated['employee_code'] = $this->generateEmployeeCode();

        $employee = Employee::create($validated);
        $employee->syncBranches($validated['branch_ids'] ?? []);
        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    public function show(Employee $employee)
    {
        $employee->load('branches');

        // Fetch total sales from Commission table (sum of all sale_amount)
        $totalSales = Commission::where('employee_id', $employee->id)->sum('sale_amount');
        
        // Fetch total commissions from Commission table
        $totalCommissions = Commission::where('employee_id', $employee->id)->sum('commission_amount');
        
        // Fetch recent sales - from Commission records (each has sale_amount linked)
        $recentSales = Commission::where('employee_id', $employee->id)
            ->select('id', 'sale_amount', 'commission_date')
            ->latest('commission_date')
            ->take(10)
            ->get();
        
        // Fetch recent commissions from Commission table (ordered by commission_date)
        $recentCommissions = Commission::where('employee_id', $employee->id)->latest('commission_date')->take(10)->get();

        return view('employees.show', compact('employee', 'totalSales', 'totalCommissions', 'recentSales', 'recentCommissions'));
    }

    public function edit(Employee $employee)
    {
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = $employee->branches()->pluck('branches.id')->all();
        return view('employees.form', compact('employee', 'branches', 'selectedBranchIds'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string',
            'base_salary' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $validated['name_ar'] = $validated['name'];
        $validated['position_ar'] = $validated['position'];
        $validated['address_ar'] = $validated['address'] ?? null;

        $employee->update($validated);
        $employee->syncBranches($validated['branch_ids'] ?? []);
        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(['success' => true]);
    }

    public function export()
    {
        $employees = Employee::all();
        // Generate Excel file
        return response()->json(['success' => true]);
    }
}
