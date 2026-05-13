<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles first
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Seed multi-tenant system (currencies, packages, super admin)
        $this->call([
            CurrencySeeder::class,
            PackageSeeder::class,
            SuperAdminSeeder::class,
        ]);

        // Seed feature access permissions for all merchants and roles
        $this->call([
            FeatureAccessSeeder::class,
        ]);

        // Seed users using explicit credentials defined in UserSeeder
        $this->call([
            UserSeeder::class,
        ]);

        // Then run other seeders
        $this->call([
            ChartOfAccountsSeeder::class,
            EmployeeSeeder::class,
            CommissionSeeder::class,
            StorageSeeder::class,
            SafeSeeder::class,
            BranchSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            InvoiceSeeder::class,
            PayrollSeeder::class,
        ]);

        // Seed demo merchants for testing
        $this->call([
            DemoMerchantSeeder::class,
        ]);
    }
}
