<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\EmployeeCommission;
use App\Models\Commission;
use App\Models\EmployeeAdvance;
use App\Models\EmployeeDeduction;
use App\Models\Safe;
use App\Models\SafeOutcome;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Support\SimplePdf;
use App\Support\SimpleExcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!\App\Traits\ChecksFeatureAccess::hasFeatureAccess('payroll')) {
                abort(403);
            }

            return $next($request);
        });
    }

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

    private function calculateDeductionsForEmployeePeriod(Employee $employee, int $month, int $year): float
    {
        $advances = (float) EmployeeAdvance::query()
            ->where('employee_id', $employee->id)
            ->whereMonth('advance_date', $month)
            ->whereYear('advance_date', $year)
            ->sum('amount');

        $deductions = (float) EmployeeDeduction::query()
            ->where('employee_id', $employee->id)
            ->where('month', $month)
            ->where('year', $year)
            ->sum('amount');

        return $advances + $deductions;
    }

    private function calculateGrossAndNet(float $basicSalary, float $commission, float $allowances, float $deductions): array
    {
        $gross = $basicSalary + $commission + $allowances;
        $net = $gross - $deductions;

        return [
            'gross' => $gross,
            'net' => $net,
        ];
    }

    private function applyPayrollCalculations(Payroll $payroll): Payroll
    {
        if ($payroll->employee) {
            $payroll->calculated_commission = $this->calculateCommissionForEmployeePeriod(
                $payroll->employee,
                (int) $payroll->month,
                (int) $payroll->year
            );

            $payroll->calculated_deductions = $this->calculateDeductionsForEmployeePeriod(
                $payroll->employee,
                (int) $payroll->month,
                (int) $payroll->year
            );

            $totals = $this->calculateGrossAndNet(
                (float) $payroll->basic_salary,
                (float) $payroll->calculated_commission,
                (float) ($payroll->allowances ?? 0),
                (float) $payroll->calculated_deductions
            );

            $payroll->gross_salary = $totals['gross'];
            $payroll->calculated_net_salary = $totals['net'];
        }

        return $payroll;
    }

    public function index()
    {
        $activePayrolls = Payroll::with(['employee.branches', 'safe'])
            ->unpaid()
            ->latest()
            ->paginate(20);

        $activePayrolls->getCollection()->transform(fn (Payroll $payroll) => $this->applyPayrollCalculations($payroll));

        $paidPayrolls = Payroll::with(['employee.branches', 'safe'])
            ->paid()
            ->latest('processed_at')
            ->paginate(15, ['*'], 'paid_page');

        $paidPayrolls->getCollection()->transform(fn (Payroll $payroll) => $this->applyPayrollCalculations($payroll));

        $unpaidPayrollWidgets = Payroll::with('employee')
            ->unpaid()
            ->latest()
            ->get()
            ->transform(fn (Payroll $payroll) => $this->applyPayrollCalculations($payroll));

        $unpaidNetSalaryTotal = (float) $unpaidPayrollWidgets->sum(fn (Payroll $payroll) => (float) ($payroll->calculated_net_salary ?? $payroll->net_salary ?? 0));
        $unpaidPayrollCount = $unpaidPayrollWidgets->count();
        $paidPayrollCount = $paidPayrolls->total();
        $safes = Safe::where('is_active', true)->orderBy('name')->get();

        return view('payroll.index', compact(
            'activePayrolls',
            'paidPayrolls',
            'unpaidPayrollWidgets',
            'unpaidNetSalaryTotal',
            'unpaidPayrollCount',
            'paidPayrollCount',
            'safes'
        ));
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
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Auto-fetch commission from employee's commission records for the given month/year
        $month = (int) $data['month'];
        $year = (int) $data['year'];

        $employee = Employee::with('branches')->findOrFail($data['employee_id']);
        $commission = $this->calculateCommissionForEmployeePeriod($employee, $month, $year);
        $allowances = (float) ($data['allowances'] ?? 0);
        // Allow manual override of deductions if provided in the form
        $deductions = isset($data['deductions']) ? (float) $data['deductions'] : $this->calculateDeductionsForEmployeePeriod($employee, $month, $year);
        $totals = $this->calculateGrossAndNet((float) $data['basic_salary'], $commission, $allowances, $deductions);

        $data['commission'] = $commission;
        $data['allowances'] = $allowances;
        $data['deductions'] = $deductions;
        $data['net_salary'] = $totals['net'];

        Payroll::create($data);

        return redirect()->route('payroll.index')->with('success', __('messages.created'));
    }

    public function show(Payroll $payroll)
    {
        $payroll->loadMissing('employee.branches', 'safe', 'processedBy');

        $commissions = collect();
        if ($payroll->employee) {
            $commissions = $this->commissionsForEmployeePeriod(
                $payroll->employee,
                (int) $payroll->month,
                (int) $payroll->year
            );
        }

        $payroll->calculated_commission = (float) $commissions->sum('commission_amount');

        if ($payroll->employee) {
            $payroll->calculated_deductions = $this->calculateDeductionsForEmployeePeriod(
                $payroll->employee,
                (int) $payroll->month,
                (int) $payroll->year
            );

            $totals = $this->calculateGrossAndNet(
                (float) $payroll->basic_salary,
                (float) $payroll->calculated_commission,
                (float) ($payroll->allowances ?? 0),
                (float) $payroll->calculated_deductions
            );

            $payroll->gross_salary = $totals['gross'];
            $payroll->calculated_net_salary = $totals['net'];
        }

        return view('payroll.show', compact('payroll', 'commissions'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::pluck('name', 'id');

        $payroll->loadMissing('employee.branches');
        $this->applyPayrollCalculations($payroll);
        
        return view('payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Auto-fetch commission from employee's commission records
        $month = (int) $payroll->month;
        $year = (int) $payroll->year;

        $employee = $payroll->employee()->with('branches')->firstOrFail();
        $commission = $this->calculateCommissionForEmployeePeriod($employee, $month, $year);

        $allowances = (float) ($data['allowances'] ?? 0);
        $deductions = array_key_exists('deductions', $data) && $data['deductions'] !== null
            ? (float) $data['deductions']
            : (float) $payroll->deductions;
        $totals = $this->calculateGrossAndNet((float) $data['basic_salary'], $commission, $allowances, $deductions);

        $data['commission'] = $commission;
        $data['allowances'] = $allowances;
        $data['deductions'] = $deductions;
        $data['net_salary'] = $totals['net'];

        $payroll->update($data);

        return redirect()->route('payroll.index')->with('success', __('messages.updated'));
    }

    public function pay(Request $request, Payroll $payroll)
    {
        if ($payroll->isPaid()) {
            return back()->with('warning', 'Payroll is already paid.');
        }

        $data = $request->validate([
            'safe_id' => 'required|exists:safes,id,is_active,1',
        ]);

        $payroll->loadMissing('employee.branches');
        $employee = $payroll->employee()->with('branches')->firstOrFail();
        $safe = Safe::findOrFail($data['safe_id']);

        $commission = $this->calculateCommissionForEmployeePeriod($employee, (int) $payroll->month, (int) $payroll->year);
        $allowances = (float) ($payroll->allowances ?? 0);
        $deductions = (float) ($payroll->deductions ?? 0);
        $totals = $this->calculateGrossAndNet((float) $payroll->basic_salary, $commission, $allowances, $deductions);

        if ((float) $safe->balance < (float) $totals['net']) {
            return back()->withErrors(['safe_id' => 'Selected safe does not have enough balance for this payroll payment.']);
        }

        DB::transaction(function () use ($payroll, $employee, $safe, $commission, $allowances, $deductions, $totals) {
            $now = now();
            $month = (int) $payroll->month;
            $year = (int) $payroll->year;

            $payroll->update([
                'commission' => $commission,
                'allowances' => $allowances,
                'deductions' => $deductions,
                'net_salary' => $totals['net'],
                'status' => 'paid',
                'safe_id' => $safe->id,
                'processed_by' => auth()->id(),
                'processed_at' => $now,
            ]);

            SafeOutcome::create([
                'safe_id' => $safe->id,
                'amount' => $totals['net'],
                'description' => sprintf('Payroll payment for %s (%s/%s)', $employee->name, $month, $year),
                'reference' => 'Payroll #' . $payroll->id,
                'reference_type' => 'payroll',
            ]);

            $safe->update([
                'balance' => (float) $safe->balance - (float) $totals['net'],
            ]);

            Commission::query()
                ->where('employee_id', $employee->id)
                ->whereMonth('commission_date', $month)
                ->whereYear('commission_date', $year)
                ->where('status', '!=', 'paid')
                ->get()
                ->each
                ->markAsPaid();

            $employeeCommission = $employee->getOrCreateCommission($month, $year);
            $employeeCommission->update([
                'status' => 'paid',
                'paid_at' => $now,
            ]);
        });

        return redirect()->route('payroll.index')->with('success', 'Payroll marked as paid successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        DB::transaction(function () use ($payroll) {
            $payroll->loadMissing('employee.branches', 'safe');

            if ($payroll->isPaid() && $payroll->safe) {
                $reverseAmount = (float) ($payroll->net_salary ?? $payroll->calculateNetSalary());

                SafeOutcome::query()
                    ->where('safe_id', $payroll->safe_id)
                    ->where('reference', 'Payroll #' . $payroll->id)
                    ->delete();

                $payroll->safe->update([
                    'balance' => (float) $payroll->safe->balance + $reverseAmount,
                ]);

                $employee = $payroll->employee;
                if ($employee) {
                    Commission::query()
                        ->where('employee_id', $employee->id)
                        ->whereMonth('commission_date', (int) $payroll->month)
                        ->whereYear('commission_date', (int) $payroll->year)
                        ->update(['status' => 'pending']);

                    EmployeeCommission::query()
                        ->where('employee_id', $employee->id)
                        ->where('month', (int) $payroll->month)
                        ->where('year', (int) $payroll->year)
                        ->update([
                            'status' => 'pending',
                            'paid_at' => null,
                        ]);
                }
            }

            $payroll->delete();
        });

        return redirect()->route('payroll.index')->with('success', __('messages.deleted'));
    }

    public function downloadPayslip(Request $request, Payroll $payroll)
    {
        $this->authorizeDownloads($request);

        $payroll->loadMissing('employee.branches');
        $commission = $payroll->employee
            ? $this->calculateCommissionForEmployeePeriod($payroll->employee, (int) $payroll->month, (int) $payroll->year)
            : (float) $payroll->commission;

        $deductions = $payroll->employee
            ? $this->calculateDeductionsForEmployeePeriod($payroll->employee, (int) $payroll->month, (int) $payroll->year)
            : (float) $payroll->deductions;

        $totals = $this->calculateGrossAndNet(
            (float) $payroll->basic_salary,
            (float) $commission,
            (float) ($payroll->allowances ?? 0),
            (float) $deductions
        );

        $lines = [
            'Employee: ' . ($payroll->employee?->name ?? '-'),
            'Month/Year: ' . $payroll->month . '/' . $payroll->year,
            'Basic Salary: ' . number_format((float) $payroll->basic_salary, 2),
            'Commission: ' . number_format((float) $commission, 2),
            'Allowances: ' . number_format((float) $payroll->allowances, 2),
            'Total Before Deductions: ' . number_format((float) $totals['gross'], 2),
            'Deductions: ' . number_format((float) $deductions, 2),
            'Net Salary: ' . number_format((float) $totals['net'], 2),
        ];

        $pdf = SimplePdf::textDocument('Payslip ' . ($payroll->employee?->name ?? 'Employee'), $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="payslip-' . $payroll->id . '.pdf"',
        ]);
    }

    public function downloadPayslipExcel(Request $request, Payroll $payroll)
    {
        $this->authorizeDownloads($request);

        $payroll->loadMissing('employee.branches');
        $commission = $payroll->employee
            ? $this->calculateCommissionForEmployeePeriod($payroll->employee, (int) $payroll->month, (int) $payroll->year)
            : (float) $payroll->commission;

        $deductions = $payroll->employee
            ? $this->calculateDeductionsForEmployeePeriod($payroll->employee, (int) $payroll->month, (int) $payroll->year)
            : (float) $payroll->deductions;

        $totals = $this->calculateGrossAndNet(
            (float) $payroll->basic_salary,
            (float) $commission,
            (float) ($payroll->allowances ?? 0),
            (float) $deductions
        );

        $headers = ['Description', 'Amount'];
        $rows = [
            ['Employee', $payroll->employee?->name ?? '-'],
            ['Month/Year', $payroll->month . '/' . $payroll->year],
            ['Basic Salary', number_format((float) $payroll->basic_salary, 2)],
            ['Commission', number_format((float) $commission, 2)],
            ['Allowances', number_format((float) $payroll->allowances, 2)],
            ['Total Before Deductions', number_format((float) $totals['gross'], 2)],
            ['Deductions', number_format((float) $deductions, 2)],
            ['Net Salary', number_format((float) $totals['net'], 2)],
        ];

        $metadata = [
            'Employee' => $payroll->employee?->name ?? '-',
            'Period' => $payroll->month . '/' . $payroll->year,
        ];

        $excel = SimpleExcel::createFromTable('Payslip ' . ($payroll->employee?->name ?? 'Employee'), $headers, $rows, $metadata);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        return response($excel, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="payslip-' . $payroll->id . '.xlsx"',
        ]);
    }
}



