<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        User::firstOrCreate(
            ['email' => 'superadmin@system.local'],
            [
                'name' => 'System Super Admin',
                'password' => bcrypt('admin12345'),
                'user_type' => 'super_admin',
                'merchant_id' => null,
                'default_language' => 'en',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin user created successfully!');
        $this->command->line('Email: superadmin@system.local');
        $this->command->line('Password: admin12345');
        $this->command->line('Type: super_admin');
    }
}
