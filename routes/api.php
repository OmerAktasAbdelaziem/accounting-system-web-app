<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ChartOfAccountController;
use App\Http\Controllers\Api\JournalEntryController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ReportingController;

// ============================================================================
// PUBLIC AUTHENTICATION ROUTES (No middleware required)
// ============================================================================

Route::prefix('v1/auth')->group(function () {
    // User login - generates API token (with rate limiting - 5 attempts per minute)
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('rate-limit:login')
        ->name('auth.login');
});

// ============================================================================
// PROTECTED ROUTES (Require API token authentication)
// ============================================================================

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Apply CheckApiToken middleware to all API routes
Route::prefix('v1')->middleware('check-api-token')->group(function () {

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');
        Route::post('change-password', [AuthController::class, 'changePassword'])->name('auth.changePassword');
    });
    
    // Category routes
    Route::apiResource('categories', CategoryController::class, ['names' => 'api.categories']);

    // Product routes
    Route::apiResource('products', ProductController::class, ['names' => 'api.products']);
    Route::get('products/low-stock', [ProductController::class, 'lowStock'])->name('api.products.lowStock');
    Route::get('categories/{category}/products', [ProductController::class, 'byCategory'])->name('api.products.byCategory');

    // Inventory routes
    Route::prefix('inventory')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/movement', [InventoryController::class, 'recordMovement'])->name('inventory.record');
        Route::get('/summary', [InventoryController::class, 'summary'])->name('inventory.summary');
        Route::get('/products/{product}/history', [InventoryController::class, 'productHistory'])->name('inventory.history');
        Route::get('/movements/{type}', [InventoryController::class, 'byType'])->name('inventory.byType');
    });

    // Ledger Accounting Routes (Phase 2)
    Route::prefix('accounting')->group(function () {
        // Chart of Accounts
        Route::apiResource('chart-of-accounts', ChartOfAccountController::class, ['names' => 'api.chartOfAccounts']);
        Route::get('chart-of-accounts/{account}/balance', [ChartOfAccountController::class, 'balance'])->name('api.accounts.balance');
        Route::get('chart-of-accounts/type/{type}', [ChartOfAccountController::class, 'byType'])->name('api.accounts.byType');

        // Journal Entries
        Route::apiResource('journal-entries', JournalEntryController::class, ['names' => 'api.journalEntries']);
        Route::post('journal-entries/{entry}/post', [JournalEntryController::class, 'post'])->name('api.journal.post');
        Route::post('journal-entries/{entry}/reverse', [JournalEntryController::class, 'reverse'])->name('api.journal.reverse');
        Route::get('trial-balance', [JournalEntryController::class, 'trialBalance'])->name('api.accounting.trialBalance');
        Route::get('general-ledger/{account}', [JournalEntryController::class, 'generalLedger'])->name('api.accounting.generalLedger');
    });

    // ============================================================================
    // ALIASES FOR BACKWARD COMPATIBILITY (Phase 6 dashboards)
    // ============================================================================
    
    // Accounts endpoint alias (maps to chart-of-accounts for backward compatibility)
    Route::apiResource('accounts', ChartOfAccountController::class, ['names' => 'api.accounts']);
    Route::get('accounts/{account}/balance', [ChartOfAccountController::class, 'balance'])->name('api.accountsAlias.balance');
    Route::get('accounts/type/{type}', [ChartOfAccountController::class, 'byType'])->name('api.accountsAlias.byType');

    // Direct employee-sales endpoint (aggregates all employee sales)
    Route::get('employee-sales', [EmployeeController::class, 'getAllSales'])->name('allEmployeeSales');

    // Warehouse Management Routes (Phase 2)
    Route::prefix('warehouses')->group(function () {
        Route::apiResource('/', WarehouseController::class, ['names' => 'api.warehouses']);
        Route::get('{warehouse}/inventory', [WarehouseController::class, 'inventory'])->name('warehouse.inventory');
        Route::post('transfer', [WarehouseController::class, 'transfer'])->name('warehouse.transfer');
        Route::post('transfers/{transfer}/complete', [WarehouseController::class, 'completeTransfer'])->name('warehouse.completeTransfer');
        Route::post('transfers/{transfer}/reject', [WarehouseController::class, 'rejectTransfer'])->name('warehouse.rejectTransfer');
        Route::get('transfer-history', [WarehouseController::class, 'transferHistory'])->name('warehouse.history');
    });

    // Employee Management Routes (Phase 3)
    Route::prefix('employees')->group(function () {
        Route::apiResource('/', EmployeeController::class, ['names' => 'api.employees']);
        Route::get('{employee}/commissions', [EmployeeController::class, 'getCommissions'])->name('employee.commissions');
        Route::post('{employee}/commissions/calculate', [EmployeeController::class, 'calculateCommission'])->name('employee.calculateCommission');
        Route::post('commissions/{commission}/approve', [EmployeeController::class, 'approveCommission'])->name('employee.approveCommission');
        Route::post('commissions/{commission}/pay', [EmployeeController::class, 'payCommission'])->name('employee.payCommission');
        Route::post('{employee}/deductions', [EmployeeController::class, 'addDeduction'])->name('employee.addDeduction');
        Route::get('{employee}/deductions', [EmployeeController::class, 'getDeductions'])->name('employee.deductions');
        Route::post('{employee}/sales', [EmployeeController::class, 'recordSale'])->name('employee.recordSale');
        Route::get('{employee}/sales', [EmployeeController::class, 'getSales'])->name('employee.sales');
        Route::get('{employee}/salary-summary', [EmployeeController::class, 'getSalarySummary'])->name('employee.salarySummary');
        Route::get('/reports/payroll', [EmployeeController::class, 'payrollReport'])->name('employee.payrollReport');
    });

    // Advanced Reporting Routes (Phase 3)
    Route::prefix('reports')->group(function () {
        Route::get('financial-summary', [ReportingController::class, 'financialSummary'])->name('report.financialSummary');
        Route::get('revenue-by-account', [ReportingController::class, 'revenueByAccount'])->name('report.revenueByAccount');
        Route::get('expense-by-account', [ReportingController::class, 'expenseByAccount'])->name('report.expenseByAccount');
        Route::get('sales-performance', [ReportingController::class, 'salesPerformance'])->name('report.salesPerformance');
        Route::get('top-selling-products', [ReportingController::class, 'topSellingProducts'])->name('report.topSellingProducts');
        Route::get('commission-report', [ReportingController::class, 'commissionReport'])->name('report.commissionReport');
        Route::get('inventory-movement', [ReportingController::class, 'inventoryMovement'])->name('report.inventoryMovement');
        Route::get('account-drill-down/{account}', [ReportingController::class, 'accountDrillDown'])->name('report.accountDrillDown');
        Route::get('comparison-report', [ReportingController::class, 'comparisonReport'])->name('report.comparisonReport');
    });
});
