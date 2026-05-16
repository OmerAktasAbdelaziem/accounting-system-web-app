<?php

namespace App\Http\Controllers\Commissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Models\Commission;
use App\Models\EmployeeCommission;
use App\Models\Employee;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $employees = Employee::where('is_active', true)->get();
        
        // Get commission data from Commission model for transaction history
        $commissions = Commission::with('employee')
            ->latest('commission_date')
            ->paginate(15);
        
        // Get aggregated commission stats
        $totalCommission = Commission::sum('commission_amount');
        
        // Get monthly commission aggregation from EmployeeCommission
        // Using SQLite compatible query with strftime
        $monthlyCommissions = EmployeeCommission::selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, SUM(commission_earned) as total")
            ->groupByRaw("strftime('%Y', created_at), strftime('%m', created_at)")
            ->orderByRaw("strftime('%Y', created_at) DESC, strftime('%m', created_at) DESC")
            ->get();
        
        $stats = compact('totalCommission');
        
        return view('commissions.index', compact('commissions', 'stats', 'employees', 'monthlyCommissions'));
    }

    public function create()
    {
        $commission = null;
        $employees = Employee::where('is_active', true)->get();
        return view('commissions.form', compact('commission', 'employees'));
    }

    public function store(StoreCommissionRequest $request)
    {
        $validated = $request->validated();
        $validated['commission_amount'] = ($validated['sale_amount'] * $validated['commission_rate']) / 100;
        Commission::create($validated);

        return redirect()->route('commissions.index')->with('success', 'Commission recorded successfully!');
    }

    public function show(Commission $commission)
    {
        return view('commissions.show', compact('commission'));
    }

    public function edit(Commission $commission)
    {
        $employees = Employee::where('is_active', true)->get();
        return view('commissions.form', compact('commission', 'employees'));
    }

    public function update(UpdateCommissionRequest $request, Commission $commission)
    {
        $validated = $request->validated();
        $validated['commission_amount'] = ($validated['sale_amount'] * $validated['commission_rate']) / 100;
        $commission->update($validated);

        return redirect()->route('commissions.index')->with('success', 'Commission updated successfully!');
    }

    public function destroy(Commission $commission)
    {
        $commission->delete();
        return response()->json(['success' => true]);
    }

    public function exportPdf($id = null)
    {
        if ($id) {
            $commission = Commission::findOrFail($id);
            return $this->generatePdf($commission);
        } else {
            $commissions = Commission::latest()->get();
            return $this->generatePdf($commissions);
        }
    }

    private function generatePdf($data)
    {
        // Placeholder for PDF generation
        // Use DomPDF or similar package
        return response()->download('path/to/pdf');
    }
}
