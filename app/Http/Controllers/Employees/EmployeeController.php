<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSale;
use App\Models\EmployeeCommission;
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
        return view('employees.form', compact('employee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone' => 'required|string',
            'position' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $validated['employee_code'] = $this->generateEmployeeCode();

        Employee::create($validated);
        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    public function show(Employee $employee)
    {
        $totalSales = EmployeeSale::where('employee_id', $employee->id)->sum('total_amount');
        $totalCommissions = EmployeeCommission::where('employee_id', $employee->id)->sum('commission_earned');
        $recentSales = EmployeeSale::where('employee_id', $employee->id)->latest()->take(10)->get();
        $recentCommissions = EmployeeCommission::where('employee_id', $employee->id)->latest()->take(10)->get();
        $pendingCommissions = EmployeeCommission::where('employee_id', $employee->id)->where('status', 'pending')->count();
        $paidCommissions = EmployeeCommission::where('employee_id', $employee->id)->where('paid_at', '!=', null)->count();

        return view('employees.show', compact('employee', 'totalSales', 'totalCommissions', 'recentSales', 'recentCommissions', 'pendingCommissions', 'paidCommissions'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.form', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string',
            'position' => 'required|string',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        $employee->update($validated);
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
