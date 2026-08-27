<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Support\SimplePdf;
use App\Support\SimpleExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!\App\Traits\ChecksFeatureAccess::hasFeatureAccess('invoicing')) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index()
    {
        $invoices = Invoice::latest()->paginate(20);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::pluck('name', 'id');
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = request()->input('branch_ids', []);
        return view('invoices.create', compact('customers', 'branches', 'selectedBranchIds'));
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
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'items' => 'nullable|array',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ]);

        $data['invoice_number'] = strtoupper('INV-' . Str::random(6));

        $invoice = Invoice::create($data);
        $invoice->syncBranches($data['branch_ids'] ?? []);

        // Add line items if provided
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                if (isset($item['quantity']) && isset($item['unit_price'])) {
                    $invoice->addItem(
                        $item['product_id'] ?? null,
                        $item['quantity'],
                        $item['unit_price']
                    );
                }
            }
            $invoice->recalculateTotals();
        }

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
        $invoice->load('items.product');
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $customers = Customer::pluck('name', 'id');
        $invoice->load('items');
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = $invoice->branches()->pluck('branches.id')->all();
        return view('invoices.edit', compact('invoice', 'customers', 'branches', 'selectedBranchIds'));
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

    public function downloadPdf(Request $request, Invoice $invoice)
    {
        $this->authorizeDownloads($request);

        $invoice->load('items.product');

        $lines = [
            'Invoice Number: ' . $invoice->invoice_number,
            'Customer: ' . ($invoice->customer?->name ?? '-'),
            'Date: ' . ($invoice->date ? $invoice->date->format('Y-m-d') : now()->toDateString()),
            '',
            'Line Items:',
        ];

        foreach ($invoice->items as $item) {
            $lines[] = sprintf(
                '%s | Qty: %d | Unit: $%s | Total: $%s',
                $item->product?->name ?? 'Item ' . $item->id,
                $item->quantity,
                number_format((float) $item->unit_price, 2),
                number_format((float) $item->line_total, 2)
            );
        }

        $lines[] = '';
        $lines[] = 'Sub Total: ' . number_format((float) $invoice->sub_total, 2);
        $lines[] = 'Tax: ' . number_format((float) $invoice->tax, 2);
        $lines[] = 'Total: ' . number_format((float) $invoice->total, 2);

        $pdf = SimplePdf::textDocument('Invoice ' . $invoice->invoice_number, $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }

    public function downloadExcel(Request $request, Invoice $invoice)
    {
        $this->authorizeDownloads($request);

        $invoice->load('items.product');

        $headers = ['Product', 'Quantity', 'Unit Price', 'Line Total'];
        $rows = [];

        foreach ($invoice->items as $item) {
            $rows[] = [
                $item->product?->name ?? 'Item ' . $item->id,
                $item->quantity,
                number_format((float) $item->unit_price, 2),
                number_format((float) $item->line_total, 2),
            ];
        }

        // Add totals as additional rows
        $rows[] = ['', '', 'Sub Total', number_format((float) $invoice->sub_total, 2)];
        $rows[] = ['', '', 'Tax', number_format((float) $invoice->tax, 2)];
        $rows[] = ['', '', 'Total', number_format((float) $invoice->total, 2)];

        $metadata = [
            'Invoice Number' => $invoice->invoice_number,
            'Customer' => $invoice->customer?->name ?? '-',
            'Date' => $invoice->date ? $invoice->date->format('Y-m-d') : now()->toDateString(),
        ];

        $excel = SimpleExcel::createFromTable('Invoice ' . $invoice->invoice_number, $headers, $rows, $metadata);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        return response($excel, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $invoice->invoice_number . '.xlsx"',
        ]);
    }
}