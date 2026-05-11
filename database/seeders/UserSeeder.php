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

        // Create or update Admin User
        User::updateOrCreate(
            ['email' => 'admin@hamid.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123456'),
                'role_id' => $adminRole?->id,
                'phone' => '+966501234567',
                'address' => 'Riyadh, Saudi Arabia',
                'is_active' => true,
            ]
        );

        // Create or update Manager User
        User::updateOrCreate(
            ['email' => 'manager@hamid.com'],
            [
                'name' => 'Ahmed Al-Sudairi',
                'password' => Hash::make('manager123456'),
                'role_id' => $managerRole?->id ?? $adminRole?->id,
                'phone' => '+966502345678',
                'address' => 'Jeddah, Saudi Arabia',
                'is_active' => true,
            ]
        );

        // Create or update Standard User
        User::updateOrCreate(
            ['email' => 'user@hamid.com'],
            [
                'name' => 'Fatima Al-Rashid',
                'password' => Hash::make('user123456'),
                'role_id' => $userRole?->id ?? $adminRole?->id,
                'phone' => '+966503456789',
                'address' => 'Dammam, Saudi Arabia',
                'is_active' => true,
            ]
        );

        // Create or update Test User (inactive)
        User::updateOrCreate(
            ['email' => 'test@hamid.com'],
            [
                'name' => 'Test Account',
                'password' => Hash::make('test123456'),
                'role_id' => $userRole?->id ?? $adminRole?->id,
                'phone' => '+966504567890',
                'address' => 'Makkah, Saudi Arabia',
                'is_active' => false,
            ]
        );

        echo "\n✅ User seeding completed:\n";
        echo "  - Admin: admin@hamid.com / admin123456\n";
        echo "  - Manager: manager@hamid.com / manager123456\n";
        echo "  - User: user@hamid.com / user123456\n";
        echo "  - Test (inactive): test@hamid.com / test123456\n\n";
    }
}
