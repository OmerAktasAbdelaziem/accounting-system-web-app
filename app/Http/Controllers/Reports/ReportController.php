<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Payroll;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Employee;
use App\Models\SafeOutcome;
use Illuminate\Http\Request;
use App\Support\SimplePdf;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $branchId = $request->query('branch_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $salesData = JournalEntry::where('reference_type', 'invoice')
            ->when($fromDate, fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->latest()
            ->paginate(20);
        return view('reports.sales', compact('salesData', 'branchId', 'fromDate', 'toDate'));
    }

    public function showSale(JournalEntry $sale)
    {
        abort_unless($sale->reference_type === 'invoice', 404);

        $sale->load(['items.account', 'createdBy']);

        return view('reports.sales-show', compact('sale'));
    }

    public function destroySale(JournalEntry $sale)
    {
        abort_unless($sale->reference_type === 'invoice', 404);

        $sale->delete();

        return redirect()->back()->with('success', 'Sales report entry deleted successfully.');
    }

    public function inventory(Request $request)
    {
        $branchId = $request->query('branch_id');
        $products = Product::with('category')
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->paginate(20);
        return view('reports.inventory', compact('products', 'branchId'));
    }

    public function financial(Request $request)
    {
        $branchId = $request->query('branch_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        // Get journal entry items with account details for detailed financial report
        $entries = JournalEntry::with('items.account')
            ->when($fromDate, fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->latest()
            ->paginate(20);

        $payrollSettlements = Payroll::with(['employee', 'safe'])
            ->paid()
            ->when($fromDate, fn ($query) => $query->whereDate('processed_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('processed_at', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->whereHas('employee.branches', function ($branchQuery) use ($branchId) {
                $branchQuery->where('branches.id', $branchId);
            }))
            ->latest('processed_at')
            ->get();

        $commissionSettlements = Commission::with('employee')
            ->where('status', 'paid')
            ->when($fromDate, fn ($query) => $query->whereDate('updated_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('updated_at', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->whereHas('employee.branches', function ($branchQuery) use ($branchId) {
                $branchQuery->where('branches.id', $branchId);
            }))
            ->latest('updated_at')
            ->get();

        $safePayrollOutcomes = SafeOutcome::with('safe')
            ->where('reference_type', 'payroll')
            ->when($fromDate, fn ($query) => $query->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('created_at', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->whereHas('safe.branches', function ($branchQuery) use ($branchId) {
                $branchQuery->where('branches.id', $branchId);
            }))
            ->latest()
            ->get();

        return view('reports.financial', compact('entries', 'branchId', 'fromDate', 'toDate', 'payrollSettlements', 'commissionSettlements', 'safePayrollOutcomes'));
    }

    public function generatePdf(Request $request)
    {
        $this->authorizeDownloads($request);

        $report = $request->input('report', 'sales');
        $format = strtolower((string) $request->input('format', 'pdf'));

        if ($report === 'financial') {
            $lines = $this->buildFinancialLines($request);
            $title = __('messages.financial_report');
        } elseif ($report === 'inventory') {
            $lines = $this->buildInventoryLines($request);
            $title = __('messages.inventory_report');
        } else {
            $lines = $this->buildSalesLines($request);
            $title = __('messages.sales_report');
        }

        if ($format === 'csv' || $format === 'excel') {
            $csv = implode("\n", array_map(function (string $line): string {
                return '"' . str_replace('"', '""', $line) . '"';
            }, $lines));

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $this->exportFilename($report, 'csv') . '"',
            ]);
        }

        $pdf = SimplePdf::textDocument($title, $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->exportFilename($report, 'pdf') . '"',
        ]);
    }

    private function buildSalesLines(Request $request): array
    {
        $branchId = $request->input('branch_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $entries = JournalEntry::where('reference_type', 'invoice')
            ->when($fromDate, fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->latest()
            ->get();

        $lines = [
            'Sales report export',
            'Generated: ' . now()->format('Y-m-d H:i'),
            'Entries: ' . $entries->count(),
        ];

        foreach ($entries as $entry) {
            $lines[] = sprintf(
                '%s | %s | Debit: %s | Credit: %s | %s',
                optional($entry->date)->format('Y-m-d') ?? '-',
                $entry->reference_number ?? '-',
                number_format((float) $entry->total_debit, 2),
                number_format((float) $entry->total_credit, 2),
                $entry->description ?? '-'
            );
        }

        return $lines;
    }

    private function buildFinancialLines(Request $request): array
    {
        $branchId = $request->input('branch_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $entries = JournalEntry::with('items.account')
            ->when($fromDate, fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->latest()
            ->get();

        $payrollSettlements = Payroll::with(['employee', 'safe'])
            ->paid()
            ->when($fromDate, fn ($query) => $query->whereDate('processed_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('processed_at', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->whereHas('employee.branches', function ($branchQuery) use ($branchId) {
                $branchQuery->where('branches.id', $branchId);
            }))
            ->latest('processed_at')
            ->get();

        $commissionSettlements = Commission::with('employee')
            ->where('status', 'paid')
            ->when($fromDate, fn ($query) => $query->whereDate('updated_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('updated_at', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->whereHas('employee.branches', function ($branchQuery) use ($branchId) {
                $branchQuery->where('branches.id', $branchId);
            }))
            ->latest('updated_at')
            ->get();

        $safePayrollOutcomes = SafeOutcome::with('safe')
            ->where('reference_type', 'payroll')
            ->when($fromDate, fn ($query) => $query->whereDate('created_at', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('created_at', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->whereHas('safe.branches', function ($branchQuery) use ($branchId) {
                $branchQuery->where('branches.id', $branchId);
            }))
            ->latest()
            ->get();

        $lines = [
            'Financial report export',
            'Generated: ' . now()->format('Y-m-d H:i'),
            'Entries: ' . $entries->count(),
            'Paid payrolls: ' . $payrollSettlements->count(),
            'Paid commissions: ' . $commissionSettlements->count(),
        ];

        foreach ($entries as $entry) {
            $lines[] = 'Entry: ' . ($entry->reference_number ?? '-') . ' | ' . optional($entry->date)->format('Y-m-d') ?? '-';

            foreach ($entry->items as $item) {
                $lines[] = sprintf(
                    '  %s | Debit: %s | Credit: %s | %s',
                    $item->account->name ?? 'N/A',
                    $item->debit > 0 ? number_format((float) $item->debit, 2) : '-',
                    $item->credit > 0 ? number_format((float) $item->credit, 2) : '-',
                    $item->description ?? '-'
                );
            }
        }

        if ($payrollSettlements->isNotEmpty()) {
            $lines[] = 'Payroll settlements';

            foreach ($payrollSettlements as $payroll) {
                $lines[] = sprintf(
                    '%s | %s | Safe: %s | Net: %s | Paid: %s',
                    optional($payroll->processed_at)->format('Y-m-d') ?? '-',
                    $payroll->employee?->name ?? '-',
                    $payroll->safe?->name ?? '-',
                    number_format((float) $payroll->net_salary, 2),
                    optional($payroll->processed_at)->format('H:i') ?? '-'
                );
            }
        }

        if ($commissionSettlements->isNotEmpty()) {
            $lines[] = 'Commission settlements';

            foreach ($commissionSettlements as $commission) {
                $lines[] = sprintf(
                    '%s | %s | Commission: %s | Status: %s',
                    optional($commission->updated_at)->format('Y-m-d') ?? '-',
                    $commission->employee?->name ?? '-',
                    number_format((float) $commission->commission_amount, 2),
                    $commission->status ?? '-'
                );
            }
        }

        if ($safePayrollOutcomes->isNotEmpty()) {
            $lines[] = 'Payroll safe outcomes';

            foreach ($safePayrollOutcomes as $outcome) {
                $lines[] = sprintf(
                    '%s | Safe: %s | Amount: %s | %s',
                    optional($outcome->created_at)->format('Y-m-d') ?? '-',
                    $outcome->safe?->name ?? '-',
                    number_format((float) $outcome->amount, 2),
                    $outcome->description ?? '-'
                );
            }
        }

        return $lines;
    }

    private function buildInventoryLines(Request $request): array
    {
        $branchId = $request->input('branch_id');

        $products = Product::with('category')
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->get();

        $lines = [
            'Inventory report export',
            'Generated: ' . now()->format('Y-m-d H:i'),
            'Products: ' . $products->count(),
        ];

        foreach ($products as $product) {
            $lines[] = sprintf(
                '%s | Qty: %s | Category: %s',
                $product->name ?? '-',
                $product->quantity ?? '-',
                $product->category->name ?? '-'
            );
        }

        return $lines;
    }

    private function exportFilename(string $report, string $extension): string
    {
        return $report . '-report-' . now()->format('Y-m-d-His') . '.' . $extension;
    }
}
