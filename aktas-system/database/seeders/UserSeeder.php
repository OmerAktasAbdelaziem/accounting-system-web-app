<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder - Populate users table with test data
 *
 * Creates demo users with different roles for testing
 * authentication and authorization
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if (!$adminRole) {
            $adminRole = Role::create([
                'name' => 'Admin',
                'name_ar' => 'مسؤول',
                'description' => 'Administrator with full system access',
                'description_ar' => 'مسؤول مع الوصول الكامل إلى النظام',
            ]);
        }

        // Get Manager role
        $managerRole = Role::where('name', 'Manager')->first();

        // Get User role
        $userRole = Role::where('name', 'User')->first();

        // Create Admin User
        if (!User::where('email', 'admin@hamid.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@hamid.com',
                'password' => Hash::make('admin123456'),
                'role_id' => $adminRole?->id,
                'phone' => '+966501234567',
                'address' => 'Riyadh, Saudi Arabia',
                'is_active' => true,
            ]);
        }

        // Create Manager User
        if (!User::where('email', 'manager@hamid.com')->exists()) {
            User::create([
                'name' => 'Ahmed Al-Sudairi',
                'email' => 'manager@hamid.com',
                'password' => Hash::make('manager123456'),
                'role_id' => $managerRole?->id ?? $adminRole?->id,
                'phone' => '+966502345678',
                'address' => 'Jeddah, Saudi Arabia',
                'is_active' => true,
            ]);
        }

        // Create Standard User
        if (!User::where('email', 'user@hamid.com')->exists()) {
            User::create([
                'name' => 'Fatima Al-Rashid',
                'email' => 'user@hamid.com',
                'password' => Hash::make('user123456'),
                'role_id' => $userRole?->id ?? $adminRole?->id,
                'phone' => '+966503456789',
                'address' => 'Dammam, Saudi Arabia',
                'is_active' => true,
            ]);
        }

        // Create Test User (inactive)
        if (!User::where('email', 'test@hamid.com')->exists()) {
            User::create([
                'name' => 'Test Account',
                'email' => 'test@hamid.com',
                'password' => Hash::make('test123456'),
                'role_id' => $userRole?->id ?? $adminRole?->id,
                'phone' => '+966504567890',
                'address' => 'Makkah, Saudi Arabia',
                'is_active' => false,
            ]);
        }

        echo "\n✅ User seeding completed:\n";
        echo "  - Admin: admin@hamid.com / admin123456\n";
        echo "  - Manager: manager@hamid.com / manager123456\n";
        echo "  - User: user@hamid.com / user123456\n";
        echo "  - Test (inactive): test@hamid.com / test123456\n\n";
    }
}
