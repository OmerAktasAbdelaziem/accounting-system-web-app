<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\User;

class SuperAdminDashboardController extends Controller
{
    /**
     * Show the super admin dashboard
     */
    public function index()
    {
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
            'recentSubscriptions',
            'expiringSubscriptions',
            'merchantsByPackage',
            'currencies'
        ));
    }
}
