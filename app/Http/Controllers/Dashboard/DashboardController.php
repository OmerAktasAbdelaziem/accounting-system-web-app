<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Employee;
use App\Models\JournalEntry;
use App\Models\Commission;
use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\Safe;
use App\Models\SafeTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalEmployees = Employee::count();
        $lowStockCount = Product::whereRaw('current_stock <= min_stock')->count();
        
        // Calculate total sales from journal entries (sales = credit side of invoices)
        $totalSales = JournalEntry::where('reference_type', 'invoice')
            ->where('status', 'posted')
            ->sum('total_credit');

        // New feature data
        $pendingCommissions = Commission::where('status', 'pending')->count();
        
        // Storage usage calculation
        $totalStorageCapacity = Storage::sum('capacity') ?? 1;
        $totalStorageUsage = StorageItem::sum('quantity') ?? 0;
        $storageUsage = $totalStorageCapacity > 0 ? round(($totalStorageUsage / $totalStorageCapacity) * 100) : 0;
        
        // Safe balance
        $safeBalance = Safe::sum('balance') ?? 0;
        
        // Today's transactions
        $transactionsToday = SafeTransaction::where('created_at', '>=', now()->startOfDay())->count();

        $recentTransactions = JournalEntry::latest()->take(5)->get();
        
        // Sample data for charts
        $salesData = [1200, 1900, 3000, 5000, 4200, 3200];
        $inventoryData = [70, 30];

        return view('dashboard.index', compact(
            'totalProducts',
            'totalEmployees',
            'lowStockCount',
            'totalSales',
            'pendingCommissions',
            'storageUsage',
            'safeBalance',
            'transactionsToday',
            'recentTransactions',
            'salesData',
            'inventoryData'
        ));
    }
}
