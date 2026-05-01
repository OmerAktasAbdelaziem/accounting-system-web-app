$root = 'd:\accounting system web app\aktas-system'

function Write-Text([string]$Path, [string]$Text) {
    [IO.File]::WriteAllText($Path, $Text)
}

$invoicePath = Join-Path $root 'app\Http\Controllers\InvoiceController.php'
$invoice = [IO.File]::ReadAllText($invoicePath)
$oldInvoice = @'
    // Placeholder PDF download — implement with Dompdf/laravel-dompdf later
    public function downloadPdf(Invoice $invoice)
    {
        // If Dompdf is installed, generate a downloadable PDF
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) || app()->bound('dompdf.wrapper')) {
            try {
                if (app()->bound('dompdf.wrapper')) {
                    $pdf = app('dompdf.wrapper');
                    $html = view('invoices.pdf', compact('invoice'))->render();
                    $pdf->loadHTML($html);
                    return $pdf->download($invoice->invoice_number . '.pdf');
                }

                if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', compact('invoice'));
                    return $pdf->download($invoice->invoice_number . '.pdf');
                }
            } catch (\Exception $e) {
                logger()->error('PDF generation failed: ' . $e->getMessage());
            }
        }

        // Fallback: render HTML view
        return view('invoices.pdf', compact('invoice'));
    }
}
'@
$newInvoice = @'
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
$invoice = $invoice.Replace($oldInvoice, $newInvoice)
Write-Text $invoicePath $invoice

$payrollPath = Join-Path $root 'app\Http\Controllers\PayrollController.php'
$payroll = [IO.File]::ReadAllText($payrollPath)
$oldProcess = @'
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

        return redirect()->route('payroll.show', $payroll)->with('success', __('Payroll processed'));
    }
}
'@
$newProcess = @'
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
$payroll = $payroll.Replace($oldProcess, $newProcess)
Write-Text $payrollPath $payroll

$routesPath = Join-Path $root 'routes\web.php'
$routes = [IO.File]::ReadAllText($routesPath)
$oldRoutes = @'
        Route::delete('{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
        Route::post('{payroll}/process', [PayrollController::class, 'process'])->name('process');
    });
'@
$newRoutes = @'
        Route::delete('{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
        Route::post('{payroll}/process', [PayrollController::class, 'process'])->name('process');
        Route::get('{payroll}/payslip', [PayrollController::class, 'downloadPayslip'])->name('payslip');
    });
'@
$routes = $routes.Replace($oldRoutes, $newRoutes)
Write-Text $routesPath $routes
