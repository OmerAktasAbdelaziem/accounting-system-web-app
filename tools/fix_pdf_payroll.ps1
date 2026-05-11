$root = 'd:\accounting system web app\aktas-system'

function Set-Text([string]$Path, [string]$Text) {
    [IO.File]::WriteAllText($Path, $Text)
}

# Invoice controller
$invoicePath = Join-Path $root 'app\Http\Controllers\InvoiceController.php'
$invoice = [IO.File]::ReadAllText($invoicePath)
$invoice = $invoice -replace '(?s)    // Placeholder PDF download.*?    public function downloadPdf\(Invoice \$invoice\)\s*\{.*?\n    \}\r?\n\}', @'
    public function downloadPdf(Invoice $invoice)
    {
        $lines = [
            'Invoice Number: ' . $invoice->invoice_number,
            'Customer: ' . ($invoice->customer?->name ?? '-'),
            'Date: ' . (($invoice->date ? $invoice->date->format('Y-m-d') : now()->toDateString())),
            'Sub Total: ' . number_format((float) $invoice->sub_total, 2),
            'Tax: ' . number_format((float) $invoice->tax, 2),
            'Total: ' . number_format((float) $invoice->total, 2),
        ];

        $pdf = SimplePdf::textDocument('Invoice ' . $invoice->invoice_number, $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $invoice->invoice_number + '.pdf"',
        ]);
    }
}
'@
Set-Text $invoicePath $invoice

# Payroll controller
$payrollPath = Join-Path $root 'app\Http\Controllers\PayrollController.php'
$payroll = [IO.File]::ReadAllText($payrollPath)
$payroll = $payroll.Replace('nuse App\Support\SimplePdf;', 'use App\Support\SimplePdf;')
$payroll = $payroll -replace '(?s)    public function process\(Payroll \$payroll\)\s*\{.*?\n    \}\r?\n\}', @'
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
            $expense = ChartOfAccount::where('account_code', '5020')->first();
            $payable = ChartOfAccount::where('account_code', '2020')->first();

            if ($expense && $payable) {
                $journalEntry = JournalEntry::create([
                    'date' => now(),
                    'description' => 'Payroll for ' . ($payroll->employee?->name ?? 'Employee'),
                    'reference_number' => 'PAY-' . $payroll->id,
                    'reference_type' => 'payroll',
                    'reference_id' => $payroll->id,
                    'created_by' => auth()->id(),
                    'status' => 'draft',
                ]);

                $journalEntry->addItem($expense->id, $payroll->net_salary, 0, 'Payroll expense');
                $journalEntry->addItem($payable->id, 0, $payroll->net_salary, 'Salaries payable');
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
            'Month/Year: ' . $payroll->month + '/' . $payroll->year,
            'Basic Salary: ' . number_format((float) $payroll->basic_salary, 2),
            'Allowances: ' . number_format((float) $payroll->allowances, 2),
            'Deductions: ' . number_format((float) $payroll->deductions, 2),
            'Net Salary: ' . number_format((float) $payroll->net_salary, 2),
            'Status: ' . $payroll->status,
        ];

        $pdf = SimplePdf::textDocument('Payslip ' . ($payroll->employee?->name ?? 'Employee'), $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="payslip-' . $payroll->id + '.pdf"',
        ]);
    }
}
'@
Set-Text $payrollPath $payroll

# Routes
$routesPath = Join-Path $root 'routes\web.php'
$routes = [IO.File]::ReadAllText($routesPath)
$routes = $routes -replace "        Route::delete\('\{payroll\}', \[PayrollController::class, 'destroy'\]\)->name\('destroy'\);\r?\n        Route::post\('\{payroll\}/process', \[PayrollController::class, 'process'\]\)->name\('process'\);", "        Route::delete('{payroll}', [PayrollController::class, 'destroy'])->name('destroy');`r`n        Route::post('{payroll}/process', [PayrollController::class, 'process'])->name('process');`r`n        Route::get('{payroll}/payslip', [PayrollController::class, 'downloadPayslip'])->name('payslip');"
Set-Text $routesPath $routes

# Views
$invShowPath = Join-Path $root 'resources\views\invoices\show.blade.php'
$invShow = [IO.File]::ReadAllText($invShowPath)
$invShow = $invShow.Replace("<h3>{{ $invoice->invoice_number }}</h3>", "<div class=\"d-flex justify-content-between align-items-center\">`r`n        <h3>{{ $invoice->invoice_number }}</h3>`r`n        <a href=\"{{ route('invoices.pdf', $invoice) }}\" class=\"btn btn-outline-danger\">{{ __('messages.download_pdf') }}</a>`r`n    </div>")
Set-Text $invShowPath $invShow

$payShowPath = Join-Path $root 'resources\views\payroll\show.blade.php'
$payShow = [IO.File]::ReadAllText($payShowPath)
$payShow = $payShow.Replace("<h3>{{ __('Payroll') }} - {{ $payroll->employee?->name }}</h3>", "<div class=\"d-flex justify-content-between align-items-center\">`r`n        <h3>{{ __('Payroll') }} - {{ $payroll->employee?->name }}</h3>`r`n        <div class=\"d-flex gap-2\">`r`n            <a href=\"{{ route('payroll.payslip', $payroll) }}\" class=\"btn btn-outline-danger\">{{ __('messages.download_pdf') }}</a>`r`n            @if($payroll->status === 'draft')`r`n                <form action=\"{{ route('payroll.process', $payroll) }}\" method=\"POST\" class=\"d-inline\">`r`n                    @csrf`r`n                    <button class=\"btn btn-success\">{{ __('messages.process') }}</button>`r`n                </form>`r`n            @endif`r`n        </div>`r`n    </div>")
Set-Text $payShowPath $payShow
