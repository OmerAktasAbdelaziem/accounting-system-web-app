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
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $employees = Employee::where('is_active', true)->get();
        $commissions = Commission::with('employee')
            ->latest('commission_date')
            ->paginate(15);

        $commissionProfiles = Commission::with('employee')
            ->latest('commission_date')
            ->get()
            ->groupBy('employee_id')
            ->map(function ($items) {
                $sortedItems = $items->sortByDesc('commission_date')->values();
                $employee = $sortedItems->first()?->employee;

                if (! $employee) {
                    return null;
                }

                $employee->total_commission_amount = (float) $sortedItems->sum('commission_amount');
                $employee->last_commission_date = $sortedItems->first()?->commission_date;
                $employee->latest_commission = $sortedItems->first();
                $employee->commission_count = $sortedItems->count();
                $employee->commissions = $sortedItems;

                return $employee;
            })
            ->filter()
            ->sortByDesc(fn (Employee $employee) => $employee->last_commission_date?->timestamp ?? 0)
            ->values();

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
        
        return view('commissions.index', compact('commissions', 'commissionProfiles', 'stats', 'employees', 'monthlyCommissions'));
    }

    public function create()
    {
        $commission = null;
        $employees = Employee::where('is_active', true)
            ->whereDoesntHave('commissionTransactions')
            ->orderBy('name')
            ->get();
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = request()->input('branch_ids', []);
        return view('commissions.form', compact('commission', 'employees', 'branches', 'selectedBranchIds'));
    }

    public function store(StoreCommissionRequest $request)
    {
        $validated = $request->validated();
        if (Commission::where('employee_id', $validated['employee_id'])->exists()) {
            throw ValidationException::withMessages([
                'employee_id' => 'This employee already has a commission profile. Open the profile to add more commissions.',
            ]);
        }

        $validated['commission_amount'] = ((float)$validated['sale_amount'] * (float)$validated['commission_rate']) / 100;
        $commission = Commission::create($validated);
        $commission->syncBranches($validated['branch_ids'] ?? []);

        return redirect()->route('commissions.index')->with('success', 'Commission recorded successfully!');
    }

    public function append(StoreCommissionRequest $request, Commission $commission)
    {
        $validated = $request->validated();
        $validated['employee_id'] = $commission->employee_id;
        $validated['commission_amount'] = ((float) $validated['sale_amount'] * (float) $validated['commission_rate']) / 100;

        $newCommission = Commission::create($validated);
        $newCommission->syncBranches($validated['branch_ids'] ?? []);

        return redirect()
            ->route('commissions.show', $newCommission)
            ->with('success', 'Additional commission added successfully!');
    }

    public function show(Commission $commission)
    {
        $branches = Branch::orderBy('name')->get();
        $employee = $commission->employee()->with(['branches', 'commissionTransactions' => fn ($query) => $query->latest('commission_date')])->first();
        $commissions = $employee->commissionTransactions->sortByDesc('commission_date')->values();
        $totalSales = $commissions->sum('sale_amount');
        $totalCommissions = $commissions->sum('commission_amount');
        $averageRate = $commissions->count() ? $commissions->avg('commission_rate') : 0;

        $latestCommission = $commissions->first();

        return view('commissions.show', compact(
            'employee',
            'commissions',
            'commission',
            'latestCommission',
            'totalSales',
            'totalCommissions',
            'averageRate',
            'branches'
        ));
    }

    public function edit(Commission $commission)
    {
        $employees = Employee::where(function ($query) use ($commission) {
            $query->where('is_active', true)
                ->whereDoesntHave('commissionTransactions')
                ->orWhere('id', $commission->employee_id);
        })->orderBy('name')->get();
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
