<?php

namespace App\Http\Controllers\Commissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Models\Commission;
use App\Models\Employee;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $commissions = Commission::with('employee')->paginate(20);
        $stats = [
            'total' => Commission::sum('commission_amount'),
            'pending' => Commission::where('status', 'pending')->sum('commission_amount'),
            'approved' => Commission::where('status', 'approved')->sum('commission_amount'),
        ];
        return view('commissions.index', compact('commissions', 'stats'));
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

    public function approve(Commission $commission)
    {
        $commission->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Commission approved!');
    }

    public function reject(Commission $commission)
    {
        $commission->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Commission rejected!');
    }
}
