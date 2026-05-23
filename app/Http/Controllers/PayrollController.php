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
    private function commissionQueryForEmployeePeriod(Employee $employee, int $month, int $year)
    {
        $query = Commission::query()
            ->where('employee_id', $employee->id)
            ->whereMonth('commission_date', $month)
            ->whereYear('commission_date', $year);

        $branchIds = $employee->branches()->pluck('branches.id');

        if ($branchIds->isNotEmpty()) {
            $query->whereHas('branches', function ($branchQuery) use ($branchIds) {
                $branchQuery->whereIn('branches.id', $branchIds);
            });
        }

        return $query;
    }

    private function calculateCommissionForEmployeePeriod(Employee $employee, int $month, int $year): float
    {
        return (float) $this->commissionQueryForEmployeePeriod($employee, $month, $year)->sum('commission_amount');
    }

    private function commissionsForEmployeePeriod(Employee $employee, int $month, int $year)
    {
        return $this->commissionQueryForEmployeePeriod($employee, $month, $year)
            ->orderBy('commission_date', 'desc')
            ->get();
    }

    public function index()
    {
        $payrolls = Payroll::with('employee.branches')->latest()->paginate(20);

        $payrolls->getCollection()->transform(function (Payroll $payroll) {
            if ($payroll->employee) {
                $payroll->calculated_commission = $this->calculateCommissionForEmployeePeriod(
                    $payroll->employee,
                    (int) $payroll->month,
                    (int) $payroll->year
                );
            }

            return $payroll;
        });

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
        $month = (int) $data['month'];
        $year = (int) $data['year'];

        $employee = Employee::with('branches')->findOrFail($data['employee_id']);
        $commission = $this->calculateCommissionForEmployeePeriod($employee, $month, $year);
        $data['commission'] = $commission;
        $data['allowances'] = $data['allowances'] ?? 0;

        // Get approved advances for this employee
        $advances = EmployeeAdvance::where('employee_id', $data['employee_id'])
            ->sum('amount') ?? 0;

        $data['deductions'] = $advances;
        $data['net_salary'] = $data['basic_salary'] + $commission + ($data['allowances'] ?? 0) - $advances;

        Payroll::create($data);

        return redirect()->route('payroll.index')->with('success', __('messages.created'));
    }

    public function show(Payroll $payroll)
    {
        $payroll->loadMissing('employee.branches');

        $commissions = collect();
        if ($payroll->employee) {
            $commissions = $this->commissionsForEmployeePeriod(
                $payroll->employee,
                (int) $payroll->month,
                (int) $payroll->year
            );
        }

        $payroll->calculated_commission = (float) $commissions->sum('commission_amount');

        return view('payroll.show', compact('payroll', 'commissions'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::pluck('name', 'id');

        $payroll->loadMissing('employee.branches');
        if ($payroll->employee) {
            $payroll->calculated_commission = $this->calculateCommissionForEmployeePeriod(
                $payroll->employee,
                (int) $payroll->month,
                (int) $payroll->year
            );
        }
        
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

        $employee = $payroll->employee()->with('branches')->firstOrFail();
        $commission = $this->calculateCommissionForEmployeePeriod($employee, $month, $year);
        
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
        $payroll->loadMissing('employee.branches');
        $commission = $payroll->employee
            ? $this->calculateCommissionForEmployeePeriod($payroll->employee, (int) $payroll->month, (int) $payroll->year)
            : (float) $payroll->commission;

        $lines = [
            'Employee: ' . ($payroll->employee?->name ?? '-'),
            'Month/Year: ' . $payroll->month . '/' . $payroll->year,
            'Basic Salary: ' . number_format((float) $payroll->basic_salary, 2),
            'Commission: ' . number_format((float) $commission, 2),
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



