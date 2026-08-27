<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Commission;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Safe;
use App\Models\Storage;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DataRecoveryController extends Controller
{
    public function index()
    {
        $merchants = Merchant::with(['branches' => function ($query) {
            $query->orderBy('name');
        }])->orderBy('business_name')->get();

        $recoverySections = [
            [
                'key' => 'branches',
                'title' => 'Branches without Merchant',
                'mode' => 'merchant',
                'description' => 'Attach orphan branches to a merchant.',
                'records' => Branch::whereNull('merchant_id')->latest()->get(),
                'columns' => ['name', 'code', 'city'],
                'emptyLabel' => 'No orphan branches found',
            ],
            [
                'key' => 'users',
                'title' => 'Users without Merchant',
                'mode' => 'merchant',
                'description' => 'Assign unlinked users to a merchant.',
                'records' => User::whereNull('merchant_id')->where('user_type', '!=', 'super_admin')->latest()->get(),
                'columns' => ['name', 'email', 'user_type'],
                'emptyLabel' => 'No orphan users found',
            ],
            [
                'key' => 'employees',
                'title' => 'Employees without Merchant',
                'mode' => 'merchant',
                'description' => 'Attach orphan employees to a merchant.',
                'records' => Employee::whereNull('merchant_id')->latest()->get(),
                'columns' => ['name', 'email', 'position'],
                'emptyLabel' => 'No orphan employees found',
            ],
            [
                'key' => 'products',
                'title' => 'Products without Branch',
                'mode' => 'branch',
                'description' => 'Attach products to a branch inside the chosen merchant.',
                'records' => Product::doesntHave('branches')->with('category')->latest()->get(),
                'columns' => ['name', 'barcode', 'category'],
                'emptyLabel' => 'No orphan products found',
            ],
            [
                'key' => 'categories',
                'title' => 'Categories without Branch',
                'mode' => 'branch',
                'description' => 'Attach categories to a branch inside the chosen merchant.',
                'records' => Category::doesntHave('branches')->latest()->get(),
                'columns' => ['name', 'code', 'display_order'],
                'emptyLabel' => 'No orphan categories found',
            ],
            [
                'key' => 'suppliers',
                'title' => 'Suppliers without Branch',
                'mode' => 'branch',
                'description' => 'Attach suppliers to a branch inside the chosen merchant.',
                'records' => Supplier::doesntHave('branches')->latest()->get(),
                'columns' => ['name', 'email', 'phone'],
                'emptyLabel' => 'No orphan suppliers found',
            ],
            [
                'key' => 'customers',
                'title' => 'Customers without Branch',
                'mode' => 'branch',
                'description' => 'Attach customers to a branch inside the chosen merchant.',
                'records' => Customer::doesntHave('branches')->latest()->get(),
                'columns' => ['name', 'email', 'phone'],
                'emptyLabel' => 'No orphan customers found',
            ],
            [
                'key' => 'invoices',
                'title' => 'Invoices without Branch',
                'mode' => 'branch',
                'description' => 'Attach invoices to a branch inside the chosen merchant.',
                'records' => Invoice::doesntHave('branches')->with('customer')->latest()->get(),
                'columns' => ['invoice_number', 'customer', 'total'],
                'emptyLabel' => 'No orphan invoices found',
            ],
            [
                'key' => 'storages',
                'title' => 'Storages without Branch',
                'mode' => 'branch',
                'description' => 'Attach storages to a branch inside the chosen merchant.',
                'records' => Storage::doesntHave('branches')->latest()->get(),
                'columns' => ['name', 'storage_type', 'location'],
                'emptyLabel' => 'No orphan storages found',
            ],
            [
                'key' => 'safes',
                'title' => 'Safes without Branch',
                'mode' => 'branch',
                'description' => 'Attach safes to a branch inside the chosen merchant.',
                'records' => Safe::doesntHave('branches')->latest()->get(),
                'columns' => ['name', 'balance', 'location'],
                'emptyLabel' => 'No orphan safes found',
            ],
            [
                'key' => 'commissions',
                'title' => 'Commissions without Branch',
                'mode' => 'branch',
                'description' => 'Attach commissions to a branch inside the chosen merchant.',
                'records' => Commission::doesntHave('branches')->with('employee')->latest()->get(),
                'columns' => ['employee', 'commission_amount', 'status'],
                'emptyLabel' => 'No orphan commissions found',
            ],
        ];

        return view('super-admin.data-recovery.index', compact('merchants', 'recoverySections'));
    }

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|string|in:branches,users,employees,products,categories,suppliers,customers,invoices,storages,safes,commissions',
            'merchant_id' => 'required|exists:merchants,id',
            'branch_id' => 'nullable|exists:branches,id',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $merchant = Merchant::with(['branches'])->findOrFail($validated['merchant_id']);
        $recordIds = array_values(array_unique(array_map('intval', $validated['ids'])));
        $affected = 0;

        if ($validated['group'] === 'branches') {
            $affected = Branch::whereIn('branches.id', $recordIds)
                ->whereNull('merchant_id')
                ->update(['merchant_id' => $merchant->id]);

            return back()->with('success', $affected . ' branch(es) moved to the selected merchant.');
        }

        if ($validated['group'] === 'users') {
            $affected = User::whereIn('id', $recordIds)
                ->whereNull('merchant_id')
                ->where('user_type', '!=', 'super_admin')
                ->update(['merchant_id' => $merchant->id]);

            return back()->with('success', $affected . ' user(s) moved to the selected merchant.');
        }

        if ($validated['group'] === 'employees') {
            $affected = Employee::whereIn('id', $recordIds)
                ->whereNull('merchant_id')
                ->update(['merchant_id' => $merchant->id]);

            return back()->with('success', $affected . ' employee(s) moved to the selected merchant.');
        }

        $branch = null;
        if (!empty($validated['branch_id'])) {
            $branch = Branch::where('merchant_id', $merchant->id)->whereKey($validated['branch_id'])->first();

            if (!$branch) {
                return back()->withErrors(['branch_id' => 'Selected branch does not belong to the chosen merchant.']);
            }
        }

        if (!$branch) {
            $branch = $merchant->branches()->orderBy('branches.id')->first();
        }

        if (!$branch) {
            $branch = Branch::create([
                'merchant_id' => $merchant->id,
                'name' => ($merchant->business_name ?: $merchant->name) . ' Main Branch',
                'code' => $this->generateBranchCode($merchant),
                'is_active' => true,
            ]);
        }

        $modelClass = $this->resolveBranchableModel($validated['group']);
        if (!$modelClass) {
            return back()->withErrors(['group' => 'Unsupported recovery group.']);
        }

        $records = $modelClass::whereIn('id', $recordIds)->doesntHave('branches')->get();
        foreach ($records as $record) {
            $record->branches()->syncWithoutDetaching([$branch->id]);
            $affected++;
        }

        return back()->with('success', $affected . ' record(s) attached to ' . $branch->name . '.');
    }

    protected function resolveBranchableModel(string $group): ?string
    {
        return match ($group) {
            'products' => Product::class,
            'categories' => Category::class,
            'suppliers' => Supplier::class,
            'customers' => Customer::class,
            'invoices' => Invoice::class,
            'storages' => Storage::class,
            'safes' => Safe::class,
            'commissions' => Commission::class,
            default => null,
        };
    }

    protected function generateBranchCode(Merchant $merchant): string
    {
        $base = strtoupper(Str::slug($merchant->business_name ?: $merchant->name, ''));
        $base = $base !== '' ? $base : 'BRANCH';

        return $base . '-' . Str::upper(Str::random(5));
    }
}
