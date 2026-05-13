<?php

namespace Database\Seeders;

use App\Models\Merchant;
use App\Models\Role;
use App\Models\FeatureAccess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeatureAccessSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the feature_access table with default feature permissions.
     * 
     * Features are enabled by default for all merchants and their roles.
     * Super admins can later customize these permissions per merchant/role.
     */
    public function run(): void
    {
        // Get all features from FeatureAccessController
        $features = $this->getAvailableFeatures();
        
        // Get all merchants and roles
        $merchants = Merchant::all();
        $roles = Role::all();

        // For each merchant and role combination, create feature_access records
        foreach ($merchants as $merchant) {
            foreach ($roles as $role) {
                foreach ($features as $feature) {
                    FeatureAccess::updateOrCreate(
                        [
                            'merchant_id' => $merchant->id,
                            'role_id' => $role->id,
                            'feature_key' => $feature['key'],
                        ],
                        [
                            'is_enabled' => true, // All features enabled by default
                        ]
                    );
                }
            }
        }

        $this->command->info('✓ Feature access seeded successfully for all merchants and roles.');
    }

    /**
     * Get all available features from the controller.
     */
    private function getAvailableFeatures(): array
    {
        return [
            // Core Modules
            ['key' => 'products', 'name' => 'Products Management'],
            ['key' => 'categories', 'name' => 'Categories Management'],
            ['key' => 'employees', 'name' => 'Employees Management'],
            ['key' => 'customers', 'name' => 'Customers Management'],
            ['key' => 'suppliers', 'name' => 'Suppliers Management'],

            // Financial & Billing
            ['key' => 'invoicing', 'name' => 'Invoicing'],
            ['key' => 'payroll', 'name' => 'Payroll Management'],
            ['key' => 'commissions', 'name' => 'Commissions Tracking'],
            ['key' => 'financial_report', 'name' => 'Financial Reports'],

            // Inventory & Storage
            ['key' => 'inventory', 'name' => 'Inventory Management'],
            ['key' => 'inventory_report', 'name' => 'Inventory Reports'],
            ['key' => 'storages', 'name' => 'Storage Management'],
            ['key' => 'safes', 'name' => 'Safe Management'],

            // Operations & Reporting
            ['key' => 'branches', 'name' => 'Branch Management'],
            ['key' => 'sales_report', 'name' => 'Sales Reports'],
            ['key' => 'audit_logs', 'name' => 'Audit Logs'],

            // Advanced Features
            ['key' => 'basic_reporting', 'name' => 'Basic Reporting'],
            ['key' => 'advanced_reporting', 'name' => 'Advanced Reporting'],
            ['key' => 'multi_branch', 'name' => 'Multi-Branch Operations'],
            ['key' => 'api_access', 'name' => 'API Access'],
            ['key' => 'custom_integration', 'name' => 'Custom Integrations'],
            ['key' => 'dedicated_support', 'name' => 'Dedicated Support'],
            ['key' => 'backup_restore', 'name' => 'Backup & Restore'],

            // Administration
            ['key' => 'user_management', 'name' => 'User Management'],
            ['key' => 'roles_management', 'name' => 'Roles Management'],
            ['key' => 'permissions_management', 'name' => 'Permissions Management'],
        ];
    }
}
