<?php

namespace App\Http\Controllers\Api;

use App\Models\ChartOfAccount;
use App\Models\EmployeeCommission;
use App\Models\EmployeeSale;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\WarehouseTransfer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportingController extends Controller
{
    /**
     * Get financial summary report
     */
    public function financialSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        // Get revenue
        $revenue = ChartOfAccount::where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) use ($startDate, $endDate) {
                return $account->getBalance($startDate, $endDate);
            });

        // Get expenses
        $expenses = ChartOfAccount::where('account_type', 'expense')
            ->get()
            ->sum(function ($account) use ($startDate, $endDate) {
                return $account->getBalance($startDate, $endDate);
            });

        // Get assets
        $assets = ChartOfAccount::where('account_type', 'asset')
            ->get()
            ->sum(function ($account) use ($startDate, $endDate) {
                return $account->getBalance($startDate, $endDate);
            });

        // Get liabilities
        $liabilities = ChartOfAccount::where('account_type', 'liability')
            ->get()
            ->sum(function ($account) use ($startDate, $endDate) {
                return $account->getBalance($startDate, $endDate);
            });

        $profit = $revenue - $expenses;

        return response()->json([
            'success' => true,
            'period' => "{$startDate} to {$endDate}",
            'data' => [
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'assets' => $assets,
                'liabilities' => $liabilities,
                'equity' => $assets - $liabilities,
            ],
        ]);
    }

    /**
     * Get revenue breakdown by account
     */
    public function revenueByAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $revenueAccounts = ChartOfAccount::where('account_type', 'revenue')->get();

        $breakdown = [];
        $totalRevenue = 0;

        foreach ($revenueAccounts as $account) {
            $balance = $account->getBalance($startDate, $endDate);
            $breakdown[] = [
                'account_id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_name_ar' => $account->account_name_ar,
                'amount' => $balance,
                'percentage' => 0, // Will be calculated after total
            ];
            $totalRevenue += $balance;
        }

        // Calculate percentages
        foreach ($breakdown as &$item) {
            $item['percentage'] = $totalRevenue > 0 ? ($item['amount'] / $totalRevenue) * 100 : 0;
        }

        return response()->json([
            'success' => true,
            'period' => "{$startDate} to {$endDate}",
            'total_revenue' => $totalRevenue,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Get expense breakdown by account
     */
    public function expenseByAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];

        $expenseAccounts = ChartOfAccount::where('account_type', 'expense')->get();

        $breakdown = [];
        $totalExpense = 0;

        foreach ($expenseAccounts as $account) {
            $balance = $account->getBalance($startDate, $endDate);
            $breakdown[] = [
                'account_id' => $account->id,
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_name_ar' => $account->account_name_ar,
                'amount' => $balance,
                'percentage' => 0, // Will be calculated after total
            ];
            $totalExpense += $balance;
        }

        // Calculate percentages
        foreach ($breakdown as &$item) {
            $item['percentage'] = $totalExpense > 0 ? ($item['amount'] / $totalExpense) * 100 : 0;
        }

        return response()->json([
            'success' => true,
            'period' => "{$startDate} to {$endDate}",
            'total_expenses' => $totalExpense,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Get sales performance report
     */
    public function salesPerformance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|integer|exists:employees,id',
        ]);

        $query = EmployeeSale::whereBetween('sale_date', [$validated['start_date'], $validated['end_date']]);

        if ($request->has('employee_id')) {
            $query->where('employee_id', $validated['employee_id']);
        }

        $sales = $query->get();

        $totalSales = $sales->sum('total_amount');
        $totalCount = $sales->count();

        // Group by employee
        $byEmployee = $sales->groupBy('employee_id')
            ->map(function ($group) {
                return [
                    'employee_id' => $group->first()->employee->id,
                    'employee_name' => $group->first()->employee->name,
                    'employee_name_ar' => $group->first()->employee->name_ar,
                    'total_sales' => $group->sum('total_amount'),
                    'count' => $group->count(),
                    'average' => $group->avg('total_amount'),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'period' => "{$validated['start_date']} to {$validated['end_date']}",
            'summary' => [
                'total_sales' => $totalSales,
                'total_transactions' => $totalCount,
                'average_transaction' => $totalCount > 0 ? $totalSales / $totalCount : 0,
            ],
            'by_employee' => $byEmployee,
        ]);
    }

    /**
     * Get top selling products
     */
    public function topSellingProducts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = $validated['limit'] ?? 10;

        $products = EmployeeSale::whereBetween('sale_date', [$validated['start_date'], $validated['end_date']])
            ->selectRaw('product_id, sum(quantity) as total_quantity, sum(total_amount) as total_sales, count(*) as count')
            ->groupBy('product_id')
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->with('product')
            ->get()
            ->map(function ($sale) {
                return [
                    'product_id' => $sale->product_id,
                    'product_name' => $sale->product->name,
                    'product_name_ar' => $sale->product->name_ar ?? 'N/A',
                    'quantity_sold' => $sale->total_quantity,
                    'total_sales' => $sale->total_sales,
                    'transaction_count' => $sale->count,
                ];
            });

        return response()->json([
            'success' => true,
            'period' => "{$validated['start_date']} to {$validated['end_date']}",
            'top_products' => $products,
        ]);
    }

    /**
     * Get commission report
     */
    public function commissionReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'department' => 'nullable|in:sales,inventory,accounting,management,other',
        ]);

        $query = EmployeeCommission::where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->with('employee');

        if ($request->has('department')) {
            $query->whereHas('employee', function ($q) {
                $q->where('department', request('department'));
            });
        }

        $commissions = $query->get();

        $summary = [
            'pending' => $commissions->where('status', 'pending')->sum('commission_earned'),
            'approved' => $commissions->where('status', 'approved')->sum('commission_earned'),
            'paid' => $commissions->where('status', 'paid')->sum('commission_earned'),
            'total' => $commissions->sum('commission_earned'),
        ];

        $details = $commissions->map(function ($commission) {
            return [
                'employee_id' => $commission->employee_id,
                'employee_name' => $commission->employee->name,
                'employee_name_ar' => $commission->employee->name_ar,
                'department' => $commission->employee->department,
                'sales_amount' => $commission->sales_amount,
                'commission_earned' => $commission->commission_earned,
                'bonus' => $commission->bonus,
                'status' => $commission->status,
            ];
        });

        return response()->json([
            'success' => true,
            'period' => "{$validated['month']}/{$validated['year']}",
            'summary' => $summary,
            'details' => $details,
        ]);
    }

    /**
     * Get inventory movement report
     */
    public function inventoryMovement(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'warehouse_id' => 'nullable|integer|exists:warehouses,id',
        ]);

        $query = WarehouseTransfer::whereBetween('transfer_date', [$validated['start_date'], $validated['end_date']]);

        if ($request->has('warehouse_id')) {
            $query->where(function ($q) {
                $q->where('from_warehouse_id', request('warehouse_id'))
                    ->orWhere('to_warehouse_id', request('warehouse_id'));
            });
        }

        $transfers = $query->with(['product', 'fromWarehouse', 'toWarehouse'])->get();

        $summary = [
            'total_transfers' => $transfers->count(),
            'total_quantity' => $transfers->sum('quantity'),
            'pending' => $transfers->where('status', 'pending')->count(),
            'in_transit' => $transfers->where('status', 'in_transit')->count(),
            'received' => $transfers->where('status', 'received')->count(),
            'rejected' => $transfers->where('status', 'rejected')->count(),
        ];

        $details = $transfers->map(function ($transfer) {
            return [
                'transfer_id' => $transfer->id,
                'product_id' => $transfer->product_id,
                'product_name' => $transfer->product->name,
                'quantity' => $transfer->quantity,
                'from_warehouse' => $transfer->fromWarehouse->name,
                'to_warehouse' => $transfer->toWarehouse->name,
                'status' => $transfer->status,
                'transfer_date' => $transfer->transfer_date,
            ];
        });

        return response()->json([
            'success' => true,
            'period' => "{$validated['start_date']} to {$validated['end_date']}",
            'summary' => $summary,
            'details' => $details,
        ]);
    }

    /**
     * Get account details with drill-down
     */
    public function accountDrillDown(Request $request, $accountId): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $account = ChartOfAccount::findOrFail($accountId);

        // Get account details
        $journalItems = $account->journalEntryItems()
            ->with('journalEntry')
            ->whereBetween('created_at', [$validated['start_date'], $validated['end_date']])
            ->get();

        $details = $journalItems->map(function ($item) {
            return [
                'journal_entry_id' => $item->journal_entry_id,
                'date' => $item->journalEntry->date,
                'description' => $item->journalEntry->description,
                'description_ar' => $item->journalEntry->description_ar,
                'debit' => $item->debit,
                'credit' => $item->credit,
                'balance_change' => $item->debit - $item->credit,
            ];
        });

        $summary = [
            'account_id' => $account->id,
            'account_code' => $account->account_code,
            'account_name' => $account->account_name,
            'account_name_ar' => $account->account_name_ar,
            'account_type' => $account->account_type,
            'total_debit' => $journalItems->sum('debit'),
            'total_credit' => $journalItems->sum('credit'),
            'balance' => $account->getBalance($validated['start_date'], $validated['end_date']),
            'transaction_count' => $journalItems->count(),
        ];

        return response()->json([
            'success' => true,
            'period' => "{$validated['start_date']} to {$validated['end_date']}",
            'summary' => $summary,
            'transactions' => $details,
        ]);
    }

    /**
     * Get comparison report (previous period vs current)
     */
    public function comparisonReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_start' => 'required|date',
            'current_end' => 'required|date|after_or_equal:current_start',
            'previous_start' => 'required|date',
            'previous_end' => 'required|date|after_or_equal:previous_start',
        ]);

        // Current period
        $currentRevenue = ChartOfAccount::where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) {
                return $account->getBalance($validated['current_start'], $validated['current_end']);
            });

        $currentExpenses = ChartOfAccount::where('account_type', 'expense')
            ->get()
            ->sum(function ($account) {
                return $account->getBalance($validated['current_start'], $validated['current_end']);
            });

        // Previous period
        $previousRevenue = ChartOfAccount::where('account_type', 'revenue')
            ->get()
            ->sum(function ($account) {
                return $account->getBalance($validated['previous_start'], $validated['previous_end']);
            });

        $previousExpenses = ChartOfAccount::where('account_type', 'expense')
            ->get()
            ->sum(function ($account) {
                return $account->getBalance($validated['previous_start'], $validated['previous_end']);
            });

        $currentProfit = $currentRevenue - $currentExpenses;
        $previousProfit = $previousRevenue - $previousExpenses;

        $revenueChange = $previousRevenue > 0 ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $expenseChange = $previousExpenses > 0 ? (($currentExpenses - $previousExpenses) / $previousExpenses) * 100 : 0;
        $profitChange = $previousProfit > 0 ? (($currentProfit - $previousProfit) / $previousProfit) * 100 : 0;

        return response()->json([
            'success' => true,
            'comparison' => [
                'period' => [
                    'current' => "{$validated['current_start']} to {$validated['current_end']}",
                    'previous' => "{$validated['previous_start']} to {$validated['previous_end']}",
                ],
                'revenue' => [
                    'current' => $currentRevenue,
                    'previous' => $previousRevenue,
                    'change' => $currentRevenue - $previousRevenue,
                    'change_percentage' => $revenueChange,
                ],
                'expenses' => [
                    'current' => $currentExpenses,
                    'previous' => $previousExpenses,
                    'change' => $currentExpenses - $previousExpenses,
                    'change_percentage' => $expenseChange,
                ],
                'profit' => [
                    'current' => $currentProfit,
                    'previous' => $previousProfit,
                    'change' => $currentProfit - $previousProfit,
                    'change_percentage' => $profitChange,
                ],
            ],
        ]);
    }
}
