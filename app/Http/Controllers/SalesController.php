<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeSale;
use App\Models\EmployeeSaleDetail;
use App\Support\SimplePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!\App\Traits\ChecksFeatureAccess::hasFeatureAccess('sales')) {
                abort(403);
            }

            return $next($request);
        })->only(['index', 'store', 'edit', 'update', 'exportPdf']);
    }

    public function index(Request $request)
    {
        $sales = EmployeeSale::with(['branch', 'employee', 'employeeSaleDetails.employee'])
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->integer('branch_id'));
            })
            ->when($request->filled('from_date'), function ($query) use ($request) {
                $query->whereDate('sale_date', '>=', $request->input('from_date'));
            })
            ->when($request->filled('to_date'), function ($query) use ($request) {
                $query->whereDate('sale_date', '<=', $request->input('to_date'));
            })
            ->latest('sale_date')
            ->latest('id')
            ->paginate(6)
            ->withQueryString();

        $stats = [
            'count' => EmployeeSale::count(),
            'gross_total' => (float) EmployeeSale::sum('total_amount'),
            'spent_total' => (float) EmployeeSale::sum('spent_amount'),
        ];
        $stats['net_total'] = $stats['gross_total'] - $stats['spent_total'];

        $branches = Branch::orderBy('name')->get();
        $employees = Employee::active()->orderBy('name')->get();

        return view('sales.index', compact('sales', 'stats', 'branches', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'branch_id' => 'required|exists:branches,id',
            'spent_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0.01',
            // employee_sales is optional now
            'employee_sales' => 'nullable|array',
            'employee_sales.*.employee_id' => 'sometimes|nullable|exists:employees,id',
            'employee_sales.*.amount' => 'sometimes|nullable|numeric|min:0.01',
            'notes' => 'nullable|string|max:2000',
        ]);

        $employeeSales = collect($validated['employee_sales'] ?? [])->filter(fn ($row) => !empty($row['employee_id']))->values();
        $totalAmount = (float) $validated['total_amount'];

        $sale = DB::transaction(function () use ($validated, $employeeSales, $totalAmount, $request) {
            // Determine a primary employee for the sale: prefer first breakdown, else try to use current user's employee, else fallback to first employee record
            $primaryEmployeeId = null;
            if ($employeeSales->isNotEmpty()) {
                $primaryEmployeeId = (int) $employeeSales->first()['employee_id'];
            } else {
                $primaryEmployeeId = Employee::where('email', $request->user()?->email)->first()?->id ?? Employee::first()?->id;
            }

            $sale = EmployeeSale::create([
                'employee_id' => $primaryEmployeeId,
                'product_id' => null,
                'quantity' => 1,
                'unit_price' => $totalAmount,
                'total_amount' => $totalAmount,
                'spent_amount' => (float) ($validated['spent_amount'] ?? 0),
                'sale_date' => $validated['sale_date'],
                'branch_id' => $validated['branch_id'],
                'sale_reference' => null,
                'notes' => $validated['notes'] ?? null,
                'notes_ar' => null,
            ]);

            foreach ($employeeSales as $row) {
                $sale->employeeSaleDetails()->create([
                    'employee_id' => (int) $row['employee_id'],
                    'amount' => (float) ($row['amount'] ?? 0),
                    'description' => null,
                ]);
            }

            return $sale;
        });

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function edit(EmployeeSale $sale)
    {
        $branches = Branch::orderBy('name')->get();
        $employees = Employee::active()->orderBy('name')->get();
        return view('sales.edit', compact('sale', 'branches'));
    }

    public function update(Request $request, EmployeeSale $sale)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date|before_or_equal:today',
            'branch_id' => 'required|exists:branches,id',
            'spent_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0.01',
            // employee_sales is optional now
            'employee_sales' => 'nullable|array',
            'employee_sales.*.employee_id' => 'sometimes|nullable|exists:employees,id',
            'employee_sales.*.amount' => 'sometimes|nullable|numeric|min:0.01',
            'notes' => 'nullable|string|max:2000',
        ]);

        $employeeSales = collect($validated['employee_sales'] ?? [])->filter(fn ($row) => !empty($row['employee_id']))->values();
        $totalAmount = (float) $validated['total_amount'];

        DB::transaction(function () use ($sale, $validated, $employeeSales, $totalAmount, $request) {
            $primaryEmployeeId = null;
            if ($employeeSales->isNotEmpty()) {
                $primaryEmployeeId = (int) $employeeSales->first()['employee_id'];
            } else {
                $primaryEmployeeId = Employee::where('email', $request->user()?->email)->first()?->id ?? Employee::first()?->id;
            }

            $sale->update([
                'employee_id' => $primaryEmployeeId,
                'quantity' => 1,
                'unit_price' => $totalAmount,
                'total_amount' => $totalAmount,
                'spent_amount' => (float) ($validated['spent_amount'] ?? 0),
                'sale_date' => $validated['sale_date'],
                'branch_id' => $validated['branch_id'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $sale->employeeSaleDetails()->delete();

            foreach ($employeeSales as $row) {
                $sale->employeeSaleDetails()->create([
                    'employee_id' => (int) $row['employee_id'],
                    'amount' => (float) ($row['amount'] ?? 0),
                    'description' => null,
                ]);
            }
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function exportPdf(Request $request)
    {
        $this->authorizeDownloads($request);

        $validated = $request->validate([
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);

        $query = EmployeeSale::with(['branch', 'employee', 'employeeSaleDetails.employee'])
            ->latest('sale_date')
            ->latest('id');

        // Apply filters
        if (!empty($validated['branch_id'])) {
            $query->where('branch_id', (int) $validated['branch_id']);
        }
        if (!empty($validated['from_date'])) {
            $query->whereDate('sale_date', '>=', $validated['from_date']);
        }
        if (!empty($validated['to_date'])) {
            $query->whereDate('sale_date', '<=', $validated['to_date']);
        }

        $sales = $query->get();

        if ($sales->isEmpty()) {
            return redirect()->route('sales.index')->with('error', 'Secilen tarih araliginda satis bulunamadi.');
        }

        $currencySymbol = config('app.currency_symbol', '$');
        $lines = [
            'Satış Dışa Aktarımı',
            'Olusturuldu: ' . now()->format('Y-m-d H:i'),
            'Tarih Araligi: ' . ($validated['from_date'] ?? 'Başlangıç') . ' - ' . ($validated['to_date'] ?? 'Son'),
            'Giri_ler: ' . $sales->count(),
            str_repeat('-', 72),
        ];

        foreach ($sales as $sale) {
            $lines[] = sprintf(
                'Tarih: %s | Şube: %s | Toplam: %s%s | Harcanan: %s%s | Net: %s%s',
                optional($sale->sale_date)->format('Y-m-d') ?? '-',
                $sale->branch?->name ?? '-',
                $currencySymbol,
                number_format((float) $sale->total_amount, 2),
                $currencySymbol,
                number_format((float) ($sale->spent_amount ?? 0), 2),
                $currencySymbol,
                number_format((float) $sale->net_income, 2)
            );

            $lines[] = 'Ana Çalışan: ' . ($sale->employee?->name ?? '-');

            if ($sale->employeeSaleDetails->isNotEmpty()) {
                foreach ($sale->employeeSaleDetails as $detail) {
                    $lines[] = sprintf(
                        '  - %s: %s%s',
                        $detail->employee?->name ?? 'Silinen çalışan',
                        $currencySymbol,
                        number_format((float) $detail->amount, 2)
                    );
                }
            }

            if (!empty($sale->notes)) {
                $lines[] = 'Notlar: ' . trim((string) $sale->notes);
            }

            $lines[] = str_repeat('-', 72);
        }

        $pdf = SimplePdf::textDocument('Satış Raporu Dışa Aktarımı', $lines);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="sales-export-' . now()->format('Y-m-d-His') . '.pdf"',
        ]);
    }
}