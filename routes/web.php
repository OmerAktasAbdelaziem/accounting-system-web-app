<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Commissions\CommissionController;
use App\Http\Controllers\AdvanceController;
use App\Http\Controllers\Storages\StorageController;
use App\Http\Controllers\Safes\SafeController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\MerchantController;
use App\Http\Controllers\SuperAdmin\PackageController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\FeatureAccessController;
use App\Http\Controllers\SuperAdmin\VatRateController;
use App\Http\Controllers\SuperAdmin\SystemUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

// Locale switcher
Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Landing Page
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->user_type === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('landing');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    
    // Super Admin Login Routes
    Route::get('super-admin/login', [AuthController::class, 'showSuperAdminLoginForm'])->name('super-admin.login');
    Route::post('super-admin/login', [AuthController::class, 'superAdminLogin']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    // Keep old path for backward compatibility but redirect to the canonical `dashboard`
    Route::get('system-dashboard-fix', function () { return redirect()->route('dashboard'); });

    // Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('{product}', [ProductController::class, 'show'])->name('show');
        Route::get('{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('filter', [ProductController::class, 'filter'])->name('filter');
        Route::get('export', [ProductController::class, 'export'])->name('export');
        Route::post('{product}/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjustStock');
    });

    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Employees
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::get('{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::get('export', [EmployeeController::class, 'export'])->name('export');
    });

    // Sales
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::post('/', [SalesController::class, 'store'])->name('store');
    });

    // Suppliers
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::get('{supplier}/statement-pdf', [SupplierController::class, 'statementPdf'])->name('statement-pdf');
        Route::get('{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
        Route::post('{supplier}/purchases', [SupplierController::class, 'storePurchase'])->name('purchases.store');
        Route::put('{supplier}/purchases/{purchase}', [SupplierController::class, 'updatePurchase'])->name('purchases.update');
        Route::delete('{supplier}/purchases/{purchase}', [SupplierController::class, 'destroyPurchase'])->name('purchases.destroy');
        Route::post('{supplier}/payments', [SupplierController::class, 'storePayment'])->name('payments.store');
        Route::put('{supplier}/payments/{payment}', [SupplierController::class, 'updatePayment'])->name('payments.update');
        Route::delete('{supplier}/payments/{payment}', [SupplierController::class, 'destroyPayment'])->name('payments.destroy');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('{invoice}/edit', [InvoiceController::class, 'edit'])->name('edit');
        Route::put('{invoice}', [InvoiceController::class, 'update'])->name('update');
        Route::delete('{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
        Route::get('{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('pdf');
    });
    // Payroll
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('index');
        Route::get('create', [PayrollController::class, 'create'])->name('create');
        Route::post('/', [PayrollController::class, 'store'])->name('store');
        Route::get('{payroll}', [PayrollController::class, 'show'])->name('show');
        Route::get('{payroll}/edit', [PayrollController::class, 'edit'])->name('edit');
        Route::put('{payroll}', [PayrollController::class, 'update'])->name('update');
        Route::delete('{payroll}', [PayrollController::class, 'destroy'])->name('destroy');
        Route::get('{payroll}/payslip', [PayrollController::class, 'downloadPayslip'])->name('payslip');
    });

    // Branches
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::get('create', [BranchController::class, 'create'])->name('create');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::get('{branch}', [BranchController::class, 'show'])->name('show');
        Route::get('{branch}/edit', [BranchController::class, 'edit'])->name('edit');
        Route::put('{branch}', [BranchController::class, 'update'])->name('update');
        Route::delete('{branch}', [BranchController::class, 'destroy'])->name('destroy');
        Route::post('{branch}/assign-employee', [BranchController::class, 'assignEmployee'])->name('assign-employee');
        Route::post('{branch}/remove-employee', [BranchController::class, 'removeEmployee'])->name('remove-employee');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('financial', [ReportController::class, 'financial'])->name('financial');
        Route::post('generate-pdf', [ReportController::class, 'generatePdf'])->name('generate-pdf');
    });

    // Commissions
    Route::prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('index');
        Route::get('create', [CommissionController::class, 'create'])->name('create');
        Route::post('/', [CommissionController::class, 'store'])->name('store');
        Route::get('{commission}', [CommissionController::class, 'show'])->name('show');
        Route::get('{commission}/edit', [CommissionController::class, 'edit'])->name('edit');
        Route::put('{commission}', [CommissionController::class, 'update'])->name('update');
        Route::delete('{commission}', [CommissionController::class, 'destroy'])->name('destroy');
        Route::post('{commission}/approve', [CommissionController::class, 'approve'])->name('approve');
        Route::post('{commission}/reject', [CommissionController::class, 'reject'])->name('reject');
    });

    // Employee Advances
    Route::prefix('advances')->name('advances.')->group(function () {
        Route::post('/', [AdvanceController::class, 'store'])->name('store');
        Route::delete('{advance}', [AdvanceController::class, 'destroy'])->name('destroy');
    });

    // Storages
    Route::prefix('storages')->name('storages.')->group(function () {
        Route::get('/', [StorageController::class, 'index'])->name('index');
        Route::get('create', [StorageController::class, 'create'])->name('create');
        Route::post('/', [StorageController::class, 'store'])->name('store');
        Route::get('{storage}/edit', [StorageController::class, 'edit'])->name('edit');
        Route::put('{storage}', [StorageController::class, 'update'])->name('update');
        Route::delete('{storage}', [StorageController::class, 'destroy'])->name('destroy');
        Route::get('{storage}/items', [StorageController::class, 'items'])->name('items');
        Route::post('{storage}/items', [StorageController::class, 'storeItem'])->name('storeItem');
        Route::put('items/{itemId}', [StorageController::class, 'updateItem'])->name('updateItem');
        Route::delete('items/{itemId}', [StorageController::class, 'destroyItem'])->name('destroyItem');
        Route::post('{storage}/transfer', [StorageController::class, 'transfer'])->name('transfer');
        Route::get('{storage}/transfer-history', [StorageController::class, 'transferHistory'])->name('transferHistory');
    });

    // Safes & Cash Management
    Route::prefix('safes')->name('safes.')->group(function () {
        Route::get('/', [SafeController::class, 'index'])->name('index');
        Route::get('create', [SafeController::class, 'create'])->name('create');
        Route::post('/', [SafeController::class, 'store'])->name('store');
        Route::get('{safe}', [SafeController::class, 'show'])->name('show');
        Route::get('{safe}/edit', [SafeController::class, 'edit'])->name('edit');
        Route::put('{safe}', [SafeController::class, 'update'])->name('update');
        Route::delete('{safe}', [SafeController::class, 'destroy'])->name('destroy');
        Route::get('{safe}/transactions', [SafeController::class, 'transactions'])->name('transactions');
        Route::post('{safe}/add-income', [SafeController::class, 'addIncome'])->name('add-income');
        Route::post('{safe}/add-outcome', [SafeController::class, 'addOutcome'])->name('add-outcome');
        Route::post('{safe}/add-currency', [SafeController::class, 'addCurrency'])->name('add-currency');
    });

    // Profile & Settings
    Route::get('profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('change-password', [ProfileController::class, 'changePassword'])->name('change-password');

    // Settings (System Preferences)
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
    });

    // Audit Logs
    Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('{auditLog}', [AuditLogController::class, 'show'])->name('show');
        Route::get('export', [AuditLogController::class, 'export'])->name('export');
    });

    // Exit Inspection (accessible from merchant dashboard when inspecting)
    Route::post('super-admin/exit-inspection', [SystemUserController::class, 'exitInspection'])->name('super-admin.exit-inspection');

    // Admin - Role Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // Super Admin Routes
    Route::middleware('super_admin')->prefix('super-admin')->name('super-admin.')->group(function () {
        // Dashboard
        Route::get('/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard/analytics', [SuperAdminDashboardController::class, 'analytics'])->name('dashboard.analytics');

        // System Users Management (above Merchants)
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [SystemUserController::class, 'index'])->name('index');
            Route::get('create', [SystemUserController::class, 'create'])->name('create');
            Route::post('/', [SystemUserController::class, 'store'])->name('store');
            Route::get('{user}/edit', [SystemUserController::class, 'edit'])->name('edit');
            Route::put('{user}', [SystemUserController::class, 'update'])->name('update');
            Route::delete('{user}', [SystemUserController::class, 'destroy'])->name('destroy');
            Route::post('{user}/toggle-status', [SystemUserController::class, 'toggleStatus'])->name('toggleStatus');
        });

        // Merchants Management
        Route::resource('merchants', MerchantController::class);
        Route::post('merchants/{merchant}/inspect', [SystemUserController::class, 'inspectMerchant'])->name('merchants.inspect');
        Route::post('merchants/{merchant}/currencies', [MerchantController::class, 'addCurrency'])->name('merchants.addCurrency');
        Route::delete('merchants/{merchant}/currencies/{currency}', [MerchantController::class, 'removeCurrency'])->name('merchants.removeCurrency');
        Route::put('merchants/{merchant}/vat', [MerchantController::class, 'updateVat'])->name('merchants.updateVat');
        Route::get('merchants/{merchant}/details', [MerchantController::class, 'details'])->name('merchants.details');

        // Packages Management
        Route::resource('packages', PackageController::class);
        Route::get('packages/{package}/features', [PackageController::class, 'features'])->name('packages.features');

        // Subscriptions Management
        Route::resource('subscriptions', SubscriptionController::class);
        Route::get('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renewForm'])->name('subscriptions.renew');
        Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renewStore'])->name('subscriptions.renew.store');
        Route::get('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

        // VAT Rate Management
        Route::resource('vat-rates', VatRateController::class, ['except' => ['create', 'edit', 'show']]);

        // Feature Access Management
        Route::prefix('feature-access')->name('feature-access.')->group(function () {
            Route::get('/', [FeatureAccessController::class, 'index'])->name('index');
            Route::post('/update', [FeatureAccessController::class, 'update'])->name('update');
            Route::post('/reset', [FeatureAccessController::class, 'reset'])->name('reset');
        });

        // Telegram Error Testing Routes (for development only)
        if (app()->environment('local')) {
            Route::prefix('telegram-test')->name('telegram-test.')->group(function () {
                Route::get('error', [\App\Http\Controllers\SuperAdmin\TelegramTestController::class, 'testError'])->name('error');
                Route::get('exception', [\App\Http\Controllers\SuperAdmin\TelegramTestController::class, 'testException'])->name('exception');
                Route::get('500-error', [\App\Http\Controllers\SuperAdmin\TelegramTestController::class, 'test500Error'])->name('500-error');
                Route::get('404-error', [\App\Http\Controllers\SuperAdmin\TelegramTestController::class, 'test404Error'])->name('404-error');
                Route::get('detailed-error', [\App\Http\Controllers\SuperAdmin\TelegramTestController::class, 'testDetailedError'])->name('detailed-error');
                Route::get('real-error', [\App\Http\Controllers\SuperAdmin\TelegramTestController::class, 'triggerRealError'])->name('real-error');
                Route::get('raw-send', [\App\Http\Controllers\SuperAdmin\TelegramTestController::class, 'testRawSend'])->name('raw-send');
            });
        }
    });
});

// Redirect old HTML files to new Blade routes
Route::redirect('/login.html', '/login');
Route::get('/admin-dashboard.html', function() { return redirect()->route('dashboard'); });
Route::get('/dashboard.html', function() { return redirect()->route('dashboard'); });
Route::redirect('/products-management.html', '/products');
Route::redirect('/employees-management.html', '/employees');
Route::redirect('/sales-dashboard.html', '/reports/sales');
Route::redirect('/inventory-dashboard.html', '/reports/inventory');
Route::redirect('/accounting-management.html', '/reports/financial');
Route::redirect('/profile-settings.html', '/profile');

