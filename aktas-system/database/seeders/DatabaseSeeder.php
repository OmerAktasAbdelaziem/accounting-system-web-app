<?php

namespace Database\Seeders;

use App\Models\User;
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

        // Create test admin user after roles exist
        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@aktas-system.com',
            'password' => bcrypt('password'),
            'role_id' => 1, // Admin role
            'is_active' => true,
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
    }
}
