<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\Categories\CategoryController;
use App\Http\Controllers\Employees\EmployeeController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Commissions\CommissionController;
use App\Http\Controllers\Storages\StorageController;
use App\Http\Controllers\Safes\SafeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\BranchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

// Locale switcher
Route::get('locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    // Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    // Suppliers
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('{supplier}', [SupplierController::class, 'show'])->name('show');
        Route::get('{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
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
        Route::post('{payroll}/process', [PayrollController::class, 'process'])->name('process');
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
        Route::post('{safe}/deposit', [SafeController::class, 'deposit'])->name('deposit');
        Route::post('{safe}/withdraw', [SafeController::class, 'withdraw'])->name('withdraw');
        Route::get('{safe}/transactions', [SafeController::class, 'transactions'])->name('transactions');
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

    // Admin - User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('{user}/reset-password', [UserController::class, 'resetPassword'])->name('resetPassword');
    });

    // Admin - Role Management
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('create', [RoleController::class, 'create'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');
    });

    // Admin - Permission Management
    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::get('create', [PermissionController::class, 'create'])->name('create');
        Route::post('/', [PermissionController::class, 'store'])->name('store');
        Route::get('{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    });
});

// Redirect old HTML files to new Blade routes
Route::redirect('/login.html', '/login');
Route::redirect('/admin-dashboard.html', '/dashboard');
Route::redirect('/dashboard.html', '/dashboard');
Route::redirect('/products-management.html', '/products');
Route::redirect('/employees-management.html', '/employees');
Route::redirect('/sales-dashboard.html', '/reports/sales');
Route::redirect('/inventory-dashboard.html', '/reports/inventory');
Route::redirect('/accounting-management.html', '/reports/financial');
Route::redirect('/profile-settings.html', '/profile');

