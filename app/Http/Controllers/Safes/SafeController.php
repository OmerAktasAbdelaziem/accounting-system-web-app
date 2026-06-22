<?php

namespace App\Http\Controllers\Safes;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSafeRequest;
use App\Http\Requests\UpdateSafeRequest;
use App\Models\Branch;
use App\Models\Safe;
use App\Models\SafeTransaction;
use App\Models\SafeIncome;
use App\Models\SafeOutcome;
use App\Models\SafeCurrency;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Support\SimplePdf;

class SafeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!\App\Traits\ChecksFeatureAccess::hasFeatureAccess('safes')) {
                abort(403);
            }

            return $next($request);
        })->only(['index', 'show', 'transactions']);
    }

    public function index()
    {
        $safes = Safe::with('transactions')->paginate(20);
        $stats = [
            'total_balance' => Safe::sum('balance'),
            'total_safes' => Safe::count(),
            'active_safes' => Safe::where('is_active', true)->count(),
        ];
        return view('safes.index', compact('safes', 'stats'));
    }

    public function create()
    {
        $safe = null;
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = request()->input('branch_ids', []);
        return view('safes.form', compact('safe', 'branches', 'selectedBranchIds'));
    }

    public function show(Safe $safe)
    {
        $todayIncome = SafeIncome::where('safe_id', $safe->id)
            ->whereDate('created_at', today())
            ->sum('amount');

        $todayOutcome = SafeOutcome::where('safe_id', $safe->id)
            ->whereDate('created_at', today())
            ->sum('amount');

        $todayNetChange = $todayIncome - $todayOutcome;

        $todayTransactionCount = SafeIncome::where('safe_id', $safe->id)
            ->whereDate('created_at', today())
            ->count() + SafeOutcome::where('safe_id', $safe->id)
            ->whereDate('created_at', today())
            ->count();

        $totalIncome = SafeIncome::where('safe_id', $safe->id)->sum('amount');
        $totalOutcome = SafeOutcome::where('safe_id', $safe->id)->sum('amount');
        $recentIncomes = SafeIncome::where('safe_id', $safe->id)->with('currency')->latest()->get();
        $recentOutcomes = SafeOutcome::where('safe_id', $safe->id)->with(['currency', 'supplier'])->latest()->get();
        $currencies = SafeCurrency::where('safe_id', $safe->id)->where('is_active', true)->get();

        $suppliersWithOutstanding = Supplier::query()
            ->withSum('purchases as total_purchased', 'total_amount')
            ->withSum('payments as total_paid', 'amount')
            ->get()
            ->map(function ($supplier) {
                $opening = (float) ($supplier->opening_balance ?? 0);
                $purchased = (float) ($supplier->total_purchased ?? 0);
                $paid = (float) ($supplier->total_paid ?? 0);
                $supplier->outstanding_amount = $opening + $purchased - $paid;
                return $supplier;
            })
            ->filter(fn ($supplier) => $supplier->outstanding_amount > 0)
            ->sortByDesc('outstanding_amount')
            ->values();

        return view('safes.show', compact('safe', 'todayIncome', 'todayOutcome', 'todayNetChange', 'todayTransactionCount', 'totalIncome', 'totalOutcome', 'recentIncomes', 'recentOutcomes', 'currencies', 'suppliersWithOutstanding'));
    }

    public function store(StoreSafeRequest $request)
    {
        $validated = $request->validated();
        $validated['balance'] = 0;
        $safe = Safe::create($validated);
        $safe->syncBranches($validated['branch_ids'] ?? []);

        return redirect()->route('safes.index')->with('success', 'Safe created successfully!');
    }

    public function edit(Safe $safe)
    {
        $branches = Branch::orderBy('name')->get();
        $selectedBranchIds = $safe->branches()->pluck('branches.id')->all();
        return view('safes.form', compact('safe', 'branches', 'selectedBranchIds'));
    }

    public function update(UpdateSafeRequest $request, Safe $safe)
    {
        $validated = $request->validated();
        $safe->update($validated);
        $safe->syncBranches($validated['branch_ids'] ?? []);
        return redirect()->route('safes.index')->with('success', 'Safe updated successfully!');
    }

    public function destroy(Safe $safe)
    {
        $safe->delete();
        return response()->json(['success' => true]);
    }

    public function transactions(Safe $safe)
    {
        $transactions = $safe->transactions()->with('user')->paginate(20);
        return view('safes.transactions', compact('safe', 'transactions'));
    }

    public function addIncome(Request $request, Safe $safe)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'source' => 'required|in:cash,bank',
            'currency_id' => 'nullable|exists:safe_currencies,id',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'income_date' => 'nullable|date|before_or_equal:today',

        ]);

        $validated['safe_id'] = $safe->id;
        $validated['created_at'] = $request->income_date  // ← زود السطرين دول
                                ? \Carbon\Carbon::parse($request->income_date)
                                : now();
        unset($validated['income_date']);
        SafeIncome::create($validated);

        // Update currency balance if currency is specified
        if ($validated['currency_id']) {
            $currency = SafeCurrency::findOrFail($validated['currency_id']);
            $currency->update(['balance' => $currency->balance + $validated['amount']]);
        }

        $safe->update(['balance' => $safe->balance + $validated['amount']]);

        return back()->with('success', 'Income recorded successfully!');
    }

    public function addOutcome(Request $request, Safe $safe)
    {
        $validated = $request->validate([
        'amount'         => 'required|numeric|min:0.01',
        'description'    => 'nullable|string',
        'currency_id'    => 'nullable|exists:safe_currencies,id',
        'reference'      => 'nullable|string|max:255',
        'reference_type' => 'nullable|in:general,supplier',
        'supplier_id'    => 'nullable|exists:suppliers,id|required_if:reference_type,supplier',
        'outcome_date'   => 'nullable|date|before_or_equal:today',
        ]);

        $referenceType = $validated['reference_type'] ?? 'general';
        $supplierId = $referenceType === 'supplier' ? ($validated['supplier_id'] ?? null) : null;

        // ← زيادة
        $outcomeDate = $request->outcome_date
        ? \Carbon\Carbon::parse($request->outcome_date)
        : now();

        if ($referenceType === 'supplier' && $supplierId) {
        $supplier = Supplier::query()
            ->withSum('purchases as total_purchased', 'total_amount')
            ->withSum('payments as total_paid', 'amount')
            ->findOrFail($supplierId);

        $currentOutstanding = ((float) ($supplier->opening_balance ?? 0)
            + (float) ($supplier->total_purchased ?? 0)
            - (float) ($supplier->total_paid ?? 0));

        if ($currentOutstanding <= 0) {
            return back()->withErrors(['supplier_id' => 'This supplier has no outstanding amount.']);
        }

        if ((float) $validated['amount'] > $currentOutstanding) {
            return back()->withErrors(['amount' => 'Outcome amount cannot be greater than supplier outstanding amount.']);
        }
        }

        DB::transaction(function () use ($safe, $validated, $referenceType, $supplierId, $outcomeDate) {
        $outcome = SafeOutcome::create([
            'safe_id'        => $safe->id,
            'amount'         => $validated['amount'],
            'description'    => $validated['description'] ?? null,
            'currency_id'    => $validated['currency_id'] ?? null,
            'reference'      => $validated['reference'] ?? null,
            'reference_type' => $referenceType,
            'supplier_id'    => $supplierId,
            'created_at'     => $outcomeDate, 
        ]);

        if (!empty($validated['currency_id'])) {
            $currency = SafeCurrency::findOrFail($validated['currency_id']);
            if ($currency->balance >= $validated['amount']) {
                $currency->update(['balance' => $currency->balance - $validated['amount']]);
            }
        }

        if ($referenceType === 'supplier' && $supplierId) {
            SupplierPayment::create([
                'supplier_id'  => $supplierId,
                'payment_date' => $outcomeDate->toDateString(), // ← زيادة عشان الـ payment_date يتطابق
                'amount'       => $validated['amount'],
                'note'         => 'Paid from safe: ' . $safe->name . ' (Outcome #' . $outcome->id . ')',
            ]);
        }

        $safe->update(['balance' => $safe->balance - $validated['amount']]);
        });

        return back()->with('success', 'Outcome recorded successfully!');
    }

    public function addCurrency(Request $request, Safe $safe)
    {
        $validated = $request->validate([
        'code' => 'required|string|max:3|unique:safe_currencies,code,NULL,id,safe_id,' . $safe->id,
            'name' => 'required|string|max:255',
        ]);

        $validated['safe_id'] = $safe->id;
        $validated['balance'] = 0;

        SafeCurrency::create($validated);

        return back()->with('success', 'Currency ' . $validated['code'] . ' added successfully!');
    }

    public function updateIncome(Request $request, Safe $safe, SafeIncome $income)
    {
        if ($income->safe_id !== $safe->id) {
            abort(404);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'source' => 'required|in:cash,bank',
            'currency_id' => 'nullable|exists:safe_currencies,id',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $oldAmount = $income->amount;
        $amountDifference = (float) $validated['amount'] - $oldAmount;

        DB::transaction(function () use ($safe, $income, $validated, $amountDifference) {
            $income->update($validated);

            // Update safe balance
            if ($amountDifference !== 0) {
                $safe->update(['balance' => $safe->balance + $amountDifference]);
            }

            // Update currency balance if needed
            if ($validated['currency_id']) {
                $currency = SafeCurrency::findOrFail($validated['currency_id']);
                $currency->update(['balance' => $currency->balance + $amountDifference]);
            }
        });

        return back()->with('success', 'Income updated successfully!');
    }

    public function deleteIncome(Safe $safe, SafeIncome $income)
    {
        if ($income->safe_id !== $safe->id) {
            abort(404);
        }

        DB::transaction(function () use ($safe, $income) {
            $amount = $income->amount;
            
            // Update safe balance
            $safe->update(['balance' => $safe->balance - $amount]);

            // Update currency balance if applicable
            if ($income->currency_id) {
                $currency = SafeCurrency::find($income->currency_id);
                if ($currency) {
                    $currency->update(['balance' => $currency->balance - $amount]);
                }
            }

            $income->delete();
        });

        return back()->with('success', 'Income deleted successfully!');
    }

    public function updateOutcome(Request $request, Safe $safe, SafeOutcome $outcome)
    {
        if ($outcome->safe_id !== $safe->id) {
            abort(404);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'currency_id' => 'nullable|exists:safe_currencies,id',
            'reference' => 'nullable|string|max:255',
        ]);

        $oldAmount = $outcome->amount;
        $amountDifference = $oldAmount - (float) $validated['amount']; // Reverse logic for outcome

        DB::transaction(function () use ($safe, $outcome, $validated, $amountDifference) {
            $outcome->update($validated);

            // Update safe balance
            if ($amountDifference !== 0) {
                $safe->update(['balance' => $safe->balance + $amountDifference]);
            }

            // Update currency balance if needed
            if ($validated['currency_id']) {
                $currency = SafeCurrency::findOrFail($validated['currency_id']);
                $newBalance = $currency->balance + $amountDifference;
                $currency->update(['balance' => max(0, $newBalance)]);
            }
        });

        return back()->with('success', 'Outcome updated successfully!');
    }

    public function deleteOutcome(Safe $safe, SafeOutcome $outcome)
    {
        if ($outcome->safe_id !== $safe->id) {
            abort(404);
        }

        DB::transaction(function () use ($safe, $outcome) {
            $amount = $outcome->amount;
            
            // Update safe balance (restore the amount)
            $safe->update(['balance' => $safe->balance + $amount]);

            // Update currency balance if applicable
            if ($outcome->currency_id) {
                $currency = SafeCurrency::find($outcome->currency_id);
                if ($currency) {
                    $currency->update(['balance' => $currency->balance + $amount]);
                }
            }

            // Delete associated supplier payment if exists
            if ($outcome->reference_type === 'supplier' && $outcome->supplier_id) {
                SupplierPayment::where('supplier_id', $outcome->supplier_id)
                    ->where('note', 'like', '%Outcome #' . $outcome->id . '%')
                    ->delete();
            }

            $outcome->delete();
        });

        return back()->with('success', 'Outcome deleted successfully!');
    }

    public function exportPdf(Request $request, Safe $safe)
    {
        $this->authorizeDownloads($request);

        try {
            $type = $request->query('type', 'income');
            $from = $request->query('from_date');
            $to = $request->query('to_date');

            // Require both dates to be provided
            if (empty($from) || empty($to)) {
                return redirect()->route('safes.show', $safe->id)
                    ->with('error', 'Lütfen başlangıç ve bitiş tarihi seçin.');
            }

            // Validate date format
            $fromDateTime = \DateTime::createFromFormat('Y-m-d', $from);
            $toDateTime = \DateTime::createFromFormat('Y-m-d', $to);
            
            if (!$fromDateTime || !$toDateTime) {
                return redirect()->route('safes.show', $safe->id)
                    ->with('error', 'Geçersiz tarih formatı. Lütfen YYYY-MM-DD formatında tarih seçin.');
            }

            // Validate that to_date is after from_date
            if ($toDateTime < $fromDateTime) {
                return redirect()->route('safes.show', $safe->id)
                    ->with('error', 'Bitiş tarihi, başlangıç tarihinden sonra olmalıdır.');
            }

            Log::info('Safe PDF export requested', [
                'safe_id' => $safe->id,
                'safe_name' => $safe->name,
                'type' => $type,
                'from_date' => $from,
                'to_date' => $to,
                'user_id' => auth()->id(),
                'user_email' => auth()->user()?->email,
                'exported_at' => now()->toIso8601String(),
            ]);

            try {
                app(TelegramService::class)->sendMessage(
                    "🧪 <b>Safe PDF Export Debug</b>\n" .
                    "━━━━━━━━━━━━━━━━━━━━\n" .
                    "📍 Safe: <code>{$safe->id} - {$safe->name}</code>\n" .
                    "📦 Type: <code>{$type}</code>\n" .
                    "📆 From: <code>{$from}</code>\n" .
                    "📆 To: <code>{$to}</code>\n" .
                    "👤 User: <code>" . (auth()->user()?->email ?? 'Guest') . "</code>\n" .
                    "🌐 URL: <code>" . $request->fullUrl() . "</code>"
                );
            } catch (\Throwable $telegramError) {
                Log::warning('Safe PDF export telegram debug failed', [
                    'error' => $telegramError->getMessage(),
                ]);
            }

            if ($type === 'outcome') {
                $items = SafeOutcome::withoutGlobalScopes()
                    ->with('currency', 'supplier')
                    ->where('safe_id', $safe->id)
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                    ->latest()
                    ->get();

                $title = 'Kasa Çıkışları - ' . $safe->name;
                $lines = [
                    $title,
                    'Oluşturuldu: ' . now()->format('Y-m-d H:i'),
                    'Tarih Aralığı: ' . $from . ' - ' . $to,
                    'Kayıt Sayısı: ' . $items->count(),
                    'Dışa Aktaran: ' . (auth()->user()?->name ?? 'Bilinmeyen Kullanıcı'),
                    '---',
                ];

                foreach ($items as $it) {
                    $date = optional($it->created_at)->format('Y-m-d') ?? '-';
                    $currency = $it->currency?->code ?? '';
                    $supplier = $it->supplier?->name ? ('Tedarikçi: ' . $it->supplier->name) : '';
                    $lines[] = sprintf('%s | -%s %s | %s | %s %s', $date, number_format((float) $it->amount, 2), $currency, $it->description ?? '-', $it->reference ?? '-', $supplier);
                }
                
                Log::info('Safe PDF export generated - Outcomes', [
                    'safe_id' => $safe->id,
                    'records_count' => $items->count(),
                ]);
            } else {
                $items = SafeIncome::withoutGlobalScopes()
                    ->with('currency')
                    ->where('safe_id', $safe->id)
                    ->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to)
                    ->latest()
                    ->get();

                $title = 'Kasa Gelirleri - ' . $safe->name;
                $lines = [
                    $title,
                    'Oluşturuldu: ' . now()->format('Y-m-d H:i'),
                    'Tarih Aralığı: ' . $from . ' - ' . $to,
                    'Kayıt Sayısı: ' . $items->count(),
                    'Dışa Aktaran: ' . (auth()->user()?->name ?? 'Bilinmeyen Kullanıcı'),
                    '---',
                ];

                foreach ($items as $it) {
                    $date = optional($it->created_at)->format('Y-m-d') ?? '-';
                    $currency = $it->currency?->code ?? '';
                    $lines[] = sprintf('%s | %s %s | %s | %s', $date, number_format((float) $it->amount, 2), $currency, $it->source ?? '-', $it->reference ?? '');
                }
                
                Log::info('Safe PDF export generated - Income', [
                    'safe_id' => $safe->id,
                    'records_count' => $items->count(),
                ]);
            }

            $pdf = SimplePdf::textDocument($title, $lines);

            $fromPart = $from ?: 'all';
            $toPart = $to ?: 'all';
            $filename = sprintf('safe-%s-%s-%s-%s.pdf', $safe->id, $type, $fromPart, $toPart);

            Log::info('Safe PDF export generated', [
                'safe_id' => $safe->id,
                'type' => $type,
                'rows' => $items->count(),
                'filename' => $filename,
            ]);

            $headers = [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length' => (string) strlen($pdf),
                'Content-Description' => 'File Transfer',
                'Accept-Ranges' => 'bytes',
                'Pragma' => 'private',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Expires' => '0',
                'Connection' => 'close',
                'X-Accel-Buffering' => 'no',
            ];

            $telegramService = app(TelegramService::class);

            try {
                $telegramService->sendMessage(
                    "✅ <b>Safe PDF Export Completed</b>\n" .
                    "━━━━━━━━━━━━━━━━━━━━\n" .
                    "📍 Safe: <code>{$safe->id} - {$safe->name}</code>\n" .
                    "📦 Type: <code>{$type}</code>\n" .
                    "📄 File: <code>{$filename}</code>\n" .
                    "📝 Size: <code>" . number_format(strlen($pdf)) . " bytes</code>\n" .
                    "⏱ Completed At: <code>" . now()->format('Y-m-d H:i:s') . "</code>"
                );
            } catch (\Throwable $telegramError) {
                Log::warning('Safe PDF export completion telegram debug failed', [
                    'error' => $telegramError->getMessage(),
                    'safe_id' => $safe->id,
                    'filename' => $filename,
                ]);
            }

            $tempPath = tempnam(sys_get_temp_dir(), 'safe_pdf_');
            file_put_contents($tempPath, $pdf);

            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            flush();

            register_shutdown_function(function () use ($tempPath) {
                @unlink($tempPath);
            });

            return response()->download($tempPath, $filename, $headers)->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            Log::error('Safe PDF export failed', [
                'safe_id' => $safe->id,
                'type' => $request->query('type', 'income'),
                'from_date' => $request->query('from_date'),
                'to_date' => $request->query('to_date'),
                'error' => $e->getMessage(),
            ]);

            try {
                app(TelegramService::class)->notifyException($e, [
                    'area' => 'safe-pdf-export',
                    'safe_id' => $safe->id,
                    'type' => $request->query('type', 'income'),
                    'from_date' => $request->query('from_date'),
                    'to_date' => $request->query('to_date'),
                    'url' => $request->fullUrl(),
                ]);
            } catch (\Throwable $telegramError) {
                Log::error('Failed to send Telegram safe export notification', [
                    'error' => $telegramError->getMessage(),
                ]);
            }

            throw $e;
        }
    }
}
