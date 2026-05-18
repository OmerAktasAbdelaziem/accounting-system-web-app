<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EmployeeSale;
use App\Models\Merchant;
use App\Models\Package;
use App\Models\Safe;
use App\Models\SafeIncome;
use App\Models\SafeOutcome;
use App\Models\Subscription;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    /**
     * Show the super admin dashboard
     */
    public function index()
    {
        $currencySymbol = '$';
        $totalSales = (float) EmployeeSale::sum('total_amount');
        $salesCount = EmployeeSale::count();
        $totalIncome = (float) SafeIncome::sum('amount');
        $totalOutcome = (float) SafeOutcome::sum('amount');

        $totalMerchants = Merchant::count();
        $activeMerchants = Merchant::where('is_active', true)->count();

        $activeSubscriptions = Subscription::where('is_active', true)->count();
        $expiringSoon = Subscription::where('expires_at', '<=', now()->addDays(7))
            ->where('expires_at', '>=', now())
            ->where('is_active', true)
            ->count();

        // Calculate total revenue from active subscriptions
        $totalRevenue = Subscription::where('subscriptions.is_active', true)
            ->join('packages', 'subscriptions.package_id', '=', 'packages.id')
            ->sum('packages.price');

        $totalPackages = Package::count();
        $activePackages = Package::where('is_active', true)->count();

        $totalUsers = User::count();
        $merchantAdmins = User::where('user_type', 'merchant_admin')->count();
        $employees = User::where('user_type', 'employee')->count();

        $activeSafes = Safe::where('is_active', true)->count();
        $totalSafes = Safe::count();
        $netCashFlow = $totalIncome + $totalSales - $totalOutcome;

        // Recent subscriptions
        $recentSubscriptions = Subscription::with(['merchant', 'package'])
            ->latest()
            ->limit(10)
            ->get();

        // Expiring subscriptions
        $expiringSubscriptions = Subscription::where('expires_at', '<=', now()->addDays(30))
            ->where('expires_at', '>=', now())
            ->where('is_active', true)
            ->with(['merchant', 'package'])
            ->latest('expires_at')
            ->limit(10)
            ->get();

        // Merchants by package
        $merchantsByPackage = Package::withCount('subscriptions')->get();

        $recentSales = EmployeeSale::with(['employee:id,name', 'product:id,name'])
            ->latest('sale_date')
            ->limit(8)
            ->get();

        $recentIncomeEntries = SafeIncome::with(['safe:id,name', 'currency:id,code,name'])
            ->latest()
            ->limit(8)
            ->get();

        $recentOutcomeEntries = SafeOutcome::with(['safe:id,name', 'currency:id,code,name', 'supplier:id,name'])
            ->latest()
            ->limit(8)
            ->get();

        $recentCashMovements = $recentIncomeEntries
            ->map(function ($entry) {
                return [
                    'type' => 'income',
                    'label' => $entry->reference ?: $entry->source,
                    'safe' => $entry->safe->name ?? 'N/A',
                    'amount' => (float) $entry->amount,
                    'currency' => $entry->currency->code ?? '',
                    'date' => $entry->created_at,
                ];
            })
            ->concat($recentOutcomeEntries->map(function ($entry) {
                return [
                    'type' => 'outcome',
                    'label' => $entry->description ?: ($entry->reference ?: 'Outcome'),
                    'safe' => $entry->safe->name ?? 'N/A',
                    'amount' => (float) $entry->amount,
                    'currency' => $entry->currency->code ?? '',
                    'date' => $entry->created_at,
                ];
            }))
            ->sortByDesc('date')
            ->take(10)
            ->values();

        $topProducts = EmployeeSale::select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_amount) as total_amount'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        $safes = Safe::with('currencies')
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        // Most used currencies
        $currencies = \DB::table('merchant_currencies')
            ->select('currency_id', \DB::raw('count(*) as total'))
            ->groupBy('currency_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('super-admin.dashboard', compact(
            'totalMerchants',
            'activeMerchants',
            'activeSubscriptions',
            'expiringSoon',
            'totalRevenue',
            'totalPackages',
            'activePackages',
            'totalUsers',
            'merchantAdmins',
            'employees',
            'currencySymbol',
            'totalSales',
            'salesCount',
            'totalIncome',
            'totalOutcome',
            'netCashFlow',
            'activeSafes',
            'totalSafes',
            'recentSubscriptions',
            'expiringSubscriptions',
            'merchantsByPackage',
            'recentSales',
            'recentIncomeEntries',
            'recentOutcomeEntries',
            'recentCashMovements',
            'topProducts',
            'safes',
            'suppliers',
            'currencies'
        ));
    }

    /**
     * Dashboard analytics endpoint for live charts.
     */
    public function analytics(): JsonResponse
    {
        $months = [];
        $salesSeries = [];
        $incomeSeries = [];
        $outcomeSeries = [];

        for ($offset = 5; $offset >= 0; $offset--) {
            $month = Carbon::now()->startOfMonth()->subMonths($offset);

            $months[] = $month->format('M Y');
            $salesSeries[] = (float) EmployeeSale::whereYear('sale_date', $month->year)
                ->whereMonth('sale_date', $month->month)
                ->sum('total_amount');
            $incomeSeries[] = (float) SafeIncome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            $outcomeSeries[] = (float) SafeOutcome::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
        }

        $profitSeries = [];
        foreach ($months as $index => $label) {
            $profitSeries[] = round($salesSeries[$index] + $incomeSeries[$index] - $outcomeSeries[$index], 2);
        }

        $topProducts = EmployeeSale::select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(total_amount) as total_amount'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'name' => $sale->product->name ?? 'Unknown Product',
                    'quantity' => (float) $sale->total_quantity,
                    'amount' => (float) $sale->total_amount,
                ];
            })
            ->values();

        $summary = [
            'total_merchants' => Merchant::count(),
            'active_merchants' => Merchant::where('is_active', true)->count(),
            'total_revenue' => (float) Subscription::where('subscriptions.is_active', true)
                ->join('packages', 'subscriptions.package_id', '=', 'packages.id')
                ->sum('packages.price'),
            'total_sales' => (float) EmployeeSale::sum('total_amount'),
            'sales_count' => EmployeeSale::count(),
            'total_income' => (float) SafeIncome::sum('amount'),
            'total_outcome' => (float) SafeOutcome::sum('amount'),
            'net_cash_flow' => (float) SafeIncome::sum('amount') + (float) EmployeeSale::sum('total_amount') - (float) SafeOutcome::sum('amount'),
            'active_subscriptions' => Subscription::where('is_active', true)->count(),
            'expiring_soon' => Subscription::where('expires_at', '<=', now()->addDays(30))
                ->where('expires_at', '>=', now())
                ->where('is_active', true)
                ->count(),
            'total_packages' => Package::count(),
            'total_safes' => Safe::count(),
            'active_safes' => Safe::where('is_active', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'charts' => [
                'months' => $months,
                'sales' => $salesSeries,
                'income' => $incomeSeries,
                'outcome' => $outcomeSeries,
                'profit' => $profitSeries,
                'top_products' => $topProducts,
                'subscription_labels' => ['Active', 'Expiring Soon', 'Inactive'],
                'subscription_values' => [
                    Subscription::where('is_active', true)->count(),
                    Subscription::where('expires_at', '<=', now()->addDays(30))
                        ->where('expires_at', '>=', now())
                        ->where('is_active', true)
                        ->count(),
                    Subscription::where('is_active', false)->count(),
                ],
            ],
        ]);
    }
}
