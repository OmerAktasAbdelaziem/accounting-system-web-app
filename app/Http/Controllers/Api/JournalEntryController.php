<?php

namespace App\Http\Controllers\Api;

use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class JournalEntryController extends Controller
{
    /**
     * Get all journal entries
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 20);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = JournalEntry::with('createdBy', 'items.account');

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $entries = $query->orderByDesc('date')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $entries->items(),
            'pagination' => [
                'total' => $entries->total(),
                'per_page' => $entries->perPage(),
                'current_page' => $entries->currentPage(),
                'last_page' => $entries->lastPage(),
            ],
        ]);
    }

    /**
     * Get a single journal entry
     */
    public function show(JournalEntry $entry): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $entry->load('createdBy', 'items.account'),
        ]);
    }

    /**
     * Create a new journal entry
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'reference_type' => 'nullable|string',
            'reference_id' => 'nullable|integer',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'nullable|numeric|min:0',
            'items.*.credit' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string',
        ]);

        $entry = JournalEntry::create([
            'date' => $validated['date'],
            'description' => $validated['description'],
            'description_ar' => $validated['description_ar'] ?? null,
            'reference_type' => $validated['reference_type'] ?? null,
            'reference_id' => $validated['reference_id'] ?? null,
            'branch_id' => $request->input('branch_id'),
            'created_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        // Add items
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($validated['items'] as $item) {
            $debit = $item['debit'] ?? 0;
            $credit = $item['credit'] ?? 0;

            $entry->addItem(
                $item['account_id'],
                $debit,
                $credit,
                $item['description'] ?? null
            );

            $totalDebit += $debit;
            $totalCredit += $credit;
        }

        // Update totals
        $entry->update([
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Journal entry created successfully',
            'data' => $entry->load('items.account'),
        ], 201);
    }

    /**
     * Post a journal entry
     */
    public function post(JournalEntry $entry): JsonResponse
    {
        if (!$entry->post()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot post entry: Debits do not equal credits',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Journal entry posted successfully',
            'data' => $entry,
        ]);
    }

    /**
     * Reverse a journal entry
     */
    public function reverse(JournalEntry $entry, Request $request): JsonResponse
    {
        $reversalDate = $request->get('reversal_date');

        $reversalEntry = $entry->reverse($reversalDate);

        return response()->json([
            'success' => true,
            'message' => 'Journal entry reversed successfully',
            'data' => $reversalEntry->load('items.account'),
        ]);
    }

    /**
     * Get trial balance
     */
    public function trialBalance(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $accounts = ChartOfAccount::active()->get();

        $data = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balance = $account->getBalance($startDate, $endDate);

            // Determine if balance is debit or credit
            if (in_array($account->account_type, ['asset', 'expense'])) {
                $debit = max(0, $balance);
                $credit = max(0, -$balance);
            } else {
                $credit = max(0, $balance);
                $debit = max(0, -$balance);
            }

            if ($debit != 0 || $credit != 0) {
                $data[] = [
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'debit' => $debit,
                    'credit' => $credit,
                ];

                $totalDebit += $debit;
                $totalCredit += $credit;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'totals' => [
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'balanced' => abs($totalDebit - $totalCredit) < 0.01,
            ],
        ]);
    }

    /**
     * Get general ledger for an account
     */
    public function generalLedger(ChartOfAccount $account, Request $request): JsonResponse
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = $account->journalEntryItems()
            ->with('journalEntry');

        if ($startDate && $endDate) {
            $query->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            });
        }

        $items = $query->orderBy('created_at')->get();

        $balance = $account->opening_balance;
        $ledger = [];

        foreach ($items as $item) {
            if (in_array($account->account_type, ['asset', 'expense'])) {
                $balance += $item->debit - $item->credit;
            } else {
                $balance += $item->credit - $item->debit;
            }

            $ledger[] = [
                'date' => $item->journalEntry->date,
                'reference' => $item->journalEntry->reference_number,
                'description' => $item->journalEntry->description,
                'debit' => $item->debit,
                'credit' => $item->credit,
                'balance' => $balance,
            ];
        }

        return response()->json([
            'success' => true,
            'account' => $account,
            'opening_balance' => $account->opening_balance,
            'ledger' => $ledger,
            'closing_balance' => $balance,
        ]);
    }
}
