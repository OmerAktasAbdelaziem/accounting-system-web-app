<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\SupplierPurchase;
use App\Support\SimplePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::query()
            ->withSum('purchases as total_purchased', 'total_amount')
            ->withSum('payments as total_paid', 'amount')
            ->latest()
            ->paginate(20);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = request()->input('branch_ids', []);
        return view('suppliers.create', compact('branches', 'selectedBranchIds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $supplier = Supplier::create($data);
        $supplier->syncBranches($data['branch_ids'] ?? []);

        return redirect()->route('suppliers.index')->with('success', __('messages.created'));
    }

    public function show(Request $request, Supplier $supplier)
    {
        $branchId = $request->integer('branch_id') ?: null;
        if ($branchId && ! $supplier->branches()->whereKey($branchId)->exists()) {
            abort(404);
        }

        $ledger = $this->buildSupplierLedger($supplier, $branchId, 30);
        $branches = $supplier->branches()->orderBy('name')->get();

        return view('suppliers.show', array_merge($ledger, [
            'supplier' => $supplier,
            'branches' => $branches,
            'selectedBranchId' => $branchId,
        ]));
    }

    public function edit(Supplier $supplier)
    {
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = $supplier->branches()->pluck('branches.id')->all();
        return view('suppliers.edit', compact('supplier', 'branches', 'selectedBranchIds'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $supplier->update($data);
        $supplier->syncBranches($data['branch_ids'] ?? []);

        return redirect()->route('suppliers.index')->with('success', __('messages.updated'));
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', __('messages.deleted'));
    }

    public function storePurchase(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'purchase_date' => 'required|date',
            'note' => 'nullable|string',
            'product_name' => 'required|array|min:1',
            'product_name.*' => 'required|string|max:255',
            'weight' => 'required|array|min:1',
            'weight.*' => 'required|numeric|min:0.001',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($validated, $supplier) {
            $purchase = SupplierPurchase::create([
                'supplier_id' => $supplier->id,
                'branch_id' => $validated['branch_id'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'note' => $validated['note'] ?? null,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($validated['product_name'] as $index => $productName) {
                $weight = (float) ($validated['weight'][$index] ?? 0);
                $unitPrice = (float) ($validated['unit_price'][$index] ?? 0);
                $lineTotal = $weight * $unitPrice;
                $total += $lineTotal;

                $purchase->items()->create([
                    'product_name' => $productName,
                    'weight' => $weight,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $purchase->update(['total_amount' => $total]);
        });

        return redirect()->route('suppliers.show', array_merge(['supplier' => $supplier], request()->only('branch_id')))->with('success', 'Purchase saved successfully.');
    }

    public function storePayment(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $currentOutstanding = ((float) $supplier->opening_balance + (float) $supplier->purchases()->sum('total_amount'))
            - (float) $supplier->payments()->sum('amount');

        if ((float) $validated['amount'] > max($currentOutstanding, 0)) {
            return back()->withErrors([
                'amount' => 'Payment amount cannot be greater than outstanding balance.',
            ])->withInput();
        }

        SupplierPayment::create([
            'supplier_id' => $supplier->id,
            'branch_id' => $validated['branch_id'] ?? null,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('suppliers.show', array_merge(['supplier' => $supplier], request()->only('branch_id')))->with('success', 'Payment saved successfully.');
    }

    public function updatePurchase(Request $request, Supplier $supplier, SupplierPurchase $purchase)
    {
        abort_unless($purchase->supplier_id === $supplier->id, 404);

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'purchase_date' => 'required|date',
            'note' => 'nullable|string',
            'product_name' => 'required|array|min:1',
            'product_name.*' => 'required|string|max:255',
            'weight' => 'required|array|min:1',
            'weight.*' => 'required|numeric|min:0.001',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($purchase, $validated) {
            $purchase->items()->delete();

            $total = 0;
            foreach ($validated['product_name'] as $index => $productName) {
                $weight = (float) ($validated['weight'][$index] ?? 0);
                $unitPrice = (float) ($validated['unit_price'][$index] ?? 0);
                $lineTotal = $weight * $unitPrice;
                $total += $lineTotal;

                $purchase->items()->create([
                    'product_name' => $productName,
                    'weight' => $weight,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ]);
            }

            $purchase->update([
                'branch_id' => $validated['branch_id'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'note' => $validated['note'] ?? null,
                'total_amount' => $total,
            ]);
        });

        return redirect()->route('suppliers.show', array_merge(['supplier' => $supplier], request()->only('branch_id')))->with('success', 'Purchase updated successfully.');
    }

    public function destroyPurchase(Supplier $supplier, SupplierPurchase $purchase)
    {
        abort_unless($purchase->supplier_id === $supplier->id, 404);
        $purchase->delete();

        return redirect()->route('suppliers.show', array_merge(['supplier' => $supplier], request()->only('branch_id')))->with('success', 'Purchase deleted successfully.');
    }

    public function updatePayment(Request $request, Supplier $supplier, SupplierPayment $payment)
    {
        abort_unless($payment->supplier_id === $supplier->id, 404);

        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
        ]);

        $payment->update([
            'branch_id' => $validated['branch_id'] ?? null,
            'payment_date' => $validated['payment_date'],
            'amount' => $validated['amount'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('suppliers.show', array_merge(['supplier' => $supplier], request()->only('branch_id')))->with('success', 'Payment updated successfully.');
    }

    public function destroyPayment(Supplier $supplier, SupplierPayment $payment)
    {
        abort_unless($payment->supplier_id === $supplier->id, 404);
        $payment->delete();

        return redirect()->route('suppliers.show', array_merge(['supplier' => $supplier], request()->only('branch_id')))->with('success', 'Payment deleted successfully.');
    }

    public function statementPdf(Request $request, Supplier $supplier)
    {
        $branchId = $request->integer('branch_id') ?: null;
        if ($branchId && ! $supplier->branches()->whereKey($branchId)->exists()) {
            abort(404);
        }

        $ledger = $this->buildSupplierLedger($supplier, $branchId, 1000);

        $branchName = 'All branches';
        if ($branchId) {
            $branchName = optional($supplier->branches()->whereKey($branchId)->first())->name ?? 'Selected branch';
        }

        $lines = [
            'Supplier: ' . $supplier->name,
            'Branch filter: ' . $branchName,
            'Opening balance: ' . number_format((float) $ledger['openingBalance'], 2),
            'Total purchased: ' . number_format($ledger['totalPurchased'], 2),
            'Total paid: ' . number_format($ledger['totalPaid'], 2),
            'Outstanding: ' . number_format($ledger['outstanding'], 2),
            '--- Ledger ---',
        ];

        foreach ($ledger['timeline'] as $entry) {
            if ($entry['kind'] === 'purchase') {
                $lines[] = 'Purchase ' . Carbon::parse($entry['date'])->format('Y-m-d') . ' | ' . number_format($entry['amount'], 2) . ' | ' . ($entry['model']->note ?? '');
                foreach ($entry['model']->items as $item) {
                    $lines[] = '  - ' . $item->product_name . ' | ' . number_format((float) $item->weight, 3) . 'kg | ' . number_format((float) $item->unit_price, 2) . ' | ' . number_format((float) $item->line_total, 2);
                }
            } else {
                $payment = $entry['model'];
                $lines[] = 'Payment ' . Carbon::parse($entry['date'])->format('Y-m-d') . ' | ' . number_format($entry['amount'], 2) . ' | ' . ($payment->note ?? '');
            }
        }

        $pdf = SimplePdf::textDocument('Supplier Statement - ' . $supplier->name, $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="supplier-statement-' . $supplier->id . '.pdf"',
        ]);
    }

    private function buildSupplierLedger(Supplier $supplier, ?int $branchId = null, int $limit = 30): array
    {
        $purchaseQuery = $supplier->purchases()->with(['items', 'branch'])->orderByDesc('purchase_date');
        $paymentQuery = $supplier->payments()->with(['branch'])->orderByDesc('payment_date');

        if ($branchId) {
            $purchaseQuery->where('branch_id', $branchId);
            $paymentQuery->where('branch_id', $branchId);
        }

        $purchases = $purchaseQuery->take($limit)->get();
        $payments = $paymentQuery->take($limit)->get();

        $totalPurchased = (float) ($branchId
            ? $supplier->purchases()->where('branch_id', $branchId)->sum('total_amount')
            : $supplier->purchases()->sum('total_amount'));

        $totalPaid = (float) ($branchId
            ? $supplier->payments()->where('branch_id', $branchId)->sum('amount')
            : $supplier->payments()->sum('amount'));

        $openingBalance = $branchId
            ? ((int) $supplier->branch_id === (int) $branchId ? (float) $supplier->opening_balance : 0.0)
            : (float) $supplier->opening_balance;

        $outstanding = ($openingBalance + $totalPurchased) - $totalPaid;

        $timeline = $purchases
            ->map(function ($purchase) {
                return [
                    'kind' => 'purchase',
                    'date' => Carbon::parse($purchase->purchase_date),
                    'amount' => (float) $purchase->total_amount,
                    'model' => $purchase,
                ];
            })
            ->concat($payments->map(function ($payment) {
                return [
                    'kind' => 'payment',
                    'date' => Carbon::parse($payment->payment_date),
                    'amount' => (float) $payment->amount,
                    'model' => $payment,
                ];
            }))
            ->sortByDesc(function (array $entry) {
                return $entry['date']->timestamp;
            })
            ->values();

        return compact('purchases', 'payments', 'timeline', 'totalPurchased', 'totalPaid', 'outstanding', 'openingBalance');
    }
}
