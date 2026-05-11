$root = 'd:\accounting system web app\aktas-system'

function Write-Text([string]$Path, [string]$Text) {
    [IO.File]::WriteAllText($Path, $Text)
}

$invoiceController = @'
<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Support\SimplePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::pluck('name', 'id');
        return view('invoices.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'nullable|date',
            'sub_total' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'branch_id' => 'nullable|integer',
        ]);

        $data['invoice_number'] = strtoupper('INV-' . Str::random(6));

        $invoice = Invoice::create($data);

        try {
            $accountsReceivable = ChartOfAccount::where('account_code', '1020')->first();
            $salesRevenue = ChartOfAccount::where('account_code', '4010')->first();

            if ($accountsReceivable && $salesRevenue) {
                $journalEntry = JournalEntry::create([
                    'date' => $invoice->date ?? now(),
                    'description' => 'Sale Invoice ' . $invoice->invoice_number,
                    'reference_number' => $invoice->invoice_number,
                    'reference_type' => 'invoice',
                    'reference_id' => $invoice->id,
                    'branch_id' => $invoice->branch_id,
                    'created_by' => auth()->id(),
                    'status' => 'draft',
                ]);

                $journalEntry->addItem($accountsReceivable->id, $invoice->total, 0, 'Accounts Receivable');
                $journalEntry->addItem($salesRevenue->id, 0, $invoice->total, 'Sales Revenue');
                $journalEntry->post();
            }
        } catch (\Exception $exception) {
            logger()->error('Failed to create journal entry for invoice: ' . $exception->getMessage());
        }

        return redirect()->route('invoices.index')->with('success', __('messages.created'));
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $customers = Customer::pluck('name', 'id');
        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'date' => 'nullable|date',
            'sub_total' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
        ]);

        $invoice->update($data);

        return redirect()->route('invoices.index')->with('success', __('messages.updated'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', __('messages.deleted'));
    }

    public function downloadPdf(Invoice $invoice)
    {
        $lines = [
            'Invoice Number: ' . $invoice->invoice_number,
            'Customer: ' . ($invoice->customer?->name ?? '-'),
            'Date: ' . ($invoice->date ? $invoice->date->format('Y-m-d') : now()->toDateString()),
            'Sub Total: ' . number_format((float) $invoice->sub_total, 2),
            'Tax: ' . number_format((float) $invoice->tax, 2),
            'Total: ' . number_format((float) $invoice->total, 2),
        ];

        $pdf = SimplePdf::textDocument('Invoice ' . $invoice->invoice_number, $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }
}
'@

$payrollController = @'
<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
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
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['net_salary'] = $data['basic_salary'] + ($data['allowances'] ?? 0) - ($data['deductions'] ?? 0);

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

        $data['net_salary'] = $data['basic_salary'] + ($data['allowances'] ?? 0) - ($data['deductions'] ?? 0);

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
            'Allowances: ' . number_format((float) $payroll->allowances, 2),
            'Deductions: ' . number_format((float) $payroll->deductions, 2),
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
'@

$invoicePath = Join-Path $root 'app\Http\Controllers\InvoiceController.php'
$payrollPath = Join-Path $root 'app\Http\Controllers\PayrollController.php'
Write-Text $invoicePath $invoiceController
Write-Text $payrollPath $payrollController

$routesPath = Join-Path $root 'routes\web.php'
$routes = [IO.File]::ReadAllText($routesPath)
$oldPayrollRoutes = @'
    // Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('create', [PayrollController::class, 'create'])->name('create');
        Route::post('/', [PayrollController::class, 'store'])->name('store');
        Route::get('{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::get('{payroll}/edit', [PayrollController::class, 'edit'])->name('edit');
        Route::put('{payroll}', [PayrollController::class, 'update'])->name('update');
        Route::delete('{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
        Route::post('{payroll}/process', [PayrollController::class, 'process'])->name('process');
    });
'@
$newPayrollRoutes = @'
    // Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('create', [PayrollController::class, 'create'])->name('create');
        Route::post('/', [PayrollController::class, 'store'])->name('store');
        Route::get('{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::get('{payroll}/edit', [PayrollController::class, 'edit'])->name('edit');
        Route::put('{payroll}', [PayrollController::class, 'update'])->name('update');
        Route::delete('{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
        Route::post('{payroll}/process', [PayrollController::class, 'process'])->name('process');
        Route::get('{payroll}/payslip', [PayrollController::class, 'downloadPayslip'])->name('payslip');
    });
'@
$routes = $routes.Replace($oldPayrollRoutes, $newPayrollRoutes)
Write-Text $routesPath $routes
