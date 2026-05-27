<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FeatureAccess;
use App\Models\FeatureAccessOverride;
use App\Models\Employee;
use App\Models\Merchant;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FeatureAccessController extends Controller
{
    /**
     * All available features in the system
     */
    private const AVAILABLE_FEATURES = [
        // Core Modules
        'products' => 'Products Management',
        'categories' => 'Categories Management',
        'employees' => 'Employees Management',
        'customers' => 'Customers Management',
        'suppliers' => 'Suppliers Management',
        
        // Financial & Billing
        'invoicing' => 'Invoicing & Billing',
        'payroll' => 'Payroll Management',
        'commissions' => 'Commissions Management',
        'financial_report' => 'Financial Reports',
        
        // Inventory & Storage
        'inventory' => 'Inventory Management',
        'inventory_report' => 'Inventory Reports',
        'storages' => 'Storage Management',
        'safes' => 'Safes Management',
        
        // Operations & Reporting
        'branches' => 'Branches Management',
        'sales' => 'Sales Page',
        'sales_report' => 'Sales Reports',
        'downloads' => 'Download & Export Actions',
        'audit_logs' => 'Audit Logs',
        
        // Advanced Features
        'basic_reporting' => 'Basic Reporting',
        'advanced_reporting' => 'Advanced Reporting',
        'multi_branch' => 'Multi-Branch Support',
        'api_access' => 'API Access',
        'custom_integration' => 'Custom Integration',
        'dedicated_support' => 'Dedicated Support',
        'backup_restore' => 'Backup & Restore',
        
        // Administration
        'user_management' => 'User Management',
        'roles_management' => 'Roles Management',
        'permissions_management' => 'Permissions Management',
    ];

    /**
     * Get all available features
     */
    public static function getAvailableFeatures(): array
    {
        return self::AVAILABLE_FEATURES;
    }

    /**
     * Show feature access management matrix
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', FeatureAccess::class);

        $merchants = Merchant::where('is_active', true)->get();
        $availableFeatures = self::AVAILABLE_FEATURES;
        $existingEmails = User::whereNotNull('email')->pluck('email')->values();
        $selectedMerchant = null;
        $rows = [];
        $columns = [];
        $featureAccess = [];
        $employees = collect();
        $employeeOverrides = collect();
        $employeeUserMap = collect();
        $roles = collect();

        if ($request->merchant_id) {
            $selectedMerchant = Merchant::findOrFail($request->merchant_id);
            
            // Get all roles that exist in this app
            $roles = Role::orderBy('name')->get();
            $columns = $roles->pluck('name')->all();
            
            // Get features
            $rows = array_keys(self::AVAILABLE_FEATURES);
            
            // Build feature access matrix
            $featureAccess = $this->buildFeatureAccessMatrix($selectedMerchant, $roles, $rows);

            $employees = Employee::where('merchant_id', $selectedMerchant->id)
                ->orderBy('name')
                ->get();

            $employeeUserMap = User::where('merchant_id', $selectedMerchant->id)
                ->where('user_type', 'employee')
                ->get()
                ->keyBy('email');

            $employeeOverrides = FeatureAccessOverride::where('merchant_id', $selectedMerchant->id)
                ->get()
                ->groupBy('user_id');
        }

        return view('super-admin.feature-access.index', compact(
            'merchants',
            'selectedMerchant',
            'rows',
            'columns',
            'roles',
            'featureAccess',
            'employees',
            'employeeOverrides',
            'employeeUserMap',
            'availableFeatures',
            'existingEmails'
        ));
    }

    /**
     * Update feature access
     */
    public function update(Request $request)
    {
        $this->authorize('update', FeatureAccess::class);

        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'role_id' => 'required|exists:roles,id',
            'feature' => 'required|string',
            'action' => 'required|in:enable,disable',
        ]);

        $role = Role::findOrFail($validated['role_id']);

        $featureAccess = FeatureAccess::where('merchant_id', $validated['merchant_id'])
            ->where('role_id', $role->id)
            ->where('feature_key', $validated['feature'])
            ->first();

        if ($validated['action'] === 'enable') {
            if (!$featureAccess) {
                FeatureAccess::create([
                    'merchant_id' => $validated['merchant_id'],
                    'role_id' => $role->id,
                    'feature_key' => $validated['feature'],
                    'is_enabled' => true,
                ]);
            } else {
                $featureAccess->update(['is_enabled' => true]);
            }
        } else {
            if ($featureAccess) {
                $featureAccess->update(['is_enabled' => false]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Feature access updated',
                'merchant_id' => (int) $validated['merchant_id'],
                'role_id' => (int) $role->id,
                'feature' => $validated['feature'],
                'enabled' => $validated['action'] === 'enable',
            ]);
        }

        return redirect()->route('super-admin.feature-access.index', ['merchant_id' => $validated['merchant_id']])
            ->with('success', 'Feature access updated');
    }

    /**
     * Update special access for a specific employee.
     */
    public function updateEmployeeAccess(Request $request)
    {
        $this->authorize('update', FeatureAccess::class);

        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'user_id' => 'required|exists:users,id',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'decision' => 'required|in:grant,deny',
        ]);

        $user = User::where('merchant_id', $validated['merchant_id'])
            ->where('user_type', 'employee')
            ->whereKey($validated['user_id'])
            ->firstOrFail();

        FeatureAccessOverride::where('merchant_id', $validated['merchant_id'])
            ->where('user_id', $user->id)
            ->delete();

        foreach (($validated['features'] ?? []) as $featureKey) {
            FeatureAccessOverride::create([
                'merchant_id' => $validated['merchant_id'],
                'user_id' => $user->id,
                'feature_key' => $featureKey,
                'is_enabled' => $validated['decision'] === 'grant',
            ]);
        }

        $message = $validated['decision'] === 'grant'
            ? "Special access granted for {$user->name}"
            : "Access denied for selected pages for {$user->name}";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'merchant_id' => (int) $validated['merchant_id'],
                'user_id' => (int) $user->id,
                'decision' => $validated['decision'],
                'features' => array_values($validated['features'] ?? []),
            ]);
        }

        return redirect()->route('super-admin.feature-access.index', ['merchant_id' => $validated['merchant_id']])
            ->with('success', $message);
    }

    /**
     * Create a login user account for an employee.
     */
    public function createEmployeeLogin(Request $request)
    {
        $this->authorize('update', FeatureAccess::class);

        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
            'employee_id' => 'required|exists:employees,id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $employee = Employee::where('merchant_id', $validated['merchant_id'])
            ->whereKey($validated['employee_id'])
            ->firstOrFail();

        $employeeRole = Role::firstOrCreate(
            ['name' => 'employee'],
            ['description' => 'Merchant employee user']
        );

        DB::transaction(function () use ($employee, $validated, $employeeRole) {
            $employee->update(['email' => $validated['email']]);

            User::create([
                'name' => $employee->name,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'merchant_id' => $employee->merchant_id,
                'user_type' => 'employee',
                'role_id' => $employeeRole->id,
                'is_active' => true,
                'phone' => $employee->phone,
                'address' => $employee->address,
            ]);
        });

        $message = "Login user created for {$employee->name}";

        return redirect()->route('super-admin.feature-access.index', ['merchant_id' => $validated['merchant_id']])
            ->with('success', $message);
    }

    /**
     * Reset feature access to package defaults
     */
    public function reset(Request $request)
    {
        $this->authorize('update', FeatureAccess::class);

        $validated = $request->validate([
            'merchant_id' => 'required|exists:merchants,id',
        ]);

        $merchant = Merchant::findOrFail($validated['merchant_id']);
        $subscription = $merchant->subscriptions->first();

        if ($subscription) {
            // Clear existing feature access
            FeatureAccess::where('merchant_id', $merchant->id)->delete();

            // Set default access based on package features
            $packageFeatures = $subscription->package->features->pluck('feature_key')->toArray();
            $roles = Role::whereIn('name', ['merchant_admin', 'employee', 'viewer'])->get()->keyBy('name');
            
            foreach ($roles as $role) {
                foreach ($packageFeatures as $feature) {
                    FeatureAccess::create([
                        'merchant_id' => $merchant->id,
                        'role_id' => $role->id,
                        'feature_key' => $feature,
                        'is_enabled' => true,
                    ]);
                }
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Feature access reset to package defaults',
                    'merchant_id' => (int) $merchant->id,
                    'package_features' => array_values($packageFeatures),
                    'enabled_role_ids' => $roles->pluck('id')->values()->all(),
                ]);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Feature access reset to package defaults',
                'merchant_id' => (int) $merchant->id,
                'package_features' => [],
                'enabled_role_ids' => [],
            ]);
        }

        return redirect()->route('super-admin.feature-access.index', ['merchant_id' => $validated['merchant_id']])
            ->with('success', 'Feature access reset to package defaults');
    }

    /**
     * Build feature access matrix for a merchant
     */
    private function buildFeatureAccessMatrix($merchant, $roles, $features): array
    {
        $matrix = [];

        foreach ($features as $feature) {
            $matrix[$feature] = [];
            
            foreach ($roles as $role) {
                $access = FeatureAccess::where('merchant_id', $merchant->id)
                    ->where('role_id', $role->id)
                    ->where('feature_key', $feature)
                    ->where('is_enabled', true)
                    ->exists();

                $matrix[$feature][$role->id] = $access;
            }
        }

        return $matrix;
    }
}

