<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\FeatureAccess;
use App\Models\Merchant;
use App\Models\Role;
use Illuminate\Http\Request;

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
        'sales_report' => 'Sales Reports',
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
        $selectedMerchant = null;
        $rows = [];
        $columns = [];
        $featureAccess = [];

        if ($request->merchant_id) {
            $selectedMerchant = Merchant::findOrFail($request->merchant_id);
            
            // Get all roles
            $columns = ['merchant_admin', 'employee', 'viewer'];
            
            // Get features
            $rows = array_keys(self::AVAILABLE_FEATURES);
            
            // Build feature access matrix
            $featureAccess = $this->buildFeatureAccessMatrix($selectedMerchant, $columns, $rows);
        }

        return view('super-admin.feature-access.index', compact(
            'merchants',
            'selectedMerchant',
            'rows',
            'columns',
            'featureAccess'
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
            'role' => 'required|string',
            'feature' => 'required|string',
            'action' => 'required|in:enable,disable',
        ]);

        $key = "{$validated['merchant_id']}-{$validated['role']}-{$validated['feature']}";
        
        $featureAccess = FeatureAccess::where('merchant_id', $validated['merchant_id'])
            ->where('role_name', $validated['role'])
            ->where('feature_name', $validated['feature'])
            ->first();

        if ($validated['action'] === 'enable') {
            if (!$featureAccess) {
                FeatureAccess::create([
                    'merchant_id' => $validated['merchant_id'],
                    'role_name' => $validated['role'],
                    'feature_name' => $validated['feature'],
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

        return redirect()->back()->with('success', 'Feature access updated');
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
            
            foreach (['merchant_admin', 'employee', 'viewer'] as $role) {
                foreach ($packageFeatures as $feature) {
                    FeatureAccess::create([
                        'merchant_id' => $merchant->id,
                        'role_name' => $role,
                        'feature_name' => $feature,
                        'is_enabled' => true,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Feature access reset to package defaults');
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
                    ->where('role_name', $role)
                    ->where('feature_name', $feature)
                    ->where('is_enabled', true)
                    ->exists();
                    
                $matrix[$feature][$role] = $access;
            }
        }

        return $matrix;
    }
}

