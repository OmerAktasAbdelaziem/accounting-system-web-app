<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'name' => 'Acme Corporation',
            'email' => 'contact@acme.com',
            'phone' => '+966 50 1234567',
            'address' => '123 Business Street, Riyadh',
            'opening_balance' => 50000,
        ]);

        Customer::create([
            'name' => 'Tech Solutions Ltd',
            'email' => 'info@techsol.com',
            'phone' => '+966 50 7654321',
            'address' => '456 Innovation Avenue, Jeddah',
            'opening_balance' => 75000,
        ]);

        Customer::create([
            'name' => 'Global Enterprises',
            'email' => 'sales@global.com',
            'phone' => '+966 50 5555555',
            'address' => '789 Commerce Plaza, Dammam',
            'opening_balance' => 100000,
        ]);
    }
}
