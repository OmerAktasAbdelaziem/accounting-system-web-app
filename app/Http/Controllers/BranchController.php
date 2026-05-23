<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Payroll;
use App\Models\Commission;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(20);
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Branch::create($data);

        return redirect()->route('branches.index')->with('success', __('messages.branch_created_successfully'));
    }

    public function show(Branch $branch)
    {
        $branch->loadCount([
            'employees',
            'products',
            'categories',
            'suppliers',
            'customers',
            'invoices',
            'storages',
            'safes',
            'commissions',
        ]);

        $branchPayrolls = Payroll::with('employee')
            ->whereHas('employee.branches', function ($query) use ($branch) {
                $query->where('branches.id', $branch->id);
            })
            ->latest()
            ->take(10)
            ->get();

        $branchCommissions = Commission::with('employee')
            ->whereHas('employee.branches', function ($query) use ($branch) {
                $query->where('branches.id', $branch->id);
            })
            ->latest('commission_date')
            ->take(10)
            ->get();

        $branchSuppliers = $branch->suppliers()
            ->withSum('purchases as total_purchased', 'total_amount')
            ->withSum('payments as total_paid', 'amount')
            ->latest()
            ->take(10)
            ->get()
            ->map(function (Supplier $supplier) {
                $supplier->outstanding_amount = ((float) ($supplier->opening_balance ?? 0))
                    + (float) ($supplier->total_purchased ?? 0)
                    - (float) ($supplier->total_paid ?? 0);

                return $supplier;
            });

        $branchOutstandingTotal = (float) $branchSuppliers->sum('outstanding_amount');

        $recentEmployees = $branch->employees()->latest()->take(5)->get();
        $recentProducts = $branch->products()->latest()->take(5)->get();
        $recentCategories = $branch->categories()->latest()->take(5)->get();
        $recentSuppliers = $branchSuppliers->take(5)->values();
        $recentCommissions = $branchCommissions->take(5)->values();
        $recentInvoices = $branch->invoices()->latest()->take(5)->get();
        $recentStorages = $branch->storages()->latest()->take(5)->get();
        $recentSafes = $branch->safes()->latest()->take(5)->get();
        $recentCustomers = $branch->customers()->latest()->take(5)->get();

        // Load unassigned employees (those with no branches)
        $unassignedEmployees = \App\Models\Employee::where('is_active', true)
            ->whereDoesntHave('branches')
            ->orderBy('name')
            ->get();

        return view('branches.show', compact(
            'branch',
            'recentEmployees',
            'recentProducts',
            'recentCategories',
            'recentSuppliers',
            'recentCustomers',
            'recentInvoices',
            'recentStorages',
            'recentSafes',
            'recentCommissions',
            'branchPayrolls',
            'branchCommissions',
            'branchSuppliers',
            'branchOutstandingTotal',
            'unassignedEmployees'
        ));
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'manager_name' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $branch->update($data);

        return redirect()->route('branches.index')->with('success', __('messages.branch_updated_successfully'));
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', __('messages.branch_deleted_successfully'));
    }

    public function assignEmployee(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = \App\Models\Employee::findOrFail($validated['employee_id']);
        // Use attach to add the branch without removing existing branches
        $employee->branches()->attach($branch->id);

        return back()->with('success', $employee->name . ' assigned to branch successfully!');
    }

    public function removeEmployee(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = \App\Models\Employee::findOrFail($validated['employee_id']);
        $employee->branches()->detach($branch->id);

        return back()->with('success', $employee->name . ' removed from branch successfully!');
    }
}
