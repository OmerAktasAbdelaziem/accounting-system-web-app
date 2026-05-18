<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\EmployeeSale;
use App\Models\JournalEntry;
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

        $totalProducts = Product::count();
        $totalEmployees = Employee::count();
        $lowStockCount = Product::where('current_stock', '<=', 0)->count();
        $lowStockProducts = Product::orderBy('current_stock')->limit(5)->get();

        $totalSales = (float) JournalEntry::where('reference_type', 'invoice')->sum('total_credit');
        $salesCount = JournalEntry::where('reference_type', 'invoice')->count();

        // The `status` column was removed from `commissions` table; fall back to total counts/sums
        $pendingCommissions = Commission::count();
        $commissionAmount = (float) Commission::sum('commission_amount');

        $totalStorageCapacity = (float) Storage::sum('capacity');
        $totalStorageUsage = (float) StorageItem::sum('quantity');
        $storageUsage = $totalStorageCapacity > 0 ? round(($totalStorageUsage / $totalStorageCapacity) * 100, 2) : 0;

        $safeBalance = (float) Safe::sum('balance');
        $safeIncomeTotal = (float) SafeIncome::sum('amount');
        $safeOutcomeTotal = (float) SafeOutcome::sum('amount');
        $netCashFlow = $safeIncomeTotal + $totalSales - $safeOutcomeTotal;

        $safeCount = Safe::count();
        $transactionsToday = SafeTransaction::whereDate('created_at', today())->count();

        $recentTransactions = JournalEntry::latest()->take(6)->get();
        $recentSales = EmployeeSale::with(['employee:id,name', 'product:id,name'])->latest('sale_date')->take(6)->get();
        $recentIncomeEntries = SafeIncome::with(['safe:id,name', 'currency:id,code,name'])->latest()->take(6)->get();
        $recentOutcomeEntries = SafeOutcome::with(['safe:id,name', 'currency:id,code,name', 'supplier:id,name'])->latest()->take(6)->get();
        $topProducts = Product::orderByDesc('current_stock')->limit(6)->get();

        $storageSnapshot = Storage::withCount('items')->orderByDesc('current_usage')->take(5)->get();

        $salesData = [];
        $incomeData = [];
        $outcomeData = [];
        $months = [];

        for ($offset = 5; $offset >= 0; $offset--) {
            $month = Carbon::now()->startOfMonth()->subMonths($offset);
            $months[] = $month->format('M');

            $salesData[] = (float) JournalEntry::where('reference_type', 'invoice')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('total_credit');

            $incomeData[] = (float) SafeIncome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');

            $outcomeData[] = (float) SafeOutcome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
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

    public function analytics(): JsonResponse
    {
        $months = [];
        $salesData = [];
        $incomeData = [];
        $outcomeData = [];

        for ($offset = 5; $offset >= 0; $offset--) {
            $month = Carbon::now()->startOfMonth()->subMonths($offset);

            $months[] = $month->format('M Y');
            $salesData[] = (float) JournalEntry::where('reference_type', 'invoice')
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('total_credit');
            $incomeData[] = (float) SafeIncome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $outcomeData[] = (float) SafeOutcome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'total_products' => Product::count(),
                'total_employees' => Employee::count(),
                'low_stock_count' => Product::where('current_stock', '<=', 0)->count(),
                'total_sales' => (float) JournalEntry::where('reference_type', 'invoice')->sum('total_credit'),
                'sales_count' => JournalEntry::where('reference_type', 'invoice')->count(),
                // `status` was removed; show total commissions instead
                'pending_commissions' => Commission::count(),
                'storage_usage' => (float) (Storage::sum('capacity') > 0 ? round((StorageItem::sum('quantity') / Storage::sum('capacity')) * 100, 2) : 0),
                'safe_balance' => (float) Safe::sum('balance'),
                'safe_income_total' => (float) SafeIncome::sum('amount'),
                'safe_outcome_total' => (float) SafeOutcome::sum('amount'),
                'transactions_today' => SafeTransaction::whereDate('created_at', today())->count(),
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
