<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::create([
            'name' => 'Riyadh Main Branch',
            'code' => 'RYD-MAIN',
            'address' => 'King Fahd Road',
            'city' => 'Riyadh',
            'phone' => '+966 11 1000000',
            'manager_name' => 'Ahmed Al-Qahtani',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Jeddah Branch',
            'code' => 'JED-01',
            'address' => 'Corniche Road',
            'city' => 'Jeddah',
            'phone' => '+966 12 2000000',
            'manager_name' => 'Maha Al-Harbi',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Dammam Branch',
            'code' => 'DAM-01',
            'address' => 'King Abdulaziz Road',
            'city' => 'Dammam',
            'phone' => '+966 13 3000000',
            'manager_name' => 'Fahad Al-Salem',
            'is_active' => true,
        ]);
    }
}
