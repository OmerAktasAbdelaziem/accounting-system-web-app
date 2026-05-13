<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PackageFeature;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        // Basic Package
        $basic = Package::firstOrCreate(
            ['name' => 'Basic'],
            [
                'description' => 'Perfect for small businesses',
                'price' => 29.99,
                'duration_days' => 30,
                'max_employees' => 5,
                'max_currencies' => 2,
                'max_languages' => 2,
                'is_active' => true,
            ]
        );

        $basicFeatures = ['invoicing', 'basic_reporting', 'products', 'categories', 'customers', 'employees', 'user_management'];
        foreach ($basicFeatures as $feature) {
            PackageFeature::firstOrCreate(
                ['package_id' => $basic->id, 'feature_key' => $feature],
                [
                    'feature_name' => ucfirst(str_replace('_', ' ', $feature)),
                    'description' => 'Feature ' . $feature,
                ]
            );
        }

        // Professional Package
        $pro = Package::firstOrCreate(
            ['name' => 'Professional'],
            [
                'description' => 'For growing businesses',
                'price' => 79.99,
                'duration_days' => 30,
                'max_employees' => 25,
                'max_currencies' => 5,
                'max_languages' => 3,
                'is_active' => true,
            ]
        );

        $proFeatures = [
            'products', 'categories', 'customers', 'suppliers', 'employees', 'invoicing', 'payroll', 
            'inventory', 'basic_reporting', 'advanced_reporting', 'multi_branch', 'branches', 
            'commissions', 'storages', 'user_management', 'roles_management', 'permissions_management', 'audit_logs'
        ];
        foreach ($proFeatures as $feature) {
            PackageFeature::firstOrCreate(
                ['package_id' => $pro->id, 'feature_key' => $feature],
                [
                    'feature_name' => ucfirst(str_replace('_', ' ', $feature)),
                    'description' => 'Feature ' . $feature,
                ]
            );
        }

        // Enterprise Package
        $enterprise = Package::firstOrCreate(
            ['name' => 'Enterprise'],
            [
                'description' => 'For large enterprises',
                'price' => 299.99,
                'duration_days' => 30,
                'max_employees' => null,
                'max_currencies' => 10,
                'max_languages' => 10,
                'is_active' => true,
            ]
        );

        $enterpriseFeatures = [
            // Core Modules
            'products', 'categories', 'employees', 'customers', 'suppliers',
            // Financial & Billing
            'invoicing', 'payroll', 'commissions', 'financial_report',
            // Inventory & Storage
            'inventory', 'inventory_report', 'storages', 'safes',
            // Operations & Reporting
            'branches', 'sales_report', 'audit_logs',
            // Advanced Features
            'basic_reporting', 'advanced_reporting', 'multi_branch', 'api_access', 
            'custom_integration', 'dedicated_support', 'backup_restore',
            // Administration
            'user_management', 'roles_management', 'permissions_management',
        ];
        foreach ($enterpriseFeatures as $feature) {
            PackageFeature::firstOrCreate(
                ['package_id' => $enterprise->id, 'feature_key' => $feature],
                [
                    'feature_name' => ucfirst(str_replace('_', ' ', $feature)),
                    'description' => 'Feature ' . $feature,
                ]
            );
        }
    }
}
