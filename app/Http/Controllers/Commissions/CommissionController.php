<?php

namespace App\Http\Controllers\Commissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Models\Branch;
use App\Models\Commission;
use App\Models\EmployeeCommission;
use App\Models\Employee;
use Carbon\Carbon;
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
        
        $monthlyCommissions = EmployeeCommission::query()
            ->get()
            ->groupBy(fn (EmployeeCommission $commission) => Carbon::parse($commission->created_at)->format('Y-m'))
            ->map(function ($items, string $key) {
                $date = Carbon::createFromFormat('Y-m', $key);

                return (object) [
                    'year' => $date->format('Y'),
                    'month' => $date->format('m'),
                    'total' => (float) $items->sum('commission_earned'),
                ];
            })
            ->sortByDesc(fn ($item) => $item->year . '-' . $item->month)
            ->values();
        
        $stats = compact('totalCommission');
        
        return view('commissions.index', compact('commissions', 'stats', 'employees', 'monthlyCommissions'));
    }

    public function create()
    {
        $commission = null;
        $employees = Employee::where('is_active', true)->get();
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = request()->input('branch_ids', []);
        return view('commissions.form', compact('commission', 'employees', 'branches', 'selectedBranchIds'));
    }

    public function store(StoreCommissionRequest $request)
    {
        $validated = $request->validated();
        $validated['commission_amount'] = ((float)$validated['sale_amount'] * (float)$validated['commission_rate']) / 100;
        $commission = Commission::create($validated);
        $commission->syncBranches($validated['branch_ids'] ?? []);

        return redirect()->route('commissions.index')->with('success', 'Commission recorded successfully!');
    }

    public function show(Commission $commission)
    {
        return view('commissions.show', compact('commission'));
    }

    public function edit(Commission $commission)
    {
        $employees = Employee::where('is_active', true)->get();
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = $commission->branches()->pluck('branches.id')->all();
        return view('commissions.form', compact('commission', 'employees', 'branches', 'selectedBranchIds'));
    }

    public function update(UpdateCommissionRequest $request, Commission $commission)
    {
        $validated = $request->validated();
        $validated['commission_amount'] = ((float)$validated['sale_amount'] * (float)$validated['commission_rate']) / 100;
        $commission->update($validated);
        $commission->syncBranches($validated['branch_ids'] ?? []);

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
