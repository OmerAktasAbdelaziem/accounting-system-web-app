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
        // If the authenticated user is a super admin, send them to the super-admin dashboard
        $user = auth()->user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }
        $merchantScope = $this->merchantScope();
        $branchIds = $merchantScope['branch_ids'];
        $safeIds = $merchantScope['safe_ids'];
        $storageIds = $merchantScope['storage_ids'];
        $isMerchantUser = $this->isMerchantUser();

        $totalProducts = Product::count();
        $totalEmployees = Employee::count();
        $lowStockCount = Product::where('current_stock', '<=', 0)->count();
        $lowStockProducts = Product::orderBy('current_stock')->limit(5)->get();

        $journalSalesQuery = JournalEntry::query()->where('reference_type', 'invoice');
        $this->applyTenantIds($journalSalesQuery, $branchIds, 'branch_id', $isMerchantUser);

        $totalSales = (float) $journalSalesQuery->sum('total_credit');
        $salesCount = (clone $journalSalesQuery)->count();

        // The `status` column was removed from `commissions` table; fall back to total counts/sums
        $pendingCommissions = Commission::count();
        $commissionAmount = (float) Commission::sum('commission_amount');

        $storageQuery = Storage::query();
        $this->applyTenantIds($storageQuery, $storageIds, 'id', $isMerchantUser);

        $storageItemQuery = StorageItem::query();
        $this->applyTenantIds($storageItemQuery, $storageIds, 'storage_id', $isMerchantUser);

        $totalStorageCapacity = (float) $storageQuery->sum('capacity');
        $totalStorageUsage = (float) $storageItemQuery->sum('quantity');
        $storageUsage = $totalStorageCapacity > 0 ? round(($totalStorageUsage / $totalStorageCapacity) * 100, 2) : 0;

        $safeQuery = Safe::query();
        $this->applyTenantIds($safeQuery, $safeIds, 'id', $isMerchantUser);

        $safeIncomeQuery = SafeIncome::query();
        $this->applyTenantIds($safeIncomeQuery, $safeIds, 'safe_id', $isMerchantUser);

        $safeOutcomeQuery = SafeOutcome::query();
        $this->applyTenantIds($safeOutcomeQuery, $safeIds, 'safe_id', $isMerchantUser);

        $safeBalance = (float) $safeQuery->sum('balance');
        $safeIncomeTotal = (float) $safeIncomeQuery->sum('amount');
        $safeOutcomeTotal = (float) $safeOutcomeQuery->sum('amount');
        $netCashFlow = $safeIncomeTotal + $totalSales - $safeOutcomeTotal;

        $safeCount = $safeQuery->count();
        $transactionsTodayQuery = SafeTransaction::query()->whereDate('created_at', today());
        $this->applyTenantIds($transactionsTodayQuery, $safeIds, 'safe_id', $isMerchantUser);
        $transactionsToday = $transactionsTodayQuery->count();

        $recentTransactionsQuery = JournalEntry::query();
        $this->applyTenantIds($recentTransactionsQuery, $branchIds, 'branch_id', $isMerchantUser);
        $recentTransactions = $recentTransactionsQuery->latest()->take(6)->get();

        $recentSalesQuery = EmployeeSale::with(['employee:id,name', 'product:id,name']);
        $this->applyTenantIds($recentSalesQuery, $branchIds, 'branch_id', $isMerchantUser);
        $recentSales = $recentSalesQuery->latest('sale_date')->take(6)->get();

        $recentIncomeQuery = SafeIncome::query()->with(['safe:id,name', 'currency:id,code,name']);
        $this->applyTenantIds($recentIncomeQuery, $safeIds, 'safe_id', $isMerchantUser);
        $recentIncomeEntries = $recentIncomeQuery->latest()->take(6)->get();

        $recentOutcomeQuery = SafeOutcome::query()->with(['safe:id,name', 'currency:id,code,name', 'supplier:id,name']);
        $this->applyTenantIds($recentOutcomeQuery, $safeIds, 'safe_id', $isMerchantUser);
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
            $this->applyTenantIds($monthSalesQuery, $branchIds, 'branch_id', $isMerchantUser);
            $salesData[] = (float) $monthSalesQuery->sum('total_credit');

            $monthIncomeQuery = SafeIncome::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ;
            $this->applyTenantIds($monthIncomeQuery, $safeIds, 'safe_id', $isMerchantUser);
            $incomeData[] = (float) $monthIncomeQuery->sum('amount');

            $monthOutcomeQuery = SafeOutcome::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ;
            $this->applyTenantIds($monthOutcomeQuery, $safeIds, 'safe_id', $isMerchantUser);
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

    private function isMerchantUser(): bool
    {
        $user = auth()->user();

        return (bool) ($user && !$user->isSuperAdmin() && !empty($user->merchant_id));
    }

    private function applyTenantIds($query, array $ids, string $column, bool $isMerchantUser): void
    {
        if (!empty($ids)) {
            $query->whereIn($column, $ids);
            return;
        }

        if ($isMerchantUser) {
            $query->whereRaw('1 = 0');
        }
    }

    public function analytics(): JsonResponse
    {
        $merchantScope = $this->merchantScope();
        $user = auth()->user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return response()->json(['success' => false, 'message' => 'Use super-admin analytics route'], 403);
        }
        $branchIds = $merchantScope['branch_ids'];
        $safeIds = $merchantScope['safe_ids'];
        $isMerchantUser = $this->isMerchantUser();

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
            $this->applyTenantIds($salesQuery, $branchIds, 'branch_id', $isMerchantUser);
            $salesData[] = (float) $salesQuery->sum('total_credit');

            $incomeQuery = SafeIncome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ;
            $this->applyTenantIds($incomeQuery, $safeIds, 'safe_id', $isMerchantUser);
            $incomeData[] = (float) $incomeQuery->sum('amount');

            $outcomeQuery = SafeOutcome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ;
            $this->applyTenantIds($outcomeQuery, $safeIds, 'safe_id', $isMerchantUser);
            $outcomeData[] = (float) $outcomeQuery->sum('amount');
        }

        $journalSalesQuery = JournalEntry::query()->where('reference_type', 'invoice');
        $this->applyTenantIds($journalSalesQuery, $branchIds, 'branch_id', $isMerchantUser);

        $storageQuery = Storage::query();
        $storageItemQuery = StorageItem::query();
        $this->applyTenantIds($storageQuery, $merchantScope['storage_ids'], 'id', $isMerchantUser);
        $this->applyTenantIds($storageItemQuery, $merchantScope['storage_ids'], 'storage_id', $isMerchantUser);

        $safeQuery = Safe::query();
        $safeIncomeQuery = SafeIncome::query();
        $safeOutcomeQuery = SafeOutcome::query();
        $transactionsTodayQuery = SafeTransaction::query()->whereDate('created_at', today());
        $this->applyTenantIds($safeQuery, $safeIds, 'id', $isMerchantUser);
        $this->applyTenantIds($safeIncomeQuery, $safeIds, 'safe_id', $isMerchantUser);
        $this->applyTenantIds($safeOutcomeQuery, $safeIds, 'safe_id', $isMerchantUser);
        $this->applyTenantIds($transactionsTodayQuery, $safeIds, 'safe_id', $isMerchantUser);

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
