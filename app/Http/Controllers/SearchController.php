<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $q = trim($q);

        // Require a minimum query length to avoid expensive broad searches
        if ($q === '' || mb_strlen($q) < 3) {
            return response()->json(['results' => []]);
        }

        $user = $request->user();
        $results = [];

        if ($user && $user->isSuperAdmin()) {
            // Give precedence to business_name matches, then name
            $merchants = Merchant::where(function($qWhere) use ($q) {
                    $qWhere->where('business_name', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%");
                })
                ->orderByRaw("CASE WHEN business_name LIKE ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END", ["%{$q}%","%{$q}%"])
                ->limit(12)
                ->get(['id', 'business_name', 'name']);

            foreach ($merchants as $m) {
                $results[] = [
                    'type' => 'merchant',
                    'id' => $m->id,
                    'title' => $m->business_name ?: $m->name,
                    'url' => route('super-admin.merchants.show', $m->id),
                ];
            }

            return response()->json(['results' => $results]);
        }

        // For merchant users: search within merchant-scoped models
        $merchantId = $user->merchant_id ?? null;

        // Helper to conditionally scope by merchant_id when that column exists
        $scopeByMerchant = function ($query) use ($merchantId) {
            if ($merchantId && Schema::hasColumn((new \Illuminate\Database\Eloquent\Model)->getTable(), 'merchant_id')) {
                // This generic check above is a fallback; prefer model tables below when used explicitly
            }
        };

        // Products
        $productsQuery = Product::query()->where('name', 'like', "%{$q}%");
        if ($merchantId && Schema::hasColumn((new Product)->getTable(), 'merchant_id')) {
            $productsQuery->where('merchant_id', $merchantId);
        }
        $products = $productsQuery->orderByRaw("LOCATE(?, name)", [$q])->limit(6)->get(['id','name']);
        foreach ($products as $p) {
            $results[] = ['type' => 'product', 'id' => $p->id, 'title' => $p->name, 'url' => route('products.show', $p->id)];
        }
        $employeesQuery = Employee::query()->where('name', 'like', "%{$q}%");
        if ($merchantId && Schema::hasColumn((new Employee)->getTable(), 'merchant_id')) {
            $employeesQuery->where('merchant_id', $merchantId);
        }
        $employees = $employeesQuery->orderByRaw("LOCATE(?, name)", [$q])->limit(6)->get(['id','name']);
        foreach ($employees as $e) {
            $results[] = ['type' => 'employee', 'id' => $e->id, 'title' => $e->name, 'url' => route('employees.show', $e->id)];
        }
        $branchesQuery = Branch::query()->where('name', 'like', "%{$q}%");
        if ($merchantId && Schema::hasColumn((new Branch)->getTable(), 'merchant_id')) {
            $branchesQuery->where('merchant_id', $merchantId);
        }
        $branches = $branchesQuery->orderByRaw("LOCATE(?, name)", [$q])->limit(4)->get(['id','name']);
        foreach ($branches as $b) {
            $results[] = ['type' => 'branch', 'id' => $b->id, 'title' => $b->name, 'url' => route('branches.show', $b->id)];
        }
        $customersQuery = Customer::query()->where('name', 'like', "%{$q}%");
        if ($merchantId && Schema::hasColumn((new Customer)->getTable(), 'merchant_id')) {
            $customersQuery->where('merchant_id', $merchantId);
        }
        $customers = $customersQuery->orderByRaw("LOCATE(?, name)", [$q])->limit(6)->get(['id','name']);
        foreach ($customers as $c) {
            $results[] = ['type' => 'customer', 'id' => $c->id, 'title' => $c->name, 'url' => '#'];
        }
        $suppliersQuery = Supplier::query()->where('name', 'like', "%{$q}%");
        if ($merchantId && Schema::hasColumn((new Supplier)->getTable(), 'merchant_id')) {
            $suppliersQuery->where('merchant_id', $merchantId);
        }
        $suppliers = $suppliersQuery->orderByRaw("LOCATE(?, name)", [$q])->limit(6)->get(['id','name']);
        foreach ($suppliers as $s) {
            $results[] = ['type' => 'supplier', 'id' => $s->id, 'title' => $s->name, 'url' => route('suppliers.show', $s->id)];
        }

        // Deduplicate by type+id keeping first occurrence, then limit total results
        $seen = [];
        $filtered = [];
        foreach ($results as $r) {
            $key = $r['type'].'-'.$r['id'];
            if (isset($seen[$key])) continue;
            $seen[$key] = true;
            $filtered[] = $r;
            if (count($filtered) >= 12) break;
        }

        return response()->json(['results' => $filtered]);
    }
}
