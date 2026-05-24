<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\EmployeeSale;
use App\Models\JournalEntry;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Safe;
use App\Models\SafeIncome;
use App\Models\SafeOutcome;
use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\SafeTransaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $currencySymbol = $this->currencySymbol();
        $merchantScope = $this->merchantScope();
        $branchIds = $merchantScope['branch_ids'];
        $safeIds = $merchantScope['safe_ids'];
        $storageIds = $merchantScope['storage_ids'];

        $totalProducts = Product::count();
        $totalEmployees = Employee::count();
        $lowStockCount = Product::where('current_stock', '<=', 0)->count();
        $lowStockProducts = Product::orderBy('current_stock')->limit(5)->get();

        $journalSalesQuery = JournalEntry::query()->where('reference_type', 'invoice');
        if (!empty($branchIds)) {
            $journalSalesQuery->whereIn('branch_id', $branchIds);
        }

        $totalSales = (float) $journalSalesQuery->sum('total_credit');
        $salesCount = (clone $journalSalesQuery)->count();

        // The `status` column was removed from `commissions` table; fall back to total counts/sums
        $pendingCommissions = Commission::count();
        $commissionAmount = (float) Commission::sum('commission_amount');

        $storageQuery = Storage::query();
        if (!empty($storageIds)) {
            $storageQuery->whereIn('id', $storageIds);
        }

        $storageItemQuery = StorageItem::query();
        if (!empty($storageIds)) {
            $storageItemQuery->whereIn('storage_id', $storageIds);
        }

        $totalStorageCapacity = (float) $storageQuery->sum('capacity');
        $totalStorageUsage = (float) $storageItemQuery->sum('quantity');
        $storageUsage = $totalStorageCapacity > 0 ? round(($totalStorageUsage / $totalStorageCapacity) * 100, 2) : 0;

        $safeQuery = Safe::query();
        if (!empty($safeIds)) {
            $safeQuery->whereIn('id', $safeIds);
        }

        $safeIncomeQuery = SafeIncome::query();
        if (!empty($safeIds)) {
            $safeIncomeQuery->whereIn('safe_id', $safeIds);
        }

        $safeOutcomeQuery = SafeOutcome::query();
        if (!empty($safeIds)) {
            $safeOutcomeQuery->whereIn('safe_id', $safeIds);
        }

        $safeBalance = (float) $safeQuery->sum('balance');
        $safeIncomeTotal = (float) $safeIncomeQuery->sum('amount');
        $safeOutcomeTotal = (float) $safeOutcomeQuery->sum('amount');
        $netCashFlow = $safeIncomeTotal + $totalSales - $safeOutcomeTotal;

        $safeCount = $safeQuery->count();
        $transactionsTodayQuery = SafeTransaction::query()->whereDate('created_at', today());
        if (!empty($safeIds)) {
            $transactionsTodayQuery->whereIn('safe_id', $safeIds);
        }
        $transactionsToday = $transactionsTodayQuery->count();

        $recentTransactionsQuery = JournalEntry::query();
        if (!empty($branchIds)) {
            $recentTransactionsQuery->whereIn('branch_id', $branchIds);
        }
        $recentTransactions = $recentTransactionsQuery->latest()->take(6)->get();

        $recentSalesQuery = EmployeeSale::with(['employee:id,name', 'product:id,name']);
        if (!empty($branchIds)) {
            $recentSalesQuery->whereIn('branch_id', $branchIds);
        }
        $recentSales = $recentSalesQuery->latest('sale_date')->take(6)->get();

        $recentIncomeQuery = SafeIncome::query()->with(['safe:id,name', 'currency:id,code,name']);
        if (!empty($safeIds)) {
            $recentIncomeQuery->whereIn('safe_id', $safeIds);
        }
        $recentIncomeEntries = $recentIncomeQuery->latest()->take(6)->get();

        $recentOutcomeQuery = SafeOutcome::query()->with(['safe:id,name', 'currency:id,code,name', 'supplier:id,name']);
        if (!empty($safeIds)) {
            $recentOutcomeQuery->whereIn('safe_id', $safeIds);
        }
        $recentOutcomeEntries = $recentOutcomeQuery->latest()->take(6)->get();

        $topProducts = Product::orderByDesc('current_stock')->limit(6)->get();

        $storageSnapshot = $storageQuery->withCount('items')->orderByDesc('current_usage')->take(5)->get();

        $salesData = [];
        $incomeData = [];
        $outcomeData = [];
        $months = [];

        for ($offset = 5; $offset >= 0; $offset--) {
            $month = Carbon::now()->startOfMonth()->subMonths($offset);
            $months[] = $month->format('M');

            $monthSalesQuery = JournalEntry::query()->where('reference_type', 'invoice')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month);
            if (!empty($branchIds)) {
                $monthSalesQuery->whereIn('branch_id', $branchIds);
            }
            $salesData[] = (float) $monthSalesQuery->sum('total_credit');

            $monthIncomeQuery = SafeIncome::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ;
            if (!empty($safeIds)) {
                $monthIncomeQuery->whereIn('safe_id', $safeIds);
            }
            $incomeData[] = (float) $monthIncomeQuery->sum('amount');

            $monthOutcomeQuery = SafeOutcome::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ;
            if (!empty($safeIds)) {
                $monthOutcomeQuery->whereIn('safe_id', $safeIds);
            }
            $outcomeData[] = (float) $monthOutcomeQuery->sum('amount');
        }

        $inventoryData = [
            max(0, round(100 - $storageUsage, 2)),
            min(100, round($storageUsage, 2)),
        ];

        return view('dashboard.index', compact(
            'totalProducts',
            'totalEmployees',
            'lowStockCount',
            'totalSales',
            'salesCount',
            'pendingCommissions',
            'commissionAmount',
            'storageUsage',
            'totalStorageCapacity',
            'totalStorageUsage',
            'safeBalance',
            'safeIncomeTotal',
            'safeOutcomeTotal',
            'safeCount',
            'netCashFlow',
            'transactionsToday',
            'recentTransactions',
            'recentSales',
            'recentIncomeEntries',
            'recentOutcomeEntries',
            'lowStockProducts',
            'storageSnapshot',
            'topProducts',
            'months',
            'salesData',
            'incomeData',
            'outcomeData',
            'inventoryData',
            'currencySymbol'
        ));
    }
    private function merchantScope(): array
    {
        $user = auth()->user();

        if (!$user || $user->isSuperAdmin() || empty($user->merchant_id)) {
            return [
                'branch_ids' => [],
                'safe_ids' => [],
                'storage_ids' => [],
            ];
        }

        $branchIds = Branch::query()->pluck('id')->all();
        $safeIds = Safe::query()->pluck('id')->all();
        $storageIds = Storage::query()->pluck('id')->all();

        return [
            'branch_ids' => $branchIds,
            'safe_ids' => $safeIds,
            'storage_ids' => $storageIds,
        ];
    }

    public function analytics(): JsonResponse
    {
        $merchantScope = $this->merchantScope();
        $branchIds = $merchantScope['branch_ids'];
        $safeIds = $merchantScope['safe_ids'];

        $months = [];
        $salesData = [];
        $incomeData = [];
        $outcomeData = [];

        for ($offset = 5; $offset >= 0; $offset--) {
            $month = Carbon::now()->startOfMonth()->subMonths($offset);

            $months[] = $month->format('M Y');
            $salesQuery = JournalEntry::where('reference_type', 'invoice')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month);
            if (!empty($branchIds)) {
                $salesQuery->whereIn('branch_id', $branchIds);
            }
            $salesData[] = (float) $salesQuery->sum('total_credit');

            $incomeQuery = SafeIncome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ;
            if (!empty($safeIds)) {
                $incomeQuery->whereIn('safe_id', $safeIds);
            }
            $incomeData[] = (float) $incomeQuery->sum('amount');

            $outcomeQuery = SafeOutcome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ;
            if (!empty($safeIds)) {
                $outcomeQuery->whereIn('safe_id', $safeIds);
            }
            $outcomeData[] = (float) $outcomeQuery->sum('amount');
        }

        $journalSalesQuery = JournalEntry::query()->where('reference_type', 'invoice');
        if (!empty($branchIds)) {
            $journalSalesQuery->whereIn('branch_id', $branchIds);
        }

        $storageQuery = Storage::query();
        $storageItemQuery = StorageItem::query();
        if (!empty($merchantScope['storage_ids'])) {
            $storageQuery->whereIn('id', $merchantScope['storage_ids']);
            $storageItemQuery->whereIn('storage_id', $merchantScope['storage_ids']);
        }

        $safeQuery = Safe::query();
        $safeIncomeQuery = SafeIncome::query();
        $safeOutcomeQuery = SafeOutcome::query();
        $transactionsTodayQuery = SafeTransaction::query()->whereDate('created_at', today());

        if (!empty($safeIds)) {
            $safeQuery->whereIn('id', $safeIds);
            $safeIncomeQuery->whereIn('safe_id', $safeIds);
            $safeOutcomeQuery->whereIn('safe_id', $safeIds);
            $transactionsTodayQuery->whereIn('safe_id', $safeIds);
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'total_products' => Product::count(),
                'total_employees' => Employee::count(),
                'low_stock_count' => Product::where('current_stock', '<=', 0)->count(),
                'total_sales' => (float) $journalSalesQuery->sum('total_credit'),
                'sales_count' => (clone $journalSalesQuery)->count(),
                // `status` was removed; show total commissions instead
                'pending_commissions' => Commission::count(),
                'storage_usage' => (float) ($storageQuery->sum('capacity') > 0 ? round(($storageItemQuery->sum('quantity') / $storageQuery->sum('capacity')) * 100, 2) : 0),
                'safe_balance' => (float) $safeQuery->sum('balance'),
                'safe_income_total' => (float) $safeIncomeQuery->sum('amount'),
                'safe_outcome_total' => (float) $safeOutcomeQuery->sum('amount'),
                'transactions_today' => (clone $transactionsTodayQuery)->count(),
            ],
            'charts' => [
                'months' => $months,
                'sales' => $salesData,
                'income' => $incomeData,
                'outcome' => $outcomeData,
                'inventory' => [
                    Product::where('current_stock', '>', 0)->count(),
                    Product::where('current_stock', '<=', 0)->count(),
                ],
            ],
        ]);
    }

    private function currencySymbol(): string
    {
        $currency = \App\Models\Currency::byCode((string) \App\Models\Setting::get('currency', 'AED'));

        return $currency?->symbol ?? '$';
    }
}
