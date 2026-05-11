<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\Employee;
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
            ->where('status', 'posted')
            ->when($fromDate, fn ($query) => $query->whereDate('date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate('date', '<=', $toDate))
            ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
            ->latest()
            ->paginate(20);
        return view('reports.sales', compact('salesData', 'branchId', 'fromDate', 'toDate'));
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
        return view('reports.financial', compact('entries', 'branchId', 'fromDate', 'toDate'));
    }

    public function generatePdf(Request $request)
    {
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
            ->where('status', 'posted')
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

        $lines = [
            'Financial report export',
            'Generated: ' . now()->format('Y-m-d H:i'),
            'Entries: ' . $entries->count(),
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
