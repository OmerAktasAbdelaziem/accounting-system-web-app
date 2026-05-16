<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\EmployeeCommission;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Support\SimplePdf;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->latest()->paginate(20);
        return view('payroll.index', compact('payrolls'));
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
        $employeeCommission = EmployeeCommission::where('employee_id', $data['employee_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->first();

        $commission = $employeeCommission?->commission_earned ?? 0;
        $data['commission'] = $commission;
        $data['allowances'] = $data['allowances'] ?? 0;
        $data['net_salary'] = $data['basic_salary'] + $commission + ($data['allowances'] ?? 0);

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
        $employeeCommission = EmployeeCommission::where('employee_id', $payroll->employee_id)
            ->where('month', $payroll->month)
            ->where('year', $payroll->year)
            ->first();
        
        $payroll->calculated_commission = $employeeCommission?->commission_earned ?? 0;
        
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
        $employeeCommission = EmployeeCommission::where('employee_id', $payroll->employee_id)
            ->where('month', $payroll->month)
            ->where('year', $payroll->year)
            ->first();

        $commission = $employeeCommission?->commission_earned ?? 0;
        $data['commission'] = $commission;
        $data['allowances'] = $data['allowances'] ?? 0;
        $data['net_salary'] = $data['basic_salary'] + $commission + ($data['allowances'] ?? 0);

        $payroll->update($data);

        return redirect()->route('payroll.index')->with('success', __('messages.updated'));
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', __('messages.deleted'));
    }

    public function process(Payroll $payroll)
    {
        if ($payroll->status !== 'draft') {
            return redirect()->route('payroll.show', $payroll)->with('error', __('Payroll already processed'));
        }

        $payroll->update([
            'status' => 'processed',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        try {
            $salaryExpense = ChartOfAccount::where('account_code', '5020')->first();
            $salariesPayable = ChartOfAccount::where('account_code', '2020')->first();

            if ($salaryExpense && $salariesPayable) {
                $journalEntry = JournalEntry::create([
                    'date' => now(),
                    'description' => 'Payroll for ' . ($payroll->employee?->name ?? 'Employee'),
                    'reference_number' => 'PAY-' . $payroll->id,
                    'reference_type' => 'payroll',
                    'reference_id' => $payroll->id,
                    'branch_id' => $payroll->employee?->branch_id,
                    'created_by' => auth()->id(),
                    'status' => 'draft',
                ]);

                $journalEntry->addItem($salaryExpense->id, $payroll->net_salary, 0, 'Payroll expense');
                $journalEntry->addItem($salariesPayable->id, 0, $payroll->net_salary, 'Salaries payable');
                $journalEntry->post();
            }
        } catch (\Exception $exception) {
            logger()->error('Failed to create payroll journal entry: ' . $exception->getMessage());
        }

        return redirect()->route('payroll.show', $payroll)->with('success', __('Payroll processed'));
    }

    public function downloadPayslip(Payroll $payroll)
    {
        $lines = [
            'Employee: ' . ($payroll->employee?->name ?? '-'),
            'Month/Year: ' . $payroll->month . '/' . $payroll->year,
            'Basic Salary: ' . number_format((float) $payroll->basic_salary, 2),
            'Commission: ' . number_format((float) $payroll->commission, 2),
            'Net Salary: ' . number_format((float) $payroll->net_salary, 2),
            'Status: ' . $payroll->status,
        ];

        $pdf = SimplePdf::textDocument('Payslip ' . ($payroll->employee?->name ?? 'Employee'), $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="payslip-' . $payroll->id . '.pdf"',
        ]);
    }
}