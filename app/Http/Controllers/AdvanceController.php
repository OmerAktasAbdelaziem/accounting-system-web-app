<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{
    /**
     * Store a new advance for an employee
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0.01',
            'advance_date' => 'required|date',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = auth()->id();

        EmployeeAdvance::create($validated);

        return redirect()->back()->with('success', 'Advance recorded successfully!');
    }

    /**
     * Delete an advance
     */
    public function destroy(EmployeeAdvance $advance)
    {
        $advance->delete();
        return redirect()->back()->with('success', 'Advance deleted successfully!');
    }
}
