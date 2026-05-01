<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::create([
            'name' => 'Prime Wholesale Inc',
            'email' => 'sales@primewholesale.com',
            'phone' => '+966 50 1111111',
            'address' => '100 Supply Drive, Riyadh',
            'opening_balance' => 25000,
        ]);

        Supplier::create([
            'name' => 'Manufacturing Co',
            'email' => 'contact@mfgco.com',
            'phone' => '+966 50 2222222',
            'address' => '200 Factory Road, Jeddah',
            'opening_balance' => 35000,
        ]);

        Supplier::create([
            'name' => 'Logistics Partners',
            'email' => 'ops@logistics.com',
            'phone' => '+966 50 3333333',
            'address' => '300 Warehouse Zone, Dammam',
            'opening_balance' => 15000,
        ]);
    }
}
