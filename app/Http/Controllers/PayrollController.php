<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\EmployeeCommission;
use App\Models\Commission;
use App\Models\EmployeeAdvance;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Support\SimplePdf;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $payrolls = Payroll::with('employee')
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('payroll.index', compact('payrolls', 'search'));
    }

    public function create()
    {
        $employees = Employee::pluck('name', 'id');
        $currentMonth = now()->month;
        $currentYear = now()->year;
        return view('payroll.create', compact('employees', 'currentMonth', 'currentYear'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Auto-fetch commission from employee's commission records for the given month/year
        $month = (int) $data['month'];
        $year = (int) $data['year'];
        
        $commission = Commission::where('employee_id', $data['employee_id'])
            ->whereMonth('commission_date', $month)
            ->whereYear('commission_date', $year)
            ->sum('commission_amount') ?? 0;
        $data['commission'] = $commission;
        $data['allowances'] = $data['allowances'] ?? 0;
       
            // Get approved advances for this employee
            $advances = EmployeeAdvance::where('employee_id', $data['employee_id'])
                ->sum('amount') ?? 0;
        
            $data['commission'] = $commission;
            $data['allowances'] = $data['allowances'] ?? 0;
            $data['deductions'] = $advances;
            $data['net_salary'] = $data['basic_salary'] + $commission + ($data['allowances'] ?? 0) - $advances;

        Payroll::create($data);

        return redirect()->route('payroll.index')->with('success', __('messages.created'));
    }

    public function show(Payroll $payroll)
    {
        return view('payroll.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::pluck('name', 'id');
        
        // Get employee's commission for this payroll month/year
        $month = (int) $payroll->month;
        $year = (int) $payroll->year;
        
        $payroll->calculated_commission = Commission::where('employee_id', $payroll->employee_id)
            ->whereMonth('commission_date', $month)
            ->whereYear('commission_date', $year)
            ->sum('commission_amount') ?? 0;
        
        return view('payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Auto-fetch commission from employee's commission records
        $month = (int) $payroll->month;
        $year = (int) $payroll->year;
        
        $commission = Commission::where('employee_id', $payroll->employee_id)
            ->whereMonth('commission_date', $month)
            ->whereYear('commission_date', $year)
            ->sum('commission_amount') ?? 0;
        
        $data['commission'] = $commission;
        $data['allowances'] = $data['allowances'] ?? 0;
        
            // Get approved advances for this employee
            $advances = EmployeeAdvance::where('employee_id', $payroll->employee_id)
                ->sum('amount') ?? 0;
        
            $data['deductions'] = $advances;
            $data['net_salary'] = $data['basic_salary'] + $commission + ($data['allowances'] ?? 0) - $advances;

        $payroll->update($data);

        return redirect()->route('payroll.index')->with('success', __('messages.updated'));
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', __('messages.deleted'));
    }

    public function downloadPayslip(Payroll $payroll)
    {
        $lines = [
            'Employee: ' . ($payroll->employee?->name ?? '-'),
            'Month/Year: ' . $payroll->month . '/' . $payroll->year,
            'Basic Salary: ' . number_format((float) $payroll->basic_salary, 2),
            'Commission: ' . number_format((float) $payroll->commission, 2),
            'Allowances: ' . number_format((float) $payroll->allowances, 2),
            'Deductions: ' . number_format((float) $payroll->deductions, 2),
            'Net Salary: ' . number_format((float) $payroll->net_salary, 2),
        ];

        $pdf = SimplePdf::textDocument('Payslip ' . ($payroll->employee?->name ?? 'Employee'), $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="payslip-' . $payroll->id . '.pdf"',
        ]);
    }
}



